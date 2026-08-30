# Page Builder v2.4 Static Import Runtime Overlap Follow-up

- Tanggal: 2026-08-28
- Input fixture: \`E:/Apps/Laragon/www/ceo-masters/index.html\`
- Laravel runtime root: \`D:/Laragon/www/laravel-13-phoenix/public\`
- Scope: root-cause correction for overlapping and duplicated Tailwind Canvas content

## Evidence

The supplied screenshots showed narrow, overlapping text and desktop/mobile navigation rendered together. The exact fixture was imported through the current service. The source contains 76 \`div\` elements; 52 are plain wrappers without explicit flex/grid/Bootstrap row classes, and 43 of those have multiple children.

Before this follow-up, the importer defaulted every generic wrapper to flex row. It also ignored Tailwind visibility tokens such as \`hidden\`, \`xl:flex\`, and \`xl:hidden\`. That made normal block-flow wrappers horizontal and allowed responsive desktop/mobile branches to coexist.

## Correction

\`StaticPageImportService::layoutSettings()\` now:

- defaults an unclassified wrapper to flex column;
- keeps explicit Tailwind \`flex\` row behavior;
- keeps explicit \`flex-row\`, \`flex-col\`, and Bootstrap \`.row\` behavior;
- maps \`hidden\` and responsive flex visibility to \`hideDesktop\`, \`hideTablet\`, and \`hideMobile\`.

No Canvas global normalizer, renderer, widget definition, v2.3 source, or E fixture was modified.

## Fresh fixture result

- frameworks: \`["tailwind"]\`
- mappedNodes: 303
- nodes: 304
- containers: 123
- explicit flex rows: 12
- vertical flex wrappers: 94
- gridContainers: 17
- gridWithoutColumns: 0
- gridLooseChildren: 0
- gridCells: 63
- emptyGridCells: 1
- hiddenDesktop: 4
- hiddenTablet: 4
- hiddenMobile: 3
- desktopOnly: 1

The desktop navigation payload is visible only on desktop; the mobile menu payload is hidden on desktop and remains separately represented for smaller devices.

## TDD and verification

- RED: \`test_it_maps_unclassified_wrappers_to_vertical_flex_flow\` failed with actual \`row\`.
- RED: \`test_it_keeps_explicit_tailwind_flex_rows_and_responsive_visibility_distinct\` failed with actual \`column\`.
- GREEN focused importer: **12 passed, 71 assertions**.
- Focused static Node test: **1 passed, 0 failed**.
- PHP lint and JS syntax checks: **pass**.
- \`git diff --check\`: **pass**.

Full Node v2.4 remained **401 passed, 0 failed** in the preceding verification; no JavaScript file changed in this follow-up. Full PHPUnit remains affected by the existing 419 CSRF test-environment failures documented in \`QA_REPORT.md\`.

## Browser boundary

The supplied browser screenshots predate this latest wrapper/visibility patch. Authenticated browser re-import after a hard reload is still required to visually accept the fix. The current draft in the browser will not change retroactively; the user must reload the editor and import \`index.html\` again.

## Backup

- \`D:/Laragon/www/laravel-13-phoenix/app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php.bak_20260828_211904_static_import_visibility_flow\`
- \`D:/Laragon/www/laravel-13-phoenix/tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php.bak_20260828_211904_static_import_visibility_flow\`

The earlier canonical-grid backups remain preserved as well.
