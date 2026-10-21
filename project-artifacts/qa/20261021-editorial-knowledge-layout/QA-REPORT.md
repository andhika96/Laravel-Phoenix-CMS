# Editorial Feature dan Knowledge & TOC — perapihan detail

Tanggal: 21 Oktober 2026. Project: D:/Laragon/www/laravel-13-phoenix.

## Hasil

Perapihan source sudah diterapkan pada dua renderer detail yang dipakai bersama oleh frontend dan preview. Setting tersimpan tidak diubah; hasil perbandingan draft setelah reload identik dengan snapshot sebelum pekerjaan. Template aktif tetap Focused Reader. Tidak ada perubahan konten artikel, database, route, font CMS, atau stylesheet konten CKEditor.

Editorial Feature: cover/hero maksimal1200px, kolom baca maksimal880px, tombol kembali di atas hero, urutan kategori/judul/ringkasan/metadata, spasi konsisten. Desktop mempertahankan teks di atas cover dengan lapisan gelap; mobile menempatkan header pada panel gelap di bawah gambar. Teks panjang bisa membungkus. Cover diprioritaskan pemuatannya.

Knowledge & TOC: shell maksimal1260px, kolom baca maksimal880px dan sidebar260px dengan jarak40px. Sidebar sticky pada layar mulai1200px, pindah di atas konten di bawah1200px, bisa dilipat dengan mouse/keyboard. Daftar isi panjang mempunyai batas tinggi dan scroll sendiri. Artikel tanpa heading tidak menyisakan kolom TOC kosong. ID heading yang sudah ada dipertahankan tanpa atribut id ganda; heading kosong dilewati; heading tanpa ID tetap diberi anchor. Link TOC sekarang dapat digunakan pada preview fixture, sedangkan link keluar preview tetap dicegah.

Kedua template menggunakan partial Reading Cards yang sudah ada. CSS kartu dibuat berlaku pada seluruh detail; kontrol Focused Reader tetap berlaku. Opsi reading time yang sudah ada kini juga dihormati pada header kedua template. Tidak menambah dependensi atau setting baru.

## Audit dan konteks

Mode desain preserve: mempertahankan cover-led Editorial Feature, struktur knowledge/sidebar, label/URL artikel, aksen tema melalui --article-accent, font SiteTypography dan skala responsive CMS. Screenshot before-* menyimpan baseline. Masalah yang ditangani: hero terlalu melebar, posisi backlink sesudah cover, hierarki metadata, celah sidebar besar, TOC kurang jelas/interaktif di preview, dan navigasi detail lama.

Memori yang digunakan:
- E:/AI/Memories/20260825_232054_laravel13_phoenix_manage_event_article_template_options_handoff.md (renderer bersama, normalizer, shell dan scope detail).
- C:/Users/aruna/.codex/memories/MEMORY.md:149–158 (hierarki/spacing, typography CMS, membedakan preview dengan template aktif). Seluruh kondisi aktual diperiksa ulang.

Graphify: graphify-out/graph.json bertanggal20 Oktober; query `editorial-feature knowledge-toc detail-navigation`, budget1000. Node yang digunakan: kedua Blade detail, detail-navigation, dan ArticleTemplateOptions::detail. Graph dipakai untuk peta file saja dan kemudian diverifikasi ke source aktif; tidak memakai relasi INFERRED/AMBIGUOUS sebagai bukti. Graph tidak diperbarui sesuai instruksi update manual.

Skill: UI UX Pro Max, Design Taste Frontend, Graphify, Ponytail dan pedoman Karpathy. Implementasi memakai Blade/CSS/Font Awesome yang sudah ada.

## Source dibaca dan dimodifikasi

1. public/assets/css/article/article-frontend-2026.css — layout dua template dan cakupan Reading Cards bersama.
2. resources/views/article/templates/detail/editorial-feature.blade.php — backlink, cover, kartu navigasi.
3. resources/views/article/templates/detail/knowledge-toc.blade.php — TOC native details, anchor, empty-outline layout dan navigasi.
4. resources/views/article/templates/partials/detail-header-copy.blade.php — urutan header dan reading time pada cabang non-Focused Reader.
5. resources/views/manage_article/templates/preview.blade.php — izinkan anchor lokal pada fixture.

Source tambahan yang dibaca: ArticleTemplateOptions::detail, shared detail-navigation/detail-reading-cards, template-shell-attributes, article/detail wrapper, CSS konten CKEditor, Vue manager, fixture dan test artikel. Pencarian menunjukkan tidak ada JS khusus detail yang perlu diubah. Repository bersih saat pekerjaan dimulai; tidak ada reset/commit/push.

## Backup

Folder project-artifacts/backups/20261021-105207-editorial-knowledge-layout/ memuat lima file source di atas dengan struktur relatif sebelum edit. Snapshot setting read-only berada di settings-before.json dalam folder QA ini. File pengujian artifact juga dibackup sebelum koreksi fixture/wait. Tidak ada credential disimpan dalam artifact.

## Verifikasi

- PHP final: 49 tests,724 assertions lulus. Meliputi suite artikel, pilihan display/header, kombinasi dua/satu/tanpa neighbor, reading time, ID existing/encoded/data-id, dan artikel tanpa heading. Database test SQLite terpisah. Lihat php-tests-final.txt dan DetailLayoutTest.php.
- Node:76 tests lulus untuk presentation/manager/preview fixture. Satu test tambahan lama article-theme-link-isolation gagal di assertion warna pagination archive. Diulang terhadap CSS backup sebelum perubahan dan gagal dengan pesan identik (node-theme-baseline.txt). Penyebab assertion membaca rule terakhir border-width saja, bukan gabungan deklarasi CSS; tidak diubah karena di luar scope detail. Lihat node-tests.txt.
- Preview guard self-check:4 kasus lulus, anchor TOC lokal diizinkan dan navigasi keluar tetap dicegah. Lihat preview-guard-tests.txt.
- Runtime Edge Chromium headless:3 template ×6 viewport (320,390,576,834,1024,1440px),18 kasus lulus. Tidak ada horizontal overflow; gambar termuat; Reading Cards dua kolom kecuali mobile di bawah576px. Ketiga template terukur880px pada desktop1440px. Lihat runtime-verification.json.
- Hover, focus-visible outline2px dan reduced motion kartu diuji pada ketiga template. Toggle TOC memakai mouse dan Enter lulus; link TOC preview memindah hash dan scroll menuju heading. Pengujian menunggu scroll smooth selesai.
- Stress mobile320px: judul header sangat panjang dan judul kartu panjang tanpa spasi pada dua template, tanpa overflow. Artikel QA5059 memberi15 link TOC; sidebar terukur top24px saat scroll1800px, tinggi826px dalam viewport900px. Lihat stress-verification.json.
- Setting sebelum/sesudah identik; active detail focused-reader. Lihat settings-verification.json.
- Regresi CKEditor: export ulang ketiga Blade menggunakan artikel QA5059,12 kombinasi template/viewport,0 perbedaan computed style pada properti yang diuji.9 gambar termuat, tabel merge cell dan todo valid. Lihat ckeditor-regression.txt dan render-verification.json. Tidak membandingkan auto-margin yang bergantung lebar kolom atau wrapper seleksi inline image editor.
- Kompilasi Blade dan git diff --check lulus. Screenshot desktop kedua template serta mobile390px diperiksa visual; screenshot tablet/card disimpan sebagai bukti tambahan.

Runtime lokal dibuka langsung ke127.0.0.1 melalui pemetaan hostname khusus browser pengujian, karena resolver browser awal gagal. Tidak mengubah hosts atau konfigurasi server pengguna.

## Batas

Belum diuji di Safari/Firefox/perangkat fisik. Template publik aktif tidak diganti bergantian demi pengujian; kedua template diperiksa melalui preview renderer aktual dan export artikel tersimpan dari Blade yang sama. Tidak mengklaim kesamaan100% seluruh halaman per piksel. Test pagination archive yang sudah gagal sebelum perubahan tetap tercatat dan tidak ditutupi.
