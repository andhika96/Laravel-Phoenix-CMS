import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const css = readFileSync(
    'public/assets/css/themes/arunika_aurora/arunika_aurora.css',
    'utf8',
);

assert.match(
    css,
    /\[data-bs-theme=light\]\s*\{[^}]*--ph-sidebar-category-border:\s*#d8d3dc;/s,
    'the light sidebar should expose a clearer category-divider color',
);
assert.match(
    css,
    /\.ph-sidebar\.ph-expanded \.ph-nav-category\s*\{[^}]*border-top:\s*1px solid var\(--ph-sidebar-category-border\);/s,
    'expanded category names should use the dedicated higher-contrast divider token',
);

console.log('Arunika Aurora category-border contrast regression passed.');
