import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

const testDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(testDir, '..');
const mgImageUrl = 'https://assets.mgmotor.id/contents/userfiles/mgmodels/coverimage/202405/02318367ffba401342f0b62a66747ba7.webp';

async function loadSfc(relativePath) {
  const filename = join(rootDir, relativePath);
  const source = await readFile(filename, 'utf8');
  const { descriptor, errors } = parse(source, { filename });
  assert.deepEqual(errors, []);
  const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
  component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function' }).code)(Vue);
  return component;
}

const editor = {
  settingsTab: 'content', responsiveDevices: [], sizeControlUnits: ['px'],
  chooseMedia() {}, clearMedia() {}, openControlResponsiveMenu() {}, applyResponsiveDevice() {},
  responsiveDeviceLabel: () => 'Desktop', responsiveDeviceIcon: () => 'fas fa-desktop',
  isControlResponsiveMenuOpen: () => false, deviceOptionLabel: () => '',
  sizeControlMax: () => 100, sizeControlStep: () => 1, sizeControlDisplayValue: () => 100,
  sizeControlUnit: () => 'px', onSizeControlInput() {}, setSizeControlUnit() {},
};

async function renderComponent(component, props) {
  return renderToString(Vue.createSSRApp(component, props));
}

test('v2.3 Image defaults legacy nodes to CKFinder and switches to an external URL without clearing src', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/basic/image/Settings.vue');
  const node = { settings: { src: 'legacy.jpg', alt: 'Legacy image' } };

  const legacyHtml = await renderComponent(component, { node, editor });
  assert.match(legacyHtml, /Image Source/);
  assert.match(legacyHtml, /Choose Image/);
  assert.doesNotMatch(legacyHtml, /Image URL/);
  assert.equal(component.computed.imageSource.call({ node }), 'ckfinder');

  component.methods.setImageSource.call({ node }, 'url');
  assert.equal(node.settings.imageSource, 'url');
  assert.equal(node.settings.src, 'legacy.jpg');
  node.settings.src = mgImageUrl;

  const urlHtml = await renderComponent(component, { node, editor });
  assert.match(urlHtml, /External URL/);
  assert.match(urlHtml, /Image URL/);
  assert.match(urlHtml, new RegExp(`value="${mgImageUrl}"`));
  assert.doesNotMatch(urlHtml, /Choose Image/);

  node.settings.imageSource = 'unexpected';
  assert.equal(component.computed.imageSource.call({ node }), 'ckfinder');
});

test('v2.3 Image canvas renders the canonical external settings.src', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/basic/image/Canvas.vue');
  const html = await renderComponent(component, {
    item: { settings: { src: mgImageUrl, alt: 'MG5 GT' } },
    responsiveDevice: 'desktop',
  });
  assert.match(html, new RegExp(`src="${mgImageUrl}"`));
  assert.match(html, /alt="MG5 GT"/);
});

test('v2.0 Image settings remain isolated from the v2.3 source selector', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor/widgets/basic/image/Settings.vue');
  const html = await renderComponent(component, {
    node: { settings: { src: 'legacy.jpg', alt: 'Legacy image' } },
    editor,
  });
  assert.doesNotMatch(html, /Image Source/);
});
