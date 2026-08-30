# Page Builder v2.4 Guided Compiled Native Import Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengubah `Compiled Native` menjadi workflow dua fase yang mendeteksi Header, ordered Sections, dan Footer terlebih dahulu, menampilkan rekomendasi mapping tree yang dapat dikoreksi per block, lalu mengompilasi hanya mapping yang sudah dikonfirmasi menjadi layout native editable.

**Architecture:** Import `Compiled Native` menjadi dua request stateless terhadap endpoint yang sama: `phase=analyze` mengembalikan inventory region/block tanpa layout Canvas, sedangkan `phase=compile` mengirim ulang file source yang sama bersama mapping JSON tervalidasi. Server menangani struktur, rekomendasi widget, dan validasi mapping; browser tetap memakai compiler/scanner iframe yang sudah ada untuk CSSOM, geometry, residual CSS, dan hydration native setelah mapping disetujui.

**Tech Stack:** Laravel 13, PHP `DOMDocument`, existing `ModuleCatalog`, Vue 3, browser CSSOM/iframe compiler, existing Page Builder v2.4 module registry, PHPUnit, Node `node:test`, Playwright CLI, Graphify.

**Spec:** `project-artifacts/plans/2026-08-29-pagebuilder-v24-section-computed-style-scanner-plan.md`, `project-artifacts/plans/2026-08-28-pagebuilder-v24-static-import-styling-semantic-design.md`, `project-artifacts/plans/2026-08-28-pagebuilder-v24-static-import-canonical-grid-design.md`, dan keputusan Guided Compiled Native pada percakapan 2026-08-29.

## Global Constraints

- Hanya workflow mode `Compiled Native` yang berubah; mode manual, `Native`, `Exact Visual`, Save, drag/drop, responsive engine utama, dan frontend renderer manual tetap memakai jalur lama.
- Workflow harus memakai rekomendasi tree otomatis yang dapat dikoreksi per block; pengguna tidak diwajibkan memilih seluruh widget dari nol.
- Header, top-level Sections, dan Footer adalah `region`; satu region dapat menghasilkan nested tree berisi beberapa Layout/Basic/General/Pro widgets.
- Pemilihan widget harus memakai stable source marker dan `sourceHash`, tidak boleh memakai DOM index.
- Analyze tidak boleh menghasilkan atau memasang layout Canvas, custom CSS, atau draft mutation.
- Compile harus stateless: browser mengirim ulang file `File` yang sama bersama mapping JSON; tidak ada source upload sementara, token session, queue cleanup, tabel, atau migration baru.
- Analyze dan Compile tidak boleh auto-save; hasil Compile hanya mengganti draft Canvas setelah seluruh validasi dan browser compilation berhasil.
- Source script, inline event handler, form submit source, `iframe`, `object`, dan `embed` tetap tidak dieksekusi.
- Existing Asset Base URL tetap optional; File Manager V2 dan ZIP asset promotion tidak termasuk scope.
- Mapping fallback tetap tersedia sebagai `exact_visual` per region, tetapi tidak boleh dipilih otomatis jika seluruh block region mempunyai mapping native valid.
- UI memakai progressive disclosure: daftar region selalu terlihat, mapping tree hanya dibuka untuk region aktif, dan rekomendasi dapat diterapkan sekaligus.
- Setiap kontrol interaktif memiliki accessible name, keyboard path, visible focus, dan state `aria-selected`/`aria-expanded` yang sesuai.
- Preserve dirty worktree dan seluruh backup. Sebelum mengubah existing file, buat backup timestamp. Jangan reset, clean, stage, commit, push, deploy, Save, atau menjalankan form submit nyata tanpa otorisasi terpisah.

---

## Product Decisions

### Workflow

```text
Select source file
  -> Analyze source
  -> Region inventory
  -> Review recommended mapping tree
  -> Correct strategy/widget choices
  -> Validate mapping
  -> Compile selected regions
  -> Browser computed-style scan
  -> Native hydration + scoped residual CSS
  -> Unsaved draft Canvas review
```

### Region strategies

- `auto_native`: memakai seluruh recommended mapping tree tanpa perubahan.
- `guided_native`: tree tetap berasal dari rekomendasi mesin, tetapi block widget dapat diganti pengguna.
- `exact_visual`: satu region menjadi isolated `static_html`; source script/form submit tetap disabled.
- `skip`: region tidak dimasukkan ke hasil.

`auto_native` adalah default bila semua block valid. Region dengan warning tetap default ke `guided_native`, bukan `exact_visual`, agar pengguna dapat memperbaiki mapping sebelum memilih fallback.

### Region kinds

- `header`: top-level `<header>` atau landmark `role="banner"`.
- `section`: top-level `<section>`, `<main>` direct child, atau body direct child fallback yang mempunyai visible content.
- `footer`: top-level `<footer>` atau landmark `role="contentinfo"`.

Nested `<section>` tetap menjadi block subtree dari region induknya agar inventory tidak menggandakan konten.

### Block roles

```text
layout, heading, text, image, button, icon, form,
divider, video, card_collection, navigation, unknown
```

Compatibility awal:

| Role | Allowed widget types |
|---|---|
| `layout` | `container`, `container_fluid`, `grid`, `row_grid` |
| `heading` | `heading`, `text_editor` |
| `text` | `text_editor`, `heading` |
| `image` | `image`, `image_box` |
| `button` | `button`, `call_to_action` |
| `icon` | `icon`, `icon_box` |
| `form` | `form` |
| `divider` | `divider` |
| `video` | `video` |
| `card_collection` | `container`, `grid`, `carousel` |
| `navigation` | `container`, `grid` |
| `unknown` | no native widget; region must use `exact_visual`/`skip` or analyzer must first classify the block into a supported role |

Compatibility class harus memeriksa `ModuleCatalog`; widget inactive atau tidak terdaftar tidak boleh muncul atau diterima.

---

## API Contracts

### Analyze request

```http
POST /pagebuilder-elementor/v2.4/import-static
Content-Type: multipart/form-data

phase=analyze
mode=compiled
framework=auto|tailwind|bootstrap5
baseUrl=https://optional.example/assets/
source=<same HTML/ZIP file>
```

### Analyze response

```json
{
  "success": true,
  "phase": "analyze",
  "mode": "compiled",
  "sourceHash": "sha256-hex",
  "pageName": "CEO Masters Indonesia 2026",
  "frameworks": ["tailwind"],
  "regions": [
    {
      "id": "region-import-node-2",
      "marker": "import-node-2",
      "kind": "header",
      "sourceId": "site-header",
      "label": "Header",
      "order": 0,
      "recommendedStrategy": "auto_native",
      "confidence": 0.94,
      "warnings": [],
      "stats": { "elements": 18, "blocks": 7, "images": 0, "forms": 0 },
      "blocks": []
    }
  ],
  "previewPayload": {
    "html": "<!doctype html>...sanitized source markers...",
    "frameworks": ["tailwind"],
    "tailwindConfig": {},
    "sourceCss": ""
  },
  "report": { "warnings": [], "relativeAssets": [] }
}
```

Analyze response must not contain `layout`, `customCss`, or executable source JavaScript.

### Block contract

```json
{
  "id": "block-import-node-38",
  "marker": "import-node-38",
  "parentId": "block-import-node-35",
  "role": "heading",
  "tag": "h1",
  "label": "CEO Masters Indonesia 2026",
  "textPreview": "CEO Masters Indonesia 2026",
  "recommendedWidget": "heading",
  "allowedWidgets": ["heading", "text_editor"],
  "confidence": 0.99,
  "children": [],
  "warnings": []
}
```

### Compile mapping request

```http
POST /pagebuilder-elementor/v2.4/import-static
Content-Type: multipart/form-data

phase=compile
mode=compiled
framework=auto
source=<same source file>
sourceHash=<analyze response hash>
mapping=<JSON below>
```

```json
{
  "version": 1,
  "regions": [
    {
      "regionId": "region-import-node-2",
      "strategy": "guided_native",
      "blocks": [
        { "blockId": "block-import-node-3", "widgetType": "container" },
        { "blockId": "block-import-node-4", "widgetType": "heading" }
      ]
    },
    { "regionId": "region-import-node-71", "strategy": "exact_visual", "blocks": [] },
    { "regionId": "region-import-node-320", "strategy": "skip", "blocks": [] }
  ]
}
```

### Compile response

Compile response keeps the current browser compiler contract and adds mapping metadata:

```json
{
  "success": true,
  "phase": "compile",
  "mode": "compiled",
  "sourceHash": "sha256-hex",
  "layout": [],
  "compilePayload": {},
  "exactFallback": {},
  "mappingReport": {
    "regions": 11,
    "nativeRegions": 9,
    "exactRegions": 1,
    "skippedRegions": 1,
    "mappedBlocks": 287,
    "unmappedBlocks": 0
  },
  "report": {}
}
```

### Error codes

- `source-hash-mismatch`: compile source berbeda dari analyze source.
- `mapping-version-unsupported`: mapping version bukan `1`.
- `unknown-region`: `regionId` tidak ditemukan pada source terbaru.
- `unknown-block`: `blockId` tidak ditemukan di region pemiliknya.
- `duplicate-region-mapping`: region dipetakan lebih dari sekali.
- `duplicate-block-mapping`: block dipetakan lebih dari sekali.
- `incompatible-widget`: widget bukan allowed widget untuk role block.
- `inactive-widget`: widget tidak aktif di `ModuleCatalog`.
- `unresolved-block`: strategy native masih mempunyai block tanpa mapping valid.

---

## Client State Contract

Create `window.PhoenixGuidedStaticImport` with pure helpers:

```js
createState()
normalizeAnalysis(payload, moduleCatalog)
setRegionStrategy(state, regionId, strategy)
setBlockWidget(state, regionId, blockId, widgetType)
regionValidation(state, regionId)
buildCompileMapping(state)
buildRegionPreviewSrcdoc(previewPayload, regionMarker)
```

State shape:

```js
{
  phase: 'idle', // idle|analyzing|mapping|compiling|complete|failed
  sourceFile: null,
  sourceHash: '',
  analysis: null,
  selectedRegionId: '',
  expandedBlockIds: [],
  regionMappings: {},
  errors: [],
}
```

`sourceFile` stays only in browser memory and is not serialized into page JSON or browser storage.

---

## File Map

### Create

- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageRegionAnalyzer.php`
  - Detect top-level regions, block tree, semantics, stats, confidence, and warnings.
- `app/Support/PageBuilderElementorV24/StaticImport/StaticImportWidgetCompatibility.php`
  - Own role-to-widget compatibility and validate active types through `ModuleCatalog`.
- `app/Support/PageBuilderElementorV24/StaticImport/StaticImportMappingValidator.php`
  - Validate mapping version, ownership, compatibility, completeness, and source hash.
- `public/js/pagebuilder_elementor_v24/static-import-guided.js`
  - Pure wizard state/mapping helpers; no Vue rendering and no network calls.
- `tests/Unit/PageBuilderElementorV24StaticPageRegionAnalyzerTest.php`
- `tests/Unit/PageBuilderElementorV24StaticImportMappingValidatorTest.php`
- `tests/pagebuilder-v24-guided-static-import.test.mjs`
- `tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php`
- `project-artifacts/qa/pagebuilder-v24-guided-static-import-20260829/QA_REPORT.md`

### Modify

- `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`
  - Add `phase`, `sourceHash`, and bounded `mapping` validation.
- `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
  - Route analyze/compile phase without adding a new route family.
- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
  - Expose analyze/compile orchestration and reuse existing source/CSS/compiler helpers.
- `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
  - Load `static-import-guided.js` before `app.js`.
- `public/js/pagebuilder_elementor_v24/app.js`
  - Add wizard state/network orchestration and reuse existing compile/scanner hydration.
- `public/assets/css/pagebuilder_elementor_v24.css`
  - Add scoped wizard layout; no global control or Canvas selectors.
- `tests/pagebuilder-v24-static-import.test.mjs`
  - Preserve old-mode isolation and loader order contracts.
- `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`
  - Preserve scanner/compiled response behavior after guided mapping.

### Verify only unless a proven gap requires a separate approved change

- `app/Support/PageBuilderElementorV24/ModuleCatalog.php`
- `resources/pagebuilder_elementor_v24/modules/**/module.json`
- `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
- `public/js/pagebuilder_elementor_v24/static-import-native.js`
- All Layout/Widget Canvas and frontend renderer files.

---

## Implementation Tasks

### Task 1: Lock the two-phase request boundary

**Files:**

- Create: `tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php`
- Modify: `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`

**Interfaces:**

- Consumes: existing authenticated `import_static` route and upload validation.
- Produces: validated `phase(): analyze|compile`, bounded mapping JSON, and normalized request accessors for Task 5.

- [ ] Write failing feature tests that reject an invalid phase and reject `phase=compile` without `sourceHash`/mapping before the service is invoked.

```php
$this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
    'phase' => 'invalid-phase',
    'mode' => 'compiled',
    'source' => UploadedFile::fake()->createWithContent('regions.html', '<section>Hero</section>'),
])->assertUnprocessable()->assertJsonValidationErrors('phase');

$this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
    'phase' => 'compile',
    'mode' => 'compiled',
    'source' => UploadedFile::fake()->createWithContent('regions.html', '<section>Hero</section>'),
])->assertUnprocessable()->assertJsonValidationErrors(['sourceHash', 'mapping']);
```

- [ ] Add request tests rejecting mapping over 512 KB, malformed JSON, and mapping version other than `1`.
- [ ] Run `php artisan test tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php --colors=never` and verify RED because phase handling is absent.
- [ ] Add request rules:

```php
'phase' => ['nullable', 'in:analyze,compile'],
'sourceHash' => ['nullable', 'string', 'regex:/^[a-f0-9]{64}$/', 'required_if:phase,compile'],
'mapping' => ['nullable', 'string', 'json', 'max:524288', 'required_if:phase,compile'],
```

- [ ] Add request accessors `phase(): string` and `mappingPayload(): array`; `mappingPayload()` decodes JSON and returns an empty array only when mapping is absent outside Compile.
- [ ] Preserve backward compatibility: absent `phase` follows the current direct path for `Native`/`Exact Visual`; Compiled wizard always sends an explicit phase.
- [ ] Return structured 422 JSON for invalid phase/mapping without echoing raw mapping or source HTML.
- [ ] Run the focused test and require GREEN.
- [ ] Review checkpoint: confirm no controller, route, database, Save, Native, or Exact behavior changed in this task.

### Task 2: Detect ordered Header, Sections, and Footer

**Files:**

- Create: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageRegionAnalyzer.php`
- Create: `tests/Unit/PageBuilderElementorV24StaticPageRegionAnalyzerTest.php`
- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`

**Interfaces:**

- Consumes: sanitized marked `DOMDocument`, `sourceHash`, framework/report metadata.
- Produces: `analyze(DOMDocument $dom): array<int, array<string,mixed>>` using the Region contract.

- [ ] Write failing tests for exact ordering `header → section → section → footer`, stable marker IDs, source IDs, labels, and kind.
- [ ] Add a test proving nested `<section>` is a block of its top-level region and is not duplicated in the inventory.
- [ ] Add a test for pages without semantic tags: visible body direct children become ordered `section` regions; hidden/script/style/meta nodes are excluded.
- [ ] Add a test for multiple top-level headers/footers; label them `Header 1`, `Header 2`, `Footer 1`, and preserve source order.
- [ ] Run `php artisan test tests/Unit/PageBuilderElementorV24StaticPageRegionAnalyzerTest.php --colors=never` and verify RED.
- [ ] Implement region traversal restricted to body landmarks/direct children. Region IDs must be `region-{$marker}`.
- [ ] Add stats for elements, blocks, images, forms, and unsupported interactive elements without returning executable code.
- [ ] Reuse existing marker assignment and source sanitizer; do not add a second marker generator.
- [ ] Run focused analyzer and existing import service tests; require GREEN.
- [ ] Review checkpoint: compare detected CEO Masters order against `header`, nine body sections, and `footer` for 11 total regions.

### Task 3: Build block trees and widget recommendations

**Files:**

- Create: `app/Support/PageBuilderElementorV24/StaticImport/StaticImportWidgetCompatibility.php`
- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageRegionAnalyzer.php`
- Modify: `tests/Unit/PageBuilderElementorV24StaticPageRegionAnalyzerTest.php`
- Test: `tests/Feature/PageBuilderElementorV24ProductionModuleCatalogTest.php`

**Interfaces:**

- Consumes: region DOM subtree and `ModuleCatalog::all()`.
- Produces:

```php
roleFor(DOMElement $element): string
allowedWidgets(string $role): array
recommendedWidget(string $role, DOMElement $element): ?string
isCompatible(string $role, string $widgetType): bool
```

- [ ] Write failing tests for Hero structure: root layout → heading/text/button group/image blocks with parent/child IDs.
- [ ] Write failing tests mapping supported `<form>` to role/widget `form`, `<video>` to `video`, `<hr>` to `divider`, and repeated articles to `card_collection`.
- [ ] Write a test proving `<header>` and `<footer>` are regions whose root recommendation is Layout, not invented Header/Footer widget types.
- [ ] Write a test proving inactive/unknown catalog types are removed from `allowedWidgets`.
- [ ] Run analyzer/catalog tests and verify RED.
- [ ] Implement the compatibility table from this plan as the single source of truth; do not duplicate allowed-widget lists in JavaScript.
- [ ] Build stable block IDs as `block-{$marker}` and parent links from source markers.
- [ ] Use confidence values `0.00–1.00`; set `auto_native` only when every block has a registered recommendation and no blocking warning.
- [ ] Set unknown meaningful subtrees to `guided_native` with `unmapped-structure`; do not silently map them to empty Text Editor.
- [ ] Return allowed widget type IDs only; the client resolves labels/icons/categories from the existing `PB_ELEMENTOR_V24_MODULE_CATALOG`.
- [ ] Run focused tests and require GREEN.
- [ ] Review checkpoint: inspect one real Hero, cards, Form, Header, and Footer tree from CEO Masters.

### Task 4: Validate user mapping against the analyzed source

**Files:**

- Create: `app/Support/PageBuilderElementorV24/StaticImport/StaticImportMappingValidator.php`
- Create: `tests/Unit/PageBuilderElementorV24StaticImportMappingValidatorTest.php`
- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`

**Interfaces:**

- Consumes: `validate(array $analysis, array $mapping, string $actualSourceHash, string $submittedSourceHash): array`.
- Produces: normalized mapping with version `1`, region order preserved, and stable error codes.

- [ ] Write failing tests for source hash mismatch, unknown/duplicate region, unknown/duplicate block, incompatible widget, inactive widget, invalid strategy, and unresolved native block.
- [ ] Write tests proving `skip` and `exact_visual` require no block mappings, while `auto_native` ignores user block overrides and uses recommendations.
- [ ] Write a test proving a block cannot be assigned to a different region even if its marker exists elsewhere in the source.
- [ ] Run the validator test and verify RED.
- [ ] Implement validation without trusting client-provided labels, roles, allowed widgets, confidence, or DOM order.
- [ ] Normalize `guided_native` mappings in source tree order; reject extra properties rather than persisting them.
- [ ] Return safe diagnostics containing IDs/codes only; never include raw source HTML or JavaScript.
- [ ] Run validator and service tests; require GREEN.
- [ ] Review checkpoint: manually compare normalized mapping region order to analyzer output.

### Task 5: Add stateless Analyze and Compile orchestration

**Files:**

- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php`
- Modify: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`

**Interfaces:**

- Produces:

```php
analyze(UploadedFile $source, string $framework, ?string $entry, ?string $baseUrl): array
compile(UploadedFile $source, string $framework, ?string $entry, array $mapping, string $sourceHash, ?string $baseUrl): array
```

- [ ] Write a failing Analyze feature test asserting regions, blocks, previewPayload, sourceHash, frameworks, warnings, and no layout/custom CSS.
- [ ] Write a failing Compile feature test that resends the same source and a valid mapping, then asserts the response has layout, compilePayload, mappingReport, and no persistence.
- [ ] Add a hash-mismatch test by analyzing source A and compiling source B with source A's hash; require `source-hash-mismatch`.
- [ ] Add a mixed strategy test: Header native, Hero guided, About exact, Footer skipped; assert only selected regions appear and source order is preserved.
- [ ] Run feature tests and verify RED.
- [ ] Refactor common source reading/parsing only as far as needed so Analyze and Compile share one sanitizer/marker/hash path.
- [ ] Compile each region using normalized mapping; reuse current `mapNode`, native form mapping, compilePayload builder, exact srcdoc, and computed-style scanner contracts.
- [ ] Keep `exactFallback` page-level recovery for compiler failure, while `exact_visual` region strategy remains a deliberate user choice.
- [ ] Run focused feature/unit tests and require GREEN.
- [ ] Review checkpoint: verify no source/temp file remains on disk after either request.

### Task 6: Implement pure Guided Import client state

**Files:**

- Create: `public/js/pagebuilder_elementor_v24/static-import-guided.js`
- Create: `tests/pagebuilder-v24-guided-static-import.test.mjs`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Modify: `tests/pagebuilder-v24-baseline-isolation.test.mjs`

**Interfaces:**

- Consumes: Analyze response and existing browser module catalog.
- Produces: `window.PhoenixGuidedStaticImport` helpers from Client State Contract.

- [ ] Write failing Node tests for initial state, normalized recommendations, region selection, strategy changes, compatible block widget changes, validation errors, and compile mapping serialization.
- [ ] Add tests proving `sourceFile` is never included in `JSON.stringify(buildCompileMapping(state))` and mapping excludes labels/confidence/source HTML.
- [ ] Add cancellation/reset tests that clear analysis/mapping while preserving no page state.
- [ ] Run `node --test tests/pagebuilder-v24-guided-static-import.test.mjs` and verify RED.
- [ ] Implement the helper as a dependency-free IIFE with pure functions and no DOM/network access.
- [ ] Load it after compiler/native helpers and before `app.js` in the v2.4 editor shell only.
- [ ] Run guided/static baseline tests and `node --check public/js/pagebuilder_elementor_v24/static-import-guided.js`; require GREEN.
- [ ] Review checkpoint: confirm v2.3 and public frontend do not load this helper.

### Task 7: Build the Guided Mapping wizard UI

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Modify: `tests/pagebuilder-v24-guided-static-import.test.mjs`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`

**Interfaces:**

- Consumes: `PhoenixGuidedStaticImport` state and existing `toolbox`/module registry.
- Produces: modal state machine `analyzing → mapping → compiling → complete|failed`.

- [ ] Write failing source-contract tests for one modal with region list, active region detail, strategy select, mapping tree, validation summary, Back/Compile/Cancel buttons, and progress reuse.
- [ ] Write a test proving Analyze occurs before `rootNodes.value` or `customCss.value` can change.
- [ ] Build a three-column desktop wizard:
  - left: ordered Header/Section/Footer list with status/confidence/warning count;
  - center: sandboxed selected-region source preview;
  - right: strategy and collapsible mapping tree.
- [ ] At widths below 1024 px, stack region list above detail; do not introduce horizontal scrolling.
- [ ] Default all valid regions to `Use recommendations`; expose `Review mapping` only when a region is selected.
- [ ] Use native `<select>` for strategy/widget choices, `<button>` for region rows, and `aria-expanded` for block tree disclosure.
- [ ] Disable `Compile selected regions` while any native region has a blocking validation error; show linked error summary at the top of the active detail.
- [ ] Add visible loading/cancel feedback and restore focus to the Import trigger when the wizard closes.
- [ ] Keep `Exact Visual` and `Skip` as region-level choices; do not offer them as individual widget types.
- [ ] Run guided/static import Node tests and SFC/app syntax checks; require GREEN.
- [ ] Review checkpoint: keyboard-only pass through Analyze, region selection, mapping edit, validation, and Cancel.

### Task 8: Connect Analyze, mapping confirmation, and existing browser compiler

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/js/pagebuilder_elementor_v24/static-import-guided.js`
- Modify: `tests/pagebuilder-v24-guided-static-import.test.mjs`
- Modify: `tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php`

**Interfaces:**

- Consumes: selected `File`, analysis sourceHash, `buildCompileMapping()`, and current `hydrateCompiledStaticImport()`.
- Produces: compile request followed by existing browser CSS compile/scan/native hydration.

- [ ] Write failing tests proving `Compiled Native` file selection sends only Analyze first; `Native` and `Exact Visual` keep their direct one-request flow.
- [ ] Write a failing test proving Compile resends the same `File`, `sourceHash`, and mapping JSON.
- [ ] Write a test proving stale/replaced File blocks Compile and requires Re-analyze.
- [ ] Write a test proving rootNodes/customCss change only after server Compile and browser hydration both succeed.
- [ ] Run focused tests and verify RED.
- [ ] Route Compiled import through the guided state machine; preserve current `hydrateCompiledStaticImport()` as the final stage.
- [ ] Reuse existing compile progress stages after mapping confirmation; prepend `Analyzing regions` and `Validating mapping` to the wizard state only.
- [ ] On browser compiler cancellation/failure, keep the wizard mapping intact so the user can retry or choose Exact Visual per affected region.
- [ ] On successful hydration, close the wizard, mark draft dirty, retain mapping summary in report only, and do not save snapshot/source file.
- [ ] Run focused Node/PHP tests and require GREEN.
- [ ] Review checkpoint: inspect source to confirm no conditional branch changes manual/Native/Exact hydration.

### Task 9: Complete error handling and report output

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Modify: `tests/pagebuilder-v24-guided-static-import.test.mjs`
- Modify: `tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php`

**Interfaces:**

- Consumes: server safe diagnostics and browser compiler/scanner errors.
- Produces: region/block error display and final `mappingReport` summary.

- [ ] Add tests for analyze network failure, invalid mapping, source hash mismatch, inactive widget after analysis, missing asset warning, compiler timeout, and user cancellation.
- [ ] Display errors at region/block level and in one accessible summary; do not silently force page-level Exact Visual.
- [ ] Add `Re-analyze source` for source hash/inactive catalog errors; it must reset mapping because IDs/recommendations may change.
- [ ] Include counts in Import Report: detected regions, native/exact/skipped regions, mapped/unmapped blocks, relative assets, and source scripts detected.
- [ ] Keep raw mapping/source HTML out of toast messages and report download unless the existing JSON export explicitly includes final native layout.
- [ ] Run focused tests and require GREEN.
- [ ] Review checkpoint: verify Cancel and every failure leave the pre-import draft byte-for-byte unchanged.

### Task 10: Real fixture QA, regression, and release gate

**Files:**

- Create: `project-artifacts/qa/pagebuilder-v24-guided-static-import-20260829/QA_REPORT.md`
- Create/update scoped fixture files only inside that QA folder.
- Verify all files listed above.

- [ ] Run CEO Masters Analyze and record exactly 11 ordered regions: Header, nine Sections, Footer.
- [ ] Verify the Contact region recommends native `form` with eight fields and no placeholder.
- [ ] Verify the About horizontal snap region recommends `guided_native` with `card_collection` and offers `container`, `grid`, and `carousel`; it must not auto-select Exact Visual.
- [ ] Change at least one safe block mapping in the wizard, compile, and confirm the selected widget type appears in final draft JSON.
- [ ] Verify Header/Footer support `auto_native`, `guided_native`, `exact_visual`, and `skip` while preserving source order.
- [ ] Verify missing relative assets show warnings and remain editable image nodes when Base URL is absent; repeat with a valid Base URL and confirm direct HTTP(S) URLs.
- [ ] Run browser QA at 390, 768, and 1180 px. Record no normal-section horizontal overflow, no unintended sibling overlap, zero compiler iframes after cleanup, and clean mapping validation state.
- [ ] Do not click Save, Reset, Preview, Apply Dataset, submit forms, or execute copied source JavaScript during QA.
- [ ] Run:

```powershell
node --test tests/pagebuilder-v24-guided-static-import.test.mjs tests/pagebuilder-v24-static-import-compiler.test.mjs tests/pagebuilder-v24-static-import-native.test.mjs tests/pagebuilder-v24-static-import.test.mjs
node --test tests/pagebuilder-v24-*.test.mjs
php artisan test tests/Unit/PageBuilderElementorV24StaticPageRegionAnalyzerTest.php tests/Unit/PageBuilderElementorV24StaticImportMappingValidatorTest.php tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php tests/Feature/PageBuilderElementorV24GuidedStaticImportTest.php tests/Feature/PageBuilderElementorV24StaticImportCompiledFlowTest.php tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php --colors=never
node --check public/js/pagebuilder_elementor_v24/static-import-guided.js
node --check public/js/pagebuilder_elementor_v24/static-import-compiler.js
node --check public/js/pagebuilder_elementor_v24/static-import-native.js
node --check public/js/pagebuilder_elementor_v24/app.js
php -l app/Support/PageBuilderElementorV24/StaticImport/StaticPageRegionAnalyzer.php
php -l app/Support/PageBuilderElementorV24/StaticImport/StaticImportWidgetCompatibility.php
php -l app/Support/PageBuilderElementorV24/StaticImport/StaticImportMappingValidator.php
php -l app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php
php artisan view:cache
git diff --check
```

- [ ] Run `php artisan test --filter=PageBuilderElementorV24 --colors=never`; classify any existing POST-CSRF `419` baseline separately and require the guided/import targeted slice to remain fully green.
- [ ] Review `git status --short`; preserve all unrelated modifications/backups and confirm zero tracked deletions.
- [ ] Run `graphify update . --no-cluster`, then query `StaticPageRegionAnalyzer`, `StaticImportMappingValidator`, `PhoenixGuidedStaticImport`, and `hydrateCompiledStaticImport`.
- [ ] Do not commit, merge, push, deploy, or remove backups without separate authorization.

---

## Acceptance Criteria

1. Compiled Native always completes Analyze before any layout extraction or draft mutation.
2. Header, top-level Sections, and Footer appear in correct source order with stable IDs and no duplicated nested section content.
3. Each region receives a recommended nested widget tree, not one widget for an entire complex section.
4. Users can select region strategy and change only compatible block widgets through a progressively disclosed mapping tree.
5. Compile rejects stale source, unknown IDs, incompatible/inactive widgets, and unresolved native blocks before Canvas mutation.
6. Analyze/Compile remain stateless and require no DB migration, server temp source, cleanup job, or File Manager change.
7. Existing browser compiler/scanner/native hydration remains the final CSS/geometry stage after mapping approval.
8. Manual, Native, Exact Visual, Save, drag/drop, responsive engine, and frontend output remain unchanged for pages without Guided Compiled metadata.
9. Header/Footer can be native, guided, exact, or skipped independently.
10. A mixed-strategy page preserves region order and compiles only selected regions.
11. Source scripts never execute during Analyze, preview, mapping, Compile, or browser QA.
12. Focused tests, full Node regression, syntax/view checks, browser geometry QA, and Graphify update are recorded with honest limitations.

## Rollback

- Restore only the guided-import slice from timestamped backups.
- Remove the `phase=analyze|compile` branches and guided helper script loader to return Compiled Native to the existing one-request workflow.
- Keep current compiler/scanner/native helper files and existing Compiled Native behavior intact; no DB rollback is required.
- If mapping validation fails in production, disable the Guided Compiled trigger and retain Native/Exact/manual paths while investigating.

## Self-Review Checklist

- [x] Scope is one isolated Guided Compiled Native subsystem, not multiple independent products.
- [x] Analyze, mapping review, and Compile have distinct contracts and ownership.
- [x] Automatic recommendation plus per-block correction is explicit.
- [x] Header, Sections, Footer, mixed strategies, source order, and nested tree behavior are concrete.
- [x] Stateless source handling, source hash verification, and 512 KB mapping limit are specified.
- [x] Compatibility derives from ModuleCatalog and one server-side role table.
- [x] Manual/Native/Exact/responsive/Save boundaries are protected.
- [x] Every task includes focused RED/GREEN checks and exact commands.
- [x] No database migration, File Manager V2, source execution, commit, push, deploy, or auto-save is included.
- [x] Placeholder scan contains no unresolved implementation placeholders.
