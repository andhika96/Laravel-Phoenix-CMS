# Arunika V3 Continuous Gradient Shell Design

## Goal

Make the Arunika V2-derived gradient remain visible while presenting the V3 sidebar and outer shell as one continuous surface, without a border or shadow seam between the sidebar and the rounded right canvas.

## Approved visual contract

- `.ph-theme-arunika-v3 .ph-app-shell` owns `var(--ph-sidebar-surface)` so the existing light/dark, color-responsive gradient remains active.
- `.ph-theme-arunika-v3 .ph-sidebar` and its expanded state use `background: transparent` so the gradient is painted once by the parent instead of restarting inside the sidebar box.
- The V3 sidebar edge uses `border-right: 0` because the screenshot seam is produced by the current one-pixel sidebar border.
- `.ph-theme-arunika-v3 .ph-layout-right` explicitly keeps `border-left: 0` and `box-shadow: none` to prevent a second seam source.
- Use `15px 15px 15px 0` as the right canvas outer margin while preserving its `12px` radius, content surface, header, sidebar widths, menu states, profile placement, and responsive behavior.
- Preserve the gradient composition while decoupling sidebar hover feedback into dedicated Arunika V3 light and dark hover tokens, so hover remains readable across every theme color.
- Keep the existing active navigation state unchanged and visually stronger than hover.

## Verification

- Update the focused static regression so it proves the parent owns the gradient, the sidebar is transparent and borderless, and the right canvas has neither a left border nor shadow.
- Observe the focused test failing before production CSS changes and passing afterward.
- Run all Arunika V3 Node regressions, the full Laravel test suite, Blade cache, served-CSS checks, and `git diff --check`.
- Runtime screenshot QA uses the authenticated dashboard when browser access is available; otherwise the user can confirm after `Ctrl+F5` with a fresh screenshot.

## Scope boundary

Do not alter menu content, active styling, typography, component dimensions, content padding, routes, application behavior, or the gradient composition. The only navigation-state change in scope is the approved Arunika V3-specific hover contrast correction.
