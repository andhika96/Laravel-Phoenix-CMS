# Article Search and Pagination customization runtime QA — 2026-09-06

## Scope

Production-shaped fixture for the three Search models and the new Search/Pagination visual overrides.

## Verified Search models

- `Attached Classic`: joined input/button edges, custom radius, Font Awesome search icon.
- `Soft Field`: separated controls, custom gap/background/text, Font Awesome sliders icon.
- `Minimal Underline`: underline field, compact icon action, Font Awesome arrow icon.

## Verified Pagination overrides

- Custom number gap.
- Custom item radius.
- Custom active background/text.
- Custom hover background/text.
- Font Awesome previous/next icon classes.

## Browser result

Viewport `1440 × 900` and `390 × 844`:

- Search custom radius/gap/colors computed correctly.
- Font Awesome classes computed correctly.
- Pagination gap/radius/active colors computed correctly.
- Horizontal overflow: `0`.
- Console errors/warnings: `0`.

Fixture: [runtime-fixture.html](./runtime-fixture.html)

Screenshots:

- [Desktop 1440](./desktop-1440.png)
- [Mobile 390](./mobile-390.png)

Graphify was not updated during this implementation; Graphify updates remain manual.
