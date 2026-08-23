# Product Lead Form v2.4 — Settings Refactor QA

Tanggal: 2026-08-23 17:52 WIB  
Surface: Chrome authenticated session, temporary draft ID 32  
Batasan: read-only; tidak Save, Apply Dataset, Reset, atau submit.

## Perubahan terverifikasi

- Content section sekarang bernama Data Source, Selector & Levels, Product Presentation, Form Structure, Submit Button, Submit Actions, Multi-step, Submission Messages, dan Form Identity & Validation.
- Style section sekarang bernama Overall Layout, Product Detail Media, Selector Cards, Form Layout, Form Labels, HTML Field, Form Inputs, Form Button, Feedback Colors, dan Step Indicator.
- Selector Cards memakai satu `Editing Level`; saat jumlah level 3, pilihan hanya Model, Type, Variant dan hanya level aktif yang tampil.
- Selected Indicator berada di luar state Normal/Hover/Selected sehingga tetap dapat diatur tanpa berpindah state.
- Label Gap tidak lagi tampil sebagai kontrol kedua; Content Gap menjadi sumber kebenaran.
- Legacy `imageLabelGap` custom dipromosikan ke `contentGap` saat normalisasi.

## Runtime checks

- Label luar dengan Card Padding `200px`: computed padding `200px` pada seluruh sisi; tidak lagi dipaksa `0`.
- Label luar dengan Content Gap `40px`: computed `row-gap: 40px`.
- Padding `16px` + Image Width/Height `100%`: media box square, `overflow:hidden`, aspect ratio `1 / 1`, dan image memenuhi box.
- Image Fit `contain` dan `cover` tetap diterapkan sebagai computed `object-fit` walaupun Shape `Circle` dan ukuran `100%`.
- Padding `0` + Content Gap `0`: computed padding `0px`, gap `0px`, dan jarak thumbnail-label `0px`.
- State tabs Normal/Hover/Selected berpindah dan menampilkan control state yang sesuai.
- Console errors/warnings: kosong.

## Automated verification

- `node --test tests/pagebuilder-v24-product-lead-form.test.mjs`: 11 passed.
- `node --test tests/pagebuilder-v24-*.test.mjs`: 389 passed, 0 failed.
- `php artisan test --filter=PageBuilderElementorV24`: 154 passed, 10,310 assertions.
- Focused PHP FormSubmission + FrontendRendering: 34 passed, 344 assertions.
- `npm.cmd run build`: sukses, 58 modules transformed.
- Blade `php -l`: sukses.
- `git diff --check`: sukses.
- Control audit: 50 modules, 1,794 controls, 0 consumerless controls.

## Graphify

Code-only incremental update selesai: 20,054 nodes dan 34,936 edges. Diagnostic: missing endpoints 0, dangling endpoints 0, self-loops 0, duplicate edges 0, edge-collapse groups 0.

## Cleanup

Draft QA ID 32 dihapus dan diverifikasi hilang. Dataset shared user tidak dihapus. Tab QA ditutup.
