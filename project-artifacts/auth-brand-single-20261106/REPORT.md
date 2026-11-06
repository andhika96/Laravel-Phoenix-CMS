# Nama situs tunggal pada tema auth baru — 6 November 2026

Penyebab duplikasi adalah dua pemanggilan nama situs dalam shared view modern: `ph-auth-panel-brand` di panel visual dan `ph-auth-brand` di area form. Elemen panel dihapus dari template bersama. Nama/logo di area form tetap terlihat ketika panel visual disembunyikan pada mobile. Footer copyright tetap terpisah sesuai fungsinya.

## Source, konteks dan backup

- Memori resmi `E:\AI\Memories` tidak tersedia pada sesi ini (Test-Path false). Konteks pekerjaan dilanjutkan dari laporan `auth-theme-notice-refinement-20261106/REPORT.md` dan `midnight-corner-fix-20261106/REPORT.md` dalam project-artifacts.
- Status repository diperiksa sebelum perubahan. Perubahan sebelumnya dipertahankan; tidak ada reset, commit, push atau deploy.
- Graphify `explain resources_views_auth_auth_layout_blade` menunjukkan relasi EXTRACTED dari auth layout ke auth-base.css. Graph lebih lama daripada shared view modern baru sehingga pemilik markup dikonfirmasi langsung melalui pencarian source aktif. Tidak ada relasi INFERRED/AMBIGUOUS yang dipakai sebagai bukti.
- Source dibaca: shared view `resources/views/auth/modern/page.blade.php`, `panel.blade.php`, auth layout, wrapper Verdant Reset Password, stylesheet modern, serta tes render auth yang sudah ada. Semua pemanggilan `ph-auth-panel-brand` ditelusuri sebelum penghapusan.
- File yang diubah: `resources/views/auth/modern/page.blade.php` (satu baris markup dihapus), `public/assets/css/auth/auth-modern.css` (dua aturan yang tidak lagi dipakai dihapus), serta thumbnail Ocean Split dan Verdant Split pada `public/assets/images/auth/themes/`.
- Keempat file dibackup sebelum perubahan. Path lengkap dan SHA256 yang cocok antara source dan backup tercatat dalam `backups.json`; nama backup bertimestamp `20261106_134413_single_brand`.
- Slider, kontur Midnight, notice CMS, pilihan tema dan opsi tersimpan tidak diubah oleh perbaikan ini. Render QA tidak melakukan penyimpanan konfigurasi.

## Verifikasi terbaru

- Sebelum perubahan: render 16 view baru menemukan 12 elemen panel-brand duplikat di markup. Midnight sebelumnya menyembunyikan elemen tersebut melalui CSS; Ocean dan Verdant menampilkannya.
- Sesudah perubahan: 16/16 view memiliki tepat satu brand di area form, nol brand di panel visual dan satu footer copyright. Hasil di `before-render-checks.json` dan `after-render-checks.json`.
- Chrome: 48/48 pemeriksaan lulus untuk empat tema × empat halaman × viewport 320, 820 dan 1440px. Setiap halaman memiliki satu brand yang terlihat, footer terlihat, tidak ada panel-brand dan tidak ada overflow horizontal. Hasil di `browser-checks.json`.
- Pemeriksaan browser di atas menggunakan HTML yang dirender oleh Blade aktual dengan opsi tersimpan saat ini, disajikan dari origin aplikasi agar CSS/font asli bekerja. GET langsung `/auth/login` juga HTTP200, tema aktif Clean Peach, satu brand terlihat, nol panel-brand (`live-login.json`).
- Tes render yang sudah ada lulus: **1 test, 104 assertions**. Tes menggunakan DatabaseTransactions untuk Page Theme Setting; tidak melakukan update Site Config.
- `git diff --check` lulus; hanya peringatan normalisasi CRLF pada perubahan lama. Tidak ada pemanggilan panel-brand tersisa di source auth/CSS aktif.
- Screenshot desktop Verdant dan mobile diperiksa secara visual. Thumbnail Ocean dan Verdant diperbarui dari hasil render. Screenshot tersimpan di folder ini.
- Junction preview sementara diverifikasi targetnya, lalu dipindahkan dari public ke `retained-preview-junction`. URL QA sementara sudah ditutup; artefak dipertahankan dan dikecualikan dari analisis source/Graphify.

Graphify tidak diperbarui sesuai instruksi manual; SHA256 tetap `0cb6deffe24e3d77dd2d4a59281835dc773a73f6155c3bab9650f4a67056ecb6`.

Keterbatasan: pemeriksaan responsive menggunakan viewport Chrome, belum perangkat fisik, Safari atau Firefox. Tidak mengulang seluruh pengujian backend karena perubahan hanya penghapusan markup/CSS brand serta pembaruan thumbnail.
