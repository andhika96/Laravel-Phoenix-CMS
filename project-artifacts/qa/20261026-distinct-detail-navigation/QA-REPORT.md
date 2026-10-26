# Navigasi detail artikel — hasil implementasi

Tanggal:26 Oktober2026. Project:D:/Laragon/www/laravel-13-phoenix.

## Hasil dan scope

Pengguna menyetujui preview26Oktober dan meminta langsung implementasi. Source sekarang memilih navigasi berbeda untuk setiap template:

- Focused Reader:Reading Cards yang sudah ada tetap.
- Editorial Feature:Story Spread, dua panel foto dengan overlay gelap, Next lebih lebar pada desktop; mobile bertumpuk. Hover memperbesar gambar sedikit, menggeser panah dan menampilkan garis aksen.
- Knowledge & TOC:Knowledge Links, dua baris penuh tanpa foto atau kartu; judul, kategori, waktu baca dan arah navigasi. Hover menyorot baris serta menggarisbawahi judul.

Gate display.navigation dan ketersediaan tetangga tetap di partial bersama. Tanpa tetangga tidak ada navigasi; satu tetangga mengisi area tersedia. Cover Story Spread mengutamakan thumb_l, kemudian thumb_s, kemudian placeholder. Judul tetap escaped; URL memakai route yang sama; kategori opsional; estimasi baca minimal1menit.

Tidak menambah dependensi, JavaScript aplikasi, setting atau migration. Font mengikuti skala CMS. CSS/HTML konten CKEditor tidak diubah. Pengaturan tersimpan sama persis sebelum/sesudah dan template aktif tetap focused-reader; tidak menekan Save Template. Helper t() yang sudah ada dapat mendaftarkan label terjemahan baru pada pemakaian pertama. Artikel dan isi konten tidak diubah.

## File source

Dimodifikasi:
1.public/assets/css/article/article-frontend-2026.css —69baris CSS khusus dua navigasi, container queries dan reduced motion.
2.resources/views/article/templates/partials/detail-navigation.blade.php —dispatcher dua gaya baru di dalam gate yang sudah ada.
3.resources/views/article/templates/detail/editorial-feature.blade.php —memilih story-spread.
4.resources/views/article/templates/detail/knowledge-toc.blade.php —memilih knowledge-links.

Dibuat:
5.resources/views/article/templates/partials/detail-story-spread.blade.php.
6.resources/views/article/templates/partials/detail-knowledge-links.blade.php.

Source tambahan dibaca:detail-reading-cards,Focused Reader,ArticleTemplateOptions,PublicArticleQuery,ArticleFrontendController::neighbors,ManageArticleTemplateController,fixture,helper t(),pengaturan filesystem,serta test artikel. Tidak mengubah controller,model,route,normalizer atau kedua file source Focused Reader/Reading Cards. Folder untracked project-artifacts/previews/manage-article-revamp-20261026 berasal dari pekerjaan lain dan dibiarkan.

## Backup dan konteks

Backup sebelum perubahan:project-artifacts/backups/20261026-143251-distinct-detail-navigation/ berisi empat source lama dengan struktur relatif dan source-hashes-before.json. Dua partial baru tidak menimpa file. Koreksi helper QA/plan juga dibackup di folder itu. settings-before.json menyimpan snapshot setting UI tanpa credential.

Memori relevan dicari terfilter di E:/AI/Memories (handoff artikel/template dan Focused Reader). Registry C:/Users/aruna/.codex/memories/MEMORY.md:200–208 dibaca untuk kontrak font CMS dan status layout sebelumnya; kondisi diperiksa ulang terhadap source/runtime. Preview yang disetujui:project-artifacts/mockups/20261026-distinct-detail-navigation/detail-navigation-concepts.html dan PREVIEW-QA.md.

Graphify query `editorial-feature knowledge-toc detail-navigation`,budget650,memakai node kedua detail Blade/dispatcher/normalizer sebagai peta teknis. Graph bertanggal20Oktober belum memuat seluruh navigasi baru; source aktif diperiksa langsung. Tidak menggunakan relasiINFERRED/AMBIGUOUS sebagai bukti. Graph tidak diperbarui sesuai instruksi pengguna.

Implementasi mengikuti rencana singkat di IMPLEMENTATION-PLAN.md,Superpowers TDD/verifikasi dan Ponytail; UI mengikuti preview dengan pedoman UI UX Pro Max/Design Taste Frontend. Dikerjakan inline pada checkout aplikasi lokal; tidak ada commit,push,reset atau pergantian branch.

## Pengujian terbaru

- TDD sebelum source diubah:2test baru gagal sesuai harapan karena template masih memakai Reading Cards dan gambar kecil (php-red.txt). Test kemudian lulus dengan implementasi.
- PHP terakhir:48tests,750assertions lulus (php-verification.txt). Meliputi seluruh suite tests/Feature/Article dan DistinctNavigationTest.php,12kombinasi template/tetangga,gate off,escaping,link tujuan,read time,kategori kosong,cover besar/cadangan/placeholder.
- JavaScript:76tests presentation/manager/fixture lulus (node-verification.txt).
- Browser runtime aktual:3template×6viewport320/390/576/834/1024/1440px,18kasus lulus tanpa horizontal overflow (runtime-verification.json). Story Spread memakai dua panel asimetris saat area>560px dan stack di bawahnya. Knowledge Links tetap dua baris tanpa gambar. Reading Cards tetap tampil seperti sebelumnya.
- Hover/focus-visible2px/reduced motion diperiksa pada ketiga template. Gambar dan panah tidak bergerak saat reduced-motion. Dua template baru diuji dengan hanya satu kartu dan judul sangat panjang tanpa spasi pada320px.
- Navigasi frontend Focused Reader diuji previous lalu next kembali ke artikel QA5059.
- Render artikel nyata pertama5058,tengah59,dan QA5059 menggunakan Blade serta query tetangga controller aktual:6render×2viewport390/1440px,12kasus lulus,termasuk previous-only,next-only dan dua tetangga. Gambar termuat,link sama dengan route tujuan,tidak overflow.4URL tujuan dibuka dan mengembalikanHTTP200 serta artikel detail (saved-navigation-verification.json). Ini export renderer ke konteks browser frontend,bukan mengganti template global sementara.
- Setting UI sebelum/sesudah identik;activeDetail focused-reader (settings-verification.json).
- Regresi CKEditor:artikel QA5059 diekspor ulang pada3template×4viewport,12kasus/0perbedaan pada properti computed style yang dibandingkan;9gambar,tabel merge cells dan todo tetap valid (ckeditor-regression.txt,render-verification.json). content_sha256 tetap f1b3be5fd0c0098f89f717b90ec8951908a57dfd48429f173a67c39acb1b5e7c.
- Kompilasi Blade dan git diff --check lulus. Screenshot desktop/mobile dua navigasi diperiksa visual. Screenshot dengan pointer tetap pada link dapat memperlihatkan state hover.

## Koreksi lingkungan QA yang sudah dituntaskan

Percobaan memaksa seluruh suite memakai SQLite kosong membuat11test render lama gagal karena helper t() membutuhkan tabel language. Environment CLI dibetulkan ke host database lokal yang dipakai suite; test yang membuat/mengubah data tetap mengatur SQLite terpisah sendiri. Final48test lulus; tidak ada source aplikasi yang diubah untuk mengatasi masalah lingkungan ini.

Storage::fake pada test awal membersihkan disk testing bersama dan menghilangkan14SVG tracked. Baseline Git awal menyatakan file tersebut utuh;14file dipulihkan persis dari HEAD ke path yang hilang melalui restore-testing-assets.cjs,kemudian test baru menggunakan disk terisolasi dalam folder QA. Final git status tidak menyisakan perubahan aset testing. Tidak ada file media produksi yang dihapus.

Percobaan membuka export dari about:blank/route sintetik tidak mempunyai konteks jaringan frontend yang cocok untuk aset lokal. Helper QA dibetulkan dengan membuka frontend nyata dahulu lalu memuat export di origin yang sama;12kasus gambar/link lulus. Tidak mengubah CSP,hosts,server,atau browser pengguna. Sesi admin diperiksa ulang melaluiHTTPS setelah HTTP mengarah ke login;setting berhasil diverifikasi. Browser QA memakai pemetaan hostname127.0.0.1 khusus proses pengujian.

## Batas verifikasi

Belum menguji Safari,Firefox atau perangkat fisik. Dua desain baru diuji melalui preview renderer aktual dan render artikel tersimpan;frontend global tetap Focused Reader. Regresi CKEditor membandingkan properti konten tertentu,bukan keseluruhan halaman per piksel. Tidak menyatakan semua fitur/plugin CKEditor di luar build terpasang ikut diuji.
