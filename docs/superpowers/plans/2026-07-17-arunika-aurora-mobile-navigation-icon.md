# Arunika Aurora Mobile Navigation Icon Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Render the Aurora mobile navigation control as the approved unboxed panel icon while preserving its accessible sidebar interaction.

**Architecture:** The Blade mobile trigger will reuse the sidebar's existing panel SVG. A narrowly scoped Aurora CSS override keeps a 36px touch target while removing only its visual shell. The existing mobile sidebar regression receives direct DOM and CSS expectations.

**Tech Stack:** Laravel Blade, theme CSS, Node.js static regression tests, Playwright CLI.

## Global Constraints

- Work in `D:\Laragon\www\laravel-13-phoenix`; preserve the dirty working tree.
- Back up every existing production/test file before modification.
- Do not remove the semantic button or change desktop/sidebar drawer behavior.
- Validate at 414px by 846px and 1440px by 900px.

---

### Task 1: Apply and verify the Aurora mobile icon treatment

**Files:**
- Modify: `resources/views/themes/arunika_aurora/cms/cms_layout.blade.php:183-185`
- Modify: `public/assets/css/themes/arunika_aurora/arunika_aurora.css:2890-2900` and `:2993-2997`
- Modify: `tests/arunika-aurora-mobile-sidebar-toggle-static.test.mjs`

**Interfaces:**
- Consumes: Existing `toggleSidebar()` function and `.ph-sidebar-toggle-icon` SVG styles.
- Produces: A 36px mobile button with an 18px panel SVG, no visual button shell, and unchanged accessible navigation behavior.

- [ ] **Step 1: Write the failing regression**

Add assertions that the `.ph-mobile-sidebar-trigger` button contains `.ph-sidebar-toggle-icon` with the existing panel paths, and that its Aurora CSS rule includes `width: 36px`, `height: 36px`, `padding: 0`, `border: 0`, `border-radius: 0`, `background: transparent`, and `box-shadow: none`.

- [ ] **Step 2: Verify the regression fails**

Run: `node --test tests\arunika-aurora-mobile-sidebar-toggle-static.test.mjs`

Expected: fail because the mobile trigger still renders Font Awesome bars and has a boxed 40px treatment.

- [ ] **Step 3: Implement the minimal patch**

Replace the mobile trigger content with:

```blade
<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
	<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
	<path d="M8.25 3.25V20.75"></path>
	<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
</svg>
```

Use the Aurora `.ph-mobile-sidebar-trigger` rule to retain a 36px hit target and set:

```css
padding: 0;
border: 0;
border-radius: 0;
background: transparent;
box-shadow: none;
```

- [ ] **Step 4: Verify the regression passes**

Run: `node --test tests\arunika-aurora-mobile-sidebar-toggle-static.test.mjs`

Expected: one passing test with `Arunika Aurora mobile sidebar close-toggle regression passed.`

- [ ] **Step 5: Run focused application and browser checks**

Run: `php artisan test`, `php artisan view:clear`, `php artisan view:cache`, and `git diff --check`.

At `https://laravel-13-phoenix.aruna/manage_article`, select Aurora and verify computed mobile control values at `414x846`, open and close the drawer, then check desktop `1440x900` for an unchanged 32px desktop control and a hidden mobile trigger.
