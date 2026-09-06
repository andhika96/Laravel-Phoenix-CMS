# Editorial Journal Template Options QA

Date: 2026-09-06
Project: `D:\Laragon\www\laravel-13-phoenix`

## Scope

Added template-scoped options for `editorial-journal` while preserving the existing modern Editorial Journal layout and all other archive templates.

- Lead/grid divider toggle and separate spacing values.
- Inset or edge-to-edge grid thumbnails.
- Card border type, width, color, and four-corner radius.
- Card background color or CKFinder-backed image with safe URL fallback.
- Auto or responsive fixed card height.
- Opt-in Read More link for lead and grid cards with left/center/right alignment and allowlisted Font Awesome icons.
- Editorial Journal grid thumbnail default changed to `12.5rem`; other template defaults remain unchanged.

## Verification

- PHPUnit Article suite: `42 passed`, `610 assertions`.
- Node Article/Template suite: `89 passed`.
- `php artisan view:cache`: passed.
- `php -l app/Support/Article/ArticleTemplateOptions.php`: passed.
- `php -l app/Http/Controllers/Web/Manage_Article/ManageArticleTemplateController.php`: passed.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: passed.
- `node --check tests/manage-article-template-manager.test.mjs`: passed.
- `node --check tests/article-template-presentation.test.mjs`: passed.
- `git diff --check`: passed.
- URL sanitizer smoke check: relative/HTTPS accepted; protocol-relative, `data:`, and `javascript:` rejected.

## Browser boundary

The authenticated Manager route redirected to `/auth/login` in the available browser session. No credentials were entered, and CKFinder selection, Save, and Apply were not executed. Manager controls are covered statically and through draft/persistence tests; authenticated visual QA remains pending a user-provided session.

## Graphify

Graphify was queried for the Editorial Journal option/renderer/CKFinder path. No Graphify update was run because project policy keeps updates manual.

## Backups

Hash-verified backups are stored under:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20260906_201715-editorial-journal-template-options\`
