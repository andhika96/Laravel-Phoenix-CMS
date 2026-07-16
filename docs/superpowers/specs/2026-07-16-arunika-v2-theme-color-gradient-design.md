# Arunika V2 Theme Color Gradient Design

## Goal

Restore the Arunika V2 theme color control in the CMS header and make the selected color drive the interface accents plus the existing sidebar and header gradients.

## Approved behavior

- Place a palette dropdown immediately to the left of the dark/light mode toggle.
- Show color choices only. Do not include background pattern controls.
- Reuse the existing Arunika V2 `colorMainList`, `color-picker-container`, and `changeMainColor(color)` behavior.
- Continue storing the selected color in `localStorage` under `theme-color`.
- Keep `--ph-theme-primary` as the single color source for buttons, active navigation, icons, hover accents, and related themed elements.
- Derive the sidebar and header gradient tint from `--ph-theme-primary` with CSS `color-mix()`.
- Preserve the current gradient geometry, softness, transparency, and neutral light/dark base surfaces.
- Apply the dynamic gradient in both light and dark mode without changing Arunika V1 or the database.

## UI details

The trigger uses the existing circular `ph-btn-action-icon` header action style and a Font Awesome palette icon. The dropdown is right-aligned, uses Bootstrap's existing dropdown behavior, and contains the existing color swatches in a four-column grid.

## Technical approach

The Blade layout restores only the palette trigger and swatch container. The CSS replaces hard-coded colored gradient stops with `color-mix(in srgb, var(--ph-theme-primary), transparent ...)` values while keeping all existing gradient positions and neutral base colors. No new JavaScript state or backend persistence is introduced.

## Verification

A focused static Node test verifies trigger order, the swatch target, absence of pattern controls, reuse of the existing JavaScript persistence path, and dynamic gradient tokens in both color modes.
