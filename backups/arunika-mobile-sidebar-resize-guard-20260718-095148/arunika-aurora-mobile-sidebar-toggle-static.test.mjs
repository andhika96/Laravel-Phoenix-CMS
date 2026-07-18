import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const layout = readFileSync(
    'resources/views/themes/arunika_aurora/cms/cms_layout.blade.php',
    'utf8',
);
const css = readFileSync(
    'public/assets/css/themes/arunika_aurora/arunika_aurora.css',
    'utf8',
);

assert.match(
    layout,
    /id="sidebar-toggle"[\s\S]*?<svg class="ph-sidebar-toggle-icon"[\s\S]*?ph-sidebar-toggle-chevron/,
    'the sidebar toggle should keep the approved desktop SVG icon',
);

assert.match(
    layout,
    /class="ph-mobile-sidebar-trigger"(?:(?!<\/button>)[\s\S])*?<svg class="ph-sidebar-toggle-icon"(?:(?!<\/button>)[\s\S])*?<rect x="2\.75"(?:(?!<\/button>)[\s\S])*?<path d="M8\.25 3\.25V20\.75"(?:(?!<\/button>)[\s\S])*?<path class="ph-sidebar-toggle-chevron"/,
    'the mobile sidebar trigger should reuse the approved panel SVG icon',
);
assert.match(
    css,
    /\.ph-mobile-sidebar-trigger\s*\{[^}]*width:\s*36px;[^}]*height:\s*36px;[^}]*padding:\s*0;[^}]*border:\s*0;[^}]*border-radius:\s*0;[^}]*background:\s*transparent;[^}]*box-shadow:\s*none;/s,
    'the mobile sidebar trigger should expose only its icon without a visual button shell',
);
assert.match(
    css,
    /\.ph-sidebar:not\(\.ph-expanded\) \+ \.ph-layout-right \.ph-mobile-sidebar-trigger \.ph-sidebar-toggle-chevron\s*\{[^}]*transform:\s*rotate\(180deg\);/s,
    'the closed mobile trigger should show the desktop panel chevron as an arrow right',
);

const mobileMediaQueries = [...css.matchAll(/@media \(max-width: 768px\)\s*\{([\s\S]*?)(?=\n\})\n\}/g)];
assert.ok(mobileMediaQueries.length > 0, 'the mobile sidebar media query should exist');

const finalMobileRules = mobileMediaQueries.at(-1)[1];

assert.match(
    finalMobileRules,
    /\.ph-sidebar-toggle\s*\{[^}]*display:\s*none;/s,
    'the sidebar toggle should stay hidden while the mobile sidebar is closed',
);
assert.match(
    finalMobileRules,
    /\.ph-sidebar\.ph-expanded \.ph-sidebar-toggle\s*\{[^}]*display:\s*inline-flex;/s,
    'the desktop SVG toggle should become visible when the mobile sidebar is open',
);

console.log('Arunika Aurora mobile sidebar close-toggle regression passed.');
