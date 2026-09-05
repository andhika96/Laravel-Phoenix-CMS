# Arunika Mobile V2 — Visual Implementation Specification

Tanggal: 2026-09-03. Status: rencana saja, belum diimplementasikan.
Project root: `D:/Laragon/www/laravel-13-phoenix`.

## 1. Tujuan dan sumber kebenaran

Permintaan user: implementasi mobile tiap tema sesuai persis dengan gambar preview V2 masing-masing. Lima PNG di bawah adalah target, bukan screenshot V1, bukan prompt ImageGen, dan bukan satu gaya generik untuk semua tema.

| Tema | Gambar target | Mode yang diperlihatkan |
|---|---|---|
| Lucent | [PNG Lucent](D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-lucent-v2.png) | Light |
| Mosaic | [PNG Mosaic](D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-mosaic-v2.png) | Dark |
| Aurora | [PNG Aurora](D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-aurora-v2.png) | Light |
| Prism | [PNG Prism](D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-prism-v2.png) | Light |
| Equinox | [PNG Equinox](D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-equinox-v2.png) | Light |

Seluruh gambar sudah diperiksa langsung. Semuanya board dua state; judul board, caption, angka SHOW/HIDE, frame presentasi, dan disclaimer bukan elemen aplikasi.

## 2. Arti target 100% dan batas yang harus dinyatakan

- Target: semua elemen visual yang terlihat pada screen di PNG masuk acceptance checklist; tidak ada redesign tambahan atau penggantian tema oleh template seragam.
- Jangan mengklaim kesamaan piksel 100% sebelum pengukuran. Gambar AI raster tidak menyediakan font, CSS, ukuran layout, atau state interaksi yang deterministik.
- PNG tidak memiliki rasio screen yang seragam meskipun prompt menyebut 400x844. Contoh: screen Lucent jauh lebih lebar relatif terhadap tingginya dibanding Mosaic. Label viewport di board bukan bukti ukuran internalnya.
- Pada tahap baseline, ukur crop screen tertutup/terbuka secara terpisah dari PNG, simpan koordinat dan SHA-256. Untuk pembanding visual, set viewport browser dengan rasio crop, skala uniform ke lebar 400 CSS px. Jangan stretch gambar untuk memaksakan semua frame menjadi 400x844.
- Uji responsive terpisah tetap wajib di 300x844, 400x844, 500x844. Jadi referensi visual dinormalisasi tanpa distorsi; acceptance usability berlaku pada ketiga lebar.
- Gambar Lucent menunjuk drawer sekitar separuh screen, sedangkan prompt menyebut 320px. Gunakan gambar sebagai acuan, ukur ulang; jangan memakai angka prompt secara buta. Lebar minimum usable 240px pada screen kecil harus ditandai sebagai deviasi jika berbeda crop.
- Font raster, ikon rekaan AI, logo/avatar, data contoh, dan chart kategori yang berubah tidak boleh direkayasa seolah data aplikasi. Pertahankan data dan permission; gunakan fixture terkontrol untuk visual QA.
- Setiap penyimpangan yang perlu karena readability, permission, logo aktual, atau 300px dicatat dan meminta keputusan user sebelum tema dinyatakan final. Toleransi pengukuran di rencana adalah guard QA, bukan izin mengganti target 100%.

## 3. Kontrak visual per tema

### Lucent — refined mobile

- Header putih konsisten: hamburger kiri, logo/wordmark Lucent di kanan langsung, bell notifikasi dan avatar bundar di ujung kanan.
- Tidak lagi ada toggle floating kanan di atas Dashboard. Saat drawer terbuka, hanya satu close X yang aktif; background header terlihat melalui scrim tetapi tidak interaktif.
- Drawer putih/off-white: brand + X pada baris atas, avatar dengan nama penuh dan role, separator, Dashboard aktif dengan strip mint sangat lembut, View site, label CONTENT, Manage Articles / Manage Cover Image / Manage Event.
- Footer drawer: Awesome Admin, Settings, Log out dengan separator dari menu. Reuse route/role guards; jangan menampilkan admin link pada role yang dilarang.
- Dashboard: judul tanpa ikon besar lama, subtitle Indonesia sesuai gambar, empat metric dalam grid 2x2; masing-masing memiliki ikon/badge halus, label, nilai dan trend.
- Chart hijau: title, legend, plot berada pada ruang berbeda. Card chart utuh, bukan title menumpuk canvas. Tinggi responsif eksplisit.
- Halaman di belakang drawer tidak bergeser. Scrim abu-abu hanya menutupi konten belakang.
- Aksen #1FA675, surface putih/#FAFAFA mengacu identitas Lucent yang sudah ada; sampling final dari gambar.
- Nama Administrator di gambar tidak boleh hardcoded. Avatar huruf A dan logo daun di PNG adalah aset konsep: source final harus diputuskan pada asset gate, tidak dibuat diam-diam sebagai logo situs global.

### Mosaic — dark classic

- Header dark emerald: hamburger, palette, moon di kiri; bell, ellipsis, avatar di kanan.
- Drawer charcoal: logo situs + nama dan X, Visit Site lalu Dashboard, ALL MENUS, daftar menu aplikasi, Awesome Admin di bawah.
- Dashboard empat card satu kolom, teks putih, border tipis. Trend hijau/merah. Chart bawah tetap dapat diakses dengan scroll walaupun tidak tampak di crop.
- Jangan mengubah Mosaic menjadi light untuk meniru empat tema lainnya. Light mode yang sudah ada tetap berfungsi tetapi hanya dark mode memiliki target PNG.
- Style close X menggantikan bentuk panel collapse pada mobile; desktop tetap memakai bentuk sebelumnya.

### Aurora — light ambient

- Header lembut: panel-open icon kiri, tiga tombol bundar palette / mode / Awesome Admin kanan.
- Drawer putih dengan gradient lavender/abu lembut; nama situs dan close X, Visit Site, active Dashboard putih dengan shadow halus, ALL MENUS plus garis, daftar menu.
- Footer profil dengan avatar/nama/role/chevron dan tombol Logout tersendiri.
- Empat card satu kolom, putih dengan border halus. Area title/legend chart mengikuti posisi terlihat tanpa saling overlap.
- Jangan menambahkan bell/profile ke header Aurora; PNG tidak memuatnya.
- Current source menampilkan email di footer; rencana mobile menampilkan role seperti PNG, email tetap boleh di halaman profil.

### Prism — light commerce

- Header putih: hamburger berbingkai tipis di kiri, ikon Awesome Admin di kanan, tanpa bell/palette/avatar header.
- Drawer putih opaque: nama situs, close X berbingkai, Visit Site, active Dashboard putih dengan shadow dan teks hijau, divider, ALL MENUS, daftar menu.
- Footer satu profile card Administrator/Super Admin dengan avatar dan chevron.
- Empat card satu kolom dengan radius/border tipis. Pertahankan urutan dan weight tipografi.
- Ikon account-shaped di kanan gambar tetap action Awesome Admin (sesuai caption), bukan dialihkan menjadi Profile.
- Prompt memakai panel icon, hasil PNG hamburger: PNG menang. Static test lama yang mengunci SVG panel mobile harus diperbarui setelah RED, bukan mempertahankan ikon yang salah.

### Equinox — mint landscape

- Header mint sangat terang: hamburger rounded outline kiri, teks Awesome Admin dan avatar kanan.
- Drawer mint: square L, nama situs, close X, Visit Site, active Dashboard mint, daftar menu. Landscape pegunungan berlapis dan matahari pada bagian bawah.
- Jangan menambah footer profil seperti Aurora/Prism; profil ada pada header.
- Empat card satu kolom dengan ikon bulat di kiri, angka dan label di kanan, trend di bawah. Ini berbeda dari capture runtime lama tanpa badge besar.
- Reuse aset landscape existing sebagai langkah awal. Bandingkan silhouette, crop, warna, posisi matahari. Jika tidak cocok, buat aset turunan khusus mobile; jangan menimpa aset desktop.
- Pada 300px teks Awesome Admin tetap berupa teks seperti gambar selama muat; wrap teks secara terkendali daripada diam-diam mengganti ikon. Jika tetap tidak muat, gate keputusan user.

## 4. Kontrak responsive, interaksi dan accessibility

- Breakpoint mobile mengikuti existing <=768px; desktop >=769px tidak didesain ulang. Verifikasi boundary 768/769.
- Baseline visual: lebar 400px, tinggi mengikuti crop ratio; usability tambahan 300/400/500 x844, short-height 400x568.
- Teks kontrol/body normal 14–16px sebagai titik awal, mengikuti SiteTypography. Jangan menurunkan teks sampai sulit dibaca demi screenshot.
- Target touch minimum 44x44 CSS px; bisa memakai wrapper hit-area tanpa mengubah ukuran ikon referensi.
- Di 300px, Lucent tetap 2 kolom bila value dan trend wrap rapi; tidak menurunkan font di bawah batas keterbacaan. Reflow satu kolom hanya jika dibuktikan perlu dan disetujui sebagai deviasi.
- Drawer mempunyai area header/footer tetap dan menu scroll sendiri; long menu/nama panjang tidak memotong tombol X/footer.
- Open/close, backdrop click, Escape, focus masuk drawer, Tab tidak keluar modal, focus kembali ke opener; drawer tertutup tidak focusable.
- Inert diterapkan hanya ke main/background saat drawer modal, jangan pada ancestor drawer. Desktop tidak boleh tersisa inert.
- Gunakan class state existing .ph-expanded. Simpan desktop preference hanya ketika >768px; toggle mobile tidak menulis sidebar-state.
- Transisi mobile tidak mengubah lebar konten belakang; gunakan transform, respect prefers-reduced-motion. Rapid toggle tidak menghasilkan state/ARIA mismatch.
- Satu sumber menu tetap menu_versioning(). Nested menu dan active route tidak diganti daftar hardcoded.
- Label CONTENT khusus presentation Lucent; category dari data tidak dihapus. Jika label konfigurasi berbeda dari fixture, dokumentasikan.
- Dropdown palette, notifications, profile tetap fungsional dan muat layar 300px. Jangan klik clear-all notification/Logout/Save data dalam QA tanpa kebutuhan dan izin.
- Posisi menu tetap sesuai tiap tema; tidak memakai satu partial header yang menyeragamkan kelimanya.

## 5. Arsitektur dan batas scope

- Pertahankan Blade layout CMS, script theme existing, shared SiteTypography dan rendering menu existing.
- Tambahkan overlay CSS mobile-v2.css per tema, dimuat HANYA layout CMS tema bersangkutan sesudah stylesheet typography; media <=768px dan body marker khusus CMS. Auth/front-end publik tidak memuat overlay.
- Tambahkan marker body data-ph-mobile-theme dengan nilai slug tema; dipakai CSS/testing tanpa membuang class existing.
- JS wajib kompatibel **VueJS 3 CDN** yang sudah dimuat CMS, memakai global `Vue`. Tidak memakai SFC `.vue`, import bundler, Vite, npm build, atau memasang versi Vue kedua.
- State drawer mobile dikelola satu controller Vue headless kecil pada mount root CMS khusus yang tidak berisi `@yield('content')`, form, notification component, atau Vue app lain. Gunakan `Vue.createApp`, `Vue.ref`, `Vue.watch`, lifecycle; render kosong dan binding DOM terbatas pada nav. Ini mempertahankan Blade menu dinamis tanpa parent Vue mengompilasi ulang nested apps.
- Script classic bersama `public/assets/js/themes/arunika-mobile-navigation-v2.js` hanya menangani state/interaksi mobile. Theme script existing tetap pemilik desktop, palette, active route, dan popover; `toggleSidebar()` mendelegasikan ke controller saat mobile, tidak memasang handler toggle ganda.
- Controller expose `window.PhoenixMobileNavigation` dengan `isMobile()`, `open()`, `close()`, `toggle()`, `syncViewport()`. Tidak ada library/sidebar framework baru; markup dan styling berbeda tetap milik masing-masing tema.
- Hook resize legacy harus diintegrasikan agar controller Vue menjadi satu-satunya pemilik `.ph-expanded` pada <=768px, sekaligus memulihkan preference desktop ketika melewati 769px. Controller tidak pernah menulis `sidebar-state`.
- Shared dashboard boleh ditambah semantic class untuk region summary/grid/metric/icon/chart. Style variasi hanya melalui marker tema dan media mobile.
- Chart appearance berubah sesuai tema pada mobile. Dataset source saat ini static demo Matcha/Milk/dst; data bulanan Jan–Jun pada PNG Lucent bukan izin membuat pipeline data baru. Putuskan fixture/semantik pada gate sebelum membuat pixel-match chart.
- Reuse notification component; tidak menduplikasi id cmsNotifBell/cmsNotifWrapper atau subscriber realtime.
- Tidak menyentuh Page Builder, File Manager internals, theme database registration, login/auth patch, atau desktop public layouts.
- Tidak ada commit/push, update setting permanen, penghapusan backup atau full Graphify rebuild dalam rencana otomatis.

## 6. Gate fidelity sebelum implementasi tiap tema

Catat dalam reference-measurements.json (artifact, dibuat saat eksekusi):
- image filename + SHA256, image width/height;
- screenClosed and screenOpen crop {x,y,width,height};
- normalized viewport each state, anchor rectangles header/toggle/avatar/drawer/menu-first/menu-last/footer;
- sampled surface/border/text/accent colors, font estimate, icon library mapping;
- unresolved differences berikut beserta keputusan user.

Keputusan yang belum dapat dipastikan dari PNG saja:
1. Lucent logo/wordmark/initial avatar versus Site Config logo/nama aktual.
2. Chart Lucent Jan–Jun/50K dan Prism “This Year Actuals” versus dataset dan copy aplikasi.
3. Variasi rasio dan ukuran drawer pada PNG; titik minimum readable pada 300px.
4. State di luar screenshot (palette/profile/menu bertingkat, light Mosaic/dark tema lain) mempertahankan perilaku existing dan diberi styling kompatibel, bukan diklaim pixel-match.

Tidak perlu menahan penulisan rencana karena gate ini. Tetapi jangan mengarang keputusan tersebut saat eksekusi.

## 7. Acceptance dan handoff

- Review PNG V2 versus browser screenshot dalam skala uniform yang sama, per tema, closed/open.
- Geometry guard: anchor displacement maksimal 2 CSS px dari ukuran target terukur; typography size +/-1px sebagai alat diagnosis. Semua mismatch tetap dicatat; bukan label otomatis “100%”.
- Color guard: flat-token hex sesuai referensi tersampling; perbedaan antialiasing, avatar dinamis, dan teks data dimask hanya jika daftar mask disetujui. Jangan menutupi area bermasalah.
- Tidak ada clipped controls, horizontal page overflow, overlap header/title/legend, logo distorted, unreachable footer.
- 30 baseline state checks = 5 themes x3 widths x2 drawer states. Tambahkan 768/769 transition, dark-mode regression, real pages, keyboard, 200% zoom/reduced motion.
- Desktop 1024/1440/1920, auth guest login + notice Lucent tetap sama setelah overlay mobile.
- Laporan akhir per tema memisahkan runtime verified, static only, dan untested.
