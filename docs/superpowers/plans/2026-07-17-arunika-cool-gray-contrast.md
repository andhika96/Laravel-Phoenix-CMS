# Arunika Cool Gray Contrast Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Preserve `#C7CCD8` as the cool-gray background tint while using contrast-safe interactive and hover colors in all Arunika themes.

**Architecture:** Split the selected palette value into a surface token and an interactive token only when cool gray is selected. Persist the original palette value, expose a document state attribute, and let CSS resolve light/dark hover surfaces without changing other palette colors.

**Tech Stack:** JavaScript, Blade inline bootstrap, CSS custom properties, Node test runner, Laravel.

## Global Constraints

- Cool-gray surface stays `#C7CCD8`.
- Cool-gray interactive color is `#667085`.
- Cool-gray light hover is `#E4E7EC`; dark hover is `rgba(199, 204, 216, 0.16)`.
- Other palette colors, persistence, gradients, dynamic CMS content, and theme behavior remain unchanged.
- Back up every existing file before modification; do not stage or commit unless requested.

---

### Task 1: Separate cool-gray surface and interaction roles

**Files:**
- Create: `tests/arunika-cool-gray-contrast-static.test.mjs`
- Modify: `public/assets/js/themes/arunika_mosaic/arunika_mosaic.js`
- Modify: `public/assets/js/themes/arunika_aurora/arunika_aurora.js`
- Modify: `public/assets/js/themes/arunika_canvas/arunika_canvas.js`
- Modify: `resources/views/themes/arunika_mosaic/cms/cms_layout.blade.php`
- Modify: `resources/views/themes/arunika_aurora/cms/cms_layout.blade.php`
- Modify: `resources/views/themes/arunika_canvas/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_mosaic/arunika_mosaic.css`
- Modify: `public/assets/css/themes/arunika_aurora/arunika_aurora.css`
- Modify: `public/assets/css/themes/arunika_canvas/arunika_canvas.css`

**Interfaces:**
- Consumes: `colorMainList`, `changeMainColor(color)`, `localStorage['theme-color']`, and `--ph-theme-primary`.
- Produces: `--ph-theme-surface-tint`, `--ph-theme-hover-surface`, and `data-ph-theme-color="cool-gray"`.

- [ ] **Step 1:** Back up the nine existing JavaScript, Blade, and CSS files with hash verification.
- [ ] **Step 2:** Add a focused regression for the approved cool-gray token mapping and verify it fails before implementation.
- [ ] **Step 3:** Add shared color-application logic to each JavaScript entrypoint while preserving the stored palette value.
- [ ] **Step 4:** Apply the same mapping in each Blade early-color bootstrap.
- [ ] **Step 5:** Route background gradients through `--ph-theme-surface-tint` and cool-gray hover states through `--ph-theme-hover-surface`.
- [ ] **Step 6:** Run focused regressions, syntax checks, Laravel tests, Blade compilation, runtime asset checks, and diff hygiene.
