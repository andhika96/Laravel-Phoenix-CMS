# Arunika V3 Sidebar Logo Offset Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Align the sidebar logo area with the right canvas by applying the approved `15px` structural top offset.

**Architecture:** Keep the existing flex-centered logo container and add one V3-scoped `margin-top`. A focused static regression ties that offset to the existing right-canvas top margin.

**Tech Stack:** CSS and Node.js static regression tests.

## Global Constraints

- Back up the existing Arunika V3 stylesheet before editing.
- Preserve all unrelated dirty-worktree changes.
- Keep the logo container height at `var(--ph-v3-header-height)` and use `margin-top: 15px`.
- Do not use transforms or change logo dimensions.

---

### Task 1: Align the sidebar logo area

**Files:**
- Create: `tests/arunika-v3-sidebar-logo-offset-static.test.mjs`
- Modify: `public/assets/css/themes/arunika_v3/arunika_v3.css`

**Interfaces:**
- Consumes: `.ph-layout-right`, `.ph-sidebar-logo-container`, `--ph-v3-header-height`.
- Produces: a shared `15px` top alignment boundary for the sidebar logo and right canvas.

- [ ] **Step 1: Back up the stylesheet**

Copy `public/assets/css/themes/arunika_v3/arunika_v3.css` into `backups/arunika-v3-sidebar-logo-offset-<timestamp>/` and verify its SHA-256 hash.

- [ ] **Step 2: Write and run the failing regression**

Require `margin: 15px 15px 15px 0` on `.ph-layout-right` and `margin-top: 15px` on the V3 logo container. Run `node --test tests/arunika-v3-sidebar-logo-offset-static.test.mjs`; expect failure because the logo offset is absent.

- [ ] **Step 3: Apply the minimal CSS change**

Add `margin-top: 15px` inside the existing V3 logo-container override without changing any other declaration.

- [ ] **Step 4: Verify GREEN and regressions**

Run the focused test, all Arunika V3 Node tests, `php artisan test`, Blade cache commands, served CSS validation, and `git diff --check`.

