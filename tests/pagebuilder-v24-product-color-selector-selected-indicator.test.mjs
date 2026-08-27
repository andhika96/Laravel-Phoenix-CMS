import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const root = path.resolve(import.meta.dirname, '..');
const moduleRoot = path.join(root, 'resources', 'pagebuilder_elementor_v24', 'modules', 'widgets', 'pro', 'product-color-selector');
const read = (name) => fs.readFileSync(path.join(moduleRoot, name), 'utf8');

function loadDefinition() {
    const registry = {
        definition: null,
        advancedDefaults: () => ({}),
        register(definition) { this.definition = definition; },
    };
    const context = vm.createContext({ window: { PageBuilderElementorV24Widgets: registry } });
    vm.runInContext(read('definition.js'), context);
    assert.ok(registry.definition, 'Product Color Selector definition should register');
    return registry.definition;
}

test('Product Color Selector exposes and normalizes the Selected Indicator contract', () => {
    const definition = loadDefinition();
    const defaults = definition.defaults();

    assert.deepEqual(
        Object.fromEntries([
            ['selectedCheckVisible', defaults.selectedCheckVisible],
            ['selectedCheckPosition', defaults.selectedCheckPosition],
            ['selectedCheckSize', defaults.selectedCheckSize],
            ['selectedCheckIconSize', defaults.selectedCheckIconSize],
            ['selectedCheckOffset', defaults.selectedCheckOffset],
            ['selectedCheckRadius', defaults.selectedCheckRadius],
            ['selectedCheckRadiusTop', defaults.selectedCheckRadiusTop],
            ['selectedCheckRadiusRight', defaults.selectedCheckRadiusRight],
            ['selectedCheckRadiusBottom', defaults.selectedCheckRadiusBottom],
            ['selectedCheckRadiusLeft', defaults.selectedCheckRadiusLeft],
            ['selectedCheckColor', defaults.selectedCheckColor],
            ['selectedCheckBackground', defaults.selectedCheckBackground],
            ['unselectedRingVisible', defaults.unselectedRingVisible],
            ['unselectedRingColor', defaults.unselectedRingColor],
            ['unselectedRingBorderWidth', defaults.unselectedRingBorderWidth],
            ['unselectedRingBackground', defaults.unselectedRingBackground],
            ['variantListSpacing', defaults.variantListSpacing],
            ['variantListSpacingTablet', defaults.variantListSpacingTablet],
            ['variantListSpacingMobile', defaults.variantListSpacingMobile],
        ]),
        {
            selectedCheckVisible: true,
            selectedCheckPosition: 'top-right',
            selectedCheckSize: '20px',
            selectedCheckIconSize: '10px',
            selectedCheckOffset: '14px',
            selectedCheckRadius: '50%',
            selectedCheckRadiusTop: '50%',
            selectedCheckRadiusRight: '50%',
            selectedCheckRadiusBottom: '50%',
            selectedCheckRadiusLeft: '50%',
            selectedCheckColor: '#ffffff',
            selectedCheckBackground: '#6979f8',
            unselectedRingVisible: false,
            unselectedRingColor: '#ffffff',
            unselectedRingBorderWidth: '2px',
            unselectedRingBackground: 'transparent',
            variantListSpacing: '16px',
            variantListSpacingTablet: '',
            variantListSpacingMobile: '',
        },
    );

    const settings = definition.normalize({ settings: {
        selectedCheckVisible: false,
        selectedCheckPosition: 'bottom-left',
        selectedCheckSize: '24px',
        selectedCheckIconSize: '12px',
        selectedCheckOffset: '8px',
        selectedCheckRadius: '2px 4px 6px 8px',
        selectedCheckColor: '#111827',
        selectedCheckBackground: '#ffffff',
        unselectedRingVisible: true,
        unselectedRingColor: 'url(javascript:alert(1))',
        unselectedRingBorderWidth: '4vh',
        unselectedRingBackground: 'javascript:alert(1)',
    } }).settings;

    assert.equal(settings.selectedCheckVisible, false);
    assert.equal(settings.selectedCheckPosition, 'bottom-left');
    assert.equal(settings.selectedCheckSize, '24px');
    assert.equal(settings.selectedCheckIconSize, '12px');
    assert.equal(settings.selectedCheckOffset, '8px');
    assert.equal(settings.selectedCheckRadiusTop, '2px');
    assert.equal(settings.selectedCheckRadiusRight, '4px');
    assert.equal(settings.selectedCheckRadiusBottom, '6px');
    assert.equal(settings.selectedCheckRadiusLeft, '8px');
    assert.equal(settings.selectedCheckColor, '#111827');
    assert.equal(settings.selectedCheckBackground, '#ffffff');
    assert.equal(settings.unselectedRingVisible, true);
    assert.equal(settings.unselectedRingColor, '#ffffff');
    assert.equal(settings.unselectedRingBorderWidth, '2px');
    assert.equal(settings.unselectedRingBackground, 'transparent');

    const responsive = definition.normalize({ settings: { selectedCheckRadiusTopTablet: '3pt' } }).settings;
    assert.equal(responsive.selectedCheckRadiusTopTablet, '3pt');

    const legacy = definition.normalize({ settings: { listGap: '28px' } }).settings;
    assert.equal(legacy.variantListSpacing, '28px');
    const explicit = definition.normalize({ settings: { listGap: '0px', variantListSpacing: '24px' } }).settings;
    assert.equal(explicit.variantListSpacing, '24px');
});

test('Product Color Selector renders the Selected Indicator settings and keeps Canvas clicks interactive', () => {
    const settings = read('Settings.vue');
    const canvas = read('Canvas.vue');
    const frontend = read('frontend.blade.php');

    for (const marker of [
        'Selected Indicator',
        'Show Selected Check',
        'Selected Check Position',
        'Selected Check Size',
        'Selected Check Icon Size',
        'Selected Check Offset',
        'Selected Check Radius',
        'Selected Check Color',
        'Selected Check Background',
        'selectedCheckRadius',
        'Show Unselected Ring',
        'Unselected Ring Color',
        'Unselected Ring Border Width',
        'Unselected Ring Background',
        'unselectedRingVisible',
        'Variant List Spacing',
        'variantListSpacing',
    ]) assert.ok(settings.includes(marker), `Settings must include ${marker}`);

    for (const marker of [
        'selectedCheckVisible',
        'selectedCheckPosition',
        'selectedCheckSize',
        'selectedCheckIconSize',
        'selectedCheckOffset',
        'selectedCheckRadius',
        'selectedCheckColor',
        'selectedCheckBackground',
        'unselectedRingVisible',
        'is-unselected',
        '--pb-pcs-unselected-ring-color',
        '--pb-pcs-unselected-ring-border-width',
        '--pb-pcs-unselected-ring-background',
        '--pb-pcs-variant-list-spacing',
        'pointer-events: auto',
    ]) assert.ok(canvas.includes(marker), `Canvas must include ${marker}`);

    assert.match(canvas, /\.pb-node-product_color_selector \.pb-preview[\s\S]*?pointer-events:\s*auto/);
    assert.match(canvas, /\.pb-product-color-selector__body\{[^}]*gap:var\(--pb-pcs-variant-list-spacing\)/);
    assert.match(canvas, /\.pb-product-color-selector__list\{[^}]*gap:var\(--pb-pcs-list-gap\)/);

    for (const marker of [
        'unselectedRingVisible',
        'is-unselected',
        '--pb-pcs-unselected-ring-color',
        '--pb-pcs-unselected-ring-border-width',
        '--pb-pcs-unselected-ring-background',
    ]) assert.ok(frontend.includes(marker), `Frontend must include ${marker}`);
});
