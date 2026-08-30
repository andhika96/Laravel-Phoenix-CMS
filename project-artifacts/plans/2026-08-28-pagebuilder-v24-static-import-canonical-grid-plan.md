# Page Builder v2.4 Static Import Canonical Grid Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (\`- [ ]\`) syntax for tracking.

**Goal:** Make Tailwind and Bootstrap 5 static imports emit canonical responsive grid/layout data without changing existing Canvas migration behavior.

**Architecture:** Keep the change inside \`StaticPageImportService\`. The service tokenizes source classes, maps framework layout tokens to v2.4 settings, and emits canonical grid cells before the existing \`norm()\` call. Existing Canvas normalization, widget definitions, renderer, and v2.3 code remain unchanged.

**Tech Stack:** Laravel 13, PHP \`DOMDocument\`, PHPUnit, Vue 3/JavaScript source checks, PowerShell, Graphify.

**Spec:** \`project-artifacts/plans/2026-08-28-pagebuilder-v24-static-import-canonical-grid-design.md\`

## Global Constraints

- Work only on the v2.4 static import service and its focused tests.
- Do not modify \`norm()\`, \`moveLooseGridChildrenIntoColumns()\`, global Canvas migration, v2.3, CDN loading, asset ingestion, or custom CSS extraction.
- Back up every existing file before modification with a timestamped \`.bak_YYYYMMDD_HHMMSS_static_import_grid\` suffix.
- Keep the current static-import feature and historical backups recoverable.
- Do not commit, push, deploy, migrate, or change the database.
- Run a failing regression test before each production-code slice.

---

### Task 1: Establish baseline and recovery checkpoint

**Files:**

- Read: \`git status\`, \`git rev-parse HEAD\`, existing importer files and tests.
- Create backup: \`app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php.bak_YYYYMMDD_HHMMSS_static_import_grid\`.
- Create backup: \`tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php.bak_YYYYMMDD_HHMMSS_static_import_grid\`.

**Interfaces:**

- Consumes: current baseline \`bc662a9946039bfea1d60f7049e08e5c423cad88\`.
- Produces: verified recovery paths and a clean focused-test baseline.

- [ ] **Step 1: Confirm the working tree and baseline.**

Run:

\`\`\`powershell
git status --short
git rev-parse HEAD
git branch --show-current
\`\`\`

Expected: no source status output, \`main\`, and \`bc662a9946039bfea1d60f7049e08e5c423cad88\`.

- [ ] **Step 2: Run the focused importer baseline.**

Run:

\`\`\`powershell
php artisan test tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php
\`\`\`

Expected: the existing importer tests pass before new tests are added.

- [ ] **Step 3: Copy existing files to timestamped backups.**

Use \`Copy-Item -LiteralPath\` with one generated timestamp for the service and unit test. Verify both destination files exist and are non-empty before editing.

- [ ] **Step 4: Inspect the backup paths and status.**

Run:

\`\`\`powershell
Get-Item -LiteralPath 'app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php.bak_*_static_import_grid'
Get-Item -LiteralPath 'tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php.bak_*_static_import_grid'
git status --short
\`\`\`

Expected: backups exist; only the new backup artifacts are outside the source baseline.

---

### Task 2: Add RED regression tests for import-only canonical layout

**Files:**

- Modify: \`tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php\` after the existing Tailwind mapping test.
- Test: the same focused PHPUnit file.

**Interfaces:**

- Consumes: \`StaticPageImportService::convert(UploadedFile $source, string $framework = 'auto', ?string $entry = null): array\`.
- Produces: assertions for tokenized detection, canonical Tailwind grid cells, responsive counts, and Bootstrap width mapping.

- [ ] **Step 1: Add a test proving \`flex-row\` is not a Bootstrap row signal.**

Import:

\`\`\`html
<main class="flex flex-row gap-4"><h1>Tailwind row</h1></main>
\`\`\`

Assert \`frameworks\` is exactly \`['tailwind']\` and the mapped main node has \`direction\` equal to \`row\`.

- [ ] **Step 2: Add a test proving Tailwind grid classes create canonical columns.**

Import:

\`\`\`html
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-[.9fr_1.1fr]"><h2>A</h2><h2>B</h2><h2>C</h2></section>
\`\`\`

Assert the section has \`displayType: grid\`, desktop \`gridColumns: 2\`, tablet \`gridColumnsTablet: 2\`, mobile \`gridColumnsMobile: 1\`, desktop \`gridTemplateColumns: '.9fr 1.1fr'\`, and no loose \`children\`. Assert the first three canonical cells contain the three heading nodes in source order and no child is lost.

- [ ] **Step 3: Add a test proving responsive Tailwind gaps use matching settings.**

Import a grid with \`gap-3 sm:gap-5 lg:gap-8\` and assert desktop, tablet, and mobile gap values use the existing spacing scale. The test must assert output values, not duplicate the converter's implementation.

- [ ] **Step 4: Extend the Bootstrap test with desktop and mobile width assertions.**

For the existing \`<div class="col-md-6">\`, assert \`containerWidth\` and \`containerWidthTablet\` are \`50%\`, while \`containerWidthMobile\` is \`100%\`. Keep the existing row direction and child-widget assertions.

- [ ] **Step 5: Run the focused tests and verify RED.**

Run:

\`\`\`powershell
php artisan test tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php
\`\`\`

Expected: the new assertions fail because the current service detects Bootstrap from a raw \`row\` substring, does not emit canonical grid columns, lacks responsive grid settings, and does not set the Bootstrap desktop/mobile width contract. Existing tests must not fail from test syntax errors.

---

### Task 3: Implement tokenized framework detection and responsive layout mapping

**Files:**

- Modify: \`app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php\` in \`convertHtml()\`, \`mapNode()\`, \`layoutSettings()\`, \`detectFrameworks()\`, and adjacent private helpers.
- Test: \`tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php\`.

**Interfaces:**

- Consumes: parsed \`DOMDocument\`, source class attributes, existing safe URL and report behavior.
- Produces: the same public \`convert()\` payload shape with canonical grid fields and no global Canvas changes.

- [ ] **Step 1: Replace raw class-substring detection with exact class tokens.**

Collect class attributes from parsed DOM elements and detect Bootstrap only from exact tokens/resources such as \`container\`, \`container-fluid\`, \`row\`, \`row-cols-*\`, \`col\`, \`col-*\`, or Bootstrap 5 stylesheet markers. Detect Tailwind from exact Tailwind resource markers or class tokens such as \`flex-col\`, \`grid-cols-*\`, \`gap-*\`, \`p-*\`, responsive variants, and \`max-w-*\`. Keep explicit \`framework\` override behavior.

- [ ] **Step 2: Add a bounded responsive variant parser.**

Parse one optional \`sm:\`, \`md:\`, \`lg:\`, \`xl:\`, or \`2xl:\` prefix from a class token. Store values in the mobile/tablet/desktop buckets defined by the spec. Ignore state variants such as \`hover:\` for this task and leave them available for later class/CSS preservation.

- [ ] **Step 3: Add safe Tailwind spacing and track parsing.**

Reuse the existing spacing scale. Support \`gap\`, \`gap-x\`, \`gap-y\`, \`p\`, \`px\`, \`py\`, \`pt\`, \`pr\`, \`pb\`, and \`pl\` with optional responsive prefixes. Decode arbitrary grid underscores to spaces and accept only the bounded grid-template grammar from the spec.

- [ ] **Step 4: Map Tailwind grid settings before creating the node.**

For a grid wrapper, compute desktop/tablet/mobile track counts, optional desktop arbitrary template, gaps, and row counts. Create one canonical cell per source child in row-major order and append only the minimum empty cells required by the configured desktop row count. Put the cells in \`columns\`, keep \`children\` empty, and preserve nested mapped nodes.

- [ ] **Step 5: Map Bootstrap row and column widths to the existing flex contract.**

Set \`.row\` to row direction with wrapping. Map \`.col-N\` and breakpoint column tokens to \`containerWidth\`, \`containerWidthTablet\`, and \`containerWidthMobile\` using the mobile/tablet/desktop contract. Keep \`.container-fluid\` mapped to \`container_fluid\`.

- [ ] **Step 6: Run the focused tests and verify GREEN.**

Run:

\`\`\`powershell
php artisan test tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php
\`\`\`

Expected: all focused importer tests pass with zero failures. If a test fails, inspect the payload and correct production mapping; do not weaken the assertion.

---

### Task 4: Verify Canvas compatibility without changing global normalization

**Files:**

- Read: \`public/js/pagebuilder_elementor_v24/app.js:771-791\` and \`public/js/pagebuilder_elementor_v24/app.js:3865-3950\`.
- Modify: none unless a focused importer-only adapter is demonstrably required.
- Test: \`tests/pagebuilder-v24-static-import.test.mjs\` only if a static contract needs to be added.

**Interfaces:**

- Consumes: canonical service payload from Task 3.
- Produces: evidence that existing \`norm()\` receives grid nodes with \`columns\` and does not redistribute loose imported children.

- [ ] **Step 1: Add a focused assertion only if the existing Node contract lacks coverage.**

If the current static contract does not document the no-global-normalizer boundary, add one source assertion that the import trigger still calls \`norm(result.layout)\` and that no new global migration branch was introduced. Do not duplicate PHP payload semantics.

- [ ] **Step 2: Run Node static importer checks.**

Run:

\`\`\`powershell
node --test "tests/pagebuilder-v24-*.test.mjs"
\`\`\`

Expected: zero failures.

- [ ] **Step 3: Inspect the diff boundary.**

Run:

\`\`\`powershell
git diff --name-only -- ':!project-artifacts/**'
\`\`\`

Expected: only the static import service and focused importer test are modified; no \`public/js\` change unless Task 4 Step 1 was necessary.

---

### Task 5: Run proportionate regression and quality checks

**Files:**

- Read-only verification of changed source and existing v2.4 tests.
- Create: \`project-artifacts/qa/pagebuilder-v24-static-import-20260828/\` report/screenshots only if browser QA is available.

**Interfaces:**

- Consumes: green focused tests and canonical import payload.
- Produces: fresh verification evidence and a rollback-ready status report.

- [ ] **Step 1: Run all v2.4 PHPUnit tests.**

Run:

\`\`\`powershell
php artisan test --filter=PageBuilderElementorV24
\`\`\`

Expected: zero failures and no new warnings attributable to this change.

- [ ] **Step 2: Run PHP lint for changed source and the importer controller contract.**

Run:

\`\`\`powershell
php -l app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php
php -l app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php
\`\`\`

Expected: no syntax errors.

- [ ] **Step 3: Run JavaScript syntax and diff checks.**

Run:

\`\`\`powershell
node --check public/js/pagebuilder_elementor_v24/app.js
node --check public/js/pagebuilder_elementor_v24/frontend-runtime.js
git diff --check
\`\`\`

Expected: all commands exit 0.

- [ ] **Step 4: Verify source, backups, and status.**

Run:

\`\`\`powershell
git status --short
git diff --stat
git diff --name-only -- ':!project-artifacts/**'
\`\`\`

Expected: no unrelated files, backups remain present, and no commit/push was made.

- [ ] **Step 5: Classify final evidence.**

Report separately: static source/test evidence, runtime browser evidence, untested CDN/custom CSS/asset boundaries, exact backup paths, and command exit results. Do not claim full visual parity if authenticated browser QA was unavailable.
