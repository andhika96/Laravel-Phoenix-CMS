# Preview posisi kontrol dan filter status Manage Article

Tanggal: 28 Oktober 2026. Project: Laravel 13 Phoenix.

Status: preview terpisah untuk ditinjau; belum ada pilihan desain atau persetujuan implementasi baru.

## Tujuan

Menata posisi tombol detail dan checkbox agar konsisten pada desktop, tablet, dan mobile; menawarkan penempatan All, Publish, Draft, dan Pending di luar deretan tab lama. Tetap memakai warna, font, dan pola pengelolaan artikel Phoenix.

## Temuan aktual

Pemeriksaan CMS dengan login dan pembacaan halaman saja menunjukkan warna tema #1FA675 dan font Noto Sans. Pada lebar 390 px, awal tombol detail berada sekitar 41 px di bawah awal judul karena kontrol mengikuti pusat seluruh tinggi baris. Total status yang terlihat: All 5.041, Publish 5.035, Draft 0, Pending 6. Angka ini adalah snapshot saat pemeriksaan; artikel preview menggunakan enam contoh lokal, bukan salinan seluruh daftar.

## Usulan untuk dipilih

- **Status di toolbar** — rekomendasi: satu dropdown All/Publish/Draft/Pending dengan jumlah masing-masing, berdekatan dengan pencarian. Layar sempit menempatkan pencarian di baris tersendiri, kemudian status, filter, dan urutan. Menghemat ruang, tetapi perlu membuka dropdown untuk melihat semua jumlah.
- **Navigasi status** — filter status menjadi navigasi vertikal di samping daftar pada desktop. Ketika ruang menipis, berubah menjadi empat pilihan ringkas di luar kotak tabel. Semua jumlah mudah dilihat, dengan kebutuhan ruang yang lebih besar.

Kedua usulan mempertahankan urutan detail lalu checkbox. Pada desktop dan tablet yang masih memuat kolom, pusat kontrol sejajar dengan baris pertama judul. Pada daftar mobile, detail dan checkbox berada di baris kontrol atas bersama menu opsi; judul dan metadata menggunakan lebar penuh di bawahnya. Jarak antartarget detail dan checkbox 24 px. Target mobile/sentuh sedikitnya 44 × 44 px. Detail tetap dibuka di bawah artikel.

Preview tidak menambahkan ketergantungan. Implementasi nanti perlu mempertahankan Vue 3 dan kontrak server yang ada, termasuk pencarian, filter, pagination, selection, dan bulk status. Pemilihan desain masih menunggu pengguna.

## Konteks yang digunakan

- Memori C: `C:/Users/aruna/.codex/memories/MEMORY.md`, bagian Manage Article; ringkasan percakapan 26 Oktober 2026. Drive `E:` tidak tersedia saat pemeriksaan sehingga memori utama di sana belum dapat dibaca.
- Graphify query `ManageArticleVue3 manage_article.blade.php` untuk lokasi modul; query dibatasi 850 token dan bukan pembuktian semantik semua metode Vue.
- Graphify path `manage_article.blade.php` ke `public/assets/css/article/manage-article-2026.css`: hubungan `references_stylesheet`, `EXTRACTED`. Hash kedua file dan modul Vue cocok dengan manifest graph saat diperiksa. Graph tidak diperbarui pada pekerjaan preview ini.
- Source aktif yang dibaca: `resources/views/manage_article/manage_article.blade.php`, `public/assets/css/article/manage-article-2026.css`, `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`, dan `package.json`.
- Referensi QC sebelumnya: preview dan browser QA Manage Article 26 Oktober 2026. Source aktif dan pemeriksaan runtime baru menjadi acuan akhir.

## File dan backup

Semua file baru ada di folder preview ini. `manage-article-controls.html` merupakan sumber preview; `qa/preview-inspection.html` pembungkus pemeriksaan; `qa/inspect-live.mjs` membaca tampilan aktif; `qa/check-preview.mjs` menguji preview. Bukti pemeriksaan, snapshot hash, dan screenshot berada di `qa/`.

Salinan sebelum revisi file preview dan pembungkus pemeriksaan disimpan di `backups/` dengan timestamp. Tidak ada source CMS yang dimodifikasi. Hash tiga source utama dan `graph.json` tetap sama; status Git awal bersih, akhir hanya menambahkan folder preview ini.

## Verifikasi baru

- Runtime CMS: membaca tampilan aktual pada 1440, 768, dan 390 px. Tidak mengirim perubahan artikel.
- Runtime preview di Chrome: 32 kombinasi desain, lebar, tema terang/gelap, dan pointer biasa/sentuh. Ukuran yang diperiksa mencakup 320, 390, 768, 1024, dan 1440 px.
- Tidak ada horizontal overflow atau elemen melewati batas preview; jarak kontrol 24 px; kontrol mobile/sentuh 44 × 44 px; sejajar terhadap baris pertama pada layout tabel.
- Interaksi lolos: detail melalui Enter/Space, checkbox terpisah, select-all indeterminate, dropdown dan navigasi status, Draft kosong, pencarian dan reset, kategori/jadwal, pagination contoh, Escape untuk menutup menu.
- Runtime error preview: 0. Source dan graph tidak berubah. Lihat `qa/verification.json`.

Belum diuji di Firefox/Safari atau perangkat fisik. Preview merupakan simulasi lokal; desain baru belum diuji sebagai implementasi Vue di CMS. Graphify hanya dipakai sebagai peta source, bukan bukti runtime.

## Revisi loading pada 28 Oktober 2026

Permintaan tambahan pengguna: spinner harus tetap di tengah isi tabel untuk next/previous page, pencarian, sort header, dan proses pemuatan daftar lainnya. Preview kini memakai satu region pemuatan di bawah header tabel, dengan spinner di pusat area isi. Baris lama disembunyikan selama simulasi pemuatan; toolbar dan header tidak bergeser ke atas spinner. Durasi simulasi 650 ms hanya untuk memperlihatkan perilaku preview, bukan ketentuan delay saat implementasi CMS.

Semua pemicu daftar memakai indikator yang sama: pemuatan awal, next/previous page, nomor halaman, pencarian/clear, sort judul/tanggal pada header, dropdown sort, status, kategori, jadwal, dan reset filter. `aria-busy` mengikuti region data; preferensi reduced motion menghentikan putaran spinner dan tetap menampilkan teks status. Kontrol detail dan checkbox tidak memicu loading daftar.

Source aktif yang diverifikasi menunjukkan `loadingNextPage` sudah dipakai bersama oleh `listData()`, `refreshArticleList()`, dan `sortArticles()`. Posisi visual saat ini berasal dari `.ph-article-refresh` di atas tabel pada Blade, sehingga koreksi implementasi kelak dapat mengikuti alur Vue yang sama. Belum ada modifikasi Blade/CSS/Vue CMS pada tahap preview ini.

Pemeriksaan baru: `qa/check-loading.mjs` dan `qa/loading-verification.json` mencatat 36 kasus pemicu dan posisi di kedua desain, termasuk desktop 1440 px, tablet 768 px, mobile 390/320 px, dan dark mode. Pusat horizontal/vertikal spinner sesuai area isi dan selalu berada di bawah header. Script ini gagal sebelum pembaruan karena indikator pemuatan belum ada, kemudian lolos setelah revisi. Uji layout/interaksi awal diperbarui agar menunggu pemuatan selesai dan dijalankan ulang terhadap revisi terbaru. Backup sebelum perubahan berada pada subfolder `backups/` bertanda `centered_loading` dan `loading_regression`.
