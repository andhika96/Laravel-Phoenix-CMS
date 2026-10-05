import assert from 'node:assert/strict';
import test from 'node:test';

let options;
globalThis.window = {
  Vue: { createApp(value) { options = value; return { mount() { return {}; } }; } },
  WorkflowEditorBootstrap: { permissions: { write: true }, workflow: null },
  confirm: () => true, addEventListener() {}, removeEventListener() {}, setTimeout,
};
globalThis.document = { getElementById: () => ({}) };
await import('../../public/assets/js/vue3/workflow/workflow-editor.js');

const rect = { left: 450, top: 80, width: 1020, height: 650 };
function editor(viewport) {
  const state = options.data();
  state.graph = structuredClone(state.graph);
  state.graph.viewport = { ...viewport };
  state.$refs = { canvas: { getBoundingClientRect: () => rect } };
  for (const [key, method] of Object.entries(options.methods)) state[key] = method.bind(state);
  for (const [key, getter] of Object.entries(options.computed)) Object.defineProperty(state, key, { get: getter.bind(state) });
  state.ready = true;
  return state;
}

test('screen conversion preserves signed world coordinates and round trips the cursor', () => {
  for (const zoom of [0.35, 0.63, 1, 2.5]) {
    const viewport = { x: 430, y: 140, zoom };
    const state = editor(viewport);
    for (const [x, y] of [[16, 16], [1000, 16], [16, 630], [1000, 630]]) {
      const world = state.screenToGraph(rect.left + x, rect.top + y);
      assert.ok(Math.abs(world.x * zoom + viewport.x - x) < 1e-8);
      assert.ok(Math.abs(world.y * zoom + viewport.y - y) < 1e-8);
    }
  }
});

test('drag reaches all four visible corners after pan and zoom without losing its grip', () => {
  for (const zoom of [0.35, 0.63, 1, 2.5]) {
    for (const [x, y] of [[16, 16], [1000, 16], [16, 630], [1000, 630]]) {
      const viewport = { x: 430, y: 140, zoom };
      const state = editor(viewport);
      const node = state.graph.nodes[0];
      node.x = 0; node.y = 259;
      const grip = { x: 30, y: 20 };
      state.beginNodeDrag({ button: 0, target: { closest: () => null },
        clientX: rect.left + viewport.x + (node.x + grip.x) * zoom,
        clientY: rect.top + viewport.y + (node.y + grip.y) * zoom,
      }, node);
      state.moveNode({ clientX: rect.left + x + grip.x * zoom, clientY: rect.top + y + grip.y * zoom });
      assert.ok(Math.abs(node.x * zoom + viewport.x - x) <= zoom / 2 + 1e-8, `horizontal wall at ${x}, zoom ${zoom}`);
      assert.ok(Math.abs(node.y * zoom + viewport.y - y) <= zoom / 2 + 1e-8, `vertical wall at ${y}, zoom ${zoom}`);
      state.endNodeDrag();
      assert.equal(state.nodeDrag, null);
    }
  }
});

test('keyboard moves through the world origin in both negative directions', () => {
  const state = editor({ x: 430, y: 140, zoom: 1 });
  const node = state.graph.nodes[0];
  node.x = 0; node.y = 0;
  const target = {};
  for (const key of ['ArrowLeft', 'ArrowUp']) state.handleNodeKeydown({ key, target, currentTarget: target, preventDefault() {} }, node);
  assert.equal(node.x, -12);
  assert.equal(node.y, -12);
  state.undo(); assert.equal(state.graph.nodes[0].y, 0);
});

test('wheel zoom keeps the world point under the pointer fixed in every quadrant', () => {
  for (const [x, y] of [[16, 16], [1000, 16], [16, 630], [1000, 630]]) {
    const state = editor({ x: 430, y: 140, zoom: 0.63 });
    const before = { x: (x - 430) / 0.63, y: (y - 140) / 0.63 };
    state.zoomAt({ clientX: rect.left + x, clientY: rect.top + y, deltaY: -120 });
    const view = state.graph.viewport;
    assert.ok(Math.abs(before.x * view.zoom + view.x - x) < 1e-8);
    assert.ok(Math.abs(before.y * view.zoom + view.y - y) < 1e-8);
  }
});

test('fit includes nodes with negative coordinates and nodes beyond old SVG dimensions', () => {
  const state = editor({ x: 430, y: 140, zoom: 1 });
  state.graph.nodes[0].x = -200; state.graph.nodes[0].y = -160;
  state.graph.nodes[1].x = 2500; state.graph.nodes[1].y = 1800;
  state.fitCanvas();
  const view = state.graph.viewport;
  assert.ok(Number.isFinite(view.x) && Number.isFinite(view.y) && Number.isFinite(view.zoom));
  assert.ok(view.zoom > 0);
  for (const node of state.graph.nodes) {
    assert.ok(node.x * view.zoom + view.x >= 0);
    assert.ok(node.y * view.zoom + view.y >= 0);
    assert.ok((node.x + 220) * view.zoom + view.x <= rect.width);
    assert.ok((node.y + 96) * view.zoom + view.y <= rect.height);
  }
});
