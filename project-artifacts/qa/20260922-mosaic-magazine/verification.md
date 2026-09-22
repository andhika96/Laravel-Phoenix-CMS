# Mosaic Magazine — implementasi dan verifikasi

Tanggal: 22 September 2026
Project: D:\Laragon\www\laravel-13-phoenix

## Scope dan hasil

- Desain Mosaic Magazine diterapkan: hero foto lebar dengan judul overlay desktop/tablet, judul di bawah foto pada mobile, kartu terbuka, hierarki judul lebih jelas.
- Warna memakai variabel Article yang berasal dari `--ph-theme-primary`; warna oranye mockup tidak dijadikan warna tetap.
- Opsi khusus `mosaic_magazine`: `supporting_count` (0–24), `match_grid`, `columns.desktop/tablet/mobile`, `proportion` (`equal` atau `asymmetric`).
- Default: 2 post pembuka, kolom 2/2/1, sama rata; regular grid tetap 3/2/1. Nilai 0 menghapus grup pembuka, tidak menghapus hero.
- Proporsi 60:40 hanya untuk dua kolom; grup pembuka yang hanya memiliki satu post menggunakan satu kolom.
- Hero + pembuka + regular membagi artikel dalam halaman yang sama tanpa duplikasi atau artikel terlewat. Total per halaman tetap memakai pengaturan lama.
- Search default di kanan, select kategori default aktif di kiri; pengaturan tersimpan yang eksplisit tetap dihormati. Search via Enter/tombol, kategori auto-submit memakai Vue frontend yang sudah ada.
- Hasil search/category/tag memakai regular grid tanpa hero; ringkasan hasil dan Clear filters tersedia. Fallback tanpa JavaScript memakai tombol Filter.
- Preview utama dan modal memakai Blade yang sama dengan frontend; thumbnail SVG pemilih template diperbarui.
- Default tinggi thumbnail Mosaic menjadi 13.5rem. Tinggi thumbnail kustom yang sudah tersimpan tetap dipertahankan.
- Tidak mengaktifkan Mosaic sebagai template default di database aplikasi, tidak commit/push, tidak update Graphify.

## Source yang dimodifikasi

1. app/Support/Article/ArticleTemplateOptions.php
2. resources/views/article/templates/archive/mosaic-magazine.blade.php
3. resources/views/article/templates/partials/archive-header.blade.php
4. resources/views/manage_article/templates/index.blade.php
5. public/assets/css/article/article-frontend-2026.css
6. public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js
7. public/assets/images/article/template-previews/mosaic-magazine.svg
8. tests/Feature/Article/ArticleTemplateOptionsTest.php — assertion default non-Editorial memakai Balanced Card Grid; default baru Mosaic dicakup tes baru.

Source baru: resources/views/manage_article/templates/partials/options-mosaic-magazine.blade.php

## Source tambahan yang dibaca

- app/Support/Article/ArticleTemplateCatalog.php
- app/Support/Article/ArticleTemplatePreviewFixture.php
- app/Models/Article/ArticleTemplateSetting.php
- app/Http/Controllers/Web/Article/ArticleFrontendController.php
- app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php
- app/Http/Requests/Article/UpdateArticleTemplateSettingRequest.php
- resources/views/article/archive.blade.php
- resources/views/article/templates/archive/minimal-reading-list.blade.php
- resources/views/article/templates/archive/mosaic-classic.blade.php (analisis awal)
- resources/views/article/templates/partials/archive-title.blade.php
- resources/views/article/templates/partials/media-link.blade.php
- resources/views/article/templates/partials/template-shell-attributes.blade.php
- resources/views/manage_article/templates/preview.blade.php
- resources/views/manage_article/templates/partials/options-styling.blade.php
- public/assets/js/vue3/article/vueV3-article-frontend-2026.js
- public/assets/js/article/article-theme-color-sync-2026.js
- public/assets/css/article/article-template-manager-2026.css
- Tes Article terkait, phpunit.xml, tests/TestCase.php, package.json.

## Memori dan Graphify

- E:\AI\Memories tidak tersedia di sesi ini. Pencarian dilakukan, tanpa scan luas.
- C:\Users\aruna\.codex\memories\MEMORY.md dipakai untuk konteks normalizer/persistence/preview Article; kondisi diverifikasi pada source aktif.
- Query: `graphify query "ArticleTemplateOptions mosaic-magazine template manager options grid frontend" --budget 1600`.
- Pada analisis sebelumnya: query Mosaic Magazine dan `graphify explain 'mosaic-magazine.blade.php'` memberi lima relasi include `EXTRACTED`, dicocokkan dengan Blade. Hasil heuristik yang tidak relevan tidak dijadikan dasar perubahan.
- Graph tidak diperbarui karena instruksi pengguna meminta pembaruan manual. Peta untuk source yang berubah kini perlu pembaruan pada perintah berikutnya.

## Backup

Folder: project-artifacts/backups/20260922_120301-mosaic-magazine/

- Enam source awal disalin dengan struktur path dan ekstensi `.bak`; hash sumber dan backup diverifikasi sama sebelum perubahan.
- Tambahan backup: `mosaic-magazine.svg.bak` dan `ArticleTemplateOptionsTest.php.bak` sebelum perubahan kedua file tersebut.
- Source baru dan test baru tidak menggantikan file lama.
- Worktree awal hanya mempunyai folder mockup dari percakapan ini; folder tersebut dipertahankan.

## Tes otomatis — hasil akhir

Command:

```powershell
php vendor/bin/phpunit tests/Feature/Article project-artifacts/tests/MosaicMagazineTest.php project-artifacts/tests/MosaicMagazinePersistenceTest.php --cache-directory=project-artifacts/tests/.phpunit-cache
```

Hasil: **51 tests, 720 assertions, PASS**. Persistence diuji di SQLite in-memory melalui fixture tes yang sudah ada, bukan database aplikasi.

Node: seluruh `tests/article*.test.mjs`, `tests/manage-article-template-manager.test.mjs`, dan `project-artifacts/tests/mosaic-magazine.test.mjs`: **111 tests, PASS**.

- PHP lint normalizer: PASS.
- Node syntax check manager JS: PASS.
- `php artisan view:cache`: PASS.
- `git diff --check`: PASS.
- Tidak menjalankan Vite build: source yang berubah merupakan public CSS/JS yang dimuat langsung dan Blade/PHP, tanpa perubahan bundle.

Log: `phpunit-article.txt` dan `node-article.txt` di folder ini.

Tes baru dimulai dengan kegagalan pada opsi/region yang belum tersedia, lalu lulus setelah implementasi. Error kompilasi Blade ketika inline `@php(...)` mendahului blok `@php` diselesaikan dengan blok PHP yang eksplisit di file Mosaic. Assertion lama default thumbnail diperbarui untuk mencerminkan scope desain baru. Review independen tidak menemukan masalah tambahan; default shell padding dibatasi agar tidak menimpa custom padding.

## Verifikasi browser runtime

Browser menggunakan harness lokal `router.php` di localhost:8769, yang merender source Blade/JS/CSS aktif dengan sample fixture aplikasi. Semua POST ditolak; pengujian tidak menyimpan setting CMS nyata.

- Manager utama dan modal Template Options: hero baru serta thumbnail baru terlihat.
- Mengubah Posts after hero dari 2 ke 3 dan mengaktifkan Match regular grid columns: iframe modal memuat 3 post dalam track `377.333px 377.333px 377.333px`.
- Apply changes meneruskan 3 post / 3 kolom ke iframe utama.
- Desktop 1440px: hero 1180px, pembuka 578px/578px, regular tiga track 377.333px; tidak overflow horizontal.
- Tablet 768px: pembuka/regular dua track 348.333px; tidak overflow horizontal.
- Mobile 390px: satu kolom, search berada di atas select, judul hero di bawah gambar; tidak overflow horizontal.
- Empat post pembuka, mode gambar asset, proporsi asimetris pada tablet: track 418px/278.667px (60:40), tidak overflow.
- Aksen hijau `#1aa879` dan ungu `#6542d7` diteruskan lewat mekanisme theme_color yang sudah ada; kedua warna terlihat di kontrol.
- Supporting count 0: tidak ada grup pembuka, lima post fixture berada di regular grid.
- Custom shell padding tablet 60px atas / 70px bawah tetap dipakai.
- Alur frontend memakai JS aktif: Enter dengan `Designing` menghasilkan satu hasil tanpa hero. Pilih kategori Design mempertahankan search dan menghasilkan URL `?search=Designing&category=2`. Clear filters kembali ke hero dan enam artikel fixture. Back mengembalikan URL dan nilai kedua kontrol; penyelesaian request setelah Back tidak diperiksa terpisah.
- Console yang diperiksa pada manager dan halaman fixture: tidak ada error/warning.

## Batas verifikasi

- Pengujian browser dilakukan pada harness lokal dengan source aktif dan data contoh; bukan sesi Firefox terautentikasi pengguna dan bukan archive publik dengan Mosaic disimpan sebagai default.
- Manager Save Template diuji melalui HTTP test SQLite, bukan diklik pada database aplikasi.
- Pagination, route, dan regresi template lain tercakup tes Article; tidak seluruh tema CMS dan seluruh kombinasi opsi diperiksa visual.
- Setting lama tetap dihormati: jika kategori sebelumnya disimpan nonaktif, aktifkan pada Archive toolbar; jika tinggi thumbnail lama disimpan kecil, atur ulang pada Thumbnail.
- Graphify belum diperbarui sesuai instruksi pengguna.

## Cara melihat

Hard refresh halaman Manage Article Templates, pilih Mosaic Magazine. Buka Template Options > Mosaic Magazine untuk pengaturan baru; Grid columns mengatur grid reguler. Apply changes mengubah draft/preview, Save Template mengikuti alur penyimpanan aplikasi yang sudah ada.
