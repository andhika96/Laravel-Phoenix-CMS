# Theme Manager Mockup Implementation Plan

> **For agentic workers:** Implement inline in the current session. Keep production routes, controllers, database, and active theme untouched.

**Goal:** Deliver a standalone, responsive, interactive Manage Themes mockup containing Arunika V1 and Arunika V2 with real repository-backed screenshots.

**Architecture:** One self-contained HTML prototype owns presentation and local interaction state. Two captured PNG assets provide theme previews. A Node static regression test verifies the required content, controls, state hooks, and asset references.

**Tech Stack:** HTML5, CSS, vanilla JavaScript, Font Awesome 5, Node built-in test runner.

## Global Constraints

- Draw only the settings content area.
- Start with Arunika V2 active.
- Selection remains pending until `Save changes` is pressed.
- Do not change Laravel production code or database state.

---

### Task 1: Static contract and preview assets

**Files:**
- Create: `tests/theme-manager-mockup-static.test.mjs`
- Create: `public/mockups/assets/theme-manager/arunika-v1-theme-preview.png`
- Create: `public/mockups/assets/theme-manager/arunika-v2-theme-preview.png`

- [ ] Write and run the static test before the mockup exists; verify it fails because the target file is missing.
- [ ] Capture the existing Arunika V1 and V2 mockup pages at the same desktop viewport.
- [ ] Confirm both PNG files are readable images with non-zero dimensions.

### Task 2: Interactive mockup

**Files:**
- Create: `public/mockups/theme-manager-interactive-mockup.html`

- [ ] Build the content-only settings surface with two theme cards.
- [ ] Add pending selection, cancel, save, toast, preview modal, Escape handling, and responsive states.
- [ ] Run `node --test tests/theme-manager-mockup-static.test.mjs` and confirm all assertions pass.

### Task 3: Browser verification and design QA

**Files:**
- Create: `design-qa.md`

- [ ] Open the prototype through the local Laravel host and inspect desktop rendering.
- [ ] Verify V1 selection enables actions, Cancel restores V2, Save promotes V1, and preview modal opens/closes.
- [ ] Capture the final implementation screenshot.
- [ ] Compare it with the supplied source reference, record findings, and fix all P0/P1/P2 issues.
- [ ] Run the static test and `git diff --check` again before handoff.
