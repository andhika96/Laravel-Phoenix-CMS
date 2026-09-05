# QA - Minimal Reading List category controls and post-list spacing

- Project: Laravel 13 Phoenix
- Date: 2026-09-05
- Scope: correct the spacing option target, remove the `Latest Articles` summary row, and retain the Minimal Reading List category/pagination behavior.
- UI/UX reference: UI/UX Pro Max guidance and the approved clean blog-list direction from the preceding Minimal Reading List redesign.

## Implemented contract

- `post_list.item_gap` is the single normalized spacing setting for the vertical gap between the main reading-list posts.
- Default spacing is `0.75rem`; values continue through the existing allowlisted unit normalizer.
- The manager exposes `Post list spacing` in its own Minimal Reading List section, independent of the sidebar toggle.
- `sidebar.popular.item_gap` and the `Popular post spacing` manager control were removed. Popular Posts keeps its own fixed internal `0.75rem` layout gap.
- The Blade renderer applies `--article-reading-list-post-gap` to the main article-list wrapper and clears the old item margin/padding that caused compounded vertical spacing.
- The `Latest Articles` / total-count row is removed from the Minimal Reading List DOM and its unused scoped CSS.
- Category filter behavior remains option-driven: disabled, `Form select` with automatic submit, or `Button list` with a client-side search and a maximum of ten category options.
- Category links and Vue pagination continue using the existing asynchronous archive loader; no new endpoint or dependency was added.

## Files changed in the correction

- `app/Support/Article/ArticleTemplateOptions.php`
- `resources/views/article/templates/archive/minimal-reading-list.blade.php`
- `resources/views/manage_article/templates/index.blade.php`
- `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`
- `public/assets/css/article/article-frontend-2026.css`
- `tests/article-minimal-reading-list-filter.test.mjs`
- `tests/article-minimal-reading-list-option-scope.test.mjs`
- `tests/Feature/Article/ArticleMinimalReadingListFilterTest.php`

The preceding Minimal Reading List implementation also remains covered by the existing archive header, sidebar, controller, preview, Vue, and manager files/tests in the dirty worktree.

## Backups

Backups were created before the correction under:

`project-artifacts/backups/20260905_article_minimal_reading_list_category_filter/`

Correction backup marker: `20260905_051733_move_spacing_to_post_list`.

The backup files were copied from the active source and SHA-256 verified during the change. Earlier category-filter and heading-removal backups remain in the same folder.

## Automated verification

- Node focused Minimal Reading List suite: **13 passed, 0 failed**.
- PHP focused article/template suite: **22 passed, 321 assertions**.
- `php -l app/Support/Article/ArticleTemplateOptions.php`: passed.
- `node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js`: passed.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: passed.
- `php artisan view:cache`: passed.
- `git diff --check`: passed; only pre-existing line-ending warnings for unrelated dirty files were reported.
- Full `php artisan test tests/Feature/Article --testdox`: **28 passed, 1 failed, 399 assertions**. The unchanged failure is `ArticleFrontendRouteTest::private and password protected articles use access gates without leaking content`: the wrong-password request receives **419 CSRF** instead of the expected **422** at `tests/Feature/Article/ArticleFrontendRouteTest.php:274`. It is outside this UI/template scope and the failure remains identical in cause.

## Public runtime QA (read-only)

URL checked: `https://laravel-13-phoenix.aruna/article?category=11&page=2`.

- `sectionHeadingCount`: `0`.
- Visible text matching `Latest Articles`: `0`.
- Main article rows: `12`.
- Main list inline gap: `0.75rem`; computed row gap: `12px`.
- Main item margin/padding: `0px / 0px` for sampled rows.
- Popular list has no spacing inline variable; its computed internal row gap is fixed at `12px`.
- Scroll remains enabled: document height `2720px`, viewport `720px`, body `overflow-y: auto`.
- Category search narrowed the visible button list to the matching category without navigation.
- Clicking a category changed the URL to `?category=11` and replaced the article list asynchronously; the page remained mounted.
- Clicking Vue page `2` changed the URL to `?category=11&page=2` and replaced the list asynchronously; the result context changed to `Showing 13–24 of 715 Articles`.
- Browser console warnings/errors: none.

The manage-template page was not changed through the browser: its live route requires administrator authentication. Manager synchronization is covered statically and by the PHP/Node tests; no credentials or live settings were submitted.

## Graphify

Graphify was updated after the final source changes using the project root. Final report:

- `21,693 nodes`
- `37,585 edges`
- `1,565 communities`
- `graph.json` and `GRAPH_REPORT.md` updated under `graphify-out`.
- `graph.html` was skipped because the graph exceeds the 5,000-node visualization limit.
- Graphify reported 58 metadata/generated files producing zero nodes; they were omitted as non-code nodes and did not block the rebuild.

The final targeted Graphify query confirmed the `ArticleTemplateOptions`, Minimal Reading List Blade, manager template, manager Vue, frontend Vue, and article CSS nodes are present in the updated graph.

## Remaining limitations

- The full Article suite still has the pre-existing CSRF/unlock test failure described above.
- Live manager preview interaction after opening Template Options was not possible without administrator authentication; no live database/template setting was changed.
- No commit, staging, push, or broad cleanup was performed.
