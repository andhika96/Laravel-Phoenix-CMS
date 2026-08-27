import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const root = path.resolve(import.meta.dirname, '..');
const moduleRoot = path.join(root, 'resources', 'pagebuilder_elementor_v24', 'modules', 'widgets', 'pro', 'product-color-selector');
const read = (name) => fs.readFileSync(path.join(moduleRoot, name), 'utf8');

function loadDefinition() {
    const registry = {
        definition: null,
        advancedDefaults: () => ({}),
        register(definition) { this.definition = definition; },
    };
    const context = vm.createContext({ window: { PageBuilderElementorV24Widgets: registry } });
    vm.runInContext(read('definition.js'), context);
    assert.ok(registry.definition, 'Product Color Selector definition should register');
    return registry.definition;
}

test('Product Color Selector normalizes the widget-level Image Render Mode', () => {
    const definition = loadDefinition();
    assert.equal(definition.defaults().imageRenderMode, 'image');
    assert.equal(definition.normalize({ settings: { imageRenderMode: 'background' } }).settings.imageRenderMode, 'background');
    assert.equal(definition.normalize({ settings: { imageRenderMode: 'video' } }).settings.imageRenderMode, 'image');
});

test('Product Color Selector exposes Image and Background Image paths across editor and parser', () => {
    const settings = read('Settings.vue');
    const canvas = read('Canvas.vue');
    const frontend = read('frontend.blade.php');

    for (const marker of ['Image Render Mode', 'Image', 'Background Image', 'imageRenderMode']) {
        assert.ok(settings.includes(marker), `Settings must include ${marker}`);
    }
    for (const marker of ['imageRenderMode', 'pb-product-color-selector__image--background', 'safeUrl']) {
        assert.ok(canvas.includes(marker), `Canvas must include ${marker}`);
    }
    for (const marker of ['imageRenderMode', 'pb-product-color-selector__image--background', 'background-image', 'safeMedia']) {
        assert.ok(frontend.includes(marker), `Frontend must include ${marker}`);
    }
});
