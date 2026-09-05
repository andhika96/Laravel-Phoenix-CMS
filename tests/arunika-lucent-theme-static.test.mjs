import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const paths = {
  auth: 'resources/views/themes/arunika_lucent/auth/auth_layout.blade.php',
  layout: 'resources/views/themes/arunika_lucent/cms/cms_layout.blade.php',
  menu: 'resources/views/themes/arunika_lucent/components/menu.blade.php',
  frontend: 'resources/views/themes/arunika_lucent/frontend/frontend_layout.blade.php',
  css: 'public/assets/css/themes/arunika_lucent/arunika_lucent.css',
  js: 'public/assets/js/themes/arunika_lucent/arunika_lucent.js',
  preview: 'public/assets/images/themes/previews/arunika-lucent-theme-preview.png',
  migration: 'database/migrations/2026_09_01_210000_add_arunika_lucent_theme.php',
};

const read = (relativePath) => readFileSync(path.join(root, relativePath), 'utf8');

test('Arunika Lucent owns an isolated runtime theme and preview', () => {
  for (const relativePath of Object.values(paths)) {
    assert.equal(existsSync(path.join(root, relativePath)), true, `Missing ${relativePath}`);
  }
});

test('Arunika Lucent layout keeps dynamic CMS content and only Lucent assets', () => {
  const layout = read(paths.layout);
  const auth = read(paths.auth);
  const frontend = read(paths.frontend);
  const account = read('app/Models/Awesome_Admin/Account.php');

  assert.match(layout, /themes\.arunika_lucent\.components\.menu/);
  assert.match(layout, /assets\/css\/themes\/arunika_lucent\/arunika_lucent\.css/);
  assert.match(layout, /assets\/js\/themes\/arunika_lucent\/arunika_lucent\.js/);
  assert.match(layout, /class="ph-theme-arunika-lucent"/);
  assert.match(layout, /menu_versioning\(\)/);
  assert.match(layout, /@yield\('content'\)/);
  assert.match(layout, /theme-responsive-typography\.css/);
  assert.doesNotMatch(layout, /components\.cms-realtime-notification|ph-header-notification|cmsNotifBell/);
  assert.match(auth, /assets\/css\/themes\/arunika_lucent\/arunika_lucent\.css/);
  assert.match(auth, /theme-responsive-typography\.css/);
  assert.match(auth, /SiteTypography::class/);
  assert.match(auth, /--ph-font-size:/);
  assert.match(auth, /<body class="ph-theme-arunika-lucent">/);
  assert.match(frontend, /assets\/css\/themes\/arunika_lucent\/arunika_lucent\.css/);
  assert.match(frontend, /theme-responsive-typography\.css/);
  assert.match(frontend, /SiteTypography::class/);
  assert.match(frontend, /--ph-font-size:/);
  assert.match(frontend, /<body class="ph-theme-arunika-lucent">/);
  assert.match(layout, /ph-lucent-sidebar-account/);
  assert.match(layout, /auth\(\)->user\(\)->fullname/);
  assert.match(layout, /get_avatar\('frame'/);
  assert.match(layout, /ph-lucent-sidebar-utilities/);
  assert.match(layout, /@if\(checkIsAdmin\(\)\)/);
  assert.match(layout, /ph-lucent-sidebar-admin/);
  assert.match(layout, /t\('Awesome Admin'\)/);
  assert.doesNotMatch(layout, /t\('Support'\)/);
  assert.match(account, /hasRole\(\['Super Admin', 'Administrator'\]\)/);
  assert.match(layout, /id="ph-lucent-sidebar-toggle"/);
  assert.match(layout, /onclick="toggleSidebar\(\)"/);
  assert.doesNotMatch(layout, /ph-sidebar-logo-container/);
  assert.doesNotMatch(layout, /themes\.arunika_prism|themes\/arunika_prism|ph-theme-arunika-prism/);
  assert.doesNotMatch(layout, /themes\.arunika_equinox|themes\/arunika_equinox|ph-theme-arunika-equinox/);
});

test('Arunika Lucent keeps auth buttons and notices styled without a data theme attribute', () => {
  const css = read(paths.css);
  const rootVariables = css.match(/:root\s*\{([\s\S]*?)\}/)?.[1] ?? '';
  const defaultLightVariables = css.match(/:root,\s*\[data-bs-theme=light\]\s*\{([\s\S]*?)\}/)?.[1] ?? '';

  assert.match(rootVariables, /--ph-primary:\s*var\(--ph-theme-primary\);/);
  assert.match(rootVariables, /--ph-primary-hover-bg:\s*var\(--ph-theme-primary-hover\);/);
  assert.match(rootVariables, /--ph-primary-hover-text:\s*#FFFFFF;/);
  assert.match(rootVariables, /--ph-primary-active-bg:\s*var\(--ph-theme-primary\);/);
  assert.match(rootVariables, /--ph-primary-disable-bg:/);

  assert.notEqual(defaultLightVariables, '', 'Lucent must expose light-mode fallbacks on :root for auth pages.');
  assert.match(defaultLightVariables, /--ph-callout-bg:\s*#FFFFFF;/);
  assert.match(defaultLightVariables, /--ph-callout-color:\s*#000000;/);
  assert.match(defaultLightVariables, /--ph-callout-danger-color:\s*#DC3545;/);
  assert.match(defaultLightVariables, /--ph-toast-danger-rgb:\s*220,\s*53,\s*69;/);
});

test('Arunika Lucent uses a neutral Swiss shell with existing green semantic actions', () => {
  const css = read(paths.css);
  const faithfulShell = css.slice(css.lastIndexOf('ARUNIKA LUCENT — REFERENCE-FAITHFUL CMS SHELL'));

  assert.match(css, /ARUNIKA LUCENT/);
  assert.match(css, /--ph-lucent-accent:\s*var\(--ph-theme-primary\)/i);
  assert.match(css, /--ph-lucent-sidebar-surface:\s*#fafafa/i);
  assert.match(css, /--ph-lucent-content-surface:\s*#ffffff/i);
  assert.match(css, /--ph-lucent-border:\s*#e7e9e8/i);
  assert.match(css, /--ph-lucent-sidebar-width:\s*clamp\(250px,\s*20vw,\s*294px\)/i);
  assert.match(css, /\.ph-lucent-sidebar-account/);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-lucent-sidebar-account\s*\{[^}]*gap:\s*12px/s);
  assert.match(css, /\.ph-lucent-sidebar-account[\s\S]*?\.ph-sidebar-user-card[\s\S]*?border:\s*0\s*!important/i);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-lucent-sidebar-account\s+\.ph-sidebar-user-avatar\s*\{[^}]*border:\s*1px\s+solid\s+var\(--ph-lucent-border\)/s);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-lucent-sidebar-account\s+\.ph-sidebar-profile-menu\s*\{[^}]*top:\s*calc\(100% \+ 12px\)\s*!important/s);
  assert.match(css, /\.ph-lucent-sidebar-utilities/);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-content\s*\{[^}]*border:\s*1px\s+solid\s+var\(--ph-lucent-border\)\s*!important/s);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-content\s*\{[^}]*border-radius:\s*10px\s*!important/s);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-content\s*\{[^}]*background:\s*var\(--ph-lucent-content-surface\)/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-section\s*\{[^}]*border:\s*1px\s+solid\s+var\(--ph-lucent-border\)/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-section\s*\{[^}]*padding:\s*24px/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-section\s*\{[^}]*border-radius:\s*10px/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-section\s*\{[^}]*background:\s*var\(--ph-lucent-content-surface\)/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-content\.ph-section\s*\{[^}]*border:\s*1px\s+solid\s+var\(--ph-lucent-border\)\s*!important/s);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent[\s\S]*?\.ph-scrollable-content[\s\S]*?padding:\s*24px\s+22px\s+32px/i);
  assert.match(css, /\.ph-theme-arunika-lucent[\s\S]*?\.arv7-title[\s\S]*?display:\s*none/i);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-top-bar[\s\S]*?height:\s*var\(--ph-lucent-header-height\)/i);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-mobile-sidebar-trigger[\s\S]*?display:\s*inline-flex/i);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-mobile-sidebar-close[\s\S]*?display:\s*none/i);
  assert.match(faithfulShell, /--ph-lucent-sidebar-icon-size:\s*clamp\(15px,\s*calc\(var\(--ph-adaptive-font-size, 14px\) \+ 2px\),\s*16px\)/i);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-nav-icon[\s\S]*?font-size:\s*var\(--ph-lucent-sidebar-icon-size\)/i);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-sidebar:not\(\.ph-expanded\)\s+\.ph-lucent-sidebar-utilities\s+span[\s\S]*?display:\s*none/i);
  assert.match(faithfulShell, /\.ph-theme-arunika-lucent\s+\.ph-sidebar:not\(\.ph-expanded\)\s+\.ph-lucent-sidebar-utilities\s+a[\s\S]*?width:\s*40px/i);
  assert.match(faithfulShell, /@media\s*\(max-width:\s*768px\)[\s\S]*?\.ph-theme-arunika-lucent\s+\.ph-section\s*\{[^}]*padding:\s*16px/s);
  assert.match(faithfulShell, /@media\s*\(max-width:\s*768px\)[\s\S]*?\.ph-theme-arunika-lucent\s+\.ph-scrollable-content\s*\{[^}]*padding:\s*18px\s+14px\s+28px/s);
  assert.doesNotMatch(css, /\.ph-theme-arunika-lucent\s+a\s*,[\s\S]*?color:\s*var\(--ph-lucent-accent\)\s*!important/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.btn-primary[\s\S]*?background-color:\s*var\(--ph-lucent-accent\)/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.btn-outline-primary[\s\S]*?color:\s*var\(--ph-lucent-accent\)/s);
  assert.match(css, /\.ph-theme-arunika-lucent\s+\.ph-sidebar\s+\.list-group-item-action:hover[^}]*background:\s*var\(--ph-lucent-hover\)/s);
  assert.match(css, /prefers-reduced-motion:\s*reduce/);
  assert.doesNotMatch(css, /#2563EB|#3B82F6|#6542d7/i);
});

test('Arunika Lucent JS keeps the shared palette and responsive sidebar behavior', () => {
  const script = read(paths.js);

  assert.match(script, /let colorMainList\s*=\s*\[/);
  assert.match(script, /const coolGrayThemeColor\s*=\s*'#C7CCD8';/);
  assert.match(script, /function toggleSidebar\(\)/);
  assert.match(script, /lucentSidebarToggle/);
  assert.match(script, /lucentSidebarToggle\.setAttribute\('aria-expanded'/);
  assert.match(script, /function changeMainColor\(color\)/);
  assert.match(script, /function syncSidebarForViewport\(\)/);
});

test('Theme Manager and seed data register Arunika Lucent', () => {
  const controller = read('app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Themes_Controller.php');
  const seeder = read('database/seeders_new/ThemesSeeder.php');
  const migration = read(paths.migration);

  assert.match(controller, /'arunika_lucent'/);
  assert.match(controller, /'display_name'\s*=>\s*'Arunika Lucent'/);
  assert.match(controller, /arunika-lucent-theme-preview\.png/);
  assert.match(seeder, /'theme_code'\s*=>\s*'arunika_lucent'/);
  assert.match(seeder, /'theme_name'\s*=>\s*'Arunika Lucent'/);
  assert.match(seeder, /'theme_foldername'\s*=>\s*'arunika_lucent'/);
  assert.match(migration, /'theme_code'\s*=>\s*'arunika_lucent'/);
  assert.match(migration, /public function down\(\): void/);
});
