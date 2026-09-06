# Search form models mockup

Mockup-only artifact for the Minimal Reading List Search form and related Pagination customization. No production source, database, or Graphify data is changed by this mockup.

## Search models

1. `Attached Classic` — joined input and filled action button.
2. `Soft Field` — quiet filled input surface and lighter accent action.
3. `Minimal Underline` — editorial underline input with compact icon action.

## Interactive controls

- Desktop / Tablet / Mobile preview switch.
- Search model switch.
- Search border radius.
- Search button gap — distance between the input and Search action; Attached Classic keeps joined edges as its intentional model behavior.
- Search icon preview using local Font Awesome class conventions.
- Search input background/text colors.
- Search button background/text colors.
- Pagination model, radius, number gap, normal/active/text colors, and arrow icon preview.

## Browser QA

Playwright Chromium inspected the mockup at `1440 × 1000` and `390 × 844` after changing the model, radius, gap, icon, and device controls:

- Horizontal overflow: `0`.
- Console errors/warnings: `0`.
- Dynamic model state: verified.
- Dynamic Font Awesome icon state: verified.
- Dynamic radius and gap CSS variables: verified.

Screenshots:

- `desktop-attached.png`
- `desktop-soft.png`
- `desktop-underline.png`
- `mobile-soft.png`
