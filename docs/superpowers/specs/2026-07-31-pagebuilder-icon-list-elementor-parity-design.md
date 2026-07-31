# Page Builder Icon List Elementor Parity Design

## Goal

Add `General > Icon List` using the current modular Page Builder Elementor architecture, aligned with Elementor's official Icon List controls and behavior across editor state, canvas preview, persistence, and frontend rendering.

## Scope

- Content: Default/Inline layout, three default repeatable items, item Text/Icon/Link options, add/duplicate/remove, and Apply Link On.
- Style > List: responsive Space Between and Alignment, plus conditional Divider Style, Weight, Width/Height, and Color.
- Style > Icon: Normal/Hover color and transition, responsive Size, Gap, Horizontal Alignment, Vertical Alignment, and Adjust Vertical Position.
- Style > Text: typography, text shadow, Normal/Hover color and transition.
- Advanced: the existing complete shared widget controls, including Display Conditions and Cache Settings.
- Frontend: semantic list markup, safe URLs/attributes/icon classes, responsive CSS, hover states, and shared advanced resolution.

## Architecture

- Register the widget in `config/pagebuilder_elementor_widgets.php`.
- Add a focused modular definition, settings panel, and canvas under `public/js/pagebuilder_elementor/widgets/general/icon-list/`.
- Extend the existing complex-widget runtime in `app.js` with defaults, normalization, repeater actions, and item-specific icon-library targeting.
- Add a dedicated Blade renderer and route cached rendering through `render_node.blade.php`.
- Add only the base frontend CSS needed by the renderer; widget-specific responsive values remain scoped to each rendered widget.

## Data Contract

Each item has a stable `id`, `text`, `iconStyle`, `iconName`, `iconClass`, `linkUrl`, `linkTarget`, `linkNofollow`, and `linkCustomAttributes`. Responsive settings use the established `Base`, `BaseTablet`, and `BaseMobile` convention. Invalid enums, CSS lengths, icon classes, URLs, and attributes are normalized or rejected before rendering.

## Verification

- Feature/parity tests first, observed failing before implementation.
- PHP syntax, JS syntax, focused PHPUnit, full PageBuilderElementor suite, and `git diff --check`.
- Browser-level checks when the local browser control surface is available; otherwise explicitly report static/runtime-test coverage versus unverified visual interaction.
