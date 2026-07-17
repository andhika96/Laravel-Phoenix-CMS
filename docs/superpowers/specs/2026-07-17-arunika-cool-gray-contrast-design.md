# Arunika Cool Gray Contrast Design

## Goal

Keep the reference-matched cool gray surface `#C7CCD8` while restoring clear contrast for buttons, active states, icons, borders, and hover surfaces across Arunika Mosaic, Aurora, and Canvas.

## Approved color roles

- Stored palette value and surface tint: `#C7CCD8`.
- Interactive color for buttons, active states, icons, and borders: `#667085`.
- Light-mode hover and selected surface: `#E4E7EC`.
- Dark-mode hover surface: `rgba(199, 204, 216, 0.16)`.

## Runtime contract

- `localStorage['theme-color']` continues to store the selected palette value `#C7CCD8`.
- Selecting cool gray maps `--ph-theme-primary` to `#667085` and `--ph-theme-surface-tint` to `#C7CCD8`.
- Other palette selections map both variables to the selected color, preserving their current behavior.
- The document receives `data-ph-theme-color="cool-gray"` only while cool gray is selected.
- Surface gradients and decorative background tint use `--ph-theme-surface-tint`.
- Interactive controls continue using `--ph-theme-primary`, which is now contrast-safe for cool gray.
- Hover and selected surfaces use `--ph-theme-hover-surface` when the cool-gray state is active, with separate light and dark values.
- The early inline bootstrap applies the same mapping before the theme JavaScript loads to prevent a low-contrast first paint.

## Verification

- Add a focused static regression covering JavaScript mapping, early bootstrap mapping, surface gradients, light hover, and dark hover for all three themes.
- Confirm the existing palette regression and concept-rename regression remain green.
- Run JavaScript syntax checks, Laravel tests, Blade compilation, runtime asset checks, and `git diff --check`.
