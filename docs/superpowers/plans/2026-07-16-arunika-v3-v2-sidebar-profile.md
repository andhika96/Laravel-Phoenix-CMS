# Arunika V3 V2 Sidebar Profile Implementation Plan

> **For agentic workers:** Execute inline in the existing workspace because this change depends on the current uncommitted Arunika V3 fidelity work. Do not create a separate worktree or commit unrelated changes.

**Goal:** Make the Arunika V3 sidebar use Arunika V2's navigation and footer UI while keeping the current V3 header collapse control, removing the standalone sidebar Settings button, and relocating the complete profile dropdown to the sidebar footer.

**Architecture:** Reuse the existing dynamic menu and Arunika V2 sidebar CSS contract already present in the V3 stylesheet. Move the existing profile dropdown markup without duplicating its IDs or behaviors, then add only the positioning rules needed for a sidebar-hosted dropdown.

**Tech Stack:** Laravel Blade, Bootstrap 5 dropdowns, theme-scoped CSS, Node static regression tests, Playwright CLI.

## Global Constraints

- Preserve dynamic `menu_versioning()`, authentication data, role guards, notifications, theme color controls, and `@yield('content')`.
- Keep the current collapse button, SVG, divider, search placement, and collapse behavior in the header.
- Remove only the standalone sidebar Settings link; Settings remains available inside the profile dropdown for administrators.
- Back up every existing file before editing.

---

### Task 1: Lock the approved DOM hierarchy with a failing test

**Files:**
- Modify: `tests/arunika-v3-theme-static.test.mjs`

- [ ] Assert that the sidebar footer contains the profile trigger and dropdown before `.ph-layout-right`.
- [ ] Assert that the standalone `.ph-sidebar-settings-link` and header profile trigger are absent.
- [ ] Assert that the header collapse control remains before search.
- [ ] Run the focused test and confirm it fails on the old structure.

### Task 2: Move the profile and restore the Arunika V2 sidebar contract

**Files:**
- Modify: `resources/views/themes/arunika_v3/cms/cms_layout.blade.php`
- Modify: `public/assets/css/themes/arunika_v3/arunika_v3.css`

- [ ] Replace the sidebar Settings footer with an Arunika V2-style user card containing the current avatar, name, and role.
- [ ] Move the existing Appearance, theme color, profile, admin Settings, and Logout dropdown into that footer.
- [ ] Remove the profile trigger from the header while retaining notifications.
- [ ] Restore Arunika V2 sidebar spacing, typography, active states, category rhythm, submenu geometry, and footer card styling under the V3 scope.
- [ ] Position the profile dropdown beside the desktop sidebar and above the profile card on mobile.
- [ ] Run the focused test and confirm it passes.

### Task 3: Rendered verification and regression suite

**Files:**
- Modify: `design-qa.md`

- [ ] Verify desktop expanded, desktop collapsed, profile dropdown open, and mobile drawer/profile states in the authenticated browser.
- [ ] Confirm no horizontal overflow and no relevant console errors or warnings.
- [ ] Run all Arunika V3 Node tests, the Laravel suite, Blade cache, and `git diff --check`.
- [ ] Record accepted screenshots and measured geometry in `design-qa.md`.
