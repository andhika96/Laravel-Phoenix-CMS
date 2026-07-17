# Theme Manager Production Design

## Goal

Add a real Awesome Admin page for choosing the active CMS theme. The content area must preserve the approved interactive mockup while inheriting the active CMS shell, typography, colors, breadcrumbs, buttons, and responsive behavior.

## Scope

- Add `/awesome_admin/themes` and its save endpoint.
- Add `Manage Themes` to the Awesome Admin menu grid.
- List only the installed Arunika V1 and Arunika V2 records from the existing `themes` table.
- Read and update the active theme through the existing `theme_settings` table.
- Keep the current preview PNGs as temporary production assets; replacing the files later must not require a layout change.
- Preserve pending selection until Save, support Cancel, Live Preview, keyboard selection, loading, success, and validation-error states.
- Remove `Browse installed themes` from the standalone mockup.

## Explicit Exclusions

- No new database table or migration.
- No theme upload, installation, deletion, marketplace, or preview-image upload UI.
- No change to the existing Manage Appearance page for login, signup, and recovery page themes.
- No change to unrelated sidebar/menu configuration records.

## Architecture

- A focused Awesome Admin themes controller queries `themes`, resolves `theme_settings`, validates allowed Arunika theme codes, and updates the existing active-theme row in a transaction.
- A dedicated Blade view extends `themes.` plus `custom_theme('cms')`, so the page always uses the active Arunika CMS shell.
- A dedicated Vue 3 + Axios script owns pending/active state, Save/Cancel behavior, Bootstrap preview modal, and toast feedback.
- Preview image paths remain stable under `public/assets/images/themes/previews/` so user-supplied images can replace them later.

## Success Criteria

- An administrator can open the new page from the Awesome Admin grid.
- Arunika V1 and V2 render with their current preview images.
- The current database theme is marked Active.
- Selecting a different card does not write until Save.
- Cancel restores the active card.
- Save updates `theme_settings` without creating a table and reloads into the selected CMS shell.
- The page remains usable at desktop and mobile widths and has no actionable P0, P1, or P2 visual issues.

