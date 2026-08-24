# Article Frontend Template System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Deliver a dedicated, scalable Article Archive/Detail frontend and a global Manage Article Templates screen using the selected Minimal Reading List visual direction.

**Architecture:** A code-owned `ArticleTemplateCatalog` validates registered view keys while one `ArticleTemplateSetting` row stores global Archive/Detail selections. `PublicArticleQuery` produces the same eligible article set for SSR, JSON pagination, and future consumers. Vue 3 global production enhances the SSR pages and manager UI only after their server-rendered content is available.

**Tech Stack:** Laravel 13, Blade, Eloquent, MySQL/SQLite feature tests, Vue 3.5.21 global CDN with existing local global fallback, Axios, existing VuejsPaginateNext UMD.

**Spec:** `docs/superpowers/specs/2026-08-24-article-frontend-template-system-design.md`

## Global Constraints

- Article only; do not modify Event, Page Builder v2.4, or unrelated CMS modules.
- Keep Article frontend middleware policy unchanged: `auth`, `checkSuspended`, and existing Article permissions.
- Global Archive/Detail template choice only; no per-category/per-Article overrides.
- Use allowlisted template keys and no database-stored Blade/HTML source.
- Vue is CDN/global production, pinned to 3.5.21, and must not depend on npm/Vite.
- Preserve the dirty worktree; do not reset, checkout, stage, commit, push, or delete unrelated files.

---

### Task 1: Establish template settings and article public-query contracts

**Files:**
- Create: `database/migrations/2026_08_24_000007_create_article_template_settings_table.php`
- Create: `database/migrations/2026_08_24_000008_add_article_public_listing_indexes.php`
- Create: `app/Models/Article/ArticleTemplateSetting.php`
- Create: `app/Support/Article/ArticleTemplateCatalog.php`
- Create: `app/Support/Article/PublicArticleQuery.php`
- Modify: `app/Models/Article/Article.php`
- Test: `tests/Feature/Article/ArticleTemplateCatalogTest.php`

**Interfaces:**
- `ArticleTemplateCatalog::archive(): array`, `detail(): array`, `defaultSettings(): array`, and `isAllowed(string $surface, string $key): bool` own all valid keys.
- `PublicArticleQuery::builder(Request $request): Builder` returns only currently eligible public Articles.
- `ArticleTemplateSetting::current(): ArticleTemplateSetting` returns/create the singleton row.

- [ ] **Step 1: Write failing catalog/schema tests**

Assert the four Archive keys, three Detail keys, default minimal/focused values, singleton settings persistence, and public query exclusion of draft, private, and future rows.

- [ ] **Step 2: Run the focused test to verify RED**

Run: `php artisan test tests/Feature/Article/ArticleTemplateCatalogTest.php --no-ansi`

Expected: FAIL because the settings table, catalog, and query service do not exist.

- [ ] **Step 3: Add migrations, model, catalog, query service, and Article relations**

Create the singleton settings table with allowlisted template string columns and bounded per-page integer. Add public listing indexes. Add `category()` and `author()` relations. Keep query eligibility in one service and eager-load only card needs.

- [ ] **Step 4: Run focused tests to verify GREEN**

Run: `php artisan test tests/Feature/Article/ArticleTemplateCatalogTest.php --no-ansi`

Expected: PASS.

### Task 2: Replace incomplete Article frontend routing with SSR + JSON contract

**Files:**
- Create: `app/Http/Controllers/Web/Article/ArticleFrontendController.php`
- Create: `app/Http/Resources/Article/PublicArticleResource.php`
- Modify: `routes/web.php`
- Modify: `routes/breadcrumbs.php`
- Test: `tests/Feature/Article/ArticleFrontendRouteTest.php`

**Interfaces:**
- `ArticleFrontendController@index(Request)` returns the selected Archive Blade view with initial paginator data.
- `listData(Request)` returns the existing standard JSON pagination envelope using `PublicArticleResource`.
- `detail(string $idOrSlug)` returns the selected Detail Blade view or 404.

- [ ] **Step 1: Write failing route tests**

Cover static `/article/listdata` precedence, published/public/current visibility, safe search/category/tag filters, pagination metadata, and 404 for non-public detail records.

- [ ] **Step 2: Run route test to verify RED**

Run: `php artisan test tests/Feature/Article/ArticleFrontendRouteTest.php --no-ansi`

Expected: FAIL because the active route/controller/view contract is incomplete.

- [ ] **Step 3: Implement controller/resource/routes**

Register static list data before dynamic lookup. Preserve existing middleware. Use catalog-selected views, validated query inputs, and safe id-or-URI lookup.

- [ ] **Step 4: Run route test to verify GREEN**

Run: `php artisan test tests/Feature/Article/ArticleFrontendRouteTest.php --no-ansi`

Expected: PASS.

### Task 3: Implement the Article Archive and Detail template registry

**Files:**
- Create: `resources/views/article/archive.blade.php`
- Create: `resources/views/article/detail.blade.php`
- Create: `resources/views/article/templates/archive/minimal-reading-list.blade.php`
- Create: `resources/views/article/templates/archive/mosaic-magazine.blade.php`
- Create: `resources/views/article/templates/archive/editorial-journal.blade.php`
- Create: `resources/views/article/templates/archive/balanced-card-grid.blade.php`
- Create: `resources/views/article/templates/detail/focused-reader.blade.php`
- Create: `resources/views/article/templates/detail/editorial-feature.blade.php`
- Create: `resources/views/article/templates/detail/knowledge-toc.blade.php`
- Create: `public/assets/css/article/article-frontend-2026.css`
- Test: `tests/Feature/Article/ArticleTemplateRenderTest.php`

**Interfaces:**
- Every registered catalog key maps to one existing Blade partial.
- Archive partials consume `$articles`, `$templateSettings`, and `$articleRouteBase`.
- Detail partials consume `$article`, `$templateSettings`, and use no dynamic view name from a request.

- [ ] **Step 1: Write failing render tests**

Assert every catalog key maps to an existing partial; archive output has accessible article links and numeric pagination; details expose title/content/image only through public article data.

- [ ] **Step 2: Run render test to verify RED**

Run: `php artisan test tests/Feature/Article/ArticleTemplateRenderTest.php --no-ansi`

Expected: FAIL because registry views do not exist.

- [ ] **Step 3: Implement selected visual direction and all registered templates**

Make Minimal Reading List the default. Implement Mosaic Magazine as the second visual option, then Editorial Journal and Balanced Card Grid. Implement the three detail views with coherent responsive rules and keyboard-visible pagination styles.

- [ ] **Step 4: Run render test to verify GREEN**

Run: `php artisan test tests/Feature/Article/ArticleTemplateRenderTest.php --no-ansi`

Expected: PASS.

### Task 4: Add Vue 3 CDN archive enhancement

**Files:**
- Create: `public/assets/js/vue3/article/vueV3-article-frontend-2026.js`
- Modify: `resources/views/article/archive.blade.php`
- Test: `tests/article-frontend-pagination.test.mjs`

**Interfaces:**
- Vue state consumes the server-provided `/article/listdata` endpoint and only replaces the archive list after a successful response.
- `loadArticles(page)` preserves `search`, `category`, and `tag` query state.
- SSR pagination remains navigable without JavaScript.

- [ ] **Step 1: Write a failing Node test**

Assert page/filter state is included in the request and a failed request retains the previous list rather than blanking it.

- [ ] **Step 2: Run Node test to verify RED**

Run: `node --test tests/article-frontend-pagination.test.mjs`

Expected: FAIL because the Vue module does not exist.

- [ ] **Step 3: Implement Vue global bootstrap and pinned CDN/fallback loading**

Use `Vue.createApp` only after the pinned CDN script succeeds or the already-loaded local Vue global is available. Do not load a second app on the same root. Use Axios already supplied by the frontend theme.

- [ ] **Step 4: Run Node test and syntax check to verify GREEN**

Run: `node --test tests/article-frontend-pagination.test.mjs; node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js`

Expected: PASS with no syntax output.

### Task 5: Build Manage Article Templates

**Files:**
- Create: `app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php`
- Create: `app/Http/Requests/Article/UpdateArticleTemplateSettingRequest.php`
- Create: `resources/views/manage_article/templates/index.blade.php`
- Create: `public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`
- Modify: `routes/web.php`
- Modify: `routes/breadcrumbs.php`
- Modify: `resources/views/manage_article/manage_article.blade.php`
- Test: `tests/Feature/Article/ManageArticleTemplateTest.php`
- Test: `tests/manage-article-template-manager.test.mjs`

**Interfaces:**
- `GET /manage_article/templates` provides catalog metadata and settings.
- `POST /manage_article/templates` validates catalog keys/per-page and persists one singleton record.
- The manager Vue app exposes Archive/Detail tabs, selected/default template cards, device preview state, and one Save action.

- [ ] **Step 1: Write failing feature/static tests**

Test permissions, invalid key rejection, valid setting persistence, visual-contract Blade markers, selected state, and single-save request payload.

- [ ] **Step 2: Run tests to verify RED**

Run: `php artisan test tests/Feature/Article/ManageArticleTemplateTest.php --no-ansi; node --test tests/manage-article-template-manager.test.mjs`

Expected: FAIL because the management controller/views/scripts do not exist.

- [ ] **Step 3: Implement the manager page from selected visual direction**

Add a Manage Templates action to Manage Article. Implement source-safe thumbnail previews, Archive/Detail tabs, device preview controls, selected/default states, and the Vue save flow. Do not add freeform template editing or template CRUD.

- [ ] **Step 4: Run focused tests to verify GREEN**

Run: `php artisan test tests/Feature/Article/ManageArticleTemplateTest.php --no-ansi; node --test tests/manage-article-template-manager.test.mjs`

Expected: PASS.

### Task 6: Integrated verification and browser QA

**Files:**
- Create: `project-artifacts/qa/20260824_article-frontend-template-system.md`
- Update: `graphify-out/` via incremental code-only Graphify run

- [ ] **Step 1: Run focused Article suite and static checks**

Run: `php artisan test tests/Feature/Article --no-ansi; php -l app/Http/Controllers/Web/Article/ArticleFrontendController.php; node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js; node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js; php artisan view:cache --no-ansi; git diff --check`

- [ ] **Step 2: Browser QA with hard reload**

Verify `/article`, a detail URL, `/manage_article/templates`, numeric pagination, search/filter state, Archive/Detail switching, template selection/save validation, desktop/mobile layout, console, and network evidence. Do not create/delete Article content during QA.

- [ ] **Step 3: Run proportional regression and update Graphify**

Run: `php artisan test --no-ansi`, then `graphify . --update --no-viz --code-only` and `graphify cluster-only`.

- [ ] **Step 4: Record evidence**

Write command outcomes, browser evidence, known unrelated failures, files backed up, and Graphify result in the QA artifact.
