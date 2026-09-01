import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const css = readFileSync('public/assets/css/themes/arunika_aurora/arunika_aurora.css', 'utf8');

const submenuContainerRules = [...css.matchAll(/\.ph-submenu-container\s*\{([^}]*)\}/g)];
const submenuLinkRules = [...css.matchAll(/\.ph-submenu-link\s*\{([^}]*)\}/g)];
const submenuLabelRules = [...css.matchAll(/\.ph-submenu-link\s*>\s*\.ph-submenu-label\s*\{([^}]*)\}/g)];
const navTextRules = [...css.matchAll(/\.ph-nav-text\s*\{([^}]*)\}/g)];

assert.ok(submenuContainerRules.length > 0, 'submenu container CSS should exist');
assert.ok(submenuLinkRules.length > 0, 'submenu link CSS should exist');
assert.ok(submenuLabelRules.length > 0, 'submenu label CSS should exist');
assert.ok(navTextRules.length > 0, 'main menu label CSS should exist');
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
const finalNavTextRule = navTextRules.filter(([, body]) => /font-size:/.test(body)).at(-1)[1];

assert.match(
  css,
  /--ph-shell-hover:\s*var\(--ph-theme-hover-surface,\s*color-mix\(in srgb,\s*var\(--ph-theme-primary\),\s*white 94%\)\);/,
  'light sidebar hover should use the softer Prism-compatible tint',
);
assert.match(
  css,
  /--ph-aurora-menu-hover:\s*rgba\(255,\s*255,\s*255,\s*0\.86\);/,
  'light sidebar menu hover should use the screenshot white surface',
);
assert.match(
  css,
  /--ph-aurora-menu-hover-shadow:\s*0 5px 16px rgba\(60,\s*44,\s*78,\s*0\.05\);/,
  'light sidebar menu hover should use the screenshot shadow',
);
assert.match(
  css,
  /\.ph-sidebar \.list-group-item-action:hover,\s*\.ph-sidebar \.list-group-item-action:focus\s*\{[^}]*background:\s*var\(--ph-bg-hover\);/s,
  'main menu hover should use the shared sidebar hover token',
);
assert.match(
  css,
  /html\[data-bs-theme=light\]\s+\.ph-sidebar \.list-group-item-action:hover,\s*html\[data-bs-theme=light\]\s+\.ph-sidebar \.list-group-item-action:focus\s*\{[^}]*background:\s*var\(--ph-aurora-menu-hover\);[^}]*box-shadow:\s*var\(--ph-aurora-menu-hover-shadow\);/s,
  'light main menu hover should use the screenshot surface and elevation tokens',
);
assert.match(
  css,
  /\.ph-submenu-link:hover,\s*\.ph-submenu-link:focus\s*\{[^}]*background:\s*var\(--ph-bg-hover\);/s,
  'submenu hover should use the same shared hover token as main menu items',
);
assert.match(finalNavTextRule, /font-size:\s*14px;/, 'main menu labels should use the requested 14px size');
assert.match(finalLinkRule, /font-size:\s*14px;/, 'submenu labels should use the requested 14px size');

assert.match(
  finalContainerRule,
  /margin:\s*0 0 8px;/,
  'expanded submenu hover area should span the same width as its parent row',
);
assert.match(
  finalContainerRule,
  /padding:\s*2px 0 4px;/,
  'submenu container should not narrow the hover background with side padding',
);
assert.match(finalContainerRule, /min-width:\s*0;/, 'submenu container should be allowed to shrink');
assert.match(finalContainerRule, /overflow:\s*hidden;/, 'submenu content should remain clipped to its available width');
assert.match(finalContainerRule, /border-left:\s*0;/, 'submenu should not render a vertical guide line');

assert.match(finalLinkRule, /min-width:\s*0;/, 'submenu row should be allowed to shrink inside the container');
assert.match(finalLinkRule, /gap:\s*10px;/, 'submenu icon and label should use only the approved grid gap');
assert.match(
  finalLinkRule,
  /padding:\s*7px 11px 7px 34px;/,
  'submenu content should keep its visual indent while the hover background spans the parent width',
);
assert.match(finalLabelRule, /display:\s*block;/, 'submenu label should use a truncatable block box');
assert.match(finalLabelRule, /min-width:\s*0;/, 'submenu label should be allowed to shrink');
assert.match(finalLabelRule, /overflow:\s*hidden;/, 'submenu label should hide overflow');
assert.match(finalLabelRule, /text-overflow:\s*ellipsis;/, 'submenu label should show an ellipsis');
assert.match(finalLabelRule, /white-space:\s*nowrap;/, 'submenu label should stay on one line');
assert.match(
  css,
  /\.ph-submenu-link\s*>\s*i,\s*\.ph-submenu-link\s*>\s*\.ph-submenu-icon\s*\{[^}]*width:\s*21px\s*!important;[^}]*min-width:\s*21px;[^}]*flex:\s*0 0 21px;[^}]*margin-right:\s*0\s*!important;/s,
  'submenu icons should use the same fixed-width column as parent icons without stacked margins',
);

console.log('Arunika Aurora submenu layout static regression passed.');
