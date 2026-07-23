# Image Box Typography Compact UI Design QA

- source visual truth path: `output/design-qa/image-box-typography-before.png`
- implementation screenshot path: unavailable
- viewport: 1920 x 1032 px
- state: Image Box selected, Style > Content > Title Typography open
- full-view comparison evidence: the supplied runtime screenshot shows dimension subcomponent styles failing to apply; post-patch capture remains unavailable
- focused region comparison evidence: sidebar Typography popover in the supplied screenshot; post-patch focused region unavailable

## Findings

- [P1] Post-patch rendered comparison is unavailable
  - Location: shared `TypographyControl.vue`, Image Box Style sidebar.
  - Evidence: the before screenshot is available and the corrected SFC is served byte-for-byte from the local HTTPS URL, but the configured browser runtime cannot start under the Windows ACL sandbox.
  - Impact: visual alignment and clipping cannot be formally approved from source and automated tests alone.
  - Fix: refresh the existing Firefox page, reopen Typography, and capture the same state for side-by-side review.

## Open Questions

- None about the requested layout. Runtime capture remains the only missing evidence.

## Implementation Checklist

- Confirm all dimension labels render at 11px.
- Confirm responsive icon and unit selector share one 28-30px-high tool row.
- Confirm range and 68px numeric input remain on the same row.
- Confirm Weight, Transform, Font Style, and Decoration selects are consistently 34px high.
- Confirm number spinners are hidden and no panel clipping occurs.

## Patches Made Since Previous QA Pass

- Corrected scoped-style penetration for local `DimensionField` and `ResponsivePicker` subcomponents with `:deep(...)`.
- Standardized dimension/select controls to 11px labels and 30-34px heights.
- Standardized range/numeric layout to a compact two-column row.
- Removed native numeric spinners.
- Added a regression test for the scoped deep layout contract.

## Follow-up Polish

- None identified until the post-patch screenshot is available.

final result: blocked

# Page Builder Setting Controls Consistency QA — 2026-07-23

- source visual truth path: `output/design-qa/image-box-advanced-background-before.png`
- implementation screenshot path: unavailable
- affected scope: shared Advanced, Image Box, Basic widgets, Accordion/Tabs, Grid/Row Grid, Container/Container Fluid
- static source audit: clean for targeted raw color and CSS-dimension input patterns
- automated regression: 109 tests passed, 1,355 assertions

## Findings

- [P1] Post-patch rendered comparison is unavailable
  - Evidence: before screenshot exists; browser automation remains blocked by the Windows ACL sandbox.
  - Impact: layout density, clipping, focus popovers, and picker placement are not formally visually approved.
  - Required manual check: refresh the existing builder page, inspect Content/Style/Advanced on each changed widget, and verify no control clips at the 320px sidebar width.

## Static checks completed

- Canonical color rows use native swatch plus local Coloris input.
- Numeric CSS dimensions expose a numeric field and unit selector; constrained values also expose a slider.
- Spacing groups preserve responsive keys and link/unlink behavior.
- Existing state keys and canvas/frontend render contracts remain covered by parity tests.

final result: blocked (visual runtime only; static regression passed)