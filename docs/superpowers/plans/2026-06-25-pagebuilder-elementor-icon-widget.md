# PageBuilder Elementor Icon Widget Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a first-pass Elementor-like `Icon` widget and local icon library modal while syncing the `pagebuilder_elementor` editor shell to Font Awesome Pro `5.15.3`.

**Architecture:** Keep the implementation inside the existing builder surfaces: the editor shell loads the shared icon library, `public/js/pagebuilder_elementor/app.js` owns widget state plus modal UI, a new Vue widget component renders the canvas preview, and the Blade renderer emits matching frontend markup. Use local Font Awesome metadata under `public/assets/plugins/fontawesome/5.15.3` to drive modal groups and search instead of introducing any remote fetch dependency.

**Tech Stack:** Laravel Blade, Vue 3 template strings in `public/js/pagebuilder_elementor/app.js`, Vue SFC widget components via `vue3-sfc-loader`, Font Awesome Pro `5.15.3` local assets and metadata, Laravel feature tests.

---

### Task 1: Lock the New Surface With Focused Failing Coverage

**Files:**
- Create: `tests/Feature/PageBuilderElementorIconWidgetContentParityTest.php`
- Verify: `resources/views/pagebuilder_elementor/editor_shell.blade.php`
- Verify: `public/js/pagebuilder_elementor/app.js`
- Verify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`

- [ ] **Step 1: Write the failing test file**

Add a focused parity test that checks the new shell path, palette entry, content controls, library groups, and frontend renderer fragments:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorIconWidgetContentParityTest extends TestCase
{
    public function test_editor_shell_uses_font_awesome_5_15_3(): void
    {
        $blade = file_get_contents(resource_path('views/pagebuilder_elementor/editor_shell.blade.php'));

        $this->assertIsString($blade);
        $this->assertStringContainsString("assets/plugins/fontawesome/5.15.3/css/all.min.css", $blade);
    }

    public function test_editor_exposes_icon_widget_controls_and_library_groups(): void
    {
        $appJs = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

        $this->assertIsString($appJs);
        $this->assertStringContainsString("{ type:'icon',        label:'Icon'", $appJs);
        $this->assertStringContainsString("view: 'default'", $appJs);
        $this->assertStringContainsString("shape: 'circle'", $appJs);
        $this->assertStringContainsString('Font Awesome - Regular', $appJs);
        $this->assertStringContainsString('Font Awesome - Solid', $appJs);
        $this->assertStringContainsString('Font Awesome - Brands', $appJs);
        $this->assertStringContainsString('Font Awesome - Light', $appJs);
        $this->assertStringContainsString('Font Awesome - Duotone', $appJs);
        $this->assertStringContainsString('Open in new window', $appJs);
        $this->assertStringContainsString('Add nofollow', $appJs);
        $this->assertStringContainsString('Custom Attributes', $appJs);
    }

    public function test_frontend_renderer_emits_icon_markup(): void
    {
        $html = view('pagebuilder_elementor.partials.render_node', [
            'node' => [
                'id' => 'icon-node',
                'type' => 'icon',
                'settings' => [
                    'iconClass' => 'fas fa-star',
                    'iconStyle' => 'solid',
                    'iconName' => 'star',
                    'view' => 'framed',
                    'shape' => 'circle',
                    'link' => 'https://example.com',
                    'openInNewWindow' => true,
                    'nofollow' => true,
                    'attributes' => [
                        ['name' => 'data-track', 'value' => 'hero-icon'],
                    ],
                    'cssClass' => 'custom-icon-node',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('custom-icon-node', $html);
        $this->assertStringContainsString('fas fa-star', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-track="hero-icon"', $html);
    }
}
```

- [ ] **Step 2: Run the focused test to confirm it fails**

Run: `php artisan test --filter=PageBuilderElementorIconWidgetContentParityTest`
Expected: FAIL because the shell still points to Font Awesome `6.5.1`, the `Icon` widget does not exist yet, and the renderer has no `icon` branch.

### Task 2: Sync the Editor Shell and Builder Chrome to Font Awesome 5.15.3

**Files:**
- Modify: `resources/views/pagebuilder_elementor/editor_shell.blade.php`
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Switch the editor shell stylesheet to the local 5.15.3 asset**

Update the stylesheet line:

```blade
<link href="{{ asset('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}?v={{ @filemtime(public_path('assets/plugins/fontawesome/5.15.3/css/all.min.css')) }}" rel="stylesheet">
```

- [ ] **Step 2: Remap existing FA6-only builder icon classes in `app.js`**

Replace FA6-only classes with FA5.15.3-compatible equivalents in the builder chrome. The exact map should stay local to this builder file:

```js
const NODE_LABEL_ICONS = {
    container: 'fas fa-cube',
    container_fluid: 'fas fa-cube',
    row_grid: 'fas fa-th-large',
    grid: 'fas fa-th-large',
    heading: 'fas fa-heading',
    text_editor: 'fas fa-edit',
    image: 'far fa-image',
    video: 'fas fa-video',
    button: 'fas fa-link',
    divider: 'fas fa-minus',
    spacer: 'fas fa-arrows-alt-v',
    icon: 'far fa-star',
};
```

Also replace FA6-only action icons such as:
- `fa-pen-to-square` -> `fas fa-edit`
- `fa-table-cells-large` -> `fas fa-th-large`
- `fa-mobile-screen-button` -> `fas fa-mobile-alt`
- `fa-tablet-screen-button` -> `fas fa-tablet-alt`
- `fa-trash-can` -> `fas fa-trash-alt`
- `fa-xmark` -> `fas fa-times`
- `fa-up-right-from-square` -> `fas fa-external-link-alt`
- `fa-up-right-and-down-left-from-center` -> `fas fa-expand-alt`

- [ ] **Step 3: Re-run the focused test**

Run: `php artisan test --filter=test_editor_shell_uses_font_awesome_5_15_3`
Expected: PASS

### Task 3: Add Local Icon Library Data and Modal State in the Builder App

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`

- [ ] **Step 1: Add local library configuration and parser helpers**

Introduce a small helper layer near the top of `app.js`:

```js
const FONT_AWESOME_5_ICON_GROUPS = [
    { key: 'all', label: 'All Icons', style: null },
    { key: 'regular', label: 'Font Awesome - Regular', style: 'regular' },
    { key: 'solid', label: 'Font Awesome - Solid', style: 'solid' },
    { key: 'brands', label: 'Font Awesome - Brands', style: 'brands' },
    { key: 'light', label: 'Font Awesome - Light', style: 'light' },
    { key: 'duotone', label: 'Font Awesome - Duotone', style: 'duotone' },
];

function fontAwesomeStylePrefix(style) {
    if (style === 'regular') return 'far';
    if (style === 'brands') return 'fab';
    if (style === 'light') return 'fal';
    if (style === 'duotone') return 'fad';
    return 'fas';
}
```

Build icon records from local metadata so each item includes:
- `style`
- `name`
- `label`
- `className`
- `searchText`

- [ ] **Step 2: Add modal state and selection helpers**

Extend the app state with:

```js
showIconLibraryModal: false,
iconLibraryGroup: 'all',
iconLibrarySearch: '',
iconLibrarySelected: null,
iconLibraryTargetNodeId: null,
```

Add methods for:
- opening the modal for the current `Icon` widget
- filtering by group
- filtering by search text
- selecting one icon card
- inserting the selected icon into the active node
- closing and resetting the modal state

- [ ] **Step 3: Add the modal template markup**

Insert a modal block in the main app template that mirrors the agreed Elementor structure:
- dark modal shell
- left sidebar groups
- search input
- grid of icon cards
- selected state
- `Insert` button

### Task 4: Add the New Basic Icon Widget to Editor State and Canvas Preview

**Files:**
- Modify: `public/js/pagebuilder_elementor/app.js`
- Create: `public/js/pagebuilder_elementor/widgets/basic/Icon.vue`

- [ ] **Step 1: Register the widget and default node shape in `app.js`**

Add the widget path:

```js
icon: '/js/pagebuilder_elementor/widgets/basic/Icon.vue',
```

Add the palette tile:

```js
{ type:'icon', label:'Icon', icon:'far fa-star' },
```

Add default settings in `makeNode()`:

```js
if (type === 'icon') {
    return {
        id: uid(type),
        type,
        settings: {
            iconStyle: 'regular',
            iconName: 'star',
            iconClass: 'far fa-star',
            view: 'default',
            shape: 'circle',
            link: '',
            openInNewWindow: false,
            nofollow: false,
            customAttributes: [],
            cssClass: '',
        },
    };
}
```

- [ ] **Step 2: Add the `Content` controls**

Inside the selected-widget settings branch, render:
- icon chooser preview button
- `View` select
- conditional `Shape` select
- link field
- link-options toggle section

Keep the link options aligned with the existing builder `attributes` array pattern.

- [ ] **Step 3: Create the canvas preview component**

Create `public/js/pagebuilder_elementor/widgets/basic/Icon.vue` with a small wrapper that:
- reads `settings.iconClass`, `settings.view`, `settings.shape`, and link-related fields
- renders an `<i>` tag for the icon
- applies `default`, `stacked`, and `framed` preview classes
- wraps the icon in `<a>` only when a link exists

The component can keep its first-pass visuals small and stable:

```vue
<template>
  <component :is="tagName" v-bind="linkAttrs" class="el-icon-widget" :class="viewClass">
    <span class="el-icon-widget__inner" :class="shapeClass">
      <i :class="iconClass"></i>
    </span>
  </component>
</template>
```

### Task 5: Add Matching Frontend Renderer Output

**Files:**
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `public/assets/css/frontend_elementor.css` (only if minimal shared frontend classes are needed)

- [ ] **Step 1: Add the `icon` renderer branch**

Add a new branch that:
- normalizes `iconClass`
- emits view and shape classes
- emits optional `cssClass`
- emits optional anchor attributes
- uses the existing custom-attributes format

Expected structure:

```blade
@elseif($type === 'icon')
    @php
        $iconClass = $normalize_class_tokens($settings['iconClass'] ?? 'far fa-star');
    @endphp
    <div class="el-widget el-widget-icon {{ $normalize_class_tokens($settings['cssClass'] ?? '') }}">
        <a ...>
            <span class="el-icon-widget__inner ...">
                <i class="{{ $iconClass }}"></i>
            </span>
        </a>
    </div>
@endif
```

- [ ] **Step 2: Add only the minimum shared frontend styling**

If the renderer needs consistent `stacked` or `framed` spacing, add a narrowly scoped rule set in `public/assets/css/frontend_elementor.css` rather than a broad visual sweep.

### Task 6: Run Verification and Builder Checks

**Files:**
- Verify: `tests/Feature/PageBuilderElementorIconWidgetContentParityTest.php`
- Verify: `resources/views/pagebuilder_elementor/editor_shell.blade.php`
- Verify: `public/js/pagebuilder_elementor/app.js`
- Verify: `public/js/pagebuilder_elementor/widgets/basic/Icon.vue`
- Verify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`

- [ ] **Step 1: Run the focused icon parity test file**

Run: `php artisan test --filter=PageBuilderElementorIconWidgetContentParityTest`
Expected: PASS

- [ ] **Step 2: Run syntax and diff hygiene checks**

Run: `node --check public/js/pagebuilder_elementor/app.js`
Expected: exit `0`

Run: `php -l resources/views/pagebuilder_elementor/partials/render_node.blade.php`
Expected: `No syntax errors detected`

Run: `git diff --check -- resources/views/pagebuilder_elementor/editor_shell.blade.php public/js/pagebuilder_elementor/app.js public/js/pagebuilder_elementor/widgets/basic/Icon.vue resources/views/pagebuilder_elementor/partials/render_node.blade.php tests/Feature/PageBuilderElementorIconWidgetContentParityTest.php public/assets/css/frontend_elementor.css`
Expected: no output

- [ ] **Step 3: Confirm the builder route still responds**

Run: `powershell -Command "try { (Invoke-WebRequest -UseBasicParsing 'http://laravel-13-phoenix.aruna/pagebuilder-elementor/create' -TimeoutSec 10).StatusCode } catch { $_.Exception.Message }"`
Expected: `200`

- [ ] **Step 4: Confirm the runtime UI markers are served**

Run a quick served-asset check for strings that prove the new widget is live:

```powershell
powershell -Command "(Invoke-WebRequest -UseBasicParsing 'http://laravel-13-phoenix.aruna/js/pagebuilder_elementor/app.js').Content | Select-String 'Font Awesome - Duotone|{ type:''icon'',        label:''Icon'''"
```

Expected: matches for the new icon widget and new icon library group labels.
