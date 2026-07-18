import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const paths = {
  auth: 'resources/views/themes/arunika_equinox/auth/auth_layout.blade.php',
  layout: 'resources/views/themes/arunika_equinox/cms/cms_layout.blade.php',
  menu: 'resources/views/themes/arunika_equinox/components/menu.blade.php',
  frontend: 'resources/views/themes/arunika_equinox/frontend/frontend_layout.blade.php',
  css: 'public/assets/css/themes/arunika_equinox/arunika_equinox.css',
  js: 'public/assets/js/themes/arunika_equinox/arunika_equinox.js',
  preview: 'public/assets/images/themes/previews/arunika-equinox-theme-preview.png',
  migration: 'database/migrations/2026_07_18_143000_add_arunika_equinox_theme.php',
};

const read = (relativePath) => readFileSync(path.join(root, relativePath), 'utf8');

test('Arunika Equinox owns an isolated runtime theme and preview', () => {
  for (const relativePath of Object.values(paths)) {
    assert.equal(existsSync(path.join(root, relativePath)), true, `Missing ${relativePath}`);
  }
});

test('Arunika Equinox preserves the dynamic Prism CMS contract', () => {
  const layout = read(paths.layout);

  assert.match(layout, /themes\.arunika_equinox\.components\.menu/);
  assert.match(layout, /assets\/css\/themes\/arunika_equinox\/arunika_equinox\.css/);
  assert.match(layout, /assets\/js\/themes\/arunika_equinox\/arunika_equinox\.js/);
  assert.match(layout, /class="ph-theme-arunika-equinox"/);
  assert.match(layout, /menu_versioning\(\)/);
  assert.match(layout, /@yield\('content'\)/);
  assert.match(layout, /class="ph-sidebar-user-card"/);
  assert.match(layout, /components\.cms-realtime-notification/);
  assert.doesNotMatch(layout, /Your collateral|John Deere|Claim Rewards/);
  assert.doesNotMatch(layout, /themes\.arunika_prism|themes\/arunika_prism/);
});

test('Arunika Equinox defines the approved light and dark visual systems', () => {
  const css = read(paths.css);

  assert.match(css, /ARUNIKA EQUINOX DASHBOARD SHELL/);
  assert.match(css, /--ph-equinox-backdrop:\s*#dff5ed/i);
  assert.match(css, /--ph-equinox-sidebar-surface:\s*rgba\(231,\s*241,\s*237,\s*0\.94\)/i);
  assert.match(css, /--ph-equinox-content-surface:\s*#f7faf8/i);
  assert.match(css, /--ph-equinox-card-surface:\s*rgba\(255,\s*255,\s*255,\s*0\.78\)/i);
  assert.match(css, /--ph-equinox-border:\s*#d7e5df/i);
  assert.match(css, /--ph-equinox-text:\s*#1d2d29/i);
  assert.match(css, /--ph-equinox-muted:\s*#667872/i);
  assert.match(
    css,
    /html\[data-bs-theme=dark\]\s+\.ph-theme-arunika-equinox\s*\{[^}]*--ph-equinox-backdrop:\s*#082f2c;[^}]*--ph-equinox-content-surface:\s*#063f3b;[^}]*--ph-equinox-text:\s*#f6fffc;/s,
  );
});

test('Arunika Equinox recreates the unified rounded dashboard shell', () => {
  const css = read(paths.css);

  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-app-shell\s*\{[^}]*width:\s*calc\(100%\s*-\s*32px\)\s*!important;[^}]*height:\s*calc\(100%\s*-\s*32px\)\s*!important;[^}]*margin:\s*16px;[^}]*border-radius:\s*28px;[^}]*overflow:\s*hidden;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar,\s*\.ph-theme-arunika-equinox\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*var\(--ph-equinox-sidebar-gradient\);[^}]*border-right:\s*1px solid var\(--ph-equinox-border\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-layout-right\s*\{[^}]*margin:\s*0;[^}]*border-radius:\s*0;[^}]*background:\s*var\(--ph-equinox-content-gradient\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-top-bar\s*\{[^}]*height:\s*76px;[^}]*background:\s*var\(--ph-equinox-header-surface\);[^}]*border-bottom:\s*0;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-search-container\s*\{[^}]*width:\s*min\(320px,\s*34vw\);[^}]*display:\s*flex\s*!important;[^}]*border:\s*1px solid var\(--ph-equinox-border\);[^}]*border-radius:\s*16px;/s,
  );
});

test('Theme Manager, seeder, and migration register Arunika Equinox', () => {
  const controller = read('app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php');
  const seeder = read('database/seeders_new/ThemesSeeder.php');
  const migration = read(paths.migration);

  assert.match(controller, /'arunika_equinox'/);
  assert.match(controller, /'display_name'\s*=>\s*'Arunika Equinox'/);
  assert.match(controller, /arunika-equinox-theme-preview\.png/);
  assert.match(seeder, /'theme_code'\s*=>\s*'arunika_equinox'/);
  assert.match(seeder, /'theme_foldername'\s*=>\s*'arunika_equinox'/);
  assert.match(migration, /'theme_code'\s*=>\s*'arunika_equinox'/);
  assert.match(migration, /public function down\(\): void/);
  assert.match(migration, /arunika_prism/);
});
