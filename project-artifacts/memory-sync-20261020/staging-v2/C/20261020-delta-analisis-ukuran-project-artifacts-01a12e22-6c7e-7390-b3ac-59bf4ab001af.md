# Analisis ukuran project-artifacts

Tanggal penyimpanan: 20 Oktober 2026 (WIB)
Project: Laravel 13 CMS BUM
Project saat chat: D:\Laragon\www\laravel-13-cms-bum
Chat ID: 01a12e22-6c7e-7390-b3ac-59bf4ab001af
Jenis sumber: codex-local
Rentang chat: 2026-10-12 10:22:50 WIB sampai 2026-10-12 10:41:55 WIB
Tindakan: new
Evidence SHA-256: d954002230332995f994e7ff3a4d9e464f97b39feb52846b3d1e1de2a8b46bca

## Tujuan dan konteks awal

- ChatGPT ini di folder project-artifacts kenapa besar sekali totalnya sampai 8-9 GB apa yang membuat sampai besar sekali seperti itu ukurannya dan bisakah dioptimize?
- Data Dump dan backup database MariaDB hapus dulu deh

## Keputusan dan implementasi historis

Laporan di bawah merupakan riwayat pekerjaan, bukan pengujian runtime terbaru pada tanggal penyimpanan. Checkpoint terbaru mengungguli pending dari laporan lebih lama.

- Artifact Graphify terkumpul sampai **816,73 MiB**. Backup lama bisa dikompres sambil mempertahankan manifest, laporan, dan checkpoint yang diperlukan. JSON hasil ekstraksi workbook juga layak dikompres jika analisisnya sudah selesai. Penghematan pada kelompok ini belum saya ukur melalui uji kompresi.
- Belum ada cleanup, perubahan source, backup baru, atau pembaruan Graphify.** Verifikasi yang selesai adalah pengukuran ukuran, pemeriksaan source, perbandingan SHA-256, dan uji kompresi/dekompresi; build Android dan aplikasi tidak dijalankan ulang.
- Status Git tetap sama; perubahan lama Anda dipertahankan.
- Source aplikasi dan Graphify tidak diubah; pengujian runtime aplikasi tidak dijalankan karena pekerjaan ini hanya menghapus artifact yang Anda setujui.
- Untuk mendapat ruang dengan perubahan paling sedikit, saya akan memprioritaskan **pemeriksaan retensi binlog MySQL, database pengujian Pokok, lalu cleanup artifact build Android**. Game memang paling besar, tetapi pengurangan ukurannya biasanya berarti memindahkan atau menghapus instalasi melalui Steam/launcher.
- Belum ada cleanup tambahan, perubahan source, atau pembaruan Graphify; hanya dua laporan audit yang dibuat.** Pemeriksaan koneksi database, kebutuhan recovery, dan penggunaan artifact belum dilakukan.

## Checkpoint substantif terakhir

Tanggal checkpoint: 2026-10-12 10:41:09 WIB

**Sudah saya cek dengan scan metadata utama selama 34 detik.** Sisa ruang D: saat ini **55,73 GiB**, sesuai angka **55,7 GB** pada screenshot. Dari kapasitas sekitar **195,31 GiB**, ruang yang terpakai sekitar **139,58 GiB**.

**Penyumbang terbesarnya adalah game, lingkungan pengembangan Laragon, dan backup.** Ada peluang optimasi yang cukup berarti tanpa harus menghapus game, tetapi sebagian kandidat perlu diperiksa penggunaannya terlebih dahulu.

| Folder | Ukuran terukur | Penyumbang utama |
|---|---:|---|
| `D:\SteamLibrary` | **51,03 GiB** | Hampir seluruhnya **Age of Empires II: Definitive Edition** |
| `D:\Laragon` | **47,49 GiB** | Project web, database MySQL, dan perangkat server |
| `D:\Games` | **15,59 GiB** | **Point Blank sekitar 14,48 GiB**, ditambah game lainnya |
| `D:\Backup` | **8,66 GiB** | Backup uploaded file web, dalam folder `New MG` dan `Old MG` |
| `D:\Arunika Map Studio` | **4,00 GiB** | Terutama artifact paket aplikasi, backup, dan browser pengujian |
| `D:\Files` | **3,28 GiB** | Backup website lama dan file MBTiles |
| `D:\Application` | **2,40 GiB** | Hampir seluruhnya instalasi QGIS |
| `D:\w` | **1,86 GiB** | Dua folder project: `pba` dan `pbimgurl` |

Ukuran folder pada tabel berasal dari panjang file yang dapat dibaca. Total yang terukur sekitar **134,54 GiB**; selisih sekitar **5,04 GiB** terhadap penggunaan partisi belum dirinci karena ada direktori terlindungi, junction yang dilewati, serta perbedaan ukuran file dengan alokasi filesystem.

**Bagian Laragon yang paling besar sudah saya rinci:**

| Bagian | Ukuran | Temuan |
|---|---:|---|
| Seluruh project di `D:\Laragon\www` | **23,21 GiB** | CMS BUM paling besar, sekitar **11,04 GiB** |
| Data MySQL di `D:\Laragon\data\mysql-8` | **16,26 GiB** | Database aplikasi, database pengujian, dan binary log |
| Perangkat di `D:\Laragon\bin` | **7,56 GiB** | Beberapa versi MySQL, PHP, Apache, dan PostgreSQL |

**Prioritas optimasi yang saya sarankan adalah sebagai berikut.**

1. **Binary log MySQL — total 6,14 GiB.**  
   Ada **40 file `binlog.*`**; beberapa masing-masing sekitar 1 GiB. Ini salah satu kandidat terbesar. Cleanup harus dilakukan melalui MySQL setelah memastikan kebutuhan recovery, replication, dan retensinya. **Jangan menghapus file binlog melalui Explorer.** Belum saya tetapkan seluruh 6,14 GiB tersebut aman dibuang.

2. **Database pengujian lama — kandidat sekitar 1,57 GiB.**  
   Yang paling besar:
   
   - `codex_pokok_import_20260915133132`: **1,41 GiB**.
   - `codex_pokok_scale_20260915_20260915131645`: **0,11 GiB**.
   - Database pengujian Pokok lainnya: sekitar **0,05 GiB**.

   Nama dan riwayatnya menunjukkan database pengujian, tetapi pemakaian saat ini belum diperiksa melalui server MySQL. Jika sudah tidak diperlukan, penghapusannya dilakukan lewat MySQL. Database `laravel_13_bum_09092026` sebesar **6,07 GiB** masih tercantum sebagai database CMS BUM pada konfigurasi saat ini, sehingga perlu dipertahankan.

3. **Artifact project — cukup besar untuk diprioritaskan.**  
   Ukuran aktual yang ditemukan:
   
   - CMS BUM: **5,61 GiB**, setelah dump MariaDB tadi dihapus.
   - Phoenix Standalone: **1,40 GiB**.
   - Phoenix: **1,18 GiB**.

   Untuk CMS BUM, kandidat yang sudah diperiksa sebelumnya adalah output build Android **416,85 MiB**, ZIP installer **460,39 MiB**, serta cache Gradle **857,19 MiB**. Ketiganya berjumlah sekitar **1,69 GiB**. SDK/JDK dan kunci penandatanganan APK perlu tetap dijaga; penghapusan cache dapat membuat build berikutnya membutuhkan unduhan ulang.

4. **Artifact Arunika Map Studio — perlu pemeriksaan terarah.**  
   Folder ini berisi:
   
   - `project-artifacts/dist`: **1,84 GiB**.
   - `project-artifacts/backups`: **1,04 GiB**.
   - `project-artifacts/browsers`: **0,69 GiB**.

   Ini kandidat peninjauan yang baik. Namun, paket di `dist` bisa merupakan aplikasi yang sedang digunakan, dan browser bisa diperlukan untuk pengujian. Saya belum mengklasifikasikannya sebagai aman dihapus.

5. **Backup dan salinan lama — cocok dipindahkan atau ditinjau.**  
   Selain backup uploaded file **8,66 GiB**, ditemukan arsip website tahun 2021 sebesar **2,89 GiB** di `D:\Files`. Folder `D:\Laragon\www\New folder\contents_old` juga memakai hampir seluruh **2,12 GiB** ukuran parent-nya. Usia atau nama folder belum membuktikan bahwa datanya tidak diperlukan; ini perlu ditinjau sebelum dibuang.

Untuk mendapat ruang dengan perubahan paling sedikit, saya akan memprioritaskan **pemeriksaan retensi binlog MySQL, database pengujian Pokok, lalu cleanup artifact build Android**. Game memang paling besar, tetapi pengurangan ukurannya biasanya berarti memindahkan atau menghapus instalasi melalui Steam/launcher.

Hasil scan tersimpan di [disk-usage.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/drive-d-audit_20261012_103932/disk-usage.json), dan rincian MySQL serta artifact ada di [targeted-details.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/drive-d-audit_20261012_103932/targeted-details.json). Memori pemeriksaan storage sebelumnya dipakai sebagai konteks; seluruh ukuran di atas diukur ulang sekarang.

**Belum ada cleanup tambahan, perubahan source, atau pembaruan Graphify; hanya dua laporan audit yang dibuat.** Pemeriksaan koneksi database, kebutuhan recovery, dan penggunaan artifact belum dilakukan.  
Risiko utama ada pada penghapusan data MySQL, backup unik, dan paket aplikasi yang masih digunakan—ketiganya belum masuk daftar aman dihapus.

## File dibuat, dimodifikasi, atau dirujuk

- Tidak tercatat eksplisit; jangan mengasumsikan telah dilakukan.

- | Analisis rumus — `formula-detail-20260914` | **0,15 GiB** | Terutama `workbook-extraction.json`, sebesar **141,50 MiB** |
- Selain itu, `android-user/debug.keystore` adalah kunci penandatanganan APK debug. Kebutuhan mempertahankannya tercantum di [README Android](/D:/Laragon/www/laravel-13-cms-bum/mobile/android/README.md:64). File ini hanya sekitar 2,6 KB dan perlu dijaga agar pembaruan APK tetap dapat menggunakan tanda tangan yang sama.
- | `mariadb-dump-fix_20260908_155301` | `laravel_13_bum_mariadb.sql` dan `laravel_13_bum.sql.bak_20260908_155301_mariadb` |
- | `mariadb1011-native_20260908_162648` | `laravel_13_bum_MariaDB1011_READY.sql` dan `laravel_13_bum.sql.bak_before_native_srid` |
- | Root `project-artifacts` | `laravel_13_bum.sql.bak_20260908_154532_collation` |
- Daftar file, ukuran, SHA-256 sebelum penghapusan, dan hasil verifikasi tersimpan di [deletion-audit.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/cleanup-mariadb-dumps_20261012_103558/deletion-audit.json).
- Hasil scan tersimpan di [disk-usage.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/drive-d-audit_20261012_103932/disk-usage.json), dan rincian MySQL serta artifact ada di [targeted-details.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/drive-d-audit_20261012_103932/targeted-details.json). Memori pemeriksaan storage sebelumnya dipakai sebagai konteks; seluruh ukuran di atas diukur ulang sekarang.

## Backup historis

- Sekitar **68% ukurannya berasal dari perangkat build Android dan lima salinan dump database besar**. Folder ini menampung alat build, cache, database pengujian, dan backup dari berbagai pekerjaan sebelumnya.
- | Dump dan backup database MariaDB | **2,88 GiB** | Lima file besar, masing-masing sekitar **589 MiB** |
- | Snapshot dan pemeriksaan Graphify | **0,80 GiB** | Akumulasi backup graph, manifest, dan hasil pemeriksaan |
- | Peta MBTiles dan QGIS | **0,36 GiB** | Ekspor peta dan backup beberapa versi |
- | Artifact lainnya | **0,68 GiB** | APK penyerahan, backup, laporan, dan pekerjaan lain |
- Artifact Graphify terkumpul sampai **816,73 MiB**. Backup lama bisa dikompres sambil mempertahankan manifest, laporan, dan checkpoint yang diperlukan. JSON hasil ekstraksi workbook juga layak dikompres jika analisisnya sudah selesai. Penghematan pada kelompok ini belum saya ukur melalui uji kompresi.
- Belum ada cleanup, perubahan source, backup baru, atau pembaruan Graphify.** Verifikasi yang selesai adalah pengukuran ukuran, pemeriksaan source, perbandingan SHA-256, dan uji kompresi/dekompresi; build Android dan aplikasi tidak dijalankan ulang.
- Runtime database uji belum dipastikan tidak dipakai—pembacaan command line proses ditolak oleh sistem. Karena itu, penghapusan runtime tersebut belum layak dilakukan; SDK/JDK, kunci APK, dan backup unik juga harus dipertahankan.
- Sudah saya hapus 5 file dump dan backup database MariaDB.** Ruang yang dibebaskan sebesar **2,88 GiB**, sehingga ukuran `project-artifacts` turun dari **8,49 GiB menjadi 5,61 GiB**.
- | `mariadb-dump-fix_20260908_155301` | `laravel_13_bum_mariadb.sql` dan `laravel_13_bum.sql.bak_20260908_155301_mariadb` |
- | `mariadb1011-native_20260908_162648` | `laravel_13_bum_MariaDB1011_READY.sql` dan `laravel_13_bum.sql.bak_before_native_srid` |
- | Root `project-artifacts` | `laravel_13_bum.sql.bak_20260908_154532_collation` |
- Penyumbang terbesarnya adalah game, lingkungan pengembangan Laragon, dan backup.** Ada peluang optimasi yang cukup berarti tanpa harus menghapus game, tetapi sebagian kandidat perlu diperiksa penggunaannya terlebih dahulu.
- | `D:\Backup` | **8,66 GiB** | Backup uploaded file web, dalam folder `New MG` dan `Old MG` |
- Rujukan tambahan tersedia di transkrip kronologis.

## Pengujian dan verifikasi historis

- Pemeriksaan ini memakai memori proyek di `E:\AI\Memories` terkait recovery Git, Android/MBTiles, MariaDB, dan cakupan Graphify, kemudian diverifikasi terhadap file aktual. Source yang diperiksa mencakup `build.ps1`, kedua `build.gradle.kts`, README Android, serta laporan pengujian artifact terkait. Graphify membantu menemukan kedua file konfigurasi Gradle; hash keduanya cocok dengan source saat ini, tetapi node tersebut tidak memiliki relasi yang cukup untuk membuktikan ketergantungan, sehingga ketergantungannya saya pastikan langsung dari source.
- Belum ada cleanup, perubahan source, backup baru, atau pembaruan Graphify.** Verifikasi yang selesai adalah pengukuran ukuran, pemeriksaan source, perbandingan SHA-256, dan uji kompresi/dekompresi; build Android dan aplikasi tidak dijalankan ulang.
- Verifikasi setelah penghapusan memastikan:
- Daftar file, ukuran, SHA-256 sebelum penghapusan, dan hasil verifikasi tersimpan di [deletion-audit.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/cleanup-mariadb-dumps_20261012_103558/deletion-audit.json).

## Masalah belum selesai dan langkah lanjutan

- Penyumbang terbesarnya adalah game, lingkungan pengembangan Laragon, dan backup.** Ada peluang optimasi yang cukup berarti tanpa harus menghapus game, tetapi sebagian kandidat perlu diperiksa penggunaannya terlebih dahulu.
- Ukuran folder pada tabel berasal dari panjang file yang dapat dibaca. Total yang terukur sekitar **134,54 GiB**; selisih sekitar **5,04 GiB** terhadap penggunaan partisi belum dirinci karena ada direktori terlindungi, junction yang dilewati, serta perbedaan ukuran file dengan alokasi filesystem.
- Ada **40 file `binlog.*`**; beberapa masing-masing sekitar 1 GiB. Ini salah satu kandidat terbesar. Cleanup harus dilakukan melalui MySQL setelah memastikan kebutuhan recovery, replication, dan retensinya. **Jangan menghapus file binlog melalui Explorer.** Belum saya tetapkan seluruh 6,14 GiB tersebut aman dibuang.
- Nama dan riwayatnya menunjukkan database pengujian, tetapi pemakaian saat ini belum diperiksa melalui server MySQL. Jika sudah tidak diperlukan, penghapusannya dilakukan lewat MySQL. Database `laravel_13_bum_09092026` sebesar **6,07 GiB** masih tercantum sebagai database CMS BUM pada konfigurasi saat ini, sehingga perlu dipertahankan.
- Untuk CMS BUM, kandidat yang sudah diperiksa sebelumnya adalah output build Android **416,85 MiB**, ZIP installer **460,39 MiB**, serta cache Gradle **857,19 MiB**. Ketiganya berjumlah sekitar **1,69 GiB**. SDK/JDK dan kunci penandatanganan APK perlu tetap dijaga; penghapusan cache dapat membuat build berikutnya membutuhkan unduhan ulang.
- 4. **Artifact Arunika Map Studio — perlu pemeriksaan terarah.
- Ini kandidat peninjauan yang baik. Namun, paket di `dist` bisa merupakan aplikasi yang sedang digunakan, dan browser bisa diperlukan untuk pengujian. Saya belum mengklasifikasikannya sebagai aman dihapus.
- Selain backup uploaded file **8,66 GiB**, ditemukan arsip website tahun 2021 sebesar **2,89 GiB** di `D:\Files`. Folder `D:\Laragon\www\New folder\contents_old` juga memakai hampir seluruh **2,12 GiB** ukuran parent-nya. Usia atau nama folder belum membuktikan bahwa datanya tidak diperlukan; ini perlu ditinjau sebelum dibuang.
- Hasil scan tersimpan di [disk-usage.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/drive-d-audit_20261012_103932/disk-usage.json), dan rincian MySQL serta artifact ada di [targeted-details.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/drive-d-audit_20261012_103932/targeted-details.json). Memori pemeriksaan storage sebelumnya dipakai sebagai konteks; seluruh ukuran di atas diukur ulang sekarang.
- Belum ada cleanup tambahan, perubahan source, atau pembaruan Graphify; hanya dua laporan audit yang dibuat.** Pemeriksaan koneksi database, kebutuhan recovery, dan penggunaan artifact belum dilakukan.
- Risiko utama ada pada penghapusan data MySQL, backup unik, dan paket aplikasi yang masih digunakan—ketiganya belum masuk daftar aman dihapus.

Untuk melanjutkan, verifikasi kembali Git/source/runtime aktif, gunakan Graphify sebagai peta teknis, dan backup sebelum perubahan. Jangan mengulangi mutasi database/deploy hanya berdasarkan arsip.

## Laporan terbaru lain yang relevan

### 2026-10-12 10:27:30 WIB

**Bisa dioptimalkan, dan penyebab besarnya sudah terlihat jelas.** Saya sudah mengukur [folder project-artifacts](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts): totalnya **8,49 GiB atau 9,12 GB**, berisi **39.565 file**. Perbedaan GiB dan GB menjelaskan kenapa angka yang terlihat bisa sekitar 8–9 GB.

Sekitar **68% ukurannya berasal dari perangkat build Android dan lima salinan dump database besar**. Folder ini menampung alat build, cache, database pengujian, dan backup dari berbagai pekerjaan sebelumnya.

| Kelompok | Ukuran aktual | Penyebab |
|---|---:|---|
| Android — `android-offline_20260922` | **2,91 GiB** | SDK, JDK, Gradle, cache dependency, ZIP installer, dan hasil build |
| Dump dan backup database MariaDB | **2,88 GiB** | Lima file besar, masing-masing sekitar **589 MiB** |
| Snapshot dan pemeriksaan Graphify | **0,80 GiB** | Akumulasi backup graph, manifest, dan hasil pemeriksaan |
| Pengujian import Pokok — `pokok-vps-import_20260915_222025` | **0,56 GiB** | MariaDB pengujian, file database, installer, dan SQL |
| Peta MBTiles dan QGIS | **0,36 GiB** | Ekspor peta dan backup beberapa versi |
| Analisis rumus — `formula-detail-20260914` | **0,15 GiB** | Terutama `workbook-extraction.json`, sebesar **141,50 MiB** |
| `downloads` | **0,14 GiB** | Dokumen dan bahan referensi |
| Artifact lainnya | **0,68 GiB** | APK penyerahan, backup, laporan, dan pekerjaan lain |
| **Total** | **8,49 GiB** | |

**Peluang optimasi terbesar ada di bagian berikut, dengan urutan yang saya sarankan.**

1. **Kompres dump database dan kurangi salinan yang benar-benar identik.**  
   Saya mencoba kompresi satu dump SQL sepenuhnya di memori, tanpa menulis atau mengganti file:
   
   - Ukuran asli: **589,03 MiB**.
   - Hasil GZIP: **70,97 MiB**.
   - Penghematan: **518,06 MiB 

[Cuplikan; lihat riwayat sumber untuk laporan lengkap.]

### 2026-10-12 10:36:21 WIB

**Sudah saya hapus 5 file dump dan backup database MariaDB.** Ruang yang dibebaskan sebesar **2,88 GiB**, sehingga ukuran `project-artifacts` turun dari **8,49 GiB menjadi 5,61 GiB**.

File yang dihapus berasal dari:

| Lokasi di dalam `project-artifacts` | Yang dihapus |
|---|---|
| `mariadb-dump-fix_20260908_155301` | `laravel_13_bum_mariadb.sql` dan `laravel_13_bum.sql.bak_20260908_155301_mariadb` |
| `mariadb1011-native_20260908_162648` | `laravel_13_bum_MariaDB1011_READY.sql` dan `laravel_13_bum.sql.bak_before_native_srid` |
| Root `project-artifacts` | `laravel_13_bum.sql.bak_20260908_154532_collation` |

Verifikasi setelah penghapusan memastikan:

- Kelima file tersebut sudah tidak ada.
- Tidak ada file lain yang hilang atau berubah berdasarkan ukuran dan waktu modifikasi.
- Status Git tetap sama; perubahan lama Anda dipertahankan.
- Script pemeriksaan dan laporan kecil di kedua folder MariaDB tetap tersedia.
- Artifact Android, Graphify, peta, dan pengujian import Pokok tetap dipertahankan.

Daftar file, ukuran, SHA-256 sebelum penghapusan, dan hasil verifikasi tersimpan di [deletion-audit.json](/D:/Laragon/www/laravel-13-cms-bum/project-artifacts/cleanup-mariadb-dumps_20261012_103558/deletion-audit.json).

Source aplikasi dan Graphify tidak diubah; pengujian runtime aplikasi tidak dijalankan karena pekerjaan ini hanya menghapus artifact yang Anda setujui.  
Penghapusan dilakukan permanen tanpa membuat salinan dump baru, sehingga kelima file tersebut tidak lagi tersedia sebagai bahan recovery di folder ini.

## Hubungan dengan memori sebelumnya

- Tidak tercatat eksplisit; jangan mengasumsikan telah dilakukan.

## Sumber dan keterbatasan

- Riwayat: C:\Users\aruna\.codex\sessions\2026\10\12\rollout-2026-10-12T10-22-50-01a12e22-6c7e-7390-b3ac-59bf4ab001af.jsonl
- Metode: C:\Users\aruna\.codex\thread_history_1.sqlite / thread_items + thread_turns + rollout comparison (0 missing text messages recovered); read_thread diverifikasi 1 halaman sampai selesai
- Pesan pengguna: 3; laporan akhir: 3
- Kesalahan sumber: Rollout lokal tidak ditemukan.
[Kredensial disamarkan]
- Memori lama dipertahankan; catatan ini menambahkan pembaruan per chat.
- Graphify diproses terpisah berdasarkan source aktif setelah arsip memori tersimpan.
- Verifikasi terbaru hanya mencakup integritas penyimpanan E/C; hasil uji aplikasi di atas bersifat historis.

## Memori pendukung dari sesi terdahulu

Bagian ini adalah handoff historis tambahan, berguna ketika proyeksi pesan aplikasi terpotong. Hasil uji tetap mengikuti tanggal sesi sumber; bukan verifikasi runtime hari ini.

Sumber: C:\Users\aruna\.codex\memories\rollout_summaries\2026-10-12T03-22-50-NFdJ-cms_bum_artifact_cleanup_and_drive_d_storage_audit.md

thread_id: 01a12e22-6c7e-7390-b3ac-59bf4ab001af
updated_at: 2026-10-12T03:41:55+00:00
rollout_path: C:\Users\aruna\.codex\sessions\2026\10\12\rollout-2026-10-12T10-22-50-01a12e22-6c7e-7390-b3ac-59bf4ab001af.jsonl
cwd: \\?\D:\Laragon\www\laravel-13-cms-bum
git_branch: main

# CMS BUM artifact cleanup and D: drive storage audit

Rollout context: The work was performed from `D:\Laragon\www\laravel-13-cms-bum` using PowerShell on Windows. The user first asked why `project-artifacts` was 8–9 GB and whether it could be optimized, then explicitly authorized deletion of MariaDB dumps/backups, and finally asked for a fast, efficient audit of the whole D: partition because only about 55.7 GB remained free. The initial artifact inspection was read-only; the explicitly approved MariaDB cleanup was executed and independently verified. The later D: drive scan was read-only and produced audit reports.

## Task 1: Analyze and optimize `project-artifacts`

Outcome: success

Preference signals:

- When asking why the project artifact directory was so large, the user wanted a concrete size breakdown and optimization possibilities rather than a generic cleanup recommendation -> future storage audits should quantify total bytes/files, rank first-level folders and largest files, identify duplicates, and distinguish rebuildable artifacts from recovery-critical data.
- The assistant explicitly stated that the first inspection was read-only, and the user later narrowed the request to “Data Dump dan backup database MariaDB hapus dulu deh” -> preserve a read-only diagnostic phase and wait for itemized authorization before destructive cleanup.
- The user’s deletion request referred specifically to MariaDB dumps/backups, not all database/runtime data -> interpret cleanup scope narrowly and leave Android, Graphify, maps, Pokok test runtime, source, and active database state untouched unless separately authorized.

Key steps:

- Measured `project-artifacts` at 39,565 files and 9,115,793,413 bytes: 8.49 GiB / 9.116 GB.
- Ranked the dominant groups: `android-offline_20260922` 2.913 GiB; large MariaDB dump variants 2.876 GiB; Graphify snapshots/QA 0.798 GiB; Pokok SQL test runtime 0.564 GiB; map/QGIS artifacts 0.364 GiB; workbook extraction 0.148 GiB.
- Identified five large MariaDB files, each around 589 MiB. Two files were SHA-256 identical (`3146A8...A43490`), while other variants had different hashes and therefore were not assumed duplicates.
- Confirmed Android build dependencies are still located inside `project-artifacts/android-offline_20260922`: JDK, Gradle, SDK, Gradle cache, Android user directory, project cache, and build output. `mobile/android/build.ps1` points directly at these paths, so the entire Android artifact directory cannot be removed blindly.
- Confirmed `android-user/debug.keystore` is needed if future debug APK updates must preserve the same signing key.
- Verified the Android build APK and the delivery APK had identical size/hash, and the generated MBTiles matched the source map asset, making some build outputs candidates for later cleanup but not automatically deleted.
- Tested GZIP compression in memory on one 589.03 MiB SQL dump: compressed size 70.97 MiB, 87.95% savings, and decompressed SHA-256 matched the original. No replacement file was written during that test.

Reusable knowledge:

- `project-artifacts` is already ignored by Git (`.gitignore` contains `/project-artifacts/`) and has zero tracked files, but Git ignore does not reclaim local disk space.
- Large artifact groups include both disposable/rebuildable material and recovery-critical material. Android SDK/JDK/toolchain, `debug.keystore`, unique database dumps, active test runtimes, maps, and source-linked artifacts require separate validation before removal.
- The active Android build script uses `project-artifacts/android-offline_20260922` for `JAVA_HOME`, `GRADLE_USER_HOME`, `ANDROID_USER_HOME`, `ANDROID_HOME`, Gradle project cache, Kotlin cache, and build output. Any future cleanup must preserve or relocate these dependencies together and then update the build script.
- Compression is a high-value non-lossy optimization for SQL dumps; the tested dump achieved 87.95% savings with a successful hash-preserving round trip.

Failures and how to do differently:

- No cleanup was performed during the initial optimization analysis; this was correct because the user had not yet authorized a specific deletion scope.
- Process command-line inspection for running database processes was denied by Windows (`Access denied`), so database/runtime ownership could not be fully proven from that inspection. Do not classify active database runtime folders as removable solely from names or size.
- Graphify output was modified by the read/query workflow (`graphify-out/*` appeared modified), but no Graphify rebuild or intentional graph update was performed. Future read-only graph queries should account for possible cache/query-stamp changes and restore or report them explicitly.

References:

- `D:\Laragon\www\laravel-13-cms-bum\mobile\android\build.ps1` lines 4–14: artifact-local Android tool/cache/output paths.
- `D:\Laragon\www\laravel-13-cms-bum\mobile\android\README.md` lines 58–64: build artifact location and debug keystore retention.
- `graphify-out\graph.json`, `graphify-out\manifest.json`: existing graph and manifest; Gradle source hashes matched current source in the inspection.
- MariaDB files inspected before deletion included `mariadb-dump-fix_20260908_155301\laravel_13_bum_mariadb.sql`, `mariadb-dump-fix_20260908_155301\laravel_13_bum.sql.bak_20260908_155301_mariadb`, `mariadb1011-native_20260908_162648\laravel_13_bum_MariaDB1011_READY.sql`, `mariadb1011-native_20260908_162648\laravel_13_bum.sql.bak_before_native_srid`, and root `laravel_13_bum.sql.bak_20260908_154532_collation`.

## Task 2: Delete the explicitly approved MariaDB dumps/backups

Outcome: success

Preference signals:

- The user said “Data Dump dan backup database MariaDB hapus dulu deh” after reviewing the measured breakdown -> future agents should delete only the named MariaDB dump/backup files, not infer authorization to remove MySQL data directories, binlogs, active databases, or unrelated artifacts.
- The assistant performed a final target listing and scope check before deletion -> retain this confirmation-and-audit workflow for destructive file operations.

Key steps:

- Resolved all five targets under the canonical artifact root and rejected unexpected target types/reparse paths.
- Wrote `cleanup-mariadb-dumps_20261012_103558\deletion-audit.json` containing target paths, byte counts, SHA-256 hashes, pre-cleanup inventory, Git status, and deletion status.
- Deleted exactly five files: two in `mariadb-dump-fix_20260908_155301`, two in `mariadb1011-native_20260908_162648`, and one at the artifact root.
- Re-scanned the artifact root after deletion. Result: 3,088,208,376 bytes freed (2.876 GiB); size decreased from 8.49 GiB to 5.614 GiB; file count decreased from 39,565 to 39,561.
- Verified all five targets were absent, `UnexpectedMissing=0`, `UnexpectedChanged=0`, and Git status was unchanged.

Reusable knowledge:

- The verified deletion result was: `Status=verified`, `DeletedFiles=5`, `FreedGiB=2.876`, `AfterGiB=5.614`, `AfterFiles=39561`, `TargetsAbsent=true`, `GitStatusUnchanged=true`.
- The audit directory itself remains inside `project-artifacts` and records the deleted files’ pre-deletion hashes and sizes. The deleted SQL dumps were removed permanently from this artifact root; no replacement backup was created.
- The cleanup did not alter application source, database runtime directories, Graphify data intentionally, Android toolchain, maps, or Pokok import artifacts.

Failures and how to do differently:

- The deletion command initially exceeded the tool’s first polling interval and returned no final result. Completion was obtained by polling the session; future destructive commands must always poll to completion and validate the filesystem afterward rather than treating empty initial output as success.
- No application/runtime tests were run because the operation was limited to artifact deletion; do not report application health based on this cleanup.

References:

- Audit: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\cleanup-mariadb-dumps_20261012_103558\deletion-audit.json`.
- Exact deletion result: `{"Status":"verified","DeletedFiles":5,"FreedGiB":2.876,"BeforeGiB":8.49,"AfterGiB":5.614,"AfterFiles":39561,"UnexpectedMissing":0,"UnexpectedChanged":0,"GitStatusUnchanged":true,"TargetsAbsent":true}`.

## Task 3: Audit D: partition usage and identify optimization candidates

Outcome: partial

Preference signals:

- The user asked to “check partisi D file dan folder apa saja ... dan bisa dioptimize kah, lakukan secara cepat dan efisien?” -> future drive audits should use staged metadata scans, prioritize dominant folders and largest files, produce actionable findings quickly, and avoid exhaustive content reads unless needed.
- Historical memory records that the user prefers stopping a slow drive scan once dominant causes are known; in this rollout the scan completed in about 34 seconds, but the same staged approach remains appropriate.
- The user asked about optimization, not immediate deletion -> keep the D: scan read-only and classify candidates by risk; require explicit approval for each destructive category.

Key steps:

- Read D: capacity/free-space metadata: total 209,714,147,328 bytes (~195.31 GiB), free 59,841,269,760 bytes (~55.73 GiB), used 149,872,877,568 bytes.
- Completed a metadata-only scan of major root folders in about 34 seconds with no errors for ordinary folders. The scan measured about 144.47 GB of logical file contents; the difference from volume-used space was attributed to protected directories, skipped reparse/junction paths, and filesystem allocation differences.
- Largest root folders: `D:\SteamLibrary` 51.03 GiB; `D:\Laragon` 47.49 GiB; `D:\Games` 15.59 GiB; `D:\Backup` 8.66 GiB; `D:\Arunika Map Studio` 4.00 GiB; `D:\Files` 3.28 GiB; `D:\Application` 2.40 GiB; `D:\w` 1.86 GiB.
- Largest targeted subfolders/files included `D:\SteamLibrary\steamapps` 51.03 GiB (AoE2DE manifest reported 54,469,022,622 bytes), `D:\Laragon\www` 23.21 GiB, `D:\Laragon\data` 17.51 GiB, `D:\Games\Point Blank` 15.54 GiB, and `D:\Backup\Uploaded_File` 9.24 GiB.
- Within Laragon/MySQL, measured 40 `binlog.*` files totaling 6.137 GiB. Large database directories included `laravel_13_bum_09092026` 6.071 GiB, `codex_pokok_import_20260915133132` 1.412 GiB, and `laravel_11` 1.289 GiB.
- Found CMS BUM project artifacts at 5.614 GiB after the approved dump deletion, Phoenix Standalone artifacts at 1.395 GiB, Phoenix artifacts at 1.176 GiB, and CMS BUM `.git` at 0.805 GiB.
- Created audit reports under `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\drive-d-audit_20261012_103932\disk-usage.json` and `targeted-details.json`.

Reusable knowledge:

- Highest-impact candidates are not all safe deletions. Game folders should be managed through Steam/official launchers; MySQL binlogs must be purged through MySQL after retention/recovery review; database directories must not be deleted from Explorer; project backups/artifacts require owner review.
- The CMS BUM `.env` pointed to database `laravel_13_bum_09092026`, so that 6.071 GiB database was treated as active/current and not proposed for deletion.
- `D:\Laragon\data\mysql-8\binlog.*` is a major storage consumer. Historical memory explicitly warns never to delete binlog files manually; use MySQL retention or `PURGE BINARY LOGS` only after confirming recovery/replication requirements.
- A single old archive was 3,102,853,648 bytes: `D:\Files\Backup Script Web & Database aruna-dev.id\Backup_14072021\Script Website\backup-7.14.2021_01-09-15_u5165748.tar.gz`. Other large candidates include CMS BUM project artifacts, Arunika Map Studio `project-artifacts\dist` (1.843 GiB), and `project-artifacts\backups` (1.044 GiB), but age/name alone does not prove they are disposable.
- The audit found two large Git pack files in CMS BUM (`.git\objects\pack`) totaling roughly 0.8 GiB. Git history optimization would require a separate repository-aware audit and explicit authorization; do not delete `.git` objects manually.

Failures and how to do differently:

- The whole-volume scan did not account for all used space: approximately 5 GiB remained unexplained due to protected/system locations, reparse-point handling, and filesystem allocation differences. Treat the report as a strong targeted inventory, not a mathematically exhaustive deletion manifest.
- `$RECYCLE.BIN`, `Config.Msi`, and `System Volume Information` produced permission errors. A complete accounting would require an elevated terminal; future reports should explicitly preserve the `partial`/permission boundary.
- No cleanup was performed from the D: audit. The findings are recommendations only. Before any deletion, verify current usage, active processes, database connections, backup recoverability, and user approval for each exact path/category.

References:

- Full audit: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\drive-d-audit_20261012_103932\disk-usage.json`.
- Targeted details: `D:\Laragon\www\laravel-13-cms-bum\project-artifacts\drive-d-audit_20261012_103932\targeted-details.json`.
- Exact scan capacity result: `capacity_bytes=209714147328`, `free_bytes=59841269760`, `used_bytes=149872877568`, `scanned_logical_bytes=144465054214`.
- Exact high-value paths: `D:\Laragon\data\mysql-8\binlog.*`; `D:\Laragon\data\mysql-8\laravel_13_bum_09092026`; `D:\Laragon\www\laravel-13-cms-bum\project-artifacts`; `D:\SteamLibrary\steamapps\common\AoE2DE`; `D:\Games\Point Blank`; `D:\Backup\Uploaded_File`; `D:\Files\Backup Script Web & Database aruna-dev.id\Backup_14072021`; `D:\Arunika Map Studio\project-artifacts`.
