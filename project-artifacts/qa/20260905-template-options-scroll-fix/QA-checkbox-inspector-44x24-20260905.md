# QA — Template Options checkbox inspector parity

Tanggal: 2026-09-05

## Source of truth

Pengguna memberikan computed Inspect Element dari Header content dan meminta nilai styling diikuti persis.

| Property | Required value | Implemented value |
|---|---:|---:|
| `width` | `2.75rem !important` | `2.75rem !important` |
| `min-width` | `2.75rem !important` | `2.75rem !important` |
| `height` | `1.5rem !important` | `1.5rem !important` |
| `margin` | `0 0 0 auto !important` | `0 0 0 auto !important` |
| `border-radius` | `2rem` | `2rem` |
| `background-size` | `1rem 1rem` | `1rem 1rem` |

## Implementation

`public/assets/css/article/article-template-manager-2026.css` now declares `--article-template-switch-width: 2.75rem` and keeps the height at `1.5rem`. The shared `.form-switch .form-check-input` rule now applies `!important` to both width and min-width, matching the screenshot’s cascade requirement.

The V3 mockup mirrors the same visible geometry with `--switch-w: 44px`, `--switch-h: 24px`, a 16px thumb, and a 20px active-thumb translation.

## Browser evidence

The exact Header content markup from the manager was rendered with active production Bootstrap and CSS.

### Desktop 1440 × 900

- Switch rect: **44 × 24px**.
- Computed width/min-width/height: `44px / 44px / 24px`.
- Computed margin: `0px 0px 0px auto` resolved by flex layout.
- Computed border radius: `32px` (`2rem`).
- Computed background size: `16 × 16px` (`1rem`).
- Parent header label: `display:flex`, `justify-content:space-between`.
- Horizontal overflow: `false`.

### Mobile 390 × 844

- Switch rect: **44 × 24px**.
- Parent label stays full width and flex-aligned.
- Horizontal overflow: `false`.

Console: **0 error, 0 warning**.

## Regression coverage

- The presentation test changed from the old 46px switch token to the requested `2.75rem` / 44px token and was verified red before the CSS update.
- `node --test tests/article-template-presentation.test.mjs`: **25 passed, 0 failed**.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs`: **87 passed, 0 failed**.
- `php artisan test --compact tests/Feature/Article`: **30 passed, 411 assertions**.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: **passed**.
- `php artisan view:cache`: **passed**.
- scoped `git diff --check`: **passed**.

## Artifacts

- `checkbox-inspector-44x24-header-1440.png`
- `checkbox-inspector-44x24-header-390.png`
- refreshed `forms-v3/screens/01-header-content.png` through `09-archive-shell.png`

## Runtime boundary

The authenticated manager page was not operated directly because the available browser QA session has no login credentials. No credentials, Apply, or Save action were used. Production CSS plus the exact modal/Header DOM chain were used for the computed-style proof.

## Graphify

Graphify was not run and graph data was not modified, following the explicit user instruction.

## Backups

- `project-artifacts/backups/20260905_182913-template-options-checkbox-44x24/`
- `project-artifacts/backups/20260905_183908-template-options-checkbox-44x24-screens/`
- `project-artifacts/backups/20260905_184036-template-options-checkbox-44x24-qa/`
