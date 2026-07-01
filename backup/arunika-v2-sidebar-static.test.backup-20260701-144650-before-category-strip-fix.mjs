import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const cmsLayout = readFileSync('resources/views/themes/arunika_v2/cms/cms_layout.blade.php', 'utf8');
const menuBlade = readFileSync('resources/views/themes/arunika_v2/components/menu.blade.php', 'utf8');
const css = readFileSync('public/assets/css/themes/arunika_v2/arunika_v2.css', 'utf8');
const js = readFileSync('public/assets/js/themes/arunika_v2/arunika_v2.js', 'utf8');

assert.match(cmsLayout, /class="ph-sidebar-toggle"/, 'sidebar rail toggle button should exist');
assert.match(cmsLayout, /aria-label="Toggle sidebar"/, 'sidebar toggle should be accessible');
assert.match(cmsLayout, /id="sidebar-toggle-icon"/, 'sidebar toggle icon should be addressable from JS');
assert.match(cmsLayout, /ph-static-nav-category/, 'static sidebar links should be grouped under a category like the reference');
assert.match(cmsLayout, /ph-mobile-sidebar-trigger/, 'topbar hamburger should be scoped as a mobile-only fallback');
assert.match(cmsLayout, /class="ph-sidebar-user-card"/, 'sidebar footer should include the profile card from the reference');
assert.match(cmsLayout, /class="ph-sidebar-user-avatar"/, 'sidebar footer should keep a dedicated avatar element');
assert.match(cmsLayout, /class="ph-sidebar-user-meta"/, 'sidebar footer should hide user text separately from the avatar');
assert.match(cmsLayout, /class="ph-sidebar-logout"/, 'sidebar footer should include the logout control from the reference');
assert.match(cmsLayout, /href="\{\{ url\('awesome_admin'\) \}\}" class="list-group-item list-group-item-action\{\{ request\(\)->is\('awesome_admin\*'\) \? ' active' : '' \}\}"/, 'Awesome Admin shortcut should live in the normal menu flow, not the footer');
assert.match(cmsLayout, /ph-sidebar-theme-toggle/, 'sidebar should include the Dark mode row from the reference');
assert.match(cmsLayout, /ph-sidebar-theme-switch/, 'sidebar Dark mode row should include a switch control');

const footerMarkup = cmsLayout.match(/<div class="ph-sidebar-footer">([\s\S]*?)<\/div>\s*<\/div>\s*<\/div>\s*<div class="ph-layout-right"/)?.[1] ?? '';
assert.doesNotMatch(footerMarkup, /ph-list-group-wrapper/, 'sidebar footer should only contain profile/logout like the reference bottom area');

assert.match(menuBlade, /ph-nav-category-label/, 'category label should be separately styleable');
assert.match(menuBlade, /ph-nav-category-initial/, 'category initial should be available for collapsed state');

assert.match(css, /--ph-sidebar-width-collapsed:\s*72px/, 'collapsed sidebar should match the reference icon rail width');
assert.match(css, /--ph-sidebar-width-expanded:\s*206px/, 'expanded sidebar should match the reference full menu width');
assert.match(css, /--ph-theme-primary-subtle:\s*#e7f6f0/, 'active sidebar item should use the same soft green as the reference');
assert.match(css, /\.ph-sidebar\s*\{[\s\S]*?display:\s*flex;[\s\S]*?flex-direction:\s*column;/, 'sidebar should explicitly stack logo, menu, and footer vertically');
assert.match(css, /#sidebar-scroll-content\s*\{[\s\S]*?flex:\s*1 1 auto;[\s\S]*?min-height:\s*0;/, 'sidebar scroll content should take the remaining height');
assert.match(css, /\.ph-sidebar-footer\s*\{[\s\S]*?margin-top:\s*auto;[\s\S]*?flex:\s*0 0 auto;/, 'sidebar footer should stay pinned at the bottom');
assert.match(css, /\.ph-sidebar-theme-switch\s*\{[\s\S]*?border-radius:\s*999px;/, 'Dark mode sidebar row should render as a compact switch');
assert.match(css, /\.ph-sidebar-theme-toggle\.is-dark \.ph-sidebar-theme-switch::after\s*\{[\s\S]*?transform:\s*translateX\(12px\);/, 'Dark mode switch should visibly move when active');
assert.match(css, /\.ph-sidebar-toggle/, 'sidebar toggle should be styled');
assert.match(css, /\.ph-mobile-sidebar-trigger\s*\{[\s\S]*?display:\s*none;/, 'desktop topbar hamburger should not compete with the sidebar reference toggle');
assert.match(css, /@media \(max-width:\s*768px\)[\s\S]*?\.ph-mobile-sidebar-trigger\s*\{[\s\S]*?display:\s*flex;/, 'mobile should keep a fallback sidebar trigger');
assert.match(css, /\.ph-sidebar:not\(\.ph-expanded\) \.ph-nav-category-label/, 'collapsed category should hide full label');
assert.match(css, /\.ph-sidebar\.ph-expanded \.ph-nav-category-initial/, 'expanded category should hide single-letter initial');
assert.match(css, /--ph-sidebar-shadow:\s*none/, 'sidebar should not cast a shadow in the reference layout');
assert.doesNotMatch(css, /box-shadow:\s*18px 0 45px/, 'sidebar should not keep the old floating-card side shadow');
assert.match(css, /\.ph-sidebar:not\(\.ph-expanded\) \.ph-sidebar-user-avatar\s*\{[\s\S]*?display:\s*inline-flex;/, 'collapsed sidebar should keep the profile avatar visible');
assert.match(css, /\.ph-sidebar:not\(\.ph-expanded\) \.ph-sidebar-logout\s*\{[\s\S]*?width:\s*39px;[\s\S]*?height:\s*39px;[\s\S]*?justify-content:\s*center;/, 'collapsed sidebar should center logout as a square icon control');

assert.match(js, /function updateSidebarToggleState/, 'JS should centralize sidebar toggle state');
assert.match(js, /sidebarToggle\.setAttribute\('aria-expanded'/, 'JS should update aria-expanded state');
assert.match(js, /sidebarToggleIcon\.className/, 'JS should update the toggle chevron icon');
assert.match(js, /function syncSidebarActiveLinks/, 'JS should set active menu item from the current URL');
assert.match(js, /link\.classList\.add\('active'\)/, 'current sidebar link should receive active class');
assert.match(js, /function syncThemeControls/, 'JS should synchronize topbar and sidebar theme controls');
assert.match(js, /sidebarThemeToggle\.classList\.toggle\('is-dark'/, 'sidebar Dark mode switch should reflect the active theme');
assert.match(js, /sidebarThemeToggle\.setAttribute\('aria-pressed'/, 'sidebar Dark mode switch should expose pressed state');
