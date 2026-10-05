import assert from 'node:assert/strict';
import test from 'node:test';
import { createNode, nodeSize } from '../../public/assets/js/vue3/workflow/workflow-graph.js';

let options;
globalThis.window = {
  Vue: { createApp(value) { options = value; return { mount() { return {}; } }; } },
  WorkflowEditorBootstrap: { permissions: { write: true }, workflow: null },
  confirm: () => true,
  addEventListener() {},
  removeEventListener() {},
  setTimeout,
};
globalThis.document = { getElementById: () => ({}) };
await import('../../public/assets/js/vue3/workflow/workflow-editor.js');

function editor() {
  const state = options.data();
  state.graph = structuredClone(state.graph);
  for (const [key, method] of Object.entries(options.methods)) state[key] = method.bind(state);
  for (const [key, getter] of Object.entries(options.computed)) Object.defineProperty(state, key, { get: getter.bind(state) });
  state.ready = true;
  return state;
}

test('table cell selection and row/column actions preserve existing content', () => {
  const state = editor();
  const table = createNode('table', 'table-1', 0, 0);
  table.config.cells[1][1] = 'keep me';
  state.graph.nodes.push(table);
  state.selectedNodeId = table.id;
  state.selectTableCell({ stopPropagation() {} }, table, 1, 1);
  state.insertTableRowAt(table, false);
  state.insertTableColumnAt(table, false);
  assert.equal(table.config.cells[2][2], 'keep me');
  state.deleteSelectedTableRow(table);
  state.deleteSelectedTableColumn(table);
  assert.equal(table.config.cells[1][1], 'keep me');
  assert.equal(state.dirty, true);
});

test('table pointer resizing uses the current zoom and cleans up the gesture', () => {
  const state = editor();
  const table = createNode('table', 'table-1', 0, 0);
  state.graph.nodes.push(table);
  state.graph.viewport.zoom = 2;
  state.startTableResize({ button: 0, clientX: 100, clientY: 100, preventDefault() {}, stopPropagation() {} }, table, 'column', 0);
  state.moveTableResize({ clientX: 140, clientY: 100 });
  state.endTableResize();
  assert.equal(table.config.columnWidths[0], 140);
  assert.equal(state.tableResize, null);
});

test('visual nodes do not create execution connections and simulation needs a trigger', () => {
  const state = editor();
  const table = createNode('table', 'table-1', 0, 0);
  state.graph.nodes.push(table);
  state.connecting = { sourceId: 'trigger-1', port: 'out' };
  state.finishConnection(table.id);
  assert.equal(state.graph.edges.length, 1);
  assert.equal(state.connecting, null);
  state.workflow.id = 'workflow-1';
  assert.equal(state.canSimulate, true);
  state.graph.nodes = state.graph.nodes.filter((node) => node.type !== 'trigger');
  assert.equal(state.canSimulate, false);
});

test('node style sizing follows the helper for table and icon list nodes', () => {
  const state = editor();
  const table = createNode('table', 'table-1', -8, 12);
  const list = createNode('icon-list', 'list-1', 4, 6);
  assert.deepEqual(state.nodeStyle(table), { left: '-8px', top: '12px', width: `${nodeSize(table).width}px`, height: `${nodeSize(table).height}px` });
  assert.equal(state.nodeStyle(list).width, `${nodeSize(list).width}px`);
});

test('variable-size collision checks use actual padded rectangles', () => {
  const state = editor();
  const wideTable = createNode('table', 'wide-table', 710, 350, {
    rows: 3,
    columns: 3,
    columnWidths: [203, 203, 205],
    rowHeights: [42, 42, 42],
  });
  state.graph.nodes = [
    wideTable,
    createNode('end', 'far-1', -1000, -1000),
    createNode('end', 'far-2', -1000, -800),
    createNode('end', 'far-3', -1000, -600),
    createNode('end', 'far-4', -1000, -400),
    createNode('end', 'far-5', -1000, -200),
  ];
  state.addNode('icon');
  const added = state.graph.nodes.at(-1);
  assert.equal(added.x, 110);
  assert.equal(added.y, 400);
  assert.equal(nodeSize(wideTable).width, 613);
});
