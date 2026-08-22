# Page Builder v2.4 Full-Stack Modular Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengubah 49 tipe Page Builder v2.4 menjadi module yang ditemukan dari satu folder per tipe, dapat dilepas/pasang lewat folder, menggunakan shared Advanced, dan tetap parity dengan baseline v2.4 tanpa mengubah v2.3.

**Architecture:** `ModuleCatalog` menemukan `module.json` di `resources/pagebuilder_elementor_v24/modules`, memvalidasi asset, dan menjadi source of truth bagi editor registry serta frontend renderer. Migration berjalan melalui compatibility bridge: platform dan pilot lebih dahulu, 31 dedicated modules berikutnya, 18 Pro shared modules kemudian, lalu Advanced/runtime/CSS extraction dan legacy cleanup.

**Tech Stack:** Laravel 13, PHP 8.x, Blade, Vue 3 SFC via `vue3-sfc-loader`, vanilla JavaScript, Node test runner, PHPUnit/Pest-compatible Laravel tests, Vite.

**Spec:** `project-artifacts/plans/2026-08-22-pagebuilder-v24-full-stack-modular-design.md`

## Global Constraints

- Scope source hanya Page Builder Elementor v2.4; v2.3 version-owned source harus memiliki checksum sebelum/sesudah yang identik.
- Pertahankan semua user changes dan dirty worktree; jangan reset, clean, checkout, bulk-stage, commit, atau push tanpa izin eksplisit.
- Sebelum memodifikasi existing file, simpan timestamped backup di `project-artifacts/backups/pagebuilder-v24-modular_<timestamp>/`.
- Tidak menambah Composer/NPM dependency baru.
- Persisted `type`, node IDs, settings keys, tree order, layout/grid structure, dan responsive inheritance tidak boleh berubah tanpa compatibility adapter serta regression test.
- Browser QA bersifat read-only: jangan Save, Reset, Apply Dataset, atau real form submission.
- Satu invalid module tidak boleh membuat editor gagal mount.
- Folder removal behavior berlaku setelah reload; live filesystem watching bukan scope.
- Graphify diperbarui incremental hanya setelah source berubah substantif.

---

## Phase 1 — Module platform and one pilot

### Task 1: Capture modular-refactor baseline and backups

**Files:**

- Create: `project-artifacts/qa/pagebuilder-v24-modular-implementation/v24-source-before.sha256`
- Create: `project-artifacts/qa/pagebuilder-v24-modular-implementation/v23-source-before.sha256`
- Create: `project-artifacts/backups/pagebuilder-v24-modular_<timestamp>/...`
- Read: `project-artifacts/audits/pagebuilder-v24-modularity-20260822/architecture-audit.md`

**Interfaces:**

- Consumes: current isolated v2.4 baseline.
- Produces: immutable comparison evidence for all later tasks.

- [ ] **Step 1: Re-run the current focused baseline**

```powershell
php artisan test --compact tests/Feature/PageBuilderElementorV24AssetIsolationTest.php tests/Feature/PageBuilderElementorV24BaselineIsolationTest.php tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php tests/Feature/PageBuilderElementorV24WidgetParityTest.php
node --test tests/pagebuilder-v24-baseline-isolation.test.mjs tests/pagebuilder-v24-all-settings-tab-mount-regression.test.mjs tests/pagebuilder-v24-widget-runtime-parity.test.mjs tests/pagebuilder-v24-frontend-canvas-css-parity.test.mjs
```

Expected: PHP `15 passed`; Node `7 passed`.

- [ ] **Step 2: Snapshot v2.3 and v2.4 version-owned files**

Extend the existing snapshot artifact script with a v2.4 parameter or create a scoped sibling script under `project-artifacts/scripts/`; exclude `.bak`, build output, graph output, and historical copies.

- [ ] **Step 3: Back up existing integration files before their first edit**

Back up at minimum:

```text
routes/pagebuilder_elementor_v24.php
resources/views/pagebuilder_elementor_v24/editor_shell.blade.php
resources/views/pagebuilder_elementor_v24/frontend_renderer.blade.php
resources/views/pagebuilder_elementor_v24/partials/render_node.blade.php
public/js/pagebuilder_elementor_v24/widget-registry.js
public/js/pagebuilder_elementor_v24/app.js
config/pagebuilder_elementor_v24_widgets.php
```

- [ ] **Step 4: Verify backup and snapshot counts are non-zero**

Run `Get-Item`/`Get-Content` against exact artifact paths and stop if any required backup is missing.

### Task 2: Build `ModuleCatalog` with filesystem-fixture TDD

**Files:**

- Create: `app/Support/PageBuilderElementorV24/ModuleCatalog.php`
- Create: `tests/Unit/PageBuilderElementorV24ModuleCatalogTest.php`
- Create: `tests/Fixtures/PageBuilderElementorV24Modules/valid-button/module.json`
- Create fixture assets beside that manifest: `definition.js`, `Canvas.vue`, `Settings.vue`, `frontend.blade.php`

**Interfaces:**

- Consumes: constructor `__construct(?string $root = null)`; default root is `resource_path('pagebuilder_elementor_v24/modules')`.
- Produces: `all(): array`, `find(string): ?array`, `toolbox(): array`, `diagnostics(): array`, `active(string): bool`, `clientCatalog(): array`.

- [ ] **Step 1: Write failing discovery tests**

Cover:

```php
$catalog = new ModuleCatalog($fixtureRoot);

expect(array_keys($catalog->all()))->toBe(['button']);
expect($catalog->find('button')['assets']['settings'])->toEndWith('Settings.vue');
expect($catalog->toolbox()['basic'][0]['type'])->toBe('button');
expect($catalog->active('button'))->toBeTrue();
```

Also create temporary test folders for invalid JSON, duplicate type, unsupported schema, missing required asset, `../` traversal, `toolbox:false`, ordering tie, folder removal, and folder restoration.

- [ ] **Step 2: Run test and verify RED**

```powershell
php artisan test --compact tests/Unit/PageBuilderElementorV24ModuleCatalogTest.php
```

Expected: fail because `ModuleCatalog` does not exist.

- [ ] **Step 3: Implement minimal manifest discovery**

Use native PHP filesystem and JSON APIs only:

```php
final class ModuleCatalog
{
    private ?array $modules = null;
    private array $issues = [];

    public function __construct(private readonly ?string $root = null) {}

    public function all(): array
    {
        return $this->modules ??= $this->discover();
    }

    public function find(string $type): ?array { return $this->all()[$type] ?? null; }
    public function active(string $type): bool { return $this->find($type) !== null; }

    public function diagnostics(): array
    {
        $this->all();
        return $this->issues;
    }

    private function discover(): array
    {
        $root = $this->root ?? resource_path('pagebuilder_elementor_v24/modules');
        if (! is_dir($root)) return [];

        $candidates = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getFilename() !== 'module.json') continue;
            try {
                $manifest = json_decode($file->getContents(), true, 512, JSON_THROW_ON_ERROR);
                $module = $this->validateManifest($manifest, $file->getPath());
                $candidates[$module['type']][] = $module;
            } catch (Throwable $error) {
                $this->issues[] = ['folder' => $file->getPath(), 'reason' => $error->getMessage()];
            }
        }

        $modules = [];
        foreach ($candidates as $type => $matches) {
            if (count($matches) !== 1) {
                $this->issues[] = ['type' => $type, 'reason' => 'duplicate type'];
                continue;
            }
            $modules[$type] = $matches[0];
        }

        uasort($modules, fn (array $a, array $b) =>
            [$a['categoryOrder'], $a['order'], $a['type']]
            <=> [$b['categoryOrder'], $b['order'], $b['type']]
        );
        return $modules;
    }
}
```

Implement `validateManifest(array $manifest, string $directory): array` with the exact schema/containment rules from the spec. Implement `toolbox()` as an ordered reduction over `all()`, and `clientCatalog()` as a map containing only label/category/icon/toolbox/order and URLs generated from the whitelisted asset keys. Do not cache discovery across PHP requests; per-instance memoization above is request-local.

- [ ] **Step 4: Run catalog tests and verify GREEN**

Expected: all ModuleCatalog tests pass; duplicate/traversal modules are absent from `all()`.

### Task 3: Add authenticated, whitelisted module asset delivery

**Files:**

- Create: `app/Http/Controllers/Web/PageBuilderElementorV24/ModuleAssetController.php`
- Modify: `routes/pagebuilder_elementor_v24.php`
- Create: `tests/Feature/PageBuilderElementorV24ModuleAssetTest.php`

**Interfaces:**

- Consumes: `ModuleCatalog::find($type)` and manifest `assets` keys.
- Produces: `GET /pagebuilder-elementor/v2.4/module-assets/{type}/{assetKey}`.

- [ ] **Step 1: Write failing authorization and containment tests**

Test authenticated 200 for `definition`, `canvas`, and `settings`; unauthenticated redirect/401 according to existing route middleware; 404 for unknown module, unknown asset key, Blade asset request, and traversal string.

- [ ] **Step 2: Run the feature test and verify RED**

```powershell
php artisan test --compact tests/Feature/PageBuilderElementorV24ModuleAssetTest.php
```

- [ ] **Step 3: Implement the controller**

Controller resolves only manifest keys and returns a streamed/file response with explicit MIME mapping:

```php
private const MIME = [
    'definition' => 'text/javascript; charset=UTF-8',
    'canvas' => 'text/plain; charset=UTF-8',
    'settings' => 'text/plain; charset=UTF-8',
    'runtime' => 'text/javascript; charset=UTF-8',
    'styles' => 'text/css; charset=UTF-8',
];
```

Never accept a filesystem path from the URL. Add ETag/Last-Modified from the resolved file.

- [ ] **Step 4: Run feature test and route-focused suite**

```powershell
php artisan test --compact tests/Feature/PageBuilderElementorV24ModuleAssetTest.php tests/Feature/PageBuilderElementorV24RoutesAndPersistenceTest.php
```

### Task 4: Make the JavaScript registry catalog-driven

**Files:**

- Modify: `public/js/pagebuilder_elementor_v24/widget-registry.js`
- Create: `tests/pagebuilder-v24-module-registry.test.mjs`

**Interfaces:**

- Consumes: `configure(catalog)` with client-safe metadata and asset URLs.
- Produces: `register(definition)`, `get(type)`, `all()`, `toolbox()` with manifest metadata merged into definition behavior.

- [ ] **Step 1: Write RED tests**

Prove:

```js
registry.configure({ button: manifest });
registry.register({ type: 'button', defaults: () => ({}), normalize: node => node });
assert.equal(registry.get('button').canvas, manifest.assets.canvas);
assert.throws(() => registry.register({ type: 'missing-from-catalog', defaults() {}, normalize() {} }));
```

- [ ] **Step 2: Run RED**

```powershell
node --test tests/pagebuilder-v24-module-registry.test.mjs
```

- [ ] **Step 3: Implement `configure` and reduce definition requirements**

Registry-required definition fields become `type`, `defaults`, and `normalize`. Label/category/icon/toolbox/canvas/settings come from catalog. Keep duplicate type rejection and cloned defaults.

- [ ] **Step 4: Run registry plus existing module tests**

```powershell
node --test tests/pagebuilder-v24-module-registry.test.mjs tests/pagebuilder-v24-widget-runtime-parity.test.mjs
```

### Task 5: Migrate Button as the pilot module

**Files:**

- Create: `resources/pagebuilder_elementor_v24/modules/widgets/basic/button/module.json`
- Create: same folder `definition.js`, `Canvas.vue`, `Settings.vue`, `frontend.blade.php`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV24/PageBuilderElementorV24Controller.php`
- Modify: `public/js/pagebuilder_elementor_v24/widgets/basic/button/definition.js` only if a temporary compatibility shim is required
- Test: `tests/Feature/PageBuilderElementorV24ModulePilotTest.php`
- Test: `tests/pagebuilder-v24-module-registry.test.mjs`

**Interfaces:**

- Consumes: catalog and asset route from Tasks 2-4.
- Produces: one production module loaded exclusively from canonical root while all other types remain on legacy bridge.

- [ ] **Step 1: Write pilot shell and parity tests**

Assert Button manifest is discovered, shell contains its definition asset URL exactly once, toolbox contains Button, and legacy Button definition URL is absent when discovered.

- [ ] **Step 2: Copy Button sources and create exact manifest**

Copy active files; do not use backups. Preserve Button type/defaults/normalize and Blade output byte-semantically except for module path wiring.

- [ ] **Step 3: Inject client catalog before registry boot**

Controller passes `clientCatalog`; shell calls `PageBuilderElementorV24Widgets.configure(...)` before discovered definition scripts. Add a private controller method `legacyClientCatalog(array $legacyModules): array` that maps only non-discovered legacy config entries to the existing public URLs. Merge with discovered entries using discovered type as authority; remove this method in Task 20.

- [ ] **Step 4: Run pilot tests and Button parity checks**

```powershell
php artisan test --compact tests/Feature/PageBuilderElementorV24ModulePilotTest.php tests/Feature/PageBuilderElementorV24FrontendRenderingTest.php
node --test tests/pagebuilder-v24-module-registry.test.mjs tests/pagebuilder-v24-widget-runtime-parity.test.mjs
```

- [ ] **Step 5: Prove removal/restoration using a temporary fixture root**

Do not delete/move the production Button folder. The Feature test must rename a copied fixture directory, rebuild a new catalog instance, assert Button absent, restore it in `finally`, and assert Button present.

## Phase 2 — Migrate the 31 currently dedicated implementations

### Task 6: Migrate all four Layout modules

**Files:**

- Create complete folders under `resources/pagebuilder_elementor_v24/modules/layout/`: `container`, `container-fluid`, `grid`, `row-grid`.
- Copy corresponding active definition, Canvas, Settings, and Blade view.
- Update tests: v2.4 Container/Grid/row-grid Node and Feature suites to resolve paths through manifests.

**Interfaces:**

- Produces: four independently discoverable runtime types; Container Fluid and Row Grid retain `toolbox:false`.

- [ ] Write manifest-contract RED assertions for the four exact types.
- [ ] Run focused Layout tests and record RED caused by missing manifests.
- [ ] Copy active source into each canonical folder and add manifests with unchanged type/category/toolbox/order.
- [ ] Keep existing inline Advanced temporarily to preserve behavior; shared Advanced conversion belongs to Phase 4.
- [ ] Run all `pagebuilder-v24-*grid*.test.mjs`, child-container tests, and relevant PHP rendering tests until GREEN.

### Task 7: Migrate the remaining eight Basic modules

**Files:**

- Create module folders: `heading`, `video`, `google-maps`, `text-editor`, `image`, `divider`, `spacer`, `icon`.
- Copy their dedicated frontend Blade views.
- Update Basic-specific Node/PHP path assertions to catalog resolution.

**Interfaces:**

- Produces: all nine Basic modules including pilot Button as independent folders.

- [ ] Add RED manifest completeness tests for the eight exact types.
- [ ] Copy each active definition/Canvas/Settings/frontend view and add its manifest.
- [ ] Preserve Button `className` semantics and all existing Basic settings keys.
- [ ] Run Basic Image, Google Maps, Heading, Image parity, widget runtime, and frontend rendering suites.

### Task 8: Migrate all fifteen General modules

**Files:**

- Create module folders for `image-box`, `icon-box`, `image-carousel`, `basic-gallery`, `feature-showcase`, `icon-list`, `tabs`, `accordion`, `counter`, `progress-bar`, `testimonial`, `social-icons`, `alert`, `rating`, `text-path`.
- Move Accordion under physical `widgets/general/accordion`; manifest type remains `accordion`.
- Copy each current partial/view into its module `frontend.blade.php`.

**Interfaces:**

- Produces: all General types independent from the central partial directory.

- [ ] Add RED catalog assertions listing the 15 exact types and stable ordering.
- [ ] Copy active editor files and frontend partials; preserve any included generic partial helper calls.
- [ ] Update every General parity test to derive module paths from manifest fixtures.
- [ ] Run full General Node suites plus Feature Showcase/Image Carousel/frontend rendering Feature tests.

### Task 9: Migrate the three already-dedicated Pro modules

**Files:**

- Create module folders for `hero-banner`, `hero-slider`, `product-color-selector`.
- Copy their definition, Canvas, Settings, and dedicated Blade views.

**Interfaces:**

- Produces: three Pro modules no longer dependent on legacy config paths.

- [ ] Add RED manifest and frontend-view assertions for all three types.
- [ ] Copy exact active sources and manifests.
- [ ] Run Hero Banner, Hero Slider, and Product Color Selector Node/PHP suites.

### Task 10: Switch dedicated modules to catalog-only and remove their legacy copies

**Files:**

- Modify: `config/pagebuilder_elementor_v24_widgets.php`
- Modify: `resources/views/pagebuilder_elementor_v24/editor_shell.blade.php`
- Remove only migrated legacy files after backup and parity proof.

**Interfaces:**

- Consumes: 31 discovered complete modules.
- Produces: legacy bridge containing only the 18 unsplit Pro types.

- [ ] Write a test asserting no migrated type remains in the legacy bridge.
- [ ] Run it RED.
- [ ] Remove migrated catalog entries/legacy files with exact backup verification.
- [ ] Run all v2.4 PHP and Node tests; update path-only assertions without weakening behavioral assertions.

## Phase 3 — Split the eighteen Pro shared implementations

### Task 11: Create a Pro extraction safety harness

**Files:**

- Create: `tests/pagebuilder-v24-pro-module-boundaries.test.mjs`
- Create: `tests/Feature/PageBuilderElementorV24ProModuleBoundaryTest.php`
- Read/extract from: legacy Pro shared Settings, Canvas, and Blade renderer.

**Interfaces:**

- Produces: per-type oracle for defaults, normalization, visible Settings branch, Canvas root, frontend marker, runtime selectors, and Advanced include.

- [ ] Enumerate the exact 18 Pro types in one test constant.
- [ ] Assert each target manifest folder is incomplete before extraction (RED target).
- [ ] Capture current renderer output fixtures using existing independent expected selectors, not snapshots of implementation source.
- [ ] Run tests and retain expected RED for missing independent assets.

### Task 12: Extract content/marketing Pro modules

**Files:**

- Create complete module folders for `animated-headline`, `blockquote`, `share-buttons`, `code-highlight`, `price-list`, `price-table`, `call-to-action`, `countdown`.

**Interfaces:**

- Produces: eight independent Pro modules.

- [ ] For each exact type, isolate only its template branch, computed/method dependencies, and scoped style rules from shared Settings/Canvas.
- [ ] Copy its Blade branch into `frontend.blade.php`; retain sanitization and data attributes.
- [ ] Add runtime/styles entry only when behavior or CSS is actually type-specific.
- [ ] Run existing Blockquote/Share Buttons, Code Highlight, and Pro widget parity suites after each extracted type group.

### Task 13: Extract carousel/media Pro modules

**Files:**

- Create complete module folders for `slides`, `carousel`, `reviews`, `testimonial-carousel`, `media-carousel`.

**Interfaces:**

- Produces: five independent carousel/media modules while shared primitive controls remain under shared resources.

- [ ] Extract per-type Settings, Canvas, Blade, and optional runtime/style assets.
- [ ] Keep only truly generic arrow/pagination helpers in shared controls; no type switch may remain in shared Canvas/Settings.
- [ ] Run carousel navigation, Reviews, Testimonial Carousel, and Media Carousel PHP/Node suites.

### Task 14: Extract interactive Pro modules

**Files:**

- Create complete module folders for `hotspot`, `flip-box`, `progress-tracker`, `video-playlist`.

**Interfaces:**

- Produces: four independently removable interactive modules.

- [ ] Extract each type’s editor/frontend behavior and optional runtime initializer.
- [ ] Preserve keyboard/focus behavior and reduced-motion handling where present.
- [ ] Run Progress Tracker/Video Playlist and applicable shared Pro runtime tests.

### Task 15: Extract Form last and guard backend capabilities

**Files:**

- Create complete module folder: `resources/pagebuilder_elementor_v24/modules/widgets/pro/form/`.
- Modify: `PageBuilderElementorV24Controller.php` form endpoints.
- Modify: `FormDatasetController.php` entry points if dataset UI remains module-specific.
- Modify: Form Node/PHP tests to catalog-aware paths.

**Interfaces:**

- Consumes: manifest capability `form-submission`.
- Produces: Form independent UI/frontend module with core backend services fail-closed when inactive.

- [ ] Write RED tests where a fake catalog omits Form and submit/dataset endpoints return 404/422 without sending mail or changing storage.
- [ ] Extract Form Settings/Canvas/Blade/runtime while preserving conditional logic, row-grid, messages, datasets, validation, and mail behavior.
- [ ] Add catalog guard before handler execution.
- [ ] Run all Form Dataset, Row Grid, Submission, conditional logic, and frontend runtime tests.

### Task 16: Delete the Pro shared multi-type implementation

**Files:**

- Remove after backup: legacy `widgets/pro/shared/Settings.vue`, `Canvas.vue`, and multi-type branches in `render_pro_widget.blade.php`.
- Modify: legacy bridge/config.

**Interfaces:**

- Produces: 49 complete module folders, zero type depending on a multi-widget Settings/Canvas/Blade file.

- [ ] Add a static test asserting no manifest points to a shared Canvas, shared Settings, or shared multi-widget view.
- [ ] Run RED before cleanup.
- [ ] Remove shared multi-type files/branches only after all 18 per-type suites pass.
- [ ] Run the full v2.4 PHP and Node suites.

## Phase 4 — Universal Advanced, runtime/styles, and legacy removal

### Task 17: Move shared controls to their canonical root

**Files:**

- Create: `resources/pagebuilder_elementor_v24/shared/AdvancedControls.vue`
- Create: `resources/pagebuilder_elementor_v24/shared/controls/*`
- Create: `app/Http/Controllers/Web/PageBuilderElementorV24/SharedAssetController.php`
- Modify: `routes/pagebuilder_elementor_v24.php`
- Modify: `app.js` shared control paths and shared asset route/catalog.
- Test: `tests/pagebuilder-v24-shared-advanced-contract.test.mjs`
- Test: `tests/Feature/PageBuilderElementorV24SharedAssetTest.php`

**Interfaces:**

- Produces: one canonical shared Advanced component and `GET /pagebuilder-elementor/v2.4/shared-assets/{assetKey}`.

- [ ] Write RED test asserting exactly one AdvancedControls source and every module Settings wrapper references it.
- [ ] Write Feature assertions that only `advanced`, `typography`, `link`, `dynamic-tag`, `css-filter`, `text-stroke`, `text-shadow`, dan `grid-column-style` resolve; Blade/path traversal requests return 404.
- [ ] Implement `SharedAssetController::ASSETS` as a fixed key-to-relative-file map and copy active shared controls.
- [ ] Update loader URLs and run shared asset, shared control structure, and all-settings mount tests.

### Task 18: Normalize the ten inline Advanced modules

**Files:**

- Modify module Settings/definitions for Button, Divider, Icon, Spacer, Text Editor, Video, Container, Container Fluid, Grid, Row Grid.
- Modify: canonical `AdvancedControls.vue` for capability-driven controls.

**Interfaces:**

- Consumes: manifest `advanced.profile` and `advanced.capabilities`.
- Produces: all 49 module Settings wrappers using one shared Advanced UI.

- [ ] Add RED tests for shared include count, Button class compatibility, layout motion keys, Grid overflow, sticky-per-device, attributes, and custom CSS.
- [ ] Add capability rendering without hardcoding type slugs inside shared Advanced.
- [ ] Preserve legacy keys via definition normalizers; do not mutate saved data during render.
- [ ] Run focused Basic/Layout/Advanced tests and browser QA for representative Basic, Layout, General, and Pro modules.

### Task 19: Extract optional module runtime and styles

**Files:**

- Create per-module `runtime.js`/`styles.css` only for types with type-specific behavior/styles.
- Create: `app/Support/PageBuilderElementorV24/ModuleUsageCollector.php`
- Create: `tests/Unit/PageBuilderElementorV24ModuleUsageCollectorTest.php`
- Modify: `frontend_renderer.blade.php` and module usage collection.
- Reduce: global frontend runtime and both global CSS files.

**Interfaces:**

- Produces: `ModuleUsageCollector::types(array $nodes): array` and active-used module asset list for each rendered page.

- [ ] Write RED tests rendering a page with two types and assert only their optional module assets plus core assets are emitted.
- [ ] Implement `types(array $nodes): array` using a deterministic recursive walk over `children` and Grid column children; return unique types in first-seen order without mutating page data.
- [ ] Move one runtime/style at a time and run its existing runtime/frontend parity tests.
- [ ] Keep foundation tokens, editor shell layout, drag/drop, shared Advanced, and generic runtime in core files.

### Task 20: Remove host type branches and legacy catalog

**Files:**

- Modify: `app.js`, `widget-registry.js`, `editor_shell.blade.php`, `render_node.blade.php`.
- Remove after backup: `config/pagebuilder_elementor_v24_widgets.php` and migrated public module copies.
- Modify affected tests to catalog-based discovery.

**Interfaces:**

- Produces: one catalog path with no legacy fallback.

- [ ] Add static tests rejecting legacy config reads, legacy public module paths, Pro shared paths, and known type gates that belong to definitions.
- [ ] Move remaining type-specific services to definitions/modules.
- [ ] Remove legacy fallback only after all 49 manifests pass catalog validation.
- [ ] Run full v2.4 PHP, Node, lint, Vite build, and `git diff --check`.

### Task 21: Final removal/restoration, parity, and isolation QA

**Files:**

- Create: `project-artifacts/qa/pagebuilder-v24-modular-final/QA_REPORT.md`
- Create: `project-artifacts/qa/pagebuilder-v24-modular-final/design-qa.md`
- Create final v2.3/v2.4 checksum artifacts.

**Interfaces:**

- Produces: release-readiness evidence and honest residual limits.

- [ ] Run all v2.4 PHP and Node tests, PHP lint, Vite build, and diff check.
- [ ] Run v2.3 Node and scoped PHP regression plus final checksum comparison; require zero v2.3 version-owned changes.
- [ ] Use a temporary fixture catalog to prove delete/move/restore behavior without deleting production modules.
- [ ] Browser QA authenticated v2.4: 47 toolbox widgets at baseline, representative module settings/canvas/frontend parity, shared Advanced, responsive modes, context menu outside click, and zero console errors/warnings.
- [ ] Verify inactive persisted node produces deterministic marker and unchanged JSON.
- [ ] Run `graphify . --update --no-viz --code-only` and record graph result/warnings.
- [ ] Report migration/database/save boundaries; do not claim persistence behavior not exercised.

## Plan self-review result

- Spec coverage: every locked requirement maps to Tasks 2-5, 10, 16, 18, 20, or 21.
- Placeholder scan: no placeholder tokens, speculative dependency, or undefined future service remains.
- Interface consistency: `ModuleCatalog` method names and manifest keys are consistent across discovery, asset, editor, renderer, Form guard, and QA tasks.
- Scope decomposition: four phases are independently runnable; every phase ends with full affected regression checks before the next phase starts.
