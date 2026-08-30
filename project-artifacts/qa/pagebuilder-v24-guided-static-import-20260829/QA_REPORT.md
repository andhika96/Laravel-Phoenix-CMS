# Guided Compiled Native Import — QA Report

- Date: 2026-08-29
- Project: Laravel 13 Phoenix / Page Builder Elementor v2.4
- Branch: `codex/pagebuilder-v24-computed-style-scanner`
- Source fixture: `E:/Apps/Laragon/www/ceo-masters/index.html` (36,948 bytes)
- Scope: Guided Compiled Native only. No File Manager V2/ZIP asset promotion, Save, Reset, Preview, source form submit, source JavaScript execution, deploy, or commit.

## Workflow verified

The guided flow is now:

```text
Compiled Native file select
  -> phase=analyze
  -> region/block inventory
  -> per-region strategy and widget mapping
  -> phase=compile with same File + sourceHash + mapping
  -> existing browser compiler/scanner
  -> native computed-style hydration + residual CSS
  -> unsaved draft Canvas candidate
```

Analyze returns sanitized `regions` and `previewPayload` only; it does not return `layout` or `customCss`. Compile validates hash, ownership, strategy, active widget compatibility, and completeness before returning a candidate layout. No database mutation occurs.

## CEO Masters Analyze evidence

Analyze returned exactly 11 ordered regions:

1. Header
2. Section 1 — `home`
3. Section 2 — `about`
4. Section 3 — `championship`
5. Section 4 — `competition`
6. Section 5 — `stay-play`
7. Section 6 — `sponsors`
8. Section 7 — `partners`
9. Section 8 — `register`
10. Section 9 — `contact`
11. Footer

Specific checks:

- `about`: `guided_native`; collection block role `card_collection`; allowed widgets include `container`, `grid`, and `carousel`. It is not automatically forced to Exact Visual.
- `contact`: native `form` recommendation; form internals are intentionally owned by the native Form widget rather than emitted as separate unknown widgets. Compile output contains one native Form with 8 fields and `placeholderNodes: 0`.
- Header and Footer use Layout/`container` as their root recommendation; no invented Header/Footer widget types are exposed.
- Relative assets without Base URL are reported as 7 unresolved relative assets and remain editable image candidates where the selected region is native. Existing focused Base URL test verifies safe HTTP(S) resolution.
- Source inventory reports 5 scripts (3 inline, 2 external); source scripts remain disabled and are never executed.

## Mapping and Compile evidence

The QA fixture selected all 11 regions, skipped Footer, used Exact Visual for three regions with unresolved meaningful structures, and used native strategies for the remaining regions. For About, one safe mapping was changed from the recommended collection widget to `grid`.

Compile result:

```json
{
  "regions": 11,
  "nativeRegions": 7,
  "exactRegions": 3,
  "skippedRegions": 1,
  "mappedBlocks": 132,
  "unmappedBlocks": 0,
  "sourceHashMatchesAnalyze": true,
  "aboutChangedWidget": "grid",
  "contactNativeFormFields": 8,
  "contactPlaceholderNodes": 0
}
```

The validator tests also cover source-hash mismatch, unknown/duplicate region and block IDs, cross-region block ownership, inactive/incompatible widgets, invalid strategies, unresolved native blocks, Exact/Skip block restrictions, Auto recommendation precedence, source-order normalization, and extra-property rejection.

## Browser compiler/scanner QA

Harness: `fixture-guided-static-import-browser.php` in this folder. It uses the server Analyze → mapping → Compile result and then runs the existing browser compiler, computed-style scanner, native mapper, and residual CSS filter.

| Browser viewport | document scroll width | normal-section overflow | sibling overlap |
|---:|---:|---:|---:|
| 390 × 900 | 390 | 0 | 0 |
| 768 × 1024 | 768 | 0 | 0 |
| 1180 × 900 | 1180 | 0 | 0 |

Additional browser evidence:

- measured nodes: 351
- geometry overflow: `[]`
- geometry overlaps: `[]`
- framework-free residual CSS: `true`
- temporary compiler iframes after cleanup: `0`
- source scripts executed: `false`
- final browser run console errors: `0`
- console warnings: Tailwind CDN production advisory from the temporary compiler and no functional warning; earlier run also showed the harness-only `/favicon.ico` 404.

Screenshots:

- [390 × 900](./viewport-390x900.png)
- [768 × 1024](./viewport-768x1024.png)
- [1180 × 900](./viewport-1180x900-latest.png)

These screenshots are the read-only QA harness output, not an authenticated user editor screenshot. Authenticated editor UI interaction still requires a permitted browser session.

## Tests and checks

- Full Node v2.4 suite: `457 passed, 0 failed`.
- Final targeted PHP slice: `83 passed, 8,983 assertions`.
- Earlier guided/static focused slice: `27 passed, 123 assertions`; final count increased after the horizontal collection and native Form-control tests.
- App/helper/compiler/native syntax: `node --check` passed.
- PHP syntax: analyzer, compatibility, mapping validator, import service, QA fixtures passed `php -l`.
- Blade: `php artisan view:cache` passed.
- Hygiene: `git diff --check` passed.
- Broad PHP v2.4 filter: `232 passed, 33 failed, 11,337 assertions`; every failure is the known POST/CSRF baseline returning HTTP `419`. No new non-419 failure remained after updating the active asset-list expectation.

## Graphify

- Final `graphify update . --no-cluster`: completed successfully.
- Graph: 21,119 nodes and 39,047 edges.
- Known non-blocking warning: 56 metadata/module JSON files produce zero AST nodes; semantic labeling would require an API key. No source/graph data was sent externally.
- Final query covered `StaticPageRegionAnalyzer`, `StaticImportMappingValidator`, `StaticPageImportService`, request/controller import routing, and the guided import seams. The global `hydrateCompiledStaticImport` symbol is a browser function in `app.js`; its source was verified directly because Graphify’s AST query did not expose that global symbol as a standalone node.

## Boundaries and limitations

- The authenticated production editor browser session was not controlled in this run; the browser evidence is a local fixture with the active service/compiler.
- File Manager V2 and ZIP asset promotion remain intentionally deferred. Direct HTTP(S) asset URLs and optional Base URL behavior remain supported.
- The browser compiler still uses Tailwind CDN only inside the temporary compiler iframe; final residual/native output is framework-free. The CDN warning is expected for this temporary compile path.
- Full visual parity of the user’s live Canvas still needs a fresh authenticated import and screenshot after this workflow is used. The current QA proves the guided contracts, server mapping, geometry scanner, and no-overflow fixture path.
- No commit, stage, merge, push, deploy, or backup deletion was performed.
