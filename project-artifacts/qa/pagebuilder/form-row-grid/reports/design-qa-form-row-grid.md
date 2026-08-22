# Design QA — Form Widget Internal Row Grid

Date: 2026-08-22

## Comparison target

- Source visual truth: `C:\Users\aruna\.codex\generated_images\01a0246e-a0b3-7091-bb4f-3b4f1821d073\exec-0adc2828-8b86-4edd-8cd8-4f7c999c2a52.png`
- Browser-rendered implementation: `C:\Users\aruna\.codex\visualizations\2026\08\21\01a0246e-a0b3-7091-bb4f-3b4f1821d073\form-row-grid-option-3-runtime-1716x920.png`
- Additional runtime capture: `C:\Users\aruna\.codex\visualizations\2026\08\21\01a0246e-a0b3-7091-bb4f-3b4f1821d073\form-row-grid-option-3-runtime.png`
- Final clean runtime capture: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\pagebuilder\form-row-grid\previews\design-qa-form-row-grid-final-20260822.png`
- Final Steps capture: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\pagebuilder\form-row-grid\previews\design-qa-form-steps-final-20260822.png`
- Final reference/implementation board: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\pagebuilder\form-row-grid\previews\design-qa-form-row-grid-comparison-20260822.png`
- Route: `https://laravel-13-phoenix.aruna/pagebuilder-elementor/v2.3/create`
- State: new unsaved Form widget, Desktop Row Grid, one column, Step settings collapsed, Name field expanded, Email/Message collapsed, Canvas handle idle.
- Source pixels: 1717 × 916.
- Requested comparison viewport: 1716 × 920 CSS px at density 1.
- Browser capture pixels: 1636 × 920 due to the in-app browser's available content width; the component state and vertical viewport match, while the existing Page Builder shell keeps its production sidebar proportion.

The source and runtime images were opened together in one multi-image comparison input. A focused comparison was required for the sidebar accordion, row header controls, responsive switch, and Canvas drag handle because those details are too small to judge reliably from the full composition alone.

## Findings

No actionable P0, P1, or P2 differences remain.

- Typography: the implementation keeps the Page Builder's existing font stack and hierarchy. Row, Step, type metadata, and field labels remain readable without the duplicate oversized headings from the broken version.
- Spacing and layout: Row Grid controls fit the production sidebar without wrapping. The expanded field uses a two-column control grid; collapsed fields remain compact. Canvas fields retain full width and do not shift when the handle appears.
- Colors and tokens: active responsive state, borders, muted metadata, and purple interaction color stay within the existing Phoenix palette and preserve contrast.
- Image quality and assets: no raster imagery is used by this UI. Existing Font Awesome icons are retained; no placeholder, CSS-art, custom SVG, or generated icon replacement was introduced.
- Copy and content: `Row Grid`, responsive device labels, `Step`, `Row`, column count, `Add Field`, and `Add Row` match the selected direction while existing field configuration labels remain intact.
- Interaction and accessibility: field headers are real buttons with `aria-expanded`; Step settings use native `details/summary`; delete controls expose labels and disabled state; drag uses dedicated handles so inputs and submit controls remain interactive.

## Comparison history

### Pass 1 — blocked

- [P1] All field settings were permanently expanded, creating an unusable, extremely tall sidebar.
- [P1] Canvas drag handles rendered as permanent black browser-default buttons and shifted every field.
- [P1] transient missing layout state threw `can't access property "steps", layout is undefined`.

Fixes:

- Added one-open-item accordion state and restored collapse/expand behavior.
- Rebuilt the sidebar hierarchy around compact Step, Row, field, and action surfaces.
- Added safe `layoutSteps` projections in Settings and Canvas.
- Made internal Canvas Row Grid selectors global across the dynamic child-component boundary and gave the handle a centered circular hover state.

Post-fix evidence:

- Browser capture shows compact cards and full-width Canvas controls without black handle boxes.
- Runtime accordion test: visible → hidden → visible.
- Runtime console: zero application errors and zero rendered `undefined` text.

### Pass 2 — passed with P3 cleanup

- [P3] The outer Form Fields disclosure injected a second Step chevron into the nested summary.

Fix:

- Suppressed the inherited `summary::before` pseudo-element specifically on the nested Step summary.

Post-fix evidence:

- Fresh hard-reload computed state: nested Step `::before` is `none`, `::marker` is empty, Step remains collapsed.
- Idle Canvas handles report opacity `0`; hover verification reports opacity `1` and a 26 × 26 circular geometry.

### Pass 3 — passed (2026-08-22)

- [P1] Native HTML5 drag reached the Form handle but did not produce a reliable `dragstart` lifecycle.
- [P2] The empty `Drop field` target was only about 26px high and visually too small for its interaction importance.

Fixes:

- Enabled SortableJS pointer fallback only for the internal Form Row Grid; page-level Container/Grid drag configuration was not changed.
- Increased the empty drop target to a 56px minimum height with 12px padding, centered content, 12px text, and a 6px radius.

Post-fix evidence:

- Hard reload reports every Form cell with `forceFallback: true` and `nativeDraggable: false`.
- Native Chrome pointer drag moved a field between rows through the internal Form drop zones.
- Runtime `Drop field` geometry measured 55.995px high with computed `min-height: 56px`, `padding: 12px`, and `font-size: 12px`.
- Row 1 had no Submit placeholder, Row 2 retained one Submit item, and browser console warnings/errors were empty.

### Pass 4 — passed (2026-08-22)

- [P1] The pointer-fallback clone was appended to `body`, outside `.pb-pro-form`, so ancestor-dependent field CSS disappeared and the clone briefly looked like raw browser input/textarea controls.
- [P2] A very small accidental movement on the drag handle could begin fallback rendering too early.

Fixes:

- Added Form-only global fallback styling for the cloned field card, label, input, textarea, and select while keeping `fallbackOnBody` enabled for stable cross-row targeting.
- Hid the clone's drag handle and Row Span toolbar, and added a 4px fallback movement tolerance.

Post-fix evidence:

- Clicking the Message textarea produced zero fallback-clone mutations.
- A 2px handle movement produced zero fallback clones.
- During a real fallback drag, the body clone computed to a white background, purple border, 6px radius, 92% opacity, full-width styled input, and hidden handle.
- The completed cross-row drag removed the fallback clone and retained the canonical field definitions.

### Pass 5 — passed (2026-08-22)

- [P1] Dropping onto an occupied field redirected the item into another empty cell, so the visible target did not control the outcome.
- [P1] Sortable could retain the last valid cell crossed even when the pointer was finally released outside the Form.
- [P2] The fallback clone was visually too large and could obscure the intended target.

Fixes:

- Made the exact target canonical: an empty cell receives the item; an occupied cell swaps with the source.
- Deferred the final mutation from `onAdd` to `onEnd`, resolving the cell under the release coordinates and restoring the pre-drag snapshot when released outside the owner Form.
- Kept a compact label-only fallback clone at 220 × 44px with `pointer-events: none`.

Post-fix evidence:

- Chrome pointer drag moved `Message` from Row 1 into the empty field cell in Row 2 on the first attempt.
- Dropping `Email` directly onto occupied `Message` swapped the two fields even though Row 1 still had another empty cell.
- Releasing a field over the settings sidebar left both rows unchanged.
- Reverse-direction drag moved `Email` from Row 2 back into an empty Row 1 cell.
- During drag the fallback clone measured 220 × 44px, displayed only `Email`, and computed `pointer-events: none`; zero fallback clones remained after release.
- The page rendered no `undefined` text.

### Pass 6 — passed (2026-08-22)

- [P1] A two-column row containing three fields had no canonical fourth cell, so the visible area below Email could not accept a drop.
- [P1] The compact fallback clone retained the grab offset of the original full-width field and appeared far from the cursor.
- [P1] An abnormal pointer release or browser blur could leave Sortable's fallback/chosen state behind.

Fixes:

- Materialized the final empty cell needed to complete an incomplete visual grid line in both JS and PHP normalizers.
- Applied Sortable's native `fallbackOffset` from the handle position and tracked the fallback clone center while dragging.
- Added delayed pointer-release recovery, deferred canonical finalization, geometry and DOM target fallbacks, and owner-scoped ghost cleanup.
- Moved the empty-cell hint outside Sortable's managed children so it cannot be dragged or left behind as stale DOM.

Post-fix evidence:

- Row 1 with two columns and Name, Email, and Message rendered four canonical cells; the fourth cell appeared directly below Email.
- Native Chrome drag moved `New Field` from Row 2 into that fourth cell; Canvas showed it below Email and sidebar membership moved from Row 2 to Row 1.
- The fallback clone measured 220 × 44px and its center matched the pointer exactly (`dx: 0`, `dy: 0`).
- A forced browser blur during an active fallback drag left zero fallback, chosen, ghost, drag, or `is-dragging` elements, restored `user-select: auto`, and retained exactly one `New Field`.

### Pass 7 — visual and structural QA passed; native drag automation blocked (2026-08-22)

The cell-per-field architecture from Passes 3–6 was replaced after runtime use showed that disappearing empty cells and automatic swap remained confusing. The current canonical model is `Form → Step → Row → Column → ordered fields`; each Column is one persistent Sortable list and Submit is derived as a final-step footer.

Changes:

- Migrated `rowGrid` to schema version 2. Legacy version 1 cells are distributed into persistent Column lists while field definitions, row span, dataset, conditional logic, and validation data remain intact.
- Removed stored Submit items and full-span action cells. Submit now renders once after all final-step rows.
- Replaced per-cell Sortable instances and manual pointer/snapshot recovery with one normal VueDraggable list per Column.
- Removed automatic swap. The mutation contract is ordered insertion/reorder; occupied fields remain and later items shift.
- Added a permanent trailing `Drop field here` surface to every Column, including empty Rows.
- Kept the sidebar as non-draggable accordion lists and added a compact `Submit button — Final step footer` status card.

Runtime evidence after hard reload in a fresh unsaved Chrome tab:

- Default Form rendered one Row, one Column, three ordered fields, one persistent drop surface, and one Submit footer outside the Column.
- Changing Desktop to two columns produced `Column 1 = Name, Message` and `Column 2 = Email`; both Columns retained their own drop surface.
- Adding Row 2 and a field produced one field plus one persistent drop surface in Row 2 while Submit remained a single footer.
- Every Form Column had an active Sortable `pointerdown`, `dragenter`, and `dragover` listener; Form owner/group metadata remained isolated from page-level groups.
- Browser console contained no application warning or error, and no fallback/ghost element remained after attempted automation drags.

Automation limitation:

- Chrome CUA and permitted CDP mouse dispatch generated `pointerdown`/`mousedown` on the correct Form handle and `pointerup`/`mouseup` over the correct destination, but did not generate the browser-native `dragstart`/`dragover` sequence. Therefore the final native pointer move is not claimed as passed in this iteration. The deterministic insertion, same-column reorder, cross-row move, cross-step confirmation, owner rejection, empty-row target, migration, projection, renderer, and submission paths are covered by executable Node/PHP tests.

### Pass 8 — passed (2026-08-22)

- [P1] Native textarea resizing wrote an inline height that overrode the `Rows` setting and made it appear blocked while Row Span was active.
- [P1] The selected-field Row Span toolbar used a negative top offset and was clipped when the field occupied the first Form position.

Fixes:

- Made `Rows` the canonical textarea height control in the editor Canvas by using `height: auto` and disabling native Canvas resize; the published frontend textarea remains browser-native.
- Kept Row Span responsible only for the field wrapper's grid footprint.
- Moved the Row Span toolbar into the selected field's layout flow and added an 8px right inset, so it never escapes the Form boundary.

Post-fix evidence:

- With Row Span fixed at 4, changing `Rows` from 4 to 6 changed the textarea from 113px to 161px while `grid-row: span 4` remained unchanged.
- The textarea computed `resize: none`, retained `height: auto`, and reflected the selected `rows` attribute.
- On the first field in the Form, the toolbar was fully inside both the field and Form bounds, used `position: relative`, and measured an 8px right inset.
- Chrome runtime logs contained only normal Page Builder load messages; no error or warning was emitted.

### Pass 9 — passed (2026-08-22)

- [P1] Keeping the Row Span toolbar in normal layout flow prevented clipping but pushed the field label and input downward whenever the field was selected.

Fix:

- Matched the existing Widget/Container label pattern: the selected field remains the positioning anchor while the Row Span toolbar is an absolute overlay on its top border.
- Used `top: 0`, `right: 8px`, and `translateY(-50%)`; removed the temporary Grid layout from the field wrapper.

Post-fix evidence:

- Before and after selecting the first field, its item height stayed `67.06px`, label top stayed `156.93px`, and control top stayed `182.52px`.
- The toolbar computed `position: absolute`, remained visible above the selected field border, and retained the 8px right inset.
- Clicking the toolbar plus button changed Row Span from 1 to 2 and updated the item to `grid-row: span 2`.
- Chrome console warnings/errors remained empty.

### Pass 10 — shared tracks, Steps, and Submit width passed (2026-08-22)

- New fields added from a Row keep the existing accordion state and remain collapsed.
- Row Span now uses shared parent-row tracks through CSS `subgrid`, while each canonical Column remains an independent Sortable list.
- Added explicit Add Step and safe Delete Step actions. Deleting the active final Step preserves nested fields and clamps the Canvas back to a visible Step.
- Steps Settings now explains that at least two Steps are required; `number` displays two indicators plus Next/Previous navigation once a second Step exists.
- Submit exposes a compact width selector in its final-step card and stays synchronized with the existing Button Width setting.

Runtime evidence from a fresh unsaved Chrome Form:

- Adding a field left the new `New Field` card at `aria-expanded="false"`; the previously opened card did not change.
- In a two-column Row, `Message` with Row Span 4 measured `298.206px`; the matching four-field range in the adjacent Column also measured `298.206px`, with both top and bottom deltas equal to `0px`.
- The Canvas textarea computed `resize: none`; its textarea filled the spanning field without changing the shared track boundaries.
- Before Add Step, the helper appeared and no indicator rendered. After Add Step, two indicators and Next rendered; Step 2 showed Previous and the final Submit.
- Deleting Step 2 while it was active left one visible Canvas Step, three fields, and one visible Submit.
- Selecting Submit width 50% produced a measured Canvas ratio of exactly `0.5` (`563.785px / 1127.581px`).
- Chrome runtime logs contained only normal Page Builder load messages; no error or warning was emitted.

### Pass 11 — native drag, stable targets, Step variants, and editor draft contract passed (2026-08-22)

- [P1] The active Sortable instance stopped at `choose` in native HTML5 mode, so automated and real-world cross-row movement could appear to drag without committing a mutation.
- [P1] Revealing a trailing target on populated Columns during drag added one Grid track and moved every later Row while the pointer was travelling toward it.
- [P1] The compact 220px fallback preview retained the horizontal grab offset of the original 1126px field, placing its center about 453px away from the cursor.
- [P1] Dropping on the lower half of an occupied field could append to the Column tail instead of inserting after the field under the pointer.

Fixes:

- Constructed only the internal Form Sortables with `forceFallback: true`; page-level Container/Grid Sortables remain unchanged.
- Kept populated trailing hints at zero height in both idle and drag states, and stopped adding synthetic tail rows to the shared track plan.
- Calculated the compact fallback offset from the source and ghost widths and applied it with the independent CSS `translate` property, preserving Sortable's own transform.
- Returned explicit `-1`/`1` Sortable move directions from the pointer's upper/lower half of the related Form field.

Post-fix evidence:

- Fresh runtime instances reported `forceFallback: true` and `nativeDraggable: false`.
- Native Chrome CUA moved Email from a populated Row to an empty Row (`[3,0]` to `[2,1]`), then inserted it below Name in the populated Row (`Name, Email, Message`).
- The fallback preview measured about 220 × 44px; its horizontal center differed from the pointer by approximately `0.006px`, and zero fallback/ghost/chosen nodes remained after release.
- Populated tail computed to `height: 0px`, `opacity: 0`, and `pointer-events: none`; the empty Row target remained visible at approximately 56px high.
- The selected Row Span toolbar remained fully inside the field with top and right insets of approximately 8px.
- Step types rendered distinct contracts: Text (labels), Icon/Number (markers), Progress (one progressbar), and Number/Icon + Text (markers and labels). Progress advanced from 50% to 100%; Circle, Square, Rounded, and None produced distinct marker geometry.
- Browser console warnings/errors were empty after the final clean hard reload.
- Canvas draft submit reached the protected endpoint. The current Chrome QA session was anonymous, so the endpoint correctly returned `401 Unauthenticated`; the authenticated unsaved-action execution is covered by the passing feature test and was not falsely claimed as browser-passed.

### Pass 12 — Row Span corner alignment passed (2026-08-22)

- [P1] The toolbar still used `top: 8px; right: 8px`, placing it inside the textarea body instead of on the field's top-right corner shown by the approved screenshot.

Fix:

- Changed only the absolute offsets to `top: 0; right: 0`; sizing, shadow, controls, field flow, Row Span behavior, and drag handling were preserved.

Post-fix evidence:

- Source visual truth: `C:\Users\aruna\AppData\Local\Temp\codex-clipboard-bca81edd-4b6c-474d-bcb8-64774235dec9.png` (1161 × 784px).
- Browser implementation: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\pagebuilder\form-row-grid\previews\design-qa-form-row-span-corner-final-20260822.png` (1422 × 612px at the Chrome viewport).
- Focused normalized comparison: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\qa\pagebuilder\form-row-grid\previews\design-qa-form-row-span-corner-comparison-20260822.png`.
- Chrome computed `position: absolute`, `top: 0px`, and `right: 0px`; measured field-relative offsets were `top: 0px` and effectively `right: 0px`.
- The focused comparison shows the toolbar aligned to the same top-right field corner as the target. Typography, colors, radius, shadow, icons, and copy remain unchanged.
- Chrome console warnings/errors were empty.

## Primary interactions tested

- Collapse and re-expand Name field settings.
- Keep Email and Message collapsed while Name is open.
- Change Desktop row layout from one to two columns and verify balanced field distribution and computed tracks.
- Verify the Step settings disclosure remains collapsed by default.
- Verify Canvas handle idle/hover states without moving the form.
- Verify each populated and empty Column keeps a persistent trailing drop surface.
- Verify Submit renders as a final-step footer outside every Row/Column.
- Verify ordered insertion and reorder through the shared JS/PHP mutation contract without swapping occupied fields.
- Verify native Chrome drag into an empty Row and pointer-directed insertion into a populated Row.
- Verify the compact fallback preview follows the cursor and cleans up on release.
- Verify all Step type and Shape variants plus 50% → 100% navigation progress.
- Verify Row Span toolbar aligns exactly to the selected field's top-right corner.
- Verify page root, Container, Grid, child Container, and another Form owner reject the internal Form group.
- Hard reload and create a fresh unsaved Form without Save, Reset, or Apply dataset.
- Check browser console errors and rendered `undefined` text.

## Follow-up polish

- [P3] The production sidebar contains more existing field options than the visual concept, so a long expanded field can still require normal panel scrolling. This preserves all existing Form functionality and is acceptable for this scope.
- [Verification boundary] Browser execution of real draft actions requires an authenticated editor session by design. This Chrome QA session was anonymous; server-side authenticated execution, ownership rejection, CSRF route contract, and test-submission metadata passed automated feature coverage.

## Final result

final result: passed
