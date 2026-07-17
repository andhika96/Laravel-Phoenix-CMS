# Arunika V3 Sidebar Logo Offset Design

## Goal

Align the sidebar logo area vertically with the Arunika V3 right canvas after the right canvas received a `15px` top margin.

## Approved contract

- Add `margin-top: 15px` to `.ph-theme-arunika-v3 .ph-sidebar-logo-container`.
- Move the whole logo area structurally so the menu begins below the aligned header boundary.
- Preserve the `52px` header height, vertical centering, logo dimensions, expanded/collapsed widths, sidebar profile, and right-canvas geometry.
- Do not use transforms or one-off child offsets.

## Verification

- Add a focused static regression that requires the logo container to keep its V3 header height and use the same `15px` top offset as `.ph-layout-right`.
- Verify the test fails before the CSS change and passes afterward.
- Run Arunika V3 regressions, Laravel tests, Blade cache, served CSS validation, and `git diff --check`.

