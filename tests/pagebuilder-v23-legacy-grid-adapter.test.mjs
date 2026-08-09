import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const app = fs.readFileSync(path.resolve('public/js/pagebuilder_elementor_v23/app.js'), 'utf8');

function containerFactory() {
    return {
        id: '',
        type: 'container',
        settings: {
            displayType: 'flex',
            containerWidth: '100%',
            containerWidthTablet: '',
            containerWidthMobile: '',
        },
        children: [],
    };
}

function loadLegacyGridAdapter() {
    const helpers = app.match(/\/\/ V23_CHILD_CONTAINER_HELPERS_START([\s\S]*?)\/\/ V23_CHILD_CONTAINER_HELPERS_END/)?.[1];
    const converter = app.match(/function convertGridNodeToContainer[\s\S]*?(?=\n\tfunction makeNode)/)?.[0];
    const setters = app.match(/function setContainerGridColumnsValue\(node, next\) \{[\s\S]*?(?=\s*function syncContainerGap)/)?.[0];
    assert.ok(helpers, 'child Container helpers should exist');
    assert.ok(converter, 'Grid adapter should exist');
    assert.ok(setters, 'Grid track setters should exist');

    const context = { Map, WeakSet, structuredClone: globalThis.structuredClone, containerFactory };
    vm.runInNewContext(`
        const clamp = (value, min, max) => Math.min(Math.max(Number(value), min), max);
        const isGrid = (type) => type === 'grid' || type === 'row_grid';
        const jclone = (value) => structuredClone(value);
        const widgetRegistry = { get: () => ({ defaults: () => containerFactory() }) };
        const makeNode = () => containerFactory();
        const legacyGridMigrationStates = new WeakMap();
        const responsiveDevice = { value: 'tablet' };
        const normalizeResponsiveDevice = (device) => ['tablet', 'mobile'].includes(device) ? device : 'desktop';
        const responsiveKey = (base, device) => device === 'tablet' ? base + 'Tablet' : (device === 'mobile' ? base + 'Mobile' : base);
        const setContainerResponsiveSetting = (settings, base, value) => { settings[responsiveKey(base, responsiveDevice.value)] = value; };
        ${helpers}
        ${converter}
        ${setters}
        this.api = { normalizeLegacyContainerSnapshot, setContainerGridColumnsValue, setContainerGridRowsValue };
    `, context);
    return context.api;
}

test('legacy Grid and Row Grid load as direct child Containers with stable column IDs and order', () => {
    const api = loadLegacyGridAdapter();
    const source = [
        {
            id: 'legacy-grid', type: 'grid', settings: { columns: 2, gridRows: '2' }, children: [],
            columns: [
                { id: 'grid-left', flexBasis: '40%', children: [{ id: 'heading-a', type: 'heading', settings: { text: 'A' } }] },
                { id: 'grid-right', flexBasis: '60%', children: [{ id: 'heading-b', type: 'heading', settings: { text: 'B' } }] },
            ],
        },
        {
            id: 'legacy-row', type: 'row_grid', settings: { columns: 3 }, children: [],
            columns: [
                { id: 'row-one', children: [{ id: 'widget-one', type: 'button', settings: {} }] },
                { id: 'row-two', children: [{ id: 'widget-two', type: 'image', settings: {} }] },
            ],
        },
    ];

    const { nodes, migrationState } = api.normalizeLegacyContainerSnapshot(source, containerFactory);

    assert.equal(migrationState.migrated, true);
    assert.deepEqual(nodes.map((node) => node.type), ['container', 'container']);
    assert.deepEqual(nodes.map((node) => node.settings.displayType), ['grid', 'grid']);
    assert.deepEqual(nodes[0].children.map((child) => child.id), ['grid-left', 'grid-right']);
    assert.deepEqual(nodes[1].children.map((child) => child.id), ['row-one', 'row-two']);
    assert.deepEqual(nodes.flatMap((node) => node.children.flatMap((child) => child.children.map((nested) => nested.id))), ['heading-a', 'heading-b', 'widget-one', 'widget-two']);
    assert.equal(Object.hasOwn(nodes[0], 'columns'), false);
    assert.equal(Object.hasOwn(nodes[1], 'columns'), false);
    assert.equal(source[0].type, 'grid');
    assert.equal(Object.hasOwn(source[0], 'columns'), true);
});

test('Grid track setters update settings without changing migrated child identity or order', () => {
    const api = loadLegacyGridAdapter();
    const { nodes } = api.normalizeLegacyContainerSnapshot([{
        id: 'legacy-grid', type: 'grid', settings: { columns: 2 }, children: [],
        columns: [
            { id: 'grid-left', children: [{ id: 'heading-a', type: 'heading', settings: {} }] },
            { id: 'grid-right', children: [{ id: 'heading-b', type: 'heading', settings: {} }] },
        ],
    }], containerFactory);
    const node = nodes[0];
    const beforeIds = node.children.map((child) => child.id);
    const beforeNestedIds = node.children.map((child) => child.children.map((nested) => nested.id));

    api.setContainerGridColumnsValue(node, 6);
    api.setContainerGridRowsValue(node, 4);

    assert.equal(node.settings.gridColumnsTablet, 6);
    assert.equal(node.settings.gridRowsTablet, '4');
    assert.deepEqual(node.children.map((child) => child.id), beforeIds);
    assert.deepEqual(node.children.map((child) => child.children.map((nested) => nested.id)), beforeNestedIds);
    assert.equal(Object.hasOwn(node, 'columns'), false);
});

test('legacy Grid repairs duplicate or malformed column IDs without colliding with direct children', () => {
    const api = loadLegacyGridAdapter();
    const { nodes } = api.normalizeLegacyContainerSnapshot([{
        id: 'legacy-grid', type: 'grid', settings: {},
        children: [{ id: 'taken', type: 'heading', settings: {} }],
        columns: [
            { id: 'taken', children: [{ id: 'one', type: 'heading', settings: {} }] },
            { id: 'taken', children: [{ id: 'two', type: 'heading', settings: {} }] },
            { id: '', children: [{ id: 'three', type: 'heading', settings: {} }] },
        ],
    }], containerFactory);
    const node = nodes[0];

    assert.deepEqual(node.children.map((child) => child.id), [
        'taken',
        'legacy-grid-child-container-1',
        'legacy-grid-child-container-2',
        'legacy-grid-child-container-3',
    ]);
    assert.deepEqual(node.children.slice(1).map((child) => child.children.map((nested) => nested.id)), [['one'], ['two'], ['three']]);
    assert.equal(Object.hasOwn(node, 'columns'), false);
});
