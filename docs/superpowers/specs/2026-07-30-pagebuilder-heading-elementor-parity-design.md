# Page Builder Heading Elementor Parity Design

## Goal

Bring the modular `heading` widget to the option set and interaction model observed in the official Elementor Flexbox playground while keeping editor state, canvas preview, persisted JSON, Blade frontend output, and shared advanced runtime aligned.

## Approved Direction

Use the existing modular widget boundary for Heading-specific Content and Style controls, and reuse the existing shared control/runtime boundary for Advanced controls. Extend shared controls only where the Elementor Heading audit exposed capabilities that are genuinely generic to all widgets.

## Content Contract

- `title`/legacy `text` with Heading-compatible dynamic bindings.
- Link URL, target, nofollow, custom attributes, and link dynamic binding.
- HTML tag whitelist: `h1`-`h6`, `div`, `span`, `p`.
- A configured link wraps the title inside the selected heading tag.

## Style Contract

- Responsive alignment: left, center, right, justify.
- Complete typography control: family, size, weight, transform, style, decoration, line height, letter spacing, and word spacing.
- Responsive text stroke width and stroke color.
- Text shadow.
- Blend mode.
- Normal text color.
- Hover link color and transition duration, applied only when a link exists.

## Advanced Contract

Heading uses `AdvancedControls.vue` and `WidgetAdvancedStyleResolver` for:

- Layout, responsive margin/padding/width, flex/grid item placement, positioning, z-index, CSS ID/classes.
- Display conditions and cache settings.
- Motion effects, transform Normal/Hover, background Normal/Hover, border Normal/Hover, mask, responsive visibility, attributes, and custom CSS.
- Existing Accordion and Image Box behavior must not regress when the shared engine is extended.

## Rendering Structure

The frontend output uses a widget wrapper for Advanced styles and attributes, matching Elementor's separation between widget-level controls and the inner heading title. The selected tag is the child `.elementor-heading-title`; link settings apply to its child anchor. Canvas uses the existing BuilderNode shell as the Advanced wrapper and `Canvas.vue` renders the inner title.

Legacy `text`, `align`, `color`, and `cssClass` values remain readable. Normalization upgrades them into the new keys without destroying saved page data.

## Safety

- Escape the title by default; dynamic values are resolved through the existing trusted dynamic-value path.
- Sanitize tag, URL attributes, CSS ID/class tokens, and custom attributes through existing shared render guards.
- Do not execute event-handler attributes or unsafe URL schemes.

## Verification

- Focused RED/GREEN tests for Heading defaults, controls, responsive canvas contract, link output, typography/effects, Advanced resolver integration, and backward compatibility.
- Full `PageBuilderElementor` regression suite.
- Browser verification in Chrome for Content, Style, Advanced, desktop/tablet/mobile preview, save/reload, and frontend rendering.
- Design QA compares supplied Elementor screenshots with the implemented panel states at the same desktop viewport.
