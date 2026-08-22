import assert from 'node:assert/strict';
import { existsSync } from 'node:fs';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';
import { configureSingleModule } from './helpers/pagebuilder-v24-module-test.mjs';

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const definitionPath = 'resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/definition.js';

async function source(relativePath) {
    return readFile(join(rootDir, relativePath), 'utf8');
}

test('Product Color Selector definition exposes the Task 1 settings contract and normalizes unsafe values', async () => {
    assert.equal(existsSync(join(rootDir, definitionPath)), true, 'Product Color Selector definition must exist');

    const context = { window: {} };
    vm.runInNewContext(await source('public/js/pagebuilder_elementor_v24/widget-registry.js'), context);
    configureSingleModule(context, await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/module.json'));
    vm.runInNewContext(await source(definitionPath), context);

    const widget = context.window.PageBuilderElementorV24Widgets.get('product_color_selector');
    assert.ok(widget);
    assert.equal(widget.label, 'Product Color Selector');
    assert.equal(widget.category, 'pro');
    assert.equal(widget.icon, 'fas fa-palette');
    assert.equal(widget.canvas, '/pagebuilder-elementor/v2.4/module-assets/product_color_selector/canvas.vue');
    assert.equal(widget.settings, '/pagebuilder-elementor/v2.4/module-assets/product_color_selector/settings.vue');

    const defaults = widget.defaults();
    for (const key of [
        'title', 'description', 'titleTag', 'descriptionTag',
        'titleAlignment', 'titleAlignmentTablet', 'titleAlignmentMobile',
        'items', 'defaultItemId',
        'listPosition', 'listPositionTablet', 'listPositionMobile',
        'listAlignment', 'listAlignmentTablet', 'listAlignmentMobile',
        'imageAspectRatio', 'imageAspectRatioTablet', 'imageAspectRatioMobile',
        'imageFit', 'imageFitTablet', 'imageFitMobile',
        'imagePosition', 'imagePositionTablet', 'imagePositionMobile',
        'transition', 'transitionDuration',
        'surfaceBackground', 'surfaceRadius', 'surfacePadding', 'imageRadius',
        'swatchWidth', 'swatchHeight', 'swatchRadius', 'listGap', 'itemTextGap',
        'activeIndicatorColor', 'activeIndicatorSize', 'titleColor', 'descriptionColor',
        'itemNameColor', 'itemDescriptionColor',
        'titleFontSize', 'titleFontSizeTablet', 'titleFontSizeMobile',
        'descriptionFontSize', 'descriptionFontSizeTablet', 'descriptionFontSizeMobile',
        'itemNameFontSize', 'itemNameFontSizeTablet', 'itemNameFontSizeMobile',
        'itemDescriptionFontSize', 'itemDescriptionFontSizeTablet', 'itemDescriptionFontSizeMobile',
    ]) assert.ok(Object.hasOwn(defaults, key), `missing default ${key}`);
    assert.equal(defaults.items.length, 3);
    assert.equal(defaults.defaultItemId, defaults.items[0].id);
    assert.equal(defaults.listPosition, 'bottom');
    assert.equal(defaults.listAlignment, 'auto');
    assert.equal(defaults.imageAspectRatio, '16 / 9');
    assert.equal(defaults.imageFit, 'contain');
    assert.equal(defaults.transition, 'fade');
    assert.equal(defaults.transitionDuration, '300ms');
    assert.equal(defaults.activeIndicatorColor, '#ffffff');
    assert.equal(defaults.activeIndicatorSize, '34px');
    assert.equal(defaults.surfaceBackground, '#f7f9fa');
    assert.ok(defaults.swatchWidth && defaults.swatchHeight, 'swatches must default to visible blocks');

    for (const item of defaults.items) {
        for (const key of [
            'id', 'name', 'description', 'swatchColor',
            'imageSource', 'imageUrl', 'imageAlt',
            'imageSourceTablet', 'imageUrlTablet', 'imageAltTablet',
            'imageSourceMobile', 'imageUrlMobile', 'imageAltMobile',
        ]) assert.ok(Object.hasOwn(item, key), `missing item default ${key}`);
    }

    const normalized = widget.normalize({
        settings: {
            titleTag: 'script',
            descriptionTag: 'marquee',
            titleAlignment: 'diagonal',
            listPosition: 'diagonal',
            listAlignment: 'diagonal',
            imageAspectRatio: 'expression(1)',
            imageFit: 'stretch',
            imagePosition: 'url(javascript:alert(1))',
            transition: 'spin',
            transitionDuration: 'expression(1)',
            surfaceBackground: 'url(javascript:alert(1))',
            swatchWidth: 'expression(1)',
            items: [{
                id: '', name: 42, description: null, swatchColor: 'url(javascript:alert(1))',
                imageSource: 'invalid', imageUrl: 'javascript:alert(1)', imageAlt: null,
                imageSourceTablet: 'invalid', imageUrlTablet: 'javascript:alert(1)', imageAltTablet: null,
                imageSourceMobile: 'invalid', imageUrlMobile: 'javascript:alert(1)', imageAltMobile: null,
            }],
            defaultItemId: 'missing',
        },
    }).settings;

    assert.equal(normalized.titleTag, defaults.titleTag);
    assert.equal(normalized.descriptionTag, defaults.descriptionTag);
    assert.equal(normalized.titleAlignment, defaults.titleAlignment);
    assert.equal(normalized.listPosition, 'bottom');
    assert.equal(normalized.listAlignment, 'auto');
    assert.equal(normalized.imageAspectRatio, '16 / 9');
    assert.equal(normalized.imageFit, 'contain');
    assert.equal(normalized.imagePosition, defaults.imagePosition);
    assert.equal(normalized.transition, 'fade');
    assert.equal(normalized.transitionDuration, '300ms');
    assert.equal(normalized.surfaceBackground, '#f7f9fa');
    assert.equal(normalized.swatchWidth, defaults.swatchWidth);
    assert.equal(normalized.items.length, 1);
    assert.equal(normalized.items[0].name, '42');
    assert.equal(normalized.items[0].swatchColor, defaults.items[0].swatchColor);
    assert.equal(normalized.items[0].imageSource, 'ckfinder');
    assert.equal(normalized.items[0].imageUrl, '');
    assert.equal(normalized.items[0].imageSourceTablet, 'ckfinder');
    assert.equal(normalized.items[0].imageUrlTablet, '');
    assert.equal(normalized.items[0].imageSourceMobile, 'ckfinder');
    assert.equal(normalized.items[0].imageUrlMobile, '');
    assert.equal(normalized.defaultItemId, normalized.items[0].id);
});

test('Product Color Selector is integrated into the v2.4 catalog and editor metadata', async () => {
    const manifest = JSON.parse(await source('resources/pagebuilder_elementor_v24/modules/widgets/pro/product-color-selector/module.json'));

    assert.equal(manifest.type, 'product_color_selector');
    assert.equal(manifest.label, 'Product Color Selector');
    assert.equal(manifest.category, 'pro');
    assert.equal(manifest.icon, 'fas fa-palette');
    assert.equal(manifest.assets.definition, 'definition.js');
});
