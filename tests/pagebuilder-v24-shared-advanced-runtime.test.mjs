import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

const filename = path.resolve(import.meta.dirname, '..', 'resources', 'pagebuilder_elementor_v24', 'shared', 'AdvancedControls.vue');
const source = fs.readFileSync(filename, 'utf8');
const parsed = parse(source, { filename });
assert.deepEqual(parsed.errors, []);
const component = Function(parsed.descriptor.script.content.replace(/export\s+default/, 'return'))();
component.render = Function('Vue', compile(parsed.descriptor.template.content, { mode: 'function', prefixIdentifiers: true }).code)(Vue);

globalThis.window ??= globalThis;
const definitions = new Map();
window.PageBuilderElementorV24Widgets = { get: (type) => definitions.get(type) ?? null };

async function render(type, advanced, settings = {}) {
    definitions.set(type, { type, advanced });
    return renderToString(Vue.createSSRApp(component, {
        node: { type, settings },
        responsiveDevice: 'desktop',
    }));
}

test('minimal Button Advanced preserves className without exposing unsupported controls', async () => {
    const html = await render('button', {
        profile: 'widget',
        capabilities: ['minimal-advanced', 'class-name'],
    }, { className: 'btn btn-primary' });

    assert.match(html, /Attributes/);
    assert.match(html, /CSS Class/);
    assert.match(html, /btn btn-primary/);
    assert.doesNotMatch(html, /Motion Effects/);
});

test('layout capability exposes legacy keys from the one canonical Advanced source', async () => {
    const html = await render('grid', {
        profile: 'layout',
        capabilities: ['legacy-layout'],
    }, {
        scrollingEffects: true,
        mouseEffects: true,
        sticky: 'top',
        overflow: 'auto',
    });

    for (const label of ['Layout Compatibility', 'Overflow', 'Scroll Effect Type', 'Mouse Effect Type', 'Sticky On Desktop', 'Position Offsets', 'Scale X']) {
        assert.match(html, new RegExp(label));
    }
});

test('ordinary widget capability keeps the complete canonical Advanced surface', async () => {
    const html = await render('heading', { profile: 'widget', capabilities: [] }, {});

    for (const label of ['Layout', 'Motion Effects', 'Transform', 'Background', 'Border', 'Mask', 'Responsive', 'Attributes', 'Custom CSS']) {
        assert.match(html, new RegExp(label));
    }
    assert.doesNotMatch(html, /Layout Compatibility/);
});
