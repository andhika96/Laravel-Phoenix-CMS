import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const layout = readFileSync(
	'resources/views/themes/arunika_aurora/cms/cms_layout.blade.php',
	'utf8',
);
const mobileCss = readFileSync(
	'public/assets/css/themes/arunika_aurora/mobile-v2.css',
	'utf8',
);
const js = readFileSync(
	'public/assets/js/themes/arunika_aurora/arunika_aurora.js',
	'utf8',
);
const waveAsset = readFileSync(
	'public/assets/images/themes/arunika_aurora/arunika-aurora-sidebar-wave.png',
);
const mobileMediaIndex = mobileCss.indexOf('@media (max-width: 768px)');
const preMobileCss = mobileCss.slice(0, mobileMediaIndex);

const mobileTrigger = layout.match(/class="ph-mobile-sidebar-trigger"[\s\S]*?<\/button>/)?.[0] ?? '';
const mobileClose = layout.match(/class="ph-mobile-sidebar-close"[\s\S]*?<\/button>/)?.[0] ?? '';

assert.match(
	mobileTrigger,
	/<svg class="ph-sidebar-toggle-icon"[\s\S]*?<rect x="2\.75"[\s\S]*?<path d="M8\.25 3\.25V20\.75"[\s\S]*?<path class="ph-sidebar-toggle-chevron" d="M16 8\.75L12\.75 12L16 15\.25"><\/path>/,
	'Aurora mobile trigger must reuse the CMS desktop panel SVG',
);
assert.doesNotMatch(mobileTrigger, /fa-bars|fa-bars-staggered/, 'Aurora mobile must not fall back to a hamburger icon');
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-mobile-sidebar-trigger\s*\{[^}]*padding:\s*0;[^}]*border:\s*0;[^}]*background:\s*transparent;[^}]*box-shadow:\s*none;/s,
	'Aurora mobile sidebar trigger should expose only the CMS icon without a button shell',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar:not\(\.ph-expanded\)\s*~\s*\.ph-layout-right \.ph-mobile-sidebar-trigger \.ph-sidebar-toggle-chevron\s*\{[^}]*transform:\s*rotate\(180deg\);/s,
	'closed Aurora mobile trigger should point right even when the navigation backdrop sits between siblings',
);
assert.match(
	mobileClose,
	/<svg class="ph-sidebar-toggle-icon"[\s\S]*?ph-sidebar-toggle-chevron/,
	'Aurora drawer close control must use the CMS panel icon',
);
assert.doesNotMatch(mobileClose, /fa-times/, 'the X beside the site name should be removed');

assert.match(layout, /class="[^"]*ph-aurora-mobile-profile[^"]*"/);
assert.match(layout, /id="ph-aurora-mobile-theme-colors"/);
assert.equal(
	(layout.match(/data-ph-theme-color-picker/g) ?? []).length,
	2,
	'Aurora should expose one palette mount for desktop and one for the mobile profile sheet',
);
assert.match(
	layout,
	/@if\(checkIsAdmin\(\)\)[\s\S]*?class="ph-aurora-sidebar-admin"[\s\S]*?awesome_admin[\s\S]*?@endif/,
	'Aurora mobile drawer should provide the guarded Awesome Admin footer action',
);

assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar,\s*body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar\.ph-expanded\s*\{[^}]*width:\s*80vw;[^}]*min-width:\s*80vw;[^}]*max-width:\s*80vw;[^}]*background:\s*var\(--ph-sidebar-surface\);/s,
	'Aurora mobile drawer should use 80vw and the dynamic CMS sidebar surface',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar::after\s*\{[^}]*background:[^;]*var\(--ph-theme-surface-tint\)/s,
	'Aurora drawer should keep a theme-color-driven lower aurora decoration',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar::after\s*\{[^}]*arunika-aurora-sidebar-wave\.png/s,
	'Aurora sidebar should use the approved Aurora wave background asset rather than a generic gradient',
);
assert.match(
	mobileCss,
	/background-blend-mode:\s*normal;/s,
	'Aurora wave asset should be tinted through the active theme surface token',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar::after\s*\{[^}]*height:\s*42%;/s,
	'Aurora wave layer should cover the preview-matched lower sidebar area',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar::after\s*\{[^}]*background:[^;]*\/\s*100%\s*100%\s*no-repeat;/s,
	'Aurora wave image should fill its lower layer so its fade follows the image boundary',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar::after\s*\{[^}]*background-color:\s*transparent;/s,
	'Aurora wave layer should not create a separate rectangular surface above the image',
);
assert.match(
	mobileCss,
	/mask-image:\s*linear-gradient\(to bottom, transparent 0%, black 18%, black 100%\);/s,
	'Aurora wave layer should fade into the sidebar surface without a hard horizontal edge',
);
assert.equal(waveAsset.readUInt32BE(16), 642, 'Aurora wave asset should preserve the preview sidebar crop width');
assert.equal(waveAsset.readUInt32BE(20), 650, 'Aurora wave asset should stop before the preview footer card');
assert.ok(
	mobileCss.indexOf('body[data-ph-mobile-theme="arunika_aurora"] .ph-sidebar::after') < mobileCss.indexOf('@media (max-width: 768px)'),
	'Aurora wave decoration should be available to desktop and mobile sidebar surfaces',
);
assert.doesNotMatch(
	preMobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar-toggle\s*\{[^}]*display:\s*none !important;/s,
	'Aurora desktop sidebar toggle must remain available',
);
assert.match(
	mobileCss,
	/@media \(min-width:\s*769px\)[\s\S]*?body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-mobile-sidebar-close\s*\{[^}]*display:\s*none !important;/s,
	'Aurora desktop should hide only the mobile close control beside the site name',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-aurora-mobile-profile-menu\s*\{[^}]*position:\s*fixed !important;[^}]*top:\s*72px !important;[^}]*right:\s*14px !important;/s,
	'Aurora profile sheet should stay anchored to the topbar',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-aurora-mobile-profile-menu::before,\s*body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-aurora-mobile-profile-menu::after\s*\{[^}]*left:\s*auto !important;[^}]*right:\s*14px !important;/s,
	'Aurora profile pointer should align with the avatar rather than the menu left edge',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-dashboard-stats > \.row\s*\{[^}]*display:\s*grid;[^}]*grid-template-columns:\s*repeat\(2,\s*minmax\(0,\s*1fr\)\);/s,
	'Aurora mobile dashboard should use the approved 2x2 card grid',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-dashboard-stat-icon\s*\{[^}]*display:\s*grid;[^}]*place-items:\s*center;/s,
	'Aurora mobile dashboard should keep visible stat icons',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar-user-panel,\s*body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-sidebar-logout\s*\{[^}]*display:\s*none !important;/s,
	'Aurora mobile drawer should hide the desktop profile and logout footer rows',
);
assert.match(
	mobileCss,
	/body\[data-ph-mobile-theme="arunika_aurora"\] \.ph-aurora-sidebar-admin\s*\{[^}]*display:\s*flex;/s,
	'Aurora mobile drawer should show only the Awesome Admin footer button',
);
assert.match(js, /document\.querySelectorAll\('\[data-ph-theme-color-picker\]'\)/);
assert.match(js, /document\.querySelectorAll\('\.ph-theme-toggle'\)/);

console.log('Arunika Aurora mobile design regression passed.');
