# Article CMS typography and frontend link — 2026-10-18

Supersedes the fixed-pixel typography described in IMPLEMENTATION-QA.md. The original inline concept remains a historical mockup, not the current application.

## Changes
- All archive/detail H1–H6 now follow the current shared CMS `--ph-global-rfs-h*` matrix.
- Article base text follows Site Config via `--ph-adaptive-font-size`, with the existing CMS mobile content token at mobile widths. Focused Reader summary/body no longer use independent pixel sizes.
- Focused Reader pixel-size controls are hidden; existing stored values are retained for compatibility but do not override CMS typography. Other layout, spacing, line-height and cover options remain active.
- View frontend link added beside preview information. Archive opens `/article`; detail opens the latest eligible published public article. Excludes private, password-protected, draft and scheduled articles. Empty catalog shows a message instead of an invalid link.
- Link opens a new tab with noopener/noreferrer. It uses saved frontend settings, not unsaved fixture data.

## Files and backups
Changed in this follow-up: ManageArticleTemplateController.php, index.blade.php, vueV3-manage-article-templates-2026.js, article-frontend-2026.css, options-focused-reader.blade.php.
Read supporting sources: PublicArticleQuery.php, ArticleFrontendController.php, SiteTypography.php, theme-responsive-typography.css, frontend layout, article detail layout, manager styles and existing tests.
Backup folder: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20261018-reader-frontend-link-194342`.

## Verified
- Authenticated manager loaded successfully. Focused Reader settings show CMS typography notice; old pixel font controls absent.
- Actual View frontend click opened public detail in a new tab. Current saved template is Knowledge + TOC; preserved without live Save.
- 32 live authenticated preview checks: 8 templates × widths 320/390/834/1440. H1 matches CMS token; no horizontal page overflow.
- 6 live public checks: archive and current detail × widths 390/834/1440. No page overflow.
- Current Site Config 14px: Focused Reader H1 24/28/28px and body 13.45/14/13px at widths 390/834/1440, height 900. These are CMS-computed values, not new hardcoded defaults.
- Article PHP suite: 43 passed, 632 assertions. Frontend link suite: 5 passed, 92 assertions (4 inherited preview regressions plus one link eligibility/empty-state test). JS suites: 76 passed.
- `verify-cms-font.cjs` checks heading parity, Site Config changes, and legacy pixel override resistance.
- PHP/JS syntax, Blade cache, diff whitespace checks passed.

## Context and limits
E: drive unavailable; relevant C: memory used for established CMS settings patterns. Graphify query `ManageArticleTemplateController PublicArticleQuery` supplied code locations; source was verified directly. Graph not updated per instruction.
No credentials, session state or cookies saved in artifacts. No live template defaults or Site Config settings changed. No commit/push.
Earlier `verify-runtime.cjs` asserts the superseded pixel font contract and must not be treated as the current font check. Current evidence is `cms-font-live-verification.json` and `cms-font-regression.json`.
