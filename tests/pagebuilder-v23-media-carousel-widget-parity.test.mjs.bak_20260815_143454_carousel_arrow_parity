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
    typographyControl: { template: '<div>Typography</div>' },
    textShadowControl: EmptyControl,
    fontFamilies: [],
    chooseMedia() {},
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
  };
}

const baseSettings = {
  skin: 'carousel',
  slidesName: 'Slides',
  items: [
    { id: 'media-1', type: 'image', imageUrl: '/one.jpg', linkType: 'media', linkUrl: '', title: 'One', caption: 'First', description: 'First image' },
    { id: 'media-2', type: 'video', imageUrl: '/two.jpg', videoUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', linkType: 'none', title: 'Two', caption: '', description: '' },
  ],
  effect: 'slide',
  slidesToShow: 2,
  slidesToScroll: 1,
  height: '300px',
  width: '100%',
  arrows: true,
  pagination: 'dots',
  autoplay: true,
  overlay: 'text',
  captionSource: 'title',
  overlayAnimation: 'fade',
  imageResolution: 'full',
  imageFit: 'cover',
};

test('Media Carousel registers as a Pro widget with all three Elementor skins', async () => {
  const context = { window: {} };
  vm.runInNewContext(await source('public/js/pagebuilder_elementor_v23/widget-registry.js'), context);
  vm.runInNewContext(await source('public/js/pagebuilder_elementor_v23/widgets/pro/media-carousel/definition.js'), context);

  const definition = context.window.PageBuilderElementorV23Widgets.get('media_carousel');
  const defaults = definition.defaults();

  assert.equal(definition.category, 'pro');
  assert.equal(definition.label, 'Media Carousel');
  assert.equal(defaults.items.length, 5);
  assert.equal(defaults.skin, 'carousel');
  assert.equal(defaults.pagination, 'dots');
  assert.equal(defaults.autoplay, true);
  assert.deepEqual(Array.from(defaults.skins), ['carousel', 'slideshow', 'coverflow']);
});

test('Media Carousel settings expose mapped Content, conditional skins, Style and Advanced controls', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue');
  const contentHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'media_carousel', settings: { ...baseSettings } },
    editor: editorFor('content'),
  }));
  for (const label of ['Skin', 'Carousel', 'Slideshow', 'Coverflow', 'Slides Name', 'Type', 'Image', 'Video Link', 'Link', 'Effect', 'Slides Per View', 'Slides to Scroll', 'Height', 'Width', 'Additional Options', 'Arrows', 'Pagination', 'Transition Duration', 'Overlay', 'Image Resolution', 'Image Fit', 'Lazy Load']) {
    assert.match(contentHtml, new RegExp(label));
  }

  const slideshowHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'media_carousel', settings: { ...baseSettings, skin: 'slideshow', thumbsRatio: '21:9', centeredSlides: false } },
    editor: editorFor('content'),
  }));
  for (const label of ['Thumbnails', 'Ratio', 'Centered Slides']) assert.match(slideshowHtml, new RegExp(label));

  const styleHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'media_carousel', settings: { ...baseSettings } },
    editor: editorFor('style'),
  }));
  for (const label of ['Space Between', 'Background Color', 'Border Width', 'Border Radius', 'Padding', 'Navigation', 'Arrows', 'Pagination', 'Play Icon', 'Overlay', 'Text Color', 'Icon Size', 'Lightbox', 'UI Color', 'UI Hover Color', 'Video Width']) {
    assert.match(styleHtml, new RegExp(label));
  }

  const advancedHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'media_carousel', settings: { ...baseSettings } },
    editor: editorFor('advanced'),
  }));
  assert.match(advancedHtml, /responsive-device="desktop"/);
});

test('Media Carousel canvas renders slideshow thumbnails, media overlay and interactive lightbox triggers', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
  const html = await renderToString(Vue.createSSRApp(component, {
    item: { id: 'media-test', type: 'media_carousel', settings: { ...baseSettings, skin: 'slideshow', slidesToShow: 1, lightboxUiHoverColor: '#ff3366' } },
    responsiveDevice: 'desktop',
  }));

  assert.match(html, /data-pro-carousel/);
  assert.match(html, /pb-pro-media-carousel--slideshow/);
  assert.match(html, /pb-pro-media-carousel__thumbnail/);
  assert.match(html, /pb-pro-media-carousel__overlay/);
  assert.match(html, /pb-pro-media-carousel__play/);
  assert.match(html, /data-pro-media-lightbox/);
  assert.match(html, /--media-lightbox-ui-hover:#ff3366/);
  assert.match(html, /Previous slide/);
  assert.match(html, /Next slide/);

  const canvasSource = await source('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
  assert.match(canvasSource, /\.pb-pro-media-lightbox__close:(?:hover|focus-visible)[^{]*\{[^}]*var\(--media-lightbox-ui-hover/);
});

test('Media Carousel arrows stay fully inside the viewport edge at every configured size', async () => {
  const canvasSource = await source('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
  const frontendSource = await source('resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');

  assert.match(canvasSource, /\.pb-pro-carousel\s*\{[^}]*--carousel-arrow-gutter:\s*46px;[^}]*padding:\s*0 var\(--carousel-arrow-gutter\) 24px;/s);
  assert.match(canvasSource, /\.pb-pro-media-carousel \.pb-pro-arrow--prev\s*\{[^}]*left:\s*var\(--carousel-arrow-gutter, 46px\);[^}]*transform:\s*translateY\(-50%\);/s);
  assert.match(canvasSource, /\.pb-pro-media-carousel \.pb-pro-arrow--next\s*\{[^}]*right:\s*var\(--carousel-arrow-gutter, 46px\);[^}]*transform:\s*translateY\(-50%\);/s);

  assert.match(frontendSource, /\.pb-pro-media-carousel\{--carousel-arrow-gutter:46px\}/);
  assert.match(frontendSource, /\.pb-pro-media-carousel \.pb-pro-arrow\{[^}]*width:var\(--carousel-arrow-size,24px\);[^}]*height:var\(--carousel-arrow-size,24px\)/);
  assert.match(frontendSource, /\.pb-pro-media-carousel \.pb-pro-arrow--prev\{left:var\(--carousel-arrow-gutter,46px\);transform:translateY\(-50%\)\}/);
  assert.match(frontendSource, /\.pb-pro-media-carousel \.pb-pro-arrow--next\{right:var\(--carousel-arrow-gutter,46px\);transform:translateY\(-50%\)\}/);
});

test('Media Carousel arrow icons stay centered and scale inside small buttons', async () => {
  const canvasSource = await source('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
  const frontendSource = await source('resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');

  assert.match(canvasSource, /\.pb-pro-media-carousel \.pb-pro-arrow\s*\{[^}]*display:\s*grid;[^}]*place-items:\s*center;[^}]*padding:\s*0;[^}]*line-height:\s*1;/s);
  assert.match(canvasSource, /\.pb-pro-media-carousel \.pb-pro-arrow\s*\{[^}]*font-size:\s*var\(--carousel-arrow-size, 20px\);/s);
  assert.match(canvasSource, /\.pb-pro-media-carousel \.pb-pro-arrow\s*>\s*i\s*\{[^}]*font-size:\s*min\(50%, 16px\);[^}]*line-height:\s*1;/s);

  assert.match(frontendSource, /\.pb-pro-media-carousel \.pb-pro-arrow\{[^}]*display:grid;[^}]*place-items:center;[^}]*padding:0;[^}]*line-height:1/);
  assert.match(frontendSource, /\.pb-pro-media-carousel \.pb-pro-arrow>i\{font-size:min\(50%,16px\);line-height:1\}/);
});
