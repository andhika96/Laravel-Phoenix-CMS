# Page Builder Image Carousel Elementor Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menambahkan widget General `Image Carousel` yang selaras pada editor state, canvas, persistence, dan frontend.

**Architecture:** Widget mengikuti registry modular yang dipakai Image Box/Icon Box. Gallery memakai CKFinder multi-select dan data array terstruktur; canvas Vue dan frontend runtime menggunakan kontrak konfigurasi yang sama tanpa library eksternal.

**Tech Stack:** Laravel, Blade, Vue 3 browser compiler, CKFinder, vanilla JavaScript, PHPUnit.

## Global Constraints

- Jangan mengubah kontrak media picker single-file yang sudah dipakai widget lain.
- Jangan memakai CDN atau menambah package carousel.
- Semua perubahan file aktif harus memiliki backup timestamp sebelum diedit.
- Advanced tidak boleh membuat accordion Display Conditions dan Cache Settings yang berlebih.
- State editor, canvas, persistence, dan frontend harus memakai nama setting yang sama.

---

### Task 1: Lock the widget public contract with failing tests

**Files:**
- Create: `tests/Feature/PageBuilderElementorImageCarouselWidgetParityTest.php`

**Interfaces:**
- Consumes: registry config dan renderer Page Builder yang sudah ada.
- Produces: acceptance contract untuk `image_carousel`, media gallery helper, panel, canvas, Blade, runtime, dan rendition custom.

- [ ] **Step 1: Write the failing test**

Tambahkan assertion untuk config key `image_carousel`, file modular, defaults `images`, seluruh label Elementor, method `chooseMediaGallery`, canvas navigation, partial frontend, `data-image-carousel`, dan runtime `bindImageCarousel`.

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PageBuilderElementorImageCarouselWidgetParityTest --stop-on-failure`

Expected: FAIL karena registry/file widget belum tersedia.

### Task 2: Add registry, normalized state, and gallery picker

**Files:**
- Modify: `config/pagebuilder_elementor_widgets.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Create: `public/js/pagebuilder_elementor/widgets/general/image-carousel/definition.js`

**Interfaces:**
- Produces: `imageCarouselWidgetDefaults()`, `normalizeImageCarouselSettings(settings)`, `chooseMediaGallery(targetObj, propName)`, `removeMediaGalleryItem(targetObj, propName, itemId)`, dan `moveMediaGalleryItem(targetObj, propName, itemId, offset)`.

- [ ] **Step 1: Register module and defaults**

Daftarkan type `image_carousel`, label `Image Carousel`, category `general`, canvas/settings paths, dan partial view.

- [ ] **Step 2: Implement normalized gallery state**

Normalisasi tiap image menjadi `{ id, url, alt, title, caption, description }`, deduplicate URL/id, clamp numeric values, whitelist enum, dan apply shared Advanced defaults.

- [ ] **Step 3: Implement CKFinder multi-select**

Gunakan `chooseFiles: true` dan seluruh collection `files:choose`; append item ke `settings.images`, pertahankan picker single-file lama, serta expose remove/move helper ke component settings.

- [ ] **Step 4: Run focused test**

Run: `php artisan test --filter=PageBuilderElementorImageCarouselWidgetParityTest --stop-on-failure`

Expected: registry/state assertions PASS; UI/renderer assertions masih FAIL.

### Task 3: Build Elementor-mapped settings and canvas

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/general/image-carousel/Settings.vue`
- Create: `public/js/pagebuilder_elementor/widgets/general/image-carousel/Canvas.vue`
- Modify: `public/assets/css/pagebuilder_elementor.css`

**Interfaces:**
- Settings consumes gallery helpers dan shared Advanced controls.
- Canvas consumes `item.settings` dan `responsiveDevice`, serta menghasilkan interactive preview tanpa memutasi persistence selain active preview index lokal.

- [ ] **Step 1: Implement Content/Style/Advanced controls**

Gunakan conditional controls sesuai mapping: custom resolution, navigation, link/lightbox, caption, border, dan caption section. Gunakan responsive helpers dan Coloris/shared typography/text-shadow controls yang sudah ada.

- [ ] **Step 2: Implement canvas carousel**

Render empty state atau track/slides, prev/next, dots, captions, responsive slides count, local autoplay, pause behaviors, keyboard, dan reduced-motion guard.

- [ ] **Step 3: Add narrow editor CSS**

Scope semua panel/gallery styles ke `.pb-widget-settings--image-carousel` dan semua preview styles ke `.pb-image-carousel`.

- [ ] **Step 4: Run focused test**

Expected: registry/state/panel/canvas assertions PASS; frontend assertions masih FAIL.

### Task 4: Add safe frontend rendering and shared runtime

**Files:**
- Create: `resources/views/pagebuilder_elementor/partials/render_image_carousel.blade.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `public/js/pagebuilder_elementor/frontend-runtime.js`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Blade emits `.pb-image-carousel[data-image-carousel]` and JSON `data-carousel-config`.
- Runtime produces `bindImageCarousel(root)` and includes carousel roots in `init(scope)`.

- [ ] **Step 1: Render safe semantic markup**

Escape metadata, sanitize URLs, generate unique accessible ids/labels, apply responsive CSS variables, captions, links, advanced wrapper, and navigation conditionals.

- [ ] **Step 2: Bind frontend behavior**

Implement index normalization, transform updates, responsive visible count, arrow/dot/keyboard handlers, autoplay/pause, resize, infinite navigation, and lightbox dialog.

- [ ] **Step 3: Add scoped frontend CSS**

Style track, slides, arrows, pagination, captions, image borders, empty state, lightbox, responsive layout, and reduced-motion.

- [ ] **Step 4: Run focused test**

Expected: all Image Carousel parity tests PASS.

### Task 5: Support custom image rendition and verify regressions

**Files:**
- Modify: `app/Http/Controllers/Web/PageBuilderElementor/PageBuilderElementor_Controller.php`
- Modify: `app/Support/PageBuilderElementor/ImageRenditionResolver.php`
- Test: `tests/Feature/PageBuilderElementorImageCarouselWidgetParityTest.php`

**Interfaces:**
- Extends `resolve(string $url, string $size, ?int $width = null, ?int $height = null): string` without breaking existing two-argument callers.

- [ ] **Step 1: Add failing custom rendition assertion**

Call endpoint with `size=custom&width=320&height=180` and assert HTTP 200 plus a custom rendition URL.

- [ ] **Step 2: Implement bounded custom dimensions**

Validate width/height from 1 through 4096, include dimensions in fingerprint, dan use Intervention `scaleDown(width:, height:)`.

- [ ] **Step 3: Run focused and affected suites**

Run Image Carousel test, Image Box test, Widget Registry test, Widget Advanced test, then all `PageBuilderElementor` tests.

- [ ] **Step 4: Run static and runtime verification**

Run JS syntax checks, Blade/PHP lint where applicable, Vue compiler/build check, `git diff --check`, HTTP editor request, and browser interaction for image selection, reorder, responsive slides, navigation, autoplay pause, save/reload, and frontend output.

- [ ] **Step 5: Update Graphify incrementally**

Run `graphify update` untuk file source aktif yang berubah dan simpan outcome query awal sebagai useful.
