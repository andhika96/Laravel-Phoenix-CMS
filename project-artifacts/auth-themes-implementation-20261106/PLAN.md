# Phoenix Authentication Themes Implementation Plan

> Execute inline, task by task. User explicitly requested direct implementation after the design review.

**Goal:** Eight working authentication theme choices, orderly settings, real immediate image upload/preview/removal and responsive auth pages.
**Architecture:** Keep existing Laravel/Blade/Vue/Bootstrap foundations. Store theme-specific options in one JSON column, use a small auth theme catalog for supported settings/defaults/validation, and share new auth form/panel partials. Immediate image endpoints own stored image references; regular settings saves cannot overwrite them.
**Tech Stack:** Laravel 13, Blade, Vue 3, Axios, Bootstrap, Font Awesome, Coloris, existing Site Config fonts.
**Spec:** SPEC.md.

## Global constraints

Existing themes/settings/account flows must remain available. Current checkout is reused; work is local and uncommitted. Preserve unrelated artifacts. Back up every pre-existing file before editing. Retain plans and logs. No Graphify update or external/private-source processing.

## Review focus

Unauthorized upload/deletion; forged image paths; stale settings overwriting immediate uploads; failed replacements deleting a valid image; multi-image/concurrent requests losing references; unsafe text/CSS; responsive forms and closed signup; old themes and settings surviving installation.

## Tasks

- [x] 1. Back up source/auth DB rows. Add failing catalog/validation/storage tests. Implement catalog, JSON migration and theme registration; apply only the new migration.
- [x] 2. Add failing upload/storage tests. Implement authorized immediate upload/removal with validation, locked updates, safe replacement and controlled cleanup. Preserve image references during normal settings saves.
- [x] 3. Implement Appearance tabs/gallery/grouped controls, genuine upload progress, Bootstrap image preview, error handling and persistent theme drafts. Reuse existing Vue app and Coloris. Verify controller/API tests and JS syntax.
- [x] 4. Implement four new auth theme families through shared Blade form/panel partials and scoped CSS. Preserve existing auth IDs/field names/requests. Add password visibility and preserve custom button labels. Make empty legacy image areas gray.
- [x] 5. Verify all 32 theme/page combinations, responsive sizes, upload/preview/remove, settings save/load, authentication and old-theme regressions. Use transaction/storage fakes for mutation tests and restore any local browser-test settings. Generate final theme thumbnails as source assets.
- [x] 6. Review the connected change, fix consequential findings, run final checks, and report exact runtime/static limits and recovery paths.

Final scope includes the user's later corrections: original full-image cards, full-width five-column gallery on large screens, a smaller 25px check marker, compact CMS typography, and removable built-in images adapted from Dribbble references. See REPORT.md for actual test boundaries and the MyISAM fixture recovery.

Tests reside under this artifact directory. Do not run any broad test that refreshes/drops the active database. Do not stage/commit/push. File-level and DB recovery snapshots remain available after delivery.
