# QA — Template Options form rhythm and frame radius correction

Tanggal: 2026-09-05

## Reported symptoms

- Archive toolbar controls appeared cramped and were pushed into a narrow right-side column despite the available panel width.
- Thumbnail `Border radius` label wrapped while its unit select expanded across the row.
- Spacing between independent setting groups was too small after the two-step compact density pass.

## Root cause

### Toolbar width

The toolbar controls had been changed from flex to grid, but the older `justify-content: flex-end` remained active. CSS Grid therefore created a max-content track instead of a full-width track:

- before correction at a 429px settings panel: segmented control was **132px** wide and dependent selects were **107px** wide;
- after correction: the grid uses `grid-template-columns: minmax(0, 1fr)` plus `justify-content: stretch`.

### Radius unit width

The generic selector `.article-template-frame-fields .form-select` had greater specificity than `.article-template-radius-unit`. Its `width: 100%` expanded the radius unit to **357px** in a 429px panel and left the label with only a narrow remainder.

The correction targets the unit inside the frame fields with higher specificity, fixes it at `--article-template-unit-width` (**68px**), and gives the label a shrinkable, no-wrap flex area.

### Visual rhythm

The two-step compact pass intentionally reduced control heights, but it also reduced gaps down to 6–8px. That made distinct settings read as a single block. The correction restores hierarchy without increasing control height:

- independent settings: **16px** (`--article-template-setting-gap`);
- directly related controls: **12px** (`--article-template-related-gap`);
- group separator padding: **14px**.

## Production changes

`public/assets/css/article/article-template-manager-2026.css` adds a final scoped form-rhythm correction for Template Options only:

- full-width toolbar track and full-width category controls;
- 16px outer / 12px related form rhythm;
- fixed radius unit and no-wrap radius label;
- consistent spacing for Thumbnail, Pagination, Archive shell, Header content, sidebar, box spacing, and toolbar groups;
- no Vue, data, normalizer, persistence, or public article template behavior changed.

## Mockup parity

`project-artifacts/mockups/template-options-20260905/forms-v3/index.html` now mirrors the final group rhythm:

- desktop group gap 16px;
- mobile group gap 14px;
- nested/conditional gap 12px;
- radius unit 68px and label uses `white-space: nowrap`.

All nine desktop state screenshots were regenerated at `1440 × 900`.

## Browser evidence

### Production CSS fixture

Toolbar at `1440 × 900`:

- panel width: 429px;
- Search segmented control: **429px** full width;
- Category controls: **417px** full width inside the accent rail;
- top-level row gap: **16px**;
- related control gap: **12px**;
- horizontal overflow: `false`.

Toolbar at `640 × 900`:

- Search segmented control: **592px**;
- Category selects: **580px**;
- horizontal overflow: `false`.

Thumbnail at `1440 × 900`:

- top-level row gap: **16px**;
- frame control gap: **12px**;
- Radius unit: **68 × 38px**;
- Radius label: **351px** wide, `white-space: nowrap`.

Thumbnail at `390 × 844`:

- Radius unit: **68 × 38px**;
- Radius label: **270px** wide, no wrap;
- frame controls retain responsive four-side radius behavior;
- horizontal overflow: `false`.

Internal settings scroll remains confirmed:

- desktop: `clientHeight=740`, `scrollHeight=1337`, `scrollTop 0 → 597`;
- mobile: `clientHeight=723`, `scrollHeight=1335`, `scrollTop 0 → 612`;
- both: `canScroll=true`, no horizontal overflow, console `0 error / 0 warning`.

## Evidence files

- `form-rhythm-toolbar-1440.png`
- `form-rhythm-toolbar-640.png`
- `form-rhythm-thumbnail-1440.png`
- `form-rhythm-thumbnail-640.png`
- `form-rhythm-thumbnail-390.png`
- refreshed mockup states: `forms-v3/screens/01-header-content.png` through `09-archive-shell.png`

## Automated verification

- Red phase: new regression test failed while the source retained the max-content toolbar track and full-width radius select.
- `node --test tests/article-template-presentation.test.mjs`: **24 passed, 0 failed**.
- `node --test tests/article*.test.mjs tests/manage-article-template-manager.test.mjs`: **86 passed, 0 failed**.
- `php artisan test --compact tests/Feature/Article`: **30 passed, 411 assertions**.
- `node --check public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js`: **passed**.
- `php artisan view:cache`: **passed**.
- scoped `git diff --check`: **passed**.

## Runtime boundary

The authenticated manager route remains unavailable in this browser session without login. No credentials, Apply, or Save action were performed. The browser evidence uses the active production CSS, Bootstrap, and the actual modal/settings DOM chain; a hard refresh in the authenticated manager remains required for final click-through confirmation.

## Graphify

Graphify was not run and graph data was not modified, following the explicit user instruction.

## Backups

- `project-artifacts/backups/20260905_172140-template-options-form-rhythm-radius/`
- `project-artifacts/backups/20260905_172830-template-options-form-rhythm-screens/`
- `project-artifacts/backups/20260905_174145-template-options-form-rhythm-screens-final/`
- `project-artifacts/backups/20260905_174358-template-options-form-rhythm-qa/`
