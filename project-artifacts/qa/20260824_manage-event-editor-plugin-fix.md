# Manage Event editor plugin fix QA

Tanggal: 2026-08-24
Project: `D:\Laragon\www\laravel-13-phoenix`

## Scope

- Mengganti Content textarea Event menjadi CKEditor 5 dengan konfigurasi Article yang sudah aktif.
- Mengarahkan CKFinder upload Content ke resource type `Events`.
- Mengganti input `datetime-local` occurrence menjadi `VueDatePicker` untuk jadwal dan registration window.
- Mengubah timezone occurrence menjadi select terkontrol.
- Membuat slug otomatis dari Title dengan opsi kembali ke auto-generated slug.
- Menjaga summary, tags, lokasi, kapasitas, dan override menit sebagai input metadata yang memang perlu diisi admin.

## File dimodifikasi

- `resources/views/manage_event/partials/form.blade.php`
- `resources/views/manage_event/manage_event_add.blade.php`
- `resources/views/manage_event/manage_event_edit.blade.php`
- `public/assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js`
- `tests/Feature/Event/ManageEventTemplateTest.php`

## Backup

Backup dengan SHA-256 tersedia di `project-artifacts/backups/20260824_210000_manage_event` dengan suffix `event-editor-plugin`.

## Verifikasi lulus

- `php artisan test tests\\Feature\\Event --no-ansi`: 15 passed, 82 assertions.
- `php artisan view:cache --no-ansi`: passed.
- `node --check` seluruh asset JavaScript Event/Manage Event: passed.
- PHP lint file scope: 4 files passed pada final gate; lint seluruh Event sebelumnya juga passed.
- `git diff --check`: passed.
- Partial Blade dirender via Tinker: `PARTIAL_RENDERED_BYTES=12742`, tanpa error Blade.
- Runtime helper probe slug/date: passed.
- Asset HTTP smoke check: CKEditor, CKFinder, VueDatePicker CSS/JS, dan asset Event seluruhnya HTTP 200.
- Regression Manage Article responsive: 4 passed.

## Browser QA

Browser in-app berhasil membuka URL target, tetapi sesi tidak login dan diarahkan ke `/auth/login`. Tidak ada kredensial yang dimasukkan atau bypass autentikasi. Karena itu toolbar CKEditor, DatePicker popup, slug auto-sync, responsive desktop/mobile, console, dan network flow authenticated belum dapat diklaim sebagai terverifikasi browser. Tab login dipertahankan sebagai handoff untuk QA lanjutan setelah user login.

## Full regression

`php artisan test --no-ansi`: 689 passed, 1 pre-existing failure pada `Tests\\Feature\\PageBuilderElementorV23ShellTest` (expected 200, received 302); failure tidak terkait Manage Event.

## Graphify

Incremental code-only update selesai: 5 file code diekstrak ulang; graph menjadi 20,134 nodes dan 34,768 edges. `cluster-only --no-viz` juga selesai. Community labels belum direfresh melalui LLM.
