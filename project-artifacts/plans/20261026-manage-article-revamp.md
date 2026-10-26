# Manage Article: penerapan desain yang disetujui

Tanggal: 26 Oktober 2026. Persetujuan: user meminta implementasi langsung setelah preview dan koreksi jarak 24px antara chevron dan checkbox.

1. Gunakan aplikasi Vue 3 dan VuejsPaginateNext aktif, Bootstrap modal, ikon dan token tema Phoenix. Pertahankan form tambah/edit, thumbnail, modal kategori dan hapus, serta perubahan repository lain.
2. Tabel memiliki kolom chevron, checkbox, judul/kategori, penulis, status/jadwal, tanggal dan aksi. Chevron dan checkbox terpisah 24px. Detail dirender dengan binding Vue, bukan HTML hasil salinan DOM. Status dan aksi tetap dapat dijangkau pada layar kecil.
3. Satukan permintaan daftar agar pencarian, filter, urutan, pagination dan batas halaman selalu dipertahankan. Abaikan respons jaringan yang sudah kedaluwarsa. Seleksi hanya untuk halaman aktif dan dibersihkan saat query berubah.
4. Laravel memvalidasi parameter, menggunakan pengurutan yang diizinkan, menghitung status pada server, memuat kategori/penulis, dan menyediakan judul lengkap serta ringkasan. Pertahankan field resource lama karena resource juga digunakan oleh daftar publik.
5. Tampilkan aksi massal hanya setelah seleksi, gunakan konfirmasi dan transaksi utuh. Uji mutasi hanya dengan SQLite terisolasi, bukan data CMS asli.
6. Jalankan uji JavaScript daftar dan thumbnail, PHP fitur daftar/mutasi, lint PHP, kompilasi Blade, serta browser QA autentikasi pada desktop, tablet, mobile, terang/gelap, keyboard dan jarak sentuh. Jangan update Graphify tanpa perintah manual.

Backup: project-artifacts/backups/manage-article-revamp-20261026. Hasil QA dan skrip pemeriksaan: project-artifacts/qa/manage-article-revamp-20261026 dan project-artifacts/tests/manage-article-revamp-20261026.
