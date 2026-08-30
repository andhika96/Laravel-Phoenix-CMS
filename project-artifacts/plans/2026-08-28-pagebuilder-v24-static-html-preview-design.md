# Page Builder v2.4 — Static HTML Preview / Exact Visual

Tanggal: 2026-08-28  
Project: `D:\Laragon\www\laravel-13-phoenix`

## Tujuan

Menambahkan jalur `Exact Visual` untuk static HTML import tanpa mengorbankan jalur `Editable Native` atau fungsi manual Page Builder v2.4.

## Keputusan

- Tidak melakukan restore terhadap converter native yang sudah ada.
- `native` tetap menjadi default API untuk backward compatibility.
- Toolbar editor v2.4 memilih `exact` sebagai default untuk import baru.
- `static_html` adalah modul v2.4 internal, terdaftar di ModuleCatalog tetapi `toolbox: false`.
- Exact mode menyimpan DOM sumber yang sudah disanitasi dalam `srcdoc` dan merendernya melalui iframe sandbox.
- Iframe tidak diberi `allow-same-origin`, `allow-forms`, `allow-popups`, atau top-navigation permission.
- Semua source script dibuang kecuali dependency Phosphor Web versi tepat yang di-allowlist untuk kebutuhan ikon visual.
- Tailwind config, Google Fonts, Bootstrap CSS, custom CSS, dan remote image URL dipertahankan melalui allowlist/sanitizer.
- Relative asset tidak diupload; tetap menjadi unresolved sampai Base URL atau File Manager V2 diputuskan pada scope terpisah.

## Isolasi fungsi lama

- Converter native tetap menghasilkan Container/Grid/Heading/Button/Text/Icon/Image seperti sebelumnya.
- `staticImport` metadata dan `static_html` hanya muncul pada alur import.
- Class sanitizer manual tidak diubah untuk node biasa; `preserveCssClasses` hanya berasal dari import native.
- Widget manual Button/Heading tetap menggunakan default style/settings lama.
- Frontend halaman manual tidak memuat dependency static import.

## Alur

```text
Import Static
  ├─ Exact Visual  -> static_html -> iframe sandbox + srcdoc DOM asli
  └─ Editable Native -> converter native -> Container/Grid/widgets
```

## Batasan

- Exact Visual menjamin fidelity markup/CSS jauh lebih tinggi, tetapi source JavaScript interaktif tetap tidak dijalankan kecuali dependency ikon yang di-allowlist.
- Gambar relatif dari file tunggal tidak dapat diselesaikan tanpa URL dasar publik atau upload asset.
