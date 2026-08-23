import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';
import { compileTemplate, parse } from '@vue/compiler-sfc';

const root = path.resolve(import.meta.dirname, '..');
const moduleRoot = path.join(root, 'resources', 'pagebuilder_elementor_v24', 'modules', 'widgets', 'pro', 'product-lead-form');

function loadDefinition() {
    const source = fs.readFileSync(path.join(moduleRoot, 'definition.js'), 'utf8');
    const registry = {
        definition: null,
        advancedDefaults: () => ({}),
        normalizeAdvanced: (settings) => settings,
        register(definition) { this.definition = definition; },
    };
    const context = vm.createContext({
        console,
        Math,
        URL,
        URLSearchParams,
        window: {
            PageBuilderElementorV24Widgets: registry,
            PageBuilderElementorV24ComplexWidgetRuntime: { image_box: { defaults: () => ({}) } },
        },
    });

    vm.runInContext(source, context);
    assert.ok(registry.definition, 'Product Lead Form definition should register');
    assert.equal(registry.definition.type, 'product_lead_form');
    assert.equal(typeof context.window.PageBuilderElementorV24ProductLeadFormLogic, 'object');

    return {
        definition: registry.definition,
        logic: context.window.PageBuilderElementorV24ProductLeadFormLogic,
    };
}

const clone = (value) => JSON.parse(JSON.stringify(value));
const nodes = [
    {
        id: 'model-s5', parentId: null, label: 'MGS5 EV', code: 'mgs5ev', value: 'MGS5_EV', active: true, sortOrder: 1,
        meta: { thumbnailUrl: '/s5-thumb.jpg', imageUrl: '/s5.jpg', description: 'Because Everyone Matters', detailUrl: '/models/mgs5ev' },
    },
    {
        id: 'type-lux', parentId: 'model-s5', label: 'Luxury', code: 'luxury', value: 'LUXURY', active: true, sortOrder: 1,
        meta: { imageUrl: '/s5-luxury.jpg' },
    },
    {
        id: 'variant-long', parentId: 'type-lux', label: 'Long Range', code: 'long-range', value: 'LONG_RANGE', active: true, sortOrder: 1,
        meta: { detailLabel: 'Explore Long Range' },
    },
    { id: 'variant-short', parentId: 'type-lux', label: 'Standard Range', code: 'standard-range', value: 'STANDARD_RANGE', active: true, sortOrder: 2 },
    { id: 'model-zs', parentId: null, label: 'MG ZS', code: 'mgzs', value: 'MG_ZS', active: true, sortOrder: 2 },
    { id: 'type-zs', parentId: 'model-zs', label: 'Activate', code: 'activate', value: 'ACTIVATE', active: true, sortOrder: 1 },
    { id: 'variant-inactive', parentId: 'type-zs', label: 'Hidden', code: 'hidden', value: 'HIDDEN', active: false, sortOrder: 1 },
];

test('Product Lead Form defaults expose a shared dataset and three configurable levels', () => {
    const { definition } = loadDefinition();
    const node = definition.normalize({ id: 'lead-1', type: 'product_lead_form', settings: {} });

    assert.deepEqual(clone(node.settings.productData), { datasetMode: 'dataset', datasetId: '' });
    assert.equal(node.settings.productLevelCount, 3);
    assert.deepEqual(clone(node.settings.productLevels), [
        { key: 'model', label: 'Model', fieldId: 'product_model', queryKey: 'model', presentation: 'cards', required: true, defaultNodeId: '' },
        { key: 'type', label: 'Type', fieldId: 'product_type', queryKey: 'type', presentation: 'select', required: true, defaultNodeId: '' },
        { key: 'variant', label: 'Variant', fieldId: 'product_variant', queryKey: 'variant', presentation: 'select', required: true, defaultNodeId: '' },
    ]);
    assert.equal(node.settings.productMediaPosition, 'left');
    assert.equal(node.settings.productMediaPositionMobile, 'top');
    assert.equal(node.settings.productTitlePlacement, 'media-below');
    assert.equal(node.settings.productTitleAlign, 'left');
    assert.equal(node.settings.productTitleAlignTablet, '');
    assert.equal(node.settings.productTitleAlignMobile, '');
    assert.equal(node.settings.productTitleGap, '4px');
    assert.equal(node.settings.productFormVerticalAlign, 'top');
    assert.equal(node.settings.syncProductQuery, true);
    assert.equal(node.settings.productLevelStyles.length, 3);
    assert.equal(node.settings.productLevelStyles[0].imageWidth, '120px');
    assert.equal(node.settings.productLevelStyles[0].imageShape, 'circle');
    assert.equal(node.settings.productLevelStyles[0].imageLabelPlacement, 'below');
    assert.equal(node.settings.productLevelStyles[0].imageLabelGap, '12px');
    assert.equal(node.settings.productLevelStyles[0].imagePadding, '0px');
    assert.equal(node.settings.productLevelStyles[0].imagePaddingTop, '0px');
    assert.equal(node.settings.productLevelStyles[0].selectedBorderColor, '#6979f8');
    assert.equal(node.settings.productLevelStyles[0].hoverBorderWidth, '1px');
    assert.equal(node.settings.productLevelStyles[0].selectedBorderWidth, '1px');
    assert.equal(node.settings.productLevelStyles[0].cardWidth, '100%');
    assert.equal(node.settings.productLevelStyles[0].cardHeight, '220px');
    assert.equal(node.settings.productLevelStyles[0].cardHeightMode, 'auto');
    assert.equal(node.settings.productLevelStyles[0].cardPadding, '16px');
    assert.equal(node.settings.productLevelStyles[0].cardPaddingTop, '16px');
    assert.equal(node.settings.productLevelStyles[0].cardPaddingRight, '16px');
    assert.equal(node.settings.productLevelStyles[0].cardMarginLeft, '0px');
    assert.equal(node.settings.productLevelStyles[0].borderRadiusTop, '8px');
    assert.equal(node.settings.productLevelStyles[0].cardMargin, '0px');
    assert.equal(node.settings.productLevelStyles[0].selectedCheckVisible, true);
    assert.equal(node.settings.productLevelStyles[0].selectedCheckPosition, 'top-right');
    assert.equal(node.settings.productLevelStyles[0].selectedCheckSize, '20px');
    assert.equal(node.settings.productLevelStyles[0].selectedCheckIconSize, '10px');
    assert.equal(node.settings.productLevelStyles[0].selectedCheckColor, '#ffffff');
    assert.equal(node.settings.productLevelStyles[0].selectedCheckBackground, '#6979f8');
    assert.equal(node.settings.productImageFit, 'cover');
});

test('product title placement and form alignment normalize legacy or invalid enum values safely', () => {
    const { definition } = loadDefinition();
    const settings = definition.normalize({
        id: 'lead-title-placement-1',
        type: 'product_lead_form',
        settings: {
            productTitlePlacement: 'invalid',
            productTitleAlign: 'invalid',
            productTitleAlignTablet: 'right',
            productTitleAlignMobile: 'invalid',
            productFormVerticalAlign: 'center',
            productFormVerticalAlignTablet: 'bottom',
            productFormVerticalAlignMobile: 'invalid',
        },
    }).settings;

    assert.equal(settings.productTitlePlacement, 'media-below');
    assert.equal(settings.productTitleAlign, 'left');
    assert.equal(settings.productTitleAlignTablet, 'right');
    assert.equal(settings.productTitleAlignMobile, '');
    assert.equal(settings.productFormVerticalAlign, 'center');
    assert.equal(settings.productFormVerticalAlignTablet, 'bottom');
    assert.equal(settings.productFormVerticalAlignMobile, '');
});

test('a unique standalone variant query infers its type and model ancestors', () => {
    const { definition, logic } = loadDefinition();
    const settings = definition.normalize({ id: 'lead-1', type: 'product_lead_form', settings: {} }).settings;
    const selection = logic.resolveSelection(nodes, settings, { variant: 'long-range' });

    assert.deepEqual(clone(selection.ids), ['model-s5', 'type-lux', 'variant-long']);
    assert.deepEqual(clone(selection.codes), ['mgs5ev', 'luxury', 'long-range']);
    assert.deepEqual(clone(selection.values), ['MGS5_EV', 'LUXURY', 'LONG_RANGE']);
    assert.deepEqual(clone(selection.invalidQueryKeys), []);
});

test('legacy shorthand card spacing and radius values expand into standard side settings', () => {
    const { definition } = loadDefinition();
    const settings = definition.normalize({
        id: 'lead-1',
        type: 'product_lead_form',
        settings: {
            productLevelStyles: [{ cardPadding: '4px 8px', cardMargin: '2px', borderRadius: '10px 20px' }],
        },
    }).settings;

    assert.equal(settings.productLevelStyles[0].cardPaddingTop, '4px');
    assert.equal(settings.productLevelStyles[0].cardPaddingRight, '8px');
    assert.equal(settings.productLevelStyles[0].cardPaddingBottom, '4px');
    assert.equal(settings.productLevelStyles[0].cardPaddingLeft, '8px');
    assert.equal(settings.productLevelStyles[0].cardMarginRight, '2px');
    assert.equal(settings.productLevelStyles[0].borderRadiusTop, '10px');
    assert.equal(settings.productLevelStyles[0].borderRadiusRight, '20px');
});

test('legacy custom label gap is promoted to canonical content gap', () => {
    const { definition } = loadDefinition();
    const settings = definition.normalize({
        id: 'lead-gap-1',
        type: 'product_lead_form',
        settings: {
            productLevelStyles: [{ imageLabelGap: '28px', imageLabelGapTablet: '22px' }],
        },
    }).settings;

    assert.equal(settings.productLevelStyles[0].contentGap, '28px');
    assert.equal(settings.productLevelStyles[0].contentGapTablet, '22px');
});

test('thumbnail padding uses responsive four-side values', () => {
    const { definition } = loadDefinition();
    const settings = definition.normalize({
        id: 'lead-image-padding-1',
        type: 'product_lead_form',
        settings: { productLevelStyles: [{ imagePadding: '4px 8px' }] },
    }).settings;

    assert.deepEqual([
        settings.productLevelStyles[0].imagePaddingTop,
        settings.productLevelStyles[0].imagePaddingRight,
        settings.productLevelStyles[0].imagePaddingBottom,
        settings.productLevelStyles[0].imagePaddingLeft,
    ], ['4px', '8px', '4px', '8px']);
});

test('legacy custom card height stays fixed while the untouched default becomes auto', () => {
    const { definition } = loadDefinition();
    const normalized = definition.normalize({
        id: 'lead-1',
        type: 'product_lead_form',
        settings: {
            productLevelStyles: [{ cardHeight: '280px' }],
        },
    }).settings.productLevelStyles[0];

    assert.equal(normalized.cardHeightMode, 'fixed');
    assert.equal(definition.normalize({ id: 'lead-2', type: 'product_lead_form', settings: {} }).settings.productLevelStyles[0].cardHeightMode, 'auto');
});

test('invalid query values fall back to configured defaults and then first active descendants', () => {
    const { definition, logic } = loadDefinition();
    const settings = definition.normalize({
        id: 'lead-1',
        type: 'product_lead_form',
        settings: {
            productLevels: [
                { key: 'model', defaultNodeId: 'model-s5' },
                { key: 'type', defaultNodeId: '' },
                { key: 'variant', defaultNodeId: 'variant-short' },
            ],
        },
    }).settings;
    const selection = logic.resolveSelection(nodes, settings, { model: 'missing' });

    assert.deepEqual(clone(selection.ids), ['model-s5', 'type-lux', 'variant-short']);
    assert.deepEqual(clone(selection.invalidQueryKeys), ['model']);
});

test('query entries use configured keys while inherited metadata prefers the deepest selected node', () => {
    const { definition, logic } = loadDefinition();
    const settings = definition.normalize({
        id: 'lead-1',
        type: 'product_lead_form',
        settings: {
            productLevels: [
                { key: 'model', queryKey: 'car' },
                { key: 'type', queryKey: 'trim' },
                { key: 'variant', queryKey: 'edition' },
            ],
        },
    }).settings;
    const selection = logic.resolveSelection(nodes, settings, { edition: 'long-range' });

    assert.deepEqual(clone(logic.queryEntries(selection, settings)), [
        ['car', 'mgs5ev'],
        ['trim', 'luxury'],
        ['edition', 'long-range'],
    ]);
    assert.deepEqual(clone(logic.inheritedMeta(selection.nodes)), {
        thumbnailUrl: '/s5-thumb.jpg',
        imageUrl: '/s5-luxury.jpg',
        description: 'Because Everyone Matters',
        detailUrl: '/models/mgs5ev',
        detailLabel: 'Explore Long Range',
    });
});

test('Product Lead Form runtime owns its registry key and never binds generic Form roots', () => {
    const source = fs.readFileSync(path.join(moduleRoot, 'runtime.js'), 'utf8');
    const selectors = [];
    const scope = {
        querySelectorAll(selector) {
            selectors.push(selector);
            return [];
        },
    };
    const context = vm.createContext({
        console,
        URL,
        URLSearchParams,
        FormData: class FormData {},
        CustomEvent: class CustomEvent {},
        fetch: async () => ({ ok: true, json: async () => ({}) }),
        document: { readyState: 'loading', addEventListener() {} },
        window: {
            PageBuilderElementorV24Runtime: {},
            PageBuilderElementorV24ModuleRuntimes: {},
            location: { href: 'https://example.com/page', assign() {} },
            history: { replaceState() {} },
            addEventListener() {},
        },
    });

    vm.runInContext(source, context);

    assert.equal(typeof context.window.PageBuilderElementorV24ModuleRuntimes.product_lead_form?.init, 'function');
    assert.equal(context.window.PageBuilderElementorV24ModuleRuntimes.form, undefined);
    context.window.PageBuilderElementorV24ModuleRuntimes.product_lead_form.init(scope);
    assert.deepEqual(selectors, ['[data-product-lead-form]']);
});

test('legacy Form runtime excludes Product Lead Form-owned form elements', () => {
    const source = fs.readFileSync(path.join(root, 'resources/pagebuilder_elementor_v24/modules/widgets/pro/form/runtime.js'), 'utf8');
    const selectors = [];
    const context = vm.createContext({
        console,
        FormData: class FormData {},
        CustomEvent: class CustomEvent {},
        fetch: async () => ({ ok: true, json: async () => ({}) }),
        document: { readyState: 'loading', addEventListener() {} },
        window: {
            PageBuilderElementorV24Runtime: {},
            PageBuilderElementorV24ModuleRuntimes: {},
            location: { assign() {} },
        },
    });
    vm.runInContext(source, context);
    context.window.PageBuilderElementorV24ModuleRuntimes.form.init({
        querySelectorAll(selector) { selectors.push(selector); return []; },
    });
    assert.deepEqual(selectors, ['[data-pro-form]:not([data-product-lead-form])']);
});

test('frontend runtime resolves standalone child queries and replaces only configured query keys', () => {
    const source = fs.readFileSync(path.join(moduleRoot, 'runtime.js'), 'utf8');
    const historyCalls = [];
    const context = vm.createContext({
        console,
        URL,
        URLSearchParams,
        FormData: class FormData {},
        CustomEvent: class CustomEvent {},
        fetch: async () => ({ ok: true, json: async () => ({}) }),
        document: { readyState: 'loading', addEventListener() {} },
        window: {
            PageBuilderElementorV24Runtime: {},
            PageBuilderElementorV24ModuleRuntimes: {},
            location: { href: 'https://example.com/test-drive?variant=long-range&utm_source=qa#form' },
            history: { replaceState(_state, _title, url) { historyCalls.push(String(url)); } },
            addEventListener() {},
        },
    });
    vm.runInContext(source, context);
    const runtime = context.window.PageBuilderElementorV24ModuleRuntimes.product_lead_form;
    const settings = {
        productLevelCount: 3,
        productLevels: [
            { key: 'model', queryKey: 'model', defaultNodeId: '' },
            { key: 'type', queryKey: 'type', defaultNodeId: '' },
            { key: 'variant', queryKey: 'variant', defaultNodeId: '' },
        ],
    };

    const selection = runtime.resolveProductSelection(nodes, settings, { variant: 'long-range' });
    assert.deepEqual(JSON.parse(JSON.stringify(selection.ids)), ['model-s5', 'type-lux', 'variant-long']);
    runtime.updateProductQuery(selection, settings);
    assert.equal(historyCalls.length, 1);
    assert.equal(historyCalls[0], 'https://example.com/test-drive?variant=long-range&utm_source=qa&model=mgs5ev&type=luxury#form');
});

test('Product Lead Form Canvas and Settings compile with product dataset, selector, media, and style controls', () => {
    for (const filename of ['Canvas.vue', 'Settings.vue']) {
        const source = fs.readFileSync(path.join(moduleRoot, filename), 'utf8');
        const parsed = parse(source, { filename });
        assert.deepEqual(parsed.errors, [], `${filename} must parse`);
        const compiled = compileTemplate({
            id: `product-lead-${filename}`,
            filename,
            source: parsed.descriptor.template.content,
        });
        assert.deepEqual(compiled.errors, [], `${filename} template must compile`);
    }

    const canvas = fs.readFileSync(path.join(moduleRoot, 'Canvas.vue'), 'utf8');
    for (const marker of [
        'pb-product-lead__selectors',
        'role="radiogroup"',
        'pb-product-lead__media',
        'productSelectionValid',
        'productLevelOptions',
        'productMediaPosition',
        'productTitlePlacement',
        'productTitleAlign',
        'productTitleGap',
        'productFormVerticalAlign',
        'pb-product-lead__form-title',
        'data-title-placement',
        'data-pb-interactive="true"',
        'Add child items under',
        'pagebuilder:v24-form-datasets-loaded',
        'productDatasetRevision',
        'product-card-check',
        'imageShape',
        'imageLabelPlacement',
        'imageLabelGap',
        'is-preview-hover',
        'pagebuilder:v24-product-card-state-preview',
        'pb-product-lead__card-media',
        'selectedCheckIconSize',
        'productLevelCheckIconStyle',
        'cardHeightMode',
    ]) assert.ok(canvas.includes(marker), `Canvas must include ${marker}`);

    assert.match(canvas, /class="pb-product-lead__card"[\s\S]*?data-pb-interactive="true"[\s\S]*?@click\.stop/);
    assert.match(canvas, /data-product-level-index[\s\S]*?:disabled="!productLevelOptions\(levelIndex\)\.length"/);
    assert.match(canvas, /<Teleport[\s\S]*?to="\.webpage-frame"/);
    assert.match(canvas, /\['toast',\s*'modal'\]\.includes\(formMessageDisplay\)/);
    assert.match(canvas, /\['basic',\s*'above-form'\]\.includes\(formMessageDisplay\)/);

    const settings = fs.readFileSync(path.join(moduleRoot, 'Settings.vue'), 'utf8');
    for (const marker of [
        'Data Source',
        'Selector & Levels',
        'Product Presentation',
        'Product Title Placement',
        'Form Structure',
        'Submit Button',
        'Submit Actions',
        'Multi-step',
        'Submission Messages',
        'Form Identity & Validation',
        'Overall Layout',
        'Product Detail Media',
        'Product Title',
        'Title Alignment',
        'Title Gap',
        'Form Vertical Alignment',
        'Selector Cards',
        'Thumbnail Source',
        'Main Image Source',
        'Normal',
        'Hover',
        'Selected',
        'productLevelStyles',
        'Card Width',
        'Card Height',
        'Card Padding',
        'Card Margin',
        'Card Alignment',
        'Selected Border',
        'Selected Background',
        'Hover Border Width',
        'Selected Border Width',
        'Show Selected Check',
        'Selected Check Position',
        'Selected Check Size',
        'Selected Check Color',
        'Selected Check Background',
        'Selected Indicator',
        'Image Shape',
        'Label Position',
        'Content Gap',
        'Selected Check Icon Size',
        'Card Height Mode',
        'editor.chooseMedia',
    ]) assert.ok(settings.includes(marker), `Settings must include ${marker}`);

    const frontend = fs.readFileSync(path.join(moduleRoot, 'frontend.blade.php'), 'utf8');
    for (const marker of ['product-card-check', 'pb-product-lead__card-media', '--product-card-width', '--product-card-padding', '--product-card-label-gap', '--product-card-border-width-hover', '--product-card-border-width-selected', '--product-card-check-position', '--product-card-image-radius', '--product-card-check-icon-size', '$productTitlePlacement', '$productTitleAlign', '$productTitleGap', '$productFormVerticalAlign', 'pb-product-lead__form-title', 'data-title-placement', '--product-form-vertical-align']) {
        assert.ok(frontend.includes(marker), `frontend must include ${marker}`);
    }

    for (const manualStyleControl of [
        'Card Width',
        'Card Width Tablet',
        'Card Width Mobile',
        'Card Min Width',
        'Card Height',
        'Card Padding',
        'Card Margin',
        'Content Gap',
        'Image Width',
        'Image Height',
        'Thumbnail Padding',
        'Image Radius',
        'Image Border Width',
        'Card Border Width',
        'Card Radius',
        'Box Shadow',
        'Normal Shadow',
        'Hover Shadow',
        'Selected Shadow',
        'Selected Check Size',
        'Selected Check Offset',
        'Selected Check Radius',
    ]) {
        assert.doesNotMatch(settings, new RegExp(`<text-control label="${manualStyleControl}"`), `${manualStyleControl} must use a standard style control`);
    }
    assert.match(settings, /<size-control label="Card Width"[\s\S]*?:target="item"/);
    assert.match(settings, /<sides-control label="Card Padding"[\s\S]*?:target="item"/);
    assert.match(settings, /<sides-control label="Thumbnail Padding" base="imagePadding"[\s\S]*?:target="item"/);
    assert.doesNotMatch(settings, /<size-control label="Label Gap"/);
    assert.match(settings, /label="Editing Level"/);
    assert.match(settings, /<sides-control label="Card Margin"[\s\S]*?:target="item"/);
    assert.match(settings, /<responsive-select label="Card Alignment"[\s\S]*?:target="item"/);
    assert.match(settings, /<size-control label="Image Width"[^>]*:max="600"/);
    assert.match(settings, /<size-control label="Card Width"[^>]*:max="1200"/);
    assert.doesNotMatch(settings, /<size-control label="Image Width"[^>]*:max="100"/);
    assert.doesNotMatch(settings, /<size-control label="Card Width"[^>]*:max="100"/);
    assert.match(canvas, /border-width:var\(--product-card-border-width/);
    assert.match(frontend, /border-width:var\(--product-card-border-width/);
    assert.match(canvas, /\.pb-product-lead__card\.is-label-outside\s*\{[^}]*padding:var\(--product-card-padding/);
    assert.match(frontend, /\.pb-product-lead__card\.is-label-outside\s*\{[^}]*padding:var\(--product-card-padding/);
    assert.match(canvas, /\.pb-product-lead__card-media\s*\{[^}]*overflow:hidden/);
    assert.match(frontend, /\.pb-product-lead__card-media\s*\{[^}]*overflow:hidden/);
    assert.match(canvas, /aspect-ratio:1\s*\/\s*1/);
    assert.match(frontend, /aspect-ratio:1\s*\/\s*1/);
    assert.match(canvas, /--product-card-image-padding/);
    assert.match(frontend, /--product-card-image-padding/);
    assert.match(canvas, /\.pb-product-lead__card-media\s*\{[^}]*padding:var\(--product-card-image-padding/);
    assert.match(frontend, /\.pb-product-lead__card-media\s*\{[^}]*padding:var\(--product-card-image-padding/);
    assert.match(canvas, /\.pb-product-lead__card-media img\s*\{[^}]*object-fit:var\(--product-card-image-fit/);
    assert.match(frontend, /\.pb-product-lead__card-media img\s*\{[^}]*object-fit:var\(--product-card-image-fit/);
    assert.match(canvas, /productTitlePlacement === 'media-above'[\s\S]*?productTitlePlacement === 'media-below'/);
    assert.match(canvas, /productTitlePlacement === 'form-above'[\s\S]*?pb-product-lead__form-title/);
    assert.match(frontend, /data-title-placement="\{\{ \$productTitlePlacement \}\}"/);
    assert.match(frontend, /pb-product-lead__form-title[\s\S]*?data-product-title/);
});
