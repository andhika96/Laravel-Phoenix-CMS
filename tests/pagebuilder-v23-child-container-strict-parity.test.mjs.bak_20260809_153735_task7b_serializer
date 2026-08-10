import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';

const app = fs.readFileSync(path.resolve('public/js/pagebuilder_elementor_v23/app.js'), 'utf8');

function loadChildContainerHelpers() {
    const block = app.match(/\/\/ V23_CHILD_CONTAINER_HELPERS_START([\s\S]*?)\/\/ V23_CHILD_CONTAINER_HELPERS_END/)?.[1];
    assert.ok(block, 'child Container helpers should exist');
    const context = { structuredClone: globalThis.structuredClone };
    vm.runInNewContext(`${block}\nthis.api = { createLegacyContainerMigrationState, claimCanonicalNodeId, migrateLegacyContainerColumns, createChildContainerNode, presetChildContainers, appendChildContainer, canMoveNodeIntoContainer, responsiveContainerWidth, applyAdjacentContainerWidths };`, context);
    return context.api;
}

function loadLegacyContainerSnapshotNormalizer() {
    const block = app.match(/\/\/ V23_CHILD_CONTAINER_HELPERS_START([\s\S]*?)\/\/ V23_CHILD_CONTAINER_HELPERS_END/)?.[1];
    assert.ok(block, 'child Container helpers should exist');
    const context = { structuredClone: globalThis.structuredClone };
    vm.runInNewContext(`${block}\nthis.normalizeLegacyContainerSnapshot = normalizeLegacyContainerSnapshot;`, context);
    return context.normalizeLegacyContainerSnapshot;
}

function containerFactory() {
    return {
        id: '',
        type: 'container',
        settings: {
            displayType: 'flex',
            contentWidth: 'full',
            containerWidth: '100%',
            containerWidthTablet: '',
            containerWidthMobile: '',
        },
        children: [],
    };
}

test('a two-column preset creates child Containers and never columns', () => {
    const api = loadChildContainerHelpers();
    const children = api.presetChildContainers(containerFactory, { cols: 2, flexWidths: ['33%', '67%'] });

    assert.equal(children.length, 2);
    assert.deepEqual(Array.from(children, (child) => child.type), ['container', 'container']);
    assert.deepEqual(Array.from(children, (child) => child.settings.direction), ['column', 'column']);
    assert.deepEqual(Array.from(children, (child) => child.settings.containerWidth), ['33%', '67%']);
    assert.ok(children.every((child) => Array.isArray(child.children) && !Object.hasOwn(child, 'columns')));
});

test('Add Container appends one selectable canonical child', () => {
    const api = loadChildContainerHelpers();
    const parent = { id: 'parent', type: 'container', settings: { displayType: 'flex' }, children: [] };
    const first = api.appendChildContainer(parent, containerFactory);
    const child = api.appendChildContainer(parent, containerFactory);

    assert.equal(parent.children.length, 2);
    assert.equal(parent.children[0], first);
    assert.equal(parent.children[1], child);
    assert.deepEqual(parent.children.map((entry) => entry.settings.containerWidth), ['50%', '50%']);
    assert.equal(child.type, 'container');
    assert.equal(Object.hasOwn(child, 'columns'), false);
});

test('legacy columns become real child Containers without losing nested widgets', () => {
    const api = loadChildContainerHelpers();
    const source = [{
        id: 'parent', type: 'container', settings: { displayType: 'flex' }, children: [],
        columns: [
            { id: 'left', flexBasis: '33%', flexBasisTablet: '45%', children: [{ id: 'heading-a', type: 'heading', settings: { text: 'A' } }] },
            { id: 'right', flexBasis: '67%', children: [{ id: 'heading-b', type: 'heading', settings: { text: 'B' } }] },
        ],
    }];
    const state = api.createLegacyContainerMigrationState(source);
    const node = structuredClone(source[0]);

    assert.equal(api.migrateLegacyContainerColumns(node, state, containerFactory), true);
    assert.equal(Object.hasOwn(node, 'columns'), false);
    assert.deepEqual(node.children.map((child) => child.id), ['left', 'right']);
    assert.deepEqual(node.children.map((child) => child.settings.containerWidth), ['33%', '67%']);
    assert.equal(node.children[0].settings.containerWidthTablet, '45%');
    assert.deepEqual(node.children.flatMap((child) => child.children.map((nested) => nested.id)), ['heading-a', 'heading-b']);
    assert.equal(api.migrateLegacyContainerColumns(node, state, containerFactory), false);
    assert.equal(node.children.length, 2);
});

test('duplicate or malformed legacy column ids are repaired deterministically', () => {
    const api = loadChildContainerHelpers();
    const source = [{
        id: 'parent', type: 'container', settings: {}, children: [{ id: 'taken', type: 'heading', settings: {} }],
        columns: [
            { id: 'taken', children: [{ id: 'one', type: 'heading', settings: {} }] },
            { id: 'taken', children: [{ id: 'two', type: 'heading', settings: {} }] },
            { id: '', children: [null, { id: 'three', type: 'heading', settings: {} }, 'invalid'] },
        ],
    }];
    const state = api.createLegacyContainerMigrationState(source);
    const node = structuredClone(source[0]);

    api.migrateLegacyContainerColumns(node, state, containerFactory);

    assert.deepEqual(node.children.slice(1).map((child) => child.id), [
        'parent-child-container-1',
        'parent-child-container-2',
        'parent-child-container-3',
    ]);
    assert.deepEqual(node.children.slice(1).map((child) => child.children.map((nested) => nested.id)), [['one'], ['two'], ['three']]);
});

test('unknown node types remain untouched by the Container adapter', () => {
    const api = loadChildContainerHelpers();
    const node = { id: 'unknown', type: 'future_widget', settings: { mode: 'x' }, columns: [{ id: 'opaque', children: [] }] };
    const state = api.createLegacyContainerMigrationState([node]);

    assert.equal(api.migrateLegacyContainerColumns(node, state, containerFactory), false);
    assert.equal(node.type, 'future_widget');
    assert.equal(node.settings.mode, 'x');
    assert.equal(node.columns[0].id, 'opaque');
});

test('generated child Container ids never steal an existing node id', () => {
    const api = loadChildContainerHelpers();
    const source = [{
        id: 'parent', type: 'container', settings: {},
        children: [{ id: 'parent-child-container-1', type: 'heading', settings: {} }],
        columns: [{ id: '', children: [] }],
    }];
    const state = api.createLegacyContainerMigrationState(source);
    const node = structuredClone(source[0]);

    api.migrateLegacyContainerColumns(node, state, containerFactory);

    assert.equal(node.children[0].id, 'parent-child-container-1');
    assert.equal(node.children[1].id, 'parent-child-container-1-2');
});

test('canonical node IDs stay stable when a legacy snapshot is normalized twice', () => {
    const normalizeLegacyContainerSnapshot = loadLegacyContainerSnapshotNormalizer();
    const source = [{
        id: 'parent', type: 'container', settings: {}, children: [],
        columns: [{ id: 'child', children: [{ id: 'heading', type: 'heading', settings: {} }] }],
    }];

    const first = normalizeLegacyContainerSnapshot(source, containerFactory).nodes;
    const second = normalizeLegacyContainerSnapshot(first, containerFactory).nodes;

    assert.deepEqual(first, second);
    assert.deepEqual(second[0].children.map((child) => child.id), ['child']);
    assert.equal(second[0].children[0].children[0].id, 'heading');
    assert.equal(Object.hasOwn(second[0], 'columns'), false);
});

test('a Container cannot be dropped into itself or its descendant', () => {
    const api = loadChildContainerHelpers();
    const tree = { id: 'parent', type: 'container', children: [{ id: 'child', type: 'container', children: [] }] };

    assert.equal(api.canMoveNodeIntoContainer(tree, 'parent'), false);
    assert.equal(api.canMoveNodeIntoContainer(tree, 'child'), false);
    assert.equal(api.canMoveNodeIntoContainer(tree, 'other'), true);
});

test('responsive width inherits from the nearest larger device', () => {
    const api = loadChildContainerHelpers();
    const settings = { containerWidth: '60%', containerWidthTablet: '55%', containerWidthMobile: '' };

    assert.equal(api.responsiveContainerWidth(settings, 'desktop'), '60%');
    assert.equal(api.responsiveContainerWidth(settings, 'tablet'), '55%');
    assert.equal(api.responsiveContainerWidth(settings, 'mobile'), '55%');
});

test('edge resize writes only the active device and preserves the pair total', () => {
    const api = loadChildContainerHelpers();
    const children = [
        containerFactory(),
        containerFactory(),
        containerFactory(),
    ];
    children[0].settings.containerWidth = '25%';
    children[1].settings.containerWidth = '50%';
    children[2].settings.containerWidth = '25%';

    const result = api.applyAdjacentContainerWidths(children, 0, 35, 'tablet', { minPercent: 8 });

    assert.deepEqual(JSON.parse(JSON.stringify(result)), { current: 35, next: 40, total: 75 });
    assert.equal(children[0].settings.containerWidth, '25%');
    assert.equal(children[1].settings.containerWidth, '50%');
    assert.equal(children[0].settings.containerWidthTablet, '35%');
    assert.equal(children[1].settings.containerWidthTablet, '40%');
    assert.equal(children[2].settings.containerWidthTablet, '');
});

test('edge resize clamps a Container to the pair minimum width', () => {
    const api = loadChildContainerHelpers();
    const children = [containerFactory(), containerFactory()];
    children[0].settings.containerWidth = '25%';
    children[1].settings.containerWidth = '50%';

    assert.deepEqual(
        JSON.parse(JSON.stringify(api.applyAdjacentContainerWidths(children, 0, 2, 'mobile', { minPercent: 8 }))),
        { current: 8, next: 67, total: 75 }
    );
    assert.equal(children[0].settings.containerWidthMobile, '8%');
    assert.equal(children[1].settings.containerWidthMobile, '67%');
});

test('child Container edge resizing uses its responsive pair and one history snapshot', () => {
    const resizeBody = app.match(/function startContainerEdgeResize\(event, parent, index\) \{([\s\S]*?)\n\s*const selectedNode =/)?.[1];

    assert.ok(resizeBody, 'Container edge resize controller should exist');
    assert.match(app, /showContainerEdgeResizeHandle\(\) \{/);
    assert.match(app, /class="pb-container-edge-resizer"/);
    assert.match(app, /onStartContainerEdgeResize\(\$event, parentNode, siblingIndex\)/);
    assert.match(resizeBody, /containerPercentages\(children, responsiveDevice\.value\)/);
    assert.match(resizeBody, /applyAdjacentContainerWidths\(children, index, requested, responsiveDevice\.value, \{ minPercent \}\)/);
    assert.match(resizeBody, /suppressHistory\.value = true/);
    assert.match(resizeBody, /suppressHistory\.value = false/);
    assert.match(resizeBody, /const stop = \(\) => \{[\s\S]*?snap\(\);/);
    assert.doesNotMatch(resizeBody.match(/const onMove = \(moveEvent\) => \{([\s\S]*?)\n\s*\};/)?.[1] || '', /snap\(\)/);
});

test('Container edge resizer uses the dedicated child Container selector', () => {
    const css = fs.readFileSync(path.resolve('public/assets/css/pagebuilder_elementor_v23.css'), 'utf8');

    assert.match(css, /\.pb-container-edge-resizer\s*\{/);
    assert.match(css, /cursor:\s*col-resize/);
    assert.match(css, /\.pb-container-edge-resizer i\s*\{/);
});

test('Grid and Flexbox retain canonical child Containers without pseudo-column synchronization', () => {
	assert.doesNotMatch(app, /responsiveColumnsCache|function syncCols\(|function syncGridColumnsForDevice\(|syncAllGridCellsForDevice|scheduleResponsiveGridSync/);
	assert.doesNotMatch(app, /function addContainerFlexColumn\(|function startColumnResize\(|function applyColumnPairWidths\(|function legacyColumnDropAdapter\(|function rerouteTabsDropToNestedColumn\(|function rerouteAccordionDropToNestedColumn\(/);
});
