# QA Report 09 — Page Builder v2.4 Custom JavaScript

Tanggal verifikasi: 2026-08-29 02:00 WIB  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Plan: `project-artifacts/plans/2026-08-28-pagebuilder-v24-custom-javascript-implementation-plan.md`

## Kesimpulan

Implementasi Custom JavaScript page-level selesai pada scope plan tanpa mengubah v2.3 atau mengaktifkan arbitrary JavaScript di Canvas editor utama.

Mode yang tersedia:

| Mode | Perilaku |
| --- | --- |
| `disabled` | Kode disimpan, tidak dieksekusi. Default untuk page baru dan legacy row. |
| `exact_sandbox` | Kode hanya ditambahkan ke `static_html` Exact Visual iframe dengan `sandbox="allow-scripts"`; tidak masuk parent editor. |
| `published` | Kode hanya dirender pada route publik `/pages/{uri}` ketika status page `publish`. |

## Perubahan yang diverifikasi

- Migration reversible menambahkan `page_builder.custom_js` nullable dan `custom_js_mode` default `disabled`.
- Request v2.4 memvalidasi mode dan batas payload 100 KB.
- `CustomJavaScriptPolicy` menormalisasi line ending, menolak wrapper `<script>`, external script, JavaScript URL, dynamic evaluation, control/null byte, dan mode invalid.
- Policy mengembalikan `warnings`, `blocked`, serta `diagnostics` terstruktur dengan `line` dan `column`.
- Page Settings memiliki modal editor terpisah dari Custom CSS: mode selector, code editor, byte/line count, diagnostic, focus trap, Escape close, dan konfirmasi kedua untuk `published`.
- Static import hanya melaporkan jumlah inline/external script, selector, dan warning secara default. Raw inline code hanya tersedia setelah aksi copy eksplisit dan checkbox konfirmasi.
- Exact sandbox menjaga source DOM ID/class dan tidak memberi `allow-same-origin`, `allow-forms`, `allow-popups`, atau top-navigation permission.
- Published script memiliki marker `data-pb-custom-javascript="published"`, lolos policy, dan ditempatkan setelah runtime frontend.
- Route editor/preview, draft, disabled, invalid code, serta renderer v2.3 tidak mengeluarkan marker executable Custom JavaScript.

## Permission dan audit boundary

Graph/source audit menemukan route editor v2.4 memakai middleware yang sudah ada (`auth`, `checkSuspended`) dan controller memakai ownership guard berdasarkan `user_id` serta `editor_version=2.4`. Tidak ditemukan audit-log seam khusus di jalur v2.4; karena itu tidak dibuat logger atau permission system paralel. Mode `published` tetap dilindungi oleh akses editor/ownership yang sudah berjalan, dengan peringatan eksplisit di UI.

Tidak ditemukan infrastructure CSP nonce pada source aplikasi saat audit ini. Published mode tetap route/status/policy-gated; Exact sandbox memakai CSP internal iframe yang sudah ada.

## Pengujian

Lulus:

- Focused combined PHPUnit run untuk policy, Custom JavaScript, migration, Exact sandbox, dan source-script import — **22 tests, 86 assertions lulus**.
- `php artisan test tests/Unit/PageBuilderElementorV24CustomJavaScriptPolicyTest.php` — 6 tests, 20 assertions.
- `php artisan test tests/Feature/PageBuilderElementorV24CustomJavaScriptTest.php` — seluruh test lulus.
- `php artisan test tests/Feature/PageBuilderElementorV24CustomJavaScriptMigrationTest.php` — migration up/down dan legacy default lulus.
- `php artisan test tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php` — source iframe dan Exact sandbox lulus.
- `php artisan test tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php --filter="source_scripts|exact_mode|native_mode"` — seluruh test lulus.
- `php artisan test tests/Feature/PageBuilderElementorV24StaticImportTest.php --filter=source_script` — explicit `includeScripts` lulus.
- `node --test tests/pagebuilder-v24-*.test.mjs` — **411/411 lulus**, exit code 0.
- `node --check public/js/pagebuilder_elementor_v24/app.js` — lulus.
- PHP syntax check pada policy, import service, controller, tiga request, dan migration — lulus.
- `php artisan view:cache` — berhasil.
- `git diff --check` — tidak menemukan whitespace error.

`php artisan test --filter=PageBuilderElementorV24` menghasilkan **181 pass dan 33 failure**. Seluruh 33 failure terklasifikasi sebagai baseline test harness CSRF yang menerima HTTP 419 pada request POST; tidak ada failure non-419 setelah regression assertion static-import diperbaiki.

Full `php artisan test` menghasilkan **717 pass dan 68 failure**. Failure tersebut berasal dari baseline POST CSRF 419 lintas test harness serta satu v2.3 shell authentication 302; tidak ada failure production baru yang terkait implementasi Custom JavaScript. Test suite sempat menghapus 14 fixture SVG tracked di `storage/framework/testing/disks/...`; seluruh 14 path dikonfirmasi sebagai test fixture dan dipulihkan dari `HEAD`.

## Browser/runtime boundary

Blade/feature evidence memverifikasi:

1. Page published + mode `published` menghasilkan satu script marker pada public route.
2. Page draft, mode `disabled`, mode `exact_sandbox` pada parent renderer, editor preview, invalid code, dan v2.3 tidak menghasilkan published marker.
3. Exact sandbox menghasilkan marker pada atribut iframe dan script berada di `srcdoc` ter-escape, bukan sebagai `<script>` parent.
4. Static import default tidak mengembalikan `inlineCode`; explicit copy request mengembalikan kandidat inline untuk editor.

Browser QA interaktif read-only belum dijalankan karena tidak ada session browser yang dapat dikontrol secara aman pada turn ini. DB migration juga belum diaplikasikan ke database development/live; yang diverifikasi adalah migration test isolated SQLite.

## Backup dan recovery

Backup timestamp dibuat sebelum perubahan lanjutan pada file yang sudah ada, termasuk:

- `app/Support/PageBuilderElementorV24/StaticImport/StaticPageImportService.php.bak_20260829_011200_custom_javascript_scripts`
- `app/Http/Requests/Page_Builder_Elementor_V24/ImportStaticPageRequest.php.bak_20260829_011200_custom_javascript_scripts`
- `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php.bak_20260829_011200_custom_javascript_scripts`
- `tests/Unit/PageBuilderElementorV24StaticPageImportServiceTest.php.bak_20260829_011200_custom_javascript_scripts`
- `tests/Feature/PageBuilderElementorV24StaticImportFrontendDependencyTest.php.bak_20260829_011200_custom_javascript_scripts`
- `tests/Feature/PageBuilderElementorV24StaticImportTest.php.bak_20260829_013000_custom_javascript_scripts`
- Backup editor/controller/request/frontend/test yang dibuat pada `20260829_000243_custom_javascript_execution` dan backup CSS recovery `public/assets/css/pagebuilder_elementor_v24.css.bak_20260829_020000_custom_javascript_editor`.

Rollback tetap aman: set mode page ke `disabled` atau restore source dari backup. Data `custom_js` tidak perlu dihapus.

## Graphify

Graphify di-update incremental/raw dengan `graphify update . --no-cluster`; hasil: **20.765 nodes dan 38.294 edges**. Backup, QA, generated output, dan secrets dikecualikan melalui `.graphifyignore`. Graphify melaporkan 56 metadata/source files tanpa node; tidak ada dampak pada jalur source Custom JavaScript.

## Status lanjutan

- File Manager V2/ZIP asset tetap di luar scope sesuai keputusan user.
- Tidak ada commit, push, deploy, browser Save, publish action, atau perubahan v2.3.
- Sebelum mengaktifkan migration di environment nyata, lakukan backup database dan jalankan migration melalui prosedur deployment proyek.
