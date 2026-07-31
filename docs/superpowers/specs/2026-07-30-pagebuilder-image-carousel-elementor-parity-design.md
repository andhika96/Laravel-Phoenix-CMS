# Page Builder Image Carousel Elementor Parity Design

## Goal

Menambahkan widget `Image Carousel` pada kategori `General` dengan parity Elementor untuk state editor, panel Content/Style/Advanced, canvas preview, persistence, dan frontend renderer.

## Data contract

- Widget type: `image_carousel`.
- `settings.images` adalah array item `{ id, url, alt, title, caption, description }`.
- Gallery dipilih melalui CKFinder multi-select, dapat diurutkan ulang, dan tiap item dapat dihapus tanpa mengubah helper media single-file yang sudah dipakai widget lain.
- Nilai pilihan, angka, boolean, URL, metadata gambar, dan responsive values dinormalisasi sebelum dipakai canvas atau disimpan.

## Editor controls

- Content / Image Carousel: Carousel Name, Images, Image Resolution termasuk Custom Width/Height, Slides to Show, Slides to Scroll, Image Stretch, Navigation, Previous/Next Arrow Icon, Link, Lightbox, dan Caption.
- Content / Additional Options: Lazyload, Autoplay, Pause on Hover, Pause on Interaction, Autoplay Speed, Infinite Loop, Animation Speed, dan Direction.
- Style / Navigation: posisi, ukuran, dan warna arrows; posisi, spacing, ukuran, warna, dan active color pagination.
- Style / Image: vertical align, spacing, border type/width/color, dan border radius.
- Style / Caption: responsive alignment, color, typography, text shadow, dan spacing.
- Advanced memakai shared controls, tetapi tidak menambahkan accordion `Display Conditions` dan `Cache Settings` yang berlebih.

## Rendering and interaction

- Canvas Vue menampilkan empty state jika gallery kosong dan track carousel jika berisi gambar.
- Slides to Show/Scroll mengikuti desktop, tablet, dan mobile; navigation, captions, links, border, dan typography diterapkan langsung pada canvas.
- Autoplay berhenti sesuai Pause on Hover/Interaction dan `prefers-reduced-motion`.
- Frontend Blade mencetak markup aman serta konfigurasi JSON lewat `data-carousel-config`; runtime lokal menginisialisasi arrows, dots, keyboard, resize, autoplay, infinite/clamped navigation, dan lightbox media file.
- Tidak memakai CDN atau dependency carousel eksternal.

## Security and compatibility

- Hanya URL `http`, `https`, root-relative, atau fragment yang boleh dirender.
- Metadata selalu di-escape; custom attributes Advanced tetap melalui resolver shared.
- Helper media single-file lama tidak berubah kontraknya.
- Custom image resolution memperluas endpoint rendition secara backward-compatible dengan `size=custom`, `width`, dan `height` terbatas.

## Verification

- Feature test memeriksa registry, defaults/normalization markers, seluruh label/conditional settings, canvas contract, frontend safe markup, runtime carousel binding, dan custom rendition.
- Jalankan focused test, suite PageBuilderElementor terkait, syntax checks, build/compiler check, `git diff --check`, HTTP editor, dan runtime browser QA.
