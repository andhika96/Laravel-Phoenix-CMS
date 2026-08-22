import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const app = readFileSync(`${root}/public/js/pagebuilder_elementor_v24/app.js`, 'utf8');
const css = readFileSync(`${root}/public/assets/css/pagebuilder_elementor_v24.css`, 'utf8');

test('shared settings loader exposes loading and error states', () => {
  assert.match(app, /const WidgetSettingsLoading\s*=\s*\{/);
  assert.match(app, /const WidgetSettingsError\s*=\s*\{/);
  assert.match(app, /defineAsyncComponent\(\{[\s\S]*?loader:\s*\(\)\s*=>\s*loadSfcModule\(path\)/);
  assert.match(app, /loadingComponent:\s*WidgetSettingsLoading/);
  assert.match(app, /errorComponent:\s*WidgetSettingsError/);
  assert.match(app, /delay:\s*0/);
  assert.match(app, /Loading widget settings\.\.\./);
  assert.match(app, /role="status"/);
  assert.match(app, /role="alert"/);
  assert.match(css, /\.pb-widget-settings-loading/);
  assert.match(css, /@keyframes pb-widget-settings-spin/);
});
