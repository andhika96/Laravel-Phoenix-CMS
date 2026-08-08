import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';
import vm from 'node:vm';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/app.js'), 'utf8');
const css = readFileSync(resolve(root, 'public/assets/css/pagebuilder_elementor_v23.css'), 'utf8');
const widgetConfig = readFileSync(resolve(root, 'config/pagebuilder_elementor_v23_widgets.php'), 'utf8');
const containerSettings = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/widgets/layout/container/Settings.vue'), 'utf8');

function registeredWidgets() {
    return Array.from(widgetConfig.matchAll(/^\s*'(?<key>[^']+)'\s*=>\s*\[(?<body>.*?)^\s*\],/gms), (match) => {
        const body = match.groups.body;
        return {
            type: body.match(/'type'\s*=>\s*'([^']+)'/)?.[1],
            category: body.match(/'category'\s*=>\s*'([^']+)'/)?.[1],
            settings: body.match(/'settings'\s*=>\s*'([^']+)'/)?.[1],
        };
    }).filter((widget) => widget.type && widget.category && widget.settings);
}

function mountPropertyHelpers() {
    const helperBlock = app.match(/\/\/ V23_CONTEXTUAL_PROPERTY_HELPERS_START([\s\S]*?)\/\/ V23_CONTEXTUAL_PROPERTY_HELPERS_END/)?.[1];
    assert.ok(helperBlock, 'v2.3 contextual property helpers should exist');

    const context = {
        isCont: (type) => type === 'container' || type === 'container_fluid',
        isGrid: (type) => type === 'grid' || type === 'row_grid',
    };
    vm.runInNewContext(`${helperBlock}\nthis.helpers = { propertyTabsForNodeType, propertyKindForNodeType };`, context);
    return context.helpers;
}

function settingsTabsFor(widget) {
    const source = readFileSync(resolve(root, 'public', widget.settings), 'utf8');
    return Array.from(source.matchAll(/settingsTab\s*(?:===|==|=)\s*['"]([a-z]+)['"]/g), (match) => match[1])
        .filter((value, index, values) => values.indexOf(value) === index)
        .sort();
}

test('every registered Layout and Widget exposes exactly the tabs implemented by its settings module', () => {
    const helpers = mountPropertyHelpers();
    const widgets = registeredWidgets();

    assert.equal(widgets.length, 36, 'all active v2.3 registry entries should be audited');

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

test('widget labels expose a stable 24px target at the default 80 percent zoom', () => {
    assert.match(css, /\.webpage-frame \.pb-node-label\.widget-label::before\s*\{[^}]*inset:\s*-5px;[^}]*content:\s*['"]{2};/);
});

test('clicking a canvas node label reveals the collapsed properties panel', () => {
    assert.match(app, /@click\.stop="onSelect\(node,\s*\{\s*revealPanel:\s*true\s*\}\)"/);
    assert.match(app, /function selectNode\(n,\s*options\s*=\s*\{\}\)\s*\{[\s\S]*?if\s*\(options\.revealPanel\)\s*leftCollapsed\.value\s*=\s*false;/);
});

test('flex column widths live in Container Layout instead of selectable canvas column chrome', () => {
    assert.match(containerSettings, /<summary>Column widths<\/summary>/);
    assert.match(containerSettings, /\['row','row-reverse'\]\.includes\(editor\.containerResponsiveValue\(node\.settings,'direction','row'\)\)/);
    assert.match(containerSettings, /v-for="\(column, index\) in node\.columns"/);
    assert.match(containerSettings, /editor\.columnSettingsWidthValue\(node, index\)/);
    assert.match(containerSettings, /editor\.setColumnSettingsWidthValue\(node, index, \$event\.target\.value\)/);
    assert.match(app, /columnSettingsWidthValue,/);
    assert.match(app, /setColumnSettingsWidthValue,/);
    assert.match(css, /\.webpage-frame \.pb-grid-col-label,[\s\S]*?\.webpage-frame \.pb-col-resizer\s*\{\s*display:\s*none\s*!important;/);
});
