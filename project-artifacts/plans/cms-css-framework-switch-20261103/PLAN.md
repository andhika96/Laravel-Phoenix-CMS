# Rencana switch CSS framework CMS — Bootstrap 5 dan Tailwind

Project: Laravel 13 Phoenix  
Tanggal: 3 November 2026 (WIB)  
Status: **audit awal dan rencana; implementasi migrasi belum dimulai**  
Scope yang disepakati: **CMS utama dahulu**.

## 1. Tujuan dan ukuran selesai

Administrator dapat memilih Bootstrap 5 atau Tailwind untuk CMS. Tema, desain custom, warna, typography, data, hak akses, dan perilaku kerja CMS dipertahankan. Pilihan framework berbeda dari pilihan tema.

Tidak menjanjikan nol bug untuk kondisi yang belum diuji. Target rilis: seluruh kontrak dan kasus uji scope yang disepakati lulus, tidak ada regresi UI/UX yang belum diselesaikan, tidak ada error JavaScript/HTTP asset baru, dan recovery ke Bootstrap terbukti bekerja. Tiap tahap mempunyai gerbang kelulusan; tahap berikutnya tidak dilanjutkan dengan kegagalan yang ditutupi.

Tidak ada implementasi fitur dalam pekerjaan perencanaan ini. File baru hanya artifact audit/rencana di direktori ini. Source aplikasi, package, konfigurasi, database bisnis, tema aktif, dan Graphify tidak diubah.

## 2. Bukti source dan runtime yang sudah dikumpulkan

- Pemindaian terarah: **65 view kandidat CMS dan 28 script literal yang dimuatnya**. Ini cakupan source terpilih, bukan klaim seluruh file pasti reachable pada runtime.
- **239 rujukan API Bootstrap** dan **78 rujukan event Bootstrap** terdeteksi pada cakupan tersebut; jumlah ini bukan jumlah komponen unik.
- API yang ditemukan: Modal, Toast, Collapse, Tooltip. Dropdown/offcanvas/tabs juga ditemukan melalui markup/data API.
- Rantai event yang ditemukan: `hidden.bs.toast`, `shown.bs.modal`, `hidden.bs.modal`, `show.bs.collapse`, `hide.bs.collapse`.
- Ada ketergantungan nyata: selesai toast → tutup modal → reset tombol/form → kemungkinan buka kembali modal sebelumnya. Article Templates menjalankan resize/rebuild preview pada event modal.
- Pagination memakai **VuejsPaginateNext 1.0.2**, bukan Bootstrap JavaScript. Source list article juga memiliki state pencarian/filter/pagination sendiri.
- Runtime CMS pada 12 halaman yang dibuka memakai **Vue global 3.5.21**; package npm Vue adalah 3.5.24. Menginstal pustaka Vue tanpa mengatur bundling dapat menghasilkan dua runtime.
- Layout CMS memakai asset Bootstrap CSS `5.3.6_custom` dan JS 5.3.6; dependency npm Bootstrap tercatat 5.3.8. Jangan menyamakan versi npm dengan asset yang benar-benar dimuat.
- Tailwind dan CLI lokal 4.3.3 tersedia, tetapi `resources/css/app.css` kosong dan layout CMS masih memuat Bootstrap secara langsung. Belum ada pipeline Tailwind CMS yang terbukti bekerja.
- Dua belas route baca-saja: Dashboard, Manage Article, Cover Image, Event, User, Role, Permission, SMTP, Site Config, Appearance, Themes, Workflow. Seluruhnya HTTP 200, 0 page errors selama pembukaan halaman. Tidak ada submit form/CRUD. Jumlah modal di DOM bukan bukti modal tersebut sudah diuji interaksinya.
- Route catalog berisi **130 kandidat GET**. Termasuk endpoint data; tidak boleh disebut 130 halaman. Literal view controller belum dipetakan per action. Ini harus diselesaikan pada G0.

Artifact: `component-audit.json`, `source-files.txt`, `audit-summary.log`, `route-catalog.json`, `browser-inventory.json`, `COMPONENT-MATRIX.md`, `ACCEPTANCE-CHECKLIST.md`.

## 3. Batas scope

### Termasuk

- Shell CMS: header, profil, sidebar, submenu, drawer mobile, footer menu, tooltip, breadcrumb, warna dan typography.
- Dashboard; account/profile; Manage Article dan Article Templates admin; Cover Image; Manage Event; Workflow list/editor yang memakai shell CMS; halaman Awesome Admin termasuk config/menu/user/role/permission/SMTP/language/appearance/themes.
- Semua halaman/partial CMS yang dibuktikan reachable saat penutupan inventaris G0.
- Empat tema yang dapat dipilih di Manage Themes: Prism, Aurora, Lucent, Equinox.
- Default dan Calm Green ikut uji kompatibilitas karena layout masih ada. Rilis tidak boleh diam-diam merusak tema legacy yang masih dapat menjadi konfigurasi aktif.
- Interaksi CMS dengan media picker/editor/preview harus tetap bekerja.

### Tetap mandiri

- Login/signup/forgot/reset password beserta Primary Color dan CSS auth.
- Frontend website, artikel publik, email, setup.
- Editor/canvas Page Builder, frontend output builder, dan File Manager V2 tidak dikonversi internalnya pada tahap CMS ini. File Manager V2 memiliki dokumen dan bundle Vue/Bootstrap sendiri; Page Builder mempunyai editor shell sendiri. Ini batas dokumen/modul, bukan pengecualian semua pengujian integrasinya.
- Jika dibuka dari CMS, tombol peluncur, overlay induk, iframe, callback pemilihan file, focus, dan pemulihan scroll tetap masuk uji. Bootstrap di dokumen iframe terpisah boleh tetap ada; tidak boleh bocor ke dokumen CMS Tailwind.

## 4. Apakah Tailwind mendukung komponen CMS?

Tailwind menyediakan styling utility dan dapat digunakan membuat seluruh tampilan dasar yang ditemukan. Ia tidak otomatis menggantikan JavaScript/data API/event Bootstrap. Komponen interaktif memerlukan implementasi yang sesuai.

Rekomendasi awal:

| Bagian | Pilihan yang direncanakan | Alasan |
|---|---|---|
| Styling engine | Bootstrap existing / Tailwind 4.3.3 | Dua bundle terpisah sesuai pilihan CMS |
| Interaksi kompleks mode Tailwind | **Reka UI, kandidat sampai lulus G1** | Vue, unstyled, dialog/menu/toast/collapsible/tooltip/tabs tersedia |
| Pagination existing | **Pertahankan VuejsPaginateNext** | Pertahankan state/callback/API; ganti styling sesuai driver |
| Table, button, form, badge, breadcrumb, skeleton | HTML/Blade/Vue existing + CSS sesuai driver | Tidak perlu pustaka baru untuk presentasi dasar |
| Vue Select, VueDatePicker, Coloris, CKEditor, CKFinder, Sortable, SimpleBar, ECharts/jsVectorMap | **Pertahankan** | Komponen bukan otomatis diganti oleh Tailwind; integrasi/skin diuji |
| Drawer mobile | Controller mobile yang ada atau Dialog stabil dengan skin drawer | Jangan mengadopsi Drawer alpha sebagai fondasi |

Reka UI bukan paket yang sudah terpasang dan bukan pengganti langsung `bootstrap.Modal`. Ia memerlukan integrasi serta pengujian di CMS ini. Gunakan hanya primitive stabil yang diperlukan; jangan memasang paket Form/DatePicker/Drawer alpha atau mengganti plugin yang sudah bekerja hanya demi keseragaman.

Headless UI Vue juga memiliki Dialog/Menu/Disclosure/Tabs, tetapi inventaris CMS mencakup toast dan tooltip serta lifecycle imperatif. Kandidat Reka lebih luas untuk kebutuhan ini. Jangan mencampur beberapa pustaka komponen untuk perilaku yang sama.

Jika uji Reka dengan Vue global tidak lulus, hentikan adopsi kandidat tersebut dan revisi keputusan teknis. Jangan memuat Vue kedua atau membiarkan Bootstrap/Tailwind sama-sama mengendalikan satu komponen sebagai jalan pintas.

## 5. Arsitektur target

### Konfigurasi

- Satu pilihan framework CMS global, mengikuti akses Administrator/Super Admin dan pola controller/UI yang sudah ada.
- Kandidat nama kolom: `theme_settings.cms_css_framework`, enum aplikasi `bootstrap`/`tailwind`, default Bootstrap. Nama final diperiksa terhadap schema saat implementasi.
- Validasi server allowlist, CSRF dan otorisasi mengikuti struktur sekarang. Tidak menyusun path file dari input/DB bebas.
- Tema dan framework disimpan sebagai pengaturan berbeda. Mengganti framework tidak mengubah theme code, palet, font, atau page theme auth.
- Simpan lalu full reload; tidak menukar CSS pada DOM yang sedang memiliki modal/form aktif. Perubahan berlaku pada dokumen baru, bukan memaksa reload tab lain yang menyimpan input.
- AJAX bisnis tetap data-only. Endpoint yang mengembalikan HTML harus diinventarisasi agar dokumen lama tidak menerima markup driver baru setelah administrator lain mengganti pengaturan.

### Pemuatan asset

- Dokumen CMS Bootstrap memuat Bootstrap existing dan driver yang sesuai.
- Dokumen CMS Tailwind memuat Tailwind, primitive terpilih dan driver Tailwind; **tidak memuat Bootstrap CSS/JS di dokumen CMS utama** setelah migrasi lengkap.
- CSS auth/renderer frontend/editor mandiri tidak dimasukkan ke bundle CMS.
- Pilihan engine diselesaikan server sebelum HTML head dirender; tidak ada dua framework global dimuat lalu salah satunya disembunyikan.
- Manifest build dan asset hash diperiksa sebelum opsi Tailwind dapat diaktifkan. Build gagal/asset tidak ada → aktivasi ditolak dan konfigurasi lama dipertahankan.
- Konfigurasi rusak/tidak dikenal → jalur pemulihan Bootstrap yang diuji, dicatat pada log. Pesan admin harus menjelaskan keadaan aktual; tidak mengklaim Tailwind aktif saat fallback.

### Tampilan dan komponen

- CSS variables desain bersama **khusus CMS** untuk primary, on-primary, hover/focus/active/disabled, warna surface/text/border, typography, spacing, radius, layer dan motion. Warna diambil dari pengaturan tema CMS, bukan page primary auth.
- Class custom `ph-*`, id, data hook, nama function dan struktur kerja existing dipertahankan bila tidak terikat framework. Class custom yang masih memakai `--bs-*` atau struktur Bootstrap perlu penyesuaian; prefix `ph-` saja bukan bukti independen.
- Presentasi komponen dapat dipisahkan dari driver framework melalui Blade partial/component sesuai pola proyek. Jangan menyalin seluruh controller/Vue state/validasi ke dua versi.
- Buat jalur UI bersama hanya untuk operasi yang memang memiliki banyak caller: modal, toast, collapse, tooltip dan menu. Driver Bootstrap mendelegasikan ke API existing; driver Tailwind memakai state/primitive baru. Tidak membuat emulator seluruh API Bootstrap.
- Semua 239 rujukan dan 78 event dipetakan per caller. Ganti event ke kontrak UI yang jelas; callback selesai open/close dipanggil tepat sekali setelah transisi yang sesuai. Jangan sekadar mengganti nama constructor atau menyiarkan event Bootstrap palsu.
- Modal yang sekarang menyimpan DOM/form ketika ditutup tidak boleh mendadak unmount dan menghilangkan input/editor. Kebijakan mount/preserve/reset ditetapkan per modal.
- Portal overlay harus memperoleh token tema dan scope CMS yang sama. Konten tidak boleh terpotong ancestor overflow atau kehilangan warna karena dipindahkan ke body.

### Build dan CSS

- Entry CMS terpisah dari Vite File Manager dan editor. Jangan menjalankan build yang mengosongkan direktori asset luas atau menimpa bundle modul lain.
- Kandidat source: `resources/css/cms/` dan `resources/js/cms/ui/`; output pada subfolder yang sesuai dalam `public/assets/css/cms/` dan `public/assets/js/cms/`. Lokasi konkret ditetapkan pada G1, dengan `emptyOutDir` yang aman dan manifest.
- Tailwind v4 diproses sebagai CSS; jangan mencampurkan entry Tailwind ke Sass File Manager yang mengimpor Bootstrap RFS.
- Utility memakai prefix Tailwind agar tidak berbenturan dengan class existing seperti container/collapse. Preflight global tidak langsung diaktifkan; pilih base style CMS eksplisit dan ukur hasil terhadap baseline.
- Periksa urutan CSS layer; stylesheet tema yang unlayered dapat mengalahkan utility ber-layer. Jangan mengatasi semuanya dengan `!important` global.
- Breakpoint harus mengikuti perilaku sekarang, bukan default Tailwind. Bootstrap grid dan drawer tema tidak selalu sama; khusus drawer existing uji **768/769 px**. Ukur juga batas grid actual custom Bootstrap sebelum menetapkan config.
- Tailwind scan hanya sumber CMS aktif yang dibutuhkan. Kecualikan `.bak`, backup, project-artifacts, vendor, node_modules, cache, generated, auth/frontend/editor yang di luar bundle. Dynamic class harus berupa nama lengkap yang dapat dideteksi; warna runtime memakai CSS variable, bukan `bg-${hex}`.
- Pin versi dependency yang lulus prototype; tidak memakai `@latest` CDN untuk fondasi baru.

## 6. Risiko yang sudah teridentifikasi

| Risiko | Dampak | Pencegahan wajib |
|---|---|---|
| Vue global 3.5.21 vs npm 3.5.24 | Dua runtime, komponen tidak terdaftar, reactivity berbeda | G1 external/shared Vue; buktikan satu runtime, register sebelum mount; jangan upgrade serentak |
| Modal/toast event chain | Form tidak reset, dua modal, callback ganda | Ledger setiap callback dan sequence test |
| Modal → modal sebelumnya / native dialog konfirmasi | Escape/backdrop/focus menutup lapisan salah | Uji stack, dirty form, fallback focus setelah trigger terhapus |
| Plugin popup/iframe di dalam modal | Focus trap memblokir datepicker/CKFinder/Coloris | Penempatan portal/allowed layers diuji; jangan mematikan seluruh aksesibilitas |
| Responsive defaults berbeda | Sidebar/card/table berubah ukuran | Breakpoint dan geometry baseline dipertahankan |
| RFS / Site Typography / custom font | Heading/input membesar | Pertahankan ukuran aktual dan pilihan font; ukur computed style |
| CSS `--bs-*`, state `.show`/`.fade`/`.is-invalid` | Style hilang ketika Bootstrap dilepas | Pemetaan dependency dan driver/component CSS lengkap |
| HTML dihasilkan lewat JS | Tombol setelah loading berbeda dari render awal | Audit `insertAdjacentHTML`, innerHTML, template literal dan semua state |
| Select/date picker/editor di modal tersembunyi | Ukuran salah, popup tertutup, input hilang | Test resize/after-open/destroy/remount dan ukuran viewport |
| API request berulang | Save/delete dua kali | Assert satu request per tindakan dan listener cleanup |
| Semua tema dan warna | Teks putih di tinted surface terulang | Uji on-color per surface, termasuk matte/kuning/gelap dan state |
| CSS reset memengaruhi modul lain | Auth/editor/frontend berubah | Asset boundary dan computed-style isolation test |
| Cache/asset hilang | Pilihan tidak sesuai renderer / halaman rusak | Manifest tervalidasi, cache version, fail-safe Bootstrap dan rollback drill |
| Worktree berubah selama proyek | Perubahan user tertimpa | Status/hash/backup tepat sebelum edit; jangan memakai backup sebagai source |

## 7. Tahapan dan gerbang kelulusan

### G0 — Tutup inventaris dan baseline

1. Verifikasi ulang memori/status/source aktif dan hash sebelum pekerjaan implementasi.
2. Selesaikan daftar route → controller action → view → partial → JS → CSS/plugin. Tandai halaman data, preview, route bersyarat/permission dan halaman tidak reachable. Cari include dinamis/JS lazy-loaded yang tidak tercakup scanner literal.
3. Peta setiap komponen/state/event/plugin dan owner perilakunya. Popover/carousel belum ditemukan pada source scan; jangan membuat pengganti tanpa consumer.
4. Rekam screenshot dan computed geometry/typography/palet dari build existing untuk tema/mode/viewport yang direncanakan; data/clock/chart dipastikan deterministik.
5. Catat bug baseline yang sudah ada, termasuk request/console/permission. Bug baru tidak boleh diberi label baseline tanpa bukti pembanding.

**Lulus:** setiap route CMS dalam scope memiliki status coverage; tidak ada dependency tak diketahui; baseline disimpan; semua gap scan diselesaikan. Audit awal saat ini belum menutup G0 sepenuhnya.

### G1 — Prototype terisolasi dan keputusan pustaka

1. Buat entry prototype pada project-artifacts/dev-only, tidak mengaktifkan engine global.
2. Buktikan Reka terpilih memakai runtime Vue yang sama dengan aplikasi existing; primitive dapat diregistrasikan pada createApp existing tanpa mount ganda.
3. Prototype satu modal CRUD berisi form dan toast, dropdown, collapse, tooltip, pagination existing, serta datepicker/select/Coloris popup. Sertakan satu alur modal berantai dan dialog perubahan belum disimpan.
4. Tunjukkan tampilan berdampingan Bootstrap/Tailwind pada satu tema; nilai desktop/mobile/dark/focus dan plugin layer.
5. Ukur bundle, network, startup, memory/listener cleanup. Jika integrasi gagal, revisi pilihan dan rencana sebelum mengubah halaman produksi.

**Lulus:** tidak ada Bootstrap CSS/JS di dokumen prototype Tailwind, satu Vue runtime, event parity benar, tampilan diterima, paket/versi diputuskan. Ketersediaan komponen di dokumentasi bukan kelulusan gate ini.

### G2 — Fondasi dual engine

1. Backup file dan row konfigurasi terkait dengan timestamp/hash.
2. Tambahkan enum setting + migration reversible default Bootstrap. UI publik switch belum dibuka.
3. Loader/manifest, token CMS, CSS base dan driver UI dibuat minimal sesuai inventaris.
4. Driver Bootstrap diuji terhadap baseline sebelum halaman dimigrasikan.

**Lulus:** Bootstrap existing tetap lulus, pemilihan asset deterministik, permission/validasi benar, asset gagal dapat dipulihkan.

### G3 — Shell dan komponen bersama

1. Header/profil/sidebar/submenu/drawer/tooltip/breadcrumb disesuaikan untuk dua driver.
2. Button/form/input/select/check/radio/switch, table/card/badge/alert, loading/skeleton/progress, modal/toast/dropdown/collapse/tabs dituntaskan sesuai pemakaian nyata.
3. Pagination memakai state/callback existing; style disabled/current/focus dan compact mobile dipertahankan.
4. Token dan overlay root bekerja pada enam tema serta light/dark.

**Lulus:** component contract checklist lulus di kedua mode; tidak ada tumpang tindih dua pengendali DOM; active text tidak dipaksa satu warna global.

### G4 — Halaman CMS dan plugin

Urutan: Dashboard → Users/Role/Permission/Language → Appearance/Themes/Config/Menu → SMTP → Cover Image → Event → Article list/add/edit/Templates → Workflow/account/profile dan sisa route inventory.

Urutan ini dapat ditukar hanya jika dependency nyata mengharuskan. Halaman sulit sengaja dipakai di prototype, meskipun migrasi lengkapnya di gelombang akhir.

Untuk setiap halaman: migrasikan presentasi, jaga request/payload/validation/permission, uji state kosong/loading/error/success, uji plugin dan output HTML dinamis, bandingkan Bootstrap baseline dan Tailwind. Penyesuaian parent shell tidak boleh merembes ke iframe preview atau frontend output.

**Lulus:** coverage ledger setiap halaman selesai; tidak ada halaman diam-diam fallback ke Bootstrap CSS ketika engine Tailwind dipilih.

### G5 — QA lintas halaman dan pemulihan

Jalankan matriks pada bagian 8 dan checklist terpisah, lakukan rollback drill, uji missing asset/build, authorization, stale tab, cache/hard reload, repeated interaction, serta isolation auth/frontend/modul.

**Lulus:** seluruh tes wajib lulus; setiap perbedaan visual memiliki penilaian; tidak ada regresi scope terbuka; daftar baseline issue dipisahkan; paket deploy lengkap dan recovery tervalidasi.

### G6 — Buka pilihan framework

Tambahkan satu kontrol yang mengikuti UI Manage Themes existing; Bootstrap tetap default. Tailwind dapat dipilih setelah semua gate di atas. Setting ditolak bila driver/manifest/coverage theme tidak memenuhi syarat. Verifikasi simpan → reload → state aktif yang benar → kembali Bootstrap. Deployment/push mengikuti otorisasi pengguna, tidak otomatis dalam pekerjaan rencana.

## 8. Matriks pengujian

### Dimensi

- Framework: Bootstrap / Tailwind.
- Tema: Prism, Aurora, Lucent, Equinox, Default, Calm Green.
- Mode: light / dark.
- Ukuran inti: 320, 390, 768, 1024, 1440, 1920 px; boundary tambahan 575/576, 767/768/769, 991/992, 1199/1200, 1399/1400 sesuai hasil G0. Portrait/landscape dan zoom 200% disertakan untuk shell/form utama.
- Browser: Chromium, Firefox dan WebKit automation; Safari/iOS/Android fisik untuk final touch, keyboard virtual dan drawer. Browser fisik yang tidak tersedia tetap dicatat belum diverifikasi; tidak diklaim lulus.
- Target minimum Tailwind v4 menurut dokumentasi: Chrome 111, Safari 16.4, Firefox 128. Kebutuhan browser yang lebih tua harus diputuskan sebelum G1; jangan menganggap seluruh browser yang mendukung Bootstrap otomatis mendukung Tailwind v4.
- Peran: Administrator/Super Admin dan role terbatas/non-admin, termasuk izin read/add/update/delete yang berbeda.
- Data: kosong, satu row, banyak row, judul panjang, unicode, payload validation error, loading lambat, server/network error, request saling menyalip.

### Strategi supaya cakupan jelas

- Semua komponen dasar: dua framework × enam tema × dua mode pada ukuran desktop/mobile representatif.
- Shell visual: seluruh tema/mode/framework pada ukuran inti dan batas breakpoint yang relevan.
- Semua halaman reachable: minimal tes alur utama pada kedua framework. Halaman berisiko tinggi (Article CRUD/Templates, Cover Image, User, Config/Appearance, modal berantai dan picker) menjalani matriks tema/mode lebih luas.
- Palet tema: delapan warna yang tersedia dan contoh terang/gelap/pastel diuji pada komponen warna/kontras khusus; tidak menggandakan semua tes CRUD untuk tiap warna.
- API tests membandingkan endpoint, method, parameter pagination/filter/sort dan payload; UI test tidak boleh mengubah kontrak server.
- Data tulis hanya fixture/DB pengujian/transaksi rollback. Tidak mengirim email SMTP atau delete/upload data pengguna demi QA.
- Screenshot comparison menunggu font dan data stabil; chart/clock/motion dimask/diatur deterministik. Pemeriksaan geometry elemen kunci mempunyai toleransi pembulatan yang disepakati (target ≤1 CSS px pada kondisi sama); perbedaan hierarki, wrap, clipping, overflow, ukuran teks atau posisi aksi tidak boleh lolos hanya karena skor screenshot kecil.
- Accessibility: nama/label, keyboard, focus trap/return, aria-expanded/current/invalid, live region, kontras teks biasa ≥4.5:1, teks besar ≥3:1, reduced-motion dan penggunaan sentuh.
- Kinerja: bandingkan dengan baseline bytes/requests/startup/interaksi. Batas budget final ditetapkan setelah pengukuran G0; tidak menjanjikan Tailwind pasti lebih cepat/kecil.

## 9. Rollback dan kriteria rilis

- Sebelum edit existing file: backup timestamp + SHA-256 + patch scope + status worktree. Snapshot konfigurasi terkait sebelum migration/aktivasi.
- Artefak build Bootstrap working disimpan; setting default tetap Bootstrap sepanjang pengerjaan.
- Membatalkan Tailwind tidak mengubah data bisnis atau page primary auth. Migration memiliki down, tetapi rollback operasional pertama cukup mengganti driver yang valid; jangan sembarang rollback migration database.
- Restore hanya file/setting scope yang diperlukan setelah mengecek perubahan terbaru user.
- Release gate: semua checklist wajib pass; tidak ada error baru, request duplikat, state hilang, kebocoran CSS, kegagalan izin, atau regresi UI/UX yang belum diselesaikan. Exception baseline harus tertulis dan terpisah, bukan alasan melewati tes migrasi.
- Graphify hanya diperbarui manual atas perintah user setelah source final, tidak di-update dalam rencana ini.

## 10. Hal yang masih harus dibuktikan

1. Kelengkapan route/view/JS dinamis di luar 65 view/28 script kandidat.
2. Integrasi primitive Reka terpilih dengan Vue global dan banyak createApp existing.
3. Perilaku picker/editor/native-dialog dan form state ketika konten dipindahkan ke portal.
4. Ukuran build serta kemampuan pulih ketika asset/manifest hilang.
5. Paritas tampilan dan semua alur bisnis pada Tailwind; belum ada prototype Tailwind yang dijalankan saat rencana dibuat.

Karena itu keputusan sekarang adalah **layak secara kemampuan komponen, lanjut melalui gate prototype bila rencana disetujui**. Tidak ada klaim migrasi siap diaktifkan atau bebas bug saat ini.

## 11. Referensi dan konteks

- [Tailwind utility styling](https://tailwindcss.com/docs/styling-with-utility-classes)
- [Tailwind custom styles](https://tailwindcss.com/docs/adding-custom-styles)
- [Tailwind Preflight](https://tailwindcss.com/docs/preflight#disabling-preflight)
- [Tailwind source detection](https://tailwindcss.com/docs/detecting-classes-in-source-files)
- [Tailwind compatibility](https://tailwindcss.com/docs/compatibility)
- [Bootstrap JavaScript](https://getbootstrap.com/docs/5.3/getting-started/javascript/)
- [Bootstrap breakpoints](https://getbootstrap.com/docs/5.3/layout/breakpoints/)
- [Reka introduction](https://www.reka-ui.com/docs/overview/introduction)
- [Reka Dialog](https://www.reka-ui.com/docs/components/dialog), [DropdownMenu](https://www.reka-ui.com/docs/components/dropdown-menu), [Toast](https://www.reka-ui.com/docs/components/toast), [Collapsible](https://www.reka-ui.com/docs/components/collapsible), [Tooltip](https://www.reka-ui.com/docs/components/tooltip), [Pagination](https://www.reka-ui.com/docs/components/pagination)
- [Headless UI Vue Dialog](https://headlessui.com/v1/vue/dialog)

Memori diperiksa: `E:\AI\Memories\20260901-arunika-lucent-handoff.md` dan konteks pemisahan/bug bridge pada `project-artifacts/theme-auth-isolation-20261103/REPORT.md`. Memori menjelaskan sejarah; versi dan wiring diverifikasi pada source/runtime sekarang.

Graphify lokal dipakai untuk bootstrap/CMS/modal/pagination dan Theme_Settings; hasil query terpilih di `graph-query.txt`. Graph mengarahkan ke layout/helper/model dan komponen tetapi belum memetakan seluruh perubahan terbaru. Tidak ada relasi inferred yang dipakai sebagai bukti kecocokan library/runtime.

Skills: Ponytail dan panduan Karpathy untuk scope/gerbang verifikasi, UI UX Pro Max untuk kontrak interaksi/aksesibilitas, Design Taste untuk mempertahankan desain existing (tanpa redesign), Playwright untuk inventaris baca-saja. Superpowers tidak tersedia pada katalog lokal sesi; tidak memasang paket pengganti.
