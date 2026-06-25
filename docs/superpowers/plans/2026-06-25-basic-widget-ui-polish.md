# Basic Widget Settings UI Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring the remaining basic widget settings panels closer to the cleaned-up Video widget rhythm so the builder sidebar feels consistently tidy before moving on to layout and grid.

**Architecture:** Reuse the Video-panel spacing and grouping ideas by introducing a shared basic-widget settings wrapper in the editor sidebar markup, then add scoped CSS that normalizes label rhythm, field sizing, toggle rows, media fields, and responsive-control spacing across `Heading`, `Text Editor`, `Image`, `Button`, `Divider`, and `Spacer`.

**Tech Stack:** Vue template strings inside `public/js/pagebuilder_elementor/app.js`, scoped sidebar CSS in `public/assets/css/pagebuilder_elementor.css`, local HTTP builder route verification, Node syntax check, Git diff validation.

---

### Task 1: Document Scope And Protect Current Work

**Files:**
- Modify: `docs/superpowers/plans/2026-06-25-basic-widget-ui-polish.md`
- Verify: `public/js/pagebuilder_elementor/app.js.bak_20260625_basic_widget_ui_polish`
- Verify: `public/assets/css/pagebuilder_elementor.css.bak_20260625_basic_widget_ui_polish`

- [ ] Confirm the sweep is limited to basic widgets only, with layout/grid deferred.
- [ ] Confirm fresh backups exist for `app.js` and `pagebuilder_elementor.css` before editing.

### Task 2: Restructure Basic Widget Sidebar Markup

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] Wrap `Heading`, `Text Editor`, `Image`, `Button`, `Divider`, and `Spacer` settings blocks in a shared basic-widget wrapper.
- [ ] Split each widget into logical groups such as content/media, sizing/options, and advanced/CSS class.
- [ ] Keep existing behavior and bindings intact while only improving presentation structure.

### Task 3: Add Shared Sidebar Polish Styles

**Files:**
- Modify: `public/assets/css/pagebuilder_elementor.css`

- [ ] Add scoped CSS for the shared basic-widget wrapper.
- [ ] Match the Video-panel rhythm for spacing, font sizes, field heights, toggle layout, media field treatment, and responsive-control rows.
- [ ] Keep styling editor-only and avoid touching frontend renderer box-model output.

### Task 4: Verify Builder Integrity

**Files:**
- Verify: `public/js/pagebuilder_elementor/app.js`
- Verify: `public/assets/css/pagebuilder_elementor.css`

- [ ] Run `node --check public/js/pagebuilder_elementor/app.js`.
- [ ] Run `git diff --check -- public/js/pagebuilder_elementor/app.js public/assets/css/pagebuilder_elementor.css`.
- [ ] Run an HTTP check for `http://laravel-13-phoenix.aruna/pagebuilder-elementor/create`.
- [ ] Review the final diff to ensure the sweep stayed inside basic widget settings.
