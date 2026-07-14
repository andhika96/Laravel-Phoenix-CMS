import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const profileBlade = readFileSync('resources/views/profile/profile.blade.php', 'utf8');
const css = readFileSync('public/assets/css/themes/arunika_v2/arunika_v2.css', 'utf8');
const expandedCategoryRules = [...css.matchAll(/\.ph-sidebar\.ph-expanded \.ph-nav-category\s*\{([^}]*)\}/g)];

assert.match(
  profileBlade,
  /assets\/js\/vue3\/account\/vueV3-account-2026\.js/,
  'profile page should load the existing Vue account controller',
);
assert.doesNotMatch(
  profileBlade,
  /assets\/js\/c\/vue3\/vueV3-account-2025\.js/,
  'profile page should not request the missing legacy account script',
);

assert.ok(expandedCategoryRules.length > 0, 'expanded sidebar category CSS should exist');
const finalExpandedCategoryRule = expandedCategoryRules.filter(([, body]) => /padding:/.test(body)).at(-1)[1];
assert.match(finalExpandedCategoryRule, /position:\s*relative;/, 'category should anchor its faded separator');
assert.match(finalExpandedCategoryRule, /margin:\s*12px 0 10px;/, 'each category should have consistent top separation');
assert.match(finalExpandedCategoryRule, /border-top:\s*0;/, 'category should replace the hard border with a faded separator');
assert.match(
  css,
  /\.ph-sidebar\.ph-expanded \.ph-nav-category::before\s*\{[^}]*content:\s*"";[^}]*position:\s*absolute;[^}]*top:\s*0;[^}]*left:\s*0;[^}]*right:\s*0;[^}]*height:\s*1px;[^}]*background:\s*linear-gradient\(90deg, transparent 0, var\(--ph-sidebar-border\) 32px, var\(--ph-sidebar-border\) 100%\);/s,
  'expanded categories should fade the left edge of their separator',
);

console.log('Arunika v2 profile photo and category static regression passed.');
