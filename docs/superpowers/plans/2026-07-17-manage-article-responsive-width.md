# Manage Article Responsive Width Implementation Plan

> **For agentic workers:** Execute this plan inline in the current Laragon checkout. Do not dispatch subagents for this narrow task.

**Goal:** Make Manage Article responsive column hiding stable across browser scrollbar and rounding differences.

**Architecture:** Keep the existing Vue responsive-table flow. Correct its three measurement boundaries: usable wrapper width, fractional column widths, and subpixel comparison tolerance. Exercise the actual production methods through a Node VM sandbox.

**Tech Stack:** Vue 3 CDN application, browser DOM APIs, Node.js `node:test`, Laravel Blade.

## Global Constraints

- Back up the production JavaScript before modifying it.
- Modify only Manage Article production logic.
- Preserve markup, styles, child rows, priorities, and box model.
- Do not add dependencies, create a branch, or commit unless requested.

---

### Task 1: Add the regression test

**Files:**
- Create: `tests/manage-article-responsive-width.test.mjs`
- Read: `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`

**Interfaces:**
- Consumes: Vue option methods `getTableWidth`, `measureColWidths`, and `recalcResponsive`.
- Produces: a Node test suite that loads the real methods through `vm.runInNewContext`.

- [ ] Create a sandbox where `createApp(...).mount(...)` returns the Vue options object without mounting a browser application.
- [ ] Assert `getTableWidth()` returns `clientWidth` instead of `offsetWidth` for a wrapper with `clientWidth=1753` and `offsetWidth=1768`.
- [ ] Assert `measureColWidths()` retains fractional `getBoundingClientRect().width` values.
- [ ] Assert a `1px` accumulated rounding difference does not hide `Options`.
- [ ] Assert a material shortage still hides `Options`.
- [ ] Run `node --test tests/manage-article-responsive-width.test.mjs` and confirm the first three assertions fail for the expected pre-fix behavior.

### Task 2: Apply the minimal production fix

**Files:**
- Modify: `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:2194-2291`
- Test: `tests/manage-article-responsive-width.test.mjs`

**Interfaces:**
- Consumes: `HTMLElement.clientWidth` and `Element.getBoundingClientRect().width`.
- Produces: the existing `responsiveHiddenCols` array with cross-browser-stable decisions.

- [ ] Change `getTableWidth()` to return `wrapper.clientWidth` when positive.
- [ ] Change `measureColWidths()` to prefer fractional bounding-rectangle widths, retaining existing fallbacks.
- [ ] Add a local `1px` tolerance to the negative-space comparison in `recalcResponsive()`.
- [ ] Run `node --test tests/manage-article-responsive-width.test.mjs` and confirm all tests pass.
- [ ] Run `node --check public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`.

### Task 3: Verify the rendered behavior

**Files:**
- Verify: `resources/views/manage_article/manage_article.blade.php`
- Verify: `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`

**Interfaces:**
- Consumes: the live route `https://laravel-13-phoenix.aruna/manage_article`.
- Produces: desktop DOM measurements, screenshot evidence, and console-health evidence.

- [ ] Reload Manage Article in the authenticated Chrome tab.
- [ ] Verify all seven columns are visible with the sidebar expanded.
- [ ] Collapse the sidebar and verify all seven columns remain visible.
- [ ] Verify no relevant browser console errors or warnings.
- [ ] Run `git diff --check`, inspect `git diff`, and confirm the backup exists.

