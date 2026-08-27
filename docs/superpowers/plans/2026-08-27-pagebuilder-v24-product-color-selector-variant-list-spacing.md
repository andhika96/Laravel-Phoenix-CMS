# Product Color Selector Variant List Spacing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task with review checkpoints.

**Goal:** Separate the gap between the product image and its color-variant list from the gap between individual variants.

**Architecture:** Keep `listGap` as the intra-list gap and add responsive `variantListSpacing` for the flex body gap. New nodes default to the current 16px spacing; legacy nodes without the new key inherit their existing `listGap` value so their visual output remains unchanged.

**Tech Stack:** Laravel Blade, Vue 3 SFC, v2.4 widget definition/settings/canvas, vanilla frontend runtime, Node `node:test`, PHPUnit, Chrome QA.

**Spec:** User screenshots `C:\Users\CAHYO\AppData\Local\Temp\codex-clipboard-2ef3525f-3f13-4fb9-ad2e-25839e47bb09.png` and `C:\Users\CAHYO\AppData\Local\Temp\codex-clipboard-c6dfeef4-9e95-479f-bfc5-7b4a88e0c937.png` plus the request to control image-to-variant spacing independently from `List Gap`.

## Global Constraints

- Existing pages keep their current rendered spacing when `variantListSpacing*` is absent.
- `List Gap` continues to control spacing between variant cards only.
- `Variant List Spacing` is responsive and uses the existing dimension control UI.
- Keep Canvas and frontend parser CSS variables in parity.
- Preserve the dirty worktree; do not reset, clean, stage, commit, or push.
- Browser QA is read-only; do not press Save or Reset.

---

### Task 1: Add the responsive setting contract

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/definition.js`
- Test: `tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs`

- [ ] Add failing assertions for `variantListSpacing`, Tablet, and Mobile defaults plus migration from legacy `listGap` values.
- [ ] Run the focused Node test and confirm it fails because the keys are absent.
- [ ] Add defaults `16px`, empty Tablet/Mobile overrides, and normalize CSS lengths safely.
- [ ] Migrate only when the new key is absent: desktop from `listGap`, Tablet/Mobile from their legacy `listGap*` values with fallback inheritance.
- [ ] Run the focused Node test and confirm it passes.

### Task 2: Add the editor control with existing UI patterns

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Settings.vue`
- Test: `tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs`

- [ ] Add a failing static assertion for the `Variant List Spacing` label and `variantListSpacing` binding.
- [ ] Run the focused test and confirm it fails.
- [ ] Add `ResponsiveLength` beside `List Gap` under `Selection & Layout`, reusing the existing slider, numeric input, unit selector, and responsive handling.
- [ ] Run the SFC compile/static test and confirm the new control is present without introducing a custom form component.

### Task 3: Split Canvas layout spacing

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Canvas.vue`
- Test: `tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs`

- [ ] Add a failing source assertion for the new CSS variable and independent body/list gap declarations.
- [ ] Run the focused test and confirm it fails.
- [ ] Bind `--pb-pcs-variant-list-spacing` from the responsive setting and change only `.pb-product-color-selector__body` to use it; retain `--pb-pcs-list-gap` on the list.
- [ ] Compile the Canvas SFC and run the focused tests.

### Task 4: Mirror the split in the frontend parser

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/frontend.blade.php`
- Test: `tests/Feature/PageBuilderElementorV24ProductColorSelectorWidgetTest.php`

- [ ] Add a failing Blade test using `listGap=0px` and `variantListSpacing=24px`, asserting separate CSS variables and body/list declarations.
- [ ] Run the PHP test and confirm it fails.
- [ ] Emit sanitized desktop/Tablet/Mobile spacing variables, with legacy list-gap fallback when new values are absent.
- [ ] Change parser body gap to `--pb-pcs-variant-list-spacing` and preserve list item gap as `--pb-pcs-list-gap`.
- [ ] Run the focused PHP test, `php -l`, `view:cache`, and `git diff --check`.

### Task 5: Full regression and browser QA

**Files:**
- Verify all changed Product Color Selector files and existing v2.4 tests.

- [ ] Run all `tests/pagebuilder-v24-*.test.mjs` and all `tests/Feature/PageBuilderElementorV24*.php`.
- [ ] Reload the existing Chrome editor and add Product Color Selector without saving.
- [ ] Set `List Gap` to 0 and `Variant List Spacing` to a visible value; verify image-to-list spacing remains while cards touch each other.
- [ ] Set `Variant List Spacing` to 0; verify the list touches the image.
- [ ] Confirm browser console has no errors/warnings.
- [ ] Refresh Graphify incrementally and leave generated graph artifacts unstaged.

