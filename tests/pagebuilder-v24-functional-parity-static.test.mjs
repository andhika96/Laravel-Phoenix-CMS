import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const v20 = readFileSync(resolve(root, 'public/js/pagebuilder_elementor/app.js'), 'utf8');
const v24 = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v24/app.js'), 'utf8');

function escapeRegExp(value) {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function assertBothContain(marker) {
    const pattern = new RegExp(escapeRegExp(marker));
    assert.match(v20, pattern, `v2.0 is missing the baseline marker: ${marker}`);
    assert.match(v24, pattern, `v2.4 lost the baseline marker: ${marker}`);
}

test('v2.4 retains the v2.0 editor action functions', () => {
    for (const marker of [
        'function undo()',
        'function redo()',
        'async function savePage()',
        'function onRootAdd',
        'function onAddContainer',
        'function showToolboxPanel',
        'function openCustomCssEditor',
        'function openCkFinder',
        'function chooseMedia',
        'function chooseMediaGallery',
        'function openIconLibrary',
        'function setResponsiveDevice',
    ]) {
        assertBothContain(marker);
    }
});

test('v2.4 retains modal state and root-canvas callback bindings', () => {
    for (const marker of [
        'v-if="showCssEditor"',
        '@click.self="closeCustomCssEditor"',
        '@click.self="closeTextEditorModal"',
        'v-if="showIconLibraryModal"',
        '@click.self="closeIconLibrary"',
        'v-if="modal.visible"',
        '@click.self="closeModal"',
        'v-model="rootNodes"',
        '@add="onRootAdd"',
        '@start="onDragStart"',
        '@end="onDragEnd"',
        '<BuilderNode',
        ':on-add-container="onAddContainer"',
        ':on-select="selectNode"',
        ':on-remove="removeNode"',
        ':on-duplicate="dupNode"',
        ':on-open-modal="openModal"',
        ':on-show-toolbox="showToolboxPanel"',
        ':on-toggle-accordion-item="toggleAccordionItem"',
    ]) {
        assertBothContain(marker);
    }
    assert.match(v20, /v-if="showTextEditorModal && selectedType==='text_editor' && selectedNode"/);
    assert.match(v24, /v-if="showTextEditorModal && selectedSupportsRichTextModal && selectedNode"/);
});

test('v2.4 keeps Flexbox child Containers and restores conceptual Grid columns', () => {
    for (const marker of [
        'function addContainerChild(node)',
        'function startContainerEdgeResize',
        'v-model="node.children"',
        'v-for="(col, ci) in node.columns"',
        'v-model="col.children"',
        "target.type === 'column'",
        'function onAddCol(',
        ':parent-node="node"',
        ':on-start-container-edge-resize="startContainerEdgeResize"',
    ]) {
        assert.match(v24, new RegExp(escapeRegExp(marker)));
    }
    assert.doesNotMatch(v24, /selectedColumnContext|:on-select-column="selectColumn"/);
    assert.doesNotMatch(v24, /responsiveColumnsCache|function syncCols\(|function syncGridColumnsForDevice\(|scheduleResponsiveGridSync/);
});

test('v2.4 exposes the page-settings state, actions, and anchored popover fields', () => {
    for (const marker of [
        'const pageSettingsOpen = ref(false);',
        'function openPageSettings()',
        'function closePageSettings()',
        'pageSettingsOpen, openPageSettings, closePageSettings',
        'class="page-settings-popover"',
        'v-model="pageName"',
        'v-model="pageStatus"',
        '{{ customCssSummary }}',
        '@click="openCustomCssEditor"',
    ]) {
        assert.match(v24, new RegExp(escapeRegExp(marker)), `v2.4 is missing the page-settings contract: ${marker}`);
    }

    assert.match(v24, /<button\b(?=[^>]*class="page-name")(?=[^>]*@click\.stop="openPageSettings")[^>]*>/);
});

test('v2.4 closes page settings on Escape, outside click, preview, and successful save', () => {
    assert.match(v24, /function handlePageSettingsKeydown\(event\)[\s\S]{0,500}event\.key === ['"]Escape['"][\s\S]{0,500}closePageSettings\(\)/);
    assert.match(v24, /document\.addEventListener\(['"]keydown['"], handlePageSettingsKeydown\)/);
    assert.match(v24, /document\.removeEventListener\(['"]keydown['"], handlePageSettingsKeydown\)/);
    assert.match(v24, /<div\b(?=[^>]*class="builder-app")(?=[^>]*@click="closePageSettings")[^>]*>/);
    assert.match(v24, /class="page-settings-popover"[^>]*@click\.stop/);
    assert.match(v24, /watch\(previewMode,[\s\S]{0,500}if \(enabled\) closePageSettings\(\)/);
    assert.match(v24, /saveState\.value = ['"]success['"];[\s\S]{0,500}closePageSettings\(\)/);
});

test('v2.4 toolbox cards add at the root when no targeted insert is active', () => {
	assert.match(v24, /function insertToolAtRoot\(toolDef\)[\s\S]{0,800}if \(isWgt\(item\.type\)\)[\s\S]{0,800}makeNode\(defaultContainerType\(\)\)[\s\S]{0,800}rootNodes\.value\.push\(container\)/);
	assert.match(v24, /function onToolboxItemClick\(toolDef\)[\s\S]{0,300}if \(pendingInsertTarget\.value\) return insertToolIntoPendingTarget\(toolDef, pendingInsertTarget\.value\);[\s\S]{0,300}return insertToolAtRoot\(toolDef\);/);
});
