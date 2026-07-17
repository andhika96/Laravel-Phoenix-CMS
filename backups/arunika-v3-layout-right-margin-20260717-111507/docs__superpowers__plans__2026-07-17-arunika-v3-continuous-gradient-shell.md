# Arunika V3 Continuous Gradient Shell Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Render one continuous Arunika gradient behind the V3 sidebar and shell while removing the vertical seam beside the right canvas.

**Architecture:** The parent `.ph-app-shell` becomes the single gradient painter. The sidebar becomes transparent, and both possible seam sources are explicitly disabled at the V3 override layer.

**Tech Stack:** CSS custom properties, Laravel Blade theme assets, Node.js static regression tests.

## Global Constraints

- Back up every existing file before modifying it.
- Preserve the dirty working tree and do not stage, commit, reset, clean, or overwrite unrelated changes.
- Keep V3 sidebar geometry, menu states, profile placement, content padding, and responsive behavior unchanged.
- Use the existing `--ph-sidebar-surface` gradient for both light and dark modes.

---

### Task 1: Lock the continuous-gradient and seam-free contract

**Files:**
- Modify: `tests/arunika-v3-sidebar-shell-surface-static.test.mjs`
- Modify: `public/assets/css/themes/arunika_v3/arunika_v3.css:3076-3096`

**Interfaces:**
- Consumes: `--ph-sidebar-surface`, `.ph-app-shell`, `.ph-sidebar`, `.ph-layout-right`.
- Produces: one parent-painted gradient with transparent, borderless sidebar and seam-free right canvas.

- [ ] **Step 1: Back up the current CSS and focused test**

Create `backups/arunika-v3-continuous-gradient-shell-<timestamp>/` and copy both files into it, then verify SHA-256 hashes match their sources.

- [ ] **Step 2: Change the focused regression to the approved behavior**

Require these declarations:

```css
.ph-theme-arunika-v3 .ph-app-shell {
    background: var(--ph-sidebar-surface);
}

.ph-theme-arunika-v3 .ph-sidebar,
.ph-theme-arunika-v3 .ph-sidebar.ph-expanded {
    background: transparent;
    border-right: 0;
}

.ph-theme-arunika-v3 .ph-layout-right {
    border-left: 0;
    box-shadow: none;
}
```

- [ ] **Step 3: Run the focused test and verify RED**

Run:

```powershell
node --test tests/arunika-v3-sidebar-shell-surface-static.test.mjs
```

Expected: FAIL because the app shell still uses `--ph-v3-shell-gutter`, the sidebar uses that same solid token, and the seam guards are absent.

- [ ] **Step 4: Apply the minimal CSS implementation**

Change only the V3 override declarations shown in Step 2. Do not alter shared Arunika V2 rules.

- [ ] **Step 5: Run the focused test and verify GREEN**

Run the same Node command. Expected: `1 passed, 0 failed`.

- [ ] **Step 6: Run proportional verification**

Run all Arunika V3 Node tests, `php artisan test`, `php artisan view:clear`, `php artisan view:cache`, verify the served CSS returns HTTP 200 with the new declarations, and run `git diff --check`.

- [ ] **Step 7: Report visual QA status honestly**

If an authenticated browser is callable, verify expanded, collapsed, mobile, light, and dark states. Otherwise report the browser blocker and request a fresh `Ctrl+F5` screenshot without claiming screenshot proof.
