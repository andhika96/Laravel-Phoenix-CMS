import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { readPageBuilderV24EditorStyles } from './helpers/pagebuilder-v24-module-source.mjs';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.readFileSync(path.join(root, relative), 'utf8');

const css = readPageBuilderV24EditorStyles(root);
const iconList = read('resources/pagebuilder_elementor_v24/modules/widgets/general/icon-list/Settings.vue');
const socialIcons = read('resources/pagebuilder_elementor_v24/modules/widgets/general/social-icons/Settings.vue');
const productColors = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Settings.vue');

for (const [name, source] of Object.entries({ iconList, socialIcons })) {
    assert.match(
        source,
        /__title span\{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap\}/,
        `${name} repeater labels must shrink before action buttons`
    );
}

assert.match(
    productColors,
    /\.pb-pcs-item>summary strong\{min-width:0;overflow:hidden;font-size:/,
    'Product Color Selector labels must shrink before action buttons'
);
assert.match(
    css,
    /\.pb-panel\.left \.pb-widget-settings--icon-list \.pb-collapsible-body > \.pb-form-group\s*\{[^}]*min-width:\s*0;/,
    'Icon List grid items must shrink before a long repeater label expands the sidebar'
);

assert.match(
    css,
    /\.pb-panel\.left \.pb-tabs-settings \.pb-tabs-item-fields\s*\{[^}]*padding:\s*10px 10px 12px;/,
    'Tabs item fields must keep bottom breathing room below CSS ID'
);
for (const [settingsClass, itemClass] of [['tabs', 'tabs'], ['accordion', 'accordion']]) {
    assert.match(
        css,
        new RegExp(`\\.pb-panel\\.left \\.pb-${settingsClass}-settings \\.pb-${itemClass}-item-main \\{[^}]*min-width:\\s*0;[^}]*overflow:\\s*hidden;`),
        `${settingsClass} repeater headers must stay inside the sidebar`,
    );
    assert.match(
        css,
        new RegExp(`\\.pb-panel\\.left \\.pb-${settingsClass}-settings \\.pb-${itemClass}-item-main > span \\{[^}]*min-width:\\s*0;[^}]*text-overflow:\\s*ellipsis;[^}]*white-space:\\s*nowrap;`),
        `${settingsClass} labels must use a bounded ellipsis`,
    );
}

console.log('pagebuilder v2.4 repeater boundary regression test passed');
