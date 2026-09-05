# Template Options four UI fixes — 2026-09-05

## Scope

This pass covers the four requested Template Options corrections:

1. Header Content description textareas now use ten rows.
2. Archive toolbar Search position buttons use the active CMS theme color on hover/focus.
3. Minimal Reading List Category filter Position and Category filter style controls are left-aligned and remain full-width.
4. List-style settings use consistent bottom dividers, including nested Categories and Popular Posts items in Reading list sidebar.

The existing Vue 3 CDN bindings and setting behavior were not changed.

## Implementation

- `resources/views/manage_article/templates/index.blade.php`: both Header Content description textarea variants use `rows="10"` (archive and custom detail content).
- `public/assets/css/article/article-template-manager-2026.css`: scoped toolbar hover/focus rule uses `var(--article-template-accent)`; category controls explicitly use left text alignment and stretch within the settings column; Header fields, option rows, nested sidebar options, and compound box controls share `--article-template-list-divider`.
- `project-artifacts/mockups/template-options-20260905/forms-v3/index.html`: V3 artifact mirrors the ten-row textarea, toolbar-only green hover, left category controls, and divider hierarchy.
- Nine affected desktop mockup screenshots were refreshed under `project-artifacts/mockups/template-options-20260905/forms-v3/screens/`.
- Graphify was not updated, following the user's explicit instruction to avoid graph updates during coding/design.

## Browser evidence

### Production-shaped CSS fixture

| Viewport | Description | Search hover | Category controls | Dividers | Overflow |
|---|---|---|---|---|---|
| 1440 × 900 | `rows=10`, height 254px | theme green when hovered | 417px wide, left aligned | Show sidebar, Categories, Popular Posts: 1px `#edf0f3` | none |
| 390 × 844 | `rows=10`, height 254px | theme green `rgb(31, 169, 123)` | 334px wide, left aligned with 14px nested inset | all three: 1px `#edf0f3` | none |

### V3 mockup

- Header state: textarea resolves to `rows=10`, 215px rendered height at 1440px, no horizontal overflow.
- Toolbar button-list state: hovered Search position button resolves to `rgb(22, 165, 121)` with white text; Position and Filter style controls start at the same left edge and are full-width.
- Sidebar state: both nested field cards resolve to a 1px bottom divider at desktop and mobile.
- Screenshots were visually inspected for Header, Toolbar, and Sidebar states after regeneration.

Evidence screenshots:

- [production-shaped fixture desktop](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-four-ui-fixes-1440.png)
- [production-shaped fixture mobile](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-four-ui-fixes-390.png)
- [mockup Header](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-four-ui-fixes/mockup-header-1440.png)
- [mockup Toolbar hover](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-four-ui-fixes/mockup-toolbar-hover-1440.png)
- [mockup Sidebar mobile](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/qa/20260905-template-options-four-ui-fixes/mockup-sidebar-390.png)

## Regression evidence

- `node --test tests/article-template-presentation.test.mjs` — 29 passed.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — 91 passed.
- `php artisan test --compact tests/Feature/Article` — 30 passed, 411 assertions.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` — passed.
- `php artisan view:cache` — passed.
- `git diff --check` for the scoped source, test, plan, and mockup files — passed.
- Playwright fixture console check — 0 errors, 0 warnings.

## Runtime boundary

The authenticated Manage Article browser session is not available in this environment. No Apply, Save Template, or other write action was executed. The source, production-shaped fixture, and V3 mockup are verified; live authenticated click-through remains an external runtime boundary.
