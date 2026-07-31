# Page Builder Icon List Elementor Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a fully functional Elementor-parity Icon List widget to the General category.

**Architecture:** Follow the existing modular General-widget contract: registry definition, normalized app runtime, isolated Settings and Canvas Vue modules, dedicated Blade renderer, and scoped frontend CSS. Reuse the shared icon library, link, typography, and advanced controls.

**Tech Stack:** Laravel Blade, Vue 3 runtime SFC loader, Font Awesome 5, PHPUnit.

## Global Constraints

- Preserve unrelated dirty-worktree changes.
- Back up every existing file before modification.
- Keep editor state, canvas preview, saved settings, and frontend output aligned.
- Do not add new dependencies or refactor unrelated widgets.

---

### Task 1: Define the failing Icon List parity contract

**Files:**
- Create: `tests/Feature/PageBuilderElementorIconListWidgetParityTest.php`
- Modify: `tests/Feature/PageBuilderElementorComplexWidgetModulesTest.php`

**Interfaces:**
- Consumes: existing widget catalog and modular widget contract.
- Produces: assertions for registry, content/style/advanced controls, canvas behavior, icon picker integration, and frontend output.

- [ ] Write focused tests for all required controls and rendering behavior.
- [ ] Run the focused tests and confirm they fail because `icon_list` is absent.

### Task 2: Add state, registry, and editor interactions

**Files:**
- Modify: `config/pagebuilder_elementor_widgets.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Create: `public/js/pagebuilder_elementor/widgets/general/icon-list/definition.js`

**Interfaces:**
- Produces: `iconListWidgetDefaults()`, `normalizeIconListSettings()`, repeater actions, and `openIconListItemIconLibrary(itemId, node)`.

- [ ] Register the widget and default contract.
- [ ] Normalize all enums, booleans, URLs, attributes, responsive values, and stable item IDs.
- [ ] Wire add, duplicate, remove, and item-specific icon selection.
- [ ] Run focused tests until the state/registry assertions pass.

### Task 3: Build the panel and canvas

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/general/icon-list/Settings.vue`
- Create: `public/js/pagebuilder_elementor/widgets/general/icon-list/Canvas.vue`

**Interfaces:**
- Consumes: editor service methods and normalized settings.
- Produces: complete Content/Style/Advanced UI and responsive canvas rendering.

- [ ] Implement the three tabs and Elementor conditional controls.
- [ ] Implement semantic list canvas markup and all responsive/hover/divider styles.
- [ ] Run focused tests until panel/canvas assertions pass.

### Task 4: Add frontend parity

**Files:**
- Create: `resources/views/pagebuilder_elementor/partials/render_icon_list.blade.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Consumes: normalized Icon List settings and shared advanced resolver.
- Produces: safe semantic HTML, responsive scoped CSS, hover states, and cache-compatible rendering.

- [ ] Implement safe renderer and scoped responsive rules.
- [ ] Add minimal base CSS and cache dispatch.
- [ ] Run focused tests until all frontend assertions pass.

### Task 5: Verify and refresh the graph

**Files:**
- Update generated local graph only: `graphify-out/graph.json`

- [ ] Run PHP/JS syntax checks and focused PHPUnit.
- [ ] Run the broader PageBuilderElementor test suite and `git diff --check`.
- [ ] Run available browser/runtime checks and record limitations.
- [ ] Update Graphify incrementally and query the new Icon List path.
