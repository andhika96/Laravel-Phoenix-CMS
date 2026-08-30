import assert from 'node:assert/strict';
import test from 'node:test';
import fs from 'node:fs';
import vm from 'node:vm';

const root = new URL('../', import.meta.url);
const source = fs.readFileSync(new URL('public/js/pagebuilder_elementor_v24/static-import-css.js', root), 'utf8');

function manager() {
  const window = {};
  vm.runInNewContext(source, { window, globalThis: window });
  return window.PhoenixStaticImportCss;
}

test('compiled CSS manager preserves user CSS and creates one marked block', () => {
  const api = manager();
  const result = api.replaceGeneratedStaticImportCss(
    '.manual { color: blue; }',
    '.pb-import-root [data-pb-import-node="import-node-1"]{color:red}',
    { sourceHash: 'abc123', generatedRules: 1 },
  );

  assert.equal(result.valid, true);
  assert.equal(result.replacedBlocks, 0);
  assert.equal((result.css.match(/PHOENIX_STATIC_IMPORT_COMPILED_START/g) || []).length, 1);
  assert.match(result.css, /\.manual \{ color: blue; \}/);
  assert.match(result.css, /sourceHash: abc123/);
  assert.doesNotMatch(result.css, /tailwind|bootstrap|cdn\.tailwindcss|--tw-/i);
});

test('compiled CSS manager replaces duplicate blocks and keeps outside CSS byte-for-byte', () => {
  const api = manager();
  const start = api.START_MARKER;
  const end = api.END_MARKER;
  const existing = '.before{color:blue}\n' + start + '\nold-a\n' + end + '\n' + start + '\nold-b\n' + end + '\n.after{color:green}';
  const result = api.replaceGeneratedStaticImportCss(existing, '.new{color:red}', { sourceHash: 'new' });

  assert.equal(result.valid, true);
  assert.equal(result.replacedBlocks, 2);
  assert.equal((result.css.match(/PHOENIX_STATIC_IMPORT_COMPILED_START/g) || []).length, 1);
  assert.match(result.css, /\.before\{color:blue\}/);
  assert.match(result.css, /\.after\{color:green\}/);
  assert.doesNotMatch(result.css, /old-a|old-b/);
});

test('compiled CSS manager fails closed for forbidden markers and malformed blocks', () => {
  const api = manager();
  const malformed = '.manual{color:blue}\n' + api.START_MARKER + '\nnot-closed';
  const result = api.replaceGeneratedStaticImportCss(malformed, '.new{color:red}', { sourceHash: 'x' });

  assert.equal(result.valid, true);
  assert.ok(result.warnings.includes('malformed-generated-block'));
  assert.match(result.css, /not-closed/);

  const forbidden = api.replaceGeneratedStaticImportCss('.manual{}', '.tailwind{--tw-color:red}', { sourceHash: 'x' });
  assert.equal(forbidden.valid, false);
  assert.equal(forbidden.css, '.manual{}');
  assert.ok(forbidden.warnings.includes('forbidden-framework-marker'));
});

test('compiled CSS manager warns when stored source hash differs before replacement', () => {
  const api = manager();
  const existing = api.START_MARKER + '\n/* sourceHash: old */\n.safe{color:blue}' + api.END_MARKER;
  const result = api.replaceGeneratedStaticImportCss(existing, '.safe{color:red}', { sourceHash: 'new' });

  assert.ok(result.warnings.includes('generated-block-source-mismatch'));
  assert.match(result.css, /sourceHash: new/);
});
