# Arunika V3 Header Admin Action Design

## Goal

Restore a proportional Awesome Admin shortcut at the far-right edge of the Arunika V3 header while temporarily hiding the notification bell.

## Approved contract

- Render the shortcut only when `checkIsAdmin()` is true.
- Link to `url('awesome_admin')` and use the Font Awesome `fa-user-secret` glyph.
- Keep the notification component mounted inside a hidden V3 wrapper so it can be restored without rebuilding its markup or JavaScript.
- Place the hidden notification wrapper before the Awesome Admin shortcut, making the admin shortcut the final header action.
- Use a `34px` square action button and a `16px` glyph for both the Awesome Admin shortcut and notification bell.
- Preserve the sidebar profile, search, collapse control, header height, and all shared notification behavior.

## Verification

- Add a focused static regression covering permission gating, action order, hidden notification state, route, glyph, and proportional sizes.
- Verify RED before changing Blade/CSS and GREEN afterward.
- Run the relevant Arunika V3 regressions, Laravel suite, Blade cache, served asset checks, and `git diff --check`.

