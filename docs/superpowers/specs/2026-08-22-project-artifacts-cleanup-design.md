# Project Artifacts Cleanup Design

**Tanggal:** 2026-08-22  
**Project:** `D:\Laragon\www\laravel-13-phoenix`  
**Status:** disetujui secara prinsip melalui instruksi `Gas`; implementasi menunggu review spec ini.  

## Tujuan

Merapikan root repository tanpa merusak Laravel 13, Phoenix CMS, Page Builder v2.3, File Manager V2, tema aktif, tooling Graphify, atau runtime lokal. Backup lokal, mockup non-runtime, laporan QA, preview, dan screenshot dipusatkan di `project-artifacts/`. File yang terbukti regenerable, tidak direferensikan, atau sudah aman di Git dihapus.

## Kondisi awal yang telah diverifikasi

- Worktree `main` sengaja dirty oleh pekerjaan Form Page Builder v2.3 dan perubahan lama milik user.
- `backup/` berisi 224 file sekitar 9,4 MB; `backups/` berisi 386 file sekitar 37,8 MB.
- Terdapat 2.335 file backup tracked sekitar 160 MB di seluruh repository dan 167 backup untracked terbaru sekitar 19,6 MB.
- `graphify-out/` berisi 1.269 file sekitar 326 MB, termasuk graph aktif, cache incremental, snapshot bertanggal, backup graph, dan intermediate lama.
- `mockups/` berisi 4.623 file sekitar 292 MB. Mayoritas ukuran berasal dari `node_modules`, `dist`, dan banyak backup `dist` milik mockup File Manager V2.
- Root `node_modules/` berisi 4.947 file sekitar 86 MB. `npm ls --depth=0` lulus dan `npm prune --dry-run` melaporkan nol removal.
- `output/` dan `tmp/` tidak direferensikan runtime; keduanya berisi bukti QA dan file sementara.
- `frankenphp` berukuran sekitar 66,8 MB, di-ignore Git, memiliki `Caddyfile`, dan merupakan instalasi runtime yang sengaja dibuat user.

## Pendekatan yang dipilih

### A. `project-artifacts/` sebagai pusat artefak — dipilih

Folder non-runtime dipusatkan tanpa memindahkan direktori yang menjadi kontrak Laravel, npm, atau Graphify. Keuntungannya adalah root menjadi bersih, artefak mudah ditemukan, dan build/runtime tetap memakai layout standar.

### B. Memasukkan semua ke `docs/` — tidak dipilih

Pendekatan ini mencampur dokumentasi yang dilacak Git dengan backup lokal dan output regenerable. `docs/superpowers` juga merupakan path konvensional skill, sehingga `docs/` tetap dipertahankan sebagai dokumentasi.

### C. Hanya menghapus cache tanpa merapikan path — tidak dipilih

Pendekatan ini berisiko paling rendah tetapi tidak memenuhi permintaan satu folder terpusat dan tetap menyisakan `backup`, `backups`, `mockups`, `output`, `tmp`, serta laporan QA di root.

## Struktur canonical

```text
project-artifacts/
├── README.md
├── backups/
│   ├── current-work/
│   └── database/
├── mockups/
│   ├── file-manager-v2-ckbox/
│   └── pagebuilder-v23-responsive-hero-prototype/
├── qa/
│   ├── file-manager-v2/
│   ├── mg5-showcase/
│   ├── pagebuilder/
│   └── themes/
└── previews/
```

Aturan pelacakan:

- `project-artifacts/README.md`, source mockup, laporan QA aktif, dan preview yang dipertahankan tetap tracked.
- `project-artifacts/backups/**` di-ignore Git karena merupakan rollback lokal.
- Backup mempertahankan struktur path asal agar nama file yang sama dari folder berbeda tidak bertabrakan.
- Manifest lokal mencatat path asal, path tujuan, ukuran, timestamp, status Git, dan SHA-256 untuk backup yang dipindahkan.

## Klasifikasi pertahankan

Direktori dan file berikut tetap pada lokasi teknisnya:

- Seluruh struktur Laravel aktif: `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `stubs`, `tests`, `vendor`, dan file manifest/config utamanya.
- Root `node_modules`, `package.json`, `package-lock.json`, `vite.config.js`, serta `vite.filemanager-v2.config.js` karena dipakai build Laravel dan File Manager V2.
- `graphify-out` di root karena `.githooks` dan `scripts/graphify` merujuk path tersebut secara eksplisit.
- `graphify-out/graph.json`, `manifest.json`, `cache/`, `memory/`, `reflections/`, `.graphify_python`, `.graphify_root`, report/analysis/labels aktif, dan metadata current yang diperlukan query/update.
- `frankenphp` dan `Caddyfile` karena merupakan runtime lokal yang sengaja diinstal.
- `docs/` sebagai dokumentasi developer dan lokasi canonical `docs/superpowers`.
- Final `public/mockups` dan asset yang masih direferensikan test atau URL browser, termasuk prototype Page Builder v2.3, prototype legacy pembanding, Theme Manager, dan konfigurasi Arunika aktif.
- `.githooks`, `scripts/graphify`, `.gitignore`, `.graphifyignore`, serta config deployment yang masih relevan.

## Klasifikasi pindah

- Seluruh 167 backup untracked terbaru dipindahkan ke `project-artifacts/backups/current-work/` dengan struktur path asal dan hash yang terverifikasi.
- `laravel_12_phoenix_17082026.sql` dipindahkan ke `project-artifacts/backups/database/` dan tidak lagi dilacak sebagai file root.
- Source bersih `mockups/file-manager-v2-ckbox` dipindahkan ke `project-artifacts/mockups/file-manager-v2-ckbox` setelah `node_modules`, `dist`, dan backup build dibuang.
- Source `mockups/pagebuilder-v23-responsive-hero-prototype` dipindahkan ke `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype` setelah backup internal dibuang.
- Referensi test, README mockup, spec/plan aktif, dan `.graphifyignore` diperbarui ke path mockup baru.
- Bukti visual tracked dari `output/`, root PNG, `tmp/`, dan `docs/qa/` dipindahkan ke subfolder `project-artifacts/qa` atau `project-artifacts/previews` berdasarkan fitur. Log Playwright dan snapshot YAML tidak dipertahankan.
- `design-qa.md`, laporan Form Row Grid, dan PNG QA terbaru dipindahkan ke `project-artifacts/qa/pagebuilder/`; referensi aktif diperbarui.
- `REVERB_NOTIFICATION_SETUP.md` dan `laragon-nginx-reverb.conf` dipindahkan ke `docs/deployment/reverb/` karena merupakan dokumentasi/config deployment, bukan entrypoint root.

### Berkas Form Row Grid yang terlihat pada screenshot user

- Semua `design-qa-form-row-grid*.bak_*` dipindahkan ke `project-artifacts/backups/current-work/root/` dan diverifikasi dengan SHA-256; tidak dihapus sebagai file sementara.
- `design-qa-form-row-grid.md`, `design-qa-form-row-map.md`, `design-qa-form-row-span.md`, serta `design-qa-form-sidebar-accordion.md` dipindahkan ke `project-artifacts/qa/pagebuilder/form-row-grid/reports/`.
- PNG dengan nama `design-qa-form-row-grid-*`, `design-qa-form-row-span-*`, dan `design-qa-form-steps-*` dipindahkan ke `project-artifacts/qa/pagebuilder/form-row-grid/previews/`.
- Laporan aktif diperbarui agar merujuk path PNG baru; pemeriksaan akhir memastikan tidak ada referensi absolut lama ke root project.

## Klasifikasi hapus

- Semua backup tracked lama (`*.bak*`, `*.backup-*`, `*.bad-current-*`, serta isi tracked `backup/` dan `backups/`). Versi tersebut tetap dapat dipulihkan dari Git.
- Folder root `backup/` dan `backups/` setelah seluruh backup untracked berhasil dipindahkan dan diverifikasi.
- Backup `dist`, backup package manifest, dan build `dist` pada mockup File Manager V2; semuanya dapat dibuat ulang dari source dan lockfile.
- `node_modules` milik mockup File Manager V2 setelah build/test verifikasi; root `node_modules` tidak dihapus.
- Snapshot bertanggal, backup graph/report/manifest, serta intermediate Graphify lama yang tidak dipakai hooks atau incremental cache. Graph aktif dan cache incremental dipertahankan.
- Cache/tool state regenerable: `.npm-cache`, `.playwright-daemon`, `.tmp-chrome-profile`, dan folder kosong `work`/`.worktrees`. `.playwright-cli` hanya dibuang setelah dipastikan tidak ada sesi QA yang sedang dipakai.
- Log lama `nginx-access.log`, `nginx-error.log`, serta folder salah-encode `DLaragonwwwlaravel-12-phoenixstoragelogs`.
- File kecelakaan terminal/browser yang tidak mempunyai referensi: `{console.error(e.message)`, `JSON.stringify({open`, `JSON.stringify({viewport`, dua file `k.toLowerCase().includes(...)`, `.rnd`, dan `test.php`.
- Backup lama di `public/mockups`; hanya final mockup dan asset aktif yang dipertahankan.
- `mockups/lost-in-signal-404` karena tidak mempunyai referensi source, test, route, atau dokumentasi fitur aktif.
- File sementara dan duplikat QA yang tidak mempunyai referensi; versi tracked tetap dapat dipulihkan dari Git.

## Perubahan referensi

Pemindahan tidak boleh meninggalkan path rusak. Implementasi harus:

1. Mengganti referensi `mockups/pagebuilder-v23-responsive-hero-prototype` pada test, spec, plan, dan dokumentasi aktif.
2. Mengganti instruksi `cd mockups/file-manager-v2-ckbox` pada README mockup.
3. Memperbarui `.graphifyignore` untuk `project-artifacts/backups`, hasil build mockup, QA binary, serta path baru mockup.
4. Memperbarui `.gitignore` agar hanya backup lokal dan output regenerable di dalam `project-artifacts` yang di-ignore; source mockup dan README tetap tracked.
5. Mempertahankan `public/mockups/...` untuk seluruh URL/test aktif dan memastikan backup variants tidak lagi direferensikan.
6. Menjalankan pencarian akhir terhadap path lama `backup/`, `backups/`, `mockups/`, `output/`, dan `tmp/`; hanya konfigurasi migrasi/ignore yang disengaja boleh tersisa.

## Prosedur aman dan rollback

1. Ambil ulang `git status`, daftar file target, ukuran, dan hash sebelum mutasi.
2. Validasi seluruh absolute path target berada di bawah `D:\Laragon\www\laravel-13-phoenix` sebelum move/delete recursive.
3. Buat backup timestamped untuk setiap file konfigurasi aktif yang dimodifikasi, kemudian pindahkan backup tersebut langsung ke folder canonical.
4. Salin backup untracked ke lokasi canonical terlebih dahulu, cocokkan jumlah/ukuran/SHA-256, baru hapus lokasi asal.
5. Kerjakan per kelompok: backup, mockup, QA, Graphify, cache/sampah root. Verifikasi Git diff dan referensi setelah setiap kelompok.
6. Jangan menggunakan `git clean`, `git reset`, broad checkout, staging, commit, atau push.
7. Jika jumlah/hash tidak cocok atau test relevan mengalami regresi baru, hentikan kelompok tersebut dan pulihkan dari backup canonical atau Git sesuai status file.

## Baseline sebelum cleanup

- `npm.cmd run build`: lulus, 58 modules.
- `npm.cmd run build:filemanager-v2`: lulus, 89 modules.
- `node --test tests/pagebuilder-v23-*.test.mjs`: 226 lulus, 0 gagal.
- `php artisan test tests/Feature/FileManagerV2 --compact`: 51 lulus, 298 assertions.
- Mockup File Manager V2 static contract: lulus.
- Responsive Hero prototype self-check: lulus.
- Full PHP suite: 506 lulus, 1 baseline failure pada `PageBuilderElementorV23ShellTest` karena expected 200 menerima redirect auth 302.
- Full Node suite: 375 lulus, 6 baseline failure pada kontrak tema Arunika lama.
- `php artisan route:list --except-vendor`: baseline failure karena `App\Http\Controllers\Api\v1\Testing\Testing_Controller` tidak tersedia.

## Verifikasi setelah cleanup

Verifikasi akhir wajib membedakan regresi baru dari baseline:

1. Cek jumlah/hash seluruh backup current-work yang dipertahankan.
2. Cek tidak ada file backup di luar `project-artifacts/backups` dan tidak ada `backup/` atau `backups/` root.
3. Jalankan pencarian referensi path lama dan pemeriksaan link/file existence untuk test serta dokumentasi aktif.
4. Jalankan `npm ls --depth=0`, `npm prune --dry-run`, build Laravel, dan build File Manager V2.
5. Jalankan mockup File Manager V2 contract/build dan Responsive Hero self-check/build dari path baru; dependency sementara hasil verifikasi dibuang kembali.
6. Jalankan seluruh Page Builder v2.3 Node suite, seluruh File Manager V2 PHP suite, focused Form PHP suite, static mockup tests, dan test hooks Graphify.
7. Jalankan full PHP dan Node suite; hasil boleh tetap memiliki baseline failure yang sama, tetapi tidak boleh menambah failure baru.
8. Jalankan `php artisan about`, pemeriksaan route terfokus, `php -l`/`node --check` yang relevan, serta `git diff --check`.
9. Jalankan Graphify update/query dari `graphify-out` yang telah dirapikan dan pastikan hooks masih menemukan graph/interpreter.
10. Lakukan browser QA read-only pada dashboard, Page Builder v2.3, File Manager V2, published MG5 page, dan final public mockup URL. Jangan menekan Save, Reset, Apply Dataset, atau mengirim aksi eksternal.

## Batas scope

- Tidak memperbaiki enam baseline failure tema Arunika, baseline auth redirect pada shell test, atau missing `Testing_Controller` kecuali cleanup terbukti menyebabkan perubahan baru.
- Tidak mengubah schema database, data tersimpan, dependency versions, source feature behavior, atau package manifest selain path/tooling yang benar-benar diperlukan.
- Tidak menghapus `vendor`, root `node_modules`, `frankenphp`, graph aktif, current Graphify cache, source produksi, final public mockup, atau perubahan dirty worktree milik user.
- Tidak commit atau push tanpa instruksi eksplisit.
