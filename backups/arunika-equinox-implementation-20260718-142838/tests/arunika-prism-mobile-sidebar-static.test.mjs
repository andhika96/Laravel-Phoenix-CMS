import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const layout = readFileSync(
	'resources/views/themes/arunika_prism/cms/cms_layout.blade.php',
	'utf8',
);
const css = readFileSync(
	'public/assets/css/themes/arunika_prism/arunika_prism.css',
	'utf8',
);
const js = readFileSync(
	'public/assets/js/themes/arunika_prism/arunika_prism.js',
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
	layout,
	/class="ph-mobile-sidebar-trigger"(?:(?!<\/button>)[\s\S])*?<svg class="ph-sidebar-toggle-icon"(?:(?!<\/button>)[\s\S])*?<rect x="2\.75"(?:(?!<\/button>)[\s\S])*?<path d="M8\.25 3\.25V20\.75"(?:(?!<\/button>)[\s\S])*?<path class="ph-sidebar-toggle-chevron"/,
	'mobile navigation should reuse the desktop panel SVG',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-sidebar,\s*\.ph-theme-arunika-prism \.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*#ffffff;[^}]*backdrop-filter:\s*none;/s,
	'light mobile drawer should use a solid white surface without backdrop blur',
);
assert.match(
	css,
	/\[data-bs-theme=dark\] \.ph-theme-arunika-prism \.ph-sidebar,\s*\[data-bs-theme=dark\] \.ph-theme-arunika-prism \.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*#202120;/s,
	'dark mobile drawer should use an opaque Prism surface',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-sidebar\.ph-expanded \.ph-mobile-sidebar-close\s*\{[^}]*display:\s*inline-flex;/s,
	'mobile close control should become visible only inside an expanded Prism drawer',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-mobile-sidebar-close\s*\{[^}]*color:\s*var\(--ph-text-main\);/s,
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
	js,
	/if \(window\.innerWidth > MOBILE_SIDEBAR_BREAKPOINT\)\s*\{\s*localStorage\.setItem\('sidebar-state', isExpanded \? 'expanded' : 'collapsed'\);\s*notifyLayoutResize\(\);\s*\}/,
	'mobile Prism drawer toggles should not broadcast a synthetic layout resize',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-theme-arunika-prism \.ph-layout-right\s*\{[^}]*margin:\s*0;/s,
	'mobile Prism layout should remove the desktop outer margin',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-theme-arunika-prism \.ph-top-bar\s*\{[^}]*padding:\s*0 14px;/s,
	'mobile Prism header should use modest 14px horizontal padding',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-mobile-sidebar-trigger\s*\{[^}]*width:\s*36px;[^}]*height:\s*36px;/s,
	'mobile Prism navigation button should use a balanced 36px control',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-mobile-sidebar-trigger\s*\{[^}]*padding:\s*0;[^}]*border:\s*0;[^}]*border-radius:\s*0;[^}]*background:\s*transparent;[^}]*box-shadow:\s*none;/s,
	'mobile Prism navigation should expose only the icon without a button shell',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-sidebar:not\(\.ph-expanded\) \+ \.ph-layout-right \.ph-mobile-sidebar-trigger \.ph-sidebar-toggle-chevron\s*\{[^}]*transform:\s*rotate\(180deg\);/s,
	'closed mobile Prism navigation should show the desktop panel chevron as an arrow right',
);
assert.match(
	css,
	/\.ph-theme-arunika-prism \.ph-search-container\s*\{[^}]*display:\s*none\s*!important;/s,
	'Prism search should stay hidden at every viewport',
);

console.log('Arunika Prism mobile sidebar regression passed.');
