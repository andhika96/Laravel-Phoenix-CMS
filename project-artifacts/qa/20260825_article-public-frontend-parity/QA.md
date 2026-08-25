# Public Article Frontend Parity QA

Date: 2026-08-25  
Scope: public `/article` archive/detail access, protected `listdata`, and live-preview parity.

## Route contract

- `GET /article` is public and returns `200` without cookies.
- `GET /article/{idOrSlug}` is public and returns only currently eligible Articles.
- `GET /article/listdata` remains protected by `auth`, `checkSuspended`, and `permission:read data`; a guest receives `302` to `/auth/login`.
- The static `/listdata` route remains before `/{idOrSlug}`.

## Automated verification

- `php artisan test tests/Feature/Article --testdox`: 15 passed, 185 assertions.
- Node Article and template-manager contracts: 29 passed, 0 failed.
- PHP lint for `routes/web.php` and `ArticleFrontendRouteTest.php`, Blade view cache, and `git diff --check`: passed.
- The focused route test proves guest archive/detail access; hides private, draft, and future content; proves suspended access is forbidden; and locks the three `listdata` middleware entries.
- Independent code review found no route conflict or renderer duplication. Its two coverage findings (suspended middleware attachment and draft/private eligibility) were added before the final suite run.

## Browser evidence

1. `01-public-article-desktop.jpg` — public archive with theme header and real published data.
2. `02-live-preview-minimal-desktop.jpg` — corresponding Minimal Reading List fixture preview.
3. `03-public-article-detail.jpg` — public Focused Reader detail.
4. `04-live-preview-detail.jpg` — corresponding Focused Reader fixture preview.

Observed parity on both archive and detail:

- Identical template class (`article-page--reading-list` / `article-detail--focused-reader`), heading font family, computed H1 `29.375px`, and `--ph-adaptive-font-size` value.
- Intentional differences only: frontend theme header, published data and thumbnails, and preview-only sample-content marker.
- Public Search navigated through SSR to `/article?search=Load+Test+Technology`; opening a result rendered the public detail page and Back/neighbor navigation.
- Console logs were empty for the public page and preview.

## Limits

- Browser evidence used the available desktop viewport. Tablet/mobile parity is covered by the shared responsive CSS and existing Node contract tests; this browser surface exposes no viewport-resize control.
- `php artisan route:list` cannot inspect any route because an unrelated legacy controller class is missing. Targeted router introspection verified the three Article route middleware stacks instead.
