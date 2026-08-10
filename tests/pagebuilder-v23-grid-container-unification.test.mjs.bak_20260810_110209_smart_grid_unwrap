import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/app.js'), 'utf8');
const containerSettings = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/widgets/layout/container/Settings.vue'), 'utf8');
const containerCanvas = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/widgets/layout/container/Canvas.vue'), 'utf8');
const renderNode = readFileSync(resolve(root, 'resources/views/pagebuilder_elementor_v23/partials/render_node.blade.php'), 'utf8');
const containerBlade = readFileSync(resolve(root, 'resources/views/pagebuilder_elementor_v23/widgets/layout/container.blade.php'), 'utf8');

test('legacy and nested Grid nodes retain Grid identity with conceptual columns', () => {
    assert.match(app, /function reconcileGridColumns\(node, columns, rows, createColumnId\)/);
    assert.match(app, /function ensureNodeGridColumns\(node\)/);
    assert.match(app, /if \(isGrid\(item\.type\)\) \{[\s\S]*?ensureNodeGridColumns\(item\);[\s\S]*?return;/);
    assert.match(app, /if \(isGrid\(c\.type\)\)[\s\S]*?reconcileGridColumns\(c, targetCols/);
    assert.doesNotMatch(app, /function convertGridNodeToContainer\(|convertGridNodeToContainer\(/);
});

test('Grid identity follows its Container display mode in the editor', () => {
    assert.match(app, /const isGridContainer = isCont\(node\.type\) && \(node\.settings\?\.displayType \|\| 'flex'\) === 'grid';/);
    assert.match(app, /const base = isGridContainer \? 'Grid' : baseNodeLabel/);
    assert.match(app, /nodeLabelIcon\(this\.node\)/);
    assert.match(app, /nodeLabelIcon\(selectedNode\)/);
});

test('Grid uses conceptual columns while Flexbox uses selectable child Containers', () => {
    assert.match(app, /function setContainerGridColumnsValue\(node, next\) \{[\s\S]*?node\.settings\[responsiveKey\('gridColumns', device\)\] = value;/);
    assert.match(app, /function setContainerGridRowsValue\(node, next\) \{[\s\S]*?setContainerResponsiveSetting\(node\.settings, 'gridRows', String\(value\)\);/);
    assert.match(app, /function onContainerDisplayTypeChange\(node\) \{[\s\S]*?if \(dt === 'flex'\)[\s\S]*?delete node\.columns;[\s\S]*?if \(dt === 'grid'\)[\s\S]*?ensureNodeGridColumns\(node\);/);
    assert.match(app, /v-for="\(col, ci\) in node\.columns"[\s\S]*?v-model="col\.children"/);
    assert.match(app, /v-else[\s\S]*?v-model="node\.children"/);
    assert.doesNotMatch(app, /responsiveColumnsCache|reconcileColumnsContent|function syncCols\(|function syncGridColumnsForDevice\(|scheduleResponsiveGridSync/);
    assert.doesNotMatch(containerCanvas, /node\.columns|pb-grid-col/);
});

test('Container Grid exposes the official Elementor control groups without legacy-only fields', () => {
    for (const label of [
        'Container Layout', 'Content Width', 'Width', 'Min Height', 'Grid Outline',
        'Columns', 'Rows', 'Gaps', 'Auto Flow', 'Justify Items', 'Align Items',
        'Additional Options', 'Overflow', 'HTML Tag', 'Background Overlay',
        'Border', 'Shape Divider', 'Motion Effects', 'Transform', 'Responsive',
        'Attributes', 'Custom CSS',
    ]) {
        assert.match(containerSettings, new RegExp(label.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')), `${label} should be present`);
    }

    assert.doesNotMatch(containerSettings, /<option value="dense">Dense<\/option>/);
    assert.doesNotMatch(containerSettings, />Grid Auto Height</);
    assert.doesNotMatch(containerSettings, />Grid Template Columns</);
});

test('Grid Additional Options match Elementor overflow and linked HTML tag behavior', () => {
    const additionalOptions = containerSettings.match(/<summary>Additional Options<\/summary>[\s\S]*?<\/details>/)?.[0] || '';
    assert.ok(additionalOptions, 'Additional Options section should be present');
    assert.match(additionalOptions, /<option value="default">Default<\/option>[\s\S]*?<option value="hidden">Hidden<\/option>[\s\S]*?<option value="auto">Auto<\/option>/);
    assert.doesNotMatch(additionalOptions, /<option value="visible">Visible<\/option>/);
    assert.doesNotMatch(additionalOptions, /<option value="scroll">Scroll<\/option>/);
    assert.match(additionalOptions, /<option value="a">A \(Link\)<\/option>/);
    assert.match(additionalOptions, /v-if="node\.settings\.htmlTag==='a'"[\s\S]*?v-model="node\.settings\.linkUrl"/);
    assert.match(containerCanvas, /const allowed = \[[^\]]*'a'/);
    assert.match(containerCanvas, /attrs\.href = this\.safeLinkUrl\(this\.settings\.linkUrl\);/);
    assert.match(renderNode, /'a'/);
    assert.match(containerBlade, /\$attrBag\['href'\]/);
});
