# 2026-10-12 — Laravel 13 Phoenix: File Manager V2, integritas upload, 8 worker dan 4 paralel

## Identitas dan tujuan

- Project aktif: `D:\Laragon\www\laravel-13-phoenix`.
- Tanggal checkpoint: 12 Oktober 2026, Asia/Jakarta; kondisi terakhir diperiksa sekitar 15:20 WIB.
- Tujuan: melanjutkan kendala upload folder/file bersamaan, memperkuat retry dan integritas upload, mengatur PHP-CGI Laragon, serta menyiapkan pengujian Cloudflare R2 melalui File Manager V2.
- Halaman: `http://laravel-13-phoenix.aruna/admin/file-manager-v2`.
- Folder sumber pengguna: `D:\Laragon\www\laravel-13-phoenix\public\assets\plugins\fontawesome\5.15.3`.
- Pengguna secara eksplisit meminta rangkuman ini disimpan ke E dan drive C. Jangan mengubah registry memori lama; tambahkan note baru ini saja.

## Konteks awal dan memori sebelumnya

- Keluhan awal: upload 10 file bersamaan pernah gagal ketika worker PHP tidak tersedia.
- Konteks dari E: `20260727-163700-laravel13-phoenix-file-manager-v2-batch-upload-reliability.md`, `20260727-173221-laravel13-phoenix-file-manager-v2-upload-center-safe-retry.md`, dan `20260728-165517-laravel13-phoenix-file-manager-v2-startup-and-bulk-retry.md`, di `E:\AI\Memories\extensions\ad_hoc\notes`.
- Memori lama mencatat error Nginx `no live upstreams`, connection refused, dan 502. Jangan menyimpulkan semua error historis terbukti berasal dari satu penyebab.
- Repository mempunyai perubahan Article dan artifact lama milik pengguna. Semua dipertahankan; tidak ada reset, clean, stage, commit, atau push.
- Laragon aktif di D, bukan instalasi E yang disebut beberapa memori lama. Versi yang diperiksa: Laragon 6.0.0.2, Nginx 1.28.1, PHP-CGI 8.5.6.

## Keputusan dan kondisi final

- Pengguna awalnya menyetujui 4 worker + 2 upload aktif untuk baseline; kemudian secara eksplisit meminta **8 worker + 4 upload paralel**.
- Kondisi aktual saat penyimpanan: `D:\Laragon\usr\laragon.ini` berisi `[nginx] Upstream=8`; `connections.json` berisi `upload.max_parallel=4`.
- Delapan listener PHP-CGI aktif: 9003=PID 25696, 9004=31440, 9005=33932, 9006=23808, 9007=27848, 9008=36444, 9009=18572, 9010=22752. PID hanya snapshot, wajib diperiksa ulang pada sesi berikutnya.
- Windows User Environment `PHP_FCGI_MAX_REQUESTS=0`; seluruh delapan worker telah diverifikasi melalui HTTP memakai nilai 0.
- PHP-CGI source yang diperiksa memakai batas default 500 request saat environment tersebut unset. Satu worker berhasil menjalankan 520 request tanpa perubahan PID setelah nilai 0 diterapkan. Ini bukti perbaikan batas umur proses saat ini, bukan bukti tunggal akar semua kegagalan historis.
- Worker adalah pool bersama; tidak ada pasangan dua worker khusus untuk satu file. Empat upload aktif menyisakan kapasitas untuk request lain ketika beban memungkinkan.
- Browser yang sudah terbuka perlu refresh sekali/Ctrl+F5 untuk membaca maxParallel baru. Pengguna kemudian melaporkan upload lokal 4 paralel berhasil melalui screenshot 7984/7984 complete, 100%.
- Ukuran chunk aktif 8 MiB, threshold 16 MiB, maksimum lima percobaan total dengan backoff. Catatan pemulihan berlaku 24 jam dan memerlukan pemilihan ulang file, storage, serta destination yang sama setelah refresh.
- Tidak ada jaminan keberhasilan 100% saat jaringan/storage gagal berkepanjangan, quota habis, atau host kehabisan resource.

## Implementasi aplikasi yang sudah dilakukan

- SHA-256 streaming browser; validasi checksum sumber, chunk, ukuran hasil, dan SHA-256 objek/file setelah write.
- Write gagal tidak dilaporkan sebagai berhasil. Chunk dipertahankan ketika penyelesaian gagal.
- Session JSON ditulis secara atomik; receipt idempotency/session/hasil tersimpan di disk, tidak bergantung hanya pada cache PHP.
- Kunci upload/browser journal dan kunci batch folder dipertahankan untuk retry/refresh; received chunks dikenali dan dilewati saat resume. Retry completion yang responsnya hilang mengembalikan asset yang sudah selesai tanpa duplikasi.
- Lock per file dan quota reservation tersimpan; validasi pemilik sesi dan batas TTL diperbaiki.
- Journal folder besar dioptimalkan: kunci dipersist saat pemilihan/batas batch; tidak menulis ulang seluruh 7984 entri setelah setiap file selesai. Server receipt memulihkan status hasil setelah refresh.
- Error awal/preflight journal ditampilkan melalui notifikasi aplikasi.
- Fixture test folder lama yang mengklaim 4096 byte tetapi isinya kosong diganti dengan konten 4096 byte sungguhan.
- Aset File Manager V2 dibangun ulang, tanpa dependency baru atau perubahan schema database.

## File aktif dibuat atau diubah

- `app/Services/FileManagerV2/FileManagerV2Storage.php`.
- `app/Http/Controllers/Api/V2/FileManagerV2Controller.php`.
- `config/filemanager_v2.php` — default aman 2; pengaturan persisted instalasi ini sekarang 4.
- `resources/js/filemanager_v2/data/uploadIntegrity.js` — baru.
- `resources/js/filemanager_v2/data/uploadRecovery.js` — baru.
- `resources/js/filemanager_v2/data/live.js`.
- `resources/js/filemanager_v2/data/folderUploadBatch.js`.
- `resources/js/filemanager_v2/components/FilePondUploadEngine.vue`.
- `resources/js/filemanager_v2/App.vue`.
- `resources/views/filemanager_v2/index.blade.php`.
- `tests/Feature/FileManagerV2/FileManagerV2FolderBatchUploadTest.php`.
- `storage/app/filemanager_v2/settings/connections.json` — max_parallel saja; credential dan connection lain dipertahankan.
- `public/assets/plugins/filemanager_v2/filemanager-v2.js` dan `filemanager-v2.css` — build.
- `docs/filemanager_v2-deployment.md` — kondisi akhir 8 worker + 4 paralel.
- `D:\Laragon\usr\laragon.ini` dan `D:\Laragon\etc\nginx\php_upstream.conf`.
- Phoenix vhost menjadi `D:\Laragon\etc\nginx\sites-enabled\laravel-13-phoenix.aruna.conf`, tanpa prefix `auto.`, agar guard tidak diregenerasi Laragon.

## Backup dan artifact

Semua backup dibuat sebelum edit dan disimpan di project-artifacts. Jangan gunakan backup sebagai source aktif.

- Tahap reliabilitas: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\file-manager-v2-reliability_20261012_121417`.
- Backup awal: `backups\project` (mirror source), `backups\public-filemanager-v2` (aset build), dan `backups\laragon` (INI, upstream, vhost, PID recovery, serta metadata perubahan environment).
- Manifest awal: `backup-manifest.json`; inventaris tambahan: `qc\backup-inventory-final.json`. Hash manifest awal 12 backup telah lulus pemeriksaan.
- Tahap 8 worker/4 paralel: `D:\Laragon\www\laravel-13-phoenix\project-artifacts\file-manager-v2-workers8_20261012_142211`.
- Backup kedua: `backups\laragon.ini.bak_20261012_142211_workers8_parallel4`, `php_upstream.conf.bak_20261012_142211_workers8_parallel4`, `connections.json.bak_20261012_142211_workers8_parallel4`, dan `filemanager_v2-deployment.md.bak_20261012_142211_workers8_parallel4`. Semua empat hash backup terverifikasi.
- Laporan utama: masing-masing folder mempunyai `qc\final-report.json`. Laporan kedua memuat PID, hasil HTTP, uji 4 paralel, perubahan, backup, dan batas verifikasi.
- Artifact pengujian/backup memiliki `.gitignore`; jangan commit fixture, credential backup, log, atau graph.

## Runtime Laragon/Nginx

- Phoenix vhost memakai guard `location ^~ /storage/filemanager_v2/ { return 404; }` untuk runtime/cache/file privat melalui symlink storage.
- Runtime probe lulus: halaman admin 302 ke login tanpa sesi; API bootstrap 401 dengan Accept JSON; bundle 200; private runtime 404.
- Nginx config test dan reload master aktif lulus. Master produksi terakhir PID 21356; Laragon asli PID 17684, satu instance.
- Pada tahap awal, perintah CLI Laragon reload sempat membuat instance/master tambahan. Keduanya dihentikan secara terarah dan master asli dipertahankan. Jangan mengulang `laragon.exe reload nginx` secara buta saat Laragon sudah terbuka.
- Empat CGI awal di-restart langsung dengan environment 0; empat tambahan dijalankan langsung hidden tanpa menghentikan worker lama. INI 8 tersimpan; **restart penuh Laragon belum diuji**, termasuk pewarisan environment dan regenerasi worker setelah startup baru.
- Seluruh server QC sementara pada loopback 18087/18088 sudah ditutup. Produksi dan delapan CGI tetap berjalan.
- RAM saat uji 8 worker: total PHP-CGI sekitar 508 MiB. Laptop i5-1335U, RAM visible 23,64 GiB. Sisa RAM berubah sesuai aplikasi lain; jangan memakai angka ini sebagai peak atau sizing server aaPanel.

## Pengujian dan bukti

- 51 tes regresi FileManagerV2: PASS, 298 assertions, PHP 8.5.6.
- 9 tes tambahan di artifact: PASS, 27 assertions; mencakup write gagal, checksum, receipt/cache loss, quota, idempotency, dan pemulihan batch.
- 13 vektor/batas SHA-256 dibanding Node crypto, cancellation, user isolation, journal reselection: PASS.
- Metadata aktual Font Awesome 7984 file: journal 4.015.648 byte UTF-16, muat pada batas konservatif 5 MiB; fresh module/refresh mempertahankan keys; selesai membersihkan journal; hanya empat penulisan pada skenario uji, PASS.
- Uji HTTP lima file 2/7/18/40/70 MiB pada baseline 2 paralel: PASS, semua hash cocok, transient HTML 503 dan lost complete response pulih tanpa duplikasi.
- Upload 19 MiB: resume setelah CGI restart dan client refresh, chunk 0 tidak dikirim ulang, hash cocok, satu hasil saja, PASS.
- 520 request berturut-turut pada satu CGI, PID tetap dan maxRequests=0: PASS.
- Setelah upgrade: delapan endpoint worker masing-masing HTTP 200, PID cocok, maxRequests=0; lima file 2/7/18/40/70 MiB dengan peakActive=4, checksum cocok, retry tanpa duplikasi: PASS.
- Syntax PHP/JS, build, git diff check, dan credential-preservation check: PASS. SNMP MIB warning CLI tersimpan sebagai stderr; tidak menyebabkan test gagal.
- Folder sumber 7984 file, 70.129.455 byte (66,88 MiB); tidak ada file kosong/ekstensi terlarang pada pemeriksaan. Mayoritas 7880 SVG, terbesar 14.820.648 byte, di bawah threshold chunk 16 MiB.
- Hasil upload lokal pertama diperiksa fisik di `storage\app\public\filemanager_v2\files\5.15.3`: 7984 file, 70.129.455 byte. Ini verifikasi jumlah/total byte, bukan audit hash seluruh folder.
- Pengguna memberikan screenshot sukses baseline 2 paralel dan screenshot sukses berikutnya yang dilaporkan memakai 4 paralel, masing-masing UI 7984/7984 complete. Screenshot tidak sendirinya mengukur jumlah request paralel; uji HTTP terpisah membuktikan peakActive=4.
- R2 connection `r2` berhasil diperiksa read-only; quota File Manager 1 GiB, snapshot free sekitar 976,83 MiB. Tidak ada upload/delete nyata ke R2 oleh agent.

## Pembahasan aaPanel — belum dieksekusi

- Worker aaPanel umumnya PHP-FPM; pilih versi PHP yang dipakai website melalui App Store / Deployment / PHP / Settings / Optimization atau FPM profile.
- Contoh 8 worker selalu hidup: `pm=static`, `pm.max_children=8`.
- Contoh maksimal 8 dinamis: `pm=dynamic`, `pm.max_children=8`, `pm.start_servers=4`, `pm.min_spare_servers=2`, `pm.max_spare_servers=4`.
- Backup dahulu, apply/reload PHP, lakukan restart saat tidak ada upload aktif; periksa Load average/Logs dan status listen queue/max children reached. Pool yang sama dapat melayani website lain.
- Referensi resmi: `https://www.aapanel.com/docs/Function/Deployment.html`, `https://www.php.net/manual/en/install.fpm.configuration.php`, `https://www.php.net/manual/en/fpm.status.php`.
- Versi PHP, mode pool, RAM, jumlah site, dan runtime server aaPanel belum diperiksa; jangan menganggap contoh sizing sudah diterapkan atau cocok di produksi.

## Graphify dan batas yang belum selesai

- Graph tersedia di `D:\Laragon\www\laravel-13-phoenix\graphify-out\graph.json` (sekitar 29 MB), last write yang diperiksa 12 Oktober 2026 pukul 09:56:40.
- Sebelum implementasi awal, query lokal antara lain `FileManagerV2Storage storeChunk completeUpload existingIdempotentUpload uploadFile` dengan budget 1300. Source yang ditunjuk dibuka; hash delapan file relevan sesuai manifest graph sebelum perubahan.
- **Graph belum diperbarui setelah perubahan sesi ini.** Pengguna meminta update Graphify manual dan menunggu perintah eksplisit. Pertanyaan apakah sudah di-update bukan izin untuk mengubahnya.
- Graph dapat stale untuk service/controller/data upload yang sudah berubah. Gunakan graph sebagai peta historis dan verifikasi source aktif. Jangan full rebuild otomatis, jangan indeks backup/artifact/secret atau kirim graph privat keluar.
- Full folder upload ke R2 dengan empat paralel belum dilakukan/dibuktikan.
- Restart penuh Laragon dan pembuktian persistence pada startup berikutnya belum dilakukan.
- Tidak ada tindakan pada server aaPanel.

## Langkah lanjutan untuk chat berikutnya

1. Baca note ini dan kedua `qc\final-report.json`, lalu periksa ulang INI, settings, listener, PID, service, dan git status. Jangan menganggap PID/snapshot tetap berlaku.
2. Jika pengguna melanjutkan tes R2, gunakan File Manager V2 dengan 4 paralel, folder tujuan baru yang jelas, dan pantau log serta hasil jumlah/hash; jangan mengunggah data nyata tanpa scope yang diberikan pengguna.
3. Bila menguji restart Laragon, pilih saat upload tidak aktif, backup perubahan, pastikan satu instance, validasi delapan listener dan environment 0 setelah startup baru.
4. Untuk aaPanel, periksa versi PHP website, pool/config aktif, RAM tersisa, dan beban website lain sebelum memilih worker. Belum ada izin eksekusi remote dari pembahasan aaPanel ini.
5. Perbarui Graphify incremental untuk source aktif yang berubah hanya setelah pengguna memberi perintah eksplisit; simpan artifact di project-artifacts dan kecualikan backup/runtime/build/dependency/secret.
