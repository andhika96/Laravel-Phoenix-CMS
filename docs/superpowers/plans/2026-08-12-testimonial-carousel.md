# Testimonial Carousel Widget Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the isolated v2.3 Pro `Testimonial Carousel` widget with Elementor Content, Style, Advanced, responsive, canvas, frontend, and carousel runtime parity.

**Architecture:** Register a new `testimonial_carousel` definition and reuse the existing Pro shared Settings/Canvas plus the existing `data-pro-carousel` frontend runtime. Add dedicated testimonial branches and CSS rather than aliasing Reviews, because the Elementor repeater and visual controls are different.

**Tech Stack:** Laravel Blade, PHP feature tests, Vue 3 SFC loaded by the v2.3 editor, Node `node:test`, Vue SSR, existing frontend runtime, Chrome read-only QA, Graphify navigation.

## Global Constraints

- Modify only the Page Builder Elementor v2.3 track; preserve v2.0.
- Use `testimonial_carousel` as the stable widget type and `Testimonial Carousel` as the label.
- Use the existing shared `Settings.vue`, `Canvas.vue`, AdvancedControls, responsive helpers, safe media helpers, and `data-pro-carousel` runtime.
- Do not add Rating, Icon, or Link fields from Reviews to this widget.
- Create timestamped backups before modifying existing source or test files.
- Do not Save or Submit in Chrome; browser QA is read-only.
- Do not stage or modify `graphify-out` manually; update Graphify only after the source change is verified.

---

### Task 1: Add failing parity tests

**Files:**
- Create: `tests/pagebuilder-v23-testimonial-carousel-widget-parity.test.mjs`
- Create: `tests/Feature/PageBuilderElementorV23TestimonialCarouselWidgetTest.php`

**Interfaces:**
- Consumes the future registry type `testimonial_carousel` and the existing shared SFC/Blade renderer.
- Produces executable RED checks for the definition, settings, canvas, registry, and frontend markup.

- [x] **Step 1: Write the failing Node test**

Assert that the definition registers as Pro, has three default items, validates `skin`, `layout`, `pagination`, slide counts, image dimensions, and exposes the Content labels (`Slides Name`, `Content`, `Image`, `Name`, `Title`, `Skin`, `Layout`, `Alignment`, `Slides Per View`, `Slides to Scroll`, `Width`, `Additional Options`, `Pagination`, `Transition Duration`, `Image Resolution`, `Lazy Load`). Assert Style labels (`Space Between`, `Background Color`, `Border Width`, `Border Radius`, `Padding`, `Gap`, `Text Stroke`, `Text Shadow`, `Image`, `Navigation`, `Active Color`) and SSR canvas markers (`data-pro-carousel`, `.pb-pro-testimonial-carousel`, image/name/title/content, previous/next).

- [x] **Step 2: Write the failing PHP test**

Follow the existing Reviews test shape and render the new registry view with two items. Assert `label`, `category`, v2.3 definition path, `data-pro-widget="testimonial_carousel"`, `data-pro-carousel`, skin/layout classes, safe image markup, `data-pro-config`, pagination controls, and text escaping. Include a malicious attribute-like string in content and assert it is escaped.

- [x] **Step 3: Run the focused tests and verify RED**

Run:

```powershell
node --test tests/pagebuilder-v23-testimonial-carousel-widget-parity.test.mjs
php artisan test tests/Feature/PageBuilderElementorV23TestimonialCarouselWidgetTest.php
```

Expected result: both commands fail because `testimonial_carousel` and its active renderer branch do not exist yet; fix test setup if the failure is an import/fixture error rather than a missing feature.

### Task 2: Register the widget and defaults

**Files:**
- Create: `public/js/pagebuilder_elementor_v23/widgets/pro/testimonial-carousel/definition.js`
- Modify: `config/pagebuilder_elementor_v23_widgets.php`
- Modify: `public/js/pagebuilder_elementor_v23/app.js`

**Interfaces:**
- `definition.js` exports through `window.PageBuilderElementorV23Widgets.register()` with `defaults()` and `normalize(node)`.
- Config points `definition`, `canvas`, `settings`, and the existing Pro renderer view to v2.3 paths.
- `app.js` maps the label/icon and includes the type in `hasNewGeneralAdvancedControls()`.

- [x] **Step 1: Back up the three existing files**

Create timestamped siblings such as `config/pagebuilder_elementor_v23_widgets.php.bak_YYYYMMDD_HHMMSS_testimonial_carousel`, `public/js/pagebuilder_elementor_v23/app.js.bak_YYYYMMDD_HHMMSS_testimonial_carousel`, and keep the new definition outside the backup set.

- [x] **Step 2: Add the definition and normalizer**

Use the approved data contract from `docs/superpowers/specs/2026-08-12-testimonial-carousel-design.md`. Default items use the Elementor placeholder URL, quote text, `John Doe`, and `CEO`. Normalize enum values to `default|bubble`, `image_inline|image_stacked|image_above|image_left|image_right`, `none|dots|fraction|progress`; clamp slide counts and custom image dimensions; coerce carousel toggles to booleans; and normalize every item to `id/content/imageUrl/name/title`.

- [x] **Step 3: Add registry and editor metadata**

Register the module under the `pro` category with icon `fas fa-quote-right`, shared v2.3 Canvas/Settings paths, and `pagebuilder_elementor_v23.partials.render_pro_widget`. Add `testimonial_carousel: 'Testimonial Carousel'` and `testimonial_carousel: 'fas fa-quote-right'` to the v2.3 label/icon maps and append the type to `hasNewGeneralAdvancedControls()`.

- [x] **Step 4: Run the definition test**

Run `node --test tests/pagebuilder-v23-testimonial-carousel-widget-parity.test.mjs` and verify the registry/default assertions now pass while Settings/Canvas assertions still identify the next missing slice.

### Task 3: Map Content and Style controls in the shared Settings component

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue`

**Interfaces:**
- The existing editor passes `node`, `editor`, `editor.typographyControl`, `editor.textStrokeControl`, `editor.textShadowControl`, media controls, responsive controls, and AdvancedControls.
- New branches are selected only when `type === 'testimonial_carousel'`.

- [x] **Step 1: Back up Settings.vue**

Create `public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue.bak_YYYYMMDD_HHMMSS_testimonial_carousel` and leave prior backups untouched.

- [x] **Step 2: Add Content controls**

Add a Slides section with `Slides Name`, a duplicate-capable repeater whose item fields are `Content` textarea, `Image` media control, `Name`, and `Title`, then responsive `Alignment`, `Slides Per View`, `Slides to Scroll`, and `Width`. Add Additional Options with Arrows, Pagination, Transition Duration, Autoplay, conditional Autoplay Speed, Infinite Loop, Pause on Hover, Pause on Interaction, Image Resolution, conditional Custom Width/Height, and Lazy Load.

- [x] **Step 3: Add Style controls**

Add Slides controls for Space Between, Background Color, Border Width, Border Radius, Border Color, and Padding. Add Content controls for Gap, Text Color, Typography, Text Stroke, Text Shadow, Name Text Color/Typography, and Title Text Color/Typography. Add Image controls for Size, Gap, Border toggle, conditional Border Color/Border Width, and Border Radius. Add Navigation controls for arrows and pagination dot size/spacing/colors, respecting the Arrows/Pagination settings.

- [x] **Step 4: Run the Settings SSR assertions**

Run the focused Node test and verify all mapped labels render, the custom-image and border conditional branches are reachable, and Advanced still renders through the existing `editor.widgetAdvancedControls` component.

### Task 4: Render the editor canvas and interaction styles

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue`

**Interfaces:**
- The new branch emits `.pb-pro-carousel.pb-pro-testimonial-carousel`, `data-pro-carousel`, `data-pb-interactive` arrows/dots, and uses existing `previous`, `next`, `selectSlide`, `carouselPageCount`, `carouselTrackStyle`, `carouselRootStyle`, autoplay, and responsive helpers.

- [x] **Step 1: Back up Canvas.vue**

Create `public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue.bak_YYYYMMDD_HHMMSS_testimonial_carousel`.

- [x] **Step 2: Add testimonial markup**

Render each item as quote content, image, name, and title with layout/skin classes. Apply safe media URLs, image size/gap/border styles, text typography, Text Stroke and Text Shadow on quote content, and responsive width/alignment. Render arrows and dots/fraction/progress only when their settings allow them.

- [x] **Step 3: Extend shared carousel type lists**

Include `testimonial_carousel` in the visible-count, max-index, arrow step, autoplay step, and slider interaction arrays. Keep `media_carousel` slideshow/fade/cube special cases unchanged.

- [x] **Step 4: Add isolated CSS**

Add only `.pb-pro-testimonial-carousel` selectors for Default/Bubble skins and five layouts. Keep the generic arrow/dot geometry shared; add no new global runtime or broad selector that can affect Reviews/Media Carousel.

- [x] **Step 5: Run SSR canvas tests**

Run the focused Node test and verify class, content, image, text effect, arrow, dot, and responsive style assertions pass.

### Task 5: Render the frontend Blade output

**Files:**
- Modify: `resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php`

**Interfaces:**
- The switch branch consumes the normalized settings from `render_node.blade.php` and existing `$safeMediaUrl`, `$responsive`, `$safeLength`, `$safeColor`, `$safeShadow`, `$typographyStyle`, `$jsonConfig`, and image rendition resolver closures.
- The branch emits the same `data-pro-config` keys consumed by `frontend-runtime.js`.

- [x] **Step 1: Back up the Blade renderer**

Create `resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php.bak_YYYYMMDD_HHMMSS_testimonial_carousel`.

- [x] **Step 2: Add the renderer branch and responsive maps**

Add testimonial typography prefixes and responsive CSS declarations, then render escaped quote/name/title text, safe/resolved images with optional lazy loading, skin/layout classes, and arrows/dots/fraction/progress controls. Apply quote Text Stroke and Text Shadow with sanitized CSS values.

- [x] **Step 3: Run the PHP renderer test**

Run `php artisan test tests/Feature/PageBuilderElementorV23TestimonialCarouselWidgetTest.php` and verify the output is escaped, contains the interactive carousel contract, and has no v2.0 asset/view references.

### Task 6: Verify the integrated v2.3 slice

**Files:**
- Inspect all changed files and new focused tests.

- [x] **Step 1: Run focused Node and PHP suites**

```powershell
node --test tests/pagebuilder-v23-testimonial-carousel-widget-parity.test.mjs tests/pagebuilder-v23-frontend-runtime.test.mjs tests/pagebuilder-v23-widget-runtime-parity.test.mjs
php artisan test tests/Feature/PageBuilderElementorV23TestimonialCarouselWidgetTest.php tests/Feature/PageBuilderElementorV23WidgetParityTest.php tests/Feature/PageBuilderElementorV23AssetIsolationTest.php tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php
```

- [x] **Step 2: Run syntax and diff checks**

Run `node --check public/js/pagebuilder_elementor_v23/app.js`, `php -l config/pagebuilder_elementor_v23_widgets.php`, `php -l resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php`, and `git diff --check`.

- [x] **Step 3: Inspect the diff and isolation**

Confirm only v2.3 files, focused tests, specification, and plan changed; confirm no `.bak` is staged or used as source and no v2.0 path was introduced.

### Task 7: Read-only Chrome QA and Graphify update

**Files:**
- Generated Graphify output only if the source change makes the existing graph stale.

- [x] **Step 1: Refresh the v2.3 editor**

Hard-refresh the existing v2.3 tab, inspect the Pro toolbox for `Testimonial Carousel`, add/select it without Save/Submit, and inspect Content, Style, Advanced, arrows/dots, and one responsive device. Record console warnings/errors.

- [x] **Step 2: Verify the demo-parity boundaries**

Check that the repeater does not expose Reviews-only Rating/Icon/Link fields, that Image Border conditional controls appear, and that the five layouts and two skins update the canvas without selecting the widget when interactive controls are clicked.

- [x] **Step 3: Update Graphify incrementally**

Run the repository’s incremental Graphify update only after source/runtime tests pass, confirm the report is built from the current commit, and leave `graphify-out` unstaged.
