# PageBuilder Elementor Image Box Widget Design

## Context

The long-term goal is for `pagebuilder_elementor` to match the Elementor Flexbox demo as closely as practical across widget availability, settings, editor preview, responsive behavior, and frontend output.

This design adds a dedicated `Image Box` widget using the supplied Elementor screenshots as the visual source. It follows the existing Accordion implementation for panel rhythm, responsive controls, state tabs, shared typography, shared Advanced settings, editor/frontend parity, accessibility, and focused tests.

Image Box is a composite leaf widget. It owns an image, title, description, and optional link, but it does not own nested builder children.

## Approved Direction

Implement Image Box as a dedicated `image_box` widget in the `General` toolbox category.

Use a dedicated editor preview component and a dedicated frontend Blade partial. Reuse the proven Accordion shared controls instead of composing the existing Image, Heading, and Text Editor widgets internally.

Extract only the reusable controls required by this feature:

- a generic link control
- a reusable CSS filter control
- a prefix-aware typography control
- a shared dynamic-tag binding engine
- an image-rendition resolver for CKFinder/File Manager images

Do not turn the entire page builder into a schema-driven form engine in this feature. That broader refactor would create unnecessary regression risk.

## Goals

- Add `Image Box` under the `General` toolbox category.
- Match all visible Content and Style controls in the supplied Elementor screenshots.
- Use the same `Content / Style / Advanced` tab shell and spacing rhythm as Accordion.
- Keep form rows readable, evenly spaced, and responsive without cramped labels or controls.
- Reuse the shared widget Advanced engine already introduced by Accordion.
- Make every supported setting functional in the editor canvas and frontend output.
- Add reusable link, typography, CSS-filter, dynamic-tag, and media-rendition foundations for later widgets.
- Preserve existing Image, Heading, Text Editor, Tabs, Accordion, Container, and Grid behavior.
- Keep saved data compatible and safely normalized.
- Add focused automated coverage and real-browser parity verification.

## Non-goals and Boundaries

- Do not rebuild Image Box from nested Image/Heading/Text Editor nodes.
- Do not modify the existing basic Image widget behavior unless extracting a backward-compatible shared helper.
- Do not redesign Accordion or change its approved UI.
- Do not introduce a repository-wide schema-driven settings renderer.
- Do not add unrelated Elementor widgets in the same implementation.
- Do not connect to Elementor cloud services.
- Dynamic tags are local Laravel CMS bindings, not Elementor cloud/WordPress bindings.
- Image sizes are generated or resolved by this Laravel application, not WordPress attachment metadata.
- `Animate With AI` remains an unavailable external integration, consistent with Accordion.

## Visual and Interaction Contract

### Panel shell

Image Box uses the same editor shell as Accordion:

- top tabs: `Content`, `Style`, `Advanced`
- tab minimum height: 48px
- icon and label vertically centered
- first section starts with comfortable space below the top tabs
- section headers use the same 48px accordion-summary rhythm
- opened section bodies use consistent top and bottom padding
- labels never collide with responsive icons, units, or action buttons
- conditional controls remain hidden until their parent option activates them

### Form rhythm

- Standard vertical space between full form groups: 12-14px.
- Space from label row to its control: 6-8px.
- Input/select minimum height follows the existing pagebuilder form controls.
- Responsive device picker and unit selector sit in the label-row tool area.
- Slider controls use one row containing slider, numeric value, and unit where applicable.
- Four-side controls use four joined numeric cells plus the link/unlink cell.
- `Normal / Hover` tabs are two equal-width columns.
- Popovers are used for dense Typography and CSS Filters rather than expanding long control stacks inline.
- The panel preserves the existing light page-builder design system; it does not copy Elementor's dark shell.

### Responsive control behavior

Responsive settings use a per-control device picker, matching the corrected Accordion behavior.

- Desktop is the base value.
- Tablet falls back to Desktop when empty.
- Mobile falls back to Tablet, then Desktop, when empty.
- Switching the editor device updates the control value and canvas preview immediately.
- No global Desktop/Tablet/Mobile row is added to Content or Style.

## High-level Architecture

The feature has seven coordinated layers:

1. Widget defaults and normalization in `public/js/pagebuilder_elementor/app.js`.
2. Dedicated canvas preview in `public/js/pagebuilder_elementor/widgets/general/ImageBox.vue`.
3. Image Box Content and Style controls in the existing sidebar app.
4. Reusable shared controls for links, typography, CSS filters, dynamic tags, and media renditions.
5. Shared widget Advanced controls from `AdvancedControls.vue`.
6. Dedicated semantic frontend rendering in `render_image_box.blade.php`.
7. Focused tests plus editor/frontend browser verification.

## Data Model

### Node shape

```js
{
	id,
	type: 'image_box',
	label: 'Image Box',
	labelSuffix: '',
	settings: imageBoxWidgetDefaults(),
}
```

`imageBoxWidgetDefaults()` merges widget-specific defaults with `widgetAdvancedDefaults()`.

### Content settings

- `imageUrl`
- `imageAlt`
- `imageResolution`: `thumbnail`, `medium`, `medium_large`, `large`, `1536x1536`, `2048x2048`, `full`
- `title`: default `This is the heading`
- `description`: Elementor-like placeholder description
- `linkUrl`
- `linkNewTab`
- `linkNofollow`
- `linkAttributes`: normalized array of safe name/value pairs
- `titleTag`: `h1`, `h2`, `h3`, `h4`, `h5`, `h6`, `div`, `span`, `p`; default `h3`
- `dynamicBindings`: normalized field-to-binding map for `imageUrl`, `title`, `description`, and `linkUrl`

### Box style settings

- Responsive `imagePosition`: `left`, `top`, `right`
- Responsive `alignment`: `left`, `center`, `right`, `justify`
- Responsive `imageSpacing`
- Responsive `contentSpacing`

### Image style settings

- Responsive `imageWidth`
- `imageBorderType`: `default`, `none`, `solid`, `double`, `dotted`, `dashed`, `groove`
- `imageBorderWidth`
- `imageBorderColor`
- Responsive scalar `imageBorderRadius`
- Normal and Hover image filters:
  - blur
  - brightness
  - contrast
  - saturation
  - hue rotation
- Normal and Hover opacity
- Hover transition duration
- Hover animation preset when confirmed by the live Elementor control

If a live control cannot be confirmed from the supplied screenshots, it is not silently invented. It must be verified against the live Elementor panel before implementation is considered complete.

### Content style settings

Title:

- color
- typography values
- text stroke width and color
- text shadow

Description:

- color
- typography values
- text shadow

Title and Description typography use separate prefixed setting sets so editing one never affects the other.

### Shared Advanced settings

Image Box consumes the complete `widgetAdvancedDefaults()` contract:

- Layout
- Display Conditions
- Cache Settings
- Motion Effects
- Transform
- Background
- Border
- Mask
- Responsive visibility
- Attributes
- Custom CSS

Advanced styles apply to the outer Image Box wrapper. Widget-specific image/title/description styles apply only to their inner elements.

## Content Tab

### Image Box section

1. `Choose Image`
   - CKFinder/File Manager picker
   - visual preview
   - change and remove actions
   - safe URL fallback when CKFinder is unavailable
2. `Image Resolution`
   - uses the rendition resolver
   - defaults to `full`
3. `Title`
   - text input
   - dynamic-tag action
4. `Description`
   - multiline textarea
   - dynamic-tag action
5. `Link`
   - URL input
   - dynamic-tag action
   - link-options action
6. Link options popover
   - Open in new window
   - Add `nofollow`
   - Custom Attributes
7. `Title HTML Tag`
   - semantic tag select

## Style Tab

### Box section

- Responsive Image Position segmented control: Left, Top, Right.
- Responsive Alignment segmented control: Left, Center, Right, Justified.
- Responsive Image Spacing scalar control.
- Responsive Content Spacing scalar control.

### Image section

- Responsive Width scalar control.
- Border Type.
- Conditional Border Width and Border Color.
- Responsive scalar Border Radius.
- Normal/Hover state tabs.
- CSS Filters popover per state.
- Opacity per state.
- Hover Transition Duration.
- Hover Animation only when confirmed in the live source.

### Content section

Title controls:

- Color
- Typography popover
- Text Stroke
- Text Shadow

Description controls:

- Color
- Typography popover
- Text Shadow

## Shared Link Control

The existing Icon link behavior is extracted into a reusable control without changing Icon behavior.

The control manages:

- URL
- Open in new window
- `nofollow`
- safe custom attributes
- optional dynamic binding

Frontend link attributes reject event handlers, `style`, managed `id`/`class`, and unsafe URL protocols.

The Image Box link is emitted on the image and title, matching Elementor's structure. The description remains plain content and is not wrapped in the link.

## Shared Typography Control

The existing Accordion `TypographyControl.vue` becomes prefix-aware or model-driven while preserving Accordion behavior.

Each instance supports:

- searchable Custom Fonts and System Fonts groups
- family
- responsive size
- weight
- transform
- style
- decoration
- responsive line height
- responsive letter spacing
- responsive word spacing

Accordion continues using its current header keys through an explicit mapping. Image Box provides separate mappings for Title and Description.

## Shared CSS Filter Control

The new reusable filter control manages a complete CSS filter token without overwriting unrelated transforms or opacity:

- blur
- brightness
- contrast
- saturation
- hue rotation

It supports Normal and Hover values and emits a stable ordered filter string. Empty/default values produce no unnecessary inline filter declaration.

## Dynamic Tags Engine

Dynamic Tags are implemented as a reusable local CMS capability rather than decorative buttons.

### Initial supported sources

- current page title
- current page excerpt/summary when available
- current page featured image when available
- current page URL
- site title
- site URL
- authenticated user display name when available
- explicit fallback text or URL

### Binding model

Each supported field stores a normalized binding object:

```js
{
	source: 'page-title',
	fallback: '',
}
```

Static field values remain stored and become the fallback when a binding cannot resolve.

### Rendering rules

- Editor preview resolves against the pagebuilder edit context.
- Frontend rendering resolves server-side.
- Missing context uses the static value/fallback.
- Image and URL bindings are protocol-validated.
- Text bindings are escaped according to their output context.
- Existing layouts without `dynamicBindings` continue rendering normally.

The engine is intentionally small and whitelisted. It does not execute arbitrary expressions or PHP callbacks.

## Image Rendition Resolver

Elementor's `Image Resolution` control depends on WordPress attachment sizes. This project uses CKFinder/File Manager, so an adapter is required.

### Behavior

- Persist the original media URL and selected resolution key.
- For locally managed images, resolve or generate the selected rendition through the existing File Manager/Intervention Image infrastructure.
- Cache generated renditions by source identity, modification time, and size key.
- Preserve aspect ratio and never upscale above the source dimensions.
- Return the original image for `full`.
- For external URLs or unsupported files, fall back safely to the original URL.
- Do not mutate or overwrite the original asset.

The renderer may emit `srcset`/`sizes` when local rendition metadata is available. Missing metadata must not break rendering.

## Canvas Rendering

`ImageBox.vue` renders one wrapper containing:

- image area
- content area
- title
- description

Layout behavior:

- Top: column layout.
- Left: row layout with image before content.
- Right: row layout with content before image.
- Responsive settings update the canvas immediately.
- Image Spacing controls image-to-content distance.
- Content Spacing controls title-to-description distance.
- Alignment affects the title, description, and top-layout content alignment.
- Image hover values preview without changing editor selection chrome.
- Advanced styles apply to the widget shell, not to the editor helper shell.

## Frontend Rendering and Accessibility

Use a dedicated Blade partial with semantic markup:

```html
<div class="el-widget-image-box">
	<figure class="el-widget-image-box__image">...</figure>
	<div class="el-widget-image-box__content">
		<h3 class="el-widget-image-box__title">...</h3>
		<p class="el-widget-image-box__description">...</p>
	</div>
</div>
```

Requirements:

- The configured title tag replaces `h3` safely.
- Image `alt` text is escaped.
- Empty title or description elements are omitted.
- When a link exists, image and title receive equivalent safe links.
- New-window links include `noopener noreferrer`.
- `nofollow` is merged without duplicating rel tokens.
- Custom attributes are sanitized.
- Responsive layout and typography rules are scoped to the node ID.
- Hover filters, borders, and transitions match the editor preview.
- Shared Advanced visibility, conditions, cache, motion, mask, attributes, and Custom CSS remain functional.

## Normalization and Error Handling

- Missing settings receive defaults without rewriting unrelated saved data.
- Invalid title tags fall back to `h3`.
- Invalid enum values fall back to safe defaults.
- Invalid units and dimensions are clamped or rejected by existing size helpers.
- Invalid dynamic tags fall back to static values.
- Invalid media renditions fall back to the original URL.
- Unsafe links become empty links rather than executable URLs.
- Invalid custom attributes are omitted.
- Missing images render the builder's approved image placeholder in the editor; frontend output omits an unsafe or empty image source.
- Failed rendition generation falls back to the original image and does not prevent page rendering.
- Existing Accordion and Icon settings remain compatible after shared-control extraction.

## Expected File Areas

New files are expected for:

- `public/js/pagebuilder_elementor/widgets/general/ImageBox.vue`
- a shared link control
- a shared CSS filter control
- shared dynamic-tag UI/runtime helpers
- a backend dynamic-tag resolver
- a backend image-rendition resolver
- `resources/views/pagebuilder_elementor/partials/render_image_box.blade.php`
- `tests/Feature/PageBuilderElementorImageBoxWidgetParityTest.php`
- focused tests for dynamic tags and image renditions where separate coverage is clearer

Existing files expected to change include:

- `public/js/pagebuilder_elementor/app.js`
- `public/js/pagebuilder_elementor/widgets/shared/TypographyControl.vue`
- `public/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue` only if a backward-compatible integration hook is required
- `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- pagebuilder editor/frontend asset shells if a shared runtime asset is introduced
- `public/assets/css/pagebuilder_elementor.css`
- `public/assets/css/frontend_elementor.css`

Before modifying any existing file, create timestamped backups as required by the workspace workflow.

## Testing Strategy

### Automated tests

Start with failing tests for:

- toolbox registration and default node data
- widget normalization and legacy-safe defaults
- every Content and Style control marker
- responsive Image Position, Alignment, spacing, width, radius, typography, and filters
- CKFinder/media picker integration markers
- link options and safe attributes
- title tag sanitization
- Dynamic Tags static fallback and server-side resolution
- rendition selection, original fallback, no-upscale behavior, and cache-key changes
- editor component structure
- frontend semantic markup
- image/title link output
- Normal/Hover CSS filter output
- Advanced defaults and frontend behavior
- regression coverage for Accordion, Icon, Image, Tabs, Container, and Grid

Run:

```powershell
php artisan test --filter=PageBuilderElementorImageBoxWidgetParityTest
php artisan test --filter=PageBuilderElementor
node --check public/js/pagebuilder_elementor/app.js
php -l resources/views/pagebuilder_elementor/partials/render_image_box.blade.php
php -l resources/views/pagebuilder_elementor/partials/render_node.blade.php
git diff --check
```

### Browser verification

Use the user-selected Chrome browser when browser control is available.

Validate:

- insert Image Box from General
- default image/title/description rendering
- CKFinder choose/change/remove flow
- every image resolution option
- link, new-window, nofollow, and custom attributes
- title tags
- all Box controls on Desktop, Tablet, and Mobile
- all Image controls and Normal/Hover behavior
- both Typography popovers
- text stroke and shadow
- Dynamic Tags selection, fallback, save/reload, and frontend resolution
- complete shared Advanced panel behavior
- editor/frontend visual parity
- no clipped, crowded, or overflowing controls at the standard sidebar width
- keyboard access to tabs, sections, popovers, buttons, and form controls
- zero console errors and warnings caused by the widget

### Visual comparison

- Capture matching Elementor reference states and local implementation states.
- Compare the same viewport, device mode, selected tab, opened section, and state.
- Correct spacing, alignment, field sizing, typography, border radius, image size, and content placement before declaring visual parity.

## Completion Criteria

Image Box is complete only when:

- all confirmed Elementor controls are present
- every control changes editor preview and frontend output where applicable
- Content, Style, and Advanced panels match the established Accordion UI rhythm
- responsive inheritance behaves consistently
- shared controls do not regress Accordion or Icon
- Dynamic Tags resolve safely with static fallbacks
- Image Resolution is functional for local assets and safe for external assets
- automated tests pass
- authenticated Chrome validation passes
- editor and frontend comparison shows no material parity defect
- repository diff is clean and reviewable
