/**
 * filemanager-embed.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Snippet kecil untuk integrasi Arunika File Manager dari CMS / app APAPUN.
 * Tidak butuh Vue, tidak butuh Laravel — cukup include JS ini dan panggil API-nya.
 *
 * CARA PAKAI:
 *  <script src="https://your-arunika.com/assets/js/filemanager-embed.js"></script>
 *
 *  ArunikaFM.init({
 *      url:    'https://your-arunika.com/filemanager',
 *      apiKey: 'your_fm_api_key_here',
 *  });
 *
 *  // Buka sebagai popup window
 *  ArunikaFM.open({ type: 'image' }, function(file) {
 *      console.log(file.url); // URL file yang dipilih
 *  });
 *
 *  // Atau embed sebagai iframe di dalam container
 *  ArunikaFM.embed('#my-container', { type: 'image' }, function(file) {
 *      console.log(file.url);
 *  });
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function (global) {
    'use strict';

    let _config = {
        url:           '',       // URL halaman file manager (filemanager.blade.php)
        apiKey:        '',       // fm_key untuk autentikasi
        width:         1100,
        height:        700,
        popupTitle:    'File Manager',
    };

    let _callbackStore = {};  // { callbackId: Function }
    let _messageListenerAdded = false;

    // ── Internal: listen semua postMessage ─────────────────

    function _ensureListener() {
        if (_messageListenerAdded) return;
        _messageListenerAdded = true;

        window.addEventListener('message', function (e) {
            const d = e.data;
            if (!d || typeof d !== 'object') return;

            // fm:insert → file dipilih
            if (d.type === 'fm:insert') {
                const cb = d.callbackId ? _callbackStore[d.callbackId] : null;
                if (cb) {
                    cb(d.file);
                    delete _callbackStore[d.callbackId];
                }
            }

            // fm:cancel → user menutup tanpa pilih
            if (d.type === 'fm:cancel') {
                if (d.callbackId) delete _callbackStore[d.callbackId];
            }

            // fm:ready → iframe selesai load
            if (d.type === 'fm:ready') {
                // bisa dipakai untuk hide loading state
            }
        });
    }

    // ── Internal: generate callback ID ────────────────────

    function _genId() {
        return 'fmcb_' + Math.random().toString(36).substr(2, 9);
    }

    // ── Internal: build FM URL ─────────────────────────────

    function _buildUrl(options, callbackId) {
        const base   = _config.url.replace(/\/$/, '');
        const params = new URLSearchParams();

        if (_config.apiKey) params.set('fm_key', _config.apiKey);
        if (options.type)   params.set('type', options.type);
        if (callbackId)     params.set('fm_callback', callbackId);

        return base + '?' + params.toString();
    }

    // ══════════════════════════════════════════════════════
    //  PUBLIC API
    // ══════════════════════════════════════════════════════

    const ArunikaFM = {

        /**
         * Inisialisasi config global.
         * Panggil sekali di halaman, biasanya saat page load.
         *
         * @param {Object} cfg
         * @param {string} cfg.url     - URL halaman file manager Arunika
         * @param {string} cfg.apiKey  - API Key (dari tabel fm_api_keys)
         * @param {number} [cfg.width]
         * @param {number} [cfg.height]
         */
        init(cfg) {
            _config = Object.assign(_config, cfg);
            _ensureListener();
        },

        /**
         * Buka file manager sebagai popup window (window.open).
         *
         * @param {Object}   options
         * @param {string}   [options.type]     - Filter tipe: image|video|audio|document|archive
         * @param {Function} callback           - Dipanggil dengan object file saat user memilih
         *
         * @example
         * ArunikaFM.open({ type: 'image' }, function(file) {
         *     document.getElementById('imgUrl').value = file.url;
         * });
         */
        open(options, callback) {
            _ensureListener();

            const cbId = _genId();
            if (typeof callback === 'function') {
                _callbackStore[cbId] = callback;
            }

            const url    = _buildUrl(options || {}, cbId);
            const w      = _config.width;
            const h      = _config.height;
            const left   = Math.max(0, (screen.width  - w) / 2);
            const top    = Math.max(0, (screen.height - h) / 2);
            const specs  = `width=${w},height=${h},left=${left},top=${top},resizable=yes,scrollbars=no`;

            const popup = window.open(url, 'ArunikaFM_' + cbId, specs);
            if (!popup) {
                alert('Popup diblokir oleh browser. Izinkan popup untuk domain ini.');
                delete _callbackStore[cbId];
            }
            return popup;
        },

        /**
         * Embed file manager sebagai iframe di dalam sebuah container.
         *
         * @param {string|Element} container  - CSS selector atau DOM element
         * @param {Object}         options
         * @param {string}         [options.type]    - Filter tipe file
         * @param {string}         [options.height]  - Tinggi iframe, default '500px'
         * @param {Function}       callback          - Dipanggil dengan object file saat user memilih
         *
         * @example
         * ArunikaFM.embed('#fm-container', { type: 'image', height: '600px' }, function(file) {
         *     console.log(file.url);
         * });
         */
        embed(container, options, callback) {
            _ensureListener();

            const el = typeof container === 'string'
                ? document.querySelector(container)
                : container;

            if (!el) {
                console.error('ArunikaFM.embed: container tidak ditemukan', container);
                return;
            }

            const cbId = _genId();
            if (typeof callback === 'function') {
                _callbackStore[cbId] = callback;
            }

            const url    = _buildUrl(options || {}, cbId);
            const height = (options && options.height) ? options.height : '500px';

            // Hapus iframe lama jika ada
            const old = el.querySelector('iframe[data-arunika-fm]');
            if (old) old.remove();

            const iframe = document.createElement('iframe');
            iframe.setAttribute('data-arunika-fm', '1');
            iframe.src             = url;
            iframe.style.width     = '100%';
            iframe.style.height    = height;
            iframe.style.border    = 'none';
            iframe.style.display   = 'block';
            iframe.allowFullscreen = true;

            el.appendChild(iframe);
            return iframe;
        },

        /**
         * Destroy / hapus semua iframe yang di-embed oleh ArunikaFM.
         *
         * @param {string|Element} container
         */
        destroy(container) {
            const el = typeof container === 'string'
                ? document.querySelector(container)
                : container;
            if (!el) return;
            el.querySelectorAll('iframe[data-arunika-fm]').forEach(f => f.remove());
        },

        /**
         * Kirim perintah ke iframe yang sudah di-embed.
         * Contoh: ganti filter tipe file tanpa reload iframe.
         *
         * @param {string|Element} container
         * @param {Object}         message   - { type: 'fm:set_filter', fileType: 'image' }
         */
        sendMessage(container, message) {
            const el = typeof container === 'string'
                ? document.querySelector(container)
                : container;
            if (!el) return;
            const iframe = el.querySelector('iframe[data-arunika-fm]');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.postMessage(message, '*');
            }
        },

        // ── CKEditor 4 helper ──────────────────────────────

        /**
         * Gunakan sebagai filebrowserBrowseUrl di CKEditor 4.
         * Akan di-append otomatis oleh CKEditor dengan &CKEditorFuncNum=xxx.
         *
         * CKEDITOR.replace('editor', {
         *     filebrowserBrowseUrl:      ArunikaFM.ck4Url(),
         *     filebrowserImageBrowseUrl: ArunikaFM.ck4Url({ type: 'image' }),
         * });
         */
        ck4Url(options) {
            const base   = _config.url.replace(/\/$/, '');
            const params = new URLSearchParams();
            if (_config.apiKey) params.set('fm_key', _config.apiKey);
            if (options && options.type) params.set('type', options.type);
            return base + '?' + params.toString();
            // CKEditor akan otomatis append &CKEditorFuncNum=xxx
        },

        // ── TinyMCE helper ─────────────────────────────────

        /**
         * Buat handler untuk file_picker_callback TinyMCE.
         *
         * tinymce.init({
         *     file_picker_callback: ArunikaFM.tinymceCallback(),
         * });
         */
        tinymceCallback() {
            return function (callback, value, meta) {
                const type = meta.filetype === 'image' ? 'image'
                           : meta.filetype === 'media' ? 'video'
                           : '';

                ArunikaFM.open({ type }, function (file) {
                    if (meta.filetype === 'image') {
                        callback(file.url, { title: file.original_name, width: file.meta?.width, height: file.meta?.height });
                    } else {
                        callback(file.url, { title: file.original_name });
                    }
                });
            };
        },
    };

    // Export ke global
    global.ArunikaFM = ArunikaFM;

})(window);
