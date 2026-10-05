# Workflow Editor Vue 3 — QA 5 Oktober 2026

Project: D:\Laragon\www\laravel-13-phoenix.

## Hasil implementasi

Halaman `/workflow` dan `/workflow/create`, editor `/workflow/{id}/edit`. Vue global 3 yang sudah dimuat tema CMS, tanpa dependency baru. Daftar/search/filter/pagination, canvas node/koneksi SVG, palette, inspector, drag handler, keyboard alternatif, undo/redo, zoom/pan/fit, template Blank/Approval/Form/Automation, JSON import/export, simpan draft, publish snapshot, duplicate, archive, dan simulasi tersimpan.

Node: Trigger, Condition, Approval, Delay, Email, Webhook, Update, End. Draft boleh belum lengkap. Publish mensyaratkan satu Trigger, minimal End, output valid lengkap, graph tanpa cycle, semua node dapat dicapai, konfigurasi valid. Batas backend 200 nodes/400 edges. Revision conflict mendapat 409 untuk update/publish/archive/simulate. Non-admin yang diberi custom permission hanya dapat mengakses workflow miliknya. Admin dapat mengelola seluruh workflow. Akun suspended ditolak.

Status publish berarti definisi telah divalidasi dan dibuat snapshot. Ini belum mengaktifkan trigger otomatis atau mesin produksi.

## Database aktual

Environment `local`; MySQL lokal, database `laravel_12_phoenix`, prefix `lr_`.

Hanya dua migration baru dijalankan:

- `2026_10_05_000001_create_workflow_tables.php`
- `2026_10_05_000002_add_workflow_navigation.php`

Perintah:

```powershell
php artisan migrate --path=database/migrations/2026_10_05_000001_create_workflow_tables.php --path=database/migrations/2026_10_05_000002_add_workflow_navigation.php --pretend
php artisan migrate --path=database/migrations/2026_10_05_000001_create_workflow_tables.php --path=database/migrations/2026_10_05_000002_add_workflow_navigation.php
php project-artifacts/scripts/workflow-database-verification.php
```

Kedua migration `DONE`. Lima tabel terverifikasi: `lr_workflows`, `lr_workflow_versions`, `lr_workflow_runs`, `lr_workflow_run_logs`, `lr_workflow_approval_tasks`. Verifikasi terakhir: semua masih 0 record; data QA berada di SQLite memory atau fixture JSON, bukan database aktif. Menu workflow satu entry, custom permission admin dua row. Tidak menjalankan migration lain. Rollback schema diuji di SQLite; rollback navigasi menghapus default yang belum diubah dan mempertahankan customizations, tidak diuji down pada database aktif.

## Pengujian terbaru

```powershell
php vendor/phpunit/phpunit/phpunit --configuration project-artifacts/tests/workflow-phpunit.xml --no-progress --do-not-cache-result
node --test project-artifacts/tests/workflow-graph.test.mjs project-artifacts/tests/workflow-editor-interaction.test.mjs
node --check public/assets/js/vue3/workflow/workflow-editor.js
node --check public/assets/js/vue3/workflow/workflow-graph.js
node --check public/assets/js/vue3/workflow/workflow-list.js
git diff --check
```

- PHP: 12 test, 79 assertion, seluruhnya lulus. Isolasi DB SQLite memory dipaksa lewat XML.
- Node: 12 test, seluruhnya lulus.
- PHP lint: 11 file PHP runtime/routes/migration lulus.
- JavaScript syntax: 3 file lulus.
- Blade view cache sempat dikompilasi berhasil; render template produksi juga diuji melalui harness.
- Diff whitespace: lulus; hanya warning CRLF pada file Article lama, tidak diubah pekerjaan ini.
- Route inspection via application bootstrap menemukan 11 route workflow dengan auth/checkSuspended/WorkflowAccess.
- `php artisan route:list --path=workflow` terhalang controller API lama `App\Http\Controllers\Api\v1\Testing\Testing_Controller` yang tidak tersedia. Tidak diperbaiki karena di luar scope; bukan kegagalan route workflow.

Pengujian mencakup CSRF token (419 tanpa token, 201 dengan token), guest/permission/ownership/suspension, draft persistence, search description, stale revision, immutable version, publish idempotency, invalid type/duplicate ID/dangling edge/cycle/orphan, konfigurasi salah, simulasi kondisi deterministik dan tanpa mail/HTTP side effects, schema rollback, additive menu/up idempotency/down preserving customizations, keyboard/undo/redo/connections, non-overlapping palette insertion, archived controls, accessible mobile controls, invalid import protection, export/import graph round trip.

## Browser QA

Browser aplikasi aktual `/workflow` diarahkan `/auth/login`, sehingga authenticated CMS layout/menu/data E2E belum diverifikasi.

Harness lokal di `project-artifacts/qa/workflow-20261005/router.php` menggunakan Blade produksi, JS/CSS produksi, Vue/axios/Bootstrap lokal yang sama. Layout minimal khusus QA, fake persistence JSON, dan WorkflowGraph PHP produksi untuk simulasi. Ini bukan login bypass dan bukan QA database aktif.

Runtime harness terverifikasi:

- create dari template Approval → simpan → publish → simulasi → log approval/end;
- edit name/config, add node, port connection lewat Enter, undo/redo;
- list menampilkan workflow dan status;
- tidak ada console error/warning pada checkpoint akhir;
- viewport desktop default, tablet 768x1024, mobile 390x844; tidak ada document horizontal overflow (tablet scrollWidth 753/viewport 768; mobile 375/390);
- tombol Fit/Delete mobile memiliki accessible names setelah QC.

Screenshot: `editor-desktop-final.jpg`, `editor-tablet.jpg`, `editor-mobile.jpg`, `workflow-list.jpg`.

Download blob export via browser tooling mengalami timeout, sehingga unduhan native browser dan picker import belum diverifikasi E2E. Pembentukan JSON export, file payload, import round trip, dan invalid import refusal diverifikasi runnable Node tests.

## Scope yang belum aktif

Trigger Form/data belum dihubungkan ke modul lain. Tidak ada executor produksi, scheduler resume, inbox approval manusia, pengiriman email/webhook nyata, atau mutasi data modul dari workflow. Approval simulasi selalu disetujui, delay dilewati, Update hanya mengubah context simulasi. Tabel run/log/approval disiapkan; kesiapan tabel tidak berarti integrasi tersebut aktif.

## Source dan backup

Source baru: controller Workflow, WorkflowAccess, tiga model, WorkflowGraph, dua migration, routes/workflow.php, dua Blade views, CSS, tiga JS. Source lama yang dimodifikasi hanya `routes/web.php` (require route), `routes/breadcrumbs.php` (dua breadcrumb).

Daftar lengkap source/hash: `source-manifest.json` di folder QA ini.

Backup: `project-artifacts/backups/20261005_095620_workflow-editor/`, termasuk web.php/breadcrumbs.php, backup setiap perbaikan berikutnya, baseline hash file dirty lama, snapshot database-navigation-before-migration.json dan backup-manifest.json.

Hash seluruh file dirty lama dibanding baseline terakhir: 0 perubahan. Perubahan Article/Page Builder milik pengguna tidak ditimpa. Tidak ada commit/push/reset/clean.

## Konteks yang dibaca

Memori resmi E:\AI\Memories tidak tersedia. Quick pass C:\Users\aruna\.codex\memories\MEMORY.md untuk pola Manage Event/Vue CMS, Form dan navigasi. Memory hanya konteks; source aktif diperiksa ulang.

Source referensi aktif yang dibaca: routes/web.php, routes/breadcrumbs.php, bootstrap/app.php, config/auth.php, config/queue.php, composer.json/package.json, Account.php, CheckSuspended.php/ArunaPermission.php, controller/view/JS Manage Event, migration navigasi Event, test Event, cms_layout Arunika Lucent, project-artifacts/README.md. Semua source Workflow diperiksa langsung.

Graphify digunakan sebagai peta: query ManageEventController/manage_event/permissions/navigation/routes, FormSubmissionHandler, konteks Vue/Vite/theme; explain Manage Event oleh agent UI. Graph lama tidak memuat Workflow yang baru. Tidak memakai inferred relation sebagai bukti runtime. Graph tidak diperbarui, sesuai instruksi manual.

Dua label `Workflow editor` dan `Skip to workflow canvas` sempat diregistrasikan helper t() ke tabel language saat render harness pertama; setelah itu harness menggunakan identity translation untuk menghindari mutasi lanjutan.

