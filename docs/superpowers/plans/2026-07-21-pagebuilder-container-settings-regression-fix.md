# PageBuilder Container Settings Regression Fix Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restore Container and Container Fluid settings after the modular refactor while keeping Container Fluid and Row Grid registered internally but hidden from the Layout toolbox.

**Architecture:** Keep the registry-driven module architecture. Route background-state writes through one editor service instead of assigning to a dynamically computed key inside a Vue event expression, because `vue3-sfc-loader@0.8.4` rejects that generated assignment. Keep runtime registration and toolbox visibility separate through the existing `toolbox` flag.

**Tech Stack:** Laravel 13, PHPUnit, Vue 3 CDN, `vue3-sfc-loader@0.8.4`, Chrome headless runtime audit.

## Global Constraints

- Back up every existing file before modifying it.
- Do not restore inline settings templates in `app.js`.
- Preserve Container, Container Fluid, Grid, and Row Grid saved-data compatibility.
- Keep Container and Grid visible; keep Container Fluid and Row Grid hidden.
- Verify every registered `Settings.vue` with the same browser compiler used by the editor.

---

### Task 1: Add regression tests for visibility and safe Container bindings

**Files:**
- Modify: `tests/Feature/PageBuilderElementorContainerWidgetModulesTest.php`
- Modify: `tests/Feature/PageBuilderElementorGridWidgetModulesTest.php`

**Interfaces:**
- Consumes: widget catalog entries and definition source files.
- Produces: expected toolbox visibility per type and a source contract for `editor.setBgStateValue(node, base, value)`.

- [ ] **Step 1: Back up both test files**

Create timestamped `.bak_YYYYMMDD_HHMMSS_container_settings_regression` copies beside the originals.

- [ ] **Step 2: Write the failing assertions**

Extend each data provider with `bool $toolboxVisible`. Assert the config flag and definition marker match that value. For both Container settings modules, reject direct `@click="node.settings[editor.bgStateKey(` assignments and require helper calls for background and overlay gradient type.

- [ ] **Step 3: Run tests and verify RED**

Run:

```powershell
php artisan test tests\Feature\PageBuilderElementorContainerWidgetModulesTest.php tests\Feature\PageBuilderElementorGridWidgetModulesTest.php --stop-on-failure
```

Expected: FAIL because Container Fluid and Row Grid are visible and the Container settings still contain direct computed-key assignments.

### Task 2: Restore Container Settings compilation

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/js/pagebuilder_elementor/widgets/layout/container/Settings.vue`
- Modify: `public/js/pagebuilder_elementor/widgets/layout/container-fluid/Settings.vue`

**Interfaces:**
- Produces: `setBgStateValue(node: object, base: string, value: mixed): void` through `widgetEditorServices`.

- [ ] **Step 1: Back up all three production files**

Create timestamped `.bak_YYYYMMDD_HHMMSS_container_settings_regression` copies.

- [ ] **Step 2: Add the minimal shared setter**

Add:

```javascript
function setBgStateValue(node, base, value) {
    if (!node || !node.settings) return;
    node.settings[bgStateKey(node, base)] = value;
}
```

Expose it through `widgetEditorServices`.

- [ ] **Step 3: Replace the four invalid event assignments in each Settings module**

Use calls such as:

```vue
@click="editor.setBgStateValue(node, 'bgGradientType', 'linear')"
```

Apply the same pattern to radial and overlay gradient type controls.

- [ ] **Step 4: Run the Container/Grid tests**

Expected: Container binding assertions pass; visibility assertions remain red until Task 3.

### Task 3: Restore internal layout type visibility

**Files:**
- Modify: `config/pagebuilder_elementor_widgets.php`
- Modify: `public/js/pagebuilder_elementor/widgets/layout/container-fluid/definition.js`
- Modify: `public/js/pagebuilder_elementor/widgets/layout/row-grid/definition.js`

**Interfaces:**
- Produces: `toolbox: false` for internal compatibility types and `toolbox: true` for public Container/Grid types.

- [ ] **Step 1: Back up all three files**

Create timestamped `.bak_YYYYMMDD_HHMMSS_container_settings_regression` copies.

- [ ] **Step 2: Set visibility flags**

Set Container Fluid and Row Grid to `toolbox: false` in config and definitions. Leave Container and Grid unchanged.

- [ ] **Step 3: Run tests and verify GREEN**

Run the two module test files. Expected: PASS with no failures.

### Task 4: Runtime and regression verification

**Files:**
- Verify: all `public/js/pagebuilder_elementor/widgets/**/Settings.vue`
- Verify: `public/js/pagebuilder_elementor/app.js`

**Interfaces:**
- Consumes: production editor paths and the CDN SFC loader.
- Produces: evidence that every Settings module compiles and the toolbox contains only Container and Grid under Layout.

- [ ] **Step 1: Run syntax and focused PHPUnit checks**

Run `node --check`, PHP lint, the Container/Grid module tests, responsive parity, and related PageBuilder widget module tests.

- [ ] **Step 2: Run Chrome SFC compilation audit**

Compile all 15 Settings modules with Vue 3.4.38 and `vue3-sfc-loader@0.8.4`. Expected: 15 OK, zero errors.

- [ ] **Step 3: Verify runtime toolbox output**

Load the editor registry in Chrome and confirm Layout resolves to exactly `container` and `grid`.

- [ ] **Step 4: Review and commit**

Run `git diff --check`, confirm no backup files are staged, then commit the implementation.
