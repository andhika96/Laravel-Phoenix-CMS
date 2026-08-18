# Hero Natural Image Mode Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a responsive Natural Image mode to Hero Banner and image slides in Hero Slider, preserving each source image's ratio while retaining Cover as the existing default.

**Architecture:** Persist a small `imageLayout` enum next to the existing responsive media settings. The Canvas and Blade renderer will select the matching class/data attribute; the frontend runtime will remove the slider minimum-height floor only for an active natural image slide and recalculate on image load. No new dependency or database migration is needed.

**Tech Stack:** Laravel Blade, Vue 3 single-file components, vanilla browser runtime, Node test runner, PHPUnit.

**Spec:** User-approved design brief in this conversation (2026-08-18); `Cover` remains the default and `Natural Image Ratio` is responsive.

## Global Constraints

- Work only in Page Builder v2.3 Hero Banner and Hero Slider paths.
- Keep all existing saved settings compatible by defaulting missing or invalid layouts to `cover`.
- Do not modify user-owned dirty test files unrelated to these widgets.
- Keep editor state, Canvas preview, saved settings, and frontend output aligned.
- Use no new package, database migration, Figma, deployment, commit, or push.

---

### Task 1: Establish the failing Natural Image contract

**Files:**
- Modify: `tests/Feature/PageBuilderElementorV23HeroBannerWidgetTest.php`
- Modify: `tests/Feature/PageBuilderElementorV23HeroSliderWidgetTest.php`
- Modify: `tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs`
- Modify: `tests/pagebuilder-v23-hero-slider-widget-parity.test.mjs`
- Modify: `tests/pagebuilder-v23-hero-slider-runtime.test.mjs`

**Interfaces:**
- Consumes: current Hero Banner `imageUrl` settings and Hero Slider `slides[]` media settings.
- Produces: executable assertions for `imageLayout: natural`, responsive fallback, rendered classes/data, and the slider's natural-height floor.

- [ ] **Step 1: Write failing PHP renderer cases**

  Add a Hero Banner node with `imageLayout => 'natural'` and assert its rendered section contains `is-natural-image` plus its ordinary `<picture><img>`. Add a Hero Slider image slide with `imageLayout => 'natural'` and assert the output contains `data-hero-image-layout="natural"` and the value inside `data-hero-slider-config`.

- [ ] **Step 2: Run the PHP tests and verify RED**

  Run: `php artisan test tests/Feature/PageBuilderElementorV23HeroBannerWidgetTest.php tests/Feature/PageBuilderElementorV23HeroSliderWidgetTest.php`

  Expected before implementation: the new renderer assertions fail because no Natural Image class/data exists.

- [ ] **Step 3: Write failing editor/runtime cases**

  Assert that both widget defaults normalize missing/invalid layouts to `cover`, responsive tablet/mobile values inherit correctly, Canvas markup/classing recognizes `natural`, and `initHeroSlider()` uses natural image dimensions without its configured minimum-height floor. Use a literal image size fixture such as `1200x500` and a root width of `600` so the expected height is the hand-derived `250px`.

- [ ] **Step 4: Run the Node tests and verify RED**

  Run: `node --test tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs tests/pagebuilder-v23-hero-slider-widget-parity.test.mjs tests/pagebuilder-v23-hero-slider-runtime.test.mjs`

  Expected before implementation: the new layout/default/runtime assertions fail because `imageLayout` has no implementation.

### Task 2: Add Banner state, editor control, Canvas, and frontend rendering

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/definition.js`
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Settings.vue`
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Canvas.vue`
- Modify: `resources/views/pagebuilder_elementor_v23/widgets/pro/hero-banner.blade.php`
- Modify: `public/assets/css/frontend_elementor_v23.css`

**Interfaces:**
- Consumes: `imageLayout`, `imageLayoutTablet`, and `imageLayoutMobile` values.
- Produces: `cover | natural` media layout behavior for editor Canvas and published Blade output.

- [ ] **Step 1: Back up every existing Task 2 file**

  Copy each file to a timestamped sibling backup, using the `natural_image_mode` suffix. Confirm all copies exist before editing.

- [ ] **Step 2: Implement the smallest state and UI change**

  Add `imageLayout: 'cover'` plus blank tablet/mobile overrides; normalize desktop to `cover|natural` and responsive overrides to blank or valid values. In Responsive Media, add a responsive `Image Layout` select with `Cover (fixed height)` and `Natural Image Ratio`; hide Object Fit and Object Position when Natural is effective.

- [ ] **Step 3: Implement Canvas and Blade rendering**

  Give the Canvas and Blade root `is-natural-image` only when the effective layout is `natural`. In Natural mode, keep the existing `<img src>` source but make the Banner picture/image ordinary-flow `width:100%; height:auto`, clear the Banner fixed minimum height, and retain overlay/content as absolute layers.

- [ ] **Step 4: Add the minimal v2.3 frontend CSS rule**

  Extend the active `frontend_elementor_v23.css` Hero Banner rules with the natural modifier. Do not edit legacy v2 CSS.

- [ ] **Step 5: Run Banner checks and verify GREEN**

  Run: `php artisan test tests/Feature/PageBuilderElementorV23HeroBannerWidgetTest.php`

  Run: `node --test tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs`

### Task 3: Add Slider state, editor control, Canvas, Blade metadata, and natural-height runtime

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/definition.js`
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Settings.vue`
- Modify: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Canvas.vue`
- Modify: `resources/views/pagebuilder_elementor_v23/widgets/pro/hero-slider.blade.php`
- Modify: `public/js/pagebuilder_elementor_v23/frontend-runtime.js`
- Modify: `public/assets/css/frontend_elementor_v23.css`

**Interfaces:**
- Consumes: each image slide's responsive `imageLayout` values.
- Produces: per-slide natural-ratio sizing while leaving video and Cover slides unchanged.

- [ ] **Step 1: Back up every existing Task 3 file**

  Copy each file to a timestamped sibling backup, using the `natural_image_mode` suffix. Confirm all copies exist before editing.

- [ ] **Step 2: Implement slide state and editor control**

  Add the same `cover | natural` responsive layout fields to `defaultSlide`, normalizer output, and new-slide construction. Place the responsive Image Layout control in the image-only portion of the slide Media editor and show Object Fit/Position only for Cover.

- [ ] **Step 3: Keep Canvas preview in sync**

  Tag an effective natural image slide in Canvas, capture an image's intrinsic ratio on load, and make only the active natural slide use that ratio with zero minimum height. Keep the current fixed/adaptive behavior for Cover and video slides.

- [ ] **Step 4: Implement frontend metadata and runtime sizing**

  Normalize the layout in Blade, emit the active slide's layout as `data-hero-image-layout`, include it in runtime slide config, and update `initHeroSlider().updateHeight()` so natural image slides use their intrinsic ratio with minimum `0px`; reset inline height/min-height when returning to a fixed Cover/video slide. Recalculate after image load and on resize.

- [ ] **Step 5: Add the required v2.3 slider CSS base/modifier**

  Reuse the existing v2 Hero Slider base CSS as the narrow active-v2.3 equivalent, then add only the natural-mode modifier needed for the current image layout feature. Do not change the legacy stylesheet.

- [ ] **Step 6: Run Slider checks and verify GREEN**

  Run: `php artisan test tests/Feature/PageBuilderElementorV23HeroSliderWidgetTest.php`

  Run: `node --test tests/pagebuilder-v23-hero-slider-widget-parity.test.mjs tests/pagebuilder-v23-hero-slider-runtime.test.mjs`

### Task 4: Integrated verification and runtime inspection

**Files:**
- Modify: none unless an observed, scoped test failure requires a minimal correction.

**Interfaces:**
- Consumes: completed Tasks 1–3.
- Produces: fresh static, feature, Node, and browser evidence.

- [ ] **Step 1: Inspect the scoped diff**

  Run: `git diff --check -- <all modified widget paths and tests>`

  Expected: exit 0 with no whitespace errors.

- [ ] **Step 2: Run the focused feature and Node suite together**

  Run: `php artisan test tests/Feature/PageBuilderElementorV23HeroBannerWidgetTest.php tests/Feature/PageBuilderElementorV23HeroSliderWidgetTest.php tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php`

  Run: `node --test tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs tests/pagebuilder-v23-hero-slider-widget-parity.test.mjs tests/pagebuilder-v23-hero-slider-runtime.test.mjs`

- [ ] **Step 3: Run a browser/runtime check**

  Use the existing local Laravel runtime to create a safe temporary or page-scoped preview fixture only if necessary, inspect desktop/tablet/mobile Natural Image behavior, and verify Cover behavior remains unchanged. Do not alter the MG5 page configuration solely for this feature test.

- [ ] **Step 4: Update Graphify incrementally if the project supports it**

  Run the narrow Graphify update/query for the changed Hero Banner and Hero Slider files; do not commit graph artifacts.
