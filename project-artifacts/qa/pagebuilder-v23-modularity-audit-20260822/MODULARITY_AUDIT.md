# Audit Modularitas Page Builder Elementor v2.3

- Tanggal: 2026-08-22
- Project: `D:\Laragon\www\laravel-13-phoenix`
- Scope: seluruh widget Basic, General, Pro, serta Layout/Container/Grid pada Page Builder v2.3
- Mode: read-only terhadap source production; simulasi pelepasan module dilakukan pada registry di memory tanpa memindahkan folder source aktif

## Verdict

**FAIL — arsitektur saat ini belum 100% modular atau folder-driven.**

Baseline catalog sehat dan konsisten, tetapi kriteria utama "folder dipindahkan/dihapus lalu item langsung hilang dari sidebar" gagal untuk `container` dan `grid`. Selain itu, catalog masih berasal dari config statis, bukan discovery folder, dan 18 widget Pro masih berbagi satu Canvas, Settings, serta Blade renderer berbasis type branching.

## Requirement-to-evidence matrix

| Requirement | Evidence | Result |
|---|---|---|
| Semua module aktif mempunyai definition, Canvas, Settings, dan view | `PageBuilderElementorV23AssetIsolationTest` dan `PageBuilderElementorV23WidgetParityTest` | PASS: 49 module, 2 test / 2.954 assertion |
| Semua definition pada disk tercatat dalam catalog | `audit-widget-modularity.mjs` membandingkan filesystem dengan config | PASS: 49 definition = 49 config entry |
| Semua config entry mendaftarkan module JS yang valid | Registry dijalankan terhadap seluruh definition aktif | PASS: 49 registration, 0 execution error |
| Module toolbox hilang bila definition/folder tidak tersedia | Simulasi melewatkan definition satu per satu lalu membentuk sidebar seperti `app.js` | PARTIAL: 45/47 hilang; `container` dan `grid` tetap muncul |
| Folder baru dapat dipasang tanpa mengedit daftar pusat | Editor shell dan source discovery diperiksa | FAIL: tidak ada folder discovery; wajib menambah `config/pagebuilder_elementor_v23_widgets.php` |
| Folder dapat dilepas tanpa request asset mati/stale config | Editor shell L88-L90 | FAIL: config tetap menghasilkan `<script>` untuk definition yang hilang; tidak ada existence filter |
| Setiap widget memiliki Canvas/Settings sendiri di folder module | Perbandingan directory definition, Canvas, Settings | PARTIAL: 31 self-contained; 18 widget Pro memakai `pro/shared` |
| Frontend renderer ikut terlepas bersama folder widget | Config view dan Blade renderer diperiksa | FAIL: 18 widget Pro tetap bercabang dalam satu `render_pro_widget.blade.php` |

## Inventory aktual

- 49 module config: 4 Layout, 9 Basic, 15 General, 21 Pro.
- 47 module berstatus `toolbox: true`.
- `container_fluid` dan `row_grid` berstatus `toolbox: false`.
- 49 `definition.js` ditemukan dan semuanya terdaftar tanpa error.
- 31 module memiliki `definition.js`, `Canvas.vue`, dan `Settings.vue` pada folder JS yang sama.
- 18 module Pro menggunakan `widgets/pro/shared/Canvas.vue` dan `widgets/pro/shared/Settings.vue`.

## Findings

### 1. High — Container dan Grid masih hard-coded di sidebar

`public/js/pagebuilder_elementor_v23/app.js` membuat seed toolbox literal untuk `container` dan `grid`, lalu baru menggabungkan hasil `widgetRegistry.toolbox()`.

Konsekuensi yang sudah direproduksi oleh audit script:

- definition/folder `container` dilewatkan: `Container` tetap ada di sidebar;
- definition/folder `grid` dilewatkan: `Grid` tetap ada di sidebar;
- ketika dipakai, loader tidak menemukan registration dan jatuh ke placeholder `??type`.

Ini melanggar acceptance criterion utama secara langsung.

### 2. High — Catalog adalah manifest statis, bukan discovery folder

`resources/views/pagebuilder_elementor_v23/editor_shell.blade.php` mengiterasi `config('pagebuilder_elementor_v23_widgets')`. Tidak ditemukan `glob`, filesystem scan, atau discovery service terhadap folder widget.

Konsekuensi:

- folder baru tidak muncul sampai config pusat diedit;
- folder dipindahkan membuat config stale;
- folder dihapus masih menghasilkan URL `<script>` yang mati, walaupun kebanyakan widget akhirnya tidak ter-register dan hilang dari sidebar.

### 3. High — Delapan belas widget Pro bukan module folder atomik

Widget berikut hanya memiliki definition per folder, tetapi editor dan frontend utamanya berada pada shared mega-modules:

`form`, `slides`, `animated_headline`, `hotspot`, `price_list`, `price_table`, `call_to_action`, `countdown`, `carousel`, `reviews`, `testimonial_carousel`, `media_carousel`, `flip_box`, `code_highlight`, `blockquote`, `share_buttons`, `progress_tracker`, dan `video_playlist`.

`Settings.vue`, `Canvas.vue`, dan `render_pro_widget.blade.php` memilih behavior melalui rangkaian `type === ...` / `@case(...)`. Menghapus folder definition dapat menghilangkan item sidebar, tetapi tidak melepaskan implementasi Canvas/Settings/renderer sebagai satu unit.

### 4. Medium — Metadata dan capability masih tersebar di `app.js`

Selain registry/config, `app.js` masih mempunyai:

- `isCont()` / `isGrid()` dengan type literal;
- label dan icon map terpisah dari module definition;
- daftar type untuk Advanced Controls;
- `PageBuilderElementorV23ComplexWidgetRuntime` dengan entry widget eksplisit;
- cabang tree/layout khusus berdasarkan type.

Akibatnya folder widget belum menjadi satu-satunya source of truth dan perubahan module masih dapat memerlukan edit core.

### 5. Medium — Frontend tetap hidup dari config/view meskipun definition JS dilepas

`render_node.blade.php` memilih view dari config, sedangkan 18 Pro widgets diteruskan ke satu Blade switch. Karena config dan view berada di luar folder JS widget, persisted node masih dapat dirender walaupun definition folder JS dipindahkan. Perilaku editor dan frontend tidak benar-benar dilepas bersama.

## Yang sudah baik

- Registry menggunakan `Map`, memvalidasi contract definition, menolak duplicate type, dan membuat toolbox dari registration aktif.
- Baseline config, definition, Canvas, Settings, serta view saat ini sinkron 49/49.
- Untuk 45 dari 47 item sidebar aktif, tidak menjalankan definition memang menghapus item dari registry/sidebar.
- Missing Settings/Canvas mempunyai fallback terkontrol, bukan crash seluruh editor.
- Isolation v2.3 dari v2.0 tetap lulus pada test yang dijalankan.

## Arah remediasi minimum untuk mencapai target 100%

1. Jadikan manifest per-folder sebagai source of truth, misalnya satu file module yang berisi type, label, category, icon, toolbox, definition, Canvas, Settings, view, dan capability.
2. Tambahkan discovery server yang hanya memuat manifest folder valid dan dapat di-cache; config pusat tidak lagi mencantumkan setiap widget.
3. Hapus seed literal `container`/`grid`; sidebar hanya berasal dari registry.
4. Pindahkan branch Canvas/Settings/Blade widget Pro ke module masing-masing, atau jadikan shared primitives tanpa mengetahui `type` tertentu.
5. Pindahkan label, icon, Advanced capability, dan frontend initializer ke metadata/capability module.
6. Tetapkan fallback persisted node yang eksplisit untuk module hilang agar data lama tidak rusak, tetapi module tidak muncul sebagai item baru.
7. Tambahkan contract test yang membuat catalog sandbox, melepas setiap folder satu per satu, dan memastikan sidebar/catalog/frontend route tidak lagi mengiklankan type tersebut.

## Commands dan evidence

```text
node project-artifacts/qa/pagebuilder-v23-modularity-audit-20260822/audit-widget-modularity.mjs
  49 config, 49 definition, 49 registration, 0 error
  folderRemovalSidebarFailures: [container, grid]
  31 JS self-contained, 18 shared

php artisan test tests/Feature/PageBuilderElementorV23AssetIsolationTest.php tests/Feature/PageBuilderElementorV23WidgetParityTest.php --compact
  2 passed, 2.954 assertions

node --test tests/pagebuilder-v23-*.test.mjs
  227 passed, 0 failed
```

## Verification limits

- Folder source aktif tidak dipindahkan atau dihapus karena worktree sangat dirty dan harus dipertahankan.
- Pelepasan folder disimulasikan pada boundary yang benar-benar membentuk registry/sidebar: definition tiap type dilewatkan satu per satu, lalu hard-coded toolbox seed digabung persis seperti source aktif.
- Sesi browser QA sekarang sudah terautentikasi, tetapi folder source aktif tetap tidak dipindahkan/dihapus. Verdict modularitas berasal dari executable registry simulation, PHP contract tests, dan source aktual—bukan klaim uji browser dengan folder produksi yang benar-benar dilepas.
