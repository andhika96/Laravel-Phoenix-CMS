# QA — Article Templates frontend UI/UX audit

- Date: 2026-09-05
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Scope: five archive templates, three detail templates, responsive typography, CMS-theme CSS interference, public frontend versus Manage Article Templates preview.
- Browser policy: public pages were hard-reloaded and inspected read-only. No Template Manager Save/Apply, settings mutation, login submission, or external write was performed.

## UI/UX rules applied

The local `ui-ux-pro-max` searches were used for active-state clarity, focus appearance, readable responsive font size, line height, and consistent type scale. The relevant checks were:

- Active state must be visibly distinguishable and expose current state.
- Focus indicators should remain visible and have sufficient contrast.
- Body text should remain readable on mobile; the local guidance recommends 16px minimum for mobile body text.
- Body line-height should remain approximately 1.5–1.75.
- Responsive layouts must not introduce horizontal scrolling.

## Confirmed public/preview architecture

- `ArticleFrontendController::archiveContext()` and `renderDetail()` select views through `ArticleTemplateCatalog` and pass normalized options.
- `ManageArticleTemplateController::preview()` selects the same catalog view and passes the same `ArticleTemplateOptions` output to `manage_article.templates.preview`.
- Public archive/detail and preview load `article-frontend-2026.css` and `theme-responsive-typography.css`.
- Existing preview captures for all eight templates were reviewed from `project-artifacts/qa/20260825_article-template-audit/` and compared with the active public surfaces.
- Current unauthenticated requests to `/manage_article/templates` and direct preview URLs return `302` to login, so no new authenticated interactive preview evidence was collected in this audit.

## Per-template audit

| Template | Layout/responsive result | Preview parity | Verdict and remaining note |
| --- | --- | --- | --- |
| Minimal Reading List | Row list, 96px mobile media, stacked search, clamped title/excerpt, no horizontal overflow | Shared renderer and CSS contract | Good after theme isolation; body/mobile type remains compact |
| Editorial Journal | Lead story plus responsive card grid; lead stacks cleanly on mobile | Shared renderer and CSS contract | Good; no template-specific blocker found |
| Mosaic Magazine | Feature media plus dark content panel; mobile stacks feature and cards | Shared renderer and CSS contract | Good; title link now preserves light text on dark panel |
| Mosaic Classic | Lead/sidebar desktop layout; single-column mobile fallback | Shared renderer and CSS contract | Partial; mobile lead image remains 390px tall and feels too heavy compared with the 16:9 alternatives |
| Balanced Card Grid | Equal cards, 3 desktop / 2 tablet / 1 mobile by default; named category select | Shared renderer and CSS contract | Partial against the board; approved board shows a denser 2-column desktop composition |
| Focused Reader | Narrow centered reading shell, 16:9 cover, long-form content, stacked navigation on mobile | Shared renderer and CSS contract | Good; body/mobile type remains compact |
| Editorial Feature | Cover-led hero with overlay, responsive full-width mobile hero, reading shell below | Shared renderer and CSS contract | Good functionally; exact board composition/CTA treatment remains partial |
| Knowledge + TOC | Desktop sticky TOC rail; TOC moves above content on tablet/mobile; 16:9 cover | Shared renderer and CSS contract | Good functionally; board right-rail CTA treatment is not implemented |

## Responsive typography audit

Responsive typography is implemented, but not every Article text role uses the same responsive layer:

- `theme-responsive-typography.css` is loaded by archive, detail, and preview.
- Article headings use `--ph-fmv2-rfs-h1`, `--ph-fmv2-rfs-h2`, `--ph-fmv2-rfs-h3`, and `--ph-fmv2-rfs-h4` through Article-specific variables.
- Archive titles use `--article-list-title-size` and responsive breakpoint caps.
- Detail H1/dek and rich-content H2/H3 respond at desktop, tablet, and mobile breakpoints.
- Article body copy uses `--article-font-size` from `--ph-adaptive-font-size`, but does not inherit the shared `--ph-mobile-content-font-size` because that variable is currently scoped to `.ph-scrollable-content`.

Fresh live measurements with Site Config `14px`:

| Surface | 1280x720 | 834x1112 | 390x844 / 375x800 |
| --- | --- | --- | --- |
| Archive body | 13px | 14px | 14px |
| Archive item title | 14.82px | 15.4px | 14.7px |
| Archive excerpt | 13px | 14px | 14px |
| Detail H1 | 21.06px | 19.6px | 18.2px |
| Detail dek | 15.86px | 15.12px | 14.7px |
| Detail rich-content paragraph | 13px | 14px | 14px |

Result: heading/dek RFS is confirmed, but body copy is below the local 16px mobile recommendation. Raising Article body text would override the current Site Config/density contract and needs an explicit design decision, so it was not changed in this pass.

## CSS theme interference found and corrected

Arunika Lucent applies a global rule equivalent to:

```css
.ph-theme-arunika-lucent a:not(.btn) {
    color: var(--ph-lucent-accent) !important;
}
```

That rule overrode Article's intended hierarchy:

- active pagination text became the same green as its green background;
- non-active pagination numbers and navigation controls were tinted green;
- archive title links became green instead of inheriting the dark card/title color;
- detail TOC and previous/next links were also tinted green.

The fix is isolated in `public/assets/css/article/article-frontend-2026.css`; the CMS theme stylesheet was not broadened or rewritten. Article-specific selectors now preserve dark/inherited link colors, retain the Article accent for intentional CTA/back links, and keep active pagination text white.

The active page now renders live as:

```text
text: 2
color: rgb(255, 255, 255)
background: rgb(31, 166, 117)
```

The active page also receives `aria-current="page"` after Vue hydration through `updated()` and `syncPaginationAccessibility()`.

Contrast note: white on the current Lucent green `#1FA675` measures approximately `3.10:1`, so it fixes the original green-on-green legibility defect but remains below the 4.5:1 target for normal text. The current pagination control is `37.6px` square; moving it to the recommended 44px touch target requires a separate mobile width/layout check.

## Accessibility correction

The live archive configuration had its visual header title disabled, resulting in zero H1 elements. The shared archive header now emits a visually-hidden `Articles` H1 when the visual title is disabled or empty. This preserves the user's visual setting while maintaining a meaningful document heading.

## Automated verification

| Check | Result |
| --- | --- |
| Active pagination color regression | Pass; RED before patch, GREEN after patch |
| CMS-theme link isolation regression | Pass; RED before patch, GREEN after patch |
| Archive H1 fallback regression | Pass; RED before patch, GREEN after patch |
| Vue pagination `aria-current` regression | Pass; RED before lifecycle fix, GREEN after fix |
| Article/template Node contracts plus new audit regressions | Pass — 49 tests, 0 failures |
| `php artisan test tests/Feature/Article --testdox` with Laravel test config cache bypassed | Pass — 25 tests, 358 assertions |
| PHP lint and Node syntax checks | Pass |
| `git diff --check` | Pass; existing CRLF warning only |

## Live runtime verification

- Archive `?page=2` hard-reloaded at `1280x720`, `390x844`, and `375x800`.
- Detail `load-test-20260825-00001` hard-reloaded at `1280x720`, `390x844`, and `375x800`.
- Archive and detail remained vertically scrollable with `body overflow-y: auto`.
- `document.documentElement.scrollWidth` equaled `clientWidth` at tested viewports.
- Archive active page text was white on the green active background.
- Archive title link was dark instead of theme-green.
- Detail TOC links were muted gray and previous/next navigation text was dark.
- Archive contained one visually-hidden H1 when the visual header title was disabled.
- Console errors and warnings: 0 on the tested archive/detail reloads.

## Backups

- `project-artifacts/backups/20260905_article_uiux_audit/article-frontend-2026.css.bak_20260905_012749_active-pagination-color`
- `project-artifacts/backups/20260905_article_uiux_audit/article-frontend-2026.css.bak_20260905_013208_theme-link-isolation`
- `project-artifacts/backups/20260905_article_uiux_audit/archive-header.blade.php.bak_20260905_013811_accessible-h1`
- `project-artifacts/backups/20260905_article_uiux_audit/vueV3-article-frontend-2026.js.bak_20260905_013941_pagination-a11y`
- `project-artifacts/backups/20260905_article_uiux_audit/vueV3-article-frontend-2026.js.bak_20260905_014129_pagination-a11y-lifecycle`

## Deliberate backlog

- Decide whether Article body copy should use a 16px mobile minimum or preserve the current Site Config density contract.
- Decide whether pagination controls should grow from the current 37.6px to a 44px touch target and use a darker active background to meet 4.5:1 contrast; mobile pagination width must be rechecked if changed.
- Add `loading="lazy"` and/or responsive `srcset`/`sizes` for below-the-fold archive and previous/next thumbnails; current aspect-ratio reserves layout space but image delivery is not lazy/responsive.
- Decide whether to bring Mosaic Classic mobile lead media to a 16:9 or shorter aspect ratio.
- Decide whether Balanced Card Grid should default to the board's 2-column desktop composition.
- Decide whether to implement the board's contained detail panels and Knowledge + TOC right-rail CTA.
