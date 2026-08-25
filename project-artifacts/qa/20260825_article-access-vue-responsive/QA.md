# Article frontend access, Vue, and responsive QA

> Correction (2026-08-25): this report did not inspect the end-of-list geometry for pagination and loading controls. Do not use it as evidence for their placement. That scope is corrected in `../20260825_article-pagination-layout-repair/QA.md`.

Date: 2026-08-25

## Scope

- Public `/article` archive: Vue 3 CDN ownership of the list request, search, category filter contract, pagination, and loaders.
- Public Article access gates: public, private, and password-protected states.
- Responsive Article archive/detail typography and no horizontal overflow.

## Fresh runtime evidence

1. Archive hard reload hydrated the Vue shell with one visible Vue paginator; the SSR paginator was hidden after hydration. Console logs were empty.
2. Search for `Load Test Technology` updated the URL to `/article?search=Load+Test+Technology`, retained the entered query, and rendered matching Article cards through the Vue list endpoint. Console logs were empty.
3. Vue paginator `Next` changed the result summary from `1–12` to `13–24`; `Previous` returned to `1–12`. Both paths retained the active search query and rendered the expected records.
4. Browser Back restored page 2 data and Browser Forward restored page 1 data without duplicate or stale list content. Console logs were empty.
5. Tablet archive at 834×1194 and mobile archive at 390×844 had no horizontal overflow, retained a hydrated Vue archive, and kept toolbar/pagination within the viewport.
6. Public detail responsive typography was measured at desktop/tablet/mobile:
   - desktop: title 20.25px, dek 15.25px;
   - tablet: title 19.6px, dek 15.12px;
   - mobile: title 18.2px, dek 14.7px.
7. `/article/testing` rendered the private access state with no detail renderer or private content/title leak.
8. `/article/11` rendered the Vue password modal with a password input, show/hide toggle, close/back controls, and no protected detail/title leak. No password was entered or submitted during browser QA.

## Visual evidence

- `02-article-archive-tablet.jpg` — inspected tablet archive.
- `03-article-archive-mobile.jpg` — inspected mobile archive.
- `04-password-protected-modal.jpg` — inspected password-protected modal.

## Automated verification

- `node --test tests/article-template-presentation.test.mjs tests/article-template-preview-fixture.test.mjs tests/article-frontend-pagination.test.mjs tests/manage-article-template-manager.test.mjs` — 38 passed.
- `php artisan test tests/Feature/Article --testdox` — 18 passed, 219 assertions.
- `php artisan test tests/Feature/ManageArticleThumbnailTest.php --testdox` — 5 passed, 30 assertions.
- `php artisan test tests/Feature/Article/ArticlePasswordStorageMigrationTest.php --testdox` — 1 passed, 4 assertions; exercises migrations `000010` then `000011` in order.
- `node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js` — passed.
- PHP lint for affected Article access files and migrations — passed.
- `git diff --check` — passed; only existing CRLF normalization warnings for two Manage Article Blade files.

## Notes

- The saved active template configuration currently enables search and disables the category toolbar. The category Vue path is covered by source/runtime contracts without changing the user's saved template options.
- Legacy duplicate Article URIs remain outside this QA change. Numeric Article ID `/article/11` was used for the protected-state browser check so the target was unambiguous.
