# Responsive Hero Banner Prototype Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan. For this session the primary agent executes inline because the user requested direct implementation and project policy forbids unrequested delegation.

**Goal:** Extend the isolated Page Builder v2.3 hero prototype with content ordering, one-to-three configurable buttons, link/video/image actions, responsive media inheritance, and an accessible shared modal.

**Architecture:** Keep React local state in `App.jsx`, retain the existing positioning helpers, and add one small pure `hero-model.js` module for behavior that can be checked without a browser. Reuse the prototype's existing assets and native browser controls; do not add dependencies or touch production Page Builder files.

**Tech Stack:** React 18, Vite, native CSS, Node `assert` self-check.

**Execution boundary:** Work only in the prototype and these plan/spec documents. Create timestamped backups before changing existing files. Do not commit, stage, save Page Builder state, or modify production v2.3 source.

---

### Task 1: Back up the current prototype and establish failing model checks

**Files:**
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/scripts/self-check.mjs`
- Test: `mockups/pagebuilder-v23-responsive-hero-prototype/scripts/self-check.mjs`

**Step 1: Create timestamped backups**

Back up `src/App.jsx`, `src/styles.css`, `src/positioning.js`, `scripts/self-check.mjs`, and `design-qa.md` with the suffix `.bak_YYYYMMDD_HHMMSS_responsive_hero_actions`.

**Step 2: Write the failing checks**

Import the not-yet-created helpers from `../src/hero-model.js` and assert:

- `moveItem()` moves Title and Subtitle without duplicates.
- `normalizeButtons()` always returns one to three buttons.
- `resolveResponsive()` follows Mobile to Tablet to Desktop fallback.
- `isSafeMediaUrl()` accepts HTTP(S) and local absolute paths but rejects protocol-relative and unsafe schemes.
- `resolveVideoMedia()` normalizes YouTube, Vimeo, Dailymotion, and self-hosted sources.

**Step 3: Run the check to verify RED**

Run: `npm.cmd run check`

Expected: FAIL with `ERR_MODULE_NOT_FOUND` for `src/hero-model.js`.

### Task 2: Add the minimum pure hero behavior model

**Files:**
- Create: `mockups/pagebuilder-v23-responsive-hero-prototype/src/hero-model.js`
- Test: `mockups/pagebuilder-v23-responsive-hero-prototype/scripts/self-check.mjs`

**Step 1: Implement the pure helpers**

Export:

```js
moveItem(order, id, direction)
normalizeButtons(buttons, max = 3)
resolveResponsive(values, device)
isSafeMediaUrl(url)
resolveVideoMedia(source, url)
```

Use plain arrays, `URL`, and regular expressions only. Return `null` for unsupported popup media.

**Step 2: Run the check to verify GREEN**

Run: `npm.cmd run check`

Expected: PASS.

### Task 3: Wire content order and the button repeater into the prototype

**Files:**
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/src/App.jsx`
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/src/styles.css`

**Step 1: Add grouped content ordering**

Keep the three fixed blocks `title`, `subtitle`, and `buttons`. Show Move Up/Down and visibility controls only where relevant. Render the grouped canvas using the resulting order.

**Step 2: Add a compact one-to-three button editor**

Support add, duplicate, select, and remove while preserving at least one button. Keep Button Group as one positioning target in Independent mode.

**Step 3: Add conditional action controls**

Support:

- Link: URL, target, nofollow.
- Video Popup: YouTube, Vimeo, Dailymotion, self-hosted URL.
- Image Popup: simulated Media Library selection or external URL plus alt text.

### Task 4: Add responsive button layout and media inheritance

**Files:**
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/src/App.jsx`
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/src/styles.css`

**Step 1: Implement device inheritance**

Desktop is always the base. Tablet and Mobile may create or reset overrides. Display `Custom override` or the inherited device in the panel.

**Step 2: Add responsive Button Group layout controls**

Expose Direction, Alignment, Gap, and Wrap for the active device. Editing an inherited Tablet/Mobile value creates a local override.

**Step 3: Add responsive hero media controls**

Expose source type, media URL or simulated picker, alt text, object fit, and object position. Keep the current local MG desktop/mobile assets as picker choices.

### Task 5: Add one accessible shared popup

**Files:**
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/src/App.jsx`
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/src/styles.css`

**Step 1: Render button actions safely**

Do not navigate an empty link. Add `noopener noreferrer` for new-window links. Open supported image/video actions through one modal state.

**Step 2: Implement modal interaction**

Support close button, Escape, backdrop close, body scroll lock, focus into the modal, focus return, and media teardown on close. Respect `prefers-reduced-motion` in CSS.

### Task 6: Build and run browser/design QA

**Files:**
- Modify: `mockups/pagebuilder-v23-responsive-hero-prototype/design-qa.md`
- Optional assets: `mockups/pagebuilder-v23-responsive-hero-prototype/public/assets/qa-*.png`

**Step 1: Run static verification**

Run:

```powershell
npm.cmd run check
npm.cmd run build
```

Expected: both exit 0.

**Step 2: Run browser QA at the local prototype URL**

Verify Content Order, Grouped/Independent positioning, add/duplicate/remove/limit, each action type, modal dismissal methods, responsive inheritance/reset, simulated media picker, viewport overflow at 1440/1024/720, and console errors.

**Step 3: Patch only verified visual or interaction defects**

Repeat the relevant check/build/browser path after every patch.

**Step 4: Record the final QA evidence**

Update `design-qa.md` with the tested URL, viewports, interactions, findings, fixes, remaining prototype-only boundary, and end with exactly:

```text
final result: passed
```
