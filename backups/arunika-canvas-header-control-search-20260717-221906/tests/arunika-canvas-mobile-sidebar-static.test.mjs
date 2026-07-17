import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const layout = readFileSync(
	'resources/views/themes/arunika_canvas/cms/cms_layout.blade.php',
	'utf8',
);
const css = readFileSync(
	'public/assets/css/themes/arunika_canvas/arunika_canvas.css',
	'utf8',
);
const js = readFileSync(
	'public/assets/js/themes/arunika_canvas/arunika_canvas.js',
	'utf8',
);

assert.match(
	layout,
	/class="ph-mobile-sidebar-close"[\s\S]*?onclick="toggleSidebar\(\)"[\s\S]*?Close navigation/,
	'mobile drawer should provide an accessible in-drawer close control',
);
assert.match(
	layout,
	/class="ph-header-nav-control"[\s\S]*?id="sidebar-toggle"/,
	'desktop collapse control should remain in the approved header location',
);
assert.match(
	css,
	/\.ph-theme-arunika-canvas \.ph-sidebar,\s*\.ph-theme-arunika-canvas \.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*#ffffff;[^}]*backdrop-filter:\s*none;/s,
	'light mobile drawer should use a solid white surface without backdrop blur',
);
assert.match(
	css,
	/\[data-bs-theme=dark\] \.ph-theme-arunika-canvas \.ph-sidebar,\s*\[data-bs-theme=dark\] \.ph-theme-arunika-canvas \.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*#202120;/s,
	'dark mobile drawer should use an opaque Canvas surface',
);
assert.match(
	css,
	/\.ph-theme-arunika-canvas \.ph-sidebar\.ph-expanded \.ph-mobile-sidebar-close\s*\{[^}]*display:\s*inline-flex;/s,
	'mobile close control should become visible only inside an expanded Canvas drawer',
);
assert.match(
	css,
	/\.ph-theme-arunika-canvas \.ph-mobile-sidebar-close\s*\{[^}]*color:\s*var\(--ph-text-main\);/s,
	'mobile close control should use an existing light/dark text token',
);
assert.match(js, /const MOBILE_SIDEBAR_BREAKPOINT = 768;/);
assert.match(js, /function syncSidebarForViewport\(\)/);
assert.match(
	js,
	/function syncSidebarForViewport\(\)[\s\S]*?sidebar\.classList\.remove\('ph-expanded'\)/,
	'crossing into mobile should close the expanded desktop sidebar',
);
assert.match(js, /window\.addEventListener\('resize', syncSidebarForViewport\)/);
assert.match(
	js,
	/if \(window\.innerWidth > MOBILE_SIDEBAR_BREAKPOINT\)[\s\S]*?localStorage\.setItem\('sidebar-state'/,
	'mobile drawer toggles should not overwrite the saved desktop preference',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-theme-arunika-canvas \.ph-layout-right\s*\{[^}]*margin:\s*0;/s,
	'mobile Canvas layout should remove the desktop outer margin',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-theme-arunika-canvas \.ph-top-bar\s*\{[^}]*padding:\s*0 14px;/s,
	'mobile Canvas header should use modest 14px horizontal padding',
);

console.log('Arunika Canvas mobile sidebar regression passed.');
