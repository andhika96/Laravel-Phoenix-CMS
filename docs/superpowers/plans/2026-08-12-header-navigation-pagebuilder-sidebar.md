# Header Navigation Page Builder Inspector Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (recommended) or `superpowers:executing-plans` to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the Header Navigation right inspector so its information architecture, density, tabs, accordions, controls, and spacing follow the Page Builder Elementor v2.3 settings-panel pattern while preserving every existing Header Navigation control, preview behavior, JSON configuration, API payload, and save flow.

**Architecture:** Keep the existing 400/420px outer inspector width because the current implementation became unusably narrow when it tried to imitate the Page Builder width. Replace the six-icon navigation with a three-tab inspector (`Layout`, `Style`, `Advanced`), render only the selected tab panel, and group the existing native `<details>` sections as follows: Layout = Preview, Layout, Sizing; Style = Colors, Effects; Advanced = Behavior, Config Preview. Keep all existing field IDs and control classes so the current JavaScript state/update code remains the source of truth. Add a scoped CSS contract that mirrors the measured Page Builder v2.3 compact geometry without loading or modifying Page Builder assets.

**Tech Stack:** Laravel Blade, scoped CSS, existing vanilla JavaScript controller, PHPUnit/Pest feature tests, Node syntax check, `php artisan view:cache`, Graphify incremental update, authenticated read-only browser QA.

## Global Constraints

- Work only in `D:\Laragon\www\laravel-13-phoenix`; preserve unrelated dirty changes and all existing backup artifacts.
- Read the approved design spec at `docs/superpowers/specs/2026-08-12-header-navigation-pagebuilder-sidebar-design.md` before implementation.
- Before changing an existing file, create a timestamped sibling backup. The four source files in scope are the Blade view, its CSS, its JavaScript, and `tests/Feature/HeaderNavigationSettingsTest.php`.
- Do not modify the Header Navigation backend, Page Builder v2.0/v2.3 source, frontend renderer, preview artboard, timeline behavior, or persistence contract.
- Do not use the Page Builder stylesheet globally. All new selectors must be scoped below `.header-navigation-editor.editorial-option-two`.
- Keep the right inspector outer sizing at `--mock-sidebar-min: 400px`, `--mock-sidebar-width: 420px`, and preserve the existing panel-height contract; improve usable space through the internal layout and density.
- Do not click Save or change persisted page settings during browser QA. Do not commit, push, or stage files without separate user authorization.
- The approved compact contract is: panel header min-height 58px; tabs height 42px; panel body padding 16px; summary card padding 10px and radius 10px; accordion summary min-height 34px; form labels 9.5px with 6px bottom spacing; inputs/selects 34px high with 10px horizontal padding and 8px radius; segmented controls 27px high; toggles 28x16px; compact fields 30px high.

---

## Task 1: Prepare the implementation workspace and backups

- [ ] Confirm the active repository and dirty worktree without changing it:

  ```powershell
  git status --short
  git branch --show-current
  git rev-parse --show-toplevel
  ```

- [ ] Read the approved spec and confirm the four active source paths before editing:

  ```powershell
  Get-Content -Raw docs/superpowers/specs/2026-08-12-header-navigation-pagebuilder-sidebar-design.md
  Test-Path resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php
  Test-Path public/assets/css/awesome-admin-header-navigation.css
  Test-Path public/assets/js/awesome-admin-header-navigation.js
  Test-Path tests/Feature/HeaderNavigationSettingsTest.php
  ```

- [ ] Create a fresh timestamped backup for each existing file. Run from the repository root and retain the exact paths printed by the command:

  ```powershell
  $backupStamp = Get-Date -Format 'yyyyMMdd_HHmmss_header_navigation_pagebuilder_sidebar'
  $backupTargets = @(
    'resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php',
    'public/assets/css/awesome-admin-header-navigation.css',
    'public/assets/js/awesome-admin-header-navigation.js',
    'tests/Feature/HeaderNavigationSettingsTest.php'
  )
  foreach ($backupTarget in $backupTargets) {
    Copy-Item -LiteralPath $backupTarget -Destination "$backupTarget.bak_$backupStamp"
  }
  $backupTargets | ForEach-Object { Get-Item -LiteralPath "$_.*$backupStamp" }
  ```

## Task 2: Add the contract tests first and capture the expected RED state

- [ ] Update `tests/Feature/HeaderNavigationSettingsTest.php` while preserving its existing tests and assertions. Replace the old six-button `data-inspector-index` contract with a three-tab contract, but continue asserting that all seven existing settings groups and their field IDs remain present.

- [ ] Add a view contract test that asserts:
  - exactly three `data-inspector-tab` controls exist;
  - the tablist has `role="tablist"` and the three tabs expose `role="tab"` and `aria-selected`;
  - the tab values are `layout`, `style`, and `advanced`;
  - exactly three `inspector-tab-panel` elements exist with `role="tabpanel"` and matching `data-inspector-panel` values;
  - the mapped section labels occur in the correct panel: Preview/Layout/Sizing, Colors/Effects, and Behavior/Config Preview;
  - the original controls such as `deviceMode`, `headerBg`, `linkActive`, `headerHeight`, `innerBg`, and `jsonOutput` are still rendered.

- [ ] Add a source contract test for `public/assets/js/awesome-admin-header-navigation.js` that requires `selectInspectorTab`, `[data-inspector-tab]`, `aria-selected`, and the Effects deep-focus path, while rejecting the old listener dependency on `[data-inspector-index]`.

- [ ] Add a scoped CSS contract test that checks the approved Page Builder geometry values are present under the editorial Header Navigation scope: 58px header, 42px tabs, 16px body padding, 10px summary padding, 34px summaries/inputs, 9.5px labels, 27px segmented controls, 28x16px toggles, and 30px compact controls.

- [ ] Run the focused test before production changes and record the expected failure as the TDD RED checkpoint:

  ```powershell
  php artisan test --filter='HeaderNavigationSettingsTest'
  ```

## Task 3: Restructure the Blade inspector into the three-tab Page Builder pattern

- [ ] In `resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php`, replace the six icon navigation buttons with a semantic inspector header and three text tabs:
  - a compact back/reset affordance that selects the Layout tab and does not mutate settings;
  - title `Header Navigation` and subtitle `Header navigation settings`;
  - a selection summary card with `Header Navigation` and `Frontend header`;
  - a `role="tablist"` containing `Layout`, `Style`, and `Advanced`, with one active tab and matching `aria-controls`/`aria-selected` attributes.

- [ ] Wrap the existing settings groups in three `role="tabpanel"` containers. Move the existing `<details class="header-setting-group">` nodes without changing their field markup or IDs:
  - `data-inspector-panel="layout"`: Preview, Layout, Sizing;
  - `data-inspector-panel="style"`: Colors, Effects;
  - `data-inspector-panel="advanced"`: Behavior, Config Preview.

- [ ] Keep native `<details>` behavior and the existing group classes. The active panel must be the only visible panel, and the initial state must show Layout with Preview open. Preserve the existing `.shadow-control`, `.json-output`, responsive controls, link controls, and all form labels/IDs exactly.

- [ ] Add stable IDs for tabs and panels so `aria-labelledby` and `aria-controls` form a complete accessible relationship. Keep `data-inspector-anchor="effects"` only where it is needed by the new JavaScript deep-focus behavior.

- [ ] Verify the Blade structure statically before moving on:

  ```powershell
  rg -n "data-inspector-tab|data-inspector-panel|role=\"tablist\"|role=\"tabpanel\"|header-setting-group|deviceMode|jsonOutput" resources/views/awesome_admin/header_navigation/awesome_admin_header_navigation.blade.php
  ```

## Task 4: Apply the scoped Page Builder density and spacing contract

- [ ] Append a narrowly scoped final block to `public/assets/css/awesome-admin-header-navigation.css`. Override the prior six-icon navigation rules rather than deleting historical rules needed by existing tests or other shells.

- [ ] Style the inspector using the measured v2.3 geometry:
  - `.inspector-shell` remains a vertical flex container with `min-width: 0` and `overflow: hidden`;
  - `.inspector-layout` remains a column layout with full width and min-height 0;
  - `.inspector-header` uses min-height 58px, 16px horizontal padding, and compact title/subtitle typography;
  - `.inspector-tabs` is a three-column grid with 42px tab height, 12px horizontal padding, 1px bottom border, and no icon column;
  - `.inspector-tab` uses 10px/650 typography, 42px height, a 2px active underline, and no oversized button padding;
  - `.inspector-content` has `min-width: 0`, `min-height: 0`, and owns the vertical scroll;
  - `.inspector-tab-panel` uses 16px body padding and hides inactive panels;
  - `.inspector-selection-summary` uses 10px padding, 10px radius, 14px bottom margin, and 9.5-10px typography;
  - `.header-setting-summary` uses a 34px minimum height, 10px text, and 2px horizontal padding;
  - `.header-setting-panel` uses 14px bottom padding, 11px group spacing, and no negative margins;
  - labels, selects, color controls, text inputs, and segmented controls use the approved compact dimensions and `min-width: 0` so fields do not collide;
  - 2/3-column color and shadow grids use explicit gaps and `minmax(0, 1fr)` tracks; linked color cards reserve their link button column without clipping values;
  - all existing CMS responsive font variables remain the source for `font-family` and base scaling; the new contract only limits the inspector's local scale and never introduces a new global font family.

- [ ] Add responsive safeguards for narrow container widths: collapse multi-column setting grids when their measured width cannot contain labels and fields, keep the three top tabs readable, and retain the 400/420px sidebar minimum so the inspector does not become a squeezed column.

- [ ] Check for accidental global selectors and layout regressions:

  ```powershell
  rg -n "(^|[,{[:space:]])(\.inspector-|\.header-setting-|\.segmented|\.box-|\.form-label)" public/assets/css/awesome-admin-header-navigation.css
  git diff --check -- public/assets/css/awesome-admin-header-navigation.css
  ```

## Task 5: Replace the old six-button focus logic with tab state and deep focus

- [ ] In `public/assets/js/awesome-admin-header-navigation.js`, add `selectInspectorTab(tabOrName, options = {})` that:
  - resolves the requested `layout`, `style`, or `advanced` tab;
  - toggles the active class and `aria-selected` on exactly three tab controls;
  - toggles `hidden`/active state on exactly three panel elements;
  - resets the inspector content scroll position to the top for a normal tab click;
  - does not touch `state`, form controls, preview rendering, API payloads, or save behavior.

- [ ] Bind the new function to `editor.querySelectorAll('[data-inspector-tab]')`. Bind the header back/reset affordance to `selectInspectorTab('layout')`. Remove the old listener and any `data-inspector-index`/`data-inspector-group` lookup dependency.

- [ ] Preserve the existing Effects affordance through a small `focusInspectorSection` adaptation: select the Style tab first, close sibling details, open Effects, focus/scroll `.shadow-control` when present, and keep the active accordion state visually synchronized. Do not change existing Preview/Layout/Behavior/Sizing/Colors/Config Preview control behavior.

- [ ] Keep the script compatible with the current editor initialization and avoid adding a framework or a second state store. Run a syntax check immediately:

  ```powershell
  node --check public/assets/js/awesome-admin-header-navigation.js
  ```

## Task 6: Run focused and full verification

- [ ] Run the focused feature test and confirm the new contract is GREEN:

  ```powershell
  php artisan test --filter='HeaderNavigationSettingsTest'
  ```

- [ ] Run the full relevant feature test file and inspect the count/assertions for regressions:

  ```powershell
  php artisan test tests/Feature/HeaderNavigationSettingsTest.php
  ```

- [ ] Compile Blade views and re-check the diff:

  ```powershell
  php artisan view:cache
  git diff --check
  git status --short
  ```

  - [ ] Query the existing Graphify output for the four modified source nodes and their test relationship. If the graph is stale after the source changes, run the project's incremental Graphify update for this scope only. Leave `graphify-out` untracked/unstaged and report the update status.

- [ ] Perform authenticated, read-only browser QA at `/awesome_admin/header-navigation` at the CMS viewport and one narrower viewport. Verify the three tabs, panel switching, accordion spacing, color/shadow forms, Effects deep focus, preview height, and no horizontal clipping. Do not click Save, Reset settings, or any action that persists data. If the browser/session is unavailable, report that boundary instead of claiming visual verification.

## Task 7: Handoff and rollback boundary

- [ ] Report the exact backup paths, modified files, tests/commands and their results, Graphify status, browser QA status, and any remaining limitation.

- [ ] Keep the worktree uncommitted unless the user separately authorizes staging or a commit. Rollback, if requested, uses only the fresh timestamped backups created in step 1 after verifying their paths; do not restore older backups blindly.
