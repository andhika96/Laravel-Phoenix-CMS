# Article archive H4 title and category badge QA

Date: 2026-08-25

## Scope

- Archive/list Article titles only; archive page H1 and Article detail headings are unchanged.
- All five archive renderers: Minimal Reading List, Editorial Journal, Mosaic Magazine, Mosaic Classic, and Balanced Card Grid.

## Implementation

- Every `.article-title-clamp` in archive templates now renders as `<h4>`; no archive list title remains as H1 or H2.
- Archive title CSS uses `--article-list-title-size`, bound to the responsive CMS H4 token and capped for compact list presentation.
- Archive `.article-chip` category badges use a smaller `.66em` font, reduced padding, and tighter letter spacing. Detail chips retain their existing scale.

## Fresh browser evidence

- Desktop active archive: all inspected list title nodes were `H4`; list `H2` count was zero; first title computed at 14.25px and category badge at 8.25px.
- Mobile 390px: first title remained `H4` at 14.7px; category badge 9.24px; no horizontal overflow.
- Browser console was empty in both checks.

## Automated verification

- `php artisan test tests/Feature/Article --testdox` — 19 passed, 274 assertions.
- Article Node/template suite — 40 passed.
- `node --check public/assets/js/vue3/article/vueV3-article-frontend-2026.js` — passed.
- `php artisan view:clear` and `php artisan view:cache` — passed.
- `git diff --check` — passed; only existing CRLF warnings for unrelated Manage Article Blade files.
