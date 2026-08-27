# Product Color Selector Unselected Ring Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task with review checkpoints.

**Goal:** Add an opt-in unselected ring indicator to Product Color Selector while preserving the current selected-check-only behavior when the option is disabled.

**Architecture:** Keep the existing Selected Indicator contract as the single source for position, size, offset, and radius. Add only the unselected-ring visibility and visual tokens, then keep state/Canvas/Blade/runtime behavior aligned through the existing widget module boundaries.

**Tech Stack:** Laravel Blade, Vue 3 SFC loaded by the v2.4 editor, vanilla JavaScript widget runtime, Node `node:test`, PHPUnit feature tests, Chrome browser QA.

**Spec:** User request and reference screenshot in `C:\Users\CAHYO\AppData\Local\Temp\codex-clipboard-a8a081b7-739c-4758-ab90-e05e8365099b.png`.

## Global Constraints

- `Show Unselected Ring` defaults to `false` so existing pages render exactly as they do today.
- When enabled, inactive swatches show an outline ring; the active swatch keeps the existing selected check.
- Reuse the existing Selected Indicator position, size, offset, and radius controls; do not create a second position system.
- Reuse the existing Product Color Selector form controls and compact styling; no bespoke panel pattern.
- Keep editor Canvas, saved settings, frontend Blade, and runtime behavior synchronized.
- Preserve dirty worktree changes and create timestamped backups before editing existing files.
- Browser QA remains read-only: do not click Save or Reset.
- Do not commit, push, reset, clean, or modify unrelated v2.4 work.

---

### Task 1: Extend the widget settings contract safely

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/definition.js`
- Test: `tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs`

**Interfaces:**
- Produces normalized settings: `unselectedRingVisible`, `unselectedRingColor`, `unselectedRingBorderWidth`, `unselectedRingBackground`.
- Existing `selectedCheck*` keys and legacy `activeIndicator*` fallbacks remain valid.

- [ ] Write a failing normalization test asserting the new defaults are `false`, `#ffffff`, `2px`, and `transparent`, and that invalid values fall back safely.
- [ ] Run `node --test tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs` and confirm the new assertions fail because the keys are absent.
- [ ] Add the four defaults and normalize visibility, safe colors, and CSS-valid border-width units (`px`, `pt`, `em`, `rem`).
- [ ] Run the focused test and confirm it passes without changing legacy selected-indicator expectations.

### Task 2: Add the conditional settings UI using existing controls

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Settings.vue`
- Test: `tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs`

**Interfaces:**
- Adds `Show Unselected Ring` under the existing `Selected Indicator` accordion.
- When enabled, renders `Unselected Ring Color`, `Unselected Ring Border Width`, and `Unselected Ring Background`.
- Uses the existing `ToggleField`, `ColorField`, and responsive size-control pattern; the new controls do not introduce a separate layout language.

- [ ] Extend the static settings test with the four new labels and the conditional binding marker.
- [ ] Run the focused test and confirm it fails before the UI is added.
- [ ] Add the toggle with default-off state and render the three dependent fields only when enabled.
- [ ] Reuse the existing responsive unit control, restricting border width to CSS-valid units while retaining the established `px`, `pt`, `em`, `rem`, and `%` support where valid.
- [ ] Compile the SFC through the existing v2.4 settings-mount test and confirm the focused tests pass.

### Task 3: Render selected and unselected indicator states in Canvas

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Canvas.vue`
- Test: `tests/pagebuilder-v24-product-color-selector-selected-indicator.test.mjs`

**Interfaces:**
- Active indicator keeps class/state `is-selected` and the existing Selected Indicator CSS variables.
- Inactive indicator uses `is-unselected` and new ring variables only when `unselectedRingVisible` is true.
- Existing scoped pointer override remains unchanged so swatches stay clickable in `.pb-preview`.

- [ ] Add a failing source-contract assertion for `unselectedRingVisible`, `is-unselected`, ring variables, and conditional inactive rendering.
- [ ] Run the focused test and confirm it fails before Canvas support exists.
- [ ] Render inactive ring elements only for the opt-in mode; keep the default-off DOM equivalent to current behavior.
- [ ] Add scoped CSS for transparent/background ring, border width/color, and shared position/size/offset/radius variables.
- [ ] Run all Product Color Selector SFC compile and focused tests.

### Task 4: Keep the frontend parser and runtime state synchronized

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/frontend.blade.php`
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/runtime.js`
- Test: `tests/pagebuilder-v24-frontend-runtime.test.mjs`
- Test: `tests/Feature/PageBuilderElementorV24ProductColorSelectorWidgetTest.php`

**Interfaces:**
- Blade emits inactive ring markup only when `unselectedRingVisible` is enabled and keeps inactive indicators hidden otherwise.
- Runtime toggles active/inactive classes and visibility without recreating the widget or changing panel selection.
- Parser CSS uses sanitized ring variables and responsive shared placement values.

- [ ] Add a failing runtime test with three tabs that asserts inactive rings become visible only in opt-in mode while the active check remains unique.
- [ ] Add a failing Blade feature assertion for default-off output and opt-in ring CSS/markup.
- [ ] Run both tests and confirm they fail for the missing ring contract.
- [ ] Add sanitized Blade variables and state classes, then update runtime `setActive()` to preserve the ring for inactive tabs only when the root opt-in flag is true.
- [ ] Run focused Node/PHP tests, `php -l`, `node --check`, and `git diff --check`.

### Task 5: Full verification and Chrome QA

**Files:**
- Verify: all changed Product Color Selector files and existing v2.4 tests.

- [ ] Run `node --test tests/pagebuilder-v24-*.test.mjs` and require zero failures.
- [ ] Run all `tests/Feature/PageBuilderElementorV24*.php` and require zero failures.
- [ ] Run `php artisan view:cache` and syntax checks.
- [ ] Reload the already-open Chrome editor, add Product Color Selector, and verify default-off behavior shows only the active check.
- [ ] Enable `Show Unselected Ring` in the Style panel and verify inactive outline rings appear at the shared position.
- [ ] Disable it again and verify the Canvas returns to selected-check-only behavior.
- [ ] Verify the parser runtime test and report live published frontend visual QA as unverified if no saved v2.4 page is available.
- [ ] Refresh Graphify incrementally after source changes; do not stage `graphify-out`.

