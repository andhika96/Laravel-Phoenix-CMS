# QA - Template Options Revamp

- Project: Laravel 13 Phoenix
- Date: 2026-09-05
- Plan: `project-artifacts/plans/20260905-template-options-revamp-plan.md`
- Status: implemented in the existing dirty worktree; no commit, staging, push, or broad cleanup performed.

## Scope and design source

The approved visual reference was:

`project-artifacts/mockups/template-options-20260905/template-options-concept-03-modal-radius.png`

The implementation used the required `ui-ux-pro-max` skill at `C:\Users\aruna\.agents\skills\ui-ux-pro-max\SKILL.md`. Focused searches covered keyboard focus/modal settings, responsive settings modal overflow, and Laravel implementation guidance. The applied guidance was:

- visible focus rings and complete keyboard operation for modal controls;
- vertical tab navigation with selected state and roving tabindex;
- 44px-class touch targets and native controls where appropriate;
- independent scroll regions without horizontal overflow;
- mobile-first breakpoints at the plan's 1279px and 767px boundaries;
- theme-token-driven accent/surface styling and reduced-motion handling;
- non-blocking loading, timeout/error feedback, Retry, and auth-redirect detection.

## Planning and source inspection

- Read the complete implementation plan before editing.
- Read the relevant memory handoff `E:\AI\Memories\20260825_232054_laravel13_phoenix_manage_event_article_template_options_handoff.md`, including the Vue 3 CDN, normalizer, preview, and pagination contract.
- Queried local Graphify for `ArticleTemplateOptions`, `ManageArticleTemplateController`, Minimal Reading List, manager Vue, preview, and CSS relationships. Graph results were treated as a map and verified against active source.
- Inspected the active manager Blade, `options-styling.blade.php`, manager CSS/JavaScript, preview controller, preview Blade, Article Template Options normalizer, and existing manager/presentation tests.
- Confirmed the worktree was already dirty with user changes. Work continued in place as explicitly announced, with per-file backup and scoped edits to avoid overwriting unrelated changes.

## Implementation

- Reorganized Template Options into one Bootstrap modal with a header, persistent vertical Customize navigation, settings panel, independent preview panel, and footer.
- Menu items are derived from the active surface/template option shape. Minimal-only Post list and Reading list sidebar entries and grid-only Grid columns are not shown for unsupported templates.
- Header content is the default panel on every new modal session.
- Added tab semantics: `role=tablist`, `role=tab`, `aria-selected`, `aria-controls`, `role=tabpanel`, `aria-labelledby`, roving `tabindex`, Arrow/Home/End navigation, and visible focus rings.
- Added mobile category select and Settings/Preview switch for narrower viewports. Desktop keeps the three-region modal; tablet uses a two-region settings/preview switch; mobile shows one region at a time.
- Preserved all existing Header, Archive toolbar, Post list, Reading list sidebar, Grid columns, Thumbnail, Pagination, Article title, Archive shell, and Detail shell bindings and conditionals.
- Added isolated modal preview state. Preview URL uses the modal clone, not the page draft; edits are debounced at 350ms; page Save is not called by preview watchers.
- Reused the existing preview endpoint, normalizer, fixtures, theme-color query, and device profiles: Desktop 1440x900, Tablet 834x1112, Mobile 390x844.
- Added fixed virtual iframe sizing with fit-to-panel scaling, a non-blocking loading state, 8-second timeout, network error handling, Retry, and auth/login redirect detection so a login page is not treated as a successful preview.
- Added last-request sequence/timeout guards and clears debounce/timeout timers on hidden/unmount. The modal ResizeObserver is created only after `shown.bs.modal` and disconnected on hide/unmount.
- Added static backdrop and disabled Bootstrap keyboard dismissal so dirty Cancel/X/Escape flows through the inline Keep editing / Discard changes confirmation.
- Apply copies the complete modal option clone into the page draft and refreshes the existing page preview. Cancel, X, Escape, and Discard leave the page draft unchanged. Save Template remains the only persistence action.
- Coloris remains the local implementation, is initialized after a relevant panel appears, and its picker z-index is raised above the modal. Four-side link/unlink controls retain their behavior and now expose `aria-label` values.
- Modal CSS keeps the CMS radius/token approach and adds responsive shell sizing, independent scroll regions, mobile overflow protection, and `prefers-reduced-motion` behavior.

## Files modified for the plan

- `resources/views/manage_article/templates/index.blade.php`
- `resources/views/manage_article/templates/partials/options-styling.blade.php`
- `public/assets/css/article/article-template-manager-2026.css`
- `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`
- `tests/manage-article-template-manager.test.mjs`
- `tests/article-template-presentation.test.mjs`

Read/reused without modification for this plan:

- `app/Support/Article/ArticleTemplateOptions.php`
- `app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php`
- `resources/views/manage_article/templates/preview.blade.php`
- `tests/article-template-preview-fixture.test.mjs`

The earlier Minimal Reading List archive/frontend changes in the same dirty worktree remain intact, including `post_list.item_gap`, category modes, async category filtering, pagination, sidebar positions, and the removal of the `Latest Articles` summary row.

## Backup

Backups were created before plan edits under:

`project-artifacts/backups/20260905_055037-template-options-revamp/`

The backup preserves the original relative paths for ten target/reused files. `MANIFEST.sha256` contains their SHA-256 values and was verified successfully: **10 entries, 0 mismatches**.

## Automated verification

Baseline before plan source edits, on the pre-existing dirty worktree:

- Node manager/presentation/preview fixture suite: **30 passed, 0 failed**.
- PHP `tests/Feature/Article`: **28 passed, 1 failed, 399 assertions**. The known failure was the password-unlock test receiving 419 CSRF instead of expected 422.

Final after plan source edits:

- Node plan suite (`manage-article-template-manager.test.mjs`, `article-template-presentation.test.mjs`, `article-template-preview-fixture.test.mjs`): **40 passed, 0 failed**.
- Additional Minimal Reading List regression suite remains green: **13 Node tests passed** and **22 PHP tests passed with 321 assertions** before this plan's manager-only additions.
- PHP full `tests/Feature/Article` at the final parse-fix verification: **29 passed, 408 assertions, 0 failed**. An earlier run in the same session showed the known transient 419-vs-422 CSRF failure; the final fresh run completed green.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: passed.
- `php -l app/Support/Article/ArticleTemplateOptions.php`: passed.
- `php artisan view:cache`: passed.
- `git diff --check`: passed; only pre-existing CRLF/LF warnings for unrelated dirty files were emitted.

## Runtime QA

### Manager route

Read-only browser navigation to `https://laravel-13-phoenix.aruna/manage_article/templates` redirected to `https://laravel-13-phoenix.aruna/auth/login`. No credentials were entered and no live manager setting was changed. Therefore the following remain unverified at live manager runtime: computed modal radius/shadow, actual Coloris popup layering, keyboard focus trap/return, modal interactions, and responsive screenshots at 1920/1440/1280/1024/768/390/320px and 200% zoom.

Those boundaries are covered statically and by the Node method/markup tests, but are not represented as live-browser success claims.

### Public frontend smoke check

After a fresh public archive load at `https://laravel-13-phoenix.aruna/article`:

- article rows: `12`;
- `Latest Articles` text count: `0`;
- `.article-reading-list__section-heading` count: `0`;
- category links: `9` in the current dataset (All + eight categories);
- Vue pagination visible: `true`;
- document height: `2896px` vs viewport `720px`;
- body overflow-y: `auto`, page remains scrollable;
- browser console warnings/errors: none.

The public smoke check confirms the manager-only revamp did not regress the previously fixed archive frontend.

## Graphify

Graphify was rebuilt incrementally after the final plan source changes:

- `21,721 nodes`
- `37,612 edges`
- `1,562 communities`
- `graphify-out/graph.json` and `graphify-out/GRAPH_REPORT.md` updated.
- `graph.html` skipped because the graph exceeds the 5,000-node visualization limit.
- 58 metadata/generated files produced zero nodes and were omitted; the warning did not block the rebuild.
- Final targeted query confirmed the `ManageArticleTemplateVue3` node and the manager/template/preview source neighborhood. Individual helper methods are not all emitted as graph nodes, so source/tests remain the final evidence.

## Outstanding boundaries

- Live manager modal interaction and computed-style verification require an authenticated browser session; no credentials were requested or stored.
- Full Article suite still has the pre-existing 419-vs-422 CSRF failure described above.
- Persistence through the live Save Template button was intentionally not exercised; backend persistence behavior remains covered by existing feature tests.
- No schema, migration, Page Builder, global theme, or public article template redesign was added by this plan.
- No commit, staging, push, reset, checkout, or cleanup of unrelated user changes was performed.

## ParseError follow-up

After the initial revamp implementation, the authenticated manager screenshot showed:

`syntax error, unexpected end of file, expecting "elseif" or "else" or "endif"`

Root cause was confirmed by compiling the exact active Blade view with PHP 8.5.6. The Vue iframe attribute `@error="onModalPreviewError"` was interpreted by Blade as Laravel's `@error` validation directive, opening a Blade conditional that had no closing directive. The generated compiled view contained `__errorArgs` and failed `php -l` at EOF.

Fix:

- Changed only the Vue event binding to `v-on:error="onModalPreviewError"` in `resources/views/manage_article/templates/index.blade.php`.
- Added a static regression assertion that the unsafe `@error="onModalPreviewError"` pattern is absent and `v-on:error` is present.
- Cleared and rebuilt views with PHP 8.5.6 (`artisan optimize:clear`, `artisan view:cache`).
- Recompiled the exact view and verified the generated file with `php -l`: **No syntax errors detected**.
- Verified compiled output contains `v-on:error` and no `__errorArgs` / Blade error directive conversion.
- Fresh browser smoke navigation to `/manage_article/templates` now reaches the expected `/auth/login` boundary; no ParseError page appeared. Authenticated modal interaction remains unverified because no credentials were entered.

Parse-fix backups and SHA-256 evidence are under:

`project-artifacts/backups/20260905_063116-template-options-parse-fix/`

The pre-update QA file is backed up there as `QA.md` with SHA-256 `1A9A544B87EFE6A5038AFAFF1028D02E4A02CE90A0D5313D8019DA72F2BABD5C`.

## Undefined `copy` follow-up

The next runtime error was:

`Undefined constant "copy"` at `index.blade.php:103`.

Root cause was the same Blade/Vue boundary in a different form: the new modal preview loading label was written as `{{ copy.loadingPreview }}` instead of escaped Vue syntax. Blade compiled it as PHP `copy.loadingPreview`, producing the undefined constant error.

Fix:

- Changed it to `@{{ copy.loadingPreview }}` in `resources/views/manage_article/templates/index.blade.php`.
- Added a regression assertion requiring the escaped Vue interpolation and rejecting an unescaped `copy.loadingPreview` expression.
- Recompiled with PHP 8.5.6 and verified `php -l storage/framework/views/d5f93f510492f2fe45a51b87319979fb.php`: **No syntax errors detected**.
- Compiled output now keeps `{{ copy.loadingPreview }}` as Vue text and does not emit an undefined PHP constant.
- Manager Node regression test: **25 passed, 0 failed** after this focused fix.

Backup for this follow-up is under:

`project-artifacts/backups/20260905_111240-template-options-undefined-copy-fix/`

## Sidebar dependent-control ordering

The Reading list sidebar form was corrected after visual review. Categories and Popular Posts are now separate dependent-control groups:

- Categories toggle
- Categories position select directly below it
- Popular Posts toggle
- Popular Posts position select directly below it

The previous side-by-side toggle row and separate position row were removed. A regression assertion checks the DOM order for both pairs. The final combined Node suite is **49 passed, 0 failed**, PHP Article is **29 passed, 408 assertions**, and Blade cache/diff checks remain green.

The sidebar-order backup is under:

`project-artifacts/backups/20260905_113501-template-options-sidebar-dependent-order/`

## Template Options switch sizing follow-up

The Template Options switches were enlarged to match the approved visual direction without changing CMS-wide switches:

- scoped width: `3.5rem`;
- scoped height: `1.75rem`;
- scoped minimum width: `3.5rem`;
- thumb/background scale: `1.25rem`;
- scope: `.article-template-options-panel .form-switch .form-check-input` only.

The values remain rem-based and are exposed as modal-local CSS tokens. Labels, keyboard/native switch behavior, right alignment, and the existing theme accent are preserved.

Verification after this follow-up:

- focused presentation contract: **15 passed, 0 failed**;
- combined manager/presentation/preview/sidebar Node suite: **50 passed, 0 failed**;
- Laravel Article Feature suite: **29 passed, 408 assertions**;
- `php artisan view:cache`: passed;
- `git diff --check`: passed; only existing CRLF normalization warnings were reported;
- live manager computed-style verification: **not available** because the read-only browser session redirected to `/auth/login` and no credentials were entered.

Switch-size backup and SHA-256 evidence are under:

`project-artifacts/backups/20260905_114200-template-options-switch-size-fix/`

- `article-template-manager-2026.css`: `98FC532DD3BF4EA6943A06BD0A0DECF0586387EB0190E39547FCCE00D0EC24A4`
- `article-template-presentation.test.mjs`: `85F5F0C1603815A0102001727611F7AF042BD5F04B5667770A5FB8566F1A7DF0`

## Form UX preview set follow-up

A design-only preview set was created under:

`project-artifacts/mockups/template-options-20260905/forms-v2/`

It covers Header content, Archive toolbar in three states (Category filter off, Button list, Form select), Post list, Reading list sidebar, Thumbnail, Pagination, and Archive shell. The key conditional contract is explicit in the visuals: Category filter OFF hides dependent controls; ON stacks Position and Filter style vertically; Button list and Form select show distinct archive-preview behavior.

Typography was reworked after visual review. The mockup uses `clamp()` tokens for body, meta, caption, label, section, modal, and page heading sizes; no fixed-pixel `font-size` declarations remain in the design HTML. The preview was also adjusted so Button list categories live in a dedicated Categories panel rather than colliding with the search row.

Design-system and focused UI/UX searches used from `ui-ux-pro-max`:

- Minimal Swiss / system-first sans direction for admin settings;
- consistent modular type hierarchy;
- readable responsive font sizing;
- visible labels, focus rings, progressive disclosure, no horizontal overflow;
- chip collection wrapping and native form controls.

Preview verification:

- Playwright screenshot render completed for all 9 PNGs in `forms-v2/screens/`;
- browser console: **0 errors, 0 warnings**;
- mobile viewport check at `390×844`: document `scrollWidth=390`, `clientWidth=390`, `horizontalOverflow=false`;
- final visual inspection confirmed Category filter conditional states, Button list panel placement, Form select layout, Pagination numeric/unit geometry, and readable typography.

The QA note backup for this follow-up is:

`project-artifacts/backups/20260905_120000-template-options-form-previews/QA.md`

SHA-256: `5DA9AEA543A89B01B8CF48B3CF3207E41A56E4670EFB58B714EF61E9B962F1BE`

## Form UX preview V3 follow-up

The V2 preview was rejected during visual review because its typography and card density were too large for a CMS inspector. A compact V3 direction was created under:

`project-artifacts/mockups/template-options-20260905/forms-v3/`

V3 follows the active Phoenix baseline more closely:

- modal title computed at `18.56px` on a 1440px viewport;
- panel heading computed at `16.32px`;
- form label computed at `12.22px`;
- settings input/select computed at `13.44px` with a `40px` control height;
- switch visual remains `56 × 28px` but the row keeps the larger interaction area;
- nested field cards were removed in favor of whitespace, dividers, and a slim conditional accent rail;
- Category filter OFF hides Position and Filter style;
- Category filter ON stacks Position and Filter style without overlap.

V3 verification:

- 9 views rendered at `1440×900`;
- console: **0 errors, 0 warnings**;
- mobile `390×844`: `scrollWidth=390`, `clientWidth=390`, horizontal overflow **false**;
- V3 artifact remains design-only; no production Blade, Vue, or CSS was changed in this follow-up;
- Graphify was not rebuilt for the mockup-only change; `graphify check-update .` remained exit code `0`.

V3 QA note backup:

`project-artifacts/backups/20260905_123000-template-options-form-previews-v3/QA.md`

SHA-256: `E2A1F10A0DF1E126E313799D5C0CDB1B2F25845896EE8C91C41FA8677167C59C`

## Production continuation — Category filter, frame rows, and Page Builder radius group

The approved V3 direction was applied to the active Template Options implementation.

### Changes

- Minimal Reading List Category filter now uses progressive disclosure. When disabled, Position and Filter style are not rendered; when enabled, both controls appear vertically inside the category owner row.
- The same disabled-state gate also hides category position controls for other archive templates instead of leaving a detached control visible.
- Category Position is now a native select with `Left`, `Center`, and `Right`; Category filter style remains a native select with `Button list` and `Form select`.
- Frame fields are stacked one per row for Thumbnail, Pagination, and Archive/Detail shell: Border color, Border width, Border radius, and Background color no longer share a cramped 2×2/3-column layout.
- Border radius now follows the Page Builder form-group pattern: label row + unit selector, four corner values (Top Left, Top Right, Bottom Right, Bottom Left), and explicit link/unlink control.
- Existing single radius values remain backward compatible. New four-corner values are normalized as safe CSS shorthand and are consumed by the existing renderer/preview CSS variables.
- Production modal CSS received the compact V3 pass: local typography cap, flat settings rhythm, whitespace/dividers instead of nested frame cards, and a conditional accent rail. CMS-global theme CSS is untouched.

### Source and tests

- `app/Support/Article/ArticleTemplateOptions.php`: radius shorthand normalization.
- `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: radius parsing, unit conversion, four-corner values, and link state.
- `resources/views/manage_article/templates/index.blade.php`: conditional category controls and Position select.
- `resources/views/manage_article/templates/partials/options-styling.blade.php`: Page Builder-style radius group in Thumbnail, Pagination, and Shell.
- `public/assets/css/article/article-template-manager-2026.css`: compact production inspector, frame rows, radius group, and scoped switch sizing.
- `tests/article-template-presentation.test.mjs`: conditional, full-row, radius-group, and switch-size contracts.
- `tests/manage-article-template-manager.test.mjs`: radius parse/link/unit behavior.
- `tests/Feature/Article/ArticleTemplateOptionsTest.php`: four-value radius normalization and unsafe fallback.

### Final verification after production continuation

- combined Node coverage used for this scope: **59 passed, 0 failed**;
- Laravel `tests/Feature/Article`: **30 passed, 411 assertions**;
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: passed;
- `php artisan view:cache`: passed;
- `git diff --check`: passed; existing CRLF normalization warnings only;
- Graphify incremental update: **21,735 nodes, 39,954 edges**, no clustering;
- `graphify check-update .`: exit code **0** after the final graph update;
- generated graph remains local and was not staged or committed.

### Backups

The production continuation source backups and hashes are under:

`project-artifacts/backups/20260905_133000-template-options-v3-category-radius/`

The final QA/plan pre-update backup is under:

`project-artifacts/backups/20260905_141500-template-options-v3-final-qa/`

- `QA.md`: `67D0B49C3EB0217E6E99FFFB98DFF1270406EB7041EB467DB6444F695EEA17D5`
- `20260905-template-options-revamp-plan.md`: `DB5C1335BB4388A6D25DCF7C9AA9DF1FFBFA6F51A97885977D275B8AE2FFECD0`

### Runtime boundary

The authenticated live manager modal was not exercised in this pass because the available read-only browser session redirects `/manage_article/templates` to `/auth/login`. No credentials were entered, and no Apply/Save action was performed. The design artifact was independently rendered at 1440px and checked at 390px; the production implementation is covered by source contracts, PHP feature tests, syntax/cache checks, and existing preview URL/lifecycle tests, but live computed-style and click-through evidence still require an authenticated session.

## Latest mockup and production synchronization

The latest design correction removed the unused `Content source` control from the archive Header content mockup. Description now has only its owner switch and the description textarea. The existing detail dynamic/custom mode remains available in the detail implementation for backward-compatible detail behavior; it was not removed from the backend contract.

The production spacing controls were synchronized with the approved mockup:

- Padding and Margin are each one Page Builder-style form group;
- label and switch are separated with the switch at the far right;
- unit selector is in the same group header;
- Desktop / Tablet / Mobile controls remain inside the group;
- Top / Right / Bottom / Left fields share one joined edge-control row;
- chain-link/unlink remains bound to the existing Vue `setBoxValue`, `setBoxUnit`, and `toggleBoxLinked` methods;
- Border radius uses the same four-corner group and `radius` helpers.

Latest mockup verification after this synchronization:

- 9 V3 screenshots re-rendered;
- desktop `1440×900`: switch-to-track alignment delta `0px`, settings group gap `24px`, joined edge-control gap `0px`, no horizontal overflow;
- Archive shell: 3 form groups and 3 chain controls detected;
- mobile `390×844`: no horizontal overflow; two-column side values and full-width chain control remain usable;
- browser console: **0 errors, 0 warnings**.

The production synchronization regression suite is **60 Node tests passed**, Laravel Article remains **30 tests passed / 411 assertions**, and `php artisan view:cache` remains passed.

This mockup-only correction did **not** run Graphify and did **not** modify graph data. The latest mockup backup is:

`project-artifacts/backups/20260905_160000-template-options-mockup-v3-description-source/`

The production synchronization backup is:

`project-artifacts/backups/20260905_163000-template-options-latest-design-apply/`

The QA pre-update backup is:

`project-artifacts/backups/20260905_170000-template-options-description-source-final/QA.md`

SHA-256: `D46FAB8E466D651B745EB068D239D400A5503CD6BE77F4D924559A9C43ED92E7`

## Settings panel scroll regression follow-up

The settings panel regression was traced to the flex-height chain. The settings parent was not a clipping boundary and the scroll panel was forced to `height: 100%`, so the browser could lay the content outside the usable modal region after the denser form groups were added.

The narrow fix was applied in `public/assets/css/article/article-template-manager-2026.css`:

- `.article-template-options-settings` now owns `overflow: hidden`;
- `.article-template-options-panel` now uses `height: auto` and `flex: 1 1 0%`;
- the panel keeps `min-height: 0`, `max-height: 100%`, `overflow-y: auto`, and `overflow-x: hidden`;
- no body scroll lock, fixed-position workaround, scroll-jacking, or new JavaScript was added.

Verification with a QA fixture using the active production CSS and the same modal DOM chain:

- desktop `1440×900`: `clientHeight=751`, `scrollHeight=1807`, `scrollTop 0 → 1056`, `canScroll=true`;
- mobile `390×844`: `clientHeight=702`, `scrollHeight=1798`, `scrollTop 1056 → 1096`, `canScroll=true`;
- both viewports: horizontal overflow **false**;
- fixture console: **0 errors, 0 warnings**;
- screenshot: `project-artifacts/qa/20260905-template-options-scroll-fix/scroll-fixture-1440.png`;
- static presentation contract: **20 passed, 0 failed**;
- complete relevant Node suite: **61 passed, 0 failed**;
- Laravel Article suite: **30 passed, 411 assertions**;
- `php artisan view:cache`: passed;
- `git diff --check`: passed with existing CRLF normalization warnings only.

No Graphify command was run for this regression fix, and graph data was not modified. The scroll-fix source backup is:

`project-artifacts/backups/20260905_173000-template-options-scroll-regression/`

The fixture backup is:

`project-artifacts/backups/20260905_180000-template-options-scroll-fixture/`

The QA pre-update backup is:

`project-artifacts/backups/20260905_190000-template-options-scroll-final-qa/QA.md`

SHA-256: `C325C48EB24DE1CACDFE4CDDE09C1880A7CAEC63EE9FEA27785E8B1FC7F2DDEA`
