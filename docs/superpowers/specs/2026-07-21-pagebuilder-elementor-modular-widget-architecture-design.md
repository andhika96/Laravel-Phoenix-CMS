# Page Builder Elementor Modular Widget Architecture Design

## Status

Approved direction from the 2026-07-21 discussion, pending review of this written specification before implementation.

## Goal

Turn every current Page Builder Elementor widget, layout, and grid into an independently registered module whose editor canvas renderer, sidebar settings UI, defaults, normalization rules, toolbox metadata, and frontend renderer can be enabled or disabled without adding another type-specific branch to the main `app.js` or `render_node.blade.php` files.

## Non-goals

- Do not redesign the existing Elementor-like sidebar UI.
- Do not change the saved `page_builder.vars` JSON contract.
- Do not change existing widget behavior, responsive behavior, canvas box models, or frontend output.
- Do not add a JavaScript build tool or replace `vue3-sfc-loader`.
- Do not delete legacy widget support while saved pages may still reference it.

## Constraints

- Back up every existing runtime file before its first modification using a timestamped `.bak_YYYYMMDD_HHMMSS_modular_widgets` sibling file.
- Keep Vue 3 CDN and `vue3-sfc-loader` as the editor runtime.
- Existing saved nodes must continue to load through additive default normalization.
- A disabled module must remain renderable for existing saved pages unless it is explicitly removed through a separate content migration.
- Editor canvas, sidebar state, saved JSON, and Laravel frontend output must stay aligned.
- Refactor in vertical slices and keep the full Page Builder Elementor test suite green after each slice.

## Chosen Architecture

### 1. Server-side module catalog

Create `config/pagebuilder_elementor_widgets.php` as the authoritative enabled-module catalog. Each entry identifies:

- widget `type`;
- category, label, and icon for the toolbox;
- browser definition script;
- canvas SFC path;
- settings SFC path;
- Laravel frontend partial;
- whether the widget is visible in the toolbox.

Disabling `toolbox` visibility removes a widget from new insertions while preserving rendering of existing saved nodes. Completely unregistering a type is intentionally not the normal removal path because it would break saved content.

### 2. Browser registry

Create `public/js/pagebuilder_elementor/widget-registry.js`. It exposes a small global API:

```js
window.PageBuilderElementorWidgets.register(definition)
window.PageBuilderElementorWidgets.get(type)
window.PageBuilderElementorWidgets.all()
window.PageBuilderElementorWidgets.toolbox()
```

Every definition must provide:

```js
{
    type,
    label,
    category,
    icon,
    canvas,
    settings,
    defaults(),
    normalize(node),
}
```

Duplicate types and incomplete definitions fail loudly during editor bootstrap. Unknown saved types render a visible unsupported-widget placeholder instead of throwing and stopping the whole editor.

### 3. One package per widget

Each widget family moves to a folder with one clear responsibility per file:

```text
public/js/pagebuilder_elementor/widgets/basic/heading/
├── definition.js
├── Canvas.vue
└── Settings.vue
```

The same shape applies to layout, grid, general, and advanced widgets. `definition.js` owns metadata, defaults, and node normalization. `Canvas.vue` owns only editor canvas rendering. `Settings.vue` owns Content/Layout, Style, and Advanced panel markup and widget-local control logic.

Shared controls such as Advanced, Typography, Link, Dynamic Tag, and CSS Filter remain under `widgets/shared/` and are consumed by settings components.

### 4. Editor shell responsibilities

`editor_shell.blade.php` loads the registry bootstrap and enabled module definition scripts before `app.js`. `app.js` remains responsible only for application-level concerns:

- selection and history;
- drag/drop orchestration;
- page save/load;
- modal state;
- responsive preview state;
- CKFinder and shared editor services;
- loading the selected definition's canvas and settings components.

The existing `widgetMap`, hard-coded toolbox arrays, `makeNode()` switch branches, and `selectedType === ...` settings blocks are removed only after their corresponding module has passed its vertical-slice tests.

Settings components receive a stable editor-service object for genuinely application-level operations. Widget-local formatting, unit conversion, state tabs, and default helpers stay inside their module rather than expanding the service interface.

### 5. Frontend registry and partials

Create a frontend dispatcher that resolves the node type through the server-side module catalog and includes the registered Blade partial. Existing large renderer branches move into focused partials such as:

```text
resources/views/pagebuilder_elementor/widgets/basic/heading.blade.php
resources/views/pagebuilder_elementor/widgets/layout/container.blade.php
resources/views/pagebuilder_elementor/widgets/layout/grid.blade.php
```

Recursive child rendering continues through the existing `render_node` entry point. Shared PHP normalization and CSS helpers remain centralized until at least two modules need the same behavior; they are not duplicated merely to make folders look self-contained.

Unknown types produce an HTML comment in public rendering and do not break the rest of the page.

## Data Flow

1. Laravel reads the enabled module catalog and emits definition script paths into the editor shell.
2. Definition scripts register their metadata, defaults, normalization, canvas path, and settings path.
3. `app.js` builds the toolbox from registered definitions.
4. Inserting a widget calls its `defaults()` factory.
5. Loading saved JSON calls its `normalize(node)` function additively.
6. `BuilderNode` loads the registered `Canvas.vue` component.
7. The sidebar loads the registered `Settings.vue` component for the selected node.
8. Saving sends the same node tree and `settings` objects currently stored in `page_builder.vars`.
9. Frontend rendering resolves the node type to its registered Blade partial.

## Migration Order

The refactor uses vertical slices so every checkpoint remains runnable:

1. Registry infrastructure plus `Heading` pilot.
2. Remaining simple basic widgets: Text Editor, Image, Button, Divider, Spacer, Icon.
3. Video.
4. General widgets: Image Box and Tabs.
5. Advanced widget: Accordion.
6. Grid and Row Grid.
7. Container and Container Fluid.
8. Remove obsolete centralized branches and run full regression verification.

`Heading` is the pilot because it exercises defaults, normalization, toolbox registration, canvas loading, settings loading, saving, and frontend dispatch with the smallest behavioral surface. Container and Grid move last because they carry recursive layout, responsive, drag/drop, and frontend box-model risk.

## Backup Strategy

Immediately before implementation, create timestamped backups for all existing files that the first vertical slice will modify. Add further backups before later slices touch additional existing files. At minimum the initial backup set includes:

- `public/js/pagebuilder_elementor/app.js`;
- `resources/views/pagebuilder_elementor/editor_shell.blade.php`;
- `resources/views/pagebuilder_elementor/partials/render_node.blade.php`;
- current widget `.vue` files as each is migrated;
- relevant Page Builder Elementor tests if an existing test must be changed.

New files do not need sibling backups because they have no previous version. Backups are retained and reported; they are not included in runtime loading.

## Compatibility and Failure Handling

- Normalizers merge missing defaults without overwriting saved values.
- Legacy `className` and `cssClass` distinctions remain unchanged until a separately approved data migration.
- Module registration errors are shown in the console and editor status area.
- Missing settings components fall back to a read-only notice while preserving canvas rendering and page saving.
- Missing canvas components show an unsupported-widget placeholder containing the node type.
- Frontend rendering skips unknown nodes safely and continues rendering siblings.

## Testing Strategy

Follow red-green-refactor for each vertical slice.

### Registry contract tests

- enabled catalog entries expose required fields;
- registered types are unique;
- toolbox items come from visible registered modules;
- defaults return new objects rather than shared mutable state;
- unknown types use a safe fallback.

### Compatibility tests

- legacy saved Heading JSON loads unchanged;
- normalization adds absent defaults without replacing stored settings;
- save payload retains the current layout structure;
- disabling toolbox visibility does not disable existing-node rendering.

### Per-widget parity tests

- settings UI marker exists in the module `Settings.vue` rather than `app.js`;
- canvas component path resolves;
- frontend partial produces the same meaningful markup and classes;
- responsive and nested-widget tests remain green for layout modules.

### Verification commands

At the appropriate checkpoints run:

```powershell
node --check public\js\pagebuilder_elementor\app.js
node --check public\js\pagebuilder_elementor\widget-registry.js
php -l resources\views\pagebuilder_elementor\partials\render_node.blade.php
php artisan test --filter=PageBuilderElementor
git diff --check
```

Final verification also includes HTTP `200` checks for the create/edit editor routes, served asset checks for registry and SFC paths, and browser interaction checks covering insert, select, edit, responsive preview, save/reload, and frontend rendering.

## Acceptance Criteria

- Every currently supported widget type is represented by a registered module.
- Every module has its own canvas SFC, settings SFC, definition, and frontend partial or an explicitly documented shared partial.
- `app.js` contains no widget-specific settings markup and no widget-specific `makeNode()` switch.
- The toolbox is derived from the module catalog/registry.
- Existing `page_builder.vars` payloads remain compatible without database migration.
- Modules can be hidden from new insertions through configuration while existing content still renders.
- Editor and frontend behavior match the pre-refactor behavior.
- Full Page Builder Elementor tests, syntax checks, diff checks, HTTP checks, and selected browser flows pass.

## Rollback

Because each vertical slice preserves backups and commits independently, a failed slice can be rolled back to its timestamped backups without reverting already verified modules. No database rollback is required because the JSON schema is unchanged.
