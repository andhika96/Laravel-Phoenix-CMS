# Laravel 13 Phoenix · Manage Article mobile · Kontrol di kiri

**Tanggal** 3 November 2026, Asia/Jakarta  
**Repo** `D:\Laragon\www\laravel-13-phoenix`

## Tujuan dan keputusan

User meminta preview mobile untuk memperbaiki posisi ikon dan teks ketiga tombol halaman, merapikan daftar artikel, dan menambahkan pilihan seperti Gmail. Preview memuat dua varian. User menyetujui **Kontrol di kiri**, mengizinkan implementasi di CMS, dan meminta rangkuman percakapan disimpan setelah pekerjaan selesai.

Pada mobile, chevron 44 × 44 px berada di kiri dan sejajar dengan keseluruhan isi baris. Judul dan kategori berada di kanan, lalu status dan tanggal ditumpuk dengan menu titik tiga di sisi kanan bawah. Penulis tersedia di detail. Ketiga tombol halaman memakai komponen tema CMS dengan isi terpusat. Klik atau tap biasa pada judul dan area isi tetap membuka detail.

Tahan baris sekitar 700 ms atau tekan **Pilih** untuk memasuki mode pilih. Checklist bulat dan warna tema menandai artikel terpilih. Tap kembali membatalkan pilihan per baris. **Semua** berlaku untuk halaman aktif. X dan Escape keluar dari mode. Penggeseran membatalkan tekan-tahan. ID dipertahankan saat ukuran layar berubah, dengan pilihan direset saat daftar dimuat ulang. Implementasi memakai `selectedArticleIds`, status massal, dan konfirmasi Vue yang sudah ada.

## Konteks yang dibaca

- Preview yang disetujui berada di `project-artifacts/previews/manage-article-mobile-20261103/manage-article-mobile.html`.
- Catatan E berada di `E:\AI\Memories\extensions\ad_hoc\notes\20260717-171107-laravel13-phoenix-manage-article-responsive-width-fix.md`. Registry memori C baris 280–287 merangkum urutan kontrol, pengecekan tanpa mutasi, tema CMS, dan Graphify manual.
- Graphify `query ManageArticleVue3` menemukan kelas Vue di file JS aktif. `explain public/assets/css/article/manage-article-2026.css` menunjukkan referensi dari Blade. Kedua relasi bertanda `EXTRACTED` dan diverifikasi terhadap sumber aktif. Graphify dipakai untuk navigasi dan tetap pada hash sebelum pekerjaan.
- Source tambahan yang diperiksa meliputi handler `bulk_update` di `app/Http/Controllers/Web/Manage_Article/Manage_Article_Controller.php`, style tema di `public/assets/css/cms/cms-bootstrap-theme.css`, template login CMS, dan tes responsif sebelumnya.

## Source dan backup

- `resources/views/manage_article/manage_article.blade.php` untuk tombol halaman, bar mode pilih, semantik kontrol dan baris.
- `public/assets/css/article/manage-article-2026.css` untuk tata letak kiri, posisi menu, pemusatan tombol dan checklist.
- `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js` untuk pointer events, mode pilih, pilihan dan pergantian ukuran.
- Backup ketiga file dibuat sebelum edit dan dicocokkan dengan SHA-256. Foldernya `project-artifacts/backups/manage-article-mobile-leading-20261103/` dengan akhiran `.bak_20261103_155540_before_mobile_leading`. Daftar file lengkap berada di `backups.json` pada folder QA. Perubahan Auth, Appearance, dan artefak lama dipertahankan.

## Hasil verifikasi

- Baseline CMS mereproduksi chevron yang tidak sejajar dengan baris, checkbox permanen, dan teks dua tombol yang bergeser ke atas. Rinciannya ada di `baseline.log`.
- CMS lokal dengan data artikel aktual lulus 15 skenario Chrome dan 14 Firefox. Ukuran 320, 414, 768, dan 1440 px mencakup tata letak mobile, tablet, dan desktop. Interaksi meliputi detail, tekan-tahan, tap ulang, pilih semua, keyboard, resize, pembatalan saat bergeser, menu, dan konfirmasi status. Input sentuh diemulasikan di Chrome.
- Loader terpusat di isi tabel pada next, previous, pencarian, sort toolbar, dan sort header. Form status berisi ID yang benar, modal konfirmasi dibatalkan, dan terdapat nol permintaan tulis artikel serta nol error Vue/browser dalam kedua pengujian.
- Tes Node terkait lulus 45/45, pemeriksaan sintaks JavaScript dan `git diff --check` lulus, dan cache Blade berhasil dibangun.
- Bukti berada di `project-artifacts/qa/manage-article-mobile-leading-20261103/`, termasuk tangkapan Chrome 414 px mode biasa dan mode pilih, JSON kedua browser, dan log Node. Skrip QA berada di `project-artifacts/tests/manage-article-mobile-leading-20261103/runtime.mjs`.

## Batas dan tindak lanjut

Pengujian memakai CMS lokal dan emulasi sentuh. Pengujian perangkat fisik, pengiriman perubahan status artikel yang sebenarnya, dan deployment berada di luar cakupan. Graphify dipertahankan untuk pembaruan manual terpisah. Saat melanjutkan, gunakan tiga source aktif di atas dan pakai bukti browser dari folder QA yang sama.
