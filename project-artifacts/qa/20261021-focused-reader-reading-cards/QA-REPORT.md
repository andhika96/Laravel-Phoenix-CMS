# Focused Reader — Reading Cards

Tanggal: 21 Oktober 2026. Project: Laravel 13 Phoenix.

## Hasil implementasi

Reading Cards pilihan pengguna sudah diterapkan dan disimpan melalui Save Template pada manager (HTTP 200). Setting dimuat ulang untuk memastikan persisten. Active detail tetap focused-reader; archive minimal-reading-list tetap. Hanya tujuh nilai leaf yang berubah, tercatat pada saved-settings-changes.json:

- Reading width 680 → 880px, cover width 880 → 1040px, title width 680 → 800px.
- Cover desktop/tablet 16:9 → 5:2, mobile 4:3 → 16:9.
- Thumbnail navigasi false → true.

Previous/Next berisi thumbnail, kategori bila tersedia, estimasi waktu baca, judul dan panah. Hover memakai warna tema untuk border/latar/judul/panah; panah bergerak 3px. Focus-visible memakai outline 2px. Reduced motion menghilangkan transisi dan transform. Satu tetangga memenuhi satu baris; tanpa tetangga navigasi tidak ditampilkan. Dua template detail lainnya tetap memakai navigasi semula. Stylesheet konten CKEditor bersama tidak diubah.

## Source yang dibaca dan diubah

- app/Support/Article/ArticleTemplateOptions.php — default ukuran, thumbnail, rasio dan allowlist 5:2.
- resources/views/article/templates/detail/focused-reader.blade.php — memilih Reading Cards.
- resources/views/article/templates/partials/detail-navigation.blade.php — cabang komponen Focused Reader; jalur template lain dipertahankan.
- resources/views/article/templates/partials/detail-reading-cards.blade.php — partial baru.
- public/assets/css/article/article-frontend-2026.css — styling responsif dan interaksi kartu.
- resources/views/manage_article/templates/partials/options-focused-reader.blade.php — opsi rasio 5:2.

Source tambahan dibaca: controller template manager, PublicArticleQuery, fixture preview, route preview, JS manager dan kontrak pengujian artikel. Perubahan lama di repository dipertahankan; tidak ada commit atau push.

## Backup

Folder: project-artifacts/backups/20261021-focused-reader-reading-cards-101922/.

Lima source yang sudah ada disalin dengan struktur relatif sebelum modifikasi. Partial Reading Cards merupakan file baru. article-template-settings-before.json menyimpan snapshot setting sebelum perubahan. ReadingCardsTest.before-fixture-fix.php menyimpan test sebelum pembetulan fixture pengujian.

## Bukti pengujian terbaru

- PHP: 48 tests / 703 assertions lulus. Meliputi suite artikel, default/custom normalization, kombinasi tetangga, display navigation off dan isolasi template lainnya. Pengujian menggunakan database testing terpisah. Lihat php-tests.txt.
- Node: 76 tests lulus untuk presentation, manager dan fixture. Lihat node-tests.txt.
- Runtime browser Edge Chromium headless: 3 template × 4 viewport (320, 390, 834, 1440px), 12 kasus lulus, tanpa horizontal overflow. Focused Reader desktop terukur konten 880px/cover 1040px; tablet 770px/770px pada viewport 834; mobile 350px pada 390 dan 280px pada 320. Dua kartu berdampingan desktop/tablet dan satu kolom mobile. Lihat runtime-verification.json.
- Hover border/latar/judul/panah berubah; shadow tetap none. Keyboard focus-visible outline 2px. Reduced motion transition 0s/transform none.
- Thumbnail gambar preview termuat. Toggle thumbnail dan divider diverifikasi melalui atribut render tanpa menyimpan perubahan tambahan. Panel opsi menampilkan reading width 880 dan rasio 5/2. Lihat option-verification.json.
- Artikel publik QA5059 memiliki satu previous card selebar grid; klik previous menuju artikel publik sebelumnya (dua kartu) lalu next kembali ke QA5059. Tidak menggunakan tautan fixture untuk klaim navigasi publik.
- Regresi konten CKEditor: export ulang ketiga Blade aktif terhadap artikel5059 dan bandingkan computed style dengan build CKEditor lokal. 12 kasus / 0 perbedaan pada properti yang diuji; 9 gambar termuat, tabel/merge cells dan todo tetap valid. Lihat ckeditor-regression.txt, render-verification.json dan saved-article-manifest.json. Auto margins yang bergantung lebar kolom dan wrapper seleksi inline image tidak dibandingkan; bukan klaim pixel-identical seluruh halaman.
- PHP lint, kompilasi Blade dan git diff --check lulus. Tidak ada perubahan setelah pemeriksaan tersebut selain artifact laporan.
- Screenshot desktop/mobile/tablet dan hover disimpan di folder ini. Screenshot desktop top, desktop hover dan mobile 390 diperiksa secara visual.

## Konteks memori dan Graphify

Memori: E:/AI/Memories/20261020-delta-revamp-focused-reader-01a14ef8-cc2f-78d3-b204-f92352ffbb78.md dan C:/Users/aruna/.codex/memories/MEMORY.md bagian Focused Reader (149–158). Dipakai untuk menjaga typography CMS dan pekerjaan sebelumnya; source serta setting runtime diperiksa ulang.

Graphify query: focused-reader detail-navigation, budget650. Memakai node normalizer, view Focused Reader dan shared navigation untuk mempersempit source. Graph tanggal20 Oktober dipakai sebagai peta saja; source aktif diverifikasi langsung, tidak menyimpulkan hubungan INFERRED sebagai bukti implementasi. Graph tidak diperbarui sesuai instruksi pengguna agar update manual.

## Batas verifikasi

Pengujian browser mencakup Edge Chromium dengan emulasi viewport. Belum menguji Safari/Firefox atau perangkat fisik. Regresi CKEditor membandingkan konten dan properti tertentu, bukan keseluruhan halaman secara pixel per pixel. Tidak menambahkan plugin CKEditor atau perubahan konten artikel pada pekerjaan Reading Cards ini.
