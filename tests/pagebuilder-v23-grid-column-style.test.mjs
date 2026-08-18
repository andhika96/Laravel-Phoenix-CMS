import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const appPath = path.join(root, 'public/js/pagebuilder_elementor_v23/app.js');
const app = fs.readFileSync(appPath, 'utf8');
const gridSettings = fs.readFileSync(path.join(root, 'public/js/pagebuilder_elementor_v23/widgets/layout/grid/Settings.vue'), 'utf8');
const containerSettings = fs.readFileSync(path.join(root, 'public/js/pagebuilder_elementor_v23/widgets/layout/container/Settings.vue'), 'utf8');
const gridBlade = fs.readFileSync(path.join(root, 'resources/views/pagebuilder_elementor_v23/widgets/layout/grid.blade.php'), 'utf8');
const containerBlade = fs.readFileSync(path.join(root, 'resources/views/pagebuilder_elementor_v23/widgets/layout/container.blade.php'), 'utf8');

function extractBlock(source, startIndex) {
    const open = source.indexOf('{', startIndex);
    assert.notEqual(open, -1, 'function opening brace should exist');
    let depth = 0;
    let quote = '';
    let escaped = false;
    for (let index = open; index < source.length; index += 1) {
        const char = source[index];
        if (quote) {
            if (escaped) escaped = false;
            else if (char === '\\') escaped = true;
            else if (char === quote) quote = '';
            continue;
        }
        if (char === '"' || char === "'" || char === '`') {
            quote = char;
            continue;
        }
        if (char === '{') depth += 1;
        if (char === '}') {
            depth -= 1;
            if (depth === 0) return source.slice(open + 1, index);
        }
    }
    assert.fail('function closing brace should exist');
}

function namedFunction(name) {
    const start = app.indexOf(`function ${name}(`);
    assert.notEqual(start, -1, `${name} should exist`);
    const body = extractBlock(app, start);
    const signatureStart = app.indexOf('(', start);
    const signatureEnd = app.indexOf(')', signatureStart);
    return `function ${name}${app.slice(signatureStart, signatureEnd + 1)} {${body}}`;
}

function loadApi() {
    const names = [
        'gridSlotCount',
        'gridColumnStyleDefaults',
        'normalizeGridColumnStyle',
        'gridColumnResponsiveValue',
        'reconcileGridColumnStyles',
        'resolveGridColumnStyle',
        'resetGridColumnStyleOverrides',
        'gridColumnTrackRemovalImpact',
        'reconcileGridColumns',
        'removeGridColumnTrack',
    ];
    const context = vm.createContext({ structuredClone, console });
    vm.runInContext(`${names.map(namedFunction).join('\n')}\nthis.api = { ${names.join(', ')} };`, context);
    return context.api;
}

test('Grid column style defaults are sparse-safe and include the approved normal controls', () => {
    const api = loadApi();
    const defaults = api.gridColumnStyleDefaults();

    assert.equal(defaults.borderType, 'none');
    assert.equal(defaults.borderWidthTop, '0px');
    assert.equal(defaults.borderWidthRight, '0px');
    assert.equal(defaults.borderWidthBottom, '0px');
    assert.equal(defaults.borderWidthLeft, '0px');
    assert.equal(defaults.borderColor, 'transparent');
    assert.equal(defaults.borderRadiusTL, '0px');
    assert.equal(defaults.borderRadiusTR, '0px');
    assert.equal(defaults.borderRadiusBR, '0px');
    assert.equal(defaults.borderRadiusBL, '0px');
    assert.equal(defaults.bgType, 'none');
    assert.equal(defaults.bgOpacity, 1);
});

test('Grid column styles reconcile by visual track count without changing cell content', () => {
    const api = loadApi();
    const node = {
        columnStyles: [{ borderColor: '#111111' }],
        columns: [{ id: 'cell-1', children: [{ id: 'heading-1' }] }],
    };

    api.reconcileGridColumnStyles(node, 2);

    assert.equal(node.columnStyles.length, 2);
    assert.equal(node.columnStyles[0].borderColor, '#111111');
    assert.equal(node.columnStyles[1].borderType, 'none');
    assert.deepEqual(node.columns[0].children.map((child) => child.id), ['heading-1']);

    api.reconcileGridColumnStyles(node, 1);
    assert.equal(node.columnStyles.length, 1);
    assert.equal(node.columnStyles[0].borderColor, '#111111');
});

test('Cell overrides inherit per field and per responsive device from the track style', () => {
    const api = loadApi();
    const track = {
        borderType: 'solid',
        borderTypeTablet: 'dashed',
        borderColor: '#111111',
        borderColorTablet: '#222222',
        bgType: 'color',
        bgColor: '#eeeeee',
    };
    const cell = {
        borderColor: '#333333',
        borderColorMobile: '#444444',
        bgColor: '#fafafa',
    };

    const desktop = api.resolveGridColumnStyle(track, cell, 'desktop');
    const tablet = api.resolveGridColumnStyle(track, cell, 'tablet');
    const mobile = api.resolveGridColumnStyle(track, cell, 'mobile');

    assert.equal(desktop.borderType, 'solid');
    assert.equal(desktop.borderColor, '#333333');
    assert.equal(desktop.bgType, 'color');
    assert.equal(desktop.bgColor, '#fafafa');
    assert.equal(tablet.borderType, 'dashed');
    assert.equal(tablet.borderColor, '#333333');
    assert.equal(tablet.bgOpacity, 1);
    assert.equal(mobile.borderType, 'solid');
    assert.equal(mobile.borderColor, '#444444');
    assert.equal(mobile.bgType, 'color');
    assert.equal(mobile.bgColor, '#fafafa');
});

test('Resetting a cell style removes only the sparse override object', () => {
    const api = loadApi();
    const cell = { id: 'cell-1', children: [], styleOverrides: { borderColor: '#f00', bgType: 'color' } };

    api.resetGridColumnStyleOverrides(cell);

    assert.equal(Object.keys(cell.styleOverrides).length, 0);
    assert.equal(cell.id, 'cell-1');
    assert.deepEqual(cell.children, []);
});

test('Deleting a Grid track removes its matching default style and preserves remaining cell overrides', () => {
    const api = loadApi();
    const node = {
        type: 'grid',
        settings: { columns: 3, gridRows: '1' },
        columnStyles: [
            { borderColor: '#111111' },
            { borderColor: '#222222' },
            { borderColor: '#333333' },
        ],
        columns: [
            { id: 'cell-1', children: [] },
            { id: 'cell-2', children: [], styleOverrides: { bgType: 'color' } },
            { id: 'cell-3', children: [] },
        ],
    };

    assert.equal(api.removeGridColumnTrack(node, 1), true);

    assert.deepEqual(node.columnStyles.map((style) => style.borderColor), ['#111111', '#333333']);
    assert.deepEqual(node.columns.map((cell) => cell.id), ['cell-1', 'cell-3']);
    assert.equal(Object.keys(node.columns[1].styleOverrides).length, 0);
});

test('Grid settings expose expandable track and cell style targets for both Grid surfaces', () => {
    for (const source of [gridSettings, containerSettings]) {
        assert.match(source, /gridColumnTrackCells\(node, track\.index\)/);
        assert.match(source, /selectGridColumnStyleTarget\(node, track\.index\)/);
        assert.match(source, /selectGridColumnStyleTarget\(node, track\.index, cell\.cellIndex\)/);
        assert.match(source, /editor\.gridColumnStyleControls/);
    }
});

test('Grid cell renderers expose track and cell metadata and apply the effective style', () => {
    assert.match(app, /gridColumnCellStyle\(col, ci\)/);
    assert.match(app, /data-pb-grid-track/);
    assert.match(app, /data-pb-grid-cell/);
    assert.match(gridBlade, /data-pb-grid-track/);
    assert.match(gridBlade, /data-pb-grid-cell/);
    assert.match(containerBlade, /data-pb-grid-track/);
    assert.match(containerBlade, /data-pb-grid-cell/);
});
