# Article Frontend Template System Design

## Status and decision

Approved direction: the selected visual foundation is **Minimal Reading List**. Article is the only content type in this delivery; Event remains untouched. The system is dedicated Laravel rendering, not a Page Builder v2.4 integration.

## Goal

Provide a production-ready Article archive and article detail experience, plus a CMS page where an administrator can choose the active global Archive and Detail templates.

## Binding decisions

- Article keeps its existing authenticated frontend policy for this delivery. Public guest/SEO exposure is a separate authorization decision and is not inferred here.
- Template selection is global per surface in v1: one Archive template and one Detail template. There is no per-category or per-article override.
- Archive templates: `minimal-reading-list` (default), `mosaic-magazine`, `editorial-journal`, and `balanced-card-grid`.
- Detail templates: `focused-reader`, `editorial-feature`, and `knowledge-toc`.
- Category, tag, author, and search views reuse the active Archive template with different filters; they are not independent template types.
- Numeric pagination is the default. It keeps deep links, browser history, accessibility, and predictable database behaviour. `Load more` and infinite scroll are deferred.
- The UI uses the Vue 3 global production build through a pinned CDN URL, with the theme-provided local Vue global as fallback. It does not introduce Vite, npm compilation, or a new frontend dependency.
- Page Builder v2.4 is not changed. A later integration can reuse the public Article query contract for v2.4 widgets.

## Current-state constraints

- The existing `/article` route is protected by `auth`, `checkSuspended`, and permission middleware, and must retain that policy.
- The existing route namespace does not match the available `App\Http\Controllers\Web\Article\Article_Controller`, and the referenced Article frontend views do not exist. The dedicated frontend replaces that incomplete path.
- The legacy `articles` table uses `status = publish`, `visibility = public`, and `created_at` for publication eligibility.
- The existing theme frontend layout already exposes a Vue 3 global runtime; Article adds a pinned CDN preference without duplicate app mounting.

## Data model

`article_template_settings` is a singleton configuration table:

- `archive_template`: one of the four registered Archive keys.
- `detail_template`: one of the three registered Detail keys.
- `archive_per_page`: allowed values 12, 18, or 24; default 12.
- `updated_by`: nullable account id for audit attribution.
- timestamps.

Template definitions themselves are code-owned in `ArticleTemplateCatalog`; there is no editable HTML/template source stored in the database. This keeps validation, deployment, and rendering deterministic.

The Article model receives lightweight `category()` and `author()` relations. `PublicArticleQuery` owns eligibility and filtering so archive, JSON pagination, category/tag/search pages, and future widgets cannot drift apart.

## Public frontend contract

Routes remain under `/article`:

- `GET /article` — archive page.
- `GET /article/listdata` — JSON page result used by Vue pagination/search.
- `GET /article/{idOrSlug}` — article detail by numeric id or URI.

`/article/listdata` must be registered before `/{idOrSlug}`. Archive queries expose only `status=publish`, `visibility=public`, and `created_at <= now()` rows. Optional query parameters are `page`, `search`, `category`, and `tag`; every value is validated and bounded.

The first HTML render contains a complete paginated page. Vue enhances filter/search/pagination interactions after mount, preserving usable navigation when JavaScript is unavailable. The selected Archive template controls only presentation; it never changes query semantics.

## Manage Article Templates contract

The new CMS route family is:

- `GET /manage_article/templates`
- `POST /manage_article/templates`

It uses existing Manage Article permissions. The screen follows visual direction 3:

- left template selector with thumbnail previews;
- Archive / Detail surface switch;
- selected and default states;
- desktop/tablet/mobile preview controls;
- a single Save Template action;
- no arbitrary template-code editor and no create/delete template CRUD.

The management Vue app posts only `archive_template`, `detail_template`, and `archive_per_page`; server-side validation treats the catalog as the allowlist.

## Production and scale requirements

- Add a composite listing index supporting published/public/current archive queries and a category-oriented listing index.
- Select only fields required by list cards and eager-load category and author, avoiding per-card user queries.
- Use Laravel length-aware pagination with a bounded per-page allowlist. Cache is deliberately deferred until cache invalidation has a reliable Article publish/update hook.
- All template names and route keys come from server-owned catalogs, never from client-provided view paths.
- Detail content keeps current rich HTML rendering behaviour; template output does not execute user-provided code.

## Verification

- Schema/catalog singleton tests.
- Feature tests for publication eligibility, ID/URI detail lookup, filters, pagination, validation, and template persistence.
- Blade/static tests for all template registry keys and the Vue CDN/fallback contract.
- Node tests for Vue archive pagination state and manager template selection state.
- PHP lint, Node syntax checks, focused Laravel tests, `view:cache`, `git diff --check`, route inspection, and browser QA with hard reload and console/network evidence.
- Regression: existing Manage Article CRUD and all Event files/behaviour remain unchanged.
