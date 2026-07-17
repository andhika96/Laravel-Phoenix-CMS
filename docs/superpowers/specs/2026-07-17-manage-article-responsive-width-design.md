# Manage Article Responsive Width Design

## Goal

Prevent the Manage Article responsive table from hiding the `Options` column on a wide viewport because of browser-specific scrollbar gutters or integer width rounding, while preserving priority-based hiding on genuinely narrow viewports.

## Scope

- Change only the Manage Article responsive width calculation.
- Preserve the existing table markup, child-row UI, column priorities, and frontend box model.
- Do not change the duplicated Manage User implementation in this task.
- Add a Node regression test that exercises the real Vue method definitions from the production script.

## Selected Design

Use `wrapper.clientWidth` as the usable table width because it excludes borders and scrollbar gutters. Measure each header with fractional `getBoundingClientRect().width` values rather than integer-rounded `offsetWidth`. Allow a maximum `1px` comparison tolerance so harmless subpixel accumulation cannot hide a complete column.

The responsive algorithm continues to hide columns in ascending priority order when the measured overflow is greater than `1px`. `Options` remains the last-priority column and the child-row behavior remains unchanged.

## Alternatives Rejected

- Tolerance only: leaves the calculation dependent on browser scrollbar gutters and integer-rounded widths.
- Replace the custom table with a third-party responsive library: changes too much code and UI behavior for this defect.

## Verification

- A regression test must fail against the current implementation for usable width, fractional measurement, and a `1px` rounding difference.
- The same test must pass after the patch and must still hide `Options` for a material shortage.
- `node --check` must pass for the production script.
- Live Chrome validation must show all columns at `1920px` with both expanded and collapsed sidebar states, with no relevant console warnings or errors.

