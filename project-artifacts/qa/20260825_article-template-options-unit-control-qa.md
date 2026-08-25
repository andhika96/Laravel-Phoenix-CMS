# QA — Article Template Options: Unit Control + Archive/Detail Styling

- Date: 2026-08-25
- Scope: structured Article Template Options for archive/detail, Vue CDN modal, Coloris, renderer parity, responsive footer.
- Browser policy: read-only; no Save Template, Apply changes, or login submission was performed.

## Automated verification

| Check | Result |
| --- | --- |
| `php artisan test tests/Feature/Article --testdox` | Pass — 25 tests, 358 assertions |
| Article Node contracts | Pass — 44 tests |
| PHP lint affected files | Pass |
| `node --check` affected Vue CDN files | Pass |
| `php artisan view:clear; php artisan view:cache` | Pass |
| `git diff --check` | Pass; only pre-existing CRLF warnings from unrelated Article form files |
| Native `type="color"` inputs in Template Options | None found |

## Browser QA — public Article

| Surface | Evidence | Result |
| --- | --- | --- |
| Desktop archive | Vue hydrated; current default H4 list title; background thumbnail `cover`; Vue footer nested in stable shell slot; console/network error log empty | Pass |
| Next page | Click page 2 through Vue pager; URL became `/article?page=2`; first record changed; footer remained inside shell; no error | Pass |
| Tablet `834×1112` | No horizontal overflow; two-column archive grid; shell width 787px; console/network error log empty | Pass |
| Mobile `390×844` | No horizontal overflow; single-column grid; shell width 343px; total-data block above centered pager | Pass |
| Desktop detail | Knowledge + TOC detail shell rendered with cover `object-fit: cover`; no horizontal overflow; console/network error log empty | Pass |
| Mobile detail `390×844` | Single-column detail shell and TOC order retained; no horizontal overflow | Pass |

## Manager modal boundary

The in-app browser redirected `/manage_article/templates` to `/auth/login`. No credentials were entered and no persistence action was performed. Modal behavior, Coloris loading/configuration, unit allowlists, linked/unlinked behavior, normalization, preview query payload, and save persistence are covered by the Article Feature/Node tests above and Blade cache compilation.

## Review fixes included before final verification

1. Removed the preview-only default shell padding override so preview and public renderer use the same default shell spacing.
2. Changed Vue pagination chevrons from Font Awesome Regular to Solid; browser DOM now renders the expected Solid SVG chevrons.

## Known non-scope environment issue

`php artisan route:list --name=cms.core.article` is blocked by the pre-existing missing `App\Http\Controllers\Api\v1\Testing\Testing_Controller`. Article route behavior is independently covered by the passing `ArticleFrontendRouteTest` suite.

