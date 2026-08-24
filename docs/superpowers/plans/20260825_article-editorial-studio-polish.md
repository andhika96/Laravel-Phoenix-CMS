# Article Editorial Studio Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make all public Article templates and their Manage Article live previews match the approved Editorial Studio mockups.

**Additional approved polish:** Use the active CMS accent (`--ph-theme-primary`) for every Article Template control and preview accent, including a live CMS colour change such as purple to orange. Archive thumbnail media in every live preview must render as a rounded, non-distorted `background-image` using `cover`; preserve the source image aspect ratio without stretching, blurring, or constraining it into a cramped box.

**Architecture:** Keep the existing `ArticleTemplateCatalog` as the shared template selector. Add a deterministic preview fixture that feeds the existing preview iframe, enhance the public controller with eligible neighbors, and use shared CSS/pagination contracts so the eight Blade templates remain visual variations rather than divergent implementations.

**Tech Stack:** Laravel 13, Blade, Vue 3 CDN, Bootstrap 5, CSS custom properties, PHPUnit, Node static tests, Intervention/Image-generated preview media.

**Spec:** `docs/superpowers/specs/20260825_article-editorial-studio-polish-design.md`

## Global Constraints

- Do not modify Event, existing Article rows, template settings, or public route names.
- Preserve SSR links and the current Vue interception for public pagination.
- Preserve Site Config font family/size and responsive typography tokens.
- Use timestamped backups before modifying existing files; do not commit, reset, clean, stage, or push.
- Store generated media and all QA/mockup artifacts under the approved project locations.

---

### Task 1: Curated preview fixture and media

**Files:**
- Create: `app/Support/Article/ArticleTemplatePreviewFixture.php`
- Create: `storage/app/public/articles/template-preview-20260825/*.png`
- Modify: `app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php`
- Test: `tests/Feature/Article/ArticleTemplatePreviewFixtureTest.php`

**Interfaces:**
- Produces `archivePaginator(): LengthAwarePaginator`, `detailArticle(): Article`, and `neighbors(): array{previous:?Article,next:?Article}`.
- `ManageArticleTemplateController::preview()` consumes fixture data for both preview surfaces only.

- [x] Write failing PHPUnit coverage asserting deterministic fixture cards, media paths, rich detail body, and no Article table writes.
- [x] Run the fixture test and confirm the class is missing.
- [x] Generate six curated local editorial images, create the fixture, and wire only the preview controller to it.
- [x] Run focused fixture/controller tests and confirm archive/detail preview payloads use fixture data.

### Task 2: Public detail neighbors

**Files:**
- Modify: `app/Http/Controllers/Web/Article/ArticleFrontendController.php`
- Modify: `resources/views/article/detail.blade.php`
- Test: `tests/Feature/Article/ArticleFrontendRouteTest.php`

**Interfaces:**
- `detail()` exposes nullable `$previousArticle` and `$nextArticle` based on the existing public ordering.
- Detail templates receive both values through the common wrapper.

- [x] Write a failing route test that expects eligible next/previous neighbors but never private/draft records.
- [x] Run it and confirm neighbor values are unavailable.
- [x] Add one private controller helper that queries eligible neighbors with created-at/id tie handling.
- [x] Run the route test and confirm neighbor links render without changing route eligibility.

### Task 3: Shared pagination system

**Files:**
- Modify: `resources/views/article/templates/partials/pagination.blade.php`
- Modify: `public/assets/css/article/article-frontend-2026.css`
- Test: `tests/Feature/Article/ArticleTemplateRenderTest.php`
- Test: `tests/article-frontend-pagination.test.mjs`

**Interfaces:**
- Pagination produces accessible SSR links with `data-article-pagination-link`.
- It renders result summary, first/last controls, bounded page window, ellipses, and Manage Article-compatible `pagination ph-pagination` classes.

- [x] Write failing tests for summary copy, `ph-pagination` classes, chevron controls, first/last page links, and retained SSR data attributes.
- [x] Run the focused tests and confirm current generic pagination fails.
- [x] Implement the bounded paginator markup and theme-aware CSS.
- [x] Run PHP/Node pagination tests and test a deep load-test page in the browser.

### Task 4: Archive template parity

**Files:**
- Modify: all five `resources/views/article/templates/archive/*.blade.php`
- Modify: `public/assets/css/article/article-frontend-2026.css`
- Modify: `app/Support/Article/ArticleTemplateCatalog.php`
- Modify: `resources/views/manage_article/templates/index.blade.php`
- Modify: `public/assets/css/article/article-template-manager-2026.css`
- Test: `tests/Feature/Article/ArticleTemplateRenderTest.php`
- Test: `tests/article-template-presentation.test.mjs`
- Test: `tests/manage-article-template-manager.test.mjs`

**Interfaces:**
- Catalog exposes `best_for` text for selector cards.
- Archive cards share `article-card`/media/body semantics but retain template-specific hierarchy.

- [x] Write failing static/render tests for `best_for`, rounded media wrappers, clamped title/excerpt classes, and uniform card body contract.
- [x] Run tests and confirm current templates lack those contracts.
- [x] Add catalog metadata, selector card labels, and rewrite Archive markup/CSS to the approved layouts.
- [x] Run focused tests and visually inspect all five Archive previews on Desktop/Tablet/Mobile.

### Task 5: Detail template parity

**Files:**
- Modify: all three `resources/views/article/templates/detail/*.blade.php`
- Modify: `public/assets/css/article/article-frontend-2026.css`
- Test: `tests/Feature/Article/ArticleTemplateRenderTest.php`
- Test: `tests/article-template-presentation.test.mjs`

**Interfaces:**
- Each Detail template accepts `$article`, `$previousArticle`, `$nextArticle`, and renders `.article-detail-navigation` when appropriate.

- [x] Write failing tests for detail navigation and the Focused Reader/Editorial Feature/Knowledge visual contracts.
- [x] Run the tests and confirm absent navigation/design hooks.
- [x] Implement approved detail layouts and shared navigation CSS.
- [x] Run focused tests and visually inspect all three Detail previews at three devices.

### Task 6: Theme-synced accent, media safety, and loading transition

**Files:**
- Modify: Article template CSS, manager CSS/ Vue 3 CDN runtime, preview shell, and shared archive media partial.
- Test: `tests/article-template-presentation.test.mjs` and `tests/manage-article-template-manager.test.mjs`

- [x] Add an accessible loading overlay while an iframe preview reloads after a template selection.
- [x] Derive Article and manager accent tokens from `--ph-theme-primary`.
- [x] Pass the active parent theme color to the isolated preview and reload it when the CMS root style changes.
- [x] Convert all five Archive template thumbnails to the shared `background-image: cover` media partial.
- [x] Add a regression test preventing template-specific CSS from resetting the shared media image with `background` shorthand.

### Task 7: Integrated verification and QA

**Files:**
- Create: `project-artifacts/qa/20260825_article-editorial-studio-polish.md`
- Modify: `docs/superpowers/specs/20260825_article-editorial-studio-polish-design.md` only if a verified implementation constraint differs.

- [x] Run `php artisan test tests/Feature/Article` and all related Node tests.
- [x] Run PHP/JS syntax checks, `view:clear`, `view:cache`, and `git diff --check`.
- [x] Perform browser hard-reload QA for all five Archive and three Detail previews, Desktop/Tablet/Mobile, pagination, image loading, and console.
- [x] Run `graphify . --update --no-viz --code-only` and record output in the QA artifact.
- [x] Review the combined diff for scope, then report only verified results and explicit limitations.
