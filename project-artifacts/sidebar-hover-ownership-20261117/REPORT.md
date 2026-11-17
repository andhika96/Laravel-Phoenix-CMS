# Perbaikan panel hover salah menu

Tanggal: 17 November 2026, WIB.
Project: `D:\Laragon\www\laravel-13-phoenix`.
Permintaan: perbaiki submenu Testing yang muncul ketika pointer berada pada ikon Workflow Editor, Manage Event, atau menu lain, serta periksa tema lain.

## Hasil

Bug berhasil direproduksi pada Aurora. Perbaikan source hanya menyentuh `public/assets/js/themes/arunika_aurora/arunika_aurora.js`: dua kondisi dan satu komentar. Prism, Lucent dan Equinox sudah menggunakan pembatas lookup yang benar; Default dan Calm Green memakai hover CSS. Semua enam tema diuji tanpa mengubah tema global atau data kategori.

Pencarian panel sekarang berhenti pada anchor menu berikutnya. Tooltip hanya ditampilkan ketika menu tidak mempunyai panel submenu. Testing tetap menampilkan submenu miliknya sendiri, sedangkan Workflow Editor dan menu single lain menampilkan nama masing-masing.

## Penyebab dan batas perubahan

`getPopover(currentItem)` menelusuri sibling untuk mencari `.ph-floating-submenu`. Aurora masih memakai `nextEl.tagName === 'a'` sebagai tanda batas menu. DOM HTML mengembalikan tagName `A`, sehingga pembatas itu tidak bekerja. Pencarian dari Manage Articles, Manage Event, Manage Cover Image dan Workflow Editor melewati menu berikutnya sampai menemukan panel Testing.

Fungsi yang sama dipanggil oleh handler mouseenter dan mouseleave. Perbaikan dilakukan pada helper tersebut agar semua caller memperoleh panel yang benar. Kondisi tooltip disamakan dengan tiga tema Arunika lain menjadi `if (tooltip && ! popover)`.

Tidak ada perubahan CSS, renderer, layout, menu database, warna, spacing, posisi badan panel, atau panah. Tidak ada refactor bersama atau dependensi baru. Perubahan sidebar 13 November yang telah dipilih pengguna dipertahankan.

## Memori, Graphify, dan source yang diperiksa

- Memori terfilter: `E:\AI\Memories\20261103_160100-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md`, bagian riwayat sidebar serta isolasi CSS.
- Laporan sesi sebelumnya: `project-artifacts/sidebar-implementation-20261113/REPORT.md`. Pemeriksaan sebelumnya menguji posisi dan tampilan panel Testing, tetapi belum menguji kepemilikan panel untuk setiap menu. Celah ini ditutup oleh test baru.
- Graphify: `graphify explain arunika_aurora.js`, graph lokal `graphify-out/graph.json` bertanggal 3 November 2026. Graph menunjukkan fungsi `getPopover`, hover helper, dan caller dengan relasi `EXTRACTED`. Source aktual setelah commit `1204f66b4` diperiksa kembali; graph bukan bukti bahwa kondisi dalam function sudah benar.
- Empat script `public/assets/js/themes/<arunika_theme>/<arunika_theme>.js`, khususnya `getPopover`, `closeAllOtherPopovers`, handler mouseenter/mouseleave, dan kondisi tooltip.
- `resources/views/themes/arunika_aurora/components/menu.blade.php`, bagian tooltip single menu, anchor, collapse, dan floating submenu.
- `public/assets/css/cms/cms-bootstrap-theme.css`, bagian sidebar untuk memastikan styling yang telah disetujui tidak perlu diubah.
- Helper render dan verifikasi aktif pada `project-artifacts/sidebar-implementation-20261113` dibaca sebagai rujukan cara QA, lalu halaman seluruh tema dirender ulang dari Blade aktual untuk pengujian hari ini.
- `tests/arunika-prism-collapsed-menu-static.test.mjs` dan lima file regresi terkait diperiksa/dijalankan.
- UI UX Pro Max dipakai untuk pemeriksaan prinsip feedback hover. Design Taste dibatasi pada preservasi desain existing. Ponytail dipakai untuk patch kecil pada owning helper. Lokasi skill Superpowers lama tidak tersedia dalam instalasi saat ini; pemeriksaan reproduksi, patch minimal, dan verifikasi dilakukan langsung.

Graphify tidak diperbarui, sesuai instruksi manual pengguna. Tidak ada source atau graph privat yang dikirim ke layanan eksternal.

## Backup

Backup source sebelum perubahan:

`D:\Laragon\www\laravel-13-phoenix\project-artifacts\sidebar-hover-ownership-20261117\backups\20261117_110927_before_fix\arunika_aurora.js`

SHA-256 sebelum perubahan dan backup sama:

`C27D24E76FD0B2B4B6B442CDB55766151042E669EFEC1358EA6E274F6A79152E`

Manifest berada di `backup-manifest.json`. Revisi helper capture dan keluaran QC awal disimpan di `backups/*_capture/`. Backup tidak dipakai sebagai source aplikasi.

## Pengujian perilaku browser

Command:

`node project-artifacts/sidebar-hover-ownership-20261117/verify-hover.mjs before`

Baseline: **392 pemeriksaan, 32 gagal**. Kegagalan hanya Aurora, dalam light dan dark. Pemeriksaan benar-benar mereproduksi lookup dari ikon lain yang mengembalikan Testing dan membuka panel Testing pada ikon yang salah. Lima tema lain lulus pada baseline.

Command setelah patch:

`node project-artifacts/sidebar-hover-ownership-20261117/verify-hover.mjs after`

Hasil terbaru: **392 pemeriksaan, 0 gagal**.

Ruang uji:

- Enam tema: Aurora, Lucent, Prism, Equinox, Default, Calm Green.
- Dua mode: light dan dark.
- Setiap anchor menu utama diuji; menu single harus memiliki tooltip/panel namanya sendiri dan tidak boleh membuka submenu Testing.
- Pada empat Arunika, hasil `getPopover` juga diperiksa langsung terhadap owner menu yang sedang dihover.
- Transisi cepat Testing → Workflow Editor → Manage Event → Manage Articles → Testing.
- Perpindahan pointer dari ikon Testing ke baris submenu harus mempertahankan panel.
- Setelah pointer meninggalkan navigasi, seluruh panel harus menutup.

Fixture adalah Account Settings dari Blade/layout/renderer aktual, dihasilkan ulang pada proses PHP terpisah tiap tema. Aset CSS/JS dibaca dari source aktif `public` melalui route Playwright dengan pembatasan path. Pengaturan tema dan data menu global tidak diubah. Untuk Default/Calm Green, collapsed state diatur pada class DOM karena test ini memeriksa hover CSS; tombol collapse legacy end-to-end tidak termasuk hasil ini.

Tidak ada injeksi CSS untuk meluluskan pemeriksaan perilaku. Capture screenshot menunggu opacity panel mencapai keadaan akhir agar gambar tidak menangkap frame awal fade-in. File `before-results.json`, `after-results.json`, dan log masing-masing menyimpan rincian per menu.

## Halaman aplikasi langsung

Login admin dilakukan pada browser pengujian terpisah menggunakan kredensial yang sudah diberikan pengguna. `/profile` dibuka langsung tanpa intercept response atau injeksi CSS. Tema yang aktif saat verifikasi adalah **Prism**, bukan Aurora; pengaturan ini tidak diubah.

Urutan hover Testing, Workflow Editor, Manage Event, Manage Cover Image, Manage Articles, lalu Testing lulus. Panel Testing hanya tampil pada Testing. Empat menu single menampilkan tooltip namanya sendiri. Transfer pointer ke submenu dan penutupan setelah meninggalkan navigasi juga lulus.

Hasil tersimpan di `live-verification.txt`, `live-workflow.png`, `live-event.png`, dan `live-testing.png`. Hasil Aurora berasal dari browser yang menjalankan render Blade aktual, sedangkan bukti halaman `/profile` langsung adalah Prism. Keduanya dibedakan agar tidak mengklaim tema global pernah dialihkan.

## Sintaks dan regresi lain

- `node --check public/assets/js/themes/arunika_aurora/arunika_aurora.js`: lulus.
- `git diff --check`: lulus.
- Lima file regresi terkait menghasilkan **5 tes lulus, 1 tes gagal**. Tes yang gagal adalah `tests/arunika-aurora-sidebar-static.test.mjs`, pada assertion lama bahwa layout memiliki `id="sidebar-toggle-icon"`. Layout aktif memakai struktur toggle lain dan file layout tidak diubah oleh patch ini.
- Kegagalan yang sama dikonfirmasi ketika test dijalankan terhadap JavaScript Aurora asli dari `git show HEAD:...`, dengan layout dan CSS yang tidak berubah. Bukti di `baseline-static-check.log`. Ini kegagalan existing, bukan kegagalan hover yang diperbaiki, dan tidak ditutupi dengan perubahan layout di luar scope.

## Artifact dan status

Semua hasil berada di folder laporan ini: helper render, `verify-hover.mjs`, `live-hover.js`, hasil JSON/log, screenshot, backup, dan manifest. Screenshot sebelum/sesudah Workflow Editor serta Manage Event diperiksa secara visual.

Status repository awal bersih. Status akhir hanya satu file source Aurora berubah dan folder artifact baru. Tidak ada reset, penghapusan, staging, commit, push, atau deployment. Tidak ada perubahan data kategori maupun setting tema.

## Batas yang masih ada

Sudah diverifikasi di Chromium lokal. Belum diverifikasi pada Firefox/Opera, perangkat fisik, produksi, menu management v1, atau seluruh halaman CMS. Suite seluruh aplikasi tidak dijalankan. Satu tes statis legacy toggle yang dijelaskan di atas masih gagal.

Jika browser pengguna masih memegang script lama pada halaman yang sudah terbuka, reload halaman dengan Ctrl+F5 untuk memuat source yang telah diperbaiki. Update Graphify tetap menunggu perintah manual.
