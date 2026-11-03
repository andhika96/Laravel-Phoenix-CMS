# Preview revisi mobile Manage Article — 3 November 2026

## Scope dan hasil

Preview saja; tidak diterapkan ke source CMS. Dua alternatif interaktif berada di `project-artifacts/previews/manage-article-mobile-20261103/manage-article-mobile.html`:

- **Judul utama**: judul mendapatkan lebar penuh, kategori/author di bawah judul, status/tanggal di kiri bawah, kelompok detail → checkbox → menu di kanan bawah. Tidak ada baris kontrol kosong di atas judul.
- **Kontrol di kiri**: detail dan checkbox berada di depan, metadata dan judul di sebelah kanan. Mempertahankan pola kontrol berurutan dan memberikan pembanding terhadap pendekatan title-first.

Kedua alternatif menampilkan ketiga aksi header secara langsung. Ikon serta teks memakai inline-flex dengan alignment center, ukuran sentuh minimal 44px, dan label dapat membungkus tanpa bergeser ke atas. Warna ungu mengikuti referensi pengguna; Publish/Pending memakai warna semantic yang berbeda. Appearance terang/gelap mengikuti host.

Konten menggunakan empat artikel contoh. Search, status, sort, kategori, detail, selection, menu, serta feedback tombol bekerja lokal. Simulasi loading berada di tengah area hasil. Tidak ada API CMS atau mutasi artikel dari preview.

## Konteks dan source yang dibaca

Primary memory E disaring melalui nama file Article; kandidat yang tersedia membahas template lama. Konteks preferensi terbaru diambil dari registry C `MEMORY.md:280–287`: urutan detail/checkbox, preview tanpa mutasi, dan penjagaan semantic palette.

Source aktual yang relevan dibaca secara terbatas: aturan header dan mobile grid dalam `public/assets/css/article/manage-article-2026.css`, serta aturan ukuran font/control Lucent mobile. `min-height:44px` pada aksi header menetapkan tinggi minimum tetapi tidak menambahkan alignment vertikal untuk elemen link. Preview menunjukkan perataan ikon/teks bersama-sama; tidak mengubah aturan aktif.

Graphify explain sebelumnya menunjukkan stylesheet Manage Article sebagai metadata dengan relasi `references_stylesheet [EXTRACTED]` dari Blade L8. Graphify dipakai sebagai peta lokasi, bukan bukti visual runtime. Tidak ada graph yang diperbarui.

Digunakan pedoman UI UX Pro Max untuk mobile table handling/target sentuh, pendekatan audit/preserve Design Taste, dan kontrak preview inline Visualize 1.0.66. Preview baru tidak mengubah desain atau file preview lama.

## Pengujian

- Chrome: 16 kombinasi layout lulus. Firefox: 16 kombinasi layout lulus. Masing-masing mencakup dua alternatif × lebar 320/390/414/720 × appearance terang/gelap.
- Ketiga label dan ikon header terukur di tengah; deviasi maksimum center label/icon 0px pada pemeriksaan Chrome. Semua aksi header minimal 44px, label tidak terpotong, tidak ada elemen keluar dari lebar produk.
- Gap tombol detail ke checkbox yang terlihat tetap 24px di kedua desain. Target klik dan label selection tetap lebih besar daripada glyph checkbox.
- Kedua alternatif diuji pada masing-masing browser: buka/tutup detail melalui judul dan metadata, checkbox tanpa membuka detail, cancel selection, Enter/Space, search, status Pending, status Draft/empty/reset, kategori, sorting, menu/Escape, feedback Add Post, serta pemusatan loading.
- Error JavaScript 0 dan request ke domain CMS 0 selama pengujian preview. Screenshot terang/gelap diperiksa, termasuk Firefox pada 320px.
- Echo state dari host awalnya merender ulang row sehingga fokus keyboard hilang. Echo snapshot yang sama kini diabaikan; probe membuktikan fokus tetap pada judul setelah Enter/Space dan fungsi disclosure berjalan.
- Percobaan baseline halaman aktif tidak selesai karena selector submit form login tidak cocok dengan form saat ini. Tidak ada klaim pengukuran posisi tombol CMS live yang diperoleh dari percobaan tersebut, dan tidak ada perubahan pada auth untuk mengatasi test harness itu.

Hasil: `verification.json`, `verification-firefox.json`, `preview-qa.log`, `preview-firefox.log`, `preservation-verification.json`, screenshot dalam folder ini. `rendered.html` hanya wrapper untuk QC; fragment sumber tetap file editable untuk preview inline.

## Backup dan preservasi

Revisi fragment dan script pengujian dibackup sebelum perubahan ke `project-artifacts/backups/manage-article-mobile-preview-20261103`. Source aktif tidak diubah sehingga tidak diperlukan backup source aplikasi baru. Hash CSS, Blade, Vue Manage Article dan graph cocok dengan `before-source.json`; HEAD tetap sama. Perubahan Appearance/Auth lain yang sudah ada dipertahankan. Tidak ada stage, commit, push, dependency baru, atau penulisan memori.

## Batas dan langkah berikutnya

Ini simulasi desain, bukan integrasi Vue/backend baru. Safari, perangkat fisik, dan runtime CMS final belum diuji untuk rancangan ini. Tunggu pilihan/persetujuan desain pengguna sebelum implementasi, lalu buat backup source dan lakukan QA runtime pada implementasi tersebut. Graphify tetap menunggu perintah manual.
