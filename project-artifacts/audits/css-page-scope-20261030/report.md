# Audit CSS per halaman — Laravel 13 Phoenix — 30 Oktober 2026

Audit baca-saja. Semua halaman Page Builder dan File Manager baru dikecualikan. Tidak ada source aplikasi, pengaturan tema, halaman, atau data bisnis yang diedit/disimpan oleh audit. Login memakai akun yang telah diotorisasi; efek sesi/login normal aplikasi tetap dapat terjadi.

## Kesimpulan

Pemuatan belum sepenuhnya mengikuti kebutuhan minimum tiap halaman. CSS khusus artikel, Header Navigation, Workflow, Spectrum/Croppie/Coloris umumnya memiliki consumer yang tepat. Temuan konkret adalah Vue Select global, Datepicker pada daftar Cover Image, stylesheet tema gabungan auth/frontend/CMS, cache-busting per waktu, dan empat file dengan penulisan CSS tidak valid.

- 127 view Blade diperiksa statis; 145 action GET dipetakan tanpa memanggil controller. Action GET mencakup JSON/data, bukan 145 halaman HTML.
- 25 CSS custom di luar Page Builder diperiksa: 15 teramati runtime; 10 milik tema alternatif diperiksa wiring source saja.
- 51 URL pada viewport 1440 dan 390px: 102 kasus; 98 response 200, empat response 500 pada dua URL.
- Seluruh stylesheet yang terdaftar pada halaman sampel terdeteksi oleh engine CSS browser. Tidak ada stylesheet Page Builder/File Manager yang dimasukkan ke pengukuran.
- 173 hash CSS dari inventaris awal dan seluruh hash view audit tetap sama. Tidak ada source aplikasi yang berubah.

## Temuan terverifikasi

### 1. Vue Select dimuat terlalu luas

Layout CMS memuat vue-select.3.20.3.css untuk semua halaman yang memakainya. Dashboard, akun, artikel, event, dan banyak halaman lain tidak memiliki elemen .v-select pada sampel. Source pemakaian <v-select> ditemukan pada delapan view: Site Config; kategori/parent/submenu-detail BE dan FE; serta view Role lama. Sebagian komponen baru tampil ketika modal dibuka. Jangan menghapus plugin secara global; pindahkan pemuatan ke halaman/kelompok consumer tersebut setelah validasi modal. Tiga rule yang tercatat pada Dashboard berasal dari deklarasi :root, bukan bukti komponen select hadir.
Sumber layout: resources/views/themes/arunika_lucent/cms/cms_layout.blade.php:29. Daftar halaman observasi tanpa komponen:

- `/account`
- `/account/general`
- `/account/profilepicture`
- `/awesome_admin`
- `/awesome_admin/appearance`
- `/awesome_admin/header-navigation`
- `/awesome_admin/language`
- `/awesome_admin/language/translated`
- `/awesome_admin/language/untranslated`
- `/awesome_admin/menu`
- `/awesome_admin/menu/be`
- `/awesome_admin/menu/be/parentmenu`
- `/awesome_admin/menu/be/submenu`
- `/awesome_admin/menu/fe`
- `/awesome_admin/menu/fe/parentmenu`
- `/awesome_admin/menu/fe/submenu`
- `/awesome_admin/permission`
- `/awesome_admin/role`
- `/awesome_admin/role/create/v2`
- `/awesome_admin/role/edit/v2/1`
- `/awesome_admin/smtp`
- `/awesome_admin/themes`
- `/awesome_admin/user`
- `/dashboard`
- `/event`
- `/manage_article`
- `/manage_article/add`
- `/manage_article/edit/5059`
- `/manage_article/templates`
- `/manage_coverimage`
- `/manage_coverimage/add`
- `/manage_coverimage/edit/1`
- `/manage_event`
- `/manage_event/add`
- `/notification`
- `/notification/empty`
- `/profile`
- `/workflow`
- `/workflow/create`

### 2. Datepicker pada daftar Cover Image

resources/views/manage_coverimage/manage_coverimage.blade.php:9 memuat CSS Datepicker. View daftar dan script vueV3-manage-coverimage-2026.js tidak merender picker; script hanya mendaftarkan VueDatePicker pada components. Kedua viewport daftar memiliki nol picker. Form tambah/edit memiliki komponen picker dan rule CSS-nya terpakai. CSS picker adalah kandidat dipisahkan dari halaman daftar, tetap dipertahankan pada tambah/edit.

### 3. Lucent dipakai auth, frontend, dan CMS melalui tema global

Tema database saat audit: {"auth": "arunika_lucent.auth.auth_layout", "cms": "arunika_lucent.cms.cms_layout", "frontend": "arunika_lucent.frontend.frontend_layout", "login_template": "default"}.
Rantai login: Auth_Controller::login() → auth.default.login → @extends(themes + custom_theme(auth)) → themes/arunika_lucent/auth/auth_layout.blade.php → arunika_lucent.css.
Default pada login adalah template susunan halaman; bukan pilihan tema global Default. custom_theme() membaca theme_settings.id=1, lalu Themes.theme_id, dan memilih folder/layout auth/cms/frontend dari record yang sama.
Layout auth secara eksplisit memuat stylesheet Lucent dan memasang body.ph-theme-arunika-lucent. CSS itu menyediakan token --ph-primary yang dipakai .btn-larapx dari phoenix-cms.css. File Lucent juga berisi sidebar, topbar, dashboard, dan aturan khusus halaman tanpa admin shell.
Uji browser menonaktifkan hanya stylesheet Lucent, tanpa perubahan source/server: latar tombol login hilang dan tautan berubah dari hijau menjadi putih. Sheet dikembalikan dan seluruh computed style yang disampel kembali cocok pada kedua viewport.

| Viewport login | Panjang source CSS (karakter) | Karakter rule teramati terpakai | Persentase indikatif |
|---:|---:|---:|---:|
| 1440 | 108480 | 11234 | 10.36% |
| 390 | 108480 | 11321 | 10.44% |

Ini mengindikasikan bundle auth membawa aturan CMS yang tidak diperlukan pada login. Tidak berarti seluruh sisa rule boleh dihapus dari proyek. Pemisahan yang layak adalah fondasi/token/komponen tema bersama dan aturan khusus CMS; auth tetap membutuhkan bagian tema bersama.
Bukti gambar: login-with-lucent-1440.png; login-without-lucent-1440.png; pasangan 390px juga tersedia.

### 4. Cache CSS umum tidak digunakan kembali antarhalaman

Empat URL stylesheet memakai ?v=time(). Saat berpindah Dashboard ke Account, query berubah dan browser mengunduh ulang empat file. Transfer terukur pada masing-masing CSS; cms-bootstrap-theme.css memakai versi filemtime dan transfer kedua 0 byte. Ini membuat file yang relevan tetap diunduh berulang. Kandidat perbaikan: versi berdasarkan perubahan file atau build, setelah verifikasi perilaku cache.

| CSS | Transfer Dashboard | Transfer Account |
|---|---:|---:|
| `/assets/css/cms-bootstrap-theme.css` | 0 | 0 |
| `/assets/css/phoenix-cms.css` | 41306 | 41306 |
| `/assets/css/theme-responsive-typography.css` | 8907 | 8907 |
| `/assets/css/themes/arunika_lucent/arunika_lucent.css` | 108784 | 108784 |
| `/assets/css/themes/arunika_lucent/mobile-v2.css` | 21307 | 21307 |

### 5. Penulisan CSS tidak valid di empat file

| File | Baris kegagalan parser pertama | Temuan |
|---|---:|---|
| `public/assets/css/aruna-v4.css` | 325 | Unknown word !important |
| `public/assets/css/phoenix-cms.css` | 1682 | Unknown word !important |
| `public/assets/css/themes/calm_green/aruna-admin-v7-calm-green.css` | 128 | Unknown word position-relative |
| `public/assets/css/themes/default/aruna-admin-v7-default.css` | 122 | Unknown word position-relative |

phoenix-cms.css:1682/1688 dan aruna-v4.css:325/331 menulis background-color: var(--ph-content-bg); !important;. Browser tetap menerima background-color tetapi mengabaikan token !important yang terpisah. CSSOM browser mengonfirmasi priority kosong untuk .button-collapsed dan .list-group-item. Dua file tema lama menulis position-relative; di dalam deklarasi, yang bukan properti CSS yang valid. Kegagalan parser bukan berarti browser mengabaikan seluruh file; browser melakukan pemulihan atas deklarasi invalid.

### 6. Mobile CSS dimuat juga pada desktop

mobile-v2.css Lucent berukuran 21.007 karakter. Pada Dashboard desktop hanya dua rule (273 karakter) tercatat terpakai; pada mobile dengan drawer dibuka tercatat 124 rule. Ini lapisan responsive aktif, bukan file mati. Link CSS tidak memiliki media attribute. Jangan langsung membatasi seluruh file ke max-width karena file masih memiliki guard desktop yang menyembunyikan kontrol mobile.

## Bagian yang sesuai kebutuhan

- CSS khusus Manage Article hanya dimuat pada daftar; editor-content pada tambah/edit/detail/preview artikel; template-manager pada pengaturan template.
- Header Navigation memakai dua CSS khusus halaman itu; Workflow memakai stylesheet bersama daftar/editor; frontend dropdown dipakai frontend dan preview konfigurasi parent menu FE.
- SimpleBar dipakai sidebar pada halaman CMS yang berhasil dirender; nol komponen select tidak berarti SimpleBar juga boleh dilepas.
- Spectrum dipakai form Cover Image; Croppie pada halaman pengelolaan foto; Coloris pada pengaturan warna. Nol rule pada keadaan awal Croppie/fullscreen/modal bukan bukti file tidak dibutuhkan.
- Font aktif tetap dibutuhkan. Pelacakan rule Chrome tidak mengukur pemakaian @font-face dengan cara yang sama; font dengan usedRules=0 tidak dianggap unused.

## Matriks semua URL yang diuji

| URL | Desktop HTTP | Mobile HTTP | CSS unik pada sampel |
|---|---:|---:|---:|
| `/account` | 200 | 200 | 10 |
| `/account/general` | 200 | 200 | 10 |
| `/account/profilepicture` | 200 | 200 | 11 |
| `/article` | 200 | 200 | 8 |
| `/auth/forgotpassword` | 200 | 200 | 6 |
| `/auth/login` | 200 | 200 | 6 |
| `/auth/signup` | 200 | 200 | 6 |
| `/awesome_admin` | 200 | 200 | 10 |
| `/awesome_admin/appearance` | 200 | 200 | 11 |
| `/awesome_admin/config` | 200 | 200 | 21 |
| `/awesome_admin/header-navigation` | 200 | 200 | 13 |
| `/awesome_admin/language` | 200 | 200 | 10 |
| `/awesome_admin/language/translated` | 200 | 200 | 10 |
| `/awesome_admin/language/untranslated` | 200 | 200 | 10 |
| `/awesome_admin/menu` | 200 | 200 | 10 |
| `/awesome_admin/menu/be` | 200 | 200 | 10 |
| `/awesome_admin/menu/be/categorymenu` | 200 | 200 | 10 |
| `/awesome_admin/menu/be/parentmenu` | 200 | 200 | 10 |
| `/awesome_admin/menu/be/submenu` | 200 | 200 | 10 |
| `/awesome_admin/menu/fe` | 200 | 200 | 10 |
| `/awesome_admin/menu/fe/categorymenu` | 200 | 200 | 10 |
| `/awesome_admin/menu/fe/parentmenu` | 200 | 200 | 11 |
| `/awesome_admin/menu/fe/submenu` | 200 | 200 | 10 |
| `/awesome_admin/permission` | 200 | 200 | 10 |
| `/awesome_admin/role` | 200 | 200 | 10 |
| `/awesome_admin/role/create/v2` | 200 | 200 | 10 |
| `/awesome_admin/role/edit/v2/1` | 200 | 200 | 10 |
| `/awesome_admin/smtp` | 200 | 200 | 10 |
| `/awesome_admin/themes` | 200 | 200 | 10 |
| `/awesome_admin/user` | 200 | 200 | 10 |
| `/awesome_admin/user/edit/1` | 500 | 500 | 0 |
| `/dashboard` | 200 | 200 | 10 |
| `/event` | 200 | 200 | 10 |
| `/event/registrations` | 200 | 200 | 0 |
| `/home` | 200 | 200 | 8 |
| `/manage_article` | 200 | 200 | 11 |
| `/manage_article/add` | 200 | 200 | 11 |
| `/manage_article/edit/5059` | 200 | 200 | 11 |
| `/manage_article/templates` | 200 | 200 | 12 |
| `/manage_coverimage` | 200 | 200 | 11 |
| `/manage_coverimage/add` | 200 | 200 | 12 |
| `/manage_coverimage/edit/1` | 200 | 200 | 12 |
| `/manage_event` | 200 | 200 | 10 |
| `/manage_event/add` | 200 | 200 | 11 |
| `/notification` | 200 | 200 | 10 |
| `/notification/empty` | 200 | 200 | 10 |
| `/notification/filled` | 500 | 500 | 0 |
| `/profile` | 200 | 200 | 11 |
| `/setup` | 200 | 200 | 5 |
| `/workflow` | 200 | 200 | 11 |
| `/workflow/create` | 200 | 200 | 11 |

## CSS custom dalam scope (25)

| File | Bukti audit |
|---|---|
| `public/assets/css/article/article-editor-content.css` | Runtime aktif |
| `public/assets/css/article/article-frontend-2026.css` | Runtime aktif |
| `public/assets/css/article/article-template-manager-2026.css` | Runtime aktif |
| `public/assets/css/article/manage-article-2026.css` | Runtime aktif |
| `public/assets/css/aruna-v4.css` | Runtime aktif |
| `public/assets/css/awesome-admin-header-navigation-fullscreen.css` | Runtime aktif |
| `public/assets/css/awesome-admin-header-navigation.css` | Runtime aktif |
| `public/assets/css/cms-bootstrap-theme.css` | Runtime aktif |
| `public/assets/css/extending-css-bootstrap-5.css` | Runtime aktif |
| `public/assets/css/frontend-menu-dropdown.css` | Runtime aktif |
| `public/assets/css/phoenix-cms.css` | Runtime aktif |
| `public/assets/css/theme-responsive-typography.css` | Runtime aktif |
| `public/assets/css/themes/arunika_aurora/arunika_aurora.css` | Tema alternatif; source saja |
| `public/assets/css/themes/arunika_aurora/mobile-v2.css` | Tema alternatif; source saja |
| `public/assets/css/themes/arunika_equinox/arunika_equinox.css` | Tema alternatif; source saja |
| `public/assets/css/themes/arunika_equinox/mobile-v2.css` | Tema alternatif; source saja |
| `public/assets/css/themes/arunika_lucent/arunika_lucent.css` | Runtime aktif |
| `public/assets/css/themes/arunika_lucent/mobile-v2.css` | Runtime aktif |
| `public/assets/css/themes/arunika_prism/arunika_prism.css` | Tema alternatif; source saja |
| `public/assets/css/themes/arunika_prism/mobile-v2.css` | Tema alternatif; source saja |
| `public/assets/css/themes/calm_green/aruna-admin-v7-calm-green.css` | Tema alternatif; source saja |
| `public/assets/css/themes/calm_green/phoenix-cms-calm-green.css` | Tema alternatif; source saja |
| `public/assets/css/themes/default/aruna-admin-v7-default.css` | Tema alternatif; source saja |
| `public/assets/css/themes/default/phoenix-cms-default.css` | Tema alternatif; source saja |
| `public/assets/css/workflow-editor.css` | Runtime aktif |

## Halaman yang belum dapat dinilai penuh

- /notification/filled: HTTP 500 pada desktop/mobile; controller menunjuk notification.notification_filled yang file view-nya tidak tersedia.
- /awesome_admin/user/edit/1: HTTP 500 pada desktop/mobile; penyebab server belum didiagnosis karena di luar audit pemuatan CSS.
- /auth/recoveryaccount: observasi awal menghasilkan 419 tanpa sesi pemulihan valid; tidak mengirim email/reset password untuk membuat sesi.
- Tema alternatif diperiksa melalui source enam layout CMS/auth/frontend; tidak mengubah tema global untuk menguji semuanya secara runtime.
- Sampel edit memakai record yang sudah tersedia. Tidak semua record, isi modal, hover/focus, dark mode, role non-admin, dan template auth alternatif diuji secara menyeluruh.
- Persentase coverage hanya karakter rule yang teramati pada keadaan sampel, bukan bukti seluruh deklarasi di rule terpakai atau instruksi menghapus bagian lain.

## Source, memori, Graphify, backup, dan perubahan

- Memori E: yang relevan: 20260903-arunika-lucent-mobile-final-handoff.md; 20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md. Konteks theme juga ditemukan pada handoff migrasi 13 Agustus, lalu diverifikasi kembali ke source/record aktif.
- Graphify explain: custom_theme(); Auth_Controller web; arunika_lucent/auth/auth_layout.blade.php. Relasi stylesheet EXTRACTED dicocokkan source. Panggilan model yang dilabeli INFERRED diverifikasi langsung pada Common.php; tidak diperlakukan sebagai fakta dari graph saja.
- Source utama: app/Helper/Common.php; Auth_Controller.php; auth/default/login.blade.php; enam layout tema CMS/auth/frontend; 127 view dalam view-css-map-reviewed.json; controller/action dalam route-view-map.json; script daftar Cover Image; 25 stylesheet custom.
- Source aplikasi tidak diubah. Tidak ada backup source baru. Backup revisi alat audit ada di folder audit ini dengan suffix .bak_20261030_*. Tidak memakai backup sebagai source.
- Tiga perubahan user di Manage Article tetap dipertahankan. Tidak staging, commit, push, deploy, atau build ulang.
- Graphify tidak diperbarui, sesuai instruksi manual.

## Berkas bukti

- page-css-matrix.csv: stylesheet per URL/viewport, penggunaan rule indikatif, dan keberadaan komponen.
- browser-cases.json: 102 hasil browser, pemulihan sheet login, dan CSS yang dimuat.
- view-css-map-reviewed.json / route-view-map.json / theme-and-routes.json: jalur source, layout, dan tema aktif.
- css-parse-reviewed.json / syntax-browser.json: hasil parser dan pemulihan browser atas deklarasi invalid.
- cache-browser.json: permintaan/transfer CSS antarhalaman.
- verification-summary.json: ringkasan pemeriksaan dan batas scope.
- auth-browser.json dan auth-browser-v2.json berisi percobaan alat yang gagal; kesimpulan memakai auth-browser-v3.json yang berhasil.
