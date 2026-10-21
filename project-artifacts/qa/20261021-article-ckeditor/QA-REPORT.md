# CKEditor ↔ seluruh template detail artikel

Tanggal: 21 Oktober 2026 WIB. Project: Laravel 13 Phoenix.

## Tujuan dan hasil

Konten yang disunting di CKEditor harus mempertahankan formatnya setelah create, save, edit, dan render pada Focused Reader, Editorial Feature, serta Knowledge + TOC. Layout halaman, cover, metadata, navigasi dan TOC tetap milik template. Editor dan seluruh renderer kini memakai satu stylesheet konten; ukuran dasar mengikuti CMS dan format inline eksplisit penulis tetap dipertahankan.

Kesamaan yang dibuktikan: HTML round-trip identik; properti computed konten yang diuji identik pada 12 kombinasi template/viewport; geometri 12 elemen representatif pada lebar kolom 640px cocok dengan toleransi 1px. Ini bukan klaim pixel-diff seluruh halaman atau dukungan semua plugin/premium CKEditor yang tidak terpasang.

## Artikel uji nyata

- ID: **5059**.
- Judul: **[QA CKEditor] Panduan Sistem Andal — Uji Seluruh Format Konten**.
- Frontend: http://laravel-13-phoenix.aruna/article/qa-ckeditor-panduan-sistem-andal-uji-seluruh-format-konten
- Edit: http://laravel-13-phoenix.aruna/manage_article/edit/5059
- Sekitar **1.911 kata**, **9 gambar**. Dibiarkan publish/public agar dapat direview sesuai permintaan membuat artikel uji. Tidak mengubah artikel lain atau template default global.
- Aset unggahan: `/storage/ckfinder/articles/qa-ckeditor-20261021-lake.png`, dari gambar danau lokal yang sudah tersedia dalam project-artifacts.
- Form create dan update mengembalikan HTTP 200/success. Editor membuka ulang HTML secara persis.
- Slug awal `qa-ckeditor-detail-parity-20261021` berubah menjadi slug judul saat uji form edit karena perilaku form yang ada. URL di atas adalah URL akhir yang telah diverifikasi HTTP 200.
- Template aktif saat pengujian: **Focused Reader**. Dua template lain diuji melalui render Blade server terhadap record database yang sama tanpa mengubah pilihan global.

## Implementasi

New source:
- `public/assets/css/article/article-editor-content.css`: content styles dari CKEditor 5 build 38.1 yang sudah terpasang, dengan atribusi lisensi. Aturan editor UI tidak dimuat sebagai UI frontend. Menambahkan kontrak tipografi CMS, heading, kutipan, nested list, checklist, marker/pen highlight, ukuran relatif teks, gambar block/inline/float/resized, caption, tabel, serta kode. CSS hanya berlaku pada `.article-rich-content`.

Existing source changed (6 files):
- `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`: menambahkan class content yang sama pada editable root; membetulkan image styles configuration; menyediakan tombol alignment gambar yang valid; menampilkan strikethrough/code/codeBlock/horizontalLine yang sudah termasuk dalam build; opsi CKFinder resource Articles eksplisit; menghapus referensi toolbar `tableCellProperties` karena plugin tersebut tidak ada di build aktual.
- `resources/views/manage_article/manage_article_add.blade.php` dan `manage_article_edit.blade.php`: memuat stylesheet bersama.
- `resources/views/article/detail.blade.php` dan `resources/views/manage_article/templates/preview.blade.php`: memuat stylesheet bersama setelah CSS template.
- `resources/views/manage_article/templates/partials/options-focused-reader.blade.php`: menyembunyikan kontrol body/quote/paragraph/heading spacing yang akan bertentangan dengan kesamaan editor. Header, shell, cover, metadata, navigation serta jarak cover-ke-konten tetap dapat diatur. Data opsi lama tidak dihapus.

Konflik yang ditemukan dan diperbaiki: heading warna/ukuran dari tema mobile CMS, padding TH dari `.table thead tr th !important`, figure margin-inline Focused Reader, serta batas 50% untuk image-style-side. Tabel mewarisi font/kepadatan konten secara eksplisit agar editor tanpa shell CMS juga konsisten.

## Cakupan fitur yang diuji

- Heading, bold, italic, underline, strikethrough, inline code.
- Link, font family eksplisit (Georgia), font color, ukuran tiny/small/default/big/huge.
- Enam highlight: marker yellow/green/pink/blue, pen red/green.
- Alignment left/center/right/justify.
- Bulleted/numbered/nested list, indent/outdent daftar, todo list checked/unchecked.
- Block quote, code block, horizontal line.
- Select all, remove format, undo/redo; word count terbaca.
- Image styles inline/block/side/alignLeft/alignRight/alignCenter/alignBlockLeft/alignBlockRight.
- Resize original/25/50/75/100%, alt text, caption, link image.
- Upload asli melalui CKEditor CKFinder upload adapter; membuka library Articles dan memilih file dengan double-click menambah gambar ke editor. Gambar pilihan tambahan dipulihkan sebelum save agar artikel tetap berisi 9 gambar.
- Insert table, insert row/column, heading row, merge/split cell, background/border/width properties, rowspan/colspan.

## Bukti dan hasil

- `command-checks.json`: **33** pemeriksaan command teks/editor.
- `image-table-command-checks.json`: **22** pemeriksaan command gambar/tabel.
- `render-verification.json`: **12** kasus (3 template × 320/390/834/1440px), tidak ada perbedaan pada properti yang dibandingkan. Sekitar 43 selector representatif, font/warna/alignment/decoration/list/float/border/padding/margin. Auto margins bergantung pada lebar kolom, sehingga diperiksa terpisah lewat geometri.
- `equal-width-geometry.json`: 12 elemen representatif × 3 template pada lebar 640px cocok dalam toleransi 1px; gambar side turut diperbaiki sampai cocok.
- `live-parity.json`: properti representatif editor vs frontend aktual dengan tema situs cocok.
- `live-checks.json`: frontend aktual pada 320/390/834/1440px tanpa overflow; semua 9 gambar termuat.
- Tabel menggunakan display table, checklist checked/disabled tepat, colspan/rowspan tetap 2.
- `final-editor-output.html` sama dengan keluaran setelah buka ulang; font-family eksplisit memakai format sah yang dikenali konverter CKEditor.
- `php-article-tests.txt`: **43 passed / 632 assertions**.
- `node-article-tests.txt`: **76 passed**.
- JS syntax, Blade compilation, dan `git diff --check` passed.
- Screenshot `focused-reader-*`, `editorial-feature-*`, `knowledge-toc-*` menampilkan artikel record yang sama. Screenshot `live-*` berasal dari frontend aktual; `live-mobile-formats.png` menunjukkan format teks (diambil sebelum koreksi terakhir contoh font family).

## Menjalankan ulang

```powershell
php project-artifacts/qa/20261021-article-ckeditor/render-saved-article.php
node project-artifacts/qa/20261021-article-ckeditor/run-parity.cjs
```

Perlu web server lokal berjalan dan artikel QA ID 5059 masih ada. Export PHP hanya membaca record dan render view, tidak mengubah DB. Runner menggunakan build CKEditor lokal dan konfigurasi uji yang diekspor, tanpa kredensial atau cookies. `verify-render.cjs` juga dipakai terhadap editor admin sebenarnya dalam sesi browser terautentikasi.

## Backup, konteks dan batas

- Backup sebelum edit: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20261021-article-ckeditor-094127`. Hash dan daftar backup di `backup-manifest.json`. Semua artifact uji/scan/QC berada dalam project-artifacts; aset upload berada pada storage aplikasi melalui adapter resmi.
- Memori relevan: `E:\AI\Memories\20261020-delta-revamp-focused-reader-01a14ef8-cc2f-78d3-b204-f92352ffbb78.md` dan bagian awal registry C: MEMORY.md. Isi digunakan sebagai konteks dan diverifikasi ulang.
- Graphify graph dated 20 Oktober 2026; query `Focused Reader CKEditor article blockquote` dan `Manage_Article_Controller ckeditor focused-reader` memberi lokasi file. Semua kesimpulan implementasi diperiksa pada source aktual; tidak mengandalkan relasi INFERRED/AMBIGUOUS. Graph tidak diperbarui sesuai instruksi manual.
- Read sources: konfigurasi CKEditor Article, build package/src, template detail ketiganya, renderer public dan manager preview, controller store/update/edit, request validation, SiteTypography/CMS typography dan CSS template. Tidak memindai backup sebagai source aktif.
- UI UX Pro Max, Design Taste Frontend, Ponytail, Karpathy digunakan. Superpowers tidak tersedia pada instalasi yang diperiksa.
- Batas build: `tableCellProperties` tidak terpasang; tidak mengklaim fitur tersebut berfungsi. Indent yang tersedia adalah indent daftar. MediaEmbed/source editing/raw HTML/premium plugin yang tidak diaktifkan pada toolbar Article tidak diperluas diam-diam. Tidak menambah dependensi atau mengganti build CKEditor.
- Perbedaan lebar kolom menghasilkan reflow yang wajar. Editing chrome/selection handles/caret bukan konten publik. Font sistem eksternal, browser lain, dan seluruh kombinasi HTML pihak ketiga belum mendapat pixel-by-pixel testing.
- Tidak commit, push, atau memperbarui memori E:/C:; laporan ini adalah artifact QC dalam project.
