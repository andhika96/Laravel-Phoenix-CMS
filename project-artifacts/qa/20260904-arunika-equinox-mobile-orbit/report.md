# Arunika Equinox Mobile — Runtime QA

Tanggal: 2026-09-04

Scope: mobile dashboard, profile sheet, sidebar, desktop-identical panel-chevron icon, dan desktop-identical sidebar artwork.

## Keputusan final

- Drawer mobile memakai gradient/surface Equinox desktop.
- Background bagian bawah diwarisi langsung dari pseudo-element desktop `ph-sidebar::after` dengan asset `arunika-equinox-sidebar-landscape.png`.
- Mobile CSS tidak menduplikasi atau mengganti URL, posisi, ukuran, opacity, maupun aturan dark-mode asset tersebut.
- Tombol buka dan tutup sidebar memakai geometri SVG panel-chevron yang sama dengan toggle desktop.
- Drawer menampilkan menu dinamis yang sama serta tepat satu footer `Awesome Admin` untuk admin/super admin.
- Profile topbar disederhanakan menjadi avatar; profile sheet tetap memuat identitas, Profile, Settings, Dark Mode, Theme Color, dan Logout.
- Dashboard mobile mempertahankan empat metric card dalam grid dua kolom pada 300–500px.

## TDD

Siklus RED diamati untuk:

1. surface desktop tanpa override asset;
2. profile identity dan footer admin;
3. grid metric dua kolom;
4. stacking footer di atas artwork;
5. ruang nama situs pada drawer;
6. kontrak lintas-tema yang sebelumnya mengharuskan URL asset diduplikasi di mobile CSS.

Hasil akhir affected suite:

```text
31 tests
31 passed
0 failed
```

Pemeriksaan tambahan:

- `node --check public/assets/js/themes/arunika_equinox/arunika_equinox.js`: lulus.
- `node --check public/assets/js/themes/arunika-mobile-navigation-v2.js`: lulus.
- `php artisan view:cache`: lulus.
- `git diff --check`: lulus; warning line-ending hanya berasal dari perubahan lama di luar scope.

## Browser QA

Harness lokal memuat stylesheet, JavaScript, font, dan background image produksi.

| Viewport | Drawer | Metric grid | Horizontal overflow | Icon mobile = desktop |
|---:|---:|---:|---:|---:|
| 300px | 240px (`80vw`) | 2 kolom | 0 | Ya |
| 400px | 320px (`80vw`) | 2 kolom | 0 | Ya |
| 500px | 400px (`80vw`) | 2 kolom | 0 | Ya |
| 1024px | desktop rules | desktop rules | 0 | Ya |

Background sidebar runtime:

```text
asset: /public/assets/images/themes/arunika_equinox/arunika-equinox-sidebar-landscape.png
position: 50% calc(100% + 24px)
size: 100% auto
opacity light: 0.72
```

Profile sheet pada 400px:

- lebar 280px;
- offset kanan 14px;
- lima menu item;
- identity row terlihat;
- horizontal overflow 0.

Drawer terbuka pada 400px:

- lebar 320px;
- background image desktop aktif;
- satu footer `Awesome Admin` berada di foreground;
- tombol close terlihat;
- main content inert;
- horizontal overflow 0;
- console browser 0 error dan 0 warning.

## Bukti visual

- `profile-400.png`
- `sidebar-400-final.png`
- `sidebar-400-active.png`
- `harness.html`

## Backup

`project-artifacts/backups/20260904_225326_arunika-equinox-mobile-orbit/`

## Graphify

Incremental update selesai:

```text
21,644 nodes
39,810 links
```

Graphify melaporkan 58 file metadata/config menghasilkan nol node; peringatan ini tidak menghentikan pembaruan dan tidak terkait dengan source Equinox yang diubah.

## Batas verifikasi

- Browser QA menggunakan harness lokal read-only dengan asset produksi; tidak mengubah database atau session aplikasi.
- Route Laravel dengan role pengguna riil tidak dimutasi selama QA.
