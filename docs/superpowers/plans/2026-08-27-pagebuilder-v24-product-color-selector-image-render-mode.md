# Product Color Selector Image Render Mode Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task with review checkpoints.

**Goal:** Let Product Color Selector choose between a semantic `<img>` renderer and a CSS `background-image` renderer without changing existing pages by default.

**Architecture:** Add one widget-level `imageRenderMode` setting with `image` as the default and `background` as the opt-in value. Both modes reuse the existing per-item responsive URLs, `Image Fit`, `Image Position`, aspect ratio, and placeholder behavior; only the media element and its CSS rendering strategy differ.

**Tech Stack:** Laravel Blade, Vue 3 SFC, vanilla frontend runtime, Node `node:test`, PHPUnit, Chrome QA.

**Spec:** User-approved design in this conversation and screenshot `C:\Users\CAHYO\AppData\Local\Temp\codex-clipboard-578d6481-c9b6-4cef-95af-28808b25f46d.png`.

## Global Constraints

- `Image` remains the default and preserves current parser/Canvas markup for legacy data.
- `Background Image` is a widget-level mode, not a per-color-item mode.
- Responsive image URLs, fit, position, aspect ratio, and fallback placeholder stay consistent in both modes.
- Use the existing `pb-form-group`, `pb-select`, media picker, and responsive controls; no bespoke settings UI.
- Sanitize every URL before putting it in an inline CSS declaration or HTML attribute.
- Preserve the dirty worktree and create timestamped backups before editing existing files.
- Do not Save, Reset, commit, push, reset, or clean.

---

### Task 1: Add the image mode contract

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/definition.js`
- Test: `tests/pagebuilder-v24-product-color-selector-image-mode.test.mjs`

- [ ] Write a failing test for `imageRenderMode: image` and invalid-value fallback to `image`.
- [ ] Run the focused test and confirm it fails because the setting is absent.
- [ ] Add the default and enum normalization without altering existing image URL normalization.
- [ ] Run the focused test and confirm it passes.

### Task 2: Expose the mode in the existing Content controls

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Settings.vue`
- Test: `tests/pagebuilder-v24-product-color-selector-image-mode.test.mjs`

- [ ] Add a failing static assertion for `Image Render Mode`, `Image`, and `Background Image`.
- [ ] Add the select beside `Image Fit`/`Image Position` under `Selection & Layout` using the existing form-group/select pattern.
- [ ] Keep the current per-item image picker and responsive image fields unchanged.
- [ ] Run the SFC compile/static test.

### Task 3: Implement Canvas rendering parity

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Canvas.vue`
- Test: `tests/pagebuilder-v24-product-color-selector-image-mode.test.mjs`

- [ ] Add a failing source assertion for mode branching, background media class, and sanitized CSS URL handling.
- [ ] Render the current `<img>` path for `image`; render a `role="img"` background media element for `background`.
- [ ] Reuse existing responsive image selection, fit, position, aspect-ratio, and placeholder state.
- [ ] Add only the CSS needed to map `contain`, `cover`, and `fill` to background sizing.
- [ ] Compile Canvas and run focused tests.

### Task 4: Implement frontend parser parity

**Files:**
- Modify: `resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/frontend.blade.php`
- Test: `tests/Feature/PageBuilderElementorV24ProductColorSelectorWidgetTest.php`

- [ ] Add a failing Blade test for default `<img>` output and opt-in background output with responsive URLs.
- [ ] Normalize mode to `image` for invalid/raw values and keep URL sanitization before `url(...)`.
- [ ] Emit the matching background media element and responsive CSS variables/media overrides.
- [ ] Keep direct-image `<picture>` output unchanged in `image` mode.
- [ ] Run focused PHP tests, `php -l`, `view:cache`, and `git diff --check`.

### Task 5: Full regression and Chrome QA

**Files:**
- Verify all changed Product Color Selector files and v2.4 tests.

- [ ] Run all `tests/pagebuilder-v24-*.test.mjs` and all `tests/Feature/PageBuilderElementorV24*.php`.
- [ ] Reload the existing Chrome editor and add Product Color Selector without saving.
- [ ] Verify default `Image` mode renders the direct image path.
- [ ] Switch to `Background Image` and verify the Canvas changes to the background media element while retaining fit/position controls.
- [ ] Switch back to `Image` and verify the original path returns.
- [ ] Confirm browser console has no errors/warnings.
- [ ] Refresh Graphify incrementally and leave generated graph artifacts unstaged.

