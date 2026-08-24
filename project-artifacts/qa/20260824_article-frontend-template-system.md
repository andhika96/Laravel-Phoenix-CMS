# Article frontend template system QA

Date: 2026-08-24

## Scope delivered

- Dedicated Article Archive and Detail frontend routes now replace the incomplete legacy Article route/controller binding.
- Four Archive templates: Minimal Reading List, Mosaic Magazine, Editorial Journal, Balanced Card Grid.
- Three Detail templates: Focused Reader, Editorial Feature, Knowledge + TOC.
- Global template settings persisted in `article_template_settings`; v1 has no per-category or per-Article override.
- Manage Article Templates page follows selected visual direction 3 and uses a real iframe preview, Archive/Detail tabs, device previews, and a single Save action.
- Vue 3.5.21 global production CDN is pinned for Archive and Manager enhancement; the existing theme Vue global remains the fallback.
- Public Article query uses publish/public/current eligibility, eager-loaded category/author, bounded page sizes, and new listing indexes.

## Database verification

- Applied migrations:
  - `2026_08_24_000007_create_article_template_settings_table`
  - `2026_08_24_000008_add_article_public_listing_indexes`
- Database check confirmed `articles_public_listing_idx`, `articles_public_category_listing_idx`, and one Article settings row.

## Browser QA (hard reload, no save/delete/create)

- `/article`: Minimal Reading List rendered with real Article data, search control, article links, no new console errors.
- `/article/testing-testing`: Focused Reader detail rendered from the Archive link, no new console errors.
- `/manage_article/templates`: Archive and Detail tabs rendered; live iframe preview changed to Mosaic Magazine; Detail preview loaded Focused Reader; no new console errors.
- Regression fix checked: an unsaved Mosaic preview displays `Selected`, while persisted Minimal Reading List remains `Default`.
- A fresh in-app Browser tab persisted the rendered Manager capture. Source/implementation comparison passed; see `20260824_article-frontend-template-design-qa.md`.

## Automated verification

- `php artisan test tests\Feature\Article --no-ansi` — 7 passed, 46 assertions.
- Article frontend/manager/Article regression Node tests — 12 passed.
- PHP lint, Node syntax checks, `php artisan view:cache`, and `git diff --check` — passed.
- Full `php artisan test --no-ansi` — 706 passed, 1 failed, 19,375 assertions. The only failure remains the unrelated historical `Tests\Feature\PageBuilderElementorV23ShellTest` (expected 200, received 302).

## Backups

- `project-artifacts/backups/20260824_230000_article-frontend-template-system/`

## Graphify

- Incremental code-only update completed: 34 files re-extracted; graph has 20,265 nodes and 34,943 edges.
- `graphify cluster-only` completed with 1,493 communities. LLM labels were not refreshed.
