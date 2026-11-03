# Laravel 13 Phoenix — penataan CSS, auth mandiri, sidebar, dan rencana Tailwind

Tanggal penyimpanan 3 November 2026, WIB. Percakapan mencakup pekerjaan 30 Oktober hingga 3 November 2026. Workspace `D:\Laragon\www\laravel-13-phoenix`.

## Tujuan dan konteks awal

Gumara meminta audit CSS yang dipakai dan tidak dipakai, pengelompokan file menurut fungsi, serta pemisahan pengaturan warna autentikasi dari tema CMS. Setelah perubahan auth, screenshot menunjukkan teks Dashboard putih pada highlight ungu muda. Temuan itu ditelusuri sebagai masalah aturan CSS CMS. Percakapan terakhir membahas kemungkinan pilihan Bootstrap 5 dan Tailwind untuk CMS utama dan menghasilkan rencana migrasi untuk ditinjau.

## Penataan aset pada 30 Oktober

- Enam belas CSS aktif dipindahkan dari root `public/assets/css` ke subfolder berdasarkan fungsi. Folder aktif meliputi `shared`, `cms`, `article`, `frontend`, `header-navigation`, `pagebuilder` dengan subfolder versi, `setup`, `themes`, dan `workflow`. Folder `auth` ditambahkan pada pekerjaan berikutnya.
- Pada tahap relokasi, isi 32 CSS aktif tetap identik berdasarkan SHA-256. Path consumer diperbarui pada 35 view dan 53 file test/helper. Catatan warna dan 18 backup lama dipindahkan keluar root CSS.
- Tiga keluaran Vite lama dari `public/build` diarsipkan ke `project-artifacts/builds/legacy-public-build-20261030`. Sebelas HTML mockup dan empat gambar dari `public/mockups` dipindahkan ke `project-artifacts/mockups/legacy-public-mockups-20261030`. Sebelas test consumer mockup mengikuti path baru. Build File Manager V2 aktif berada di `public/assets/plugins/filemanager_v2`.
- Verifikasi historis penataan CSS mencakup 256 tes Node dan 5 tes PHP dengan 8.619 assertion. Relokasi build/mockup mencakup 23 tes Node dan delapan halaman sampel HTTP 200. `npm run build` dapat membuat `public/build` kembali karena konfigurasi default Vite masih menunjuk ke sana.
- Laporan dan backup utama: `project-artifacts/css-organization-20261030/report.md`, `backups_20261030_142705`, serta `project-artifacts/public-build-mockups-relocation-20261030/report.md`, `backups_20261030_145709`. Masing-masing laporan menunjuk manifest pemulihan.

## Auth mandiri dan Primary Color

- Login, signup, forgot password, dan reset password memakai `resources/views/auth/auth_layout.blade.php`. Seluruh 16 view dalam `resources/views/auth/{default,card,split_left,split_right}` memakai layout itu. Layout memuat `public/assets/css/auth/auth-base.css` dan satu CSS halaman yang sesuai, yakni `login.css`, `signup.css`, `forgot-password.css`, atau `reset-password.css`.
- Bootstrap, Font Awesome, dan font Site Config menjadi fondasi bersama. Dokumen auth tidak memuat stylesheet tema CMS, `cms-bootstrap-theme.css`, atau `shared/phoenix-cms.css`. Scope CSS auth ialah `.ph-auth-page`; pilihan layout, background, dan Color Nuances tetap diatur per halaman.
- Manage Appearance memiliki satu field Primary Color di atas Color Nuances untuk setiap halaman. Blade dan Vue mengikuti pola existing dan memakai Coloris yang sudah ada. Penyimpanan memakai `page_theme_settings.page_primary_color`, format HEX enam digit, default `#1FA675`.
- Source utama `app/Support/AuthAppearance.php`, `app/Models/Awesome_Admin/Page_Theme_Setting.php`, `app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Appearance_Controller.php`, `resources/views/awesome_admin/awesome_admin_appearance.blade.php`, `public/assets/js/vue3/manage_appearance/vueV3-manage-appearance-2026.js`, dan `database/migrations/2026_10_30_163000_add_primary_color_to_page_theme_settings.php`. Migration sudah dijalankan di DB lokal pada pekerjaan tersebut, batch 34.
- Teks tombol normal, hover, dan active memilih hitam atau putih berdasarkan kontras warna latar masing-masing. Fokus memakai outline. Disabled memakai latar yang dicampur putih dan teks hitam tetap. Permintaan uji khusus kuning/matte dan kemungkinan perhitungan disabled tersendiri berhenti pada batas 16:50 WIB yang diminta pengguna. Checkpoint `project-artifacts/auth-primary-20261030/CHECKPOINT_1650_button-text-contrast.md` mencatat follow-up tersebut.
- Koreksi label menyamakan Primary Color, Color Nuances, Background Image, dan Interface Theme dengan class `form-label fw-bold mb-1`. Browser mengukur 13 px pada 1280, 14 px pada 1920, dan 13,45 px pada 390. Kesalahan kutip lama yang menghalangi render login card juga diperbaiki.
- Verifikasi historis: 7 tes PHP dengan 34 assertion, 18 tes regresi Node, 16 layout auth pada desktop dan mobile, login admin, dan percobaan simpan warna ungu dari Appearance sampai tampil di login. Warna uji dipulihkan dan pengaturan lama dibandingkan dengan snapshot. Reset dirender dengan fixture. Signup dan pemulihan sungguhan belum diuji. Error `grecaptcha is not defined` juga dapat direproduksi dari versi lama dan berada di luar pekerjaan CSS.
- Laporan, screenshot, dan backup 20 source serta empat row pengaturan tersedia di `project-artifacts/auth-primary-20261030/REPORT.md`, `backup-manifest.json`, `backups_20261030_163029`, dan backup label `awesome_admin_appearance.blade.php.bak_20261030_164434_label_size`.

## Sidebar putih dan verifikasi isolasi pada 3 November

- Penyebabnya adalah rule `.list-group .list-group-item.active` dengan `!important` di `public/assets/css/cms/cms-bootstrap-theme.css`. Rule sudah ada pada commit `0e8065faf` tanggal 30 Oktober 2026 pukul 15:08:31 WIB, sebelum pekerjaan Primary Color auth dimulai sekitar 16:30 WIB.
- Rule sekarang mengecualikan navigasi `.ph-sidebar`, `.arv7-menu`, `.arv7-menu-mobile`, dan `.arv7-menu-footer`. Item navigasi memakai warna yang ditetapkan masing-masing tema. Daftar Bootstrap biasa masih memakai aturan bridge. Source yang diubah pada perbaikan ini hanya file bridge CMS.
- Enam tema diuji melalui render Blade aktif dalam Chromium lokal, tanpa mengubah tema global. Desktop 36 kondisi membandingkan rule lama, warna asli tema, dan rule final. Mobile 24 kondisi membandingkan warna tema dan rule final. Lucent desktop light kembali `#171717`; tema lain mengikuti token masing-masing. Total 30 tes regresi Node lulus.
- Seluruh 16 variasi auth tidak memuat CSS CMS. Keenam layout CMS tidak memuat CSS auth. Injeksi kelima CSS auth pada fixture CMS tidak mengubah warna yang diukur. Tes penyimpanan auth dalam transaksi rollback membuktikan row tema CMS tetap sama, 1 tes dengan 3 assertion.
- Bukti dan backup berada pada `project-artifacts/sidebar-active-contrast-20261103/REPORT.md`, `backups_20261103_094515`, dan `project-artifacts/theme-auth-isolation-20261103/REPORT.md`, `backups_20261103_095530`. Firefox, perangkat fisik, dan produksi belum diuji.

## Keputusan rencana Bootstrap 5 dan Tailwind

- Gumara memilih CMS utama dahulu. Auth dan frontend menjadi area terpisah. Editor/canvas Page Builder dan File Manager V2 mempertahankan bundlenya pada fase CMS, dengan peluncur/picker dari CMS tetap menjadi kasus integrasi.
- Tailwind 4.3.3 dan CLI sudah tersedia di proyek. CMS yang diamati memakai Bootstrap CSS 5.3.6 custom dan JS 5.3.6 serta Vue global 3.5.21. Vue npm tercatat 3.5.24. Tailwind menyediakan styling, sedangkan perilaku modal, dropdown, collapse, tooltip, toast, dan interaksi lain perlu implementasi padanan.
- Reka UI adalah kandidat pustaka tanpa desain bawaan. Adopsi bergantung pada prototype yang membuktikan satu runtime Vue, fokus/portal yang benar, dan urutan event existing. Pertahankan VuejsPaginateNext, Coloris, Vue Select, VueDatePicker, CKEditor/CKFinder, Sortable, SimpleBar, serta chart/map existing jika lulus integrasi. Pisahkan setting framework dan tema, muat bundle yang sesuai sebelum render, dan lakukan reload setelah memilih framework.
- Audit statis mencakup 65 view kandidat, 28 script, 239 rujukan API Bootstrap, dan 78 rujukan event. Dua belas halaman CMS nyata merespons HTTP 200 dengan nol page error saat dibuka. Ini baseline baca-saja. Katalog 130 kandidat GET juga mencakup endpoint data dan masih memerlukan pemetaan per action.
- Rencana memiliki tujuh tahap G0–G6 dan 72 kasus uji penerimaan. G0 masih perlu menutup inventaris dan baseline. G1 menguji prototype Lucent dan komponen berisiko seperti urutan toast, modal, reset form, dan kembali ke modal sebelumnya. Setelah itu fondasi, shell, halaman, QA lintas tema, dan pemulihan diuji sebelum opsi Tailwind dibuka. Tidak ada implementasi migrasi, instalasi Reka, atau aktivasi framework baru pada tahap diskusi.
- Dokumen `project-artifacts/plans/cms-css-framework-switch-20261103/PLAN.md`, `COMPONENT-MATRIX.md`, dan `ACCEPTANCE-CHECKLIST.md`. Bukti audit `component-audit.json`, `browser-inventory.json`, dan `verification.json` berada di folder yang sama. Checklist merupakan kriteria rilis, bukan hasil tes migrasi.

## Lanjutan untuk chat berikutnya

1. Baca ringkasan ini dan PLAN.md. Cek source serta status Git aktual. Worktree pada saat penyimpanan masih membawa perubahan auth/sidebar dan artifact terkait. Pertahankan perubahan pengguna yang tidak terkait.
2. Untuk migrasi, gunakan G0 lalu prototype G1 terisolasi setelah instruksi implementasi diberikan. Validasi kesetaraan Bootstrap sebelum membuka Tailwind. Prioritaskan kasus modal berantai, pagination, mobile sidebar, akses terbatas, dan plugin di overlay.
3. Bila warna tombol auth dilanjutkan, mulai dari checkpoint kontras. Uji kuning, hitam, matte terang/gelap, serta tiap state tanpa mengganti pengaturan pengguna secara permanen.
4. Gunakan Graphify sebagai peta lokal dan verifikasi hubungan terhadap source aktif. Pembaruan graph berikutnya tetap mengikuti perintah manual Gumara. Jangan masukkan artifact rencana, backup, file generated, atau rahasia ke graph.
