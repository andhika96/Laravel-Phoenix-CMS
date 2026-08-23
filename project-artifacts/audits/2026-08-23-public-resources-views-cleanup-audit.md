# Audit Cleanup `public`, `resources`, dan `resources/views`

Tanggal: 2026-08-23  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Mode: audit read-only; belum ada penghapusan, pemindahan, atau perubahan source/runtime.

## Ringkasan

- Branch/HEAD saat audit: `main` pada `62bcb370a79f4861b9c35ce296bce76b4a76df10`, sama dengan `origin/main`.
- Working tree bersih sebelum laporan audit ini dibuat.
- `public` memuat sekitar 58.910 file yang terdeteksi `rg`; sebagian besar berasal dari plugin/vendor publik.
- `resources` memuat 528 file, termasuk 311 file Page Builder v2.4 dan 191 file fisik di `resources/views`.
- Ditemukan 4.967 file kandidat dengan ukuran total 263.899.088 byte atau 251,67 MiB.
- Semua 4.967 file kandidat masih tracked dan identik dengan Git `HEAD`, sehingga recovery melalui blob Git tersedia.
- Kandidat tidak boleh langsung dihapus seluruhnya. Kelompok high-confidence dapat dieksekusi setelah approval; aset publik tanpa referensi internal tetap membutuhkan keputusan karena direct external URL tidak dapat dibuktikan dari source/database.

## Metode verifikasi

1. Membaca memori cleanup 2026-08-22 dan mempertahankan `project-artifacts`, `public/mockups`, `graphify-out`, `frankenphp`, dan `Caddyfile`.
2. Memeriksa `git status`, HEAD, upstream, file tracking, dan kecocokan kandidat terhadap `HEAD`.
3. Menggunakan graph existing di `graphify-out/graph.json` untuk menelusuri route/controller/view/Page Builder/File Manager yang aktif.
4. Membuka source aktual yang ditunjukkan Graphify dan melakukan pencarian fixed-string dengan `rg`.
5. Memindai 224 kolom teks database aktif untuk referensi nama/path aset; tidak ditemukan hit untuk kandidat aset statis, video demo, slideshow, gamification icons, rendition, CSS/JS lama, logo/SVG kandidat, dan plugin kandidat.
6. Menjalankan tes resolver rendition: 5 test lulus, 19 assertion.

Catatan Graphify: `.graphifyignore` mengecualikan `public/assets/plugins/`, sehingga keputusan plugin tidak berasal dari edge Graphify. Keputusan tersebut memakai source aktual, konfigurasi, database read-only, Git history, dan pencarian literal.

## Rekap kandidat

| Kelompok | File | Ukuran | Confidence | Rekomendasi |
|---|---:|---:|---|---|
| Versi/root plugin tidak direferensikan | 4.337 | 161,97 MiB | Tinggi | Hapus setelah backup manifest dan approval |
| Image publik tanpa source/database reference | 40 | 33,97 MiB | Menengah | Pindahkan ke artifact/backup atau hapus setelah konfirmasi direct URL |
| Companion asset yatim | 31 | 25,12 MiB | Menengah-tinggi | Bersihkan bersama halaman/demo pemiliknya |
| Rendition Page Builder generated | 169 | 15,80 MiB | Tinggi | Hapus dari Git dan ignore output directory |
| Snapshot build File Manager V2 | 374 | 14,59 MiB | Tinggi | Hapus setelah manifest/HEAD recovery gate |
| Artifact di `resources/views` | 11 | 0,15 MiB | Tinggi | Relokasi ke `project-artifacts` atau hapus sesuai jenis |
| Prototype publik lama | 3 | 0,07 MiB | Tinggi | Relokasi ke `project-artifacts/mockups` |
| Debug script publik | 2 | 1.973 byte | Sangat tinggi | Prioritas hapus karena risiko keamanan |

Potensi total: 4.967 file, 251,67 MiB. Kelompok yang tidak memasukkan 40 image publik dan 31 companion asset berjumlah sekitar 192,59 MiB dan memiliki confidence lebih tinggi.

## Kandidat prioritas tinggi

### 1. Debug script di web root

- `public/phpinfo.php`: menjalankan `phpinfo()` dan mengekspos konfigurasi PHP/runtime bila URL dapat diakses.
- `public/change.php`: menjalankan operasi tulis terhadap `testing.php` saat file dipanggil.

Keduanya tidak memiliki route/controller/view/test/database consumer. Keduanya berasal dari commit lama dan cocok dengan `HEAD`.

### 2. Snapshot build File Manager V2

Sebelas directory berikut berisi 374 file/15,30 MB decimal dan tidak direferensikan oleh source, route, view, test, build config, atau database:

- `public/assets/plugins/filemanager_v2.bak_20260726_031716_root_listing_and_breadcrumb`
- `public/assets/plugins/filemanager_v2.bak_20260726_032641_persistent_star_indicator`
- `public/assets/plugins/filemanager_v2.bak_20260726_033217_high_contrast_star_badge`
- `public/assets/plugins/filemanager_v2.bak_20260726_051722_unified_action_modal`
- `public/assets/plugins/filemanager_v2.bak_20260726_052045_unified_action_modal_final`
- `public/assets/plugins/filemanager_v2.bak_20260726_101431_folder_tree_and_modal_footer`
- `public/assets/plugins/filemanager_v2.bak_20260726_102300_reduced_motion_scope`
- `public/assets/plugins/filemanager_v2.bak_20260726_111329_folder_details_tree_rename`
- `public/assets/plugins/filemanager_v2.bak_20260726_113343_selection_tree_regressions`
- `public/assets/plugins/filemanager_v2.bak_20260726_115034_checklist_only_selection`
- `public/assets/plugins/filemanager_v2.bak_20260726_120143_folder_open_and_checklist_toggle`

Build aktif adalah `resources/js/filemanager_v2` -> `vite.filemanager-v2.config.js` -> `public/assets/plugins/filemanager_v2`.

### 3. Plugin/version public yang tidak direferensikan

- `public/assets/plugins/bootstrap/5.3.3`
- `public/assets/plugins/bootstrap/5.3.6_source`
- `public/assets/plugins/bootstrap/5.3.8`
- `public/assets/plugins/bootstrap/5.3.8_custom`
- `public/assets/plugins/bootstrap/5.3.8_source`
- `public/assets/plugins/echarts/5.6.0`
- `public/assets/plugins/fontawesome/5.15.4`
- `public/assets/plugins/datatables`
- `public/assets/plugins/uikit-compatible-w-bootstrap`

Versi yang harus dipertahankan:

- Bootstrap `5.3.6` dan `5.3.6_custom`.
- ECharts `5.5.1`.
- Font Awesome `5.15.3` dan `6.5.1`.
- CKFinder, CKEditor, Vue, File Manager V2, Axios, Lodash, Simplebar, Sortable, Spectrum.

`config/breadcrumbs.php` memilih `breadcrumbs::bootstrap5`; penyebutan UIkit hanya berada di komentar daftar template dan tidak mengaktifkan plugin UIkit publik.

### 4. Rendition Page Builder generated

- Directory: `public/assets/pagebuilder_elementor/renditions`
- Isi: 169 file, 15,80 MiB, seluruhnya tracked.
- Database: tidak ada referensi path atau basename rendition.
- Runtime: tiga `ImageRenditionResolver` memakai output directory yang sama dan membuat ulang file bila destination belum ada.
- Verifikasi: 5 test resolver lulus, 19 assertion.

Rekomendasi implementasi: hapus file generated dari Git dan tambahkan ignore khusus directory rendition, tanpa menghapus resolver atau endpoint rendition.

### 5. Artifact yang tertinggal di view/public

- `public/prototypes/` berisi tiga lab Page Builder tanpa consumer. Relokasi ke `project-artifacts/mockups/pagebuilder-labs/`.
- `resources/views/auth/templates/html/` berisi empat HTML `*_RAW*`; mail aktif memakai `resources/views/auth/templates/forgotpassword_email.blade.php`.
- `resources/views/filemanager/filemanager.blade.zip` adalah archive backup tanpa consumer.
- `resources/views/pagebuilder/dataType.txt` dan `resources/views/pagebuilder/style.css` tidak direferensikan.
- `resources/views/themes/calm_green/frontend/asdasds.txt` tidak direferensikan.
- `resources/views/dashboard/newTheme.blade.php` tidak memiliki route/controller consumer.
- `resources/views/homepage/homepage_test.blade.php` tidak memiliki route/controller consumer.
- `resources/views/welcome.blade.php` hanya tersisa pada route yang dikomentari.

## Companion asset tanpa consumer aktif

### Pasangan demo lama

- `resources/views/dashboard/newTheme.blade.php` -> satu-satunya consumer `public/assets/beta_css/new.css`.
- `resources/views/homepage/homepage_test.blade.php` -> satu-satunya consumer video `public/assets/videos/MG_10s_Left_Hand_Drive.mp4`; URL di view masih menunjuk domain Laravel 12 lama.

### CSS/JS lama

- `public/assets/css/aruna-admin-v6.css`
- `public/assets/css/aruna-admin-v7-phoenix-elegant.css`
- `public/assets/css/aruna-admin-v7-simple-part-2.css`
- `public/assets/css/aruna-admin-v7-simple.css`
- `public/assets/css/aruna-admin-v7.css`
- `public/assets/css/aruna-v3.css`
- `public/assets/css/base_color_from_heroui_theme.txt`
- `public/assets/css/base_color.txt`
- `public/assets/js/global/ckeditor/cs-ckeditor5.js`
- `public/assets/js/vue3/filemanager/vueV3-filemanager-embed-206.js`
- `public/js/pagebuilder/pb-widgets.js`

`public/assets/css/aruna-v4.css` bukan kandidat karena dipakai `resources/views/setup/setup.blade.php`.

### Slideshow, font, logo, dan SVG

- Seluruh tujuh file di `public/assets/slideshow/` tidak memiliki source/database reference.
- `public/assets/fonts/nunito/ttf/Nunito-Italic.ttf` dan `public/assets/fonts/nunito/woff2/Nunito-Italic.woff2` tidak direferensikan CSS.
- `public/assets/logos/Artboard 1 copy 2.png` dan `public/assets/logos/laraphoenix_onlybird_colored.png` tidak direferensikan.
- Seluruh tujuh file di `public/assets/svgs/` tidak direferensikan.

## Image publik tanpa consumer internal

Database-wide dan source scan tidak menemukan consumer untuk:

- Seluruh `public/assets/images/gamification-icons/` (26 file, 13,92 MiB).
- `30ac96f3c5c0e9d0303dd77410fe1992.jpg`
- `palm-trees-palm-oil-plantation-south-east-asia.jpg`
- `pexels-jplenio-2850287.jpg`
- `pexels-jplenio-2850287(1).jpg`
- `pexels-pixabay-33227.jpg`
- `pexels-pixabay-33227(1).jpg`
- `pexels-quang-nguyen-vinh-222549-2649403.jpg`
- `pexels-scottwebb-593158.jpg`
- `pexels-simon73-1765729.jpg`
- `pexels-umkreisel-app-2832041.jpg`
- `pexels-visually-us-1643402.jpg`
- `sunrise-1014712_1280.jpg`
- `sunrise-1014712_1920.jpg`
- `trees-on-forest-at-daytime-1083515-cropped.png`

Catatan risiko: file di bawah `public` dapat dipakai oleh direct external URL yang tidak tercatat di repository/database. Kelompok ini sebaiknya direlokasi ke backup/artifact dahulu atau diverifikasi melalui access log sebelum penghapusan permanen.

## Harus dipertahankan

- `public/mockups`: sengaja dipertahankan pada cleanup sebelumnya dan masih memiliki test/reference.
- `public/build`: output Vite yang ignored dan dipakai runtime.
- `public/storage`: junction ke `storage/app/public`.
- `public/assets/plugins/ckfinder`: runtime CKFinder aktif; jangan diperlakukan sebagai vendor yang boleh dipangkas generik.
- `resources/pagebuilder_elementor_v24`: source modular v2.4 aktif.
- `resources/data/pagebuilder_elementor*_shapes.json`: ketiganya dipakai editor/renderer masing-masing versi.
- `resources/js/filemanager_v2`: source build dan test aktif.
- `resources/css/app.css`: kosong tetapi tetap merupakan input `vite.config.js`; jangan dihapus tanpa mengubah build contract.
- Seluruh layout theme, auth variant, vendor mail/breadcrumb view, Page Builder v2.0/v2.3/v2.4, dan `notification.notification_testing`: sebagian dipanggil secara dinamis atau melalui route aktif.
- `resources/views/pagebuilder/pagebuilder_edit.blade.php`: jangan dihapus; route edit aktif tetapi controller saat ini menunjuk nama view yang salah.

## Temuan wiring yang bukan cleanup

- Route `/notification/filled` memanggil `notification.notification_filled`, tetapi file view tersebut tidak ada.
- Route legacy `/pagebuilder/edit/{idOrSlug}` memanggil `pagebuilder.editor`, sedangkan file yang tersedia adalah `pagebuilder.pagebuilder_edit`.

Kedua temuan ini merupakan bug/wiring lama dan tidak boleh “diselesaikan” dengan menghapus view terkait. Perbaikannya membutuhkan scope terpisah.

## Batas verifikasi

- Tidak ada file kandidat yang dihapus atau dipindahkan pada audit ini.
- Tidak ada build yang dijalankan karena belum ada source/output change.
- Direct external URL/access log belum diperiksa; ini terutama memengaruhi confidence image, video, slideshow, logo, dan SVG publik.
- Browser QA belum diperlukan pada tahap audit.
- `php artisan route:list` tidak digunakan karena baseline project memiliki controller API Testing yang hilang; route relevan diverifikasi langsung dari source route/controller.

## Rencana eksekusi setelah approval

1. Buat backup terpusat dan manifest SHA-256 di `project-artifacts/backups`, lalu verifikasi seluruh payload dan Git `HEAD` recovery.
2. Hapus dua public debug script dan 11 snapshot build File Manager V2.
3. Hapus versi/root plugin yang tidak direferensikan.
4. Relokasi prototype/raw/template/reference artifact ke `project-artifacts`.
5. Hapus rendition generated dan tambahkan ignore khusus.
6. Eksekusi aset confidence menengah hanya sesuai keputusan user: relokasi dulu atau hapus setelah pemeriksaan access log.
7. Jalankan build Laravel/Vite/File Manager V2, focused PHP/Node tests, broad baseline comparison, `git diff --check`, dan browser read-only smoke test untuk halaman publik/protected yang relevan.
8. Update Graphify incremental hanya bila source/runtime berubah substantif.
