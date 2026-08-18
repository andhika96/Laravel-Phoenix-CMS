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
  for (const scope of ['widget', 'container', 'grid', 'gridColumn']) {
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
