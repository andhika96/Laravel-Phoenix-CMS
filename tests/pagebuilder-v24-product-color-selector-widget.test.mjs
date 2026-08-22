import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.existsSync(path.join(root, relative)) ? fs.readFileSync(path.join(root, relative), 'utf8') : '';

const definition = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/definition.js');
const canvas = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Canvas.vue');
const settings = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Settings.vue');
const blade = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/frontend.blade.php');
const manifest = JSON.parse(read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/module.json'));
const runtime = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/runtime.js');

assert.ok(definition, 'Product Color Selector definition must exist');
assert.match(definition, /type:\s*['"]product_color_selector['"]/);
assert.match(definition, /defaultItemId/);
assert.match(definition, /swatchColor/);
assert.match(definition, /imageUrlTablet/);
assert.match(definition, /imageUrlMobile/);
assert.match(definition, /normalize\s*\(/);

assert.ok(canvas, 'Product Color Selector Canvas must exist');
for (const marker of ['props: { item:', 'responsiveDevice', 'this.item.settings', 'data-pb-interactive', 'role="tablist"', 'aria-selected', 'ArrowLeft', 'ArrowUp', 'defaultItemId', 'responsiveValue']) {
    assert.ok(canvas.includes(marker), `Canvas must include ${marker}`);
}

assert.ok(settings, 'Product Color Selector Settings must exist');
for (const marker of ['Color Items', 'Color Name', 'Short Description', 'coloris pb-coloris-input', 'CKFinder / Media Library', 'Default Active Color', 'List Position', "['top','right','bottom','left']", 'widgetAdvancedControls']) {
    assert.ok(settings.includes(marker), `Settings must include ${marker}`);
}

assert.match(settings, /formatChoiceLabel\(option\)/, 'Choice labels should be formatted for display');
assert.match(settings, /responsiveValue\('imageAspectRatio','16 \/ 9'\)/, 'Aspect ratio fallback should match the normalized value');
assert.match(settings, /<option value="16 \/ 9">16:9<\/option>/, 'The default aspect ratio option must be selectable');
assert.doesNotMatch(settings, /<option value="16\/9">16:9<\/option>/, 'The aspect ratio option must not use the unnormalized value');
assert.match(settings, /\.pb-pcs-devices\{[^}]*display:grid;[^}]*gap:3px/, 'Responsive device tabs should use the compact grid pattern');
assert.match(settings, /pb-pcs-devices \.pb-seg-btn\)\{[^}]*gap:5px!important/, 'Responsive device tab icons and labels should have a consistent 5px gap');
assert.match(settings, /\.pb-pcs-item>summary strong\{[^}]*font-size:11px/, 'Repeater item names should use compact typography');
assert.match(settings, /\.pb-pcs-item__body \.pb-textarea\{[^}]*height:64px/, 'Short descriptions should use a compact textarea');
assert.match(settings, /\.pb-pcs-item>summary\{[^}]*min-height:36px/, 'Color item rows should match the canonical repeater height');
assert.match(settings, /\.pb-pcs-repeater\{gap:8px\}/, 'Color item rows should match the canonical repeater gap');
assert.match(settings, /\.pb-pcs-item>summary strong\{[^}]*font-size:11px/, 'Color item names should match the shared repeater typography');
assert.match(settings, /\.pb-pcs-item__actions button\{[^}]*width:26px;height:26px/, 'Color item action buttons should be compact');
assert.match(settings, /\.pb-pcs-add\{[^}]*height:34px/, 'Add Color should match the compact repeater add control');
assert.match(settings, /\.pb-pcs-add\{[^}]*font-size:11px/, 'Add Color should match the canonical repeater typography');
assert.match(settings, /pb-pcs-item__caret/, 'Color item headers should use an explicit compact caret');
assert.match(settings, /\.pb-pcs-item__caret\{[^}]*flex:0 0 16px;[^}]*width:16px;[^}]*font-size:9px/, 'Color item disclosure should match the canonical leading icon');
assert.match(settings, /<summary @click\.prevent="toggleItem\(item\.id\)"><i class="fas pb-pcs-item__caret"[\s\S]*?<span class="pb-pcs-item__label">/, 'Color item disclosure should sit before the label like Slides');
assert.match(settings, /\.pb-pcs-item>summary>\.pb-pcs-item__label\{[^}]*flex:1 1 auto;[^}]*justify-content:flex-start/, 'Color item names should stay left aligned');
assert.match(settings, /\.pb-pcs-item>summary>\.pb-pcs-item__label\{[^}]*gap:6px/, 'Color item labels should keep the canonical icon-to-label spacing');
assert.match(settings, /\.pb-pcs-item__caret\{display:grid;[^}]*place-items:center/, 'Color item disclosure should be centered in its leading cell');
assert.match(settings, /\.pb-pcs-item>summary::?-webkit-details-marker\{display:none!important\}/, 'Native details marker should not create an alignment offset');
assert.match(settings, /\.pb-pcs-item>summary::before\{content:none!important;display:none!important/, 'Inherited collapsible caret should not offset color item labels');

assert.ok(blade, 'Product Color Selector Blade renderer must exist');
for (const marker of ['data-product-color-selector', 'data-product-color-panel', 'data-product-color-index', 'aria-selected', '@media(max-width:1024px)', '@media(max-width:767px)']) {
    assert.ok(blade.includes(marker), `Blade renderer must include ${marker}`);
}

assert.equal(manifest.type, 'product_color_selector');
assert.equal(manifest.label, 'Product Color Selector');
assert.equal(manifest.icon, 'fas fa-palette');
assert.equal(manifest.category, 'pro');
assert.equal(manifest.assets.runtime, 'runtime.js');

assert.match(runtime, /function initProductColorSelector\(/);
assert.match(runtime, /\[data-product-color-selector\]/);
assert.match(runtime, /PageBuilderElementorV24ModuleRuntimes\s*\|\|=/);
assert.match(runtime, /runtimes\["product_color_selector"\]\s*=\s*Object\.freeze\(\{\s*init,\s*initProductColorSelector\s*\}\)/);

console.log('pagebuilder v2.4 product color selector widget parity test passed');
