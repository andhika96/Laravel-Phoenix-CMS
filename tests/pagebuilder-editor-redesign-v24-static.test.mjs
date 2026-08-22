import test from 'node:test';
import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';
import vm from 'node:vm';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const v22Path = resolve(root, 'public/mockups/pagebuilder-editor-redesign-prototype.html');
const v24Path = resolve(root, 'public/mockups/pagebuilder-editor-redesign-prototype-v2.4.html');

function readRailTools(html) {
    const match = html.match(/const railTools = (\[[\s\S]*?\n\s*\]);/);
    assert.ok(match, 'railTools definition should exist');

    return vm.runInNewContext(`(${match[1]})`);
}

function mountPrototypeSetup(html) {
    const inlineScript = html.match(/<script>\s*([\s\S]*?)\s*<\/script>\s*<\/body>/)?.[1];
    assert.ok(inlineScript, 'inline Vue script should exist');

    let component = null;
    const Vue = {
        createApp(definition) {
            component = definition;
            return { mount() {} };
        },
        computed(getter) {
            return { get value() { return getter(); } };
        },
        reactive(value) {
            return value;
        },
        ref(value) {
            return { value };
        },
    };

    vm.runInNewContext(inlineScript, { Vue, window: { setTimeout() {} } });
    assert.ok(component, 'Vue component should be created');

    return component.setup();
}

test('Page Builder editor redesign v2.4 only exposes tools supported by production', () => {
    assert.equal(existsSync(v24Path), true, 'v2.4 prototype file should exist');

    const html = readFileSync(v24Path, 'utf8');

    assert.match(html, /<title>[^<]*v2\.4[^<]*<\/title>/i);
    assert.doesNotMatch(html, /activeTool === ['"](?:layers|pages|global)['"]/);
    assert.doesNotMatch(html, /\b(?:layerSearch|filteredLayers|globalColors)\b/);

    const inlineScript = html.match(/<script>\s*([\s\S]*?)\s*<\/script>\s*<\/body>/)?.[1];
    assert.ok(inlineScript, 'inline Vue script should exist');
    assert.doesNotThrow(() => new Function(inlineScript));
});

test('Page Builder editor redesign v2.4 uses one contextual sidebar', () => {
    const html = readFileSync(v24Path, 'utf8');
    const sidebars = html.match(/<aside\b/g) || [];
    const contextualSidebar = html.match(/<aside class="side-panel left-panel">[\s\S]*?<\/aside>/)?.[0];

    assert.equal(sidebars.length, 1, 'only the contextual left sidebar should remain');
    assert.ok(contextualSidebar, 'contextual left sidebar should exist');
    assert.doesNotMatch(html, /<nav class="tool-rail"/);
    assert.doesNotMatch(html, /<aside class="side-panel right"/);
    assert.match(contextualSidebar, /<template v-if="!selectedSection">[\s\S]*Search widgets/);
    assert.match(contextualSidebar, /<template v-else>[\s\S]*activePropertyTabs/);
});

test('Page Builder editor redesign v2.4 switches the left sidebar between Elements and selected settings', () => {
    const html = readFileSync(v24Path, 'utf8');
    const state = mountPrototypeSetup(html);

    assert.equal(state.selectedId.value, null, 'Elements should be visible initially');
    assert.equal(state.selectedEntity.value, null);

    state.selectSection('hero');
    assert.equal(state.selectedId.value, 'hero');
    assert.equal(state.selectedEntity.value.kind, 'Container');
    assert.equal(state.propertyTab.value, 'layout');

    state.selectSection('hero-heading');
    assert.equal(state.selectedEntity.value.kind, 'Widget');
    assert.equal(state.propertyTab.value, 'content');

    state.showToolboxPanel();
    assert.equal(state.selectedId.value, '');
    assert.equal(state.selectedEntity.value, null);
    assert.equal(state.pendingInsertTarget.value, null);
    assert.equal('rightCollapsed' in state, false, 'right sidebar state should no longer exist');
});

test('Page Builder editor redesign v2.2 remains unchanged as the historical baseline', () => {
    const html = readFileSync(v22Path, 'utf8');
    const railTools = readRailTools(html);

    assert.deepEqual(Array.from(railTools, (tool) => tool.id), ['elements', 'layers', 'pages', 'global']);
});
