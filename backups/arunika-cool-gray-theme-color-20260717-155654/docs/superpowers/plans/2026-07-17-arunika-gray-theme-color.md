# Arunika Gray Theme Color Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add neutral gray `#6B7280` as the eighth Theme Color option in Arunika Mosaic, Aurora, and Canvas.

**Architecture:** Keep the existing palette renderer and persistence flow unchanged. Extend only the three `colorMainList` arrays and lock the shared contract with one focused Node regression.

**Tech Stack:** JavaScript, Node test runner, Laravel Blade cache.

## Global Constraints

- Append `#6B7280` as the final item in all three palettes.
- Do not change existing color order, gradient CSS, dark mode, `changeMainColor(color)`, or `localStorage['theme-color']`.
- Preserve unrelated dirty-working-tree changes and make backups before editing production files.
- Do not stage or commit changes unless explicitly requested.

---

### Task 1: Add the shared gray palette option

**Files:**
- Create: `tests/arunika-gray-theme-color-static.test.mjs`
- Modify: `public/assets/js/themes/arunika_mosaic/arunika_mosaic.js:2`
- Modify: `public/assets/js/themes/arunika_aurora/arunika_aurora.js:2`
- Modify: `public/assets/js/themes/arunika_canvas/arunika_canvas.js:2`

**Interfaces:**
- Consumes: the existing `colorMainList` arrays and palette renderer.
- Produces: eight color swatches per theme with `#6B7280` as the final swatch.

- [ ] **Step 1: Back up the three production JavaScript entrypoints**

Create a timestamped folder under `backups/` and copy each entrypoint while preserving its theme-relative path.

- [ ] **Step 2: Write the failing regression**

Create a Node test that parses `colorMainList` from each file and asserts eight values, one occurrence of `#6B7280`, and placement as the final item.

- [ ] **Step 3: Run the focused test and verify RED**

Run: `node --test tests/arunika-gray-theme-color-static.test.mjs`

Expected: three failures because each list currently contains seven colors and no `#6B7280`.

- [ ] **Step 4: Implement the minimal palette change**

Append `'#6B7280'` to each existing `colorMainList` array without modifying any other JavaScript behavior.

- [ ] **Step 5: Run focused and related regressions**

Run the new focused test and `tests/arunika-aurora-theme-color-gradient-static.test.mjs`.

Expected: all checks pass.

- [ ] **Step 6: Verify Laravel compilation and diff hygiene**

Run `php artisan view:cache`, `git diff --check`, and confirm all three served JavaScript assets return HTTP 200.
