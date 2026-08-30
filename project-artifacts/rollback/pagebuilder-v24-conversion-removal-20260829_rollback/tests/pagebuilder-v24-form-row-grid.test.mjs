import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';
import { compileStyle, parse } from '@vue/compiler-sfc';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const definitionPath = path.join(root, 'resources/pagebuilder_elementor_v24/modules/widgets/pro/form/definition.js');
const settingsPath = path.join(root, 'resources/pagebuilder_elementor_v24/modules/widgets/pro/form/Settings.vue');
const canvasPath = path.join(root, 'resources/pagebuilder_elementor_v24/modules/widgets/pro/form/Canvas.vue');
const appPath = path.join(root, 'public/js/pagebuilder_elementor_v24/app.js');
const runtimePath = path.join(root, 'resources/pagebuilder_elementor_v24/modules/widgets/pro/form/runtime.js');
const bladePath = path.join(root, 'resources/pagebuilder_elementor_v24/modules/widgets/pro/form/frontend.blade.php');
const editorShellPath = path.join(root, 'resources/views/pagebuilder_elementor_v24/editor_shell.blade.php');
const routesPath = path.join(root, 'routes/experimentalFeaturesWebv2.php');

function loadDefinition() {
    const source = fs.readFileSync(definitionPath, 'utf8');
    const registry = {
        definition: null,
        advancedDefaults: () => ({}),
        normalizeAdvanced: (settings) => settings,
        register(definition) { this.definition = definition; },
    };
    const context = vm.createContext({
        console,
        Math,
        window: {
            PageBuilderElementorV24Widgets: registry,
            PageBuilderElementorV24ComplexWidgetRuntime: { image_box: { defaults: () => ({}) } },
        },
    });
    vm.runInContext(source, context);
    assert.ok(registry.definition, 'Form definition should register');
    assert.ok(context.window.PageBuilderElementorV24FormRowGrid, 'Form row grid helpers should be exposed');
    return { definition: registry.definition, api: context.window.PageBuilderElementorV24FormRowGrid };
}

function loadCanvasSfc() {
    const source = fs.readFileSync(canvasPath, 'utf8');
    const { descriptor, errors } = parse(source, { filename: canvasPath });
    assert.deepEqual(errors, []);
    const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
    return { component, descriptor };
}

function loadSettingsSfc() {
    const source = fs.readFileSync(settingsPath, 'utf8');
    const { descriptor, errors } = parse(source, { filename: settingsPath });
    assert.deepEqual(errors, []);
    const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
    return { component, descriptor };
}

const clone = (value) => JSON.parse(JSON.stringify(value));
const fieldIds = (column) => (column.items || [])
    .filter((item) => item?.kind === 'field' && item.field)
    .map((item) => item.field.id);
const itemMeta = (step, row, column, item, index = 0) => ({
    ownerId: 'form-1',
    group: 'pb-form-grid:form-1',
    stepId: step.id,
    rowId: row.id,
    columnId: column.id,
    itemId: item?.id || '',
    index,
    kind: 'field',
});

test('Form native import preserves source markers on the form, labels, and controls', () => {
    const canvas = fs.readFileSync(canvasPath, 'utf8');
    const blade = fs.readFileSync(bladePath, 'utf8');

    assert.match(canvas, /sourceImportNodeKey\(s\.importNodeKey\)/);
    assert.match(canvas, /sourceImportNodeKey\(field\.sourceLabelImportNodeKey\)/);
    assert.match(canvas, /sourceImportNodeKey\(field\.sourceImportNodeKey\)/);
    assert.match(blade, /safeImportMarker\(\$settings\['importNodeKey']/);
    assert.match(blade, /sourceLabelImportNodeKey/);
    assert.match(blade, /sourceImportNodeKey/);
});

test('Form defaults use one column list, keep all fields, and derive Submit outside rowGrid', () => {
    const { definition, api } = loadDefinition();
    const node = definition.normalize({ id: 'form-1', type: 'form', settings: {} });
    const layout = node.settings.rowGrid;
    const row = layout.steps[0].rows[0];

    assert.equal(layout.version, 2);
    assert.deepEqual(clone(row.columnCounts), { desktop: 1, tablet: 1, mobile: 1 });
    assert.equal(row.columns.length, 1);
    assert.deepEqual(clone(fieldIds(row.columns[0])), ['name', 'email', 'message']);
    assert.equal(JSON.stringify(layout).includes('"kind":"submit"'), false);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['name', 'email', 'message']);
    assert.equal(node.settings.messageDisplay, 'basic');
    assert.equal(node.settings.successTitle, 'Message sent');
    assert.equal(node.settings.errorTitle, 'Submission failed');
    assert.equal(node.settings.messageShowIcon, true);
    assert.equal(node.settings.messageDismissible, true);
});

test('Custom Messages exposes four display modes and previews the selected state only in its Form owner', () => {
    const { definition } = loadDefinition();
    const normalized = definition.normalize({
        id: 'form-1',
        type: 'form',
        settings: {
            customMessages: true,
            messageDisplay: 'unsupported',
            successTitle: 'Thank you',
            errorTitle: 'Please retry',
        },
    });
    assert.equal(normalized.settings.messageDisplay, 'basic');

    const settings = loadSettingsSfc();
    assert.match(settings.descriptor.template.content, /title="Messages"/);
    for (const mode of ['basic', 'above-form', 'toast', 'modal']) {
        assert.match(settings.descriptor.template.content, new RegExp(`value:\\s*['"]${mode}['"]`));
    }
    assert.match(settings.descriptor.template.content, /previewFormMessage/);

    const dispatched = [];
    const originalWindow = globalThis.window;
    const originalCustomEvent = globalThis.CustomEvent;
    globalThis.window = { dispatchEvent: (event) => dispatched.push(event) };
    globalThis.CustomEvent = class CustomEvent {
        constructor(type, options = {}) {
            this.type = type;
            this.detail = options.detail;
        }
    };
    try {
        settings.component.methods.previewFormMessage.call({
            node: { id: 'form-1' },
            formMessageEditorState: 'error',
        });
    } finally {
        globalThis.window = originalWindow;
        globalThis.CustomEvent = originalCustomEvent;
    }
    assert.equal(dispatched.length, 1);
    assert.equal(dispatched[0].type, 'pagebuilder:v24-form-message-preview');
    assert.deepEqual(dispatched[0].detail, { nodeId: 'form-1', state: 'error' });

    const canvas = loadCanvasSfc();
    const canvasContext = {
        item: { id: 'form-1' },
        s: {
            customMessages: true,
            successMessage: 'Saved successfully.',
            errorMessage: 'Could not save.',
        },
        formSubmitState: 'idle',
        formSubmitMessage: '',
        formSubmitRedirect: 'https://example.com',
        previewFormMessage: canvas.component.methods.previewFormMessage,
    };
    canvas.component.methods.handleFormMessagePreview.call(canvasContext, {
        detail: { nodeId: 'other-form', state: 'error' },
    });
    assert.equal(canvasContext.formSubmitState, 'idle');
    canvas.component.methods.handleFormMessagePreview.call(canvasContext, {
        detail: { nodeId: 'form-1', state: 'success' },
    });
    assert.equal(canvasContext.formSubmitState, 'success');
    assert.equal(canvasContext.formSubmitMessage, 'Saved successfully.');
    assert.equal(canvasContext.formSubmitRedirect, '');
    assert.match(canvas.descriptor.template.content, /data-pro-form-message-layer/);
    assert.match(canvas.descriptor.template.content, /formMessageDisplay/);
    assert.match(canvas.descriptor.template.content, /clearFormMessage/);
});

test('legacy width values migrate contiguous fields into 2, 3, and 4 track rows', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([
        { id: 'a', label: 'A', type: 'text', width: 50 },
        { id: 'b', label: 'B', type: 'text', width: 50 },
        { id: 'c', label: 'C', type: 'text', width: 33 },
        { id: 'd', label: 'D', type: 'text', width: 33 },
        { id: 'e', label: 'E', type: 'text', width: 33 },
        { id: 'f', label: 'F', type: 'text', width: 25 },
        { id: 'g', label: 'G', type: 'text', width: 25 },
        { id: 'h', label: 'H', type: 'text', width: 25 },
        { id: 'i', label: 'I', type: 'text', width: 25 },
    ]);

    assert.equal(layout.version, 2);
    assert.deepEqual(clone(layout.steps[0].rows.map((row) => row.columns.length)), [2, 3, 4]);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']);
});

test('version 1 cell matrix migrates to persistent columns with unlimited ordered items', () => {
    const { api } = loadDefinition();
    const item = (id) => api.fieldItem(api.createField({ id, label: id }));
    const layout = api.normalizeLayout({
        version: 1,
        steps: [{
            id: 'step-root',
            rows: [{
                id: 'row-1',
                columnCounts: { desktop: 2, tablet: 1, mobile: 1 },
                columns: [
                    { id: 'cell-1', span: 'auto', items: [item('name')] },
                    { id: 'cell-2', span: 'auto', items: [item('email')] },
                    { id: 'cell-3', span: 'auto', items: [item('message')] },
                    { id: 'cell-4', span: 'auto', items: [item('phone')] },
                    { id: 'submit-slot', span: 'full', items: [{ id: 'submit', kind: 'submit' }] },
                ],
            }],
        }],
    });
    const columns = layout.steps[0].rows[0].columns;

    assert.equal(layout.version, 2);
    assert.deepEqual(clone(columns.map(fieldIds)), [['name', 'message'], ['email', 'phone']]);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['name', 'email', 'message', 'phone']);
    assert.equal(JSON.stringify(layout).includes('"kind":"submit"'), false);
});

test('row-scoped Add Field balances persistent columns and never creates another row', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([
        { id: 'first', label: 'First', type: 'text', width: 50 },
        { id: 'second', label: 'Second', type: 'text', width: 50 },
    ]);
    const step = layout.steps[0];
    const row = step.rows[0];

    api.appendFieldToRow(layout, step.id, row.id, api.fieldItem(api.createField({ id: 'third', label: 'Third' })));
    api.appendFieldToRow(layout, step.id, row.id, api.fieldItem(api.createField({ id: 'fourth', label: 'Fourth' })));

    assert.equal(step.rows.length, 1);
    assert.deepEqual(clone(row.columns.map(fieldIds)), [['first', 'third'], ['second', 'fourth']]);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['first', 'second', 'third', 'fourth']);
});

test('cross-row move inserts at the requested position without swapping the occupied field', () => {
    const { api } = loadDefinition();
    const layout = api.normalizeLayout({
        version: 2,
        steps: [{ id: 'step-root', rows: [
            { id: 'row-1', columnCounts: { desktop: 2, tablet: 1, mobile: 1 }, columns: [
                { id: 'column-1', items: [api.fieldItem(api.createField({ id: 'name' })), api.fieldItem(api.createField({ id: 'message' }))] },
                { id: 'column-2', items: [api.fieldItem(api.createField({ id: 'email' }))] },
            ] },
            { id: 'row-2', columnCounts: { desktop: 1, tablet: 1, mobile: 1 }, columns: [
                { id: 'column-3', items: [api.fieldItem(api.createField({ id: 'new' }))] },
            ] },
        ] }],
    });
    const step = layout.steps[0];
    const [targetRow, sourceRow] = step.rows;
    const sourceColumn = sourceRow.columns[0];
    const targetColumn = targetRow.columns[1];
    const sourceItem = sourceColumn.items[0];

    assert.deepEqual(clone(api.moveItem(
        layout,
        itemMeta(step, sourceRow, sourceColumn, sourceItem),
        itemMeta(step, targetRow, targetColumn, null, 1),
    )), { ok: true });
    assert.deepEqual(clone(fieldIds(targetColumn)), ['email', 'new']);
    assert.deepEqual(clone(fieldIds(sourceColumn)), []);
});

test('same-column move reorders by insertion index instead of exchanging two items', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([
        { id: 'a', label: 'A', type: 'text' },
        { id: 'b', label: 'B', type: 'text' },
        { id: 'c', label: 'C', type: 'text' },
    ]);
    const step = layout.steps[0];
    const row = step.rows[0];
    const column = row.columns[0];

    assert.deepEqual(clone(api.moveItem(
        layout,
        itemMeta(step, row, column, column.items[2], 2),
        itemMeta(step, row, column, null, 0),
    )), { ok: true });
    assert.deepEqual(clone(fieldIds(column)), ['c', 'a', 'b']);
});

test('column count reduction merges removed tracks into the nearest remaining column', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([
        { id: 'a', type: 'text', width: 33 },
        { id: 'b', type: 'text', width: 33 },
        { id: 'c', type: 'text', width: 33 },
    ]);
    const row = layout.steps[0].rows[0];
    row.columnCounts = { desktop: 1, tablet: 1, mobile: 1 };

    api.ensureColumns(row);

    assert.equal(row.columns.length, 1);
    assert.deepEqual(clone(fieldIds(row.columns[0])), ['a', 'b', 'c']);
});

test('empty rows keep a real persistent column and Submit is not stored as a row item', () => {
    const { api } = loadDefinition();
    const layout = api.normalizeLayout({
        version: 2,
        steps: [{ id: 'step-root', rows: [{
            id: 'row-empty',
            columnCounts: { desktop: 2, tablet: 1, mobile: 1 },
            columns: [],
        }] }],
    });
    const row = layout.steps[0].rows[0];

    assert.equal(row.columns.length, 2);
    assert.deepEqual(clone(row.columns.map((column) => column.items)), [[], []]);
    assert.equal(JSON.stringify(layout).includes('"kind":"submit"'), false);
});

test('delete row merges fields into matching columns while preserving visual order', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([
        { id: 'a', type: 'text', width: 50 },
        { id: 'b', type: 'text', width: 50 },
        { id: 'c', type: 'text', width: 100 },
    ]);
    const step = layout.steps[0];
    const secondRow = step.rows[1];

    assert.equal(api.deleteRow(layout, step.id, secondRow.id), true);
    assert.equal(step.rows.length, 1);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['a', 'b', 'c']);
});

test('cross-step moves still require confirmation and preserve field identity', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([
        { id: 'first', type: 'text' },
        { type: 'step', id: 'step-two', stepTitle: 'Second' },
        { id: 'second', type: 'text' },
    ]);
    const [firstStep, secondStep] = layout.steps;
    const sourceRow = firstStep.rows[0];
    const targetRow = secondStep.rows[0];
    const sourceColumn = sourceRow.columns[0];
    const targetColumn = targetRow.columns[0];
    const source = itemMeta(firstStep, sourceRow, sourceColumn, sourceColumn.items[0]);
    const target = itemMeta(secondStep, targetRow, targetColumn, null, 1);

    assert.deepEqual(clone(api.moveItem(layout, source, target)), { ok: false, reason: 'cross-step' });
    assert.deepEqual(clone(api.moveItem(layout, source, target, { confirmed: true })), { ok: true });
    assert.deepEqual(clone(fieldIds(targetColumn)), ['second', 'first']);
});

test('Form owner boundaries reject page-level and other Form drop groups', () => {
    const { api } = loadDefinition();
    const source = { ownerId: 'form-1', group: 'pb-form-grid:form-1', kind: 'field' };

    assert.equal(api.canAcceptDrop(source, { ownerId: 'form-1', group: 'pb-form-grid:form-1' }), true);
    assert.equal(api.canAcceptDrop(source, { ownerId: 'form-2', group: 'pb-form-grid:form-2' }), false);
    assert.equal(api.canAcceptDrop(source, { ownerId: 'form-1', group: 'pb-container' }), false);
});

test('field Row Span remains responsive and follows the compatibility projection', () => {
    const { api } = loadDefinition();
    const layout = api.fromLegacyFields([{
        id: 'message',
        label: 'Message',
        type: 'textarea',
        rowSpan: { desktop: 2, tablet: 9, mobile: 0 },
    }]);

    assert.deepEqual(clone(api.projectFields(layout)[0].rowSpan), { desktop: 2, tablet: 4, mobile: 1 });
});

test('shared row tracks align a spanning textarea with sibling fields on every device', () => {
    const { api } = loadDefinition();
    const item = (id, rowSpan = 1) => api.fieldItem(api.createField({
        id,
        label: id,
        type: id === 'message' ? 'textarea' : 'text',
        rowSpan: { desktop: rowSpan, tablet: rowSpan, mobile: rowSpan },
    }));
    const row = {
        id: 'row-1',
        columnCounts: { desktop: 2, tablet: 2, mobile: 1 },
        columns: [
            { id: 'left', items: [item('name'), item('message', 4)] },
            { id: 'right', items: [item('email'), item('two'), item('three'), item('four'), item('five')] },
        ],
    };

    assert.deepEqual(clone(api.trackPlan(row, 'desktop')), {
        columnCount: 2,
        totalRows: 5,
        placements: [
            { gridColumn: 1, rowStart: 1, rowSpan: 5 },
            { gridColumn: 2, rowStart: 1, rowSpan: 5 },
        ],
    });
    assert.deepEqual(clone(api.trackPlan(row, 'desktop', true)), {
        columnCount: 2,
        totalRows: 6,
        placements: [
            { gridColumn: 1, rowStart: 1, rowSpan: 6 },
            { gridColumn: 2, rowStart: 1, rowSpan: 6 },
        ],
    });
    assert.deepEqual(clone(api.trackPlan(row, 'mobile')), {
        columnCount: 1,
        totalRows: 10,
        placements: [
            { gridColumn: 1, rowStart: 1, rowSpan: 5 },
            { gridColumn: 1, rowStart: 6, rowSpan: 5 },
        ],
    });
});

test('Add Field keeps the new sidebar field collapsed', () => {
    const { component } = loadSettingsSfc();
    const editor = component.components.FormRowGridEditor;
    const step = { id: 'step-root' };
    const row = { id: 'row-1' };
    const newItem = { id: 'field:new', kind: 'field', field: { id: 'new' } };
    const emitted = [];
    const context = {
        layout: { steps: [step] },
        expandedRowId: '',
        expandedItemId: 'field:email',
        api: {
            createField: () => newItem.field,
            fieldItem: () => newItem,
            appendFieldToRow: () => row,
        },
        $emit: (...args) => emitted.push(args),
    };

    editor.methods.addField.call(context, step, row);

    assert.equal(context.expandedRowId, 'row-1');
    assert.equal(context.expandedItemId, 'field:email');
    assert.deepEqual(emitted, [['sync']]);
});

test('Form field context request opens the exact row and sidebar field card', () => {
    const { component, descriptor } = loadSettingsSfc();
    const editor = component.components.FormRowGridEditor;
    assert.equal(typeof editor.methods.applyFieldEditRequest, 'function');
    assert.match(descriptor.template.content, /:node-id="node\.id"/);
    assert.match(descriptor.template.content, /:editor="editor"/);

    const context = {
        nodeId: 'form-1',
        layout: {
            steps: [{
                id: 'step-root',
                rows: [{
                    id: 'row-1',
                    columns: [{
                        id: 'column-1',
                        items: [{ id: 'field:email', kind: 'field', field: { id: 'email' } }],
                    }],
                }],
            }],
        },
        expandedRowId: '',
        expandedItemId: '',
        $nextTick: (callback) => callback(),
        $el: { querySelectorAll: () => [] },
    };

    editor.methods.applyFieldEditRequest.call(context, {
        nodeId: 'form-1',
        fieldId: 'email',
        itemId: 'field:email',
        requestId: 1,
    });

    assert.equal(context.expandedRowId, 'row-1');
    assert.equal(context.expandedItemId, 'field:email');
});

test('Steps can be added and removed without losing nested fields', () => {
    const { api } = loadDefinition();
    const { component: canvas } = loadCanvasSfc();
    const layout = api.fromLegacyFields([{ id: 'name', label: 'Name', type: 'text' }]);
    const second = api.appendStep(layout, { title: 'Details' });
    api.appendFieldToRow(
        layout,
        second.id,
        second.rows[0].id,
        api.fieldItem(api.createField({ id: 'email', label: 'Email', type: 'email' })),
    );

    assert.equal(layout.steps.length, 2);
    assert.equal(second.rows.length, 1);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['name', second.id, 'email']);
    assert.equal(api.deleteStep(layout, second.id), true);
    assert.equal(layout.steps.length, 1);
    assert.deepEqual(clone(api.projectFields(layout).map((field) => field.id)), ['name', 'email']);

    assert.equal(canvas.computed.clampedFormStep.call({ currentFormStep: 1, formSteps: layout.steps }), 0);
});

test('Submit width is editable in Row Grid and respected by the Canvas footer', () => {
    const { component, descriptor } = loadCanvasSfc();
    const { descriptor: settingsDescriptor } = loadSettingsSfc();
    const formStyle = component.computed.formStyle.call({
        s: { buttonAlign: 'center' },
        responsiveValue: (_key, fallback) => fallback,
        safeLength: (value) => value,
    });
    const css = descriptor.styles.map((style, index) => compileStyle({
        source: style.content,
        filename: canvasPath,
        id: `data-v-form-submit-width-${index}`,
        scoped: style.scoped,
    }).code).join('\n');
    const footerRule = css.match(/\.pb-pro-form__submit-footer[^\{]*\{([^}]*)\}/)?.[1] || '';

    assert.equal(formStyle['--form-button-align'], 'center');
    assert.match(settingsDescriptor.template.content, /:button-width="s\.buttonWidth"/);
    assert.match(settingsDescriptor.template.content, /@update:button-width="s\.buttonWidth=\$event"/);
    assert.match(settingsDescriptor.script.content, /aria-label="Submit button width"/);
    assert.match(footerRule, /display:\s*flex/);
    assert.match(footerRule, /justify-content:\s*var\(--form-button-align/);
    assert.doesNotMatch(css, /\.pb-pro-form__submit-footer\s*>\s*\*[^\{]*\{[^}]*width:\s*100%/);
});

test('Canvas textarea uses a Rows-driven non-resizable preview style', () => {
    const { component, descriptor } = loadCanvasSfc();
    const inputStyle = component.computed.formInputStyle.call({
        s: { inputSize: 'small' },
        responsiveValue: () => '1px',
        safeLength: (value) => value,
    });
    const textareaStyle = component.computed.formTextareaStyle.call({ formInputStyle: inputStyle });

    assert.equal(textareaStyle.height, 'auto');
    assert.equal(textareaStyle.resize, 'none');
    assert.equal(textareaStyle.minHeight, '36px');
    assert.match(
        descriptor.template.content,
        /v-else-if="field\.type === 'textarea'"[\s\S]*?:rows="Math\.max\(1, Number\(field\.rows\) \|\| 4\)"[\s\S]*?:style="formTextareaStyle"/,
    );
});

test('Row Span toolbar anchors to the selected field top-right corner without changing layout flow', () => {
    const { descriptor } = loadCanvasSfc();
    const css = descriptor.styles.map((style, index) => compileStyle({
        source: style.content,
        filename: canvasPath,
        id: `data-v-form-row-grid-${index}`,
        scoped: style.scoped,
    }).code).join('\n');
    const layoutItemRule = css.match(/\.pb-pro-form__layout-item[^\{]*\{([^}]*)\}/)?.[1] || '';
    const toolbarRule = css.match(/\.pb-pro-form__row-span-toolbar[^\{]*\{([^}]*)\}/)?.[1] || '';

    assert.match(layoutItemRule, /position:\s*relative/);
    assert.doesNotMatch(layoutItemRule, /display:\s*grid/);
    assert.match(toolbarRule, /position:\s*absolute/);
    assert.match(toolbarRule, /top:\s*0(?:px)?\s*;/);
    assert.match(toolbarRule, /right:\s*0(?:px)?\s*;/);
    assert.doesNotMatch(toolbarRule, /translateY/);
    assert.doesNotMatch(toolbarRule, /justify-self|margin:/);
});

test('Drop field hint is idle-visible only for empty columns and returns while dragging', () => {
    const { component, descriptor } = loadCanvasSfc();
    const row = {
        columnCounts: { desktop: 2, tablet: 1, mobile: 1 },
        columns: [
            { id: 'filled', items: [{ kind: 'field', field: { rowSpan: { desktop: 1 } } }] },
            { id: 'empty', items: [] },
        ],
    };
    const trackPlanCalls = [];
    const context = {
        responsiveDevice: 'desktop',
        dragging: false,
        api: {
            trackPlan(_row, device, includeTail) {
                trackPlanCalls.push({ device, includeTail });
                return { columnCount: 2, totalRows: 1, placements: [] };
            },
        },
    };

    component.components.FormRowGridCanvas.methods.rowPlan.call(context, row);
    context.dragging = true;
    component.components.FormRowGridCanvas.methods.rowPlan.call(context, row);
    const css = descriptor.styles.map((style, index) => compileStyle({
        source: style.content,
        filename: canvasPath,
        id: `data-v-form-drop-hint-${index}`,
        scoped: style.scoped,
    }).code).join('\n');
    const hiddenTailRule = css.match(/\.pb-pro-form__dropzone-tail:not\(\.is-empty\)[^\{]*\{([^}]*)\}/)?.[1] || '';

    assert.deepEqual(trackPlanCalls, [
        { device: 'desktop', includeTail: false },
        { device: 'desktop', includeTail: false },
    ]);
    assert.match(descriptor.script.content, /:class="\{'is-empty':!column\.items\.length,'is-dragging':dragging\}"/);
    assert.doesNotMatch(css, /\.pb-pro-form__dropzone-tail:not\(\.is-empty\):not\(\.is-dragging\)/);
    assert.match(hiddenTailRule, /min-height:\s*0/);
    assert.match(hiddenTailRule, /opacity:\s*0/);
    assert.match(hiddenTailRule, /pointer-events:\s*none/);
});

test('Step indicator types preserve per-step icons and render distinct Canvas and frontend markup', () => {
    const { definition, api } = loadDefinition();
    const { descriptor: canvasDescriptor } = loadCanvasSfc();
    const { descriptor: settingsDescriptor } = loadSettingsSfc();
    const blade = fs.readFileSync(bladePath, 'utf8');
    const runtime = fs.readFileSync(runtimePath, 'utf8');
    const layout = api.fromLegacyFields([
        { id: 'name', label: 'Name', type: 'text' },
        {
            id: 'step-details',
            type: 'step',
            stepTitle: 'Details',
            iconSource: 'library',
            iconStyle: 'solid',
            iconName: 'star',
            iconClass: 'fas fa-star',
            iconSvg: '',
        },
        { id: 'email', label: 'Email', type: 'email' },
    ]);
    const step = layout.steps[1];
    const marker = api.projectFields(layout).find((field) => field.type === 'step');
    const normalized = definition.normalize({
        id: 'form-invalid-step-style',
        type: 'form',
        settings: { stepType: 'invalid', stepShape: 'invalid' },
    });

    assert.equal(step.iconClass, 'fas fa-star');
    assert.equal(marker.iconName, 'star');
    assert.equal(normalized.settings.stepType, 'none');
    assert.equal(normalized.settings.stepShape, 'circle');
    assert.match(settingsDescriptor.template.content, /target-key="formStep"/);
    assert.match(settingsDescriptor.template.content, /v-if="\['icon','icon-text'\]\.includes\(s\.stepType\)"/);
    assert.match(settingsDescriptor.template.content, /v-if="\['icon','number','icon-text','number-text'\]\.includes\(s\.stepType\)"[\s\S]*?label="Shape"/);
    assert.match(canvasDescriptor.template.content, /s\.stepType === 'progress'/);
    assert.match(canvasDescriptor.template.content, /data-pro-step-progress-fill/);
    assert.match(canvasDescriptor.template.content, /stepIndicatorShowsIcon/);
    assert.match(canvasDescriptor.template.content, /stepIndicatorShowsText/);
    assert.match(blade, /data-pro-step-progress-fill/);
    assert.match(blade, /data-pro-step-marker/);
    assert.match(blade, /data-pro-step-label/);
    assert.match(runtime, /data-pro-step-progress-fill/);
    assert.match(runtime, /data-pro-step-progress-text/);
});

test('Canvas submits the current unsaved Form node through the authenticated editor draft service', () => {
    const { descriptor } = loadCanvasSfc();
    const app = fs.readFileSync(appPath, 'utf8');
    const editorShell = fs.readFileSync(editorShellPath, 'utf8');
    const routes = fs.readFileSync(routesPath, 'utf8');

    assert.match(descriptor.template.content, /@submit\.prevent="submitEditorForm"/);
    assert.match(descriptor.template.content, /:name="fieldInputName\(field\)"/);
    assert.match(descriptor.template.content, /:disabled="formSubmitState === 'sending'"/);
    assert.match(descriptor.template.content, /data-pro-form-message/);
    assert.match(descriptor.script.content, /async submitEditorForm\(event\)/);
    assert.match(app, /async function submitFormDraft\(node, form\)/);
    assert.match(app, /new FormData\(form\)/);
    assert.match(app, /__pb_editor_node/);
    assert.match(app, /X-CSRF-TOKEN/);
    assert.match(editorShell, /formDraftSubmitUrl/);
    assert.match(routes, /form\/editor-draft/);
    assert.match(routes, /middleware\(\['auth', 'checkSuspended'\]\)[\s\S]*?form\/editor-draft/);
    assert.match(routes, /form\/editor-draft[\s\S]*?middleware\('throttle:10,1'\)/);
});

test('Canvas replaces a raw draft 401 with an actionable editor session message', async () => {
    const { component } = loadCanvasSfc();
    const context = {
        formSubmitState: 'idle',
        formSubmitMessage: '',
        formSubmitRedirect: '',
        item: { id: 'form-1', type: 'form' },
        editor: {
            submitFormDraft: async () => {
                const error = new Error('Request failed with status code 401');
                error.response = { status: 401, data: { message: 'Unauthenticated.' } };
                throw error;
            },
        },
    };

    await component.methods.submitEditorForm.call(context, { currentTarget: {} });

    assert.equal(context.formSubmitState, 'error');
    assert.equal(context.formSubmitMessage, 'Your editor session has expired. Please sign in again, then retry.');
    assert.equal(context.formSubmitRedirect, '');
});

test('Canvas and Settings expose persistent column lists while Submit is a final-step footer', () => {
    const settings = fs.readFileSync(settingsPath, 'utf8');
    const canvas = fs.readFileSync(canvasPath, 'utf8');
    const app = fs.readFileSync(appPath, 'utf8');
    const blade = fs.readFileSync(bladePath, 'utf8');

    assert.match(settings, /Fields in this row/);
    assert.match(settings, /Final step footer/);
    assert.doesNotMatch(settings, /draggableComponent/);
    assert.match(canvas, /v-model="column\.items"/);
    assert.match(canvas, /:sort="true"/);
    assert.match(canvas, /:force-fallback="true"/);
    assert.match(canvas, /<template #footer>/);
    assert.match(canvas, /pb-pro-form__dropzone-tail/);
    assert.match(canvas, /pb-pro-form__submit-footer/);
    assert.doesNotMatch(canvas, /forceSortableRelease/);
    assert.match(app, /isFormLayoutDrag/);
    assert.match(blade, /pb-pro-form__submit-slot/);
});

test('Canvas fallback drag preview stays centered beneath the Form drag handle', () => {
    const { component, descriptor } = loadCanvasSfc();
    const methods = component.components.FormRowGridCanvas.methods;
    const css = descriptor.styles.map((style, index) => compileStyle({
        source: style.content,
        filename: canvasPath,
        id: `data-v-form-drag-preview-${index}`,
        scoped: style.scoped,
    }).code).join('\n');

    assert.equal(methods.fallbackGhostOffset(1126, 220), 453);
    assert.equal(methods.fallbackGhostOffset(180, 220), 0);
    assert.match(descriptor.script.content, /positionFallbackGhost\(event\)/);
    assert.match(css, /translate:\s*var\(--pb-form-drag-offset-x,\s*0px\)\s+0/);
});

test('Canvas drop direction inserts before or after the field under the pointer', () => {
    const { component } = loadCanvasSfc();
    const method = component.components.FormRowGridCanvas.methods.relativeInsertDirection;
    const related = {
        classList: { contains: (name) => name === 'pb-pro-form__layout-item' },
        getBoundingClientRect: () => ({ top: 100, height: 80 }),
    };

    assert.equal(method({ related, originalEvent: { clientY: 120 } }), -1);
    assert.equal(method({ related, originalEvent: { clientY: 170 } }), 1);
    assert.equal(method({ related: null, originalEvent: { clientY: 170 } }), true);
});
