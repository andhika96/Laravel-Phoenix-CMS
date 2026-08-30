# Page Builder v2.4 Section Computed-Style Scanner Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` or `superpowers:executing-plans` to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengubah `Compiled Native` menjadi proses import berbasis section dan computed style browser sehingga padding, margin, ukuran, layout, typography, border, warna, dan responsive source dipindahkan ke setting widget native dalam satuan pixel/CSS yang terukur.

**Architecture:** Browser compiler tetap memakai iframe sementara yang terisolasi, tetapi tidak berhenti pada ekstraksi CSS. Iframe akan merender source HTML dengan framework aktif, memindai section dan setiap node bertanda `data-pb-import-node`, lalu mengirim snapshot geometry/computed-style per viewport ke editor. Snapshot tersebut digunakan untuk mengisi setting native; CSS generated hanya menyimpan residual rule yang tidak mempunyai padanan setting native. Section yang tidak aman atau tidak dapat direpresentasikan tetap memakai fallback `static_html`/hybrid tanpa merusak section lain.

**Tech Stack:** Laravel 13, PHP `DOMDocument`, Vue 3, browser CSSOM, `getBoundingClientRect()`, `document.fonts.ready`, `HTMLImageElement.decode()`, `postMessage`, existing v2.4 module registry, PHPUnit, Node `node:test`, Graphify, browser QA read-only.

**Spec:** `project-artifacts/plans/2026-08-28-pagebuilder-v24-static-import-styling-semantic-design.md`, `project-artifacts/plans/2026-08-28-pagebuilder-v24-static-import-canonical-grid-design.md`, dan keputusan computed-style scanner pada percakapan 2026-08-29.

## Global Constraints

- Hanya mode `Compiled Native` yang berubah; mode manual, `Native`, `Exact Visual`, dan responsive engine utama tetap memakai jalur lama.
- Setiap imported Container menerima nilai spacing eksplisit dari snapshot; jika source tidak memiliki spacing, nilainya harus `0px`, bukan default Container manual `1rem`.
- Snapshot computed style bersifat ephemeral dan tidak disimpan sebagai JSON page; yang disimpan hanya layout native, residual generated CSS, dan metadata import minimum.
- Scanner harus mengukur source setelah framework CSS, source `<style>`, font, dan gambar selesai diproses.
- Scanner tidak boleh mengeksekusi script source, inline event handler, form submit, `iframe`, `object`, atau `embed` source.
- Responsive diukur sekurangnya pada `390px` mobile, `768px` tablet, dan lebar desktop Canvas yang dipilih; media query source tetap menjadi input observasi, bukan sumber layout final yang ditempel mentah.
- Asset relatif hanya di-resolve bila ada Base URL yang aman; File Manager V2/ZIP asset ingestion tetap di luar scope plan ini.
- CSS residual harus tetap scoped pada imported root/marker, tidak boleh memengaruhi editor shell atau node manual.
- Tidak mengubah Page Builder v2.3, module global, drag/drop, legacy normalizer, database schema, Save contract, atau frontend renderer manual.
- Tidak auto-save, commit, push, deploy, atau menghapus backup historis.
- Setiap existing file yang dimodifikasi wajib dibackup timestamp sebelum edit.

## Problem Evidence

Current source path:

- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php` memetakan DOM source menjadi Container/Grid/widget native.
- `public/js/pagebuilder_elementor_v24/static-import-compiler.js` saat ini mengambil CSS framework dan me-rewrite selector ke marker.
- `public/js/pagebuilder_elementor_v24/app.js` meng-hydrate layout native dan menempel generated CSS ke editor.
- `resources/pagebuilder_elementor_v24/modules/layout/container/definition.js` memiliki default padding `1rem` untuk Container manual.

Observed failure:

- Source `body` tidak meminta padding, tetapi imported Container dapat tampil dengan padding default `1rem`.
- Source CSS diterapkan ke DOM native yang berbeda sehingga selector descendant, `nth-child`, pseudo-element, dan geometry tidak lagi identik.
- Source typography/warna dapat kalah oleh inline style widget native.
- `snap-track`/`min-w-[78vw]` dapat berubah menjadi Grid native yang tidak sesuai.

## File Map

### Existing files to modify

- `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
  - Tambah lifecycle scan, viewport protocol, CSSOM snapshot, asset readiness, dan residual CSS filtering.
- `public/js/pagebuilder_elementor_v24/app.js`
  - Jalankan scan sebelum `norm()`/hydrate Compiled Native, apply snapshot ke native layout, dan perluas progress modal.
- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
  - Tambah section metadata, zero-default compiled containers, source tag/marker metadata, dan fallback classification.
- `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`
  - Validasi optional `baseUrl` hanya jika fitur Base URL diaktifkan pada request; tidak menerima arbitrary executable URL.
- `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
  - Pastikan helper scanner/compiler dimuat sebelum app dan version query berubah secara deterministik bila helper berubah.
- `resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php`
  - Tetap tidak memuat compiler/scanner; hanya verifikasi agar metadata compiled tidak mengaktifkan framework CDN.

### Existing widget files to verify or modify narrowly

- `resources/pagebuilder_elementor_v24/modules/layout/container/definition.js`
- `resources/pagebuilder_elementor_v24/modules/layout/container-fluid/definition.js`
- `resources/pagebuilder_elementor_v24/modules/layout/grid/definition.js`
- `resources/pagebuilder_elementor_v24/modules/layout/row-grid/definition.js`
- `resources/pagebuilder_elementor_v24/modules/widgets/basic/{heading,text-editor,image,button,icon,divider}/`

Perubahan pada widget hanya diperlukan bila field native yang sudah ada belum dapat menerima nilai snapshot. Jangan mengubah default manual; gunakan metadata/normalization khusus compiled import.

### Tests and artifacts

- `tests/pagebuilder-v24-static-import-compiler.test.mjs`
- `tests/pagebuilder-v24-static-import.test.mjs`
- `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`
- `tests/Feature/PageBuilderElementorV24StaticImportCompiledFlowTest.php`
- Create: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`
- Create: `project-artifacts/qa/pagebuilder-v24-static-import-20260828/QA_REPORT_12-computed-style-scanner-20260829.md`

## Contracts

### Scanner API

Add this browser API to `window.PhoenixStaticImportCompiler`:

```js
await PhoenixStaticImportCompiler.scanComputedStyles(payload, {
  viewports: [
    { key: 'mobile', width: 390, height: 900 },
    { key: 'tablet', width: 768, height: 1024 },
    { key: 'desktop', width: 1180, height: 900 },
  ],
  signal,
  onProgress,
});
```

Return:

```js
{
  sections: [
    {
      marker: 'import-node-33',
      tag: 'section',
      sourceId: 'home',
      order: 0,
      bounds: {
        mobile: { x: 0, y: 0, width: 390, height: 1240 },
        tablet: { x: 0, y: 0, width: 768, height: 980 },
        desktop: { x: 0, y: 0, width: 1180, height: 820 },
      },
      fallback: false,
    },
  ],
  nodes: [
    {
      marker: 'import-node-34',
      parentMarker: 'import-node-33',
      tag: 'div',
      id: '',
      classes: ['mx-auto', 'grid'],
      text: '',
      bounds: { mobile: {}, tablet: {}, desktop: {} },
      computed: { mobile: {}, tablet: {}, desktop: {} },
      pseudo: { mobile: {}, tablet: {}, desktop: {} },
      assets: [],
      interaction: { sourceEvents: false, sourceScripts: false },
    },
  ],
  warnings: [],
  stats: {
    sections: 9,
    nodes: 351,
    measuredNodes: 351,
    mappedNodes: 301,
    fallbackSections: 0,
    missingAssets: 8,
  },
}
```

### Computed style allowlist

The scanner must serialize only these CSSOM fields:

```js
const COMPUTED_STYLE_FIELDS = [
  'display', 'position', 'inset', 'top', 'right', 'bottom', 'left', 'zIndex',
  'width', 'minWidth', 'maxWidth', 'height', 'minHeight', 'maxHeight',
  'boxSizing', 'flex', 'flexBasis', 'flexGrow', 'flexShrink', 'flexDirection',
  'flexWrap', 'gridTemplateColumns', 'gridTemplateRows', 'gridAutoFlow',
  'gap', 'rowGap', 'columnGap', 'alignItems', 'alignContent', 'alignSelf',
  'justifyContent', 'justifyItems', 'justifySelf', 'order',
  'marginTop', 'marginRight', 'marginBottom', 'marginLeft',
  'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft',
  'borderTopWidth', 'borderRightWidth', 'borderBottomWidth', 'borderLeftWidth',
  'borderTopStyle', 'borderRightStyle', 'borderBottomStyle', 'borderLeftStyle',
  'borderTopColor', 'borderRightColor', 'borderBottomColor', 'borderLeftColor',
  'borderTopLeftRadius', 'borderTopRightRadius', 'borderBottomRightRadius', 'borderBottomLeftRadius',
  'fontFamily', 'fontSize', 'fontWeight', 'fontStyle', 'lineHeight',
  'letterSpacing', 'wordSpacing', 'textAlign', 'textTransform', 'textDecoration',
  'color', 'backgroundColor', 'backgroundImage', 'backgroundSize', 'backgroundPosition',
  'backgroundRepeat', 'backgroundBlendMode', 'opacity', 'boxShadow', 'filter',
  'objectFit', 'objectPosition', 'overflow', 'overflowX', 'overflowY',
  'scrollSnapType', 'scrollSnapAlign', 'whiteSpace',
];
```

Values must be normalized to safe strings. Do not serialize event handlers, arbitrary script, CSS URLs that fail the existing URL policy, or unbounded computed values.

## Native Mapping Rules

The client must apply snapshots using `compilePayload.markerMap`, never by DOM index:

- `container`/`container_fluid`: map box model, width, min-height, position, display, direction, wrap, alignment, grid tracks, and gaps into existing responsive settings.
- `grid`/`row_grid`: preserve canonical columns; use measured track count and measured gaps. A source horizontal snap strip must remain a horizontal flex/scroll structure and must not be converted to a five-column grid.
- `heading`: map computed font family, font size, weight, line-height, letter/word spacing, color, alignment, transform, decoration, and safe text shadow into the existing heading settings.
- `text_editor`: map computed typography, color, alignment, line-height, and paragraph spacing into existing text editor settings.
- `image`: map source URL/alt plus computed width/height, object fit, object position, border, radius, opacity, and parent geometry.
- `button`: map computed display, dimensions, padding, border, radius, color, background, typography, and safe icon placement into existing button settings.
- `icon`: map recognized source icon to the existing icon widget and preserve measured size/color/alignment.
- `divider`: map measured width, thickness, color, and spacing.
- Unknown elements with meaningful structure must stay in a section fallback instead of becoming empty text editors.

All responsive values must be written to the corresponding native suffix (`Mobile`, `Tablet`, desktop base). A value observed as `0px` must be stored explicitly where the native default could otherwise change geometry.

## CSS Ownership Rules

- Computed snapshot owns properties that are mapped to native settings.
- Native layout owns structure: display, width, height, flex/grid, gap, margin, padding, overflow, position, and scroll behavior when represented by settings.
- Residual CSS owns only unsupported visual/pseudo/state rules and must target markers/section roots.
- Remove the current broad visual `!important` strategy once snapshot-based native styles are active; retain `!important` only where a residual rule must intentionally beat a known native inline value and has a marker-scoped test.
- Source selectors such as `#partners > div > figure` must not be blindly rewritten when the native DOM cannot preserve the selector path. Convert their computed effect to the target node or assign the section fallback.

## Implementation Tasks

### Task 1: Lock the snapshot and zero-default contracts

**Files:**

- Modify: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`
- Modify: `tests/Feature/PageBuilderElementorV24StaticImportCompiledFlowTest.php`
- Create: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`

- [x] Write a failing PHPUnit test asserting compiled imported Containers explicitly carry zero padding when the source wrapper has no padding class.
- [x] Write a failing PHPUnit test asserting source `px-5 py-16` metadata is retained for scanner mapping and does not overwrite manual Container defaults.
- [x] Write a failing feature test asserting the compiled response contains `sections`, `compilePayload.markerMap`, scanner viewport keys, and exact fallback.
- [x] Run:

```powershell
php artisan test tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php tests/Feature/PageBuilderElementorV24StaticImportCompiledFlowTest.php tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php --colors=never
```

- [x] Expected RED: the new scanner metadata and compiled zero-default contract are absent.
- [x] Implement the smallest server metadata/default change in `StaticPageImportService.php`.
- [x] Run the same command and require all focused tests to pass.

### Task 2: Add isolated browser viewport scanning

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
- Modify: `tests/pagebuilder-v24-static-import-compiler.test.mjs`

**Interface:** `scanComputedStyles(payload, options)` returns the snapshot contract above and uses the existing abort/cleanup lifecycle.

- [x] Write failing Node tests for `serializeComputedStyle(style)`, `serializeBounds(rect)`, `resolveViewportWidth()`, and malformed scanner responses.
- [x] Write a failing test asserting source scripts are never copied into scanner `srcdoc`.
- [x] Write a failing test asserting scanner cancellation removes its iframe and rejects with `scan-cancelled`.
- [x] Implement a scanner message protocol with request IDs, viewport switching, `document.fonts.ready`, image decode timeout, and bounded node count.
- [x] Build section detection from semantic tags plus body direct-child fallback; preserve parent/child marker relationships.
- [x] Serialize `getComputedStyle()` only through the allowlist and collect `::before`/`::after` content/style safely.
- [x] Run:

```powershell
node --test tests/pagebuilder-v24-static-import-compiler.test.mjs
```

- [x] Require scanner unit tests, existing compiler tests, and cancellation tests to pass.

### Task 3: Map snapshots into native settings before Canvas hydration

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `resources/pagebuilder_elementor_v24/modules/layout/container/definition.js`
- Modify narrowly: supported v2.4 widget definitions only when a missing field is proven.
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Modify: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`

- [x] Write a failing Node test for marker-to-native-node traversal through `children`, `columns[].children`, and nested widget collections.
- [x] Write a failing test asserting a source wrapper with no padding becomes `paddingTop/Right/Bottom/Left = 0px` before `norm()` applies manual defaults.
- [x] Write a failing test asserting `px-5` becomes `20px` and `p-6` becomes `24px` in the correct responsive setting.
- [x] Write a failing test asserting `lg:grid-cols-[.9fr_1.1fr]` maps measured desktop tracks without changing mobile/tablet values.
- [x] Implement `applyCompiledComputedSnapshot(layout, snapshot)` as an import-only helper in `app.js`.
- [x] Run scanner before `rootNodes.value = norm(...)` in the compiled import path; leave native/exact/manual branches untouched.
- [x] Keep snapshot data out of Save payload after settings are applied.
- [x] Run focused Node and PHPUnit tests and require the manual/no-metadata assertions to remain green.

### Task 4: Replace conflicting generated CSS with residual CSS

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
- Modify: `public/js/pagebuilder_elementor_v24/static-import-css.js` only if generated block metadata needs a version marker.
- Modify: `tests/pagebuilder-v24-static-import-compiler.test.mjs`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`

- [x] Write a failing test asserting generated CSS does not reintroduce native-owned `padding`, `margin`, `width`, `height`, `gap`, or grid/flex structure for nodes already mapped from a snapshot.
- [x] Write a failing test asserting residual pseudo-element/state CSS remains marker-scoped.
- [x] Write a failing test asserting user CSS outside the generated block stays unchanged byte-for-byte.
- [x] Implement property ownership filtering using the same native property table as the snapshot mapper.
- [x] Preserve unsupported `background-image`, pseudo-element, hover, transition, and selector effects only when a safe target marker exists.
- [x] Remove broad `!important` output and retain only marker-scoped exceptions covered by tests.
- [x] Run focused compiler/CSS manager tests.

### Task 5: Handle section fallback and carousel/absolute patterns

**Files:**

- Modify: `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`
- Modify: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`

- [x] Write a failing test for a section containing unsupported selector paths or non-mappable nested markup; expected result is a section fallback warning, not empty widgets.
- [x] Write a failing test for `snap-track` with `min-w-[78vw]`, `overflow-x-auto`, and `sm:grid`; expected desktop/tablet/mobile layout ownership remains coherent.
- [x] Write a failing test for source absolute overlays; expected measured position is preserved only when native position settings can represent it.
- [x] Implement fallback classification with `fallback: true`, reason codes, and section-local `static_html` payload.
- [x] Keep successfully mapped sections native even when another section falls back.
- [x] Run focused importer tests.

### Task 6: Update compile progress/report and asset readiness

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
- Modify: `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`
- Modify: `tests/pagebuilder-v24-static-import.test.mjs`
- Modify: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`

- [x] Add progress stages: `scan-sections`, `measure-layout`, `map-native-settings`, `emit-residual-css`.
- [x] Show scanner counts in the existing modal: sections, measured nodes, native mapped nodes, fallback sections, missing assets.
- [x] Add optional safe Base URL validation for relative source assets; do not invoke File Manager V2.
- [x] Preserve exact fallback on scanner failure/cancellation and do not auto-save partial output.
- [x] Test progress completion, cancellation, warning aggregation, and exact fallback.

### Task 7: Add real fixture and browser geometry QA

**Files:**

- Modify: `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`
- Create/update: `project-artifacts/qa/pagebuilder-v24-static-import-20260828/QA_REPORT_12-computed-style-scanner-20260829.md`
- Use fixture read-only: `E:\Apps\Laragon\www\ceo-masters\index.html`

- [x] Run the server-side fixture conversion and record section/node/asset/fallback counts.
- [x] Run browser smoke at `390px`, `768px`, and `1180px` using a temporary local fixture server and read-only automation.
- [x] Assert no horizontal overflow for sections that are not source carousels.
- [x] Assert no overlapping bounding boxes for mapped heading/text/button nodes within the same section, allowing intentional absolute overlays.
- [x] Assert imported Container padding equals the measured source padding, including explicit zero.
- [x] Assert `document.querySelectorAll('iframe[data-pb-compiler]').length === 0` after cleanup.
- [x] Capture section-level screenshots and DOM measurements; avoid full-page screenshot timeouts.
- [x] Do not click Save, Reset, Preview, Apply, or submit forms during QA.

### Task 8: Regression and release gate

**Files:**

- All changed files above
- QA report in `project-artifacts/qa/pagebuilder-v24-static-import-20260828/`

- [x] Run:

```powershell
node --test tests/pagebuilder-v24-*.test.mjs
php artisan test tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php tests/Feature/PageBuilderElementorV24StaticImportCompiledFlowTest.php tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php --colors=never
node --check public/js/pagebuilder_elementor_v24/app.js
node --check public/js/pagebuilder_elementor_v24/static-import-compiler.js
php -l app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php
php artisan view:cache
git diff --check
```

- [x] Verify manual page without `staticImport` metadata does not receive scanner CSS/assets.
- [x] Verify `Native` and `Exact Visual` outputs retain their existing dependency/fallback behavior.
- [x] Run `graphify update . --no-cluster` after substantial source changes and query the scanner/mapping path.
- [x] Review `git status --short`; preserve unrelated dirty changes and all backups.
- [x] Do not commit or deploy without separate user authorization.

## Acceptance Criteria

1. Compiled Native scans each detected section and every marked source node at mobile/tablet/desktop Canvas widths.
2. Imported Container with no source padding is explicitly `0px`; manual Container default remains `1rem`.
3. Source padding/margin/border/width/height/layout/typography/color values are written into native widget settings using measured CSS values.
4. Native layout no longer receives conflicting residual CSS for properties already owned by settings.
5. Carousel, absolute overlay, pseudo-element, and unsupported selector patterns either map coherently or remain section-local fallback; they do not corrupt neighboring sections.
6. Generated final output contains no Tailwind/Bootstrap CDN dependency or source visual class tokens.
7. Manual, Native, Exact Visual, Save, frontend rendering, drag/drop, and responsive engine behavior remain unchanged.
8. CEO Masters fixture has fresh section-level geometry evidence at 390px, 768px, and 1180px, with limitations documented honestly.
9. Focused and full regression checks pass, syntax/view checks pass, Graphify is incrementally updated, and no unrelated files are modified.

## Rollback

- Before each existing-file edit, create a timestamped backup in the same directory.
- If scanner mapping fails, keep the existing Compiled Native/exact fallback path available and restore only the changed scanner slice after verifying dirty-worktree ownership.
- If the visual result remains unstable after three isolated hypotheses, stop implementation and use `Exact Visual`/section fallback rather than adding another global CSS override.
- No database rollback is required because import remains draft-only and scanner snapshots are ephemeral.

## Self-Review Checklist

- [x] Scope is limited to the Compiled Native import path and project artifacts.
- [x] Manual defaults and the main responsive engine are explicitly protected.
- [x] Scanner, mapping, residual CSS, fallback, progress, asset boundary, and browser QA each have a task.
- [x] Interfaces and viewport values are concrete.
- [x] Test commands and RED/GREEN expectations are specified.
- [x] No File Manager V2 implementation is included.
- [x] No commit, push, deploy, or auto-save is included.
