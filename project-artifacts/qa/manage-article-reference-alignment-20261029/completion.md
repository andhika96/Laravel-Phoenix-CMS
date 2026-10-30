# Posisi final sesuai referensi — 29 Oktober 2026

Referensi terbaru pengguna menggantikan pendekatan alignment ke baris pertama dan inset 8px pada percobaan awal. Tombol detail serta checkbox sekarang berada di tengah tinggi baris. Inset tombol 20px mengikuti gambar referensi, dan gap 24px diukur dari tepi tombol ke kotak checkbox yang terlihat.

Area klik checkbox tetap 36px pada pointer biasa / 44px pada touch. CSS menghitung ruang bagian dalam agar gap visual tetap 24px ketika area klik dan ukuran font berubah. Kolom tidak diwajibkan sama lebar. Panel detail tetap sejajar dengan judul; pada mobile, kontrol dan judul mengikuti susunan grid yang telah disetujui dengan inset 20px. Prioritas padding khusus Manage Article terhadap aturan tabel umum CMS dipertahankan.

Backup sebelum penyesuaian ada dalam `project-artifacts/backups/manage-article-control-spacing-20261029`, termasuk snapshot `before_exact_reference_alignment`. Source yang diubah untuk alignment adalah `public/assets/css/article/manage-article-2026.css`.

Verifikasi saat alignment selesai: Chrome 17 skenario dan Firefox 17 skenario lulus; mencakup terang/gelap, desktop/tablet/mobile, serta touch. Gap visual 24px dan inset 20px terukur di runtime, kontrol terpusat terhadap tinggi baris, tidak ada overflow. Regresi loading 24 skenario, responsive 10, touch 2, dan Node 42 lulus. Screenshot referensi dan data ukur tersedia pada `reference-alignment-firefox.png` dan `reference-capture.json`.

Pengguna kemudian meminta isi baris dapat diklik untuk membuka detail. Implementasi dan verifikasi gabungan terbaru berada di `../manage-article-row-disclosure-20261029/completion.md`. Posisi sesuai referensi tetap dipertahankan setelah fitur klik baris ditambahkan.

Graphify tidak diperbarui; pembaruan menunggu instruksi manual. Safari dan perangkat fisik belum diuji.
