import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const layoutPath = path.join(root, 'resources/views/themes/arunika_canvas/cms/cms_layout.blade.php');
const menuPath = path.join(root, 'resources/views/themes/arunika_canvas/components/menu.blade.php');
const cssPath = path.join(root, 'public/assets/css/themes/arunika_canvas/arunika_canvas.css');
const jsPath = path.join(root, 'public/assets/js/themes/arunika_canvas/arunika_canvas.js');
const previewPath = path.join(root, 'public/assets/images/themes/previews/arunika-canvas-theme-preview.png');

const read = (filePath) => readFileSync(filePath, 'utf8');

test('Arunika Canvas has an isolated theme shell and preview asset', () => {
  for (const filePath of [layoutPath, menuPath, cssPath, jsPath, previewPath]) {
    assert.equal(existsSync(filePath), true, `Missing ${path.relative(root, filePath)}`);
  }
});

test('Arunika Canvas layout keeps dynamic content and uses only Canvas theme assets', () => {
  const layout = read(layoutPath);

  assert.match(layout, /themes\.arunika_canvas\.components\.menu/);
  assert.match(layout, /assets\/css\/themes\/arunika_canvas\/arunika_canvas\.css/);
  assert.match(layout, /assets\/js\/themes\/arunika_canvas\/arunika_canvas\.js/);
  assert.match(layout, /menu_versioning\(\)/);
  assert.match(layout, /@yield\('content'\)/);
  assert.match(layout, /class="ph-theme-arunika-canvas"/);
  assert.match(layout, /ph-header-profile/);
  assert.match(layout, /components\.cms-realtime-notification/);
  assert.doesNotMatch(layout, /themes\.arunika_aurora|themes\/arunika_aurora/);
});

test('Arunika Canvas stylesheet contains the approved dashboard-shell tokens', () => {
  const css = read(cssPath);

  assert.match(css, /ARUNIKA CANVAS DASHBOARD SHELL/);
  assert.match(css, /--ph-canvas-sidebar-surface:\s*#efefed/i);
  assert.match(css, /--ph-canvas-header-height:\s*60px/i);
  assert.match(css, /\.ph-theme-arunika-canvas\s+\.ph-header-profile/);
  assert.match(css, /\.ph-theme-arunika-canvas\s+\.ph-search-container/);
});

test('Theme Manager and seed data register Arunika Canvas', () => {
  const controller = read(path.join(root, 'app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php'));
  const seeder = read(path.join(root, 'database/seeders_new/ThemesSeeder.php'));

  assert.match(controller, /'arunika_canvas'/);
  assert.match(controller, /arunika-canvas-theme-preview\.png/);
  assert.match(seeder, /'theme_code'\s*=>\s*'arunika_canvas'/);
  assert.match(seeder, /'theme_foldername'\s*=>\s*'arunika_canvas'/);
});
