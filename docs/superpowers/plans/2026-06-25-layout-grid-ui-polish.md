# Layout And Grid Settings UI Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Extend the approved Video/basic-widget sidebar cleanup to structural builder panels, starting with Container/Layout and then Grid, so spacing, labels, grouped sections, and responsive controls feel closer to the Elementor demo.

**Architecture:** Keep existing Vue bindings and behavior intact while introducing scoped layout/grid wrapper classes in `public/js/pagebuilder_elementor/app.js`, then layer editor-only CSS polish in `public/assets/css/pagebuilder_elementor.css` to normalize section rhythm, collapsible spacing, field sizing, device controls, and grid section density.

**Tech Stack:** Vue template strings inside `public/js/pagebuilder_elementor/app.js`, scoped sidebar CSS in `public/assets/css/pagebuilder_elementor.css`, local HTTP builder route verification, Node syntax check, Git diff validation.

---

### Task 1: Lock Scope And Confirm Safeguards

**Files:**
- Modify: `docs/superpowers/plans/2026-06-25-layout-grid-ui-polish.md`
- Verify: `public/js/pagebuilder_elementor/app.js.bak_20260625_layout_grid_ui_polish`
- Verify: `public/assets/css/pagebuilder_elementor.css.bak_20260625_layout_grid_ui_polish`

- [ ] Confirm this pass is limited to `Layout` and `Grid` sidebar UI polish.
- [ ] Confirm fresh backups exist for `app.js` and `pagebuilder_elementor.css` before editing.

### Task 2: Add Structural Wrappers In The Editor Sidebar

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] Wrap Container/Layout settings in a dedicated layout-settings shell.
- [ ] Wrap Grid settings in a dedicated grid-settings shell.
- [ ] Keep existing controls, bindings, and conditional rendering behavior unchanged while improving presentation structure.

### Task 3: Apply Shared Layout/Grid Rhythm

**Files:**
- Modify: `public/assets/css/pagebuilder_elementor.css`

- [ ] Add scoped CSS for layout/grid spacing, labels, field height, and toggle rows.
- [ ] Tighten collapsible and section rhythm so the sidebar reads closer to Elementor.
- [ ] Keep styling editor-only and avoid touching frontend renderer output.

### Task 4: Verify Builder Integrity

**Files:**
- Verify: `public/js/pagebuilder_elementor/app.js`
- Verify: `public/assets/css/pagebuilder_elementor.css`

- [ ] Run `node --check public/js/pagebuilder_elementor/app.js`.
- [ ] Run `git diff --check -- public/js/pagebuilder_elementor/app.js public/assets/css/pagebuilder_elementor.css docs/superpowers/plans/2026-06-25-layout-grid-ui-polish.md`.
- [ ] Run an HTTP check for `http://laravel-13-phoenix.aruna/pagebuilder-elementor/create`.
- [ ] Review the final diff to ensure the sweep stayed inside layout/grid sidebar UI.
