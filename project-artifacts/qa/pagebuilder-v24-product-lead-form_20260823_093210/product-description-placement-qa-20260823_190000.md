# Product Lead Form v2.4 — Product Description Placement QA

Tanggal: 2026-08-23 19:00 WIB  
Project: `D:\Laragon\www\laravel-13-phoenix`  
Surface: Chrome authenticated session, fresh unsaved draft `/pagebuilder-elementor/v2.4/create`  
Batasan: read-only; tidak menekan Save, Apply Dataset, Reset, atau submit form.

## Hasil browser

- `Content → Product Presentation` memiliki dua placement control dengan opsi yang sama: `Above Image`, `Below Image`, dan `Above Form`.
- Perubahan `media-above → media-below → form-above` mengubah `data-description-placement` pada Canvas.
- `Product Title Placement` dan `Product Description Placement` dapat memakai placement yang sama tanpa error.
- Runtime Canvas tetap memiliki satu selector description; pada draft tanpa Product Dataset elemen description aktif tidak dirender, sehingga tidak ada perubahan data produk yang dikirim.
- Console Chrome setelah interaksi: `[]` untuk error/warning.

## Verifikasi renderer

- PHP renderer menguji Description di atas image, di bawah image, dan di atas form.
- Jika Title dan Description memakai placement yang sama, assertion memastikan urutan `Title → Description`.
- Form description memakai direct child form placement tanpa wrapper tambahan.

## Verifikasi otomatis

- `node --test tests/pagebuilder-v24-*.test.mjs`: 391 passed, 0 failed.
- `php artisan test --filter=PageBuilderElementorV24`: 155 passed, 10,350 assertions.
- `npm.cmd run build`: sukses, 58 modules transformed.
- PHP lint Blade/test dan `git diff --check`: sukses.
- Graphify incremental code-only: 20,064 nodes, 34,958 edges, 1,478 communities; missing/dangling/self-loop/duplicate/edge-collapse: 0.

Title visual dan Description visual dengan data produk tidak diuji pada browser karena draft tidak memiliki Product Dataset dan QA tidak boleh menekan Apply Dataset.
