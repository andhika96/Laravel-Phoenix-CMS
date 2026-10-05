import test from 'node:test';
import assert from 'node:assert/strict';
import {
  createDefaultGraph,
  validateGraphShape,
  validatePublishGraph,
  bezierPath,
  normalizeGraph,
} from '../../public/assets/js/vue3/workflow/workflow-graph.js';

test('default approval graph is publishable and keeps node ids unique', () => {
  const graph = createDefaultGraph('approval');
  const ids = graph.nodes.map((node) => node.id);

  assert.equal(new Set(ids).size, ids.length);
  assert.equal(validateGraphShape(graph).valid, true);
  assert.equal(validatePublishGraph(graph).valid, true);
  assert.equal(graph.nodes.find((node) => node.type === 'approval')?.config.assignee_role, 'Manager');
});

test('normalization repairs missing optional values without hiding bad references', () => {
  const graph = normalizeGraph({
    nodes: [{ id: 'trigger', type: 'trigger', label: '', x: '12', y: null }],
    edges: [{ id: 'edge', source: 'trigger', target: 'missing', port: 'out' }],
  });

  assert.equal(graph.viewport.zoom, 1);
  assert.equal(graph.nodes[0].x, 12);
  assert.equal(graph.nodes[0].label, 'Trigger');
  assert.equal(validateGraphShape(graph).valid, false);
  assert.match(validateGraphShape(graph).errors.join(' '), /target/i);
});

test('publish validation catches cycles, disconnected nodes, and invalid config', () => {
  const graph = createDefaultGraph('blank');
  graph.nodes.push({ id: 'email', type: 'email', label: 'Notify', x: 320, y: 180, config: { to: '', subject: '' } });
  graph.edges.push({ id: 'cycle', source: 'end', target: 'trigger', port: 'out' });

  const result = validatePublishGraph(graph);
  assert.equal(result.valid, false);
  assert.ok(result.errors.some((message) => /cycle|outgoing|email/i.test(message)));
});

test('bezier geometry is deterministic and points left-to-right', () => {
  const path = bezierPath(
    { x: 40, y: 80, width: 220, height: 96 },
    { x: 420, y: 200, width: 220, height: 96 },
    'out',
  );

  assert.match(path, /^M\s+260\s+128\s+C/);
  assert.match(path, /420\s+248$/);
});
