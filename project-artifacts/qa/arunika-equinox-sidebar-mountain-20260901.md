# Follow-up QA — Arunika Equinox Sidebar Mountain

Tanggal: 2026-09-01  
Status: **PASS WITH NOTES**

## Penyebab gap

Mountain landscape pada screenshot adalah bagian dari preview desain, bukan asset yang sebelumnya ada di source Equinox. Source aktif hanya menyediakan pattern dekoratif musiman melalui `arunika_equinox.js`; default pattern adalah `none`, dan CSS Equinox sebelumnya menyembunyikan `body::before`/`body::after`. Karena itu preview terlihat memiliki gunung, sementara implementasi sebelumnya tidak.

## Implementasi

- Asset baru: `D:\Laragon\www\laravel-13-phoenix\public\assets\images\themes\arunika_equinox\arunika-equinox-sidebar-landscape.svg`
- CSS baru di `D:\Laragon\www\laravel-13-phoenix\public\assets\css\themes\arunika_equinox\arunika_equinox.css`:
  - pseudo-element `sidebar::after` di bawah sidebar expanded;
  - tinggi `240px`, opacity light `0.72`;
  - opacity dark `0.22` dengan filter yang lebih redup;
  - opacity `0` saat sidebar collapsed;
  - pointer-events nonaktif dan layer menu tetap di atas landscape.
- Test baru di `D:\Laragon\www\laravel-13-phoenix\tests\arunika-equinox-theme-static.test.mjs` memastikan asset ada dan layer hanya aktif pada expanded sidebar.

SVG dipilih karena preview menggunakan ilustrasi flat/layered, tidak ada asset source asli yang dapat dipakai ulang, dan format ini tetap tajam serta ringan tanpa menambah dependency atau CSS art di runtime.

## Backup

Dibuat sebelum perubahan follow-up dan diverifikasi SHA-256 source/backup sama:

- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-sidebar-mountain\arunika_equinox.css.bak_20260901_sidebar_mountain`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-sidebar-mountain\arunika-equinox-theme-static.test.mjs.bak_20260901_sidebar_mountain`

## Verifikasi

- Scoped combined tests: **16/16 passed**.
- `git diff --check`: passed.
- `php artisan view:cache`: successful.
- Graphify incremental code-only update: 1 code file re-extracted; 116 non-code files skipped because no LLM backend was authorized.
- Browser harness `1717x916`: computed pseudo background image loaded from the new SVG, height `240px`, opacity `0.72`.
- Browser dark mode: same asset opacity `0.22`.
- Browser mobile `375x800`: collapsed sidebar landscape is hidden/off-canvas; no outer horizontal/vertical overflow.

Evidence screenshot final:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\equinox-teal-mist-implementation.png`

Comparison preview + implementation:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\preview-vs-implementation.png`

Route production `/awesome_admin` masih mengarahkan sesi QA ke `/auth/login`, sehingga authenticated production screenshot belum dapat diambil. Tidak ada login, Save, Reset, submit, atau perubahan data aplikasi.
