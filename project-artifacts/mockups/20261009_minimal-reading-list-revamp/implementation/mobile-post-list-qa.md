# Minimal Reading List: koreksi post list mobile

Tanggal: 9 Oktober 2026, Asia/Jakarta.

Permintaan: perbaiki UI/UX post list mobile yang sebelumnya memakai thumbnail kecil di kiri dan teks sempit di kanan.

Hasil: pada viewport <=575.98 px, post memakai satu kolom dengan thumbnail selebar daftar, metadata/judul/excerpt/Read more di bawahnya, gap internal 1rem, serta excerpt maksimal tiga baris. Desktop/tablet tetap berdampingan. Font tetap memakai token CMS.

Source yang diubah pada koreksi ini:

- `public/assets/css/article/article-frontend-2026.css`: kedua blok mobile yang relevan diselaraskan; asset media 16:9, background media tetap memakai tinggi dari settings; gap list mobile kembali memakai `--article-reading-list-post-gap`.
- `tests/article-template-presentation.test.mjs`: kontrak mobile menjadi satu kolom.
- `implementation/verify.js`: pengukuran browser untuk posisi/width mobile, desktop/tablet, dan gap custom mobile.

Backup hash-verified: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\20261009_162331-minimal-mobile-post-list\manifest.json`.

Renderer fixture menggunakan Blade, normalizer, dan CSS aktif; dataset lokal dan helper translation terisolasi seperti dijelaskan README implementasi. File HTML sebelumnya dibackup sebelum regenerasi.

RED: `mobile-stacked-red.json` menunjukkan kegagalan layout yang diharapkan pada 390 px sebelum koreksi CSS.

GREEN:

- Node Article/typography: 128 passed, 0 failed; `mobile-stacked-node.log`.
- Browser: 37 passed; `mobile-stacked-results.json`.
- Tidak ada horizontal overflow pada 1440, 1024, 768, 390, 375, 320 px.
- Mobile 390/375/320 memiliki gambar selebar row dan teks di bawahnya.
- Desktop 1440/1024 dan tablet 768 tetap memiliki media dan teks berdampingan.
- Custom gap mobile 24 px, background height 280 px, serta thumbnail frame radius 18 px tetap dihormati.
- Settings lain, tiga model pagination, frame OFF/ON, dan typography CMS melewati check browser yang sama.
- PHP syntax Blade dan git diff --check lulus. PHP business rules tidak diubah pada koreksi mobile ini, sehingga suite PHP tidak diulang.

Screenshot hasil source: `mobile-stacked.png` dan `mobile-stacked-full.png`, diperiksa visual.

Memori E tidak tersedia; konteks pekerjaan sebelumnya dipertahankan dan source aktif diperiksa ulang. Graphify explain `minimal-reading-list.blade.php` menunjukkan lima include EXTRACTED yang masih valid; CSS aktif tetap sumber kebenaran. Graph tidak diperbarui karena instruksi manual.

Tidak ada perubahan data/settings tersimpan, commit/push, atau perubahan layout template lain. Full authenticated Manager Save masih belum dijalankan; verifikasi mobile menggunakan renderer source aktif dan CSS yang disajikan server.
