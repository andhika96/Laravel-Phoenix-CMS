import assert from 'node:assert/strict';
import test from 'node:test';
import fs from 'node:fs';

const root = new URL('../', import.meta.url);
const read = (path) => fs.readFileSync(new URL(path, root), 'utf8');

test('v2.4 Custom JavaScript editor is visible in page settings and keeps execution opt-in', () => {
  const app = read('public/js/pagebuilder_elementor_v24/app.js');

  assert.match(app, /const customJs\s*=\s*ref\(/);
  assert.match(app, /const customJsMode\s*=\s*ref\(/);
  assert.match(app, /published/);
  assert.match(app, /page-settings-javascript/);
  assert.match(app, /pb-js-editor-modal/);
  assert.match(app, /customJsPublishAcknowledged/);
  assert.match(app, /aria-live="polite"/);
  assert.match(app, /@keydown\.esc="closeCustomJsEditor"/);
  assert.match(app, /customJs:/);
  assert.match(app, /customJsMode:/);
  assert.doesNotMatch(app, /eval\s*\(|new Function\s*\(/);
});

test('v2.4 editor does not create an executable script element from customJs', () => {
  const app = read('public/js/pagebuilder_elementor_v24/app.js');

  assert.doesNotMatch(app, /createElement\(['"]script['"]\)[\s\S]{0,220}customJs/);
  assert.doesNotMatch(app, /customJs[\s\S]{0,220}createElement\(['"]script['"]\)/);
});
