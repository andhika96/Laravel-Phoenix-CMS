# Project Artifacts Cleanup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Do not dispatch subagents unless the user explicitly requests them.

**Goal:** Membersihkan root Phoenix CMS dan memusatkan backup, mockup non-runtime, preview, serta bukti QA tanpa menambah regresi pada Laravel, Page Builder v2.3, File Manager V2, tema aktif, atau Graphify.

**Architecture:** Pertahankan path konvensional runtime/tooling (`node_modules`, `graphify-out`, `public`, `vendor`, dan `docs`) di root. Pusatkan artefak non-runtime di `project-artifacts`, simpan seluruh backup untracked dengan hash sebelum menghapus lokasi lama, dan gunakan Git sebagai recovery untuk backup tracked historis.

**Tech Stack:** Windows PowerShell, Git, Laravel 13/PHPUnit, Node.js test runner, npm/Vite, Graphify, browser QA read-only.

**Spec:** `docs/superpowers/specs/2026-08-22-project-artifacts-cleanup-design.md`

## Global Constraints

- Kerjakan in-place pada checkout `D:\Laragon\www\laravel-13-phoenix` di branch `main` yang sengaja dirty; user telah menyetujui eksekusi pada 2026-08-22.
- Jangan menjalankan `git clean`, `git reset`, broad checkout, staging, commit, push, atau perubahan remote.
- Jangan menghapus atau memindahkan source produksi, root `node_modules`, `vendor`, `frankenphp`, `Caddyfile`, graph aktif, cache incremental Graphify, atau final `public/mockups` yang dipakai test/URL.
- Sebelum recursive move/delete, resolve absolute path dan pastikan setiap target tetap berada di bawah project root yang tepat.
- Backup untracked harus sudah tersalin ke lokasi canonical dan lolos verifikasi count, size, dan SHA-256 sebelum lokasi asal dihapus.
- Enam failure Node tema Arunika, satu failure PHP shell auth redirect, dan failure `route:list` karena missing `Testing_Controller` adalah baseline; tidak boleh ada failure baru.
- Jangan menekan Save, Reset, Apply Dataset, atau melakukan form submission eksternal selama browser QA.

---

### Task 1: Buat pusat artefak dan amankan current-work

**Files:**

- Create: `project-artifacts/README.md`
- Create local/ignored: `project-artifacts/backups/current-work/`
- Create local/ignored: `project-artifacts/backups/database/`
- Create local/ignored: `project-artifacts/backups/manifests/`
- Modify: `.gitignore`
- Modify: `.graphifyignore`

**Interfaces:**

- Consumes: daftar tracked, untracked, ignored, ukuran, mtime, dan SHA-256 dari checkout aktif.
- Produces: backup canonical terverifikasi dan manifest target yang dipakai semua task destructive berikutnya.

- [ ] **Step 1: Ambil inventory fresh dan simpan manifest lokal**

Gunakan `git -c core.quotePath=false ls-files`, `git ls-files --others --exclude-standard`, serta `Get-FileHash`. Manifest berisi `OriginalPath`, `BackupPath`, `GitState`, `Bytes`, `LastWriteTime`, dan `SHA256`.

- [ ] **Step 2: Validasi target canonical berada di project root**

Resolve `project-artifacts/backups` dan pastikan `StartsWith('D:\Laragon\www\laravel-13-phoenix\project-artifacts\backups\')` sebelum menulis file.

- [ ] **Step 3: Salin seluruh backup untracked ke current-work**

Pertahankan relative directory asal di bawah `current-work`; backup root masuk `current-work/root`. Jangan gunakan glob untuk destination dan jangan overwrite collision tanpa hash equality.

- [ ] **Step 4: Verifikasi salinan sebelum mutasi asal**

Bandingkan jumlah file, total byte, dan SHA-256 setiap pasangan source/destination. Jika satu pasangan berbeda, hentikan cleanup dan jangan hapus source mana pun.

- [ ] **Step 5: Tambahkan struktur tracking minimal**

`project-artifacts/README.md` menjelaskan `backups`, `mockups`, `qa`, dan `previews`. `.gitignore` meng-ignore hanya `project-artifacts/backups/**` serta build/cache mockup; `.graphifyignore` mengecualikan backup, binary QA, dan output build tanpa mengecualikan source mockup.

- [ ] **Step 6: Checkpoint Task 1**

Jalankan pemeriksaan backup count/hash, `git diff -- .gitignore .graphifyignore`, dan `git diff --check`. Tidak ada commit.

---

### Task 2: Hapus backup tracked historis dan sampah root yang terbukti tidak dipakai

**Files:**

- Delete: tracked basename yang cocok `.bak*`, `.backup-*`, `.bad-current-*`
- Delete: tracked content di `backup/` dan `backups/`
- Delete: `{console.error(e.message)`, `JSON.stringify({open`, `JSON.stringify({viewport`, dua file `k.toLowerCase().includes(...)`, `.rnd`, dan `test.php`
- Delete: `DLaragonwwwlaravel-12-phoenixstoragelogs/laravel.log`
- Move local/ignored: `laravel_12_phoenix_17082026.sql` ke `project-artifacts/backups/database/`
- Delete ignored: `nginx-access.log`, `nginx-error.log`

**Interfaces:**

- Consumes: manifest Task 1 dan daftar exact tracked paths dari Git.
- Produces: satu backup tree canonical dan root tanpa file kecelakaan terminal/log lama.

- [ ] **Step 1: Bangun daftar exact deletion candidates**

Gunakan daftar Git, bukan recursive filename glob dari filesystem. Keluarkan `project-artifacts/**`, source aktif, lockfile aktif, dan final public mockup dari kandidat.

- [ ] **Step 2: Audit path dan recovery**

Untuk setiap kandidat tracked, pastikan blob tersedia di `HEAD` melalui `git cat-file -e HEAD:<path>`. Untuk setiap kandidat untracked, pastikan hash destination canonical cocok.

- [ ] **Step 3: Hapus kandidat dengan `Remove-Item -LiteralPath`**

Resolve dan validasi setiap absolute path masih berada di project root; jalankan penghapusan satu path exact per iterasi PowerShell. Jangan menggunakan `git clean` atau recursive wildcard deletion.

- [ ] **Step 4: Hapus direktori kosong `backup` dan `backups`**

Hanya hapus folder setelah `Get-ChildItem -Force` mengembalikan nol item dan resolved path sama persis dengan dua folder root tersebut.

- [ ] **Step 5: Verifikasi tidak ada backup di luar canonical**

Cari tracked/untracked basename backup dan laporkan setiap exception. Hasil yang diterima hanya backup di `project-artifacts/backups` dan backup spec sementara yang belum dipindahkan pada Task 3.

- [ ] **Step 6: Checkpoint Task 2**

Jalankan `git status --short`, ringkasan deletion per top-level folder, pencarian referensi file kecelakaan, dan `git diff --check`. Tidak ada commit.

---

### Task 3: Pindahkan mockup, laporan, preview, dan deployment docs

**Files:**

- Move: `mockups/file-manager-v2-ckbox` ke `project-artifacts/mockups/file-manager-v2-ckbox`
- Move: `mockups/pagebuilder-v23-responsive-hero-prototype` ke `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype`
- Delete: `mockups/lost-in-signal-404`
- Move: `output/**`, retained `tmp/**`, `docs/qa/**`, root design-QA Markdown/PNG, dan screenshot root ke `project-artifacts/qa/**` atau `project-artifacts/previews/**`
- Move: `REVERB_NOTIFICATION_SETUP.md` dan `laragon-nginx-reverb.conf` ke `docs/deployment/reverb/`
- Modify: tests dan docs yang merujuk root mockup lama
- Preserve: final `public/mockups` dan required assets; delete hanya backup variants di dalamnya

**Interfaces:**

- Consumes: active path references dari tests/docs dan source mockup bersih.
- Produces: satu tree artefak tracked, path test yang valid, dan public mockup URL yang tidak berubah.

- [ ] **Step 1: Buang output regenerable mockup sebelum move**

Pada File Manager mockup, hapus exact `node_modules`, `dist`, dan `dist.bak_*` setelah resolved-target validation. Pada Responsive Hero, hapus backup internal tetapi pertahankan `AGENTS.md`, source, scripts, public assets, package manifest, dan lockfile.

- [ ] **Step 2: Pindahkan dua source mockup**

Gunakan `Move-Item -LiteralPath` ke destination exact dan verifikasi jumlah file tracked/source sebelum dan sesudah.

- [ ] **Step 3: Perbarui referensi path**

Ganti `mockups/pagebuilder-v23-responsive-hero-prototype` menjadi `project-artifacts/mockups/pagebuilder-v23-responsive-hero-prototype` pada test, spec, plan, README, serta `.graphifyignore`. Ganti instruksi File Manager mockup ke path baru. `public/mockups` tidak diubah.

- [ ] **Step 4: Pindahkan Form Row Grid evidence secara eksplisit**

Markdown aktif masuk `project-artifacts/qa/pagebuilder/form-row-grid/reports`; PNG comparison/implementation/final/Row Span/Steps masuk `project-artifacts/qa/pagebuilder/form-row-grid/previews`; backupnya tetap di current-work. Perbarui referensi gambar absolut lama.

- [ ] **Step 5: Pusatkan evidence lain tanpa kehilangan bukti tracked**

Pindahkan screenshot QA tracked dari `output`, root, `tmp`, dan `docs/qa` menurut fitur. Hapus log `.playwright-cli`, YAML session snapshot, dan duplicate temp yang tidak direferensikan; Git tetap menjadi recovery historis.

- [ ] **Step 6: Bersihkan `public/mockups` secara konservatif**

Pertahankan final HTML/assets yang direferensikan tests, design QA, atau URL aktif. Hapus hanya basename `.bak*`/`.backup-*` setelah source search membuktikan nol referensi.

- [ ] **Step 7: Pindahkan deployment docs**

Pindahkan Reverb guide/config ke `docs/deployment/reverb`, lalu pastikan tidak ada referensi root lama.

- [ ] **Step 8: Verifikasi mockup dari path baru**

Jalankan File Manager mockup static contract, Responsive Hero self-check, static public mockup tests, dan `rg` untuk path lama. Untuk build reproducibility, jalankan `npm ci`, build, dan test pada masing-masing mockup lalu hapus kembali `node_modules`/`dist` hasil verifikasi.

- [ ] **Step 9: Checkpoint Task 3**

Jalankan `git diff --check`, `git status --short`, dan pemeriksaan semua referenced files exist. Tidak ada commit.

---

### Task 4: Pangkas Graphify dan cache lokal tanpa memutus tooling

**Files:**

- Preserve: `graphify-out/graph.json`, `manifest.json`, `cache`, `memory`, `reflections`, `.graphify_python`, `.graphify_root`, current report/analysis/labels
- Delete local/ignored: dated Graphify snapshots, backup graph/report/manifest, dan stale intermediate extraction/detection files
- Delete local/ignored: `.npm-cache`, `.playwright-daemon`, `.tmp-chrome-profile`, empty `work`, dan empty `.worktrees`
- Conditional delete: `.playwright-cli` hanya bila tidak ada active session/process

**Interfaces:**

- Consumes: Graphify current graph/cache and repository hooks/scripts.
- Produces: smaller local graph output yang tetap queryable dan updateable.

- [ ] **Step 1: Catat health dan required paths sebelum prune**

Jalankan Graphify query/reflect ringan, test hooks, dan catat hash/size `graph.json` serta manifest.

- [ ] **Step 2: Bangun exact prune list**

Target hanya immediate dated directories `YYYY-MM-DD`, basename backup Graphify, serta stale intermediate yang tercatat di spec. Jangan memasukkan current graph/cache/memory/reflections/interpreter/root metadata.

- [ ] **Step 3: Validasi dan hapus exact Graphify targets**

Resolve setiap target, pastikan parent exact `graphify-out`, lalu gunakan `Remove-Item -LiteralPath`; recursive hanya untuk dated snapshot directory yang sudah tervalidasi.

- [ ] **Step 4: Bersihkan local cache yang regenerable**

Cek proses npm/Playwright/Chrome dahulu. Hapus hanya cache idle; pertahankan sesi yang sedang dipakai sampai browser QA selesai.

- [ ] **Step 5: Verifikasi Graphify setelah prune**

Jalankan `graphify reflect --if-stale`, query terhadap Page Builder/File Manager, test hooks, dan incremental update setelah seluruh path source sudah final. Jika update menolak shrink karena path lama yang memang sudah dipindahkan keluar corpus, cocokkan diagnostic dengan daftar move manifest lalu jalankan rebuild `--force --code-only --no-viz`; jangan memakai force untuk error lain.

- [ ] **Step 6: Checkpoint Task 4**

Bandingkan ukuran sebelum/sesudah, cek required file existence, `git status`, dan `git diff --check`. Tidak ada commit.

---

### Task 5: Verifikasi regresi dan runtime read-only

**Files:**

- Verify only: source Laravel/CMS/Page Builder/File Manager dan artefak yang dipindahkan
- Update only if required by proven broken reference: exact test/doc/config path responsible

**Interfaces:**

- Consumes: combined cleanup state.
- Produces: evidence matrix `requirement -> path change -> focused check -> broad check -> status`.

- [ ] **Step 1: Verifikasi struktur dan referensi**

Pastikan root `backup`, `backups`, `mockups`, `output`, dan `tmp` sudah tidak ada; semua referenced paths exist; backup current-work cocok manifest; dan tidak ada backup di luar canonical.

- [ ] **Step 2: Verifikasi npm dan builds**

Jalankan `npm.cmd ls --depth=0`, `npm.cmd prune --dry-run --json`, `npm.cmd run build`, dan `npm.cmd run build:filemanager-v2`.

- [ ] **Step 3: Verifikasi focused suites**

Jalankan `node --test tests/pagebuilder-v23-*.test.mjs`, `php artisan test tests/Feature/FileManagerV2 --compact`, focused Form v2.3 PHP suite, static mockup tests, mockup contracts/self-checks, dan Graphify hook tests.

- [ ] **Step 4: Verifikasi broad suites terhadap baseline**

Jalankan full PHP dan full Node suite. Terima hanya failure set baseline yang sama: satu PHP shell auth redirect dan enam Node Arunika contract failures; failure baru menghentikan completion.

- [ ] **Step 5: Verifikasi boot, syntax, dan diff**

Jalankan `php artisan about`, route checks terfokus, `php -l`/`node --check` pada file yang disentuh, `git diff --check`, serta audit tidak ada staged changes.

- [ ] **Step 6: Browser QA read-only**

Periksa dashboard, Page Builder v2.3, File Manager V2, published MG5 page, dan final public mockup URL. Catat HTTP/DOM/console evidence tanpa Save/Reset/Apply Dataset/submission.

- [ ] **Step 7: Final reconciliation**

Laporkan file dibaca, memori/Graphify, backup canonical, moved/deleted/modified files, ukuran yang dibebaskan, semua command dan hasil, baseline failures, runtime/static/unverified boundaries, serta status Graphify. Tidak ada commit atau push.
