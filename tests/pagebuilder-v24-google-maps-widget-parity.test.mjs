import assert from 'node:assert/strict';
import { existsSync } from 'node:fs';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';
import { configureSingleModule } from './helpers/pagebuilder-v24-module-test.mjs';

const testDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(testDir, '..');

async function source(relativePath) {
	return readFile(join(rootDir, relativePath), 'utf8');
}

async function loadSfc(relativePath) {
	const filename = join(rootDir, relativePath);
	const contents = await readFile(filename, 'utf8');
	const { descriptor, errors } = parse(contents, { filename });
	assert.deepEqual(errors, []);
	const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
	component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function' }).code)(Vue);
	return component;
}

function editorFor(settingsTab) {
	const EmptyControl = { template: '<div>Advanced</div>' };
	const CssFilterControl = { template: '<div>CSS Filters Blur Brightness Contrast Saturation Hue</div>' };
	return {
		settingsTab,
		responsiveDevice: 'desktop',
		responsiveDevices: [
			{ value: 'desktop', icon: 'fas fa-desktop', label: 'Desktop' },
			{ value: 'tablet', icon: 'fas fa-tablet-alt', label: 'Tablet' },
			{ value: 'mobile', icon: 'fas fa-mobile-alt', label: 'Mobile' },
		],
		widgetAdvancedControls: EmptyControl,
		cssFilterControl: CssFilterControl,
		setResponsiveDevice() {},
		openControlResponsiveMenu() {},
		applyResponsiveDevice() {},
		responsiveDeviceLabel: () => 'Desktop',
		responsiveDeviceIcon: () => 'fas fa-desktop',
		isControlResponsiveMenuOpen: () => false,
		deviceOptionLabel: (device) => device.label,
		activeResponsiveKey: (key) => key,
		setResponsiveSetting(target, key, value) { target[key] = value; },
	};
}

const mapSettings = {
	location: 'Sydney Opera House',
	zoom: 14,
	height: '400px',
	heightTablet: '320px',
	heightMobile: '260px',
	mapNormalFilter: { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 },
	mapHoverFilter: { blur: 2, brightness: 110, contrast: 105, saturation: 105, hue: 0 },
	transitionDuration: 0.4,
	cssClass: 'map-preview',
};

test('Google Maps registers as a Basic widget with safe responsive defaults', async () => {
	const definitionPath = join(rootDir, 'resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/definition.js');
	assert.equal(existsSync(definitionPath), true, 'google-maps definition must exist');

	const context = { window: {} };
	vm.runInNewContext(await source('public/js/pagebuilder_elementor_v24/widget-registry.js'), context);
	configureSingleModule(context, await source('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/module.json'));
	vm.runInNewContext(await source('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/definition.js'), context);

	const definition = context.window.PageBuilderElementorV24Widgets.get('google_maps');
	const defaults = definition.defaults();

	assert.equal(definition.category, 'basic');
	assert.equal(definition.label, 'Google Maps');
	assert.equal(defaults.location, 'New York, NY');
	assert.equal(defaults.zoom, 14);
	assert.equal(defaults.height, '400px');
	assert.equal(defaults.transitionDuration, 0.3);
	assert.deepEqual(JSON.parse(JSON.stringify(defaults.mapNormalFilter)), { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 });

	const normalized = definition.normalize({ settings: {
		location: '  1600 Amphitheatre Parkway  ',
		zoom: 99,
		height: 'invalid',
		heightTablet: '320px',
		mapNormalFilter: { blur: 999, brightness: -10 },
		transitionDuration: 99,
	} });
	assert.equal(normalized.settings.location, '1600 Amphitheatre Parkway');
	assert.equal(normalized.settings.zoom, 20);
	assert.equal(normalized.settings.height, '400px');
	assert.equal(normalized.settings.heightTablet, '320px');
	assert.equal(normalized.settings.mapNormalFilter.blur, 100);
	assert.equal(normalized.settings.mapNormalFilter.brightness, 0);
	assert.equal(normalized.settings.transitionDuration, 10);
});

test('Google Maps settings expose mapped Content, Style, responsive, and Advanced controls', async () => {
	const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/Settings.vue');

	const contentHtml = await renderToString(Vue.createSSRApp(component, {
		node: { id: 'maps-settings', type: 'google_maps', settings: { ...mapSettings } },
		editor: editorFor('content'),
	}));
	for (const label of ['Location', 'Zoom', 'address, place name, or coordinates']) {
		assert.match(contentHtml, new RegExp(label, 'i'));
	}

	const styleHtml = await renderToString(Vue.createSSRApp(component, {
		node: { id: 'maps-settings', type: 'google_maps', settings: { ...mapSettings } },
		editor: editorFor('style'),
	}));
	for (const label of ['Height', 'CSS Filters', 'Normal', 'Hover', 'Blur', 'Brightness', 'Contrast', 'Saturation', 'Hue', 'Transition Duration']) {
		assert.match(styleHtml, new RegExp(label));
	}

	const advancedHtml = await renderToString(Vue.createSSRApp(component, {
		node: { id: 'maps-settings', type: 'google_maps', settings: { ...mapSettings } },
		editor: editorFor('advanced'),
	}));
	assert.match(advancedHtml, /Advanced/);

	const settingsSource = await source('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/Settings.vue');
	assert.match(settingsSource, /ResponsiveMenu/);
	assert.match(settingsSource, /editor\.cssFilterControl/);
	assert.match(settingsSource, /editor\.widgetAdvancedControls/);
});

test('Google Maps canvas renders encoded embed, responsive height, filters, and empty fallback', async () => {
	const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/Canvas.vue');
	const html = await renderToString(Vue.createSSRApp(component, {
		item: { id: 'maps-canvas', type: 'google_maps', settings: mapSettings },
		responsiveDevice: 'desktop',
	}));

	assert.match(html, /data-basic-google-maps/);
	assert.match(html, /https:\/\/www\.google\.com\/maps\?q=Sydney%20Opera%20House/);
	assert.match(html, /z=14/);
	assert.match(html, /output=embed/);
	assert.match(html, /title="Google Maps"/);
	assert.match(html, /loading="lazy"/);
	assert.match(html, /height:400px/);
	assert.match(html, /--pb-google-maps-hover-filter/);
	assert.match(html, /transition/);
	assert.doesNotMatch(html, /<script>alert/);

	const emptyHtml = await renderToString(Vue.createSSRApp(component, {
		item: { id: 'maps-empty', type: 'google_maps', settings: { ...mapSettings, location: '' } },
		responsiveDevice: 'desktop',
	}));
	assert.match(emptyHtml, /data-google-maps-empty/);
	assert.doesNotMatch(emptyHtml, /<iframe/);
});

test('Google Maps is wired through the v2.4 registry, labels, Advanced gate, and Blade view', async () => {
	const manifest = JSON.parse(await source('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/module.json'));
	const blade = await source('resources/pagebuilder_elementor_v24/modules/widgets/basic/google-maps/frontend.blade.php');

	assert.equal(manifest.type, 'google_maps');
	assert.equal(manifest.category, 'basic');
	assert.equal(manifest.label, 'Google Maps');
	assert.equal(manifest.icon, 'fas fa-map-marker-alt');
	assert.equal(manifest.assets.view, 'frontend.blade.php');
	assert.equal(manifest.advanced.profile, 'widget');
	assert.match(blade, /https:\/\/www\.google\.com\/maps\?q=/);
	assert.match(blade, /output=embed/);
	assert.match(blade, /WidgetAdvancedStyleResolver/);
	assert.match(blade, /data-basic-google-maps/);
});
