# Arunika Equinox Visual Audit and Design Previews

Tanggal: 2026-09-01

## Scope

Review read-only terhadap dua screenshot dashboard Arunika Equinox dan eksplorasi tiga arah visual untuk mengurangi kesan warna yang terlalu dalam/dominan. Tidak ada source production yang diubah dalam audit ini.

## Evidence

- `01-equinox-desktop-neutral.png`: mode terang dengan shell mineral/abu-hijau.
- `02-equinox-desktop-green.png`: mode terang dengan tint hijau yang lebih kuat.

## Temuan utama

1. **Wash warna terlalu global.** Gradient utama merentang ke seluruh canvas, sidebar, header, dan panel. Saat tint hijau/teal berubah, hampir semua permukaan ikut berubah sehingga warna terasa “masuk terlalu dalam”.
2. **Sidebar terlalu banyak membawa visual weight.** Panel terapung memakai radius `26px`, gradient internal, border berwarna, dan shadow besar. Ia menarik perhatian hampir setara dengan panel konten.
3. **Hierarchy menu lemah.** Text/icon menu menggunakan muted teal/gray di atas surface berwarna; hover dan active tidak punya pemisahan tonal yang cukup kuat pada semua palette.
4. **Panel transparan menumpuk.** `--ph-equinox-panel` dan `--ph-equinox-panel-strong` memakai alpha, sementara canvas dan sidebar juga memakai color-mix. Hasilnya permukaan terlihat berkabut, bukan berlapis jelas.
5. **Arah warna hijau terlalu dominan.** Pada SS hijau, warna brand tampil di background, sidebar, header, dan panel sekaligus. Accent sebaiknya dipindahkan ke active state, icon, focus ring, dan detail kecil saja.

## Source evidence

Source aktif yang diperiksa: `public/assets/css/themes/arunika_equinox/arunika_equinox.css` dan `resources/views/themes/arunika_equinox/cms/cms_layout.blade.php`.

Token shell aktif memuat `--ph-equinox-main-gradient` dengan radial tint `22%`, sidebar tint `14%`/`5%`, panel alpha `.46`/`.76`, dan sidebar shadow `0 22px 52px ...`. Menu memakai `--ph-bg-hover: rgba(11, 184, 159, 0.075)` dan muted text `#647A76`. Kombinasi ini menjelaskan mengapa perubahan palette terasa mewarnai seluruh UI, bukan hanya menjadi accent.

## Saran desain

### Soft Mineral

Rekomendasi utama. Pertahankan sidebar terapung dan identitas Equinox, tetapi gunakan canvas/sidebar off-white netral. Teal hanya untuk active state, icon terpilih, focus ring, dan marker kecil. Ini memberi perubahan paling aman dengan dampak visual paling besar.

### Teal Mist

Pertahankan mint/teal sebagai identitas yang lebih terlihat, tetapi desaturasi dan batasi pada sidebar serta accent. Panel utama tetap putih agar konten tidak ikut tenggelam dalam tint.

### Editorial Neutral

Gunakan sidebar flat warm-gray tanpa kapsul besar. Hierarchy dibangun dengan spacing, divider tipis, dan typography; teal/sand hanya sebagai accent kecil. Ini paling matang dan paling mudah dipelihara, tetapi paling banyak mengubah siluet Equinox.

## Accessibility limits

Screenshot belum cukup untuk menyatakan compliance. Sebelum implementasi perlu cek contrast text/icon minimal `4.5:1`, visible keyboard focus, hover/focus parity, responsive 375px dan desktop, reduced-motion, serta dark-mode pairing.

## Preview assets

Tiga preview dibuat sebagai gambar terpisah memakai dua SS di atas sebagai reference image, ukuran `1920x1024`, tanpa browser chrome:

- `03-preview-soft-mineral.png`
- `04-preview-teal-mist.png`
- `05-preview-editorial-neutral.png`

Semua file berada di `project-artifacts/qa/arunika-equinox-visual-audit-20260901/`. Preview ini adalah bahan diskusi; belum ada option yang diterapkan ke source.
