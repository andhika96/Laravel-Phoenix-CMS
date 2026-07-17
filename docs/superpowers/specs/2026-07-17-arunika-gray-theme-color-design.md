# Arunika Gray Theme Color Design

## Goal

Add one cool gray option sampled from the supplied dashboard reference to the existing Theme Color palette shared by all Arunika themes.

## Approved color

- Hex value: `#C7CCD8`
- Placement: eighth and last palette item
- Scope: Arunika Mosaic, Arunika Aurora, and Arunika Canvas

## Implementation contract

- Append `#C7CCD8` to `colorMainList` in each Arunika theme JavaScript entrypoint.
- Keep the existing color order unchanged.
- Keep `changeMainColor(color)`, `--ph-theme-primary`, and `localStorage['theme-color']` unchanged.
- Preserve the current gradient geometry, dark-mode behavior, active-menu styling, hover behavior, and dynamic CMS content.
- Do not add new backend state, database columns, patterns, or custom gray-only CSS.
- With eight colors and the existing Bootstrap `col-3` items, the palette renders as a balanced four-by-two grid.

## Verification

- Add a focused static regression that requires `#C7CCD8` in all three `colorMainList` declarations.
- Confirm each list contains the gray value exactly once and as its final item.
- Run the focused regression, relevant Arunika palette regressions, Blade compilation, and `git diff --check`.
