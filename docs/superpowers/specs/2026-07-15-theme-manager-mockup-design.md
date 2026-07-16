# Theme Manager Interactive Mockup Design

## Goal

Create a standalone interactive mockup for managing the CMS theme without changing the production Appearance page, controllers, routes, database, or active theme.

## Visual direction

- Reuse the spacious two-column setting-row composition from the supplied reference image.
- Render only the settings content area; do not reproduce the Awesome Admin sidebar or page shell.
- Match the existing Arunika/Awesome Admin visual language with a white surface, restrained lavender accents, compact typography, subtle borders, and Font Awesome icons.
- Use real screenshots captured from the existing Arunika V1 and Arunika V2 mockups.

## Interaction contract

- Arunika V2 starts as the active and selected theme.
- Clicking another theme creates a pending selection but does not activate it immediately.
- `Cancel` restores the pending selection to the active theme.
- `Save changes` promotes the pending theme to active and shows a success toast.
- `Live preview` opens a large modal for the corresponding theme screenshot.
- Escape and the close controls dismiss the preview modal.
- Keyboard focus, selected state, and disabled button states remain visible.

## Responsive behavior

- Desktop uses a label/description column beside a two-card theme grid.
- Tablet and mobile stack the explanatory copy above the cards.
- The action footer remains readable without horizontal scrolling.

## Scope boundary

The deliverable is a prototype under `public/mockups`. It does not persist data to Laravel and does not modify production state.
