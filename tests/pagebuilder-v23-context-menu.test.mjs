import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const app = readFileSync(`${root}/public/js/pagebuilder_elementor_v23/app.js`, 'utf8');
const css = readFileSync(`${root}/public/assets/css/pagebuilder_elementor_v23.css`, 'utf8');

test('context menu keeps per-target definitions behind one shared shell', () => {
  assert.match(app, /const contextMenuDefinitions\s*=\s*Object\.freeze\(/);
  for (const scope of ['widget', 'container', 'grid', 'gridColumn', 'formField']) {
    assert.match(app, new RegExp(`${scope}:\\s*Object\\.freeze\\(`), `${scope} context menu definition should exist`);
  }

  assert.match(app, /function openContextMenu\(event, target\)/);
  assert.match(app, /function runContextMenuAction\(item\)/);
  assert.match(app, /@contextmenu\.stop\.prevent="onContextMenu\(\$event, node\)"/);
  assert.match(app, /kind: 'gridColumn'/);
  assert.match(app, /class="pb-context-menu"/);
  assert.match(app, /@keydown\.esc\.window="closeContextMenu"/);
  assert.match(app, /Ctrl \+ D/);
  assert.match(app, /Save as global/);
  assert.match(app, /Save as Template/);
  assert.match(app, /Paste from other site/);
  assert.match(app, /navigator\?\.clipboard/);
  assert.match(app, /clipboard\.writeText/);
  assert.match(app, /clipboard\.readText/);
  assert.match(app, /function pasteExternalContextClipboard\(meta, node\)/);
});

test('context menu closes from capture-phase clicks outside the shared popup', () => {
  const helperBlock = app.match(/\/\/ V23_CONTEXT_TARGET_HELPERS_START([\s\S]*?)\/\/ V23_CONTEXT_TARGET_HELPERS_END/)?.[1];
  assert.ok(helperBlock, 'context target helper block should exist');
  assert.match(helperBlock, /function isContextMenuClickInsideMenu\(event\)/);

  const context = {};
  vm.runInNewContext(`${helperBlock}\nthis.helpers = { isContextMenuClickInsideMenu };`, context);
  const insideMenu = { closest: (selector) => selector === '.pb-context-menu' ? {} : null };
  const outsideMenu = { closest: () => null };

  assert.equal(context.helpers.isContextMenuClickInsideMenu({ target: insideMenu }), true);
  assert.equal(context.helpers.isContextMenuClickInsideMenu({ target: outsideMenu }), false);
  assert.equal(context.helpers.isContextMenuClickInsideMenu({ target: null }), false);
  assert.match(app, /class="builder-app"[^>]*@click\.capture="closeContextMenuFromOutside"/);
  assert.match(app, /function closeContextMenuFromOutside\(event\)\s*\{\s*if \(!isContextMenuClickInsideMenu\(event\)\) closeContextMenu\(\);\s*\}/);
});

test('context menu resolves the deepest Form field without changing outer widget targets', () => {
  const helperBlock = app.match(/\/\/ V23_CONTEXT_TARGET_HELPERS_START([\s\S]*?)\/\/ V23_CONTEXT_TARGET_HELPERS_END/)?.[1];
  assert.ok(helperBlock, 'context target helper block should exist');

  const context = {};
  vm.runInNewContext(`${helperBlock}\nthis.helpers = { resolveContextMenuTarget, findFormFieldRecord };`, context);

  const node = {
    id: 'form-1',
    type: 'form',
    settings: {
      rowGrid: {
        steps: [{
          id: 'step-root',
          rows: [{
            id: 'row-1',
            columns: [{
              id: 'column-1',
              items: [{ id: 'field:email', kind: 'field', field: { id: 'email', label: 'Email' } }],
            }],
          }],
        }],
      },
    },
  };
  const nodeElement = { dataset: { nodeId: 'form-1' } };
  const itemElement = { dataset: { formItemId: 'field:email' } };
  const fieldElement = {
    dataset: { proFormField: 'email' },
    closest(selector) {
      if (selector === '[data-node-id]') return nodeElement;
      if (selector === '[data-form-item-id]') return itemElement;
      return null;
    },
  };
  const event = {
    target: {
      closest(selector) {
        return selector === '[data-pro-form-field]' ? fieldElement : null;
      },
    },
  };

  const target = context.helpers.resolveContextMenuTarget(event, node);
  assert.equal(target.kind, 'formField');
  assert.equal(target.node, node);
  assert.equal(target.fieldId, 'email');
  assert.equal(target.itemId, 'field:email');
  assert.equal(context.helpers.findFormFieldRecord(node, target).item.field.label, 'Email');
  assert.equal(context.helpers.resolveContextMenuTarget({ target: { closest: () => null } }, node), node);
});

test('Inspect element is available for every context target and exposes the exact DOM reference', () => {
  const inspectItems = Array.from(app.matchAll(/\{ id: 'inspect', label: 'Inspect element' \}/g));
  assert.ok(inspectItems.length >= 5, 'widget, container, grid, grid column, and Form field menus should expose Inspect element');
  assert.match(app, /window\.__PB_INSPECTED_ELEMENT__\s*=\s*element/);
  assert.match(app, /console\.dir\(element\)/);
  assert.match(app, /classList\.add\('pb-inspect-target'\)/);
  assert.match(app, /writeText\?\.\(selector\)\?\.catch\?\./, 'Inspect must remain safe when Clipboard API is unavailable');
  assert.match(css, /\.pb-inspect-target\s*\{/);
});

test('external clipboard accepts only versioned Phoenix v2.3 node payloads', () => {
  const helperBlock = app.match(/\/\/ V23_CONTEXT_CLIPBOARD_HELPERS_START([\s\S]*?)\/\/ V23_CONTEXT_CLIPBOARD_HELPERS_END/)?.[1];
  assert.ok(helperBlock, 'clipboard helper block should exist');
  const constantsBlock = app.match(/const CONTEXT_CLIPBOARD_SOURCE[\s\S]*?const CONTEXT_CLIPBOARD_MAX_NODES = 50;/)?.[0];
  assert.ok(constantsBlock, 'clipboard constants should exist');

  const context = {};
  vm.runInNewContext(`${constantsBlock}\n${helperBlock}\nthis.helpers = { contextClipboardPayload, parseContextClipboardPayload };`, context);

  const node = { id: 'n_source', type: 'heading', settings: { title: 'Copied heading' } };
  const payload = context.helpers.contextClipboardPayload(node);
  const parsed = context.helpers.parseContextClipboardPayload(payload);
  assert.equal(parsed.reason, '');
  assert.equal(JSON.stringify(parsed.nodes), JSON.stringify([node]));

  assert.equal(context.helpers.parseContextClipboardPayload(JSON.stringify({ source: 'elementor', version: 1, node })).reason, 'unsupported');
  assert.equal(context.helpers.parseContextClipboardPayload('{"source":"phoenix-pagebuilder-v23","version":1,"node":{"type":"heading"}}').reason, 'invalid');
  assert.equal(context.helpers.parseContextClipboardPayload('not-json').reason, 'invalid-json');
});

test('context menu uses the light Phoenix surface and stays accessible', () => {
  assert.match(css, /\.pb-context-menu\s*\{/);
  assert.match(css, /\.pb-context-menu-item\s*\{/);
  assert.match(css, /background:\s*var\(--panel-bg\)/);
  assert.match(css, /box-shadow:\s*var\(--shadow-lg\)/);
  assert.match(css, /\.pb-context-menu-item:focus-visible/);
  assert.match(css, /\.pb-context-menu-item\.danger/);
});
