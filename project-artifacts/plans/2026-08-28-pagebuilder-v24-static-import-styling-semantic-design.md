# Page Builder v2.4 Static Import Styling and Semantic Mapping Design

- Tanggal: 2026-08-28
- Scope: framework dependency, scoped custom CSS, semantic CTA/icon
- Asset policy: remote HTTP(S) digunakan langsung; relative local assets tetap missing
- Storage/File Manager: out of scope
- Database/Save/commit/push/deploy: tidak dilakukan

## Goal

Membuat hasil static import Tailwind/Bootstrap 5 menggunakan class dan custom CSS sumber secara aman di Canvas dan frontend, serta mengubah CTA/icon yang dikenali menjadi widget native, tanpa memengaruhi halaman manual.

## Isolation contract

Fitur hanya aktif bila root layout memiliki `settings.staticImport`. Halaman manual tanpa metadata tersebut tidak memuat Tailwind/Bootstrap dependency tambahan dan tidak melalui CSS processor.

Global normalizer, drag/drop, widget definitions, dan v2.3 tidak diubah. `app.js` hanya mendapat dependency loader yang no-op untuk halaman manual.

## Framework dependencies

Metadata root:

```json
{
  "staticImport": {
    "frameworks": ["tailwind"],
    "stylesheets": ["https://fonts.googleapis.com/..."]
  }
}
```

Tailwind dimuat dari URL tetap/allowlisted. Config runtime mematikan preflight dan memakai `important: '.pb-import-root'` agar utility tidak memengaruhi editor shell atau halaman manual.

Bootstrap editor tidak perlu dimuat ulang karena editor shell sudah memakai Bootstrap 5.3.3. Frontend hanya memuat Bootstrap CSS bila metadata root menyatakan `bootstrap5`.

External stylesheet hanya menerima HTTPS Google Fonts. Stylesheet arbitrary tidak diteruskan.

## Scoped CSS

`StaticPageCssProcessor`:

- mengambil isi `<style>`;
- membuang `@import`, `@font-face`, keyframes, expression, javascript/vbscript URL, behavior, dan binding;
- mempertahankan media/supports rules yang aman;
- mem-prefix selector ke `.pb-import-root`;
- mengubah `:root`, `html`, dan `body` menjadi root import;
- membuat selector source ID kompatibel dengan `#id` Canvas dan `data-css-id` frontend;
- membuang relative CSS URL karena HTML tunggal tidak membawa asset;
- menghasilkan utility warna custom dari Tailwind config yang dapat diparse secara terbatas.

CSS hasil processor menjadi payload `customCss`, sehingga editor dan Save/frontend memakai jalur Custom CSS yang sudah ada.

## Semantic mapping

- `button` selalu menjadi widget `button`.
- `a` menjadi Button bila memiliki `.btn`, class berakhiran `-button`, atau `role=button`.
- Source custom button class sederhana seperti `gold-button` masuk `className`.
- Nested icon yang dikenali diterjemahkan ke icon Font Awesome lokal.
- Standalone `i` dengan Phosphor/Bootstrap/Font Awesome class menjadi widget `icon`.
- Link navigasi biasa tetap Text Editor.

## Safety and compatibility

- Imported scripts tidak dieksekusi ulang.
- Tailwind config source tidak dieval.
- CSS dan dependency URLs disanitasi/allowlist.
- Manual layouts tetap menghasilkan HTML/assets yang sama.
- Remote image URL tetap dipakai langsung; relative image tetap warning/placeholder.

## Acceptance

1. Tailwind fixture menghasilkan scoped custom CSS dan root metadata.
2. Manual layout tanpa metadata tidak memuat framework dependency.
3. Tailwind dependency hanya berlaku di bawah `.pb-import-root`.
4. Google Fonts link aman dipertahankan; arbitrary stylesheet ditolak.
5. CTA `gold-button` menjadi Button; Phosphor icon yang dikenali menjadi Icon/FA mapping.
6. Existing focused/full Node checks tetap lulus.
7. Browser visual tetap harus diuji ulang tanpa Save.

