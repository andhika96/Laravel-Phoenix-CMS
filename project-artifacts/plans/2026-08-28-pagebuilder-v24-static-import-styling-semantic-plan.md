# Page Builder v2.4 Static Import Styling and Semantic Mapping Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans. Steps use checkbox (`- [ ]`) syntax.

**Goal:** Apply framework/scoped CSS and semantic CTA/icon mapping only to imported static layouts.

**Architecture:** `StaticPageImportService` owns HTML-to-node mapping and import metadata. A dedicated `StaticPageCssProcessor` owns CSS extraction/sanitization/scoping. Existing Custom CSS persistence renders processed CSS; conditional dependency loaders read metadata from the imported root.

**Tech Stack:** Laravel 13, PHP DOMDocument, Vue 3 global build, Blade, PHPUnit, Node test runner.

**Spec:** `project-artifacts/plans/2026-08-28-pagebuilder-v24-static-import-styling-semantic-design.md`

## Global Constraints

- Manual pages without `settings.staticImport` must remain unchanged.
- Do not modify v2.3, File Manager, storage, migrations, or database.
- Remote images stay direct; relative images remain report-only.
- Never execute arbitrary imported script/config.
- Back up every existing target file before edits.
- TDD RED must precede each production slice.
- Do not commit, push, deploy, or press Save.

---

### Task 1: CSS processor contract

**Files:**

- Create: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageCssProcessor.php`
- Create: `tests/Unit/PageBuilderElementorV24StaticPageCssProcessorTest.php`
- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
- Test: both focused unit test files.

**Interfaces:**

- Produces `StaticPageCssProcessor::process(DOMDocument $dom, string $html): array{css:string,stylesheets:array,warnings:array,droppedStyles:int}`.
- Service returns processed `customCss` and root `settings.staticImport`.

- [ ] Write RED tests asserting selector scoping, media preservation, unsafe rule removal, ID compatibility, Google Fonts allowlist, and arbitrary stylesheet rejection.
- [ ] Run processor tests and confirm failures are missing class/behavior—not syntax errors.
- [ ] Implement minimum balanced-rule parser, declaration sanitizer, selector scoper, and stylesheet allowlist.
- [ ] Add RED service integration test for `customCss` and root metadata.
- [ ] Wire processor into `convertHtml()`; preserve existing report semantics.
- [ ] Run focused tests GREEN.

### Task 2: Conditional editor/frontend dependencies

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php`

**Interfaces:**

- Reads root `settings.staticImport.frameworks/stylesheets`.
- Editor loader is no-op without metadata.
- Frontend emits pinned Bootstrap/Tailwind/Google Fonts only with metadata.

- [ ] Add RED Node contract assertions for metadata reader, scoped Tailwind config, and post-import dependency sync.
- [ ] Add RED Blade render tests proving imported/manual dependency isolation.
- [ ] Implement allowlisted editor link/script loader and call it after import/on mount.
- [ ] Implement conditional frontend dependency output with server-side allowlist.
- [ ] Run focused Node/PHP tests GREEN.

### Task 3: Semantic CTA and icon mapping

**Files:**

- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
- Modify: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`

**Interfaces:**

- Produces native `button` settings compatible with current Button definition.
- Produces native `icon` settings with allowlisted Font Awesome mapping.
- Ordinary anchors remain Text Editor.

- [ ] Add RED tests for `gold-button`, native `button`, ordinary nav anchor, nested CTA icon, and standalone Phosphor icon.
- [ ] Implement exact CTA classifier and finite icon-name map.
- [ ] Preserve source class/ID settings and safe URL behavior.
- [ ] Run focused tests GREEN.

### Task 4: Verification and handoff

**Files:**

- Create: `project-artifacts/qa/pagebuilder-v24-static-import-20260828/QA_REPORT_06-styling-semantic.md`
- Read-only: Git diff, manual surfaces, fixture payload, tests, Graphify.

- [ ] Probe CEO Masters fixture for CSS length, metadata, CTA/icon counts, UTF-8, and remote/missing assets.
- [ ] Run focused tests, full Node v2.4, PHP v2.4 regression, lint, view cache, and diff check.
- [ ] Confirm manual app/modules/routes/controller remain unchanged except the intentional conditional loader/renderer no-op.
- [ ] Update Graphify incrementally and run `graphify check-update .`.
- [ ] Report confirmed, partial, and unverified runtime boundaries honestly.

