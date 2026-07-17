# Arunika Concept Theme Full Rename Design

## Goal

Replace the version-based Arunika V1, V2, and V3 identities with concept-based names throughout the active Laravel CMS implementation and live database.

## Approved mapping

| Previous identity | New display name | New internal code and folder |
| --- | --- | --- |
| Arunika V1 Theme | Arunika Mosaic | `arunika_mosaic` |
| Arunika V2 Theme | Arunika Aurora | `arunika_aurora` |
| Arunika V3 Theme | Arunika Canvas | `arunika_canvas` |

## Runtime contract

- Rename Blade theme directories under `resources/views/themes`.
- Rename CSS and JavaScript theme directories and their entry filenames.
- Rename Theme Manager preview assets to concept-based filenames.
- Replace active Blade namespaces, asset URLs, body classes, controller allowlists, metadata, seed data, mockup references, and regression-test references.
- Preserve the visual implementation and behavior of every theme; this is an identity migration, not a redesign.
- Preserve Theme Manager selection, Save/Cancel, Live Preview, active-theme resolution, dark mode, palette behavior, sidebar behavior, and dynamic CMS content.

## Database migration

- Add a new reversible migration that updates the existing `themes` rows and any matching `theme_settings` rows inside one transaction.
- Keep each existing theme row ID unchanged so `theme_settings.theme_id` remains valid.
- Update `theme_settings.theme_code` and `theme_settings.theme_name` for the active theme.
- Do not touch `page_themes`; the live database contains no Arunika CMS identifiers in that table.
- Update seed data to use the concept identities for fresh installations.
- Keep the already-recorded historical migration filename unchanged so Laravel's migration ledger does not treat it as a new migration.

## Safety and rollback

- Back up all affected theme trees, textual references, preview assets, and the live `themes`/`theme_settings` rows before modifying anything.
- The new migration `down()` maps the concept identifiers back to their previous version identifiers.
- Abort the migration if a target concept code belongs to a different row.
- Clear config, view, and application caches after migration.

## Verification

- A focused regression must fail against the version-based implementation and pass after the rename.
- Assert that all three concept folders, entry assets, controller identifiers, preview assets, and database rows exist.
- Assert that active runtime code and tests contain no version-based Arunika identifiers, excluding backups, historical documentation, and the migration ledger filename.
- Run focused Node regressions, Theme Manager feature tests, the complete Laravel suite, Blade compilation, route/config checks, served-asset checks, and an authenticated or internal render check for the active theme.

