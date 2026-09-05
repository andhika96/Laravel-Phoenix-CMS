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
const mobileCss = readFileSync(
  'public/assets/css/themes/arunika_equinox/mobile-v2.css',
  'utf8',
);
const js = readFileSync(
  'public/assets/js/themes/arunika_equinox/arunika_equinox.js',
  'utf8',
);

const panelIcon = /<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24"[^>]*>\s*<rect x="2\.75" y="2\.75" width="18\.5" height="18\.5" rx="4"><\/rect>\s*<path d="M8\.25 3\.25V20\.75"><\/path>\s*<path(?: class="ph-sidebar-toggle-chevron")? d="M16 8\.75L12\.75 12L16 15\.25"><\/path>\s*<\/svg>/s;

function buttonMarkup(className)
{
  return layout.match(new RegExp(`<button[^>]*class="[^"]*${className}[^"]*"[\\s\\S]*?<\\/button>`))?.[0] ?? '';
}

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
  assert.match(buttonMarkup('ph-mobile-sidebar-trigger'), panelIcon);
  assert.match(buttonMarkup('ph-mobile-sidebar-close'), panelIcon);
  assert.match(buttonMarkup('ph-sidebar-toggle'), panelIcon);
});

test('Arunika Equinox mobile drawer reuses the desktop surface and bottom artwork', () => {
  assert.match(
    mobileCss,
    /body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-sidebar,\s*body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-sidebar\.ph-expanded\s*\{[^}]*width:\s*80vw;[^}]*background:\s*linear-gradient\(180deg,\s*var\(--ph-equinox-sidebar-start\) 0%,\s*var\(--ph-equinox-sidebar-end\) 100%\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-equinox \.ph-sidebar::after\s*\{[^}]*background:\s*url\("\.\.\/\.\.\/\.\.\/images\/themes\/arunika_equinox\/arunika-equinox-sidebar-landscape\.png"\) center calc\(100% \+ 24px\) \/ 100% auto no-repeat;/s,
  );
  assert.doesNotMatch(mobileCss, /\.ph-sidebar::after/);
});

test('Arunika Equinox mobile profile and admin footer keep one compact navigation hierarchy', () => {
  assert.match(layout, /class="ph-equinox-mobile-profile-user"[\s\S]*?auth\(\)->user\(\)->fullname[\s\S]*?\$currentUserRole/);
  assert.match(layout, /@if\(checkIsAdmin\(\)\)[\s\S]*?class="ph-equinox-mobile-admin"[\s\S]*?Awesome Admin[\s\S]*?@endif/);
  assert.match(
    mobileCss,
    /body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-header-awesome-admin\s*\{[^}]*display:\s*none\s*!important;/s,
  );
  assert.match(
    mobileCss,
    /body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-equinox-mobile-admin\s*\{[^}]*display:\s*flex;[^}]*position:\s*relative;[^}]*z-index:\s*1;/s,
  );
  assert.match(
    mobileCss,
    /body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-sidebar \.ph-app-logo-text\s*\{[^}]*max-width:\s*calc\(100% - 112px\);/s,
  );
  assert.match(
    mobileCss,
    /body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-header-profile-menu\s*\{[^}]*position:\s*fixed\s*!important;[^}]*right:\s*14px\s*!important;[^}]*width:\s*min\(280px,\s*calc\(100vw - 28px\)\);/s,
  );
});

test('Arunika Equinox mobile dashboard retains the approved two-column metric grid', () => {
  assert.match(
    mobileCss,
    /body\[data-ph-mobile-theme="arunika_equinox"\] \.ph-dashboard-stats > \.row\s*\{[^}]*display:\s*grid;[^}]*grid-template-columns:\s*repeat\(2,\s*minmax\(0,\s*1fr\)\);/s,
  );
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
