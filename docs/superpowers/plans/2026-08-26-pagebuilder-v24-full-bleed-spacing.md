# Page Builder v2.4 Full Bleed and Configurable Spacing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove the editor-only `.pb-preview` inset when a widget is set to Full Bleed, while keeping widget Advanced Padding configurable with validated responsive CSS units and aligned in Canvas and frontend output.

**Architecture:** Keep the 6px `.pb-preview` value as editor shell gutter, controlled by a shared responsive `fullBleed` flag. Keep user spacing in the existing shared `EdgeControl` and route the same Advanced settings through `WidgetAdvancedStyleResolver`; do not add a generic frontend wrapper that could break Flexbox/Grid direct-child structure. Manual v2.4 renderers receive the existing resolver at their own root, with Hero Slider as the first parity path.

**Tech Stack:** Vue 3 SFC loaded by the v2.4 asset routes, vanilla JavaScript editor host, Laravel Blade module renderers, PHPUnit, Node `node:test`, Graphify.

**Spec:** `project-artifacts/plans/2026-08-22-pagebuilder-v24-full-stack-modular-design.md` plus the approved Full Bleed/spacing decision in this conversation.

## Global Constraints

- Preserve v2.3 source and behavior; modify only the v2.4 tree and v2.4 support code.
- `.pb-preview` remains editor-only; its gutter must never be emitted into frontend HTML.
- Reuse `EdgeControl`, `ResponsiveDeviceControl`, existing `pb-advanced-*` classes, and existing unit/value markup; do not create a new settings component or form styling.
- Advanced Padding accepts `px`, `%`, `em`, `rem`, `vw`, `vh`, and `pt`; negative Padding remains invalid while negative Margin remains valid.
- Full Bleed is responsive through the existing Desktop/Tablet/Mobile inheritance model and defaults to disabled.
- Do not add a generic wrapper in `render_node.blade.php`; preserve layout direct-child selectors and Grid/Flexbox structure.
- Back up every existing file before editing and preserve the untracked `mockups/` directory.
- No Save/Reset or destructive browser action is part of this run.

---

### Task 1: Establish the failing contract tests

**Files:**
- Create: `tests/pagebuilder-v24-full-bleed-spacing.test.mjs`
- Create: `tests/Feature/PageBuilderElementorV24FullBleedSpacingTest.php`

**Interfaces:**
- Consumes: current `widget-registry.js`, shared `AdvancedControls.vue`, Canvas `BuilderNode`, `WidgetAdvancedStyleResolver`, and Hero Slider module view.
- Produces: red tests for shared defaults/UI, Canvas gutter binding, resolver unit/class output, and Hero Slider frontend output.

- [x] **Step 1: Add Node assertions for the shared contract.** Assert `advancedDefaults()` exposes `fullBleed: false` plus empty Tablet/Mobile overrides; assert the source exposes `Full Bleed`, `effectiveResponsiveValue('fullBleed'...)`, `ResponsiveDeviceControl`, and the required unit list including `pt` and `vh`; assert `.pb-preview` uses a CSS variable rather than a fixed declaration.
- [x] **Step 2: Add PHPUnit resolver and Hero Slider assertions.** Render a Hero Slider with `fullBleed: true` and `paddingTop: '2em'`, `paddingRight: '3rem'`, `paddingBottom: '4pt'`, `paddingLeft: '5%'`; assert the root carries `pb-full-bleed`, the resolver CSS contains all four padding tokens and `width:100%`, and the output contains no `.pb-preview` markup.
- [x] **Step 3: Run the focused tests and verify they fail for missing behavior.**

```powershell
node --test tests/pagebuilder-v24-full-bleed-spacing.test.mjs
php artisan test tests/Feature/PageBuilderElementorV24HeroSliderWidgetTest.php tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php
```

Expected: failures identify the absent `fullBleed` contract, fixed editor gutter, and missing Hero Slider resolver output.

---

### Task 2: Add the shared responsive setting and unit parity

**Files:**
- Backup and modify: `public/js/pagebuilder_elementor_v24/widget-registry.js`
- Backup and modify: `resources/pagebuilder_elementor_v24/shared/AdvancedControls.vue`

**Interfaces:**
- Consumes: canonical `advancedDefaults()`, `normalizeAdvanced()`, `EdgeControl`, and `ResponsiveDeviceControl`.
- Produces: normalized `fullBleed` values and a consistent Advanced Layout control shared by all v2.4 Settings wrappers.

- [x] **Step 1: Extend `advancedDefaults()` and `normalizeAdvanced()`.** Add `fullBleed: false`, `fullBleedTablet: ''`, and `fullBleedMobile: ''`; normalize base and responsive values to `false` or an empty inherited value using the existing boolean conventions.
- [x] **Step 2: Extend spacing parsing without changing the control shape.** Add `pt` and `vh` to `SPACING_DIMENSION_UNITS` and to `parseDimension()`'s accepted token pattern; keep `EdgeControl`'s `allowNegative` behavior unchanged so only Margin/offset controls can go below zero.
- [x] **Step 3: Add the Full Bleed field beside Padding using existing markup.** Use the existing `pb-advanced-field pb-advanced-responsive-field`, `pb-advanced-control-head`, `ResponsiveDeviceControl`, and `pb-select`; map the select values `default`/`full` to the boolean `fullBleed` setting. Do not add a new Vue component or CSS selector.
- [x] **Step 4: Re-run the Node contract test and confirm the shared contract is green before touching renderers.**

---

### Task 3: Remove the editor gutter only for Full Bleed widgets

**Files:**
- Backup and modify: `public/js/pagebuilder_elementor_v24/app.js`
- Backup and modify: `public/assets/css/pagebuilder_elementor_v24.css`

**Interfaces:**
- Consumes: normalized `fullBleed` setting and existing `BuilderNode.nodeResponsiveValue()`.
- Produces: responsive `--pb-preview-padding` on the editor shell while preserving Advanced Padding on the outer `.pb-node`.

- [x] **Step 1: Add `widgetPreviewShellStyle` to `BuilderNode`.** Return `{ '--pb-preview-padding': '0px' }` when the active responsive `fullBleed` value is truthy and `{ '--pb-preview-padding': '6px' }` otherwise; return an empty object for layout nodes.
- [x] **Step 2: Bind the style to `.pb-preview`.** Keep the existing `.pb-preview-inner` structure and add only `:style="widgetPreviewShellStyle"` to the existing element.
- [x] **Step 3: Change the editor CSS declaration.** Replace `padding: 6px` with `padding: var(--pb-preview-padding, 6px)` and leave pointer, sizing, and box-sizing rules untouched.
- [x] **Step 4: Run Node static/runtime checks and inspect the diff for accidental changes to `.pb-node` Advanced Padding.**

---

### Task 4: Make the backend resolver emit the same Full Bleed and spacing semantics

**Files:**
- Backup and modify: `app/Support/PageBuilderElementorV24/WidgetAdvancedStyleResolver.php`
- Backup and modify: `public/assets/css/frontend_elementor_v24.css`

**Interfaces:**
- Consumes: canonical `fullBleed`, responsive inheritance, and existing `length()` validation.
- Produces: resolver classes `pb-full-bleed`/device variants and responsive `width:100%;max-width:100%` rules without weakening CSS sanitization.

- [x] **Step 1: Add responsive Full Bleed classes.** Derive desktop/tablet/mobile values through `responsive()`; add `pb-full-bleed` when any active value is enabled and device-scoped classes for CSS hooks.
- [x] **Step 2: Add the Full Bleed width rule after the normal width-mode rule.** When the active device is enabled, append `width:100%` and `max-width:100%`; leave explicit Advanced Margin and Padding rules intact.
- [x] **Step 3: Add small device-scoped frontend rules to `frontend_elementor_v24.css`.** Keep the rule limited to width/max-width so internal widget padding and content presentation are not reset.
- [x] **Step 4: Run the new resolver PHPUnit assertions and the existing v2.4 frontend rendering test.**

---

### Task 5: Wire manual v2.4 widget renderers to the shared resolver

**Files:**
- Backup and modify the manual widget views: `resources/pagebuilder_elementor_v24/modules/widgets/basic/button/frontend.blade.php`, `divider/frontend.blade.php`, `icon/frontend.blade.php`, `spacer/frontend.blade.php`, `text-editor/frontend.blade.php`, `video/frontend.blade.php`, `resources/pagebuilder_elementor_v24/modules/widgets/general/accordion/frontend.blade.php`, `resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-banner/frontend.blade.php`, and `hero-slider/frontend.blade.php`.

**Interfaces:**
- Consumes: each module's existing root id/class variables and `WidgetAdvancedStyleResolver::resolve()`.
- Produces: one resolver-backed root per manual widget, with existing widget-specific styles and markup preserved.

- [x] **Step 1: Integrate Hero Slider first.** Resolve the existing settings at the top, use the resolver id for the root and selector scope, merge resolver classes with `pb-hero-slider`, emit resolver CSS in the existing `<style>`, and retain all slide/media behavior.
- [x] **Step 2: Integrate Hero Banner with the same root pattern.** Preserve image/content classes and existing responsive CSS; append resolver CSS after the module root defaults so `width:100%` remains authoritative when Full Bleed is enabled.
- [x] **Step 3: Integrate the six Basic and one General manual views.** Add resolver id/classes/attributes and CSS at each existing root rather than introducing an extra DOM wrapper; keep Button's link markup, Video's ratio wrapper, and Accordion's nested panel structure unchanged.
- [x] **Step 4: Run the manual-renderer matrix test and focused PHP tests.** Any renderer that cannot safely accept the resolver without changing its root structure is recorded as an explicit remaining gap instead of being wrapped generically.

---

### Task 6: Verify, refresh the graph incrementally, and save the handoff

**Files:**
- Verify: `tests/pagebuilder-v24-full-bleed-spacing.test.mjs`, `tests/Feature/PageBuilderElementorV24FullBleedSpacingTest.php`
- Create handoff: `E:/AI/Memories/20260826-1700-laravel13-phoenix-pagebuilder-v24-full-bleed-spacing.md`

**Interfaces:**
- Consumes: all implementation slices and their fresh test output.
- Produces: an evidence-backed completion/continuation state for the next laptop session.

- [x] **Step 1: Run the focused Node suite, focused PHPUnit suite, JavaScript/PHP syntax checks, view/cache checks, and `git diff --check`.**

```powershell
node --test tests/pagebuilder-v24-full-bleed-spacing.test.mjs tests/pagebuilder-v24-shared-advanced-contract.test.mjs tests/pagebuilder-v24-shared-advanced-runtime.test.mjs tests/pagebuilder-v24-hero-slider-widget-parity.test.mjs tests/pagebuilder-v24-hero-slider-runtime.test.mjs tests/pagebuilder-v24-widget-runtime-parity.test.mjs
php artisan test tests/Feature/PageBuilderElementorV24HeroSliderWidgetTest.php tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php
node --check public/js/pagebuilder_elementor_v24/app.js
php -l app/Support/PageBuilderElementorV24/WidgetAdvancedStyleResolver.php
php artisan view:cache
git diff --check
```

- [x] **Step 2: If source changed substantially, run `graphify . --update --no-viz`; never stage `graphify-out`.** Used `--code-only` because the incremental scan reported 104 non-code files requiring an unavailable LLM key; Graphify updated the code graph to 20,513 nodes and 35,654 edges.
- [x] **Step 3: Write and verify the Markdown handoff under `E:\AI\Memories` with date, project, decisions, files/backups, implementation, tests, unresolved gaps, and next steps; also add the required small ad-hoc memory note under `C:\Users\CAHYO\.codex\memories\extensions\ad_hoc\notes\`.
- [x] **Step 4: Report the exact verified state before the 17:00 WIB cutoff; do not claim browser parity unless a fresh browser check was actually run.**
