import assert from 'node:assert/strict';
import test from 'node:test';
import * as graph from '../../public/assets/js/vue3/workflow/workflow-graph.js';

test('table nodes expose visual geometry and no workflow ports', () => {
  assert.ok(graph.NODE_TYPES.includes('table'));
  const table = graph.createNode('table', 'table1', -200, 100);
  assert.equal(table.type, 'table');
  assert.equal(graph.isVisualNode(table), true);
  assert.deepEqual(graph.allowedPorts('table'), []);
  assert.deepEqual(graph.nodeSize(table), { width: 362, height: 188 });
  assert.equal(graph.validatePublishGraph({nodes:[table],edges:[]}).valid, true);
});

test('table grid resize and insert/delete preserve surviving cell contents and dimensions', () => {
  const config = graph.tableConfig({rows:2,columns:2,cells:[['A','B'],['C','D']],columnWidths:[90,180],rowHeights:[30,70]});
  const larger = graph.resizeTableGrid(config,3,3);
  assert.deepEqual(larger.cells, [['A','B',''],['C','D',''],['','','']]);
  assert.deepEqual(larger.columnWidths, [90,180,120]);
  assert.deepEqual(larger.rowHeights, [30,70,42]);
  const inserted = graph.insertTableColumn(graph.insertTableRow(config,1),1);
  assert.deepEqual(inserted.cells, [['A','','B'],['','',''],['C','','D']]);
  const original = graph.deleteTableColumn(graph.deleteTableRow(inserted,1),1);
  assert.deepEqual(original.cells, config.cells);
  assert.deepEqual(original.columnWidths, config.columnWidths);
  assert.deepEqual(original.rowHeights, config.rowHeights);
});

test('table row/column and whole resize clamp only sizes, preserving cells', () => {
  const config = graph.tableConfig({rows:2,columns:2,cells:[['A','B'],['C','D']]});
  assert.equal(graph.resizeTableColumn(config,0,10).columnWidths[0],60);
  assert.equal(graph.resizeTableRow(config,0,999).rowHeights[0],240);
  const resized = graph.resizeTableTo(config,482,230);
  assert.deepEqual(resized.columnWidths,[240,240]);
  assert.deepEqual(resized.rowHeights,[84,84]);
  assert.deepEqual(resized.cells,config.cells);
});

test('visual nodes survive normalization and mixed graph publish but cannot join execution edges', () => {
  const workflow = graph.createDefaultGraph();
  const table = graph.createNode('table','table',-400,-200);
  const icon = graph.createNode('icon','icon',600,400);
  const list = graph.createNode('icon-list','list',900,400);
  workflow.nodes.push(table,icon,list);
  const normalized = graph.normalizeGraph(workflow);
  assert.deepEqual(normalized.nodes.slice(-3),workflow.nodes.slice(-3));
  assert.equal(graph.validatePublishGraph(normalized).valid,true);
  normalized.edges.push({id:'bad',source:'trigger-1',target:'table',port:'out'});
  assert.equal(graph.validateGraphShape(normalized).valid,false);
});

test('fit bounds include custom table, icon and icon list dimensions', () => {
  const table = graph.createNode('table','t',-400,-100);
  table.config.columnWidths=[200,200,200];
  table.config.rowHeights=[80,80,80];
  assert.deepEqual(graph.graphBounds({nodes:[table]}),{minX:-400,minY:-100,maxX:202,maxY:202,width:602,height:302});
  const icon = graph.createNode('icon','i',10,20);
  icon.config.width=300; icon.config.height=250;
  assert.deepEqual(graph.nodeSize(icon),{width:300,height:250});
});

test('visual config rejects invalid counts, malformed cells and unsafe icon/color strings', () => {
  const table = graph.createNode('table','t');
  table.config.rows=0;
  assert.equal(graph.validateGraphShape({nodes:[table],edges:[]}).valid,false);
  const icon = graph.createNode('icon','i'); icon.config.iconClass='fas fa-star onclick=evil';
  assert.equal(graph.validateGraphShape({nodes:[icon],edges:[]}).valid,false);
  const list = graph.createNode('icon-list','l'); list.config.color='url(javascript:evil)';
  assert.equal(graph.validateGraphShape({nodes:[list],edges:[]}).valid,false);
});
