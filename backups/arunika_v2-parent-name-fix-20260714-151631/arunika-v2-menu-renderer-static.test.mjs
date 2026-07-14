import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const menuBlade = readFileSync(
    'resources/views/themes/arunika_v2/components/menu.blade.php',
    'utf8',
);

const categorizedStart = menuBlade.indexOf('// Menu list with category');
const uncategorizedStart = menuBlade.indexOf('// Menu list without category');

assert.notEqual(categorizedStart, -1, 'categorized menu renderer should exist');
assert.notEqual(uncategorizedStart, -1, 'uncategorized menu renderer should exist');

const categorizedRenderer = menuBlade.slice(categorizedStart, uncategorizedStart);
const safeSubmenuLoop = /foreach\s*\(\$value1\['parent_submenu'\]\['list'\]\s+as\s+\$key1\s*=>\s*\$value2\)/g;
const shadowingSubmenuLoop = /foreach\s*\(\$value1\['parent_submenu'\]\['list'\]\s+as\s+\$key1\s*=>\s*\$value1\)/;

assert.doesNotMatch(
    categorizedRenderer,
    shadowingSubmenuLoop,
    'categorized submenu loops must not overwrite the parent menu variable',
);
assert.equal(
    categorizedRenderer.match(safeSubmenuLoop)?.length ?? 0,
    2,
    'categorized collapsed and floating submenu loops should preserve the parent menu variable',
);

