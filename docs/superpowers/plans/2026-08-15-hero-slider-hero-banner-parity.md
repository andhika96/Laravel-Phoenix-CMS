# Hero Slider Hero Banner Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make every Hero Slider slide retain the core Hero Banner content, responsive positioning, media, button, and optional style-override capabilities while preserving the existing slider/video controls and stored data.

**Architecture:** Hero Banner behavior is modeled inside each normalized slide. Widget-level settings remain the source for shared style and slider behavior; a slide may enable style overrides that fall back to the widget defaults field by field. Pagination keeps legacy nine-point placement through Custom Placement while exposing direction-aware basic alignment.

**Tech Stack:** Vue 3 SFC loader, Page Builder v2.3 widget registry, Laravel Blade, native frontend runtime, Coloris, CKFinder, Node test runner, PHPUnit, Vite.

## Global Constraints

- Do not modify the existing Hero Banner contract.
- Preserve all current Hero Slider slider/video settings and normalize legacy values.
- Do not add dependencies.
- Browser QA is read-only; do not Save or Reset.
- Keep Desktop, Tablet, and Mobile values independently responsive.

---

### Task 1: Regression contract

**Files:**
- Modify: `tests/pagebuilder-v23-hero-slider-widget-parity.test.mjs`
- Modify: `tests/pagebuilder-v23-hero-slider-runtime.test.mjs`
- Modify: `tests/Feature/PageBuilderElementorV23HeroSliderWidgetTest.php`

**Interfaces:**
- Consumes: current Hero Slider definition, Settings SFC, Canvas SFC, Blade renderer, and frontend runtime.
- Produces: failing assertions for the new normalized slide schema, UI controls, canvas/frontend parity, and pagination mapping.

- [ ] Add assertions for responsive CKFinder media fields, `positioningMode`, content visibility/order, responsive position targets, button layout, complete button actions, and style overrides.
- [ ] Add assertions that direction-aware `center` resolves to `bottom-center` for horizontal and `center-right` for vertical while custom positions keep all nine legacy placements.
- [ ] Run the focused Node and PHPUnit tests and confirm the failures identify missing production behavior.

### Task 2: Slide data normalization

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/definition.js`

**Interfaces:**
- Produces: normalized slide fields consumed by Settings, Canvas, Blade, and runtime.

- [ ] Extend `defaultSlide()` with Hero Banner content, responsive position, responsive media presentation, button layout, and optional style override defaults.
- [ ] Normalize at most three complete buttons with link, popup, media, and attribute fields.
- [ ] Preserve existing title, subtitle, image/video, simple buttons, and nine-point pagination values.
- [ ] Run the focused parity test until the data-contract assertions pass.

### Task 3: Settings UI parity

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Settings.vue`
- Modify: `public/assets/css/pagebuilder_elementor_v23.css`

**Interfaces:**
- Consumes: normalized slide schema and editor responsive/media/link helpers.
- Produces: per-slide Hero Banner controls and compact responsive UI.

- [ ] Replace Tablet/Mobile image and poster text-only fields with CKFinder-capable media fields while retaining external URL input.
- [ ] Add Content Behavior, content visibility/order, and responsive grouped/independent position controls per slide.
- [ ] Replace the compact two-input button row with the Hero Banner accordion pattern, max three items, duplicate/remove, LinkControl, Video Popup, and Image Popup.
- [ ] Add responsive Button Group Layout and `Override Slide Style` conditional controls.
- [ ] Add direction-aware pagination alignment plus Custom Placement using the retained nine-point values.
- [ ] Reduce Media Slides title text to `10px` and keep icon/text gaps at `3px`.
- [ ] Run the focused parity test until Settings/CSS assertions pass.

### Task 4: Canvas and frontend rendering

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Canvas.vue`
- Modify: `resources/views/pagebuilder_elementor_v23/widgets/pro/hero-slider.blade.php`
- Modify: `public/assets/css/frontend_elementor.css`
- Modify only if required: `public/js/pagebuilder_elementor_v23/frontend-runtime.js`

**Interfaces:**
- Consumes: normalized per-slide Hero Banner fields and widget-level slider/style settings.
- Produces: matching editor canvas and rendered frontend markup.

- [ ] Render grouped content in normalized order or independent title/subtitle/button blocks using responsive anchor, X/Y, width, and alignment.
- [ ] Render complete link and popup buttons through the existing lightbox contract.
- [ ] Apply responsive object fit/position, button group direction/alignment/gap/wrap, and optional per-slide style variables.
- [ ] Resolve basic pagination alignment by slider direction and preserve Custom Placement.
- [ ] Run focused Node, runtime, and PHPUnit tests until rendering assertions pass.

### Task 5: Verification and QA

**Files:**
- Modify: `graphify-out/*` only through incremental Graphify update; keep ignored.

**Interfaces:**
- Consumes: completed source changes.
- Produces: test, build, visual, and graph evidence.

- [ ] Run all `tests/pagebuilder-v23-*.test.mjs`, relevant PHPUnit tests, `node --check`, Vite build, and `git diff --check`.
- [ ] Add a temporary unsaved Hero Slider in the editor and verify image pickers, buttons, grouped/independent positioning, style override, and both pagination modes.
- [ ] Confirm no browser console errors and do not Save/Reset.
- [ ] Run `graphify . --update --code-only --no-viz`, then confirm backup files are absent from `graph.json`.
- [ ] Review `git status` and `git diff` without resetting unrelated work.

### Task 6: Navigation and exact pagination placement follow-up

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Settings.vue`
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/definition.js`
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Canvas.vue`
- Modify: `resources/views/pagebuilder_elementor_v23/widgets/pro/hero-slider.blade.php`
- Modify: `public/js/pagebuilder_elementor_v23/frontend-runtime.js`
- Modify: `public/assets/css/frontend_elementor.css`
- Modify: Hero Slider Node and PHPUnit regression tests

**Interfaces:**
- Preserve `arrows` and `pagination` as the saved/runtime compatibility contract.
- Expose one Image Carousel-style `Navigation` selector that maps to those two booleans.
- Reuse the Image Carousel compact icon picker and keep the existing Hero Slider Arrow Buttons style controls.
- Keep independent responsive custom placement state for horizontal and vertical slider directions.

- [x] Add RED coverage for Navigation mapping, compact icon picker parity, signed responsive pagination offsets, exact anchor CSS, Blade output, and runtime resize behavior.
- [x] Implement `Navigation` as Arrows and Dots, Arrows, Dots, or None without removing the legacy booleans.
- [x] Replace the Hero-only text icon picker with the established Image Carousel icon-only picker.
- [x] Add responsive Horizontal Offset and Vertical Offset controls under each custom placement grid.
- [x] Apply offsets consistently in the editor canvas, Blade renderer, and frontend runtime for all nine anchors.
- [x] Preserve and verify Arrow Position, Edge Offset, Button Size, Icon Size, colors, hover states, and radius.
