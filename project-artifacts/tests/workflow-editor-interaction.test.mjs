import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';

let options;
globalThis.window = {
  Vue: { createApp(value) { options = value; return { mount() { return {}; } }; } },
  WorkflowEditorBootstrap: { permissions: { write: true }, workflow: null },
  confirm: () => true,
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

test('keyboard controls inside a node retain their native Enter action', () => {
  const state = editor();
  let prevented = false;
  state.handleNodeKeydown({ key: 'Enter', target: {}, currentTarget: {}, preventDefault() { prevented = true; } }, state.graph.nodes[0]);
  assert.equal(prevented, false, 'Node key handler must not cancel Enter on its output button');
});

test('node keyboard movement can be undone and redone', () => {
  const state = editor();
  const node = state.graph.nodes[0];
  const original = node.x;
  const target = {};
  state.handleNodeKeydown({ key: 'ArrowRight', target, currentTarget: target, preventDefault() {} }, node);
  assert.equal(node.x, original + 12);
  state.undo(); assert.equal(state.graph.nodes[0].x, original);
  state.redo(); assert.equal(state.graph.nodes[0].x, original + 12);
});

test('palette addition chooses an unoccupied position', () => {
  const state = editor();
  state.addNode('update');
  const added = state.graph.nodes.at(-1);
  assert.equal(state.graph.nodes.slice(0, -1).some(node => Math.abs(node.x - added.x) < 244 && Math.abs(node.y - added.y) < 120), false);
});

test('archived workflows cannot edit or publish but can be duplicated', () => {
  const state = editor();
  state.permissions = { write: true, publish: true, duplicate: true };
  state.workflow.status = 'archived';
  assert.equal(state.canWrite, false);
  assert.equal(state.canPublish, false);
  assert.equal(state.canDuplicate, true);
});

test('connections replace a port only once and remain undoable', () => {
  const state = editor();
  state.addNode('update');
  const added = state.graph.nodes.at(-1);
  state.startConnection({ currentTarget: { focus() {} } }, state.graph.nodes[0], 'out');
  state.finishConnection(added.id);
  assert.equal(state.graph.edges.length, 1);
  assert.equal(state.graph.edges[0].target, added.id);
  state.undo();
  assert.equal(state.graph.edges[0].target, 'end-1');
});

test('mobile icon controls have accessible names', () => {
  const view = readFileSync(new URL('../../resources/views/workflow/editor.blade.php', import.meta.url), 'utf8');
  assert.match(view, /@click="fitCanvas" aria-label=/);
  assert.match(view, /@click="deleteSelected" aria-label=/);
});

test('invalid JSON shape does not replace the current graph on import', async () => {
  const state = editor();
  const before = structuredClone(state.graph);
  await state.importGraph({ target: { files: [{ text: async () => '{"unrelated":"file"}' }], value: 'import.json' } });
  assert.deepEqual(state.graph, before);
  assert.equal(state.notice.type, 'warning');
});

test('exported JSON graph imports without losing nodes or connections', async () => {
  const state = editor();
  state.addNode('delay');
  let exported;
  const originalCreate = URL.createObjectURL;
  const originalRevoke = URL.revokeObjectURL;
  const originalElement = document.createElement;
  const anchor = { click() {} };
  try {
    document.createElement = () => anchor;
    URL.createObjectURL = blob => { exported = blob; return 'blob:workflow-test'; };
    URL.revokeObjectURL = () => {};
    state.exportGraph();
    const json = await exported.text();
    assert.equal(anchor.download, 'untitled-workflow.json');
    const imported = editor();
    await imported.importGraph({ target: { files: [{ text: async () => json }], value: 'export.json' } });
    assert.deepEqual(imported.graph, state.graph);
    assert.equal(imported.dirty, true);
  } finally {
    URL.createObjectURL = originalCreate;
    URL.revokeObjectURL = originalRevoke;
    document.createElement = originalElement;
  }
});
