# Shared CSS consolidation and auth fixes

Date: 17 November 2026, Asia/Jakarta. Project: `D:/Laragon/www/laravel-13-phoenix`. Branch: `main`.

## Delivered

The three shared stylesheets now have one editable source: `public/assets/css/shared/phoenix-shared.css` (225379 bytes). Its index identifies three START/END sections:

1. BOOTSTRAP UTILITIES, line 15: the current completed utility extension, including the intentional Bootstrap spacing compatibility rules.
2. PHOENIX COMPONENTS, line 16026: the current CMS/component styles.
3. RESPONSIVE TYPOGRAPHY, line 18097: the current SiteTypography/responsive rules.

Different layouts previously loaded different subsets of these styles, interleaved with their own theme styles. Loading the full concatenation everywhere would change that cascade. `app/Http/Controllers/Web/SharedStylesheetController.php` therefore serves each section through its existing whitelisted stylesheet URL. Existing layouts keep their original loading order and relative asset paths. The browser continues to load the sections used by that layout; this is consolidation of the editable source, not reduction to one HTTP request. Editing the canonical CSS requires no build step. Keep its START/END flags intact.

The route in `routes/web.php` is stateless (`withoutMiddleware('web')`), accepts only the three existing stylesheet names, sends CSS/nosniff/cache headers, and supports ETag/304 responses. It does not set session cookies. Deploy the controller and route together with the canonical CSS; a deployment using cached Laravel routes must refresh its route cache in the usual way. A separate static-only/CDN host would need equivalent section delivery.

Original stylesheet files were backed up and moved into this artifact directory's `retired-sources`; only `phoenix-shared.css` remains in the active shared CSS folder. All valid declarations and utility names were retained. Two already-ignored `!important;` tokens after a completed declaration in `.button-collapsed` and `.list-group-item` were removed. The declaration priorities were retained, and the browser's parsed CSSOM remains identical.

## Auth corrections

- Midnight Story: overlap the solid left mask layer by 1 CSS pixel. The prior exact join produced a dark fractional-pixel seam in 6/15 tested viewport/DPR combinations. After the fix all 15 combinations have no measured seam; the notch contour is preserved.
- Modern auth themes: reduce the gap between the submit control and the lower page links from 24 to 16 CSS pixels. This applies consistently to login/signup/forgot/reset.
- Shared auth JavaScript: replace the three loading and three success anchor fragments with one small `submitStateHTML` helper returning disabled native buttons. Preserve the custom button classes, response handling, redirects, recovery notice behavior and safe submit-label restoration. Loading exposes `aria-busy=true`; the fixed spinner/check markup is valid. The old success fragment had an unmatched closing `div`.
- Shared auth base CSS aligns the state label/icon using inline flex with a small gap and preserves the intended opacity. Normal/process/success controls retain their measured geometry.

The reported bottom spacing was a 24px link gap. The initial 96-case baseline did not show a change in button height between idle/loading/success. One apparent mobile position change was browser scrolling to reveal a long signup form; the final geometry test uses document coordinates so it does not confuse scrolling with a layout shift.

## Source and context inspected

- Primary project memory: `E:/AI/Memories/20261103_160100-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md`, particularly CSS consumer order and the independent auth boundary. Current source/runtime were verified instead of assuming that older snapshot remained current.
- C memory registry: shared CMS component/style conventions, `MEMORY.md` line 119. Current component selectors were confirmed in source.
- Graphify `explain phoenix-cms.css`: 19 EXTRACTED stylesheet consumers, verified against current layout source. `explain resources_views_auth_auth_layout_blade`: EXTRACTED auth-base dependency, confirmed in the active independent auth layout. The graph predates recent auth additions; it was navigation only. No INFERRED/AMBIGUOUS relation was used as proof.
- All three active shared CSS sources, active Bootstrap 5.3.6_custom CSS and the prior utility regression script.
- All six CMS/frontend/legacy-auth theme layouts via current Blade rendering: default, calm_green, arunika_aurora, arunika_equinox, arunika_prism, arunika_lucent. Article archive/detail/private/password views, template preview and Setup stylesheet references were checked.
- Independent `resources/views/auth/auth_layout.blade.php`, modern page/form/panel partials, all 32 active auth views via fresh Blade rendering, `auth-base.css`, `auth-modern.css`, auth Vue script, and the real Auth_Controller login/response flow.
- Existing theme typography, Lucent and Article presentation tests. Laravel asset-response/middleware behavior was checked in installed framework source.

Applied available Ponytail, Karpathy, Graphify, UI UX Pro Max and Design Taste guidance. Superpowers was not found in the configured skill/cache folders at this checkpoint; the inspect/reproduce/minimal-fix/verify workflow was continued with the available skills. No plugin/dependency was installed.

## Production/source changes

- Added `public/assets/css/shared/phoenix-shared.css`.
- Added `app/Http/Controllers/Web/SharedStylesheetController.php`.
- Updated `routes/web.php` with the whitelisted stateless stylesheet route.
- Retired the three original shared CSS files from their public source locations; their existing HTTP URLs remain functional.
- Updated `public/assets/css/auth/auth-base.css`, `auth-modern.css`, and `public/assets/js/vue3/auth/vueV3-auth-2026.js`.
- Updated the existing `tests/theme-responsive-typography-static.test.mjs` to read the canonical typography section.

New tests, scripts, fixtures, screenshots and backups are contained in this artifact directory, including `SharedStylesheetTest.php`. No Blade layout, package/build configuration, migration, theme selection or business data was changed for this task.

## Backups and preserved work

`backup-manifest.json` lists five initial scoped backups, including the three active CSS files, routes and the existing typography test. `auth-backup-manifest.json` lists the three auth source backups. Each original backup's SHA-256 was verified at completion (8/8). Intermediate changes have additional timestamped backups.

`final-state.json` confirms:

- Branch remains `main`; Git index hash unchanged; staging remains empty.
- The pre-existing modified `public/assets/js/themes/arunika_aurora/arunika_aurora.js` has the exact same hash as before this task.
- The unrelated sidebar-hover artifact directory was preserved.
- Auth and Site Config settings hashes are unchanged. Fixtures used only in-memory overrides; no signup/recovery/email/reset request reached the real backend during UI matrix testing.
- Both temporary public QA junctions were moved into this directory and the old URLs return 404. The archived links target only their corresponding rendered fixture directories.
- Graph data hash is unchanged. Graphify was not updated, following the user's manual-update instruction.

## Verification and current evidence

1. `consumer-parity.json`: **108 paired Chrome browser comparisons passed**: 18 active layouts (6 CMS + 6 frontend + 6 legacy auth) × 390/820/1440px × light/dark. Browser CSSOM and measured geometry/typography/colors/padding/display are identical between the original sources and the consolidated section responses. No page/resource errors. Cross-origin stylesheet rules cannot be read through CSSOM, so their unchanged URLs and resulting component styles were compared. Transitions were disabled only in fixtures.
2. `final-node-tests.log`: **61/61 Node tests passed**: 53 existing typography/Lucent/Article checks plus 8 utility regressions. Utility coverage includes all current breakpoints, offsets, declaration support, native/extended spacing precedence, negative margins, min sizes/cursors/dvh/clamping, and preservation of formerly valid class names.
3. `final-php-tests.log`: **2/2 PHP tests, 31 assertions passed**, covering all three section URLs, CSS headers, no Set-Cookie, ETag/304 and rejection of unknown selections. PHP syntax, JavaScript syntax and PostCSS parsing passed; `git diff --check` passed.
4. `section-equivalence.json` / `final-assets.log`: latest real HTTP section bodies match the original bytes except the two explicitly removed ignored tokens; consolidated parser, empty 304 bodies, no session cookies, unknown stylesheet 404 and closed QA URLs all passed.
5. `auth-states-before.json` and `auth-states-after.json`: **96 combinations before and after**, covering all 8 themes × 4 flows × 390/820/1440px. Final checks assert fixed document position/size for idle/loading/success, a 16px lower link gap on modern themes, disabled status buttons, loading aria state, no horizontal overflow, safe label restoration and existing CMS notices for validation/network/recovery success. The after run intercepted **312 requests**, with no real auth mutation requests and no page errors.
6. `panel-seam-before.json` / `panel-seam-after.json`: **15 pixel checks** across 820/1280/1422px and DPR 1/1.25/1.35/1.5/2. Visible seam cases changed from 6/15 to 0/15. Children were hidden only for uniform-background pixel sampling; the active mask and panel geometry were used.
7. `responsive-auth.json`: **72 additional checks passed**: all 32 views at 320/1920px plus the four Midnight flows with mobile panel above/below. No document/control overflow or page errors.
8. `auth-keyboard-final.json`: **5/5 pending-submit Enter checks passed**, using the final button implementation on default and all four modern forgot-password forms. One mocked request each, no duplicate submission or JavaScript error.
9. `live-login.json`: **real local admin login succeeded** with HTTP200 success and redirect to `/dashboard`. The dashboard loaded its shared stylesheet sections, independent auth loaded zero shared/CMS stylesheets, and no page errors occurred. Used an isolated headless browser session and ephemeral credentials; only the login POST was allowed. No CMS save/CRUD action was performed.
10. Real GET smoke: `/home`, `/article`, `/auth/login`, `/auth/signup`, `/auth/forgotpassword` returned HTTP200. Independent auth pages load zero shared stylesheets.

Screenshots: `auth-success-after.png`, `panel-before.png`, `panel-after.png`, `midnight_story-login-320-after.png`, `ocean_split-login-1920-after.png`.

## QA notes and limits

Early harness failures were investigated rather than accepted: Chrome occasionally reports zero used inline auto margins while preserving geometry and identical CSSOM (`auto-margin-debug.json`); comparison now uses box geometry and parsed styles. Cross-origin cssRules access is restricted by the browser. A transform guard caught a fragment-match mistake before writing source; the matcher was corrected and exactly 3 loading + 3 success replacements were verified. An early mobile y-coordinate assertion detected normal click-induced scrolling; document coordinates are used in the final check. None of these transient harness failures remain in the final test results.

The Blade fixture matrix runs current application templates/CSS/JS with intercepted responses, rather than real signup/email/password changes. Only real login and read-only GETs were exercised against the backend. Setup wizard, every CMS interaction, selectors injected from arbitrary saved content, non-Chrome browsers, physical devices and deployment/CDN behavior were not tested. No claim is made that every unrelated application bug has been eliminated.

All source/runtime work in the requested scope is complete. Graphify refresh remains manual and pending an explicit user command.
