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

test('Arunika Equinox V2 preserves dynamic CMS content with a reference-aligned header profile', () => {
  const layout = read(paths.layout);

  assert.match(layout, /themes\.arunika_equinox\.components\.menu/);
  assert.match(layout, /assets\/css\/themes\/arunika_equinox\/arunika_equinox\.css/);
  assert.match(layout, /assets\/js\/themes\/arunika_equinox\/arunika_equinox\.js/);
  assert.match(layout, /class="ph-theme-arunika-equinox"/);
  assert.match(layout, /menu_versioning\(\)/);
  assert.match(layout, /@yield\('content'\)/);
  assert.match(layout, /class="dropdown ph-header-profile"/);
  assert.match(layout, /class="ph-header-user-card"/);
  assert.match(layout, /get_avatar\('no_frame',\s*'rounded-circle',\s*36\)/);
  assert.match(layout, /components\.cms-realtime-notification/);
  assert.doesNotMatch(layout, /class="ph-sidebar-footer"/);
  assert.doesNotMatch(layout, /class="ph-sidebar-user-card"/);
  assert.doesNotMatch(layout, /class="ph-profile-menu-user"/);
  assert.doesNotMatch(layout, /Your collateral|John Deere|Claim Rewards/);
  assert.doesNotMatch(layout, /themes\.arunika_prism|themes\/arunika_prism/);
});

test('Arunika Equinox V2 defines the approved mint light and deep teal dark systems', () => {
  const css = read(paths.css);

  assert.match(css, /ARUNIKA EQUINOX V2/);
  assert.match(css, /--ph-equinox-accent:\s*#0bb89f/i);
  assert.match(css, /--ph-equinox-canvas:\s*#f8fbfa/i);
  assert.match(css, /--ph-equinox-sidebar-start:\s*#eaf7f3/i);
  assert.match(css, /--ph-equinox-sidebar-end:\s*#fff8f1/i);
  assert.match(css, /--ph-equinox-panel:\s*rgba\(255,\s*255,\s*255,\s*0\.42\)/i);
  assert.match(css, /--ph-equinox-border:\s*#d9e7e3/i);
  assert.match(css, /--ph-equinox-text:\s*#142a27/i);
  assert.match(css, /--ph-equinox-muted:\s*#647a76/i);
  assert.match(
    css,
    /html\[data-bs-theme=dark\]\s+body\.ph-theme-arunika-equinox\s*\{[^}]*--ph-equinox-canvas:\s*#03433f;[^}]*--ph-equinox-sidebar-start:\s*#0c514c;[^}]*--ph-equinox-text:\s*#f5fffc;/s,
  );
});

test('Arunika Equinox V2 recreates the edge-to-edge reference shell', () => {
  const css = read(paths.css);

  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-app-shell\s*\{[^}]*width:\s*100%\s*!important;[^}]*height:\s*100%\s*!important;[^}]*margin:\s*0;[^}]*border:\s*0;[^}]*border-radius:\s*0;[^}]*overflow:\s*hidden;[^}]*background:\s*transparent;/s,
  );
  assert.match(
    css,
    /body\.ph-theme-arunika-equinox\s*\{[^}]*--ph-equinox-main-gradient:[^}]*background:\s*var\(--ph-equinox-main-gradient\)\s*!important;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar,\s*\.ph-theme-arunika-equinox\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*height:\s*calc\(100% - 28px\);[^}]*margin:\s*14px 0 14px 14px;[^}]*background:\s*linear-gradient\(180deg,\s*var\(--ph-equinox-sidebar-start\)[^}]*border:\s*1px solid var\(--ph-equinox-sidebar-border-strong\);[^}]*border-radius:\s*26px;[^}]*box-shadow:\s*var\(--ph-equinox-sidebar-shadow\);/s,
  );
  assert.match(
    css,
        /\.ph-theme-arunika-equinox\s+\.ph-layout-right\s*\{[^}]*margin:\s*0;[^}]*background:\s*transparent;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-top-bar\s*\{[^}]*height:\s*72px;[^}]*padding:\s*0 28px;[^}]*background:\s*transparent;[^}]*border-bottom:\s*0;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-search-container\s*\{[^}]*width:\s*min\(320px,\s*32vw\);[^}]*height:\s*40px;[^}]*display:\s*flex\s*!important;[^}]*border:\s*1px solid var\(--ph-equinox-border\);[^}]*border-radius:\s*999px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-header-user-card\s*\{[^}]*display:\s*grid;[^}]*grid-template-columns:\s*36px minmax\(0,\s*1fr\);[^}]*background:\s*transparent;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-header-user-avatar\s*\{[^}]*border:\s*0;[^}]*background:\s*transparent;[^}]*box-shadow:\s*none;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+#sidebar-scroll-content\s*\{[^}]*padding:\s*0 8px 16px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar\s+\.list-group-item,\s*\.ph-theme-arunika-equinox\s+\.ph-sidebar\.ph-expanded\s+\.list-group-item\s*\{[^}]*padding:\s*0 10px;/s,
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
