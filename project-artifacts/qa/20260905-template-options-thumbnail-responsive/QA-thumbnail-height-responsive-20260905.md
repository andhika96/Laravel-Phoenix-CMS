# Thumbnail mode audit, background height, and Minimal Reading List responsive layout — 2026-09-05

## Scope

This pass audited the Thumbnail setting and implemented the requested background-image Height control, then refined the Minimal Reading List article list for tablet and mobile.

## Thumbnail audit result

The existing mode split is correct and is now covered by tests and a production-shaped fixture:

- Background image mode renders an anchor with `article-background-media`, `background-image: var(--article-media-image)`, and no `<img>` child.
- Full asset image mode renders an anchor with `article-asset-media` and a real `<img>` child. The image uses `object-fit: var(--article-thumbnail-fit, cover)`, so `Cover` and `Contain` remain meaningful.
- Both modes keep the existing fallback URL, background color, frame border, radius, and link behavior.

## Background Height implementation

- `ArticleTemplateOptions` now normalizes `thumbnail.height` with the existing safe dimension/unit allowlist.
- Minimal Reading List default is `9.3rem`, matching its existing desktop `15.5rem × 5/3` thumbnail ratio.
- Other archive templates receive a neutral `5.625rem` default so existing lead/card minimums remain intact.
- The manager displays Height only when Display mode is `Background image`.
- The shared media partial emits `--article-thumbnail-height` only for background mode; asset mode does not receive or use that variable.
- Regular archive thumbnail frames consume the value; Minimal Reading List mobile intentionally switches to its responsive `16:9` media treatment for a better stacked reading card.

## Responsive article-list implementation

- Desktop keeps the established two-column editorial row.
- Tablet (`<=991.98px`) uses a smaller `10rem–13rem` media column, `1.25rem` row gap, tighter text scale, and two-line excerpts.
- Mobile (`<=575.98px`) switches each article to one column: full-width `16:9` media, content below the image, compact metadata, three-line excerpt, and a 36px read action.
- Pagination, Vue list slots, category filtering, and sidebar structure are untouched.

## Browser evidence

### Production-shaped frontend fixture

| Viewport | List geometry | Thumbnail mode evidence | Overflow |
|---|---|---|---|
| 1440 × 1000 | item columns `248px 908px` | Background: real CSS `background-image`, 0 images, `248 × 192px` at configured `12rem`; Asset: 1 image, `object-fit: contain`, `248 × 149px` | none |
| 768 × 1000 | item columns `208px 508px`, gap `20px` | Media ratio `16:10` in the tablet row | none |
| 390 × 844 | item columns `358px`, gap `14px` | Media `358 × 201px`, ratio `16:9`; body begins after media | none |

### Responsive mockup

The new interactive mockup uses container-query device states and was visually inspected at Desktop, Tablet, and Mobile:

- [responsive mockup HTML](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20260905_article-minimal-reading-list-responsive/index.html)
- [Desktop preview](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20260905_article-minimal-reading-list-responsive/desktop-1440.png)
- [Tablet preview](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20260905_article-minimal-reading-list-responsive/tablet-768.png)
- [Mobile preview](/D:/Laragon/www/laravel-13-phoenix/project-artifacts/mockups/20260905_article-minimal-reading-list-responsive/mobile-390.png)

The mockup uses the same hierarchy as the production list and keeps the responsive direction focused on the article list, as requested.

## Regression evidence

- `node --test tests/article-template-presentation.test.mjs` — 34 passed.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs` — 96 passed.
- `php artisan test --compact tests/Feature/Article` — 31 passed, 422 assertions.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js` — passed.
- `php artisan view:cache` — passed.
- `git diff --check` for scoped source, tests, plan, and mockup files — passed.
- Production fixture and responsive mockup console — 0 errors, 0 warnings.

## Runtime boundary

The authenticated Manage Article browser session is not available in this environment. No Apply, Save Template, or other write action was performed. Source contracts, production-shaped fixtures, and the responsive mockup are verified; live authenticated click-through remains an external runtime boundary.
