# Arunika Prism — Editorial Rail Mobile QA

Tanggal: 2026-09-04  
Scope: mobile view Arunika Prism; tidak mengubah desktop shell, database, atau tema lain.

## Implementasi

- Tombol pembuka sidebar mobile memakai SVG panel-chevron yang sama dengan toggle desktop.
- Kontrol tetap memiliki target sentuh 44×44 px, tetapi tampak sebagai icon-only tanpa border, padding visual, background, atau shadow.
- Drawer mobile tetap selebar `80vw`, memakai surface token Prism yang sama dengan sidebar desktop, dan mendapat spectral rail 3 px.
- Menu aktif memakai surface lembut bertema dan indikator 3 px.
- Header konten, kartu statistik, dan profile sheet mengikuti arah visual Editorial Rail tanpa mengganti logo produksi.
- Footer drawer hanya menampilkan satu tombol `Awesome Admin` untuk admin/super admin.

## Pengujian otomatis

Focused/affected suite:

```text
19 passed
0 failed
```

Suite yang dijalankan:

- `tests/arunika-prism-mobile-sidebar-static.test.mjs`
- `tests/arunika-prism-theme-static.test.mjs`
- `tests/arunika-mobile-navigation-v2-static.test.mjs`
- `tests/arunika-mobile-v2-theme-static.test.mjs`
- `tests/theme-responsive-typography-static.test.mjs`

Broader Prism suite:

```text
40 tests
38 passed
2 failed (kontrak lama di luar perubahan ini)
```

Kegagalan baseline:

1. `tests/arunika-prism-content-shell-static.test.mjs` masih mengharapkan padding desktop lama `.ph-scrollable-content { padding: 10px 8px 18px; }`.
2. `tests/arunika-prism-header-actions-static.test.mjs` memakai locator lama yang tidak mengenali struktur profile/palette mobile yang sudah ada sebelum patch ini.

Pemeriksaan tambahan:

- `node --check public/assets/js/themes/arunika_prism/arunika_prism.js`: lulus.
- `node --check public/assets/js/themes/arunika-mobile-navigation-v2.js`: lulus.
- `php artisan view:cache`: lulus.
- `git diff --check`: lulus; hanya warning line-ending pada perubahan lama di luar scope.

## QA browser

Harness lokal diuji pada 300, 400, 500, 769, dan 1024 px.

| Lebar | Hasil utama |
|---:|---|
| 300 px | Drawer 240 px (`80vw`), grid dua kolom, rail 3 px, overflow horizontal 0 |
| 400 px | Drawer 320 px, profile sheet 280 px dengan offset kanan 14 px, overflow horizontal 0 |
| 500 px | Drawer 400 px, grid dua kolom, overflow horizontal 0 |
| 769 px | Trigger mobile tersembunyi; header/toggle desktop aktif |
| 1024 px | Sidebar dan toggle desktop tetap aktif; profile sheet mobile tersembunyi |

Drawer terbuka pada 400 px:

- backdrop aktif;
- main content `inert`;
- satu tombol `Awesome Admin`;
- user panel desktop tersembunyi;
- indikator rail dan menu aktif masing-masing 3 px;
- console: 0 error, 0 warning.

## Bukti visual

- `closed-400.png`
- `profile-400.png`
- `drawer-400.png`
- `drawer-400-active.png`
- `desktop-1024.png`

## Graphify

Graph diperbarui incremental setelah implementasi:

```text
21,636 nodes
39,803 links
```

## Batas verifikasi

- QA browser memakai harness lokal yang memuat asset produksi; tidak mengubah data aplikasi.
- Route aplikasi dengan session/role riil tidak dimutasi dalam pengujian ini.
- Dua kegagalan broader suite di atas tidak diperbaiki karena berada di luar scope dan bukan regresi patch Editorial Rail.
