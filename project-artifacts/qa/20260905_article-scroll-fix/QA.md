# QA — Article frontend scroll lock fix

- Date: 2026-09-05
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Scope: public Article archive and detail vertical scrolling.
- Browser policy: live pages were inspected and scrolled read-only; no form submission, persistence, or account action was performed.

## Root cause

The active Arunika Lucent theme applied `height: 100vh` and `overflow: hidden` to `body`. The public frontend layout uses the Lucent body class but does not contain the admin `.ph-app-shell` internal scroller, so Article content extended beyond the viewport while the document body remained locked to the viewport height.

## Fix

Added a scoped CSS override in `public/assets/css/themes/arunika_lucent/arunika_lucent.css`:

```css
body.ph-theme-arunika-lucent:not(:has(.ph-app-shell)) {
    height: auto;
    min-height: 100%;
    overflow-y: auto;
}
```

The selector preserves the admin shell behavior because it does not match a Lucent body containing `.ph-app-shell`.

## Backup

- `project-artifacts/backups/20260905_article_scroll_fix/arunika_lucent.css.bak_20260905_011615_article_scroll`
- Source hash before patch: `84CD35F35231328F7D07CA8C2D0CA5CF73BE288183CEF956BB242D7FE2FB251D`
- Backup hash: `84CD35F35231328F7D07CA8C2D0CA5CF73BE288183CEF956BB242D7FE2FB251D`

## Regression test

- RED: `node --test tests/article-frontend-scroll-contract.test.mjs` failed because the scoped non-admin scroll rule was absent.
- GREEN: the same command passed after the CSS patch.
- Regression file: `tests/article-frontend-scroll-contract.test.mjs`

## Automated verification

| Check | Result |
| --- | --- |
| Article Node/template contracts plus scroll regression | Pass — 45 tests |
| `php artisan test tests/Feature/Article --testdox` with Laravel config cache bypassed for the process | Pass — 25 tests, 358 assertions |
| `node --check tests/article-frontend-scroll-contract.test.mjs` | Pass |
| `node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js` | Pass |
| PHP lint for Article controller and normalizer | Pass |
| `git diff --check` for changed scope | Pass; existing CRLF warning only |

## Live runtime evidence

Viewport: `1280x720`.

Before the patch:

- Archive: `body overflow:hidden`, `body height:720px`, document `scrollHeight:2468`, `scrollY` stayed at `0`.
- Detail: `body overflow:hidden`, `body height:720px`, document `scrollHeight:1340`, `scrollY` stayed at `0`.

After hard reload with the live CSS asset:

- Live CSS contained the new selector and declarations.
- Archive: `body overflow-y:auto`, `body height:2467.69px`, document `scrollHeight:2468`, `scrollY=600` after instant scroll.
- Detail: `body overflow-y:auto`, `body height:1340.16px`, document `scrollHeight:1340`, `scrollY=600` after instant scroll.
- Archive and detail console checks: 0 errors, 0 warnings.

## Scope and limitations

- Only `public/assets/css/themes/arunika_lucent/arunika_lucent.css` and the new regression test were changed by this task.
- Existing unrelated Arunika Lucent dirty changes were preserved; backup-vs-source comparison showed only the new scroll rule was added after the backup.
- The live admin shell was not opened because no authenticated browser session was available. Its `.ph-app-shell` boundary is protected by the selector and covered by the regression contract.
- Graphify was not updated because this was a one-rule CSS correction and no graph-relevant source relationship changed.
