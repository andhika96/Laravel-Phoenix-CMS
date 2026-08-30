# Page Builder v2.4 Automatic Layout Analyzer Compiled Native Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use an inline task-by-task implementation workflow with review checkpoints. Do not spawn subagents for this plan unless the user separately authorizes it. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a measurement-driven Compiled Native analyzer that automatically derives each section's Container/Flex/Grid structure, dynamic column count, track widths, and responsive layout from the rendered source instead of using fixed column assumptions.

**Architecture:** The analyzer renders the imported source in an isolated browser with its real CSS active, snapshots computed styles and geometry at each viewport, and converts those observations into a layout blueprint. CSS declarations and box-model values are resolved before classification. The blueprint is validated by rendering it through the existing v2.4 renderer; it never mutates the main Page Builder engine. This plan automates layout classification only; widget mapping remains a separately controlled decision until explicit approval is given for automatic widget semantics.

**Tech Stack:** Laravel orchestration, HTML5 DOM parser, PostCSS AST and selector/value parsers, Tailwind CLI/static compiler, Bootstrap 5 CSS assets, Playwright/Chromium measurement adapter, Vue 3 CDN review UI, existing v2.4 module catalog and canonical layout schema.

**Spec:** This plan is the automatic-layout specification agreed during the 2026-08-29 discussion. Implementation requires explicit user approval after review of this document.

## Execution status — 2026-08-29

Tasks 1–8 have been implemented in the isolated v2.4 workflow. The automatic analyzer still determines section boundaries, measured layout mode, dynamic columns, tracks, and responsive evidence. Per the subsequent user decision, widget semantics are now a separate mandatory manual stage: the user selects a registered widget for each source block, reviews the generated target, and explicitly applies the in-memory draft to Canvas. This extension does not alter the v2.4 responsive engine, layout modules, widget renderers, v2.3, or the normal Save endpoint.

The automated source/measurement/analyzer/mapper suites and the full v2.4 JavaScript suite are green. Fixture browser evidence is archived under `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/`. Authenticated live-editor click-through and target DOM re-measurement for a user-specific widget mapping remain QA boundaries because the available headless session redirects to `/auth/login`; they are not represented as pixel-parity claims.

## Global Constraints

- Scope is Page Builder Elementor v2.4 only; do not modify v2.3 or the main responsive engine.
- Header and footer are detected for navigation, but the first release compiles selected body sections only.
- No hard-coded default of 1, 2, or 3 columns. Every column count must be evidenced by CSS tracks, flex geometry, or measured placement.
- Automatic analysis produces a blueprint first; it never directly saves or silently replaces the current Canvas tree.
- Widget semantics are not silently guessed in this plan. The layout analyzer must preserve source block identity and expose widget candidates for a later explicit mapping decision.
- Source scripts and event handlers are never executed during analysis.
- Tailwind is compiled to static CSS before measurement. Bootstrap CSS and its global box-sizing/reset behavior are loaded exactly for measurement; Bootstrap JavaScript behavior is analyzed separately and is not treated as layout CSS.
- Existing custom CSS, Custom JavaScript, widgets, layout modules, Grid/Flex/Row Grid behavior, and responsive controls remain intact.
- Every modified existing file receives a timestamped backup before implementation.

## Automatic Blueprint Contract

The analyzer must return a blueprint independent from the Phoenix node schema:

```json
{
  "version": 1,
  "sourceHash": "sha256",
  "viewports": [
    {"name": "desktop", "width": 1180, "height": 900},
    {"name": "tablet", "width": 768, "height": 1024},
    {"name": "mobile", "width": 390, "height": 900}
  ],
  "sections": [
    {
      "id": "section-1",
      "kind": "section",
      "sourceSelector": "main > section:nth-of-type(1)",
      "boundaryConfidence": 0.98,
      "layoutByViewport": {
        "desktop": {"mode": "grid", "columns": 2, "tracks": ["42%", "58%"]},
        "tablet": {"mode": "grid", "columns": 2, "tracks": ["1fr", "1fr"]},
        "mobile": {"mode": "stack", "columns": 1, "tracks": ["1fr"]}
      },
      "nodes": [
        {
          "sourceId": "node-1",
          "tag": "h1",
          "parentSourceId": "section-1",
          "rectByViewport": {"desktop": {"x": 142, "y": 588, "width": 620, "height": 210}},
          "layoutRole": "content",
          "widgetCandidates": ["heading"]
        }
      ],
      "diagnostics": []
    }
  ]
}
```

The blueprint must retain evidence for every automatic decision: computed `display`, `gridTemplateColumns`, `gridColumnStart/End`, `flexDirection`, `flexWrap`, child rectangles, source styles, and the rule that produced the classification.

### Task 1: Define deterministic evidence and fixture corpus

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeBlueprint.php`
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeEvidence.php`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-one-column.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-two-column-grid.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-three-column-grid.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-flex-wrap.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-nested-divs.html`
- Create: `tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-responsive-collapse.html`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeBlueprintTest.php`

**Interfaces:**
- `AutomaticCompiledNativeEvidence::fromSnapshot(array $snapshot): EvidenceSet` validates and normalizes measured style/geometry evidence.
- `AutomaticCompiledNativeBlueprint::validate(array $blueprint): array` returns structural errors and evidence gaps.
- `AutomaticCompiledNativeBlueprint::normalize(array $blueprint): array` returns stable section/node/viewport ordering.

- [ ] **Step 1: Write failing tests** for one, two, three, and four-column evidence, nested wrappers, flex wrapping, and responsive column changes.
- [ ] **Step 2: Run** the focused test and verify the blueprint/evidence classes are unavailable.
- [ ] **Step 3: Implement** data validation only; reject a column count without an evidence record.
- [ ] **Step 4: Run** the focused contract suite and preserve the fixtures as the independent oracle for later classifier tests.

### Task 2: Ingest HTML/ZIP and load the correct CSS universe

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeSource.php`
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeFrameworkLoader.php`
- Create: `app/Http/Requests/Page_Builder_Elementor_V24/AutomaticCompiledNativeAnalyzeRequest.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `routes/pagebuilder_elementor_v24.php`
- Test: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeAnalyzeTest.php`

**Interfaces:**
- `AutomaticCompiledNativeSource::fromUpload(UploadedFile $source, ?string $entry): SourcePackage` supports HTML/HTM and deterministic ZIP entry selection.
- `AutomaticCompiledNativeFrameworkLoader::prepare(SourcePackage $source, string $framework): FrameworkBundle` returns sanitized HTML, CSS sources, asset manifest, detected framework, and diagnostics.
- `FrameworkBundle` distinguishes `plain_css`, `tailwind_static`, and `bootstrap_css`; it never reports a JavaScript runtime as required for style measurement.

- [ ] **Step 1: Write failing tests** for plain HTML, Tailwind utility classes, arbitrary Tailwind values, Bootstrap CSS, inline `<style>`, linked CSS, malformed CSS, and mixed framework false positives.
- [ ] **Step 2: Run** the focused feature tests and verify the new analyze endpoint is unavailable.
- [ ] **Step 3: Implement** source extraction, CSS collection, framework classification, and safe asset resolution.
- [ ] **Step 4: Compile** Tailwind through a static build path using the source HTML/classes; do not inject `cdn.tailwindcss.com` into the final output.
- [ ] **Step 5: Preserve** Bootstrap CSS reset and box-sizing rules in the measurement bundle while excluding Bootstrap JS from style classification.
- [ ] **Step 6: Run** the focused analyzer tests and route isolation tests.

### Task 3: Implement the isolated browser measurement adapter

**Files:**
- Create: `tools/pagebuilder-v24/automatic-compiled-native-measure.mjs`
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeMeasurement.php`
- Create: `tests/pagebuilder-v24-automatic-compiled-native-measurement.test.mjs`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeMeasurementTest.php`

**Interfaces:**
- CLI: `node tools/pagebuilder-v24/automatic-compiled-native-measure.mjs --input <path> --viewports <json> --output <path>` writes a versioned measurement snapshot.
- `AutomaticCompiledNativeMeasurement::measure(SourcePackage $source, array $viewports): MeasurementSnapshot` returns one DOM node record per source ID and one computed style/rectangle record per viewport.
- Each node record includes `tag`, `id`, `classList`, `parentSourceId`, `computedStyle`, `rect`, `scrollSize`, `pseudoState`, and `stylesheetSources`.

- [ ] **Step 1: Write failing measurement tests** for box-sizing, inherited font/color, four-side padding/margin, borders, width/height, flex, grid, absolute overlays, and media queries.
- [ ] **Step 2: Run** the Node/PHP measurement tests and verify no snapshot is produced.
- [ ] **Step 3: Implement** the isolated browser with scripts disabled, deterministic viewport sizes, local asset routing, and a timeout/resource limit.
- [ ] **Step 4: Capture** `getComputedStyle`, `getBoundingClientRect`, grid/flex computed properties, and source IDs/classes without modifying the DOM.
- [ ] **Step 5: Reject** a measurement when the source failed to load CSS, critical assets, or the indexed root; return diagnostics instead of partial silent data.
- [ ] **Step 6: Run** the focused measurement suite and archive a snapshot for each fixture.

### Task 4: Detect section boundaries without making layout decisions

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeSectionDetector.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeSectionDetectorTest.php`

**Interfaces:**
- `AutomaticCompiledNativeSectionDetector::detect(MeasurementSnapshot $snapshot): SectionIndex` returns Header, body sections, Footer, source selectors, boundary evidence, and ambiguity diagnostics.
- `SectionIndex` supports `split`, `merge`, `rename`, and `setCompile` operations in the review layer; detection alone never creates Phoenix nodes.

- [ ] **Step 1: Write failing tests** for semantic `<header>/<main>/<section>/<footer>`, full-width visual boundaries, nested sections, pages without semantic sections, and ambiguous boundaries.
- [ ] **Step 2: Implement** semantic markers first, then visual boundary evidence from background/geometry/spacing changes.
- [ ] **Step 3: Mark** uncertain boundaries as diagnostics and preserve the source selector; do not invent a section from a generic `div` without evidence.
- [ ] **Step 4: Run** the section detector suite and confirm Header/Footer are navigation entries only.

### Task 5: Classify Container, Flex, Grid, and dynamic column counts

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutClassifier.php`
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeResponsiveClassifier.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutClassifierTest.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeResponsiveClassifierTest.php`

**Interfaces:**
- `AutomaticCompiledNativeLayoutClassifier::classify(SectionIndex $sections, MeasurementSnapshot $snapshot): LayoutBlueprint` returns `stack`, `flex`, or `grid` plus evidence and confidence.
- `AutomaticCompiledNativeLayoutClassifier::gridTracks(NodeEvidence $parent): TrackSet` parses `gridTemplateColumns`, explicit grid placements, and measured track boundaries.
- `AutomaticCompiledNativeLayoutClassifier::flexLines(NodeEvidence $parent): FlexLineSet` groups children by measured row/column geometry while respecting `flexDirection`, `flexWrap`, order, and gaps.
- `AutomaticCompiledNativeResponsiveClassifier::classify(array $snapshots): ResponsiveLayoutBlueprint` emits per-viewport mode, columns, tracks, gap, and visibility changes.

- [ ] **Step 1: Write failing tests** proving `grid-template-columns: repeat(3, 1fr)` yields 3 columns, `0.9fr 1.1fr` yields 2 tracks, and a stack remains one column without Grid.
- [ ] **Step 2: Add tests** for flex row, flex wrap, uneven item widths, nested wrappers, absolute overlays, CSS transforms, and hidden elements.
- [ ] **Step 3: Implement** CSS-native classification first; use geometry clustering only when CSS declarations are insufficient.
- [ ] **Step 4: Implement** an explicit `unclassified` result for overlap/absolute/transform layouts that cannot be represented safely as normal columns.
- [ ] **Step 5: Implement** responsive classification from source viewport/media-query snapshots, including 3→2→1 and 2→1→1 transitions.
- [ ] **Step 6: Require** a decision evidence record for every selected mode and column count; a confidence number alone is not sufficient.
- [ ] **Step 7: Run** the classifier suite against all fixture snapshots.

### Task 6: Map the automatic layout blueprint to Phoenix structure

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutMapper.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutMapperTest.php`

**Interfaces:**
- `AutomaticCompiledNativeLayoutMapper::toPhoenixLayout(LayoutBlueprint $blueprint): array` returns existing Container/Flex/Grid nodes with canonical columns and responsive settings.
- `AutomaticCompiledNativeLayoutMapper::mapChildren(LayoutBlueprint $blueprint, array $sourceIndex): array` preserves source order, parent relationships, source IDs, and absolute/overlay diagnostics.
- `AutomaticCompiledNativeLayoutMapper::unsupported(LayoutBlueprint $blueprint): list<array>` returns explicit reasons for structures not supported by the existing schema.

- [ ] **Step 1: Write failing tests** for one-column Container, two-column Grid, three-column Grid, Flex row, Flex wrap, nested layout, and responsive collapse.
- [ ] **Step 2: Implement** schema mapping without changing `canonicalLayoutForSave`, `responsiveValue`, or existing layout module code.
- [ ] **Step 3: Preserve** measured track widths and gaps in the target settings; do not replace them with Page Builder defaults.
- [ ] **Step 4: Keep** content blocks as source-indexed placeholders until a separate widget mapping decision supplies a registered widget type.
- [ ] **Step 5: Run** mapper tests plus existing v2.4 layout/Grid/Flex tests.

### Task 7: Add automatic blueprint review and optional manual correction

**Files:**
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-review.test.mjs`

**Interfaces:**
- `automaticCompiledNativeState` stores source sections, measurement evidence, layout blueprint, selected section, corrections, and diagnostics.
- `overrideAutomaticLayout(sectionId, viewport, patch)` changes only the review blueprint and records the user override.
- `automaticCompiledNativeCanCompile` is false for structural errors, missing measurement evidence, or unresolved unsupported layouts.
- The review UI displays `Container`, `Flex`, `Grid`, actual column count, track widths, gap, and responsive transitions; it does not display a generic recommendation selector.

- [ ] **Step 1: Write failing UI tests** for dynamic 1/2/3-column display, responsive 3→1 transitions, evidence inspection, ambiguous-layout blocking, and manual correction.
- [ ] **Step 2: Implement** the review UI with source preview, blueprint preview, evidence panel, and explicit corrections.
- [ ] **Step 3: Show** source/target measurements for every section before compile.
- [ ] **Step 4: Keep** automatic widget semantics out of the compile path; show source content blocks and require a target widget mapping in the separate mapping stage.
- [ ] **Step 5: Run** the review UI suite and the existing v2.4 editor tests.

### Task 8: Validate generated layout against source geometry

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeValidator.php`
- Create: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeValidationTest.php`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-validation.test.mjs`

**Interfaces:**
- `AutomaticCompiledNativeValidator::compare(MeasurementSnapshot $source, array $targetLayout, MeasurementSnapshot $target): ValidationReport` compares source IDs by viewport.
- `ValidationReport` contains structural errors, per-node x/y/width/height deltas, box-model deltas, responsive mismatches, and CSS properties not represented by the target schema.
- `AutomaticCompiledNativeValidator::canApply(ValidationReport $report): bool` blocks Apply when structural mismatches exceed configured tolerance.

- [ ] **Step 1: Write failing tests** for wrong column count, wrong track ratio, missing padding, margin collapse, border width, image sizing, and responsive mode mismatch.
- [ ] **Step 2: Implement** source/target comparison at equal viewport widths; report absolute pixel deltas and source IDs.
- [ ] **Step 3: Add** tolerance rules only for browser rounding; do not hide meaningful layout differences behind a broad percentage tolerance.
- [ ] **Step 4: Block** compile/apply for missing evidence and structural mismatch; allow review correction and revalidation.
- [ ] **Step 5: Run** validation tests against all automatic fixtures.

### Task 9: Isolate security, caching, recovery, and final QA

**Files:**
- Create: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeSecurityTest.php`
- Create: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeIsolationTest.php`
- Create: `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/QA_REPORT.md`
- Create: `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/`

- [ ] **Step 1: Verify** authentication, upload limits, archive traversal protection, CSS/URL sanitization, and script non-execution.
- [ ] **Step 2: Verify** repeated analysis of the same source is deterministic for the same CSS/assets/viewport inputs.
- [ ] **Step 3: Verify** failed measurement or validation leaves the current Canvas tree unchanged.
- [ ] **Step 4: Verify** no new code calls or changes the main responsive engine, v2.3, or existing widget renderers.
- [ ] **Step 5: Run** `node --test tests/pagebuilder-v24-*.test.mjs` and record the exact result.
- [ ] **Step 6: Run** focused Automatic Compiled Native PHPUnit, syntax, route, module catalog, and `git diff --check` checks.
- [ ] **Step 7: Perform** read-only browser QA at 1180px, 768px, and 390px with one-, two-, three-column, flex-wrap, nested, and responsive fixtures.
- [ ] **Step 8: Record** every unresolved layout in the QA report; do not treat a high confidence score as proof of pixel parity.

## Definition of Done

- The analyzer never assumes a fixed column count.
- A one-column stack is not converted to Grid unnecessarily.
- Grid columns come from actual CSS tracks or measured geometry.
- Flex rows and wrapped lines are classified from computed properties and child rectangles.
- Responsive layout changes are represented per viewport.
- Nested `div` wrappers are preserved unless evidence proves they are layout-neutral.
- Absolute/overlay/transform structures are explicitly diagnosed instead of being forced into ordinary columns.
- Computed padding, margin, border, width, height, gap, typography, background, and box-sizing evidence is preserved.
- The generated layout is rendered and compared with the source before Apply.
- Widget mapping is not silently guessed by this analyzer.
- Existing v2.4 widgets, layout, Grid/Flex/Row Grid, responsive controls, and persistence remain unaffected.
- The implementation does not modify the main Page Builder engine or v2.3.
