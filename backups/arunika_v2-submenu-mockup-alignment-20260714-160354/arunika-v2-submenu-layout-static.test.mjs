import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const css = readFileSync('public/assets/css/themes/arunika_v2/arunika_v2.css', 'utf8');

const submenuContainerRules = [...css.matchAll(/\.ph-submenu-container\s*\{([^}]*)\}/g)];
const submenuLinkRules = [...css.matchAll(/\.ph-submenu-link\s*\{([^}]*)\}/g)];
const submenuLabelRules = [...css.matchAll(/\.ph-submenu-link\s*>\s*\.ph-submenu-label\s*\{([^}]*)\}/g)];

assert.ok(submenuContainerRules.length > 0, 'submenu container CSS should exist');
assert.ok(submenuLinkRules.length > 0, 'submenu link CSS should exist');
assert.ok(submenuLabelRules.length > 0, 'submenu label CSS should exist');
assert.doesNotMatch(
  css,
  /\.ph-submenu-link span\s*\{/,
  'submenu label styling must not target the custom-icon span wrapper',
);
assert.doesNotMatch(
  css,
  /\.ph-floating-link span\s*\{/,
  'floating submenu label styling must not target its custom-icon span wrapper',
);
assert.match(
  css,
  /\.ph-floating-link\s*>\s*\.ph-submenu-label\s*\{[^}]*flex:\s*1;/s,
  'floating submenu truncation should target only the semantic label',
);

const finalContainerRule = submenuContainerRules.at(-1)[1];
const finalLinkRule = submenuLinkRules.at(-1)[1];
const finalLabelRule = submenuLabelRules.at(-1)[1];

assert.match(
  finalContainerRule,
  /margin:\s*0 16px 8px 0;/,
  'expanded submenu should remove the redundant production-only outer left inset',
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
assert.match(finalLinkRule, /gap:\s*10px;/, 'submenu icon and label should use only the approved grid gap');
assert.match(finalLinkRule, /padding:\s*7px 8px;/, 'submenu row should use the compact production padding');
assert.match(finalLabelRule, /display:\s*block;/, 'submenu label should use a truncatable block box');
assert.match(finalLabelRule, /min-width:\s*0;/, 'submenu label should be allowed to shrink');
assert.match(finalLabelRule, /overflow:\s*hidden;/, 'submenu label should hide overflow');
assert.match(finalLabelRule, /text-overflow:\s*ellipsis;/, 'submenu label should show an ellipsis');
assert.match(finalLabelRule, /white-space:\s*nowrap;/, 'submenu label should stay on one line');
assert.match(
  css,
  /\.ph-submenu-link\s*>\s*i,\s*\.ph-submenu-link\s*>\s*\.ph-submenu-icon\s*\{[^}]*margin-right:\s*0\s*!important;/s,
  'submenu icon spacing should not stack Bootstrap or legacy margins on top of the flex gap',
);

console.log('Arunika v2 submenu layout static regression passed.');
