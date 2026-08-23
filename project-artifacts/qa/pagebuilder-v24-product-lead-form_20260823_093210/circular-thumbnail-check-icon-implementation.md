# Product Lead Form — Circular Thumbnail and Checklist Icon

Tanggal: 2026-08-23

## Perubahan

- Model thumbnail default memakai `Image Shape: Circle`, `border-radius: 50%`, dan `object-fit: cover` agar mengikuti pola thumbnail halaman MG Test Drive.
- Tersedia pilihan `Circle`, `Rounded`, dan `Custom`; `Custom` membuka kontrol radius four-sides.
- Checklist selected memisahkan ukuran badge (`Selected Check Size`) dari ukuran glyph (`Selected Check Icon Size`).
- Inset checklist diperbesar dan diberi ring warna background selected agar tidak menempel pada border kartu rounded.
- Default kartu tetap `Card Height Mode: Auto`; mode `Fixed` tersedia bila tinggi kartu memang ingin dipaksa.
- Unit `%` tersedia pada Image Height.

## Verification

- Product Lead Form Node suite: 10 passed.
- Full v2.4 Node suites: 388 passed, 0 failed.
- Full v2.4 PHP Feature+Unit suites: 154 passed, 10,303 assertions.
- Product renderer focused tests: passed, 14 assertions on the query/media render path.
- Control-binding audit: 50 modules, 1,791 controls, 0 consumerless controls.
- PHP/JS syntax, SFC compile, Vite build (58 modules), and `git diff --check`: passed.

## Graphify

Incremental update selesai tanpa clustering: 20,042 nodes dan 36,990 links. Diagnostic: 0 missing endpoints, 0 self-loops, 2,068 dangling external-reference endpoints, dan 41 directed same-endpoint relation groups.
