import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { readPageBuilderV24EditorStyles } from './helpers/pagebuilder-v24-module-source.mjs';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => fs.readFileSync(path.join(root, relative), 'utf8');

const shared = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/carousel/Settings.vue');
const heroSlider = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-slider/Settings.vue');
const productColors = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/Settings.vue');
const pageBuilderStyles = readPageBuilderV24EditorStyles(root);
const heroBanner = read('resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-banner/Settings.vue');
const tabs = read('resources/pagebuilder_elementor_v24/modules/widgets/general/tabs/Settings.vue');
const accordion = read('resources/pagebuilder_elementor_v24/modules/widgets/general/accordion/Settings.vue');
const iconList = read('resources/pagebuilder_elementor_v24/modules/widgets/general/icon-list/Settings.vue');
const socialIcons = read('resources/pagebuilder_elementor_v24/modules/widgets/general/social-icons/Settings.vue');

assert.match(shared, /props:\s*\{[\s\S]*?reorder:/, 'Shared repeater must expose reorder capability');
assert.match(shared, /fa-chevron-up.*fa-chevron-down|fa-chevron-down.*fa-chevron-up/s, 'Shared repeater must expose explicit open/closed up/down status icons');
assert.match(shared, /Move Up|move.*direction/s, 'Shared repeater must expose Move Up/Down events');
assert.match(shared, /moveItem\(/, 'Shared settings must swap order without changing the item contract');
assert.match(shared, /@move="moveItem\(/, 'Shared repeater consumers must wire reorder events');
assert.match(shared, /pb-pro-repeater__disclosure/, 'Shared repeater must keep the status icon at the leading edge');

assert.match(heroSlider, /pb-hero-slider-slide__disclosure/, 'Hero Slider must show the open/closed status icon on the left');
assert.match(heroSlider, /fa-chevron-up.*fa-chevron-down|fa-chevron-down.*fa-chevron-up/s, 'Hero Slider status must use up/down icons');
assert.match(heroSlider, /\.pb-hero-slider-slide__header\{[^}]*min-height:36px/, 'Hero Slider slide rows must match the canonical repeater height');
assert.match(heroSlider, /\.pb-hero-slider-slide__title\{[^}]*gap:6px/, 'Hero Slider slide labels must keep the canonical icon-to-label spacing');
assert.match(pageBuilderStyles, /\.pb-widget-settings\.pb-hero-slider-settings \.pb-hero-slider-slide__header \{[\s\S]*?min-height: 36px;[\s\S]*?padding: 4px 6px;/, 'Hero Slider panel overrides must preserve canonical row density');
assert.match(pageBuilderStyles, /\.pb-widget-settings\.pb-hero-slider-settings \.pb-hero-slider-slide__title \{[\s\S]*?gap: 6px;/, 'Hero Slider panel overrides must preserve canonical label spacing');
assert.match(productColors, /fa-chevron-up.*fa-chevron-down|fa-chevron-down.*fa-chevron-up/s, 'Product Color Selector status must use up/down icons');
assert.match(productColors, /<summary @click\.prevent="toggleItem\(item\.id\)"><i class="fas pb-pcs-item__caret"[\s\S]*?<span class="pb-pcs-item__label">/, 'Product Color Selector disclosure must sit before the label like Slides');
assert.match(productColors, /\.pb-product-color-settings \.pb-pcs-repeater\{gap:8px\}/, 'Product Color Selector repeater must keep the canonical item gap');
assert.match(productColors, /\.pb-product-color-settings \.pb-pcs-item>summary\{[^}]*min-height:36px/, 'Product Color Selector item headers must match the canonical repeater height');
assert.match(productColors, /\.pb-product-color-settings \.pb-pcs-item>summary>\.pb-pcs-item__label\{[^}]*gap:6px/, 'Product Color Selector labels must keep the canonical icon-to-label spacing');
assert.match(productColors, /\.pb-product-color-settings \.pb-pcs-item__caret\{[^}]*flex:0 0 16px;[^}]*width:16px;[^}]*font-size:9px/, 'Product Color Selector disclosure must use the canonical leading icon size');
assert.match(productColors, /\.pb-product-color-settings \.pb-pcs-add\{[^}]*font-size:11px/, 'Product Color Selector add action must match the canonical repeater typography');

for (const [name, source] of Object.entries({ heroBanner, tabs, accordion, iconList, socialIcons })) {
    assert.match(source, /pb-[^\n]*disclosure/, `${name} must expose a leading disclosure status icon`);
    assert.match(source, /fa-chevron-up.*fa-chevron-down|fa-chevron-down.*fa-chevron-up/s, `${name} must use the shared open/closed icon convention`);
}

assert.match(heroBanner, /pb-hero-button-item__disclosure[\s\S]*?<\/i><i class="fas fa-grip-vertical"/, 'Hero Banner disclosure must precede the label icon');
assert.match(tabs, /pb-tabs-item-disclosure[\s\S]*?<\/i><span>/, 'Tabs disclosure must remain at the leading edge');
assert.match(accordion, /pb-accordion-item-disclosure[\s\S]*?<\/i><span>/, 'Accordion disclosure must remain at the leading edge');
assert.match(iconList, /pb-icon-list-repeater__disclosure[\s\S]*?<\/i><span class="pb-icon-list-repeater__title">/, 'Icon List disclosure must remain at the leading edge');
assert.match(socialIcons, /pb-social-repeater__disclosure[\s\S]*?<\/i><span class="pb-social-repeater__title">/, 'Social Icons disclosure must remain at the leading edge');

console.log('pagebuilder v2.4 repeater standardization test passed');
