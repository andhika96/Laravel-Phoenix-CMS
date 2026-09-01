# Arunika Aurora and Prism Sidebar Hover QA

Tanggal: 2026-09-01

## Tujuan

Menyamakan hover menu sidebar Arunika Aurora dan Arunika Prism dengan referensi kartu hover putih pada screenshot, termasuk palette `cool-gray`.

## Perubahan

- `public/assets/css/themes/arunika_aurora/arunika_aurora.css:1682-1687` menambahkan Prism-compatible hover elevation `0 2px 8px rgba(42, 35, 57, 0.06)`.
- `public/assets/css/themes/arunika_aurora/arunika_aurora.css:2056` mengubah fallback light hover tint dari `white 84%` menjadi `white 94%`.
- `public/assets/css/themes/arunika_aurora/arunika_aurora.css:2080-2081` mendefinisikan surface hover netral `rgba(255, 255, 255, 0.86)` dan shadow target `0 5px 16px rgba(60, 44, 78, 0.05)`.
- `public/assets/css/themes/arunika_aurora/arunika_aurora.css:2404-2409` menerapkan surface netral tersebut pada hover menu light untuk semua palette, termasuk `cool-gray`.
- `tests/arunika-aurora-submenu-layout-static.test.mjs:35-64` mengunci tint `94%`, surface putih screenshot, dan shadow sebagai regression contract.
- `public/assets/css/themes/arunika_prism/arunika_prism.css` menambahkan override `cool-gray` light yang memakai surface dan shadow Aurora yang sama.
- `tests/arunika-prism-sidebar-shell-surface-static.test.mjs` mengunci contract Prism untuk `cool-gray` dan memperbaiki dua assertion fallback token yang stale.
- Active state putih, ukuran row, layout, markup, dan dark-mode override tidak diubah.

## Backup

- `project-artifacts/backups/20260901_172422_arunika-aurora-sidebar-hover/` menyimpan kondisi sebelum perubahan tint.
- `project-artifacts/backups/20260901_arunika-aurora-sidebar-hover-visibility/` menyimpan kondisi sebelum penambahan shadow. SHA-256 backup terakhir cocok dengan source aktif sebelum patch shadow: CSS `74089d40f91ffe60e0708c0b75a93ecd19748170fa99a0d01a63d8bc00c72c4f`; test `9acf801248814b5bd1e6a1b8e711e6c49ffe34f86a15cddaff05249815968989`.
- `project-artifacts/backups/20260901_arunika-aurora-sidebar-hover-screenshot-target/` menyimpan kondisi sebelum perubahan target screenshot; backup CSS, test, dan report diverifikasi byte-identical.
- `project-artifacts/backups/20260901_arunika-prism-cool-gray-hover/` menyimpan CSS Prism, test Prism, dan report sebelum perubahan Prism; backup CSS/test diverifikasi byte-identical.

## TDD dan test

- RED: dengan source sementara `84%`, `node --test tests/arunika-aurora-submenu-layout-static.test.mjs` gagal pada ekspektasi Prism tint.
- GREEN: setelah `94%`, focused test lulus `1/1`.
- RED kedua: sebelum override wrapper ditambahkan, test gagal pada ekspektasi surface `#D8DDE6`.
- RED ketiga: sebelum surface screenshot diterapkan, test gagal pada ekspektasi token hover putih dan shadow target.
- GREEN ketiga: setelah surface screenshot diterapkan, focused test lulus `1/1`.
- Prism RED: test baru gagal `1` assertion pada `cool-gray` sebelum override Prism ditambahkan.
- Prism GREEN: `node --test tests/arunika-prism-sidebar-shell-surface-static.test.mjs` lulus `3/3`.
- Regression terkait lulus `7/7`:
  `node --test tests/arunika-aurora-submenu-layout-static.test.mjs tests/arunika-aurora-theme-color-gradient-static.test.mjs tests/arunika-aurora-menu-group-spacing-static.test.mjs tests/arunika-aurora-category-border-contrast-static.test.mjs`
- Suite seluruh test Aurora: `15` pass, `1` fail. Failure tetap baseline pada `tests/arunika-aurora-sidebar-static.test.mjs` karena assertion lama `id="sidebar-toggle-icon"`; tidak terkait hover dan tidak diubah.
- `git diff --check`: lulus.

## Runtime browser

QA read-only memakai CSS production pada harness sidebar yang meniru DOM menu aktif.

- `data-bs-theme="light"`, `data-ph-theme-color="cool-gray"`.
- Background sebelum hover: `rgba(0, 0, 0, 0)`.
- Background setelah transition selesai: `rgba(255, 255, 255, 0.86)`.
- Token hover pada row: `rgba(255, 255, 255, 0.86)`.
- Box shadow setelah transition selesai: `rgba(60, 44, 78, 0.05) 0px 5px 16px 0px`.
- Console error: `0`.
- Screenshot: `project-artifacts/qa/playwright/arunika-aurora-sidebar-hover-20260901/aurora-sidebar-hover-screenshot-target.png`.
- Harness dan runner: `project-artifacts/qa/playwright/arunika-aurora-sidebar-hover-20260901/`.

### Prism `cool-gray`

- Background setelah transition selesai: `rgba(255, 255, 255, 0.86)`.
- Token `--ph-prism-sidebar-hover`: `rgba(255, 255, 255, 0.86)`.
- Box shadow setelah transition selesai: `rgba(60, 44, 78, 0.05) 0px 5px 16px 0px`.
- Console error: `0`.
- Screenshot: `project-artifacts/qa/playwright/arunika-aurora-sidebar-hover-20260901/prism-sidebar-hover-cool-gray.png`.

Halaman dashboard live mengarahkan browser ke `/auth/login` karena profil QA tidak memiliki sesi autentikasi yang aktif. Tidak ada login, Save, Reset, submit, atau perubahan pengaturan aplikasi yang dilakukan. Browser persistent sempat menulis cache/profile generated; seluruh perubahan di `project-artifacts/playwright-auth-matrix-profile` sudah dibersihkan kembali setelah QA.

## Graphify

Graphify query digunakan untuk memetakan hubungan Aurora/Prism sidebar dan source JS/CSS terkait. Graph tidak diperbarui karena perubahan ini berupa patch CSS kecil dan tidak mengubah struktur hubungan source secara substansial.
