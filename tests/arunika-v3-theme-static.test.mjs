import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const layoutPath = path.join(root, 'resources/views/themes/arunika_v3/cms/cms_layout.blade.php');
const menuPath = path.join(root, 'resources/views/themes/arunika_v3/components/menu.blade.php');
const cssPath = path.join(root, 'public/assets/css/themes/arunika_v3/arunika_v3.css');
const jsPath = path.join(root, 'public/assets/js/themes/arunika_v3/arunika_v3.js');
const previewPath = path.join(root, 'public/assets/images/themes/previews/arunika-v3-theme-preview.png');

const read = (filePath) => readFileSync(filePath, 'utf8');

test('Arunika V3 has an isolated theme shell and preview asset', () => {
  for (const filePath of [layoutPath, menuPath, cssPath, jsPath, previewPath]) {
    assert.equal(existsSync(filePath), true, `Missing ${path.relative(root, filePath)}`);
  }
});

test('Arunika V3 layout keeps dynamic content and uses only V3 theme assets', () => {
  const layout = read(layoutPath);

  assert.match(layout, /themes\.arunika_v3\.components\.menu/);
  assert.match(layout, /assets\/css\/themes\/arunika_v3\/arunika_v3\.css/);
  assert.match(layout, /assets\/js\/themes\/arunika_v3\/arunika_v3\.js/);
  assert.match(layout, /menu_versioning\(\)/);
  assert.match(layout, /@yield\('content'\)/);
  assert.match(layout, /class="ph-theme-arunika-v3"/);
  assert.match(layout, /ph-header-profile/);
  assert.match(layout, /components\.cms-realtime-notification/);
  assert.doesNotMatch(layout, /themes\.arunika_v2|themes\/arunika_v2/);
});

test('Arunika V3 stylesheet contains the approved dashboard-shell tokens', () => {
  const css = read(cssPath);

  assert.match(css, /ARUNIKA V3 DASHBOARD SHELL/);
  assert.match(css, /--ph-v3-sidebar-surface:\s*#efefed/i);
  assert.match(css, /--ph-v3-header-height:\s*60px/i);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-profile/);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-search-container/);
});

test('Theme Manager and seed data register Arunika V3', () => {
  const controller = read(path.join(root, 'app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php'));
  const seeder = read(path.join(root, 'database/seeders_new/ThemesSeeder.php'));

  assert.match(controller, /'arunika_v3'/);
  assert.match(controller, /arunika-v3-theme-preview\.png/);
  assert.match(seeder, /'theme_code'\s*=>\s*'arunika_v3'/);
  assert.match(seeder, /'theme_foldername'\s*=>\s*'arunika_v3'/);
});
