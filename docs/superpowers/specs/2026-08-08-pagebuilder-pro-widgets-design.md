# Page Builder Elementor Pro Widgets Design

## Goal

Add a dedicated `Pro` toolbox category with ten widgets: Form, Slides, Animated Headline, Hotspot, Price List, Price Table, Call to Action, Countdown, Carousel, and Flip Box. Each widget must use the existing module registry, shared Advanced controls, editor canvas, saved node settings, and Blade frontend renderer.

## Reference and visual contract

The source of truth is the live Elementor playground at `https://playground.elementor.com/demo/flexbox`. The implementation follows the compact geometry already established by Rating, Text Path, Counter, Progress Bar, Testimonial, Social Icons, and Alert: 30px segmented controls, 30px typography trigger, 28px text effect triggers, aligned inputs, responsive device menus, unit-aware size controls, and no oversized custom controls.

## Architecture

- Each widget has its own definition module and defaults/normalizer under `widgets/pro/<slug>/definition.js`.
- A schema-driven Pro settings SFC renders shared Elementor-like Content, Style, and Advanced tabs. Widget-specific schemas keep conditional controls and repeaters declarative without duplicating ten large settings files.
- A shared Pro canvas SFC renders type-specific previews and provides interaction only where Elementor does: Carousel/Slides navigation, Hotspot tooltip triggers, Countdown timer, and Flip Box hover/focus. Form controls are usable in the canvas but never submit editor data.
- A shared Blade renderer mirrors the same settings and sanitizes URLs, tags, CSS values, rich text, icons, and media. Saved frontend forms submit to a rate-limited Laravel endpoint. The server reloads the saved page and Form node before validating fields or executing Collect, Email, Email 2, Webhook, Message, and Redirect, so outbound actions cannot be injected through browser payloads.
- Existing shared Advanced controls remain the single implementation for layout, motion, transforms, background, border, mask, responsive visibility, attributes, custom CSS, conditions, and cache controls.

## Widget control map

### Form

Content: form name; repeatable fields; input size; labels; required mark; button size/width/text/icon/id; actions after submit; email metadata; step settings; form ID; validation; custom messages. Style: form gaps and label/HTML typography; field colors/typography/background/border/radius; button position/alignment/normal-hover/border/radius/padding; messages; steps. Canvas/frontend: accessible labels, native field types and required validation, server-side validation from saved field definitions, disabled submitting state, inline response messages, stored submissions, mail delivery, webhook delivery, and redirect only after success.

### Slides

Content: repeatable slides, height, heading/description tags, navigation, autoplay, pause behavior, speed, infinite loop, transition, and content animation. Style: content width/padding/position/alignment/shadow; title; description; button normal/hover; arrows and pagination. Canvas/frontend navigation is interactive.

### Animated Headline

Content: highlighted/rotating style, marker/rotation effect, before/animated/after text, loop, duration, delay, link, alignment, and tag. Style: shape color/width/front/rounded; headline and animated text typography, color, stroke, and shadow. Animation respects reduced-motion.

### Hotspot

Content: image/resolution, repeatable hotspots, animation, sequence, tooltip position/trigger/animation/duration. Style: image sizing and hover state; hotspot typography, size, colors, padding/radius/shadow; tooltip typography, alignment, dimensions, color, radius, and shadow. Tooltip supports hover, click, and keyboard focus.

### Price List

Content: repeatable items with title, price, description, image, and link; title and description tags. Style: title/price/description typography and colors; separator; image; item row gap and vertical alignment.

### Price Table

Content: header, pricing/currency/sale/period, repeatable features, footer button/link/info, ribbon. Style: header, pricing, features/dividers, footer button normal-hover, and ribbon. Output uses semantic heading/list/link elements.

### Call to Action

Content: classic/cover skin, media position/image, title, description, button text/link, ribbon. Style: box dimensions/alignment/padding/image sizing; content typography/colors; button normal-hover; hover animation and overlay.

### Countdown

Content: due-date or evergreen timer, display mode, day/hour/minute/second visibility, labels/custom labels, and expiration action. Style: boxes, spacing/padding/background/border/radius; digit and label typography/colors. Runtime updates once per second and stops cleanly at zero.

### Carousel

Content: repeatable items, slides shown/scrolled, equal height, autoplay, pause, speed, infinite loop, transition, navigation, and pagination. Style: slide gap/background/border/radius/padding; navigation and pagination states. Arrows/dots remain directly interactive in canvas and frontend.

### Flip Box

Content: front/back graphic, title, description, media/icon/background, button/link, and flip settings. Style: front/back padding/alignment/border, icons/images, title/description, button, height, radius, flip direction/effect. Hover and keyboard focus reveal the back face.

## State and compatibility

All defaults are JSON-serializable. Normalizers preserve unknown existing settings, clamp numeric ranges, validate enums, and initialize missing arrays. Responsive values use the existing `<base>`, `<base>Tablet`, and `<base>Mobile` convention. Existing widgets and saved layouts must remain unchanged.

## Verification

- Registry/toolbox tests for all ten widgets and the Pro category.
- Source contract tests for mapped Content/Style/Advanced controls and conditional controls.
- Blade rendering tests for semantics, sanitization, responsive CSS, and interactive hooks.
- JavaScript runtime tests for carousel/slides, countdown, tooltip, flip box, and form validation behavior.
- Focused Page Builder suite, then full Page Builder feature suite.
- Chrome visual QA against the live Elementor panels and the local editor without clicking Save.
