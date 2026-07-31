# Page Builder Basic Gallery Elementor Parity Design

## Goal

Add a dedicated `Basic Gallery` widget to the Page Builder Elementor `General`
category with editor state, sidebar controls, canvas preview, persistence,
Blade/frontend output, responsive behavior, and runtime interactions aligned.

## Reference

- Elementor demo: `https://playground.elementor.com/demo/flexbox`
- Supplied screenshots for Content, Style > Images, Style > Caption, and
  Advanced.
- Existing local `Image Carousel` widget for gallery media state, rendition,
  safe URL handling, and UI-control conventions.

## Scope

### Content > Basic Gallery

- Multi-image selection, ordering, removal, and attachment metadata.
- Image Resolution with the shared named sizes and bounded Custom width/height.
- Columns, responsive across Desktop, Tablet, and Mobile.
- Caption: None or Attachment Caption.
- Link: None, Media File, or Attachment Page.
- Lightbox: Default, Yes, or No, conditional on Media File.
- Order By: Default or Random.

### Style > Images

- Gap: Default, No Gap, Narrow, Extended, Wide, or Custom.
- Custom responsive gap with supported Page Builder units.
- Border Type: Default, None, Solid, Double, Dotted, Dashed, or Groove.
- Conditional border width and color.
- Responsive four-side Border Radius.

### Style > Caption

This group is visible only when Caption is not None.

- Responsive alignment: Left, Center, Right, or Justify.
- Text color.
- Shared typography control.
- Shared text-shadow control.
- Responsive spacing.

### Advanced

Use the shared Advanced control implementation with:

- Layout, including Display Conditions and Cache Settings.
- Motion Effects.
- Transform.
- Background.
- Border.
- Mask.
- Responsive.
- Attributes.
- Custom CSS.

## Architecture

`Basic Gallery` is a dedicated complex widget module:

- `definition.js` registers the widget.
- `Settings.vue` owns the sidebar UI.
- `Canvas.vue` renders the editor grid preview.
- `app.js` owns normalized defaults and exposes the existing gallery media
  helpers.
- `render_basic_gallery.blade.php` owns safe frontend markup.
- `frontend-runtime.js` reuses a shared lightbox opener for gallery media.

The implementation reuses the existing Image Carousel gallery item schema:

```text
{ id, url, alt, title, caption, description }
```

It does not reuse carousel navigation, autoplay, pagination, transforms, or
slide state.

## Data Flow

1. Registry creates `basic_gallery` defaults through the complex runtime.
2. The gallery picker appends normalized attachment objects to
   `settings.images`.
3. Settings update reactive widget state.
4. Canvas reads the same state to render a responsive CSS grid.
5. Saved JSON retains the normalized settings.
6. Blade validates enums, units, URLs, and attachment metadata before output.
7. Frontend lightbox opens only for safe media URLs when enabled.

## Safety and Error Handling

- Reject unsafe image and link schemes.
- Escape all attachment metadata and attributes.
- Clamp columns and custom rendition dimensions.
- Ignore malformed gallery entries rather than failing the entire widget.
- Render a stable empty state when no valid images remain.
- Respect reduced-motion behavior for the shared lightbox transition.

## Testing

- A dedicated parity test covers registry, defaults, settings, canvas,
  renderer, responsive values, safe URLs, lightbox contract, and custom image
  rendition.
- The generic complex-widget contract includes `basic_gallery`.
- The full Page Builder regression suite must remain green.
- Runtime QA covers toolbox registration, drag/drop, all three sidebar tabs,
  responsive controls, empty state, populated grid when authenticated media is
  available, and browser console errors.

## Explicit Non-Goals

- No carousel arrows, dots, autoplay, infinite loop, swipe, or slide animation.
- No masonry or justified-gallery engine.
- No external gallery dependency.
- No unrelated refactor of Image Carousel.

