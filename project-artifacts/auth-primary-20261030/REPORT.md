# Laravel 13 Phoenix — auth Primary Color dan pemisahan CSS

30 Oktober 2026, selesai sebelum batas 16:50 WIB. Pekerjaan lokal; tidak di-commit, push, atau deploy. Tidak menyimpan percakapan ke memori karena tidak diminta.

## Hasil

- Satu input Primary Color di atas Color Nuances pada masing-masing bagian Login, Signup, Forgot Password, Reset Password.
- Memakai pola Blade/Vue/Axios dan plugin Coloris yang sudah ada; tidak menambahkan dependency. Picker primary menggunakan HEX solid tanpa alpha; Color Nuances tetap memiliki perilaku lamanya.
- Warna disimpan per halaman pada `page_theme_settings.page_primary_color`, divalidasi HEX enam digit dan nama halaman yang diizinkan. Request lama tanpa field primary tetap dapat disimpan tanpa mengubah warna.
- Hover/active/disabled dibentuk otomatis; warna teks hitam/putih dipilih berdasarkan kontras. Focus menggunakan warna primary dan outline yang terlihat. Tidak ada input state warna tambahan.
- Enam belas Blade auth memakai `resources/views/auth/auth_layout.blade.php`, tanpa stylesheet tema CMS atau `shared/phoenix-cms.css`. Bootstrap, Font Awesome, font Site Config, dan script autentikasi yang sama tetap digunakan.
- CSS lokal auth berada di `public/assets/css/auth/`: `auth-base.css`, `login.css`, `signup.css`, `forgot-password.css`, `reset-password.css`.
- CSS inline lama dipindahkan ke empat file halaman, dengan pembatas `data-auth-layout` untuk default/card/split_left/split_right. Background, opsi layout, dan Color Nuances lama dipertahankan.
- Memperbaiki tanda kutip pada terjemahan `Don't have an account?` di card/login yang sebelumnya menyebabkan Blade gagal dirender. Perbaikan ditemukan saat memverifikasi seluruh variasi.

## Koreksi ukuran label dari feedback pengguna

Primary Color sebelumnya menggunakan `<label class="h6">`, sedangkan judul lama menggunakan `<div class="h6">`. Aturan aktual `shared/theme-responsive-typography.css` membuat `label` dan `.form-label` mewarisi font dengan `!important`. Elemen div dengan `.h6` mengikuti ukuran heading sehingga lebih besar.

Keempat jenis label sekarang memakai `form-label fw-bold mb-1`; tidak ada perubahan CSS typography global atau tema CMS. Bukti sebelum/sesudah ada di `typography-before.json`, `typography-after.json`, dan screenshot `appearance-labels-*.png`.

Browser sesudah koreksi: semua empat label 13 px pada 1280×720, 14 px pada 1920×1080, dan 13.45 px pada 390×844. Family dan weight sama.

## Source dan konteks yang diperiksa

- Memori: `E:\AI\Memories\20260901-arunika-lucent-handoff.md`, `E:\AI\Memories\20260903-arunika-lucent-mobile-final-handoff.md`, `E:\AI\Memories\20260905-laravel13-phoenix-css-archive-and-equinox-mobile-handoff.md`.
- Graphify: graph lokal `graphify-out/graph.json`, bagian Appearance controller, Page_Theme_Setting, helper pemilih tema, dan auth layout. Graph memiliki referensi path CSS sebelum penataan; path dan perilaku selalu diverifikasi dari source aktif. Graph tidak diperbarui sesuai instruksi manual pengguna; tidak menjadikan hubungan inferred sebagai bukti runtime.
- Source aktif utama: Appearance controller/model/Blade/Vue; `app/Helper/Common.php` (pemilih layout dan pengaturan halaman); `SiteTypography`; enam auth layout tema; 16 Blade auth; stylesheet shared/tema yang memasok tombol, input, notices, typography; script auth Vue; route web auth dan Appearance; konfigurasi dependency dan tes terkait.
- Skills: Ponytail, UI UX Pro Max, Design Taste Frontend digunakan untuk perubahan terbatas dan pemeriksaan konsistensi form. Tidak melakukan redesign.

## Backup dan file berubah

Backup lengkap 20 file lama dan empat row pengaturan sebelum perubahan:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\auth-primary-20261030\backups_20261030_163029`

Daftar path dan SHA-256 tersedia di `backup-manifest.json`. Snapshot database ada di `backups_20261030_163029/auth-settings.json`.

Backup tambahan sebelum koreksi label:

`awesome_admin_appearance.blade.php.bak_20261030_164434_label_size`

File lama diubah: controller/model Appearance, Blade Appearance, Vue Appearance, dan empat halaman pada masing-masing folder `resources/views/auth/{default,card,split_left,split_right}`. Daftar tepat ada pada manifest backup.

File source baru: `app/Support/AuthAppearance.php`, migration `2026_10_30_163000_add_primary_color_to_page_theme_settings.php`, layout auth mandiri, dan lima CSS auth tersebut.

Migration lokal sudah dijalankan (batch 34). Kolom baru default `#1FA675`. Data pengaturan lama diverifikasi sama dengan snapshot; semua warna primary akhir `#1FA675`. Pengujian database memakai transaksi rollback; warna uji browser juga dipulihkan dan diverifikasi.

## Verifikasi terbaru

- PHPUnit pada `tests/` di direktori artifact ini: **7 tests, 34 assertions lulus**. Mencakup palet terang/gelap, kontras, fallback nilai invalid, validasi request, penyimpanan, pengaturan lama tetap, dan kompatibilitas request lama. Log `phpunit.log`.
- Tes regresi Node untuk Lucent, typography, dan theme-manager production: **18 lulus**, `regression-node.log`.
- Pemeriksaan statis semua 16 Blade dan empat stylesheet halaman: lulus, tanpa import tema CMS; syntax PHP/JavaScript lulus; `git diff --check` lulus.
- Seluruh **16 variasi Blade dirender**; masing-masing dimuat di browser pada desktop 1280 dan mobile 390 (**32 kombinasi**). Tidak ditemukan overflow horizontal dan semuanya menggunakan warna primary. Bukti `browser-templates.json`, `rendered/`, screenshot masing-masing variasi login.
- State tombol browser: normal `rgb(31,166,117)`, hover `rgb(28,149,105)`, active `rgb(25,133,94)`, disabled `rgb(188,228,214)`, focus outline 2 px. Warna teks mengikuti palet.
- Login nyata ke CMS berhasil. Empat field terlihat di atas Color Nuances. Coloris terbuka; alpha primary tidak ditampilkan.
- Save melalui tombol form Appearance berhasil HTTP 200. Reload mengembalikan primary login `#6633CC`; halaman login nyata memakai ungu dengan teks putih. Signup/forgot tetap hijau. Pengaturan dikembalikan dan dibandingkan dengan sebelum tes: sama. Bukti `browser-save.json`, `restored-settings.json`.
- Route nyata login/signup/forgotpassword dibuka. Reset password diuji melalui render fixture karena tidak membuat token atau mengubah password akun.
- Koreksi label terverifikasi di tiga ukuran browser, dengan screenshot sesudah koreksi.

## Batas verifikasi dan temuan lama

- Ada error JavaScript `grecaptcha is not defined` pada signup. Error yang sama direproduksi dari Blade backup dengan layout CMS lama dan dari auth baru (`browser-final.json`). Ini masalah lama pemuatan reCAPTCHA, tidak diperbaiki dalam scope CSS/primary. Jangan mengklaim signup end-to-end telah lulus.
- Tidak mengirim signup, email forgot password, atau reset password sungguhan. Tidak menguji SSO provider, browser lain/perangkat fisik, atau produksi. Variasi auth diuji melalui fixture render, bukan mengganti pengaturan tema global.
- Tidak mengubah Page Builder/File Manager. Tidak mengubah tema CMS, typography global, atau Graphify.
- Script migrasi CSS pada artifact adalah catatan pekerjaan sekali jalan dengan guard hash dan deadline; jangan dipakai sebagai pipeline build.

## Recovery

Source lama tersedia pada backup manifest; pulihkan hanya file dalam scope ini bila diminta. Migration memiliki `down()` untuk menghapus kolom baru. Jangan melakukan rollback database atau menimpa source tanpa memeriksa perubahan terbaru pengguna. File source baru dan migration harus diperlakukan sebagai satu paket perubahan dengan source lama yang dimodifikasi.

Tidak ada pekerjaan implementasi yang tertunda dalam scope ini. Temuan reCAPTCHA memerlukan scope terpisah bila pengguna ingin memperbaikinya.
