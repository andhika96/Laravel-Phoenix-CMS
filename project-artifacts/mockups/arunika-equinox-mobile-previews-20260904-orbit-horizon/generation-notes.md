# Arunika Equinox Mobile — Orbit Horizon

Tanggal: 2026-09-04

Status: preview design saja; belum diimplementasikan ke source produksi.

## Referensi

- `public/assets/images/themes/previews/arunika-equinox-theme-preview.png`
- `resources/views/themes/arunika_equinox/cms/cms_layout.blade.php`
- `resources/views/themes/arunika_equinox/components/menu.blade.php`
- `public/assets/css/themes/arunika_equinox/arunika_equinox.css`

## Output

- `equinox-orbit-dashboard-closed.png` — dashboard mobile dengan sidebar tertutup.
- `equinox-orbit-profile-sheet.png` — profile sheet dari avatar topbar.
- `equinox-orbit-sidebar-open.png` — drawer 80% dengan Orbit Horizon bands dan footer Awesome Admin.

## Kontrak desain

- Toggle sidebar memakai icon panel-chevron yang sama dengan Equinox desktop, bukan hamburger atau tombol X.
- Menu drawer mengikuti source desktop: Visit Site, Dashboard, ALL MENUS, Manage Articles, Manage Cover Image, Manage Event.
- Profile sheet memuat Profile, Settings, Dark Mode, Theme Color, dan Logout.
- Drawer menampilkan tepat satu tombol Awesome Admin untuk Administrator/Super Admin.
- Bahasa visual menggunakan pearl, mist mint, teal, dan amber-sunset dengan abstract orbit horizon bands.
- Tidak memakai editorial rail Prism, luminous wave Aurora, atau ilustrasi gunung dari preview Equinox lama.
- Background dan active state dirancang agar dapat diturunkan dari warna tema pilihan saat implementasi.

## Generasi

Ketiga preview dibuat dengan built-in ImageGen menggunakan preview desktop Equinox sebagai style reference. Dua koreksi terarah dilakukan untuk menyederhanakan topbar profile, memperbaiki arah chevron tertutup, dan memastikan label `LaraPhoenix CMS` ditulis tepat.

