import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { readPageBuilderV24FrontendStyles } from './helpers/pagebuilder-v24-module-source.mjs';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.readFileSync(path.join(root, relative), 'utf8');

const app = read('resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/definition.js');
const settings = read('resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/Settings.vue');
const canvas = read('resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/Canvas.vue');
const blade = read('resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/frontend.blade.php');
const css = readPageBuilderV24FrontendStyles(root);

assert.match(app, /tabsNavBorderType:\s*'none'/);
assert.match(app, /tabsNavBorderWidth:\s*'0px'/);
assert.match(app, /tabsNavBorderColor:\s*'transparent'/);
assert.match(app, /normalized\.settings\s*=\s*\{ \.\.\.tabsWidgetDefaults\(\), \.\.\.\(normalized\.settings \|\| \{\}\) \};/);

assert.match(settings, /<summary>Navigation Border<\/summary>/);
assert.match(settings, /tabsNavBorderType/);
assert.match(settings, /tabsNavBorderWidth/);
assert.match(settings, /tabsNavBorderColor/);

for (const variable of [
	'--pb-tabs-nav-border-style',
	'--pb-tabs-nav-border-width',
	'--pb-tabs-nav-border-color',
]) {
	assert.match(canvas, new RegExp(variable.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')));
	assert.match(blade, new RegExp(variable.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')));
}

assert.match(css, /\.el-widget-tabs__nav\s*\{[\s\S]*border-style:\s*var\(--pb-tabs-nav-border-style/);
assert.match(css, /\.el-widget-tabs__nav\s*\{[\s\S]*border-width:\s*var\(--pb-tabs-nav-border-width/);
assert.match(css, /\.el-widget-tabs__nav\s*\{[\s\S]*border-color:\s*var\(--pb-tabs-nav-border-color/);

console.log('pagebuilder v2.4 tabs navigation border parity test passed');
