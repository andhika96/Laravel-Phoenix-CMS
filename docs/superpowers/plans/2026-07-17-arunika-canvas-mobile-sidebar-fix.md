# Arunika Canvas Mobile Sidebar Fix Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the Arunika Canvas mobile sidebar start closed, remain opaque, and provide an in-drawer close control without changing desktop behavior.

**Architecture:** Keep the existing `ph-expanded` class as the single drawer state. Add one Canvas-only close control, final Canvas-scoped mobile surface rules, and a breakpoint-crossing synchronizer that keeps the desktop preference separate from temporary mobile drawer state.

**Tech Stack:** Laravel Blade, plain JavaScript, CSS, Node.js `node:test`/`assert`, Laravel PHPUnit.

## Global Constraints

- Workspace: `D:\Laragon\www\laravel-13-phoenix`.
- Scope is Arunika Canvas only; do not modify Aurora or Mosaic.
- Mobile breakpoint remains exactly `768px`.
- Mobile drawer width remains exactly `256px`.
- Light mobile drawer is solid `#ffffff`; dark mobile drawer is solid `#202120`.
- Preserve the approved desktop header collapse control, sidebar/profile layout, and menu spacing.
- Create and verify a timestamped backup before modifying production files.
- Do not add dependencies.
- Do not commit, merge, or push the production patch unless separately requested.

---

### Task 1: Add a failing Canvas mobile-sidebar regression

**Files:**
- Create: `tests/arunika-canvas-mobile-sidebar-static.test.mjs`
- Test: `tests/arunika-canvas-mobile-sidebar-static.test.mjs`

**Interfaces:**
- Consumes: Canvas Blade layout, stylesheet, and JavaScript as UTF-8 source.
- Produces: static assertions for markup, opaque mobile surfaces, and breakpoint synchronization.

- [ ] **Step 1: Write the failing test**

Create a Node assertion test that reads the three Canvas production files and verifies:

```js
assert.match(layout, /class="ph-mobile-sidebar-close"[\s\S]*?onclick="toggleSidebar\(\)"[\s\S]*?Close navigation/);
assert.match(layout, /class="ph-header-nav-control"[\s\S]*?id="sidebar-toggle"/);
assert.match(css, /\.ph-theme-arunika-canvas \.ph-sidebar,\s*\.ph-theme-arunika-canvas \.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*#ffffff;[^}]*backdrop-filter:\s*none;/s);
assert.match(css, /\[data-bs-theme=dark\] \.ph-theme-arunika-canvas \.ph-sidebar,[\s\S]*?background:\s*#202120;/s);
assert.match(css, /\.ph-sidebar\.ph-expanded \.ph-mobile-sidebar-close\s*\{[^}]*display:\s*inline-flex;/s);
assert.match(js, /const MOBILE_SIDEBAR_BREAKPOINT = 768;/);
assert.match(js, /function syncSidebarForViewport\(\)/);
assert.match(js, /sidebar\.classList\.remove\('ph-expanded'\)/);
assert.match(js, /window\.addEventListener\('resize', syncSidebarForViewport\)/);
```

- [ ] **Step 2: Run the regression and confirm RED**

Run:

```powershell
node --test tests\arunika-canvas-mobile-sidebar-static.test.mjs
```

Expected: FAIL because `ph-mobile-sidebar-close` and the final solid Canvas mobile rules do not yet exist.

---

### Task 2: Back up and implement the Canvas mobile drawer contract

**Files:**
- Modify: `resources/views/themes/arunika_canvas/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_canvas/arunika_canvas.css`
- Modify: `public/assets/js/themes/arunika_canvas/arunika_canvas.js`
- Backup: `backups/arunika-canvas-mobile-sidebar-<timestamp>/`
- Test: `tests/arunika-canvas-mobile-sidebar-static.test.mjs`

**Interfaces:**
- Consumes: existing `toggleSidebar()`, `updateSidebarToggleState()`, `ph-expanded`, and `sidebar-state` desktop preference.
- Produces: `syncSidebarForViewport()` and `.ph-mobile-sidebar-close`.

- [ ] **Step 1: Create and verify the backup**

Copy the three production files into a timestamped backup directory while retaining their relative paths. Record SHA-256 hashes for source and backup and require exact matches before editing.

- [ ] **Step 2: Add the in-drawer close control**

Inside `.ph-sidebar-logo-container`, after `.ph-app-logo-text`, add:

```blade
<button class="ph-mobile-sidebar-close" type="button" onclick="toggleSidebar()" aria-label="{{ t('Close navigation') }}">
	<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
		<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
		<path d="M8.25 3.25V20.75"></path>
		<path d="M16 8.75L12.75 12L16 15.25"></path>
	</svg>
</button>
```

Set the header hamburger's initial state with `aria-expanded="false"`.

- [ ] **Step 3: Add final Canvas mobile surface and close-control rules**

Define `.ph-mobile-sidebar-close` as hidden by default. In the final Canvas `@media (max-width: 768px)` block:

```css
.ph-theme-arunika-canvas .ph-sidebar,
.ph-theme-arunika-canvas .ph-sidebar.ph-expanded
{
	background: #ffffff;
	backdrop-filter: none;
	-webkit-backdrop-filter: none;
}

[data-bs-theme=dark] .ph-theme-arunika-canvas .ph-sidebar,
[data-bs-theme=dark] .ph-theme-arunika-canvas .ph-sidebar.ph-expanded
{
	background: #202120;
}

.ph-theme-arunika-canvas .ph-sidebar.ph-expanded .ph-mobile-sidebar-close
{
	display: inline-flex;
}
```

Style the button as a `36px` square Canvas control positioned at the right of the logo row, with no desktop display.

- [ ] **Step 4: Synchronize state only when crossing the breakpoint**

Add `MOBILE_SIDEBAR_BREAKPOINT = 768`, track the previous viewport category, and implement `syncSidebarForViewport()` so desktop-to-mobile removes `ph-expanded`, while mobile-to-desktop restores `localStorage['sidebar-state']`. Update hamburger `aria-expanded`; only persist `sidebar-state` when toggling on desktop.

- [ ] **Step 5: Run focused GREEN verification**

Run:

```powershell
node --test tests\arunika-canvas-mobile-sidebar-static.test.mjs
node --check public\assets\js\themes\arunika_canvas\arunika_canvas.js
```

Expected: all assertions pass and JavaScript syntax is valid.

---

### Task 3: Regression and rendered QA

**Files:**
- Verify: `tests/arunika-canvas-*.test.mjs`
- Verify: Laravel test suite and Blade cache.
- Verify: rendered `/manage_article` mobile/desktop behavior.

**Interfaces:**
- Consumes: completed Canvas patch.
- Produces: automated and rendered evidence of the three requested mobile behaviors.

- [ ] **Step 1: Run all Canvas and relevant theme regressions**

Enumerate matching Canvas Node tests and run them individually so PowerShell wildcard behavior cannot skip files. Record passed/failed totals.

- [ ] **Step 2: Run Laravel and Blade verification**

Run:

```powershell
php artisan test
php artisan view:clear
php artisan view:cache
git diff --check
```

Expected: relevant tests pass, Blade compiles, and diff whitespace is clean.

- [ ] **Step 3: Run mobile interaction QA**

Use the available browser path. Because the Browser plugin failed with `windows sandbox failed: helper_unknown_error`, use the approved Playwright fallback against `https://laravel-13-phoenix.aruna/manage_article`.

Verify:

1. direct `414 x 846` load starts closed;
2. desktop expanded -> resize to `414 x 846` closes automatically;
3. hamburger opens a `256px` solid white drawer;
4. the in-drawer close control closes it;
5. dark mode drawer is solid `#202120`;
6. desktop expanded/collapsed behavior still works;
7. console has no relevant warning/error.

- [ ] **Step 4: Report without committing the production patch**

Report changed files, backup path, exact test totals, browser evidence, and remaining risks. Leave production changes uncommitted unless the user separately requests a commit.
