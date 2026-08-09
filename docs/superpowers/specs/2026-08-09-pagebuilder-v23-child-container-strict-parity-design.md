# Page Builder v2.3 Child Container Strict Parity Design

## Goal

Replace the v2.3 Flexbox `columns[]` slot abstraction with real child Container nodes, matching Elementor's container mental model while preserving existing saved v2.3 pages through a transparent, save-gated migration.

## Decisions

- Only Page Builder v2.3 is in scope. Page Builder v2.0 remains unchanged.
- The canonical new layout tree is `Container.children[]`; a visual Flexbox column is a child Container, not an internal column record.
- Legacy `columns[]` data is accepted and converted in memory when a page is opened.
- Opening a legacy page never writes to the database. The canonical schema is persisted only after an explicit user Save.
- Editor and frontend renderers remain dual-read during the transition.
- New Container presets and the `Add Container` action create real child Container nodes.
- Edge resize changes responsive width or flex-basis settings on adjacent child Containers.
- A permanent hybrid editor model is not allowed. `columns[]` is compatibility input only after migration support lands.

## Alternatives Considered

### Immediate schema rewrite

This produces the cleanest code quickly, but opening or deploying the new editor could invalidate existing saved page JSON. It also makes rollback difficult. Rejected because migration safety is more important than short-term simplicity.

### Staged strict-parity migration

The editor normalizes legacy data to canonical child Containers in memory, the renderer accepts both schemas, and only an explicit Save persists the canonical tree. Selected because it reaches strict parity without silently mutating user data.

### Permanent hybrid model

The editor would keep both internal columns and child Containers indefinitely. This reduces initial migration work but preserves two selection, drag/drop, responsive, and rendering models. Rejected because it creates long-term ambiguity and repeated bugs.

## Canonical Data Model

A Container owns layout settings and direct child nodes:

```json
{
  "id": "container_parent",
  "type": "container",
  "settings": {
    "displayType": "flex",
    "direction": "row"
  },
  "children": [
    {
      "id": "container_child_1",
      "type": "container",
      "settings": {
        "displayType": "flex",
        "contentWidth": "full",
        "containerWidth": "50%",
        "containerWidthTablet": "",
        "containerWidthMobile": ""
      },
      "children": []
    }
  ]
}
```

Canonical Container nodes do not write a `columns[]` property. Grid and Flexbox use the same `children[]`; changing `displayType` changes layout behavior without moving or deleting child content.

## Legacy Migration Adapter

The adapter has one responsibility: convert a legacy Container with `columns[]` into a canonical Container tree.

For each legacy column:

- reuse the column ID as the child Container ID when it is safe and unique;
- otherwise derive a deterministic ID from the parent Container ID and legacy column index, adding a collision suffix only when required;
- move `column.children[]` into the new child Container's `children[]`;
- map `column.flexBasis` to the child Container desktop width;
- preserve order and all nested widget data;
- copy responsive legacy column snapshots to child Container responsive widths when present;
- mark the page state as migration-pending without marking it saved.

The adapter must be idempotent. Running normalization twice must not create another Container layer or new IDs.

## Save Contract

- Loading or previewing a page performs no persistence.
- Undo/redo history starts after in-memory normalization, so migration internals are not exposed as an undo action.
- An explicit Save serializes only the canonical `children[]` schema for migrated Containers.
- A failed Save leaves the editor state and original stored JSON recoverable.
- The existing v2.3 ownership/version guard remains authoritative.
- Save responses must not claim success until the server confirms persistence.

## Editor Components and Responsibilities

### Tree normalizer

Detects legacy layout nodes, converts them once, and guarantees canonical child Container nodes before normal editor actions run.

### Container factory

Creates canonical Container nodes for toolbox insertion, presets, `Add Container`, duplicate, and nested insertion. A two-column preset creates one parent and two child Containers.

### Canvas renderer

Renders `node.children` directly. Each child Container receives the normal Container selection label, action toolbar, dropzone, Style, and Advanced behavior. No separate selectable Column entity remains.

### Drag/drop resolver

Treats child Containers as normal layout nodes. Widgets may be dropped inside them, and Containers may be reordered or nested subject to the existing depth and cycle guards.

### Edge resize controller

Shows a resize affordance on the shared vertical edge between adjacent child Containers only when the parent resolves to Flexbox, row direction, and nowrap for the active device. Dragging preserves the pair's combined width and writes responsive values to the active device.

### Frontend renderer

Renders canonical child Containers recursively. During migration it retains a legacy `columns[]` fallback so published pages continue to render before being opened and saved in the new editor.

## UX Behavior

- Selecting a child Container opens the complete Container Layout, Style, and Advanced settings.
- The canvas badge says `Container`; there is no Column 1 or Column 2 pseudo-node.
- The old `Add Column` action is replaced by `Add Container`, matching the node that is actually created.
- Duplicate and Delete operate on the selected child Container and all its nested content.
- Dragging the shared edge displays the active-device percentage and updates the canvas live.
- Width changes are added to undo history as one completed resize action, not one history entry per mouse movement.
- Grid to Flexbox and Flexbox to Grid preserve the same children and selection IDs.
- Empty child Containers remain valid drop targets and cannot disappear during selection or normalization.

## Responsive Width Rules

- Desktop width is the base value.
- Tablet and Mobile inherit the nearest larger-device value when unset.
- Resizing on Tablet or Mobile writes only that device's width.
- Pair widths are normalized to their existing combined percentage rather than always forcing the entire parent to 100%.
- A child keeps a practical minimum width during pointer resize; manual numeric controls may still express smaller valid values when the existing control contract permits them.
- Direction other than row, wrapping layouts, and Grid mode do not expose the Flexbox edge-resize affordance.

## Safety and Error Handling

- Invalid, missing, or duplicate legacy IDs are repaired in memory without dropping content.
- A malformed legacy column becomes one recoverable child Container containing every valid nested child found in that column.
- Unknown node types remain untouched and continue through the existing unknown-node handling.
- Migration never truncates extra columns or merges their children into the last child Container.
- Recursive conversion includes nested legacy Containers but rejects cycles.
- The old JSON remains unchanged until Save succeeds.
- No migration code or shared asset is added to v2.0.

## Frontend Compatibility Window

The v2.3 frontend renderer accepts both formats for at least the full implementation and QA cycle:

1. canonical `children[]` Container tree;
2. legacy `columns[]` with nested children.

The compatibility fallback may be removed only after repository evidence shows no supported v2.3 stored page requires it. Removing it is outside this implementation scope.

## Scope Boundaries

Included:

- v2.3 Container schema normalization;
- Flexbox child Container creation and selection;
- Add Container and preset behavior;
- edge resize and responsive width state;
- undo/redo integration;
- Grid/Flex child preservation;
- frontend dual-read rendering;
- persistence and regression tests;
- read-only browser QA and incremental Graphify update.

Excluded:

- changes to Page Builder v2.0;
- database-wide background migration commands;
- automatic Save after page load;
- removal of the frontend legacy fallback;
- unrelated widget, shell, or theme redesigns;
- mass cleanup, staging, or committing of existing backup and screenshot files.

## Verification Strategy

### Focused automated tests

- legacy two- and three-column nodes normalize into child Containers without content loss;
- normalization is idempotent;
- duplicate/malformed IDs are repaired safely;
- new Flexbox presets contain child Containers and no `columns[]`;
- Add Container creates a selectable child Container;
- edge resize changes exactly two adjacent child widths and creates one history snapshot;
- Desktop, Tablet, and Mobile widths remain isolated and inherit correctly;
- Grid to Flexbox and back preserves IDs, order, settings, and nested widgets;
- save serialization emits canonical children only;
- failed saves do not mutate stored page data;
- frontend renders both legacy and canonical schemas equivalently;
- v2.0 isolation and module-parity guards remain green.

### Browser QA

- open a legacy v2.3 fixture without saving and verify storage is unchanged;
- select each migrated child Container and inspect full settings;
- add, duplicate, reorder, nest, and delete child Containers;
- resize shared edges on all supported responsive devices;
- switch Grid and Flexbox without content loss;
- undo and redo an edge resize;
- hard reload after an explicit test Save and confirm canonical data reopens correctly;
- compare the interaction against the official Elementor Flexbox demo;
- verify console errors and warnings;
- do not use production or user-owned page data for destructive QA.

### Final gates

- focused Node tests;
- full Page Builder Node suite;
- focused v2.3 Laravel route, persistence, and frontend tests;
- Vue SFC and JavaScript syntax checks;
- `git diff --check`;
- read-only or disposable-fixture browser evidence;
- Graphify incremental update and health check;
- explicit confirmation that v2.0 has no diff.

## Rollout and Recovery

- Implement behind the existing v2.3 isolation boundary; no new cross-version flag is needed.
- Keep timestamped backups of every existing file before modification.
- Use a disposable v2.3 fixture for Save/reload migration QA.
- If canonical editor behavior fails, the frontend legacy renderer continues serving stored pages and the backed-up v2.3 files provide a recovery point.
- Do not remove compatibility code in the same change that introduces migration.

## Acceptance Criteria

- Every new Flexbox visual column is a real child Container node.
- Existing v2.3 `columns[]` pages open with identical content and layout without automatic persistence.
- Saving a migrated page writes canonical `children[]` and reopening it preserves the result.
- Child Containers expose normal Container selection, settings, nesting, duplicate, delete, and drag/drop behavior.
- Edge resize works between adjacent child Containers and stores responsive width on the active device.
- Switching between Grid and Flexbox never drops or relocates children.
- Canvas preview, saved state, and frontend output remain aligned.
- All scoped automated and browser checks pass.
- Page Builder v2.0 remains unchanged.
