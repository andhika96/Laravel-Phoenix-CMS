# Template Options Revamp Implementation Plan

> For agentic workers: gunakan superpowers:executing-plans untuk menjalankan task satu per satu setelah persetujuan implementasi. Jangan membuat task, branch, commit, atau subagent tanpa kebutuhan dan otorisasi yang sesuai.

**Goal:** Mengimplementasikan modal Template Options berdasarkan concept 03, dengan navigasi kategori, setting ringkas, dan preview draft terisolasi tanpa kehilangan kontrol aktif.

**Architecture:** Pertahankan Vue CDN, Bootstrap Modal, state optionsModal.value, draft halaman, normalizer PHP, dan renderer preview yang sudah ada. Navigasi mengganti panel tengah dalam modal yang sama. Preview modal membaca salinan opsi yang belum di-Apply, bukan memutasi draft halaman.

**Tech Stack:** Blade, Vue 3 CDN, Bootstrap Modal, CSS lokal, Coloris lokal, Node built-in tests, Laravel Feature tests. Tidak ada dependency baru.

**Spec:** D:\Laragon\www\laravel-13-phoenix\project-artifacts\mockups\template-options-20260905\template-options-concept-03-modal-radius.png dan kontrak UX di dokumen ini. Ini gambar terbaru yang disetujui, bukan concept 01 atau 02. Desain gambar disetujui; kontrak interaksi ini masih usulan untuk persetujuan sebelum implementasi.

## Bukti dan batasan perencanaan

- Tanggal: 2026-09-05; project D:\Laragon\www\laravel-13-phoenix.
- Memori historis: E:\AI\Memories\20260825_232054_laravel13_phoenix_manage_event_article_template_options_handoff.md, bagian Template Options; diverifikasi kembali terhadap source aktif.
- Git worktree dirty, termasuk seluruh file utama target. Semua perubahan lama wajib dipertahankan.
- Graph tersedia, mtime 2026-09-05 05:22:13. Query lokal `graphify query 'ManageArticleTemplateController ArticleTemplateOptions preview template options' --budget 1200` menemukan dua class relevan, juga beberapa hasil heuristik tidak relevan yang diabaikan. Tidak menyimpulkan relasi dari hasil tersebut; freshness penuh graph belum diverifikasi. Graph tidak diperbarui untuk pekerjaan planning.
- Source aktual: modal memakai border:0, border-radius:1rem dan shadow 0 24px 64px rgb(16 24 40 / .2). Angka CSS ini bukan hasil pengukuran computed style browser; theme, root font-size dan zoom perlu diverifikasi sebelum implementasi. Jangan mengganti radius dengan perkiraan pixel dari mockup.
- openTemplateOptions membuat clone; Apply memindahkan clone ke draft dan menutup modal; save() mengirim draft. Preview controller menerima template_options dan memakai normalizer serta fixture. Belum ada live draft preview di dalam modal.
- Source saat ini memiliki Post list spacing; jangan memindahkannya menjadi Popular Posts spacing berdasarkan screenshot lama. Grid columns bersifat conditional. Detail header juga punya kontrol dynamic/custom yang harus dipertahankan.
- Belum dilakukan runtime QA atau pengujian; tidak ada source aplikasi diubah dalam tahap perencanaan.

## Kontrak UX

### Navigasi

- Klik Customize item hanya mengganti panel tengah; tidak membuka route, tab browser, modal kedua atau halaman baru.
- Default Header content pada setiap pembukaan modal. Pertahankan seluruh nilai sementara ketika berpindah kategori; reset scroll panel tengah ke atas saat kategori berubah.
- Navigasi kiri dan footer tetap; panel tengah dan viewport preview memiliki scroll masing-masing. Preview tidak reload hanya karena berpindah kategori.
- Gunakan vertical tabs dengan aria-selected, aria-controls, tabpanel, roving tabindex dan tombol panah/Home/End. Mobile memakai select berlabel untuk kategori, bukan deretan tab terpotong.
- Coloris diinisialisasi setelah panel yang relevan muncul; popup tidak boleh terpotong, tertutup footer, atau kehilangan keyboard focus.

### Kategori dan cakupan

| Kategori | Isi | Kondisi |
|---|---|---|
| Header content | Eyebrow, Title, Description, enabled/text dan dynamic/custom yang sudah tersedia | Archive/detail sesuai kontrak aktif |
| Archive toolbar | Search, alignment, Category filter, mode Button list/Form select sesuai template | Archive |
| Post list | Post list spacing dan unit | Minimal Reading List |
| Reading list sidebar | Show sidebar, Categories, Popular Posts, Stay/Sticky masing-masing | Minimal Reading List |
| Grid columns | Jumlah kolom Desktop/Tablet/Mobile | Hanya template dengan opsi grid |
| Thumbnail | Display mode, fit, background, frame override | Archive |
| Pagination | Total data, alignment, frame, warna, border, radius, padding/margin responsif | Archive |
| Article title | Heading tag H1-H6 | Archive |
| Archive shell / Detail shell | Padding, margin responsif, frame override | Surface terkait |

- Menu diturunkan dari surface/template/schema aktual; jangan tampilkan kontrol tidak didukung dan jangan memaksa tujuh menu mockup sebagai daftar tetap.
- Label/conditional lama dipertahankan, termasuk alignment category yang bergantung mode. Toggle off menyembunyikan atau menonaktifkan kontrol turunannya tanpa menghapus nilai yang disimpan sementara.
- Tidak ada fitur styling baru; ini reorganisasi kontrol yang ada.

### Ukuran dan responsive

- Desktop >=1280 CSS px: modal near-full-width, bukan edge-to-edge. Usulan width min(1680px, calc(100vw - 48px)), max-height calc(100dvh - 48px).
- Desktop layout: navigasi 216px; setting clamp(360px, 32vw, 480px); preview minmax(0,1fr). Header dan footer di luar area scroll; min-height:0 untuk region scroll.
- 768-1279px: lebar calc(100vw - 32px), navigasi sekitar 200px, area kanan memakai switch Settings/Preview. Jangan memaksakan tiga kolom sempit.
- <768px: lebar calc(100vw - 16px), max-height calc(100dvh - 16px), dropdown kategori, switch Settings/Preview, satu panel terlihat. Pertahankan rounded shell dan margin kecil; tidak wajib fullscreen tanpa radius.
- Nilai breakpoint adalah proposal, sesuaikan hanya berdasarkan QC overflow/zoom/label panjang. Header/footer tidak boleh memotong kontrol pada landscape atau layar pendek.
- Border/radius/shadow modal dan radius tombol mengikuti cascade CMS aktual. Pertahankan rem, theme token, ph-modal-dialog, ph-btn-theme; jangan mengubah CSS modal global atau hardcode emerald jika theme aktif berbeda.

### Preview

- Satu renderer: gunakan endpoint/Blade preview existing dengan template_options dari optionsModal.value. Tidak membangun ulang frontend dengan HTML tiruan atau menyuntik opsi mentah ke halaman publik.
- Tambahkan state URL/loading/error/device/scale milik preview modal; draft utama dan iframe belakang tidak diubah saat mengetik.
- Gunakan debounce 350ms untuk pembaruan draft; cancel timer saat tutup. Gunakan mekanisme last-request-wins untuk navigasi preview, agar hasil lama tidak menggantikan draft terbaru. Tidak ada request save dalam watcher.
- Debounce meliputi color picker dan perubahan numeric. Perpindahan kategori tidak memicu request; pada mode Settings tablet/mobile, tunda render sampai Preview terlihat dan gunakan nilai paling baru.
- Gunakan profil existing Desktop 1440x900, Tablet 834x1112, Mobile 390x844. Ukuran viewport iframe tetap sesuai device, diskalakan ke area tersedia; tidak memakai lebar kolom sebagai breakpoint frontend.
- Device preview dan device editor responsive disinkronkan di modal, terpisah dari pilihan device halaman utama. Perpindahan device tidak menyalin nilai antar breakpoint.
- Tampilkan label Scaled to fit dan Sample content. Live berarti opsi terbaru dirender otomatis; artikel tetap fixture endpoint saat ini, bukan klaim data live database.
- Tampilkan loading non-blocking dan timeout/error dengan Retry; draft input tetap dapat diedit. Validasi redirect login/failure tidak dianggap preview sukses. Reuse mekanisme yang ada sejauh benar; hindari kompleksitas transport baru tanpa bukti perlu.
- Navigasi artikel dalam preview tidak boleh mengeluarkan admin dari editor atau menulis data. Audit perilaku link/filter/pagination existing sebelum memutuskan pembatasan lokal.
- Uji panjang query template_options pada payload maksimal. Jika batas nyata ditemukan, baru proposal transport alternatif read-only; tidak membuat endpoint baru secara spekulatif.
- Scroll sticky sidebar diuji dalam viewport preview dan halaman publik. Jangan menjanjikan mempertahankan interaksi filter/scroll setelah rerender jika belum diimplementasikan; default preview dapat kembali ke posisi awal setelah perubahan opsi.

### Apply, Cancel, Close, Save

1. Open: clone opsi template dari draft halaman ke optionsModal.value.
2. Edit: hanya salinan modal berubah; live preview modal mengikuti salinan.
3. Apply changes: clone seluruh kategori ke draft halaman, refresh preview halaman, tutup modal. Belum menyimpan database.
4. Save Template di halaman manager: alur existing untuk persist database.
5. Cancel/X/Escape: buang hanya perubahan sesi modal; draft halaman sebelum Open tetap utuh. Semua jalur hidden.bs.modal membersihkan timer/observer/state dan mengembalikan focus ke tombol pembuka.
6. Usulan backdrop static untuk menghindari tutup tak sengaja. Jika sesi modal dirty, Cancel/X/Escape menampilkan konfirmasi inline dalam footer (bukan nested modal): Keep editing / Discard changes. Tidak ada konfirmasi bila belum berubah.
7. Footer menjelaskan Apply belum menyimpan permanen; jangan ubah arti tombol menjadi autosave atau publish.

## Spesifikasi kontrol UI/UX

Spesifikasi berikut adalah usulan implementasi, bukan klaim bahwa semua state sudah tergambar pada concept 03. Gunakan komponen Bootstrap/CMS existing dan elemen native; tidak menambah select library atau sistem komponen baru.

### Token bersama

- Tinggi baseline kontrol 2.75rem mengikuti variabel existing --article-template-control-height; tombol utama setara. Jangan menimpa skala font responsive pengguna.
- Radius input/select/button mengambil CSS/token CMS aktif setelah pemeriksaan computed style. Radius kontrol tidak disamakan dengan radius luar modal. Hindari angka pixel perkiraan dari image generation.
- Label terlihat di atas input, helper text di bawah bila perlu. Gap label-input sekitar .5rem; gap antar field 1-1.25rem. Label toggle di kiri, switch di kanan baris yang sama.
- Border default tipis netral, background putih/surface theme, text mengikuti theme. Hover sedikit memperjelas border; focus-visible ring jelas menggunakan warna theme dan memenuhi kontras; error punya border serta pesan teks, tidak hanya warna.
- Disabled tetap terbaca, tidak sekadar opacity rendah. Jelaskan ketergantungan bila tidak jelas. Read-only berbeda dari disabled; tidak menyembunyikan data tanpa alasan.
- Ukuran target interaksi minimal 44x44 CSS px melalui area label/tombol walau switch/checkbox visual lebih kecil. Native keyboard dan label association dipertahankan.

### Text input dan textarea

- Input full width panel, rounded, label di atas. Textarea minimal tiga baris, resize vertikal saja agar tidak merusak grid.
- Placeholder hanya contoh, bukan pengganti label. Toggle Header off menonaktifkan field dan mengecualikannya dari render tanpa menghapus teks sementara.
- Jangan menambah batas karakter baru tanpa kontrak backend. Validation message muncul dekat field dan dapat dibaca screen reader.

### Form select

- Gunakan native select berkelas form-select CMS: field putih rounded, selected text di kiri, chevron kecil di kanan, padding kanan cukup, tinggi sama dengan input.
- Berlaku untuk Display mode, Image fit, Category filter style, Stay/Sticky, Heading level dan unit. Select biasa full width; unit select compact melekat pada numeric input.
- Tidak menambah searchable select untuk 2-6 pilihan. Tidak membuat dropdown custom hanya agar popup tampak identik gambar: popup native dapat berbeda antar OS/browser, sedangkan field tertutup distyling konsisten.
- Tab untuk focus; keyboard bawaan select untuk memilih. Perubahan pilihan segera mengubah draft, preview mengikuti debounce.

### Switch, checkbox, radio

- Switch khusus boolean show/enable: Eyebrow, Title, Description, Search, Category filter, Sidebar, Categories, Popular Posts, Show total, frame/padding/margin override.
- Switch visual berupa track pill dengan thumb putih; ON memakai warna theme, OFF netral. Label klik men-toggle. State harus tetap dikenali melalui posisi thumb/native checked, bukan warna saja.
- Checkbox dipakai hanya jika ada opsi existing yang bermakna pilihan independen multi-select. Tampilan kotak rounded kecil dengan checkmark putih saat aktif; label klik dan Space bekerja. Jangan menambah checkbox fiktif atau mengganti seluruh toggle menjadi checkbox.
- Radio dipakai untuk tepat satu pilihan dalam satu kelompok. Untuk alignment Left/Center/Right gunakan segmented radio group rounded dengan icon dan label; satu aktif, keyboard panah/native radio semantics, focus ring terlihat.
- Radio biasa, bila memang dibutuhkan oleh opsi existing, berupa lingkaran dengan titik aktif. Jangan mengganti Stay/Sticky atau select lain tanpa alasan hanya demi memakai semua jenis kontrol.
- Fieldset/legend memberi nama kelompok radio. Tab perangkat preview adalah pemilih viewport, bukan checkbox; pertahankan semantics tab atau aria-pressed yang konsisten dengan fungsinya.

### Segmented controls dan responsive

- Container rounded dengan latar netral lembut; active segment memakai warna theme ringan/kontras, tidak garis kotak hitam. Label Left/Center/Right tetap ada agar icon tidak ambigu.
- Desktop/Tablet/Mobile memakai icon+label pada ruang cukup; di ruang sempit icon boleh berdiri sendiri dengan accessible name dan tooltip, bukan menghilangkan fungsi.
- Jika label panjang, gunakan layout vertikal/wrap yang disengaja; jangan mengecilkan teks hingga tidak terbaca.

### Numeric, unit, spacing dan warna

- Numeric+unit satu compound control: angka di kiri, divider halus, select unit di kanan, hanya sudut terluar rounded. Native min/max/step dan validasi existing tetap dipakai; border width tetap tidak menerima %.
- Padding/margin menampilkan empat input Top/Right/Bottom/Left dan tombol Link/Unlink dengan state eksplisit. Layar sempit memakai grid 2x2. Nilai device yang tidak sedang dipilih tidak berubah.
- Color memakai Coloris lokal existing: swatch rounded kecil + teks nilai warna dalam satu field. Pertahankan format/clear behavior yang didukung, validasi normalizer, dan keyboard access.
- Popup Coloris harus di atas modal, tidak dipotong overflow, dan dapat ditutup tanpa membuang draft. Inisialisasi ulang setelah panel ditampilkan bila dibutuhkan.

### Disclosure, tombol dan pembuktian visual

- Kontrol frame/padding/margin lanjutan muncul tepat di bawah toggle induknya. Jangan membuat accordion tambahan jika toggle sudah cukup.
- Cancel menggunakan btn-secondary CMS; Apply changes memakai ph-btn-theme. Disabled/loading memiliki state dan teks yang jelas tanpa pergeseran layout. Tidak meratakan kedua tombol menjadi primary.
- Sebelum integrasi source, prototype terisolasi di project-artifacts/mockups perlu memperlihatkan Header, Archive toolbar, Reading list sidebar dan satu panel styling (Pagination) agar select, segmented radio, switch, numeric+unit, Coloris dan responsive box dapat dinilai.
- Tambahkan halaman/section contoh state default, hover, focus, disabled, error pada prototype saja bila dibutuhkan untuk QC; jangan memasukkan kontrol contoh ke aplikasi produksi.
- Bukti browser: mouse/keyboard, Tab/Space/arrows/Escape, label klik, popup select di browser target, Coloris focus/layering, touch target, theme aktif, zoom200%, tampilan mobile. Checkbox/radio yang tidak dipakai di produksi tidak perlu dibuat semata-mata untuk demo.

## File target

- Modify resources/views/manage_article/templates/index.blade.php: modal shell, tab navigation, panel grouping, preview container, footer.
- Modify resources/views/manage_article/templates/partials/options-styling.blade.php: gate kategori styling dan compact controls, mempertahankan bindings.
- Modify public/assets/css/article/article-template-manager-2026.css: scoped layout/responsive/scroll; preserve CMS radius dan token.
- Modify public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js: section state, isolated modal preview, lifecycle cleanup, dirty cancel.
- Extend tests/manage-article-template-manager.test.mjs; tests/article-template-presentation.test.mjs bila sesuai kontrak markup/CSS.
- Read/reuse app/Support/Article/ArticleTemplateOptions.php; app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php; resources/views/manage_article/templates/preview.blade.php. Tidak ditargetkan berubah kecuali bukti gap diperlukan untuk scope.
- Tidak ada perubahan schema, migrations, Page Builder, theme global, atau desain artikel publik.

## Task 1 — Baseline dan kontrak

- [ ] Cek ulang status/diff file target dan AGENTS relevan; telusuri semua caller helper yang akan diubah.
- [ ] Verifikasi computed radius/border/shadow/font-size/tombol modal aktif di browser read-only. Simpan screenshot dan ukuran viewport/zoom.
- [ ] Inventaris semua kontrol dan conditional per template/surface, termasuk Post list dan grid; matriks sebelum/sesudah wajib identik cakupannya.
- [ ] Jalankan baseline Node manager/presentation dan Laravel Article tests terkait; pisahkan kegagalan existing.
- [ ] Backup setiap file existing sebelum edit ke project-artifacts/backups/<timestamp>-template-options-revamp dengan struktur path dan manifest hash. Jangan memakai backup sebagai source.

## Task 2 — Navigasi dan shell responsive

- [ ] Tambahkan failing tests menu conditional, default section, perpindahan section tidak memutasi opsi atau reload preview, binding semua kontrol tetap ada.
- [ ] Implementasikan navigation/panels dalam markup existing; tambah partial hanya jika benar-benar mengurangi kerumitan, bukan satu file per kontrol.
- [ ] Implementasikan scoped grid, scroll dan responsive; radius mengikuti nilai CMS terverifikasi.
- [ ] Verifikasi keyboard, focus, label, zoom 200%, Coloris, dan kontrol four-side linked/unlinked. Jalankan ulang test terkait.

## Task 3 — Isolated live draft preview

- [ ] Tambahkan failing tests URL memakai modal clone, draft/saved tidak berubah, debounce, latest draft, device sizing, timer cleanup, loading/error/retry.
- [ ] Reuse builder URL/renderer/normalizer; minimum refactor helper agar dapat menerima opsi sumber tanpa menduplikasi renderer.
- [ ] Bind modal preview dan ResizeObserver hanya saat modal terlihat; cegah observer/state halaman utama saling menimpa.
- [ ] Uji rapid typing/color changes, close while loading, reopen/template switch, login expiry, viewport switch dan payload terbesar.
- [ ] Jalankan tests; pastikan tidak ada POST save saat edit/preview.

## Task 4 — Apply dan dismissal

- [ ] Tambahkan failing tests edit di beberapa kategori -> Apply seluruhnya, Cancel menjaga draft lama, reopen bersih, Escape/backdrop/hidden lifecycle, pending debounce dibatalkan.
- [ ] Implementasikan Apply existing dan dirty dismissal inline. Pastikan hanya save() existing menulis database.
- [ ] Verifikasi perubahan responsif tidak bocor antar device/template/surface.

## Task 5 — QC akhir dan handoff

- [ ] Run `node --test tests/manage-article-template-manager.test.mjs tests/article-template-presentation.test.mjs tests/article-template-preview-fixture.test.mjs`.
- [ ] Run `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`.
- [ ] Run `php artisan test --compact tests/Feature/Article` lalu bandingkan baseline.
- [ ] Run `php artisan view:cache` dan `git diff --check`; tidak menambah build pipeline baru untuk asset CDN/static.
- [ ] Browser QC 1920,1440,1280,1024,768,390,320px dan layar pendek/zoom200%; no horizontal overflow, footer tidak overlap, round corners cocok CMS, console bersih.
- [ ] Uji seluruh kategori yang tersedia, semua archive/detail template, kontrol off/on, responsive, preview failure/retry.
- [ ] Pengujian Apply/Save dan persistence hanya di konteks test aman atau setelah izin eksplisit browser write. Jika tidak diizinkan, laporkan persistence runtime belum diverifikasi; jangan mengganti dengan klaim test statis.
- [ ] Hard reload editor dan frontend; bandingkan output renderer, bukan hanya panel cantik. Screenshot dan laporan QC di project-artifacts/qa/<timestamp>-template-options-revamp.
- [ ] Setelah pekerjaan source benar-benar selesai dan tes tervalidasi, update Graphify incremental lokal sesuai scope, tanpa backup/artifacts/vendor/node_modules/generated/secrets; tidak commit graph. Jika tool tidak mendukung scope aman, laporkan sebelum rebuild luas.
- [ ] Laporan akhir: perubahan, backup, baseline vs hasil tes, runtime/static/unverified, status graph; tidak commit/push atau cleanup perubahan pengguna.

## Definition of done

- Semua opsi existing tetap dapat diakses dengan state dan conditional yang benar.
- Customize mengganti panel dalam satu modal; tidak ada nested modal/router baru.
- Live preview mengikuti salinan modal tanpa save, dengan ukuran device asli dan renderer existing.
- Apply/Cancel/Save terpisah dan diuji; dismissal tidak membocorkan draft sementara.
- Bentuk luar modal memakai CSS CMS yang diverifikasi, bukan estimasi gambar.
- Responsive dan accessibility terverifikasi; tidak ada overlap, overflow atau Coloris terpotong.
- Tidak ada klaim verifikasi melebihi bukti, dan perubahan lama pengguna tetap utuh.

## Status eksekusi 2026-09-05

Plan ini telah dieksekusi pada source aktif dan dilanjutkan dengan revisi UI/UX berdasarkan review visual pengguna.

| Area | Status | Bukti / batasan |
|---|---|---|
| Baseline, memory, Graphify, dan backup | Selesai | Source aktif diverifikasi; backup bertimestamp tersedia di `project-artifacts/backups/`. |
| Navigasi dan shell modal | Selesai secara source/test | 59 test Node terkait lulus; browser manager live masih berhenti di `/auth/login`. |
| Isolated draft preview | Selesai secara source/test | URL, debounce, latest-request lifecycle, device profile, error/retry, dan no-save watcher diuji statis/unit. |
| Apply, Cancel, dismissal | Selesai secara source/test | Clone, dirty state, discard, cleanup, dan Apply-to-draft diuji; live Apply/Save sengaja tidak dijalankan. |
| Category filter conditional | Selesai | OFF tidak merender control turunan; ON merender Position dan Filter style vertikal. |
| Frame field layout | Selesai | Border color, Border width, Border radius, Background color satu row penuh. |
| Page Builder-style Border radius | Selesai | Four-corner values, unit row, link/unlink, backward-compatible single value, normalizer, preview, dan renderer path. |
| Compact V3 UI/UX | Selesai secara source/artifact | Typography cap, flat hierarchy, dividers, responsive mockup, dan 9 screenshot preview. |
| Automated QA | Selesai | 59 Node tests, 30 Article Feature tests / 411 assertions, JS syntax, Blade cache, diff check. |
| Authenticated browser matrix | Pending external session | Memerlukan browser yang sudah login untuk computed style, keyboard/modal click-through, Coloris layering, dan zoom/device matrix. |
| Graphify final | Selesai | Incremental update: 21,735 nodes, 39,954 edges; `graphify check-update .` exit code 0. |

The only remaining boundary is authenticated live-browser verification. It does not block the source/test implementation, but it must be checked before claiming full visual runtime parity.

## Scroll correction — 2026-09-05

The latest settings-panel scroll report was traced to a selector/markup mismatch, not to Vue state or the form controls. The `article-template-options-modal` sizing hook was attached to `.modal-dialog` while the CSS expected it on the outer `.modal` wrapper. The hook is now on the wrapper, so the existing descendant selectors constrain the dialog and allow the settings panel to scroll independently across sections and breakpoints.

Regression evidence is recorded in `project-artifacts/qa/20260905-template-options-scroll-fix/QA-runtime-20260905.md`. The corrected production-shaped fixture verified desktop mouse-wheel scrolling, mobile programmatic scrolling, no horizontal overflow, and a clean console. The authenticated manager browser matrix remains the only external runtime boundary.

Graphify was intentionally not updated for this correction because the user explicitly requested that coding and design work not update graph data.

## Preview stage radius removal and Minimal Reading List options audit — 2026-09-05

The radius was removed only from the two live-preview stage wrappers (`article-template-manager__device-stage` and `article-template-options-preview__stage`) and their iframe inheritance. Modal shell radius and template-owned frame radius remain available. A browser fixture measured both stages and both iframes at `border-radius: 0px` with no overflow or console warnings.

The full Minimal Reading List option matrix was traced from manager controls through the draft URL, `ManageArticleTemplateController::preview()`, `ArticleTemplateOptions`, the shared archive Blade renderer, SSR pagination/Vue pagination, and the public `ArticleFrontendController` archive/listdata path. Header, toolbar and both category modes, post-list spacing, sidebar visibility/position, thumbnail mode/fit/background/frame/height, pagination, article title, and shell settings are covered by the integration regression. Evidence is in `project-artifacts/qa/20260905-template-options-radius-audit/QA-template-options-radius-audit-20260905.md`.

Fresh verification: 98 Node tests, 32 Article feature tests with 476 assertions, and 2 preview-controller tests with 72 assertions passed. Graphify was intentionally not updated because the user explicitly requested that coding and design work not update graph data.

## Switch thumb fit — 2026-09-05

The compact Template Options switch retains the user-approved outer `44 × 24px` geometry. Its Bootstrap SVG background image was increased from `1rem` to `1.25rem`, producing an approximately `15px` visible white circular thumb after the SVG viewBox padding is accounted for. This makes the thumb visually fit the track without enlarging the control or changing its interaction.

Fresh fixture QA verified the exact Header content switch at desktop and 390px mobile: the outer control remains `44 × 24px`, the image resolves to `20 × 20px`, and the mobile layout has no horizontal overflow. Regression results and screenshots are recorded in `project-artifacts/qa/20260905-template-options-scroll-fix/QA-switch-thumb-fit-20260905.md`.

Graphify was intentionally not updated for this correction because the user explicitly requested that coding and design work not update graph data.

## Checkbox Inspector parity — 2026-09-05

The user supplied an Inspect Element reference for Template Options switches. The source now follows it exactly: `width` and `min-width` are `2.75rem !important`, height is `1.5rem !important`, margin remains `0 0 0 auto !important`, border radius is `2rem`, and background-size is `1.25rem 1.25rem` so the visible SVG thumb fits the approved track.

Fresh browser evidence on the exact Header content DOM confirmed a 44 × 24px switch at desktop and mobile, right-aligned in its flex label, with no horizontal overflow. Full evidence and test results are in `project-artifacts/qa/20260905-template-options-scroll-fix/QA-checkbox-inspector-44x24-20260905.md`.

Graphify was intentionally not updated for this correction because the user explicitly requested that coding and design work not update graph data.

## Four UI corrections — 2026-09-05

The latest Template Options pass applies four focused corrections while preserving all existing Vue 3 CDN behavior: Header Content description textareas now use ten rows; Archive toolbar Search position buttons use the active theme accent on hover/focus; Minimal Reading List Category filter Position and Category filter style controls are explicitly left-aligned; and Header fields, option rows, nested sidebar items, and compound form groups use a consistent bottom-divider language. The V3 mockup was updated in parallel and all nine desktop state screenshots were regenerated.

Source/test/browser evidence is recorded in `project-artifacts/qa/20260905-template-options-four-ui-fixes/QA-four-ui-fixes-20260905.md`. Full regression is green: 91 Node tests, 30 Article Feature tests with 411 assertions, JS syntax check, Blade view cache, and scoped diff check. Graphify was intentionally not updated because the user requested no graph updates during coding/design.

## Toolbar hover cascade correction — 2026-09-05

The Archive toolbar hover defect was caused by the options modal being teleported to `body`: the modal no longer inherited `--article-template-accent` from `.article-template-manager`. The modal now declares the CMS-backed accent token itself, so the hovered Search position button remains visible and uses the active theme color. Category dependent selects also explicitly start at the left edge and stretch within their settings column.

The corrected teleported-modal fixture verified contiguous three-button geometry and theme-green hover at 640px and 390px with no horizontal overflow. Evidence is in `project-artifacts/qa/20260905-template-options-toolbar-hover-fix/QA-toolbar-hover-cascade-20260905.md`; current regression is 92 Node tests and 30 Article Feature tests with 411 assertions. Graphify was intentionally not updated because the user requested no graph updates during coding/design.

## Category rail, Search visibility, and heading semantics — 2026-09-05

The Category filter control wrapper no longer renders the accent-colored left rail or its extra left padding; both dependent selects now start at the left edge while retaining full-width controls. The toolbar `v-if` now includes the owning field's enabled state, so turning Search off removes its position button group automatically. All settings section titles were changed from `strong` to semantic `h5` elements, with the heading-to-fields margin set to `1.8rem` to match the inspected reference.

Read-only fixture evidence at 640px and 390px verified the h5/margin contract, zero Category rail, left-aligned selects, theme-green hover, Search OFF hidden state, and no horizontal overflow. Evidence is in `project-artifacts/qa/20260905-template-options-heading-visibility-fix/QA-heading-category-search-20260905.md`. Current regression is 93 Node tests and 30 Article Feature tests with 411 assertions. Graphify was intentionally not updated because the user requested no graph updates during coding/design.

## Box-control rail removal — 2026-09-05

The same accent rail issue also affected Padding and Margin in Pagination and Archive Shell because both use the shared `.article-template-box-control`. The shared control now removes its left inset and left border while retaining the bottom divider and all existing four-side form-group behavior. Desktop/mobile fixture evidence and regression results are recorded in `project-artifacts/qa/20260905-template-options-box-control-rail-removal/QA-box-control-rail-20260905.md`. Graphify was intentionally not updated because the user requested no graph updates during coding/design.

## Spacing device-tab removal — 2026-09-05

The repeated Desktop, Tablet, and Mobile controls were removed from every Padding and Margin form group in Pagination and Archive Shell. The existing global preview device selector and `optionsDevice` value/unit handlers remain, so responsive setting data is still preserved without duplicating three buttons inside every spacing group. Pagination and Archive Shell mockup screenshots were refreshed. Evidence is in `project-artifacts/qa/20260905-template-options-hide-spacing-device-tabs/QA-spacing-device-tabs-20260905.md`. Graphify was intentionally not updated because the user requested no graph updates during coding/design.

## Thumbnail audit, background height, and responsive article list — 2026-09-05

The Thumbnail audit confirmed the existing mode split: Background image uses the shared CSS background path, while Full asset image renders a real `<img>` with the selected object-fit. A normalized `thumbnail.height` setting was added and is shown only for Background image mode. Minimal Reading List defaults to `9.3rem` to preserve its desktop ratio; asset mode does not receive the height variable.

Minimal Reading List now has a dedicated responsive article-list treatment: tablet rows use a reduced media column and excerpt density, while mobile changes to a full-width 16:9 image with the article content below it. Pagination, sidebar, Vue slots, and filter behavior remain unchanged. The responsive mockup, production-shaped fixture, test evidence, and limitations are in `project-artifacts/qa/20260905-template-options-thumbnail-responsive/QA-thumbnail-height-responsive-20260905.md`. Graphify was intentionally not updated because the user requested no graph updates during coding/design.

## Form rhythm and radius correction — 2026-09-05

The latest visual report was traced to two CSS cascade defects and an over-compressed form rhythm. Toolbar grid controls still inherited `justify-content: flex-end`, causing max-content widths instead of full-width controls. The generic frame `.form-select` rule also overrode the fixed Border Radius unit width, forcing the unit select to expand and wrap the label.

The final correction restores a 16px setting-group rhythm and 12px related-control rhythm while preserving the requested two-step compact control sizes. Toolbar controls now fill their settings column; Radius units remain 68px with a no-wrap label; all mockup states were refreshed at 1440px. Browser CSS fixture, regression test, and full-suite evidence are recorded in `project-artifacts/qa/20260905-template-options-scroll-fix/QA-form-rhythm-radius-20260905.md`.

Graphify was intentionally not updated for this correction because the user explicitly requested that coding and design work not update graph data.

## Two-step compact density — 2026-09-05

Following explicit user feedback, the control scale was compacted by two levels rather than one: inputs/selects/link controls are now 36px, action buttons 34px, unit selectors 68px, and switches 46 × 24px. Labels remain readable and the control behavior remains unchanged.

Mobile radius fields were also corrected during visual QA so all four values remain a 2×2 group and the chain link stays in the bottom-right edge column. The two-step evidence, regression results, screenshots, and backup paths are in `project-artifacts/qa/20260905-template-options-scroll-fix/QA-compact-density-v2-20260905.md`.

Graphify was intentionally not updated for this correction because the user explicitly requested that coding and design work not update graph data.

## Compact control density — 2026-09-05

The Template Options inspector now uses a scoped compact density instead of the previous 44px control scale: inputs/selects/links are 40px, action and segmented buttons are 38px, and unit selectors are 76px. Form-group padding and vertical gaps were reduced while labels, form semantics, and the previously approved 56 × 28px switch state remain intact.

The isolated V3 mockup and all nine desktop state screenshots were refreshed to match the new density. Browser QA covered CSS production tokens, desktop/mobile no-horizontal-overflow, and the existing internal scroll regression. Evidence is in `project-artifacts/qa/20260905-template-options-scroll-fix/QA-compact-density-20260905.md`.

Graphify was intentionally not updated for this correction because the user explicitly requested that coding and design work not update graph data.
