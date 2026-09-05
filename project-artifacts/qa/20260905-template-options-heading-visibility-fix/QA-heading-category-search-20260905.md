# Template Options heading, category rail, and Search visibility — 2026-09-05

## Scope

This continuation covers the latest requested corrections:

- Settings section titles use semantic `h5` elements instead of `strong`.
- The section heading-to-fields spacing is `1.8rem`.
- The Category filter dependent-select wrapper has no colored left rail or extra left padding, and both selects start at the left edge.
- Search position controls are rendered only while the Search toggle is enabled.

## Implementation

- `resources/views/manage_article/templates/index.blade.php`: all Template Options section headings now use `h5`; the toolbar control wrapper is guarded by `optionsModal.value.toolbar[field].enabled && (field !== 'category' || optionsModal.value.toolbar.category.enabled)`.
- `resources/views/manage_article/templates/partials/options-styling.blade.php`: Thumbnail, Pagination, Article title tag, and shell headings now use `h5` as well.
- `public/assets/css/article/article-template-manager-2026.css`: heading selectors target `h5`; the final section heading margin is `1.8rem`; Category alignment uses `start`, `padding-left: 0`, and `border-left: 0`.
- Existing Vue 3 CDN state and option persistence paths remain unchanged.
- Graphify was not updated, following the user's explicit no-graph-update instruction.

## Browser evidence

Production-shaped fixture with the teleported modal class and document theme accent:

| Viewport | Heading | Category wrapper | Category controls | Search hover | Overflow |
|---|---|---|---|---|---|
| 640 × 520 | `h5`, `margin-bottom: 28.8px` (`1.8rem`), title margin `0px` | left padding `0px`, left border `0px`, `justify-content: start` | both start at `24px`, width `592px` | theme green `rgb(22, 165, 121)` | none |
| 390 × 844 | same contract | left padding `0px`, left border `0px` | both start at `21px`, width `348px` | theme green `rgb(22, 165, 121)` | none |

Search OFF behavior was checked against the new conditional contract: the Search toggle becomes unchecked and the dependent group is hidden. The source regression test verifies the actual Vue `v-if` expression used by the manager.

Screenshot:

- [Heading, Category, and Search OFF fixture](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-heading-visibility-640.png)

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
