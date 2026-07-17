# Arunika V3 Sidebar Logo Offset Design

## Goal

Align the sidebar logo area vertically with the Arunika V3 right canvas after the right canvas received a `15px` top margin.

## Approved contract

- Add `margin-top: 15px` to `.ph-theme-arunika-v3 .ph-sidebar-logo-container`.
- Move the whole logo area structurally so the menu begins below the aligned header boundary.
- Make the logo container the positioning context and center the collapsed initial at `50% / 50%` inside that container, so it inherits the same top offset as the expanded logo.
- Preserve the `52px` header height, vertical centering, logo dimensions, expanded/collapsed widths, sidebar profile, and right-canvas geometry.
- Do not introduce a hard-coded child pixel offset; positioning must remain tied to the logo container dimensions.

## Verification

- Add a focused static regression that requires the logo container to keep its V3 header height, use the same `15px` top offset as `.ph-layout-right`, and own the collapsed initial's centering context.
- Verify the test fails before the CSS change and passes afterward.
- Run Arunika V3 regressions, Laravel tests, Blade cache, served CSS validation, and `git diff --check`.
