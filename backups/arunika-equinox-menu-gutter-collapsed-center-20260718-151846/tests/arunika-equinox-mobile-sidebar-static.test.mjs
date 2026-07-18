import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const layout = readFileSync(
  'resources/views/themes/arunika_equinox/cms/cms_layout.blade.php',
  'utf8',
);
const css = readFileSync(
  'public/assets/css/themes/arunika_equinox/arunika_equinox.css',
  'utf8',
);
const js = readFileSync(
  'public/assets/js/themes/arunika_equinox/arunika_equinox.js',
  'utf8',
);

test('Arunika Equinox retains the approved accessible mobile drawer controls', () => {
  assert.match(
    layout,
    /class="ph-mobile-sidebar-close"[\s\S]*?onclick="toggleSidebar\(\)"[\s\S]*?Close navigation/,
  );
  assert.match(
    layout,
    /class="ph-mobile-sidebar-trigger"(?:(?!<\/button>)[\s\S])*?<svg class="ph-sidebar-toggle-icon"(?:(?!<\/button>)[\s\S])*?<path class="ph-sidebar-toggle-chevron"/,
  );
  assert.match(layout, /class="ph-header-nav-control"[\s\S]*?id="sidebar-toggle"/);
});

test('Arunika Equinox preserves the Prism resize guard', () => {
  assert.match(js, /const MOBILE_SIDEBAR_BREAKPOINT = 768;/);
  assert.match(js, /function syncSidebarForViewport\(\)/);
  assert.match(js, /window\.addEventListener\('resize', syncSidebarForViewport\)/);
  assert.match(
    js,
    /if \(window\.innerWidth > MOBILE_SIDEBAR_BREAKPOINT\)\s*\{\s*localStorage\.setItem\('sidebar-state', isExpanded \? 'expanded' : 'collapsed'\);\s*notifyLayoutResize\(\);\s*\}/,
  );
});

test('Arunika Equinox V2 preserves the reference shell as an accessible mobile drawer', () => {
  assert.match(
    css,
    /@media \(max-width:\s*768px\)[\s\S]*?\.ph-theme-arunika-equinox\s+\.ph-app-shell\s*\{[^}]*width:\s*100%\s*!important;[^}]*height:\s*100%\s*!important;[^}]*margin:\s*0;[^}]*border-radius:\s*0;/s,
  );
  assert.match(
    css,
    /@media \(max-width:\s*768px\)[\s\S]*?\.ph-theme-arunika-equinox\s+\.ph-sidebar,\s*\.ph-theme-arunika-equinox\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*width:\s*min\(256px,\s*calc\(100vw - 42px\)\);[^}]*height:\s*calc\(100% - 20px\);[^}]*margin:\s*10px;[^}]*background:\s*linear-gradient\(180deg,\s*var\(--ph-equinox-sidebar-start\)[^}]*border-radius:\s*22px;/s,
  );
  assert.match(
    css,
    /@media \(max-width:\s*768px\)[\s\S]*?\.ph-theme-arunika-equinox\s+\.ph-search-container\s*\{[^}]*display:\s*none\s*!important;/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox\s+\.ph-sidebar:not\(\.ph-expanded\)\s+\+\s+\.ph-layout-right\s+\.ph-mobile-sidebar-trigger\s+\.ph-sidebar-toggle-chevron\s*\{[^}]*transform:\s*rotate\(180deg\);/s,
  );
});
