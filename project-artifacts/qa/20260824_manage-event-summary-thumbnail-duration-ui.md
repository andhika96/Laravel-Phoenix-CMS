# Manage Event summary, thumbnail, duration picker, and submit placement QA

Tanggal: 2026-08-24
Project: `D:\Laragon\www\laravel-13-phoenix`

## Perubahan

- Summary memakai CKEditor kedua dengan toolbar, CKFinder resource type `Events`, word count, dan sinkronisasi submit yang sama seperti Content.
- Summary rich text dirender pada detail Event; daftar Event mengubah HTML Summary menjadi plain text agar tag tidak muncul sebagai teks mentah.
- Thumbnail mengikuti pola Add Article: input-group, preview checkerboard 350px, tombol trash remove, dan empty state.
- Pada edit Event, remove thumbnail menghapus kedua rendition Event dari storage dan mengosongkan `thumb_l` / `thumb_s` setelah submit.
- Reminder override dan Cancel cutoff override memakai `VueDatePicker` mode time-only. Nilai disimpan sebagai integer minutes; clear mengembalikan nilai kosong untuk default global 24 jam.
- Tombol Create/Save Event dipindahkan ke card Publish sebelum Category dan Thumbnail, mengikuti posisi action Add Article.

## Backup

Backup source sebelum perubahan tersedia pada `project-artifacts/backups/20260824_210000_manage_event` dengan suffix `summary-thumbnail-duration-ui`.

## Automated verification

- `php artisan test tests\\Feature\\Event --no-ansi`: 17 passed, 109 assertions.
- `ManageEventTemplateTest`: 3 passed, 43 assertions.
- `EventHttpFlowTest`: thumbnail persisted removal passed; kedua file rendition terhapus dan field database null.
- `php artisan view:cache --no-ansi`: passed.
- PHP lint scope: passed.
- `node --check` Event and Manage Event JS: passed.
- Scan `@{{ ... t(...) }}` Manage Event: no matches.
- `git diff --check`: passed.
- Full `php artisan test --no-ansi`: 691 passed, 1 pre-existing failure `Tests\\Feature\\PageBuilderElementorV23ShellTest` (expected 200, received 302).

## Authenticated Browser QA

- Add Event desktop: Summary CKEditor, Content CKEditor, two time-only picker, and Create Event above Category/Thumbnail all visible. Fresh console error/warning: none.
- Summary input `Summary rich text QA.` menghasilkan textarea submit `<p>Summary rich text QA.</p>` tanpa submit data.
- Reminder picker memilih `02:30` dan hidden input menjadi `150` minutes.
- Cancel cutoff picker memilih `03:32` dan hidden input menjadi `212` minutes; Reminder tetap `150`, membuktikan binding independen.
- Clear Reminder mengosongkan hidden input dan picker, sehingga global default 24 jam aktif kembali.
- Mobile 390x844: Summary CKEditor, duration picker, dan urutan Create Event tetap terlihat; fresh console error/warning: none.
- Tidak ada Event yang dibuat atau disubmit saat Browser QA.

## Boundary

Preview/remove thumbnail secara visual memerlukan memilih file lokal pada browser. Tidak dilakukan karena tidak ada izin upload file khusus pada pass ini. Kontrak UI dan penghapusan backend sudah diverifikasi lewat test; tombol remove akan muncul setelah file dipilih atau pada thumbnail Event yang sudah ada.

## Graphify

Incremental code-only update: 10 file code diekstrak ulang. Graph final: 20,136 nodes, 34,759 edges, 1,483 communities. Community labels belum direfresh melalui LLM.
