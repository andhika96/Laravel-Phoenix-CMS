# Product Lead Form v2.4 — Product Title Placement QA

Tanggal: 2026-08-23 18:43 WIB  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Surface: Chrome authenticated session, fresh unsaved draft `/pagebuilder-elementor/v2.4/create`  
Batasan: read-only; tidak menekan Save, Apply Dataset, Reset, atau submit form.

## Hasil browser

- `Content → Product Presentation` menampilkan Media Position desktop/tablet/mobile, Product Title Placement (`Above Image`, `Below Image`, `Above Form`), serta tiga visibility toggles.
- `Style → Overall Layout` menampilkan `Form Vertical Alignment` dengan opsi Top/Center/Bottom.
- `Style → Product Detail Media → Product Title` menampilkan Title Alignment, Title Gap default `4px`, Product Title Color, dan Typography.
- Placement berurutan `media-above → media-below → form-above` mengubah `data-title-placement` pada body Canvas sesuai pilihan.
- Desktop setelah QA seed `Title Alignment=right`, `Title Gap=12px`, `Form Vertical Alignment=center`: body memuat `--product-title-align:right`, `--product-title-gap:12px`, `--product-form-vertical-align:center`; computed `align-self` form adalah `center`.
- Tablet: body tetap dua kolom (`307.616px 343.993px`), media position `left`, computed form alignment `center`.
- Mobile dengan Media Position `bottom` dan placement `form-above`: body menjadi satu kolom (`305.59px`), media position `bottom`, computed form alignment tetap `center`; tidak memaksa layout satu baris.
- Console Chrome setelah interaksi: `[]` untuk error/warning.

## Batas browser QA

Draft baru tidak memiliki Product Dataset, sehingga title aktif tidak muncul secara visual dan pengukuran urutan `<h2>` berisi data produk tidak dapat dilakukan pada browser tanpa menekan Apply Dataset atau membuat dataset baru. Urutan dan rendering tiga placement tetap diverifikasi melalui focused PHP renderer test.

## Verifikasi otomatis

- `node --test tests/pagebuilder-v24-*.test.mjs`: 391 passed, 0 failed.
- `php artisan test --filter=PageBuilderElementorV24`: 155 passed, 10,336 assertions.
- `npm.cmd run build`: sukses, 58 modules transformed.
- PHP lint Blade dan test PHP: sukses.
- `git diff --check`: sukses.
- Graphify incremental code-only: 20,063 nodes, 34,956 edges; missing/dangling/self-loop/duplicate/edge-collapse: 0.

Catatan: Node suite masih menampilkan warning Vue lama dari `BasicImageSettings` di test widget image; warning tersebut tidak terkait Product Lead Form dan browser console tetap kosong.
