# Rekomendasi preview sidebar Lucent

Tanggal: 13 November 2026, WIB.
Workspace: `D:\Laragon\www\laravel-13-phoenix`.
Scope: rekomendasi dan preview visual lanjutan; belum implementasi aplikasi.

## Arah desain

Perbaikan bertahap pada navigasi CMS admin, mempertahankan warna, font, ikon, avatar, kategori dan menu dari tema Lucent aktif. Posisi badan panel hover tetap di tepi kanan rail, dengan panah menjorok ke kiri sesuai koreksi pengguna sebelumnya.

Rekomendasi yang dibandingkan:

1. Preview sebelumnya: judul kategori uppercase dan menu induk terbuka memakai highlight.
2. Rekomendasi: label kategori mengikuti kapitalisasi data dan memiliki garis horizontal tipis; submenu memiliki garis pandu; halaman aktif mendapat highlight dan indikator 3px; menu induk yang hanya terbuka tetap netral; area utilitas dibatasi divider.
3. Saat sidebar dilipat: ikon menu induk memiliki indikator jika salah satu submenu dipilih, dengan penanda yang sama pada item dalam panel hover.

Dipilih satu rekomendasi yang dekat dengan tema yang sudah disukai pengguna. Tidak ditambahkan fitur pencarian, pin, badge angka, kategori baru, penggantian font/ikon, dependensi, atau framework.

`Submenu Test 1` disimulasikan sebagai halaman aktif untuk menunjukkan desain. Ini perubahan class dan atribut pada DOM preview, bukan hasil navigasi atau implementasi deteksi route.

## Memori dan peta teknis

- Memori terfilter: `E:\AI\Memories\20261103_160100-laravel13-phoenix-css-auth-sidebar-tailwind-handoff.md`, khususnya koreksi rule warna navigasi dan isolasi tema. Pengujian lama tidak dipakai sebagai bukti pengujian terbaru.
- Catatan preview sesi ini: `project-artifacts/sidebar-category-preview-20261113/REPORT.md`.
- Graphify lokal: `graphify-out/graph.json`.
- Query baca saja: `graphify explain public/assets/css/themes/arunika_lucent/arunika_lucent.css`.
- Relasi stylesheet ke layout CMS berlabel `EXTRACTED`; source aktif dibaca kembali. Metadata consumer auth lama tidak dijadikan bukti keadaan auth sekarang. Graph tidak diubah atau diperbarui.

## Source aktif dan artifact yang dibaca

- `public/assets/css/themes/arunika_lucent/arunika_lucent.css`: token Lucent, kategori, row navigasi, hover/focus, expanded state, submenu, footer.
- `public/assets/js/themes/arunika_lucent/arunika_lucent.js`: `syncSidebarActiveLinks()` dan selector navigasi aktif. Fungsi saat ini beroperasi pada `.ph-sidebar .list-group-item`; contoh penanda submenu dalam rekomendasi belum terhubung dengan fungsi ini.
- `resources/views/themes/arunika_lucent/cms/cms_layout.blade.php`: pemanggilan menu dinamis dan utilitas footer.
- `project-artifacts/sidebar-category-preview-20261113/proposal.css` dan `render-preview.js`: fondasi preview yang telah disukai.
- Status dan diff Git diperiksa sebelum serta sesudah pekerjaan.

## Referensi UI dan skill

- UI UX Pro Max: query lokal `navigation hierarchy current location sidebar active state`, domain `ux`, menemukan pedoman penanda lokasi aktif.
- Design Taste Frontend: prinsip preservasi identitas dan audit existing dipakai secara terbatas. Aturan marketing/landing page tidak diterapkan pada CMS admin.
- Superpowers Brainstorming dipakai untuk tahap usulan, Ponytail untuk menjaga scope dan memakai aset existing, serta verifikasi sebelum pelaporan.
- Panduan resmi Fluent 2 Nav, dibaca 13 November 2026: https://fluent2.microsoft.design/components/web/react/core/nav/usage/ . Bagian Selection menjelaskan indikator halaman aktif dan penanda induk saat anak dipilih; bagian Grouping dan Icons menjelaskan hierarki. Ini referensi prinsip, bukan adopsi komponen Fluent. Halaman itu juga menyatakan Nav Fluent tidak mendukung icon-only; pola icon-only dalam preview tetap berasal dari perilaku Lucent yang diminta pengguna.
- Panduan resmi Carbon left panel, dibaca 13 November 2026: https://www.carbondesignsystem.com/building-blocks/core/components/ui-shell-left-panel/guidelines . Bagian Behavior menjelaskan chevron serta ekspansi submenu. Tidak ada paket Carbon yang dipasang.

Rekomendasi ini keputusan desain untuk konteks proyek, bukan klaim bahwa ada satu tren UI terbaru yang wajib diikuti.

## File baru dan backup

Seluruh hasil berada di folder laporan ini:

- `refined.css`: alternatif styling di atas proposal sebelumnya.
- `capture.js`: merender perbandingan memakai DOM aplikasi lokal dan menguji metrik visual.
- `01-previous.png`, `02-recommended.png`, `03-recommended-collapsed.png`: screenshot keadaan yang dibandingkan.
- `preview.html`: galeri statis yang bisa dibuka lokal.
- `gallery-render.js`: helper galeri dengan gambar lokal sebagai data URL, tanpa layanan eksternal.
- `comparison.png`: gambar untuk ditampilkan dalam chat.
- `verification-output.txt`: hasil self-check terbaru.

Tidak ada source aplikasi atau preview sebelumnya yang dimodifikasi. File baru yang direvisi selama pembuatan preview dibackup terlebih dahulu di `backups/*_capture_refine/` dan `backups/*_stable_visual_states/`. Berkas backup dan artifact tidak dimasukkan ke Graphify.

## Verifikasi terbaru

Runtime lokal Chromium, viewport aplikasi 1440×800, halaman `/account`, dengan CSS preview sementara di sesi browser.

Sebelas self-check lulus:

1. Menu induk terbuka netral, sementara submenu terpilih memiliki highlight.
2. Penanda submenu aktif berukuran 3px.
3. Kapitalisasi kategori mengikuti data.
4. Garis horizontal kategori setinggi 1px.
5. Garis pandu submenu selebar 1px.
6. Badan panel sejajar tepi kanan sidebar.
7. Panah tetap tampil dan menjorok ke kiri.
8. Ikon induk menunjukkan contoh anak yang terpilih saat sidebar dilipat.
9. Jarak ikon/teks tetap 12px.
10. Kategori saat dilipat tetap berupa garis 1px.
11. Tidak ada overflow horizontal halaman.

Pengukuran panel: sidebar kanan 76px, panel kiri 76px. Screenshot dibandingkan secara visual dan galeri berhasil dirender dengan tiga gambar.

Transisi dinonaktifkan hanya dalam capture supaya gambar menunjukkan keadaan akhir styling, bukan frame animasi. Reload setelah capture menghapus semua override dan state contoh. Tidak dilakukan Save pengaturan, navigasi ke submenu, atau perubahan database.

`git diff --exit-code -- resources public app tests package.json` exit 0. Status Git hanya menunjukkan dua folder artifact preview yang belum ditrack, termasuk preview sebelumnya. Tidak ada staging, commit, atau deployment.

## Batas dan keputusan berikutnya

Belum diverifikasi: penentuan aktif berdasarkan URL submenu sungguhan, keyboard/screen reader, mobile, Firefox, dark mode, zoom tinggi, data aplikasi multi-kategori, dan produksi. Suite aplikasi tidak dijalankan karena source aplikasi tidak berubah. Self-check ini menguji preview, bukan membuktikan implementasi fitur.

Pengguna dapat memilih preview sebelumnya atau alternatif yang direkomendasikan. Implementasi permanen tetap menunggu instruksi pengguna, dengan backup source dan pengujian runtime sesuai scope yang dipilih. Update Graphify tetap manual.
