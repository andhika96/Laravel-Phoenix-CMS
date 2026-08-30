# Page Builder v2.4 Static Import — CSS Classes/ID dan UTF-8

- Tanggal: 2026-08-28
- Runtime project: \`D:/Laragon/www/laravel-13-phoenix\`
- Fixture: \`E:/Apps/Laragon/www/ceo-masters/index.html\`
- Scope: static-import adapter only
- Commit/push/deploy/database/Save: tidak dilakukan

## Invariant kompatibilitas

Fitur manual Page Builder v2.4 tidak diubah. Perubahan hanya berada pada:

- \`app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php\`
- \`tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php\`

Yang dipastikan tidak berubah:

- \`public/js/pagebuilder_elementor_v24/app.js\`
- seluruh \`resources/pagebuilder_elementor_v24/modules\`
- seluruh \`resources/views/pagebuilder_elementor_v24\`
- route dan controller v2.4
- Page Builder v2.3
- schema/persistence/database

\`StaticPageImportService\` hanya dipanggil dari endpoint \`importStatic()\` dan focused tests.

## Implementasi

- Menambahkan deklarasi UTF-8 pada parser dokumen dan parser rich text.
- Source \`id\` yang valid dipreservasi ke \`settings.cssId\`.
- Source class token yang aman dipreservasi urut dan unik ke \`settings.cssClass\`.
- Responsive/state classes seperti \`lg:max-w-7xl\` dan \`hover:bg-white/5\` tetap utuh.
- Nested rich-text \`span\` dapat mempertahankan \`id\` dan \`class\`.
- Attribute berbahaya/tidak diizinkan tetap dibuang oleh sanitizer.
- Report importer mendapat counter:
  - \`preservedIds\`
  - \`preservedClasses\`
  - \`rejectedSourceAttributes\`

## TDD

RED yang diamati:

- source IDs/classes: gagal karena \`cssId\` belum ada;
- UTF-8: expected \`©\`, actual \`Â©\`;
- nested rich-text: \`span class/id\` hilang dari HTML hasil sanitizer.

GREEN final:

- focused importer: **15 passed, 80 assertions**.

## Fixture CEO Masters

- frameworks: \`["tailwind"]\`
- mappedNodes: 303
- preservedIds: 13
- preservedClasses: 1,221
- rejectedSourceAttributes: 0
- UTF-8 \`October · 2026\`: benar
- root class \`gold-button\`: ada
- nested span class: ada
- missingAssets: 8

## Verification

- PHP lint service/test: pass.
- \`php artisan view:cache\`: pass.
- \`node --check app.js\`: pass.
- \`node --check frontend-runtime.js\`: pass.
- \`git diff --check\`: pass.
- full Node v2.4: **401 passed, 0 failed**.
- Node suite masih menghasilkan 768 baris Vue warning historis.
- full PHPUnit v2.4: **151 passed, 33 failed, 10,525 assertions**.
- Semua 33 failure menerima HTTP 419; tidak ada failure non-419.

## Backup

- \`D:/Laragon/www/laravel-13-phoenix/app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php.bak_20260828_213847_static_import_classes_utf8\`
- \`D:/Laragon/www/laravel-13-phoenix/tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php.bak_20260828_213847_static_import_classes_utf8\`

Backup fase grid/flow/visibility sebelumnya tetap dipertahankan.

## Graphify

Incremental update berhasil:

- 20,566 nodes
- 35,828 edges
- 1,479 communities
- 2 code files re-extracted
- \`graphify check-update .\`: exit 0

## Batasan tersisa

- Preservation class belum memuat CSS Tailwind/Bootstrap secara otomatis.
- Custom \`<style>\` masih belum diekstrak/scoped.
- HTML tunggal tidak membawa sibling assets; 8 asset relatif tetap missing.
- CTA Tailwind dan icon Phosphor belum seluruhnya dipetakan ke Button/Icon widget.
- CSS ID selector parity pada frontend perlu ditangani bersama fase scoped CSS agar tidak bentrok dengan internal node ID.
- Browser re-import dan visual QA setelah hard reload belum dilakukan oleh agent pada fase ini.

## Pengaruh UI UX Pro Max

Preservation menjaga responsive/state class secara verbatim agar breakpoint dan state tidak kehilangan makna. Perubahan tidak memperkenalkan style baru atau mengubah komponen manual yang sudah stabil.
