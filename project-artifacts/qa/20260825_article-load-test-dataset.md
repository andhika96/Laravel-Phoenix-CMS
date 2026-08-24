# Article Load-test Dataset QA

Tanggal: 2026-08-25  
Scope: Data dummy Article lokal untuk menguji query, pagination, thumbnail, dan detail pada dataset yang lebih besar. Tidak ada data Article lama yang dihapus atau diperbarui.

## Dataset yang dibuat

- Seeder: `Database\Seeders\ArticleLoadTestSeeder`
- Prefix URI: `load-test-20260825-`
- Artikel baru: `5,000`
- Kategori Load Test baru: `7`
- Thumbnail lokal: `14` SVG (small dan large untuk setiap kategori), disimpan di `storage/app/public/articles/load-test/load-test-20260825/`
- Field setiap Article: URI unik, author yang sudah ada, kategori, content HTML, tags, `thumb_s`, `thumb_l`, `publish`, `public`, dan `scheduled=false`.
- Seeder rerun: `0 created`, `5,000 existing skipped` — aman dijalankan ulang tanpa duplikasi prefix.

Kategori:

1. Load Test Technology
2. Load Test Business
3. Load Test Design
4. Load Test Science
5. Load Test Culture
6. Load Test Health
7. Load Test Travel

## Backup sebelum perubahan data

- File: `project-artifacts/backups/20260825_ArticleLoadTestData_database/article-and-category-before-load-test.json`
- Snapshot: 40 Article dan 4 kategori sebelum seed.
- SHA-256: `96C7758C2A4D666960B5DA62E601BB206EC7F1079205D060F11D8245A3BA638B`

## Hasil data dan query lokal

- Total Article sesudah seed: `5,040`
- Public published Article yang eligible: `5,027`
- `PublicArticleQuery` halaman pertama: 12 row, `53.94ms`.
- `PublicArticleQuery` halaman 417: 12 row, `103.97ms`, item pertama `load-test-20260825-04966`.
- MySQL EXPLAIN public list memakai indeks `articles_public_listing_idx`, estimasi 12 row, `Using where; Backward index scan`.
- `Manage_Article_Controller::listData` halaman pertama: HTTP 200, 15 row dari total 5,040, `90.97ms`.
- `Manage_Article_Controller::listData` halaman 300: HTTP 200, 15 row dari total 5,040, `90.51ms`.
- Filter kategori Technology: 715 hasil, 12 row pada page, `16.79ms`; EXPLAIN memakai `articles_public_category_listing_idx` dengan backward index scan.

## Browser QA read-only

- `/article` menampilkan 12 data load test di halaman pertama, kategori dan pagination muncul.
- Semua 12 thumbnail kecil pada halaman awal termuat dari storage lokal.
- `/article?page=417` menampilkan Article #04966–#04977 dan pagination 416/417/418.
- `/article/load-test-20260825-00001` memuat content HTML dan thumbnail besar SVG.
- Console pada list, deep pagination, dan detail: tanpa error/warning.
- Endpoint browser `/manage_article/listdata` tidak dapat dibuka langsung karena `ERR_BLOCKED_BY_CLIENT`; jalur controller yang sama diuji melalui Laravel dan mengembalikan HTTP 200.

## Automated verification

- `php artisan test tests/Feature/Article` — 10 passed, 91 assertions.
- `ArticleLoadTestSeederTest` — 21 assertions mencakup 7 kategori, 14 SVG, metadata, default 5.000/7, prefix-aware query builder, idempotensi, serta Article/Category sentinel yang tidak berubah.
- `php -l database/seeders/ArticleLoadTestSeeder.php` — passed.
- `php artisan view:clear`, `php artisan view:cache`, dan `git diff --check` — passed.
- Independent read-only review: tidak ada P1/P2. Tiga P3 coverage (default count, prefix table, dan preservation data existing) sudah ditutup oleh test follow-up.

## Batas interpretasi

Ini baseline performa lokal single-user, bukan uji konkurensi atau benchmark produksi. Dataset 5.000 row sudah cukup untuk memeriksa pagination/deep offset/media path saat ini; pengujian skala berikutnya bila dibutuhkan adalah 20.000+ row dan concurrent HTTP profiling terpisah.
