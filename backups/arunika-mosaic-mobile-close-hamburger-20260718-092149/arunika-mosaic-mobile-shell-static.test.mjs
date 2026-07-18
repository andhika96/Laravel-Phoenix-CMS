import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const layout = readFileSync(
	'resources/views/themes/arunika_mosaic/cms/cms_layout.blade.php',
	'utf8',
);
const css = readFileSync(
	'public/assets/css/themes/arunika_mosaic/arunika_mosaic.css',
	'utf8',
);
const js = readFileSync(
	'public/assets/js/themes/arunika_mosaic/arunika_mosaic.js',
	'utf8',
);

assert.match(
	layout,
	/window\.innerWidth > 768[\s\S]*?sidebar-state/,
	'saved desktop sidebar state must not auto-open the mobile drawer',
);
assert.match(
	layout,
	/<button class="ph-header-btn ph-mobile-sidebar-trigger"[\s\S]*?id="sidebar-toggle"[\s\S]*?aria-expanded="false"/,
	'the header navigation trigger should be an accessible button',
);
assert.match(
	layout,
	/class="ph-mobile-sidebar-backdrop"[\s\S]*?onclick="toggleSidebar\(\)"/,
	'the mobile drawer should provide a dismissible backdrop',
);
assert.match(
	layout,
	/class="ph-mobile-sidebar-close"(?:(?!<\/button>)[\s\S])*?event\.stopPropagation\(\); toggleSidebar\(\)(?:(?!<\/button>)[\s\S])*?<svg class="ph-mobile-sidebar-close-icon"(?:(?!<\/button>)[\s\S])*?<path class="ph-mobile-sidebar-close-chevron"/,
	'the open drawer should provide a dedicated panel-style close control in its brand header',
);

assert.match(js, /const MOBILE_SIDEBAR_BREAKPOINT = 768;/);
assert.match(
	js,
	/if \(! isMobileSidebarViewport\(\)\)[\s\S]*?localStorage\.setItem\('sidebar-state'/,
	'mobile drawer toggles must preserve the saved desktop preference',
);
assert.match(js, /function syncSidebarForViewport\(\)/);
assert.match(js, /window\.addEventListener\('resize', syncSidebarForViewport\)/);
assert.match(
	js,
	/document\.body\.classList\.toggle\('ph-mobile-sidebar-open'/,
	'the document should expose mobile drawer state for scroll locking',
);

assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-sidebar\s*\{[^}]*position:\s*fixed;[^}]*width:\s*min\(var\(--ph-sidebar-width-expanded\), calc\(100vw - 42px\)\);[^}]*transform:\s*translateX\(-105%\);/s,
	'the mobile sidebar should become an off-canvas drawer',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-sidebar\.ph-expanded\s*\{[^}]*transform:\s*translateX\(0\);/s,
	'the expanded mobile drawer should slide into view',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-layout-right\s*\{[^}]*width:\s*100%;[^}]*padding:\s*0 10px 10px;/s,
	'the mobile content shell should use the full viewport width',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-scrollable-content\s*\{[^}]*padding:\s*14px 12px 24px;[^}]*overflow-x:\s*hidden;/s,
	'mobile content should use compact gutters without horizontal overflow',
);
assert.match(
	css,
	/\.ph-sidebar\.ph-expanded \+ \.ph-mobile-sidebar-backdrop\s*\{[^}]*opacity:\s*1;[^}]*pointer-events:\s*auto;/s,
	'the backdrop should become interactive only while the drawer is open',
);
assert.match(
	css,
	/\.ph-mobile-sidebar-close\s*\{[^}]*display:\s*none;[^}]*width:\s*36px;[^}]*height:\s*36px;/s,
	'the drawer close control should use a hidden-by-default 36px touch target',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-sidebar\.ph-expanded \.ph-mobile-sidebar-close\s*\{[^}]*display:\s*inline-flex;/s,
	'the close control should appear only in an expanded mobile drawer',
);
assert.match(
	css,
	/@media \(max-width: 768px\)[\s\S]*?\.ph-sidebar-logo-container\s*\{[^}]*padding-right:\s*50px;[^}]*position:\s*relative;/s,
	'the mobile brand header should reserve space for the close control',
);
assert.match(
	css,
	/\.ph-mobile-sidebar-close-icon\s*\{[^}]*width:\s*18px;[^}]*height:\s*18px;/s,
	'the panel-style close glyph should remain visually balanced inside its touch target',
);

console.log('Arunika Mosaic mobile shell regression passed.');
