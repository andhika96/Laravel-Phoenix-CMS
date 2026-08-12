# Widget Settings Loading State Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Show an accessible spinner/message while a v2.3 widget settings module loads, with a visible error fallback when loading fails.

**Architecture:** Configure the existing shared Vue async-settings loader in `app.js` with reusable loading and error components. Add only the matching v2.3 sidebar CSS and a focused static regression test; widget modules and persistence remain unchanged.

**Tech Stack:** Vue 3 `defineAsyncComponent`, existing v2.3 JavaScript app, CSS, Node test runner.

## Global Constraints

- Preserve the dirty worktree and all unrelated widget/header-navigation changes.
- Modify only the shared v2.3 settings loader, its CSS, and focused tests for this feature.
- Use the existing English editor copy: `Loading widget settings...`.
- Do not add dependencies, change persistence, press Save, commit, or push.

---

### Task 1: Add the failing regression test

**Files:**
- Create: `tests/pagebuilder-v23-widget-settings-loading-state.test.mjs`

**Interfaces:**
- Reads `public/js/pagebuilder_elementor_v23/app.js` and `public/assets/css/pagebuilder_elementor_v23.css`.
- Asserts the shared async loader exposes loading/error components and the CSS exposes the accessible loading presentation.

- [ ] **Step 1: Write the failing test**

```js
test('shared settings loader exposes loading and error states', () => {
  assert.match(app, /loadingComponent:\s*WidgetSettingsLoading/);
  assert.match(app, /errorComponent:\s*WidgetSettingsError/);
  assert.match(app, /Loading widget settings\.\.\./);
  assert.match(app, /role="status"/);
  assert.match(app, /role="alert"/);
  assert.match(css, /\.pb-widget-settings-loading/);
  assert.match(css, /@keyframes pb-widget-settings-spin/);
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run:

```powershell
node --test tests/pagebuilder-v23-widget-settings-loading-state.test.mjs
```

Expected: FAIL because the current `defineAsyncComponent` call has no `loadingComponent`/`errorComponent`, and the CSS has no loading-state selectors.

### Task 2: Implement the shared loader state

**Files:**
- Backup and modify: `public/js/pagebuilder_elementor_v23/app.js`
- Backup and modify: `public/assets/css/pagebuilder_elementor_v23.css`

**Interfaces:**
- `loadWidgetSettings(type)` continues returning the same async component contract.
- The new loading and error component objects are local to `app.js` and require no widget changes.

- [ ] **Step 1: Back up existing files**

Create timestamped `.bak_YYYYMMDDHHMMSS_widget_settings_loading` copies of both files and verify they exist before editing.

- [ ] **Step 2: Add reusable loading/error components**

Add compact templates with `role="status"`, `aria-live="polite"`, and `role="alert"`, then pass them to `defineAsyncComponent` with `delay: 0` so short loads still have a deterministic loading state.

- [ ] **Step 3: Add scoped v2.3 CSS**

Add the centered loading row, 16px circular spinner, reduced-motion rule, and error copy styles near the existing `.v23-properties-section` rules. Do not alter widget-specific CSS.

- [ ] **Step 4: Run the focused test and syntax check**

Run:

```powershell
node --test tests/pagebuilder-v23-widget-settings-loading-state.test.mjs
node --check public/js/pagebuilder_elementor_v23/app.js
```

Expected: PASS with the new test and a zero exit status from `node --check`.

### Task 3: Regression verification

**Files:**
- Read-only: existing v2.3 JavaScript/PHP tests and Graphify output.

- [ ] **Step 1: Run affected JavaScript suites**

Run the focused loading test, `tests/pagebuilder-v23-properties-toolbar-regression.test.mjs`, and `tests/pagebuilder-v23-frontend-runtime.test.mjs` with the dot reporter.

- [ ] **Step 2: Run the v2.3 build and diff checks**

Run `npm.cmd run build` and `git diff --check`.

- [ ] **Step 3: Update Graphify incrementally**

Run `graphify update . --no-cluster` and confirm the new loader/CSS/test nodes are represented. Keep `graphify-out` unstaged.

- [ ] **Step 4: Report boundaries**

Report exact files, backup paths, tests, build result, Graphify result, and whether local editor visual QA was available. Do not claim Save or browser persistence verification.
