# Page Builder Elementor v2.4 Baseline Isolation Design

## Status dan scope

- Tanggal: 2026-08-22
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Status keputusan: disetujui dalam diskusi user
- Scope tahap ini: membuat baseline v2.4 yang mempunyai parity fungsional dengan source v2.3 aktif
- Di luar scope: refactor folder-driven 100% modular, UI migrasi page, migrasi record produksi, commit, push, dan penghapusan v2.3

## Tujuan

Menyediakan Page Builder Elementor v2.4 yang dapat dikembangkan tanpa membuat perubahan internal v2.4 bocor ke v2.3. Baseline harus menyalin perilaku editor, widget, canvas, persistence, Form Dataset, submission, preview, dan frontend renderer v2.3 sebelum refactor modular dimulai.

## Keputusan yang mengikat

1. v2.3 berstatus maintenance-only dan tidak menerima refactor modular.
2. Source khusus versi dipisahkan secara fisik dan namespace:
   - `PageBuilderElementorV23` menjadi `PageBuilderElementorV24`;
   - `pagebuilder_elementor_v23` menjadi `pagebuilder_elementor_v24`;
   - route prefix `/pagebuilder-elementor/v2.3` menjadi `/pagebuilder-elementor/v2.4`;
   - route names memakai `cms.core.pagebuilder_elementor_v24.*`;
   - browser globals, storage keys, clipboard source, event names, CSS selectors bertanda versi, config, views, assets, dan test memakai v2.4.
3. Source v2.4 tidak boleh mengimpor atau menunjuk source internal v2.3.
4. Infrastruktur generik boleh dibagi: authentication, `Page_Builder` model/table, File Manager/media storage, Laravel framework, dan public page dispatcher.
5. Record baru yang dibuat editor v2.4 menyimpan `editor_version = 2.4`.
6. Endpoint v2.4 menolak record versi lain dengan 409 tanpa mutasi.
7. Form Dataset v2.4 memakai model dan table v2.4 terpisah.
8. Route editor/API/dataset v2.4 berada di `routes/pagebuilder_elementor_v24.php`.
9. Public URL tetap `/pages/{uri}` dan satu dispatcher netral memilih renderer v2.3 atau v2.4 berdasarkan `editor_version`.
10. Nama route publik v2.3 dipertahankan sebagai compatibility alias pada baseline agar consumer lama tidak putus.
11. Tidak ada page v2.3 yang disalin atau dimutasi dalam tahap source clone ini.
12. Modular refactor baru boleh dimulai setelah baseline v2.4 lulus parity, isolation, build, dan browser smoke test.

## Source mapping

### Tree yang dicopy penuh

- `app/Http/Controllers/Web/PageBuilderElementorV23/` → `PageBuilderElementorV24/`
- `app/Support/PageBuilderElementorV23/` → `PageBuilderElementorV24/`
- `app/Http/Requests/Page_Builder_Elementor_V23/` → `Page_Builder_Elementor_V24/`
- `app/Models/PageBuilderElementorV23/` → `PageBuilderElementorV24/`
- `public/js/pagebuilder_elementor_v23/` → `pagebuilder_elementor_v24/`
- `resources/views/pagebuilder_elementor_v23/` → `pagebuilder_elementor_v24/`

### File tunggal yang dicopy

- mail Form v2.3 → mail Form v2.4
- email text view v2.3 → v2.4
- widget catalog config v2.3 → v2.4
- editor dan frontend CSS v2.3 → v2.4
- shapes data v2.3 → v2.4 tanpa mengubah angka SVG
- prototype v2.3 → prototype v2.4
- Form Dataset migration v2.3 → migration/table v2.4
- seluruh PHP/Node test bernama v2.3 → pasangan test v2.4

### File shared yang berubah minimal

- `app/Models/Page_Builder/Page_Builder.php`: tambah `EDITOR_VERSION_V24 = '2.4'`.
- `routes/web.php`: require route v2.4 dan public dispatcher.
- `routes/experimentalFeaturesWebv2.php`: keluarkan deklarasi public route lama; route family v2.3 lain tetap byte-identical.
- `tests/Feature/PageBuilderEditorVersionMigrationTest.php`: tambahkan contract constant v2.4.

## Public dispatcher

`PageBuilderElementorPublishedPageController` mengambil satu page publish berdasarkan URI, lalu memilih view:

- `2.3` → `pagebuilder_elementor_v23.frontend_renderer`
- `2.4` → `pagebuilder_elementor_v24.frontend_renderer`
- versi lain → HTTP 404

Dispatcher tidak menormalisasi atau memutasi `vars`; setiap renderer tetap membaca data page sendiri.

## Failure behavior

- Page tidak ditemukan, draft, atau editor version tidak didukung: 404 pada public route.
- Editor v2.4 membuka/mengubah page bukan 2.4: 409 dan record tidak berubah.
- Definition/view/asset v2.4 hilang: isolation/parity tests gagal sebelum baseline diterima.
- Reference internal v2.3 ditemukan dalam tree v2.4: isolation test gagal.

## Acceptance criteria

1. Seluruh active file v2.3 yang termasuk mapping memiliki counterpart v2.4.
2. Setelah normalisasi token versi, content tree v2.4 setara dengan v2.3 kecuali daftar shared integration yang disetujui.
3. Tidak ada path, namespace, global, event, storage key, config name, view name, route name, mail, atau dataset table v2.3 di source v2.4.
4. Delapan route editor utama dan empat route dataset v2.4 terdaftar dengan prefix/name/controller v2.4.
5. Store v2.4 menghasilkan record `editor_version=2.4`.
6. v2.3 tetap menolak page v2.4 dan v2.4 tetap menolak page v2.3.
7. `/pages/{uri}` merender page publish v2.3 dengan asset v2.3 dan page publish v2.4 dengan asset v2.4.
8. Seluruh focused PHP/Node v2.4 tests, regression v2.3 tests, build, syntax checks, dan `git diff --check` lulus.
9. Browser smoke test memuat create v2.4 dan v2.3 secara terpisah tanpa error console dan tanpa Save/Reset.
10. Hash source v2.3 setelah pekerjaan sama dengan snapshot sebelum pekerjaan.

## Rollback

Karena page produksi tidak dimutasi, rollback baseline cukup dengan menghapus file baru v2.4 dan mengembalikan empat file shared dari backup timestamp. Penghapusan tidak dilakukan otomatis dalam pekerjaan ini.
