# Relokasi public/build dan public/mockups — Laravel 13 Phoenix — 30 Oktober 2026

## Hasil

Kedua folder dipindahkan ke project-artifacts setelah pemeriksaan source, database, browser, tes baseline, dan backup. File runtime aplikasi tidak diubah. Sebelas file test diperbarui agar tetap membaca fixture mockup dari lokasi baru.

| Folder asal | Fungsi dan kondisi aktual | Lokasi baru |
|---|---|---|
| public/build | Tiga hasil build Vite (100.580 byte). Manifest menunjuk entry app.js dan app.css; app.css kosong menghasilkan JS satu byte. Tidak ditemukan pemanggil @vite/Vite:: atau referensi aset build pada source aplikasi yang diperiksa, database, atau delapan halaman sampel. | project-artifacts/builds/legacy-public-build-20261030 |
| public/mockups | Sebelas HTML rancangan/prototipe dan empat gambar (2.436.160 byte). Dipakai sebagai referensi/test fixture oleh sebelas file tes; tidak dipakai halaman produksi yang diperiksa. | project-artifacts/mockups/legacy-public-mockups-20261030 |

## Hal yang dipertahankan

- Isi seluruh 18 file identik berdasarkan SHA-256 sebelum dan sesudah relokasi. Struktur internal dan empat gambar pendamping mockup tetap bersama foldernya.
- Sebelas test fixture consumer diperbarui dari public/mockups ke project-artifacts/mockups/legacy-public-mockups-20261030. Tidak mengubah kontrak/assertion fitur lain.
- Konfigurasi Vite, package scripts, resources/js/app.js, dan entry source tidak diubah. npm run build dapat membuat public/build kembali karena masih merupakan output default Vite.
- Build File Manager V2 yang aktif tetap berada di public/assets/plugins/filemanager_v2; folder tersebut berbeda dari public/build dan tidak dipindahkan.
- Preview tema produksi tetap di public/assets/images/themes/previews. Halaman Manage Themes memuat empat preview Prism, Aurora, Lucent, Equinox dari lokasi itu.
- Seluruh 32 CSS aktif yang sudah ditata pada pekerjaan sebelumnya tetap identik. Perubahan lama user dan penataan CSS sebelumnya dipertahankan.

## Backup dan pemulihan

- Backup utama: project-artifacts/public-build-mockups-relocation-20261030/backups_20261030_145709.
- Sebanyak 29 file dicadangkan: 18 file relokasi dan 11 consumer tes. Seluruh hash backup cocok.
- recovery-manifest.json mencatat asal, tujuan, hash, consumer sebelum/sesudah, dan lokasi backup.
- relocations.csv berisi daftar 18 file yang dipindahkan.
- Pemulihan harus membandingkan source aktif terhadap hash manifest sebelum menimpa file, agar perubahan baru tidak hilang.

## Hasil pemeriksaan

- Database: 272 kolom teks/JSON diperiksa dalam transaksi READ ONLY lalu rollback; nol referensi ke mockups/, build/assets/, atau dua nama bundle build.
- Tes Node terarah sebelum dan sesudah: 23 lulus, nol gagal; hasil tetap sama.
- Browser sebelum dan sesudah: delapan halaman HTTP 200, nol request ke /build/ dan /mockups/. Login anonymous juga diamati tanpa request kedua folder sebelum relokasi.
- git diff --check lulus. Peringatan normalisasi line ending pada perubahan yang sudah ada tidak merupakan whitespace error.
- Kedua folder asal tidak lagi berada di public. Tidak ada file yang dihapus permanen.

| Halaman | HTTP sebelum | HTTP sesudah |
|---|---:|---:|
| `/dashboard` | 200 | 200 |
| `/awesome_admin/themes` | 200 | 200 |
| `/awesome_admin/config` | 200 | 200 |
| `/awesome_admin/header-navigation` | 200 | 200 |
| `/home` | 200 | 200 |
| `/article` | 200 | 200 |
| `/pagebuilder-elementor/v2.4/create` | 200 | 200 |
| `/admin/file-manager-v2` | 200 | 200 |

## Memori, Graphify, dan source

- Memori: E:/AI/Memories/20260825-laravel13-phoenix-public-resources-views-cleanup-handoff.md mencatat kedua folder pernah dipertahankan pada audit lama. Keputusan sekarang didasarkan pemeriksaan ulang source dan runtime.
- Memori: E:/AI/Memories/extensions/ad_hoc/notes/20260716-165807-laravel13-phoenix-theme-manager-arunika-v2-v3-handoff.md menjelaskan peralihan mockup Theme Manager menjadi halaman produksi.
- Graphify explain vite.config.js dan resources/css/app.css menunjukkan entry build (EXTRACTED). Node mockup yang dijelaskan berupa metadata dengan degree nol; hal tersebut tidak dipakai sendiri sebagai bukti unused.
- Source aktual diperiksa: vite.config.js, public/build/manifest.json sebelum move, resources/js/app.js/bootstrap.js, Awesome_Admin_Themes_Controller.php, seluruh consumer mockup di tests, serta pencarian @vite/Vite::/path build/mockups pada app/resources/routes/config/bootstrap/database/scripts/tests.
- Graphify tidak diperbarui sesuai instruksi manual. Metadata graph masih dapat menunjuk lokasi mockup lama.

## Batas verifikasi

- Folder mockup sekarang merupakan file referensi dan test fixture di luar web root. URL /mockups/... lama tidak tersedia. Mockup yang memakai aset /assets atau /storage tetap memerlukan lingkungan preview/server yang menyediakan aset tersebut; interaksi semua prototipe tidak diuji ulang sebagai halaman publik.
- Snapshot build lama diarsipkan. Tidak menjalankan ulang build karena tidak ada perubahan source bundling; output build baru/future Vite belum diuji dalam task ini.
- Browser diuji lokal pada halaman sampel; bukan bukti seluruh halaman, perangkat, role, integrasi, atau akses URL eksternal langsung.
- Tidak ada staging, commit, push, deployment, atau perubahan konfigurasi build.

## Bukti

- baseline-tests.tap / after-tests.tap: hasil 23 tes terarah.
- runtime-before.json / runtime-after.json: delapan halaman dan preview tema produksi.
- database-check.json: hasil pencarian database baca-saja.
- recovery-manifest.json / relocations.csv / verification-summary.json: hash, pemulihan, dan ringkasan.
