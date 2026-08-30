# Page Builder v2.4 Manual Mapping Compiled Native Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use an inline task-by-task implementation workflow with review checkpoints. Do not spawn subagents for this plan unless the user separately authorizes it. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a Compiled Native importer where the user manually chooses the target layout and widget for every selected content block, while the importer transfers source HTML/CSS measurements into the existing Page Builder v2.4 schema.

**Architecture:** The importer is a draft-only, v2.4-isolated workflow. It parses the source into a preserved DOM/CSS intermediate representation, shows an exact section preview, then lets the user define the target Container/Flex/Grid structure and widget mapping before any Phoenix node is created. The existing Page Builder engine, module registry, responsive engine, and frontend renderers remain unchanged.

**Tech Stack:** Laravel request/controller/service, DOMDocument or an HTML5-compatible parser, PostCSS-compatible CSS AST processing, Vue 3 CDN editor UI, existing v2.4 module catalog, existing Phoenix layout/widget schema, isolated source preview iframe.

**Spec:** This plan is the manual-mapping specification agreed during the 2026-08-29 discussion. Implementation requires explicit user approval after review of this document.

## Global Constraints

- Scope is Page Builder Elementor v2.4 only; do not modify v2.3 or the main responsive engine.
- Header and footer may be detected and displayed, but the first implementation compiles selected body sections only.
- No `Use recommendations`, automatic widget selection, automatic column count, or silent Static HTML/Exact Visual fallback.
- Compile is disabled until every selected content block has an explicit target widget and every section has an explicit root layout.
- Source scripts are never executed; inline event handlers and executable script content are reported and omitted.
- Save remains a separate manual action after the generated draft is reviewed.
- Existing custom CSS, Custom JavaScript, widgets, layout modules, Grid/Flex/Row Grid behavior, and responsive controls must remain intact.
- Every modified existing file receives a timestamped backup before implementation.

## Target Contract

The manual wizard must produce a draft mapping payload with this shape:

```json
{
  "version": 1,
  "sourceHash": "sha256",
  "sections": [
    {
      "id": "section-1",
      "sourceSelector": "main > section:nth-of-type(1)",
      "compile": true,
      "root": {
        "type": "grid",
        "columns": ["42%", "58%"],
        "responsiveColumns": {"desktop": 2, "tablet": 1, "mobile": 1}
      },
      "blocks": [
        {"sourceId": "node-1", "parentTarget": "column-1", "widget": "heading"},
        {"sourceId": "node-2", "parentTarget": "column-1", "widget": "text_editor"},
        {"sourceId": "node-3", "parentTarget": "column-2", "widget": "image"}
      ]
    }
  ]
}
```

Every selected source block must retain its source tag, ID, classes, inline style, computed style snapshot, source bounds, and target node ID for traceability.

### Task 1: Freeze the manual mapping contract and fixtures

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeMapping.php`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/manual-hero.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/manual-three-column.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/manual-inline-and-class-styles.html`
- Test: `tests/Unit/PageBuilderElementorV24ManualCompiledNativeMappingTest.php`

**Interfaces:**
- `ManualCompiledNativeMapping::validate(array $payload, array $sourceIndex): array` returns `['valid' => bool, 'errors' => list<array>]`.
- `ManualCompiledNativeMapping::normalize(array $payload): array` returns a canonical mapping with stable section, block, column, and widget keys.

- [ ] **Step 1: Write failing contract tests** for explicit root layout, arbitrary column counts, explicit widget selection, skipped sections, duplicate mappings, unknown widget types, and missing target parents.
- [ ] **Step 2: Run** `php artisan test --compact tests/Unit/PageBuilderElementorV24ManualCompiledNativeMappingTest.php` and verify the contract class is absent.
- [ ] **Step 3: Implement** only payload normalization and validation; do not parse HTML or touch the editor.
- [ ] **Step 4: Run** the focused test until all mapping contract cases pass.
- [ ] **Step 5: Run** the existing v2.4 Node and PHP regression slices to establish that the contract work does not affect the main builder.

### Task 2: Add source package ingestion and section navigation

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeSource.php`
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeSectionDetector.php`
- Create: `app/Http/Requests/Page_Builder_Elementor_V24/ManualCompiledNativeAnalyzeRequest.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `routes/pagebuilder_elementor_v24.php`
- Test: `tests/Unit/PageBuilderElementorV24ManualCompiledNativeSourceTest.php`
- Test: `tests/Feature/PageBuilderElementorV24ManualCompiledNativeAnalyzeTest.php`

**Interfaces:**
- `ManualCompiledNativeSource::fromUpload(UploadedFile $source, ?string $entry): SourcePackage` supports HTML/HTM and ZIP entries using deterministic `home.html`, then `index.html`, then an unambiguous HTML entry selection.
- `ManualCompiledNativeSectionDetector::detect(SourcePackage $source): SectionIndex` returns Header, body sections, Footer, source selectors, source ranges, and ambiguity diagnostics. It does not choose layouts or widgets.
- `PageBuilderElementorV24Controller::analyzeManualCompiledNative(ManualCompiledNativeAnalyzeRequest $request, ManualCompiledNativeSource $source, ManualCompiledNativeSectionDetector $detector)` returns JSON only and never mutates a page.

- [ ] **Step 1: Add failing feature tests** for HTML upload, ZIP entry selection, malformed source, duplicate section candidates, and header/footer detection without compilation.
- [ ] **Step 2: Run** the focused tests and verify the new endpoint is unavailable.
- [ ] **Step 3: Implement** bounded upload validation, deterministic entry selection, safe temporary extraction, and section indexing.
- [ ] **Step 4: Verify** scripts are treated as inert source content and no upload path escapes the temporary directory.
- [ ] **Step 5: Run** the focused PHP tests and route contract tests.

### Task 3: Build the exact source preview and style snapshot

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeStyleSnapshot.php`
- Create: `public/js/pagebuilder_elementor_v24/manual-compiled-native-preview.js`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Test: `tests/Unit/PageBuilderElementorV24ManualCompiledNativeStyleSnapshotTest.php`
- Test: `tests/pagebuilder-v24-manual-compiled-native-preview.test.mjs`

**Interfaces:**
- `ManualCompiledNativeStyleSnapshot::collect(SourcePackage $source, SectionIndex $sections): array` returns source CSS, inline declarations, class/ID selectors, responsive media blocks, and asset references without dropping unsupported declarations.
- Browser preview exposes `window.PhoenixManualCompiledNativePreview.measure(selector, viewport)` and returns `computedStyle`, `rect`, `boxSizing`, and `sourceAttributes` for each indexed element.

- [ ] **Step 1: Write failing tests** proving inline CSS, class CSS, ID CSS, inheritance, media queries, padding, margin, border, width, height, gap, and background values remain in the snapshot.
- [ ] **Step 2: Run** the focused Node/PHP tests and verify missing snapshot behavior.
- [ ] **Step 3: Implement** an isolated preview with scripts disabled, source CSS loaded, and local/remote assets resolved according to the existing asset policy.
- [ ] **Step 4: Normalize measurements** at the supported source viewports without changing the Page Builder Canvas viewport logic.
- [ ] **Step 5: Verify** the preview never executes imported JavaScript and the style snapshot reports unsupported CSS explicitly.

### Task 4: Implement the manual layout and widget mapping UI

**Files:**
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Test: `tests/pagebuilder-v24-manual-compiled-native-mapping-ui.test.mjs`

**Interfaces:**
- `manualCompiledNativeState` stores the source index, selected section, target root layout, target columns, block assignments, style snapshots, and validation errors.
- `setManualCompiledNativeRoot(sectionId, rootType)` accepts only `container`, `flex`, or `grid`.
- `setManualCompiledNativeColumns(sectionId, columns)` accepts an integer from 1 through the Page Builder schema maximum and preserves explicit track widths.
- `setManualCompiledNativeWidget(sectionId, sourceId, widgetType, parentTarget)` requires a registered v2.4 module type.
- `manualCompiledNativeCanCompile` is true only when all selected blocks and section roots validate.

- [ ] **Step 1: Write failing UI contract tests** for section navigation, compile-disabled state, one/two/three-column choices, widget dropdowns, reparenting, and explicit skip behavior.
- [ ] **Step 2: Run** the Node contract test and verify the wizard state is unavailable.
- [ ] **Step 3: Implement** a three-pane wizard: section list, exact source preview, and manual mapping panel. Use labels `Header`, `Section N`, and `Footer`; never use `Region`.
- [ ] **Step 4: Add** per-element style inspection showing computed padding, margin, border, width, height, gap, typography, background, ID, and classes.
- [ ] **Step 5: Add** clear validation messages for every unassigned block and every unsupported target setting.
- [ ] **Step 6: Run** the Node UI contract test and the existing v2.4 editor suite.

### Task 5: Map selected styles into existing Phoenix nodes

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeNodeMapper.php`
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeStyleMapper.php`
- Test: `tests/Unit/PageBuilderElementorV24ManualCompiledNativeNodeMapperTest.php`
- Test: `tests/Unit/PageBuilderElementorV24ManualCompiledNativeStyleMapperTest.php`

**Interfaces:**
- `ManualCompiledNativeNodeMapper::compile(SectionIndex $sections, array $mapping): array` returns canonical Page Builder nodes only for selected sections.
- `ManualCompiledNativeStyleMapper::toContainerSettings(ComputedStyleSnapshot $snapshot): array` maps layout styles to existing Container/Grid/Flex settings.
- `ManualCompiledNativeStyleMapper::toWidgetSettings(string $widgetType, ComputedStyleSnapshot $snapshot, SourceElement $source): array` maps widget-compatible typography, spacing, border, background, dimensions, content, and asset settings.
- Unsupported declarations return `['property', 'value', 'reason', 'target']` diagnostics and are never silently discarded.

- [ ] **Step 1: Write failing tests** for the hero mapping: Container → Grid 2 columns → Heading/Text Editor/Image; also test one-column and three-column sections.
- [ ] **Step 2: Add independent tests** for box-sizing, four-side spacing, border shorthand, percentages, pixels, responsive values, image object-fit, and source ID/class preservation.
- [ ] **Step 3: Implement** canonical node generation using existing module definitions and existing responsive settings names.
- [ ] **Step 4: Reject** unknown widget types, orphan blocks, duplicate target IDs, invalid asset URLs, and unsafe inline content.
- [ ] **Step 5: Run** the unit mapper suite and all existing widget/layout runtime tests.

### Task 6: Add compile preview, diff review, and draft apply

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/ManualCompiledNativeValidator.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Test: `tests/Feature/PageBuilderElementorV24ManualCompiledNativeCompileTest.php`
- Test: `tests/pagebuilder-v24-manual-compiled-native-validation.test.mjs`

**Interfaces:**
- `ManualCompiledNativeValidator::compare(SourceRenderSnapshot $source, array $layout, TargetRenderSnapshot $target): ValidationReport` compares element bounds and style values by source ID.
- `ManualCompiledNativeService::compile(SourcePackage $source, array $mapping): CompileDraft` returns layout, custom CSS diagnostics, assets, source trace, and validation report without saving.
- `ManualCompiledNativeService::applyDraft(CompileDraft $draft, PageData $page): array` returns the draft layout and metadata for the existing manual Save flow.

- [ ] **Step 1: Write failing tests** proving a compile response contains only manually selected sections, canonical nodes, source trace, and validation diagnostics.
- [ ] **Step 2: Implement** a target preview that renders the generated draft with the existing v2.4 frontend renderer and compares Desktop, Tablet, and Mobile snapshots.
- [ ] **Step 3: Show** exact differences instead of hiding them: source/target width, height, x/y, padding, margin, border, and unmapped CSS property.
- [ ] **Step 4: Keep** Apply disabled when validation has structural errors; allow the user to return to mapping and correct the section.
- [ ] **Step 5: Apply** only to the in-memory Canvas draft; do not call the normal page Save endpoint automatically.

### Task 7: Wire isolation, security, and recovery checks

**Files:**
- Modify: `routes/pagebuilder_elementor_v24.php`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Test: `tests/Feature/PageBuilderElementorV24ManualCompiledNativeIsolationTest.php`
- Test: `tests/Feature/PageBuilderElementorV24ManualCompiledNativeSecurityTest.php`

- [ ] **Step 1: Verify** every new endpoint has the same v2.4 auth boundary as the editor and does not appear in v2.3 routes.
- [ ] **Step 2: Verify** source scripts, event attributes, unsafe URLs, unsafe CSS values, and archive traversal are rejected or omitted with explicit diagnostics.
- [ ] **Step 3: Verify** the importer never calls or alters `canonicalLayoutForSave`, the global responsive engine, or any v2.3 renderer.
- [ ] **Step 4: Verify** a failed compile leaves the current Canvas tree unchanged.
- [ ] **Step 5: Run** the focused security and isolation suites.

### Task 8: Final regression and browser QA

**Files:**
- Create: `project-artifacts/qa/pagebuilder-v24-manual-compiled-native-20260829/QA_REPORT.md`
- Create: `project-artifacts/qa/pagebuilder-v24-manual-compiled-native-20260829/`

- [ ] **Step 1: Run** `node --test tests/pagebuilder-v24-*.test.mjs` and record the exact result.
- [ ] **Step 2: Run** the focused Manual Compiled Native PHPUnit suite and record the exact result.
- [ ] **Step 3: Run** PHP syntax, `git diff --check`, and the v2.4 route/module catalog checks.
- [ ] **Step 4: Perform** read-only browser QA at 1180px, 768px, and 390px using the hero, one-column, and three-column fixtures; do not Save or Apply without explicit approval.
- [ ] **Step 5: Capture** source preview, mapping panel, target preview, style diagnostics, and unchanged Canvas state screenshots.
- [ ] **Step 6: Stop** if any layout/widget/style mismatch is unresolved; do not declare pixel parity based only on a green unit test.

## Definition of Done

- The user can select body sections independently and ignore Header/Footer.
- The user can choose Container, Flex, or Grid and any valid column count per section.
- The user can choose the widget for every content block before compile.
- Computed padding, margin, border, width, height, gap, typography, background, and responsive values are visible and transferred or explicitly reported.
- The generated draft is validated against the source at all supported viewports before Apply.
- No imported JavaScript executes.
- No automatic recommendation or silent fallback exists.
- Existing v2.4 widget, layout, Grid/Flex/Row Grid, responsive, and persistence tests remain green.
- The implementation does not modify the main Page Builder engine or v2.3.
