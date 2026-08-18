import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');

const settingsSources = {
  'Hero Slider': readFileSync(
    `${root}/public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Settings.vue`,
    'utf8',
  ),
  'Product Color Selector': readFileSync(
    `${root}/public/js/pagebuilder_elementor_v23/widgets/pro/product-color-selector/Settings.vue`,
    'utf8',
  ),
};

test('lag-prone widget settings mount only the active editor tab', () => {
  for (const [widget, source] of Object.entries(settingsSources)) {
    assert.match(source, /v-if="editor\.settingsTab==='content'"/, `${widget} content tab`);
    assert.match(source, /v-if="editor\.settingsTab==='style'"/, `${widget} style tab`);
    assert.match(source, /v-if="editor\.settingsTab==='advanced'"/, `${widget} advanced tab`);
    assert.doesNotMatch(source, /v-show="editor\.settingsTab===['"](?:content|style|advanced)['"]"/, `${widget} keeps hidden tabs mounted`);
  }
});
