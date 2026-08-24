# Article Editorial Studio Polish Design

## Goal

Implement the approved Editorial Studio mockups across all five Archive and three Detail Article templates, with frontend and Manage Article live preview sharing the same template Blade files.

## Scope

- Preserve the five Archive templates and three Detail templates already registered in `ArticleTemplateCatalog`.
- Make Archive cards visually consistent: image aspect ratio, rounded bordered media, two-line titles, three-line excerpts, and baseline-aligned metadata.
- Replace generic public pagination with an SSR-safe paginator that adopts the Manage Article visual language while keeping canonical URL navigation.
- Give each Detail template its approved reading treatment and previous/next Article navigation.
- Make Manage Article live preview use curated in-memory editorial sample content and generated local preview media, never production Article rows. A visible sample-content label keeps this distinction clear.
- Keep actual public frontend data, route contracts, permission, Site Config typography, and Vue SSR-navigation behavior unchanged.

## Design Contracts

### Shared visual language

- Preserve `--ph-font-family`, `--ph-font-size`, and RFS tokens; cap Article display typography within the approved mockup density rather than hard-code unrelated fonts.
- Every editorial media surface uses `overflow: hidden`, subtle 1px border, and 12–16px radius.
- Archive thumbnail links use one shared `background-image` surface with `background-size: cover`, centered positioning, and no repeat. Template-specific media selectors may set only `background-color`, never a shorthand that resets the source image.
- Every reusable Archive card owns a flex-column body. Titles clamp to two lines, excerpts clamp to three lines, and metadata sits at the bottom.
- The preview uses its existing virtual Desktop/Tablet/Mobile stage. Archive and Detail preview both display a `Sample editorial content` label.
- Article accents inherit `--ph-theme-primary`. The manager passes its active runtime color into the isolated preview URL and watches the parent root style for live palette changes; public pages retain the existing same-origin `theme-color` fallback.

### Pagination

- Server-rendered links remain the source of truth and retain `data-article-pagination-link` for Vue navigation interception.
- Render first/last pages, a bounded current window, ellipses, Previous/Next chevrons, disabled states, and a `Showing X–Y of Z articles` summary.
- Use Bootstrap pagination semantics plus `ph-pagination` theme variables. No public Vue pagination dependency is added.

### Curated preview fixture

- `ArticleTemplatePreviewFixture` returns deterministic in-memory Article models, category/author relations, preview thumbnails, one detailed body with headings, quote, and image, and a paginator with enough results to show pagination.
- Preview media lives under `storage/app/public/articles/template-preview-20260825/` and is only consumed by the Manage Template preview fixture.
- The fixture does not insert/update/delete Article database rows.

### Detail navigation

- The public detail controller resolves eligible previous/next Article neighbors using the same public eligibility and deterministic `created_at/id` ordering as the Archive list.
- Preview detail receives fixture neighbors; frontend detail receives production eligible neighbors.
- Every Detail template renders the navigation only when a neighbor exists.

## Non-goals

- No changes to Event, Page Builder, Manage Article CRUD, existing Article data, or existing template settings.
- No third-party frontend dependency.
- No conversion of public pagination to AJAX/infinite scrolling.

## Acceptance Criteria

1. All eight templates visually match the approved mockup direction at desktop, tablet, and mobile.
2. Archive card grids maintain equal card height under mixed title/excerpt length.
3. Pagination visually matches the CMS paginator language and retains accessible SSR links.
4. The live preview has curated sample content, not load-test rows, and accurately renders the selected template at the selected device size.
5. All Detail templates display refined cover/meta/body hierarchy and neighbor navigation.
6. Focused Laravel/Node tests, PHP/JS syntax, Blade cache, diff check, Graphify incremental update, and browser QA pass.
7. Changing the CMS palette from purple to another supported color, such as orange, updates Article controls and the iframe preview without a saved template change.
8. All five Archive previews preserve full source media with a rounded `cover` treatment and no horizontal overflow on desktop, tablet, or mobile.
