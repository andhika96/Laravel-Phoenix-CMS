# Preview — navigasi berbeda untuk tiga template detail

Tanggal:26 Oktober2026. Scope:preview untuk ditinjau pengguna, belum implementasi aplikasi.

## Usulan desain

- Focused Reader:Reading Cards tetap; dua kartu border tipis, thumbnail kecil, metadata, judul dan panah bundar. Hover mengubah border/latar/judul/lingkaran panah.
- Editorial Feature:Story Spread; dua bidang foto asimetris, area Next lebih lebar, judul overlay dengan lapisan gelap. Hover memperbesar foto sedikit dan menampilkan garis aksen bawah. Mobile menumpuk panel dengan ukuran judul yang sama.
- Knowledge & TOC:Knowledge Links; dua tautan penuh bertumpuk, tanpa foto dan kotak kartu, label arah, judul, kategori/waktu baca dan pemisah horizontal. Hover menggarisbawahi judul dan menyorot baris.

Foto dan contoh judul berasal dari fixture artikel yang sudah ada. Estimasi6/8menit adalah data contoh visual. Tidak ada asumsi seri/bab atau urutan konten baru. Klik hanya memperlihatkan nama tautan dalam preview.

Kontrol preview:pilihan desktop/tablet/mobile, contoh hover Next, serta dua/satu tetangga. Carousel bawaan memisahkan desain sesuai template. State kontrol bersifat lokal.

## Konteks yang diperiksa

Memori resmi E:/AI/Memories/20261020-delta-revamp-focused-reader-01a14ef8-cc2f-78d3-b204-f92352ffbb78.md ditemukan lewat pencarian terfilter. Registry C:/Users/aruna/.codex/memories/MEMORY.md bagian186–213 dan rollout_summaries/2026-10-18T12-24-50-eKCs-article_detail_editorial_feature_knowledge_toc_layout_verifi.md dibaca sebagai konteks:layout/tipografi CMS dipertahankan, ketiga template sebelumnya memakai Reading Cards bersama. Kondisi itu dikonfirmasi ulang pada source26Oktober.

Graphify query `detail-reading-cards detail-navigation editorial-feature knowledge-toc`, budget650. Graph bertanggal20Oktober, dipakai hanya sebagai peta menuju kedua Blade detail/normalizer. Node navigasi baru tidak terpetakan dengan baik pada graph tersebut; actual source detail-navigation dan detail-reading-cards diperiksa langsung. Tidak mengklaim kelengkapan graph atau memakai relasiINFERRED sebagai bukti. Graph tidak diperbarui.

Source dibaca:resources/views/article/templates/partials/detail-navigation.blade.php;detail-reading-cards.blade.php;include readingCards pada ketiga detail Blade;app/Support/Article/ArticleTemplatePreviewFixture.php. Mockup21Oktober dibaca sebagai referensi Reading Cards. Tidak ada source aplikasi yang diedit atau setting/database yang disimpan.

## Pemeriksaan preview

- Browser:Edge Chromium headless, pembungkus resmi visualize lokal.
-12kombinasi:3desain×320/390/736/1024px.Tidak ada horizontal overflow pada frame.
-Perpindahan desain diuji melalui pemilih carousel asli; dua kartu/panel menjadi satu kolom pada lebar area≤560px.Knowledge Links selalu dua baris penuh.
-Kontrol ukuran area:desktop880px,tablet650px,mobile390px pada viewport1024px.
-Hover,fokus keyboard,klik lokal,reduced-motion dan hanyaPrevious/hanyaNext diuji pada ketiga desain.
-Gambar tertanam berhasil decode; ukuran fragmen162174bytes, di bawah1MiB.Tidak ada import/API/network data dari fragmen.
-Screenshot desktop,mobile390px,hover dan dark Editorial diperiksa visual.Tidak ada errorJavaScript.
-Test awal inspeksi layout memakai toggle DOM; kemudian pengujian diulang melalui kontrol carousel asli dan lulus.Percobaan helper test memilih item sebelum membuka pemilih mengalami timeout; helper test diperbaiki, tanpa perubahan UI preview.
-git diff --name-only kosong setelah selesai; perubahan hanya folder mockup baru ini.Status awal repository bersih.

File bukti:verify.cjs,verification.json,screenshotPNG dalam folder ini.Koreksi helper QA dibackup ke project-artifacts/backups/20261026-142308-navigation-preview-qc/.

## Batas

Ini prototype terpisah, bukan renderer Laravel atau perubahan frontend aktif.Belum menguji Safari/Firefox/perangkat fisik.Kontrol Tweak opsional hanya hadir saat host mendukungnya; handler state dan perubahan ukuran diuji pada pembungkus preview.Implementasi aplikasi menunggu persetujuan pengguna terhadap desain.
