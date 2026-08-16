import assert from 'node:assert/strict';
import { existsSync } from 'node:fs';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const definitionPath = 'public/js/pagebuilder_elementor_v23/widgets/pro/product-color-selector/definition.js';

async function source(relativePath) {
    return readFile(join(rootDir, relativePath), 'utf8');
}

test('Product Color Selector definition exposes the Task 1 settings contract and normalizes unsafe values', async () => {
    assert.equal(existsSync(join(rootDir, definitionPath)), true, 'Product Color Selector definition must exist');

    const context = { window: {} };
    vm.runInNewContext(await source('public/js/pagebuilder_elementor_v23/widget-registry.js'), context);
    context.window.PageBuilderElementorV23ComplexWidgetRuntime = {
        image_box: {
            defaults: () => ({ position: 'default', cssId: '', cssClass: '' }),
        },
    };
    vm.runInNewContext(await source(definitionPath), context);

    const widget = context.window.PageBuilderElementorV23Widgets.get('product_color_selector');
    assert.ok(widget);
    assert.equal(widget.label, 'Product Color Selector');
    assert.equal(widget.category, 'pro');
    assert.equal(widget.icon, 'fas fa-palette');
    assert.match(widget.canvas, /product-color-selector\/Canvas\.vue$/);
    assert.match(widget.settings, /product-color-selector\/Settings\.vue$/);

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

test('Product Color Selector is integrated into the v2.3 catalog and editor metadata', async () => {
    const [config, app] = await Promise.all([
        source('config/pagebuilder_elementor_v23_widgets.php'),
        source('public/js/pagebuilder_elementor_v23/app.js'),
    ]);

    assert.match(config, /'product_color_selector'\s*=>[\s\S]*?'label'\s*=>\s*'Product Color Selector'/);
    assert.match(config, /widgets\/pro\/product-color-selector\/definition\.js/);
    assert.match(config, /'category'\s*=>\s*'pro'/);
    assert.match(app, /product_color_selector:\s*['"]Product Color Selector['"]/);
    assert.match(app, /product_color_selector:\s*['"]fas fa-palette['"]/);
    assert.match(app, /'product_color_selector'/);
});
