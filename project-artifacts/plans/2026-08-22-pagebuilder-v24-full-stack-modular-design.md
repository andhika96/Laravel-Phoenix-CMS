# Page Builder v2.4 Full-Stack Modular Architecture Design

Tanggal: 2026-08-22  
Status: locked by user for planning  
Implementation status: not started

## Goal

Membuat setiap Layout, Grid, dan widget Page Builder v2.4 sebagai satu paket module yang ditemukan dari foldernya, dapat dilepas/pasang melalui folder, tetap kompatibel dengan data v2.4 lama, dan tidak mengubah v2.3.

## Locked interpretation

“Menghapus folder widget” bukan langkah yang dilakukan selama refactor. Ini acceptance behavior setelah refactor:

- folder berada di canonical module root → module ditemukan dan dapat didaftarkan;
- folder dipindahkan keluar dari root atau dihapus → module tidak ditemukan dan hilang dari registry/sidebar setelah reload;
- folder dikembalikan → module muncul lagi setelah reload;
- data node lama tetap tersimpan ketika module tidak aktif.

Memindahkan folder ke lokasi lain yang masih berada di bawah canonical root tetap dianggap installed selama manifest valid.

## Canonical source structure

```text
resources/pagebuilder_elementor_v24/
├── modules/
│   ├── layout/
│   │   ├── container/
│   │   ├── container-fluid/
│   │   ├── grid/
│   │   └── row-grid/
│   └── widgets/
│       ├── basic/<slug>/
│       ├── general/<slug>/
│       └── pro/<slug>/
└── shared/
    ├── AdvancedControls.vue
    └── controls/
```

Every module directory owns:

```text
<slug>/
├── module.json
├── definition.js
├── Canvas.vue
├── Settings.vue
├── frontend.blade.php
├── runtime.js       # optional
└── styles.css       # optional
```

Tests tetap berada di `tests/` agar mengikuti Laravel/Node test discovery, tetapi harus mencari module melalui catalog atau fixture root, bukan hardcoded legacy path.

## Manifest contract

`module.json` adalah metadata source of truth. Schema version awal adalah `1`.

```json
{
  "schemaVersion": 1,
  "type": "button",
  "label": "Button",
  "category": "basic",
  "icon": "fas fa-link",
  "order": 60,
  "toolbox": true,
  "assets": {
    "definition": "definition.js",
    "canvas": "Canvas.vue",
    "settings": "Settings.vue",
    "view": "frontend.blade.php"
  },
  "advanced": {
    "profile": "widget",
    "capabilities": []
  }
}
```

Rules:

- `type` tetap sama dengan persisted node type dan tidak boleh berubah selama migration.
- `type`, `label`, `category`, `icon`, `order`, dan `toolbox` tidak diduplikasi di central config.
- Required assets: definition, Canvas, Settings, frontend view.
- Optional assets: runtime dan styles.
- Semua path harus relative, tidak boleh keluar dari module directory.
- Duplicate type, invalid JSON, unsupported schema version, atau missing required asset membuat module tidak aktif dan menghasilkan diagnostic; module lain tetap berjalan.
- Ordering toolbox deterministic: category order dari core lalu `order`, kemudian `type` sebagai tie-breaker.

Locked migration order preserves the current catalog order:

| Category | Physical slug and order |
| --- | --- |
| Layout | `container:10`, `container-fluid:20` (`toolbox:false`), `grid:30`, `row-grid:40` (`toolbox:false`) |
| Basic | `heading:10`, `video:20`, `google-maps:30`, `text-editor:40`, `image:50`, `button:60`, `divider:70`, `spacer:80`, `icon:90` |
| General | `image-box:10`, `icon-box:20`, `image-carousel:30`, `basic-gallery:40`, `feature-showcase:50`, `icon-list:60`, `tabs:70`, `accordion:80`, `counter:90`, `progress-bar:100`, `testimonial:110`, `social-icons:120`, `alert:130`, `rating:140`, `text-path:150` |
| Pro | `form:10`, `slides:20`, `animated-headline:30`, `hotspot:40`, `price-list:50`, `price-table:60`, `call-to-action:70`, `countdown:80`, `carousel:90`, `reviews:100`, `testimonial-carousel:110`, `media-carousel:120`, `hero-banner:130`, `hero-slider:140`, `flip-box:150`, `code-highlight:160`, `blockquote:170`, `share-buttons:180`, `progress-tracker:190`, `video-playlist:200`, `product-color-selector:210` |

## Module discovery

Create `App\Support\PageBuilderElementorV24\ModuleCatalog` dengan public contract:

```php
public function all(): array;
public function find(string $type): ?array;
public function toolbox(): array;
public function diagnostics(): array;
public function active(string $type): bool;
public function clientCatalog(): array;
```

Catalog memindai `resources/pagebuilder_elementor_v24/modules/**/module.json` pada setiap PHP request. Dengan 49 module, filesystem scan sederhana lebih aman daripada cache invalidation yang dapat membuat folder removal tidak langsung terlihat. Cache baru boleh ditambahkan jika profiling membuktikan scan bermasalah dan cache key mencakup directory state.

Discovery tidak bergantung pada `config:cache` dan tidak mempertahankan stale module entry.

## Editor asset boundary

Source module berada di `resources`, sehingga editor mengakses asset melalui v2.4 authenticated route:

```text
GET /pagebuilder-elementor/v2.4/module-assets/{type}/{assetKey}
```

`assetKey` hanya menerima key yang terdaftar pada manifest, bukan arbitrary path. Controller:

- resolve module melalui `ModuleCatalog`;
- reject inactive/unknown module dengan 404;
- reject unknown key dan directory traversal dengan 404;
- serve `.js`, `.vue`, dan `.css` dengan content type tepat;
- gunakan ETag/Last-Modified agar preload tetap cepat;
- tidak pernah serve Blade/PHP source sebagai browser asset.

Shared controls memakai route exact berikut:

```text
GET /pagebuilder-elementor/v2.4/shared-assets/{assetKey}
```

`SharedAssetController` memiliki whitelist key `advanced`, `typography`, `link`, `dynamic-tag`, `css-filter`, `text-stroke`, `text-shadow`, dan `grid-column-style`. Route tidak menerima path filesystem.

## Registry and editor boot

`ModuleCatalog::clientCatalog()` menghasilkan metadata dan URL asset yang aman untuk browser tanpa absolute filesystem path. Catalog ini disisipkan ke editor shell dan `widget-registry.js` dikonfigurasi sebelum definition scripts dimuat.

`definition.js` hanya memiliki type-specific defaults, normalize, dan optional editor services. Metadata/path berasal dari manifest dan digabung oleh registry. Module definition yang tidak termasuk server catalog ditolak.

`app.js` tetap menjadi core host untuk:

- selection dan tree orchestration;
- drag/drop;
- history, undo, redo;
- clipboard/context menu shell;
- responsive preview;
- module loading;
- shared editor services.

Type-specific normalizers, gates, pickers, dan preview behavior pindah ke definition/module service. Modul baru tidak boleh memerlukan branch baru di `app.js` kecuali benar-benar menambah core capability.

## Frontend rendering

`render_node.blade.php` meminta module dari `ModuleCatalog` dan merender `frontend.blade.php` dengan `view()->file(...)` hanya jika module aktif dan view path lolos boundary check.

Jika module tidak aktif:

- persisted node tidak dihapus atau dimutasi;
- editor/frontend menampilkan deterministic unsupported/inactive marker;
- renderer tidak mencoba fallback view lama;
- module-specific endpoint harus fail closed.

`App\Support\PageBuilderElementorV24\ModuleUsageCollector::types(array $nodes): array` mengumpulkan type yang benar-benar digunakan halaman secara recursive tanpa memutasi payload. Frontend renderer lalu memuat optional module styles/runtime hanya untuk active used types. Core foundation CSS/runtime tetap global.

## Shared Advanced contract

Hanya ada satu source UI:

```text
resources/pagebuilder_elementor_v24/shared/AdvancedControls.vue
```

Setiap `Settings.vue` meng-include shared component. Perbedaan dinyatakan oleh manifest:

```json
"advanced": {
  "profile": "layout",
  "capabilities": ["overflow", "sticky-per-device", "custom-attributes"]
}
```

Shared component menerima profile/capabilities sebagai data. Ia tidak mempunyai hardcoded list slug widget. Universal settings memakai canonical keys. Legacy keys seperti layout motion fields dan Button `className` dipertahankan melalui normalizer/adapter sampai existing saved data terverifikasi identik.

Widget-specific Content/Style controls tetap berada di module `Settings.vue`; hanya kategori Advanced yang shared.

## Special backend capabilities

Form submission, datasets, mail, dan media services tetap menjadi version-owned core services pada fase awal. Manifest boleh menyatakan capability seperti `form-submission`. Controller/route harus memeriksa `ModuleCatalog::active('form')` sebelum menjalankan module-specific behavior.

Hal ini menghindari memuat PHP classes dari folder resources dan menjaga trust boundary Laravel. Backend service dapat direlokasi dalam refactor terpisah hanya bila memberikan manfaat konkret; bukan syarat registry plug/unplug.

## Migration strategy

Migration berlangsung bertahap, tidak big-bang:

1. Module platform + fixture tests + satu pilot module.
2. Migrate dedicated Layout/Basic/General/Pro modules secara mekanis tanpa mengubah persisted type/settings.
3. Split 18 Pro shared implementations menjadi package independen.
4. Universalize Advanced, extract runtime/styles, lalu hapus legacy config/path fallback.

Selama transition, catalog boleh menggabungkan discovered modules dan explicit legacy bridge. Discovered module selalu menjadi authority untuk type yang sudah dimigrasikan. Legacy bridge dihapus hanya setelah 49 tipe berpindah dan parity suite lulus.

## Error handling

- Satu invalid module tidak boleh membuat editor blank.
- Diagnostics harus menyebut type/folder dan reason tanpa membocorkan absolute path di browser.
- Duplicate type tidak menggunakan “first wins”; semua conflicting entries untuk type tersebut dinonaktifkan.
- Missing runtime/styles optional tidak menggagalkan module; missing required asset menggagalkan module.
- Open editor perlu reload untuk melihat filesystem install/uninstall; live filesystem watching di browser bukan scope.

## Security

- Asset endpoint berada dalam auth middleware v2.4.
- Asset lookup hanya melalui manifest key dan resolved-path containment check.
- Manifest tidak boleh menentukan PHP class, executable command, remote URL, credential, atau arbitrary Blade path.
- Blade view hanya boleh file `frontend.blade.php` di module directory.
- Existing SVG/HTML sanitization dan Form authorization tetap dipertahankan.

## Compatibility invariants

- v2.3 source hash harus tetap identik sebelum/sesudah refactor.
- `editor_version=2.4` tetap menjadi ownership boundary.
- Persisted node `type`, IDs, settings keys, tree order, layout/grid structure, and responsive inheritance tidak berubah tanpa explicit compatibility adapter.
- Canvas, saved payload, frontend Blade, CSS, runtime, dan public renderer harus tetap parity.
- Tidak ada Save/Reset/Apply Dataset/real submission dalam browser QA tanpa izin baru.

## Acceptance criteria

1. 49 module manifests valid dan setiap type mempunyai independent definition, Canvas, Settings, dan frontend view.
2. Tidak ada type yang memakai `pro/shared/Settings.vue`, `pro/shared/Canvas.vue`, atau shared multi-widget Blade switch.
3. Temporary removal test pada fixture module membuat type hilang dari catalog/toolbox; restoration membuatnya kembali.
4. Browser QA dengan satu module fixture dinonaktifkan menunjukkan widget hilang tanpa console 404/error.
5. Existing page dengan inactive type mempertahankan JSON dan menunjukkan inactive marker.
6. Semua 31 Settings wrappers menggunakan satu shared Advanced; Pro split menghasilkan wrappers tambahan yang juga menggunakannya.
7. `app.js`, global frontend runtime, dan global CSS tidak lagi mempunyai type-specific branches/selectors untuk migrated modules, kecuali documented core capability.
8. Full v2.4 PHP/Node/build/lint/browser parity passes.
9. Focused v2.3 regression and source checksum passes with zero version-owned changes.

## Non-goals

- Marketplace/download/upload module packages.
- Hot install/uninstall tanpa page reload.
- Refactor v2.3.
- UI redesign atau penambahan opsi Elementor baru.
- Perubahan database schema atau data migration yang tidak diperlukan untuk compatibility.
- Menambah framework/module dependency baru.
