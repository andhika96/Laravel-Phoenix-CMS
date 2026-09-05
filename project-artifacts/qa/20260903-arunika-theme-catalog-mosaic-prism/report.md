# Arunika Theme Catalog and Lucent Toggle — QA Report

Tanggal: 2026-09-03

## Implementasi

- CMS Theme Manager kini hanya menampilkan dan menerima: `arunika_prism`, `arunika_aurora`, `arunika_lucent`, `arunika_equinox`.
- Urutan katalog: Prism → Aurora → Lucent → Equinox.
- Migration `2026_09_03_213000_remove_arunika_mosaic_theme` mengalihkan `theme_settings.id=1` ke Prism lalu menghapus row Mosaic.
- Seeder baru memakai Prism sebagai default dan tidak lagi menanam Mosaic.
- Runtime CMS Mosaic dipindahkan ke quarantine backup: view, CSS, JS, preview, dan dua test khusus Mosaic.
- Template artikel `mosaic-classic`/`mosaic-magazine`, migration history, mockup, dan QA historis tetap dipertahankan.
- Collapsed Lucent account rail memaksa satu kolom; toggle memakai `grid-column: 1` dan `justify-self: center` agar sejajar dengan ikon menu.

## Bukti database

- `themes`: Mosaic tidak ada; empat theme aktif tersedia.
- `theme_settings.id=1`: `theme_id=7`, `theme_code=arunika_prism`, `theme_name=Arunika Prism`.
- Migration dijalankan targeted dengan `php artisan migrate --path=database/migrations/2026_09_03_213000_remove_arunika_mosaic_theme.php --force`.
- Migration unrelated `2026_08_29_000300_add_custom_javascript_to_page_builder_table` tetap Pending dan tidak dijalankan.

## Bukti browser

- Static harness collapsed desktop `769x844`: sidebar width `76px`; pusat toggle `37.667px`; pusat ikon menu `37.667px`; selisih `0px`.
- Computed state: account `grid-template-columns: 51.3333px`, toggle `grid-column: 1`, `justify-self: center`.
- Console harness: 0 error, 0 warning.
- Screenshot: [lucent-collapsed-toggle-769.png](./lucent-collapsed-toggle-769.png)

## Test dan pemeriksaan

- Affected Node suite: **52 passed, 0 failed**.
- `ThemeManagerTest` + `SiteTypographyPreviewSettingsTest`: **9 passed, 91 assertions**.
- PHP lint migration/controller/seeders/feature tests: pass.
- `php artisan view:cache`: pass.
- `git diff --check`: pass; hanya warning normalisasi CRLF pada file dirty lama.
- Graphify update/check-update: selesai tanpa perubahan graph tertunda.

## Backup dan batasan

- Database dump `lr_themes`/`lr_theme_settings` dan source backup: `project-artifacts/backups/20260903_213000_remove-mosaic-default-prism/`.
- Runtime Mosaic yang dipindahkan masih dapat dipulihkan dari subfolder `quarantined`.
- Tidak ada `git reset`, commit, push, atau staging massal.
- Route eksperimental legacy yang berada di luar CMS Theme Manager dan migration/mockup historis tidak dihapus.
