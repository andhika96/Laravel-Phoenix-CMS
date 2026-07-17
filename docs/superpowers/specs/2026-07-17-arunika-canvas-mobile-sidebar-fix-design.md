# Arunika Canvas Mobile Sidebar Fix Design

Date: 2026-07-17

## Goal

Make the Arunika Canvas sidebar behave as an intentional mobile drawer without changing the approved desktop layout.

At viewport widths of `768px` or less:

- the sidebar is closed when the page first renders;
- changing from a desktop viewport to a mobile viewport also closes it;
- the header hamburger opens it;
- an accessible control inside the drawer closes it;
- the light-mode drawer uses a solid white surface;
- dark mode uses an opaque dark surface rather than forcing a white drawer.

## Confirmed Root Causes

1. The Canvas-specific selector for `.ph-sidebar` and `.ph-sidebar.ph-expanded` appears after the shared responsive rules and sets `background: transparent`, so the mobile drawer inherits the Canvas shell beneath it.
2. Canvas places `#sidebar-toggle` inside `.ph-header-nav-control`, while the final mobile rules hide that header control. The shared mobile selector that reveals `.ph-sidebar.ph-expanded .ph-sidebar-toggle` cannot match because the button is not a descendant of the sidebar.
3. Initial Blade logic prevents expansion when the page is loaded at mobile width, but there is no breakpoint transition handler. A sidebar expanded on desktop remains expanded when DevTools or a browser resize crosses into mobile width.

## Selected Approach

Apply a focused Arunika Canvas fix.

### Markup

Add a dedicated mobile close button inside the sidebar logo/header area. It will:

- use a unique selector and no duplicate DOM ID;
- call the existing `toggleSidebar()` behavior;
- be hidden on desktop and while the mobile drawer is closed;
- expose an explicit `Close navigation` accessible label;
- reuse the existing sidebar icon language so it looks native to Canvas.

The approved desktop collapse button remains in `.ph-header-nav-control` before search.

### Styling

Add final Canvas-scoped mobile rules after the Canvas theme overrides:

- light mode sidebar background: `#ffffff`;
- dark mode sidebar background: the opaque Canvas content surface `#202120`;
- no mobile backdrop blur or transparency;
- show the new close control only while the drawer is expanded;
- retain the current `256px` drawer width and existing shadow/transform animation.

### State Synchronization

Track whether the viewport is above or below the `768px` breakpoint.

- On the first mobile render, keep the existing closed state.
- When crossing from desktop to mobile, remove `ph-expanded` and update ARIA state.
- Mobile open/close actions do not overwrite the saved desktop sidebar preference.
- When crossing back to desktop, restore the saved desktop expanded/collapsed preference.
- Do not repeatedly close the drawer for ordinary resizes that remain within mobile width.
- Keep the existing menu-link auto-close behavior.
- Preserve the desktop sidebar preference stored in `localStorage`.

## Regression Coverage

Add a focused Node static regression test for Canvas that fails before the production patch and protects:

1. the dedicated in-drawer mobile close control;
2. the final mobile light/dark opaque surfaces;
3. removal of blur/transparency in mobile drawer state;
4. breakpoint-crossing logic that closes an expanded desktop sidebar;
5. continued presence of the desktop header collapse control.

Run the focused Canvas regression first, then the relevant Arunika tests and the existing Laravel suite.

## Browser QA

Target flow:

`/manage_article` desktop expanded -> switch to `414 x 846` -> drawer is closed -> tap hamburger -> solid drawer opens -> tap in-drawer close control -> drawer closes.

Also verify:

- direct page load at `414 x 846` starts closed;
- light mode uses solid white;
- dark mode uses an opaque dark drawer;
- no relevant console warning/error;
- desktop expanded/collapsed behavior remains unchanged.

The in-app Browser plugin failed to initialize because of a Windows sandbox setup error. Playwright terminal automation may be used as the recorded fallback for final runtime QA.

## Files Expected to Change

- `resources/views/themes/arunika_canvas/cms/cms_layout.blade.php`
- `public/assets/css/themes/arunika_canvas/arunika_canvas.css`
- `public/assets/js/themes/arunika_canvas/arunika_canvas.js`
- a new focused test under `tests/`

Before editing the three production files, create a timestamped backup containing their pre-change versions.

## Non-Goals

- no Aurora or Mosaic changes;
- no redesign of the desktop sidebar, header, profile, menu spacing, or drawer width;
- no change to Manage Article table behavior;
- no new dependency;
- no commit, merge, or push of the production patch unless separately requested.
