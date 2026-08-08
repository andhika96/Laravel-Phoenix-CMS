# Page Builder Editor v2.3 Isolation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship a fully isolated Page Builder v2.3 that exactly adopts the approved v2.3 editor shell while preserving the complete current v2.0 feature and widget set.

**Architecture:** Keep the existing `pagebuilder_elementor` source tree as v2.0 and create a complete `pagebuilder_elementor_v23` runtime/server/rendering tree. Both versions use the neutral `Page_Builder` model and `page_builder` table, with a server-enforced `editor_version` discriminator preventing cross-version reads and writes. The v2.3 shell combines production state/actions with the structure and design tokens from `public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html`.

**Tech Stack:** Laravel 13, PHP 8.3, Blade, Vue 3 global runtime, vue3-sfc-loader, Axios, Sortable/vuedraggable, Bootstrap 5, Font Awesome/Bootstrap Icons, Node test runner, PHPUnit 12, SQLite test database, Playwright/in-app browser for visual QA.

## Global Constraints

- The approved visual source is `public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html`.
- v2.3 has one contextual left sidebar and no tool rail, Layers, Pages, Global Styles, or right sidebar.
- v2.0 keeps its current URLs, internal asset paths, layout schema, feature set, and visual design.
- v2.3 must not load or import internal v2.0 JavaScript, Vue, CSS, config, Blade, renderer, mail, or support-service files.
- v2.0 must not load or import internal v2.3 files.
- Laravel, the neutral `Page_Builder` model/table, vendor libraries, CKFinder, and public media may be shared.
- Existing rows are assigned `editor_version = '2.0'`; v2.3 creates only `2.3` rows.
- A cross-version page access returns HTTP 409 and never mutates persisted data.
- Do not add new dependencies.
- Do not edit or delete existing `*.bak*` files.
- Preserve the user's untracked prototype and static test; stage only in the task that adopts them.

---

### Task 1: Add the editor-version data contract

**Files:**
- Create: `database/migrations/2026_08_08_170000_add_editor_version_to_page_builder_table.php`
- Create: `tests/Feature/PageBuilderEditorVersionMigrationTest.php`
- Modify: `app/Models/Page_Builder/Page_Builder.php`

**Interfaces:**
- Produces: `Page_Builder::EDITOR_VERSION_V20 === '2.0'`
- Produces: `Page_Builder::EDITOR_VERSION_V23 === '2.3'`
- Produces: non-null indexed `page_builder.editor_version` with database default `2.0`

- [ ] **Step 1: Write the failing migration test**

```php
<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderEditorVersionMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Schema::create('page_builder', function (Blueprint $table): void {
            $table->id();
            $table->string('uri')->unique();
            $table->timestamps();
        });
    }

    public function test_migration_backfills_v20_and_defaults_new_rows_to_v20(): void
    {
        DB::table('page_builder')->insert(['uri' => 'existing', 'created_at' => now(), 'updated_at' => now()]);
        $migration = require database_path('migrations/2026_08_08_170000_add_editor_version_to_page_builder_table.php');
        $migration->up();

        $this->assertSame('2.0', DB::table('page_builder')->where('uri', 'existing')->value('editor_version'));
        DB::table('page_builder')->insert(['uri' => 'new', 'created_at' => now(), 'updated_at' => now()]);
        $this->assertSame('2.0', DB::table('page_builder')->where('uri', 'new')->value('editor_version'));
        $this->assertSame('2.0', Page_Builder::EDITOR_VERSION_V20);
        $this->assertSame('2.3', Page_Builder::EDITOR_VERSION_V23);
    }
}
```

- [ ] **Step 2: Run the test and confirm the missing migration failure**

Run: `php artisan test tests/Feature/PageBuilderEditorVersionMigrationTest.php`

Expected: FAIL because the migration file and model constants do not exist.

- [ ] **Step 3: Add constants and the reversible migration**

```php
// app/Models/Page_Builder/Page_Builder.php
public const EDITOR_VERSION_V20 = '2.0';
public const EDITOR_VERSION_V23 = '2.3';
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('page_builder', function (Blueprint $table): void {
            $table->string('editor_version', 10)->default('2.0')->index();
        });
    }

    public function down(): void
    {
        Schema::table('page_builder', function (Blueprint $table): void {
            $table->dropIndex(['editor_version']);
            $table->dropColumn('editor_version');
        });
    }
};
```

- [ ] **Step 4: Run the focused test**

Run: `php artisan test tests/Feature/PageBuilderEditorVersionMigrationTest.php`

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Models/Page_Builder/Page_Builder.php database/migrations/2026_08_08_170000_add_editor_version_to_page_builder_table.php tests/Feature/PageBuilderEditorVersionMigrationTest.php
git commit -m "feat: add page builder editor version contract"
```

### Task 2: Enforce the v2.0 server-side ownership guard

**Files:**
- Create: `tests/Feature/PageBuilderElementorV20VersionGuardTest.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementor/PageBuilderElementor_Controller.php`
- Modify: `app/Http/Requests/Page_Builder_Elementor/EditPageBuilderElementorRequest.php`
- Modify: `tests/Feature/PageBuilderElementorFormSubmissionTest.php`

**Interfaces:**
- Consumes: `Page_Builder::EDITOR_VERSION_V20`
- Produces: `resolveOwnedPage(string|int $idOrSlug): ?Page_Builder`
- Produces: v2.0 create/store writes `editor_version = '2.0'`
- Produces: v2.0 edit/update/data/preview/form rejects a v2.3 row with 409

- [ ] **Step 1: Write a failing v2.0 version-guard feature test**

Create an in-memory `page_builder` table matching the controller fields, including `editor_version`, and insert one `2.0` row and one `2.3` row. Assert:

```php
$this->getJson('/pagebuilder-elementor/data/v20-page')->assertOk();
$this->getJson('/pagebuilder-elementor/data/v23-page')
    ->assertStatus(409)
    ->assertJsonPath('editorVersion', '2.3');

$before = DB::table('page_builder')->where('uri', 'v23-page')->value('vars');
$this->postJson('/pagebuilder-elementor/update/v23-page', [
    'pageName' => 'Must Not Change',
    'pageStatus' => 'draft',
    'layout' => '[]',
])->assertStatus(409);
$this->assertSame($before, DB::table('page_builder')->where('uri', 'v23-page')->value('vars'));
```

- [ ] **Step 2: Run the guard test and confirm cross-version access currently succeeds**

Run: `php artisan test tests/Feature/PageBuilderElementorV20VersionGuardTest.php`

Expected: FAIL because v2.0 does not filter `editor_version`.

- [ ] **Step 3: Add one scoped page resolver and one conflict responder**

Use grouped identifier conditions so `orWhere('id', ...)` cannot bypass the version predicate:

```php
private function resolveOwnedPage($idOrSlug): ?Page_Builder
{
    return Page_Builder::query()
        ->where('editor_version', Page_Builder::EDITOR_VERSION_V20)
        ->where(fn ($query) => $query
            ->where('uri', $idOrSlug)
            ->orWhere('id', $idOrSlug))
        ->first();
}
```

Use a second lookup only when the scoped lookup misses. Return JSON 409 from JSON endpoints and `abort(409, ...)` from HTML endpoints. Change `create/store` data to include:

```php
'editor_version' => Page_Builder::EDITOR_VERSION_V20,
```

- [ ] **Step 4: Scope the edit request's ignored unique ID to v2.0**

```php
$page = Page_Builder::query()
    ->where('editor_version', Page_Builder::EDITOR_VERSION_V20)
    ->where(fn ($query) => $query->where('uri', $idOrSlug)->orWhere('id', $idOrSlug))
    ->first();
```

- [ ] **Step 5: Update the existing form test schema and fixtures**

Add `$table->string('editor_version', 10)->default('2.0');` to its in-memory table. Ensure its v2.0 page fixture explicitly writes `Page_Builder::EDITOR_VERSION_V20`.

- [ ] **Step 6: Run focused v2.0 regression tests**

Run: `php artisan test tests/Feature/PageBuilderElementorV20VersionGuardTest.php tests/Feature/PageBuilderElementorFormSubmissionTest.php`

Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/Web/PageBuilderElementor/PageBuilderElementor_Controller.php app/Http/Requests/Page_Builder_Elementor/EditPageBuilderElementorRequest.php tests/Feature/PageBuilderElementorV20VersionGuardTest.php tests/Feature/PageBuilderElementorFormSubmissionTest.php
git commit -m "feat: lock page builder v20 records"
```

### Task 3: Fork the complete v2.3 server boundary and routes

**Files:**
- Create: `app/Http/Controllers/Web/PageBuilderElementorV23/PageBuilderElementorV23Controller.php`
- Create: `app/Http/Requests/Page_Builder_Elementor_V23/AddPageBuilderElementorV23Request.php`
- Create: `app/Http/Requests/Page_Builder_Elementor_V23/EditPageBuilderElementorV23Request.php`
- Create tree: `app/Support/PageBuilderElementorV23/*.php`
- Create: `app/Mail/PageBuilderElementorV23FormMail.php`
- Create: `resources/views/emails/pagebuilder-elementor-v23-form-text.blade.php`
- Modify: `routes/experimentalFeaturesWebv2.php`
- Create: `tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php`

**Interfaces:**
- Consumes: `Page_Builder::EDITOR_VERSION_V23`
- Produces: route family `cms.core.pagebuilder_elementor_v23.*`
- Produces: v2.3 controller methods with the same public action signatures as v2.0
- Produces: v2.3 support namespace `App\Support\PageBuilderElementorV23`

- [ ] **Step 1: Write failing route and persistence tests**

Assert that all eight route names resolve and that store writes a v2.3 row:

```php
$this->assertSame('/pagebuilder-elementor/v2.3/create', route('cms.core.pagebuilder_elementor_v23.create', absolute: false));
$this->assertSame('/pagebuilder-elementor/v2.3/preview/demo', route('cms.core.pagebuilder_elementor_v23.preview', 'demo', false));

$this->postJson(route('cms.core.pagebuilder_elementor_v23.store'), [
    'pageName' => 'V23 Page',
    'pageStatus' => 'draft',
    'layout' => json_encode([['id' => 'heading-1', 'type' => 'heading', 'settings' => ['text' => 'Hello']]]),
])->assertOk()->assertJsonPath('success', true);

$this->assertDatabaseHas('page_builder', [
    'page_name' => 'V23 Page',
    'editor_version' => Page_Builder::EDITOR_VERSION_V23,
]);
```

Also create a v2.0 row and assert every v2.3 identifier endpoint returns 409 for it.

- [ ] **Step 2: Run the test and confirm missing routes/classes**

Run: `php artisan test tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php`

Expected: FAIL because the v2.3 route family does not exist.

- [ ] **Step 3: Copy active support services, mail, and request behavior**

Copy only active files from `app/Support/PageBuilderElementor/`; do not copy `*.bak*`. Change namespaces and internal references to `PageBuilderElementorV23`. Copy the form mail and its plain-text email view under v2.3 names, retain inline HTML delivery, then update `FormSubmissionHandler` to instantiate `PageBuilderElementorV23FormMail`.

Keep validation, URL safety, HTTP timeouts, rendition rules, display conditions, advanced styles, fragment cache, and form action semantics byte-for-byte equivalent except for namespace/view names.

- [ ] **Step 4: Copy the controller and requests under v2.3 namespaces**

Change constants, route names, views, request classes, and support dependencies. The v2.3 controller must use the same grouped ownership lookup as Task 2 with `EDITOR_VERSION_V23`, and store:

```php
'editor_version' => Page_Builder::EDITOR_VERSION_V23,
```

- [ ] **Step 5: Register the isolated route family**

```php
Route::controller(App\Http\Controllers\Web\PageBuilderElementorV23\PageBuilderElementorV23Controller::class)
    ->prefix('pagebuilder-elementor/v2.3')
    ->group(function () {
        Route::get('/create', 'create')->name('cms.core.pagebuilder_elementor_v23.create');
        Route::post('/store', 'store')->name('cms.core.pagebuilder_elementor_v23.store');
        Route::get('/edit/{idOrSlug}', 'edit')->name('cms.core.pagebuilder_elementor_v23.edit');
        Route::post('/update/{idOrSlug}', 'update')->name('cms.core.pagebuilder_elementor_v23.update');
        Route::get('/data/{idOrSlug}', 'getData')->name('cms.core.pagebuilder_elementor_v23.data');
        Route::get('/image-rendition', 'imageRendition')->name('cms.core.pagebuilder_elementor_v23.image_rendition');
        Route::get('/preview/{idOrSlug}', 'preview')->name('cms.core.pagebuilder_elementor_v23.preview');
        Route::post('/form/{idOrSlug}/{nodeId}', 'submitForm')->middleware('throttle:20,1')->name('cms.core.pagebuilder_elementor_v23.form.submit');
    });
```

- [ ] **Step 6: Run route/persistence tests**

Run: `php artisan test tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php`

Expected: PASS because this test covers route names, store, data, update rejection, and form ownership without rendering create/edit views. Shell rendering is covered after the view fork in Task 5.

- [ ] **Step 7: Commit the server boundary**

```bash
git add app/Http/Controllers/Web/PageBuilderElementorV23 app/Http/Requests/Page_Builder_Elementor_V23 app/Support/PageBuilderElementorV23 app/Mail/PageBuilderElementorV23FormMail.php resources/views/emails/pagebuilder-elementor-v23-form-text.blade.php routes/experimentalFeaturesWebv2.php tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php
git commit -m "feat: add isolated page builder v23 server routes"
```

### Task 4: Fork every active v2.0 editor and renderer file

**Files:**
- Create tree: `public/js/pagebuilder_elementor_v23/**`
- Create: `public/assets/css/pagebuilder_elementor_v23.css`
- Create: `public/assets/css/frontend_elementor_v23.css`
- Create tree: `resources/views/pagebuilder_elementor_v23/**`
- Create: `resources/data/pagebuilder_elementor_v23_shapes.json`
- Create: `config/pagebuilder_elementor_v23_widgets.php`
- Create: `tests/Feature/PageBuilderElementorV23AssetIsolationTest.php`

**Interfaces:**
- Produces: `window.PAGE_BUILDER_ELEMENTOR_V23_CONTEXT`
- Produces: `window.PageBuilderElementorV23Widgets`
- Produces: `window.PageBuilderElementorV23Runtime`
- Produces: `window.PageBuilderElementorV23ComplexWidgetRuntime`
- Produces: `window.PB_ELEMENTOR_V23_SHAPES`
- Produces: `window.PB_ELEMENTOR_V23_FONT_FAMILIES`

- [ ] **Step 1: Write the failing asset-isolation test**

The test must recursively scan all active v2.3 source files and fail on missing trees or any exact v2.0 internal reference:

```php
$forbidden = [
    '/js/pagebuilder_elementor/',
    'assets/css/pagebuilder_elementor.css',
    'assets/css/frontend_elementor.css',
    "config('pagebuilder_elementor_widgets",
    "view('pagebuilder_elementor.",
    "@include('pagebuilder_elementor.",
    'App\\Support\\PageBuilderElementor\\',
    'cms.core.pagebuilder_elementor.',
    'PAGE_BUILDER_ELEMENTOR_CONTEXT',
    'PageBuilderElementorWidgets',
    'PageBuilderElementorRuntime',
];
```

Exclude the v2.3 forms of those strings by matching exact delimiters, not loose substrings. Assert every configured definition, canvas, settings, and view exists.

- [ ] **Step 2: Run the isolation test and confirm missing source trees**

Run: `php artisan test tests/Feature/PageBuilderElementorV23AssetIsolationTest.php`

Expected: FAIL because v2.3 source files do not exist.

- [ ] **Step 3: Mechanically copy active source only**

Copy the existing directories while excluding `*.bak*`:

```powershell
$pbSource = (Resolve-Path 'public\js\pagebuilder_elementor').Path
$pbTarget = Join-Path (Resolve-Path 'public\js').Path 'pagebuilder_elementor_v23'
Get-ChildItem -LiteralPath $pbSource -Recurse -File | Where-Object { $_.Name -notlike '*.bak*' } | ForEach-Object {
    $relative = $_.FullName.Substring($pbSource.Length).TrimStart('\')
    $destination = Join-Path $pbTarget $relative
    New-Item -ItemType Directory -Force -Path (Split-Path $destination) | Out-Null
    Copy-Item -LiteralPath $_.FullName -Destination $destination
}
```

Repeat the same active-file copy from `resources/views/pagebuilder_elementor` to `resources/views/pagebuilder_elementor_v23`. Copy the two CSS files, shapes JSON, and widget config to their v2.3 names.

- [ ] **Step 4: Rewrite only version-bound identifiers in copied files**

Apply this exact replacement map only inside v2.3 targets:

```text
/js/pagebuilder_elementor/                -> /js/pagebuilder_elementor_v23/
pagebuilder_elementor.widgets             -> pagebuilder_elementor_v23.widgets
pagebuilder_elementor.partials            -> pagebuilder_elementor_v23.partials
config('pagebuilder_elementor_widgets     -> config('pagebuilder_elementor_v23_widgets
data/pagebuilder_elementor_shapes.json    -> data/pagebuilder_elementor_v23_shapes.json
App\Support\PageBuilderElementor\        -> App\Support\PageBuilderElementorV23\
cms.core.pagebuilder_elementor.           -> cms.core.pagebuilder_elementor_v23.
PAGE_BUILDER_ELEMENTOR_CONTEXT            -> PAGE_BUILDER_ELEMENTOR_V23_CONTEXT
PB_ELEMENTOR_SHAPES                       -> PB_ELEMENTOR_V23_SHAPES
PB_ELEMENTOR_FONT_FAMILIES                -> PB_ELEMENTOR_V23_FONT_FAMILIES
PageBuilderElementorWidgets               -> PageBuilderElementorV23Widgets
PageBuilderElementorRuntime               -> PageBuilderElementorV23Runtime
PageBuilderElementorComplexWidgetRuntime  -> PageBuilderElementorV23ComplexWidgetRuntime
```

Do not alter CSS `pb-*` class names or saved widget type names; they are layout/runtime contracts rather than cross-version imports.

- [ ] **Step 5: Prove there are no stale v2.0 references**

Run:

```powershell
rg -n '/js/pagebuilder_elementor/|pagebuilder_elementor\.(widgets|partials)|pagebuilder_elementor_widgets|PageBuilderElementor\\|cms\.core\.pagebuilder_elementor\.|PAGE_BUILDER_ELEMENTOR_CONTEXT|PageBuilderElementorWidgets|PageBuilderElementorRuntime' public/js/pagebuilder_elementor_v23 resources/views/pagebuilder_elementor_v23 config/pagebuilder_elementor_v23_widgets.php
```

Expected: no output.

- [ ] **Step 6: Run the isolation test**

Run: `php artisan test tests/Feature/PageBuilderElementorV23AssetIsolationTest.php`

Expected: PASS.

- [ ] **Step 7: Commit the complete fork**

```bash
git add public/js/pagebuilder_elementor_v23 public/assets/css/pagebuilder_elementor_v23.css public/assets/css/frontend_elementor_v23.css resources/views/pagebuilder_elementor_v23 resources/data/pagebuilder_elementor_v23_shapes.json config/pagebuilder_elementor_v23_widgets.php tests/Feature/PageBuilderElementorV23AssetIsolationTest.php
git commit -m "feat: fork complete page builder v23 runtime"
```

### Task 5: Wire the v2.3 editor shell and frontend shell exclusively to v2.3

**Files:**
- Modify: `resources/views/pagebuilder_elementor_v23/editor_shell.blade.php`
- Modify: `resources/views/pagebuilder_elementor_v23/frontend_renderer.blade.php`
- Modify: `app/Http/Controllers/Web/PageBuilderElementorV23/PageBuilderElementorV23Controller.php`
- Modify: `tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php`
- Create: `tests/Feature/PageBuilderElementorV23ShellTest.php`

**Interfaces:**
- Produces: browser context `PAGE_BUILDER_ELEMENTOR_V23_CONTEXT`
- Produces: editor mount element `#pbElementorV23App`
- Produces: v2.3 preview rendered only by `pagebuilder_elementor_v23.frontend_renderer`

- [ ] **Step 1: Write failing shell assertions**

```php
$html = $this->get(route('cms.core.pagebuilder_elementor_v23.create'))->assertOk()->getContent();
$this->assertStringContainsString('id="pbElementorV23App"', $html);
$this->assertStringContainsString('PAGE_BUILDER_ELEMENTOR_V23_CONTEXT', $html);
$this->assertStringContainsString('assets/css/pagebuilder_elementor_v23.css', $html);
$this->assertStringContainsString('js/pagebuilder_elementor_v23/app.js', $html);
$this->assertStringNotContainsString('js/pagebuilder_elementor/app.js', $html);
```

- [ ] **Step 2: Run the test and confirm copied shell still points at v2.0 identifiers**

Run: `php artisan test tests/Feature/PageBuilderElementorV23ShellTest.php`

Expected: FAIL.

- [ ] **Step 3: Update editor shell context and assets**

The shell must publish:

```blade
window.PAGE_BUILDER_ELEMENTOR_V23_CONTEXT = {
    mode: @json($mode),
    editorVersion: '2.3',
    saveUrl: @json($saveUrl),
    csrfToken: @json(csrf_token()),
    pageData: @json($pageData),
    imageRenditionUrl: @json(route('cms.core.pagebuilder_elementor_v23.image_rendition')),
    previewUrl: @json($pageData ? route('cms.core.pagebuilder_elementor_v23.preview', $pageData->uri) : ''),
    dynamicPreviewContext: {
        page_excerpt: @json((string) ($pageData->description ?? $pageData->excerpt ?? '')),
        featured_image: @json((string) ($pageData->featured_image ?? $pageData->cover_image ?? '')),
        page_url: @json($pageData ? route('cms.core.pagebuilder_elementor_v23.preview', $pageData->uri) : ''),
        site_title: @json(config('app.name')),
        site_url: @json(config('app.url')),
        user_display_name: @json((string) (auth()->user()->name ?? '')),
    },
};
```

Load only v2.3 CSS, runtime, registry, configured definitions, app, and shapes/font globals. Vendor CDN/assets remain shared.

- [ ] **Step 4: Update the frontend renderer**

Use `frontend_elementor_v23.css`, include `pagebuilder_elementor_v23.partials.render_node`, and load `pagebuilder_elementor_v23/frontend-runtime.js` only.

- [ ] **Step 5: Run shell, route, and isolation tests**

Run: `php artisan test tests/Feature/PageBuilderElementorV23ShellTest.php tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php tests/Feature/PageBuilderElementorV23AssetIsolationTest.php`

Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add resources/views/pagebuilder_elementor_v23/editor_shell.blade.php resources/views/pagebuilder_elementor_v23/frontend_renderer.blade.php app/Http/Controllers/Web/PageBuilderElementorV23/PageBuilderElementorV23Controller.php tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php tests/Feature/PageBuilderElementorV23ShellTest.php
git commit -m "feat: wire isolated page builder v23 shells"
```

### Task 6: Port the approved v2.3 shell design into the production Vue editor

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v23.css`
- Modify: `resources/views/pagebuilder_elementor_v23/editor_shell.blade.php`
- Add: `public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html`
- Add: `tests/pagebuilder-editor-redesign-v23-static.test.mjs`
- Create: `tests/pagebuilder-editor-v23-production-static.test.mjs`

**Interfaces:**
- Consumes: all existing copied v2.3 state/actions from Tasks 4-5
- Produces: `elementSearch`, `filteredToolboxGroups`, `leftCollapsed`, `previewMode`, and `showToolboxPanel()` UI state
- Produces: one `.side-panel.left-panel` contextual sidebar

- [ ] **Step 1: Extend the static contract from prototype to production**

The new Node test reads v2.3 `app.js` and CSS and asserts:

```js
assert.match(app, /class="builder-app"/);
assert.match(app, /class="topbar"/);
assert.match(app, /class="workspace"/);
assert.equal((app.match(/<aside\b/g) || []).length, 1);
assert.match(app, /side-panel left-panel/);
assert.match(app, /placeholder="Search widgets"/);
assert.doesNotMatch(app, /tool-rail|activeTool\s*===\s*['"](?:layers|pages|global)/);
assert.doesNotMatch(app, /side-panel right/);
assert.match(css, /--brand:\s*#5b4cf0/);
assert.match(css, /grid-template-rows:\s*58px minmax\(0, 1fr\)/);
```

Also preserve the existing prototype test that proves v2.2 remains historical and v2.3 has one sidebar.

- [ ] **Step 2: Run Node contracts and confirm production v2.3 still has the old shell**

Run: `node --test tests/pagebuilder-editor-redesign-v23-static.test.mjs tests/pagebuilder-editor-v23-production-static.test.mjs`

Expected: prototype contract PASS; production contract FAIL.

- [ ] **Step 3: Replace only the outer application shell markup**

Port the prototype's topbar/workspace/sidebar/stage structure. Bind production state:

- `pageName`, `saveState`, `canUndo`, `canRedo`, `undo`, `redo`, and `savePage` in the topbar.
- `responsiveDevice` and `setResponsiveDevice` in the centered device switcher.
- `previewMode` to collapse the sidebar without removing editor data.
- `selectedNode || selectedColumnContext` to switch the same sidebar from Elements to settings.
- existing draggable toolbox lists and callbacks inside prototype-style element cards.
- existing root draggable canvas, BuilderNode, resize overlay, modals, toast, and custom CSS editor without behavioral rewrites.

Do not copy prototype sample sections or sample page data.

- [ ] **Step 4: Port prototype design tokens and geometry into the isolated CSS**

Adopt the prototype variables and shell rules for `builder-app`, `topbar`, `workspace`, `side-panel`, `panel-header`, `panel-body`, `element-grid`, `element-card`, stage/canvas, responsive frame, selection summary, and collapse/preview states. Adapt existing widget/control selectors under that shell rather than deleting their production styles.

Use Bootstrap Icons in the v2.3 shell because the approved prototype uses them; leave widget Font Awesome contracts intact.

- [ ] **Step 5: Implement element search without changing the registry**

```js
const elementSearch = ref('');
const filteredToolboxGroups = computed(() => {
    const query = elementSearch.value.trim().toLowerCase();
    return ['layout', 'basic', 'general', 'pro', 'advanced']
        .map(name => ({
            name,
            items: (toolbox[name] || []).filter(item => !query || `${item.label} ${item.type}`.toLowerCase().includes(query)),
        }))
        .filter(group => group.items.length);
});
```

- [ ] **Step 6: Run production design contracts**

Run: `node --test tests/pagebuilder-editor-redesign-v23-static.test.mjs tests/pagebuilder-editor-v23-production-static.test.mjs`

Expected: PASS.

- [ ] **Step 7: Commit the approved prototype and production shell**

```bash
git add public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html tests/pagebuilder-editor-redesign-v23-static.test.mjs tests/pagebuilder-editor-v23-production-static.test.mjs public/js/pagebuilder_elementor_v23/app.js public/assets/css/pagebuilder_elementor_v23.css resources/views/pagebuilder_elementor_v23/editor_shell.blade.php
git commit -m "feat: apply approved design to page builder v23"
```

Do not stage the existing timestamped `.bak*` files.

### Task 7: Preserve page settings and every editor action inside the new shell

**Files:**
- Modify: `public/js/pagebuilder_elementor_v23/app.js`
- Modify: `public/assets/css/pagebuilder_elementor_v23.css`
- Modify: `tests/pagebuilder-editor-v23-production-static.test.mjs`
- Create: `tests/pagebuilder-v23-functional-parity-static.test.mjs`

**Interfaces:**
- Produces: `pageSettingsOpen`, `openPageSettings()`, `closePageSettings()`
- Produces: topbar page-name trigger for Page Name, Page Status, and Custom CSS Editor
- Preserves: existing save, undo/redo, responsive, drag/drop, nested insertion, media, icon, RTE, modal, toast, and custom-CSS APIs

- [ ] **Step 1: Write a failing functional parity contract**

Compare required function markers in v2.0 and v2.3 and assert v2.3 still exposes them:

```js
for (const marker of [
  'function undo()', 'function redo()', 'async function savePage()',
  'function onRootAdd', 'function onAddContainer', 'function onAddCol',
  'function showToolboxPanel', 'function openCustomCssEditor',
  'function openMediaPicker', 'function openIconLibrary',
  'function setResponsiveDevice', 'function startColumnResize',
  'function rerouteTabsDropToNestedColumn', 'function rerouteAccordionDropToNestedColumn',
]) {
  assert.match(v20, new RegExp(escapeRegExp(marker)));
  assert.match(v23, new RegExp(escapeRegExp(marker)));
}
```

Assert the v2.3 template still mounts all existing modal state and `BuilderNode` callbacks.

- [ ] **Step 2: Run the test and identify any actions lost during shell port**

Run: `node --test tests/pagebuilder-v23-functional-parity-static.test.mjs`

Expected: FAIL only for actions or bindings omitted by Task 6.

- [ ] **Step 3: Add the page-settings popover without adding a permanent toolbar item**

Make the existing topbar `.page-name` element a button with unchanged visual styling. Its anchored popover contains the production `pageName` input, `pageStatus` select, custom-CSS summary, and existing `openCustomCssEditor` action. Close on Escape, outside click, preview mode, or successful save.

This preserves the approved default topbar exactly while retaining all page-level functions and avoiding a Global Styles tool.

- [ ] **Step 4: Restore any missing bindings from the parity failure**

Keep the copied implementations unchanged where possible. Reattach callbacks in the new template instead of creating duplicate handlers. Ensure `showToolboxPanel()` clears selection and retains pending nested insertion behavior exactly as v2.0.

- [ ] **Step 5: Run static parity and design tests**

Run: `node --test tests/pagebuilder-editor-v23-production-static.test.mjs tests/pagebuilder-v23-functional-parity-static.test.mjs`

Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add public/js/pagebuilder_elementor_v23/app.js public/assets/css/pagebuilder_elementor_v23.css tests/pagebuilder-editor-v23-production-static.test.mjs tests/pagebuilder-v23-functional-parity-static.test.mjs
git commit -m "feat: preserve editor actions in v23 shell"
```

### Task 8: Complete isolated frontend rendering, forms, and support-service wiring

**Files:**
- Modify tree: `resources/views/pagebuilder_elementor_v23/partials/**`
- Modify tree: `resources/views/pagebuilder_elementor_v23/widgets/**`
- Modify: `public/js/pagebuilder_elementor_v23/frontend-runtime.js`
- Modify: `app/Support/PageBuilderElementorV23/FormSubmissionHandler.php`
- Create: `tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php`
- Create: `tests/Feature/PageBuilderElementorV23FormSubmissionTest.php`
- Create: `tests/pagebuilder-v23-frontend-runtime.test.mjs`

**Interfaces:**
- Consumes: `config('pagebuilder_elementor_v23_widgets')`
- Consumes: `cms.core.pagebuilder_elementor_v23.form.submit`
- Produces: v2.3 Blade output and frontend runtime with no v2.0 internal dependency

- [ ] **Step 1: Write failing representative rendering tests**

Render one layout widget, one Basic widget, one General widget, and one Pro form through `pagebuilder_elementor_v23.partials.render_node`. Assert the expected semantic hooks and that the form action URL begins `/pagebuilder-elementor/v2.3/form/`.

```php
$html = view('pagebuilder_elementor_v23.partials.render_node', [
    'node' => ['id' => 'heading-v23', 'type' => 'heading', 'settings' => ['text' => 'V23 Heading']],
])->render();
$this->assertStringContainsString('V23 Heading', $html);
```

- [ ] **Step 2: Clone the v2.0 form submission tests for v2.3 ownership**

Keep all validation, collect, email/email2, webhook, redirect, upload, safe-URL, and throttling assertions. Change route family, mail class, support namespace, and fixture `editor_version` to `2.3`. Add one test proving a v2.0 page cannot receive submissions through the v2.3 endpoint.

- [ ] **Step 3: Run rendering/form tests and collect stale references**

Run: `php artisan test tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23FormSubmissionTest.php`

Expected: FAIL on any remaining v2.0 config, view, route, mail, or support-service reference.

- [ ] **Step 4: Replace all remaining version-bound references in copied renderers**

Do not share renderer partials. Ensure dynamic tags, renditions, advanced style resolution, display conditions, cache, shapes, registered view dispatch, form routes, and Pro rendering resolve through v2.3 files and namespaces.

- [ ] **Step 5: Add frontend runtime test against the v2.3 file**

Adapt the existing Pro frontend runtime test to import `public/js/pagebuilder_elementor_v23/frontend-runtime.js` and assert the v2.3 runtime global. Keep carousel/slides, countdown, hotspot, flip-box, and form behavior assertions.

- [ ] **Step 6: Run frontend tests**

Run: `php artisan test tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23FormSubmissionTest.php`

Run: `node --test tests/pagebuilder-v23-frontend-runtime.test.mjs`

Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add resources/views/pagebuilder_elementor_v23 public/js/pagebuilder_elementor_v23/frontend-runtime.js app/Support/PageBuilderElementorV23/FormSubmissionHandler.php tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23FormSubmissionTest.php tests/pagebuilder-v23-frontend-runtime.test.mjs
git commit -m "feat: isolate page builder v23 frontend runtime"
```

### Task 9: Prove complete widget and settings parity

**Files:**
- Create: `tests/Feature/PageBuilderElementorV23WidgetParityTest.php`
- Create: `tests/pagebuilder-v23-widget-runtime-parity.test.mjs`
- Modify copied v2.3 widget/config/view files only when the tests expose a gap

**Interfaces:**
- Consumes: v2.0 and v2.3 widget catalogs
- Produces: strict guarantee that v2.3 contains every v2.0 type and independently owned module file

- [ ] **Step 1: Write the catalog parity test**

```php
$v20 = config('pagebuilder_elementor_widgets');
$v23 = config('pagebuilder_elementor_v23_widgets');

$this->assertSame(array_keys($v20), array_keys($v23));
foreach ($v20 as $type => $module) {
    $copy = $v23[$type];
    $this->assertSame($module['type'], $copy['type']);
    $this->assertSame($module['label'], $copy['label']);
    $this->assertSame($module['category'], $copy['category']);
    $this->assertSame($module['toolbox'], $copy['toolbox']);
    $this->assertStringContainsString('pagebuilder_elementor_v23', $copy['definition']);
    $this->assertStringContainsString('pagebuilder_elementor_v23', $copy['canvas']);
    $this->assertStringContainsString('pagebuilder_elementor_v23', $copy['settings']);
    $this->assertFileExists(public_path($copy['definition']));
    $this->assertFileExists(public_path($copy['canvas']));
    $this->assertFileExists(public_path($copy['settings']));
    $this->assertTrue(view()->exists($copy['view']));
}
```

- [ ] **Step 2: Add runtime/source parity checks**

For every v2.0 `definition.js`, derive the relative target path in v2.3 and assert that the v2.3 source contains the same widget `type`, `defaults`, `normalize(node)`, canvas path, and settings path. Scan all copied Settings/Canvas Vue files to ensure no active v2.0 path remains.

- [ ] **Step 3: Run parity tests**

Run: `php artisan test tests/Feature/PageBuilderElementorV23WidgetParityTest.php`

Run: `node --test tests/pagebuilder-v23-widget-runtime-parity.test.mjs`

Expected: PASS only when every layout, Basic, General, Advanced, and Pro module is independently present.

- [ ] **Step 4: Run the complete existing Page Builder feature suite**

Run: `php artisan test --filter=PageBuilderElementor`

Expected: all existing v2.0 tests PASS, proving the fork did not regress v2.0.

- [ ] **Step 5: Run all Page Builder Node tests**

Run: `node --test tests/pagebuilder-*.test.mjs`

Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add tests/Feature/PageBuilderElementorV23WidgetParityTest.php tests/pagebuilder-v23-widget-runtime-parity.test.mjs public/js/pagebuilder_elementor_v23 resources/views/pagebuilder_elementor_v23 config/pagebuilder_elementor_v23_widgets.php
git commit -m "test: prove page builder v23 widget parity"
```

### Task 10: Browser verification and visual fidelity loop

**Files:**
- Modify as evidence requires: `public/js/pagebuilder_elementor_v23/app.js`
- Modify as evidence requires: `public/assets/css/pagebuilder_elementor_v23.css`
- Modify as evidence requires: v2.3-owned widget Settings/Canvas files
- Evidence only, do not commit: `tmp/pagebuilder-v20-baseline.png`
- Evidence only, do not commit: `tmp/pagebuilder-v23-elements.png`
- Evidence only, do not commit: `tmp/pagebuilder-v23-container-settings.png`
- Evidence only, do not commit: `tmp/pagebuilder-v23-widget-settings.png`
- Evidence only, do not commit: `tmp/pagebuilder-v23-responsive.png`
- Evidence only, do not commit: `tmp/pagebuilder-v23-preview.png`

**Interfaces:**
- Consumes: production v2.0 and v2.3 routes
- Produces: visual and interaction evidence for every critical acceptance state

- [ ] **Step 1: Start or reuse the local Laravel application**

Run: `php artisan route:list --name=pagebuilder_elementor_v23`

Expected: all eight v2.3 routes appear.

If no local server is running, run `php artisan serve --host=127.0.0.1 --port=8000` in a background terminal and keep its logs visible.

- [ ] **Step 2: Capture the untouched v2.0 baseline**

Open `/pagebuilder-elementor/create`, wait for all modules to settle, and capture the full editor viewport to `tmp/pagebuilder-v20-baseline.png`. Do not save a page.

- [ ] **Step 3: Verify the initial v2.3 Elements state against the prototype**

Open `/pagebuilder-elementor/v2.3/create` and compare side-by-side with `public/mockups/pagebuilder-editor-redesign-prototype-v2.3.html`. Confirm topbar height/sections, one sidebar, search, card geometry, stage, canvas, colors, spacing, borders, shadows, typography, and absence of the removed tools. Capture `tmp/pagebuilder-v23-elements.png`.

- [ ] **Step 4: Exercise the complete critical editor flow**

Perform in the browser:

1. Change Page Name and status through the topbar page-name popover.
2. Open/edit/apply Custom CSS, then close the modal.
3. Add a container by click and by drag/drop.
4. Add Heading, Image, Tabs, Accordion, and one Pro widget.
5. Add nested widgets into Tabs and Accordion targets.
6. Select container, column, and widget; confirm the same sidebar switches to their production settings.
7. Edit Content, Style, and Advanced values and observe live canvas updates.
8. Resize columns where supported.
9. Switch desktop/tablet/mobile and verify responsive values.
10. Undo and redo changes.
11. Toggle Preview and return to Editor.
12. Save, follow the returned v2.3 edit URL, reload, and confirm persistence.
13. Open v2.3 preview and verify interactive frontend widgets.

Capture the named state screenshots as evidence.

- [ ] **Step 5: Compare production screenshots with the prototype**

Use image inspection at original detail. Fix only v2.3 CSS/template files for visible differences. Repeat until the shell and standard states match the prototype while preserving production controls.

- [ ] **Step 6: Re-open v2.0 and compare with baseline**

Confirm its structure and design are unchanged. Capture a post-change screenshot and compare it with `tmp/pagebuilder-v20-baseline.png`; investigate any difference caused by shared source leakage.

- [ ] **Step 7: Run final automated verification after visual fixes**

Run:

```bash
php artisan test tests/Feature/PageBuilderEditorVersionMigrationTest.php tests/Feature/PageBuilderElementorV20VersionGuardTest.php tests/Feature/PageBuilderElementorV23RoutesAndPersistenceTest.php tests/Feature/PageBuilderElementorV23AssetIsolationTest.php tests/Feature/PageBuilderElementorV23ShellTest.php tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23FormSubmissionTest.php tests/Feature/PageBuilderElementorV23WidgetParityTest.php
php artisan test --filter=PageBuilderElementor
node --test tests/pagebuilder-*.test.mjs
php artisan route:list --name=pagebuilder_elementor
git diff --check
```

Expected: all tests PASS, both route families are listed, and `git diff --check` prints nothing.

- [ ] **Step 8: Commit final visual corrections**

```bash
git add public/js/pagebuilder_elementor_v23/app.js public/assets/css/pagebuilder_elementor_v23.css public/js/pagebuilder_elementor_v23/widgets resources/views/pagebuilder_elementor_v23 tests/Feature/PageBuilderElementorV23FrontendRenderingTest.php tests/Feature/PageBuilderElementorV23FormSubmissionTest.php tests/Feature/PageBuilderElementorV23WidgetParityTest.php tests/pagebuilder-editor-v23-production-static.test.mjs tests/pagebuilder-v23-functional-parity-static.test.mjs tests/pagebuilder-v23-frontend-runtime.test.mjs tests/pagebuilder-v23-widget-runtime-parity.test.mjs
git commit -m "fix: finish page builder v23 visual parity"
```

Do not stage unrelated user files or `tmp/*.png` evidence.

### Task 11: Completion audit against every acceptance criterion

**Files:**
- Read: `docs/superpowers/specs/2026-08-08-pagebuilder-editor-v23-isolation-design.md`
- Read: all files and test outputs created above
- Modify only if audit finds a concrete gap

**Interfaces:**
- Produces: requirement-by-requirement evidence that the feature is genuinely complete

- [ ] **Step 1: Audit isolation**

Confirm independent routes, controllers, requests, support services, mail/views, editor shell, app/runtime, widget tree, CSS, config, shapes, renderers, and tests. Run the forbidden-reference scan from Task 4 in both directions.

- [ ] **Step 2: Audit data locking**

Inspect migration, create/store payloads, every page lookup in both controllers, edit-request unique lookups, previews, data endpoints, and form endpoints. Confirm every cross-version write test checks the database remains unchanged.

- [ ] **Step 3: Audit visual fidelity**

Inspect the browser screenshots for all named states and compare the DOM/CSS source against prototype markers. A passing static test alone is insufficient.

- [ ] **Step 4: Audit functional parity**

Compare catalog keys, module files, runtime APIs, renderer dispatch, support services, frontend hooks, and the browser flow. A representative widget smoke test alone is insufficient; catalog/source inventory and existing suites must also pass.

- [ ] **Step 5: Inspect repository state**

Run: `git status --short`

Expected: only known user-owned timestamped backup files may remain untracked. No required implementation file is untracked or accidentally omitted.

- [ ] **Step 6: Mark completion only after all evidence is present**

If any criterion lacks direct evidence, return to the owning task and fix or verify it. Do not mark the goal complete based on intent or partial green tests.
