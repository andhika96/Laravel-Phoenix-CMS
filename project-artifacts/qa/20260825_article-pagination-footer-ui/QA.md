# Article pagination footer UI repair QA

Date: 2026-08-25

## Corrected UI goal

Pagination must be a real footer inside the active archive renderer's `.article-shell`, with the same information hierarchy as Manage Article:

- `Total Data` context on the left;
- Vue `ph-pagination` on the right;
- responsive Bootstrap columns on smaller screens;
- loading replacing the list region inside the same shell.

## Root cause

The previous negative-margin host made the Vue paginator visually overlap the bottom of the renderer section. It was still a sibling outside the shell, which caused the footer to look attached to the viewport/section boundary in inspector view.

## Final implementation

- The server-rendered archive shell is now stable (`v-once`).
- Vue updates only `data-article-vue-list-content` from the server-rendered list response.
- Each archive template exposes stable loading and footer slots inside `.article-shell`.
- Vue Teleports the Manage Article-style loader and pagination footer into those stable slots; no target is destroyed during list refresh.
- Footer markup follows Manage Article's `p-3 d-flex` → `row gx-lg-0 w-100` → two `col-md-6` columns and the same `pagination ph-pagination ms-auto m-0 font-size-inherit` class contract.

## Fresh browser QA

### Desktop / page 3

- Initial loader was inside the Article shell and loading slot: y=290–514, width 1180px.
- Settled footer was inside `.article-shell` and its footer slot: width 1180px; SSR fallback pagination hidden.
- Footer text: `Total Data: 5,027` plus `Showing 25–36 of 5,027 Articles`.
- Next changed to page 4 (`37–48`) while retaining the footer inside the shell.
- Previous returned to page 3 (`25–36`); Back and Forward browser navigation also kept the footer in the shell.
- Browser console was empty in the fresh QA tab.

### Mobile / 390px

- Initial loader was inside the shell/state slot: y=230–454, width 358px.
- Settled footer remained inside the shell at width 343px, equal to the shell width.
- No horizontal overflow.
- Browser console was empty.

### Active template and all renderers

- Browser QA exercised the currently active Editorial Journal renderer after list updates.
- Feature rendering validates the stable loading slot, list wrapper, footer slot, and SSR fallback order for all five archive templates: Minimal Reading List, Editorial Journal, Mosaic Magazine, Mosaic Classic, and Balanced Card Grid.

## Automated verification

- `php artisan test tests/Feature/Article --testdox` — 19 passed, 264 assertions.
- Article Node/template suite — 39 passed.
- `node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js` — passed.
- `php artisan view:clear` and `php artisan view:cache` — passed.
- `git diff --check` — passed; only existing CRLF warnings for unrelated Manage Article Blade files.

## Screenshot limitation

The in-app browser screenshot capability returned `Unable to capture screenshot` during this run. Layout evidence above comes from fresh rendered DOM geometry, visible browser state, and console output; it is not inferred from static CSS alone.
