# Template option parity — 13 Oktober 2026

Source aktif diubah. Backup sebelum edit: project-artifacts/backups/20261013_101843-template-option-parity/ (struktur path source dipertahankan).

Temuan: Mosaic Classic sudah memiliki show_author dan show_reading_time pada normalizer, modal Story details, session Vue dan classic-meta. Label kini Show author / editor dan Story details · metadata agar lebih mudah ditemukan. Render diuji dengan semua metadata OFF untuk semua region.

Tambahan Visible elements pada Minimal Reading List, Editorial Journal, Mosaic Magazine: kategori, tanggal, excerpt, link baca sesuai elemen yang tersedia; reading time khusus Minimal. Ketiga detail: author, tanggal, navigasi previous/next. Balanced Grid dan Mosaic Classic memakai kontrol Story details existing. Header, toolbar, thumbnail, grid, sidebar, pagination dan shell tetap memakai kontrol existing. Scope pemeriksaan ini metadata/konten tampak dan pagination; bukan audit setiap token dekorasi CSS atau seluruh fitur TOC.

Minimal Reading List default pagination: soft, frame OFF, item width/radius 0px, frame width/radius 0px, warna border rgba(0,0,0,0). Explicit saved values dipertahankan; tidak ada migrasi/overwrite database. Tambahan Button border width berlaku pada SSR dan Vue pagination dengan CSS variable yang sama. Model underline tetap mempertahankan indikator garis aktif bila dipilih eksplisit.

File utama: ArticleTemplateOptions.php; Vue manager dan frontend article; index modal; options-classic-balanced dan options-styling; tiga archive Blade Minimal/Editorial/Mosaic; detail-header-copy; detail-navigation; pagination partial; article-frontend CSS. Tests: manager sections dan ArticleTemplateOptionsTest. Seluruh path relatif terhadap root project D:\Laragon\www\laravel-13-phoenix.

Memori E tidak tersedia (path not found). Laporan project-artifacts/implementations/20261012_template-options-ui-revamp/README.md dibaca sebagai konteks historis. Graphify query ArticleTemplateOptions classic-meta pagination mengarah ke normalizer, partial dan controller; diverifikasi source aktual. Tidak ada graph update sesuai instruksi manual.

Verifikasi terbaru: 73 Node tests PASS; 20 PHP tests/334 assertions PASS; 18 pemeriksaan render Blade PASS (toggle OFF, Classic metadata, ketiga detail, override pagination, modal compilation). JS syntax PASS; Blade cache compiled. Bukti tests.txt, php-tests.txt, render-checks.txt dan check.php dalam folder ini. Tidak ada perubahan database oleh pekerjaan ini. Browser terautentikasi dan visual computed border pada sesi CMS pengguna belum diperiksa pada turn ini.

Refresh halaman CMS untuk memuat source terbaru. Setting lama yang eksplisit tetap akan tampil sesuai data tersimpan, walaupun default baru sudah berubah.
