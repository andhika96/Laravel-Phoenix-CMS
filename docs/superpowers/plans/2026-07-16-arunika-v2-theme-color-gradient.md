# Arunika V2 Theme Color Gradient Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restore the Arunika V2 header palette and make the selected theme color tint its sidebar and header gradients in light and dark mode.

**Architecture:** Reuse the existing browser-side color picker and `--ph-theme-primary` persistence path. Add only the missing Blade dropdown, then derive the existing shell gradients from the primary token in CSS so changes apply immediately without a reload or extra state.

**Tech Stack:** Laravel Blade, Bootstrap 5 dropdowns, Font Awesome, CSS custom properties and `color-mix()`, Node.js built-in test runner.

## Global Constraints

- Preserve the existing Arunika V2 layout and coding style.
- Keep color choices only; do not restore background pattern controls.
- Keep the current gradient geometry and neutral light/dark surface bases.
- Do not add a database table, backend endpoint, or new JavaScript state system.
- Preserve unrelated working-tree changes.

---

### Task 1: Add the regression contract

**Files:**
- Create: `tests/arunika-v2-theme-color-gradient-static.test.mjs`
- Test: `tests/arunika-v2-theme-color-gradient-static.test.mjs`

**Interfaces:**
- Consumes: the Arunika V2 Blade layout, stylesheet, and existing theme JavaScript.
- Produces: a static regression contract for palette placement, persistence, pattern exclusion, and dynamic shell gradients.

- [x] **Step 1: Write the failing test**

Create assertions that require a palette dropdown before `.ph-theme-toggle`, require `id="color-picker-container"`, reject pattern-control markup in V2, require the existing `changeMainColor()` localStorage update, and require both light and dark shell surfaces to use `color-mix()` with `var(--ph-theme-primary)`.

- [x] **Step 2: Run test to verify it fails**

Run: `node --test tests/arunika-v2-theme-color-gradient-static.test.mjs`

Expected: FAIL because the palette markup and token-derived shell gradients are absent.

### Task 2: Restore the palette and dynamic gradient

**Files:**
- Modify: `resources/views/themes/arunika_v2/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_v2/arunika_v2.css`
- Test: `tests/arunika-v2-theme-color-gradient-static.test.mjs`

**Interfaces:**
- Consumes: `color-picker-container`, `changeMainColor(color)`, `--ph-theme-primary`, and Bootstrap dropdown behavior.
- Produces: an interactive palette immediately before the mode toggle and theme-tinted light/dark shell gradients.

- [x] **Step 1: Add minimal Blade markup**

Add a right-aligned Bootstrap dropdown whose button uses `ph-btn-action-icon`, whose icon is `fas fa-palette`, and whose body contains only the existing `color-picker-container` swatch grid.

- [x] **Step 2: Convert fixed gradient tints to the primary token**

Keep every existing gradient position and neutral base color, replacing only fixed colored RGBA stops with `color-mix(in srgb, var(--ph-theme-primary), transparent N%)`. Derive the light hover surface from the same primary token.

- [x] **Step 3: Run the focused test**

Run: `node --test tests/arunika-v2-theme-color-gradient-static.test.mjs`

Expected: PASS with all focused assertions green.

- [x] **Step 4: Run related Arunika V2 regression tests**

Run: `node --test tests/arunika-v2-*-static.test.mjs`

Expected: PASS with zero failures.

- [x] **Step 5: Inspect the final diff**

Run: `git diff --check` and `git diff -- resources/views/themes/arunika_v2/cms/cms_layout.blade.php public/assets/css/themes/arunika_v2/arunika_v2.css tests/arunika-v2-theme-color-gradient-static.test.mjs`

Expected: no whitespace errors and only the approved focused changes.
