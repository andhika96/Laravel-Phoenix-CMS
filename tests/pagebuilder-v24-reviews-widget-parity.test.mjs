import assert from 'node:assert/strict';
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

globalThis.window ??= globalThis;
globalThis.window.matchMedia ??= () => ({
  matches: false,
  addEventListener() {},
  removeEventListener() {},
});

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
  component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function', prefixIdentifiers: true }).code)(Vue);
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
    typographyControl: { template: '<div>Typography</div>' },
    fontFamilies: [],
    chooseMedia() {},
    openProIconLibrary() {},
    chooseProIconSvg() {},
    setResponsiveDevice() {},
    openControlResponsiveMenu() {},
    applyResponsiveDevice() {},
    responsiveDeviceLabel: () => 'Desktop',
    responsiveDeviceIcon: () => 'fas fa-desktop',
    isControlResponsiveMenuOpen: () => false,
    deviceOptionLabel: () => '',
    activeResponsiveKey: (key) => key,
    setResponsiveSetting(target, key, value) { target[key] = value; },
    sizeControlDisplayValue: (node, key, fallback) => Number.parseFloat(node.settings[key] || fallback) || 0,
    sizeControlUnit: (node, key, fallback) => String(node.settings[key] || fallback).match(/[a-z%]+$/i)?.[0] || 'px',
    onSizeControlInput() {},
    setSizeControlUnit() {},
    fontAwesomeStyleLabel: () => 'Brands',
  };
}

test('Reviews registers as a Pro widget with Elementor carousel defaults', async () => {
  const context = { window: {} };
  vm.runInNewContext(await source('public/js/pagebuilder_elementor_v24/widget-registry.js'), context);
  configureSingleModule(context, await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/reviews/module.json'));
  vm.runInNewContext(await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/reviews/definition.js'), context);

  const definition = context.window.PageBuilderElementorV24Widgets.get('reviews');
  const defaults = definition.defaults();

  assert.equal(definition.category, 'pro');
  assert.equal(definition.label, 'Reviews');
  assert.equal(defaults.items.length, 3);
  assert.equal(defaults.slidesToShow, 1);
  assert.equal(defaults.pagination, 'dots');
  assert.equal(defaults.autoplay, true);
  assert.equal(defaults.items[0].iconClass, 'fab fa-twitter');
});

test('Reviews settings expose mapped Content, Style, responsive, icon and Advanced controls', async () => {
  const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/pro/reviews/Settings.vue');
  const settings = {
    slidesName: 'Slides',
    items: [{ id: 'review-1', name: 'John Doe', title: '@username', rating: 5, review: 'Excellent service', iconSource: 'library', iconClass: 'fab fa-twitter' }],
    slidesToShow: 1,
    slidesToScroll: 1,
    reviewsWidth: '100%',
    pagination: 'dots',
    arrows: true,
    autoplay: true,
    imageResolution: 'full',
    reviewSeparator: true,
    iconColorMode: 'official',
    ratingIcon: 'fontawesome',
  };

  const contentHtml = await renderToString(Vue.createSSRApp(component, { node: { type: 'reviews', settings: { ...settings } }, editor: editorFor('content') }));
  for (const label of ['Slides Name', 'Name', 'Title', 'Rating', 'Icon', 'Link', 'Review', 'Slides Per View', 'Slides to Scroll', 'Width', 'Additional Options', 'Pagination', 'Transition Duration', 'Image Resolution', 'Lazy Load']) {
    assert.match(contentHtml, new RegExp(label));
  }

  const styleHtml = await renderToString(Vue.createSSRApp(component, { node: { type: 'reviews', settings: { ...settings } }, editor: editorFor('style') }));
  for (const label of ['Space Between', 'Background Color', 'Border Width', 'Border Radius', 'Padding', 'Header', 'Separator', 'Name', 'Title', 'Review', 'Image', 'Icon', 'Official', 'Rating', 'Unmarked Style', 'Navigation', 'Space Between Dots', 'Active Color']) {
    assert.match(styleHtml, new RegExp(label));
  }

  const advancedHtml = await renderToString(Vue.createSSRApp(component, { node: { type: 'reviews', settings: { ...settings } }, editor: editorFor('advanced') }));
  assert.match(advancedHtml, /responsive-device="desktop"/);
});

test('Reviews canvas renders the review card and reuses interactive carousel controls', async () => {
  const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/pro/reviews/Canvas.vue');
  const html = await renderToString(Vue.createSSRApp(component, {
    item: { id: 'reviews-test', type: 'reviews', settings: {
      items: [
        { id: 'review-1', imageUrl: '/avatar.jpg', name: 'John Doe', title: '@username', rating: 4.5, review: 'Excellent service', linkUrl: '/profile', iconSource: 'library', iconClass: 'fab fa-twitter' },
        { id: 'review-2', imageUrl: '', name: 'Jane Doe', title: '@customer', rating: 5, review: 'Great experience', linkUrl: '', iconSource: 'library', iconClass: 'fab fa-twitter' },
      ],
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: true,
      pagination: 'dots',
    } },
    responsiveDevice: 'desktop',
  }));

  assert.match(html, /data-pro-carousel/);
  assert.match(html, /pb-pro-reviews__slide/);
  assert.match(html, /John Doe/);
  assert.match(html, /@username/);
  assert.match(html, /Excellent service/);
  assert.match(html, /fab fa-twitter/);
  assert.match(html, /Previous slide/);
  assert.match(html, /Next slide/);
});
