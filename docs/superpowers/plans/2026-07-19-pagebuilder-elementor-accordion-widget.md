# PageBuilder Elementor Accordion Widget Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a nested Elementor-style Accordion widget under `Advanced` and a reusable, functional widget Advanced engine while preserving Tabs and all existing pagebuilder behavior.

**Architecture:** Accordion is a normal widget node with `accordionItems[].children`, ephemeral editor state keyed by node ID, a dedicated Vue preview component, semantic frontend rendering, and a small shared runtime. Generic widget Advanced settings use reusable flat defaults and rendering helpers so Accordion is the first consumer without duplicating the engine inside Accordion-only code.

**Tech Stack:** Vue 3, vue3-sfc-loader, vuedraggable, plain JavaScript runtime helpers, Laravel Blade recursive rendering, Laravel Cache/Auth/Request, CSS, PHPUnit feature tests, Font Awesome Pro 5.15.3.

## Global Constraints

- Work only from `D:\Laragon\www\laravel-13-phoenix` or its isolated worktree; never use the similarly named E: checkout.
- Create timestamped backups before modifying every existing production/test file.
- Use test-first red-green-refactor for each behavior slice.
- Place Accordion in `Advanced`, even though the audited Elementor demo lists it in `General`.
- Keep sidebar `editingItemId` separate from canvas `expandedItemIds` and do not persist either runtime value.
- `Display Conditions` and `Cache Settings` are functional; `Animate With AI` is a disabled external-service affordance.
- Do not alter approved Tabs behavior except for generic traversal/target support required to interoperate with Accordion.
- Use the local Font Awesome 5.15.3 picker and sanitize uploaded SVG.
- Do not claim completion without focused tests, the complete PageBuilderElementor suite, syntax/lint checks, diff checks, and authenticated Chrome verification.

---

### Task 1: Lock Accordion registration, data, and traversal with failing tests

**Files:**
- Create: `tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`

**Interfaces:**
- Produces: `isAccordion(type)`, `accordionWidgetDefaults()`, `accordionItemDefaults(index)`, `accordionWidgetDefaultItems()`, and node shape `{ type:'accordion', settings, accordionItems }`.
- Produces: toolbox key `advanced` and widget map entry `/js/pagebuilder_elementor/widgets/advanced/Accordion.vue`.
- Consumes: existing `uid`, `jclone`, `norm`, `regenIds`, `findById`, and responsive seeding patterns.

- [ ] **Step 1: Write the failing registration/default/traversal tests**

Create tests that read the editor source and assert the exact public contract:

```php
public function test_editor_registers_advanced_accordion_with_three_nested_items(): void
{
    $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

    $this->assertStringContainsString("accordion:      '/js/pagebuilder_elementor/widgets/advanced/Accordion.vue'", $app);
    $this->assertStringContainsString("advanced: [", $app);
    $this->assertStringContainsString("{ type:'accordion',   label:'Accordion'", $app);
    $this->assertStringContainsString('function accordionWidgetDefaultItems()', $app);
    $this->assertStringContainsString('accordionItems: accordionWidgetDefaultItems()', $app);
}

public function test_recursive_helpers_visit_accordion_item_children(): void
{
    $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

    $this->assertStringContainsString("if (c.type === 'accordion')", $app);
    $this->assertStringContainsString('item.children = norm(item.children || [])', $app);
    $this->assertStringContainsString('(n.accordionItems || []).forEach', $app);
}
```

- [ ] **Step 2: Run the new test and verify RED**

Run: `php artisan test --filter=PageBuilderElementorAccordionWidgetParityTest`

Expected: FAIL because Accordion registry/default/traversal strings do not exist.

- [ ] **Step 3: Add minimal Accordion node registration and recursive normalization**

Add explicit helpers and use them in the same recursive paths that currently handle `tabItems`:

```js
function isAccordion(t) { return t === 'accordion'; }

function accordionItemDefaults(index = 0) {
	return { id: uid('accordion_item'), title: 'Item #' + (index + 1), cssId: '', children: [] };
}

function accordionWidgetDefaultItems() {
	return [0, 1, 2].map(accordionItemDefaults);
}
```

Register `accordion` in `widgetMap`, base labels/icons, `makeNode`, clone/duplicate ID regeneration, normalization, `findById`, walk/delete helpers, and `toolbox.advanced`.

- [ ] **Step 4: Run focused tests and syntax check**

Run:

```powershell
php artisan test --filter=PageBuilderElementorAccordionWidgetParityTest
node --check public/js/pagebuilder_elementor/app.js
```

Expected: registration/traversal tests PASS and syntax exit code 0.

- [ ] **Step 5: Commit the data-model slice**

```powershell
git add tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php public/js/pagebuilder_elementor/app.js
git commit -m "feat: register nested accordion widget"
```

### Task 2: Add Accordion item management and non-persisted runtime state

**Files:**
- Modify: `tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`

**Interfaces:**
- Consumes: Accordion node model from Task 1.
- Produces: `accordionItemsForNode`, `accordionRuntimeForNode`, `selectAccordionItem`, `toggleAccordionItem`, `addAccordionItem`, `duplicateAccordionItem`, `removeAccordionItem`, and `accordionItemSummary`.
- Produces: `accordionRuntimeState` as `ref({})`; it is never attached to `rootNodes` or posted by `savePage()`.

- [ ] **Step 1: Add failing tests for item actions and state separation**

```php
public function test_accordion_sidebar_and_canvas_use_separate_runtime_state(): void
{
    $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));

    $this->assertStringContainsString('const accordionRuntimeState = ref({});', $app);
    $this->assertStringContainsString('editingItemId', $app);
    $this->assertStringContainsString('expandedItemIds', $app);
    $this->assertStringContainsString('function toggleAccordionItem(node, itemId)', $app);
    $this->assertStringNotContainsString('settings.editorExpandedItemIds', $app);
}
```

Also assert add/duplicate/delete/reorder controls, Title, CSS ID, Default State, Max Items Expanded, and Animation Duration markers.

- [ ] **Step 2: Run focused test and verify RED**

Expected: FAIL because runtime/action helpers and controls are missing.

- [ ] **Step 3: Implement runtime state and item actions**

Initialize runtime from `defaultState`, deep-clone descendants during duplicate, regenerate IDs with `regenIds`, preserve one item minimum, and remove deleted IDs from both runtime arrays.

Use a draggable repeater over `selectedNode.accordionItems`; selecting its row updates only `editingItemId`.

- [ ] **Step 4: Run focused test and syntax check**

Expected: PASS with no JavaScript syntax error.

- [ ] **Step 5: Commit the item-management slice**

```powershell
git add tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php public/js/pagebuilder_elementor/app.js
git commit -m "feat: manage accordion items and runtime state"
```

### Task 3: Render nested Accordion dropzones in the editor

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue`
- Modify: `tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/assets/css/pagebuilder_elementor.css`

**Interfaces:**
- Consumes: item/runtime helpers from Task 2.
- Produces: `AdvancedAccordion` SFC props `item`, `expandedItemIds`, `responsiveDevice`; emits `toggle-item`.
- Produces: targeted insert `{ type:'accordion', nodeId, itemId }` and `rerouteAccordionDropToNestedColumn(evt, itemChildren)`.

- [ ] **Step 1: Add failing editor-shell tests**

Assert the SFC exists and contains accessible headers, `aria-expanded`, measured height hooks, reduced-motion handling, and item slots. Assert BuilderNode contains one draggable zone per expanded item, `data-parent-node-type="accordion"`, the exact targeted insert object, and recursive `<BuilderNode>` rendering.

- [ ] **Step 2: Run focused test and verify RED**

Expected: FAIL because the SFC and BuilderNode branch do not exist.

- [ ] **Step 3: Implement the SFC and BuilderNode Accordion branch**

Use stable IDs and an animated content wrapper:

```vue
<button
	type="button"
	class="el-widget-accordion__header"
	:aria-expanded="isExpanded(item.id) ? 'true' : 'false'"
	:aria-controls="panelId(item.id)"
	@click.stop="$emit('toggle-item', item.id)"
>
	<slot name="icon" :item="item" :expanded="isExpanded(item.id)" />
	<span>{{ item.title || 'Item' }}</span>
</button>
```

BuilderNode owns the nested draggable arrays and renders only open drop targets. Use `v-show`/transition height so children remain in data while closed.

- [ ] **Step 4: Extend targeted insertion and drop rerouting**

`insertToolIntoPendingTarget()` locates `accordionItems[].children`, activates the target panel in runtime state, inserts once, and clears the pending target. Generalize safe nested Container insertion rather than adding an Accordion-only bypass.

- [ ] **Step 5: Add scoped editor CSS**

Add Accordion header/panel/dropzone/active/hover styling, transition variables, pending-target pulse, and `prefers-reduced-motion` overrides without changing Tabs selectors.

- [ ] **Step 6: Run focused tests, syntax, and diff checks**

Expected: PASS; syntax exit 0; `git diff --check` empty.

- [ ] **Step 7: Commit the canvas slice**

```powershell
git add tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php public/js/pagebuilder_elementor/app.js public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue public/assets/css/pagebuilder_elementor.css
git commit -m "feat: add nested accordion canvas interaction"
```

### Task 4: Complete Accordion Content and Style controls

**Files:**
- Modify: `tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue`
- Modify: `public/assets/css/pagebuilder_elementor.css`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Produces: Content `Layout`/`Interactions`, Style `Accordion`/`Header`/`Content` setting groups, responsive values, and Normal/Hover/Active state values.
- Consumes: existing responsive, spacing, color, icon-library, typography-compatible, border, gradient, and media-picker patterns.

- [ ] **Step 1: Add failing tests for every audited Content and Style label/value**

Assert Item Position, Icon Position, Expand/Collapse icon sources, title tags, FAQ Schema, interaction defaults, all three Style sections, all state tabs, gradient controls, border types, typography, text shadow/stroke, responsive icon size/spacing, radius, and padding.

- [ ] **Step 2: Run focused test and verify RED**

- [ ] **Step 3: Add Content tabs and responsive controls**

Use the existing pagebuilder control rhythm and icon picker. SVG selection flows through a sanitizer helper and never binds untrusted markup directly.

- [ ] **Step 4: Add Style controls and preview style mapping**

Prefix settings with `accordion`, `header`, or `content`. Expose CSS variables from the SFC root so editor and frontend CSS share the same names, for example:

```js
style['--accordion-item-gap'] = responsiveCssSize('accordionItemGap', '0px');
style['--accordion-header-active-color'] = settings.headerTitleColorActive || '#101828';
style['--accordion-animation-duration'] = animationDuration + 'ms';
```

- [ ] **Step 5: Run focused tests and syntax check**

- [ ] **Step 6: Commit the Content/Style slice**

```powershell
git add tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php public/js/pagebuilder_elementor/app.js public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue public/assets/css/pagebuilder_elementor.css public/assets/css/frontend_elementor.css
git commit -m "feat: add accordion content and style controls"
```

### Task 5: Add semantic frontend Accordion, animation runtime, and FAQ schema

**Files:**
- Create: `resources/views/pagebuilder_elementor/partials/render_accordion.blade.php`
- Create: `public/js/pagebuilder_elementor/frontend-runtime.js`
- Modify: `tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `resources/views/pagebuilder_elementor/frontend_renderer.blade.php`
- Modify: `resources/views/pagebuilder_elementor/editor_shell.blade.php`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Consumes: normalized Accordion settings/items.
- Produces: semantic `.el-widget-accordion[data-accordion-root]`, details/summary IDs and ARIA, recursive children, JSON-LD `FAQPage`, and `window.PageBuilderElementorRuntime`.

- [ ] **Step 1: Add failing renderer tests with real node data**

Render a node with nested Heading/Text Editor children and assert three items, first-open/all-collapsed variants, recursive child markup, `aria-expanded`, stable IDs, sanitized CSS IDs, expand/collapse icons, and JSON-LD Question/Answer data.

- [ ] **Step 2: Run focused test and verify RED**

- [ ] **Step 3: Implement the Blade partial and recursive text extractor**

Render `details`/`summary` progressively enhanced markup. Build FAQ answers recursively from safe textual fields; omit empty schema answers and use `@json`/safe JSON encoding.

- [ ] **Step 4: Implement one shared frontend runtime**

The runtime binds Accordion roots once, animates measured height, enforces `one`/`multiple`, supports Arrow/Home/End keyboard navigation, honors reduced motion, and cleans temporary heights.

- [ ] **Step 5: Load runtime in editor and frontend shells**

Use `filemtime` cache busting matching existing assets.

- [ ] **Step 6: Run renderer tests, syntax, Blade lint, and diff checks**

Expected: all PASS with no console-syntax errors.

- [ ] **Step 7: Commit the frontend slice**

```powershell
git add tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php resources/views/pagebuilder_elementor/partials/render_accordion.blade.php resources/views/pagebuilder_elementor/partials/render_node.blade.php resources/views/pagebuilder_elementor/frontend_renderer.blade.php resources/views/pagebuilder_elementor/editor_shell.blade.php public/js/pagebuilder_elementor/frontend-runtime.js public/assets/css/frontend_elementor.css
git commit -m "feat: render accessible accordion on frontend"
```

### Task 6: Add reusable widget Advanced defaults and controls

**Files:**
- Create: `public/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue`
- Create: `tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue`

**Interfaces:**
- Produces: `widgetAdvancedDefaults()`, `normalizeWidgetAdvancedSettings(settings)`, `widgetAdvancedPreviewStyle(settings, device)`, reusable `WidgetAdvancedControls` component.
- Produces: component events for responsive value updates, media/icon selection, attribute rows, and unavailable-AI notice.

- [ ] **Step 1: Add failing Advanced model/control tests**

Assert every mapped Layout, Motion Effects, Transform, Background, Border, Mask, Responsive, Attributes, Custom CSS, Display Conditions, Cache Settings, and disabled Animate With AI marker.

- [ ] **Step 2: Run test and verify RED**

- [ ] **Step 3: Implement flat reusable defaults and normalization**

Reuse current container/grid names where semantics match. Add prefixed hover/mask/condition/cache fields where missing. Seed tablet/mobile values without overwriting saved overrides.

- [ ] **Step 4: Implement the shared controls SFC**

The component receives `node`, `responsiveDevice`, and option callbacks. It renders the approved collapsible sections and emits controlled updates instead of mutating root app globals.

- [ ] **Step 5: Integrate the component into Accordion Advanced tab**

Accordion panel has the top tab bar `Content`, `Style`, `Advanced`; only Advanced mounts the shared component.

- [ ] **Step 6: Run focused tests and syntax checks**

- [ ] **Step 7: Commit the shared-control slice**

```powershell
git add tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php public/js/pagebuilder_elementor/app.js public/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue
git commit -m "feat: add shared widget advanced controls"
```

### Task 7: Make Advanced visual, responsive, motion, attribute, and CSS behavior functional

**Files:**
- Modify: `tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php`
- Modify: `public/js/pagebuilder_elementor/app.js`
- Modify: `public/js/pagebuilder_elementor/frontend-runtime.js`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_accordion.blade.php`
- Modify: `public/assets/css/pagebuilder_elementor.css`
- Modify: `public/assets/css/frontend_elementor.css`

**Interfaces:**
- Consumes: Advanced settings/control contract from Task 6.
- Produces: matching editor/frontend styles, scoped CSS, safe attributes, mask fallbacks, transforms, responsive visibility, entrance/scroll/mouse effects, and sticky behavior.

- [ ] **Step 1: Add failing renderer and source tests for Advanced output**

Use a real Accordion node with representative Layout, responsive, Background, Border, Mask, Transform, Motion, Attributes, and Custom CSS values. Assert emitted styles/classes/media rules/attributes and reject `onclick`, unsafe protocols, `style`, and managed `id`/`class` overrides.

- [ ] **Step 2: Run focused tests and verify RED**

- [ ] **Step 3: Implement reusable PHP style/class/attribute helpers**

Apply generic Advanced output to the outer Accordion wrapper. Emit responsive node-scoped media rules and replace `selector` only with the safe node selector.

- [ ] **Step 4: Implement preview parity and visual CSS**

Apply width/flex/position/z-index, spacing, normal/hover background/border/shadow, mask, transform composition, visibility, and transition values without overwriting Accordion-specific inner styles.

- [ ] **Step 5: Implement coordinated motion runtime**

Use one `requestAnimationFrame` scroll coordinator, `IntersectionObserver` entrance handling, pointer tracking, device guards, and reduced-motion guards. Do not create one window scroll listener per widget.

- [ ] **Step 6: Run focused tests, full current suite, syntax, and diff checks**

- [ ] **Step 7: Commit functional Advanced behavior**

```powershell
git add tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php public/js/pagebuilder_elementor/app.js public/js/pagebuilder_elementor/frontend-runtime.js resources/views/pagebuilder_elementor/partials/render_node.blade.php resources/views/pagebuilder_elementor/partials/render_accordion.blade.php public/assets/css/pagebuilder_elementor.css public/assets/css/frontend_elementor.css
git commit -m "feat: apply widget advanced behavior"
```

### Task 8: Implement Display Conditions and fragment Cache Settings

**Files:**
- Create: `app/Support/PageBuilderElementor/WidgetDisplayConditionEvaluator.php`
- Create: `app/Support/PageBuilderElementor/WidgetFragmentCache.php`
- Modify: `tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php`
- Modify: `resources/views/pagebuilder_elementor/partials/render_node.blade.php`

**Interfaces:**
- Produces: `WidgetDisplayConditionEvaluator::allows(array $groups, Request $request, ?Authenticatable $user): bool`.
- Produces: `WidgetFragmentCache::remember(array $node, array $context, Closure $render): string` and `WidgetFragmentCache::key(...)`.
- Consumes: Advanced `displayConditions` and `cacheMode` values.

- [ ] **Step 1: Add failing evaluator tests**

Cover page ID/slug, guest/authenticated, role, date range, device class, AND inside groups, OR between groups, Include/Exclude, empty rules, and malformed explicit restrictions.

- [ ] **Step 2: Run focused tests and verify RED**

- [ ] **Step 3: Implement the evaluator with explicit whitelists**

Do not execute arbitrary callbacks or expressions. Normalize all operators and values before evaluation.

The public entry point is fixed:

```php
final class WidgetDisplayConditionEvaluator
{
    public function allows(array $groups, Request $request, ?Authenticatable $user): bool
    {
        if ($groups === []) {
            return true;
        }

        return collect($groups)->contains(
            fn (array $group): bool => $this->groupAllows($group, $request, $user)
        );
    }
}
```

- [ ] **Step 4: Add failing cache-key/behavior tests**

Assert `inactive` bypass, `active` reuse, and key changes for node payload, locale, page identity, authenticated visibility context, and renderer version.

- [ ] **Step 5: Implement bounded fragment caching and renderer integration**

Use application cache with configured TTL and fail-open rendering if cache access throws. The editor path always bypasses fragment cache.

The cache wrapper keeps rendering as a closure and never stores editor output:

```php
public function remember(array $node, array $context, Closure $render): string
{
    if (($node['settings']['cacheMode'] ?? 'default') !== 'active') {
        return $render();
    }

    try {
        return Cache::remember($this->key($node, $context), $this->ttl(), $render);
    } catch (Throwable) {
        return $render();
    }
}
```

- [ ] **Step 6: Run focused tests and full PageBuilderElementor suite**

- [ ] **Step 7: Commit conditions/cache slice**

```powershell
git add app/Support/PageBuilderElementor/WidgetDisplayConditionEvaluator.php app/Support/PageBuilderElementor/WidgetFragmentCache.php tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php resources/views/pagebuilder_elementor/partials/render_node.blade.php
git commit -m "feat: add widget conditions and fragment cache"
```

### Task 9: Full verification and authenticated Chrome parity audit

**Files:**
- Modify only files required by a newly reproduced failing test.

**Interfaces:**
- Consumes: all previous tasks.
- Produces: verified feature and a clean reviewable branch.

- [ ] **Step 1: Run complete automated verification**

```powershell
php artisan test --filter=PageBuilderElementor
node --check public/js/pagebuilder_elementor/app.js
node --check public/js/pagebuilder_elementor/frontend-runtime.js
php -l resources/views/pagebuilder_elementor/partials/render_node.blade.php
php -l resources/views/pagebuilder_elementor/partials/render_accordion.blade.php
git diff --check
```

Expected: all commands exit 0; PageBuilderElementor reports zero failures.

- [ ] **Step 2: Review the diff against the approved design line by line**

Confirm Content, Style, every Advanced group, nested behavior, schema, accessibility, conditions, cache, responsive output, runtime-state non-persistence, and the external AI boundary each have an implementation and test.

- [ ] **Step 3: Test in authenticated Chrome**

Open the D: checkout builder, insert Accordion from Advanced, validate three defaults, item management, nested drag/drop and targeted add, one/multiple/all-collapsed modes, 400ms animation, Content/Style/Advanced changes, save/reload, frontend output, desktop/tablet/mobile, keyboard, reduced motion, FAQ JSON-LD, conditions, caching, console, and network errors.

- [ ] **Step 4: Reproduce every discovered issue with a failing test before fixing**

Use the narrowest focused test, verify RED, implement the fix, verify GREEN, then rerun the full suite.

- [ ] **Step 5: Run final fresh verification and inspect repository state**

```powershell
php artisan test --filter=PageBuilderElementor
git status --short
git diff --check
```

- [ ] **Step 6: Commit final verified adjustments if any**

```powershell
git add public/js/pagebuilder_elementor/app.js public/js/pagebuilder_elementor/frontend-runtime.js public/js/pagebuilder_elementor/widgets/advanced/Accordion.vue public/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue public/assets/css/pagebuilder_elementor.css public/assets/css/frontend_elementor.css resources/views/pagebuilder_elementor/editor_shell.blade.php resources/views/pagebuilder_elementor/frontend_renderer.blade.php resources/views/pagebuilder_elementor/partials/render_node.blade.php resources/views/pagebuilder_elementor/partials/render_accordion.blade.php app/Support/PageBuilderElementor/WidgetDisplayConditionEvaluator.php app/Support/PageBuilderElementor/WidgetFragmentCache.php tests/Feature/PageBuilderElementorAccordionWidgetParityTest.php tests/Feature/PageBuilderElementorWidgetAdvancedParityTest.php
git commit -m "fix: verify accordion parity behavior"
```
