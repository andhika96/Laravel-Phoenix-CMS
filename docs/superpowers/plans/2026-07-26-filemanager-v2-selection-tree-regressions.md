# File Manager V2 Selection and Tree Regression Fixes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove the leaked event binding, make the card checklist an explicit selection control, and animate a folder subtree as one collapsing unit.

**Architecture:** Keep the toolbar binding solely on `AssetCard` in `App.vue`. Make `AssetCard` own an accessible checklist button that emits the existing `select` event. Replace the sidebar's flattened visible-row transition with a recursive `FolderTreeNode` component so each folder owns a collapsible descendant wrapper.

**Tech Stack:** Vue 3 `<script setup>`, Bootstrap Icons, Vite, Laravel feature tests.

## Global Constraints

- Modify only File Manager V2 source and its focused feature tests.
- Preserve file/folder navigation behavior, the existing V2 isolation boundary, and unrelated dirty worktree changes.
- Build through `npm.cmd run build:filemanager-v2` after source verification.

---

### Task 1: Guard the selection toolbar and card checklist

**Files:**
- Modify: `resources/js/filemanager_v2/App.vue:627-657`
- Modify: `resources/js/filemanager_v2/components/AssetCard.vue:13-40`
- Modify: `tests/Feature/FileManagerV2/FileManagerV2AssetCardTest.php`

**Interfaces:**
- Consumes: existing `select` event `(asset, MouseEvent)` from `AssetCard`.
- Produces: toolbar has no literal event binding and checklist buttons emit the same selection event without bubbling to the card.

- [ ] **Step 1: Write a failing regression test**

Assert that the toolbar does not contain the leaked `@open-folder` string, and that `AssetCard` contains an `asset-check` button with `@click.stop="emit('select', asset, $event)"`.

- [ ] **Step 2: Run the focused test to verify it fails**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2AssetCardTest.php`

- [ ] **Step 3: Apply the minimal template and CSS change**

Delete the stray toolbar text, replace the checklist `div` with a semantic button, and preserve the existing selected visual state.

- [ ] **Step 4: Run the focused test to verify it passes**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2AssetCardTest.php`

### Task 2: Animate folder descendants as a subtree

**Files:**
- Create: `resources/js/filemanager_v2/components/FolderTreeNode.vue`
- Modify: `resources/js/filemanager_v2/components/StorageSidebar.vue:1-137`
- Modify: `resources/js/filemanager_v2/styles.css:59-89`
- Modify: `tests/Feature/FileManagerV2/FileManagerV2FolderPresentationTest.php`

**Interfaces:**
- Consumes: normalized flat `folders` entries with `id`, `path`, `depth`, `name`, `icon`, and `count`.
- Produces: nested folder nodes that emit `change-folder` and keep collapse state local to each rendered node.

- [ ] **Step 1: Write a failing regression test**

Assert that the sidebar imports `FolderTreeNode`, supplies a nested tree, and the node uses a `Transition` named `folder-children` around its child wrapper.

- [ ] **Step 2: Run the focused test to verify it fails**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2FolderPresentationTest.php`

- [ ] **Step 3: Apply the recursive tree component and grouped CSS transition**

Build the tree from flat paths, render nodes recursively, and transition the descendants wrapper with a grid-row and opacity animation.

- [ ] **Step 4: Run the focused test to verify it passes**

Run: `php artisan test tests/Feature/FileManagerV2/FileManagerV2FolderPresentationTest.php`

### Task 3: Verify integration

- [ ] **Step 1: Run the V2 feature suite**

Run: `php artisan test tests/Feature/FileManagerV2`

- [ ] **Step 2: Build runtime assets**

Run: `npm.cmd run build:filemanager-v2`

- [ ] **Step 3: Update Graphify incrementally**

Run: `graphify . --update --no-viz --code-only; graphify cluster-only .`
