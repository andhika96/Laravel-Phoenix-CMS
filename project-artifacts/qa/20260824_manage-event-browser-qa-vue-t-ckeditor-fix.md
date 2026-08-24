# Manage Event authenticated Browser QA and Vue runtime fixes

Tanggal: 2026-08-24
Project: `D:\Laragon\www\laravel-13-phoenix`

## Defect yang direproduksi

1. Halaman Add Event gagal render dengan `TypeError: t is not a function`.
2. Setelah render defect diperbaiki, CKEditor tampil tetapi event `change:data` gagal karena instance editor diproxy oleh Vue.

## Root cause dan fix

- `resources/views/manage_event/partials/form.blade.php` sempat memakai `t(...)` di dalam ekspresi Vue `@{{ ... }}`. Fungsi `t()` hanya milik Blade/PHP, bukan method Vue. Label slug sekarang memakai dua span Vue (`v-if`/`v-else`) dengan hasil `{{ t(...) }}` dari Blade.
- `public/assets/js/vue3/manage_event/vueV3-manage-event-form-2026.js` menyimpan CKEditor dengan `Vue.markRaw(editor)` agar Vue tidak mem-proxy object CKEditor.
- `tests/Feature/Event/ManageEventTemplateTest.php` sekarang melarang `t()` di ekspresi Vue dan mewajibkan `Vue.markRaw(editor)`.

## Backup

Backup dengan suffix berikut tersedia pada `project-artifacts/backups/20260824_210000_manage_event`:

- `vue-t-function-fix`
- `ckeditor-markraw-fix`

## Browser QA authenticated

- Login administrator berhasil pada in-app Browser; halaman Add Event terautentikasi terbuka.
- Hard reload setelah fix: Rich Text Editor terlihat, word count aktif, tidak ada notice `Content editor could not be loaded`, dan tidak ada console error baru.
- Desktop: Title `QA Browser Event` menghasilkan slug `qa-browser-event` otomatis.
- Mengisi CKEditor dengan `Browser QA rich content.` menyinkronkan textarea submit menjadi `<p>Browser QA rich content.</p>` tanpa submit data.
- Toolbar `Insert image or file` membuka CKFinder. Dialog menampilkan resource type `Events` serta keadaan folder kosong, tanpa error console dan tanpa memilih/upload file.
- Mobile 390x844: CKEditor dan tombol Create Event terlihat; console error baru kosong.
- Form QA dibersihkan dengan navigasi tanpa submit; halaman Add Event terautentikasi ditinggalkan terbuka sebagai deliverable.

## DatePicker boundary

Occurrence editor hanya muncul sesudah Event tersimpan. Daftar Event aktif kosong, sehingga popup VueDatePicker occurrence tidak diuji melalui browser tanpa membuat data baru. Kontrak Blade/JS tetap diverifikasi oleh feature test, view cache, dan node syntax check; tidak ada event QA yang dibuat atau disimpan.

## Verifikasi

- `php artisan test tests\\Feature\\Event --no-ansi`: 15 passed, 85 assertions.
- `php artisan view:cache --no-ansi`: passed.
- `node --check` seluruh JS Event/Manage Event: passed.
- Scan `@{{ ... t(...) }}` di seluruh `resources/views/manage_event`: no matches.
- Asset live `vueV3-manage-event-form-2026.js`: HTTP 200 dan memuat `Vue.markRaw(editor)`.
- `git diff --check`: passed.
- `php artisan test --no-ansi`: 689 passed, 1 pre-existing failure `Tests\\Feature\\PageBuilderElementorV23ShellTest` (expected 200, received 302).

## Graphify

Incremental code-only update: 3 file code diekstrak ulang. Graph final: 20,134 nodes, 34,768 edges; clustering selesai dengan 1,475 communities. Community labels belum direfresh melalui LLM.
