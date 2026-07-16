# Arunika V3 Header Fidelity Pass Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring the Arunika V3 desktop shell closer to the approved dashboard reference by tightening the sidebar/header/content rhythm, moving the collapse control beside search, and simplifying the right-side header hierarchy.

**Architecture:** Keep all behavior inside the isolated Arunika V3 Blade and body-scoped CSS. Reuse the existing sidebar toggle, theme-color JavaScript container, dark-mode function, notification component, profile data, and Bootstrap dropdown rather than introducing new dependencies or routes.

**Tech Stack:** Laravel Blade, scoped CSS, existing Font Awesome icons, Bootstrap dropdown behavior, Node static regression tests, Playwright CLI visual QA.

## Global Constraints

- Preserve dynamic CMS menus, page content, notifications, profile data, and theme behavior.
- Do not modify Arunika V1 or Arunika V2.
- Keep mobile navigation on the existing `.ph-mobile-sidebar-trigger` path.
- Use `160px` expanded sidebar, `76px` collapsed sidebar, `52px` header, `220px` desktop search, and `8px 8px 18px` content-shell padding.
- Back up every existing file before modification.

---

### Task 1: Lock the new shell contract with failing static regressions

**Files:**
- Modify: `tests/arunika-v3-theme-static.test.mjs`
- Modify: `tests/arunika-v3-content-shell-static.test.mjs`

**Interfaces:**
- Consumes: Arunika V3 Blade and CSS source text.
- Produces: Regression assertions for DOM order, relocated theme controls, desktop dimensions, and compact content spacing.

- [ ] **Step 1: Update assertions for the approved geometry**

Assert the scoped `160px` sidebar, `52px` header, `220px` search, header-left collapse group/divider, visible desktop profile metadata, and `8px 8px 18px` content padding.

- [ ] **Step 2: Run the focused tests and verify RED**

Run: `node --test tests/arunika-v3-theme-static.test.mjs tests/arunika-v3-content-shell-static.test.mjs`

Expected: FAIL because the production Blade/CSS still contains the old header structure and dimensions.

### Task 2: Implement the minimal Blade and CSS changes

**Files:**
- Modify: `resources/views/themes/arunika_v3/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_v3/arunika_v3.css`

**Interfaces:**
- Consumes: Existing `toggleSidebar()`, `toggleTheme()`, `#color-picker-container`, notification include, and authenticated profile fields.
- Produces: Stable desktop header order `[collapse + divider] [search] ... [notification] [profile]`, with theme preferences inside the profile dropdown.

- [ ] **Step 1: Move the existing sidebar toggle into the desktop header**

Place the same button before search inside `.ph-header-nav-control`; add `.ph-header-divider`; retain its ID, click handler, accessible label, and SVG.

- [ ] **Step 2: Move secondary appearance controls into the profile dropdown**

Remove palette and dark-mode controls from the primary header row. Render `#color-picker-container` and the dark-mode action inside the existing profile menu so both behaviors remain available.

- [ ] **Step 3: Apply the scoped geometry**

Set the V3 expanded width to `160px`, header to `52px`, search to `220px`, profile metadata to remain visible above the mobile breakpoint, and content-shell padding to `8px 8px 18px`. Compact only the V3 sidebar spacing required to prevent overflow.

- [ ] **Step 4: Run the focused tests and verify GREEN**

Run: `node --test tests/arunika-v3-theme-static.test.mjs tests/arunika-v3-content-shell-static.test.mjs`

Expected: PASS.

### Task 3: Verify runtime behavior and design fidelity

**Files:**
- Modify: `design-qa.md`

**Interfaces:**
- Consumes: Authenticated `/manage_article`, reference image, and post-change rendered screenshots.
- Produces: Passing regression evidence and an updated visual comparison record.

- [ ] **Step 1: Compile Blade and run all Arunika V3 regressions**

Run: `php artisan view:clear`, `php artisan view:cache`, and `node --test tests/arunika-v3-*.test.mjs` using a PowerShell-expanded file list.

Expected: Blade cache succeeds and all Arunika V3 tests pass.

- [ ] **Step 2: Reload the authenticated page and capture evidence**

Use the existing approved Playwright session at `852 x 693`; capture expanded, collapsed, and profile-menu states. Verify collapse, search, notification, profile dropdown, dark mode action, and color swatches remain usable.

- [ ] **Step 3: Compare reference and implementation together**

Create a combined comparison image containing the source and latest implementation, inspect it, fix any remaining P0/P1/P2 shell mismatch, and repeat capture if needed.

- [ ] **Step 4: Update `design-qa.md`**

Record source path, screenshot paths, viewport/state, comparison history, interactions, console health, fidelity surfaces, and `final result: passed` only when no actionable P0/P1/P2 remains.
