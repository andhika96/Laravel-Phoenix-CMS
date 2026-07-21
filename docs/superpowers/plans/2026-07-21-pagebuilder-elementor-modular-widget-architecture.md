# Page Builder Elementor Modular Widget Architecture Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Move all Page Builder Elementor widget defaults, canvas components, settings panels, toolbox metadata, and frontend renderers behind a registry-driven module contract while preserving existing saved JSON and runtime behavior.

**Architecture:** Laravel owns the enabled widget catalog and frontend view mapping. A small browser registry receives one definition script per widget, while `app.js` retains only editor orchestration and dynamically loads registered canvas/settings SFCs. Migration proceeds by vertical slices so centralized branches are removed only after parity tests pass.

**Tech Stack:** Laravel 13, PHP 8, Blade, Vue 3 CDN, `vue3-sfc-loader`, PHPUnit/Pest-compatible Laravel feature tests, PowerShell.

## Global Constraints

- Back up each existing file before its first modification using `.bak_YYYYMMDD_HHMMSS_modular_widgets`.
- Keep Vue 3 CDN and `vue3-sfc-loader`; add no build system or dependency.
- Preserve the `page_builder.vars` JSON schema and all saved setting keys.
- Preserve editor canvas, responsive controls, drag/drop behavior, and frontend markup/CSS behavior.
- A widget hidden from the toolbox must still render existing saved nodes.
- Stop a command that hangs or exceeds its bounded timeout, record it, and continue with narrower verification.

---

### Task 1: Registry Contract and Heading Vertical Slice

**Files:**
- Create: `config/pagebuilder_elementor_widgets.php`
- Create: `public/js/pagebuilder_elementor/widget-registry.js`
- Create: `public/js/pagebuilder_elementor/widgets/basic/heading/definition.js`
- Create: `public/js/pagebuilder_elementor/widgets/basic/heading/Canvas.vue`
- Create: `public/js/pagebuilder_elementor/widgets/basic/heading/Settings.vue`
- Create: `resources/views/pagebuilder_elementor/widgets/basic/heading.blade.php`
- Create: `tests/Feature/PageBuilderElementorWidgetRegistryTest.php`
- Modify: `resources/views/pagebuilder_elementor/editor_shell.blade.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`

**Interfaces:**
- Produces: `PageBuilderElementorWidgets.register(definition)`, `.get(type)`, `.all()`, `.toolbox()`.
- Produces definition fields: `type`, `label`, `category`, `icon`, `canvas`, `settings`, `defaults()`, `normalize(node)`.
- Produces Laravel catalog fields: `label`, `category`, `icon`, `definition`, `canvas`, `settings`, `view`, `toolbox`.

- [ ] **Step 1: Write failing registry contract tests**

```php
public function test_heading_module_exposes_complete_registry_contract(): void
{
    $heading = config('pagebuilder_elementor_widgets.heading');
    $this->assertSame('basic', $heading['category']);
    $this->assertFileExists(public_path($heading['definition']));
    $this->assertFileExists(public_path($heading['canvas']));
    $this->assertFileExists(public_path($heading['settings']));
    $this->assertTrue(view()->exists($heading['view']));
}

public function test_editor_loads_registry_before_app(): void
{
    $source = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));
    $this->assertLessThan(strpos($source, "pagebuilder_elementor/app.js"), strpos($source, "widget-registry.js"));
}
```

- [ ] **Step 2: Run RED check**

Run: `php artisan test --filter=PageBuilderElementorWidgetRegistryTest --stop-on-failure`

Expected: failure because the config, registry, and Heading module files do not exist.

- [ ] **Step 3: Implement the registry and Heading module**

The registry rejects duplicates, clones default objects, groups visible definitions by category, and provides unsupported canvas/settings fallbacks. The Heading definition preserves `text`, `tag`, `align`, `color`, and `cssClass`. `app.js` reads Heading through the registry while all unmigrated types retain their existing path.

- [ ] **Step 4: Run GREEN and regression checks**

Run:

```powershell
node --check public\js\pagebuilder_elementor\widget-registry.js
node --check public\js\pagebuilder_elementor\widgets\basic\heading\definition.js
php artisan test --filter=PageBuilderElementorWidgetRegistryTest
php artisan test --filter=PageBuilderElementor
```

Expected: all commands exit `0`.

### Task 2: Simple Basic Widget Modules

**Files:**
- Create module folders and `definition.js`, `Canvas.vue`, `Settings.vue` for `text-editor`, `image`, `button`, `divider`, `spacer`, and `icon` under `public/js/pagebuilder_elementor/widgets/basic/`.
- Create matching Blade partials under `resources/views/pagebuilder_elementor/widgets/basic/`.
- Modify: `config/pagebuilder_elementor_widgets.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `tests/Feature/PageBuilderElementorWidgetRegistryTest.php`

**Interfaces:**
- Consumes the Task 1 registry definition and Laravel catalog contracts.
- Produces registered types `text_editor`, `image`, `button`, `divider`, `spacer`, and `icon`.

- [ ] **Step 1: Extend the test with a data provider for all simple basic types**

```php
public static function simpleBasicWidgets(): array
{
    return [['text_editor'], ['image'], ['button'], ['divider'], ['spacer'], ['icon']];
}
```

For every type, assert catalog presence, existing definition/canvas/settings files, existing frontend view, and absence of its `selectedType==='type'` marker after migration.

- [ ] **Step 2: Run RED check**

Run: `php artisan test --filter=PageBuilderElementorWidgetRegistryTest --stop-on-failure`

Expected: failure on the first unregistered simple widget.

- [ ] **Step 3: Migrate each simple basic widget**

Move its current canvas markup without behavior changes, move its sidebar markup into `Settings.vue`, move exact defaults/normalization into `definition.js`, add its catalog entry and frontend partial, then remove only that type's centralized branches.

- [ ] **Step 4: Run GREEN checks**

Run the registry test, Icon parity test, responsive dimension parity test, JS syntax checks for definitions, and `git diff --check`.

### Task 3: Video Module

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/basic/video/definition.js`
- Create: `public/js/pagebuilder_elementor/widgets/basic/video/Canvas.vue`
- Create: `public/js/pagebuilder_elementor/widgets/basic/video/Settings.vue`
- Create: `resources/views/pagebuilder_elementor/widgets/basic/video.blade.php`
- Modify registry/catalog/dispatcher files from Task 1.
- Test: `tests/Feature/PageBuilderElementorVideoWidgetContentParityTest.php`

**Interfaces:**
- Preserves source types `youtube`, `vimeo`, `dailymotion`, `self_hosted`, and `videopress`.
- Consumes editor services `chooseMedia`, `clearMedia`, and unavailable-control notices.

- [ ] **Step 1: Add failing assertions** that Video defaults and source markers live in the module definition/settings and that the frontend view is registered.
- [ ] **Step 2: Run RED** with `php artisan test --filter=PageBuilderElementorVideoWidgetContentParityTest --stop-on-failure`.
- [ ] **Step 3: Move Video defaults, canvas, settings, and frontend markup** without adding unfinished Style/Advanced functionality.
- [ ] **Step 4: Run GREEN** with Video parity, registry, syntax, and diff checks.

### Task 4: General and Advanced Widget Modules

**Files:**
- Create module packages and Blade partials for `image_box`, `tabs`, and `accordion`.
- Modify registry/catalog/dispatcher files from Task 1.
- Test: existing Image Box, Tabs, Accordion, and Widget Advanced parity tests.

**Interfaces:**
- Preserves nested `tabItems` and `accordionItems` node collections.
- Consumes shared `TypographyControl`, `LinkControl`, `DynamicTagControl`, `CssFilterControl`, and `WidgetAdvancedControls`.
- Consumes application callbacks for nested drop targets and accordion runtime state.

- [ ] **Step 1: Add failing registry and source-location assertions** to the relevant parity tests.
- [ ] **Step 2: Run RED** for Image Box, Tabs, and Accordion tests individually.
- [ ] **Step 3: Migrate one type at a time**, retaining nested-drop callbacks in the application editor-service interface and moving widget-local controls into settings components.
- [ ] **Step 4: Run GREEN** for all four related parity test classes and `git diff --check`.

### Task 5: Grid and Row Grid Modules

**Files:**
- Create packages and frontend partials for `grid` and `row_grid` under layout widget paths.
- Modify registry/catalog/dispatcher files from Task 1.
- Test: registry and responsive dimension parity tests.

**Interfaces:**
- Preserves `columns`, sequential-fill behavior, nested ownership, responsive gaps, motion effects, and grid advanced settings.
- Consumes application-level column selection, resize, add/drop, and modal callbacks.

- [ ] **Step 1: Add failing tests** proving Grid/Row Grid settings paths and views are registered and centralized settings branches are absent after migration.
- [ ] **Step 2: Run RED** with registry and responsive tests.
- [ ] **Step 3: Migrate Grid then Row Grid**, extracting shared layout behavior only where both modules already use the same code.
- [ ] **Step 4: Run GREEN** with registry, responsive, syntax, full Page Builder Elementor tests, and diff checks.

### Task 6: Container and Container Fluid Modules

**Files:**
- Create packages and frontend partials for `container` and `container_fluid`.
- Modify registry/catalog/dispatcher files from Task 1.
- Test: registry, responsive dimension, and Widget Advanced parity tests.

**Interfaces:**
- Preserves flex/grid display, columns, shape dividers, backgrounds, overlays, responsive widths, position/motion effects, and recursive child rendering.
- Consumes application-level layout modal, drag/drop, selection, resize, media, responsive-device, and preview callbacks.

- [ ] **Step 1: Add failing module contract and source-location tests** for both container types.
- [ ] **Step 2: Run RED** with targeted registry/responsive tests.
- [ ] **Step 3: Migrate Container and Container Fluid** while keeping shared shape data and recursive `render_node` entry behavior centralized.
- [ ] **Step 4: Run GREEN** with targeted tests, full Page Builder Elementor tests, JS/PHP syntax, and diff checks.

### Task 7: Remove Centralized Widget Branches and Verify Compatibility

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `tests/Feature/PageBuilderElementorWidgetRegistryTest.php`

**Interfaces:**
- Consumes all registered definitions and frontend views from Tasks 1-6.
- Produces an orchestration-only `app.js` with dynamic canvas/settings resolution.

- [ ] **Step 1: Add failing architectural assertions**

```php
$app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
$this->assertStringNotContainsString("const widgetMap =", $app);
$this->assertStringNotContainsString("switch (type)", $app);
$this->assertStringNotContainsString("selectedType==='", $app);
```

Also assert every catalog type has a definition, canvas, settings, and frontend view.

- [ ] **Step 2: Run RED** and confirm remaining centralized markers cause the failure.
- [ ] **Step 3: Remove obsolete fallback branches**, leaving unknown-type safeguards and application-level orchestration.
- [ ] **Step 4: Run final automated verification**

```powershell
node --check public\js\pagebuilder_elementor\app.js
node --check public\js\pagebuilder_elementor\widget-registry.js
php -l resources\views\pagebuilder_elementor\partials\render_node.blade.php
php artisan test --filter=PageBuilderElementor
git diff --check
```

- [ ] **Step 5: Run bounded HTTP and browser QA**

Target flow: `/pagebuilder-elementor/create` -> insert Heading and one layout node -> edit a setting -> verify canvas update -> save/reload when available -> verify frontend output. Check page identity, nonblank DOM, framework overlay, console health, screenshot evidence, and interaction proof. Stop and record any browser command that hangs beyond its bounded timeout.

- [ ] **Step 6: Review backup inventory and working-tree diff**

Confirm every modified pre-existing file has a matching timestamped backup and no backup is referenced by runtime code.
