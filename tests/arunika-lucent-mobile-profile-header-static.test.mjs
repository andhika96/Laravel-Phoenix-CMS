import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const layout = readFileSync('resources/views/themes/arunika_lucent/cms/cms_layout.blade.php', 'utf8');
const mobileCss = readFileSync('public/assets/css/themes/arunika_lucent/mobile-v2.css', 'utf8');
const navigationController = readFileSync('public/assets/js/themes/arunika-mobile-navigation-v2.js', 'utf8');

const between = (source, start, end) => source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start)));

test('Lucent mobile sidebar profile area matches the reference row', () => {
  const sidebar = between(layout, '<div class="ph-sidebar', '<div class="ph-layout-right');
  const topbar = between(layout, '<div class="ph-top-bar"', '<div class="ph-main-panel');

  assert.match(sidebar, /ph-lucent-sidebar-account[\s\S]*ph-sidebar-user-card[\s\S]*ph-lucent-account-menu-button[\s\S]*ph-lucent-sidebar-toggle/);
  assert.doesNotMatch(sidebar, /ph-mobile-sidebar-close/);
  assert.match(topbar, /ph-mobile-sidebar-trigger[\s\S]*ph-mobile-header-avatar/);
  assert.match(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\]\s*\{[\s\S]*overflow:\s*hidden/);
  assert.match(mobileCss, /ph-sidebar[\s\S]*inset:\s*0\s+auto\s+0\s+0\s*!important[\s\S]*width:\s*80vw/);
  assert.doesNotMatch(mobileCss, /body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-lucent-sidebar-account\s*\{[\s\S]*?(?:height|min-height):\s*132px/);
  assert.match(mobileCss, /ph-sidebar-user-card[\s\S]*grid-template-columns:\s*48px\s+minmax\(0,\s*1fr\)/);
  assert.match(mobileCss, /ph-lucent-sidebar-toggle[\s\S]*min-width:\s*44px[\s\S]*display:\s*inline-flex\s*!important/);
  assert.match(navigationController, /const isLucentTheme = document\.body\.dataset\.phMobileTheme\s*===\s*['"]arunika_lucent['"]/);
  assert.match(navigationController, /closeControl\s*=\s*isLucentTheme\s*\?/);
});
