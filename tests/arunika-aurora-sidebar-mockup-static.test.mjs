import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';

const mockupPath = 'public/mockups/arunika-aurora-sidebar-interactive-mockup.html';

assert.equal(existsSync(mockupPath), true, 'interactive sidebar mockup should exist');

const html = readFileSync(mockupPath, 'utf8');

assert.match(html, /id="appShell"/, 'mockup should expose a single app shell state root');
assert.match(html, /id="sidebarToggle"/, 'mockup should include the sidebar toggle control');
assert.match(html, /\.av2-shell\.is-collapsed/, 'mockup should implement a collapsed sidebar state');
assert.match(html, /data-short="M"/, 'collapsed category initial should be available');
assert.match(html, /data-label="Dashboard"/, 'menu items should drive title state');
assert.match(html, /id="salesChart"/, 'dashboard chart area should be present');
assert.match(html, /id="densityToggle"/, 'mockup should include a density control for review');
assert.match(html, /localStorage\.setItem\('arunika-aurora-sidebar-state'/, 'sidebar state should persist in localStorage');
assert.match(html, /@font-face[\s\S]*nunito-v32-cyrillic_cyrillic-ext_latin_latin-ext_vietnamese-regular\.woff2/, 'mockup should load the local Nunito font');
assert.match(html, /-webkit-font-smoothing:\s*antialiased/, 'mockup should smooth font rendering');
assert.match(html, /--av2-sidebar-shadow:\s*none;/, 'sidebar should not cast a shadow in the reference layout');
assert.match(html, /\.av2-sidebar \{[\s\S]*?box-shadow:\s*none;/, 'sidebar surface should remain flat without a side shadow');
assert.match(html, /\.av2-menu-btn \{[\s\S]*?font-weight:\s*500;/, 'menu labels should use a lighter default weight');
assert.match(html, /\.av2-menu-btn\.active \{[\s\S]*?font-weight:\s*700;/, 'active menu should be semibold, not extra bold');
assert.doesNotMatch(html, /\.av2-shell\.is-collapsed \.av2-user-card div/, 'collapsed state must not hide every user-card div because the avatar is also a div');
assert.match(html, /class="av2-user-meta"/, 'user text should have a dedicated wrapper that can be hidden without hiding the avatar');
assert.match(html, /\.av2-shell\.is-collapsed \.av2-user-avatar \{[\s\S]*?display:\s*inline-flex;/, 'collapsed state should explicitly keep the profile avatar visible');
assert.match(html, /\.av2-shell\.is-collapsed \.av2-logout \{[\s\S]*?width:\s*39px;[\s\S]*?height:\s*39px;[\s\S]*?justify-content:\s*center;/, 'collapsed logout should align as a centered square icon control');
