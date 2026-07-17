# Arunika Canvas Mobile Spacing Design

## Scope

Perubahan hanya berlaku pada tema Arunika Canvas di viewport dengan lebar maksimum `768px`.

## Current Runtime State

Pada viewport `414 x 846`, `.ph-layout-right` memiliki computed margin `15px 15px 15px 0`, sedangkan `.ph-top-bar` memiliki padding `0 10px` dan tinggi `52px`.

## Approved Design

- Override margin `.ph-layout-right` menjadi `0` agar canvas konten memenuhi mobile viewport tanpa outer gutter.
- Naikkan padding horizontal `.ph-top-bar` dari `10px` menjadi `14px` agar isi header sedikit lebih lega.
- Pertahankan padding vertikal `0`, tinggi header `52px`, dan seluruh perilaku desktop.
- Jangan mengubah struktur Blade, JavaScript sidebar, atau spacing konten internal.

## Implementation Boundary

Tambahkan deklarasi Canvas-specific di blok `@media (max-width: 768px)` paling akhir pada `public/assets/css/themes/arunika_canvas/arunika_canvas.css`. Spesifisitas harus cukup untuk mengalahkan aturan Canvas desktop tanpa memakai `!important`.

## Verification

- Regression test statis mengunci `margin: 0` pada `.ph-layout-right` dan `padding: 0 14px` pada `.ph-top-bar` di media query mobile Canvas.
- Runtime browser pada `414 x 846` harus menunjukkan computed margin `0px`, padding header `0px 14px`, tinggi header tetap `52px`, dan tidak ada horizontal overflow.
- Jalankan focused Node test, Laravel test suite, Blade cache, dan `git diff --check`.
