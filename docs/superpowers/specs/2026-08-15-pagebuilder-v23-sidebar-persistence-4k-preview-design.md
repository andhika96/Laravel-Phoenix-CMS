# Page Builder v2.3 Sidebar Persistence and 4K Preview Design

## Goal

Persist the left editor sidebar state per browser and extend the desktop canvas preview from the current presets through a true 3840px (4K UHD) viewport.

## Sidebar behavior

- Store the collapsed state in browser `localStorage`; page Save is not required.
- Restore the state when Page Builder v2.3 loads. Missing, malformed, or unavailable storage falls back to the sidebar being open.
- The collapse button stores `collapsed`; the floating expand button stores `open`.
- Preview mode hides the sidebar temporarily without overwriting the stored preference. Returning to Editor restores the stored state.
- Context-menu `Edit` opens the sidebar and stores `open` because the requested settings must be visible.
- Context-menu actions that do not require settings leave the preference unchanged.
- Existing toolbox actions that explicitly reveal the panel continue to store `open`.

## Desktop preview widths

- Preserve `1140`, `1180`, and `1320`.
- Add `1440`, `1600`, `1920`, `2560`, and `3840`.
- Use the selected value as the actual `.webpage-frame` width, not only `max-width`, so `1320` and larger presets are real virtual viewports.
- Keep the existing horizontal canvas scrolling and manual zoom behavior; no auto-fit system or new dependency is added.
- Tablet remains `768px`; Mobile remains `390px`.

## Verification

- Regression coverage for safe read/write, reload restoration, Preview preservation, context-menu Edit reveal, and the complete width allowlist.
- Runtime QA remains read-only and must not use Save/Reset.
- Run affected Node tests, JavaScript syntax validation, build, `git diff --check`, and incremental Graphify update.

