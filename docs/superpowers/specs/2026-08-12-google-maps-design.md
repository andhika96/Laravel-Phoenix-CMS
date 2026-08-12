# Google Maps Widget Design

## Goal

Menambahkan widget `Google Maps` ke kategori Basic pada Page Builder Elementor v2.3 dengan tiga lapisan yang selaras: state editor, canvas preview, dan frontend Blade renderer.

## Reference and compatibility decision

Mapping kontrol mengikuti dokumentasi resmi Elementor:

- [Google Maps widget](https://elementor.com/help/google-maps-widget/)
- [Google Maps integration](https://elementor.com/help/google-maps-integration/)

Implementasi v2.3 memakai URL query embed terkontrol (`https://www.google.com/maps?q=...&z=...&output=embed`) dan tidak menambahkan global API-key setting pada scope ini. Nilai Location hanya dipakai sebagai query yang di-encode; pengguna tidak dapat menyuntikkan URL iframe mentah atau script. Dukungan Google Maps API key dapat ditambahkan kemudian sebagai integrasi global terpisah bila produk membutuhkannya.

## Widget contract

- Type: `google_maps`
- Label: `Google Maps`
- Category: `basic`
- Icon: `fas fa-map-marker-alt`
- Default location: `New York, NY`
- Default zoom: `14`
- Default height: `400px`
- Empty location: tampilkan placeholder editor/frontend tanpa iframe eksternal.

## Settings mapping

### Content

- `Location`: text input untuk alamat, nama tempat, atau koordinat yang akan di-encode ke query Google Maps.
- `Zoom`: integer `1..20`.

### Style

- `Height`: responsive size control dengan unit `px`, `%`, `em`, `rem`, `vh`; default `400px`.
- `CSS Filters / Normal`: Blur, Brightness, Contrast, Saturation, Hue.
- `CSS Filters / Hover`: Blur, Brightness, Contrast, Saturation, Hue.
- `Transition Duration`: `0..10` detik.

### Advanced

Memakai `editor.widgetAdvancedControls`, sehingga kontrak shared Advanced v2.3 tetap berlaku untuk Layout, Responsive, Attributes, Custom CSS, Motion Effects, Transform, Background, Border, Mask, dan kontrol Advanced lain yang tersedia pada komponen shared. Nilai advanced juga diteruskan ke frontend melalui `WidgetAdvancedStyleResolver`.

## Runtime behavior

- Canvas dan Blade menggunakan fallback responsive Desktop -> Tablet -> Mobile untuk Height sesuai pola v2.3.
- Filter Normal diterapkan pada iframe wrapper; filter Hover dan transition diterapkan melalui CSS variable pada root widget.
- Location di-encode sebagai query parameter, zoom di-clamp `1..20`, dan height/filter/duration divalidasi pada batas CSS/server.
- `cssClass` hanya menerima token class yang dinormalisasi; Advanced attributes tetap dirender oleh resolver bersama.
- Iframe memiliki title `Google Maps` dan `loading="lazy"`.
- Tidak ada raw HTML, raw iframe URL, atau script dari setting user.

## Verification contract

- RED-GREEN test untuk definition normalization, settings mapping, canvas output, registry wiring, dan Blade safety.
- Focused Node and PHP tests for Google Maps.
- Existing v2.3 Node and PHP suites remain green.
- Graphify is updated incrementally after source changes.
- Browser inspection remains read-only; Save is not used. If the existing Chrome bridge has no usable tab, report that boundary explicitly.
