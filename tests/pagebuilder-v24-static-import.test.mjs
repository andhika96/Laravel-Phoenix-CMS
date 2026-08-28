import assert from 'node:assert/strict';
import test from 'node:test';
import fs from 'node:fs';

const root = new URL('../', import.meta.url);
const read = (path) => fs.readFileSync(new URL(path, root), 'utf8');

test('v2.4 exposes an authenticated static import endpoint and Canvas trigger', () => {
  const route = read('routes/pagebuilder_elementor_v24.php');
  const shell = read('resources/views/pagebuilder_elementor_v24/editor_shell.blade.php');
  const app = read('public/js/pagebuilder_elementor_v24/app.js');

  assert.match(route, /Route::post\('\/import\/static', 'importStatic'\)/);
  assert.match(shell, /staticImportUrl:/);
  assert.match(app, /async function importStaticFile\(event\)/);
  assert.match(app, /rootNodes\.value = norm\(result\.layout\)/);
  assert.match(app, /class="top-action"[^\n]*Import Static/);
  assert.match(app, /accept="\.html,\.htm,\.zip/);
  assert.match(app, /staticImportFramework/);
  assert.match(app, /function showStaticImportReport\(\)/);
  assert.match(app, /function downloadStaticImportJson\(\)/);
});
