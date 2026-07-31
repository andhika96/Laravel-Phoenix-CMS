# Page Builder Heading Elementor Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement complete Heading Content, Style, and shared Advanced parity across state, sidebar, canvas, persistence, and frontend output.

**Architecture:** Keep Heading-specific state and rendering inside its modular definition, Settings, Canvas, and Blade view. Reuse shared Link, Typography, Text Stroke, Text Shadow, AdvancedControls, editor shell styling, and WidgetAdvancedStyleResolver so Advanced behavior remains one engine.

**Tech Stack:** Laravel 13, Blade, Vue 3 runtime SFC loader, PHP feature tests, Node static contract tests, Coloris, Font Awesome.

## Global Constraints

- Preserve unrelated user changes and legacy saved Heading keys.
- Back up every existing file before editing.
- No production code before a focused failing test.
- Keep editor state, canvas, persistence, and frontend output aligned.
- Use only the user's Chrome session for browser verification.

---

### Task 1: Characterize the Heading parity gap

**Files:**
- Modify: `tests/Feature/PageBuilderElementorWidgetRegistryTest.php`
- Create: `tests/pagebuilder-heading-parity-static.test.mjs`

**Interfaces:**
- Consumes: registered Heading definition, Settings/Canvas SFCs, Blade view.
- Produces: failing contracts for defaults, controls, and renderer integration.

- [ ] Add assertions requiring link/dynamic/style/advanced defaults and Heading use of shared controls.
- [ ] Add static assertions requiring responsive alignment, typography, stroke, shadow, blend, Normal/Hover, and Advanced component wiring.
- [ ] Run the focused PHP and Node tests and confirm failures identify absent Heading parity.

### Task 2: Implement Heading state and sidebar controls

**Files:**
- Modify: `public/js/pagebuilder_elementor/widgets/basic/heading/definition.js`
- Modify: `public/js/pagebuilder_elementor/widgets/basic/heading/Settings.vue`

**Interfaces:**
- Consumes: `editor.dynamicTagControl`, `editor.linkControl`, `editor.typographyControl`, `editor.textStrokeControl`, `editor.textShadowControl`, `editor.widgetAdvancedControls`.
- Produces: normalized Heading settings shared by Canvas and Blade.

- [ ] Expand defaults and normalize legacy `text`, `align`, and `color` values.
- [ ] Build Content controls for title, dynamic tag, link options/dynamic tag, and tag.
- [ ] Build Style controls for responsive alignment, typography, stroke, shadow, blend mode, and Normal/Hover colors.
- [ ] Mount shared Advanced controls with media, responsive-device, and unsupported-AI callbacks.
- [ ] Run the focused static tests until green.

### Task 3: Align canvas preview and editor shell

**Files:**
- Modify: `public/js/pagebuilder_elementor/widgets/basic/heading/Canvas.vue`
- Modify: `public/js/pagebuilder_elementor/app.js`

**Interfaces:**
- Consumes: normalized Heading settings and responsive device.
- Produces: title/link styles plus Advanced wrapper styles and runtime classes.

- [ ] Add a failing contract for link structure, responsive title styles, and Heading inclusion in shared Advanced handling.
- [ ] Render the safe tag and optional anchor with typography, stroke, shadow, blend, and hover CSS variables.
- [ ] Include Heading in Advanced defaults, shell class/id/style, media, motion, and responsive handling.
- [ ] Run focused tests and verify green.

### Task 4: Align frontend rendering

**Files:**
- Modify: `resources/views/pagebuilder_elementor/widgets/basic/heading.blade.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `app/Support/PageBuilderElementor/WidgetAdvancedStyleResolver.php`

**Interfaces:**
- Consumes: persisted Heading settings.
- Produces: safe wrapper, responsive CSS, optional anchor, Advanced CSS/data attributes, display/cache behavior.

- [ ] Add failing feature assertions for tag/link/rel/custom attributes, typography/effects, responsive CSS, wrapper Advanced CSS, conditions, and cache.
- [ ] Resolve Heading Advanced settings through `WidgetAdvancedStyleResolver`.
- [ ] Render safe link/title markup and responsive/hover CSS with backward-compatible fallbacks.
- [ ] Extend fragment-cache routing to Heading without changing other widget cache behavior.
- [ ] Run focused PHP tests until green.

### Task 5: Complete generic Advanced gaps used by Heading

**Files:**
- Modify: `public/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `app/Support/PageBuilderElementor/WidgetAdvancedStyleResolver.php`
- Modify: affected Page Builder tests.

**Interfaces:**
- Consumes: shared Advanced setting schema.
- Produces: generic grid item, responsive transform, richer background, and per-side border behavior.

- [ ] Write failing contracts for grid spans, responsive transform, custom Classic positioning/sizing, and per-side border width.
- [ ] Implement those generic controls and matching preview/frontend resolution.
- [ ] Preserve existing Accordion and Image Box output through regression assertions.
- [ ] Keep Video/Slideshow controls capability-gated if their runtime media layer cannot be safely shared without widening the widget runtime contract.
- [ ] Run affected tests until green.

### Task 6: Broad verification and design QA

**Files:**
- Create: `design-qa.md`
- Create: implementation screenshots under the thread visualization directory.

**Interfaces:**
- Consumes: complete implementation and supplied Elementor screenshots.
- Produces: fresh automated and visual evidence.

- [ ] Run `php artisan test --filter=PageBuilderElementor --compact`.
- [ ] Run the focused Node static contract test and `git diff --check`.
- [ ] Open the local builder in Chrome and verify Content, Style, Advanced, responsive preview, save/reload, and frontend output.
- [ ] Capture matching panel states and compare them with the supplied screenshots.
- [ ] Write `design-qa.md`; fix all P0/P1/P2 findings and repeat until `final result: passed`, or accurately mark it blocked.
