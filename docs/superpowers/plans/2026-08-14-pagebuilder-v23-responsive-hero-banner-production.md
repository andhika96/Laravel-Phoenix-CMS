# Page Builder v2.3 Responsive Hero Banner Production Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking. This session executes inline because delegation was not requested.

**Goal:** Add the approved Hero Banner as a fully functional Pro widget in Page Builder v2.3, aligned across editor state, settings, canvas, persistence, frontend rendering, and shared popup runtime.

**Architecture:** Use a dedicated widget module (`definition.js`, `Settings.vue`, `Canvas.vue`, Blade view) and reuse the v2.3 registry, responsive suffix convention, CKFinder chooser, LinkControl, AdvancedControls, persistence pipeline, and media lightbox. Only the small registry/app/runtime seams are modified.

**Tech Stack:** Laravel Blade/PHP, Vue 3 SFC runtime loader, vanilla JavaScript, CSS, Node assert tests, PHPUnit.

## Global Constraints

- Type `hero_banner`, label `Hero Banner`, category `pro`, icon `fas fa-image`.
- Maximum three buttons and minimum one.
- Page Builder v2.0 remains untouched.
- Existing unsaved editor data is not saved or replaced during QA.
- Existing dependencies only; no new package.
- Timestamped backups before every existing-file modification.
- No commit, stage, push, or PR unless explicitly requested.

---

### Task 1: Establish red tests for the production contract

**Files:**
- Create: `tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs`
- Create: `tests/Feature/PageBuilderElementorV23HeroBannerWidgetTest.php`

**Interfaces:**
- Consumes: existing config registry, SFC text contracts, Blade view dispatch, and `frontend-runtime.js`.
- Produces: executable contract for `hero_banner` registry, settings/canvas fields, shared runtime binding, safe frontend markup, and responsive CSS.

- [ ] Write Node assertions for the missing definition/config/app/SFC/runtime/CSS markers.
- [ ] Write Laravel assertions for missing registry entry and renderer output.
- [ ] Run both tests and verify failures are specifically caused by missing `hero_banner` implementation.

### Task 2: Register and normalize the widget

**Files:**
- Create: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/definition.js`
- Modify: `config/pagebuilder_elementor_v23_widgets.php`
- Modify: `public/js/pagebuilder_elementor_v23/app.js`

**Interfaces:**
- Produces: `defaults()` and `normalize(node)` registered as type `hero_banner`.
- Settings keys follow the production design document exactly.

- [ ] Add minimal defaults and normalization for content order, one-to-three buttons, responsive positions, media, style, and inherited Advanced defaults.
- [ ] Register dedicated canvas/settings/view paths in v2.3 config.
- [ ] Add label/icon and AdvancedControls eligibility in `app.js`.
- [ ] Run the Node contract until the registry section passes.

### Task 3: Implement Content, Style, and Advanced settings

**Files:**
- Create: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Settings.vue`

**Interfaces:**
- Props: `{ node: Object, editor: Object }`.
- Uses: `editor.linkControl`, `editor.widgetAdvancedControls`, `editor.chooseMedia`, `editor.activeResponsiveKey`, and `editor.setResponsiveDevice`.

- [ ] Implement Grouped/Independent content behavior, content order, and visibility.
- [ ] Implement the 1–3 button repeater and conditional Link/Video/Image controls.
- [ ] Implement responsive target positioning, Button Group layout, media source, inheritance indicator, and override reset.
- [ ] Implement focused Style controls and shared AdvancedControls.
- [ ] Run the Node contract until Settings assertions pass.

### Task 4: Implement the editor canvas

**Files:**
- Create: `public/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Canvas.vue`

**Interfaces:**
- Props: `{ item: Object, responsiveDevice: String }`.
- Calls: `window.PageBuilderElementorV23Runtime.openMediaLightbox(source, type, alt, settings)`.

- [ ] Resolve responsive fallback for media, position, layout, and size.
- [ ] Render Grouped content order and Independent blocks with Button Group as one target.
- [ ] Render safe link attributes and popup actions.
- [ ] Add scoped responsive and reduced-motion CSS.
- [ ] Run the Node contract until Canvas assertions pass.

### Task 5: Implement safe frontend rendering

**Files:**
- Create: `resources/views/pagebuilder_elementor_v23/widgets/pro/hero-banner.blade.php`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Receives normalized-ish `node.settings`; treats it as untrusted.
- Emits: `[data-hero-banner]` root and `[data-hero-media]` popup triggers.

- [ ] Normalize URLs, enums, tags, numbers, lengths, content order, and button count in Blade.
- [ ] Emit responsive picture/media, scoped CSS variables/media queries, grouped/independent content, safe links, and popup data.
- [ ] Add frontend Hero Banner layout/button/responsive styles.
- [ ] Run the Laravel feature test until registry and renderer assertions pass.

### Task 6: Extend the shared popup runtime

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/frontend-runtime.js`
- Test: `tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs`
- Test: `tests/pagebuilder-v23-frontend-runtime.test.mjs`

**Interfaces:**
- Export: `PageBuilderElementorV23Runtime.openMediaLightbox`.
- Add: `initHeroBanner(root)` binding `[data-hero-media]`.

- [ ] Extend video validation for Dailymotion embeds and self-hosted MP4/WebM/Ogg.
- [ ] Render self-hosted video with native `<video controls>` and embed providers with `<iframe>`.
- [ ] Add focus return, body scroll lock, Escape/backdrop close, and cleanup.
- [ ] Bind every Hero Banner once during runtime `init()`.
- [ ] Run both Node runtime contracts until green.

### Task 7: Full verification and graph refresh

**Files:**
- Modify only if QA exposes a failing contract.

**Interfaces:**
- Verification commands are the completion gate.

- [ ] Run `node tests/pagebuilder-v23-hero-banner-widget-parity.test.mjs`.
- [ ] Run `node tests/pagebuilder-v23-frontend-runtime.test.mjs`.
- [ ] Run `php artisan test tests/Feature/PageBuilderElementorV23HeroBannerWidgetTest.php`.
- [ ] Run the scoped `php artisan test --filter=PageBuilderElementorV23` suite.
- [ ] Run `node --check public/js/pagebuilder_elementor_v23/app.js`, `php -l` on the Blade view, and `git diff --check`.
- [ ] Update Graphify incrementally because production source changed.
- [ ] Perform browser QA with a cache-busted v2.3 editor URL, add Hero Banner without Save, inspect Desktop/Tablet/Mobile and Content/Style/Advanced, exercise video/image popup, and confirm zero console errors.
