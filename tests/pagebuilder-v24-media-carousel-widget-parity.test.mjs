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

async function renderCanvas(component, props) {
  const warnings = [];
  const app = Vue.createSSRApp(component, props);
  app.config.warnHandler = (message) => warnings.push(message);
  const html = await renderToString(app);
  assert.deepEqual(warnings, [], warnings.join('\n'));
  return html;
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
  vm.runInNewContext(await source('public/js/pagebuilder_elementor_v24/widget-registry.js'), context);
  configureSingleModule(context, await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/module.json'));
  vm.runInNewContext(await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/definition.js'), context);

  const definition = context.window.PageBuilderElementorV24Widgets.get('media_carousel');
  const defaults = definition.defaults();

  assert.equal(definition.category, 'pro');
  assert.equal(definition.label, 'Media Carousel');
  assert.equal(defaults.items.length, 5);
  assert.equal(defaults.skin, 'carousel');
  assert.equal(defaults.pagination, 'dots');
  assert.equal(defaults.autoplay, true);
  assert.equal(defaults.arrowPosition, 'inside');
  assert.equal(defaults.arrowEdgeOffset, '46px');
  assert.equal(defaults.arrowButtonSize, '20px');
  assert.equal(defaults.arrowIconSize, '10px');
  assert.equal(defaults.previousArrowIcon, 'fas fa-chevron-left');
  assert.equal(defaults.nextArrowIcon, 'fas fa-chevron-right');
  assert.deepEqual(Array.from(defaults.skins), ['carousel', 'slideshow', 'coverflow']);
});

test('Media Carousel settings expose mapped Content, conditional skins, Style and Advanced controls', async () => {
  const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/Settings.vue');
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
  for (const label of ['Space Between', 'Background Color', 'Border Width', 'Border Radius', 'Padding', 'Navigation', 'Arrows', 'Previous Arrow Icon', 'Next Arrow Icon', 'Position', 'Edge Offset', 'Button Size', 'Icon Size', 'Icon Color', 'Button Background', 'Hover Icon Color', 'Hover Background', 'Button Radius', 'Pagination', 'Play Icon', 'Overlay', 'Text Color', 'Lightbox', 'UI Color', 'UI Hover Color', 'Video Width']) {
    assert.match(styleHtml, new RegExp(label));
  }

  const advancedHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'media_carousel', settings: { ...baseSettings } },
    editor: editorFor('advanced'),
  }));
  assert.match(advancedHtml, /responsive-device="desktop"/);
});

test('Media Carousel canvas renders slideshow thumbnails, media overlay and interactive lightbox triggers', async () => {
  const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/Canvas.vue');
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

  const canvasSource = await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/Canvas.vue');
  assert.match(canvasSource, /\.pb-pro-media-lightbox__close:(?:hover|focus-visible)[^{]*\{[^}]*var\(--media-lightbox-ui-hover/);
});

test('Media Carousel canvas applies complete responsive arrow button styling and custom icons', async () => {
  const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/Canvas.vue');
  const html = await renderToString(Vue.createSSRApp(component, {
    item: {
      id: 'media-arrow-test',
      type: 'media_carousel',
      settings: {
        ...baseSettings,
        slidesToShow: 1,
        previousArrowIcon: 'fas fa-angle-left',
        nextArrowIcon: 'fas fa-angle-right',
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
      },
    },
    responsiveDevice: 'desktop',
  }));

  for (const declaration of [
    '--carousel-arrow-button-size:44px',
    '--carousel-arrow-icon-size:19px',
    '--carousel-arrow-edge-position:calc(0px - 44px - 12px)',
    '--arrow-color:#112233',
    '--arrow-background:#ddeeff',
    '--arrow-hover-color:#ffffff',
    '--arrow-hover-background:#334455',
    '--carousel-arrow-radius:4px 8px 12px 16px',
  ]) assert.match(html, new RegExp(declaration.replace(/[()]/g, '\\$&')));

  assert.match(html, /arrow-position-outside/);
  assert.match(html, /fa-angle-left/);
  assert.match(html, /fa-angle-right/);
});

test('Media Carousel slideshow thumbnail controls change count, ratio, and active centering', async () => {
  const component = await loadSfc('resources/pagebuilder_elementor_v24/modules/widgets/pro/media-carousel/Canvas.vue');
  const settings = {
    ...baseSettings,
    skin: 'slideshow',
    items: Array.from({ length: 5 }, (_, index) => ({
      id: `media-${index + 1}`,
      type: 'image',
      imageUrl: `/${index + 1}.jpg`,
      linkType: 'none',
      title: `Slide ${index + 1}`,
    })),
    thumbsSlidesToShow: 2,
    thumbsRatio: '1:1',
    centeredSlides: true,
  };
  const html = await renderCanvas(component, {
    item: { id: 'media-thumbnails-test', type: 'media_carousel', settings },
    responsiveDevice: 'desktop',
  });

  assert.match(html, /pb-pro-media-carousel__thumbnail-track/);
  assert.match(html, /--media-thumbs-per-view:2/);
  assert.match(html, /--media-thumbs-ratio:1 \/ 1/);
  assert.match(html, /pb-pro-media-carousel__thumbnails is-centered/);
  assert.match(html, /translate3d\(25%,0,0\)/);
});
