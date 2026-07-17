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
assert.match(finalExpandedCategoryRule, /margin:\s*12px 0 10px;/, 'each category should have consistent top separation');
assert.match(
  finalExpandedCategoryRule,
  /border-top:\s*1px solid var\(--ph-sidebar-category-border\);/,
  'category separator should remain solid from the left edge',
);
assert.doesNotMatch(
  css,
  /\.ph-sidebar\.ph-expanded \.ph-nav-category::before\s*\{/,
  'expanded categories should not use a pseudo-element that fades the left edge',
);

console.log('Arunika v2 profile photo and category static regression passed.');
