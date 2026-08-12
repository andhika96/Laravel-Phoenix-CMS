# Google Maps Widget Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Menambahkan widget Google Maps Basic pada Page Builder Elementor v2.3 dengan mapping Content, Style, Advanced, responsive controls, canvas preview, persistence contract, dan frontend renderer yang aman.

**Architecture:** Widget didaftarkan melalui config PHP dan `widget-registry.js`, memakai definition serta SFC `Settings.vue`/`Canvas.vue` mandiri seperti widget Basic aktif. Blade view membaca konfigurasi registry, membangun query embed Google Maps yang aman, dan memakai resolver Advanced shared. Tidak ada perubahan pada v2.0 atau penambahan global API key pada scope ini.

**Tech Stack:** Laravel Blade/PHP, Vue 3 SFC loaded by `vue3-sfc-loader`, Node `node:test`, PHPUnit/Laravel feature tests, Graphify.

## Global Constraints

- Preserve unrelated dirty worktree changes and all existing backups.
- Back up every existing file before editing it; do not use backups as source.
- Use `apply_patch` for source edits; do not stage, commit, push, or save browser data.
- Treat Graphify as navigation evidence and current source/tests as final truth.

## Task 1: Add RED parity tests

Files:

- Create `tests/pagebuilder-v23-google-maps-widget-parity.test.mjs`.
- Create `tests/Feature/PageBuilderElementorV23GoogleMapsWidgetTest.php`.

Assertions:

1. Definition registers `google_maps` as Basic, exposes defaults, clamps zoom, and normalizes responsive values/filter settings.
2. Settings exposes Content Location/Zoom, Style Height/Normal/Hover CSS Filters/Transition Duration, responsive hooks, and Advanced shared control.
3. Canvas renders safe encoded embed URL or an empty placeholder, responsive height, filter CSS, hover transition, and no raw user iframe/script.
4. PHP registry and Blade renderer output safe map markup, escaped location, clamped zoom, Advanced attributes, and empty fallback.

Run before implementation:

```powershell
node --test tests/pagebuilder-v23-google-maps-widget-parity.test.mjs
php artisan test --filter=PageBuilderElementorV23GoogleMapsWidgetTest
```

Expected result is RED because the new widget files and registry entry do not exist yet.

## Task 2: Implement the widget contract

Files:

- Create `public/js/pagebuilder_elementor_v23/widgets/basic/google-maps/definition.js`.
- Create `public/js/pagebuilder_elementor_v23/widgets/basic/google-maps/Settings.vue`.
- Create `public/js/pagebuilder_elementor_v23/widgets/basic/google-maps/Canvas.vue`.

Implementation details:

- Keep defaults and normalization local to the widget.
- Use a responsive device menu for Height; keep Zoom as the Elementor scalar control.
- Use a Normal/Hover state switch and the existing shared `CssFilterControl` through `editor.cssFilterControl`.
- Delegate Advanced to `editor.widgetAdvancedControls`.
- Build the map URL from encoded Location and clamped Zoom only.
- Keep the canvas placeholder deterministic when Location is empty.

## Task 3: Wire registry, labels, and frontend renderer

Back up first, then edit:

- `config/pagebuilder_elementor_v23_widgets.php`: add the Basic registry entry and Blade view path.
- `public/js/pagebuilder_elementor_v23/app.js`: add label/icon and include Google Maps in the shared Advanced shell gate.
- `resources/views/pagebuilder_elementor_v23/widgets/basic/google-maps.blade.php`: add safe server renderer with responsive height/filter CSS and shared Advanced resolver.
- `tests/pagebuilder-v23-properties-toolbar-regression.test.mjs`: update only the active registry count expectation if required by the new entry.

The existing generic dispatch in `render_node.blade.php` consumes the registry view path, so no unrelated renderer branch is added unless source verification proves it necessary.

## Task 4: Green focused verification

Run:

```powershell
node --test tests/pagebuilder-v23-google-maps-widget-parity.test.mjs tests/pagebuilder-v23-properties-toolbar-regression.test.mjs
php artisan test --filter=PageBuilderElementorV23GoogleMapsWidgetTest
```

Inspect rendered HTML for escaped query values, no raw script/iframe injection, responsive declarations, and Advanced resolver output.

## Task 5: Full verification and Graphify

Run the complete v2.3 Node test set and the v2.3 PHP feature suite used by the current handoff. Then update Graphify incrementally:

```powershell
graphify . --update --no-viz --code-only
graphify explain "public/js/pagebuilder_elementor_v23/widgets/basic/google-maps/Canvas.vue"
```

Finish with `git diff --check`, targeted diff review, and a status report that separates static tests, runtime/browser checks, assumptions, and remaining boundaries.
