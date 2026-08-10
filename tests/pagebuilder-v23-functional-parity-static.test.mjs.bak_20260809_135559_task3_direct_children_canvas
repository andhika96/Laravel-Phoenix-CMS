import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const v20 = readFileSync(resolve(root, 'public/js/pagebuilder_elementor/app.js'), 'utf8');
const v23 = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/app.js'), 'utf8');

function escapeRegExp(value) {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function assertBothContain(marker) {
    const pattern = new RegExp(escapeRegExp(marker));
    assert.match(v20, pattern, `v2.0 is missing the baseline marker: ${marker}`);
    assert.match(v23, pattern, `v2.3 lost the baseline marker: ${marker}`);
}

test('v2.3 retains the v2.0 editor action functions', () => {
    for (const marker of [
        'function undo()',
        'function redo()',
        'async function savePage()',
        'function onRootAdd',
        'function onAddContainer',
        'function onAddCol',
        'function showToolboxPanel',
        'function openCustomCssEditor',
        'function openCkFinder',
        'function chooseMedia',
        'function chooseMediaGallery',
        'function openIconLibrary',
        'function setResponsiveDevice',
        'function startColumnResize',
        'function rerouteTabsDropToNestedColumn',
        'function rerouteAccordionDropToNestedColumn',
    ]) {
        assertBothContain(marker);
    }
});

test('v2.3 retains modal state and root-canvas callback bindings', () => {
    for (const marker of [
        'v-if="showCssEditor"',
        '@click.self="closeCustomCssEditor"',
        'v-if="showTextEditorModal && selectedType===\'text_editor\' && selectedNode"',
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
        ':on-add-col="onAddCol"',
        ':on-select="selectNode"',
        ':on-select-column="selectColumn"',
        ':on-remove="removeNode"',
        ':on-duplicate="dupNode"',
        ':on-start-column-resize="startColumnResize"',
        ':on-open-modal="openModal"',
        ':on-show-toolbox="showToolboxPanel"',
        ':on-reroute-tabs-drop="rerouteTabsDropToNestedColumn"',
        ':on-toggle-accordion-item="toggleAccordionItem"',
        ':on-reroute-accordion-drop="rerouteAccordionDropToNestedColumn"',
        ':on-track-dropzone-pointer="trackDropzonePointerFromEvent"',
    ]) {
        assertBothContain(marker);
    }
});

test('v2.3 exposes the page-settings state, actions, and anchored popover fields', () => {
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
        assert.match(v23, new RegExp(escapeRegExp(marker)), `v2.3 is missing the page-settings contract: ${marker}`);
    }

    assert.match(v23, /<button\b(?=[^>]*class="page-name")(?=[^>]*@click\.stop="openPageSettings")[^>]*>/);
});

test('v2.3 closes page settings on Escape, outside click, preview, and successful save', () => {
    assert.match(v23, /function handlePageSettingsKeydown\(event\)[\s\S]{0,500}event\.key === ['"]Escape['"][\s\S]{0,500}closePageSettings\(\)/);
    assert.match(v23, /document\.addEventListener\(['"]keydown['"], handlePageSettingsKeydown\)/);
    assert.match(v23, /document\.removeEventListener\(['"]keydown['"], handlePageSettingsKeydown\)/);
    assert.match(v23, /<div\b(?=[^>]*class="builder-app")(?=[^>]*@click="closePageSettings")[^>]*>/);
    assert.match(v23, /class="page-settings-popover"[^>]*@click\.stop/);
    assert.match(v23, /watch\(previewMode,[\s\S]{0,500}if \(enabled\) closePageSettings\(\)/);
    assert.match(v23, /saveState\.value = ['"]success['"];[\s\S]{0,500}closePageSettings\(\)/);
});

test('v2.3 toolbox cards add at the root when no targeted insert is active', () => {
    assert.match(v23, /function insertToolAtRoot\(toolDef\)[\s\S]{0,800}rootNodes\.value\.push\(item\)[\s\S]{0,800}onRootAdd\(/);
    assert.match(v23, /function onToolboxItemClick\(toolDef\)[\s\S]{0,300}if \(pendingInsertTarget\.value\) return insertToolIntoPendingTarget\(toolDef, pendingInsertTarget\.value\);[\s\S]{0,300}return insertToolAtRoot\(toolDef\);/);
});
