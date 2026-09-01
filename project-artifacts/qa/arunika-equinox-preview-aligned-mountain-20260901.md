# Follow-up QA — Arunika Equinox Preview-Aligned Mountain

Tanggal: 2026-09-01  
Status: **PASS WITH NOTES**

## Masalah yang dikoreksi

Landscape pertama dibuat ulang sebagai SVG geometris sehingga bentuknya tidak sama dengan preview terbaru. Selain itu, asset hasil generator awal membawa checkerboard sebagai pixel RGB.

## Koreksi final

- Asset aktif sekarang adalah `D:\Laragon\www\laravel-13-phoenix\public\assets\images\themes\arunika_equinox\arunika-equinox-sidebar-landscape.png`.
- Asset dibuat dari referensi preview Teal Mist, dibersihkan menjadi PNG RGBA transparan, lalu dipasang sebagai background pseudo-element sidebar.
- CSS menggunakan `center calc(100% + 24px) / 100% auto` agar posisi sun dan puncak gunung sejajar dengan preview.
- Landscape light expanded memakai opacity `0.72`; dark memakai `0.22`; collapsed/mobile memakai `0`.
- SVG geometris lama tidak lagi dipakai dan disimpan sebagai superseded backup.

Preview target:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\arunika-equinox-visual-audit-20260901\07-preview-teal-mist-compact-hover.png`

## Evidence visual

- Final implementation: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\equinox-teal-mist-implementation.png`
- Preview vs implementation: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\preview-vs-implementation.png`
- Computed metrics: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\computed-metrics-mountain-match.json`

Comparison satu layar menunjukkan landscape final memiliki komposisi layered mountain, garis kontur, warna pale teal, sun, dan posisi bawah yang mengikuti preview. Perbedaan kecil tetap mungkin karena preview referensi adalah gambar generatif, bukan asset source asli.

## Verifikasi

- Combined scoped tests: **16/16 passed**.
- `git diff --check`: passed.
- `php artisan view:cache`: successful.
- Browser `1717x916`: pseudo background image memuat PNG aktif, tinggi `240px`, opacity `0.72`.
- Browser dark mode: opacity landscape `0.22`, hover/active menu tetap terbaca.
- Browser mobile `375x800`: sidebar collapsed/off-canvas dan landscape tidak tampil; no outer overflow.
- PNG aktif diverifikasi sebagai `923 x 1705`, `8-bit RGBA`.

## Backup dan scope

Backup sebelum koreksi final berada di:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-mountain-final\`

Backup posisi final berada di:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-preview-position\`

File source aktif yang berubah pada follow-up ini:

- `public/assets/css/themes/arunika_equinox/arunika_equinox.css`
- `public/assets/images/themes/arunika_equinox/arunika-equinox-sidebar-landscape.png`
- `tests/arunika-equinox-theme-static.test.mjs`

Perubahan Aurora/Prism dan view dashboard sebelumnya tetap dipertahankan.

Route live `/awesome_admin` masih memerlukan sesi autentikasi, sehingga capture authenticated production belum dilakukan. Tidak ada login, Save, Reset, submit, atau perubahan data aplikasi.
