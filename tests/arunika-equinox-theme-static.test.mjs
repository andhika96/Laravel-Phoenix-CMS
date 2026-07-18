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

test('Arunika Equinox exposes the admin shortcut and a persistent interactive profile palette', () => {
  const layout = read(paths.layout);
  const css = read(paths.css);
  const script = read(paths.js);

  assert.match(
    layout,
    /@if\(checkIsAdmin\(\)\)[\s\S]*?<a href="\{\{ url\('awesome_admin'\) \}\}" class="ph-btn-action-icon ph-header-awesome-admin"[^>]*>[\s\S]*?<i class="fal fa-user-secret"><\/i>[\s\S]*?@endif/,
  );
  assert.match(
    layout,
    /<a class="dropdown-item" href="\{\{ url\('account'\) \}\}">\s*<i class="fal fa-cog fa-fw"><\/i>\s*<span>\{\{ t\('Settings'\) \}\}<\/span>\s*<\/a>/,
  );
  assert.doesNotMatch(
    layout,
    /@if\(checkIsAdmin\(\)\)\s*<a class="dropdown-item" href="\{\{ url\('account'\) \}\}">/,
  );
  assert.match(layout, /const defaultEquinoxColor = '#0F766E';/);
  assert.match(
    layout,
    /const savedColor = localStorage\.getItem\('theme-color'\) \|\| defaultEquinoxColor;/,
  );
  assert.match(layout, /localStorage\.setItem\('theme-color', savedColor\);/);
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-header-awesome-admin\s*\{[^}]*display:\s*inline-flex;/s,
  );
  assert.match(
    script,
    /let colorMainList = \['#0F766E', '#1FA675',[\s\S]*'#C7CCD8'\];/,
  );
  assert.match(script, /function renderColorPicker\(\)/);
  assert.match(script, /document\.createElement\('button'\)/);
  assert.match(script, /addEventListener\('click', \(\) => changeMainColor\(color\)\)/);
  assert.match(script, /function syncColorPickerSelection\(color\)/);
  assert.match(script, /classList\.toggle\('is-active', isActive\)/);
  assert.doesNotMatch(script, /onclick="changeMainColor/);
});

test('Arunika Equinox V2 defines the approved mint light and deep teal dark systems', () => {
  const css = read(paths.css);

  assert.match(css, /ARUNIKA EQUINOX V2/);
  assert.match(css, /--ph-theme-primary:\s*#0F766E/i);
  assert.match(css, /--ph-equinox-accent:\s*var\(--ph-theme-primary\)/i);
  assert.match(
    css,
    /--ph-equinox-canvas:\s*color-mix\(in srgb,\s*var\(--ph-theme-surface-tint\) 10%,\s*#eaf2ef 90%\)/i,
  );
  assert.match(
    css,
    /--ph-equinox-sidebar-start:\s*color-mix\(in srgb,\s*var\(--ph-theme-surface-tint\) 14%,\s*#dfece7 86%\)/i,
  );
  assert.match(
    css,
    /--ph-equinox-sidebar-end:\s*color-mix\(in srgb,\s*var\(--ph-theme-surface-tint\) 5%,\s*#f2ebe3 95%\)/i,
  );
  assert.match(css, /--ph-equinox-panel:\s*rgba\(249,\s*253,\s*251,\s*0\.46\)/i);
  assert.match(
    css,
    /--ph-equinox-border:\s*color-mix\(in srgb,\s*var\(--ph-theme-surface-tint\) 18%,\s*#c8d8d3 82%\)/i,
  );
  assert.match(css, /--ph-equinox-text:\s*#142a27/i);
  assert.match(css, /--ph-equinox-muted:\s*#647a76/i);
  assert.match(
    css,
    /html\[data-bs-theme=dark\]\s+body\.ph-theme-arunika-equinox\s*\{[^}]*--ph-equinox-canvas:\s*#03433f;[^}]*--ph-equinox-sidebar-start:\s*#0c514c;[^}]*--ph-equinox-text:\s*#f5fffc;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-btn-theme\s*\{[^}]*background:\s*var\(--ph-equinox-accent\);[^}]*box-shadow:\s*0 8px 20px color-mix\(in srgb,\s*var\(--ph-equinox-accent\),\s*transparent 84%\);/s,
  );
  assert.doesNotMatch(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-btn-theme\s*\{[^}]*background:\s*linear-gradient\(/s,
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
    /html\[data-bs-theme=light\]\s+\.ph-theme-arunika-equinox\s+\.ph-header-user-avatar\s+img\s*\{[^}]*border:\s*2px solid rgba\(11,\s*184,\s*159,\s*0\.46\)\s*!important;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-header-profile-menu\s*\{[^}]*margin-top:\s*14px\s*!important;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-header-profile-menu::before,\s*\.ph-theme-arunika-equinox\s+\.ph-header-profile-menu::after\s*\{[^}]*left:\s*68%\s*!important;[^}]*right:\s*auto\s*!important;[^}]*transform:\s*translateX\(-50%\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+#sidebar-scroll-content\s*\{[^}]*padding:\s*0 8px 16px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar\s+\.ph-list-group-wrapper\s*\{[^}]*padding:\s*0 4px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar\.ph-expanded\s+\.ph-list-group-wrapper\s*\{[^}]*padding:\s*0 10px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar\s+\.list-group-item,\s*\.ph-theme-arunika-equinox\s+\.ph-sidebar\.ph-expanded\s+\.list-group-item\s*\{[^}]*padding:\s*0 10px;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar:not\(\.ph-expanded\)\s+\.list-group-item\s*\{[^}]*width:\s*44px;[^}]*margin:\s*2px auto;[^}]*padding:\s*0;[^}]*justify-content:\s*center;/s,
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
