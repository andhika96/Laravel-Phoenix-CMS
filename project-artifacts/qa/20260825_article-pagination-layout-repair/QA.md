# Article pagination and loading layout repair QA

> Superseded for footer structure and UI by `../20260825_article-pagination-footer-ui/QA.md`. The negative-margin host described here was replaced with stable renderer slots inside `.article-shell`.

Date: 2026-08-25

## Corrected scope

The earlier Article QA did not inspect the end-of-list geometry. This repair specifically verifies pagination, total-result context, initial data loading, and next/previous-page loading placement.

## Root cause reproduced

At a 1920px desktop viewport, the final Article item ended at y=2294 while the Vue paginator began at y=2406. The paginator was rendered as a sibling after the renderer's `.article-shell`, which added the shell bottom padding plus paginator margin. The original data loader also used `position: fixed`.

## Final implementation

- Pagination is still Vue 3 CDN / `VuejsPaginateNext`, but uses a stable host aligned to the shared Article shell instead of a Vue Teleport target inside `v-html`.
- Each of the five archive templates now wraps its data region with `data-article-vue-list-content`.
- `loadingData` and `loadingNextPage` both activate the same Manage Article-style `text-center p-5` loader in a stable, shell-aligned host.
- While loading, the data region and pagination are hidden; the loader appears directly after the archive header rather than at the bottom of the viewport.
- The Vue paginator aligns directly after the final list item using the same 40px visual gap as the server-rendered pagination contract.

## Fresh browser evidence

### Desktop 1920px

- Initial loading: list content was hidden; one loader host was present at y=270–494, width 1180px, aligned with the Article shell.
- Settled archive: Vue paginator width 1180px exactly matched the shell; it began 40px after the final list item; SSR pagination was hidden.
- Next: URL advanced to `?page=2`, summary became `Showing 13–24 of 5027 Articles`, and no loader remained after completion.
- Previous: URL returned to `/article`, summary became `Showing 1–12 of 5027 Articles`, paginator gap remained 40px.
- Console: zero errors in a fresh QA tab.

### Mobile 390px

- Initial loader: y=231–455, width 358px, directly under the header area.
- Settled paginator: width 343px exactly matched the shell, with a 40px gap after the final Article; no horizontal overflow.
- Console: zero errors.

## All-template verification

- `ArticleTemplateRenderTest` renders all five archive templates and verifies their list content stays within `.article-shell` before SSR pagination: 5 tests / 85 assertions.
- `ArticleTemplatePreviewControllerTest` passed with curated preview data.
- The five covered archive renderers are Minimal Reading List, Editorial Journal, Mosaic Magazine, Mosaic Classic, and Balanced Card Grid.

## Automated checks

- `php artisan test tests/Feature/Article --testdox` — 19 passed, 244 assertions.
- Article Node contracts — 39 passed.
- `node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js` — passed.
- `php artisan view:clear` and `php artisan view:cache` — passed.
- `git diff --check` — passed; only existing CRLF warnings for two unrelated Manage Article Blade files.

## Evidence limitation

The browser screenshot capability intermittently returned `Unable to capture screenshot` during active loading, so placement evidence above is from fresh runtime DOM geometry and console output. The pre-repair screenshot remains at `01-before-layout-repair.jpg`; it is diagnostic evidence only, not final-state evidence.
