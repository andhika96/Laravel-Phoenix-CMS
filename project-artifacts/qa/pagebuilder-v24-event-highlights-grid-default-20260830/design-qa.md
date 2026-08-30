# Event Highlights Grid Default Visual QA

- Date: 2026-08-30
- Reference: `C:\Users\aruna\Downloads\Screenshot 2026-08-30 at 02-18-18 CEO Masters Indonesia 2026 Play. Connect. Lead.png`
- Target: v2.4 `event_highlights_grid` default in Canvas and Blade.

## Measured reference

- Reference viewport: `1918x728`.
- Root background sample: `#091d31`.
- Card surface sample: `#0a1e33`.
- Card border sample: `#3a413f`.
- Inner content width: approximately `1636px`.
- Five cards: approximately `310px` wide, `385px` high, with `20px` gaps.
- Card padding: approximately `40px`.

## Static and renderer checks

- Canvas and Blade defaults use the measured colors, content width, card dimensions, typography, five reference cards, and separate `Media–Title Gap`.
- Canvas header colors now consume `headingColor` and `subheadingColor`, so their Style controls update the preview.
- Advanced widget padding/background remain owned by the shared `AdvancedControls` shell; the Grid Canvas no longer applies those values a second time.
- Legacy preset icons are migrated from the invalid `fa-golf-ball-tee`/solid defaults to valid Font Awesome 5 Pro light classes without overriding custom card content.
- Canvas/Blade DOM and style contract tests pass.
- Focused PHPUnit and full Node v2.4 tests pass.
- Vite production build passes.

## Browser boundary

- The authenticated Page Builder editor route redirects to `/auth/login` in the available browser session.
- The latest read-only route check reached `/auth/login`; no authenticated editor DOM or computed layout was available for post-fix comparison.
- No credentials, Save, Reset, Apply, or submit action was used.
- Authenticated browser screenshots and computed-layout comparison at `1180px`, `768px`, and `390px` could not be captured.

final result: blocked
