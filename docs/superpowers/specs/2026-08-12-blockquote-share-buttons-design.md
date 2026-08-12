# Blockquote dan Share Buttons — Widget Pro v2.3

## Tujuan

Menambahkan dua widget Pro baru ke Page Builder Elementor v2.3 dengan kontrak yang sama seperti widget Pro sebelumnya: registry, definition/defaults/normalizer, panel Content/Style/Advanced, canvas preview, parser Blade, frontend runtime, responsive values, dan state/interaction yang relevan.

Referensi perilaku dan opsi:

- [Elementor Blockquote widget](https://elementor.com/help/blockquote-widget-pro/)
- [Elementor Share Buttons widget](https://elementor.com/help/share-buttons-widget-pro/)

## Blockquote

### Content

- Skin: Border, Quotation, Boxed, Clean.
- Alignment untuk skin Quotation, Boxed, dan Clean: Left, Center, Right.
- Content.
- Author.
- Tweet Button: On/Off.
- View: Icon & Text, Icon, Text only.
- Tweet Button Skin: Classic, Bubble, Link.
- Label.
- Username.
- Target URL: Current Page, None, Custom Link.
- Custom URL ketika target memakai Custom Link.

### Style

- Content: text color, typography, gap.
- Author: text color dan typography.
- Tweet Button: size, border radius, official/custom color, normal/hover primary-secondary colors, transition duration, typography.
- Border skin: border color, border width, gap, transition duration, vertical padding.
- Quotation skin: quote color, quote size, gap.
- Boxed skin: padding, background normal/hover, border type/width/radius normal/hover, box shadow normal/hover, transition duration.
- Advanced: shared advanced controls dari kontrak image_box.

### Runtime

Canvas dan Blade memakai markup yang sama secara semantik. Tweet URL dibangun dari target yang sudah dinormalisasi dan di-encode; tidak ada HTML/URL mentah dari setting yang disisipkan tanpa escaping atau validasi.

## Share Buttons

### Content

- Repeater Add Item.
- Per item: Network dan Custom Label.
- View: Icon & Text, Icon only, Text only.
- Show Label.
- Skin.
- Shape.
- Columns: Auto atau 1–6.
- Alignment: Left, Center, Right.
- Target URL: Current Page atau Custom.
- Custom URL ketika target memakai Custom URL.

Network yang disediakan mencakup network yang umum dan yang diwajibkan referensi resmi, termasuk Facebook, Twitter legacy, X, Threads, LinkedIn, Pinterest, Reddit, WhatsApp, Telegram, Email, dan Print.

### Style

- Columns Gap dan Rows Gap dengan responsive unit control.
- Button Size, Icon Size, Button Height.
- Color: Official atau Custom.
- Primary background dan Secondary text untuk normal/hover.
- Typography.
- Skin: Flat, Gradient, Minimal, Framed, Box, 3D.
- Shape: Rounded, Square, Circle, None.
- Advanced: shared advanced controls dari kontrak image_box.

### Runtime

Network URL memakai allow-list network dan URL scheme http, https, relative, mailto, atau action internal yang aman. Copy dan Print memakai data action yang diproses frontend runtime; network eksternal memakai target blank dan rel noopener noreferrer.

## Acceptance criteria

1. Kedua widget muncul di registry kategori pro dan toolbox.
2. Defaults dan normalize mengisi fallback deterministik serta menolak enum, ukuran, URL, dan item network yang tidak valid.
3. Semua opsi Content dan Style di atas tampil di settings dengan conditional controls yang sesuai.
4. Canvas preview dan Blade renderer memakai setting yang sama, termasuk responsive style, hover state, warna official/custom, dan interaction.
5. Code Highlight serta Testimonial Carousel yang sudah ada tetap lolos test.
6. Node/PHP checks, focused tests, suite v2.3, dan health Graphify berhasil. Browser QA tetap read-only dan tidak menekan Save.
