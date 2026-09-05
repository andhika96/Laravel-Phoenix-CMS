# Lucent auth fix and mobile theme previews

Date: 2026-09-03. Project: laravel-13-phoenix.

## Implemented
- Changed only production CSS `public/assets/css/themes/arunika_lucent/arunika_lucent.css`: compatibility aliases for legacy Phoenix button tokens and default light tokens on `:root` when auth HTML has no `data-bs-theme`.
- Extended `tests/arunika-lucent-theme-static.test.mjs`. RED: 5 passed / 1 failed on missing primary alias. GREEN: 6/6, related suite 25/25.
- No auth logic, credentials, theme database definitions, or chart code changed.
- Backup: `project-artifacts/backups/20260903_051235_arunika-lucent-auth-button-notice/`; original CSS/test SHA-256 verified when copied. The draft preview HTML also has a recovery copy there.

## Fresh browser evidence
- Served guest login through a temporary PHP server at 127.0.0.1:8766 using the same project; preserved the separate authenticated admin session.
- Login computed background/border: rgb(31, 166, 117); text rgb(255, 255, 255).
- Empty-form validation produced the real Notice: Email address required / Password required.
- Notice computed background rgb(255, 255, 255), text rgb(0, 0, 0), error stripe rgb(220, 53, 69).
- Screenshot: `project-artifacts/mockups/arunika-mobile-theme-previews-20260903/login-notice-fixed.png`.
- Credential login success, Firefox-specific rendering, and all auth variants were not rerun in this verification. The empty form verification does not create an account or authenticate a user.

## Mobile previews
Folder: `project-artifacts/mockups/arunika-mobile-theme-previews-20260903/`.
- Five boards: preview-mosaic.png, preview-aurora.png, preview-prism.png, preview-equinox.png, preview-lucent.png.
- Each board shows actual 400x844 dashboard captures with sidebar closed/open, header controls, account/menu layout, and explanatory captions. Source pairs are retained at full resolution.
- index.html is the local browsable gallery; no external scripts or CDN.
- Mosaic was captured in its existing dark state; the other themes in their existing light state. These are runtime references, not a new responsive redesign or a full light/dark matrix.
- Earlier incorrectly scaled capture drafts were overwritten by fresh verified images; screenshot tooling required a fresh viewport capability and no clip/stitching for final boards.
- Existing dashboard chart overlap remains intentionally untouched and visible in the previews.
- Active theme was verified back at Arunika Lucent (`aria-checked=true`) after captures. Temporary viewport override reset.

## Checks and boundaries
- Node: Lucent static, concept rename, cool-gray contrast suites 25/25.
- `php artisan view:cache` passed; served CSS contains aliases and root fallback.
- `git diff --check` passed (line-ending warning only).
- Memory search found no dedicated Lucent auth incident. Graphify auth/login and dashboard queries were orientation only; actual source/served HTML confirmed the findings.
- No graph update for the small CSS token fix (no structural dependency change); no commit or push.
