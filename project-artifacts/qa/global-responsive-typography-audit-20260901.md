# Global responsive typography audit

Date: 2026-09-01  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Result: passed

## Scope

The shared `public/assets/css/theme-responsive-typography.css` contract now covers every active CMS theme layout: Default, Calm Green, Arunika Mosaic, Arunika Aurora, Arunika Prism, Arunika Equinox, and Arunika Lucent.

Elements audited: body, h1-h6, p, small, label, input, button, table, main sidebar menu, lower sidebar utility menu, and profile dropdown items.

## Computed browser matrix

| Viewport | Body/P/control | H1 | H2 | H3 | H4 | H5 | H6 | Small | Utility | Dropdown | Overflow |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---:|---:|---|
| 320x800 | 14 | 24 | 21.6 | 19.2 | 17.6 | 16 | 15.2 | 12.25 | 13 | 13 | none |
| 375x800 | 14 | 24 | 21.6 | 19.2 | 17.6 | 16 | 15.2 | 12.25 | 13 | 13 | none |
| 430x900 | 14 | 24 | 21.6 | 19.2 | 17.6 | 16 | 15.2 | 12.25 | 13 | 13 | none |
| 540x900 | 14 | 24 | 21.6 | 19.2 | 17.6 | 16 | 15.2 | 12.25 | 13 | 13 | none |
| 720x1024 | 14 | 25.6 | 22.4 | 20 | 18 | 16 | 15.2 | 12.25 | 13 | 13 | none |
| 800x1024 | 14 | 28 | 24 | 20.8 | 18.4 | 16.8 | 15.2 | 12.25 | 13 | 13 | none |
| 1024x900 | 14 | 30 | 25.6 | 22.4 | 19.2 | 16.8 | 15.2 | 12.25 | 13 | 13 | none |
| 1200x900 | 13 | 28 | 24 | 20.8 | 19.2 | 16.8 | 15.2 | 11.375 | 13 | 12 | none |
| 1920x1080 | 14 | 28 | 24 | 20.8 | 19.2 | 16.8 | 15.2 | 12.25 | 13 | 13 | none |

Values are pixels. The 1200x900 compact-density body floor was raised from 12/12.5px behavior to 13px to preserve operational readability.

All sidebar navigation labels are globally capped at `min(13px, --ph-adaptive-font-size)`. Lucent icons use a separate responsive 15-16px token so glyphs remain proportional without shrinking their 40-44px interaction targets. Runtime at 1254x884 measured menu labels `13px`, expanded icons `15px`, and lower utility labels `13px`.

## Contracts

- Site Config remains the source for `--ph-font-family` and `--ph-font-size`.
- Controls inherit the adaptive body size.
- Paragraphs use `1em`; small text uses `0.875em`.
- Global heading variables cover h1-h6.
- Explicit ranges: <=575, 576-767, 768-991, 992-1199, and >=1200 with height <=1100.
- Theme-specific selectors with stronger specificity can still own specialized hero/title treatment.

## Verification

- Focused typography and Lucent static tests: 13 passed.
- Combined theme/static suite: 56 passed.
- Laravel focused suite: 7 passed, 93 assertions.
- Browser audit: nine viewports, no horizontal overflow.
- Browser console: 0 errors, 0 warnings.
