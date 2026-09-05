# Audit Penggunaan CSS CMS

Tanggal: 2026-09-04  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Scope: seluruh file `*.css` di bawah `public/assets/css`, tanpa file backup.  
Mode: read-only; tidak ada CSS yang dihapus, dipindahkan, atau dimodifikasi.

## Ringkasan

| Klasifikasi | File | Ukuran |
|---|---:|---:|
| Terpakai dan terobservasi pada runtime | 22 | 1.515.298 byte / 1.445 MiB |
| Terpakai kondisional dan masih reachable | 5 | 107.740 byte / 105,21 KiB |
| Kandidat tidak terpakai, confidence tinggi | 6 | 86.880 byte / 84,84 KiB |
| **Total** | **33** | **1.709.918 byte / 1,63 MiB** |

Temuan tambahan:

- Tidak ada `@import` antar-CSS pada scope ini.
- Tidak ada dua file dengan SHA-256 identik.
- Database saat audit memakai `Arunika Equinox` (`theme_id=8`).
- Manage Themes hanya mengizinkan Prism, Aurora, Lucent, dan Equinox.
- Enam kandidat tidak memiliki referensi source produksi, test, CSS import, atau database.
- Access log host proyek tidak mencatat keenam kandidat; 22 file lain memiliki request runtime dengan status `200` atau `304`.
- Audit cleanup 2026-08-23 sebelumnya menemukan enam kandidat yang sama; audit ini memvalidasi ulang terhadap source dan runtime terbaru.

## Kandidat tidak terpakai — confidence tinggi

| File | Ukuran | Baris | Bukti |
|---|---:|---:|---|
| `public/assets/css/aruna-admin-v6.css` | 7.926 | 401 | Tidak ada consumer source/test/import/database dan tidak ada request host proyek |
| `public/assets/css/aruna-admin-v7-phoenix-elegant.css` | 22.158 | 903 | Tidak ada consumer source/test/import/database dan tidak ada request host proyek |
| `public/assets/css/aruna-admin-v7-simple-part-2.css` | 5.850 | 266 | Layout `simple_part_2` sudah tidak terdapat di `resources/views/themes` |
| `public/assets/css/aruna-admin-v7-simple.css` | 5.850 | 266 | Layout `simple` sudah tidak terdapat di `resources/views/themes` |
| `public/assets/css/aruna-admin-v7.css` | 5.392 | 249 | Tidak ada consumer source/test/import/database dan tidak ada request host proyek |
| `public/assets/css/aruna-v3.css` | 39.704 | 3.588 | Tidak ada layout/route/source consumer dan tidak ada request host proyek |

Semua file di atas masih tracked Git. Request dengan nama serupa ditemukan pada access log lama untuk host/project lain, tetapi tidak ada CSS request dengan referrer `laravel-13-phoenix.aruna`. Direct external URL tanpa referrer tidak dapat dibuktikan sepenuhnya dari source atau log gabungan; karena itu verdict-nya **high confidence**, bukan kepastian absolut terhadap seluruh kemungkinan caller eksternal.

## Terpakai kondisional — jangan dihapus

| File | Alasan tetap dipertahankan |
|---|---|
| `public/assets/css/aruna-v4.css` | Route `/setup` aktif dan `resources/views/setup/setup.blade.php` memuat file ini |
| `public/assets/css/pagebuilder.css` | Route legacy `/pagebuilder` aktif dan `resources/views/pagebuilder/pagebuilder.blade.php` memuat file ini |
| `public/assets/css/themes/calm_green/aruna-admin-v7-calm-green.css` | Calm Green masih terdaftar di database dan layout CMS-nya memuat file ini |
| `public/assets/css/themes/default/aruna-admin-v7-default.css` | Default masih terdaftar di database dan layout CMS-nya memuat file ini |
| `public/assets/css/themes/default/phoenix-cms-default.css` | Dipakai layout CMS/auth/frontend Default jika theme dipilih melalui database |

Kelima file ini tidak terlihat pada access log target terbaru, tetapi memiliki route atau resolver theme yang masih reachable. Tidak aman mengategorikannya sebagai dead asset.

## Terpakai dan terobservasi pada runtime

### Article

- `public/assets/css/article/article-frontend-2026.css`
- `public/assets/css/article/article-template-manager-2026.css`

### Core CMS dan frontend

- `public/assets/css/awesome-admin-header-navigation.css`
- `public/assets/css/extending-css-bootstrap-5.css`
- `public/assets/css/frontend-menu-dropdown.css`
- `public/assets/css/phoenix-cms.css`
- `public/assets/css/theme-responsive-typography.css`

### Page Builder Elementor

- `public/assets/css/frontend_elementor.css`
- `public/assets/css/frontend_elementor_v23.css`
- `public/assets/css/frontend_elementor_v24.css`
- `public/assets/css/pagebuilder_elementor.css`
- `public/assets/css/pagebuilder_elementor_v23.css`
- `public/assets/css/pagebuilder_elementor_v24.css`

Database aktif berisi 26 page v2.0, satu page v2.3, dan dua page v2.4. Route editor untuk ketiga versi juga masih terdaftar, sehingga enam CSS tersebut bukan kandidat cleanup.

### Theme

- `public/assets/css/themes/arunika_aurora/arunika_aurora.css`
- `public/assets/css/themes/arunika_aurora/mobile-v2.css`
- `public/assets/css/themes/arunika_equinox/arunika_equinox.css`
- `public/assets/css/themes/arunika_equinox/mobile-v2.css`
- `public/assets/css/themes/arunika_lucent/arunika_lucent.css`
- `public/assets/css/themes/arunika_lucent/mobile-v2.css`
- `public/assets/css/themes/arunika_prism/arunika_prism.css`
- `public/assets/css/themes/arunika_prism/mobile-v2.css`
- `public/assets/css/themes/calm_green/phoenix-cms-calm-green.css`

`phoenix-cms-calm-green.css` masih aktif karena layout auth/frontend Aurora, Prism, dan Equinox memuatnya sebagai compatibility surface.

## Evidence matrix

File `css-usage-matrix.csv` berisi satu baris per CSS dengan:

- classification dan confidence;
- byte dan jumlah baris;
- jumlah referensi produksi/test/artifact;
- jumlah request runtime;
- request dan HTTP status terakhir;
- status tracked/dirty Git;
- contoh consumer produksi.

Raw evidence:

- `literal-reference-scan.csv` — pencarian exact path dan basename.
- `runtime-access-log-scan.csv` — request CSS pada access log host proyek.

## Metode

1. Membaca memori cleanup dan CSS aktif sebelumnya.
2. Query Graphify untuk Page Builder, renderer, theme layout, route, dan runtime loader.
3. Menginventarisasi 33 file fisik, size, line count, SHA-256, dan Git state.
4. Memindai exact path serta basename pada `app`, `bootstrap`, `config`, `database`, `public`, `resources`, dan `routes`.
5. Memisahkan referensi source produksi, tests, dan project artifacts.
6. Memeriksa `@import` dan exact hash duplicates.
7. Memeriksa resolver `custom_theme()`, ThemesSeeder, Manage Themes controller, dan theme database aktual.
8. Memeriksa route source untuk setup, tiga Page Builder Elementor, legacy Page Builder, article templates, dan header navigation.
9. Memindai 249 kolom text/JSON database; nol hit untuk enam kandidat.
10. Memindai Nginx access log saat ini untuk request CSS dengan referrer host proyek.
11. Membandingkan dengan audit cleanup 2026-08-23.

## Keterbatasan

- `php artisan route:list` tidak dapat selesai karena baseline memiliki referensi ke `App\Http\Controllers\Api\v1\Testing\Testing_Controller` yang tidak tersedia. Route relevan diverifikasi langsung dari source route/controller.
- Access log merupakan gabungan beberapa virtual host; request target diidentifikasi melalui referrer host proyek.
- Direct external URL tanpa referrer, CDN cache eksternal, atau consumer di luar repository tidak dapat dibuktikan tidak ada secara absolut.
- Audit ini menentukan apakah **file CSS** masih digunakan. Audit selector-level dead CSS di dalam 27 file aktif merupakan pekerjaan berbeda dan belum dilakukan.

## Rekomendasi

Jika cleanup nanti disetujui, batch pertama harus dibatasi pada enam kandidat high-confidence. Sebelum penghapusan:

1. buat backup terpusat dan manifest SHA-256;
2. verifikasi blob Git `HEAD` untuk keenam file;
3. hapus hanya enam target eksplisit;
4. jalankan focused theme/setup/Page Builder tests, Blade cache, dan browser smoke test;
5. periksa access log dan `git diff --check` setelah cleanup.

