# Arunika Canvas Mobile Spacing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove the Arunika Canvas outer layout margin on mobile and increase the mobile header horizontal padding from `10px` to `14px`.

**Architecture:** Keep the change inside the existing Canvas-specific `@media (max-width: 768px)` block. Extend the existing Canvas mobile regression test so the intended selector scope and exact values remain protected.

**Tech Stack:** CSS, Node.js static regression test, Laravel Blade runtime, Browser Playwright API.

## Global Constraints

- Apply only to `.ph-theme-arunika-canvas` at viewport widths up to `768px`.
- Keep desktop spacing unchanged.
- Keep `.ph-top-bar` height at `52px` and vertical padding at `0`.
- Do not modify Blade markup, sidebar JavaScript, or content padding.
- Back up every existing file before modifying it.

---

### Task 1: Lock and Implement Mobile Canvas Spacing

**Files:**
- Modify: `tests/arunika-canvas-mobile-sidebar-static.test.mjs`
- Modify: `public/assets/css/themes/arunika_canvas/arunika_canvas.css`
- Backup: `backups/arunika-canvas-mobile-spacing-<timestamp>/tests/arunika-canvas-mobile-sidebar-static.test.mjs`
- Backup: `backups/arunika-canvas-mobile-spacing-<timestamp>/public/assets/css/themes/arunika_canvas/arunika_canvas.css`

**Interfaces:**
- Consumes: the final Canvas-specific `@media (max-width: 768px)` CSS block.
- Produces: mobile computed layout margin `0px` and header padding `0px 14px` without changing desktop rules.

- [ ] **Step 1: Back up the existing CSS and regression test**

Create a timestamped folder under `backups/`, preserve the original relative paths, copy both files, and compare SHA-256 hashes between each source and backup.

- [ ] **Step 2: Add failing static assertions**

Add these assertions before the final `console.log` in `tests/arunika-canvas-mobile-sidebar-static.test.mjs`:

```js
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-theme-arunika-canvas \.ph-layout-right\s*\{[^}]*margin:\s*0;/s,
	'mobile Canvas layout should remove the desktop outer margin',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-theme-arunika-canvas \.ph-top-bar\s*\{[^}]*padding:\s*0 14px;/s,
	'mobile Canvas header should use modest 14px horizontal padding',
);
```

- [ ] **Step 3: Run the focused test and verify RED**

Run:

```powershell
node --test tests\arunika-canvas-mobile-sidebar-static.test.mjs
```

Expected: FAIL because the mobile layout rule has no `margin: 0` and the header still uses `padding: 0 10px`.

- [ ] **Step 4: Apply the minimal CSS override**

Inside the final Canvas `@media (max-width: 768px)` block, add:

```css
	.ph-theme-arunika-canvas .ph-layout-right
	{
		margin: 0;
	}
```

Then change the existing `.ph-theme-arunika-canvas .ph-top-bar` declaration to:

```css
	.ph-theme-arunika-canvas .ph-top-bar
	{
		padding: 0 14px;
		gap: 7px;
	}
```

- [ ] **Step 5: Run the focused test and verify GREEN**

Run:

```powershell
node --test tests\arunika-canvas-mobile-sidebar-static.test.mjs
```

Expected: PASS with `Arunika Canvas mobile sidebar regression passed.`

- [ ] **Step 6: Run regression and framework verification**

Run:

```powershell
node --test tests\arunika-canvas-*.test.mjs
php artisan test
php artisan view:clear
php artisan view:cache
git diff --check
```

Expected: the focused mobile regression passes; no new Canvas failure is introduced; Laravel reports `72 passed`; Blade cache commands and diff check exit successfully.

- [ ] **Step 7: Verify authenticated mobile runtime**

At `https://laravel-13-phoenix.aruna/manage_article` with viewport `414 x 846`, reload and inspect computed styles:

```js
{
	layoutMargin: getComputedStyle(document.querySelector('.ph-layout-right')).margin,
	headerPadding: getComputedStyle(document.querySelector('.ph-top-bar')).padding,
	headerHeight: getComputedStyle(document.querySelector('.ph-top-bar')).height,
	horizontalOverflow: document.documentElement.scrollWidth > document.documentElement.clientWidth,
}
```

Expected: `layoutMargin: '0px'`, `headerPadding: '0px 14px'`, `headerHeight: '52px'`, and `horizontalOverflow: false`. Capture a mobile screenshot and check browser errors/warnings.

- [ ] **Step 8: Leave the production patch uncommitted for user review**

Run `git status --short` and report the exact changed and backup files. Do not stage, commit, or push production changes unless the user explicitly requests it.
