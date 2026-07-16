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

test('Arunika V3 places desktop navigation and appearance controls in the approved header hierarchy', () => {
  const layout = read(layoutPath);
  const topBarIndex = layout.indexOf('class="ph-top-bar"');
  const headerNavIndex = layout.indexOf('class="ph-header-nav-control"');
  const toggleIndex = layout.indexOf('id="sidebar-toggle"');
  const dividerIndex = layout.indexOf('class="ph-header-divider"');
  const searchIndex = layout.indexOf('class="ph-search-container"');
  const profileMenuIndex = layout.indexOf('ph-header-profile-menu');
  const colorPickerIndex = layout.indexOf('id="color-picker-container"');
  const themeToggleIndex = layout.indexOf('ph-profile-theme-toggle');

  assert.ok(topBarIndex >= 0, 'Missing Arunika V3 top bar');
  assert.ok(headerNavIndex > topBarIndex, 'Desktop navigation control must live inside the top bar');
  assert.ok(toggleIndex > headerNavIndex && toggleIndex < searchIndex, 'Sidebar toggle must sit before search');
  assert.ok(dividerIndex > toggleIndex && dividerIndex < searchIndex, 'Divider must separate toggle and search');
  assert.ok(colorPickerIndex > profileMenuIndex, 'Theme colors must live inside the profile menu');
  assert.ok(themeToggleIndex > profileMenuIndex, 'Dark mode must live inside the profile menu');
  assert.doesNotMatch(layout, /class="dropdown ph-theme-color-picker"/);
});

test('Arunika V3 stylesheet contains the approved dashboard-shell tokens', () => {
  const css = read(cssPath);
  const shellCss = css.slice(css.indexOf('ARUNIKA V3 DASHBOARD SHELL'));
  const rootRule = shellCss.match(/\.ph-theme-arunika-v3\s*\{([^}]*)\}/s)?.[1] ?? '';

  assert.match(css, /ARUNIKA V3 DASHBOARD SHELL/);
  assert.match(css, /--ph-v3-sidebar-surface:\s*#efefed/i);
  assert.match(rootRule, /--ph-sidebar-width-expanded:\s*256px;/);
  assert.match(css, /--ph-v3-header-height:\s*52px/i);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-nav-control/);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-divider/);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-divider\s*\{[^}]*margin-right:\s*7px;/s);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-search-container\s*\{[^}]*width:\s*min\(220px,\s*32vw\)/s);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-profile/);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-search-container/);
  assert.doesNotMatch(css, /@media\s*\(max-width:\s*1100px\)/);
});

test('Arunika V3 uses one compact typography scale across the page and profile menu', () => {
  const css = read(cssPath);

  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-profile-meta\s+strong\s*\{[^}]*font-size:\s*12px;/s);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-profile-meta\s+span\s*\{[^}]*font-size:\s*10px;/s);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-header-profile-menu\s+\.dropdown-item\s*\{[^}]*font-size:\s*13px;/s);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.ph-profile-appearance\s+\.ph-theme-color-title\s*\{[^}]*font-size:\s*10px;/s);
  assert.match(css, /\.ph-theme-arunika-v3\s+\.theme-manager-heading\s+h1\s*\{[^}]*font-size:\s*22px;/s);
});

test('Theme Manager and seed data register Arunika V3', () => {
  const controller = read(path.join(root, 'app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php'));
  const seeder = read(path.join(root, 'database/seeders_new/ThemesSeeder.php'));

  assert.match(controller, /'arunika_v3'/);
  assert.match(controller, /arunika-v3-theme-preview\.png/);
  assert.match(seeder, /'theme_code'\s*=>\s*'arunika_v3'/);
  assert.match(seeder, /'theme_foldername'\s*=>\s*'arunika_v3'/);
});
