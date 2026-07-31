# Icon Box Elementor Parity Design

## Goal

Add a standalone `icon_box` widget to the General category. It reuses the existing modular widget runtime and shared controls, but owns its definition, settings panel, canvas renderer, and frontend Blade renderer.

## Reference contract

The live Elementor playground was audited on 2026-07-30.

- Content / Icon Box: icon library picker, View (`Default`, `Stacked`, `Framed`), conditional Shape (`Square`, `Rounded`, `Circle`), Title, Description, Link, and Title HTML Tag.
- Style / Box: responsive Icon Position, Alignment, Icon Spacing, and Content Spacing.
- Style / Icon: Normal and Hover states; Primary Color; conditional Secondary Color; responsive Size and Rotate; conditional Padding; conditional Framed Border Width; responsive Border Radius; Hover Animation on Hover.
- Style / Content: Title color, typography, text stroke, text shadow; Description color, typography, and text shadow.
- Advanced: reuse the shared Elementor-parity controls so editor state, canvas shell, persistence, cache/display conditions, and frontend resolver stay aligned.

## Architecture

- Register `icon_box` in `config/pagebuilder_elementor_widgets.php` as a General toolbox widget.
- Add a dedicated module under `public/js/pagebuilder_elementor/widgets/general/icon-box/`.
- Extend `PageBuilderElementorComplexWidgetRuntime` with defaults and normalization, based on Image Box patterns and the existing Font Awesome icon library.
- Add the widget to the shared Advanced shell and frontend fragment-cache dispatch.
- Render the frontend through `pagebuilder_elementor.partials.render_icon_box` and add scoped frontend CSS.

## State model

The persisted state includes icon identity/class, view/shape, title/description/link/tag, responsive layout values, Normal/Hover icon state values, typography/text effects, and all shared Advanced fields. Unsafe URLs, class tokens, attributes, tags, CSS values, and icon classes are normalized or rejected in both canvas and frontend output.

## Verification

Start with a failing feature test for registration and module contracts. Then verify focused PHP tests, the full Page Builder suite, source syntax/diff checks, editor runtime, save/reload persistence, canvas computed styles, frontend rendering, responsive behavior, hover behavior, and a same-viewport visual comparison against the live Elementor reference.
