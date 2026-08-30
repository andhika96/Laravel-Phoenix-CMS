# Page Builder v2.4 Compiled Native Mapper Expansion Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend the isolated Page Builder v2.4 Compiled Native mapper so nested HTML/CSS compositions, Grid/Flex layouts, positioned overlays, responsive changes, composite widgets, pseudo-elements, and local assets are analyzed as explicit evidence and mapped to existing Canvas capabilities whenever representable.

**Architecture:** Preserve the measured DOM as immutable raw evidence. Add a layout relationship graph above it that identifies normal-flow groups, Grid/Flex tracks, containing blocks, positioned layers, stacking order, responsive deltas, and visual compositions. Convert that graph into existing v2.4 Container/Grid/widget nodes; use Advanced positioning or scoped Custom CSS only for residual representable properties, and block only genuinely ambiguous structure while unresolved assets remain explicit pending warnings for partial Apply.

**Tech Stack:** Laravel PHP value-array services, isolated Chromium measurement wrapper, Vue 3 CDN editor, existing v2.4 module catalog, existing Container/Grid/Advanced controls, PHPUnit, Node contract tests, Graphify.

**Spec:** `project-artifacts/plans/2026-08-29-pagebuilder-v24-automatic-layout-analyzer-compiled-native-plan.md`, `project-artifacts/plans/2026-08-29-pagebuilder-v24-normalized-visual-block-mapping-plan.md`, and the approved discussion decision: raw DOM → layout relationship graph → native Canvas mapping → explicit residual diagnostics.

## Global Constraints

- Scope is Page Builder Elementor v2.4 Compiled Native only.
- Do not modify v2.3, the main Page Builder responsive engine, existing layout modules, existing widget renderers, or the normal Save endpoint.
- Preserve every measured source node, source ID, parent relationship, attribute, HTML, computed style, rectangle, viewport, and asset reference.
- Existing multi-view measurement remains the source of truth for Desktop, Tablet, and Mobile; do not infer one viewport from another when measurement exists.
- Use existing v2.4 Container/Grid/Widget/Advanced contracts; do not create a new widget solely to represent absolute positioning or overlay layers.
- Custom JavaScript is not a layout or CSS fallback. It remains reserved for explicitly published runtime behavior.
- Native-safe values are mapped directly; residual visual properties become scoped Custom CSS review notes; structural ambiguity remains blocking, while unresolved local assets are explicit pending warnings for partial Apply and still prevent a complete visual result.
- Every modified existing file receives a timestamped SHA-256-verified backup before editing.
- No dependency is added for DOM grouping, geometry analysis, or widget signatures.
- No Save, Apply, database mutation, deploy, commit, or push is performed during implementation QA.

## Current gaps this plan addresses

- `AutomaticCompiledNativeLayoutClassifier::classifyNode()` currently returns `unclassified` as soon as a section root has a transform or an unsafe positioned/overlapping child.
- `AutomaticCompiledNativeLayoutMapper` maps root flow/Grid settings but does not map child `position`, offsets, z-index, or containing-block relationships.
- The measurement snapshot carries computed styles and rectangles per viewport but lacks an explicit relationship model for overlap, stacking, pseudo-elements, and responsive property deltas.
- Widget signatures recognize basic composites but need broader composition evidence without collapsing structural Grid/Flex wrappers.
- Local source images are detected but do not yet have a persistent source-path → File Manager/URL mapping step.

### Task 1: Freeze expanded evidence and relationship contracts

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutRelationships.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeMeasurement.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeEvidence.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutRelationshipsTest.php`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-measurement.test.mjs`

**Interfaces:**
- `AutomaticCompiledNativeLayoutRelationships::build(array $nodes, array $viewports): array` produces `normalFlowGroups`, `positionedLayers`, `overlapPairs`, `containingBlocks`, `stackingOrder`, `pseudoElements`, and `responsiveDeltas`.
- Each relationship record references existing `sourceId` values and viewport names; no duplicate source node is created.

- [x] Write failing tests for a relative parent with an absolute child, two overlapping siblings with z-index, a fixed child, Grid item placement, pseudo-element evidence, and a responsive property change.
- [x] Run the focused PHPUnit/Node measurement tests and confirm the new relationship contract is absent.
- [x] Add only the relationship value object/array normalizer; validate source IDs, viewport names, rectangle shape, and bounded property values.
- [x] Extend the measurement payload only with relationship evidence; preserve the existing raw node schema unchanged.
- [x] Run focused tests and assert every relationship points to an existing raw source node.

### Task 2: Classify normal flow, Grid, Flex, overlay, and collection boundaries

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutClassifier.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeBlockNormalizer.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutClassifierTest.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeBlockNormalizerTest.php`

**Interfaces:**
- `classifyNode()` continues returning `mode`, `columns`, `tracks`, `evidence`, and `diagnostics`, and additionally returns `relationshipEvidence` and `representability`.
- A section with positioned descendants remains classified by its root display mode when the root Grid/Flex/Stack evidence is valid; positioned descendants are reported separately.

- [x] Add a failing regression test proving a valid Grid root with an absolute badge is still classified as Grid.
- [x] Add a failing regression test proving normal-flow children and positioned overlay children are separate groups.
- [x] Add tests for Grid item spans, Flex wrapped lines, nested Grid, and a one-column mobile transition.
- [x] Implement structural classification before unsafe-child rejection; reject only a root with no valid mode/evidence or an irreducibly ambiguous relationship.
- [x] Preserve `section.nodes` and normalized mapping fields while attaching relationship evidence.
- [x] Run the classifier and normalizer suites before continuing.

### Task 3: Map positioned layers to existing Container/Advanced settings

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutMapper.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutMapperTest.php`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-canvas-flow.test.mjs`

**Interfaces:**
- `AutomaticCompiledNativeLayoutMapper::positionedLayerSettings(array $layer, array $parent, string $viewport): array` produces existing keys: `position`, `horizontalOrientation`, `verticalOrientation`, responsive `positionX`/`positionY`, `zIndex`, width, and height.
- Target build consumes normalized `positionedLayers` while retaining source IDs and member trace.

- [x] Add a failing test for a relative parent and absolute child whose measured rectangle yields deterministic left/top offsets.
- [x] Add a failing test for right/bottom anchoring and z-index ordering.
- [x] Add a failing test proving a fixed layer is not silently converted into a normal-flow child.
- [x] Map safe absolute/relative layers to existing Container or selected widget Advanced settings; use parent-relative coordinates, not global coordinates.
- [x] Preserve responsive layer settings independently for Desktop, Tablet, and Mobile.
- [x] Keep transform evidence separate; map only supported transform components and report unsupported residuals.
- [x] Verify failed target construction leaves the existing Canvas `rootNodes` unchanged.

### Task 4: Reconcile responsive layout and layer deltas

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeResponsiveDelta.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeResponsiveClassifier.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeResponsiveDeltaTest.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutClassifierTest.php`

**Interfaces:**
- `AutomaticCompiledNativeResponsiveDelta::compare(array $nodes, array $viewports): array` returns per-source property changes for layout mode, columns, tracks, direction, visibility, position, offsets, dimensions, spacing, and order.
- `AutomaticCompiledNativeResponsiveDelta::strategy(array $delta): string` returns `native`, `custom-css`, `manual-decision`, or `blocking` based on existing v2.4 capabilities.

- [x] Add failing tests for Grid 2→2→1 columns, Flex row→column, absolute overlay offset changes, visibility changes, and reordered mobile content.
- [x] Implement explicit per-viewport deltas without mutating raw source evidence.
- [x] Map supported changes to existing responsive settings such as columns, direction, spacing, width, position offsets, and z-index.
- [x] Emit a `manual-decision` finding only when the node type itself must change and v2.4 has no responsive equivalent.
- [x] Ensure `layout-mode-transition` does not block when an equivalent native responsive strategy exists.
- [x] Run focused responsive tests and the target-flow contract.

### Task 5: Expand visual composition and widget signatures

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeWidgetSignatures.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeBlockNormalizer.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeWidgetSignaturesTest.php`

**Interfaces:**
- `AutomaticCompiledNativeWidgetSignatures::candidates(array $mappingNode, array $rawNodesById = [], array $relationships = []): array` returns ranked candidates with `type`, numeric `score`, `confidence`, `reasons`, `memberSourceIds`, `diagnostics`, and `representability`.

- [x] Add failing tests for icon-label-value, image-title-description, CTA row, logo group, card collection, metadata row, image overlay, and nested wrapper compositions.
- [x] Add guard tests proving structural Grid/Flex wrappers are never collapsed into Icon Box/Image Box merely because they contain an icon or image.
- [x] Add signatures for Heading, Text Editor, Button, Image, Image Box, Icon Box, Divider, CTA, card/feature composition, and generic Container fallback.
- [x] Use tag, semantic role, child roles, classes/IDs, display, geometry, spacing, media, and relationship evidence only; do not use an LLM decision.
- [x] Keep the top registered candidate editable and preselect only when score is at least `0.60`.
- [x] Report unsupported overlay/member details without creating duplicate mapping rows.

### Task 6: Add persistent local-asset mapping

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeFrameworkLoader.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/SourcePackage.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeSourceTest.php`
- Test: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeAnalyzeTest.php`

**Interfaces:**
- Asset records retain safe `sourcePath`, `kind`, `sourceId` references, availability, and a nullable `targetUrl`; server filesystem paths never enter the browser payload.
- The target gate accepts an image only when it has an external/data/root-relative URL or an explicit user-provided target asset mapping.

- [x] Add failing tests for relative `<img>`, CSS background image, pseudo-element image, SVG, duplicate references, and rejected traversal paths.
- [x] Preserve source-path normalization and package confinement.
- [x] Expose an explicit asset mapping state in the import draft; do not silently upload or persist files during analysis.
- [x] Rewrite mapped Image/Image Box settings only after an explicit target URL/File Manager selection.
- [x] Keep unresolved local assets visible as precise `asset-mapping` pending warnings; allow explicit partial Apply with the existing safe Image/Image Box empty state, while complete asset mapping remains required for visual completion.
- [x] Run source and analyze feature tests without exposing workspace paths.

### Task 7: Make representability diagnostics and residual CSS explicit

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutMapper.php`
- Modify: `public/js/pagebuilder_elementor_v24/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-canvas-flow.test.mjs`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutMapperTest.php`

**Interfaces:**
- Every finding has `code`, `severity`, `blocking`, `surface`, `sectionId`, `sourceId` when applicable, `viewport`, `message`, `remediation`, and optional `cssPatch`/`relationshipEvidence`.
- Target report exposes `structuralErrors`, `reviewNotes`, `assetMappings`, and `canApply`; only blocking errors prevent Apply.

- [x] Add failing tests proving style-only findings are review notes and structural ambiguity remains blocking.
- [x] Add a scoped CSS residual builder for safe declarations that have no native setting, keyed to the mapped Canvas node ID and viewport media query.
- [x] Render diagnostics grouped by section and surface: Layout, Positioning, Responsive, Widget, Asset, and Custom CSS.
- [x] Show the exact source property/value causing the finding; remove generic “cannot represent safely” text when evidence exists.
- [x] Keep Custom JavaScript excluded from layout/style fallback behavior.
- [x] Verify no diagnostic UI introduces horizontal overflow or blank-screen Vue binding regressions.

### Task 8: Integrated verification, real-source replay, and artifacts

**Files:**
- Modify: `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/QA_REPORT.md`
- Create: `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/MAPPER_EXPANSION_QA.md`
- Update incrementally: `graphify-out/graph.json` through Graphify tooling only

- [x] Run focused PHPUnit suites for relationships, classifier, normalizer, responsive delta, signatures, source assets, mapper, and analyze response.
- [x] Run the full active v2.4 Node suite, `node --check`, Vue template compilation, PHP lint, and `git diff --check`.
- [x] Replay `E:\Apps\Laragon\www\ceo-masters\index.html` and record Section 1 plus `register` relationship evidence: root mode, columns, positioned layers, offsets, z-index, responsive deltas, and widget candidates.
- [x] Verify the target report distinguishes native-safe, Custom CSS review, asset mapping, manual decision, and blocking findings.
- [~] Perform read-only browser QA at 1180px, 768px, and 390px when an authenticated session is available; fixture measurement covers 1180px, 768px, and 390px, while the live authenticated import click-through remains unavailable. No Save/Apply was executed.
- [x] Refresh Graphify after source changes and query the scanner → relationship graph → mapper → target validation path.
- [x] Record any overlay, fixed-position, pseudo-element, font, remote asset, or interaction cases that remain outside native representation; do not claim pixel parity from scores.

## Acceptance criteria

- The raw DOM evidence remains lossless and traceable by source ID.
- A valid Grid/Flex/Stack root is not marked `unclassified` solely because it contains a reconstructable overlay.
- Reconstructable absolute/relative layers map to existing v2.4 Advanced positioning and preserve responsive offsets and z-index.
- Grid/Flex column counts and nested wrappers are derived from measured evidence, including 1-, 2-, 3-, and higher-column layouts.
- Composite visual groups produce one mapping unit with expandable raw members and an editable registered widget candidate.
- Candidate scores at or above 60% preselect a registered widget; lower scores remain unselected.
- Style residuals are visible as scoped Custom CSS review notes rather than generic blocking errors.
- Local assets are explicitly mapped before complete visual finalization; partial Apply never exposes server filesystem paths and uses the registered Image/Image Box empty state until mapping is supplied.
- Only genuinely ambiguous structure or invalid evidence blocks partial Apply; unresolved assets remain visible and prevent complete status without blocking the explicit partial Apply path.
- v2.3 and the main Page Builder engine remain unchanged.

## Execution status

Implementation complete through Tasks 1–7 and automated/replay verification in Task 8. The latest Chrome blank-screen regression was traced to an escaped-slash regular expression inside the outer root-template literal, then fixed by moving the CSS/asset filters into setup-level computed values; the runtime template compile regression is covered. The approved partial-asset behavior is now implemented: unresolved image/background assets remain visible as non-blocking pending warnings, partial Apply uses the existing empty-image state, and complete visual status remains gated on asset mapping. The live authenticated import click-through at all three source-import viewports remains the only unverified boundary; fixture measurement covered 1180px, 768px, and 390px. No agents were spawned, no Save/Apply was executed in browser QA, and no v2.3/main Page Builder engine file was modified.
