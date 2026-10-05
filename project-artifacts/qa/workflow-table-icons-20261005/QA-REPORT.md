# Workflow Table, Icon, Icon List — QA 5 Oktober 2026

Project: D:\Laragon\www\laravel-13-phoenix.

## Permintaan dan scope

Tambah tabel dengan jumlah row/column custom serta resize seperti diagrams.net. Klarifikasi user: kedua jenis icon diminta, yaitu picker + standalone Icon dan Icon List berisi ikon/teks. VueJS3 tetap dipakai.

Tiga visual nodes: `table`, `icon`, `icon-list`. Disimpan di graph JSON yang sudah ada; tidak membutuhkan migration baru. Visual-only diagram boleh publish. Mixed workflow boleh memuat visual widgets, tetapi hanya node executable yang dijalankan. Visual nodes tidak memiliki execution ports dan edge eksekusi ke visual ditolak.

## Implementasi

- Table1..20 rows/columns, editable cells (max500 karakter), insert/delete sebelum/sesudah cell terpilih, header row toggle, alignment, font size10..32, text/background/header/border colors.
- Row height28..240px dan column width60..600px; resize via numeric controls, keyboard handles, drag boundaries dan corner proportional resize. Pointer deltas dibagi zoom. Existing cells dan ukuran yang tidak dihapus dipertahankan. Undo/redo satu checkpoint per gesture; pointer listeners dibersihkan termasuk cancel/unmount.
- Standalone Icon: local Font Awesome choice, glyph size12..256, width/height80..1000, color.
- Icon List:1..30 items, choose icon per item, editable text, add/remove/up/down, width160..1000, gap0..40, font10..32, icon/text colors.
- Font Awesome memakai CSS/fonts/metadata lokal5.15.3 yang sama dengan Page Builder. StylesRegular/Solid/Brands/Light/Duotone, search name/label/aliases, lazy fetch, display100 item+Load more. No dependency/font installation, no raw SVG injection.
- Node sizing/Fit/collision mengikuti ukuran Table/Icon/List. Signed canvas coordinates fix sebelumnya dipertahankan.
- Backend validates rectangular ordered arrays, counts, sizes, color hex, icon class pattern, text limits. Laravel empty-string-to-null conversion dinormalisasi kembali ke strings untuk empty cells/list texts. Visual configs max256KiB, normal executable config tetap8KiB.

Scope tidak mencakup formula spreadsheet, merge/swimlane/container tables, atau full draw.io parity. Widgets merupakan elemen visual, tidak mengirim email/webhook atau menjadi action otomatis.

## Source yang diubah

- `public/assets/js/vue3/workflow/workflow-graph.js`
- `public/assets/js/vue3/workflow/workflow-editor.js`
- `public/assets/js/vue3/workflow/workflow-icons.js` (baru)
- `public/assets/css/workflow-editor.css`
- `resources/views/workflow/editor.blade.php`
- `app/Support/Workflow/WorkflowGraph.php`

Tests: `WorkflowEditorTest.php`, `workflow-visual-widgets.test.mjs` (baru), `workflow-icons.test.mjs` (baru), `workflow-editor-visual.test.mjs` (baru). Source/hash dicatat pada `source-manifest.json`.

Backup sebelum perubahan: `project-artifacts/backups/20261005_110244_workflow-table-icons/` beserta timestamped review rounds. Semua original unrelated dirty files cocok dengan baseline SHA256 (0 mismatch).

## Verifikasi terbaru

```powershell
node --test project-artifacts/tests/workflow-*.test.mjs
node --check public/assets/js/vue3/workflow/workflow-editor.js
node --check public/assets/js/vue3/workflow/workflow-icons.js
node --check public/assets/js/vue3/workflow/workflow-graph.js
php vendor/phpunit/phpunit/phpunit --configuration project-artifacts/tests/workflow-phpunit.xml --no-progress --do-not-cache-result
php -l app/Support/Workflow/WorkflowGraph.php
git diff --check
```

Node32/32 lulus. PHP17/17 lulus,104assertions. JS/PHP syntax lulus; git diff whitespace lulus (hanya warning CRLF pada file Article lama). Tests memakai SQLite memory dan do-not-cache-result.

Tests mencakup grid preserve/resize/insert/delete, size clamps, geometry/Fit, unsafe icon/color and malformed cells refusal, visual-only publish/simulation422, mixed workflow ignores visual nodes, save/publish/duplicate/run snapshot roundtrip, empty text strings, picker styles/search/pagination, zoom pointer resizing, selection, listener cleanup, no execution edges to visuals, and correct padded rectangle collision for different sizes.

Independent backend/graph dan final integrated UI review: tidak ada temuan material tersisa. Import helpers/icons menggunakan query version20261005-table-icons untuk menghindari stale module cache. Browser reload terakhir:3visualwidgets, tabletextutuh, glyphclassesfabfa-github/falfa-file-alt/fasfa-check dan0consoleerrors. Root browser QC menemukan render getter yang memutasi config dan phantom AABB overlap pada widget berukuran berbeda; keduanya diperbaiki sebelum penyelesaian.

## Browser runtime QC

CMS browser tidak memiliki sesi login; QA menggunakan harness sebelumnya dengan Blade/JS/CSS produksi, layout minimal, JSON fixture lokal. Metadata/fonts/assets juga lokal. Tidak mengubah workflow records pada DB aktif.

Verified:

1. Add Table, fill `Nama`, `Status`, `Budi`; grow counts with native stepper, insert row/column after selected cell, delete empty inserted row/column. Contents survived, final5x4 grid20cells.
2. At54%zoom, column pointer drag30px → renderedwidth delta30.27px; row drag25px → renderedheight delta24.90px. Whole table +40x/+35y → rendered delta40.50x/35.64y; differences are pixel-rounding.
3. Icon picker metadata loaded successfully. Search `github`, Brands filter, select GitHub and apply `fab fa-github`; glyph rendered.
4. Icon List: edit text, add item, reorder. Search `file-alt`, Light filter, select Alternate File and apply `fal fa-file-alt` to list item. Finaltexts: Dokumentasi/Pemeriksaan.
5. Save → publish → reload kept table values/sizes and both icon configs/list order. Drag all visual widgets +Fit worked with actual dimensions.
6. No browser console errors/warnings at final checkpoints.
7. Tablet768x1024: scrollWidth753; mobile390x844: scrollWidth375. No document horizontal overflow. Viewport override reset.

Screenshots: `widgets-final.jpg`, `widgets-saved.jpg`, `icon-picker.jpg`, `widgets-tablet.jpg`, `widgets-mobile.jpg`.

Limit: authenticated Firefox/CMS theme E2E still unverified. Browser save/publish uses harness fixture; real backend persistence is verified through PHP integration tests. No browser upload/import picker QA in this change; export/import graph roundtrip remains covered by existing Node checks.

## Referensi, memori, Graphify

- Link dokumen diagrams.net user loaded but requires Google Drive authorization; no authorization granted. Official table guide inspected: https://www.drawio.com/docs/manual/shapes/tables/ for row/column editing and resize behavior.
- Relevant local source: Page Builder v2.4 `public/js/pagebuilder_elementor_v24/app.js` icon metadata URL/styles/buildFontAwesomeIconLibrary; local icons.json metadata and fonts; Workflow canvas/inspector/helpers/backend; old QA harness.
- E:\AI\Memories unavailable. C:\Users\aruna\.codex\memories\MEMORY.md searched for Icon List/Icon Picker context. Source active remains truth.
- Graphify query `IconPicker fontawesome PageBuilderElementorV24` budget1500 used for orientation; actual v2.4 source/metadata verified separately. Graph dated2026-10-04 does not contain new Workflow nodes. No automatic graph update.
- No migration/schema/active workflow DB mutation, dependency installation, commit/push/reset/clean. Existing five workflow tables still0records; menu1entry/adminpermissions2 unchanged.

