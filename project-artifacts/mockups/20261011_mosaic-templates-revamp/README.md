# Mosaic Magazine dan Mosaic Classic — revamp source dan preview

Tanggal: 11 Oktober 2026. Project: `D:\Laragon\www\laravel-13-phoenix`.

## Hasil

Kedua revamp sudah diterapkan pada source aktif sesudah implementasi Editorial Journal. Struktur Blade Mosaic yang sudah ada tetap digunakan. Semua artifact preview, pengujian, screenshot, dan checkpoint berada di folder ini; backup source berada di `project-artifacts/backups`.

Preview lokal: `http://127.0.0.1:8776/preview.html`. Pilih Mosaic Magazine atau Mosaic Classic, lalu Desktop, Tablet, atau Mobile. Server hanya melayani folder preview ini pada loopback. Untuk menjalankannya kembali: `node project-artifacts/mockups/20261011_mosaic-templates-revamp/qa/server.cjs` dari root project.

Screenshot lengkap dan viewport tersedia di `qa/` untuk preview interaktif dan di `implementation/` untuk fixture source default. Preview dirender dari Blade, normalizer, typography CMS, dan CSS frontend aktif. `common.css`, `magazine.css`, serta `classic.css` menyimpan draft desain awal dan tidak lagi dimuat oleh HTML preview hasil akhir.

## Perubahan

- Magazine mempertahankan lead foto dengan overlay pada desktop, supporting stories, dan regular grid. Pada mobile, foto dan teks lead terpisah; daftar default menjadi satu kolom.
- Classic mempertahankan lead, sidebar stories, highlights, dan latest grid. Pada mobile, foto sidebar stories berada di atas teks dengan lebar penuh. Default tinggi foto sidebar mobile menjadi `14rem`; desktop/tablet tetap `7rem`, dan nilai tersimpan yang eksplisit tetap dihormati.
- Search memiliki tinggi input/button 48px, default gap 12px yang tetap configurable, label tombol terlihat, serta layout yang mengikuti pilihan posisi search/category. Font menggunakan CSS variables typography CMS.
- Tinggi background thumbnail pada regular cards sekarang mengikuti setting Thumbnail. Asset mode memakai gambar aktual dan mempertahankan fit yang dipilih. Lead/sidebar/highlight Classic menggunakan pengaturan tinggi per region yang sudah ada.
- Story gap, divider, lead/sidebar position, lead layout, supporting count/proportion, grid columns, visibility, thumbnail frame, shell padding, dan pagination tetap option-driven.
- Shared pagination memakai kontrol 44px pada kelima archive templates. Tiga model menggunakan CSS/model shared yang sama. Default footer frame OFF pada empat template yang direvamp; nilai frame ON/OFF yang tersimpan tetap berlaku. Balanced Card Grid mempertahankan default frame yang sudah ada.

Preview Classic menggunakan contoh setting ratio 67:33, Read More ON, dan author OFF. Preview Magazine mencontohkan supporting proportion asymmetric. Ini profil fixture; setting database dan template yang sedang dipilih pengguna tidak diubah. Search/dialog pada preview memakai helper demo; helper tersebut tidak dimasukkan ke aplikasi.

## Source dibaca dan diubah

File source yang diubah pada tahap Mosaic ini:

1. `public/assets/css/article/article-frontend-2026.css`
2. `app/Support/Article/ArticleTemplateOptions.php`
3. `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`
4. `resources/views/article/templates/partials/archive-header.blade.php`
5. `tests/Feature/Article/ArticleTemplateOptionsTest.php`

Source aktif yang juga dibaca: kedua Blade archive Mosaic, `ArticleTemplateCatalog.php`, partial `media-link`, `pagination`, `template-shell-attributes`, serta form `options-styling` dan `options-classic-balanced`. Perubahan Minimal Reading List dan Editorial Journal sebelumnya dipertahankan. `implementation/source-checkpoint-delta.json` menunjukkan hanya lima file tahap Mosaic yang berubah dari checkpoint 12 file; tujuh file lain tetap identik.

## Backup

Backup lima file sebelum perubahan, dengan manifest SHA-256 yang diverifikasi:

`project-artifacts/backups/20261011_014246-mosaic-templates-revamp/manifest.json`

Checkpoint tambahan sebelum mengubah shared pagination:

`project-artifacts/backups/20261011_014246-mosaic-templates-revamp/article-frontend-2026.css.bak_20261011_shared-pagination-size`

Checkpoint artifact preview/QA ada di `qa/backups/`. Tidak dilakukan reset, clean, commit, push, atau perubahan business data.

## Verifikasi terbaru

| Pemeriksaan | Hasil | Bukti |
| --- | --- | --- |
| Node Article/typography | 128 pass, 0 fail | `implementation/node-final.log` |
| PHP Article | 43 pass, 625 assertions | `implementation/php-final.log` |
| Browser preview interaktif | 51 pass | `qa/results.json` |
| Browser source Mosaic + setting custom | 103 pass | `implementation/results.json` |
| Browser pagination seluruh 5 archive templates × 3 model | 27 pass | `implementation/pagination-results.json` |
| Browser source Editorial setelah shared changes | 44 pass | `../20261011_editorial-journal-revamp/implementation/results.json` |
| Frontend/CSS HTTP | 200; CSS cocok dengan source | `implementation/runtime-http.json` |
| PHP/JS syntax, whitespace diff | Pass | pemeriksaan terminal |

Viewport yang diuji untuk kedua Mosaic: 1440, 1024, 768, 390, 375, dan 320px. Default grid 3/2/1 kolom, tanpa horizontal overflow. Pengaturan custom dua kolom pada mobile juga diuji agar CSS tidak memaksa pilihan pengguna. PHP berjalan dengan SQLite `:memory:`, array cache/session, compiled views di artifact, serta helper translation identity agar tidak menulis data terjemahan CMS.

Fixture active source dihasilkan oleh `implementation/render.php`; fixture pagination lintas template oleh `implementation/pagination-render.php`. Pengujian tambahan browser sempat tertunda karena automatic permission approval timeout, lalu berhasil dijalankan kembali memakai CLI yang tersedia secara offline.

## Memori, Graphify, dan batas verifikasi

`E:\AI\Memories` tidak tersedia pada host sesi ini. Registry C `C:\Users\aruna\.codex\memories\MEMORY.md` digunakan untuk konteks desain modern sans-serif dan kontrak settings; semua nilai aktif diperiksa kembali pada source. Tinggi thumbnail Editorial dalam catatan lama berbeda dari source sekarang; source aktif tetap digunakan.

Graph tersedia di `graphify-out/graph.json`. Query `mosaic-magazine mosaic-classic ArticleTemplateOptions archive-header pagination` dan explain `mosaic-classic.blade.php` dipakai sebagai peta. Hubungan include ke archive-header, archive-title, media-link, pagination, template-shell-attributes, dan classic-meta adalah EXTRACTED, lalu diverifikasi pada Blade aktif. Node artifact/historis dari query luas tidak dipakai sebagai bukti implementasi. Graph tidak diperbarui karena instruksi pengguna meminta update manual; peta tersebut kini tertinggal terhadap perubahan source ini.

Skill UI UX Pro Max, Design Taste Frontend, Graphify, Playwright, dan Ponytail digunakan. Superpowers tidak tersedia dalam daftar skill sesi ini.

Pemeriksaan browser menggunakan Blade aktif dan fixture terisolasi, bukan sesi authenticated form Apply/Save. Pengujian menyimpan setting melalui UI admin belum dilakukan. Frontend `/article` saat pemeriksaan masih memilih Minimal Reading List; pilihan ini dipertahankan. Preview pagination menggunakan sample paginator; navigasi data bisnis dan persistensi UI admin tidak disimulasikan sebagai hasil runtime.
