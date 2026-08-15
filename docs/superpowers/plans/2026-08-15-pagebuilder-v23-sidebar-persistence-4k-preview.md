# Page Builder v2.3 Sidebar Persistence and 4K Preview Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Persist the v2.3 left sidebar per browser and provide exact desktop preview widths through 3840px.

**Architecture:** Keep the behavior inside the existing v2.3 editor shell. Use guarded native `localStorage`, a single reactive state watcher, the existing context-menu reveal path, and the existing horizontally scrollable stage.

**Tech Stack:** Vue 3 global runtime, browser `localStorage`, Node test runner, Vite, Graphify.

## Global Constraints

- Do not modify Page Builder v2.0.
- Do not add dependencies or database persistence.
- Preview mode must not overwrite the sidebar preference.
- Do not run editor Save/Reset during QA.
- Preserve existing width presets and add `1440`, `1600`, `1920`, `2560`, and `3840`.

---

### Task 1: Sidebar preference and width contract

**Files:**
- Modify: `tests/pagebuilder-editor-v23-production-static.test.mjs`
- Modify: `public/js/pagebuilder_elementor_v23/app.js`

**Interfaces:**
- Consumes: browser `window.localStorage`, `leftCollapsed`, `previewMode`, `selectNode(..., { revealPanel: true })`.
- Produces: a guarded sidebar preference key and exact desktop preview width style.

- [ ] **Step 1: Write failing regression assertions**

Add assertions for the storage key/read/write contract, Preview preserving `leftCollapsed`, context Edit using `revealPanel`, the eight desktop widths, and `previewCanvasStyle()` assigning `style.width`.

- [ ] **Step 2: Run the focused test and verify RED**

Run: `node --test tests/pagebuilder-editor-v23-production-static.test.mjs`

Expected: failure because browser persistence, the larger presets, and actual width assignment are absent.

- [ ] **Step 3: Implement the minimum production change**

Add guarded read/write helpers, initialize `leftCollapsed` from storage, watch it for changes, extend `desktopPreviewWidths`, normalize against that list, and assign the selected desktop value to `style.width`.

- [ ] **Step 4: Run the focused test and verify GREEN**

Run: `node --test tests/pagebuilder-editor-v23-production-static.test.mjs`

Expected: all tests pass.

### Task 2: Integrated verification

**Files:**
- Verify: `public/js/pagebuilder_elementor_v23/app.js`
- Verify: `public/assets/css/pagebuilder_elementor_v23.css`

**Interfaces:**
- Consumes: implemented editor state and preview width menu.
- Produces: fresh runtime and build evidence.

- [ ] **Step 1: Run neighboring tests**

Run the v2.3 production shell, properties-toolbar, functional-parity, and context-menu tests.

- [ ] **Step 2: Run syntax/build checks**

Run `node --check`, `npm.cmd run build`, and `git diff --check`.

- [ ] **Step 3: Run read-only browser QA**

Verify collapse → reload, expand → reload, Preview round trip, context-menu Edit reveal, and 1320/1920/3840 computed frame widths without Save/Reset.

- [ ] **Step 4: Update Graphify incrementally**

Run `graphify update .` and report any nonfatal extraction warnings separately.

