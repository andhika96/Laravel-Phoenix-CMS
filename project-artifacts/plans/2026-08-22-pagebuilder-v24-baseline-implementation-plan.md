# Page Builder Elementor v2.4 Baseline Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Membuat clone fungsional Page Builder Elementor v2.4 yang sepenuhnya terisolasi dari source v2.3 tanpa memulai modular refactor.

**Architecture:** Seluruh code/version-owned assets v2.3 dicopy ke namespace/path v2.4 dengan transformasi token versi yang terkontrol. Infrastruktur Laravel generik tetap shared, sedangkan satu public dispatcher memilih renderer berdasarkan `editor_version`.

**Tech Stack:** Laravel 13, PHP/Pest-PHPUnit, Vue 3 SFC loader, JavaScript Node test runner, Vite, PowerShell.

**Spec:** `project-artifacts/plans/2026-08-22-pagebuilder-v24-baseline-design.md`

## Global Constraints

- v2.3 maintenance-only; jangan refactor, reset, clean, atau overwrite source v2.3.
- Jangan mengganti semua literal `2.3` secara global karena shapes JSON memuat angka SVG yang kebetulan mengandung `2.3`.
- Tidak ada dependency baru.
- Tidak ada page/database produksi yang dicopy, disimpan, atau dimutasi.
- Backup setiap file existing yang dimodifikasi ke `project-artifacts/backups/`.
- Semua plan, script, snapshot, dan QA berada di `project-artifacts/`.
- Jangan commit atau push tanpa instruksi eksplisit.
- Modular refactor tidak termasuk baseline ini.

---

### Task 1: Characterization dan isolation contracts

**Files:**
- Create: `tests/Feature/PageBuilderElementorV24BaselineIsolationTest.php`
- Create: `tests/pagebuilder-v24-baseline-isolation.test.mjs`
- Modify: `tests/Feature/PageBuilderEditorVersionMigrationTest.php`

**Interfaces:**
- Consumes: route registry Laravel, filesystem source aktif, `Page_Builder` model.
- Produces: executable acceptance contract untuk constant, route, clone tree, version guard, dan public dispatcher.

- [ ] **Step 1: Tambahkan PHP contract test** yang mengharapkan constant `EDITOR_VERSION_V24`, route family v2.4, controller namespace v2.4, shared public dispatcher, dan v2.4 asset/view/config tree.
- [ ] **Step 2: Tambahkan Node filesystem contract test** yang membandingkan daftar file aktif JS v2.3/v2.4 dan menolak marker internal v2.3 dalam tree v2.4.
- [ ] **Step 3: Jalankan focused tests untuk memastikan RED** karena constant, route, dan source v2.4 belum tersedia.

Run:

```text
php artisan test tests/Feature/PageBuilderElementorV24BaselineIsolationTest.php tests/Feature/PageBuilderEditorVersionMigrationTest.php --compact
node --test tests/pagebuilder-v24-baseline-isolation.test.mjs
```

Expected: gagal khusus karena contract v2.4 belum tersedia; v2.3 tetap hijau.

### Task 2: Backup dan immutable v2.3 snapshot

**Files:**
- Create: `project-artifacts/backups/pagebuilder-v24-baseline_<timestamp>/`
- Create: `project-artifacts/qa/pagebuilder-v24-baseline-20260822/v23-source-before.sha256`
- Create: `project-artifacts/scripts/clone-pagebuilder-v23-to-v24.ps1`

**Interfaces:**
- Consumes: source v2.3 aktif dan empat shared files.
- Produces: rollback copy, deterministic hash baseline, dan mechanical clone tool.

- [ ] **Step 1: Backup** `routes/web.php`, `routes/experimentalFeaturesWebv2.php`, `app/Models/Page_Builder/Page_Builder.php`, dan `tests/Feature/PageBuilderEditorVersionMigrationTest.php`.
- [ ] **Step 2: Rekam SHA-256** seluruh active version-owned source v2.3, mengecualikan `.bak` dan project artifacts.
- [ ] **Step 3: Buat script clone** dengan explicit source→target mapping dan ordered token replacements; abort jika target sudah ada.

### Task 3: Clone version-owned source dan integrate shared seams

**Files:**
- Create: seluruh path v2.4 dalam design mapping.
- Create: `routes/pagebuilder_elementor_v24.php`
- Create: `routes/pagebuilder_elementor_public.php`
- Create: `app/Http/Controllers/Web/PageBuilderElementor/PageBuilderElementorPublishedPageController.php`
- Modify: `routes/web.php`
- Modify: `routes/experimentalFeaturesWebv2.php`
- Modify: `app/Models/Page_Builder/Page_Builder.php`

**Interfaces:**
- Consumes: exact v2.3 behavior dan `Page_Builder.editor_version`.
- Produces: editor v2.4 route/API/runtime dan shared `GET /pages/{uri}` dispatcher.

- [ ] **Step 1: Jalankan clone script** untuk membuat source/test v2.4 tanpa menyentuh source v2.3.
- [ ] **Step 2: Tambahkan constant v2.4** pada shared model.
- [ ] **Step 3: Tambahkan route file v2.4** dengan prefix/name/controller v2.4 dan middleware sama seperti v2.3.
- [ ] **Step 4: Tambahkan public dispatcher** dan pindahkan satu public route keluar dari route file legacy.
- [ ] **Step 5: Jalankan focused tests** sampai seluruh Task 1 contract hijau.

### Task 4: Clone complete test suite dan parity repair

**Files:**
- Create: pasangan seluruh `PageBuilderElementorV24*.php`, `pagebuilder-v24-*.test.mjs`, dan prototype/static test v2.4.
- Modify: hanya file v2.4 yang gagal karena transformasi versi mekanis.

**Interfaces:**
- Consumes: source v2.4 dari Task 3.
- Produces: behavior parity executable dengan v2.3.

- [ ] **Step 1: Jalankan seluruh Node v2.4 tests** dan klasifikasikan failure sebagai clone defect atau expectation versi.
- [ ] **Step 2: Jalankan seluruh PHP V24 tests** dan perbaiki hanya boundary v2.4.
- [ ] **Step 3: Jalankan regression suite v2.3** untuk memastikan baseline lama tidak berubah.
- [ ] **Step 4: Jalankan PHP syntax checks dan Vite build**.

Commands:

```text
node --test tests/pagebuilder-v24-*.test.mjs tests/pagebuilder-editor-v24-production-static.test.mjs tests/pagebuilder-editor-redesign-v24-static.test.mjs
php artisan test --compact --filter=PageBuilderElementorV24
node --test tests/pagebuilder-v23-*.test.mjs tests/pagebuilder-editor-v23-production-static.test.mjs tests/pagebuilder-editor-redesign-v23-static.test.mjs
php artisan test --compact --filter=PageBuilderElementorV23
npm.cmd run build
git diff --check
```

### Task 5: Runtime, immutable-source, dan graph verification

**Files:**
- Create: `project-artifacts/qa/pagebuilder-v24-baseline-20260822/QA_REPORT.md`
- Create: screenshot v2.4/v2.3 runtime pada folder QA yang sama.
- Update generated: `graphify-out/graph.json` melalui incremental update.

**Interfaces:**
- Consumes: integrated v2.4 source.
- Produces: final evidence bahwa v2.4 hidup dan v2.3 tidak berubah.

- [ ] **Step 1: Bandingkan hash source v2.3** dengan snapshot Task 2; expected identical.
- [ ] **Step 2: Audit marker silang**; expected tidak ada internal v2.3 marker di source v2.4.
- [ ] **Step 3: Buka create v2.4 dan v2.3** pada browser authenticated, hard reload, screenshot, cek DOM/console, tanpa Save/Reset.
- [ ] **Step 4: Jalankan full focused verification fresh** dan rekam exit code/result.
- [ ] **Step 5: Update Graphify incremental** dengan `.bak`, artifacts, vendor, node_modules, cache, dan generated files tetap dikecualikan.
- [ ] **Step 6: Review acceptance criteria** dan laporkan Confirmed/Partial/Unverified secara jujur.

## Plan self-review

- Spec coverage: seluruh file boundary, route, persistence, public renderer, dataset, parity, isolation, browser, dan rollback tercakup.
- Placeholder scan: tidak ada placeholder atau task tanpa command/output target.
- Type consistency: seluruh penamaan menggunakan V24 / v24 / 2.4 sesuai lapisan.
- Scope check: modular refactor dan data migration tetap terpisah dari baseline clone.
- Commit steps sengaja tidak dimasukkan karena user belum mengizinkan commit/push.
