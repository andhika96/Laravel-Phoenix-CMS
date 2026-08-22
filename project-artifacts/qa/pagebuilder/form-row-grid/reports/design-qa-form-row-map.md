# Design QA — Form Row Map Sidebar

Date: 2026-08-21

## Comparison target

- Source visual: `C:\Users\aruna\AppData\Local\Temp\codex-clipboard-2c05e05d-8e56-4821-9a60-392fb444828e.png`
- Runtime capture: `C:\Users\aruna\.codex\visualizations\2026\08\21\01a0246e-a0b3-7091-bb4f-3b4f1821d073\form-row-grid-row-map-runtime-1728x910.png`
- Route: `https://laravel-13-phoenix.aruna/pagebuilder-elementor/v2.3/create`
- Viewport: requested 1728 × 910 CSS px, density 1.
- State: unsaved Form, Desktop, Row 1 expanded, two columns, Name selected in Inspector.

The source and runtime captures were opened together in one comparison input. The focused comparison covered the Row header, Row Map, Inspector, column resizing behavior, and Canvas parity.

## Findings

No actionable P0, P1, or P2 visual differences remain.

- Layout: field configuration is no longer rendered inside narrow grid cells. Row Map alone visualizes 1–4 columns; Inspector and the field list remain full-width.
- Typography: Row, field names, Inspector labels, type metadata, and column badges remain readable in the production sidebar.
- Spacing: compact map cells preserve the selected concept's hierarchy without nested configuration cards.
- Colors: active selection, drop state, and column badges use the existing Phoenix purple and neutral tokens.
- Icons and assets: existing Font Awesome icons are retained; no replacement assets or CSS-drawn icons were introduced.
- Copy: `Row Map`, `Inspector`, `Fields in this row`, and `More field options` describe the interaction directly.
- Accessibility: Row uses a real disclosure button with `aria-expanded`; Inspector selection and delete actions remain separate buttons; advanced field controls use native `details/summary`.

## Comparison history

### Pass 1 — blocked

- [P1] Two-column mode squeezed complete field configuration cards into half the sidebar width, clipping names, type metadata, and controls.
- [P1] Sortable `group.put` returned a column ID string. Sortable interpreted it as an allowed group name and rejected every drop.

Fixes:

- Replaced side-by-side configuration cards with a compact Row Map.
- Added one full-width Inspector and one full-width field list per expanded row.
- Added collapsible `More field options` so core controls remain compact without deleting existing settings.
- Centralized the Sortable owner/group guard and made it return strict booleans.

### Pass 2 — passed

- Runtime measurement at 2 columns: Row Map cell 117 px; Inspector, field list, and row body all 240 px.
- Runtime measurement at 4 columns: Row Map cell 56 px; Inspector, field list, and row body remain 240 px.
- Hard-reload Sortable evidence: `checkPut === true`, type `boolean`; internal metadata guard also returns true.
- Row collapse/expand and Name → Email Inspector selection passed.
- Console errors: none. Rendered `undefined`: none.

## Verification boundary

Native pointer drag could not be completed deterministically by browser automation. The exact previous rejection was reproduced and corrected at the Sortable boundary, while layout mutation and swap behavior are covered by automated tests. Manual drag in the user's Firefox remains the final native-input confirmation.

## Follow-up polish

- [P3] The production Page Builder shell contains more content above Row Grid than the visual concept, so `Fields in this row` may require normal sidebar scrolling at shorter viewport heights.

## Final result

final result: passed

