# Unified preview background — 16 October 2026

Fixed the visible image/brightness seam when Background follows container is enabled. The stage and hero previously painted the same forest with separate image positions and different overlays. The fullscreen CSS now paints one shared forest/overlay on the preview stage and makes the hero background transparent. Header/container background configuration remains owned by the original renderer.

Only public/assets/css/awesome-admin-header-navigation-fullscreen.css changed in this task. Backup hash was verified; exact backup path is in backups/manifest.json.

Context read: E:\AI\Memories\20261015-171349-phoenix-header-navigation-revamp-handoff.md. Graphify query: updateHeaderClasses applyColorsAndSizes. Current base CSS, fullscreen CSS and updateHeaderClasses source were checked. Graphify was not updated.

16 authenticated Chrome checks passed: shared background and transparent hero; container-only/full-width header backgrounds in Desktop, Tablet and Mobile; transparent header; scrolled state; Reset; unchanged saved configuration after reload. Desktop screenshot was visually inspected and no horizontal seam remains. See qa/result.json and qa/container-desktop.png. git diff --check passed.

No database settings were saved. Firefox/Safari and backend tests were not rerun for this CSS-only change.
