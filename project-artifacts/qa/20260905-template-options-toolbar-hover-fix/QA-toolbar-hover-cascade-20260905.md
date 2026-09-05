# Template Options toolbar hover cascade fix — 2026-09-05

## Finding

The Search position group is rendered inside a Vue `<Teleport to="body">`. The accent token had only been declared on `.article-template-manager`, so it was not inherited by the teleported modal. When the center button entered `:hover`, the CSS declaration used an unresolved custom property: its background became transparent and its white text/border made the button appear missing. The remaining buttons then looked separated by a large blank gap.

## Fix

- `.article-template-options-modal` now declares `--article-template-accent: var(--ph-theme-primary, #6542d7)` so the teleported modal retains the CMS theme accent.
- The toolbar hover/focus rule continues to use the shared accent token and is scoped to Archive toolbar position buttons.
- Category dependent controls explicitly use `justify-content: start`, `justify-items: start`, left text alignment, and stretch within the settings column. This prevents the Position and Category filter style selects from drifting toward the center.
- Vue 3 CDN behavior and option state were not changed.
- Graphify was not updated, following the user's explicit no-graph-update instruction.

## Browser evidence

Production-shaped fixture with `--ph-theme-primary: #16a579` set on the document root, matching the teleported modal structure:

| Viewport | Button group | Hover result | Category controls | Overflow |
|---|---:|---|---|---|
| 640 × 520 | 592px total, 3 contiguous buttons at 198px each | Center button `rgb(22, 165, 121)` with white text/border | Wrapper starts at 24px; controls start at 38px and fill 578px | none |
| 390 × 844 | 348px total | Center button remains theme green | Wrapper starts at 21px; controls start at 35px | none |

Screenshots:

- [640px toolbar hover](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-toolbar-hover-fix-640.png)
- [390px toolbar hover](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-toolbar-hover-fix-390.png)

Both fixture console checks returned 0 errors and 0 warnings.

## Regression evidence

- `node --test tests/article-template-presentation.test.mjs` — 30 passed.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — 92 passed.
- `php artisan test --compact tests/Feature/Article` — 30 passed, 411 assertions.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` — passed.
- `php artisan view:cache` — passed.
- `git diff --check` for the scoped source, test, and plan files — passed.

## Runtime boundary

The authenticated Manage Article browser session is not available in this environment. No Apply, Save Template, or other write action was performed. The production-shaped teleported-modal fixture and source contracts are verified; live authenticated click-through remains an external runtime boundary.
