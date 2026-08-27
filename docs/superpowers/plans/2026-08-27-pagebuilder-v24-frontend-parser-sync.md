# Page Builder v2.4 Frontend Parser Synchronization Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Keep the v2.4 frontend parser and optional module assets synchronized with all active widget definitions while removing proven default-render warnings.

**Architecture:** Preserve the catalog-driven `render_node.blade.php` dispatcher and `frontend_renderer.blade.php` asset collector. Patch only module frontend views whose current source produces a real parser warning or whose recent editor contract is missing from the frontend; do not introduce a generic wrapper that could change Flexbox/Grid direct-child structure.

**Tech Stack:** Laravel Blade, PHP 8.5, Vue 3 module assets, Node `node:test`, PHPUnit, Graphify.

**Spec:** `project-artifacts/plans/2026-08-22-pagebuilder-v24-full-stack-modular-implementation-plan.md` and the approved v2.4 Full Bleed/Advanced spacing contract.

## Global Constraints

- Modify v2.4 only; preserve v2.0/v2.3 behavior and unrelated dirty changes.
- Keep `ModuleCatalog` as the authority for definitions, Canvas, Settings, frontend views, runtime, and styles.
- Keep `.pb-preview` editor-only; frontend output must remain direct module markup.
- Reuse existing module helpers and resolver patterns; do not add dependencies or a second parser abstraction.
- Back up every existing file before editing; never use `.bak` files as active source.
- Do not Save, Reset, deploy, push, or modify production data during browser/runtime QA.

---

### Task 1: Add a failing all-module frontend default-render regression

**Files:**
- Create: `tests/Feature/PageBuilderElementorV24FrontendParserDefaultsTest.php`

**Interfaces:**
- Consumes: `ModuleCatalog`, `pagebuilder_elementor_v24.partials.render_node`, and each manifest-owned frontend view.
- Produces: a deterministic contract that every active v2.4 module can render through the real dispatcher with empty settings and no PHP warnings.

- [ ] **Step 1: Write the failing test.**

  Install a temporary error handler, render every catalog module through `pagebuilder_elementor_v24.partials.render_node` with empty settings plus empty `children`/`columns`, collect warning messages, restore the handler in `finally`, and assert the warning list is empty and every render returns a non-empty string.

- [ ] **Step 2: Run the focused test and confirm the expected failure.**

  Run: `php artisan test tests/Feature/PageBuilderElementorV24FrontendParserDefaultsTest.php --compact`

  Expected RED: current Social Icons and Text Path views emit undefined-array-key warnings for default fallback expressions.

### Task 2: Fix the proven default parser warnings

**Files:**
- Backup and modify: `resources/pagebuilder_elementor_v24/modules/widgets/general/social-icons/frontend.blade.php`
- Backup and modify: `resources/pagebuilder_elementor_v24/modules/widgets/general/text-path/frontend.blade.php`

**Interfaces:**
- Consumes: existing view settings and fallback helpers.
- Produces: identical default HTML/CSS behavior without undefined-array-key warnings.

- [ ] **Step 1: Normalize fallback values once.**

  In Social Icons, assign the raw `columns` setting with `?? 'auto'` before validation and reuse that variable. In Text Path, assign `pathType`, `textFontStyle`, `textTextTransform`, and `textTextDecoration` fallback values before validation and reuse them instead of reading the possibly missing array keys a second time.

- [ ] **Step 2: Re-run the focused regression.**

  Run: `php artisan test tests/Feature/PageBuilderElementorV24FrontendParserDefaultsTest.php --compact`

  Expected GREEN: all 50 modules render through the dispatcher with zero warnings.

### Task 3: Lock recent widget/parser parity and asset dispatch

**Files:**
- Modify only if the focused audit proves a gap: relevant module `frontend.blade.php`, `styles.css`, `runtime.js`, or `tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php`.

**Interfaces:**
- Consumes: current `module.json`, `definition.js`, `Settings.vue`, `Canvas.vue`, frontend Blade, optional assets, and `ModuleUsageCollector`.
- Produces: parser output for all 50 active modules that reflects recent Product Lead Form, Full Bleed, Advanced spacing, and interactive widget updates.

- [ ] **Step 1: Run catalog and asset checks.**

  Confirm 50 modules, 50 required frontend views, no catalog diagnostics, 28 optional styles, and 23 optional runtimes. Confirm all 46 widget views use `WidgetAdvancedStyleResolver`; the four layout views remain helper-driven by `render_node`.

- [ ] **Step 2: Inspect recent contract seams.**

  Check `product_lead_form` description placement, `hero_banner`/`hero_slider` Full Bleed and responsive spacing, Product Color Selector, Progress Tracker, Video Playlist, carousel modules, and frontend optional asset inclusion. Patch only a confirmed missing or stale consumer.

- [ ] **Step 3: Add a focused assertion for any confirmed gap before implementation.**

  Run the new assertion RED, then make the smallest parser/module change and re-run it GREEN.

### Task 4: Verify integrated v2.4 frontend parser

**Files:**
- Verify: all changed files and `graphify-out/graph.json`.

**Interfaces:**
- Consumes: the parser regression, existing Node/PHP suites, view compiler, and served v2.4 assets.
- Produces: fresh evidence for parser rendering, module asset selection, syntax, and v2.3 isolation.

- [ ] **Step 1: Run focused tests and lint checks.**

  Run the new PHP test, affected v2.4 Node parity tests, `php artisan view:cache`, `node --check` for changed JavaScript, `php -l` for changed PHP, and `git diff --check`.

- [ ] **Step 2: Run broad v2.4 regression.**

  Run: `node --test tests/pagebuilder-v24-*.test.mjs` and the complete `tests/Feature/PageBuilderElementorV24*.php` set. Record warnings separately from failures.

- [ ] **Step 3: Refresh Graphify incrementally if production source changed.**

  Run: `graphify . --update --no-viz --code-only`; keep `graphify-out` un-staged.

- [ ] **Step 4: Report verified, unverified, and deliberately skipped items.**

  Browser visual QA remains unverified if the authenticated editor route still redirects to login; do not claim frontend visual parity from static tests alone.
