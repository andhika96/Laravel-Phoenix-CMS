# Manage Event thumbnail CKFinder design and compact duration picker QA

Tanggal: 2026-08-24
Project: `D:\Laragon\www\laravel-13-phoenix`

## Design decision

- User memilih desain Thumbnail source switcher: `Upload File` dan `CKFinder library`.
- Tombol remove dipertahankan sebagai tombol trash outline `btn-outline-danger`, sesuai pola Event/Add Article sebelumnya.
- Picker Reminder override dan Cancel cutoff override dipadatkan menjadi `14px` dengan tinggi `38px`.

## Implementasi

- `resources/views/manage_event/partials/form.blade.php`
  - menambah source switcher, tombol Browse CKFinder, readonly selected-path, preview checkerboard, dan tetap tombol trash outline.
  - menambah CSS scoped `.event-duration-picker` untuk ukuran input compact.
- `public/assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js`
  - mode thumbnail `upload`/`ckfinder`, validasi path browser-side, preview, clear/remove, dan label file.
  - memakai `CKFinder.modal`, bukan `CKFinder.popup`, karena modal dapat diverifikasi dalam halaman.
- `app/Http/Requests/Event/AddEventRequest.php` dan `EditEventRequest.php`
  - memvalidasi source CKFinder dan URL hanya pada `/storage/ckfinder/events/...`.
- `app/Http/Controllers/Web/Manage_Event/Manage_Event_Controller.php`
  - mengimpor image CKFinder Events ke pipeline thumbnail Event, menghasilkan large/small rendition baru.

## Test-driven evidence

- RED: test template gagal sebelum source switcher/compact picker tersedia.
- RED: HTTP flow gagal sebelum CKFinder image diimpor ke thumbnail Event.
- GREEN:
  - `ManageEventTemplateTest`: 4 passed, 57 assertions.
  - `EventHttpFlowTest`: 5 passed, 22 assertions, termasuk import image CKFinder dan removal thumbnail tersimpan.
  - Full `php artisan test --no-ansi`: 693 passed, 1 failure pre-existing `Tests\\Feature\\PageBuilderElementorV23ShellTest` (expected 200, received 302).
  - Node syntax, Blade view cache, PHP lint, dan `git diff --check`: passed.

## Browser QA authenticated

- Reminder dan Cancel cutoff computed font size: `14px`; min height: `38px`.
- Desktop: source switcher berfungsi, CKFinder mode menampilkan Browse button dan checkerboard preview.
- Browse CKFinder membuka modal dalam halaman (bukan popup) langsung di resource root `Events`; console error/warning baru kosong.
- Mobile 390x844: kedua picker tetap `14px`, switcher responsif, Browse CKFinder terlihat, dan console error/warning baru kosong.
- Folder CKFinder `Events` saat QA kosong. Tidak ada file yang dipilih atau di-upload, dan tidak ada Event yang disimpan saat Browser QA.

## Graphify

Incremental code-only update: 7 file code diekstrak ulang. Graph final: 20,139 nodes, 34,766 edges, 1,475 communities. Community labels belum direfresh melalui LLM.
