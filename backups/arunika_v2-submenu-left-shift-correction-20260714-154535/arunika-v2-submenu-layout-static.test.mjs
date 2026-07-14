import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const css = readFileSync('public/assets/css/themes/arunika_v2/arunika_v2.css', 'utf8');

const submenuContainerRules = [...css.matchAll(/\.ph-submenu-container\s*\{([^}]*)\}/g)];
const submenuLinkRules = [...css.matchAll(/\.ph-submenu-link\s*\{([^}]*)\}/g)];
const submenuLabelRules = [...css.matchAll(/\.ph-submenu-link span\s*\{([^}]*)\}/g)];

assert.ok(submenuContainerRules.length > 0, 'submenu container CSS should exist');
assert.ok(submenuLinkRules.length > 0, 'submenu link CSS should exist');
assert.ok(submenuLabelRules.length > 0, 'submenu label CSS should exist');

const finalContainerRule = submenuContainerRules.at(-1)[1];
const finalLinkRule = submenuLinkRules.at(-1)[1];
const finalLabelRule = submenuLabelRules.at(-1)[1];

assert.match(
  finalContainerRule,
  /margin:\s*0 16px 8px 20px;/,
  'expanded submenu should move left to the approved 20px inset',
);
assert.match(
  finalContainerRule,
  /padding:\s*2px 0 4px 6px;/,
  'submenu should retain the approved 6px inner left padding',
);
assert.match(finalContainerRule, /min-width:\s*0;/, 'submenu container should be allowed to shrink');
assert.match(finalContainerRule, /overflow:\s*hidden;/, 'submenu content should remain clipped to its available width');
assert.match(finalContainerRule, /border-left:\s*0;/, 'submenu should not render a vertical guide line');

assert.match(finalLinkRule, /min-width:\s*0;/, 'submenu row should be allowed to shrink inside the container');
assert.match(finalLabelRule, /display:\s*block;/, 'submenu label should use a truncatable block box');
assert.match(finalLabelRule, /min-width:\s*0;/, 'submenu label should be allowed to shrink');
assert.match(finalLabelRule, /overflow:\s*hidden;/, 'submenu label should hide overflow');
assert.match(finalLabelRule, /text-overflow:\s*ellipsis;/, 'submenu label should show an ellipsis');
assert.match(finalLabelRule, /white-space:\s*nowrap;/, 'submenu label should stay on one line');

console.log('Arunika v2 submenu layout static regression passed.');
