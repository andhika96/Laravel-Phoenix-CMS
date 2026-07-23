# Page Builder Setting Controls Audit — 2026-07-23

## Scope

Audit konsistensi form setting Page Builder Elementor untuk color picker, numeric/slider/unit controls, tab/accordion density, responsive controls, serta parity editor/canvas/frontend pada widget yang disentuh dalam sesi ini.

## Canonical pattern

- Color: `pb-color-row` + native `pb-color-swatch` + local `coloris pb-coloris-input`.
- Dimension: slider bila rentang terdefinisi, numeric input, dan `pb-mini-unit`; tidak meminta pengguna mengetik token CSS seperti `20px` atau `30%` secara manual.
- Spacing multi-sisi: satu unit selector, empat numeric inputs, dan link/unlink state.
- Panel: shared `pb-tab-nav`, `pb-collapsible`, `pb-mini-tab-nav`, dan typography/text-effect controls.

## Status per scope

| Scope | Status statis | Ringkasan |
|---|---|---|
| Shared Advanced Controls | Selesai | Color rows, media/background fields, gradient sliders, border/shadow controls, dan unit vocabulary diseragamkan. |
| Image Box | Selesai | Resolution/title tag parity, typography/text effects, border width, responsive dimensions, Advanced canvas, dan tiga Style color rows. |
| Basic widgets | Selesai | Heading, Text Editor, Image, Video, Button, Divider, Spacer, dan Icon memakai tab/control pattern yang konsisten. |
| Accordion | Selesai | Color/gradient controls, shared Text Shadow, Text Stroke, dan accessible label. |
| Tabs | Selesai | Content/Style/Advanced panels disusun dari setting existing tanpa mengubah key. |
| Grid / Row Grid | Selesai | Gap, padding, margin, border, radius, shadow, sticky, transform, dan position memakai numeric/unit controls. |
| Container / Container Fluid | Selesai | Flex/Grid gaps, border, radius, shadow, sticky, dan transform memakai numeric/unit controls. |

## Audit ulang source

Pencarian seluruh `public/js/pagebuilder_elementor/widgets/**/Settings.vue` (file backup dikecualikan) menghasilkan nol temuan untuk kategori berikut:

- color-bound input tanpa native swatch pada control row;
- input dengan placeholder unit manual `px`, `%`, atau `deg`;
- raw `v-model` untuk kelompok dimension yang ditargetkan: border width/radius, shadow offsets, sticky offsets, serta transform rotate/offset/skew.

Field semantik yang memang bebas tetap berupa text input, misalnya URL, CSS ID/Class, Custom CSS, grid template expression, dan Rows yang menerima `auto` atau jumlah baris. Z-index tetap numeric tanpa unit karena properti tersebut unitless.

## Verification

- Regresi gabungan: **109 tests passed, 1,355 assertions**.
- Focused Grid/Row Grid: **5 tests passed, 92 assertions**.
- Focused Container variants: **4 tests passed, 80 assertions**.
- Audit pattern source: **clean** untuk kategori yang ditargetkan.

## Runtime visual QA

Before-state tersedia di `output/design-qa/image-box-advanced-background-before.png`. Post-patch browser capture belum tersedia karena browser automation tidak dapat dijalankan pada Windows ACL sandbox saat sesi ini. Karena itu hasil visual belum dinyatakan approved; pengguna perlu refresh halaman existing dan memeriksa control panels secara langsung.