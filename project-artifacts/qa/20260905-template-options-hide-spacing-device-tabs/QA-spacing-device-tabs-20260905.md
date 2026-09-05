# Template Options spacing device-tab removal — 2026-09-05

## Scope

The repeated Desktop, Tablet, and Mobile buttons were removed from every Padding and Margin form group in Pagination and Archive Shell. The global device selector in the Template preview header remains available, so responsive editing still follows the existing `optionsDevice` state.

## Implementation

- Removed `.article-template-device-tabs--compact` markup from the Pagination spacing loop and Archive Shell spacing loop.
- Removed the now-unused production CSS rule for that compact device-tab variant.
- Kept all `optionsDevice` references in `boxUnit`, `boxValue`, `boxMax`, `boxStep`, and four-side input handlers, preserving responsive data behavior.
- Updated the V3 mockup to hide the repeated `.form-group__devices` row inside spacing groups; the preview toolbar device buttons remain.
- Refreshed the Pagination and Archive Shell desktop screenshots.
- Graphify was not updated, following the user's explicit no-graph-update instruction.

## Browser evidence

V3 mockup at 1440 × 900:

- Pagination contains 2 spacing form groups and 0 visible `.form-group__devices` rows.
- Archive Shell contains the same spacing presentation without repeated device buttons.
- Settings column remains scrollable and has no horizontal overflow.
- Padding/Margin unit selectors, four-side inputs, and chain-link controls remain visible.

Screenshots:

- [Pagination without device tabs](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/template-options-20260905/forms-v3/screens/08-pagination.png)
- [Archive Shell without device tabs](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/template-options-20260905/forms-v3/screens/09-archive-shell.png)

The mockup console returned 0 errors and 0 warnings.

## Regression evidence

- `node --test tests/article-template-presentation.test.mjs` — 31 passed.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — 93 passed.
- `php artisan test --compact tests/Feature/Article` — 30 passed, 411 assertions.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` — passed.
- `php artisan view:cache` — passed.
- `git diff --check` for the scoped source, test, plan, and mockup files — passed.

## Runtime boundary

The authenticated Manage Article browser session is not available in this environment. No Apply, Save Template, or other write action was performed. Source contracts and the V3 mockup are verified; live authenticated click-through remains an external runtime boundary.
