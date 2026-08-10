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

## Task 1: Kunci perilaku Image Source v2.3 dengan focused component test

**Files:**

- Create: `tests/pagebuilder-v23-basic-image-source.test.mjs`
- Runtime component check: `public/js/pagebuilder_elementor_v23/widgets/basic/image/Canvas.vue`
- Isolation component check: `public/js/pagebuilder_elementor/widgets/basic/image/Settings.vue`

- [ ] **Step 1: Buat test yang mula-mula gagal**

Tambahkan test Node berikut. Test mengompilasi SFC yang sebenarnya dengan compiler Vue, me-render component melalui Vue SSR, lalu memeriksa state transition dan output yang terlihat. Ini menggantikan source-text assertions sesuai persetujuan pengguna tanggal 2026-08-10.

```js
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

const testDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(testDir, '..');
const mgImageUrl = 'https://assets.mgmotor.id/contents/userfiles/mgmodels/coverimage/202405/02318367ffba401342f0b62a66747ba7.webp';

async function loadSfc(relativePath) {
  const filename = join(rootDir, relativePath);
  const source = await readFile(filename, 'utf8');
  const { descriptor, errors } = parse(source, { filename });
  assert.deepEqual(errors, []);
  const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
  component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function' }).code)(Vue);
  return component;
}

const editor = {
  settingsTab: 'content', responsiveDevices: [], sizeControlUnits: ['px'],
  chooseMedia() {}, clearMedia() {}, openControlResponsiveMenu() {}, applyResponsiveDevice() {},
  responsiveDeviceLabel: () => 'Desktop', responsiveDeviceIcon: () => 'fas fa-desktop',
  isControlResponsiveMenuOpen: () => false, deviceOptionLabel: () => '',
  sizeControlMax: () => 100, sizeControlStep: () => 1, sizeControlDisplayValue: () => 100,
  sizeControlUnit: () => 'px', onSizeControlInput() {}, setSizeControlUnit() {},
};

async function renderComponent(component, props) {
  return renderToString(Vue.createSSRApp(component, props));
}

test('v2.3 Image defaults legacy nodes to CKFinder and switches to an external URL without clearing src', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/basic/image/Settings.vue');
  const node = { settings: { src: 'legacy.jpg', alt: 'Legacy image' } };

  const legacyHtml = await renderComponent(component, { node, editor });
  assert.match(legacyHtml, /Image Source/);
  assert.match(legacyHtml, /Choose Image/);
  assert.doesNotMatch(legacyHtml, /Image URL/);
  assert.equal(component.computed.imageSource.call({ node }), 'ckfinder');

  component.methods.setImageSource.call({ node }, 'url');
  assert.equal(node.settings.imageSource, 'url');
  assert.equal(node.settings.src, 'legacy.jpg');
  node.settings.src = mgImageUrl;

  const urlHtml = await renderComponent(component, { node, editor });
  assert.match(urlHtml, /External URL/);
  assert.match(urlHtml, /Image URL/);
  assert.match(urlHtml, new RegExp(`value="${mgImageUrl}"`));
  assert.doesNotMatch(urlHtml, /Choose Image/);

  node.settings.imageSource = 'unexpected';
  assert.equal(component.computed.imageSource.call({ node }), 'ckfinder');
});

test('v2.3 Image canvas renders the canonical external settings.src', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/basic/image/Canvas.vue');
  const html = await renderComponent(component, {
    item: { settings: { src: mgImageUrl, alt: 'MG5 GT' } },
    responsiveDevice: 'desktop',
  });
  assert.match(html, new RegExp(`src="${mgImageUrl}"`));
  assert.match(html, /alt="MG5 GT"/);
});

test('v2.0 Image settings remain isolated from the v2.3 source selector', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor/widgets/basic/image/Settings.vue');
  const html = await renderComponent(component, {
    node: { settings: { src: 'legacy.jpg', alt: 'Legacy image' } },
    editor,
  });
  assert.doesNotMatch(html, /Image Source/);
});
```

- [ ] **Step 2: Jalankan focused test dan pastikan RED-nya tepat**

Run:

```powershell
node --test tests/pagebuilder-v23-basic-image-source.test.mjs
```

Expected: 1 test FAIL pada assertion `Image Source`, sedangkan test canvas dan isolasi v2.0 tetap pass. Kegagalan harus disebabkan fitur selector belum ada, bukan error compiler atau fixture.

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

Expected: 3 tests pass, 0 fail.

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
