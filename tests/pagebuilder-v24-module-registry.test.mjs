import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const registrySource = readFileSync(
    new URL('../public/js/pagebuilder_elementor_v24/widget-registry.js', import.meta.url),
    'utf8',
);

function loadRegistry() {
    const context = { window: {} };
    context.window.window = context.window;
    vm.runInNewContext(registrySource, context);

    return context.window.PageBuilderElementorV24Widgets;
}

function manifest(type = 'button', overrides = {}) {
    return {
        type,
        label: 'Button',
        category: 'basic',
        icon: 'fas fa-link',
        order: 60,
        toolbox: true,
        assets: {
            definition: `/pagebuilder-elementor/v2.4/module-assets/${type}/definition.js`,
            canvas: `/pagebuilder-elementor/v2.4/module-assets/${type}/canvas.vue`,
            settings: `/pagebuilder-elementor/v2.4/module-assets/${type}/settings.vue`,
        },
        advanced: { profile: 'widget', capabilities: [] },
        capabilities: [],
        ...overrides,
    };
}

test('configured registry uses catalog metadata and asset URLs as the authority', () => {
    const registry = loadRegistry();
    const buttonManifest = manifest();

    registry.configure({ button: buttonManifest });
    registry.register({
        type: 'button',
        label: 'Wrong label',
        category: 'wrong',
        icon: 'wrong',
        canvas: '/wrong-canvas.vue',
        settings: '/wrong-settings.vue',
        defaults: () => ({ text: 'Click here' }),
        normalize: (node) => node,
    });

    const button = registry.get('button');
    assert.equal(button.label, 'Button');
    assert.equal(button.category, 'basic');
    assert.equal(button.canvas, buttonManifest.assets.canvas);
    assert.equal(button.settings, buttonManifest.assets.settings);
    assert.equal(button.defaults().text, 'Click here');
    assert.equal(registry.toolbox().basic[0].type, 'button');
});

test('configured registry rejects definitions that are absent from the server catalog', () => {
    const registry = loadRegistry();
    registry.configure({ button: manifest() });

    assert.throws(
        () => registry.register({ type: 'missing', defaults: () => ({}), normalize: (node) => node }),
        /not present in the module catalog/i,
    );
});

test('configured registry keeps hidden runtime modules registered but out of the toolbox', () => {
    const registry = loadRegistry();
    registry.configure({
        container_fluid: manifest('container_fluid', {
            label: 'Container Fluid',
            category: 'layout',
            toolbox: false,
        }),
    });

    registry.register({ type: 'container_fluid', defaults: () => ({}), normalize: (node) => node });

    assert.ok(registry.get('container_fluid'));
    assert.equal(Object.keys(registry.toolbox()).length, 0);
});

test('unconfigured definitions cannot reintroduce removed legacy asset paths', () => {
    const registry = loadRegistry();

    registry.register({
        type: 'legacy',
        label: 'Legacy',
        category: 'basic',
        icon: 'fas fa-box',
        canvas: '/legacy/Canvas.vue',
        settings: '/legacy/Settings.vue',
        defaults: () => ({}),
        normalize: (node) => node,
    });

	assert.equal(registry.get('legacy').canvas, undefined);
	assert.equal(registry.get('legacy').settings, undefined);
	assert.equal(registry.get('legacy').toolbox, false);
});

test('catalog configuration is rejected after definitions have started registering', () => {
    const registry = loadRegistry();
    registry.register({
        type: 'legacy',
        label: 'Legacy',
        category: 'basic',
        icon: 'fas fa-box',
        canvas: '/legacy/Canvas.vue',
        settings: '/legacy/Settings.vue',
        defaults: () => ({}),
        normalize: (node) => node,
    });

    assert.throws(() => registry.configure({ button: manifest() }), /before definitions register/i);
});
