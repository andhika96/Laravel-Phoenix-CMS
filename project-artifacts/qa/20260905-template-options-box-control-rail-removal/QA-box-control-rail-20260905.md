# Template Options Padding/Margin rail removal — 2026-09-05

## Scope

Padding and Margin controls in Pagination and Archive Shell shared the `.article-template-box-control` component. That component still inherited an accent-colored left rail and left inset from the earlier form-group presentation. The requested correction removes both while preserving the horizontal divider and all responsive values.

## Implementation

- Shared `.article-template-box-control` override now uses `padding-left: 0` and `border-left: 0`.
- The fix applies to Padding and Margin in both Pagination and Archive Shell through the existing shared class; no duplicated panel-specific markup or behavior was added.
- Bottom divider remains `1px solid #edf0f3` for list hierarchy.
- Vue 3 CDN state, responsive device values, unit selects, linked four-side inputs, and preview behavior are unchanged.
- Graphify was not updated, following the user's explicit no-graph-update instruction.

## Browser evidence

Production-shaped fixture with Archive Shell-style Padding and Margin groups:

| Viewport | Padding | Margin | Divider | Overflow |
|---|---|---|---|---|
| 640 × 520 | left `24px`, width `592px`, padding-left `0px`, border-left `0px` | same | 1px `#edf0f3` | none |
| 390 × 844 | left `21px`, width `348px`, padding-left `0px`, border-left `0px` | same | 1px | none |

Screenshots:

- [box controls desktop](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-box-control-rail-removal-640.png)
- [box controls mobile](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-box-control-rail-removal-390.png)

The fixture console returned 0 errors and 0 warnings.

## Regression evidence

- `node --test tests/article-template-presentation.test.mjs` — 31 passed.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — 93 passed.
- `php artisan test --compact tests/Feature/Article` — 30 passed, 411 assertions.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` — passed.
- `php artisan view:cache` — passed.
- `git diff --check` for the scoped source, test, and plan files — passed.

## Runtime boundary

The authenticated Manage Article browser session is not available in this environment. No Apply, Save Template, or other write action was performed. Source contracts and the production-shaped fixture are verified; live authenticated click-through remains an external runtime boundary.
