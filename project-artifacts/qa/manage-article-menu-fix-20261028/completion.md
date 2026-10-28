# Koreksi tombol header dan ukuran menu Manage Article

Tanggal: 28 Oktober 2026. Project: `D:/Laragon/www/laravel-13-phoenix`.

Permintaan pengguna: kembalikan Article Templates dan Category List menjadi tombol langsung di samping Add Post; sesuaikan ukuran teks dropdown opsi artikel dengan template CMS.

## Perubahan

- Header memiliki tiga aksi langsung: Add Post, Article Templates, dan Category List. Dropdown pengelolaan di header dihapus. Route template, atribut modal kategori, dan handler Vue yang sudah ada dipertahankan.
- Pada desktop yang cukup lebar, ketiganya satu baris. Header dapat membungkus pada tablet. Pada mobile, Add Post selebar baris; dua tombol sekunder berada di bawahnya dan teks boleh membungkus pada layar 320 px.
- Dropdown Manage Article memakai `font-size: inherit`, mengikuti ukuran teks dari template aktif. Ukuran tidak dikunci ke angka pixel baru. Pada pengujian desktop, font menu dan item turun dari 16 px menjadi 14 px, sama dengan sel tabel dan body CMS.
- Pointer coarse mempertahankan target item menu sekurangnya 44 px. Tidak mengubah Vue, backend, filter, loading, pagination, atau data artikel.

## Investigasi dan source

Source yang diperiksa: `resources/views/manage_article/manage_article.blade.php`, `public/assets/css/article/manage-article-2026.css`, referensi tombol di Manage Cover Image/Event, utility legacy `public/assets/css/extending-css-bootstrap-5.css`, serta penggunaan typography pada layout CMS default/calm_green.

Runtime membuktikan ukuran body/sel tabel 14 px, sedangkan dropdown mewarisi default Bootstrap 1rem/16 px. Penambahan class legacy `font-size-inherit` pada menu tidak mengubah ukurannya karena rule utility tersebut tidak dimuat oleh template aktif. Pemeriksaan CSSOM menemukan rule font dropdown aktif dari `assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css`. Solusi akhirnya satu properti `font-size: inherit` pada selector dropdown Manage Article yang sudah ada; class tambahan yang tidak bekerja dihapus kembali.

File source yang dimodifikasi hanya:

1. `resources/views/manage_article/manage_article.blade.php`: dua tombol header langsung menggantikan dropdown sebelumnya.
2. `public/assets/css/article/manage-article-2026.css`: header wrapping/grid mobile, pewarisan font dropdown, dan target menu sentuh.

Perubahan terdahulu dalam working tree tetap dipertahankan. JavaScript Manage Article masih dirty dari pekerjaan sebelumnya, tetapi hash-nya tidak berubah pada koreksi ini.

## Memori dan Graphify

`E:/AI/Memories` tidak dapat diperiksa karena drive E: tidak tersedia. Konteks berasal dari bagian Manage Article pada `C:/Users/aruna/.codex/memories/MEMORY.md` dan laporan implementasi 28 Oktober di `project-artifacts/qa/manage-article-toolbar-20261028/completion.md`, lalu diverifikasi terhadap source/runtime terbaru.

Graphify query `manage_coverimage.blade.php` dipakai untuk menemukan referensi source halaman tersebut; output dibatasi dan tidak dijadikan pembuktian menyeluruh. Query path `manage_article.blade.php` ke `public/assets/css/article/manage-article-2026.css` menampilkan `references_stylesheet [EXTRACTED]`. Graph merupakan snapshot sebelum perubahan UI sebelumnya, sehingga hanya dipakai sebagai petunjuk lokasi dan selalu dicocokkan dengan source aktual. Tidak menggunakan relasi `INFERRED`/`AMBIGUOUS` untuk mengambil keputusan. Graph tidak diperbarui sesuai arahan manual pengguna.

## Backup

Backup sebelum perubahan tersimpan pada `project-artifacts/backups/manage-article-menu-fix-20261028/manifest.json`, dengan dua source dan nama bertimestamp `direct_actions_menu_font`. SHA-256 kedua backup original telah diverifikasi. Revisi selama pemeriksaan font memiliki backup tambahan bertimestamp.

`preservation-verification.json` membuktikan dua backup valid, JavaScript/controller/resource tidak berubah, serta HEAD, index, dan graph tetap sama. Tidak ada stage, commit, atau push.

## Pengujian terbaru

Command: `node project-artifacts/tests/manage-article-menu-fix-20261028/browser-qa.mjs` dengan kredensial hanya melalui environment proses.

- Sebelum perubahan, regression gagal karena tombol template belum menjadi aksi langsung. Setelah perubahan awal, assertion font juga menangkap bahwa utility legacy belum diterapkan. Setelah koreksi cascade, seluruh pemeriksaan lolos.
- **10 kasus browser**: 1440, 1024, 768, 390, dan 320 px pada light/dark.
- Ketiga tombol header selalu terlihat, tidak terpotong/bertumpuk, dan tidak menimbulkan horizontal overflow. Pada 1440 px ketiganya sejajar satu baris.
- Font menu dan semua item View article/Delete/Edit sesuai ukuran sel tabel pada setiap ukuran layar; font family sama dengan body template. Menu tidak keluar batas viewport dan Escape menutupnya.
- Category List membuka modal lama dan dapat ditutup; Article Templates memakai route yang benar. Detail/checkbox tetap berfungsi terpisah dan Cancel selection bekerja.
- Runtime errors **0**, Vue warnings **0**, request perubahan artikel **0**. Login dikecualikan dari hitungan penulisan artikel.
- `git diff --check` lolos, dengan peringatan normalisasi CRLF yang sudah ada.

Screenshot: `page-1440-light.png`, `menu-1440-light.png`, `page-390-light.png`, dan versi dark. Hasil terstruktur: `verification.json`.

## Keterbatasan

Pengujian browser memakai Chrome; belum mencakup perangkat fisik, Firefox, atau Safari. Tidak menjalankan ulang suite backend atau seluruh repository untuk perubahan markup/CSS ini. Graphify menunggu perintah pembaruan manual.
