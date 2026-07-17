# Arunika Canvas Header Control and Search Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Use the desktop panel SVG on the mobile navigation control, size the mobile button to `36 x 36px`, and hide the Canvas search form at every viewport.

**Architecture:** Extend the existing Canvas mobile static regression, then make surgical Blade and CSS changes. Keep all sidebar behavior in the existing JavaScript unchanged.

**Tech Stack:** Laravel Blade, CSS, Node.js static regression tests, Playwright CLI.

## Global Constraints

- Scope all visual rules to Arunika Canvas.
- Keep the desktop collapse button at `32 x 32px`.
- Keep the shared SVG icon at `18 x 18px`.
- Keep `toggleSidebar()`, accessibility labels, and `aria-expanded` behavior.
- Back up every existing file before editing.

---

### Task 1: Update Canvas Header Control and Search Visibility

**Files:**
- Modify: `tests/arunika-canvas-mobile-sidebar-static.test.mjs`
- Modify: `resources/views/themes/arunika_canvas/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_canvas/arunika_canvas.css`
- Backup: `backups/arunika-canvas-header-control-search-20260717-221906/...`

**Interfaces:**
- Consumes: existing `toggleSidebar()` behavior and `.ph-sidebar-toggle-icon` styling.
- Produces: mobile navigation button `36 x 36px`, panel SVG `18 x 18px`, and hidden Canvas search on desktop/mobile.

- [ ] **Step 1: Back up Blade, CSS, and the existing mobile regression test**

Create a timestamped backup preserving relative paths and verify each source/backup SHA-256 pair matches.

- [ ] **Step 2: Add failing regression assertions**

Extend the existing test to require:

```js
assert.match(
	layout,
	/class="ph-mobile-sidebar-trigger"[\s\S]*?<svg class="ph-sidebar-toggle-icon"[\s\S]*?<rect x="2\.75"[\s\S]*?<path d="M8\.25 3\.25V20\.75"[\s\S]*?<path class="ph-sidebar-toggle-chevron"/,
	'mobile navigation should reuse the desktop panel SVG',
);
assert.match(
	css,
	/\.ph-theme-arunika-canvas \.ph-mobile-sidebar-trigger\s*\{[^}]*width:\s*36px;[^}]*height:\s*36px;/s,
	'mobile Canvas navigation button should use a balanced 36px control',
);
assert.match(
	css,
	/\.ph-theme-arunika-canvas \.ph-search-container\s*\{[^}]*display:\s*none\s*!important;/s,
	'Canvas search should stay hidden at every viewport',
);
```

- [ ] **Step 3: Run focused test and verify RED**

Run `node --test tests\arunika-canvas-mobile-sidebar-static.test.mjs`.

Expected: FAIL because the mobile trigger still contains `fas fa-bars`, remains `40px`, and Canvas search is forced visible.

- [ ] **Step 4: Implement the minimal Blade and CSS changes**

Replace the mobile trigger `<i class="fas fa-bars"></i>` with the exact desktop panel SVG markup. Add Canvas-scoped `width: 36px; height: 36px;` and set its mobile flex basis to `36px`. Change the Canvas search declaration to `display: none !important`.

- [ ] **Step 5: Run focused test and verify GREEN**

Run `node --test tests\arunika-canvas-mobile-sidebar-static.test.mjs`.

Expected: PASS.

- [ ] **Step 6: Run project verification**

Run the Canvas regression suite, `php artisan test`, `php artisan view:clear`, `php artisan view:cache`, and `git diff --check`. Confirm no new failure beyond the three known stale Canvas expectations.

- [ ] **Step 7: Verify runtime desktop and mobile**

At `/manage_article`, verify mobile `414 x 846` reports a `36 x 36px` navigation button, SVG `18 x 18px`, hidden search, working open/close behavior, no overflow, and zero console errors/warnings. Verify desktop `1440 x 900` retains a `32 x 32px` collapse button and also hides search. Capture desktop and mobile screenshots outside the repository.

- [ ] **Step 8: Leave the production patch uncommitted**

Report exact files and backup path. Do not stage, commit, or push production changes without an explicit user request.
