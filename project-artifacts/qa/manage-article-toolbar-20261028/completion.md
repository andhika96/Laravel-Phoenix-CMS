# Manage Article: Status di toolbar dan loading terpusat

Tanggal: 28 Oktober 2026. Project: `D:/Laragon/www/laravel-13-phoenix`.

Status: diterapkan pada CMS lokal dan diverifikasi melalui Chrome terautentikasi. Pengguna menyetujui implementasi preview Status di toolbar serta loading di tengah isi tabel.

## Hasil implementasi

- All/Publish/Draft/Pending beserta jumlahnya dipindah ke dropdown Bootstrap di toolbar. Ringkasan status aktif tetap terlihat di atas daftar.
- Desktop/tablet menempatkan detail dan checkbox sejajar dengan baris pertama judul. Jarak antartarget terukur 24 px.
- Mobile memakai baris kontrol detail, checkbox, dan opsi, dengan judul/metadata selebar baris di bawahnya. Select-all tersedia di atas daftar dan terhubung dengan state Vue yang sama.
- Area sentuh mobile maupun tablet dengan pointer coarse terukur 44 × 44 px. Kontrol desktop fine pointer berukuran 36 × 36 px.
- Loading awal, next/previous, search/clear, sort header/dropdown, status, kategori, jadwal, page-size, reset, serta retry memakai satu indikator di tengah isi tabel. Header desktop tetap terlihat. Tidak ada delay buatan pada source aplikasi.
- Loading, error, empty state, dan data menggunakan tbody yang sama. Spinner mengikuti reduced motion. Styling hover mobile dirapikan agar latar satu artikel tidak terpecah menjadi kotak per sel.
- Vue tetap memakai runtime 3.5.21, Options API, VuejsPaginateNext, dan Bootstrap yang tersedia. Tidak menambah package.

## Source yang dibaca dan dimodifikasi

1. `resources/views/manage_article/manage_article.blade.php`: dropdown status, summary/select-all mobile, tbody state pemuatan/error/empty, dan tanggal pada metadata mobile.
2. `public/assets/css/article/manage-article-2026.css`: toolbar, target/alignment kontrol, container-query mobile, hover, dan loading terpusat.
3. `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js`: satu computed `articleStatusLabel` yang memakai daftar status dari server.

Acuan tambahan yang dibaca: preview/design-review 28 Oktober, test responsive-width dan list-behavior yang aktif, serta browser QA dan bukti implementasi 26 Oktober. Backend controller/resource, partial status, dan form add/edit tidak dimodifikasi; hash lima file tersebut diverifikasi tetap sama.

## Memori dan Graphify

- Drive `E:` tidak tersedia saat pemeriksaan sehingga `E:/AI/Memories` belum dapat dibaca. Memori relevan yang digunakan adalah registry `C:/Users/aruna/.codex/memories/MEMORY.md`, bagian Manage Article responsive revamp, beserta konteks preview percakapan ini; kondisi source dan runtime diperiksa kembali.
- Query Graphify `ManageArticleVue3` menemukan modul aktif pada `public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js:L1`; relasi file `contains` berstatus `EXTRACTED`.
- Query path dari `manage_article.blade.php` ke `public/assets/css/article/manage-article-2026.css` menunjukkan `references_stylesheet [EXTRACTED]`.
- Graph hanya dipakai sebagai peta navigasi sebelum perubahan. Tidak ada relasi `INFERRED`/`AMBIGUOUS` yang dijadikan bukti runtime. Graph tidak diperbarui karena pengguna menetapkan pembaruan manual; tiga source kini berubah setelah snapshot graph tersebut.

## Backup dan preservasi

Backup sebelum perubahan: `project-artifacts/backups/manage-article-toolbar-20261028/manifest.json`. Tiga backup original memakai timestamp dan penanda `toolbar_loading`; hash SHA-256 masing-masing sesuai source awal. Revisi selama QC memiliki backup tambahan bertimestamp di folder yang sama.

`source-verification.json` membuktikan tiga backup original valid, lima file backend/form terkait tidak berubah, serta Git HEAD, Git index, dan hash graph tetap sama. Folder preview sebelumnya dipertahankan. Tidak ada stage, commit, push, atau penulisan data artikel saat QA.

## Verifikasi statis dan unit

Command: `node --test tests/manage-article-responsive-width.test.mjs tests/manage-article-thumbnail-source.test.mjs tests/manage-article-template-manager.test.mjs project-artifacts/tests/manage-article-revamp-20261026/list-behavior.test.mjs project-artifacts/tests/manage-article-toolbar-20261028/status-label.test.mjs`.

Hasil: **47 test lolos**. Mencakup parameter query, respons yang datang tidak berurutan, failure/retry state, selection/detail, responsive columns, thumbnail, template manager, dan computed label status. Test label dan browser dropdown gagal sebelum implementasi, kemudian lolos setelah perubahan.

`node --check public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js` dan `git diff --check` lolos; Git hanya memberi peringatan normalisasi CRLF pada dua file yang sudah memakai CRLF.

## Verifikasi runtime terbaru

QA utama: `project-artifacts/tests/manage-article-toolbar-20261028/live-browser-qa.mjs`; hasil `live-verification.json`.

- **24 kasus loading** dengan request GET sengaja ditahan oleh browser QA untuk mengukur indikator sebelum respons dilanjutkan. Teknik penahanan hanya berada di script pengujian.
- **10 kombinasi responsive**: lebar 320, 390, 768, 1024, dan 1440 px pada light/dark. Tidak ada horizontal overflow atau elemen keluar batas. Desktop/tablet alignment selisih 0 px, gap 24 px. Pada mobile, judul memang berada di bawah baris kontrol.
- **2 kasus touch** pada 320 dan 768 px: detail dan checkbox 44 × 44 px, gap 24 px, tidak overflow.
- Detail Enter/Space, checkbox terpisah, select-all mobile/header, bulk confirmation Cancel, status counts, empty/reset, parameter gabungan, pagination/page-size, delete Cancel, error 503 lalu retry, reduced motion, serta smoke add/edit berhasil.
- Runtime errors: **0**. Vue warnings: **0**. Request perubahan artikel: **0**; login tidak dihitung sebagai penulisan artikel.
- Snapshot jumlah data: All 5.041, Publish 5.035, Draft 0, Pending 6. Angka ini mencerminkan waktu pengujian.

QA tampilan final: `final-visual-qa.mjs`; hasil `final-visual-verification.json`. **5 kasus dropdown** pada desktop/tablet/mobile dan light/dark: menu muat dalam viewport, semua empat pilihan tersedia, Escape menutup menu. Screenshot final menunggu transisi tema/navigasi selesai.

Contoh screenshot: `final-1440-light.png`, `final-768-dark.png`, `final-390-light.png`, `final-menu-390-light.png`, serta `loading-initial-1440.png`.

## Keterbatasan

Verifikasi browser menggunakan Chrome, termasuk emulasi touch; belum dilakukan pada perangkat fisik, Firefox, atau Safari. Tidak menjalankan ulang seluruh suite repository atau PHP backend suite karena source backend tidak berubah. Graphify masih memakai snapshot sebelum perubahan ini dan menunggu perintah pembaruan manual.
