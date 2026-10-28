# Manage Article toolbar and centered loading Implementation Plan

> Execute inline with Superpowers Executing Plans and Ponytail. Pengguna menyetujui penerapan preview pada 28 Oktober 2026. Simpan seluruh artifact di project-artifacts, pertahankan checkout aktif, dan jangan commit/push atau memperbarui Graphify.

**Goal:** Terapkan Status di toolbar, posisi kontrol responsif yang disetujui, dan loading di tengah isi tabel untuk semua pemuatan daftar.

**Architecture:** Gunakan dropdown Bootstrap dan computed Vue 3 pada Blade aktif. Pertahankan listData(), parameter request, validasi server, pagination, bulk status, serta add/edit. Satukan seluruh state loading daftar pada satu baris tabel di bawah header; durasi mengikuti request nyata tanpa delay tambahan.

**Tech Stack:** Laravel Blade, Vue 3 Options API, Bootstrap, CSS container queries, Chrome/Playwright yang sudah terpasang.

**Spec:** project-artifacts/previews/manage-article-controls-20261028/design-review.md dan persetujuan terbaru pengguna. Desain terpilih: Status di toolbar.

## Global Constraints

- Backup tiga source sebelum perubahan dan verifikasi hash; folder preview milik pekerjaan sebelumnya dipertahankan.
- Chevron pertama, checkbox kedua, gap antartarget 24 px. Target sentuh/mobile 44 x 44 px.
- Desktop/tablet: kontrol sejajar dengan baris pertama judul. Mobile: kontrol di atas, judul selebar baris di bawahnya.
- Loading awal, pagination, search, sort header/dropdown, status/category/schedule memakai indikator yang sama di tengah isi tabel.
- Server-side data dan kontrak endpoint tetap. Tidak ada dependency baru atau penulisan data artikel saat QA browser.
- Graphify hanya dibaca, menunggu perintah pembaruan terpisah. E: tidak tersedia; pakai memori relevan C: dan verifikasi source aktual.

## Review Focus

- Judul panjang dan viewport 320 px: tidak overflow, kontrol tidak saling tumpang tindih.
- Pointer sentuh tablet: 44 px dan gap 24 px tetap, bukan hanya pada viewport mobile.
- Initial/empty/error list: loading tetap terpusat, header desktop tetap ada, retry berfungsi.
- Query berturut-turut: request lama tidak menimpa hasil terbaru atau mematikan loading yang masih berjalan.
- Dropdown status dan select-all mobile: tetap memakai state Vue yang sama, termasuk Cancel bulk dan reset pilihan saat query berubah.

## Task 1: Terapkan frontend dan buktikan perilakunya

**Files:** resources/views/manage_article/manage_article.blade.php, public/assets/css/article/manage-article-2026.css, public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js. Test/artifact baru pada project-artifacts/tests dan project-artifacts/qa/manage-article-toolbar-20261028.

- [x] Buat unit test computed label status dan browser regression untuk dropdown, posisi kontrol, dan loading; jalankan sebelum perubahan dan catat kegagalan yang diharapkan.
- [x] Backup Blade/CSS/Vue dengan timestamp, hash, dan snapshot status Git.
- [x] Ganti tab menjadi dropdown dan ringkasan status, tambah select-all mobile, satukan loading/error/empty/rows pada tbody yang sama.
- [x] Terapkan ukuran kontrol/top alignment dan layout mobile dalam CSS khusus Manage Article. Pertahankan detail, action menu, dark mode, dan pagination.
- [x] Jalankan unit/regression JavaScript yang relevan, syntax check, dan git diff --check.
- [x] Jalankan browser QA terautentikasi: desktop/tablet/mobile light-dark, touch, loading request yang ditahan, filter/query, retry, detail/selection/bulk cancel, dan smoke add/edit tanpa submit.
- [x] Periksa diff akhir dan source/backup/graph hash, tulis laporan QC beserta keterbatasan.

## Rulings

- Persetujuan langsung pengguna sudah mencakup implementasi di CMS aktif; tidak mengulang permintaan persetujuan atau membuat checkout terpisah yang tidak digunakan aplikasi lokal.
- Tidak ada perubahan backend karena alur request bersama sudah mencakup seluruh pemicu loading. Tambahan Vue cukup computed label status untuk dropdown.
- Perencanaan, pengujian, dan review dilakukan inline; tidak menambah pekerjaan fitur di luar daftar artikel.

## Completion

Implementasi dan review inline selesai. Bukti final: `project-artifacts/qa/manage-article-toolbar-20261028/completion.md`, 47 test JavaScript, 24 kasus loading runtime, 10 responsive light/dark, 2 touch, dan 5 pemeriksaan dropdown. Vue runtime 3.5.21. Tidak ada error JavaScript, Vue warning, atau request perubahan artikel dalam QA utama. Original backup, lima file backend/form terkait, HEAD/index, dan graph tetap terverifikasi.
