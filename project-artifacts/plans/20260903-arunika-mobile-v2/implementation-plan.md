# Arunika Mobile V2 — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Inline execution is the default; no sub-agents unless separately requested. Steps use checkbox syntax.

**Goal:** Implementasi lima tema mobile mengikuti PNG V2 masing-masing, sampai seluruh elemen, state dan interaksi yang tercakup mendapat bukti visual/runtime.

**Architecture:** Blade tetap merender menu, profil dan halaman. CSS overlay mobile per tema menjaga desktop dan auth. Controller kecil Vue 3 CDN memiliki state drawer mobile; existing theme JS tetap memiliki desktop, palette, active route dan popover. Dashboard memakai Vue 3 Options API existing.

**Tech Stack:** Laravel Blade; global Vue dari runtime CDN/vendor CMS existing; Bootstrap 5; ECharts 5.5.1; SimpleBar; FontAwesome. Classic JS/CSS, tanpa SFC .vue, tanpa import bundler, tanpa Vite/npm build.

**Spec:** [visual-spec.md](./visual-spec.md) dan lima PNG V2 yang ditautkan di sana. PNG, bukan prompt ImageGen, adalah target.

## Global Constraints

- Project root: D:/Laragon/www/laravel-13-phoenix. Path relatif dalam dokumen ini dihitung dari root tersebut.
- Turn ini planning saja; tidak ada produksi yang diubah atau dieksekusi.
- Mobile <=768px; desktop >=769px tidak didesain ulang.
- Viewport mandatory 300/400/500 x844; viewport reference fidelity ditentukan dari crop rasio PNG, tanpa distortion.
- Jangan mount Vue pada body, .ph-app-shell, .ph-layout-right, atau node yang memuat @yield('content').
- Gunakan satu Vue 3 CDN existing; jangan memuat Vue kedua, SFC, npm build, atau framework sidebar baru.
- Reuse menu_versioning(), checkIsAdmin(), get_avatar(), SiteTypography, route existing.
- Pertahankan pending CSS Lucent + test auth yang telah diedit sebelumnya. Jangan reset/overwrite.
- Backup file existing sebelum edit; project-artifacts/backups/<timestamp>_mobile-v2/<relative-path>; verifikasi SHA-256.
- Semua test baru, QA, mockup, laporan dan backup di project-artifacts. Existing tests boleh diubah secara targeted setelah backup.
- Tidak commit/push/stage, delete backup, atau database refresh tanpa otorisasi tersendiri.
- Jalankan Code Work, Ponytail, TDD, UI/UX Pro Max, Browser dan verification-before-completion saat eksekusi.
- Visual target 100% bukan klaim otomatis dari static test. Mismatch dibuka di laporan, tidak ditutup dengan perubahan baseline sepihak.
- Permission/data/logo dinamis tetap nyata. Detail konsep yang bertabrakan diputuskan di Task 0, bukan hardcode diam-diam.

## File map

| Surface | Peran |
|---|---|
| resources/views/themes/arunika_{theme}/cms/cms_layout.blade.php | Body marker, include mobile CSS/JS, struktur header/drawer sesuai tema |
| public/assets/js/themes/arunika_{theme}/arunika_{theme}.js | Delegasi mobile dan handoff breakpoint; desktop/palette tetap |
| public/assets/js/themes/arunika-mobile-navigation-v2.js — new | Vue 3 CDN controller kecil, state/accessibility mobile saja |
| public/assets/css/themes/arunika_{theme}/mobile-v2.css — new, 5 file | Overlay mobile masing-masing tema |
| resources/views/themes/arunika_{theme}/components/menu.blade.php | Conditional edit semantic marker/label wrapper saja bila diperlukan |
| resources/views/dashboard/dashboard.blade.php | Semantic hooks metric/icon/chart, satu dashboard bukan 5 salinan |
| public/assets/js/vue3/dashboard/vueV3-dashboard-2026.js | Responsive presentation melalui Vue 3 Options API existing |
| resources/views/components/cms-realtime-notification.blade.php | Read/reuse; jangan duplikasi ID/subscriber |
| public/assets/css/theme-responsive-typography.css | Read-only terlebih dahulu; jangan memperbaiki satu tema lewat perubahan global |
| project-artifacts/tests/arunika-mobile-v2/ | Browser scenario source dan fixture metadata |
| project-artifacts/qa/arunika-mobile-v2/ | Reference measurements, PNG, metrics, diff, report |

Arunika themes: mosaic, aurora, prism, equinox, lucent. Tidak ada auth/front-end layout yang memuat overlay baru. Patch source CSS utama hanya jika ada hambatan cascade yang terbukti, setelah backup dan alasan tertulis.

## Task 0 — Baseline dan reference gate

**Create:** project-artifacts/qa/arunika-mobile-v2/reference-measurements.json, baseline-report.md.
**Read:** visual-spec.md, semua PNG V2, source theme yang ditunjuk Graphify.

- [ ] Periksa git status --short dan git diff --stat; rekam hash dirty CSS/test Lucent sebagai baseline yang dipertahankan.
- [ ] Baca memori E:/AI/Memories/20260901-arunika-lucent-handoff.md. Status memori historis tidak menggantikan source terkini.
- [ ] Query Graphify: sidebar lucent mosaic aurora prism equinox; buka source terkait. Diagram adalah peta, source dan runtime tetap bukti.
- [ ] Ukur crop closed/open terpisah dari setiap PNG, SHA-256, native dimensions, normalized anchor header/toggle/avatar/drawer/menu/footer.
- [ ] Normalisasi dengan skala uniform ke 400 CSS px; tinggi mengikuti crop aspect ratio. Jangan memaksa raster non-400x844 menjadi 400x844.
- [ ] Putuskan dengan user konflik asset/semantik dari spec section 6: logo/wordmark/avatar Lucent, dataset chart Jan–Jun versus existing Matcha/Milk, copy Prism, drawer ratio/min-width readable.
- [ ] Capture baseline desktop dan mobile untuk setiap tema; catat tema/mode/palette dari UI. Tidak membaca cookie/session store.
- [ ] Rekam state DOM dan computed metrics selain screenshot: selected theme, innerWidth/height, rect controls/cards/sidebar, visible menu.
- [ ] Pastikan baseline representatif: screenshot aktif bukan preview gambar statis dari Manage Themes.
- [ ] Jangan mengubah setting tema global paralel. Restore tema/mode awal ketika sesi QA selesai.

Format reference-measurements:
- imagePath, sha256, nativeWidth/nativeHeight.
- closedCrop/openCrop {x,y,width,height}.
- referenceViewport {width,height} per state.
- anchors {header, opener, close, avatar, firstMenu, lastMenu, footer}.
- palette {surface,text,muted,border,accent,scrim}, typography estimates.
- acceptedDeviations: daftar eksplisit dengan alasan dan keputusan user. Kosong berarti tidak ada waiver.

Backup command template untuk task yang menyentuh existing files:
```powershell
$stamp = Get-Date -Format yyyyMMdd_HHmmss
$backupRoot = Join-Path 'project-artifacts/backups' ($stamp + '_mobile-v2')
$targets = @(
 'resources/views/themes/arunika_lucent/cms/cms_layout.blade.php',
 'public/assets/js/themes/arunika_lucent/arunika_lucent.js'
)
foreach ($relative in $targets) {
 $source = (Resolve-Path -LiteralPath $relative).Path
 $destination = Join-Path $backupRoot $relative
 New-Item -ItemType Directory -Path (Split-Path $destination) -Force | Out-Null
 Copy-Item -LiteralPath $source -Destination $destination
 if ((Get-FileHash -LiteralPath $source).Hash -ne (Get-FileHash -LiteralPath $destination).Hash) {
  throw "Backup mismatch: $relative"
 }
}
```
Gunakan target exact per task; jangan memakai backup sebagai source utama.

## Task 1 — Vue 3 CDN controller, consumer pertama Lucent

**Create:** public/assets/js/themes/arunika-mobile-navigation-v2.js.
**Modify:** CMS layout dan theme JS Lucent.
**Create test scenario:** project-artifacts/tests/arunika-mobile-v2/navigation.browser.js.

**Consumes:** global Vue, #sidebar, .ph-mobile-sidebar-trigger, .ph-mobile-sidebar-close, .ph-layout-right, backdrop existing atau node baru bila belum ada.
**Produces:** window.PhoenixMobileNavigation, interface konseptual:
```typescript
interface MobileNavigation {
 isMobile(): boolean;
 open(): void;
 close(): void;
 toggle(): void;
 syncViewport(): void;
}
```
Implementasi tetap JavaScript classic, bukan TypeScript build.

Mount root kosong:
```blade
<div id="ph-mobile-nav-controller" hidden aria-hidden="true"></div>
<script src="{{ asset('assets/js/themes/arunika-mobile-navigation-v2.js?v=').time() }}"></script>
```
Include sesudah Vue CDN dan script tema, sekali saja. App memakai render() yang mengembalikan null; setup memakai Vue.ref, Vue.watch, Vue.onMounted dan Vue.onBeforeUnmount. DOM menu Blade tidak dicompile ulang oleh Vue. Existing Vue dashboard/forms tidak menjadi descendants mount root baru.

- [ ] RED: jalankan open/close, Escape/focus-return, 20 rapid toggles, 768/769 transition melawan Lucent existing. Catat gap nyata, bukan error selector palsu.
- [ ] Implement isOpen/isMobile refs; default mobile closed. One watch commits .ph-expanded, ARIA, inert dan backdrop state.
- [ ] Drawer tertutup inert di mobile. Drawer terbuka membuat .ph-layout-right inert; jangan meng-inert ancestor sidebar.
- [ ] Simpan opener element untuk focus return; focus pindah ke close control saat open. Tab/Shift+Tab tetap dalam drawer dan dropdown anak yang sedang terbuka.
- [ ] Escape menutup dropdown aktif dulu bila perlu, berikutnya drawer; backdrop menutup drawer. Listener dibersihkan saat unmount.
- [ ] Controller mobile tidak pernah menulis sidebar-state. Desktop preference tetap pemilik theme script.
- [ ] Integrasi fungsi toggleSidebar legacy memakai branch berikut di awal:
```js
if (window.PhoenixMobileNavigation?.isMobile()) {
 window.PhoenixMobileNavigation.toggle();
 return;
}
```
- [ ] syncSidebarForViewport legacy tidak boleh mengubah .ph-expanded saat controller memiliki mobile; saat kembali desktop lepas inert/backdrop, restore preference sekali.
- [ ] Jangan menambah click listener kedua pada tombol yang sudah onclick=toggleSidebar(). Satu click satu transisi.
- [ ] Navigasi link halaman menutup drawer, tetapi klik accordion tidak dianggap pindah halaman. Jangan mengganti menu hierarchy.
- [ ] Jika Vue atau mount root tidak tersedia, jangan mengklaim controller ready; biarkan legacy fallback bekerja dan catat init error.
- [ ] GREEN: scenario yang sama lulus; node --check pada controller dan theme JS. Integrasikan ke tema lain hanya setelah consumer Lucent stabil.

Contoh assertion perilaku yang dijalankan melalui browser tool yang didukung:
```js
const assert = (await import('node:assert/strict')).default;
async function verifyMobileNav(t) {
 const opener = t.playwright.getByRole('button',{name:'Open navigation',exact:true});
 assert.equal(await opener.count(),1);
 await opener.click();
 const opened = await t.playwright.evaluate(() => ({
  expanded:document.querySelector('#sidebar').classList.contains('ph-expanded'),
  mainInert:document.querySelector('.ph-layout-right').inert,
  overflow:document.documentElement.scrollWidth > innerWidth + 1
 }));
 assert.equal(opened.expanded,true);
 assert.equal(opened.mainInert,true);
 assert.equal(opened.overflow,false);
 await t.playwright.getByRole('button',{name:'Close navigation',exact:true})
  .filter({visible:true}).press('Escape');
 const closed = await t.playwright.evaluate(() => ({
  expanded:document.querySelector('#sidebar').classList.contains('ph-expanded'),
  mainInert:document.querySelector('.ph-layout-right').inert,
  focusReturned:document.activeElement?.classList.contains('ph-mobile-sidebar-trigger')
 }));
 assert.equal(closed.expanded,false);
 assert.equal(closed.mainInert,false);
 assert.equal(closed.focusReturned,true);
}
```
Berikan backdrop accessible label berbeda dari X agar locator X tidak ambiguous. Jangan menyimpan credential dalam scenario source.

## Task 2 — Shared dashboard semantic hooks dan chart mobile

**Modify:** resources/views/dashboard/dashboard.blade.php; public/assets/js/vue3/dashboard/vueV3-dashboard-2026.js.
**Create scenario:** project-artifacts/tests/arunika-mobile-v2/dashboard.browser.js.
**Consumes:** data-ph-mobile-theme, Vue 3 EChartJSVue3 existing, chart DOM IDs existing.
**Produces classes:** .ph-dashboard-summary, .ph-dashboard-stats, .ph-dashboard-stat, .ph-dashboard-stat-icon, .ph-dashboard-stat-label, .ph-dashboard-stat-value, .ph-dashboard-stat-trend, .ph-dashboard-projection-title, .ph-dashboard-projection-chart.

- [ ] Backup kedua file. RED: title projection tidak boleh intersect canvas; chart punya tinggi plot positif; capture gap existing.
- [ ] Tambahkan class semantik ke wrappers existing; pertahankan ID dan satu EChartJSVue3 instance. Icon wrapper baru hidden by default.
- [ ] Title tidak absolute pada mobile; container chart punya tinggi/min-height eksplisit. Legend tidak mengisi posisi title.
- [ ] Adapt method echartSeriesSimpleBar pada Options API CDN existing berdasarkan body marker + mobile breakpoint. Tidak ada Vue app chart baru.
- [ ] Lucent memakai green series dan metric grid 2x2. Dataset/copy mengikuti Task 0; jangan mengubah data berdasarkan tema tanpa keputusan.
- [ ] Update options/resize instance existing pada viewport change; bersihkan resize listeners beforeUnmount. Tidak re-init chart berulang.
- [ ] Footer/halaman di bawah crop tetap scrollable; jangan hapus Revenue per Month/Total Orders/dll demi meniru screenshot yang terpotong.
- [ ] GREEN: title/canvas tidak overlap, legend readable 300px, dataset stabil saat theme switch, desktop snapshot tidak berubah.

Contoh seam CSS Lucent; angka akhir harus hasil reference measurement:
```css
@media (max-width:768px) {
 body[data-ph-mobile-theme="lucent"] .ph-dashboard-stats {
  display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px;
 }
 body[data-ph-mobile-theme="lucent"] .ph-dashboard-stats > * {width:auto;min-width:0}
 body[data-ph-mobile-theme="lucent"] .ph-dashboard-stat-value {overflow-wrap:anywhere}
 body[data-ph-mobile-theme="lucent"] .ph-dashboard-projection-title {
  position:static !important;margin-bottom:12px;
 }
 body[data-ph-mobile-theme="lucent"] .ph-dashboard-projection-chart {min-height:240px}
}
```

## Theme task contract — berlaku untuk Tasks 3–7

Setiap tema mendapat satu test cycle lengkap, bukan “sama dengan tema sebelumnya”.

1. Backup file exact sebelum edit, jangan menghapus dirty work.
2. RED dari browser: urutan header, ukuran/posisi drawer, menu/footer, metric layout, isolated Vue ownership.
3. Tambah data-ph-mobile-theme ke body CMS; append mobile-v2.css sesudah typography; include controller sekali.
4. Perubahan visual hanya di media <=768px dan body marker. Jangan edit auth/front-end include.
5. Gunakan PNG dan reference-measurements untuk warna, font, spacing, icon, radius, border, shadow. Tidak menyalin nilai prompt ImageGen.
6. Render menu tetap menu_versioning(). User/avatar/role tetap dinamis; admin guard dipertahankan.
7. Jalankan open/close/X/backdrop/Escape/Tab/long menus/dropdowns. Header target 44px hit area minimum, tanpa duplicate visible toggle.
8. GREEN screenshot closed/open di300/400/500 plus native-ratio reference capture; check metrics dan console. Label/fungsi tidak cukup dinilai static.
9. Existing mobile-specific tests yang mengunci desain lama hanya diperbarui jika bertentangan PNG; tambahkan behavior assertion, jangan menghapus test supaya hijau.
10. Desktop, auth, halaman CMS lain tidak berubah. Simpan deviations.md dan after/diff PNG. Inline review lalu lanjut; tidak otomatis commit.

## Task 3 — Lucent, visual fidelity per tema

**Target:** [PNG V2 Lucent](../../mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-lucent-v2.png).
**Mode reference:** light.
**Modify:** resources/views/themes/arunika_lucent/cms/cms_layout.blade.php; public/assets/js/themes/arunika_lucent/arunika_lucent.js.
**Create:** public/assets/css/themes/arunika_lucent/mobile-v2.css.
**Existing test:** tests/arunika-lucent-theme-static.test.mjs.
**Create browser scenario:** project-artifacts/tests/arunika-mobile-v2/lucent.browser.js.
**Conditional modify:** resources/views/themes/arunika_lucent/components/menu.blade.php hanya untuk semantic hooks yang belum tersedia.

**Consumes:** Vue 3 CDN controller/API Task 1, metric/chart hooks Task 2, data menu/profil existing.
**Produces:** data-ph-mobile-theme="lucent", stylesheet isolated, controller consumer, screenshot/diff perwidth.

**Visual delta:** Header putih satu baris hamburger → Lucent brand → bell → avatar. Opener floating kanan dihapus hanya pada mobile. Drawer brand/X di atas; account strip nama penuh/role; Dashboard aktif mint; View site; CONTENT; Manage Articles/Cover Image/Event. Footer Awesome Admin, Settings, Log out. Metrics 2× 2 dan chart hijau.

**Risiko:** Preserve :root fallback dan --ph-primary* hasil fix login. Bell existing sekarang hidden; buka hanya pada mobile, tidak include notification component dua kali. Avatar header harus membuka profil asli tanpa menggandakan id dropdown. Logo konsep versus Site Config mengikuti Task 0.

**Source gap:** CSS lama sekitar4876+ menyembunyikan seluruh isi topbar kecuali toggle. Overlay baru harus menang hanya pada mobile; jangan menimpa desktop. Existing account row di sidebar dipertahankan desktop, mobile memakai struktur account strip referensi.

- [ ] Backup exact layout/theme JS/test existing. Hash checked; original CSS auth changes tidak tersentuh.
- [ ] Buat RED browserchecks khusus: Header empat cluster berurutan; tidak ada toggle lama ganda; nama account terbaca; 2 kolom metric pada baseline, trend/value wrap 300px; profile dan tiga footer actions reachable.
- [ ] Catat gambar/crop/nativeviewport/anchor before; jangan menganggap drawer256px existing sudah sesuai gambar.
- [ ] Include isolated CSS setelah typography dan hanya layout CMS:
```blade
<link href="{{ asset('assets/css/themes/arunika_lucent/mobile-v2.css?v=').time() }}" rel="stylesheet">
```
- [ ] Tambah controller root kosong dan load classic JS sekali; periksa tidak ada Vue app parent yang menelan dashboard/forms.
- [ ] Modifikasi header/drawer/mobileclose sesuai PNG. CSS dimulai dengan media dan marker:
```css
@media (max-width:768px) {
 body[data-ph-mobile-theme="lucent"] .ph-mobile-sidebar-trigger {
  min-width:44px; min-height:44px; flex-shrink:0;
 }
 body[data-ph-mobile-theme="lucent"] .ph-sidebar {
  max-width:calc(100vw - 24px);
 }
 body[data-ph-mobile-theme="lucent"] .ph-sidebar .ph-nav-text {
  min-width:0; overflow-wrap:anywhere;
 }
}
```
Ini scaffold scope/hit-area saja; width/radius/font/shadow akhir diisi dari reference-measurements yang sudah disetujui, bukan nilai universal.
- [ ] Bind setiap aksi existing; header/sidebar tidak hanya mock button. Menu tetap data-driven dan role-aware, active path benar.
- [ ] Terapkan style Dashboard spesifik Lucent, tanpa menyalin markup dashboard baru. Konten lanjutannya masih scrollable.
- [ ] Test closed→open→X close, backdrop close, Escape, focus loop, menu/accordion, rapid toggle,768↔769; pastikan desktop preference tidak berubah oleh mobile.
- [ ] Test nama panjang, role non-admin jika tersedia, dropdown 300px, footer dengan menu panjang. Jangan test mutation Logout atau clear-all notifications.
- [ ] GREEN jalankan static regression:
```powershell
node --test tests/arunika-lucent-theme-static.test.mjs
node --check public/assets/js/themes/arunika_lucent/arunika_lucent.js
```
- [ ] Capture final 300/400/500 x844 closed/open serta capture reference-native-ratio. Check width, overflow, visible opener=1, visible close=1, semua anchor dan keterbacaan.
- [ ] Compare reference/capture satu skala dengan overlay/diff dan pemeriksaan manusia. Tidak masking area mismatch. Catat setiap deviasi.
- [ ] Simpan project-artifacts/qa/arunika-mobile-v2/lucent/: reference crop, after PNG, diff PNG, computed metrics, console, deviations.md.
- [ ] Inline reviewer gate: scope file, Vue ownership, desktop/auth, no duplicate notification ID/listener, menu/role, semua aksi. Tanpa commit/push otomatis.

## Task 4 — Mosaic, visual fidelity per tema

**Target:** [PNG V2 Mosaic](../../mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-mosaic-v2.png).
**Mode reference:** dark.
**Modify:** resources/views/themes/arunika_mosaic/cms/cms_layout.blade.php; public/assets/js/themes/arunika_mosaic/arunika_mosaic.js.
**Create:** public/assets/css/themes/arunika_mosaic/mobile-v2.css.
**Existing test:** tests/arunika-mosaic-mobile-shell-static.test.mjs.
**Create browser scenario:** project-artifacts/tests/arunika-mobile-v2/mosaic.browser.js.
**Conditional modify:** resources/views/themes/arunika_mosaic/components/menu.blade.php hanya untuk semantic hooks yang belum tersedia.

**Consumes:** Vue 3 CDN controller/API Task 1, metric/chart hooks Task 2, data menu/profil existing.
**Produces:** data-ph-mobile-theme="mosaic", stylesheet isolated, controller consumer, screenshot/diff perwidth.

**Visual delta:** Header emerald gelap: hamburger/palette/moon kiri, bell/ellipsis/avatar kanan. Drawer charcoal flat, logo dan nama situs, X, Visit Site/Dashboard, ALL MENUS, list, Awesome Admin footer. Empat metrics satu kolom; border tipis, teks putih.

**Risiko:** Mosaic sudah memiliki ph-mobile-sidebar-backdrop dan ph-mobile-sidebar-open; reuse, jangan menambah node kedua. Light mode existing tidak dipaksa dark. Enam kontrol header perlu muat 300px tanpa menurunkan hit area.

**Source gap:** toggleSidebar dan syncSidebarForViewport sudah membatasi persistence pada desktop. Pertahankan kontrak itu saat delegasi Vue; legacy listeners jangan menghasilkan double toggle.

- [ ] Backup exact layout/theme JS/test existing. Hash checked; original CSS auth changes tidak tersentuh.
- [ ] Buat RED browserchecks khusus: Pada300px semua kontrol muat dan keyboard accessible; backdrop tidak menutup dirinya sendiri; footer dapat dicapai dengan menu panjang; tidak ada light flash pada dark reload.
- [ ] Catat gambar/crop/nativeviewport/anchor before; jangan menganggap drawer256px existing sudah sesuai gambar.
- [ ] Include isolated CSS setelah typography dan hanya layout CMS:
```blade
<link href="{{ asset('assets/css/themes/arunika_mosaic/mobile-v2.css?v=').time() }}" rel="stylesheet">
```
- [ ] Tambah controller root kosong dan load classic JS sekali; periksa tidak ada Vue app parent yang menelan dashboard/forms.
- [ ] Modifikasi header/drawer/mobileclose sesuai PNG. CSS dimulai dengan media dan marker:
```css
@media (max-width:768px) {
 body[data-ph-mobile-theme="mosaic"] .ph-mobile-sidebar-trigger {
  min-width:44px; min-height:44px; flex-shrink:0;
 }
 body[data-ph-mobile-theme="mosaic"] .ph-sidebar {
  max-width:calc(100vw - 24px);
 }
 body[data-ph-mobile-theme="mosaic"] .ph-sidebar .ph-nav-text {
  min-width:0; overflow-wrap:anywhere;
 }
}
```
Ini scaffold scope/hit-area saja; width/radius/font/shadow akhir diisi dari reference-measurements yang sudah disetujui, bukan nilai universal.
- [ ] Bind setiap aksi existing; header/sidebar tidak hanya mock button. Menu tetap data-driven dan role-aware, active path benar.
- [ ] Terapkan style Dashboard spesifik Mosaic, tanpa menyalin markup dashboard baru. Konten lanjutannya masih scrollable.
- [ ] Test closed→open→X close, backdrop close, Escape, focus loop, menu/accordion, rapid toggle,768↔769; pastikan desktop preference tidak berubah oleh mobile.
- [ ] Test nama panjang, role non-admin jika tersedia, dropdown 300px, footer dengan menu panjang. Jangan test mutation Logout atau clear-all notifications.
- [ ] GREEN jalankan static regression:
```powershell
node --test tests/arunika-mosaic-mobile-shell-static.test.mjs
node --check public/assets/js/themes/arunika_mosaic/arunika_mosaic.js
```
- [ ] Capture final 300/400/500 x844 closed/open serta capture reference-native-ratio. Check width, overflow, visible opener=1, visible close=1, semua anchor dan keterbacaan.
- [ ] Compare reference/capture satu skala dengan overlay/diff dan pemeriksaan manusia. Tidak masking area mismatch. Catat setiap deviasi.
- [ ] Simpan project-artifacts/qa/arunika-mobile-v2/mosaic/: reference crop, after PNG, diff PNG, computed metrics, console, deviations.md.
- [ ] Inline reviewer gate: scope file, Vue ownership, desktop/auth, no duplicate notification ID/listener, menu/role, semua aksi. Tanpa commit/push otomatis.

## Task 5 — Aurora, visual fidelity per tema

**Target:** [PNG V2 Aurora](../../mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-aurora-v2.png).
**Mode reference:** light.
**Modify:** resources/views/themes/arunika_aurora/cms/cms_layout.blade.php; public/assets/js/themes/arunika_aurora/arunika_aurora.js.
**Create:** public/assets/css/themes/arunika_aurora/mobile-v2.css.
**Existing test:** tests/arunika-aurora-mobile-sidebar-toggle-static.test.mjs.
**Create browser scenario:** project-artifacts/tests/arunika-mobile-v2/aurora.browser.js.
**Conditional modify:** resources/views/themes/arunika_aurora/components/menu.blade.php hanya untuk semantic hooks yang belum tersedia.

**Consumes:** Vue 3 CDN controller/API Task 1, metric/chart hooks Task 2, data menu/profil existing.
**Produces:** data-ph-mobile-theme="aurora", stylesheet isolated, controller consumer, screenshot/diff perwidth.

**Visual delta:** Header panel-open kiri, palette/mode/Awesome Admin bundar kanan. Drawer ambient lavender/abu, nama situs dan X, Visit Site, active Dashboard putih/shadow, ALL MENUS+divider, menu. Profile card nama/role/chevron dan Logout terpisah di bawah.

**Risiko:** Aurora toggleSidebar saat ini selalu menulis sidebar-state. Mobile branch wajib delegasi tanpa persistence; desktop tetap original. Role menggantikan email hanya pada presentation mobile footer sesuai PNG.

**Source gap:** Existing layout belum memiliki class body spesifik; tambahkan data-ph-mobile-theme saja untuk scoped overlay. Jangan menambahkan bell/avatar di header yang tidak ada pada PNG.

- [ ] Backup exact layout/theme JS/test existing. Hash checked; original CSS auth changes tidak tersentuh.
- [ ] Buat RED browserchecks khusus: Tiga tombol kanan tidak overlap; close X terlihat; mobile tidak mengubah desktop preference; footer profile/logout tidak terpotong dan tidak mengaktifkan logout saat QA.
- [ ] Catat gambar/crop/nativeviewport/anchor before; jangan menganggap drawer256px existing sudah sesuai gambar.
- [ ] Include isolated CSS setelah typography dan hanya layout CMS:
```blade
<link href="{{ asset('assets/css/themes/arunika_aurora/mobile-v2.css?v=').time() }}" rel="stylesheet">
```
- [ ] Tambah controller root kosong dan load classic JS sekali; periksa tidak ada Vue app parent yang menelan dashboard/forms.
- [ ] Modifikasi header/drawer/mobileclose sesuai PNG. CSS dimulai dengan media dan marker:
```css
@media (max-width:768px) {
 body[data-ph-mobile-theme="aurora"] .ph-mobile-sidebar-trigger {
  min-width:44px; min-height:44px; flex-shrink:0;
 }
 body[data-ph-mobile-theme="aurora"] .ph-sidebar {
  max-width:calc(100vw - 24px);
 }
 body[data-ph-mobile-theme="aurora"] .ph-sidebar .ph-nav-text {
  min-width:0; overflow-wrap:anywhere;
 }
}
```
Ini scaffold scope/hit-area saja; width/radius/font/shadow akhir diisi dari reference-measurements yang sudah disetujui, bukan nilai universal.
- [ ] Bind setiap aksi existing; header/sidebar tidak hanya mock button. Menu tetap data-driven dan role-aware, active path benar.
- [ ] Terapkan style Dashboard spesifik Aurora, tanpa menyalin markup dashboard baru. Konten lanjutannya masih scrollable.
- [ ] Test closed→open→X close, backdrop close, Escape, focus loop, menu/accordion, rapid toggle,768↔769; pastikan desktop preference tidak berubah oleh mobile.
- [ ] Test nama panjang, role non-admin jika tersedia, dropdown 300px, footer dengan menu panjang. Jangan test mutation Logout atau clear-all notifications.
- [ ] GREEN jalankan static regression:
```powershell
node --test tests/arunika-aurora-mobile-sidebar-toggle-static.test.mjs
node --check public/assets/js/themes/arunika_aurora/arunika_aurora.js
```
- [ ] Capture final 300/400/500 x844 closed/open serta capture reference-native-ratio. Check width, overflow, visible opener=1, visible close=1, semua anchor dan keterbacaan.
- [ ] Compare reference/capture satu skala dengan overlay/diff dan pemeriksaan manusia. Tidak masking area mismatch. Catat setiap deviasi.
- [ ] Simpan project-artifacts/qa/arunika-mobile-v2/aurora/: reference crop, after PNG, diff PNG, computed metrics, console, deviations.md.
- [ ] Inline reviewer gate: scope file, Vue ownership, desktop/auth, no duplicate notification ID/listener, menu/role, semua aksi. Tanpa commit/push otomatis.

## Task 6 — Prism, visual fidelity per tema

**Target:** [PNG V2 Prism](../../mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-prism-v2.png).
**Mode reference:** light.
**Modify:** resources/views/themes/arunika_prism/cms/cms_layout.blade.php; public/assets/js/themes/arunika_prism/arunika_prism.js.
**Create:** public/assets/css/themes/arunika_prism/mobile-v2.css.
**Existing test:** tests/arunika-prism-mobile-sidebar-static.test.mjs.
**Create browser scenario:** project-artifacts/tests/arunika-mobile-v2/prism.browser.js.
**Conditional modify:** resources/views/themes/arunika_prism/components/menu.blade.php hanya untuk semantic hooks yang belum tersedia.

**Consumes:** Vue 3 CDN controller/API Task 1, metric/chart hooks Task 2, data menu/profil existing.
**Produces:** data-ph-mobile-theme="prism", stylesheet isolated, controller consumer, screenshot/diff perwidth.

**Visual delta:** Header putih hamburger berbingkai kiri, account-shaped Awesome Admin icon kanan. Drawer opaque white, nama situs/X berbingkai, Visit Site, active Dashboard hijau/white shadow, divider/ALL MENUS, menu. Satu profile card di bawah. Metrics satu kolom.

**Risiko:** Static test lama mengunci SVG panel mobile, tombol 36px borderless. Target PNG baru hamburger/outline: update assertion yang memang berubah setelah RED, bukan mengubah desain agar test lama lolos. Desktop panel tetap.

**Source gap:** Prism menyembunyikan notification dan search. Pertahankan; jangan menambah menu untuk mengisi ruang kosong di PNG.

- [ ] Backup exact layout/theme JS/test existing. Hash checked; original CSS auth changes tidak tersentuh.
- [ ] Buat RED browserchecks khusus: X dan hamburger accessible dengan hit-area 44px; profile dropdown muat 300px; ikon account tetap routeAwesome Admin, bukan Profile; tidak ada header bell.
- [ ] Catat gambar/crop/nativeviewport/anchor before; jangan menganggap drawer256px existing sudah sesuai gambar.
- [ ] Include isolated CSS setelah typography dan hanya layout CMS:
```blade
<link href="{{ asset('assets/css/themes/arunika_prism/mobile-v2.css?v=').time() }}" rel="stylesheet">
```
- [ ] Tambah controller root kosong dan load classic JS sekali; periksa tidak ada Vue app parent yang menelan dashboard/forms.
- [ ] Modifikasi header/drawer/mobileclose sesuai PNG. CSS dimulai dengan media dan marker:
```css
@media (max-width:768px) {
 body[data-ph-mobile-theme="prism"] .ph-mobile-sidebar-trigger {
  min-width:44px; min-height:44px; flex-shrink:0;
 }
 body[data-ph-mobile-theme="prism"] .ph-sidebar {
  max-width:calc(100vw - 24px);
 }
 body[data-ph-mobile-theme="prism"] .ph-sidebar .ph-nav-text {
  min-width:0; overflow-wrap:anywhere;
 }
}
```
Ini scaffold scope/hit-area saja; width/radius/font/shadow akhir diisi dari reference-measurements yang sudah disetujui, bukan nilai universal.
- [ ] Bind setiap aksi existing; header/sidebar tidak hanya mock button. Menu tetap data-driven dan role-aware, active path benar.
- [ ] Terapkan style Dashboard spesifik Prism, tanpa menyalin markup dashboard baru. Konten lanjutannya masih scrollable.
- [ ] Test closed→open→X close, backdrop close, Escape, focus loop, menu/accordion, rapid toggle,768↔769; pastikan desktop preference tidak berubah oleh mobile.
- [ ] Test nama panjang, role non-admin jika tersedia, dropdown 300px, footer dengan menu panjang. Jangan test mutation Logout atau clear-all notifications.
- [ ] GREEN jalankan static regression:
```powershell
node --test tests/arunika-prism-mobile-sidebar-static.test.mjs
node --check public/assets/js/themes/arunika_prism/arunika_prism.js
```
- [ ] Capture final 300/400/500 x844 closed/open serta capture reference-native-ratio. Check width, overflow, visible opener=1, visible close=1, semua anchor dan keterbacaan.
- [ ] Compare reference/capture satu skala dengan overlay/diff dan pemeriksaan manusia. Tidak masking area mismatch. Catat setiap deviasi.
- [ ] Simpan project-artifacts/qa/arunika-mobile-v2/prism/: reference crop, after PNG, diff PNG, computed metrics, console, deviations.md.
- [ ] Inline reviewer gate: scope file, Vue ownership, desktop/auth, no duplicate notification ID/listener, menu/role, semua aksi. Tanpa commit/push otomatis.

## Task 7 — Equinox, visual fidelity per tema

**Target:** [PNG V2 Equinox](../../mockups/arunika-mobile-theme-previews-20260903/high-resolution-v2/preview-equinox-v2.png).
**Mode reference:** light.
**Modify:** resources/views/themes/arunika_equinox/cms/cms_layout.blade.php; public/assets/js/themes/arunika_equinox/arunika_equinox.js.
**Create:** public/assets/css/themes/arunika_equinox/mobile-v2.css.
**Existing test:** tests/arunika-equinox-mobile-sidebar-static.test.mjs.
**Create browser scenario:** project-artifacts/tests/arunika-mobile-v2/equinox.browser.js.
**Conditional modify:** resources/views/themes/arunika_equinox/components/menu.blade.php hanya untuk semantic hooks yang belum tersedia.

**Consumes:** Vue 3 CDN controller/API Task 1, metric/chart hooks Task 2, data menu/profil existing.
**Produces:** data-ph-mobile-theme="equinox", stylesheet isolated, controller consumer, screenshot/diff perwidth.

**Visual delta:** Header mint: hamburger rounded border kiri; teks Awesome Admin dan avatar kanan. Drawer square L/brand/X, Visit Site, active Dashboard mint, menu. Landscape pegunungan + sun di bagianbawah. Metric cards satu kolom, badge ikon di kiri, label/angka/trend kanan.

**Risiko:** Reuse public/assets/images/themes/arunika_equinox/arunika-equinox-sidebar-landscape.png; crop/scale mobile diuji dulu. Jika tidak match silhouettePNG, mintakan keputusan aset turunan khususmobile; desktop asset tidak ditimpa.

**Source gap:** Teks admin diPNG berbeda source icon-only. Tambah span mobile-only, preserve permission guard. Tidak menambahkan footer profil yang tidak terlihat di target.

- [ ] Backup exact layout/theme JS/test existing. Hash checked; original CSS auth changes tidak tersentuh.
- [ ] Buat RED browserchecks khusus: Pada300px teks Awesome Admin+avatar muat atau wrap terkontrol; landscape tidak menutupi menu; badge ikon tidak memotong nilai; profil dariavatar tetap lengkap.
- [ ] Catat gambar/crop/nativeviewport/anchor before; jangan menganggap drawer256px existing sudah sesuai gambar.
- [ ] Include isolated CSS setelah typography dan hanya layout CMS:
```blade
<link href="{{ asset('assets/css/themes/arunika_equinox/mobile-v2.css?v=').time() }}" rel="stylesheet">
```
- [ ] Tambah controller root kosong dan load classic JS sekali; periksa tidak ada Vue app parent yang menelan dashboard/forms.
- [ ] Modifikasi header/drawer/mobileclose sesuai PNG. CSS dimulai dengan media dan marker:
```css
@media (max-width:768px) {
 body[data-ph-mobile-theme="equinox"] .ph-mobile-sidebar-trigger {
  min-width:44px; min-height:44px; flex-shrink:0;
 }
 body[data-ph-mobile-theme="equinox"] .ph-sidebar {
  max-width:calc(100vw - 24px);
 }
 body[data-ph-mobile-theme="equinox"] .ph-sidebar .ph-nav-text {
  min-width:0; overflow-wrap:anywhere;
 }
}
```
Ini scaffold scope/hit-area saja; width/radius/font/shadow akhir diisi dari reference-measurements yang sudah disetujui, bukan nilai universal.
- [ ] Bind setiap aksi existing; header/sidebar tidak hanya mock button. Menu tetap data-driven dan role-aware, active path benar.
- [ ] Terapkan style Dashboard spesifik Equinox, tanpa menyalin markup dashboard baru. Konten lanjutannya masih scrollable.
- [ ] Test closed→open→X close, backdrop close, Escape, focus loop, menu/accordion, rapid toggle,768↔769; pastikan desktop preference tidak berubah oleh mobile.
- [ ] Test nama panjang, role non-admin jika tersedia, dropdown 300px, footer dengan menu panjang. Jangan test mutation Logout atau clear-all notifications.
- [ ] GREEN jalankan static regression:
```powershell
node --test tests/arunika-equinox-mobile-sidebar-static.test.mjs
node --check public/assets/js/themes/arunika_equinox/arunika_equinox.js
```
- [ ] Capture final 300/400/500 x844 closed/open serta capture reference-native-ratio. Check width, overflow, visible opener=1, visible close=1, semua anchor dan keterbacaan.
- [ ] Compare reference/capture satu skala dengan overlay/diff dan pemeriksaan manusia. Tidak masking area mismatch. Catat setiap deviasi.
- [ ] Simpan project-artifacts/qa/arunika-mobile-v2/equinox/: reference crop, after PNG, diff PNG, computed metrics, console, deviations.md.
- [ ] Inline reviewer gate: scope file, Vue ownership, desktop/auth, no duplicate notification ID/listener, menu/role, semua aksi. Tanpa commit/push otomatis.

## Task 8 — Integrated acceptance dan recovery

**Create:** project-artifacts/qa/arunika-mobile-v2/final-report.md.

Minimum matrix:
- 5 themes × 3 widths × 2 states = 30 state checks, screenshot+DOM+console.
- Reference-native-ratio captures terpisah dari 300/400/500 x844 usability matrix.
- Short height 400x568; boundary 768/769; desktop 1024/1440/1920.
- Light/dark smoke pada 5 themes. Hanya mode yang ada PNG boleh diklaim image-matched.
- /dashboard, /manage_article, /manage_coverimage, /manage_event, /awesome_admin/themes: header/menu/layout unaffected across pages; table boleh overflow di wrapper sendiri, bukan body.
- Administrator/SuperAdmin fixture dan restricted role bila tersedia; jika role tidak tersedia laporkan untested.
- Guest login/empty-form Notice Lucent tetap diperbaiki; jangan load mobile CMS overlay pada auth.
- Dropdown/profile/palette/notifications 300px, keyboard Tab/Escape,200% zoom,reduced-motion,long menu.

- [ ] Jalankan fresh Node/CDN syntax/Blade checks:
```powershell
node --test tests/arunika-lucent-theme-static.test.mjs tests/arunika-mosaic-mobile-shell-static.test.mjs tests/arunika-aurora-mobile-sidebar-toggle-static.test.mjs tests/arunika-prism-mobile-sidebar-static.test.mjs tests/arunika-equinox-mobile-sidebar-static.test.mjs tests/arunika-concept-theme-rename-static.test.mjs tests/arunika-cool-gray-contrast-static.test.mjs
node --check public/assets/js/themes/arunika-mobile-navigation-v2.js
node --check public/assets/js/vue3/dashboard/vueV3-dashboard-2026.js
php artisan view:cache
git diff --check
```
- [ ] Inspect phpunit.xml/environment terlebih dahulu; Feature tests hanya dengan confirmed isolated test DB, tidak refresh DB pengguna:
```powershell
php artisan test tests/Feature/ThemeManagerTest.php tests/Feature/SiteTypographyPreviewSettingsTest.php
```
- [ ] Tidak menjalankan npm/Vite build untuk CSS/classic JS; frontend served asset diverifikasi HTTP dan DOM.
- [ ] Review semua screenshot berdampingan, cek geometry/typography/palette/menu/control. Toleransi diagnosis tidak sama dengan 100% acceptance; user-visible mismatch harus diselesaikan atau disetujui sebagai deviasi.
- [ ] Update Graphify incremental hanya source aktif yang berubah; exclude project-artifacts/backups/vendor/node_modules/cache/generated/secret. Tidak full rebuild atau upload external.
- [ ] Restore global theme semula Lucent, mode/palette ke recorded initial; reset viewport, stop temporary servers, verify selected theme UI. Jangan menghapus cookie/session.
- [ ] git diff --name-only dan git status untuk memastikan scope. No stage/commit/push.
- [ ] Handoff per tema: visual pass/partial, runtime check, static check, limitation, screenshot paths, backups, graph status. Auth fix sebelumnya dipertahankan.
- [ ] Dilarang menyatakan 100% kalau logo,ratio,font,data chart,role/interaksi masih berbeda tanpa keputusan.

## Urutan eksekusi

Task 0 reference gate → Task 1 Vue controller Lucent → Task 2 dashboard seam → Task 3 Lucent → Task 4 Mosaic → Task 5 Aurora → Task 6 Prism → Task 7 Equinox → Task 8 integrated QA.

Eksekusi inline dengan checkpoint per tema; tema global tidak diubah paralel. Plan ini bukan perintah implementasi atau publication.

## Self-review authoring

- [x] Lima PNG V2 memiliki task/source/acceptance masing-masing.
- [x] Vue 3 CDN, bukan SFC/npm/Vite, dinyatakan eksplisit.
- [x] Root mount tidak mengandung dashboard/form Vue atau notification Vue.
- [x] Menu/profil/data/role tetap dinamis; board caption bukan UI.
- [x] Raster ratio dan decorative/generated assets mempunyai decision gate.
- [x] Existing dirty Lucent auth fix, backup, artifacts preserved.
- [x] RED/GREEN, visual comparison, runtime/desktop/auth/role limits tercakup.
- [x] Global state recovery, no commit/push, incremental graph tercakup.

## Bukti saat menyusun plan (bukan hasil implementasi)

Source yang diperiksa: lima CMS layouts, fungsi toggleSidebar/updateSidebarToggleState/syncSidebarForViewport theme JS, CSS mobile selectors, dashboard Blade dan EChartJSVue3, notification component, package.json, existing theme tests.
Graphify query: sidebar lucent mosaic aurora prism equinox. Graph dated 2026-09-01, peta JS tetap diverifikasi ke source; CSS Lucent dirty lebih baru dan dibaca langsung, graph bukan bukti akhir.
Memori: E:/AI/Memories/20260901-arunika-lucent-handoff.md untuk identitas dan boundaries historis.
Tidak ada implementasi atau test runtime baru pada turn planning ini. Hanya dua Markdown rencana dibuat; visual-spec dibackup sebelum adaptasi Vue CDN.
