# QA Report 12 — Section Computed-Style Scanner

- Tanggal: 2026-08-29
- Project: Laravel 13 Phoenix — Page Builder Elementor v2.4
- Branch kerja: `codex/pagebuilder-v24-computed-style-scanner`
- Source fixture: `E:\Apps\Laragon\www\ceo-masters\index.html`
- Scope: Compiled Native section scanner, computed-style mapping, residual CSS, section fallback, Base URL asset resolver, dan browser QA.

## Hasil utama

Compiled Native sekarang menjalankan urutan berikut:

```text
Source HTML
  -> framework compiler iframe
  -> computed-style scanner iframe per viewport
  -> section/node geometry + CSSOM snapshot
  -> native setting mapping
  -> section fallback untuk pola unsupported
  -> residual CSS scoped
  -> draft Canvas tanpa auto-save
```

Responsive engine utama Page Builder tidak diubah. Mode manual, `Native`, `Exact Visual`, renderer frontend manual, drag/drop, dan Save contract tetap memakai jalur masing-masing.

## Implementasi

- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`
  - Compiled Container memiliki spacing explicit `0px` saat source tidak memiliki spacing.
  - Payload menyediakan viewport contract `390/768/1180` dan section metadata.
  - Section carousel horizontal ditandai `horizontal-snap-layout`.
  - Form dengan kontrol yang didukung dipetakan ke native widget `form`; fallback hanya dipakai bila kontrol benar-benar unsupported.
  - Optional HTTP(S) Base URL dapat me-resolve asset relatif secara aman.
- `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`
  - Menambahkan validasi `baseUrl` HTTP(S), max 2048 karakter.
- `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
  - Meneruskan Base URL ke service.
- `public/js/pagebuilder_elementor_v24/static-import-compiler.js`
  - Menambahkan computed-style scanner terisolasi dengan request ID, abort, timeout, font/image readiness, CSSOM allowlist, bounds rounding, dan pseudo snapshot.
  - Menambahkan residual CSS filtering untuk native-owned layout properties, termasuk keluarga border dan radius.
  - Base display utility tetap dapat mengontrol visibility; visual CSS tidak lagi memakai broad `!important`.
- `public/js/pagebuilder_elementor_v24/static-import-native.js`
  - Helper baru untuk memetakan snapshot responsive ke native Container/Grid/Heading/Text/Image/Button/Icon/Divider.
  - Memindahkan zero padding, measured dimensions, typography, borders, visibility, absolute offsets, dan z-index ke settings.
  - Mengganti section fallback secara lokal tanpa mengubah section native lain.
- `public/js/pagebuilder_elementor_v24/app.js`
  - Menjalankan scan dan mapper sebelum `norm()`.
  - Mematerialisasi fallback section dari source marker + compiled CSS tanpa CDN framework.
  - Menjaga widget import yang menjadi anak flex-row tetap intrinsic agar ikon, label, dan CTA tidak overlap; manual widget shell tidak berubah.
  - Menampilkan tahap scanner dan statistik measured/fallback.
  - Menambahkan optional Asset Base URL.
- `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
  - Memuat helper native scanner sebelum app.
- `public/assets/css/pagebuilder_elementor_v24.css`
  - Menata input Base URL dan select mode/framework secara terbatas agar kontrol topbar static import tidak menyusut.
- `resources/pagebuilder_elementor_v24/modules/widgets/pro/form/`
  - Menjaga marker form, label, dan kontrol pada Canvas/frontend agar residual CSS field tetap memiliki target native.
- Tests:
  - `tests/pagebuilder-v24-static-import-compiler.test.mjs`
  - `tests/pagebuilder-v24-static-import-native.test.mjs`
  - `tests/pagebuilder-v24-static-import.test.mjs`
  - `tests/pagebuilder-v24-baseline-isolation.test.mjs`
  - `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php`
  - `tests/Feature/PageBuilderElementorV24StaticImportComputedStyleTest.php`
  - `tests/Feature/PageBuilderElementorV24AssetIsolationTest.php`
  - `tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php`
  - `tests/pagebuilder-v24-form-row-grid.test.mjs`

## Fixture QA artifacts

- `fixture-computed-style-browser.html` — scanner contract smoke.
- `fixture-ceo-computed-style-browser.php` — scanner memakai fixture CEO Masters aktual melalui service aktif.
- `computed-style-scanner-result.png` — hasil smoke fixture sintetis.
- `ceo-computed-style-scanner-result.png` — hasil smoke fixture CEO Masters aktual.
- Geometry checker mengabaikan fixed/sticky/absolute overlay, descendant section fallback, dan pola carousel horizontal karena semuanya merupakan overlap/overflow yang disengaja oleh source.

## Server-side fixture evidence

Conversion aktual CEO Masters menghasilkan:

- source bytes: `36,948`;
- framework: Tailwind;
- mapped nodes: `301`;
- compile eligible nodes: `351`;
- source classes: `268`;
- detected sections: `11`;
- fallback sections: `1`;
- native form: `enquiry-form`, `8` fields, submit `Submit enquiry`, icon `fas fa-arrow-right`;
- placeholder nodes: `0`;
- missing assets tanpa Base URL: `8`;
- relative assets: `ceo-masters-poster.jpg` dan enam asset `competition-assets/...`;
- fallback section: `about` (`horizontal-snap-layout`); `contact` tetap native.

## Browser evidence

Fixture CEO Masters aktual dijalankan dengan Playwright CLI pada browser nyata.

- stages: `prepare`, `load-framework`, `compile`, `extract`, `rewrite`, `validate`, `cleanup`, `scan-sections`, tiga kali `measure-layout`, `cleanup`;
- scan sections: `11`;
- scan nodes/measured nodes: `351/351`;
- fallback sections: `1`;
- residual properties removed: `166`;
- residual rules filtered: `131`;
- framework-free residual: `true`;
- compiler/scanner iframe after cleanup: `0`;
- geometry normal overflow: `[]`;
- geometry normal sibling overlaps: `[]`;
- console: favicon `404` dari fixture lokal dan dua warning Tailwind CDN; keduanya expected dari harness compiler dan bukan error scanner.

Fixture sintetis juga mengonfirmasi:

- `text-4xl` menghasilkan `36px` pada mobile/tablet;
- `lg:text-6xl` menghasilkan `60px` pada desktop `1180px`;
- `px-5` menghasilkan `20px`;
- `py-16` menghasilkan `64px`;
- computed grid tracks dan gaps masuk ke setting native;
- generated residual tidak mengandung Tailwind/Bootstrap marker.

## Automated verification

- `node --test tests/pagebuilder-v24-static-import-compiler.test.mjs tests/pagebuilder-v24-static-import-native.test.mjs tests/pagebuilder-v24-static-import.test.mjs`: **35 pass, 0 fail**.
- `node --test tests/pagebuilder-v24-*.test.mjs`: **444 pass, 0 fail**.
- Focused PHPUnit importer/compiled/scanner/frontend/asset isolation: **53 pass, 488 assertions**.
- `php artisan test --filter=PageBuilderElementorV24`: **196 pass, 33 fail, 11,073 assertions** pada smoke run penuh terbaru; seluruh 33 failure menerima `419` dari baseline POST CSRF harness, di luar scope scanner. Targeted scope terbaru tetap **53/53**.
- `node --check public/js/pagebuilder_elementor_v24/app.js`: pass.
- `node --check public/js/pagebuilder_elementor_v24/static-import-compiler.js`: pass.
- `node --check public/js/pagebuilder_elementor_v24/static-import-native.js`: pass.
- `php -l app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php`: pass.
- `php -l app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php`: pass.
- `php -l app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`: pass.
- `php artisan view:cache`: pass sebelum final report.
- `git diff --check`: pass sebelum final report.
- Graphify incremental update: dijalankan setelah perubahan source.

## Backup

Backup dibuat sebelum setiap existing-file modification pada slice ini, termasuk suffix berikut:

- `computed_style_task1`
- `computed_scanner_task2`
- `computed_style_task3`
- `computed_residual_task4`
- `computed_section_fallback_task5`
- `computed_progress_task6`
- `computed_base_url_task6`
- `qa_geometry_filter`
- `computed_scanner` untuk baseline asset test
- `computed_base_url_control` untuk CSS topbar
- `computed_border_residual_task8`
- `native_form_mapping_task1`
- `native_form_residual_task2`
- `native_form_markers_task3`
- `compiled_inline_shell_task4`
- `compiled_topbar_controls_task5`
- `native_form_qa_harness`

Backup historis dari pekerjaan Compiled Native/Custom JavaScript tetap dipertahankan.

## Batasan yang masih ada

- Authenticated browser QA langsung pada halaman builder pengguna belum dijalankan oleh agent; browser QA yang dijalankan adalah fixture lokal dengan service dan compiler aktif.
- Asset relatif membutuhkan pengguna mengisi Asset Base URL yang benar; File Manager V2/ZIP asset ingestion tetap di luar scope.
- Form source sekarang native dan dapat diedit; submit tidak dijalankan dalam QA dan tetap memakai action `message` sampai konfigurasi submit ditentukan.
- Fallback section meningkatkan fidelity visual tetapi mengurangi editability section tersebut.
- Tidak ada klaim pixel-perfect 100% untuk semua HTML arbitrary; hasil akan native bila representable dan section-local fallback bila tidak.
- Tidak ada Save, Reset, Preview, database mutation, commit, push, atau deploy yang dilakukan.
