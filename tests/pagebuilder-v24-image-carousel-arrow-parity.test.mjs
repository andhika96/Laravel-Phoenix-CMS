import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

globalThis.window ??= globalThis;
globalThis.window.matchMedia ??= () => ({ matches: false });

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');

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
	const EmptyControl = { template: '<div></div>' };
	return {
		settingsTab,
		responsiveDevice: 'desktop',
		responsiveDevices: [],
		widgetAdvancedControls: EmptyControl,
		linkControl: EmptyControl,
		typographyControl: EmptyControl,
		textShadowControl: EmptyControl,
		fontFamilies: [],
		chooseMediaGallery() {},
		moveMediaGalleryItem() {},
		removeMediaGalleryItem() {},
		openImageCarouselArrowIconLibrary() {},
		chooseImageCarouselArrowSvg() {},
		setResponsiveDevice() {},
		openControlResponsiveMenu() {},
		applyResponsiveDevice() {},
		responsiveDeviceLabel: () => 'Desktop',
		responsiveDeviceIcon: () => 'fas fa-desktop',
		isControlResponsiveMenuOpen: () => false,
		deviceOptionLabel: () => '',
		activeResponsiveKey: (key) => key,
		setResponsiveSetting(target, key, value) { target[key] = value; },
		sizeControlMax: () => 200,
		sizeControlStep: () => 1,
		sizeControlDisplayValue: (node, key, fallback) => Number.parseFloat(node.settings[key] || fallback) || 0,
		sizeControlUnit: (node, key, fallback) => String(node.settings[key] || fallback).match(/[a-z%]+$/i)?.[0] || 'px',
		onSizeControlInput() {},
		setSizeControlUnit() {},
	};
}

const settings = {
	images: [
		{ id: 'one', url: '/one.jpg', alt: 'One' },
		{ id: 'two', url: '/two.jpg', alt: 'Two' },
	],
	slidesToShow: '1',
	slidesToScroll: '1',
	navigation: 'arrows',
	previousArrowIcon: 'fas fa-angle-left',
	previousArrowIconSource: 'library',
	nextArrowIcon: 'fas fa-angle-right',
	nextArrowIconSource: 'library',
	arrowPosition: 'outside',
	arrowEdgeOffset: '12px',
	arrowButtonSize: '44px',
	arrowIconSize: '19px',
	arrowColor: '#112233',
	arrowBackground: '#ddeeff',
	arrowHoverColor: '#ffffff',
	arrowHoverBackground: '#334455',
	arrowRadiusTop: '4px',
	arrowRadiusRight: '8px',
	arrowRadiusBottom: '12px',
	arrowRadiusLeft: '16px',
	infiniteLoop: true,
	autoplay: false,
};

test('Image Carousel exposes the complete responsive arrow button controls', async () => {
	const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/general/image-carousel/Settings.vue');
	const html = await renderToString(Vue.createSSRApp(component, {
		node: { type: 'image_carousel', settings: { ...settings } },
		editor: editorFor('style'),
	}));

	for (const label of ['Previous Arrow Icon', 'Next Arrow Icon', 'Position', 'Edge Offset', 'Button Size', 'Icon Size', 'Icon Color', 'Button Background', 'Hover Icon Color', 'Hover Background', 'Button Radius']) {
		assert.match(html, new RegExp(label));
	}
});

test('Image Carousel canvas applies separate arrow geometry, state colors, radius, placement and icons', async () => {
	const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/general/image-carousel/Canvas.vue');
	const html = await renderToString(Vue.createSSRApp(component, {
		item: { id: 'image-carousel-arrow-test', type: 'image_carousel', settings: { ...settings } },
		responsiveDevice: 'desktop',
	}));

	for (const declaration of [
		'--pb-carousel-arrow-button-size:44px',
		'--pb-carousel-arrow-icon-size:19px',
		'--pb-carousel-arrow-edge-offset:12px',
		'--pb-carousel-arrow-color:#112233',
		'--pb-carousel-arrow-background:#ddeeff',
		'--pb-carousel-arrow-hover-color:#ffffff',
		'--pb-carousel-arrow-hover-background:#334455',
		'--pb-carousel-arrow-radius:4px 8px 12px 16px',
	]) assert.match(html, new RegExp(declaration));

	assert.match(html, /is-arrows-outside/);
	assert.match(html, /fa-angle-left/);
	assert.match(html, /fa-angle-right/);
});
