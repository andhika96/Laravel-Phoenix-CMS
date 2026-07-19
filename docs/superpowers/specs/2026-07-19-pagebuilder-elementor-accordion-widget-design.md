# PageBuilder Elementor Accordion Widget and Shared Widget Advanced Engine Design

## Context

The project goal is to make `pagebuilder_elementor` behave as closely as practical to the current Elementor Flexbox demo at `https://playground.elementor.com/demo/flexbox/`.

The live Elementor audit and the supplied screenshots show Accordion as a nested interactive widget: each accordion item owns its own child dropzone, the open/closed state is independent from the repeater item being edited in the sidebar, and the widget exposes complete Content, Style, and Advanced tabs.

The project already has a stable nested `Tabs` implementation with recursive children, targeted `+ Add`, nested Grid rerouting, canvas preview, and frontend rendering. Accordion should reuse those proven traversal and insertion patterns without being implemented as a vertical Tabs skin.

Although the Elementor demo lists Accordion under `General`, this project will place it under `Advanced` as explicitly requested.

## Approved Direction

Implement Accordion as a dedicated widget and add a reusable Advanced-control engine for widgets.

Accordion-specific Content and Style controls remain owned by Accordion. Generic Advanced controls must be reusable by Accordion and later widgets instead of being copied into Accordion-only markup.

`Display Conditions` and `Cache Settings` are functional generic capabilities. `Animate With AI` is represented as an unavailable external integration because this application does not have Elementor AI services or credentials.

## Goals

- Add an `Advanced` toolbox category containing the new Accordion widget.
- Match the audited Elementor Accordion Content and Style controls.
- Give every accordion item an independent nested child tree.
- Support editor and frontend open/close behavior, including one-open and multiple-open modes.
- Preserve a clear separation between the item edited in the sidebar and items expanded on the canvas.
- Provide semantic, accessible frontend markup and optional FAQ schema.
- Add reusable Advanced controls and runtime behavior for widgets.
- Keep editor preview, saved node data, frontend Blade output, responsive behavior, and tests in parity.
- Preserve the stabilized Tabs behavior and existing builder data.

## Non-goals and External Boundaries

- Do not move the existing Tabs widget or redesign its approved controls.
- Do not change non-pagebuilder CMS or theme pages.
- Do not connect to Elementor cloud services.
- `Animate With AI` will not generate animations. The control may be shown disabled with a concise external-service explanation.
- Display Conditions are local page-builder conditions, not an attempt to reproduce Elementor Pro account/licensing behavior.
- Cache Settings use the Laravel application cache and content-hash invalidation, not Elementor's WordPress cache implementation.

## High-level Architecture

The feature has five coordinated layers:

1. **Node model and traversal** in `public/js/pagebuilder_elementor/app.js`.
2. **Accordion canvas shell** in a new `public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue`.
3. **Reusable widget Advanced controls** registered by the existing Vue app.
4. **Frontend rendering and runtime behavior** in the recursive Blade renderer and pagebuilder frontend assets.
5. **Focused feature tests** covering data, editor controls, nested insertion, output, accessibility, and runtime markers.

Accordion nodes remain normal widget nodes, but add `accordionItems[].children` in the same spirit as `tabItems[].children`.

## Data Model

### Accordion node

```js
{
	id,
	type: 'accordion',
	label: 'Accordion',
	labelSuffix: '',
	settings: accordionWidgetDefaults(),
	accordionItems: [
		{
			id,
			title: 'Item #1',
			cssId: '',
			children: [],
		},
	],
}
```

The default node contains three items. Item identifiers and all descendant node identifiers are regenerated during duplication.

At least one accordion item must remain. Removing the final item is disabled.

### Persisted Content settings

- `itemPosition` plus tablet/mobile variants: `start`, `center`, `end`, `stretch`
- `iconPosition` plus tablet/mobile variants: `start`, `end`
- `expandIconSource`: `none`, `library`, `svg`
- `expandIconClass`
- `expandIconSvg`
- `collapseIconSource`: `none`, `library`, `svg`
- `collapseIconClass`
- `collapseIconSvg`
- `titleTag`: `h1`, `h2`, `h3`, `h4`, `h5`, `h6`, `div`, `span`, `p`
- `faqSchema`: boolean
- `defaultState`: `first-expanded`, `all-collapsed`
- `maxExpanded`: `one`, `multiple`
- `animationDuration`: milliseconds, default `400`

Icon-library values use the builder's existing local Font Awesome 5.15.3 picker. Uploaded SVG is sanitized before storage or rendering; raw untrusted SVG markup must never be injected directly.

### Editor-only runtime state

Do not persist transient canvas interaction state in the saved layout.

The root Vue app maintains a runtime map keyed by Accordion node ID:

```js
{
	editingItemId: '',
	expandedItemIds: [],
	transitioningItemIds: [],
}
```

- `editingItemId` controls which repeater fields are visible in the sidebar.
- `expandedItemIds` controls the canvas panels.
- `transitioningItemIds` prevents conflicting rapid-toggle transitions.

Selecting a repeater row must not open or close a canvas panel. Clicking a canvas header may select the Accordion node but must not change the repeater row unless the user explicitly asks to edit that item.

When an Accordion first appears in the editor, runtime state is derived from `defaultState`. Runtime state is rebuilt safely after undo/redo, load, duplication, and item deletion.

## Tree Traversal and Nested Insertion

All recursive builder operations must understand `accordionItems[].children`:

- normalization
- `findById`
- tree walking
- duplicate and ID regeneration
- deletion
- responsive seeding
- copy/paste if present
- save/load normalization
- nested selection and navigator labels

Extend targeted insertion with:

```js
{
	type: 'accordion',
	nodeId,
	itemId,
}
```

The `+ Add` control and drag/drop behavior must insert into the exact accordion item, never the first item or currently edited item by accident.

Nested zones accept every node type that the builder can safely render recursively. Target validation must be generalized rather than adding an Accordion-only exception. Existing sequential Grid rules and nested drop rerouting remain enforced.

Dragging a nested item between accordion panels, Tabs, and Grid columns must remove it from the original array once and add it to the destination once. Failed target validation must leave the source tree unchanged.

## Sidebar Content Tab

### Layout section

- Sortable Items repeater.
- Three default items.
- Each row has summary text, Duplicate, and Delete actions.
- `Add Item` appends and selects a new item for editing.
- Expanded row fields: `Title` and `CSS ID`.
- Responsive Item Position: Start, Center, End, Stretch.
- Responsive Icon Position: Start, End.
- Expand icon: None, Upload SVG, Icon Library.
- Collapse icon: None, Upload SVG, Icon Library.
- Title HTML Tag: H1-H6, div, span, p.
- FAQ Schema toggle.

Repeater reorder changes visual and frontend order while preserving item IDs and children.

### Interactions section

- Default State: First Expanded or All Collapsed.
- Max Items Expanded: One or Multiple.
- Animation Duration: range and numeric value in milliseconds, default `400`.

Changing Default State resets the editor runtime preview only when the user changes that control; it must not continuously override manual canvas interaction.

## Canvas Interaction Design

- Each header is clickable and keyboard-operable.
- In `one` mode, opening one item begins closing the previously open item.
- In `multiple` mode, each item toggles independently.
- In `all-collapsed` mode, the initial expanded set is empty.
- During close animation, `aria-expanded` becomes false immediately while the panel remains measurable until its height transition finishes.
- Rapid repeated clicks must settle in a deterministic final state and must not leave an item stuck at a fixed pixel height.
- Reduced-motion users receive an immediate state change when `prefers-reduced-motion: reduce` is active.
- Each expanded panel renders its own nested draggable zone and targeted `+ Add` affordance.
- Empty panels show the builder's standard `Drag widget here` hint.
- Closed panels keep their data but do not expose active drop targets.

The animation uses measured panel height (`scrollHeight`) and a temporary inline height during transition. Final open panels return to `height:auto`; final closed panels are hidden from layout and accessibility traversal.

## Style Tab

### Accordion section

- Responsive Space Between Items.
- Responsive Distance from Content.
- Normal, Hover, and Active state tabs.
- Per state Background Type: Classic or Gradient.
- Classic: Color.
- Gradient: first color/location, second color/location, Linear/Radial, Angle for Linear, and Position for Radial.
- Per state Border Type: Default, None, Solid, Double, Dotted, Dashed, Groove.
- Border Width and Color when a visible type is selected.
- Responsive Border Radius.
- Responsive Padding.

### Header section

- Title Typography.
- Title states: Normal, Hover, Active.
- Per title state: Color, Text Shadow, Text Stroke.
- Responsive Icon Size.
- Responsive Icon Spacing.
- Icon states: Normal, Hover, Active.
- Per icon state: Color.

### Content section

- Background Type: Classic or Gradient.
- Classic: Color.
- Gradient: two colors and locations, Linear/Radial, Angle or Position.
- Border Type: Default, None, Solid, Double, Dotted, Dashed, Groove.
- Border Width and Color when active.
- Responsive Border Radius.
- Responsive Padding.

Accordion-specific Style values use an `accordion`, `header`, or `content` prefix so they cannot collide with generic Advanced background and border settings applied to the outer widget wrapper.

## Shared Widget Advanced Engine

The engine provides defaults, normalization, responsive lookup, editor controls, editor preview styles, frontend styles/classes, and safe attributes. It uses the builder's existing flat setting convention so current layout helpers can be reused and future widget migration remains possible.

### Layout

- Responsive Margin and Padding with linked/unlinked sides and supported units.
- Responsive Width: Default, Full Width, Inline, Custom.
- Custom Width units: px, %, em, rem, vw, custom CSS unit.
- Responsive Align Self: Start, Center, End, Stretch.
- Responsive Order: Start, End, Custom; Custom reveals numeric order.
- Responsive Size: None, Grow, Shrink, Custom; Custom reveals Flex Grow and Flex Shrink.
- Position: Default, Absolute, Fixed.
- Horizontal orientation: Left/Right plus responsive X offset.
- Vertical orientation: Top/Bottom plus responsive Y offset.
- Responsive Z-Index.
- CSS ID and CSS Classes.

CSS IDs are validated and emitted as the real element `id`, not a `data-css-id` substitute. Duplicate IDs in the current layout produce an editor warning and only the first valid occurrence may keep the ID on rendered output.

### Display Conditions

Display Conditions are represented as an ordered list of condition groups. Conditions inside a group use AND; groups use OR.

Initial local condition sources:

- page ID or page slug
- authenticated/guest state
- user role
- current date/time range
- desktop/tablet/mobile device class

Each condition supports Include or Exclude. Empty or invalid rules are ignored with an editor warning. Conditions are evaluated on the server for frontend output; device-only conditions may also add guarded client classes when server detection is inconclusive.

### Cache Settings

- `default`: inherit the pagebuilder cache policy.
- `inactive`: always render the node normally.
- `active`: cache the rendered widget fragment.

Active fragment cache keys include the node payload hash, renderer version, locale, authenticated visibility context needed by conditions, and relevant page identity. Editing content naturally invalidates the fragment through the payload hash. A bounded application-configured TTL is used; no permanent cache entry is created.

The editor canvas never serves cached markup.

### Motion Effects

- `Animate With AI`: disabled external-integration affordance.
- Scrolling Effects toggle.
- Vertical Scroll: Up/Down, Speed, Viewport start/end.
- Horizontal Scroll: Left/Right, Speed, Viewport start/end.
- Transparency: Fade In, Fade Out, Fade Out In, Fade In Out; Level and Viewport.
- Blur: the same four directions; Level and Viewport.
- Rotate: Left/Right; Speed and Viewport.
- Scale: Up, Down, Down-Up, Up-Down; Speed and Viewport.
- Apply Effects On: Desktop, Tablet Portrait, Mobile Portrait.
- Effects Relative To: Default, Viewport, Entire Page.
- Mouse Track: Direct/Opposite and Speed.
- 3D Tilt: Direct/Opposite and Speed.
- Sticky: None, Top, Bottom.
- Sticky devices, responsive Sticky Offset, Effects Offset, Anchor Offset, and Stay In Column.
- Responsive Entrance Animation presets matching the audited Fade, Zoom, Bounce, Slide, Rotate, attention, Light Speed In, and Roll In families.
- Entrance duration: Slow, Normal, Fast.
- Entrance delay in milliseconds.

Motion runtime uses one shared pagebuilder script, one scroll listener coordinated through `requestAnimationFrame`, and `IntersectionObserver` for entrance visibility. It must not add one global scroll listener per widget. Device switches and reduced-motion preferences disable effects cleanly.

### Transform

- Normal and Hover state tabs.
- Rotate and optional 3D Rotate X/Y with Perspective.
- Responsive Offset X/Y.
- Responsive Scale.
- Responsive Skew X/Y.
- Flip Horizontal and Vertical.
- Hover Transition Duration.
- X Anchor: Left, Center, Right.
- Y Anchor: Top, Center, Bottom.

Transform functions are composed in a stable order so enabling one control does not overwrite another.

### Background

- Normal and Hover states.
- Classic and Gradient types.
- Classic Color and optional Image.
- Image Position, Attachment, Repeat, and Size.
- Gradient colors/locations, Linear/Radial, Angle/Position.
- Hover Background Transition Duration.

### Border

- Normal and Hover states.
- Border Type, Width, and Color.
- Responsive Border Radius.
- Box Shadow Color, Horizontal, Vertical, Blur, Spread, and Outline/Inset.
- Hover Transition Duration.

### Mask

- Enable/disable Mask.
- Shape: Circle, Flower, Sketch, Triangle, Blob, Hexagon, Custom.
- Custom Image or sanitized SVG.
- Responsive Size: Fit, Fill, Custom; Custom reveals Scale.
- Responsive nine-point Position or Custom X/Y.
- Responsive Repeat: No-repeat, Repeat, Repeat-x, Repeat-y, Round, Space.

Mask uses CSS mask properties with the prefixed equivalents required by Chromium/WebKit. An unsupported browser receives the unmasked widget rather than hidden content.

### Responsive, Attributes, and Custom CSS

- Hide on Desktop, Tablet Portrait, Mobile Portrait.
- Custom Attributes entered as `key|value`, normalized internally to the existing attribute array.
- Reject event-handler attributes, `style`, unsafe URL protocols, and attempts to override managed `id`/`class` attributes.
- Custom CSS supports Elementor-style `selector` replacement scoped to the widget node ID.
- CSS is sanitized according to the builder's existing custom-CSS policy and emitted only inside the widget scope.

## Frontend Markup and Accessibility

Use semantic `details`/`summary` markup with a dedicated animated content wrapper.

Each item receives stable IDs derived from the node and item IDs:

- header/summary ID
- content panel ID
- `aria-expanded`
- `aria-controls`
- `aria-labelledby`

The runtime script supports Enter and Space through native summary behavior and adds optional Arrow Up/Down, Home, and End navigation across headers without trapping focus.

In `one` mode, opening an item closes any other open item using the configured transition. In `multiple` mode, native independent toggling is preserved through the same animation controller.

If JavaScript fails, the semantic details elements remain usable.

## FAQ Schema

When FAQ Schema is enabled, frontend output includes one JSON-LD `FAQPage` block for the Accordion.

- Each item title becomes `Question.name`.
- Answer text is extracted recursively from safe textual child-node fields.
- Markup, scripts, controls, and unsupported media-only data are excluded.
- Empty answers are omitted from the schema while visual items remain rendered.
- JSON is emitted with safe Laravel/PHP JSON encoding; user content is never concatenated into a script string manually.

The schema is output only on frontend rendering, not in the editor canvas.

## Error Handling and Data Compatibility

- Missing or malformed `accordionItems` normalize to at least one valid item.
- Invalid item IDs, CSS IDs, icon values, units, enum values, durations, and Advanced values fall back safely.
- Existing saved layouts are not rewritten merely by opening the editor.
- Failed SVG sanitization produces no icon and an editor notice.
- Failed cache access falls back to normal rendering.
- Failed Display Condition evaluation defaults to the safer hidden result for a malformed explicit restriction, while an empty condition set renders normally.
- Animation cleanup runs when nodes are removed or the preview device changes.

## Expected File Areas

New files are expected for:

- Accordion preview component under `public/js/pagebuilder_elementor/widgets/advanced/`
- reusable widget Advanced controls/runtime helpers where a focused extraction reduces duplication
- focused Accordion and shared-Advanced feature tests

Existing files expected to change include:

- `public/js/pagebuilder_elementor/app.js`
- `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- `public/assets/css/pagebuilder_elementor.css`
- `public/assets/css/frontend_elementor.css`
- the pagebuilder editor/frontend asset shells if new runtime files are introduced

Before any existing file is modified, create timestamped backups as required by the workspace workflow.

## Verification Strategy

### Automated

- Start with failing feature tests for toolbox registration, default data, full settings markers, traversal, targeted insertion, frontend markup, accessibility, schema, and Advanced settings.
- Add normalization cases for malformed and legacy data.
- Add renderer cases for first-expanded/all-collapsed and one/multiple modes.
- Add FAQ schema and attribute-sanitization cases.
- Add Display Condition and cache-key behavior cases.
- Run focused Accordion tests after each coherent slice.
- Run the full `PageBuilderElementor` suite.
- Run JavaScript syntax checks, Blade/PHP lint checks, and `git diff --check`.

### Browser

Validate in a real authenticated builder session:

- drag Accordion from Advanced to canvas
- confirm three default items and first-expanded behavior
- edit, reorder, add, duplicate, and remove items
- confirm duplicate descendants have regenerated IDs
- insert widgets and layout nodes into every item through drag/drop and targeted `+ Add`
- move nested nodes between Accordion, Tabs, and Grid targets
- confirm sidebar editing does not toggle canvas state
- verify one and multiple expansion modes, all-collapsed state, rapid clicks, and 400ms animation
- verify Content, Style, and Advanced controls update the canvas immediately
- verify desktop, tablet, and mobile responsive overrides
- save, reload, and verify frontend parity
- verify keyboard navigation, reduced motion, custom attributes/CSS, FAQ schema, conditions, and cache behavior
- inspect console and network output for errors

## Completion Criteria

The feature is complete only when:

- Accordion appears under Advanced and works as a nested widget.
- All agreed Content and Style controls have visible editor and frontend effects.
- Shared Advanced controls have functional preview/frontend behavior, except the explicitly disabled external AI integration.
- Editor runtime state is not persisted as layout data.
- Tabs and existing widgets remain regression-free.
- Focused and full automated tests pass.
- Authenticated browser verification passes in editor and frontend at all three responsive sizes.
