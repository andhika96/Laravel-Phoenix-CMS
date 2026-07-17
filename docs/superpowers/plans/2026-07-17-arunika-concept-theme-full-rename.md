# Arunika Concept Theme Full Rename Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace Arunika V1/V2/V3 with Arunika Mosaic/Aurora/Canvas across active code, assets, folders, tests, and the live database without changing theme behavior.

**Architecture:** Treat the work as one coordinated identifier migration. Rename filesystem entry points and active references mechanically, then use a reversible Laravel migration to preserve row IDs while updating `themes` and `theme_settings` codes and display names.

**Tech Stack:** Laravel migrations and database transactions, Blade, CSS, JavaScript, Node static regressions, PHPUnit/Pest feature tests, PowerShell filesystem operations.

## Global Constraints

- Mapping: `arunika_v1` to `arunika_mosaic`, `arunika_v2` to `arunika_aurora`, and `arunika_v3` to `arunika_canvas`.
- Display names: `Arunika Mosaic`, `Arunika Aurora`, and `Arunika Canvas`.
- Preserve visuals, routes, interactions, dynamic content, database row IDs, and unrelated dirty-worktree changes.
- Back up every affected file tree and live database row before editing.
- Do not rename the already-recorded historical migration filename.

---

### Task 1: Lock the rename contract with a failing regression

**Files:**
- Create: `tests/arunika-concept-theme-rename-static.test.mjs`

**Interfaces:**
- Consumes: the approved mapping in the design spec.
- Produces: static assertions for concept folders, asset filenames, controller codes, seeder values, and absence of active version identifiers.

- [ ] **Step 1:** Add assertions for `arunika_mosaic`, `arunika_aurora`, and `arunika_canvas` runtime paths and identifiers.
- [ ] **Step 2:** Run `node --test tests/arunika-concept-theme-rename-static.test.mjs` and confirm failure because the concept folders do not exist.

### Task 2: Rename filesystem entry points and active textual identifiers

**Files:**
- Rename: `resources/views/themes/arunika_v1` to `resources/views/themes/arunika_mosaic`.
- Rename: `resources/views/themes/arunika_v2` to `resources/views/themes/arunika_aurora`.
- Rename: `resources/views/themes/arunika_v3` to `resources/views/themes/arunika_canvas`.
- Rename: matching CSS/JS directories, entry filenames, preview images, mockup filename, and version-prefixed test filenames.
- Modify: the active files reported by the scoped identifier inventory.

**Interfaces:**
- Consumes: the old runtime paths and identifiers.
- Produces: concept-based Blade namespaces, asset URLs, CSS body classes, JS selectors, controller metadata, seeder values, mocks, and tests.

- [ ] **Step 1:** Move directories and entry assets without deleting user changes.
- [ ] **Step 2:** Apply exact mechanical replacements for underscore, hyphen, display-name, and CSS-class variants.
- [ ] **Step 3:** Update the focused test to scan only active runtime roots and permit the historical migration filename.
- [ ] **Step 4:** Run the focused Node test and confirm it passes.

### Task 3: Add and execute the reversible database migration

**Files:**
- Create: `database/migrations/2026_07_17_131400_rename_arunika_theme_identifiers.php`.
- Modify: `database/seeders_new/ThemesSeeder.php` and `database/seeders_new/ThemeSettingsSeeder.php` through the mechanical rename.

**Interfaces:**
- Consumes: existing rows identified by `arunika_v1`, `arunika_v2`, and `arunika_v3`.
- Produces: the same row IDs with concept codes, folders, and display names; matching active `theme_settings` identity.

- [ ] **Step 1:** Implement collision checks and transactional `up()`/`down()` mappings.
- [ ] **Step 2:** Run the migration and query both tables to verify codes, names, folder names, and unchanged IDs.
- [ ] **Step 3:** Run `php artisan migrate:status` and confirm the rename migration is recorded.

### Task 4: Verify runtime resolution and regression safety

**Files:**
- Test: `tests/arunika-concept-theme-rename-static.test.mjs`.
- Test: renamed Arunika static test files.
- Test: `tests/Feature/ThemeManagerTest.php`.

**Interfaces:**
- Consumes: renamed filesystem and database state.
- Produces: evidence that Theme Manager and the active theme resolve the concept identifiers.

- [ ] **Step 1:** Run the focused concept-rename and Theme Manager tests.
- [ ] **Step 2:** Run all renamed Arunika Node regressions and record any pre-existing stale assertions separately.
- [ ] **Step 3:** Run `php artisan test --compact`.
- [ ] **Step 4:** Clear and rebuild Laravel views/config, then verify the concept CSS/JS/preview URLs return HTTP 200.
- [ ] **Step 5:** Render or request the active dashboard and confirm it resolves `themes.arunika_canvas.cms.cms_layout` without a missing-view error.
- [ ] **Step 6:** Run scoped old-identifier search and `git diff --check`.

