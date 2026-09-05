# Template Options switch-thumb fit — 2026-09-05

## Scope

The white circular thumb inside the compact Template Options switch was visually too small relative to the approved `44 × 24px` outer track. Only the rendered SVG background size was adjusted. The switch geometry, Vue bindings, and checked-state behavior remain unchanged.

## Implementation

- Production selector: `.article-template-options-panel .form-switch .form-check-input`.
- Outer geometry remains `2.75rem × 1.5rem` (`44 × 24px` at the active root font size).
- The Bootstrap switch background is now `1.25rem × 1.25rem` (`20 × 20px`). Its SVG circle is drawn with `r=3` in an `8 × 8` viewBox, so the visible white circle is approximately `15px`; that leaves balanced breathing room inside the 24px track.
- No Graphify update was run, per the user's explicit instruction.

## Browser evidence

Read-only production-shaped fixture, exact Header content switch markup:

| Viewport | Track | Background image | Overflow |
|---|---:|---:|---|
| 1440 × 900 | 44 × 24px | 20 × 20px | N/A |
| 390 × 844 | 44 × 24px | 20 × 20px | none |

Screenshots:

- `switch-thumb-fit-header-1440.png`
- `switch-thumb-fit-header-390.png`

The fixture uses Bootstrap's default blue accent. The manager receives its green track from the active CMS primary-color token; the SVG thumb geometry is the same.

## Regression evidence

- `node --test tests/article-template-presentation.test.mjs` — 25 passed.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — 87 passed.
- `php artisan test --compact tests/Feature/Article` — 30 passed, 411 assertions.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` — passed.
- `php artisan view:cache` — passed.

## Runtime boundary

The authenticated Manage Article browser session is not available in this QA environment. No Apply, Save Template, or other write action was used. The fixture validates the production CSS and responsive computed values; live authenticated visual parity still requires a read-only hard reload in the user's logged-in manager session.
