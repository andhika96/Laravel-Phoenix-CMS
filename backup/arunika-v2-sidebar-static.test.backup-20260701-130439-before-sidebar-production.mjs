import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const cmsLayout = readFileSync('resources/views/themes/arunika_v2/cms/cms_layout.blade.php', 'utf8');
const menuBlade = readFileSync('resources/views/themes/arunika_v2/components/menu.blade.php', 'utf8');
const css = readFileSync('public/assets/css/themes/arunika_v2/arunika_v2.css', 'utf8');
const js = readFileSync('public/assets/js/themes/arunika_v2/arunika_v2.js', 'utf8');

assert.match(cmsLayout, /class="ph-sidebar-toggle"/, 'sidebar rail toggle button should exist');
assert.match(cmsLayout, /aria-label="Toggle sidebar"/, 'sidebar toggle should be accessible');
assert.match(cmsLayout, /id="sidebar-toggle-icon"/, 'sidebar toggle icon should be addressable from JS');

assert.match(menuBlade, /ph-nav-category-label/, 'category label should be separately styleable');
assert.match(menuBlade, /ph-nav-category-initial/, 'category initial should be available for collapsed state');

assert.match(css, /--ph-sidebar-width-collapsed:\s*84px/, 'collapsed sidebar should match slim icon rail width');
assert.match(css, /--ph-sidebar-width-expanded:\s*248px/, 'expanded sidebar should match full menu width');
assert.match(css, /\.ph-sidebar-toggle/, 'sidebar toggle should be styled');
assert.match(css, /\.ph-sidebar:not\(\.ph-expanded\) \.ph-nav-category-label/, 'collapsed category should hide full label');
assert.match(css, /\.ph-sidebar\.ph-expanded \.ph-nav-category-initial/, 'expanded category should hide single-letter initial');
assert.match(css, /box-shadow:\s*18px 0 45px/, 'sidebar should use soft vertical rail shadow');

assert.match(js, /function updateSidebarToggleState/, 'JS should centralize sidebar toggle state');
assert.match(js, /sidebarToggle\.setAttribute\('aria-expanded'/, 'JS should update aria-expanded state');
assert.match(js, /sidebarToggleIcon\.className/, 'JS should update the toggle chevron icon');
