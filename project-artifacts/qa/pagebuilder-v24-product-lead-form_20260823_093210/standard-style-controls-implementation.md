# Product Lead Form — Standard Style Controls

Tanggal: 2026-08-23

## Perbaikan

Style Product Level Cards tidak lagi memakai input teks manual untuk nilai visual.

- Width, min-width, height, gap, image size, checklist size/offset memakai `size-control` dengan slider, numeric input, unit selector, dan responsive menu.
- Padding, margin, border width, card radius, image radius, image border width, dan checklist radius memakai `sides-control` dengan four-side values serta link/unlink.
- Alignment dan posisi checklist memakai `responsive-select`.
- Shadow memakai shared `textShadowControl`.
- Nilai shorthand lama tetap dinormalisasi ke side settings agar konfigurasi existing tidak rusak.

## Verification

- Product Lead Form Node suite: 9 passed.
- Full v2.4 Node suites: 387 passed, 0 failed.
- Product Lead Form renderer tests: 2 passed, 21 assertions.
- Full v2.4 PHP Feature+Unit suites: 154 passed, 10,300 assertions.
- Control-binding audit: 50 modules, 1,788 controls, 0 consumerless controls.
- PHP/JS syntax, SFC compile, and `git diff --check`: passed.
- Vite build: passed, 58 modules transformed.

## Graphify

Incremental update selesai tanpa clustering: 20,040 nodes dan 36,986 links. Diagnostic: 0 missing endpoints, 0 self-loops, 2,068 dangling external-reference endpoints, dan 41 directed same-endpoint relation groups.
