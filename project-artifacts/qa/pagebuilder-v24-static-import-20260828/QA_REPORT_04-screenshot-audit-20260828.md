# Page Builder v2.4 Static Import Screenshot Audit

- Tanggal: 2026-08-28
- Surface: Page Builder Elementor v2.4 Canvas
- Input: \`E:/Apps/Laragon/www/ceo-masters/index.html\`
- Screenshots: 11 screenshot user-provided, viewport editor Desktop 1180 px
- Mode yang diuji user: Auto dan Tailwind
- Scope: audit visual/UX hasil import; tidak ada Save, Reset, atau perubahan database

## User goal

Mengubah landing page statis CEO Masters berbasis Tailwind menjadi node/widget native yang editable, dengan struktur, responsive behavior, asset, typography, color, dan visual hierarchy yang mendekati sumber asli.

## Yang sudah membaik

- Section order dan sebagian hierarchy sudah terbaca.
- Grid pathway tiga kolom terlihat cukup terpisah dan tidak lagi menjadi satu tumpukan total.
- Grid cards lima kolom sudah memiliki pembagian cell yang konsisten, walau typography dan spacing masih belum sesuai.
- Auto dan Tailwind menghasilkan struktur yang sama pada fixture Tailwind.
- Overlap terparah dari wrapper generik dan duplikasi menu responsive sudah berkurang pada payload terbaru.
- Editor toolbar dan outline masih terlihat sebagai chrome editor, bukan bagian dari hasil frontend.

## Temuan visual berprioritas tinggi

### P0 — Framework dan custom CSS belum tersedia

Canvas tampil hampir seluruhnya putih dengan typography default, sementara sumber memakai dark navy, gold, cream, custom font, border, shadow, button, card, dan hover treatment.

Bukti visual: screenshot 1, 2, 4, 5, 6, 8, 10, dan 11.

Bukti source: importer masih membuang \`<style>\`, stylesheet, dan script; \`customCss\` hasil import tetap kosong. Sumber CEO Masters memiliki Tailwind CDN/config serta tiga blok custom CSS.

### P0 — Asset relatif HTML tunggal menjadi placeholder

Image widget menampilkan placeholder karena sumber seperti \`ceo-masters-poster.jpg\` dan \`competition-assets/*.jpg\` relatif terhadap folder lokal yang tidak ikut di-upload bersama HTML.

Bukti visual: screenshot 2, 6, 10, dan 11.

Ini bukan kegagalan deteksi Tailwind. HTML tunggal memang tidak membawa sibling assets. Input ZIP atau asset ingestion diperlukan untuk fidelity gambar.

### P1 — Semantic widget belum cukup

- Link CTA Tailwind seperti \`gold-button\` masih cenderung menjadi Text Editor, bukan Button widget.
- Icon \`<i class="ph ...">\` menjadi node kosong/noise karena icon source belum dipetakan.
- Form sengaja menjadi placeholder static, sehingga area form kosong pada screenshot 6 dan 10.
- Heading, paragraph, CTA, dan image belum menerima source class sebagai \`cssClass\`/style settings.

Bukti visual: screenshot 1, 2, 6, 7, dan 10.

### P1 — Banyak utility Tailwind belum dipetakan ke setting

Yang masih hilang atau parsial antara lain \`max-w-7xl\`, \`w-full\`, \`h-* \`, \`items-center\`, \`justify-between\`, \`text-* \`, \`font-* \`, \`tracking-* \`, \`leading-* \`, \`bg-* \`, \`border-* \`, \`rounded-* \`, \`shadow-* \`, hover state, dan arbitrary values.

Bukti visual: screenshot 2, 3, 4, 5, 6, 8, 9, 10, dan 11.

### P1 — Encoding UTF-8 masih rusak

Teks seperti \`October Â· 2026\`, \`Dubai Â· January\`, dan karakter panah/aksen tampil sebagai mojibake.

Bukti visual: screenshot 3, 5, dan 10.

Parser perlu menormalisasi deklarasi/encoding sebelum \`DOMDocument::loadHTML()\`.

### P2 — Editor chrome memperberat persepsi hasil

Dotted grid outlines, node borders, toolbar, dan placeholder cell memang berguna untuk editing, tetapi membuat hasil import terlihat lebih jauh dari halaman asli. Acceptance visual perlu memiliki dua keadaan: editor Canvas dan Preview/frontend tanpa chrome.

## Diagnosis source-to-runtime

- \`StaticPageImportService::mapNode()\` sudah menghasilkan heading, text editor, button Bootstrap terbatas, image, divider, container, dan grid.
- \`layoutSettings()\` sudah menangani sebagian layout dan responsive grid.
- \`safeUrl()\` masih menolak asset relatif.
- \`<style>\`, \`<link>\`, dan \`<script>\` tetap dibuang sesuai batas keamanan MVP.
- Tidak ada framework CSS atau Tailwind config yang diterapkan ke Canvas/frontend.
- Tidak ada preservation layer yang mengisi \`cssClass\` dan \`cssId\` pada mapped widget.

## Recommended order

1. Tambahkan preservation layer: source \`id\` → \`cssId\`, source class tokens → \`cssClass\`, dengan sanitasi, deduplikasi, dan filter structural class yang sudah diwakili oleh setting.
2. Tambahkan dependency manifest page-level untuk framework yang terdeteksi; gunakan asset allowlist/pinned version dan jangan menjalankan script arbitrary dari HTML.
3. Untuk Tailwind custom theme, bawa konfigurasi warna/font secara aman atau gunakan generated CSS yang sudah discoped; CDN saja tidak cukup untuk \`bg-ink\`, \`text-cream\`, \`gold-button\`, dan custom selector.
4. Ubah link CTA/icon yang dapat dikenali menjadi Button/Icon widget; pertahankan form sebagai placeholder sampai behavior mapping disetujui.
5. Tambahkan ZIP asset resolver dan rewrite URL ke batch/File Manager.
6. Normalisasi UTF-8 sebelum parsing.
7. Ulangi browser QA pada Auto/Tailwind, Desktop/Tablet/Mobile, Editor dan Preview, tanpa Save.

## Evidence limits

- Screenshot yang diberikan adalah bukti visual terbaru dari user, tetapi bukan capture otomatis yang dilakukan pada turn audit ini.
- Browser DOM, computed style, console, network, dan frontend Preview belum dapat diverifikasi langsung dari session browser user.
- Full visual parity belum tercapai dan tidak boleh dinyatakan selesai dari screenshot ini saja.

## Source change status

Audit ini tidak mengubah source aplikasi. Perubahan source yang sudah ada tetap hanya pada static importer dan focused tests; \`app.js\` Canvas global tidak disentuh.
