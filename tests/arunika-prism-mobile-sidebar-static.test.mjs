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
const mobileCss = readFileSync(
	'public/assets/css/themes/arunika_prism/mobile-v2.css',
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
	/class="ph-mobile-sidebar-trigger"(?:(?!<\/button>)[\s\S])*?<svg class="ph-sidebar-toggle-icon"(?:(?!<\/button>)[\s\S])*?<rect x="2\.75"(?:(?!<\/button>)[\s\S])*?<path d="M8\.25 3\.25V20\.75"(?:(?!<\/button>)[\s\S])*?<path class="ph-sidebar-toggle-chevron" d="M16 8\.75L12\.75 12L16 15\.25"/,
	'mobile navigation should reuse the exact desktop Prism panel SVG',
);
assert.doesNotMatch(layout, /ph-prism-mobile-menu-icon|M4 7h16M4 12h16M4 17h16/, 'mobile Prism must not fall back to a hamburger icon');
assert.match(
	layout,
	/class="[^\"]*ph-prism-mobile-profile[^\"]*"[\s\S]*?class="ph-header-profile-toggle"[\s\S]*?ph-header-profile-avatar[\s\S]*?class="dropdown-menu ph-header-profile-menu ph-prism-mobile-profile-menu"/s,
	'mobile Prism should expose the approved profile sheet from the topbar',
);
assert.match(
	layout,
	/@if\(checkIsAdmin\(\)\)[\s\S]*?class="ph-prism-sidebar-admin"[\s\S]*?awesome_admin[\s\S]*?@endif/s,
	'mobile Prism drawer should provide the guarded Awesome Admin footer action',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar,\s*body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar\.ph-expanded\s*\{[^}]*width:\s*80vw;[^}]*min-width:\s*80vw;[^}]*max-width:\s*80vw;[^}]*background:\s*var\(--ph-prism-sidebar-surface\);/s,
	'mobile Prism drawer should occupy 80vw and inherit the desktop Prism surface',
);
assert.doesNotMatch(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\][\s\S]*?background:\s*#ffffff;/s,
	'mobile Prism should not hardcode a white drawer surface over the dynamic theme token',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar\.ph-expanded \.ph-mobile-sidebar-close\s*\{[^}]*display:\s*inline-flex;/s,
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
	mobileCss,
	/@media \(max-width: 768px\)[\s\S]*?body\[data-ph-mobile-theme="arunika_prism"\] \.ph-layout-right\s*\{[^}]*margin:\s*0;/s,
	'mobile Prism layout should remove the desktop outer margin',
);
assert.match(
	mobileCss,
	/@media \(max-width: 768px\)[\s\S]*?body\[data-ph-mobile-theme="arunika_prism"\] \.ph-top-bar\s*\{[^}]*padding:\s*0 14px;/s,
	'mobile Prism header should use modest 14px horizontal padding',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-mobile-sidebar-trigger\s*\{[^}]*width:\s*44px;[^}]*height:\s*44px;[^}]*flex:\s*0 0 44px;/s,
	'mobile Prism navigation button should keep a 44px accessible touch target',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-mobile-sidebar-trigger\s*\{[^}]*padding:\s*0;[^}]*border:\s*0;[^}]*background:\s*transparent;[^}]*box-shadow:\s*none;/s,
	'mobile Prism trigger should expose only the desktop panel icon without a button shell',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar:not\(\.ph-expanded\)\s*~\s*\.ph-layout-right \.ph-mobile-sidebar-trigger \.ph-sidebar-toggle-chevron\s*\{[^}]*transform:\s*rotate\(180deg\);/s,
	'closed mobile Prism panel chevron should point right even with a backdrop sibling',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar::before\s*\{[^}]*width:\s*3px;[^}]*background:\s*linear-gradient\(180deg,/s,
	'Prism drawer should expose the narrow theme-driven editorial rail',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar\.ph-expanded \.list-group-item\.active::before\s*\{[^}]*width:\s*3px;[^}]*background:\s*var\(--ph-theme-primary\);/s,
	'active Prism menu should use the editorial rail indicator',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-dashboard-title::before\s*\{[^}]*width:\s*3px;[^}]*background:\s*linear-gradient\(180deg,/s,
	'Prism mobile heading should use the editorial spectral marker',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-mobile-profile\s*\{[^}]*display:\s*block;/s,
	'mobile Prism should show the profile control only at the mobile breakpoint',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-mobile-profile-menu\s*\{[^}]*position:\s*fixed !important;[^}]*top:\s*72px !important;[^}]*right:\s*14px !important;/s,
	'mobile Prism profile sheet should anchor to the topbar without viewport overflow',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-mobile-profile-menu\s*\{[^}]*padding:\s*6px;[^}]*border:\s*1px solid var\(--ph-prism-border\);[^}]*border-radius:\s*14px;[^}]*background:\s*var\(--ph-bg-popover\);/s,
	'Prism profile sheet should use the approved compact editorial surface',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-mobile-profile-menu::before,\s*body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-mobile-profile-menu::after\s*\{[^}]*left:\s*auto !important;[^}]*right:\s*14px !important;/s,
	'mobile Prism profile sheet pointer should align with the topbar avatar',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-sidebar-user-panel\s*\{[^}]*display:\s*none !important;/s,
	'mobile Prism drawer should not duplicate the desktop profile card',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-sidebar-admin\s*\{[^}]*display:\s*flex;/s,
	'mobile Prism drawer should show the admin footer button',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-prism-sidebar-admin\s*\{\s*display:\s*none;/s,
	'desktop Prism should keep the mobile-only admin footer action hidden',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-dashboard-stats > \.row\s*\{[^}]*display:\s*grid;[^}]*grid-template-columns:\s*repeat\(2,\s*minmax\(0,\s*1fr\)\);/s,
	'mobile Prism dashboard should use the approved 2x2 stat grid',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-dashboard-stat-icon\s*\{[^}]*display:\s*grid;[^}]*place-items:\s*center;/s,
	'mobile Prism dashboard should retain the stat icon hierarchy',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_prism"\] \.ph-top-bar > \.ph-search-container[\s\S]*?display:\s*none\s*!important;/s,
	'Prism search should stay hidden at every viewport',
);

assert.match(
	js,
	/document\.querySelectorAll\('\[data-ph-theme-color-picker\]'\)/,
	'Prism should render the same theme palette into both profile menus',
);
assert.match(
	js,
	/document\.querySelectorAll\('\.ph-theme-toggle'\)/,
	'Prism should synchronize all visible dark-mode controls',
);

console.log('Arunika Prism mobile sidebar regression passed.');
