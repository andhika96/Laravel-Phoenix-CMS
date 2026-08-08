# Container Gaps Elementor Parity — Implementation Plan

> **For Codex:** Required implementation workflow: use `superpowers:executing-plans` for this plan, with the user-selected execution mode.

**Goal:** Make the `Gaps` control in Page Builder Elementor `Container` and `Container Fluid` match the official Elementor layout: one shared unit selector in the label row, two compact number inputs (`Column`, `Row`) with labels beneath, and a link/unlink button on the same row.

**Architecture:** Keep all persisted setting keys and the existing `editor.syncContainerGap()` responsive synchronization contract. Replace only the duplicated Flex and Grid sidebar markup in each Container variant. A dedicated `pb-container-gap-control` CSS class prevents the new compact layout from changing unrelated uses of `pb-grid-gap-controls` (Sticky, Transform, standalone Grid, and Row Grid).

**Tech Stack:** Vue 3 SFC settings panels, existing Page Builder Elementor editor helpers, Laravel/PHPUnit source-contract test, CSS.

## Scope and constraints

- In scope: `container` and `container-fluid`, both Flex and Grid `Gaps` sections.
- Out of scope: standalone `grid`, `row-grid`, renderer/parser behavior, saved-data migration, and general gap-control refactors.
- Preserve `flexColumnGap`, `flexRowGap`, `gridColumnGap`, `gridRowGap`, `containerGapLinked`, active responsive-device values, and `syncContainerGap()` behavior.
- Selecting the shared unit writes that unit to both dimensions without changing their numeric values. This keeps two unlinked numeric values valid while matching Elementor's single unit chooser.
- Use existing `editor.spacingControlUnits`, `editor.sizeControlDisplayValue`, `editor.onSizeControlInput`, and `editor.setSizeControlUnit`; no new dependencies or helpers.
- Before editing existing files: confirm Git state and create timestamped `.bak_..._container_gap_elementor_parity` copies. Do not stage or commit user changes, backups, or `graphify-out`.

## Task 1: Establish the narrow regression contract

**Files:**
- Modify: `tests/Feature/PageBuilderElementorContainerWidgetModulesTest.php`

1. Add a focused test covering both `container` and `container-fluid` settings files.
2. Assert each file has a dedicated compact container-gap wrapper, a single shared unit selector, two numeric fields, and an in-row `containerGapLinked` button.
3. Assert the existing four setting keys and `editor.syncContainerGap()` calls remain present.
4. Assert the old range-input markup is not retained inside the new compact gap-control contract.
5. Run `php artisan test --filter=PageBuilderElementorContainerWidgetModulesTest` and confirm the test fails before the view markup is changed.

## Task 2: Replace Container and Container Fluid Gaps markup

**Files:**
- Modify: `public/js/pagebuilder_elementor/widgets/layout/container/Settings.vue`
- Modify: `public/js/pagebuilder_elementor/widgets/layout/container-fluid/Settings.vue`

1. In each Flex `Gaps` group, replace the two slider-plus-per-field-unit controls with one dedicated compact wrapper:
   - label row containing the existing responsive device control and a shared unit selector;
   - values row containing the Column input, Row input, and link/unlink button;
   - Column/Row labels beneath their respective number inputs.
2. Do the equivalent replacement in each Grid `Gaps` group; retain its existing responsive device control and `Auto Flow` behavior.
3. Wire number input events to the same `onSizeControlInput(...); syncContainerGap(...)` calls using each mode's current fallback (`20px` Flex, `10px` Grid).
4. Wire the shared unit change to set both dimension units and then invoke `syncContainerGap()` without copying a number unnecessarily.
5. Leave the `syncContainerGap()` implementation, persisted settings, canvas, and Blade renderer unchanged.

## Task 3: Add scoped visual styling

**Files:**
- Modify only if needed: `public/assets/css/pagebuilder_elementor.css`

1. Reuse `pb-gap-row-with-link` where its existing three-column grid already fits the desired values row; otherwise add a narrowly named `pb-container-gap-control` rule.
2. Ensure input groups align horizontally, Column/Row labels sit underneath, and the link button cannot wrap to a third row at the normal sidebar width.
3. Do not alter generic `pb-grid-gap-controls`, `pb-link-btn`, or standalone Grid/Row Grid styles.

## Task 4: Verify behavior and visual parity

**Files:**
- Verify: the two settings files, `public/js/pagebuilder_elementor/app.js`, and focused test output.

1. Run `php artisan test --filter=PageBuilderElementorContainerWidgetModulesTest`.
2. Run the closest existing Page Builder Elementor feature test suite if time permits after the focused test passes.
3. In the browser, inspect both Container and Container Fluid in Flex and Grid modes at desktop and a responsive device:
   - link control remains in the same input row;
   - changing Column/Row synchronizes only while linked;
   - changing the shared unit updates both units;
   - `Auto Flow` remains visible and unchanged for Grid;
   - preview and saved state still emit the same gap values.
4. Report browser QA separately from static/test verification. Update Graphify only if the change proves substantial; otherwise report that the existing graph was used for navigation and was not regenerated.

## Completion criteria

- The button never drops below Column/Row at the regular sidebar width.
- The layout and interaction match the supplied official Elementor reference within the agreed scope.
- Existing responsive settings and renderer contract are preserved.
- Focused PHPUnit test passes with fresh output.
