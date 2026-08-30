# Page Builder Elementor v2.4 — Flex Subcontainer Resize QA

Date: 2026-08-30
Project: `D:\Laragon\www\laravel-13-phoenix`

## Scope

Fix the Flexbox child-Container resize path so dragging the shared edge changes
the adjacent subcontainer widths and keeps the resize handle aligned with the
actual column boundary.

## Root cause

`BuilderNode.nodeShellStyle()` returned `widgetAdvancedPreviewStyle()` for every
module carrying `advanced` metadata. Layout modules also carry that metadata,
so Flex child Containers returned the widget style (`width: 100%`) before the
layout branch could apply `flex`, `flexBasis`, and `width` from `containerWidth`.
The resize handler therefore changed state, but the rendered subcontainer did
not change width. The previous handle CSS also placed the handle center in the
middle of the Flex column gap.

## Changes

- `public/js/pagebuilder_elementor_v24/app.js:2623`
  - apply `widgetAdvancedPreviewStyle()` only when `isWidgetNode` is true;
  - allow layout Containers to reach the Flex sizing branch.
- `resources/pagebuilder_elementor_v24/modules/layout/container/styles.css:90`
- `resources/pagebuilder_elementor_v24/modules/layout/container-fluid/styles.css:90`
  - center the resize hit area directly on the column boundary.
- `tests/pagebuilder-v24-flex-resize-follow-cursor.test.mjs`
  - regression coverage for pointer mapping, handle alignment, and layout
    sizing branch selection.

## Verification

- RED: regression test failed on the missing layout guard before the
  `app.js` change.
- GREEN focused suite: **39 tests passed, 0 failed**.
- Full Node v2.4 suite: **426 tests passed, 0 failed**.
- `node --check public/js/pagebuilder_elementor_v24/app.js`: passed.
- `git diff --check`: passed.
- Runtime style evaluation: `47.3%` and `52.7%` produced matching
  `flexBasis`/`width` values; DOM widths measured `212.84375px` and
  `237.15625px` for the 450px content pair.
- Browser geometry comparison with a 30px gap: old handle offset from the
  boundary **15px**; new offset **0px**.
- Live editor endpoint: **302** to `/auth/login`; authenticated manual pointer
  drag could not be executed in this session.
- Broad PHPUnit v2.4 run remains non-green because the existing suite reports
  CSRF `419` failures (30 failed, 231 passed, 11,253 assertions); no PHP file
  is part of this fix.
- Graphify incremental code-only update: **21,147 nodes, 36,789 edges,
  1,526 communities**.

Post-QA environment note: the persistent browser profile used for the
read-only login smoke test contains browser cache/log changes. The current
global `git diff --check` reports trailing whitespace only in that profile;
the scoped source diff check remains clean. The profile is intentionally not
reset or deleted.

## Safety boundaries

- No database mutation, Save, Reset, commit, push, or deployment was performed.
- Page Builder v2.3 and the main responsive engine were not modified.
- The shared `app.js` already contained unrelated v2.4 work from the reference
  task; only the sizing guard above belongs to this fix.

## Backups

- `public/js/pagebuilder_elementor_v24/app.js.bak_20260830_flex_sizing_branch`
- `tests/pagebuilder-v24-flex-resize-follow-cursor.test.mjs.bak_20260830_flex_sizing_branch`
- `resources/pagebuilder_elementor_v24/modules/layout/container/styles.css.bak_20260830_flex_resize_cursor_alignment`
- `resources/pagebuilder_elementor_v24/modules/layout/container-fluid/styles.css.bak_20260830_flex_resize_cursor_alignment`
