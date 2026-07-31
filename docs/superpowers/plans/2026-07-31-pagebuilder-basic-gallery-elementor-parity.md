# Page Builder Basic Gallery Elementor Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a dedicated Elementor-style Basic Gallery widget in the General category with aligned editor, canvas, persistence, frontend, and tests.

**Architecture:** Register `basic_gallery` as a complex widget with focused definition, Settings, Canvas, and Blade renderer files. Reuse the established gallery item schema, media picker helpers, image rendition resolver, shared Advanced controls, and a shared safe lightbox opener without importing carousel behavior.

**Tech Stack:** Laravel 13, Blade, Vue 3 browser SFC loader, JavaScript, CSS, CKFinder, PHPUnit/Pest.

## Global Constraints

- Use only `https://playground.elementor.com/demo/flexbox` as the Elementor runtime reference.
- Preserve all unrelated working-tree changes.
- Back up every existing source file before modifying it.
- Keep editor state, canvas preview, saved JSON, Blade output, and frontend runtime aligned.
- Do not add carousel behavior to Basic Gallery.

---

### Task 1: Parity contract and RED test

**Files:**
- Create: `tests/Feature/PageBuilderElementorBasicGalleryWidgetParityTest.php`
- Modify: `tests/Feature/PageBuilderElementorComplexWidgetModulesTest.php`

**Interfaces:**
- Consumes: `config('pagebuilder_elementor_widgets')`, widget source files, and Blade rendering.
- Produces: executable requirements for `basic_gallery`.

- [ ] Write assertions for registry paths, normalized defaults, all mapped controls, grid canvas, safe frontend output, and lightbox.
- [ ] Run `php artisan test --filter=PageBuilderElementorBasicGalleryWidgetParityTest --stop-on-failure`.
- [ ] Confirm the test fails because `basic_gallery` is not registered.

### Task 2: Registry, defaults, and normalized state

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/general/basic-gallery/definition.js`
- Modify: `config/pagebuilder_elementor_widgets.php`
- Modify: `public/js/pagebuilder_elementor/app.js`

**Interfaces:**
- Consumes: `chooseMediaGallery`, `moveMediaGalleryItem`, `removeMediaGalleryItem`, responsive-setting helpers, and `ImageRenditionResolver`.
- Produces: `PageBuilderElementorComplexWidgetRuntime.basic_gallery.defaults()` and `.normalize(node)`.

- [ ] Back up all existing files in this task with a timestamped `basic_gallery` suffix.
- [ ] Register the widget and add bounded enum, number, responsive, and gallery normalization.
- [ ] Run the focused parity test and confirm the state/registry assertions pass.

### Task 3: Sidebar and canvas

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/general/basic-gallery/Settings.vue`
- Create: `public/js/pagebuilder_elementor/widgets/general/basic-gallery/Canvas.vue`
- Modify: `public/js/pagebuilder_elementor/app.js`

**Interfaces:**
- Consumes: normalized `node.settings`, shared media helpers, typography, text shadow, color, responsive, and Advanced controls.
- Produces: stable Content, Style, Advanced panels and a responsive grid preview.

- [ ] Implement Content and conditional controls from the approved mapping.
- [ ] Implement Images and Caption style groups with compact, responsive controls.
- [ ] Render safe grid/empty preview and apply shared Advanced preview.
- [ ] Run focused tests plus the Settings SFC structure test.

### Task 4: Frontend renderer and lightbox

**Files:**
- Create: `resources/views/pagebuilder_elementor/partials/render_basic_gallery.blade.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `public/js/pagebuilder_elementor/frontend-runtime.js`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Consumes: normalized saved JSON, `ImageRenditionResolver`, `WidgetAdvancedStyleResolver`.
- Produces: safe responsive gallery markup and lightbox interaction.

- [ ] Back up all existing files in this task.
- [ ] Render the grid, captions, links, lightbox attributes, responsive CSS variables, and Advanced output.
- [ ] Extract/reuse a safe lightbox opener for Image Carousel and Basic Gallery.
- [ ] Run focused tests and JavaScript/PHP syntax checks.

### Task 5: Regression and runtime verification

**Files:**
- Modify: `tests/Feature/PageBuilderElementorComplexWidgetModulesTest.php`
- Test: `tests/Feature/PageBuilderElementorBasicGalleryWidgetParityTest.php`

**Interfaces:**
- Consumes: complete editor and frontend implementation.
- Produces: verification evidence and an updated graph.

- [ ] Run `php artisan test --filter=PageBuilderElementor --compact --stop-on-failure`.
- [ ] Run `node --check`, `php -l`, and `git diff --check`.
- [ ] Runtime-test General toolbox registration, drag/drop, Content, Style, Advanced, empty grid, populated grid when authenticated media is available, and console logs.
- [ ] Run `graphify update .`, query the Basic Gallery path, and save a useful outcome.

