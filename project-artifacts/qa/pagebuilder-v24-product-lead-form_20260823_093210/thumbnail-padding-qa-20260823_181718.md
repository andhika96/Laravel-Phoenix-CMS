# Product Lead Form — Thumbnail Padding QA

Tanggal: 2026-08-23 18:17 WIB  
Surface: Chrome authenticated session, temporary draft ID 33  
Batasan: read-only; tidak Save, Apply Dataset, Reset, atau submit.

## Hasil

- Control baru tampil pada `Style → Selector Cards → Card Media → Thumbnail Padding`.
- Default `0px`; menggunakan four-side responsive control.
- Seed `Thumbnail Padding: 24px` menghasilkan computed padding `24px` pada Top/Right/Bottom/Left.
- Border media tetap `solid 0.74px rgb(37,99,235)`.
- Image box tetap `160px × 160px`, sedangkan image content menjadi `110.52px × 110.52px`.
- Inset aktual image terhadap media border: sekitar `24.73px` pada keempat sisi (padding + border).
- Image Fit tetap `contain`; image tidak overflow keluar dari media box.
- Console errors/warnings: kosong.

## Automated verification

- `node --test tests/pagebuilder-v24-product-lead-form.test.mjs`: 12 passed.
- `node --test tests/pagebuilder-v24-*.test.mjs`: 390 passed, 0 failed.
- `php artisan test --filter=PageBuilderElementorV24`: 154 passed, 10,310 assertions.
- `npm.cmd run build`: sukses, 58 modules transformed.
- Blade `php -l`: sukses.
- `git diff --check`: sukses.
- Graphify code-only: 20,054 nodes, 34,934 edges; missing/dangling/self-loop/duplicate/edge-collapse: 0.

## Cleanup

Draft QA ID 33 dihapus dan diverifikasi hilang. Dataset shared user tidak disentuh. Tab QA ditutup.
