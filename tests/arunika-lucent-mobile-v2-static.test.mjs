import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const layout = readFileSync('resources/views/themes/arunika_lucent/cms/cms_layout.blade.php', 'utf8');
const css = readFileSync('public/assets/css/themes/arunika_lucent/mobile-v2.css', 'utf8');
const themeCss = readFileSync('public/assets/css/themes/arunika_lucent/arunika_lucent.css', 'utf8');
const lucentShell = themeCss.slice(themeCss.lastIndexOf('/* ARUNIKA LUCENT'));

const between = (source, start, end) => source.slice(source.indexOf(start), source.indexOf(end, source.indexOf(start)));
const mobileAccountRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-lucent-sidebar-account\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const mobileAvatarRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-lucent-sidebar-account\s+\.ph-sidebar-user-avatar\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const mobileDropdownRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-lucent-sidebar-account\s+\.ph-sidebar-profile-menu\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const mobileContentRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-scrollable-content\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const mobileContentControlsRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-scrollable-content\s+:where\([\s\S]*?\)\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const mobileMenuDividerRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-sidebar\.ph-expanded\s+\.arv7-divider-line-category,[\s\S]*?\.ph-sidebar\.ph-expanded\s+\.arv7-title\s+\.navbar-vertical-divider\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const mobileMenuItemRule = css.match(/body\[data-ph-mobile-theme="arunika_lucent"\]\s+\.ph-sidebar\s+\.list-group-item,[\s\S]*?\.ph-sidebar\.ph-expanded\s+\.list-group-item\s*\{[\s\S]*?\n\s*\}/)?.[0] ?? '';
const svgFor = (source, marker) => source.slice(source.indexOf(marker), source.indexOf('</button>', source.indexOf(marker))).match(/<svg[\s\S]*?<\/svg>/)?.[0].replace(/\s+/g, ' ').trim();

test('Lucent mobile V2 keeps the profile row inside the sidebar and preserves the mobile topbar', () => {
  const sidebar = between(layout, '<div class="ph-sidebar', '<div class="ph-layout-right');
  const topbar = between(layout, '<div class="ph-top-bar"', '<div class="ph-main-panel');

  assert.match(topbar, /ph-mobile-sidebar-trigger[\s\S]*ph-mobile-header-avatar/);
  assert.doesNotMatch(topbar, /ph-mobile-account-bar|ph-mobile-brand|ph-lucent-leaf-mark|ph-header-notification|cmsNotifBell/);
  assert.doesNotMatch(sidebar, /ph-mobile-sidebar-close/);
  assert.doesNotMatch(sidebar, /ph-lucent-sidebar-mobile-brand|ph-lucent-leaf-mark|>\s*Lucent\s*</);
  assert.match(sidebar, /ph-sidebar-user-card[\s\S]*ph-lucent-account-menu-button[\s\S]*ph-lucent-sidebar-toggle/);
  assert.match(topbar, /ph-header-nav-control/);
});

test('Lucent mobile V2 keeps the drawer controls accessible without changing the desktop shell', () => {
  assert.doesNotMatch(css, /ph-mobile-brand|ph-lucent-leaf-mark|ph-header-notification|cmsNotifBell/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-top-bar[\s\S]*flex:\s*0\s+0\s+64px/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-mobile-header-avatar\s*\{[^}]*margin-left:\s*auto/);
  assert.ok(mobileAccountRule);
  assert.doesNotMatch(mobileAccountRule, /\b(?:height|min-height)\s*:/);
  assert.doesNotMatch(mobileAccountRule, /border-bottom\s*:/);
  assert.ok(mobileDropdownRule);
  assert.match(mobileDropdownRule, /position:\s*fixed\s*!important;/);
  assert.match(mobileDropdownRule, /top:\s*100px\s*!important;/);
  assert.match(mobileDropdownRule, /left:\s*14px\s*!important;/);
  assert.match(mobileDropdownRule, /width:\s*calc\(80vw\s*-\s*32px\)\s*!important;/);
  assert.match(mobileDropdownRule, /max-height:\s*calc\(100dvh\s*-\s*116px\);/);
  assert.match(mobileDropdownRule, /border-radius:\s*20px;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar\.ph-expanded \.ph-nav-category\s*\{[\s\S]*?border-top:\s*0\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar\.ph-expanded \.arv7-title\s*\{[\s\S]*?border-top:\s*0\s*!important;/);
  assert.ok(mobileMenuDividerRule);
  assert.match(mobileMenuDividerRule, /display:\s*none\s*!important;[\s\S]*border:\s*0\s*!important;/);
  assert.ok(mobileMenuItemRule);
  assert.match(mobileMenuItemRule, /border:\s*0\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-utilities\s*\{[\s\S]*?border-top:\s*0;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-account\s*\{[\s\S]*?padding:\s*0\s+22px\s+0\s+27px\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-account \.ph-sidebar-user-card[\s\S]*?gap:\s*12px/);
  assert.ok(mobileAvatarRule);
  assert.match(mobileAvatarRule, /width:\s*48px\s*!important;[\s\S]*?height:\s*48px\s*!important;/);
  assert.match(mobileAvatarRule, /display:\s*grid;[\s\S]*?place-items:\s*center;[\s\S]*?border-radius:\s*50%;[\s\S]*?overflow:\s*hidden;[\s\S]*?padding:\s*0;[\s\S]*?border:\s*1px\s+solid\s+var\(--ph-lucent-border\);[\s\S]*?box-shadow:\s*none;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-account \.ph-sidebar-user-avatar img\s*\{[\s\S]*?width:\s*46px\s*!important;[\s\S]*?height:\s*46px\s*!important;[\s\S]*?border-radius:\s*50%;[\s\S]*?border:\s*0\s*!important;/);
  assert.doesNotMatch(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-account-menu-button\s+i[\s\S]*?translateX/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar \.list-group-item-action:hover[\s\S]*?color:\s*var\(--ph-lucent-accent\)\s*!important/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar \.list-group-item\.active[\s\S]*?color:\s*var\(--ph-lucent-accent\)\s*!important/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-sidebar-toggle \.ph-sidebar-toggle-icon\s*\{[\s\S]*?width:\s*20px;[\s\S]*?height:\s*20px;/);
  assert.ok(mobileContentRule);
  assert.match(mobileContentRule, /--ph-lucent-mobile-content-font-size:\s*var\(--ph-mobile-content-font-size\);/);
  assert.match(mobileContentRule, /--ph-lucent-mobile-content-heading-size:\s*var\(--ph-mobile-content-heading-size\);/);
  assert.match(mobileContentRule, /font-size:\s*var\(--ph-lucent-mobile-content-font-size\);/);
  assert.ok(mobileContentControlsRule);
  assert.match(mobileContentControlsRule, /font-size:\s*var\(--ph-lucent-mobile-content-font-size\)\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+:where\(h4,\s*\.h4\)\s*\{[\s\S]*?font-size:\s*var\(--ph-lucent-mobile-content-heading-size\)\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+:where\(\.btn-outline-primary,\s*\.btn-outline-danger,\s*\.ph-btn-theme-outline,\s*\.btn-outline-larapx\)\s*\{[\s\S]*?border:\s*1px\s+solid\s+var\(--bs-btn-border-color\)\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+\.form-check-input\s*\{[\s\S]*?border:\s*1px\s+solid\s+var\(--ph-lucent-accent\)\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+\.form-check-input\s*\{[\s\S]*?appearance:\s*none\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+:where\(\.btn,\s*\.form-check-input\):focus\s*\{[\s\S]*?box-shadow:\s*none\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+:where\(\.btn,\s*\.form-check-input\):focus:not\(:focus-visible\)\s*\{[\s\S]*?outline:\s*none\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+:where\(\.btn,\s*\.form-check-input\):focus-visible\s*\{[\s\S]*?outline:\s*none\s*!important;[\s\S]*?box-shadow:\s*none\s*!important;/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-scrollable-content\s+\.form-check-input:checked:focus-visible\s*\{[\s\S]*?background-color:\s*color-mix\(in srgb, var\(--ph-lucent-accent\), black 12%\)\s*!important;/);
  assert.match(lucentShell, /--ph-lucent-hover:\s*color-mix\(in srgb, var\(--ph-lucent-accent\), white 93%\);[\s\S]*--ph-lucent-active:\s*color-mix\(in srgb, var\(--ph-lucent-accent\), white 88%\);/);
  assert.match(themeCss, /\.ph-theme-arunika-lucent \.ph-sidebar:not\(\.ph-expanded\)\s+~\s+\.ph-layout-right \.ph-mobile-sidebar-trigger \.ph-sidebar-toggle-chevron[\s\S]*?transform:\s*rotate\(180deg\)/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-lucent-account-menu-button/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-sidebar[\s\S]*z-index:\s*1060\s*!important/);
  assert.match(css, /body\[data-ph-mobile-theme="arunika_lucent"\] \.ph-layout-right[\s\S]*z-index:\s*1\s*!important/);
});

test('Lucent mobile trigger reuses the desktop toggle SVG', () => {
  const mobileSvg = svgFor(layout, 'class="ph-mobile-sidebar-trigger"');
  const desktopSvg = svgFor(layout, 'id="sidebar-toggle"');

  assert.doesNotMatch(between(layout, 'class="ph-mobile-sidebar-trigger"', '</button>'), /fa-bars/);
  assert.ok(mobileSvg);
  assert.ok(desktopSvg);
  assert.equal(mobileSvg, desktopSvg);
});

console.log('Arunika Lucent mobile V2 structure contract loaded.');
