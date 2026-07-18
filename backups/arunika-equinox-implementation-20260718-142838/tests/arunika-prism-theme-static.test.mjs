import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const layoutPath = path.join(root, 'resources/views/themes/arunika_prism/cms/cms_layout.blade.php');
const menuPath = path.join(root, 'resources/views/themes/arunika_prism/components/menu.blade.php');
const cssPath = path.join(root, 'public/assets/css/themes/arunika_prism/arunika_prism.css');
const jsPath = path.join(root, 'public/assets/js/themes/arunika_prism/arunika_prism.js');
const previewPath = path.join(root, 'public/assets/images/themes/previews/arunika-prism-theme-preview.png');

const read = (filePath) => readFileSync(filePath, 'utf8');

test('Arunika Prism has an isolated theme shell and preview asset', () => {
  for (const filePath of [layoutPath, menuPath, cssPath, jsPath, previewPath]) {
    assert.equal(existsSync(filePath), true, `Missing ${path.relative(root, filePath)}`);
  }
});

test('Arunika Prism layout keeps dynamic content and uses only Prism theme assets', () => {
  const layout = read(layoutPath);

  assert.match(layout, /themes\.arunika_prism\.components\.menu/);
  assert.match(layout, /assets\/css\/themes\/arunika_prism\/arunika_prism\.css/);
  assert.match(layout, /assets\/js\/themes\/arunika_prism\/arunika_prism\.js/);
  assert.match(layout, /menu_versioning\(\)/);
  assert.match(layout, /@yield\('content'\)/);
  assert.match(layout, /class="ph-theme-arunika-prism"/);
  assert.match(layout, /ph-header-profile/);
  assert.match(layout, /components\.cms-realtime-notification/);
  assert.doesNotMatch(layout, /themes\.arunika_aurora|themes\/arunika_aurora/);
});

test('Arunika Prism stylesheet contains the approved dashboard-shell tokens', () => {
  const css = read(cssPath);

  assert.match(css, /ARUNIKA PRISM DASHBOARD SHELL/);
  assert.match(css, /--ph-prism-sidebar-surface:\s*#efefed/i);
  assert.match(css, /--ph-prism-header-height:\s*60px/i);
  assert.match(css, /\.ph-theme-arunika-prism\s+\.ph-header-profile/);
  assert.match(css, /\.ph-theme-arunika-prism\s+\.ph-search-container/);
});

test('Theme Manager and seed data register Arunika Prism', () => {
  const controller = read(path.join(root, 'app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php'));
  const seeder = read(path.join(root, 'database/seeders_new/ThemesSeeder.php'));

  assert.match(controller, /'arunika_prism'/);
  assert.match(controller, /arunika-prism-theme-preview\.png/);
  assert.match(seeder, /'theme_code'\s*=>\s*'arunika_prism'/);
  assert.match(seeder, /'theme_foldername'\s*=>\s*'arunika_prism'/);
});
