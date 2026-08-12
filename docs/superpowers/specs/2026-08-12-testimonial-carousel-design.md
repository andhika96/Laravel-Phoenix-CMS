# Testimonial Carousel Widget Design

**Tanggal:** 2026-08-12

**Project:** Laravel 13 Phoenix - Page Builder Elementor v2.3

## Goal

Menambahkan widget Pro `Testimonial Carousel` yang mengikuti demo resmi Elementor pada Content, Style, Advanced, responsive behavior, editor canvas, persistence payload, Blade frontend, dan carousel interaction.

## Scope and invariants

- Widget hanya ditambahkan ke Page Builder Elementor v2.3.
- Page Builder v2.0, asset tree, route family, dan renderer v2.0 tidak disentuh.
- Browser QA tetap read-only; tidak menekan Save, Submit, atau menyimpan eksperimen demo.
- `graphify-out` tetap generated/local dan tidak di-stage.
- Source aktif dan runtime test menjadi sumber kebenaran terakhir; Graphify hanya digunakan untuk navigasi hubungan kode.

## Reference audit

Demo resmi `https://playground.elementor.com/demo/flexbox/` memperlihatkan:

- Content: Slides Name; repeater Content, Image, Name, Title; Add Item, Duplicate, Remove; Skin Default/Bubble; Layout Image Inline, Image Stacked, Image Above, Image Left, Image Right; responsive Alignment, Slides Per View, Slides to Scroll, Width; Additional Options.
- Additional Options: Arrows, Pagination None/Dots/Fraction/Progress, Transition Duration, Autoplay, Autoplay Speed, Infinite Loop, Pause on Hover, Pause on Interaction, Image Resolution, Custom image dimensions when selected, Lazy Load.
- Style: Slides (Space Between, Background Color, Border Width, Border Radius, Border Color, Padding); Content (Gap, Text Color, Typography, Text Stroke, Text Shadow, Name, Title); Image (Size, Gap, Border with conditional Border Color and Border Width, Border Radius); Navigation (Arrow Size/Color and pagination dot spacing/size/color/active color).
- Advanced: standard Elementor Layout, CSS ID, CSS Classes, Display Conditions, Cache Settings, Motion Effects, Transform, Background, Border, Mask, Responsive, Attributes, and Custom CSS controls.

## Architecture decision

Use a new registry type `testimonial_carousel` while reusing the existing v2.3 Pro carousel infrastructure:

- `definition.js` owns defaults and normalization for this widget only.
- The shared Pro `Settings.vue` gets a dedicated `testimonial_carousel` Content and Style branch; it does not reuse the Reviews repeater because Reviews has unrelated Rating, Icon, and Link fields.
- The shared Pro `Canvas.vue` gets a dedicated testimonial markup branch and CSS classes while reusing `data-pro-carousel`, existing keyboard/autoplay/page navigation methods, responsive helpers, and safe media URL handling.
- The shared Blade renderer gets a dedicated switch branch and responsive CSS map. It emits the same `data-pro-config` contract consumed by `frontend-runtime.js`; no duplicate carousel runtime is created.
- `app.js` adds the label/icon and includes the type in `hasNewGeneralAdvancedControls()` so the established v2.3 Advanced contract applies to the widget.

## Data contract

The normalized settings use these fields:

```js
{
  slidesName: 'Slides',
  items: [{
    id: 'testimonial-1',
    content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
    imageUrl: 'https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png',
    name: 'John Doe',
    title: 'CEO'
  }],
  skin: 'default',
  layout: 'image_inline',
  alignment: 'center',
  alignmentTablet: '',
  alignmentMobile: '',
  slidesToShow: 1,
  slidesToShowTablet: 1,
  slidesToShowMobile: 1,
  slidesToScroll: 1,
  slidesToScrollTablet: 1,
  slidesToScrollMobile: 1,
  width: '100%',
  widthTablet: '100%',
  widthMobile: '100%',
  arrows: true,
  pagination: 'dots',
  transitionSpeed: 500,
  autoplay: true,
  autoplaySpeed: 5000,
  infiniteLoop: true,
  pauseOnHover: true,
  pauseOnInteraction: true,
  imageResolution: 'full',
  customImageWidth: 300,
  customImageHeight: 300,
  lazyLoad: false,
  gap: '10px',
  slideBackground: '#ffffff',
  slideBorderColor: '#e4e7ec',
  slideBorderTop: '1px',
  slideBorderRight: '1px',
  slideBorderBottom: '1px',
  slideBorderLeft: '1px',
  slideRadiusTop: '0px',
  slideRadiusRight: '0px',
  slideRadiusBottom: '0px',
  slideRadiusLeft: '0px',
  slidePaddingTop: '20px',
  slidePaddingRight: '20px',
  slidePaddingBottom: '20px',
  slidePaddingLeft: '20px',
  contentGap: '10px',
  contentColor: '#344054',
  nameColor: '#101828',
  titleColor: '#667085',
  imageSize: '50px',
  imageGap: '10px',
  imageBorder: false,
  imageBorderColor: '#e4e7ec',
  imageBorderTop: '1px',
  imageBorderRight: '1px',
  imageBorderBottom: '1px',
  imageBorderLeft: '1px',
  imageRadiusTop: '50%',
  imageRadiusRight: '50%',
  imageRadiusBottom: '50%',
  imageRadiusLeft: '50%',
  arrowsSize: '20px',
  arrowColor: '#020101',
  dotsGap: '8px',
  dotsSize: '8px',
  paginationColor: '#d0d5dd',
  paginationActiveColor: '#6979f8',
  testimonialCarouselContentTextStrokeWidth: '0px',
  testimonialCarouselContentTextStrokeWidthTablet: '',
  testimonialCarouselContentTextStrokeWidthMobile: '',
  testimonialCarouselContentTextStrokeColor: '#000000',
  testimonialCarouselContentTextShadow: 'none',
  testimonialCarouselContentFontSize: '16px',
  testimonialCarouselContentFontWeight: '400',
  testimonialCarouselContentLineHeight: '1.5em',
  testimonialCarouselNameFontSize: '18px',
  testimonialCarouselNameFontWeight: '600',
  testimonialCarouselNameLineHeight: '1.3em',
  testimonialCarouselTitleFontSize: '14px',
  testimonialCarouselTitleFontWeight: '400',
  testimonialCarouselTitleLineHeight: '1.4em'
}
```

The definition clamps slide counts to 1-10, validates enum values, clamps image rendition dimensions to 1-4096, and ensures every repeater entry has the four expected fields. Unknown or malformed values fall back to the defaults above.

## Rendering behavior

- `Default` and `Bubble` skin classes are isolated under `.pb-pro-testimonial-carousel`.
- Layout classes determine whether the image is inline, stacked, above, left, or right of the testimonial content. Alignment and width resolve desktop/tablet/mobile inheritance through the existing v2.3 responsive helper.
- Text content is escaped in Blade and rendered as text in the editor canvas, matching the simple textarea contract exposed by the demo.
- Image URLs pass the existing safe media URL and rendition resolver. `Image Resolution`, Custom dimensions, and Lazy Load apply in Blade and canvas where the existing runtime supports them.
- Arrows, dots/fraction/progress, autoplay, loop, hover pause, interaction pause, keyboard arrows, and responsive slide counts use the existing `data-pro-carousel` runtime contract.
- Text Stroke and Text Shadow apply to the quote content in both canvas and Blade output. Name and Title use the shared v2.3 TypographyControl.
- All interactive controls expose button type, accessible labels, and `data-pb-interactive` in the canvas so selecting a carousel control does not select the widget shell.

## Verification contract

Focused Node tests cover definition defaults/normalization, all Content/Style labels, conditional image-border controls, text effects, responsive settings, and SSR canvas markup. Focused PHP tests cover registry metadata, v2.3 renderer output, safe escaping, responsive CSS, `data-pro-config`, and interactive markup. Existing v2.3 registry, asset isolation, frontend runtime, and route/persistence suites run afterward.

Chrome verification is read-only: refresh the v2.3 editor, confirm `Testimonial Carousel` appears under Pro, add/select it without saving, inspect Content/Style/Advanced, exercise arrows/dots and a responsive device control, and record any browser console warning/error. Manual pointer drag and Save->reload persistence remain explicit unverified boundaries unless separately authorized.

## Deliberate boundary

The widget uses the existing v2.3 shared AdvancedControls contract. That contract currently exposes Classic and Gradient backgrounds, not video/slideshow background media. Expanding that shared contract would affect every Pro widget and is outside this widget addition.
