# Arunika Canvas Header Control and Search Design

## Scope

Perubahan hanya berlaku pada header tema Arunika Canvas.

## Current Runtime State

- Mobile `414 x 846`: tombol navigation `40 x 40px`, ikon Font Awesome bars sekitar `12.25 x 14px`, dan search `298 x 32px`.
- Desktop `1440 x 900`: tombol collapse `32 x 32px`, ikon SVG panel `18 x 18px`, dan search `220 x 32px`.

## Approved Design

- Ganti ikon Font Awesome bars pada tombol navigation mobile dengan markup SVG panel yang sama seperti tombol collapse desktop.
- Gunakan ukuran tombol mobile `36 x 36px` dan ikon `18 x 18px`; ukuran desktop tetap `32 x 32px`.
- Sembunyikan `.ph-search-container` pada desktop dan mobile Canvas dengan `display: none !important`.
- Pertahankan fungsi `toggleSidebar()`, label aksesibel, state `aria-expanded`, header height `52px`, dan spacing mobile yang sudah disetujui.

## Implementation Boundary

- Ubah hanya layout Blade Canvas, stylesheet Canvas, dan regression test Canvas mobile.
- Jangan mengubah JavaScript sidebar, tema Arunika lain, atau form search global milik tema lain.

## Verification

- Static regression mengunci SVG mobile yang sama, ukuran tombol `36px`, ukuran ikon `18px`, dan search Canvas tersembunyi.
- Runtime desktop dan mobile memastikan search tidak terlihat, tombol mobile berukuran `36 x 36px`, dan sidebar tetap dapat dibuka serta ditutup.
- Jalankan focused Node test, Canvas regression suite, Laravel suite, Blade cache, dan `git diff --check`.
