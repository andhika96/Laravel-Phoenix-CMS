# Page Builder v2.4 Compiled Native Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menambahkan mode `Compiled Native` yang mengubah static HTML Tailwind/Bootstrap menjadi widget native v2.4, menyimpan CSS hasil compile di Custom CSS, dan menghapus dependency/class framework dari output final.

**Architecture:** Server tetap melakukan parsing, sanitasi, deteksi framework, dan mapping native. Browser menjalankan compiler sementara di iframe tersembunyi untuk membaca DOM/class sumber dan mengambil CSS hasil Tailwind/Bootstrap, kemudian compiler client menulis ulang selector ke `data-pb-import-node`. Setelah CSS selesai, iframe compiler dibuang; output yang disimpan hanya layout native dan blok generated CSS ter-scope. Jika compile gagal, modal menawarkan `Exact Visual` menggunakan widget `static_html` yang sudah tersedia.

**Tech Stack:** Laravel 13, PHP `DOMDocument`, Vue 3 runtime editor, Tailwind CDN hanya pada iframe compiler sementara, CSSOM, `postMessage`, existing v2.4 ModuleCatalog, existing page `custom_css` column, PHPUnit, Node `node:test`, browser QA read-only.

**Spec:** `project-artifacts/plans/2026-08-28-pagebuilder-v24-static-html-preview-design.md`, keputusan Compiled Native pada percakapan 2026-08-28.

## Global Constraints

- Tidak melakukan restore atau menghapus converter native yang sudah ada.
- `native` tetap menjadi default API untuk backward compatibility; `exact` tetap tersedia sebagai fallback; `compiled` menjadi mode baru.
- Tidak menambahkan Tailwind/Bootstrap CDN ke output final Compiled Native.
- Output final tidak menyimpan class Tailwind/Bootstrap sebagai class visual; class sumber hanya boleh dipakai di payload compile sementara.
- Generated CSS disimpan di field `custom_css` dalam satu blok bertanda `PHOENIX_STATIC_IMPORT_COMPILED_START/END`.
- CSS manual pengguna di luar blok generated wajib dipertahankan byte-for-byte semampu normalizer yang sudah ada.
- `--tw-*` harus diganti namespace menjadi `--pb-import-*`; CSS generated tidak boleh mengandung marker `tailwind`, `bootstrap`, `cdn.tailwindcss`, atau `--tw-`.
- Framework compiler hanya berjalan di iframe sementara yang dibuat selama import, tidak di editor utama atau frontend final.
- Source script arbitrary tidak dijalankan pada mode Compiled Native.
- Gambar URL absolut dipertahankan; gambar/path relatif tetap masuk report unresolved sampai Base URL/File Manager direncanakan terpisah.
- Halaman manual tanpa metadata static import harus tetap no-op.
- Tidak mengubah v2.3, File Manager V2, database schema, atau persistence flow selain yang secara eksplisit ada di scope ini.
- Tidak auto-save, commit, push, deploy, atau menjalankan browser action berisiko.

## Existing Source Map

- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php` — parser, detector, native layout mapping, exact fallback.
- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageCssProcessor.php` — sanitizer/scoper CSS dan Tailwind theme extraction.
- `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php` — validation source/framework/mode.
- `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php` — import endpoint dan Save/Update payload.
- `public/js/pagebuilder_elementor_v24/app.js` — import state, toolbar, Canvas hydration, Custom CSS editor, dirty state.
- `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php` — module catalog and editor asset loading.
- `app/Support/PageBuilderElementorV24/ModuleCatalog.php` — manifest discovery; do not expose compiler as toolbox module.
- `app/Support/PageBuilderElementorV24/WidgetAdvancedStyleResolver.php` — shared advanced root/class/attribute output.
- `resources/pagebuilder_elementor_v24/modules/layout/{container,container-fluid,grid,row-grid}/frontend.blade.php` — layout frontend roots.
- `resources/pagebuilder_elementor_v24/modules/widgets/basic/{heading,text-editor,image,button,icon,divider}/` — supported native import roots.
- `resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php` — final frontend CSS injection; must not inject framework compiler assets for compiled metadata.
- Existing tests: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`, `tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php`, `tests/pagebuilder-v24-static-import.test.mjs`, and full `tests/pagebuilder-v24-*.test.mjs`.

## Contracts to Add

### Import response contract

`StaticPageImportService::convert($source, $framework = 'auto', $entry = null, $mode = 'native')` accepts `compiled` and returns the existing result shape plus:

```php
[
    'mode' => 'compiled',
    'layout' => [...],
    'customCss' => '',
    'compilePayload' => [
        'html' => '<sanitized source document with marker attributes>',
        'frameworks' => ['tailwind'],
        'stylesheets' => [...],
        'tailwindConfig' => [...],
        'classMap' => [
            'lg:text-[6.2rem]' => ['import-node-12'],
        ],
        'markerMap' => [
            'import-node-12' => 'native-node-id',
        ],
    ],
    'exactFallback' => [...],
]
```

The response must not contain arbitrary source scripts. `html` is only a compile document with sanitized markup/styles and an allowlisted dependency description.

### Native marker contract

- Every supported native import root receives `settings.importNodeKey`, a stable safe token.
- Native Canvas and frontend roots emit `data-pb-import-node="..."` when that setting is present.
- Compiled nodes do not receive source `cssClass` visual tokens; their marker is the CSS target.
- Manual nodes without `importNodeKey` render exactly as before.
- Nested rich-text elements that had source utility classes receive the marker inside sanitized HTML where the parser can preserve their semantic location.

### Generated CSS contract

```css
/* PHOENIX_STATIC_IMPORT_COMPILED_START */
/* generatedBy: browser-utility-compiler */
/* sourceHash: ... */
.pb-import-root [data-pb-import-node="import-node-12"] { ... }
/* PHOENIX_STATIC_IMPORT_COMPILED_END */
```

Only this block is replaceable by a later compile. User CSS before/after it is not deleted.

### Client compiler contract

Create `window.PhoenixStaticImportCompiler.compile(payload, options)` in a dedicated browser helper. It returns:

```js
Promise<{
  css: string,
  warnings: string[],
  stats: { sourceClasses: number, generatedRules: number, rewrittenRules: number, droppedRules: number }
}>
```

`options.onProgress(stage)` reports `prepare`, `load-framework`, `compile`, `extract`, `rewrite`, `validate`, and `cleanup`. `options.signal` cancels the temporary iframe and rejects with a typed cancellation error.

## Implementation Tasks

### Task 1: Add failing tests for the compiled response and generated CSS contract

**Files:**
- Modify: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`
- Modify: `tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/Unit/PageBuilderElementorV24StaticImportCompiledCssBlockTest.php`

**Steps:**

- [x] Add a RED test asserting `mode=compiled` returns `compilePayload`, `importNodeKey`, `exactFallback`, and a native layout.
- [x] Assert compiled layout has no Tailwind visual `cssClass`, while native mode keeps the current behavior.
- [x] Assert generated CSS block replacement preserves user CSS and does not duplicate the markers.
- [x] Assert final compiled CSS rejects `tailwind`, `bootstrap`, `cdn.tailwindcss`, and `--tw-` markers.
- [x] Assert manual frontend rendering with no compiled metadata does not emit compiler assets.
- [x] Run the focused tests and record the expected failure in `project-artifacts/qa/pagebuilder-v24-static-import-20260828/`.

**Done when:** Tests fail only because compiled response/block/compiler contracts do not exist yet.

### Task 2: Add source marker and compile-payload generation on the server

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
- Modify: `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Test: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`

**Steps:**

- [x] Extend the accepted mode enum to `native`, `exact`, and `compiled`; keep invalid values on the safe native path or return validation error according to the existing request contract.
- [x] Add a source-marker pass that assigns only safe `import-node-*` tokens to supported source elements before native mapping.
- [x] Build `classMap` from complete class tokens, including responsive/state/arbitrary tokens, without evaluating class content.
- [x] Build `markerMap` from source marker to native node ID and preserve marker identity through nested rich-text sanitization.
- [x] Build a compile document that contains sanitized source DOM, source attributes, source `<style>` content, allowlisted stylesheet metadata, and Tailwind config, but no arbitrary script.
- [x] Return an exact fallback payload using the existing `static_html` path so a compile failure does not require a second upload.
- [x] Make compiled native settings omit source visual classes while retaining `importNodeKey`, source ID metadata where safe, and asset warnings.
- [x] Add report fields `compileEligibleNodes`, `compileSourceClasses`, `compileWarnings`, and `relativeAssets` without changing existing report meanings.
- [x] Run focused PHPUnit tests.

**Done when:** The server returns deterministic, sanitized compile input and native marker metadata without executing source code.

### Task 3: Implement the temporary browser compiler helper

**Files:**
- Create: `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/pagebuilder-v24-static-import-compiler.test.mjs`

**Steps:**

- [x] Implement an ephemeral hidden iframe creator with `sandbox="allow-scripts"`; never attach source arbitrary scripts.
- [x] Create the compile document with marker-bearing source DOM, allowlisted Google Fonts, and the selected framework assets.
- [x] Set Tailwind configuration before loading the pinned/project-approved CDN loader; preserve `preflight: false` only if needed to avoid editor chrome contamination.
- [x] For Bootstrap CSS, fetch only the fixed allowlisted Bootstrap 5.3.3 URL, inject the returned text into an inline `<style>`, and fail closed if fetching is unavailable.
- [x] Wait for a compiler-ready signal with a bounded timeout and cancellation signal.
- [x] Extract generated inline stylesheet text through CSSOM/text content; ignore unrelated compiler/editor styles.
- [x] Rewrite class selectors to `.pb-import-root [data-pb-import-node="..."]` using the server `classMap`, preserving media queries, pseudo-classes, compound selectors, and comma lists.
- [x] Rewrite safe source ID selectors to marker selectors where native output no longer has the source DOM ID.
- [x] Rename `--tw-*` declarations/references to `--pb-import-*` and strip framework comments/URLs from generated CSS.
- [x] Merge sanitized source custom CSS through the same marker rewrite path.
- [x] Validate rule count, forbidden marker strings, CSS size limit, and balanced braces before returning CSS.
- [x] Always remove the iframe/listeners in `finally`, including timeout and cancel paths.
- [x] Add unit-like Node tests with mocked CSS rules and a browser-level compiler smoke test fixture.

**Done when:** The helper returns ordinary scoped CSS and leaves no compiler iframe or framework script behind.

### Task 4: Add the generated CSS block manager

**Files:**
- Create: `public/js/pagebuilder_elementor_v24/static-import-css.js`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/pagebuilder-v24-static-import-css.test.mjs`

**Steps:**

- [x] Implement `replaceGeneratedStaticImportCss(existingCss, generatedCss, metadata)` with exact start/end marker constants.
- [x] Preserve user CSS outside the generated block.
- [x] Replace an existing generated block once, even when the user re-imports or compiles repeatedly.
- [x] Return generated CSS statistics for the modal and page settings summary.
- [x] Keep generated CSS visible through the existing Custom CSS editor; add a generated-block badge/legend rather than a second storage field.
- [x] Add warning state when the existing generated block hash differs from its stored metadata before replacement.
- [x] Add Node tests for empty CSS, one block, duplicate blocks, malformed markers, and user CSS preservation.

**Done when:** Recompile is idempotent and does not destroy manual CSS.

### Task 5: Add import modal progress UX and mode integration

**Files:**
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php` only if helper load order requires it
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Create: `tests/Feature/PageBuilderElementorV24StaticImportCompiledFlowTest.php`

**Steps:**

- [x] Add toolbar mode option `Compiled Native` while retaining `Exact Visual` and `Editable Native` labels.
- [x] Add a modal with stage indicator, progress bar, current action, counts, warning area, cancel button, and close behavior.
- [x] Show stages: `Reading source`, `Detecting framework`, `Compiling framework CSS`, `Extracting CSS`, `Rewriting selectors`, `Mapping widgets`, `Validating output`, `Completed`.
- [x] Disable duplicate import actions while compiling and restore focus to the modal after each stage.
- [x] On success, hydrate native layout, merge generated CSS into `customCss`, store compile report/payload state, and mark page dirty without Save.
- [x] On failure, show `Use Exact Visual` and `Cancel`; fallback must use the already-returned sanitized `exactFallback` and never execute source scripts.
- [x] On cancel, remove temporary compiler resources and leave the previous Canvas/custom CSS state unchanged.
- [x] Add accessible `role="dialog"`, `aria-live` progress messaging, visible focus ring, keyboard Escape handling, and no layout shift from the modal.
- [x] Add CSS editor summary `Generated CSS · N chars` when a generated block exists.
- [x] Add feature tests for success payload, failure fallback, cancel contract, and no auto-save.

**Done when:** The user can observe, cancel, complete, or safely fallback from compilation without losing the previous draft.

### Task 6: Render marker attributes across Canvas and frontend

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/WidgetAdvancedStyleResolver.php`
- Modify: `resources/pagebuilder_elementor_v24/modules/layout/container/frontend.blade.php`
- Modify: `resources/pagebuilder_elementor_v24/modules/layout/container-fluid/frontend.blade.php`
- Modify: `resources/pagebuilder_elementor_v24/modules/layout/grid/frontend.blade.php`
- Modify: `resources/pagebuilder_elementor_v24/modules/layout/row-grid/frontend.blade.php`
- Modify: supported widget Canvas/frontends under `resources/pagebuilder_elementor_v24/modules/widgets/basic/`
- Modify: `public/js/pagebuilder_elementor_v24/app.js` BuilderNode shell only if Canvas marker cannot reach semantic widget root
- Test: existing frontend/parser and module compile suites

**Steps:**

- [x] Add marker output only when `settings.importNodeKey` passes a strict safe-token validator.
- [x] Ensure compiled CSS applies in editor Canvas and final frontend using the same `.pb-import-root` scope.
- [x] Keep source classes available only in compile document; do not reintroduce them to manual DOM.
- [x] Keep existing `cssId`, `cssClass`, Advanced attributes, motion, and responsive behavior unchanged for nodes without import markers.
- [x] Verify text editor nested markers do not permit event attributes or unsafe URLs.
- [x] Add regression assertions that manual Button, Heading, Image, and Container output is byte/behavior compatible apart from expected marker code paths.

**Done when:** The same generated CSS targets the native editor preview and public frontend, while manual nodes remain unchanged.

### Task 7: Final verification and artifact evidence

**Files:**
- Create: `project-artifacts/qa/pagebuilder-v24-static-import-20260828/QA_REPORT_08-compiled-native-20260828.md`
- Modify: no production files beyond the tasks above

**Steps:**

- [x] Run focused compiled-native PHPUnit and Node tests.
- [x] Run `node --test tests/pagebuilder-v24-*.test.mjs` and record the full count: 423/423.
- [x] Run `php artisan test --filter=PageBuilderElementorV24`; classify all failures by status and do not hide existing 419 CSRF harness failures.
- [x] Run PHP/Blade/JS syntax checks, `git diff --check`, and view cache/clear checks if affected.
- [x] Probe `E:\Apps\Laragon\www\ceo-masters\index.html` in `compiled` mode and assert no final Tailwind/Bootstrap CDN/class marker remains in layout/custom CSS.
- [x] Verify responsive media-query preservation and compiler smoke output; full authenticated desktop/tablet/mobile overflow comparison remains unrun because no controllable authenticated builder session was available.
- [x] Verify manual page render has no compiler script/style and manual Button/Heading settings still use old defaults.
- [x] Run authenticated browser QA only if an existing session is available; no controllable authenticated session was available, so the local read-only compiler fixture was used.
- [x] Update Graphify incrementally, excluding backups, QA, generated output, and secrets.
- [x] Review `git status --short`; preserve all user changes and timestamped backups; do not commit/push/deploy.

**Done when:** Compiled Native is evidenced in editor/frontend/fixture tests, manual isolation is proven, and limitations are documented honestly.

## Rollback

Rollback is file-level and recoverable: preserve the pre-change backups, remove only the compiled-mode branch/helper/module changes after review approval, and leave `native`, `exact`, existing `static_html`, and existing generated CSS/user CSS data intact. Do not run `git reset --hard` or delete backups.

## Explicit Non-Goals

- Executing source JavaScript.
- Uploading relative assets to File Manager V2.
- Resolving local Windows paths automatically.
- Rebuilding Tailwind through Vite/PostCSS as a project-wide dependency.
- Guaranteeing semantic equivalence for arbitrary JS interactions or third-party plugins.
