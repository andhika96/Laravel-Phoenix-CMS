import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const css = readFileSync('public/assets/css/themes/arunika_v2/arunika_v2.css', 'utf8');
const expandedUserCardRules = [...css.matchAll(/\.ph-sidebar\.ph-expanded \.ph-sidebar-user-card\s*\{([^}]*)\}/g)];
const userMetaStrongRules = [...css.matchAll(/\.ph-sidebar-user-meta strong\s*\{([^}]*)\}/g)];
const userMetaSpanRules = [...css.matchAll(/\.ph-sidebar-user-meta span\s*\{([^}]*)\}/g)];
const expandedLogoutRules = [...css.matchAll(/\.ph-sidebar\.ph-expanded \.ph-sidebar-logout\s*\{([^}]*)\}/g)];
const expandedLogoutSpanRules = [...css.matchAll(/\.ph-sidebar\.ph-expanded \.ph-sidebar-logout span\s*\{([^}]*)\}/g)];

assert.ok(expandedUserCardRules.length > 0, 'expanded sidebar user card CSS should exist');
assert.ok(userMetaStrongRules.length > 0, 'sidebar user name CSS should exist');
assert.ok(userMetaSpanRules.length > 0, 'sidebar user secondary text CSS should exist');
assert.ok(expandedLogoutRules.length > 0, 'expanded sidebar logout CSS should exist');
assert.ok(expandedLogoutSpanRules.length > 0, 'expanded sidebar logout label CSS should exist');

const finalExpandedUserCardRule = expandedUserCardRules.at(-1)[1];
const finalUserMetaStrongRule = userMetaStrongRules.at(-1)[1];
const finalUserMetaSpanRule = userMetaSpanRules.at(-1)[1];
const finalExpandedLogoutRule = expandedLogoutRules.at(-1)[1];
const finalExpandedLogoutSpanRule = expandedLogoutSpanRules.at(-1)[1];

assert.match(finalExpandedUserCardRule, /height:\s*65px;/, 'expanded user card should be 65px high');
assert.match(finalExpandedUserCardRule, /min-height:\s*65px;/, 'expanded user card minimum height should be 65px');
assert.match(finalExpandedUserCardRule, /padding:\s*1rem \.75rem;/, 'expanded user card should use the requested padding');
assert.match(finalUserMetaStrongRule, /font-size:\s*14px;/, 'sidebar user name should use 14px text');
assert.match(finalUserMetaSpanRule, /font-size:\s*12\.5px;/, 'sidebar user secondary text should use 12.5px text');
assert.match(finalExpandedLogoutRule, /height:\s*45px;/, 'expanded sidebar logout should be 45px high');
assert.match(finalExpandedLogoutSpanRule, /font-size:\s*14px;/, 'expanded sidebar logout label should use 14px text');

console.log('Arunika v2 sidebar user card static regression passed.');
