# Design QA — Arunika Equinox Teal Mist

Tanggal: 2026-09-01  
Status: **PASS WITH NOTES**

## Target dan scope

Target visual yang dipilih adalah:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\arunika-equinox-visual-audit-20260901\07-preview-teal-mist-compact-hover.png`

Scope implementasi: mengurangi wash warna teal yang terlalu dalam, membuat hierarchy dashboard lebih compact, memperbaiki state hover/active menu, menjaga dark mode tetap terbaca, dan mempertahankan behavior dynamic dashboard untuk theme Equinox saja.

## Guidance yang digunakan

- UI UX Pro Max: `data-dense-dashboard`, `sidebar-hover-contrast`, light neutral surfaces, dark readable text, subtle hover elevation, visible focus, dan reduced-motion support.
- Product Design image-to-code/audit flow: preview digunakan sebagai visual reference, lalu diverifikasi terhadap source CSS dan Blade aktual.
- Superpowers TDD/systematic debugging/verification serta Ponytail untuk menjaga perubahan tetap scoped.
- Graphify query: `Arunika Equinox dashboard hero grid menu hover active state`.

## Source yang dibaca

- `public/assets/css/themes/arunika_equinox/arunika_equinox.css`
- `resources/views/themes/arunika_equinox/cms/cms_layout.blade.php`
- `resources/views/awesome_admin/awesome_admin.blade.php`
- `public/assets/js/themes/arunika_equinox/arunika_equinox.js`
- `tests/arunika-equinox-theme-static.test.mjs`
- `tests/arunika-equinox-mobile-sidebar-static.test.mjs`
- `tests/arunika-equinox-profile-palette-runtime.test.mjs`

## Perubahan implementasi

- Light canvas/sidebar/panel dibuat lebih netral dan opaque; teal dipusatkan pada accent, icon, active state, dan focus ring.
- Sidebar Equinox diringankan dari radius `26px` menjadi `20px` dan shadow besar menjadi shadow yang lebih halus.
- Hover menu menggunakan white raised row `rgba(255, 255, 255, 0.88)` dengan shadow ringan `0 5px 16px rgba(60, 44, 78, 0.05)`.
- Active menu menggunakan mint tint tanpa shadow; dark active state memakai warna text terang agar tidak tenggelam.
- Dashboard view Equinox mendapat hero icon + subtitle dan grid dashboard compact; theme lain mempertahankan markup lama.
- Label dashboard menggunakan `15px`, title desktop `28px`, title mobile `23px`.
- Grid hover memakai token yang sama dengan menu hover sehingga light/dark state tidak bercabang dengan warna hard-coded.
- Focus-visible outline dan reduced-motion rule ditambahkan pada scope Equinox.
- Muted light text disesuaikan menjadi `#526c67`; perhitungan contrast terhadap warna sidebar terukur sekitar `4.76:1` pada titik terang yang diuji.

## Backup

Dibuat sebelum perubahan source dan diverifikasi byte-level sebelum edit:

- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-teal-mist-implementation\arunika_equinox.css.bak_20260901_teal_mist`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-teal-mist-implementation\awesome_admin.blade.php.bak_20260901_teal_mist`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260901_arunika-equinox-teal-mist-implementation\arunika-equinox-theme-static.test.mjs.bak_20260901_teal_mist`

## File yang dimodifikasi untuk scope ini

- `public/assets/css/themes/arunika_equinox/arunika_equinox.css`
- `resources/views/awesome_admin/awesome_admin.blade.php`
- `tests/arunika-equinox-theme-static.test.mjs`

Perubahan tracked Aurora/Prism yang sudah ada sebelumnya tidak disentuh oleh implementasi Equinox ini.

## Verifikasi

### Automated

- Combined scoped test suite: **15/15 passed**.
- `git diff --check`: **passed**.
- `php artisan view:cache`: **successful**.

### Browser harness

Harness memuat CSS Equinox aktif dari source project, Bootstrap/font assets lokal project, dan struktur dashboard yang mengikuti Blade view.

- Desktop `1717x916`: no horizontal/vertical outer overflow; sidebar `256px`, radius `20px`; title `28px`; dashboard label `15px`.
- Light hover: `rgba(255, 255, 255, 0.88)` + `0 5px 16px rgba(60, 44, 78, 0.05)`.
- Light active: mint surface + teal accent text.
- Dark hover: `rgba(255, 255, 255, 0.075)` + `0 2px 8px rgba(0, 0, 0, 0.18)`.
- Dark active: `rgba(255, 255, 255, 0.13)` + `rgb(245, 255, 252)` text.
- Mobile `375x800`: sidebar closed/off-canvas, hero/grid width `347px`, title `23px`, no outer overflow.
- Console: no new runtime error from the final harness capture; favicon-only requests are not application errors.

Evidence files:

- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\equinox-teal-mist-implementation.png`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\equinox-teal-mist-dark.png`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\equinox-teal-mist-mobile.png`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\preview-vs-implementation.png`
- `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\playwright\arunika-equinox-teal-mist-implementation-20260901\computed-metrics.json`

## Notes and boundary

Perbandingan visual menunjukkan arah target tercapai pada surface, hierarchy, icon treatment, dan menu states. Wave/dot decoration pada preview generatif tidak diterapkan karena tidak ada asset source di aplikasi dan tidak perlu direka ulang sebagai CSS art.

Route live `http://laravel-13-phoenix.aruna/awesome_admin` masih mengarahkan ke `/auth/login` pada sesi QA. Tidak ada login, submit, Save, Reset, atau perubahan data yang dilakukan, sehingga screenshot terautentikasi dari route produksi belum diverifikasi. Static source, compiled Blade cache, dan harness runtime sudah diverifikasi.

## Graphify

Graphify di-update incremental dengan `--update --no-viz --code-only`: 7 file code dire-ekstrak, 21.512 nodes dan 37.398 edges ditulis. Sebanyak 115 file non-code (97 docs dan 18 images) dilewati karena tidak ada LLM backend yang diotorisasi; graph visual/report tidak diregenerasi.
