# Article Template Options QA

Date: 2026-08-25  
Scope: per-template Article header, archive toolbar, responsive grid, draft preview, and category selector.

## Delivered

- Replaced Balanced Card Grid's numeric category input with an `All categories` select. It contains only active categories with at least one eligible public Article.
- Added `archive_template_options` and `detail_template_options` JSON columns through migration `2026_08_25_000009` (ran in batch 28).
- Added normalized defaults and allowlists for header visibility/copy, toolbar enable/position, and responsive grid counts.
- Browser QA exposed a missing-option boolean fallback that initially hid default header and toolbar content; a regression test now protects the default-enabled contract.
- Archive supports independent Search and Category filter controls at left, center, or right. Controls in the same position form one responsive group.
- Editorial Journal, Mosaic Magazine, and Balanced Card Grid support 1–4 desktop, 1–3 tablet, and 1–2 mobile columns.
- Detail title remains dynamic from the Article. Eyebrow/dek remain dynamic by default and may use custom override text.
- Modal outer layout follows Awesome Admin Add New User: centered `ph-modal-dialog`, rounded content, header/body/footer, Cancel and Apply. Apply changes only the Vue draft; Save Template persists it.
- Live preview carries a normalized active option snapshot in the iframe URL, including pagination links.
- Preview-fixture Search and Category Filter submissions are now intercepted inside the iframe, so they cannot leave the unsaved preview route or discard its active draft options.

## Modal design preview

- `project-artifacts/mockups/20260825_article-template-options-modal/template-options-modal-preview.png`
- SHA-256: `46151193AA909B7786EC436D0C9C872AB15CC026A9BC13CA51FD594CD7508EF2`

## Verification

- Migration ran successfully and is recorded as batch 28.
- PHP Article suite: 15 tests, 169 assertions passed.
- Node Article/manager contracts: 29 passed, 0 failed.
- PHP lint passed for new option/category support and both Article controllers.
- Blade `view:clear`, `view:cache`, and `git diff --check` passed.

## Browser QA

- Authenticated in-app browser QA opened `Manage Article Templates` successfully.
- Template Options modal opened from the active Archive and Detail templates. Its modal structure exposed Header Content, Archive Toolbar, responsive Grid Columns, Cancel, and Apply changes.
- Applied a draft-only Minimal Reading List change: Category filter enabled at center. The iframe URL updated with the option snapshot; the modal closed; Save Template remained unclicked.
- Balanced Card Grid iframe exposed the category select with `All categories`, seven active load-test categories, and `Uncategorized`; no numeric field remains.
- Applied a Detail custom description for Focused Reader and verified the exact text inside the iframe.
- Applied Editorial Journal grid values of four desktop columns and two mobile columns; computed iframe grids were four equal desktop columns and two equal mobile columns.
- After a fresh manager reload, clicked both preview Search and Balanced Card Grid Category Filter without clicking Save Template. The manager URL and iframe source both stayed unchanged, so neither action can leave the draft preview.
- Responsive typography matches the loaded CMS token contract. Computed Article H1 values: Desktop compact `29.375px`, Tablet `30.1px`, Mobile `27.85px`, with the same `--ph-adaptive-font-size` and RFS token path as CMS.
- Reloaded the manager without Save Template; the selected/draft options reset, proving Apply does not persist data. Browser console errors/warnings were empty.

## Graphify

- Incremental source-only update covered the latest preview guard and its regression test. Generated QA/mockup artifacts were deliberately excluded.
- Graph health: `20,375` nodes, `35,174` edges, no dangling, missing, self-loop, or collapsed edges.
