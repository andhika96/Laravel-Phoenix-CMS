# Manage Event Thumbnail CKFinder selected design QA

Tanggal: 2026-08-24
Project: `D:\Laragon\www\laravel-13-phoenix`

## Selected design

- User memilih design preview Thumbnail source switcher (`Upload File` / `CKFinder library`).
- Tombol remove tetap tombol trash outline `btn-outline-danger`, sesuai permintaan dan pola form sebelumnya.

## Implementasi

- Thumbnail source switcher menulis `thumbnail_source=upload|ckfinder`.
- Mode CKFinder membuka `CKFinder.modal` (bukan popup window) pada resource root `Events`.
- Selected file CKFinder dibatasi ke path `/storage/ckfinder/events/...`; server mengimpor file ke thumbnail pipeline Event dan menghasilkan rendition large/small.
- Toggle source membersihkan state CKFinder yang stale ketika kembali ke Upload; ini mencegah preview yang tidak akan ikut tersimpan.
- Reminder dan Cancel cutoff picker scope CSS menjadi font `14px`, line-height `18.9px`, dan min-height `38px`.

## Backups

Backups dengan suffix berikut tersedia pada `project-artifacts/backups/20260824_210000_manage_event`:

- `thumbnail-ckfinder-compact-picker`
- `ckfinder-modal-runtime`
- `thumbnail-source-transition`

## Test evidence

- RED / GREEN template design test: `ManageEventTemplateTest` 4 passed, 57 assertions.
- RED / GREEN CKFinder import: `EventHttpFlowTest` 5 passed, 22 assertions.
- `node --test tests\\manage-event-thumbnail-source.test.mjs`: 1 passed; source switch back ke Upload menghapus URL, label, preview, dan remove state CKFinder stale.
- `php artisan test tests\\Feature\\Event --no-ansi`: 19 passed, 129 assertions.
- `php artisan test --no-ansi`: 693 passed, 1 pre-existing failure `Tests\\Feature\\PageBuilderElementorV23ShellTest` (expected 200, received 302).
- PHP lint, Node syntax checks, Blade view cache, anti-`t()` scan, live asset marker, dan `git diff --check`: passed.

## Browser QA authenticated

- Desktop: compact picker computed `14px` dan `38px`; source switcher bekerja; Browser modal CKFinder terbuka di root `Events`; console error/warning baru kosong.
- Mobile 390x844: dua picker tetap `14px`, source switcher dan Browse CKFinder terlihat, preview checkerboard responsif, console error/warning baru kosong.
- Final clean state: Upload File kembali selected, `thumbnail_ckfinder_url` kosong, preview tidak tersisa, upload field visible, console error/warning baru kosong.
- Folder `Events` pada CKFinder kosong saat QA. Tidak ada file dipilih/upload dan tidak ada Event disimpan melalui Browser.

## Graphify

Incremental code-only update: 2 code file dire-ekstrak pada final pass. Graph final: 20,143 nodes, 34,769 edges, 1,486 communities. Community labels belum direfresh melalui LLM.
