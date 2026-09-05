import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const layout = readFileSync('resources/views/themes/arunika_lucent/cms/cms_layout.blade.php', 'utf8');
const mobileCss = readFileSync('public/assets/css/themes/arunika_lucent/mobile-v2.css', 'utf8');
const themeCss = readFileSync('public/assets/css/themes/arunika_lucent/arunika_lucent.css', 'utf8');
const dashboard = readFileSync('resources/views/dashboard/dashboard.blade.php', 'utf8');
const navigationController = readFileSync('public/assets/js/themes/arunika-mobile-navigation-v2.js', 'utf8');

const between = (source, start, end) => source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start)));

test('Lucent mobile shell keeps the sidebar profile row, mobile topbar and logout footer', () => {
  const sidebar = between(layout, '<div class="ph-sidebar', '<div class="ph-layout-right');
  const topbar = between(layout, '<div class="ph-top-bar"', '<div class="ph-main-panel');

  assert.match(topbar, /ph-mobile-sidebar-trigger[\s\S]*ph-mobile-header-avatar/);
  assert.doesNotMatch(topbar, /ph-mobile-account-bar|ph-mobile-brand|ph-lucent-leaf-mark|ph-header-notification|cmsNotifBell/);
  assert.doesNotMatch(sidebar, /ph-mobile-sidebar-close/);
  assert.equal((layout.match(/ph-mobile-sidebar-trigger/g) ?? []).length, 1);
  assert.equal((sidebar.match(/ph-mobile-sidebar-close/g) ?? []).length, 0);
  assert.doesNotMatch(sidebar, /ph-lucent-leaf-mark|>\s*Lucent\s*</);
  assert.doesNotMatch(layout, /ph-mobile-brand-mark[^>]*>\s*L\s*</);
  assert.match(layout, /ph-sidebar-user-meta[\s\S]*\$currentUserRole/);
  assert.match(sidebar, /ph-sidebar-user-card[\s\S]*ph-lucent-account-menu-button[\s\S]*ph-lucent-sidebar-toggle/);
  assert.match(layout, /ph-lucent-sidebar-logout[\s\S]*url\('auth\/logout'\)/);
  assert.match(layout, /menu_versioning\(\)/);
});

test('Lucent mobile overlay owns the sidebar profile row, full drawer and scrim layering', () => {
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-top-bar[\s\S]*display:\s*flex/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-top-bar[\s\S]*flex:\s*0\s+0\s+64px/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-top-bar\s*>\s*\.ph-mobile-header-avatar[\s\S]*display:\s*grid\s*!important/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-mobile-header-avatar\s*\{[^}]*margin-left:\s*auto/);
  assert.doesNotMatch(mobileCss, /ph-mobile-brand|ph-lucent-leaf-mark|ph-header-notification|cmsNotifBell/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar\s*\{[\s\S]*?width:\s*80vw\s*!important;[\s\S]*?min-width:\s*80vw\s*!important;[\s\S]*?max-width:\s*80vw\s*!important;/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar\.ph-expanded\s*\{[\s\S]*?width:\s*80vw\s*!important;/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-mobile-sidebar-backdrop[\s\S]*position:\s*fixed/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-mobile-sidebar-backdrop[\s\S]*z-index:\s*1055/);
  assert.match(mobileCss, /ph-nav-category[\s\S]*display:\s*flex\s*!important/);
  assert.doesNotMatch(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-lucent-sidebar-account\s*\{[\s\S]*?(?:height|min-height):\s*132px/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-account[\s\S]*flex:\s*0\s+0\s+auto[\s\S]*display:\s*grid[\s\S]*grid-template-columns:\s*minmax\(0,\s*1fr\)\s+44px/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-account \.ph-sidebar-profile[\s\S]*display:\s*grid[\s\S]*grid-template-columns:\s*minmax\(0,\s*1fr\)\s+44px/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-account \.ph-sidebar-user-card[\s\S]*grid-template-columns:\s*48px\s+minmax\(0,\s*1fr\)/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-account-menu-button[\s\S]*position:\s*static\s*!important[\s\S]*min-width:\s*44px/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-toggle[\s\S]*min-width:\s*44px[\s\S]*display:\s*inline-flex\s*!important/);
  assert.match(mobileCss, /ph-lucent-account-menu-button[\s\S]*display:\s*inline-flex/);
  assert.match(mobileCss, /ph-lucent-sidebar-utilities[\s\S]*ph-lucent-sidebar-logout/);
});

test('Lucent semantic buttons isolate primary accent from Bootstrap variants', () => {
  assert.doesNotMatch(themeCss, /\.ph-theme-arunika-lucent\s+a\s*,[\s\S]*color:\s*var\(--ph-lucent-accent\)\s*!important/);
  assert.doesNotMatch(themeCss, /\.ph-theme-arunika-lucent\s+\.btn-primary\s*,\s*\.ph-theme-arunika-lucent\s+\.btn-success/);
  assert.doesNotMatch(themeCss, /\.ph-theme-arunika-lucent\s+\.btn-outline-primary\s*,\s*\.ph-theme-arunika-lucent\s+\.btn-outline-success/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent\s+\.btn-primary[\s\S]*--bs-btn-bg:\s*var\(--ph-lucent-accent\)/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent\s+\.btn-outline-primary[\s\S]*--bs-btn-color:\s*var\(--ph-lucent-accent\)/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent\s+\.btn-check:checked\s*\+\s*\.btn-outline-primary[\s\S]*--bs-btn-active-bg:\s*var\(--ph-lucent-accent\)/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent\s+\.btn-outline-danger[\s\S]*--bs-btn-color:\s*var\(--bs-danger/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent\s+\.text-danger[\s\S]*color:\s*var\(--bs-danger/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent[\s\S]*btn-outline-danger[\s\S]*color:\s*inherit/);
});

test('Lucent dashboard exposes mobile-only title and subtitle seams', () => {
  assert.match(dashboard, /ph-dashboard-title/);
  assert.match(dashboard, /ph-dashboard-summary-copy-default/);
  assert.match(dashboard, /ph-dashboard-summary-copy-lucent/);
  assert.match(mobileCss, /ph-dashboard-summary-copy-default[\s\S]*display:\s*none/);
  assert.match(mobileCss, /ph-dashboard-summary-copy-lucent[\s\S]*display:\s*inline/);
});

test('mobile navigation controller exposes the notification bell only when mobile header is active', () => {
  assert.match(navigationController, /ph-header-notification/);
  assert.match(navigationController, /setAttribute\(['"]aria-hidden['"],\s*isMobile\.value\s*\?\s*['"]false['"]\s*:\s*['"]true['"]\)/);
});

console.log('Arunika Lucent mobile semantic contract loaded.');
