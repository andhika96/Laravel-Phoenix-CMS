# General Tabs Widget Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the new `General` toolbox category and a nested `Tabs` widget that supports tab items, per-tab dropzones, and the approved Content/Additional Settings parity slice.

**Architecture:** Keep `Tabs` as a widget node in the builder, but give it its own nested `tabItems[].children` structure so editor state, canvas preview, and frontend renderer all share the same content tree. Reuse the existing widget pipeline in `app.js` and `render_node.blade.php`, then add focused CSS for editor and frontend shells.

**Tech Stack:** Vue 3 app shell in `public/js/pagebuilder_elementor/app.js`, async SFC widget preview component, Blade recursive renderer, Laravel feature tests, CSS in `public/assets/css/pagebuilder_elementor.css` and `public/assets/css/frontend_elementor.css`.

---

### Task 1: Lock the expected Tabs behavior with a failing feature test

**Files:**
- Create: `tests/Feature/PageBuilderElementorTabsWidgetParityTest.php`

- [ ] **Step 1: Write the failing test**
- [ ] **Step 2: Run `php artisan test --filter=PageBuilderElementorTabsWidgetParityTest` and confirm it fails for missing `tabs` support**
- [ ] **Step 3: Keep the test focused on the approved scope: toolbox category, Tabs content controls, additional settings, preview component presence, and frontend markup fragments**

### Task 2: Add the Tabs node model and builder traversal support

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Add `tabs` to the widget map, node labels, icons, toolbox, and `makeNode()` defaults**
- [ ] **Step 2: Normalize `tabItems` and `activeTabId`, and extend `norm()`, `findById()`, walk helpers, duplicate helpers, and delete helpers so nested tab children are fully traversed**
- [ ] **Step 3: Add builder helpers for add/duplicate/delete/select tab items and keep at least one tab item alive**

### Task 3: Render nested Tabs content in the editor canvas

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/general/Tabs.vue`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/assets/css/pagebuilder_elementor.css`

- [ ] **Step 1: Add a preview SFC that renders tab headers and the active tab pane**
- [ ] **Step 2: Add a dedicated `BuilderNode` branch for `tabs` with one active tab dropzone using the existing draggable nesting pattern**
- [ ] **Step 3: Add editor CSS for the tab header row/column, active state, empty pane hint, and basic breakpoint behavior**

### Task 4: Add the approved left-panel controls for Tabs

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Add the `General` toolbox section and show `Tabs` there**
- [ ] **Step 2: Add the `selectedType==='tabs'` settings panel with `Tabs Items`, add/duplicate/delete actions, `Direction`, `Justify`, `Align Title`, `Horizontal Scroll`, and `Breakpoint`**
- [ ] **Step 3: Keep the content model tied to the selected node so canvas switching updates immediately**

### Task 5: Render Tabs on the frontend

**Files:**
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `public/assets/css/frontend_elementor.css`

- [ ] **Step 1: Add frontend markup for tab headers, active pane, and recursive child rendering per tab item**
- [ ] **Step 2: Add a small inline script to switch active tabs on click**
- [ ] **Step 3: Add frontend CSS for row/column direction, active state, scrollable headers, and stacked breakpoint behavior**

### Task 6: Verify and tighten

**Files:**
- Modify: `tests/Feature/PageBuilderElementorTabsWidgetParityTest.php` if needed

- [ ] **Step 1: Run `php artisan test --filter=PageBuilderElementorTabsWidgetParityTest`**
- [ ] **Step 2: Run `node --check public/js/pagebuilder_elementor/app.js`**
- [ ] **Step 3: Run `php -l resources/views/pagebuilder_elementor/partials/render_node.blade.php`**
- [ ] **Step 4: Run `git diff --check`**
