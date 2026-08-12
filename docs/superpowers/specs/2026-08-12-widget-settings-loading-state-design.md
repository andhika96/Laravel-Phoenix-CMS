# Widget Settings Loading State

## Goal

Replace the empty v2.3 sidebar gap shown while an asynchronously loaded widget `Settings.vue` module is resolving with a compact, accessible loading state.

## Design

The shared `loadWidgetSettings(type)` seam in `public/js/pagebuilder_elementor_v23/app.js` will configure Vue's `defineAsyncComponent` with a reusable loading component. The loader is the correct boundary because every registered widget settings module passes through it, so existing and future widgets receive the same behavior without per-widget changes.

The loading component will render inside the existing properties section with a small CSS spinner and the English copy `Loading widget settings...`, matching the current English editor UI. It will use `role="status"` and `aria-live="polite"`. The selection summary, header, tabs, and canvas remain visible while the settings body loads.

The async component will also receive a small error fallback with `role="alert"` so a failed module request cannot leave the sidebar blank indefinitely. No retry flow, new dependency, persistence change, or widget-specific state is introduced.

## Acceptance criteria

1. Selecting or dropping a widget shows the loading spinner and message immediately while its settings module is unresolved.
2. The loading state disappears automatically when the settings module resolves.
3. A failed settings module renders an error message instead of an empty panel.
4. All registered widgets use the shared behavior without changing their individual `Settings.vue` files.
5. Existing editor and widget parity tests remain green.

## Verification

- A focused Node regression test checks the loader configuration, accessible copy/roles, and v2.3 CSS animation.
- `node --check` validates `app.js`.
- The focused regression and existing v2.3 runtime suites are rerun.
- Read-only Chrome QA confirms the editor panel behavior when the local editor tab is available; Save is not pressed.
