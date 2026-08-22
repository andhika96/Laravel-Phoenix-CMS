import assert from 'node:assert/strict';
import test from 'node:test';
import { readdirSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join, resolve } from 'node:path';
import vm from 'node:vm';
import {
    pageBuilderV24CapabilityPrelude,
    readPageBuilderV24EditorStyles,
} from './helpers/pagebuilder-v24-module-source.mjs';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v24/app.js'), 'utf8');
const css = readPageBuilderV24EditorStyles(root);
const containerSettings = readFileSync(resolve(root, 'resources/pagebuilder_elementor_v24/modules/layout/container/Settings.vue'), 'utf8');

function registeredWidgets() {
    const moduleRoot = resolve(root, 'resources/pagebuilder_elementor_v24/modules');
    const manifests = [];
    const visit = (directory) => {
        for (const entry of readdirSync(directory, { withFileTypes: true })) {
            const path = join(directory, entry.name);
            if (entry.isDirectory()) visit(path);
            else if (entry.name === 'module.json') manifests.push(path);
        }
    };
    visit(moduleRoot);

    return manifests.map((path) => {
        const manifest = JSON.parse(readFileSync(path, 'utf8'));
        return {
            type: manifest.type,
            category: manifest.category,
            settings: resolve(dirname(path), manifest.assets.settings),
        };
    });
}

function mountPropertyHelpers() {
    const helperBlock = app.match(/\/\/ V24_CONTEXTUAL_PROPERTY_HELPERS_START([\s\S]*?)\/\/ V24_CONTEXTUAL_PROPERTY_HELPERS_END/)?.[1];
    assert.ok(helperBlock, 'v2.4 contextual property helpers should exist');

    const context = {};
    vm.runInNewContext(`${pageBuilderV24CapabilityPrelude(root)}\n${helperBlock}\nthis.helpers = { propertyTabsForNodeType, propertyKindForNodeType };`, context);
    return context.helpers;
}

function settingsTabsFor(widget) {
    const source = readFileSync(widget.settings, 'utf8');
    return Array.from(source.matchAll(/settingsTab\s*(?:===|==|=)\s*['"]([a-z]+)['"]/g), (match) => match[1])
        .filter((value, index, values) => values.indexOf(value) === index)
        .sort();
}

test('every registered Layout and Widget exposes exactly the tabs implemented by its settings module', () => {
    const helpers = mountPropertyHelpers();
    const widgets = registeredWidgets();

	assert.equal(widgets.length, 49, 'all active v2.4 registry entries should be audited');

    for (const widget of widgets) {
        const tabs = JSON.parse(JSON.stringify(helpers.propertyTabsForNodeType(widget.type)));
        assert.deepEqual(
            tabs.map((tab) => tab.id).sort(),
            settingsTabsFor(widget),
            `${widget.type} should not show a missing category or hide an implemented category`,
        );
        assert.equal(
            helpers.propertyKindForNodeType(widget.type),
            widget.type.startsWith('container') ? 'Container' : (widget.category === 'layout' ? 'Grid' : 'Widget'),
            `${widget.type} should use the correct Properties identity`,
        );
    }

    assert.match(app, /v-for="tab in activeSettingsTabs"/);
    assert.match(app, /selectedNodeKind \+ ' settings'/);
    assert.match(app, /\{\{ selectedNodeKind \}\}[^\n]*\{\{ selectedType \}\}/);
});

test('a selected canvas node keeps its toolbar while the pointer crosses another node', () => {
    const methodBody = app.match(/isToolbarVisible\(\)\s*\{([\s\S]*?)\n\s*\},/)?.[1];
    assert.ok(methodBody, 'BuilderNode.isToolbarVisible should exist');

    const isToolbarVisible = new Function(methodBody);
    assert.equal(isToolbarVisible.call({
        selectedId: 'heading-1',
        hoveredId: 'container-1',
        node: { id: 'heading-1' },
        isAncestorVisualActive: false,
    }), true, 'selection must take priority over a transient ancestor hover');
});

test('a selected layout toolbar yields while its descendant widget is hovered', () => {
    const methodBody = app.match(/isToolbarVisible\(\)\s*\{([\s\S]*?)\n\s*\},/)?.[1];
    assert.ok(methodBody, 'BuilderNode.isToolbarVisible should exist');

    const isToolbarVisible = new Function(methodBody);
    assert.equal(isToolbarVisible.call({
        selectedId: 'grid-1',
        hoveredId: 'heading-1',
        node: { id: 'grid-1' },
        isHoveredDescendant: true,
        isAncestorVisualActive: false,
    }), false, 'a selected Grid toolbar must not cover its hovered child widget actions');
});

test('widget label and action hit areas bridge the visual gap above the node', () => {
    assert.match(css, /\.webpage-frame \.pb-node-widget > \.pb-node-toolbar > :is\(\.pb-node-label, \.pb-node-actions\)::after\s*\{[\s\S]*?bottom:\s*-10px;[\s\S]*?height:\s*10px;/);
});

test('collapsed sidebar toggle aligns with the canvas metadata row', () => {
    assert.match(css, /\.floating-expand\.left\s*\{[^}]*top:\s*65px;/);
});

test('page root reaches the frame edge without clipping node toolbars', () => {
    assert.match(css, /\.webpage-frame \.pb-canvas\s*\{[^}]*padding:\s*0;/);
    assert.match(css, /\.webpage-frame\s*\{[^}]*overflow:\s*clip;[^}]*overflow-clip-margin:\s*24px;/);
});

test('widget labels expose an enlarged hit target at the default 100 percent zoom', () => {
    assert.match(css, /\.webpage-frame \.pb-node-label\.widget-label::before\s*\{[^}]*inset:\s*-5px;[^}]*content:\s*['"]{2};/);
});

test('clicking a canvas node label reveals the collapsed properties panel', () => {
    assert.match(app, /@click\.stop="onSelect\(node,\s*\{\s*revealPanel:\s*true\s*\}\)"/);
    assert.match(app, /function selectNode\(n,\s*options\s*=\s*\{\}\)\s*\{[\s\S]*?if\s*\(options\.revealPanel\)\s*leftCollapsed\.value\s*=\s*false;/);
});

test('Container Layout exposes selectable Child Containers and Add Container only for Flexbox', () => {
    assert.match(containerSettings, /<details class="pb-collapsible" v-if="\(node\.settings\?\.displayType \|\| 'flex'\) === 'flex'" open>[\s\S]*?<summary>Child Containers<\/summary>/);
    assert.match(containerSettings, /<summary>Child Containers<\/summary>/);
    assert.match(containerSettings, /@click="editor\.addContainerChild\(node\)"/);
    assert.match(containerSettings, /Each layout item is a selectable Container with its own Layout, Style, and Advanced settings\./);
    assert.doesNotMatch(containerSettings, /Add Column|Column widths|node\.columns/);
    assert.doesNotMatch(containerSettings, /addContainerFlexColumn/);
    assert.match(app, /function addContainerChild\(node\)[\s\S]*?appendChildContainer\(node, \(\) => makeNode\(defaultContainerType\(\)\)\)/);
    assert.match(app, /addContainerChild,/);
    assert.doesNotMatch(app, /pb-grid-col-label-button|selectedColumnContext|selectColumn\(/);
    assert.match(app, /v-model="node\.children"/);
    assert.match(app, /:parent-node="node"/);
});

test('Grid canvas uses conceptual column dropzones without selectable Column settings', () => {
    const gridCanvas = app.match(/<!-- GRID -->([\s\S]*?)<!-- TABS -->/)?.[1] || '';

    assert.match(gridCanvas, /v-for="\(col, ci\) in node\.columns"/);
    assert.match(gridCanvas, /v-model="col\.children"/);
    assert.match(gridCanvas, /:parent-node="node"/);
    assert.match(gridCanvas, /pb-dropzone-col/);
    assert.match(gridCanvas, /type: 'column'/);
    assert.match(app, /pendingInsertTarget && pendingInsertTarget\.type === 'column'|target\.type === 'column'/);
    assert.doesNotMatch(app, /selectedColumnContext|:on-select-column="selectColumn"/);
    assert.doesNotMatch(app, /syncGridColumnsForDevice|responsiveGridSyncTimer|scheduleResponsiveGridSync/);
});
