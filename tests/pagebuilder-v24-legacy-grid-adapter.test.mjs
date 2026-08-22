import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';
import { pageBuilderV24CapabilityPrelude } from './helpers/pagebuilder-v24-module-source.mjs';

const app = fs.readFileSync(path.resolve('public/js/pagebuilder_elementor_v24/app.js'), 'utf8');

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
    const helpers = app.match(/\/\/ V24_CHILD_CONTAINER_HELPERS_START([\s\S]*?)\/\/ V24_CHILD_CONTAINER_HELPERS_END/)?.[1];
    assert.ok(helpers, 'Grid and Flex migration helpers should exist');

    const context = { Map, WeakSet, structuredClone: globalThis.structuredClone, containerFactory };
    vm.runInNewContext(`
        ${pageBuilderV24CapabilityPrelude()}
        ${helpers}
        this.api = { normalizeLegacyContainerSnapshot, reconcileGridColumns };
    `, context);
    return context.api;
}

test('legacy Grid and Row Grid keep conceptual columns with stable content order', () => {
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
    assert.deepEqual(nodes.map((node) => node.type), ['grid', 'row_grid']);
    assert.deepEqual(nodes[0].columns.slice(0, 2).map((column) => column.id), ['grid-left', 'grid-right']);
    assert.deepEqual(nodes[1].columns.slice(0, 2).map((column) => column.id), ['row-one', 'row-two']);
    assert.equal(nodes[0].columns.length, 4, '2 columns x 2 rows should materialize four conceptual cells');
    assert.equal(nodes[1].columns.length, 3, 'three columns should materialize three conceptual cells');
    assert.deepEqual(nodes.flatMap((node) => node.columns.flatMap((column) => column.children.map((nested) => nested.id))), ['heading-a', 'heading-b', 'widget-one', 'widget-two']);
    assert.equal(nodes[0].children.length, 0);
    assert.equal(nodes[1].children.length, 0);
    assert.equal(source[0].type, 'grid');
    assert.equal(Object.hasOwn(source[0], 'columns'), true);
});

test('Grid track reconciliation changes cells without changing existing content identity or order', () => {
    const api = loadLegacyGridAdapter();
    const { nodes } = api.normalizeLegacyContainerSnapshot([{
        id: 'legacy-grid', type: 'grid', settings: { columns: 2 }, children: [],
        columns: [
            { id: 'grid-left', children: [{ id: 'heading-a', type: 'heading', settings: {} }] },
            { id: 'grid-right', children: [{ id: 'heading-b', type: 'heading', settings: {} }] },
        ],
    }], containerFactory);
    const node = nodes[0];
    const beforeIds = node.columns.map((column) => column.id);
    const beforeNestedIds = node.columns.map((column) => column.children.map((nested) => nested.id));

    let nextId = 0;
    api.reconcileGridColumns(node, 6, 4, () => `generated-cell-${++nextId}`);

    assert.equal(node.columns.length, 24);
    assert.deepEqual(node.columns.slice(0, 2).map((column) => column.id), beforeIds);
    assert.deepEqual(node.columns.slice(0, 2).map((column) => column.children.map((nested) => nested.id)), beforeNestedIds);
    assert.ok(node.columns.slice(2).every((column) => !Object.hasOwn(column, 'type') && column.children.length === 0));
});

test('legacy Grid repairs duplicate or malformed cell IDs without colliding with node IDs', () => {
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

    assert.equal(node.type, 'grid');
    assert.equal(node.children.length, 0);
    assert.equal(node.columns.length, 3);
    assert.equal(new Set(node.columns.map((column) => column.id)).size, 3);
    assert.ok(node.columns.every((column) => column.id !== 'taken'));
    assert.deepEqual(node.columns.map((column) => column.children.map((nested) => nested.id)), [['one', 'taken'], ['two'], ['three']]);
});
