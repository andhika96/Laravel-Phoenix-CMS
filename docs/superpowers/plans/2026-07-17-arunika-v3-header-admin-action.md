# Arunika V3 Header Admin Action Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restore a proportional, permission-gated Awesome Admin shortcut as the final Arunika V3 header action while temporarily hiding notifications.

**Architecture:** Keep the shared notification component intact, but mount it inside a V3-only hidden wrapper. Add the Awesome Admin anchor after that wrapper and size both icon actions through scoped Arunika V3 CSS.

**Tech Stack:** Laravel Blade, CSS, Font Awesome, Node.js static tests.

## Global Constraints

- Back up every existing file before modifying it.
- Preserve the dirty working tree and unrelated Arunika V3 edits.
- Awesome Admin shortcut is admin-only, points to `url('awesome_admin')`, and uses `fa-user-secret`.
- Both header icon buttons are `34px` square with `16px` glyphs.
- Notification markup remains mounted but hidden.

---

### Task 1: Header action hierarchy and sizing

**Files:**
- Create: `tests/arunika-v3-header-actions-static.test.mjs`
- Modify: `resources/views/themes/arunika_v3/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_v3/arunika_v3.css`

**Interfaces:**
- Consumes: `checkIsAdmin()`, `url('awesome_admin')`, `components.cms-realtime-notification`, `.ph-header-actions`, `.ph-btn-action-icon`.
- Produces: `.ph-header-notification.is-hidden` and `.ph-header-awesome-admin`.

- [ ] **Step 1: Back up the Blade layout and Arunika V3 stylesheet**

Copy both existing files into `backups/arunika-v3-header-admin-action-<timestamp>/` and verify SHA-256 hashes.

- [ ] **Step 2: Write the failing static regression**

Require the hidden notification wrapper to precede an admin-gated Awesome Admin anchor using `fa-user-secret`, and require `34px` buttons with `16px` glyphs.

- [ ] **Step 3: Run the regression and verify RED**

Run `node --test tests/arunika-v3-header-actions-static.test.mjs`. Expected: failure because the shortcut is commented out and the current size is `32px / 13px`.

- [ ] **Step 4: Apply the minimal Blade and CSS changes**

Keep the shared notification include, wrap it in `.ph-header-notification.is-hidden`, add the admin-only shortcut after it, and update the V3 action sizing.

- [ ] **Step 5: Run focused and proportional verification**

Run the focused test, relevant Arunika V3 tests, `php artisan test`, Blade cache commands, served CSS contract checks, and `git diff --check`.

