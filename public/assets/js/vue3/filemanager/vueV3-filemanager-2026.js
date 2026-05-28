/**
 * vueV3-filemanager.js
 * Arunika CMS — File Manager
 * Vue 3 Global Build | Bootstrap 5 | Axios | Lodash
 *
 * Arsitektur: No-Database — baca/tulis langsung ke Storage (public/S3)
 *
 * PERFORMA S3/R2 — optimasi lengkap:
 * ─────────────────────────────────────────────────────────────────────
 * 1. sessionStorage cache (persistent selama tab terbuka, TTL 5 menit)
 *    - Berbeda dengan _prefetchCache lama yang hilang saat refresh
 *    - navigateTo() tampil instan dari sessionStorage, refresh di background
 *
 * 2. Request deduplication
 *    - _pendingBrowse Map: jika request ke path yang sama sedang pending,
 *      tidak kirim request baru — tunggu yang sedang berjalan selesai
 *
 * 3. loadRootFolders() dieliminasi dari setiap navigasi
 *    - Root folders disimpan sekali, hanya di-refresh setelah write ke root
 *
 * 4. Thumbnail via imagePreview endpoint (?w=220)
 *    - Bukan URL asli S3 (ukuran penuh) — hemat bandwidth drastis
 *    - Backend set Cache-Control 24 jam + ETag → browser cache thumbnail
 *    - Render instan pada kunjungan berikutnya (0 network request)
 *
 * 5. Infinite scroll pagination
 *    - Browse hanya load 100 item pertama, scroll ke bawah load berikutnya
 *    - Drastis lebih cepat untuk folder dengan ratusan/ribuan file
 *
 * 6. Prefetch on hover (dipertahankan, debounce 150ms)
 *    - Data folder dimuat di background saat mouse hover
 * ─────────────────────────────────────────────────────────────────────
 */
;(function () {
    'use strict';

    const { createApp, nextTick } = Vue;

    // ── URL params ─────────────────────────────────────────
    const urlParams  = new URLSearchParams(window.location.search);
    const fmKeyParam = urlParams.get('fm_key')          || '';
    const ck4Func    = urlParams.get('CKEditorFuncNum') || '';
    const callbackId = urlParams.get('fm_callback')     || '';
    const typeFilter = urlParams.get('type')             || '';

    function detectMode() {
        if (window.self !== window.top) return 'embed';
        if (fmKeyParam) return 'standalone';
        return 'internal';
    }
    const FM_MODE = detectMode();

    // ── Axios instance ─────────────────────────────────────
    const http = axios.create({
        baseURL: window.FM_CONFIG.apiBase,
        headers: { 'X-CSRF-TOKEN': window.FM_CONFIG.csrfToken, 'Accept': 'application/json' },
    });
    if (fmKeyParam)                      http.defaults.headers.common['X-FM-Key']       = fmKeyParam;
    else if (window.FM_CONFIG.apiToken)  http.defaults.headers.common['Authorization']  = 'Bearer ' + window.FM_CONFIG.apiToken;

    // ── sessionStorage cache helpers ───────────────────────
    // Persistent selama tab terbuka, TTL 5 menit (sama dengan backend)
    const SESSION_TTL = 60 * 60 * 1000; // 1 jam — sinkron dengan backend file cache TTL

    function scGet(key) {
        try {
            const raw = sessionStorage.getItem('fm_' + key);
            if (!raw) return null;
            const { data, ts } = JSON.parse(raw);
            if (Date.now() - ts > SESSION_TTL) { sessionStorage.removeItem('fm_' + key); return null; }
            return data;
        } catch (_) { return null; }
    }

    function scSet(key, data) {
        try { sessionStorage.setItem('fm_' + key, JSON.stringify({ data, ts: Date.now() })); } catch (_) {}
    }

    function scDel(key) {
        try { sessionStorage.removeItem('fm_' + key); } catch (_) {}
    }

    function scBrowseKey(disk, path) {
        return 'browse_' + disk + '_' + (path || '__root__');
    }

    // ── Bust sessionStorage cache (sinkron dengan backend bust) ──
    function scBustBrowse(disk, paths = []) {
        scDel(scBrowseKey(disk, ''));
        scDel(scBrowseKey(disk, '__root__'));
        paths.forEach(p => {
            if (!p) return;
            scDel(scBrowseKey(disk, p));
            const parent = p.includes('/') ? p.substring(0, p.lastIndexOf('/')) : '';
            scDel(scBrowseKey(disk, parent));
        });
    }

    // ══════════════════════════════════════════════════════
    //  SUBCOMPONENTS — Folder tree sidebar
    // ══════════════════════════════════════════════════════

    const FolderTreeNode = {
        name: 'FolderTreeNode',
        props: { folder: Object, activePath: { default: '' }, disk: { default: 'public' } },
        emits: ['navigate', 'ctx'],
        data() { return { expanded: false, children: [] }; },
        methods: {
            async toggle() {
                this.expanded = !this.expanded;
                if (this.expanded && !this.children.length) {
                    try {
                        const res = await http.get('/browse', { params: { disk: this.disk, path: this.folder.path, per_page: 500 } });
                        this.children = res.data.data.folders;
                    } catch (_) {}
                }
            },
        },
        template: `
            <div>
                <div class="fm-tree-item" :class="{ active: activePath === folder.path }"
                     @click.stop="$emit('navigate', folder.path)"
                     @contextmenu.prevent.stop="$emit('ctx', $event, folder)">
                    <i class="bi" :class="expanded ? 'bi-chevron-down' : 'bi-chevron-right'"
                       style="font-size:10px;width:14px;flex-shrink:0"
                       @click.stop="toggle"></i>
                    <i class="bi bi-folder-fill ic-folder" style="flex-shrink:0"></i>
                    <span>{{ folder.name }}</span>
                </div>
                <div v-if="expanded" style="padding-left:14px">
                    <folder-tree-node v-for="c in children" :key="c.path"
                        :folder="c" :active-path="activePath" :disk="disk"
                        @navigate="p => $emit('navigate', p)"
                        @ctx="(e, item) => $emit('ctx', e, item)"></folder-tree-node>
                </div>
            </div>`,
    };

    // MovePicker component (untuk modal pindah file/folder)
    const MovePicker = {
        name: 'MovePicker',
        props: { folder: Object, selectedPath: { default: null }, disk: { default: 'public' } },
        emits: ['select'],
        data() { return { expanded: false, children: [] }; },
        methods: {
            async toggle() {
                this.expanded = !this.expanded;
                if (this.expanded && !this.children.length) {
                    try {
                        const res = await http.get('/browse', { params: { disk: this.disk, path: this.folder.path, per_page: 500 } });
                        this.children = res.data.data.folders;
                    } catch (_) {}
                }
            },
        },
        template: `
            <div>
                <div class="fm-tree-item" :class="{ active: selectedPath === folder.path }"
                     @click.stop="$emit('select', folder.path)">
                    <i class="bi" :class="expanded ? 'bi-chevron-down' : 'bi-chevron-right'"
                       style="font-size:10px;width:14px;flex-shrink:0"
                       @click.stop="toggle"></i>
                    <i class="bi bi-folder-fill ic-folder" style="flex-shrink:0"></i>
                    <span>{{ folder.name }}</span>
                </div>
                <div v-if="expanded" style="padding-left:14px">
                    <move-picker v-for="c in children" :key="c.path"
                        :folder="c" :selected-path="selectedPath" :disk="disk"
                        @select="p => $emit('select', p)"></move-picker>
                </div>
            </div>`,
    };

    // ══════════════════════════════════════════════════════
    //  MAIN APP
    // ══════════════════════════════════════════════════════

    const app = createApp({
        components: { FolderTreeNode, MovePicker },

        data() {
            return {
                fmMode:   FM_MODE,

                // Disk & Navigation
                activeDisk:  window.FM_CONFIG.defaultDisk || 'public',
                disks:       [],
                currentPath: '',
                breadcrumb:  [],
                rootFolders: [],

                // Content
                folders: [],
                files:   [],
                loading: false,

                // Pagination / infinite scroll
                currentPage:  1,
                totalPages:   1,
                totalFiles:   0,
                totalFolders: 0,
                hasMore:      false,
                loadingMore:  false,

                // Filter & Sort
                search:      '',
                filterType:  typeFilter,
                sortBy:      'date',
                sortDir:     'desc',
                sizePreset:  '',
                sizeMin:     '',
                sizeMax:     '',
                showFilter:  false,

                // View
                viewMode: 'grid',

                // Selection
                selectedItems:    new Set(),
                lastSelectedFile: null,

                // Drag & Drop
                isDragging:     false,
                dragItem:       null,
                dragOverFolder: null,

                // Uploads
                uploads:  [],
                _uid:     0,

                // Quota
                quota: null,

                // Context menu
                ctxMenu: { show: false, x: 0, y: 0, target: null },

                // Lightbox
                lightbox: { show: false, url: '', name: '', size: '' },

                // Modals
                modalLoading:  false,
                newFolderName: '',
                renameTarget:  null,
                renameValue:   '',
                moveTarget:    null,
                moveDestPath:  null,
                deleteTarget:  null,
                deleteMsg:     '',

                // Notices
                notices: [],
                _nid:    0,

                // Info panel
                selectedInfo:        null,
                selectedInfoLoading: false,

                // Image Editor
                imageEditor: {
                    show:       false,
                    file:       null,
                    base64:     null,
                    loading:    false,
                    saving:     false,
                    saveAsNew:  true,
                    activeTool: null,
                    crop:   { x: 0, y: 0, width: 0, height: 0, aspect: 'free' },
                    resize: { width: 0, height: 0, keepRatio: true, origW: 0, origH: 0 },
                    brightness: 0,
                    contrast:   0,
                    activeFilter: '',
                    operations: [],
                },

                // Permission (ACL)
                aclSupported:    true,
                aclFetching:     false, // true saat fetch ACL background (pisah dari modalLoading)
                permissionTarget: null,
                permissionForm: {
                    acl:             'public-read',
                    recursive:       false,
                    extensionFilter: '',
                    concurrency:     5,
                },

                // Metadata
                metadataTarget: null,
                metadataForm: {
                    content_type:        '',
                    cache_control:       '',
                    content_disposition: '',
                    content_encoding:    '',
                    content_language:    '',
                    recursive:           false,
                    extensionFilter:     '',
                    concurrency:         5,
                },

                // Bulk progress (Reverb realtime)
                bulkProgress: {
                    active:      false,
                    jobId:       null,
                    current:     0,
                    total:       0,
                    percent:     0,
                    message:     '',
                    currentFile: '',
                    status:      '',
                    _channel:    null,
                },

                // Internal — request deduplication
                // Map<path, Promise> — jika request path yang sama sedang pending,
                // tidak kirim ulang — pakai promise yang sudah ada
                _pendingBrowse: new Map(),

                // Track path yang sudah di-revalidate di sesi ini
                // Mencegah _bgRevalidate dipanggil berulang untuk path yang sama
                _revalidated: new Set(),

                // Prefetch timer
                _prefetchTimer: null,

                // Search debounce
                _searchTimer: null,
            };
        },

        computed: {
            hasCallback() {
                return !!(ck4Func || callbackId || this.fmMode === 'embed');
            },
            allSelected() {
                const total = this.folders.length + this.files.length;
                return total > 0 && this.selectedItems.size === total;
            },
            availableDisks() {
                return this.disks.filter(d => d.available || d.type === 'local');
            },
        },

        async mounted() {
            // Paralel: loadDisks dan browse jalan bersamaan
            await Promise.all([
                this.loadDisks(),
                this.browse(),
            ]);

            // loadQuota non-blocking
            this.loadQuota();

            document.addEventListener('click',   this.closeCtx);
            document.addEventListener('keydown', this.onKey);
            window.addEventListener('message',   this.onMessage);
            if (this.fmMode === 'embed') this.postToParent({ type: 'fm:ready' });

            // Setup IntersectionObserver untuk infinite scroll
            this.$nextTick(() => this.setupScrollObserver());
        },

        beforeUnmount() {
            document.removeEventListener('click',   this.closeCtx);
            document.removeEventListener('keydown', this.onKey);
            window.removeEventListener('message',   this.onMessage);
            if (this._scrollObserver) this._scrollObserver.disconnect();
        },

        methods: {

            // ─── Disk management ───────────────────────────

            async loadDisks() {
                try {
                    const res = await http.get('/disks');
                    this.disks = res.data.data;
                    const current = this.disks.find(d => d.key === this.activeDisk);
                    if (!current || !current.available) {
                        const first = this.disks.find(d => d.available);
                        if (first) this.activeDisk = first.key;
                    }
                } catch (_) {
                    this.disks = [
                        { key: 'public', label: 'Local/Public', type: 'local', available: true },
                    ];
                }
            },

            async onDiskChange() {
                const disk = this.disks.find(d => d.key === this.activeDisk);
                if (disk && !disk.available) {
                    this.addNotice('warning', `${disk.label} belum dikonfigurasi: ${disk.reason ?? 'Cek config/filesystems.php dan .env'}`);
                    const fallback = this.disks.find(d => d.available);
                    this.activeDisk = fallback ? fallback.key : 'public';
                    return;
                }
                this.currentPath   = '';
                this.selectedItems = new Set();
                this.selectedInfo  = null;
                await this.browse();
                this.loadQuota();
            },

            // ─── Browse — dengan sessionStorage cache + request dedup ──

            async browse(path, { append = false } = {}) {
                if (path !== undefined) this.currentPath = path;

                // Reset pagination kecuali mode append (infinite scroll)
                if (!append) {
                    this.currentPage  = 1;
                    this.loading      = true;
                    this.selectedItems = new Set();
                }

                const page    = append ? this.currentPage + 1 : 1;
                const cacheKey = scBrowseKey(this.activeDisk, this.currentPath);

                // sessionStorage cache — hanya untuk halaman pertama
                // TTL 1 jam (sinkron dengan backend file cache 3600s)
                if (!append && !this.search && !this.filterType && this.sortBy === 'date' && this.sortDir === 'desc') {
                    const cached = scGet(cacheKey);
                    if (cached) {
                        this.folders      = cached.folders;
                        this.files        = cached.files;
                        this.breadcrumb   = cached.breadcrumb;
                        this.totalFiles   = cached.total_files;
                        this.totalFolders = cached.total_folders;
                        this.hasMore      = cached.has_more;
                        this.totalPages   = cached.total_pages;
                        this.currentPage  = 1;
                        this.loading      = false;

                        // Update root folders dari cache jika di root
                        if (!this.currentPath) this.rootFolders = cached.folders;

                        // Revalidate hanya sekali per path per sesi — tidak setiap cache hit
                        // Backend TTL 1 jam sudah cukup untuk konsistensi data
                        if (!this._revalidated.has(cacheKey)) {
                            this._revalidated.add(cacheKey);
                            this._bgRevalidate(cacheKey);
                        }
                        return;
                    }
                }

                // Request deduplication — cek apakah request ke path+page ini sudah pending
                const dedupKey = this.activeDisk + '|' + this.currentPath + '|' + page;
                if (this._pendingBrowse.has(dedupKey)) {
                    try {
                        const d = await this._pendingBrowse.get(dedupKey);
                        this._applyBrowseResult(d, append, page, cacheKey);
                    } catch (_) {}
                    if (!append) this.loading = false;
                    return;
                }

                const params = {
                    disk:     this.activeDisk,
                    path:     this.currentPath,
                    page:     page,
                    per_page: 100,
                    sort_by:  this.sortBy,
                    sort_dir: this.sortDir,
                };
                if (this.filterType) params.type     = this.filterType;
                if (this.search)     params.search   = this.search;
                if (this.sizeMin)    params.size_min = this.sizeMin;
                if (this.sizeMax)    params.size_max = this.sizeMax;

                const promise = http.get('/browse', { params }).then(res => res.data.data);
                this._pendingBrowse.set(dedupKey, promise);

                try {
                    const d = await promise;
                    this._applyBrowseResult(d, append, page, cacheKey);
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal memuat konten.');
                } finally {
                    this._pendingBrowse.delete(dedupKey);
                    if (!append) this.loading = false;
                    else         this.loadingMore = false;
                }
            },

            // Terapkan hasil browse ke state
            _applyBrowseResult(d, append, page, cacheKey) {
                if (append) {
                    // Infinite scroll: tambahkan files baru ke list yang ada
                    this.files = [...this.files, ...d.files];
                } else {
                    this.folders  = d.folders;
                    this.files    = d.files;
                    this.breadcrumb = d.breadcrumb;

                    // Update root folders — hanya jika di root, tanpa request ekstra
                    if (!this.currentPath && !this.search) {
                        this.rootFolders = d.folders;
                    }

                    // Simpan ke sessionStorage hanya untuk browse default (tanpa filter/search)
                    if (!this.search && !this.filterType && this.sortBy === 'date' && this.sortDir === 'desc') {
                        scSet(cacheKey, d);
                    }
                }

                this.currentPage  = page;
                this.totalFiles   = d.total_files ?? d.total ?? 0;
                this.totalFolders = d.total_folders ?? this.folders.length;
                this.totalPages   = d.total_pages ?? 1;
                this.hasMore      = d.has_more ?? false;
            },

            // Background revalidate — update sessionStorage cache tanpa blocking UI
            async _bgRevalidate(cacheKey) {
                try {
                    const params = {
                        disk:     this.activeDisk,
                        path:     this.currentPath,
                        page:     1,
                        per_page: 100,
                        sort_by:  'date',
                        sort_dir: 'desc',
                    };
                    const res = await http.get('/browse', { params });
                    const d   = res.data.data;
                    scSet(cacheKey, d);

                    // Update UI jika path belum berubah
                    if (this.currentPath === (d.path ?? '')) {
                        this.folders      = d.folders;
                        this.files        = d.files;
                        this.totalFiles   = d.total_files ?? d.total ?? 0;
                        this.totalFolders = d.total_folders ?? d.folders.length;
                        this.hasMore      = d.has_more ?? false;
                        this.totalPages   = d.total_pages ?? 1;
                        if (!this.currentPath) this.rootFolders = d.folders;
                    }
                } catch (_) {}
            },

            // ─── Infinite scroll ───────────────────────────

            setupScrollObserver() {
                const sentinel = document.getElementById('fm-scroll-sentinel');
                if (!sentinel) return;

                this._scrollObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && this.hasMore && !this.loadingMore && !this.loading) {
                        this.loadMore();
                    }
                }, { threshold: 0.1 });

                this._scrollObserver.observe(sentinel);
            },

            async loadMore() {
                if (this.loadingMore || !this.hasMore) return;
                this.loadingMore = true;
                await this.browse(undefined, { append: true });
            },

            // ─── Navigation ────────────────────────────────

            async navigateTo(path) {
                const target = path === undefined ? '' : path;

                // Cek sessionStorage — tampil instan
                const cacheKey = scBrowseKey(this.activeDisk, target);
                const cached   = !this.search && !this.filterType ? scGet(cacheKey) : null;

                if (cached) {
                    this.currentPath   = target;
                    this.folders       = cached.folders;
                    this.files         = cached.files;
                    this.breadcrumb    = cached.breadcrumb;
                    this.selectedItems = new Set();
                    this.selectedInfo  = null;
                    this.currentPage   = 1;
                    this.totalFiles    = cached.total_files ?? cached.total ?? 0;
                    this.totalFolders  = cached.total_folders ?? cached.folders.length;
                    this.hasMore       = cached.has_more ?? false;
                    this.totalPages    = cached.total_pages ?? 1;
                    this.loading       = false;

                    if (!target) this.rootFolders = cached.folders;

                    // Background revalidate
                    this._bgRevalidate(cacheKey);
                    return;
                }

                await this.browse(target);
            },

            // Prefetch browse folder saat mouseenter
            prefetchFolder(folder) {
                const cacheKey = scBrowseKey(this.activeDisk, folder.path);
                const cached   = scGet(cacheKey);
                if (cached) return; // Sudah ada di sessionStorage

                clearTimeout(this._prefetchTimer);
                this._prefetchTimer = setTimeout(async () => {
                    try {
                        const res = await http.get('/browse', {
                            params: { disk: this.activeDisk, path: folder.path, per_page: 100 },
                        });
                        scSet(cacheKey, res.data.data);
                    } catch (_) {}
                }, 150);
            },

            // ─── Quota ─────────────────────────────────────

            async loadQuota() {
                try {
                    const res = await http.get('/quota', { params: { disk: this.activeDisk } });
                    this.quota = res.data.data;
                } catch (_) {}
            },

            // ─── Search & Filter ───────────────────────────

            debounceSearch() {
                clearTimeout(this._searchTimer);
                this._searchTimer = setTimeout(() => this.browse(), 400);
            },

            clearSearch() {
                this.search     = '';
                this.filterType = '';
                this.sortBy     = 'date';
                this.sortDir    = 'desc';
                this.sizeMin    = '';
                this.sizeMax    = '';
                this.sizePreset = '';
                this.browse();
            },

            applyFilter() { this.browse(); },

            resetFilter() {
                this.filterType = '';
                this.sortBy     = 'date';
                this.sortDir    = 'desc';
                this.sizeMin    = '';
                this.sizeMax    = '';
                this.sizePreset = '';
                this.browse();
            },

            onSizePreset() {
                const map = {
                    tiny:   { min: '',       max: 102400   },
                    small:  { min: 102400,   max: 1048576  },
                    medium: { min: 1048576,  max: 10485760 },
                    large:  { min: 10485760, max: ''       },
                    custom: { min: '',       max: ''       },
                    '':     { min: '',       max: ''       },
                };
                const p = map[this.sizePreset] || map[''];
                this.sizeMin = p.min;
                this.sizeMax = p.max;
            },

            // ─── Thumbnail URL helper ───────────────────────
            // Thumbnail menggunakan WEB route (/filemanager/thumbnail) bukan API route.
            // Alasan: browser tidak bisa kirim Authorization header saat load <img src>.
            // Web route punya session middleware → auth::guard('web') bekerja normal.
            // API route tidak punya session → selalu 401 untuk <img> request.

            thumbUrl(file) {
                if (file.file_type !== 'image') return null;
                const params = new URLSearchParams({
                    disk: this.activeDisk,
                    path: file.path,
                    w:    220,
                });
                if (fmKeyParam) params.set('fm_key', fmKeyParam);
                return '/filemanager/thumbnail?' + params.toString();
            },

            // ─── Selection ─────────────────────────────────

            toggleSelect(key, item) {
                const s = new Set(this.selectedItems);
                if (s.has(key)) {
                    s.delete(key);
                    if (item?.type === 'file') this.lastSelectedFile = null;
                } else {
                    s.add(key);
                    if (item?.type === 'file') this.lastSelectedFile = item;
                }
                this.selectedItems = s;
                if (this.selectedItems.size === 1) {
                    this.loadInfo(item);
                } else {
                    this.selectedInfo = null;
                }
            },

            deselectAll() {
                this.selectedItems    = new Set();
                this.lastSelectedFile = null;
                this.selectedInfo     = null;
            },

            selectAll() {
                const s = new Set();
                this.folders.forEach(f => s.add('folder_' + f.path));
                this.files.forEach(f => s.add('file_' + f.path));
                this.selectedItems    = s;
                this.lastSelectedFile = this.files.length ? this.files[this.files.length - 1] : null;
                this.selectedInfo     = null;
            },

            toggleSelectAll() {
                if (this.allSelected) this.deselectAll();
                else this.selectAll();
            },

            // ─── Info panel ────────────────────────────────

            async loadInfo(item) {
                if (!item || item.type === 'folder') { this.selectedInfo = item; return; }
                this.selectedInfo        = item;
                this.selectedInfoLoading = true;
                try {
                    const res = await http.get('/info', {
                        params: { disk: this.activeDisk, path: item.path, type: item.type }
                    });
                    this.selectedInfo = res.data.data;
                } catch (_) {
                    this.selectedInfo = item;
                } finally {
                    this.selectedInfoLoading = false;
                }
            },

            // ─── Folder operations ─────────────────────────

            showNewFolder() {
                this.newFolderName = '';
                nextTick(() => {
                    const m = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNewFolder'));
                    m.show();
                    nextTick(() => this.$refs.inputFolder?.focus());
                });
            },

            async doCreateFolder() {
                const name = this.newFolderName.trim();
                if (!name) return;

                // ── Optimistic UI: tutup modal + inject folder ke state langsung ──
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNewFolder')).hide();
                this.modalLoading = false;

                const optimisticPath = this.currentPath ? this.currentPath + '/' + name : name;
                const optimisticFolder = { name, path: optimisticPath, type: 'folder' };

                // Inject ke folders list secara alphabetical
                const insertIdx = this.folders.findIndex(f => f.name.toLowerCase() > name.toLowerCase());
                if (insertIdx === -1) this.folders.push(optimisticFolder);
                else this.folders.splice(insertIdx, 0, optimisticFolder);

                scBustBrowse(this.activeDisk, [this.currentPath]);

                try {
                    await http.post('/folder/create', {
                        disk: this.activeDisk,
                        path: this.currentPath,
                        name,
                    });
                    this.addNotice('success', `Folder "${name}" berhasil dibuat`);
                    // Refresh di background — tidak blocking UI
                    this.browse();
                } catch (err) {
                    // Rollback: hapus folder yang sudah di-inject
                    this.folders = this.folders.filter(f => f.path !== optimisticPath);
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal membuat folder');
                }
            },

            // ─── Rename ────────────────────────────────────

            startRename(item) {
                this.renameTarget = item;
                this.renameValue  = item.name;
                nextTick(() => {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRename')).show();
                    nextTick(() => this.$refs.inputRename?.focus());
                });
            },

            async doRename() {
                if (!this.renameValue.trim() || !this.renameTarget) return;
                const newName    = this.renameValue.trim();
                const target     = this.renameTarget;
                const isFolder   = target.type === 'folder';
                const endpoint   = isFolder ? '/folder/rename' : '/file/rename';
                const oldName    = target.name;
                const oldPath    = target.path;

                // ── Optimistic UI: tutup modal + update nama di state langsung ──
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalRename')).hide();
                this.modalLoading = false;

                if (isFolder) {
                    const f = this.folders.find(f => f.path === oldPath);
                    if (f) f.name = newName;
                } else {
                    const f = this.files.find(f => f.path === oldPath);
                    if (f) f.name = newName;
                }

                scBustBrowse(this.activeDisk, [this.currentPath]);

                try {
                    await http.post(endpoint, { disk: this.activeDisk, path: oldPath, name: newName });
                    this.addNotice('success', `Berhasil diubah menjadi "${newName}"`);
                    this.browse();
                } catch (err) {
                    // Rollback nama
                    if (isFolder) {
                        const f = this.folders.find(f => f.path === oldPath);
                        if (f) f.name = oldName;
                    } else {
                        const f = this.files.find(f => f.path === oldPath);
                        if (f) f.name = oldName;
                    }
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal mengubah nama');
                }
            },

            // ─── Move ──────────────────────────────────────

            startMove(item) {
                this.moveTarget   = item;
                this.moveDestPath = null;
                nextTick(() => bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMove')).show());
            },

            async doMove() {
                if (!this.moveTarget || this.moveDestPath === null) return;
                this.modalLoading = true;
                const isFolder = this.moveTarget.type === 'folder';
                const endpoint = isFolder ? '/folder/move' : '/file/move';
                const path     = this.moveTarget.path;

                // ── Optimistic: hapus dari state langsung (item berpindah folder) ──
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMove')).hide();
                this.modalLoading = false;

                if (isFolder) this.folders = this.folders.filter(f => f.path !== path);
                else          this.files   = this.files.filter(f => f.path !== path);

                scBustBrowse(this.activeDisk, [this.currentPath, this.moveDestPath]);

                try {
                    await http.post(endpoint, {
                        disk:   this.activeDisk,
                        path,
                        target: this.moveDestPath,
                    });
                    this.addNotice('success', 'Berhasil dipindahkan');
                    this.browse();
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal memindahkan');
                    // Refresh untuk kembalikan state yang benar
                    this.browse();
                }
            },

            // ─── Copy ──────────────────────────────────────

            async doCopy(item) {
                try {
                    await http.post('/file/copy', {
                        disk:   this.activeDisk,
                        path:   item.path,
                        target: this.currentPath,
                    });
                    scBustBrowse(this.activeDisk, [this.currentPath]);
                    this.addNotice('success', 'File berhasil disalin');
                    await this.browse();
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal menyalin');
                }
            },

            // ─── Delete ────────────────────────────────────

            confirmDelete(item) {
                this.deleteTarget = item;
                this.deleteMsg    = item.type === 'folder'
                    ? `Hapus folder "${item.name}" dan semua isinya?`
                    : `Hapus file "${item.name}"?`;
                nextTick(() => bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete')).show());
            },

            confirmDeleteSelected() {
                if (!this.selectedItems.size) return;
                this.deleteTarget = '__selected__';
                this.deleteMsg    = `Hapus ${this.selectedItems.size} item yang dipilih?`;
                nextTick(() => bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete')).show());
            },

            async doDelete() {
                this.modalLoading = true;

                // Snapshot state untuk rollback jika gagal
                const prevFolders = [...this.folders];
                const prevFiles   = [...this.files];

                try {
                    if (this.deleteTarget === '__selected__') {
                        const items = [];
                        const keysToDelete = new Set(this.selectedItems);
                        this.selectedItems.forEach(key => {
                            const isFolder = key.startsWith('folder_');
                            const path     = key.replace(/^(folder_|file_)/, '');
                            items.push({ path, type: isFolder ? 'folder' : 'file' });
                        });

                        // ── Optimistic: hapus dari state langsung ──
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete')).hide();
                        this.modalLoading = false;
                        this.folders = this.folders.filter(f => !keysToDelete.has('folder_' + f.path));
                        this.files   = this.files.filter(f => !keysToDelete.has('file_' + f.path));
                        this.selectedItems = new Set();
                        scBustBrowse(this.activeDisk, [this.currentPath]);

                        await http.delete('/batch/delete', { data: { disk: this.activeDisk, items } });
                        this.addNotice('success', items.length + ' item berhasil dihapus');
                        this.browse();
                    } else {
                        const isFolder = this.deleteTarget.type === 'folder';
                        const endpoint = isFolder ? '/folder/delete' : '/file/delete';
                        const path     = this.deleteTarget.path;

                        // ── Optimistic: hapus dari state langsung ──
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDelete')).hide();
                        this.modalLoading = false;
                        if (isFolder) this.folders = this.folders.filter(f => f.path !== path);
                        else          this.files   = this.files.filter(f => f.path !== path);
                        scBustBrowse(this.activeDisk, [this.currentPath]);

                        await http.delete(endpoint, { data: { disk: this.activeDisk, path } });
                        this.addNotice('success', 'Berhasil dihapus');
                        this.browse();
                    }
                } catch (err) {
                    // Rollback state
                    this.folders = prevFolders;
                    this.files   = prevFiles;
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal menghapus');
                } finally {
                    this.modalLoading = false;
                }
            },

            // ─── Upload ────────────────────────────────────

            triggerUpload() { document.getElementById('fm-upload-input').click(); },

            handleFileInput(e) {
                const files = Array.from(e.target.files);
                files.forEach(f => this.uploadFile(f));
                e.target.value = '';
            },

            handleDrop(e) {
                this.isDragging = false;
                const files = Array.from(e.dataTransfer?.files || []);
                files.forEach(f => this.uploadFile(f));
            },

            async uploadFile(file) {
                const uid    = ++this._uid;
                const upload = { uid, name: file.name, progress: 0, status: 'uploading', error: null };
                this.uploads.push(upload);

                const fd = new FormData();
                fd.append('file', file);
                fd.append('disk', this.activeDisk);
                fd.append('path', this.currentPath);

                try {
                    const res = await http.post('/file/upload', fd, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                        onUploadProgress: e => {
                            const u = this.uploads.find(u => u.uid === uid);
                            if (u) u.progress = Math.round(e.loaded / e.total * 100);
                        },
                    });

                    const u = this.uploads.find(u => u.uid === uid);
                    if (u) u.status = 'done';

                    // ── Optimistic: inject file baru ke state langsung dari response ──
                    const newFile = res.data.data;
                    if (newFile && newFile.path) {
                        // Hapus jika sudah ada (upload overwrite dengan nama baru)
                        this.files = this.files.filter(f => f.path !== newFile.path);
                        // Tambah di awal (sort by date desc = terbaru di atas)
                        this.files.unshift(newFile);
                        this.totalFiles = this.files.length;
                    }

                    scBustBrowse(this.activeDisk, [this.currentPath]);
                    // Browse di background — tidak blocking
                    this.browse();

                } catch (err) {
                    const u = this.uploads.find(u => u.uid === uid);
                    if (u) { u.status = 'error'; u.error = err.response?.data?.message ?? 'Gagal upload'; }
                    this.addNotice('danger', `Gagal upload ${file.name}`);
                } finally {
                    setTimeout(() => {
                        const idx = this.uploads.findIndex(u => u.uid === uid);
                        if (idx >= 0 && this.uploads[idx].status === 'done') this.uploads.splice(idx, 1);
                    }, 3000);
                }
            },

            // ─── Drag & Drop antar folder ──────────────────

            onDragStart(e, item) {
                this.dragItem = item;
                e.dataTransfer.effectAllowed = 'move';
            },
            onDragEnd() { this.dragItem = null; this.dragOverFolder = null; },
            onDragOverFolder(e, folder) { if (this.dragItem) this.dragOverFolder = folder.path; },
            onDragLeaveFolder() { this.dragOverFolder = null; },

            async onDropToFolder(e, folder) {
                e.preventDefault();
                this.dragOverFolder = null;
                if (!this.dragItem || this.dragItem.path === folder.path) return;

                const item     = this.dragItem;
                this.dragItem  = null;
                const isFolder = item.type === 'folder';
                const endpoint = isFolder ? '/folder/move' : '/file/move';
                const target   = folder.path === '_root_' ? '' : folder.path;

                try {
                    await http.post(endpoint, { disk: this.activeDisk, path: item.path, target });
                    scBustBrowse(this.activeDisk, [this.currentPath, target]);
                    this.addNotice('success', `"${item.name}" dipindahkan ke "${folder.name}"`);
                    await this.browse();
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal memindahkan');
                }
            },

            async onDropToCurrentFolder(e) {
                e.preventDefault();
                if (!this.dragItem) return;
                const item = this.dragItem;
                this.dragItem = null;
                const itemParent = item.path.includes('/')
                    ? item.path.substring(0, item.path.lastIndexOf('/'))
                    : '';
                if (itemParent === this.currentPath) return;
                try {
                    const isFolder = item.type === 'folder';
                    await http.post(isFolder ? '/folder/move' : '/file/move', {
                        disk: this.activeDisk, path: item.path, target: this.currentPath,
                    });
                    scBustBrowse(this.activeDisk, [this.currentPath, itemParent]);
                    this.addNotice('success', `"${item.name}" dipindahkan.`);
                    await this.browse();
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal memindahkan.');
                }
            },

            // ─── Lightbox ──────────────────────────────────

            openLightbox(file) {
                // Lightbox tetap pakai URL asli (full resolution) untuk preview
                this.lightbox = { show: true, url: file.url, name: file.name, size: file.size_human };
            },

            onItemDblClick(item) {
                if (item.type === 'folder') { this.navigateTo(item.path); return; }
                if (this.hasCallback) { this.insertToEditor(item); return; }
                if (item.file_type === 'image') this.openLightbox(item);
            },

            // ─── Insert to editor ──────────────────────────

            insertToEditor(file) {
                const payload = { type: 'fm:insert', url: file.url, callbackId, file };
                if (ck4Func && window.opener) {
                    try { window.opener.CKEDITOR.tools.callFunction(ck4Func, file.url); window.close(); return; } catch (_) {}
                }
                if (this.fmMode === 'embed') { this.postToParent(payload); return; }
                if (window.opener) { window.opener.postMessage(payload, '*'); window.close(); return; }
                this.copyUrl(file);
            },

            copyUrl(file) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(file.url).then(() => this.addNotice('info', 'URL disalin ke clipboard!'));
                } else {
                    prompt('Salin URL:', file.url);
                }
            },

            postToParent(data) {
                if (window.parent && window.parent !== window) window.parent.postMessage(data, '*');
            },

            onMessage(e) {
                const d = e.data;
                if (!d || typeof d !== 'object') return;
                if (d.type === 'fm:set_filter') { this.filterType = d.fileType || ''; this.browse(); }
            },

            // ─── Image Editor ──────────────────────────────

            async openImageEditor(file) {
                this.imageEditor.show     = true;
                this.imageEditor.file     = file;
                this.imageEditor.loading  = true;
                this.imageEditor.base64   = null;
                this.imageEditor.operations = [];
                this.imageEditor.activeTool = null;
                try {
                    const res = await fetch(file.url);
                    const blob = await res.blob();
                    this.imageEditor.base64 = await new Promise(resolve => {
                        const r = new FileReader();
                        r.onload = e => resolve(e.target.result);
                        r.readAsDataURL(blob);
                    });
                } catch (_) {
                    this.addNotice('danger', 'Gagal memuat gambar untuk diedit');
                    this.imageEditor.show = false;
                } finally {
                    this.imageEditor.loading = false;
                }
            },

            addImageOp(op) { this.imageEditor.operations.push(op); },

            // ─── Image Editor tool methods ──────────────────
            // Dipanggil dari template blade (ieSetTool, ieApplyCrop, dst)

            ieSetTool(tool) {
                this.imageEditor.activeTool = this.imageEditor.activeTool === tool ? null : tool;
            },

            ieApplyCrop() {
                const c = this.imageEditor.crop;
                if (c.width <= 0 || c.height <= 0) return;
                this.imageEditor.operations.push({ type: 'crop', x: c.x, y: c.y, width: c.width, height: c.height });
                this.addNotice('info', `Crop ${c.width}×${c.height} ditambahkan.`);
            },

            ieApplyResize() {
                const r = this.imageEditor.resize;
                if (r.width <= 0 && r.height <= 0) return;
                this.imageEditor.operations.push({ type: 'resize', width: r.width, height: r.height, keep_ratio: r.keepRatio });
                this.addNotice('info', `Resize ${r.width}×${r.height} ditambahkan.`);
            },

            ieResizeWidthChanged() {
                if (this.imageEditor.resize.keepRatio && this.imageEditor.resize.origW > 0) {
                    const ratio = this.imageEditor.resize.origH / this.imageEditor.resize.origW;
                    this.imageEditor.resize.height = Math.round(this.imageEditor.resize.width * ratio);
                }
            },

            ieResizeHeightChanged() {
                if (this.imageEditor.resize.keepRatio && this.imageEditor.resize.origH > 0) {
                    const ratio = this.imageEditor.resize.origW / this.imageEditor.resize.origH;
                    this.imageEditor.resize.width = Math.round(this.imageEditor.resize.height * ratio);
                }
            },

            ieRotate(angle) {
                this.imageEditor.operations.push({ type: 'rotate', angle });
                this.addNotice('info', `Rotate ${angle}° ditambahkan.`);
            },

            ieFlip(direction) {
                this.imageEditor.operations.push({ type: direction === 'h' ? 'flip_h' : 'flip_v' });
                this.addNotice('info', `Flip ${direction === 'h' ? 'horizontal' : 'vertikal'} ditambahkan.`);
            },

            ieApplyAdjust() {
                if (this.imageEditor.brightness !== 0) {
                    this.imageEditor.operations.push({ type: 'brightness', value: this.imageEditor.brightness });
                }
                if (this.imageEditor.contrast !== 0) {
                    this.imageEditor.operations.push({ type: 'contrast', value: this.imageEditor.contrast });
                }
                this.addNotice('info', 'Penyesuaian brightness/contrast ditambahkan.');
            },

            ieApplyFilter(filter) {
                this.imageEditor.activeFilter = filter;
                const typeMap = {
                    grayscale: 'grayscale',
                    sepia:     'grayscale', // fallback, backend handle sepia via filter name
                    blur:      'blur',
                    sharpen:   'sharpen',
                    invert:    'grayscale', // fallback
                };
                this.imageEditor.operations.push({ type: typeMap[filter] || filter, filter });
                this.addNotice('info', `Filter ${filter} ditambahkan.`);
            },

            ieUndoLast() {
                this.imageEditor.operations.pop();
            },

            ieClearAll() {
                this.imageEditor.operations = [];
                this.imageEditor.activeFilter = '';
                this.imageEditor.brightness   = 0;
                this.imageEditor.contrast     = 0;
            },

            // Alias ieSave → saveImageEdit (dipanggil dari tombol Simpan Hasil Edit di blade)
            async ieSave() {
                await this.saveImageEdit();
            },

            async saveImageEdit() {
                if (!this.imageEditor.file || !this.imageEditor.operations.length) return;
                this.imageEditor.saving = true;
                try {
                    await http.post('/image/edit', {
                        disk:        this.activeDisk,
                        path:        this.imageEditor.file.path,
                        operations:  this.imageEditor.operations,
                        save_as_new: this.imageEditor.saveAsNew,
                        quality:     90,
                    });
                    scBustBrowse(this.activeDisk, [this.currentPath]);
                    this.addNotice('success', 'Gambar berhasil disimpan');
                    this.imageEditor.show = false;
                    await this.browse();
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal menyimpan gambar');
                } finally {
                    this.imageEditor.saving = false;
                }
            },

            // ─── Permission (ACL) ──────────────────────────

            async openPermission(item) {
                this.permissionTarget = item;
                this.permissionForm   = { acl: 'public-read', recursive: false, extensionFilter: '', concurrency: 5 };
                this.bulkProgress     = { active: false, jobId: null, current: 0, total: 0, percent: 0, message: '', currentFile: '', status: '', _channel: null };
                this.aclSupported = true;
                this.aclFetching  = true; // spinner kecil di dalam form, bukan di tombol Simpan

                // Buka modal DULU agar user tidak menunggu — fetch ACL di background
                const el = document.getElementById('modalPermission');
                bootstrap.Modal.getInstance(el)?.dispose();
                await nextTick();
                bootstrap.Modal.getOrCreateInstance(el).show();

                // Fetch ACL / disk-support di background, update form setelah data tiba
                try {
                    const path = item.type === 'file' ? item.path : '__disk_check__';
                    const res  = await http.get('/permission', { params: { disk: this.activeDisk, path } });
                    this.aclSupported = res.data.data?.acl_supported !== false;
                    if (item.type === 'file' && this.aclSupported && res.data.data?.acl) {
                        this.permissionForm.acl = res.data.data.acl;
                    }
                } catch (_) {
                    this.aclSupported = true;
                } finally {
                    this.aclFetching = false;
                }
            },

            async doUpdatePermission() {
                if (!this.permissionTarget) return;
                this.modalLoading = true;

                const isFolder  = this.permissionTarget.type === 'folder';
                const isBulk    = isFolder; // folder = bulk operation dengan progress
                const endpoint  = isBulk ? '/permission/bulk' : '/permission';

                const payload = {
                    disk:             this.activeDisk,
                    path:             this.permissionTarget.path,
                    acl:              this.permissionForm.acl,
                    type:             this.permissionTarget.type,
                    recursive:        this.permissionForm.recursive,
                    extension_filter: this.permissionForm.extensionFilter,
                    concurrency:      this.permissionForm.concurrency,
                };

                try {
                    if (isBulk) {
                        // Untuk bulk: subscribe Reverb setelah dapat jobId dari response
                        this.bulkProgress.active = true;
                        this.bulkProgress.status = 'processing';
                        this.bulkProgress.message = 'Mengirim request...';
                    }

                    const res = await http.post(endpoint, payload);
                    const data = res.data.data;

                    if (isBulk && data.job_id) {
                        this._subscribeBulkProgress(data.job_id);
                    } else {
                        this.addNotice('success', res.data.message ?? 'Permission berhasil diperbarui');
                        bootstrap.Modal.getInstance(document.getElementById('modalPermission'))?.hide();
                    }
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal update permission');
                    this.bulkProgress.active = false;
                } finally {
                    this.modalLoading = false;
                }
            },

            // ─── Metadata ──────────────────────────────────

            async openMetadata(item) {
                this.metadataTarget = item;
                this.metadataForm   = { content_type: '', cache_control: '', content_disposition: '', content_encoding: '', content_language: '', recursive: false, extensionFilter: '', concurrency: 5 };
                this.bulkProgress   = { active: false, jobId: null, current: 0, total: 0, percent: 0, message: '', currentFile: '', status: '', _channel: null };
                this.aclFetching    = true; // spinner kecil di form metadata (bukan di tombol Simpan)

                // Buka modal DULU, fetch metadata di background
                const elMeta = document.getElementById('modalMetadata');
                bootstrap.Modal.getInstance(elMeta)?.dispose();
                await nextTick();
                bootstrap.Modal.getOrCreateInstance(elMeta).show();

                // Pre-fill metadata dari API jika file (background fetch)
                if (item.type === 'file') {
                    try {
                        const res = await http.get('/metadata', { params: { disk: this.activeDisk, path: item.path } });
                        const d   = res.data.data;
                        if (d) {
                            this.metadataForm.content_type        = d.content_type        ?? '';
                            this.metadataForm.cache_control        = d.cache_control        ?? '';
                            this.metadataForm.content_disposition  = d.content_disposition  ?? '';
                            this.metadataForm.content_encoding     = d.content_encoding     ?? '';
                            this.metadataForm.content_language     = d.content_language     ?? '';
                        }
                    } catch (_) {}
                }

                this.aclFetching = false;
            },

            async doUpdateMetadata() {
                if (!this.metadataTarget) return;
                this.modalLoading = true;

                const isFolder = this.metadataTarget.type === 'folder';
                const endpoint = isFolder ? '/metadata/bulk' : '/metadata';

                const payload = {
                    disk:                this.activeDisk,
                    path:                this.metadataTarget.path,
                    type:                this.metadataTarget.type,
                    content_type:        this.metadataForm.content_type        || null,
                    cache_control:       this.metadataForm.cache_control        || null,
                    content_disposition: this.metadataForm.content_disposition  || null,
                    content_encoding:    this.metadataForm.content_encoding     || null,
                    content_language:    this.metadataForm.content_language     || null,
                    recursive:           this.metadataForm.recursive,
                    extension_filter:    this.metadataForm.extensionFilter,
                    concurrency:         this.metadataForm.concurrency,
                };

                try {
                    if (isFolder) {
                        this.bulkProgress.active  = true;
                        this.bulkProgress.status  = 'processing';
                        this.bulkProgress.message = 'Mengirim request...';
                    }

                    const res  = await http.post(endpoint, payload);
                    const data = res.data.data;

                    if (isFolder && data.job_id) {
                        this._subscribeBulkProgress(data.job_id);
                    } else {
                        this.addNotice('success', res.data.message ?? 'Metadata berhasil diperbarui');
                        bootstrap.Modal.getInstance(document.getElementById('modalMetadata'))?.hide();
                    }
                } catch (err) {
                    this.addNotice('danger', err.response?.data?.message ?? 'Gagal update metadata');
                    this.bulkProgress.active = false;
                } finally {
                    this.modalLoading = false;
                }
            },

            // ─── Reverb bulk progress listener ─────────────

            _subscribeBulkProgress(jobId) {
                // Unsubscribe channel lama jika masih aktif
                if (this.bulkProgress._channel) {
                    try { window.fmReverb.unsubscribe('fm-bulk-progress.' + this.bulkProgress.jobId); } catch (_) {}
                }

                this.bulkProgress.jobId = jobId;
                const channelName = 'fm-bulk-progress.' + jobId;
                const channel     = window.fmReverb.subscribe(channelName);

                this.bulkProgress._channel = channel;

                channel.bind('progress', (data) => {
                    this.bulkProgress.current     = data.current     ?? 0;
                    this.bulkProgress.total       = data.total       ?? 0;
                    this.bulkProgress.percent      = data.percent     ?? 0;
                    this.bulkProgress.message      = data.message     ?? '';
                    this.bulkProgress.currentFile  = data.current_file ?? '';
                    this.bulkProgress.status       = data.status      ?? 'processing';

                    if (data.status === 'done') {
                        this.addNotice('success', data.message || 'Bulk operation selesai');
                        setTimeout(() => {
                            this.bulkProgress.active = false;
                            window.fmReverb.unsubscribe(channelName);
                            // Tutup modal setelah 1.5 detik
                            const permModal = bootstrap.Modal.getInstance(document.getElementById('modalPermission'));
                            const metaModal = bootstrap.Modal.getInstance(document.getElementById('modalMetadata'));
                            permModal?.hide();
                            metaModal?.hide();
                        }, 1500);
                    } else if (data.status === 'error') {
                        this.addNotice('danger', data.message || 'Bulk operation gagal');
                        this.bulkProgress.active = false;
                        window.fmReverb.unsubscribe(channelName);
                    }
                });
            },

            // ─── Context menu ──────────────────────────────

            showCtx(e, item) {
                this.ctxMenu = { show: true, x: e.clientX, y: e.clientY, target: item };
            },
            closeCtx() { if (this.ctxMenu.show) this.ctxMenu.show = false; },

            // ─── Keyboard shortcuts ────────────────────────

            onKey(e) {
                if (e.key === 'Escape') {
                    this.closeCtx();
                    this.lightbox.show = false;
                }
                if (e.key === 'F5') { e.preventDefault(); scBustBrowse(this.activeDisk, [this.currentPath]); this.browse(); }
                if (e.key === 'Delete' && this.selectedItems.size && !e.target.matches('input,textarea')) {
                    this.confirmDeleteSelected();
                }
            },

            // ─── Notice ────────────────────────────────────

            addNotice(type, msg) {
                const id = ++this._nid;
                this.notices.push({ id, type, msg });
                setTimeout(() => this.removeNotice(id), 5000);
            },
            removeNotice(id) {
                const idx = this.notices.findIndex(n => n.id === id);
                if (idx >= 0) this.notices.splice(idx, 1);
            },

            // ─── Helpers ───────────────────────────────────

            fileIcon(type, ext) {
                const map = {
                    image: 'fa-image', video: 'fa-video', audio: 'fa-music',
                    document: 'fa-file-alt', archive: 'fa-file-archive', other: 'fa-file',
                };
                return map[type] || 'fa-file';
            },
            iconColor(type) {
                const map = {
                    image: 'ic-image', video: 'ic-video', audio: 'ic-audio',
                    document: 'ic-document', archive: 'ic-archive', other: 'ic-other',
                };
                return map[type] || 'ic-other';
            },
        },
    });

    app.mount('#fm-app');

})();
