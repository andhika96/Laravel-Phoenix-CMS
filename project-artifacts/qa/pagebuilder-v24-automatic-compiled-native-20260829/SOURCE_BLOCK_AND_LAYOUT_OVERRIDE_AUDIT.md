# Source block and layout override audit

Date: 2026-08-29
Project: `D:\Laragon\www\laravel-13-phoenix`
Scope: Measured layout column override and manual source-block list in the v2.4 Compiled Native workflow.

## Finding 1 — Stack deliberately forces one column

The Measured layout screen shows `Mode = Stack`, `Columns = 1`, and `Rule = user.override`. The current correction handler accepts the column input, then immediately normalizes a Stack back to one column:

```js
if (next.mode === 'stack') {
    next.columns = 1;
    next.tracks = ['1fr'];
}
```

Therefore entering `2` while the mode remains Stack cannot persist. This is not a server reset or an import failure. To represent two columns, the current workflow requires changing Mode to `Grid` or `Flex` first, then setting Columns to `2`. The UI does not explain this dependency clearly, so the input feels broken.

## Finding 2 — Source IDs are unique; displayed text is hierarchical

The real source `E:\Apps\Laragon\www\ceo-masters\index.html` was measured through the active importer. Section `home` contains 38 unique measured nodes. The duplicate-looking groups are aggregate `textContent` values:

- `home`, `node-33`, and `node-34` each expose text collected from their descendants.
- `node-62` and `node-65` each expose the same nested date-card text.

The IDs are distinct and parent relationships are preserved. The importer currently walks every non-ignored body element, including layout wrappers, containers, semantic content elements, inline spans, line breaks, and decorative icons. The UI then exposes every non-root node as a selectable source block.

## Finding 3 — Role labels are technically derived but not yet content-oriented

Current role derivation is:

- `layout` when the measured node uses `grid`, `flex`, or `inline-flex`;
- `container` when the node has any element children and is not a layout node;
- `content` only when it has no element children.

That rule classifies an `h1` containing `<br>` and `<span>` as `container`, and an anchor containing an icon as `layout` if the anchor's computed display is flex. This explains labels such as `DIV · LAYOUT`, `DIV · CONTAINER`, and repeated aggregate copy in the mapping screen. It does not indicate duplicate data.

## Impact

- The column behavior is a UX/normalization issue in the review layer.
- The source-block behavior is a DOM-to-mapping granularity issue, not a browser extraction duplicate.
- The measured CSS, rectangles, source IDs, attributes, and parent links remain available for traceability.
- No Page Builder responsive engine, layout module, widget renderer, v2.3 code, Save endpoint, or page data was changed during this audit.

## Recommended next correction

Keep the raw measurement tree for traceability, but add a normalized mapping projection:

1. Make the Mode/Columns dependency explicit: Stack locks Columns to `1`; changing Columns above `1` should either switch to Grid/Flex with confirmation or show an inline validation message.
2. Keep layout/container wrappers in the structural tree, but show child count and box-model summary instead of repeating aggregate descendant text.
3. Treat semantic content elements (`h1`–`h6`, `p`, `a`, `img`, `button`, list/form elements) as content blocks even when they contain inline children.
4. Keep inline descendants such as `span`, `br`, and decorative `i` under their parent content block unless the user explicitly expands the raw DOM details.
5. Keep source IDs unique and expose an explicit parent/children tree so a user can distinguish `node-33` from its child `node-34`.

This audit intentionally does not implement those corrections until the mapping projection and Stack/Grid/Flex interaction are approved.
