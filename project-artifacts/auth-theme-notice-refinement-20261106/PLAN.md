# Refinement Midnight Story dan notice CMS

6 November 2026. Permintaan: lengkapi implementasi referensi Upnow Studio yang masih kurang, serta pakai UI/UX notice CMS pada seluruh halaman auth dan Manage Appearance.

1. Memori E tidak tersedia; REPORT/LEDGER pekerjaan auth sebelumnya diperiksa. Graphify query notice serta explain view CMS Themes/AuthVue dipakai sebagai peta; source aktif diverifikasi.
2. Backup source aktif dan snapshot opsi auth sebelum perubahan. Pertahankan pilihan tema dan semua isi kustom pengguna; hanya ganti nilai default lama yang belum diisi bila diperlukan untuk melengkapi tampilan.
3. Ekstrak markup toast CMS yang ada menjadi include bersama untuk semua32 halaman auth dan Appearance. Tetap gunakan CSS notice auth yang sudah terisolasi, Bootstrap Toast, ikon/warna/timing CMS, dan pesan validasi yang aman. Arahkan semua respons Save/upload/remove/validasi Appearance ke notice tersebut.
4. Lengkapi Midnight Story: testimoni contoh dengan carousel, avatar bawaan Font Awesome, kartu informasi/dekorasi, dan input terang sesuai referensi. Avatar dapat dihapus/diganti melalui mekanisme gambar yang ada. Pertahankan konfigurasi kustom dan penghapusan eksplisit.
5. Uji render/contract notice semua32 view, avatar/default-image/removal, carousel, response success/error/network, toast close/mobile dan Appearance tanpa mengirim email/reset atau mengganti data akun nyata. Tes Site Config tetap di memori karena tabel lama MyISAM.
6. Refresh thumbnail Midnight, inspeksi browser/screenshot, catat batas verifikasi. Tidak ada commit/deploy/Graphify update.

Selesai: langkah1–6. Hasil akhir ada di REPORT.md. Koreksi posisi slider juga diterapkan: carousel turun mendekati kartu informasi dengan gap28px; judul tetap di atas. Combined suite19 test/437 assertion,72 kasus notice,24 viewport Midnight dan pemeriksaan notice Appearance/auth runtime lulus. Server sementara dihentikan; junction preview dipindahkan dari public ke arsip artefak sehingga seluruh file pengujian tetap tersimpan.
