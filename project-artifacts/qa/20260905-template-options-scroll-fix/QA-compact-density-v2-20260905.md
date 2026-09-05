# QA — Template Options two-step compact density

Tanggal: 2026-09-05

## Scope

Pengguna meminta dua tingkat compact dari skala sebelumnya, termasuk form checkbox/switch. Perubahan tetap scoped ke Template Options dan tidak mengubah state, Vue binding, normalizer, Coloris behavior, link/unlink, atau persistence.

## Final density tokens

| Control | Final size |
|---|---:|
| Input, select, unit field, radius/link height | 36px |
| Segmented, device, footer action button | 34px |
| Unit selector | 68px |
| Switch / checkbox | 46 × 24px |
| Main option group gap | 8px |

Font size tetap dipertahankan agar label dan helper text tidak menjadi sulit dibaca. Switch diperkecil lewat token ukuran dan `background-size: 1rem 1rem`, bukan transform yang memengaruhi layout.

## Source and mockup changes

- `public/assets/css/article/article-template-manager-2026.css`
  - token compact v2: `2.25rem`, `2.125rem`, `4.25rem`, `2.875rem`, dan `1.5rem`;
  - padding/gap header, option row, frame, sidebar, box control, Coloris, navigation, dan action buttons dipadatkan;
  - responsive radius field mobile diperbaiki: empat side field selalu menjadi grid 2×2, dengan link di kanan bawah.
- `project-artifacts/mockups/template-options-20260905/forms-v3/index.html`
  - `--control-v3: 36px`, `--action-v3: 34px`, `--switch-w: 46px`, `--switch-h: 24px`;
  - seluruh state screenshot desktop diregenerate.
- `tests/article-template-presentation.test.mjs`
  - test dua tingkat compact dan regression test urutan radius mobile.

## Browser evidence

CSS produksi aktif, memakai Bootstrap dan struktur modal production:

- Desktop `1440 × 900`
  - control `36px`, action `34px`, switch `46 × 24px`, link `34 × 36px`;
  - horizontal overflow `false`.
- Mobile `390 × 844`
  - ukuran sama dan horizontal overflow `false`.
  - radius side coordinate: dua field baris pertama `142px`, dua field baris kedua `142px`, chain link `34 × 36px` berada di kanan bawah.
- Scroll regression tetap sehat:
  - desktop: `clientHeight=741`, `scrollHeight=1337`, `scrollTop 0 → 596.67`;
  - mobile: `clientHeight=723`, `scrollHeight=1347`, `scrollTop 0 → 623.33`;
  - kedua viewport: `canScroll=true`, console `0 error, 0 warning`.

## Visual artifacts

- Production control desktop: `compact-density-v2-production-controls-1440.png`.
- Production control mobile: `compact-density-v2-production-controls-390.png`.
- Production mobile radius grid: `compact-density-v2-radius-mobile-390.png`.
- Refreshed mockup states: `forms-v3/screens/01-header-content.png` sampai `09-archive-shell.png`.

## Automated verification

- Red phase: two-step token/switch test gagal pada skala 40/38 dan switch 56 × 28 lama; mobile radius test gagal sebelum placement grid ditambahkan.
- `node --test tests/article-template-presentation.test.mjs`: **23 passed, 0 failed**.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs`: **85 passed, 0 failed**.
- `php artisan test --compact tests/Feature/Article`: **30 passed, 411 assertions**.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: **passed**.
- `php artisan view:cache`: **passed**.
- scoped `git diff --check`: **passed**.

## Runtime boundary

Authenticated manager live masih tidak dapat dibuka dari sesi browser QA tanpa login. Tidak ada kredensial, Apply, atau Save dilakukan. Browser proof memakai source CSS aktif dan struktur modal production; hard reload pada manager live tetap diperlukan untuk validasi click-through final.

## Graphify

Graphify tidak dipanggil dan data graph tidak diperbarui, sesuai instruksi pengguna.

## Backups

- `project-artifacts/backups/20260905_164825-template-options-compact-density-v2/`
- `project-artifacts/backups/20260905_165536-template-options-compact-density-v2-screens/`
- `project-artifacts/backups/20260905_165715-template-options-compact-density-v2-qa/`
