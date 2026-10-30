# Manage Article — klik area baris untuk detail, 29 Oktober 2026

## Hasil

Area isi baris (padding kosong, judul, kategori, author, status, dan tanggal) membuka/menutup detail melalui state Vue yang sama dengan chevron. Judul menjadi tombol disclosure dengan `aria-expanded`, `aria-controls`, dukungan Enter/Space, serta focus ring mengikuti tema CMS. Edit tetap melalui aksi Edit.

Checkbox dan seluruh sel selection/action, native link/button/input, dropdown, serta modal Delete tidak menjalankan klik baris. Tidak ada double toggle saat chevron atau judul diklik. Memilih teks kategori/metadata untuk disalin tidak mengubah state detail. Klik isi detail terbuka tidak menutup panel tersebut.

Posisi dari referensi terakhir tetap dipertahankan: kontrol di tengah tinggi baris, inset 20px, gap visual tombol → kotak checkbox 24px, area klik touch minimal 44 × 44px. Header checkbox dan panel detail tetap sejajar. Gap dihitung terhadap kotak checkbox yang terlihat, bukan batas luar area kliknya.

## Source, memori, dan Graphify

- Primary memory E diperiksa dengan penyaringan nama; kandidat Article yang tersedia membahas template lama. Konteks terbaru dipakai dari registry C `MEMORY.md:187–198` dan recap Bootstrap/Graphify untuk urutan kontrol, pengujian tanpa mutasi, dan refresh graph manual.
- Graphify `explain public/assets/css/article/manage-article-2026.css` menunjukkan metadata stylesheet dan relasi `references_stylesheet [EXTRACTED]` dari Blade L8. Query `ManageArticleVue3` menunjukkan source Vue. Query `toggleExpandRow` tidak memiliki node; implementasi fungsi diverifikasi langsung dari source.
- Dibaca: Blade/Vue/CSS Manage Article, aturan padding core CSS dan Lucent/mobile/responsive typography pada koreksi posisi sebelumnya, serta pola title disclosure di halaman SMTP/User. Fungsi `toggleExpandRow` yang sudah ada tetap menjadi satu tempat perubahan state.
- Hanya tiga source diubah: `resources/views/manage_article/manage_article.blade.php`, `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`, dan `public/assets/css/article/manage-article-2026.css`.
- Blade menghubungkan klik row dan disclosure judul. Vue menambahkan guard klik interaktif, event yang dicegah, klik mouse sekunder, dan text selection. CSS mengatur cursor, reset tampilan tombol judul, serta focus ring tanpa mengubah palet CMS.

## Backup dan preservasi

Tiga file source dibackup dan diverifikasi SHA-256 sebelum implementasi. Manifest: `project-artifacts/backups/manage-article-row-disclosure-20261029/manifest.json`. Snapshot lanjutan untuk penyempurnaan event bubbling juga disimpan. Koreksi posisi sebelumnya dipertahankan; tidak ada source di luar tiga file tersebut yang berubah.

`preservation-verification.json` membuktikan backup, HEAD, dan index terjaga serta hash graph tetap sama. Tidak ada stage, commit, push, perubahan dependency aplikasi, atau mutasi artikel.

## Pengujian terbaru

- Test sebelum implementasi gagal pada handler klik row yang belum tersedia; setelah implementasi, 4 unit test guard/state lulus. Bersama kontrak Manage Article terkait, total **46 Node tests lulus**. Syntax check JS dan `git diff --check` lulus.
- **Chrome 44 pemeriksaan dan Firefox 44 pemeriksaan lulus**: viewport 1440/768/390/320, padding row, judul, kategori, author/status/date yang terlihat, chevron, keyboard judul, checkbox saat panel tertutup/terbuka, pemilihan teks, dropdown, klik judul di luar menu yang menutup menu, pembatalan Delete, klik panel detail, touch 390/768, dark mode, dan navigasi Edit.
- Geometry checks dalam pengujian klik row memastikan inset 20px, gap visual 24px, kontrol di tengah baris pada layout tabel, serta tidak ada overflow.
- Regresi loading/toolbar pada source final: **24 skenario loading, 10 responsive, 2 touch lulus**. Loading tetap di tengah body tabel untuk next/previous/search/sort/status/filter/page size/empty/retry/reduced motion. Vue runtime **3.5.21**; error JS 0, warning Vue 0, request mutasi artikel 0.
- Screenshot Firefox melalui klik judul di halaman aktif: `reference-alignment-firefox.png`. Capture mengukur gap visual 24px, inset 20px, serta selisih pusat tombol/checkbox ke pusat row kurang dari 0,01px pada baris contoh.
- Percobaan browser awal terhenti karena titik tengah judul tertutup popup menu pada viewport kecil. Pengujian memakai titik judul yang terlihat untuk klik di luar menu; tidak memakai forced click dan tidak membutuhkan perubahan source aplikasi tambahan.

## Bukti dan batas

`verification-chrome.json`, `verification-firefox.json`, `contracts.log`, `unit-before.log`, `unit.log`, `loading-regression/live-verification.json`, `reference-capture.json`, dan `preservation-verification.json` merekam hasil terbaru. Screenshot desktop/tablet/mobile tersedia dalam folder ini.

Browser Firefox merupakan browser pengujian Playwright dengan profil terpisah. Safari, perangkat fisik, dan full suite repository belum diuji. Graphify tidak diperbarui pada pekerjaan ini dan tetap menunggu perintah manual pengguna; graph hanya menjadi peta navigasi source, bukan bukti runtime.
