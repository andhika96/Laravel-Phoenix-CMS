# Page Builder v2.4 Modularity Architecture Audit

Tanggal: 2026-08-22  
Scope: Page Builder Elementor v2.4 saja  
Mode: read-only audit; tidak ada source runtime atau test yang diubah

## Audit contract

Target yang dinilai adalah kontrak yang sudah dikunci pengguna:

1. Setiap Layout, Grid, dan widget merupakan satu modul full-stack yang dapat dipasang atau dilepas melalui satu folder.
2. Menghapus atau memindahkan folder keluar dari canonical module root membuat tipe tersebut hilang dari registry/sidebar setelah reload.
3. Mengembalikan folder membuat tipe ditemukan kembali.
4. Seluruh modul menggunakan satu shared `AdvancedControls.vue`; opsi khusus dinyatakan sebagai capability/profile, bukan salinan tab Advanced.
5. Data halaman lama tidak dihapus ketika modul tidak tersedia.
6. Refactor hanya dilakukan di v2.4; source v2.3 tidak boleh berubah.

## Evidence summary

| Requirement/risk | Evidence aktual | Result | Gap |
| --- | --- | --- | --- |
| Catalog berasal dari folder | `config/pagebuilder_elementor_v24_widgets.php` berisi 49 entry statis | Fail | Tidak ada filesystem discovery atau manifest per folder |
| Satu folder memiliki paket lengkap | 50 directory aktif; hanya 31 memiliki `definition.js + Canvas.vue + Settings.vue` | Fail | 18 tipe Pro hanya memiliki definition dan bergantung pada `pro/shared`; `pro/shared` bukan tipe modul |
| Sidebar bersih saat folder hilang | Sidebar dibentuk dari definition yang berhasil register | Partial | Config masih mencoba memuat definition yang hilang sehingga menghasilkan stale entry/404; frontend catalog tetap mengenal tipe |
| Frontend renderer ikut dilepas | Renderer melakukan lookup config dan view | Fail | Blade view dan config berada di lokasi terpisah dari folder editor |
| Advanced satu source of truth | 21 dari 31 `Settings.vue` memakai shared Advanced | Partial | 10 modul masih menyimpan blok Advanced inline |
| Runtime/style ikut modular | `frontend-runtime.js` dan kedua stylesheet masih global | Fail | Menghapus modul tidak menghapus initializer atau CSS khususnya |
| Existing behavior tetap aman | Focused PHP dan Node regression suites lulus | Pass untuk baseline | Suite belum menguji folder removal/discovery karena mekanismenya belum ada |
| v2.3 tetap aman | Audit hanya membaca v2.4 | Pass | Harus dipertahankan sepanjang implementasi |

## Inventory aktual

- Catalog: 49 tipe; 47 tampil di toolbox.
- Category: 4 Layout, 9 Basic, 15 General, 21 Pro.
- Unique definition path: 49.
- Unique Canvas path: 32.
- Unique Settings path: 32.
- Unique frontend view path: 32.
- Seluruh path catalog saat ini tersedia: 0 definition, Canvas, Settings, atau view yang hilang.
- Active module-like directories: 50.
- Complete three-file editor contract: 31.
- Incomplete directories: 19, terdiri dari 18 definition-only Pro directories dan `pro/shared` yang hanya memiliki Canvas/Settings.

Tipe yang memakai shared Pro implementation:

`form`, `slides`, `animated_headline`, `hotspot`, `price_list`, `price_table`, `call_to_action`, `countdown`, `carousel`, `reviews`, `testimonial_carousel`, `media_carousel`, `flip_box`, `code_highlight`, `blockquote`, `share_buttons`, `progress_tracker`, dan `video_playlist`.

## Material findings

### [High, high confidence] Catalog statis mencegah clean plug/unplug

Lokasi:

- `config/pagebuilder_elementor_v24_widgets.php` — 459 lines, 49 entry.
- `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php:88-91` — loop config dan emit definition script.
- `resources/views/pagebuilder_elementor_v24/partials/render_node.blade.php:829-839` — lookup config untuk frontend view.

Konsekuensi: folder yang hilang memang gagal mendaftarkan definition di browser, tetapi config masih menganggap modul terpasang, shell masih meminta URL yang tidak ada, dan renderer masih memiliki mapping view. Ini bukan lifecycle install/uninstall yang bersih.

Remediation direction: ganti catalog statis dengan `ModuleCatalog` yang menemukan `module.json` di canonical module root dan hanya mengembalikan folder valid.

### [High, high confidence] Delapan belas Pro widget belum independen

Lokasi:

- `public/js/pagebuilder_elementor_v24/widgets/pro/shared/Settings.vue` — 6.925 lines / 328.906 bytes.
- `public/js/pagebuilder_elementor_v24/widgets/pro/shared/Canvas.vue` — 6.626 lines / 283.528 bytes.
- `resources/views/pagebuilder_elementor_v24/partials/render_pro_widget.blade.php` — satu view untuk 18 tipe.

Konsekuensi: mengubah atau melepaskan satu tipe Pro masih membawa implementasi tipe Pro lain; satu folder belum menjadi ownership boundary.

Remediation direction: ekstrak Settings, Canvas, frontend Blade, runtime, dan CSS per tipe. Shared helper yang benar-benar generik tetap berada di shared core.

### [Medium, high confidence] Host editor masih mengetahui banyak detail tipe

Lokasi:

- `public/js/pagebuilder_elementor_v24/app.js` — 8.335 lines dan 129 referensi terkait type/registry/selection.
- Type gates terlihat pada Advanced, icon operations, Video normalizer, modal Text Editor, dan beberapa flow khusus.

Konsekuensi: folder dapat hilang dari registry tetapi host masih membawa branching dan behavior tipe tersebut. Menambah modul baru masih dapat memerlukan edit `app.js`.

Remediation direction: pindahkan behavior tipe ke definition/module services; host hanya menyimpan orchestration generik seperti history, selection, drag/drop, clipboard, responsive preview, dan module loading.

### [Medium, high confidence] Runtime dan CSS masih global

Lokasi:

- `public/js/pagebuilder_elementor_v24/frontend-runtime.js` — 1.773 lines, initializer untuk Tabs, Accordion, carousel, Form, Pro widgets, Hero Banner/Slider, Product Color Selector, dan lainnya.
- `public/assets/css/pagebuilder_elementor_v24.css` — 11.707 lines.
- `public/assets/css/frontend_elementor_v24.css` — 1.498 lines.

Konsekuensi: module removal tidak menghilangkan runtime/CSS miliknya dan satu global file tetap menjadi change hotspot.

Remediation direction: manifest memiliki optional `runtime` dan `styles`; frontend hanya memuat asset untuk tipe yang digunakan halaman. Foundation/core CSS dan runtime tetap global.

### [Medium, high confidence] Shared Advanced belum universal

- Total non-shared Settings: 31.
- Menggunakan shared Advanced: 21.
- Inline Advanced: Button, Divider, Icon, Spacer, Text Editor, Video, Container, Container Fluid, Grid, Row Grid.

Enam Basic widget hanya memiliki legacy/minimal CSS Class. Empat Layout/Grid menyimpan campuran kontrol universal dan capability khusus dengan key yang berbeda dari shared component.

Remediation direction: satu shared `AdvancedControls.vue`, manifest `advanced.profile` + `advanced.capabilities`, serta compatibility normalizer untuk key lama.

### [Medium, high confidence] Form memiliki backend surface lintas folder

Form bergantung pada route, controller methods, `FormSubmissionHandler`, dataset controller/model/normalizer, mail, migration, Blade, dan frontend runtime. Menghapus folder Form tanpa module-awareness pada server dapat meninggalkan endpoint yang masih menerima request.

Remediation direction: route boleh tetap menjadi v2.4 core, tetapi wajib memeriksa bahwa module `form` aktif sebelum submission/dataset behavior dijalankan; jika tidak aktif, fail closed dengan 404/422 tanpa menghapus data.

### [Low, high confidence] Existing tests mengunci path legacy

Banyak Feature/Node tests membaca `public/js/pagebuilder_elementor_v24/widgets/...`, shared Pro files, dan config statis secara langsung. Tests tersebut bernilai untuk parity, tetapi harus dipindahkan bersama modul atau diarahkan melalui manifest/catalog agar tidak menghambat struktur baru.

## Baseline checks executed

PHP:

```text
php artisan test --compact \
  tests/Feature/PageBuilderElementorV24AssetIsolationTest.php \
  tests/Feature/PageBuilderElementorV24BaselineIsolationTest.php \
  tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php \
  tests/Feature/PageBuilderElementorV24WidgetParityTest.php

15 passed, 5,094 assertions
```

Node:

```text
node --test \
  tests/pagebuilder-v24-baseline-isolation.test.mjs \
  tests/pagebuilder-v24-all-settings-tab-mount-regression.test.mjs \
  tests/pagebuilder-v24-widget-runtime-parity.test.mjs \
  tests/pagebuilder-v24-frontend-canvas-css-parity.test.mjs

7 passed, 0 failed
```

Kesimpulan checks: baseline v2.4 sehat dan isolated, tetapi checks tersebut membuktikan parity struktur sekarang—bukan modular folder discovery.

## Verdict

- Baseline clone/parity: **Pass**.
- Target clean registry removal: **Partial secara incidental**, belum clean lifecycle.
- Target full-stack one-folder-per-module: **Fail pada kondisi sekarang**.
- Kelayakan refactor khusus v2.4: **Feasible**, dengan migration bertahap dan compatibility bridge.

Tidak ada source runtime, config, test, database, route, atau v2.3 yang diubah selama audit.
