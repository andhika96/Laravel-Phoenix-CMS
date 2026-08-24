# Manage Event / Manage Article follow-up QA

Date: 2026-08-24

## Scope verified

- Event reminder and cancel-cutoff time picker keeps compact 14 px text and has 36 px clearance on both sides of the calendar icon.
- Manage Article Thumbnail follows the selected Event design: Upload file / CKFinder library source switcher, checkerboard preview, empty state, and remove button.
- Article CKFinder selection is restricted to `/storage/ckfinder/articles/`, then copied into the Article rendition pipeline rather than referenced in place.
- Article remove action persists `remove_thumbnail=1` and deletes both stored renditions on update.
- Event Category modal follows the Manage Article hierarchy: list, separate create/edit forms, and destructive delete confirmation.
- Event category code remains automatic from its name; no manual code field is present in the browser form.
- Static `/manage_event/update/category` now precedes `/manage_event/update/{idOrSlug}` so category updates do not route into Event content updates.

## Browser QA (hard reload, no submit/upload/delete)

- `/manage_event/add`: both duration inputs rendered at `14px`, `38px` high, `padding-left: 36px`, and `padding-right: 36px`; no console errors.
- `/manage_article/add`: source hidden inputs were present, Upload/CKFinder switching worked, and `CKFinder.modal` opened the in-page `Articles` resource modal; no console errors.
- The live `Articles` folder was empty, so no real-file selection was performed in the browser. The server import path is covered by the feature test below.
- `/manage_event`: Event Categories opened to the same visual list hierarchy as Manage Article. Create opened separately, contained one name input and no Category Code field, and Cancel returned to the list; no console errors.
- Browser runner viewport override requested at `390x844` but remained `1280x720`; mobile browser runtime evidence is therefore not claimed. Static responsive checks passed.

## Automated checks

- Focused Event template, Event HTTP-flow, and Article thumbnail tests — 15 passed, 128 assertions.
- Node static/runtime tests for Article responsive table, Article thumbnail, Event thumbnail, and Event category modal — 9 passed.
- PHP lint for changed Article controller/requests, `node --check` for changed Article/Event scripts, `php artisan view:cache`, and `git diff --check` — passed.
- Full `php artisan test --no-ansi` — 699 passed, 1 failed, 19,329 assertions. The only failure is the existing unrelated `Tests\Feature\PageBuilderElementorV23ShellTest` expectation of 200 while the shell returns 302.

## Backups

- `project-artifacts/backups/20260824_221500_manage-event-article-thumbnail-followup/`

## Graphify

- Final incremental code-only update completed: graph now has 20,168 nodes and 34,785 edges.
- `graphify cluster-only` completed with 1,480 communities. LLM labels were not refreshed.
