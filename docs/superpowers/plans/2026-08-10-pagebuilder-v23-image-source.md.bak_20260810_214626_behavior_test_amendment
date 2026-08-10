# Page Builder v2.3 Image Source Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menambahkan pilihan sumber gambar `CKFinder` atau `External URL` pada widget Image Page Builder Elementor v2.3 tanpa mengubah v2.0, dengan `settings.src` tetap menjadi nilai kanonis untuk canvas dan frontend.

**Architecture:** Perubahan dibatasi pada panel Content milik widget Image v2.3. Properti opsional `settings.imageSource` hanya mengendalikan kontrol editor; nilai kosong atau tidak dikenal selalu diperlakukan sebagai `ckfinder` untuk kompatibilitas node lama. Kedua mode menulis ke `settings.src`, sehingga `Canvas.vue`, normalizer, penyimpanan, dan Blade renderer yang sudah menggunakan `src` tidak memerlukan cabang baru.

**Tech Stack:** Vue single-file components, Node.js built-in test runner, Laravel/PHP feature tests, Chrome runtime QA, Graphify lokal.

## Global Constraints

- Jangan mengubah file apa pun di `public/js/pagebuilder_elementor/` (v2.0).
- Jangan menekan tombol Save di Page Builder selama browser QA maupun saat melanjutkan pembuatan halaman MG5 GT.
- Jangan menghapus atau menimpa perubahan pengguna yang tidak terkait.
- Buat backup bertimestamp sebelum mengubah setiap file yang sudah ada; jangan stage atau commit file `.bak_*`.
- Jangan menambah Caption, Image Resolution, Link, Lightbox, proxy, downloader, atau validasi jaringan server-side pada scope ini.
- Pertahankan URL mentah pada `settings.src`; kegagalan hotlink/CORS dari host eksternal ditampilkan apa adanya dan tidak diproksi.
- Jangan stage atau commit `graphify-out`.

---

## Task 1: Kunci kontrak Image Source v2.3 dengan focused regression test

**Files:**

- Create: `tests/pagebuilder-v23-basic-image-source.test.mjs`
- Read-only contract checks: `public/js/pagebuilder_elementor_v23/widgets/basic/image/Canvas.vue`
- Read-only contract checks: `resources/views/pagebuilder_elementor_v23/widgets/basic/image.blade.php`
- Isolation check: `public/js/pagebuilder_elementor/widgets/basic/image/Settings.vue`

- [ ] **Step 1: Buat test yang mula-mula gagal**

Tambahkan test Node berikut. Test UI harus memeriksa label, dua nilai select, fallback node lama, conditional controls, binding URL ke `settings.src`, serta isolasi v2.0. Test yang sama juga mengunci bahwa canvas dan Blade tetap membaca `settings.src`.

```js
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const testDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(testDir, '..');
const read = (path) => readFileSync(join(rootDir, path), 'utf8');

const settingsV23 = read('public/js/pagebuilder_elementor_v23/widgets/basic/image/Settings.vue');
const settingsV20 = read('public/js/pagebuilder_elementor/widgets/basic/image/Settings.vue');
const canvasV23 = read('public/js/pagebuilder_elementor_v23/widgets/basic/image/Canvas.vue');
const rendererV23 = read('resources/views/pagebuilder_elementor_v23/widgets/basic/image.blade.php');

test('v2.3 Image offers isolated CKFinder and External URL source controls', () => {
  assert.match(settingsV23, />Image Source<\/label>/);
  assert.match(settingsV23, /<option value="ckfinder">CKFinder<\/option>/);
  assert.match(settingsV23, /<option value="url">External URL<\/option>/);
  assert.match(settingsV23, /imageSource\(\).*=== 'url' \? 'url' : 'ckfinder'/s);
  assert.match(settingsV23, /setImageSource\(value\).*value === 'url' \? 'url' : 'ckfinder'/s);
  assert.match(settingsV23, /v-if="imageSource === 'ckfinder'"/);
  assert.match(settingsV23, />Image URL<\/label>/);
  assert.match(settingsV23, /type="url"[^>]*v-model\.trim="node\.settings\.src"/);
  assert.match(settingsV23, /placeholder="https:\/\/example\.com\/image\.jpg"/);
  assert.doesNotMatch(settingsV20, />Image Source<\/label>/);
});

test('v2.3 Image keeps settings.src as the canvas and frontend contract', () => {
  assert.match(canvasV23, /:src="src"/);
  assert.match(canvasV23, /settings\?\.src/);
  assert.match(rendererV23, /src="\{\{ \$settings\['src'\] \?\? '' \}\}"/);
});
```

- [ ] **Step 2: Jalankan focused test dan pastikan RED-nya tepat**

Run:

```powershell
node --test tests/pagebuilder-v23-basic-image-source.test.mjs
```

Expected: FAIL pada assertion `Image Source`, karena panel v2.3 saat ini hanya merender media picker CKFinder.

- [ ] **Step 3: Periksa diff test**

Run:

```powershell
git diff -- tests/pagebuilder-v23-basic-image-source.test.mjs
git status --short
```

Expected: hanya test baru yang relevan; source produksi belum berubah.

## Task 2: Implementasikan selector CKFinder / External URL secara minimal

**Files:**

- Modify: `public/js/pagebuilder_elementor_v23/widgets/basic/image/Settings.vue:9-13,53-57`
- Test: `tests/pagebuilder-v23-basic-image-source.test.mjs`

**Editor interface:**

- Input state: `node.settings.imageSource`, `node.settings.src`, `node.settings.alt`.
- Allowed source values: `ckfinder`, `url`.
- Compatibility fallback: selain nilai persis `url` diperlakukan sebagai `ckfinder`.
- Output state: selector menyimpan mode ke `node.settings.imageSource`; CKFinder dan input URL sama-sama menyimpan image source ke `node.settings.src`.
- Existing services: `editor.chooseMedia(node.settings, 'src')` dan `editor.clearMedia(node.settings, 'src')` tetap dipakai tanpa perubahan.

- [ ] **Step 1: Buat backup bertimestamp untuk source aktif**

Run dari project root:

```powershell
$backupStamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$sourcePath = 'public\js\pagebuilder_elementor_v23\widgets\basic\image\Settings.vue'
Copy-Item -LiteralPath $sourcePath -Destination "$sourcePath.bak_${backupStamp}_image_url_source"
Get-Item -LiteralPath "$sourcePath.bak_${backupStamp}_image_url_source"
```

Expected: backup baru terverifikasi dan tetap untracked.

- [ ] **Step 2: Tambahkan UI Content yang conditional**

Di dalam accordion `Image`, letakkan select sebelum media control, pertahankan `Alt` setelah kontrol sumber:

```vue
<div class="pb-form-group">
	<label class="pb-form-label">Image Source</label>
	<select class="pb-select" :value="imageSource" @change="setImageSource($event.target.value)">
		<option value="ckfinder">CKFinder</option>
		<option value="url">External URL</option>
	</select>
</div>
<div v-if="imageSource === 'ckfinder'" class="pb-form-group">
	<!-- pertahankan markup media picker yang sudah ada persis di sini -->
</div>
<div v-else class="pb-form-group">
	<label class="pb-form-label">Image URL</label>
	<input class="pb-input" type="url" v-model.trim="node.settings.src" placeholder="https://example.com/image.jpg">
	<div class="pb-form-note">Use a direct HTTP or HTTPS image URL.</div>
</div>
```

Tambahkan fallback dan setter lokal pada component export:

```js
computed: {
	imageSource() {
		return this.node.settings?.imageSource === 'url' ? 'url' : 'ckfinder';
	},
},
methods: {
	setImageSource(value) {
		this.node.settings.imageSource = value === 'url' ? 'url' : 'ckfinder';
	},
},
```

Jangan mengosongkan `node.settings.src` saat mode berpindah. Dengan begitu URL atau pilihan CKFinder yang sudah ada tetap tampil ketika pengguna kembali ke mode sebelumnya.

- [ ] **Step 3: Jalankan focused test hingga GREEN**

Run:

```powershell
node --test tests/pagebuilder-v23-basic-image-source.test.mjs
```

Expected: 2 tests pass, 0 fail.

- [ ] **Step 4: Inspeksi perubahan yang benar-benar terjadi**

Run:

```powershell
git diff -- public/js/pagebuilder_elementor_v23/widgets/basic/image/Settings.vue tests/pagebuilder-v23-basic-image-source.test.mjs
git diff --check
git status --short
```

Expected: hanya `Settings.vue` v2.3, focused test baru, dan backup untracked. Tidak ada perubahan pada v2.0, Canvas, definition, Blade, atau persistence layer.

## Task 3: Verifikasi regresi, runtime Chrome, dan Graphify

**Files:**

- Verify: `tests/pagebuilder-v23-basic-image-source.test.mjs`
- Verify: `tests/pagebuilder-v23-widget-runtime-parity.test.mjs`
- Verify: `tests/pagebuilder-editor-v23-production-static.test.mjs`
- Verify: `tests/Feature/PageBuilderElementorV23WidgetParityTest.php`
- Verify: `tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php`
- Verify: `tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php`
- Runtime: open Chrome Page Builder v2.3 tab and MG reference tab

- [ ] **Step 1: Jalankan suite Node yang berisiko langsung**

Run:

```powershell
node --test tests/pagebuilder-v23-basic-image-source.test.mjs tests/pagebuilder-v23-widget-runtime-parity.test.mjs tests/pagebuilder-editor-v23-production-static.test.mjs
```

Expected: seluruh test pass. Khusus runtime parity harus membuktikan katalog/module v2.3 tetap valid dan v2.0 tidak ikut berubah.

- [ ] **Step 2: Jalankan focused Laravel suites**

Run:

```powershell
php artisan test tests/Feature/PageBuilderElementorV23WidgetParityTest.php tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php
```

Expected: seluruh test dan assertion pass; route, payload, serta rendering frontend v2.3 tetap kompatibel.

- [ ] **Step 3: Lakukan runtime QA di Chrome tanpa Save**

1. Hard reload tab Page Builder v2.3 agar `Settings.vue` terbaru dimuat.
2. Pilih atau tambahkan widget Image dan buka tab Content.
3. Verifikasi node lama/default menampilkan `Image Source = CKFinder` dan media picker lama tetap berfungsi secara visual.
4. Pilih `External URL`; verifikasi media picker diganti input `Image URL`, sedangkan field `Alt` tetap berada setelahnya.
5. Isi URL MG berikut:

   `https://assets.mgmotor.id/contents/userfiles/mgmodels/coverimage/202405/02318367ffba401342f0b62a66747ba7.webp`

6. Verifikasi canvas langsung menampilkan gambar MG dan `src` DOM sama dengan URL tersebut.
7. Ubah `Alt`, lalu verifikasi atribut `alt` canvas ikut berubah.
8. Pindah ke CKFinder lalu kembali ke External URL; verifikasi `settings.src` tidak dikosongkan.
9. Bandingkan hierarchy, label, spacing, dan conditional behavior dengan screenshot Elementor yang diberikan pengguna.
10. Jangan menekan Save. Ambil screenshot bukti runtime sebelum melanjutkan pembuatan halaman MG5 GT.

- [ ] **Step 4: Commit perubahan terverifikasi saja**

Stage hanya source aktif dan test; jangan stage backup atau `graphify-out`:

```powershell
git add -- public/js/pagebuilder_elementor_v23/widgets/basic/image/Settings.vue tests/pagebuilder-v23-basic-image-source.test.mjs
git diff --cached --check
git diff --cached --name-only
git commit -m "feat: add v23 image URL source"
```

Expected staged paths: tepat dua file di atas.

- [ ] **Step 5: Verifikasi status commit dan Graphify lokal**

Run:

```powershell
git status --short --branch
git log -2 --oneline
```

Jika hook Graphify berjalan, tunggu hingga selesai dan verifikasi graph lokal mencerminkan source commit terbaru. Jangan stage `graphify-out`. Jika hook gagal atau graph masih stale, laporkan sebagai batas verifikasi; jangan menjalankan full rebuild tanpa kebutuhan.

- [ ] **Step 6: Handoff hasil dan lanjutkan tujuan utama**

Laporkan file yang dibaca, backup, file yang diubah, test beserta hasil aktual, bukti Chrome, status Save yang tetap tidak disentuh, dan status Graphify. Setelah fitur dinyatakan lolos, lanjutkan penggunaan `External URL` untuk menyusun section MG5 GT dari bawah header sampai atas footer, tetap tanpa menyimpan page.
