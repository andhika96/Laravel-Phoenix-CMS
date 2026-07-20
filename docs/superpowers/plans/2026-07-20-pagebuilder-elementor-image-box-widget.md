# PageBuilder Elementor Image Box Widget Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a production-ready Elementor-style `Image Box` widget whose Content, Style, Advanced, responsive, editor-preview, and frontend-renderer behavior follow the approved design and the existing Accordion settings UI.

**Architecture:** Register `image_box` as a dedicated General widget with its own Vue preview component and Blade renderer. Keep node data in the existing `settings` object, reuse `WidgetAdvancedControls`, generalize the existing typography control through an explicit field prefix, and add small shared controls for links, dynamic tags, and CSS filters. Resolve dynamic values and local image renditions through narrowly scoped Laravel support classes; never fetch remote images or interpret arbitrary expressions.

**Tech Stack:** Laravel 13, PHP 8, Blade, Vue 3 SFCs loaded by `vue3-sfc-loader`, existing pagebuilder CSS, Intervention Image, PHPUnit feature/unit tests.

## Global Constraints

- Back up every existing file before the first edit into a timestamped directory under `C:\tmp\laravel-13-phoenix-backups`.
- Preserve the existing Accordion, Image, Heading, Text Editor, Tabs, Container, and Grid behavior.
- Use the existing light pagebuilder UI; copy Accordion spacing, tabs, collapsible sections, responsive selectors, state tabs, and popover behavior.
- Keep editor state, canvas preview, saved JSON, Blade renderer, responsive CSS, dynamic tags, and link semantics aligned.
- Treat screenshot-only controls as confirmed; do not invent an unobserved Hover Animation option.
- Do not issue network requests for media rendition generation. Only transform local files that pass real-path containment checks.
- Follow red-green-refactor: write or extend a focused test, confirm it fails for the intended missing behavior, implement the smallest complete slice, then rerun the focused test.
- Commit after each coherent green slice. Do not stage unrelated user changes.

---

## Task 1: Establish the Baseline and Backup Set

**Files:**

- Read: `public/js/pagebuilder_elementor/app.js`
- Read: `public/assets/css/pagebuilder_elementor.css`
- Read: `public/js/pagebuilder_elementor/widgets/shared/TypographyControl.vue`
- Read: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Read: `resources/views/pagebuilder_elementor/frontend_renderer.blade.php`
- Read: `routes/experimentalFeaturesWebv2.php`
- Read: `app/Http/Controllers/Web/PageBuilderElementor/PageBuilderElementor_Controller.php`

- [ ] Run the current focused parity baseline:

```powershell
php artisan test tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php tests/Feature/PageBuilderElementorIconWidgetContentParityTest.php
```

Expected: all current tests pass before Image Box edits.

- [ ] Record `git status --short` and do not overwrite unrelated changes.
- [ ] Create `C:\tmp\laravel-13-phoenix-backups\image-box-20260720-implementation` and copy every existing file listed above into matching relative subdirectories before editing any of them.
- [ ] Verify each backup exists and has the same SHA-256 hash as its source.

## Task 2: Lock Widget Registration, Defaults, and Normalization with Tests

**Files:**

- Create: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] Add a failing source-contract test asserting:
  - `image_box` maps to `/js/pagebuilder_elementor/widgets/general/ImageBox.vue`.
  - the General toolbox contains `Image Box`.
  - `makeNode('image_box')` uses a dedicated default factory.
  - defaults contain Content, Box, Image, Content-style, responsive, dynamic-binding, link, and shared Advanced keys.
  - normalization accepts only supported enum values and restores missing defaults.
- [ ] Run:

```powershell
php artisan test tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php --filter=registration
```

Expected: FAIL because Image Box is not registered.

- [ ] Implement `imageBoxWidgetDefaults()` in `app.js`, beginning with `...widgetAdvancedDefaults()` and adding:
  - content: `imageUrl`, `imageAlt`, `imageResolution`, `title`, `description`, `linkUrl`, `linkTarget`, `linkNofollow`, `linkCustomAttributes`, `titleTag`, `dynamicBindings`;
  - box: responsive `imagePosition`, `alignment`, `imageSpacing`, `contentSpacing`;
  - image: responsive `imageWidth`, `imageBorderRadius`, normal/hover border, opacity, filter, and transition fields;
  - content style: title and description color, typography, stroke, and shadow fields with responsive size/spacing variants.
- [ ] Add `normalizeImageBoxSettings()` with enum allowlists, scalar clamping, safe array/object normalization, and backward-compatible default filling.
- [ ] Register the widget map entry, General toolbox item, and `makeNode` case.
- [ ] Normalize loaded and newly created Image Box nodes through the existing node normalization path.
- [ ] Rerun the focused registration test until green.
- [ ] Commit:

```powershell
git add tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php public/js/pagebuilder_elementor/app.js
git commit -m "feat: register image box widget state"
```

## Task 3: Add Safe Dynamic Tag Resolution

**Files:**

- Create: `app/Support/PageBuilderElementor/DynamicTagResolver.php`
- Create: `tests/Unit/PageBuilderElementorDynamicTagResolverTest.php`
- Modify: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`

- [ ] Write failing unit tests for the fixed allowlist:
  - `page_title`
  - `page_excerpt`
  - `featured_image`
  - `page_url`
  - `site_title`
  - `site_url`
  - `user_display_name`
  - fallback to the stored static value when a binding is missing, unknown, or empty.
- [ ] Assert the resolver never evaluates templates, PHP, JavaScript, property paths, or arbitrary callables.
- [ ] Run:

```powershell
php artisan test tests/Unit/PageBuilderElementorDynamicTagResolverTest.php
```

Expected: FAIL because the resolver does not exist.

- [ ] Implement a final resolver class with `resolve(string $field, mixed $fallback, array $bindings, array $context): mixed` and `options(): array`.
- [ ] Normalize model/array context access without assuming optional page fields exist.
- [ ] Return escaped-safe scalar values only; URL and image values must be strings and remain subject to renderer URL validation.
- [ ] Rerun unit and Image Box parity tests until green.
- [ ] Commit:

```powershell
git add app/Support/PageBuilderElementor/DynamicTagResolver.php tests/Unit/PageBuilderElementorDynamicTagResolverTest.php tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php
git commit -m "feat: add safe pagebuilder dynamic tags"
```

## Task 4: Add the Local Image Rendition Resolver

**Files:**

- Create: `app/Support/PageBuilderElementor/ImageRenditionResolver.php`
- Create: `tests/Unit/PageBuilderElementorImageRenditionResolverTest.php`
- Modify: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`

- [ ] Write failing tests for the named sizes `thumbnail`, `medium`, `medium_large`, `large`, `1536x1536`, `2048x2048`, and `full`.
- [ ] Test that `full`, missing files, remote URLs, traversal attempts, non-images, files outside approved roots, and processing failures safely return the original URL.
- [ ] Test that a valid local image produces a deterministic hashed filename below `public/assets/pagebuilder_elementor/renditions`, preserves aspect ratio, never upscales, and can be reused.
- [ ] Run:

```powershell
php artisan test tests/Unit/PageBuilderElementorImageRenditionResolverTest.php
```

Expected: FAIL because the resolver does not exist.

- [ ] Implement the resolver using Intervention Image and real-path containment against the physical application roots used by public assets and CKFinder/File Manager storage.
- [ ] Create rendition directories lazily and fail closed without breaking page rendering.
- [ ] Do not add a public unauthenticated transformation endpoint; the frontend renderer resolves saved local media server-side, while the editor keeps the selected source URL because resolution changes do not alter the visual crop.
- [ ] Rerun unit and Image Box parity tests until green.
- [ ] Commit:

```powershell
git add app/Support/PageBuilderElementor/ImageRenditionResolver.php tests/Unit/PageBuilderElementorImageRenditionResolverTest.php tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php
git commit -m "feat: resolve image box media renditions"
```

## Task 5: Generalize Typography and Add Shared Link, Dynamic Tag, and CSS Filter Controls

**Files:**

- Modify: `public/js/pagebuilder_elementor/widgets/shared/TypographyControl.vue`
- Create: `public/js/pagebuilder_elementor/widgets/shared/LinkControl.vue`
- Create: `public/js/pagebuilder_elementor/widgets/shared/DynamicTagControl.vue`
- Create: `public/js/pagebuilder_elementor/widgets/shared/CssFilterControl.vue`
- Modify: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`
- Modify: `tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php`

- [ ] Add failing source-contract tests proving:
  - Typography accepts a `prefix` prop and computes every key from it.
  - omitting `prefix` retains Accordion's `header*` fields.
  - Link Control manages URL, target, nofollow, and custom attributes with accessible popover buttons.
  - Dynamic Tag Control exposes only the resolver allowlist and updates a single field binding.
  - CSS Filter Control exposes blur, brightness, contrast, saturation, and hue with compact reset support.
- [ ] Run the two focused parity test files and confirm the new assertions fail.
- [ ] Refactor Typography Control to use a validated prefix/key helper, preserving current markup, events, responsive behavior, and Accordion CSS selectors.
- [ ] Build Link Control with the same popover density and button styling already used by existing Icon link options.
- [ ] Build Dynamic Tag Control as a compact globe-trigger popover that binds/unbinds one field without changing its static fallback value.
- [ ] Build CSS Filter Control as a compact popover whose model value is a plain normalized object.
- [ ] Rerun the focused tests and the existing Accordion test until green.
- [ ] Commit:

```powershell
git add public/js/pagebuilder_elementor/widgets/shared/TypographyControl.vue public/js/pagebuilder_elementor/widgets/shared/LinkControl.vue public/js/pagebuilder_elementor/widgets/shared/DynamicTagControl.vue public/js/pagebuilder_elementor/widgets/shared/CssFilterControl.vue tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php
git commit -m "feat: add reusable image box controls"
```

## Task 6: Build the Editor Canvas Component

**Files:**

- Create: `public/js/pagebuilder_elementor/widgets/general/ImageBox.vue`
- Modify: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`

- [ ] Add failing tests asserting the component:
  - renders image, title, and description;
  - validates the title tag against the supported allowlist;
  - links the image and title only;
  - applies target, nofollow, noopener, noreferrer, and parsed custom attributes safely;
  - cascades desktop/tablet/mobile responsive values;
  - renders image position, alignment, spacing, width, borders, radius, filters, opacity, typography, stroke, and shadow;
  - reflects Normal/Hover state via CSS custom properties and class selectors;
  - emits the existing selection event and remains a leaf widget.
- [ ] Run the component-focused test and confirm failure.
- [ ] Implement `ImageBox.vue` with computed styles and sanitizers local to the component; do not use `v-html` for title or description.
- [ ] Use a local neutral image placeholder only when the saved URL is empty; otherwise render the chosen media.
- [ ] Ensure keyboard focus remains visible on linked image/title and reduced-motion users are not forced through transitions.
- [ ] Rerun the focused tests until green.
- [ ] Commit:

```powershell
git add public/js/pagebuilder_elementor/widgets/general/ImageBox.vue tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php
git commit -m "feat: render image box in editor canvas"
```

## Task 7: Build the Image Box Settings Panel with Accordion UI Rhythm

**Files:**

- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/assets/css/pagebuilder_elementor.css`
- Modify: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`

- [ ] Add failing assertions for a dedicated `selectedType==='image_box'` panel with Content, Style, and Advanced tabs.
- [ ] Assert the Content section contains Choose Image, Image Resolution, Title, Description, Link, and Title HTML Tag.
- [ ] Assert the Style tab contains collapsible Box, Image, and Content sections with every confirmed screenshot control, responsive device selectors beside responsive labels, and equal-width Normal/Hover tabs.
- [ ] Assert Advanced renders `WidgetAdvancedControls` with the same props/events as Accordion.
- [ ] Assert controls are conditional: border width/color only appear for non-default borders; filter fields live inside the filter popover; hover transition belongs to Hover state.
- [ ] Run the focused parity test and confirm failure.
- [ ] Register the three new shared async components with the Vue app.
- [ ] Add small helpers for responsive Image Box setting keys, current state, dynamic binding updates, safe custom attributes, and media selection.
- [ ] Build the panel using Accordion's `pb-tab-nav`, `pb-collapsible`, `pb-form-group`, label rows, device selectors, and state tabs.
- [ ] Add scoped Image Box panel CSS with 12-14px group spacing, 6-8px label-to-control spacing, non-colliding action rows, 48px tab/summary rhythm, compact popovers, and narrow-sidebar wrapping.
- [ ] Keep shared selectors narrow so Accordion and unrelated panels retain their existing box model.
- [ ] Rerun Image Box, Accordion, and Advanced parity tests until green.
- [ ] Commit:

```powershell
git add public/js/pagebuilder_elementor/app.js public/assets/css/pagebuilder_elementor.css tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php
git commit -m "feat: add image box settings experience"
```

## Task 8: Render Image Box on the Frontend

**Files:**

- Create: `resources/views/pagebuilder_elementor/partials/render_image_box.blade.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `resources/views/pagebuilder_elementor/frontend_renderer.blade.php`
- Modify: `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`

- [ ] Add failing renderer tests for static content, all supported title tags, empty fields, dynamic bindings, named image resolutions, responsive values, Normal/Hover styles, and shared Advanced attributes.
- [ ] Add security assertions for escaped title/description, safe URL protocols, `rel` synthesis, sanitized custom attributes, no `javascript:` URLs, and no description link.
- [ ] Run the renderer-focused tests and confirm failure.
- [ ] Add an `image_box` branch in `render_node.blade.php` that includes the dedicated partial inside the existing condition/cache/Advanced wrapper flow.
- [ ] Pass page context explicitly from `frontend_renderer.blade.php` into recursive node rendering.
- [ ] In the partial, resolve dynamic fields through `DynamicTagResolver`, resolve local renditions through `ImageRenditionResolver`, validate the title tag, and generate a unique scoped style block for responsive and hover rules.
- [ ] Match editor link behavior exactly: image and title link, description remains plain text.
- [ ] Rerun renderer, Accordion, Advanced, and full Image Box parity tests until green.
- [ ] Commit:

```powershell
git add resources/views/pagebuilder_elementor/partials/render_image_box.blade.php resources/views/pagebuilder_elementor/partials/render_node.blade.php resources/views/pagebuilder_elementor/frontend_renderer.blade.php tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php
git commit -m "feat: render image box on frontend"
```

## Task 9: Regression, Syntax, and Source Hygiene Verification

**Files:**

- Modify only if a failure proves an Image Box regression.

- [ ] Run focused coverage:

```powershell
php artisan test tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php tests/Unit/PageBuilderElementorDynamicTagResolverTest.php tests/Unit/PageBuilderElementorImageRenditionResolverTest.php tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php tests/Feature/PageBuilderElementorIconWidgetContentParityTest.php
```

Expected: all pass.

- [ ] Run the full pagebuilder test slice:

```powershell
php artisan test --filter=PageBuilderElementor
```

Expected: all pass.

- [ ] Validate new PHP files:

```powershell
php -l app/Support/PageBuilderElementor/DynamicTagResolver.php
php -l app/Support/PageBuilderElementor/ImageRenditionResolver.php
```

Expected: no syntax errors.

- [ ] Search changed files for `TODO`, `TBD`, `FIXME`, placeholder prose, debug logs, and accidental backup files.
- [ ] Run `git diff --check` and inspect `git diff --stat` plus `git status --short`.
- [ ] If verification requires a repair, write a failing regression assertion first, make the narrow fix, rerun the relevant tests, and commit:

  Stage only the explicitly inspected repair paths shown by `git status --short`, then commit them with message `fix: complete image box parity verification`.

## Task 10: Real Chrome Visual and Interaction QA

**Files:**

- Modify: `design-qa.md`
- Modify only proven mismatch files from Tasks 6-8.

- [ ] Start the existing local Laravel application using the project's normal Laragon URL.
- [ ] In the user's Chrome session, open the pagebuilder editor and add Image Box from General.
- [ ] Verify Content, Style, and Advanced panels at the same sidebar width as Accordion.
- [ ] Exercise image selection, each resolution, title/description, link options, HTML tag, responsive positions/alignment/spacing/width/radius, normal/hover borders/filters/opacity, typography, stroke, shadow, and shared Advanced controls.
- [ ] Save, reload, and compare editor preview with frontend output on desktop, tablet, and mobile.
- [ ] Capture the local Image Box panel and canvas at the same viewport/state as the supplied Elementor screenshots, combine reference and local captures for comparison, and correct only visible mismatches.
- [ ] If Chrome automation remains unavailable, record that limitation explicitly and do not claim visual QA passed; leave automated and source verification results intact.
- [ ] Append exact commands, browser states, evidence, and result to `design-qa.md`.
- [ ] Run the focused and full pagebuilder test slices again after any visual repair.
- [ ] Commit verified QA changes:

  Stage `design-qa.md` plus only the explicitly inspected paths changed by a proven visual repair, then commit them with message `test: verify image box editor parity`.

## Completion Criteria

- `Image Box` appears under General and can be dragged into valid builder targets.
- All screenshot-confirmed Content and Style options are present, readable, responsive, and functional.
- Advanced behavior is inherited from the same engine as Accordion.
- Editor preview, saved JSON, and frontend Blade output agree.
- Dynamic tags are allowlisted and fallback-safe.
- Local renditions are deterministic, contained, and never fetch remote URLs.
- Existing Accordion and related widget tests remain green.
- Focused and full pagebuilder automated verification pass.
- Real Chrome visual QA is either evidenced as passed or honestly recorded as blocked.

