# Article Editorial Studio Polish QA

Date: 2026-08-25  
Scope: Article template frontend and Manage Article Template preview only. Event, saved template settings, and Article rows were not changed during this QA pass.

## Implemented polish

- Added a deterministic, curated preview fixture with six local editorial images at `1672x941`; all six source and public-storage files were present.
- Kept all five Archive and three Detail templates on shared Blade contracts while preserving their individual layouts.
- Added a short accessible loading overlay for iframe template reloads.
- Added CMS accent inheritance through `--ph-theme-primary` for Article UI. The manager appends the active parent color as `theme_color` to the isolated preview and observes root-style changes so a palette change reloads the preview with the new accent.
- Converted every Archive thumbnail to a shared rounded background-media partial. The surface uses `background-image`, `background-size: cover`, centered positioning, and no repeat.
- Replaced media-selector `background` shorthands with `background-color` after browser QA found that those shorthands reset the shared image in four Archive templates.

## Automated verification

- `php artisan test tests/Feature/Article --testdox` — 13 passed, 142 assertions.
- Related Node tests — 22 passed, 0 failed:
  `article-template-presentation`, `article-template-preview-fixture`, `manage-article-template-manager`, and `article-frontend-pagination`.
- `node --check` passed for the Article theme-sync and manager Vue scripts.
- PHP lint passed for `ArticleFrontendController`, `ManageArticleTemplateController`, `ArticleTemplateCatalog`, and `ArticleTemplatePreviewFixture`.
- `php artisan view:clear` and `php artisan view:cache` passed.
- `git diff --check` passed. The working tree remains intentionally dirty; no reset, clean, staging, commit, or push was performed.

## Browser QA (read-only)

- Hard-reloaded `/manage_article/templates` while authenticated without clicking **Save Template**.
- The active CMS manager accent was `#6542d7`; the iframe URL carried `theme_color=%236542d7`, and the iframe computed both `--ph-theme-primary` and `--article-accent` as `#6542d7`.
- Checked all five Archive previews and all three Detail previews. Each used the curated fixture; all Archive variants rendered six background-media elements with `cover`; every Detail variant rendered two neighbor-navigation entries.
- At desktop `1440x900`, tablet `834x1112`, and mobile `390x844`, all eight preview URLs had visible headings, no horizontal overflow, correct Archive media cover behavior, and Detail navigation.
- Manager virtual stages reported the expected `1440x900`, `834x1112`, and `390x844` profiles scaled to fit.
- Public `/article` and `/article?page=200` loaded with the Article accent and rounded cover media. The deep page showed `Showing 2389–2400 of 5027 Articles`, active page 200, and 12 cards in approximately 1.94 seconds from navigation start.
- Console warnings/errors were empty for the manager, standalone preview, and public Article page.

## Theme-change boundary

The live orange case is covered deterministically in the Vue test with `#FF5733` and by the `MutationObserver` contract. Browser QA did not mutate the user's persisted CMS color or local storage; changing it would have modified user state.

## Graphify

`graphify . --update --no-viz --code-only` completed at 2026-08-25 02:31:13 +07:00. `graphify-out/graph.json` updated to 27,049,257 bytes and confirms the extracted `ManageArticleTemplateController -> ArticleTemplatePreviewFixture` relation.
