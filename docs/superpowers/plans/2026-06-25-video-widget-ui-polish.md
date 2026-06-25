# Video Widget UI Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the `pagebuilder_elementor` Video widget settings panel feel visually tidy and much closer to Elementor's layout rhythm without changing other widgets yet.

**Architecture:** Keep the existing Video setting logic intact, but reorganize the Video-only markup into clearer local groups and add scoped CSS for spacing, typography, toggle rows, helper notes, and media picker rhythm. Avoid global panel regressions by targeting the Video widget section with dedicated wrapper classes.

**Tech Stack:** Vue template strings inside `public/js/pagebuilder_elementor/app.js`, scoped panel CSS in `public/assets/css/pagebuilder_elementor.css`, runtime verification via Node syntax check and served builder page inspection.

---

### Task 1: Prepare isolated workspace and safe backups

**Files:**
- Modify: `.gitignore`
- Create: `.gitignore.bak_20260625_video_ui_worktree_setup`
- Create: `public/js/pagebuilder_elementor/app.js.bak_20260625_video_ui_polish`
- Create: `public/assets/css/pagebuilder_elementor.css.bak_20260625_video_ui_polish`

- [ ] **Step 1: Ensure local worktrees stay ignored**

Add this line to `.gitignore`:

```gitignore
/.worktrees
```

- [ ] **Step 2: Create file backups before editing builder sources**

Run:

```powershell
Copy-Item -LiteralPath '.gitignore' -Destination '.gitignore.bak_20260625_video_ui_worktree_setup' -Force
Copy-Item -LiteralPath 'public/js/pagebuilder_elementor/app.js' -Destination 'public/js/pagebuilder_elementor/app.js.bak_20260625_video_ui_polish' -Force
Copy-Item -LiteralPath 'public/assets/css/pagebuilder_elementor.css' -Destination 'public/assets/css/pagebuilder_elementor.css.bak_20260625_video_ui_polish' -Force
```

Expected: backup files exist alongside the originals.

### Task 2: Reorganize the Video widget markup for local grouping

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Add a Video-only wrapper around the widget settings**

Introduce a wrapper like:

```html
<template v-if="selectedType==='video'">
  <div class="pb-video-settings">
    ...
  </div>
</template>
```

- [ ] **Step 2: Split the Video fields into logical local groups**

Organize the existing fields into groups such as:

```html
<div class="pb-video-settings__group pb-video-settings__group--basic">...</div>
<div class="pb-video-settings__group pb-video-settings__group--options">...</div>
<div class="pb-video-settings__group pb-video-settings__group--extras">...</div>
<div class="pb-video-settings__group pb-video-settings__group--advanced">...</div>
```

Use the current field order, but group:
- `Source`, `External URL`, `Link`, `Choose Video File`
- `Start Time`, `End Time`, `Aspect Ratio`
- `Video Options`
- source-specific fields like `Suggested Videos`, `Controls Color`, `Poster`, `Image Overlay`
- `CSS Class`

- [ ] **Step 3: Move helper notes closer to their related toggles**

Replace the single block-level autoplay note with row-local notes after relevant options, for example:

```html
<div class="pb-video-settings__toggle-note" v-if="option.key === 'autoplay'">
  Autoplay can still be affected by browser policy, especially when audio is enabled.
</div>
```

Do the same for the privacy note using the existing Video option context.

### Task 3: Add scoped CSS to make the Video panel feel Elementor-like

**Files:**
- Modify: `public/assets/css/pagebuilder_elementor.css`

- [ ] **Step 1: Add scoped layout rhythm rules for `.pb-video-settings`**

Add CSS rules for:

```css
.pb-panel.left .pb-video-settings { ... }
.pb-panel.left .pb-video-settings .pb-form-group { ... }
.pb-panel.left .pb-video-settings .pb-form-label { ... }
.pb-panel.left .pb-video-settings .pb-form-note { ... }
```

Target:
- larger and more consistent vertical spacing
- calmer labels
- slightly more refined note spacing

- [ ] **Step 2: Add group, divider, and section title treatment**

Add group rules such as:

```css
.pb-panel.left .pb-video-settings__group { ... }
.pb-panel.left .pb-video-settings__section-title { ... }
```

Use thin dividers and spacing to mimic Elementor's clean stacking without copying its dark theme.

- [ ] **Step 3: Refine Video Options row alignment**

Add row-specific rules such as:

```css
.pb-panel.left .pb-video-settings__toggle-row { ... }
.pb-panel.left .pb-video-settings__toggle-note { ... }
.pb-panel.left .pb-video-settings .pb-toggle-state { ... }
```

Target:
- stable row height
- better left/right balance
- more restrained status badge treatment

- [ ] **Step 4: Smooth the media picker rhythm**

Add Video-scoped styling for media cards so `Choose Video File`, `Poster`, and `Overlay` feel aligned with the cleaner form rhythm.

### Task 4: Verify syntax and served builder state

**Files:**
- Verify: `public/js/pagebuilder_elementor/app.js`
- Verify: `public/assets/css/pagebuilder_elementor.css`

- [ ] **Step 1: Run JS syntax verification**

Run:

```powershell
node --check public/js/pagebuilder_elementor/app.js
```

Expected: exit code `0`.

- [ ] **Step 2: Run whitespace/diff verification**

Run:

```powershell
git diff --check -- public/js/pagebuilder_elementor/app.js public/assets/css/pagebuilder_elementor.css
```

Expected: no diff-formatting errors.

- [ ] **Step 3: Verify the builder page serves**

Run:

```powershell
Invoke-WebRequest -UseBasicParsing 'http://laravel-13-phoenix.aruna/pagebuilder-elementor/create' | Select-Object -ExpandProperty StatusCode
```

Expected: `200`.

- [ ] **Step 4: Inspect the served builder UI for the Video panel**

Open the builder and confirm:
- Video fields no longer feel cramped
- labels, notes, and inputs share one visual rhythm
- Video Options scan cleanly
- helper notes sit near the relevant settings
- no obvious regressions in the Video panel layout
