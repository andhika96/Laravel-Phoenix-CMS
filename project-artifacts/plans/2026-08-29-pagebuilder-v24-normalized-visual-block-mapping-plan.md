# Page Builder v2.4 Normalized Visual Block Mapping Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the flat one-DOM-node mapping list with a recursive, traceable visual block projection that preserves nested `div` layouts and groups composite content such as Icon Box and Image Box candidates.

**Architecture:** Keep the measured DOM snapshot immutable as the raw evidence layer. Build a normalized mapping projection bottom-up from that snapshot: structural/layout wrappers remain in the tree, semantic content nodes become mapping units, and inline/decorative descendants become members of their nearest content/composite unit. Candidate widget signatures are evidence only; `selectedWidgetType` remains independent and user-editable.

**Tech Stack:** Laravel PHP value arrays, existing v2.4 Compiled Native measurement/classifier/mapper pipeline, Vue 3 CDN editor UI, existing v2.4 module catalog, existing v2.4 responsive and Advanced style contracts, PHPUnit, Node contract tests, Graphify.

**Spec:** `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/SOURCE_BLOCK_AND_LAYOUT_OVERRIDE_AUDIT.md` and the approved three-layer discussion: raw DOM → structural groups → visual mapping blocks.

## Global Constraints

- Scope is Page Builder Elementor v2.4 Compiled Native only.
- Preserve raw measured nodes, source IDs, parent relationships, attributes, HTML, computed styles, rectangles, and viewport evidence.
- Never remove nested DOM evidence; normalization only adds a projection.
- Never modify v2.3, the main responsive engine, existing layout modules, existing widget renderers, or the normal Save endpoint.
- Header and Footer remain navigation-only; body sections are mapped.
- No Static HTML or Exact Visual fallback is introduced.
- Automatic widget candidates are suggestions; the user-selected widget is the only value used for target compilation.
- Every modified existing file receives a timestamped SHA-256-verified backup first.
- No new dependency is added for tree grouping or signatures.

### Task 1: Freeze the normalized projection contract

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeBlockNormalizer.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeBlockNormalizerTest.php`

**Contract:**

`normalizeSection(array $section): array` returns:

```php
[
    'rawNodes' => [...],
    'mappingNodes' => [
        [
            'sourceId' => 'node-50',
            'parentMappingId' => 'node-43',
            'mappingRole' => 'composite',
            'mappingKind' => 'icon_box',
            'mappingEligible' => true,
            'memberSourceIds' => ['node-53', 'node-55', 'node-54'],
            'childMappingIds' => [],
            'textSummary' => 'Date 10 October 2026',
            'candidateWidgets' => [],
        ],
    ],
    'mappingRoots' => ['node-33'],
]
```

- [x] Write failing tests for nested layout wrappers, semantic content with inline children, icon/title/value composition, image/title composition, layout-neutral wrappers, and unique source IDs.
- [x] Run the focused PHPUnit test and confirm the normalizer class is unavailable.
- [x] Implement the smallest bottom-up projection with explicit `rawNodes`, `mappingNodes`, `mappingRoots`, `memberSourceIds`, and `childMappingIds`.
- [x] Run the focused normalizer test until all cases pass.

### Task 2: Implement recursive roles and composite grouping

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutClassifier.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutClassifierTest.php`

- [x] Add a regression test proving `h1 > br + span` is `content`, not `container`.
- [x] Add a regression test proving `a > i + text` stays one content/action mapping unit with the icon as an inline member.
- [x] Add tests for `div > dt(icon+label) + dd(value)` producing an `icon_box` composite candidate shape.
- [x] Add tests for `figure > img + overlay div` preserving the overlay as a member/composite detail without flattening the image structure.
- [x] Implement bottom-up role classification: CSS Grid/Flex or distinct box-model styles remain structural; semantic content elements remain content; inline/decorative descendants are members unless they form an independent layout boundary.
- [x] Preserve old `section.nodes` raw compatibility while adding normalized projection fields.

### Task 3: Expose normalized blocks through the layout mapper and analyze payload

**Files:**
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeLayoutMapper.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Test: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeAnalyzeTest.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeLayoutMapperTest.php`

- [x] Add `normalizedBlocks` and `mappingRoots` to each compiled body section while retaining `sourceBlocks` for compatibility.
- [x] Verify the analyze response contains no server filesystem paths and includes full member/source trace.
- [x] Verify columns are assigned only to normalized structural children, not to inline members.
- [x] Verify Header/Footer remain in navigation and never become mapping roots.
- [x] Run focused analyze/mapper tests.

### Task 4: Replace the flat mapping list with a tree and progressive disclosure

**Files:**
- Backup and modify: `public/js/pagebuilder_elementor_v24/app.js`
- Backup and modify: `public/assets/css/pagebuilder_elementor_v24.css`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-canvas-flow.test.mjs`

- [x] Add state for selected mapping node and raw-detail expansion without changing existing Canvas state.
- [x] Render mapping roots recursively with labels for `Layout`, `Container`, `Content`, `Composite`, and `Member`.
- [x] Show aggregate child counts/box-model summaries for structural parents instead of repeated descendant text.
- [x] Show member elements (`span`, `br`, `i`, `dt`, `dd`, overlay descendants) inside expandable details.
- [x] Keep one widget select per mapping unit; do not require a separate select for inline/decorative members.
- [x] Show the exact source preview beside the tree and highlight the selected mapping unit where source selector support exists.
- [x] Add per-block style inspector for active viewport: padding, margin, border, width, height, typography, background, source tag, id, and classes.
- [x] Keep Preview target gated by explicit selected widget values and preserve the existing no-auto-save behavior.
- [x] Run the Node flow and Vue template compilation contracts.

### Task 5: Add deterministic Image Box/Icon Box candidate signatures

**Files:**
- Create: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeWidgetSignatures.php`
- Test: `tests/Unit/PageBuilderElementorV24AutomaticCompiledNativeWidgetSignaturesTest.php`
- Modify: `app/Support/PageBuilderElementorV24/CompiledNative/AutomaticCompiledNativeBlockNormalizer.php`

- [x] Write tests for icon + label + value, image + title + description, CTA rows, and multi-column layout wrappers.
- [x] Implement signatures using tag, child roles, source attributes, display, geometry, and box-model evidence only.
- [x] Return candidate type, score, reasons, member IDs, and representability diagnostics.
- [x] Leave low-confidence candidates unselected; never use a candidate to bypass explicit mapping validation.
- [x] Verify existing v2.4 canonical widget types `image_box` and `icon_box` are used exactly.

### Task 6: Make target compilation consume mapping units without losing nested structure

**Files:**
- Backup and modify: `public/js/pagebuilder_elementor_v24/app.js`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-canvas-flow.test.mjs`
- Test: `tests/Feature/PageBuilderElementorV24AutomaticCompiledNativeIsolationTest.php`

- [x] Build target nodes from mapping roots and normalized child mapping IDs.
- [x] Keep composite members in source trace and map content from the member set when the selected widget supports it.
- [x] Reject an orphan mapping unit, duplicate target ID, missing selected widget, or unsafe/local asset without silently flattening it.
- [x] Strip import-only metadata only at the final Canvas payload boundary.
- [x] Verify failed build leaves `rootNodes` unchanged.

### Task 7: Verify layout override and source-block interaction

**Files:**
- Backup and modify: `public/js/pagebuilder_elementor_v24/app.js`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-review.test.mjs`
- Test: `tests/pagebuilder-v24-automatic-compiled-native-canvas-flow.test.mjs`

- [x] Keep Stack at one column and show an inline explanation when the user enters more than one column.
- [x] Preserve Grid/Flex column edits and track-count validation.
- [x] Verify a nested Grid inside a Stack section remains a nested Grid mapping root.
- [x] Verify normalized block counts do not count inline members as independent mapping blocks.

### Task 8: Final verification and artifact update

**Files:**
- Modify: `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/QA_REPORT.md`
- Create: `project-artifacts/qa/pagebuilder-v24-automatic-compiled-native-20260829/NORMALIZED_MAPPING_QA.md`

- [x] Run focused normalizer/signature/analyze/mapper PHPUnit tests.
- [x] Run the full v2.4 Node suite and `node --check`.
- [x] Run PHP lint and `git diff --check`.
- [x] Rebuild/update Graphify after source changes and query the normalized pipeline.
- [~] Perform read-only Chrome QA at desktop/tablet/mobile when the authenticated tab is available; the available authenticated editor tab was only reload-verified, while fixture measurement covered 1180px/768px/390px. No Save/Apply was executed.
- [x] Record unresolved composite cases and local-asset limitations honestly; do not claim pixel parity from candidate scores.

## Execution status — 2026-08-30

Tasks 1–7 and the automated portion of Task 8 are complete. The normalized projection is now the mapping-facing layer, while the raw DOM remains available for expandable inspection and source traceability. The only incomplete verification boundary is the authenticated live import click-through at all three viewports; the available Chrome session was reloaded read-only after the fix and the fixture suite covered the responsive measurement sizes. No agent was spawned, no Save/Apply action was performed, and the main Page Builder engine remains outside the change scope.

Post-QA binding regression on 2026-08-30 was reproduced with a failing Canvas-flow contract, fixed by exposing the two missing setup helpers, and reverified with the focused test plus a zero-missing-helper template audit.
