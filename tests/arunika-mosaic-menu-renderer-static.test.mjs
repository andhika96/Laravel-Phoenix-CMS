import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const menuBlade = readFileSync(
    'resources/views/themes/arunika_mosaic/components/menu.blade.php',
    'utf8',
);

const menuV2Start = menuBlade.indexOf('function menu_v2()');
const menuV2Renderer = menuBlade.slice(menuV2Start);
const safeSubmenuLoop = /foreach\s*\(\$value1\['parent_submenu'\]\['list'\]\s+as\s+\$key\d+\s*=>\s*\$value2\)/g;
const shadowingSubmenuLoop = /foreach\s*\(\$value1\['parent_submenu'\]\['list'\]\s+as\s+\$key1\s*=>\s*\$value1\)/;

assert.notEqual(menuV2Start, -1, 'Arunika Mosaic menu_v2 renderer should exist');
assert.doesNotMatch(
    menuV2Renderer,
    shadowingSubmenuLoop,
    'submenu loops must not overwrite the parent menu variable used by the floating header',
);
assert.equal(
    menuV2Renderer.match(safeSubmenuLoop)?.length ?? 0,
    4,
    'categorized and uncategorized collapsed/floating submenu loops should preserve the parent menu variable',
);
assert.match(
    menuV2Renderer,
    /ph-floating-header">'\.\$value1\['parent_name'\]/,
    'the floating submenu header should continue reading the preserved parent menu name',
);

