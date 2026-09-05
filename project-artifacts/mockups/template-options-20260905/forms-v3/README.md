# Template Options form UX previews — V3 compact CMS direction

This is a replacement design direction for V2. It is still a design-only artifact; production Blade, Vue, and CSS are not changed by this preview pass.

## Corrections from V2

- Removed the oversized showcase typography. The modal title computes to about `18.56px`, the panel heading about `16.32px`, the panel labels about `12.22px`, and real settings controls about `13.44px` at a 1440px viewport.
- Kept the responsive behavior, but tightened the ranges so `clamp()` does not turn a CMS inspector into a poster.
- Removed nested cards and most rounded borders from the settings pane. Sections use whitespace and a quiet divider; conditional fields use a slim accent rail.
- Border radius now follows the Page Builder form-group pattern: label row with unit selector, four corner values, and an explicit link/unlink control.
- Kept Category filter OFF as a true collapsed state. Position and Filter style are not rendered below it.
- Kept Category filter ON as a single vertical disclosure. Position and Filter style cannot overlap.
- Kept Button list and Form select as two explicit visual states in the archive preview.
- Kept switch visual size at `56 × 28px` while preserving a larger row hit area.
- Kept numeric/unit controls readable by reserving a small unit column only inside a sufficiently wide field.

## Typography tokens

The preview follows the existing Phoenix typography baseline instead of inventing a new display scale:

| Token | Responsive range | Role |
| --- | --- | --- |
| `--fs-base` | 13–14px | settings control text |
| `--fs-meta` | 10–11px | preview metadata and compact labels |
| `--fs-label` | 12–13px | form labels and helper copy |
| `--fs-heading` | 16–18px | panel and article preview heading |
| `--fs-modal` | 18–20px | modal title |

The production `SiteTypography` default is 14px, so this preview stays close to the real CMS scale. It does not use 1rem–1.5rem jumps for every UI element.

## Screenshots

- [Header content](screens/01-header-content.png)
- [Archive toolbar — Category filter OFF](screens/02-toolbar-category-off.png)
- [Archive toolbar — Button list](screens/03-toolbar-button-list.png)
- [Archive toolbar — Form select](screens/04-toolbar-form-select.png)
- [Post list](screens/05-post-list.png)
- [Reading list sidebar](screens/06-reading-list-sidebar.png)
- [Thumbnail](screens/07-thumbnail.png)
- [Pagination](screens/08-pagination.png)
- [Archive shell](screens/09-archive-shell.png)

## Verification

- 9 views rendered at `1440×900`.
- Console: 0 errors and 0 warnings after adding the isolated data favicon.
- Mobile check at `390×844`: `scrollWidth=390`, `clientWidth=390`, no horizontal overflow.
- Settings control computed size at desktop: `13.44px` font, `40px` height.
- Frame fields are stacked one per row; the four radius corners remain grouped inside one full-width Page Builder-style control.
- The mockup remains isolated under `project-artifacts/mockups` and adds no dependency or external font request.
