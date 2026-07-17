import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const css = readFileSync(
    'public/assets/css/themes/arunika_aurora/arunika_aurora.css',
    'utf8',
);

const menuGroupRules = [...css.matchAll(
    /\.ph-list-group-wrapper,\s*\.ph-sidebar\.ph-expanded \.ph-list-group-wrapper\s*\{([^}]*)\}/g,
)];

assert.ok(menuGroupRules.length > 0, 'the shared sidebar menu-group rule should exist');
assert.match(
    menuGroupRules.at(-1)[1],
    /margin:\s*0 0 15px;/,
    'the final sidebar menu-group spacing should use a 15px bottom margin',
);

console.log('Arunika Aurora menu-group spacing regression passed.');
