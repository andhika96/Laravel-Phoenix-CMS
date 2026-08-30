import assert from 'node:assert/strict';
import test from 'node:test';
import fs from 'node:fs';
import vm from 'node:vm';

const root = new URL('../', import.meta.url);
const source = fs.readFileSync(new URL('public/js/pagebuilder_elementor_v24/static-import-compiler.js', root), 'utf8');

function compiler() {
  const window = {};
  vm.runInNewContext(source, { window, globalThis: window, setTimeout, clearTimeout });
  return window.PhoenixStaticImportCompiler;
}

test('compiled CSS rewriter scopes Tailwind arbitrary and state selectors to native markers', () => {
  const api = compiler();
  const result = api.__test.rewriteCss(
    '*,::before,::after{--tw-content:""}.text-xl{font-size:1.25rem}.lg\\:text-\\[6\\.2rem\\]{font-size:6.2rem}@media (min-width:1024px){.lg\\:text-\\[6\\.2rem\\]{font-size:6.2rem}}.hover\\:bg-white\\/5:hover{background-color:rgb(255 255 255 / .05)}#hero{color:red}',
    {
      classMap: {
        'text-xl': ['import-node-1'],
        'lg:text-[6.2rem]': ['import-node-1'],
        'hover:bg-white/5': ['import-node-2'],
      },
      idMap: { hero: ['import-node-1'] },
    },
  );

  assert.match(result.css, /\.pb-import-root/);
  assert.match(result.css, /data-pb-import-node="import-node-1"/);
  assert.match(result.css, /data-pb-import-node="import-node-2"/);
  assert.match(result.css, /@media \(min-width:1024px\)/);
  assert.match(result.css, /--pb-import-content/);
  assert.doesNotMatch(result.css, /\.lg\\:text|\.hover\\:bg|#hero|--tw-/);
  assert.ok(result.stats.rewrittenRules >= 4);

  const sourceCss = api.__test.rewriteCss('.hero{background-image:url(https://images.example/hero.jpg)}', { classMap: { hero: ['import-node-1'] }, idMap: {} }, { stripUrls: false });
  assert.match(sourceCss.css, /background-image:url\(https:\/\/images\.example\/hero\.jpg\)/);
});

test('compiled CSS validator rejects framework markers and unbalanced output', () => {
  const api = compiler();

  assert.equal(api.__test.validateCompiledCss('.safe{color:red}').valid, true);
  assert.equal(api.__test.validateCompiledCss('.tailwind{color:red}').valid, false);
  assert.equal(api.__test.validateCompiledCss('.safe{color:red').valid, false);
});

test('compiled display utility rules override native inline display only for mapped source display classes', () => {
  const api = compiler();
  const result = api.__test.rewriteCss(
    '.hidden{display:none}.xl\\:flex{display:flex}.custom-layout{display:grid}',
    {
      classMap: {
        hidden: ['import-node-1'],
        'xl:flex': ['import-node-2'],
        'custom-layout': ['import-node-3'],
      },
      idMap: {},
    },
  );

  assert.match(result.css, /data-pb-import-node="import-node-1"\]\{display:none !important\}/);
  assert.match(result.css, /data-pb-import-node="import-node-2"\]\{display:flex !important\}/);
  assert.match(result.css, /data-pb-import-node="import-node-3"\]\{display:grid\}/);
});

test('compiled visual rules override native widget inline styles without forcing structural properties', () => {
  const api = compiler();
  const result = api.__test.rewriteCss(
    '.display{font-family:Cormorant Garamond,serif}.text-6xl{font-size:3.75rem}.text-cream{color:#f4eddf}.grid{display:grid}.p-6{padding:1.5rem}.w-full{width:100%}',
    {
      classMap: {
        display: ['import-node-1'],
        'text-6xl': ['import-node-1'],
        'text-cream': ['import-node-1'],
        grid: ['import-node-2'],
        'p-6': ['import-node-2'],
        'w-full': ['import-node-2'],
      },
      idMap: {},
    },
  );

  assert.match(result.css, /font-family:Cormorant Garamond,serif(?! !important)/);
  assert.match(result.css, /font-size:3\.75rem(?! !important)/);
  assert.match(result.css, /color:#f4eddf(?! !important)/);
  assert.match(result.css, /display:grid !important/);
  assert.match(result.css, /padding:1\.5rem(?! !important)/);
  assert.match(result.css, /width:100%(?! !important)/);
});

test('computed-style scanner serializes measured CSS fields and bounds into a safe snapshot shape', () => {
  const api = compiler();
  const style = {
    fontSize: '62px',
    color: 'rgb(244, 237, 223)',
    getPropertyValue(property) {
      return {
        'padding-top': '24px',
        'padding-right': '20px',
        'display': 'grid',
        'grid-template-columns': '1fr 1fr',
      }[property] || '';
    },
  };
  const serialized = api.__test.serializeComputedStyle(style);
  assert.equal(serialized.paddingTop, '24px');
  assert.equal(serialized.paddingRight, '20px');
  assert.equal(serialized.display, 'grid');
  assert.equal(serialized.gridTemplateColumns, '1fr 1fr');
  assert.equal(serialized.fontSize, '62px');
  assert.equal(serialized.color, 'rgb(244, 237, 223)');
  assert.equal(Object.prototype.hasOwnProperty.call(serialized, 'onclick'), false);

  assert.deepEqual(JSON.parse(JSON.stringify(api.__test.serializeBounds({ left: 1.2345, top: 2.3456, right: 301.2345, bottom: 202.3456, width: 300, height: 200 }))), {
    x: 1.235,
    y: 2.346,
    top: 2.346,
    right: 301.235,
    bottom: 202.346,
    width: 300,
    height: 200,
  });
});

test('computed-style scanner document contains its own scan protocol and never source scripts', () => {
  const api = compiler();
  const documentHtml = api.__test.compilerDocument({
    html: '<!doctype html><html><head></head><body><section data-pb-import-node="import-node-1"><h1>Safe</h1></section></body></html>',
    frameworks: [],
    classMap: {},
    idMap: {},
  }, '', 'request-1', 'scan');

  assert.match(documentHtml, /pb-static-import-scan-ready/);
  assert.match(documentHtml, /pb-static-import-scan-result/);
  assert.match(documentHtml, /getComputedStyle/);
  assert.match(documentHtml, /getBoundingClientRect/);
  assert.doesNotMatch(documentHtml, /payload\.scripts|sourceScripts\.inlineCode/);
});

test('computed-style scanner measures each viewport, merges marker snapshots, and cleans its iframe', async () => {
  const listeners = new Map();
  const frames = [];
  const window = {
    addEventListener(type, handler) { listeners.set(type, handler); },
    removeEventListener(type, handler) { if (listeners.get(type) === handler) listeners.delete(type); },
    dispatchMessage(event) { listeners.get('message')?.(event); },
  };
  const document = {
    body: {
      appendChild(frame) {
        frame.parentNode = this;
        const contentWindow = {
          postMessage(message) {
            setTimeout(() => window.dispatchMessage({
              source: contentWindow,
              data: {
                type: 'pb-static-import-scan-result',
                requestId: JSON.parse(frame.srcdoc.match(/requestId:([^,]+)/)?.[1] || '""'),
                viewport: message.viewport,
                sections: [{ marker: 'import-node-section', tag: 'section', sourceId: 'hero', bounds: { width: message.viewport.width }, fallback: false }],
                nodes: [{ marker: 'import-node-heading', parentMarker: 'import-node-section', tag: 'h1', bounds: { width: message.viewport.width - 40 }, computed: { fontSize: message.viewport.width + 'px' }, pseudo: {} }],
              },
            }), 0);
          },
        };
        frame.contentWindow = contentWindow;
        frames.push(frame);
        setTimeout(() => window.dispatchMessage({
          source: contentWindow,
          data: {
            type: 'pb-static-import-scan-ready',
            requestId: JSON.parse(frame.srcdoc.match(/requestId:([^,]+)/)?.[1] || '""'),
          },
        }), 0);
      },
      removeChild(frame) { frame.parentNode = null; },
    },
    createElement() { return { style: {}, setAttribute() {} }; },
  };
  const context = { window, globalThis: window, document, setTimeout, clearTimeout };
  vm.runInNewContext(source, context);
  const stages = [];
  const api = window.PhoenixStaticImportCompiler;
  const result = await api.scanComputedStyles({
    html: '<!doctype html><html><head></head><body><section data-pb-import-node="import-node-section"><h1 data-pb-import-node="import-node-heading">Hero</h1></section></body></html>',
    frameworks: [],
    classMap: {},
    idMap: {},
  }, {
    viewports: [{ key: 'mobile', width: 390, height: 900 }, { key: 'desktop', width: 1180, height: 900 }],
    onProgress: (stage) => stages.push(stage),
  });

  assert.equal(frames.length, 1);
  assert.equal(frames[0].parentNode, null);
  assert.deepEqual(JSON.parse(JSON.stringify(result.sections[0].bounds)), { mobile: { width: 390 }, desktop: { width: 1180 } });
  assert.deepEqual(JSON.parse(JSON.stringify(result.nodes[0].computed)), { mobile: { fontSize: '390px' }, desktop: { fontSize: '1180px' } });
  assert.deepEqual(stages, ['scan-sections', 'measure-layout', 'measure-layout', 'cleanup']);
});

test('computed-style scanner cancellation removes the iframe and uses a typed scan error', async () => {
  const listeners = new Map();
  let frame = null;
  const window = {
    addEventListener(type, handler) { listeners.set(type, handler); },
    removeEventListener(type, handler) { if (listeners.get(type) === handler) listeners.delete(type); },
  };
  const document = {
    body: {
      appendChild(value) { frame = value; value.parentNode = this; value.contentWindow = {}; },
      removeChild(value) { value.parentNode = null; },
    },
    createElement() { return { style: {}, setAttribute() {} }; },
  };
  const context = { window, globalThis: window, document, setTimeout, clearTimeout };
  vm.runInNewContext(source, context);
  const controller = new AbortController();
  const promise = window.PhoenixStaticImportCompiler.scanComputedStyles({
    html: '<!doctype html><html><body><section data-pb-import-node="import-node-section"></section></body></html>',
    frameworks: [],
    classMap: {},
    idMap: {},
  }, { signal: controller.signal });
  controller.abort();

  await assert.rejects(promise, (error) => error?.code === 'scan-cancelled');
  assert.equal(frame?.parentNode, null);
  assert.equal(listeners.has('message'), false);
});

test('residual CSS removes measured native-owned layout properties but keeps visual and state rules', () => {
  const api = compiler();
  const result = api.__test.filterResidualCss(
    '.pb-import-root [data-pb-import-node="import-node-1"]{display:grid;padding:24px;margin:10px;width:1180px;grid-template-columns:1fr 1fr;border:1px solid #ffffff;border-radius:8px;color:#f4eddf;background-color:#071a2d}'
      + '.pb-import-root [data-pb-import-node="import-node-1"]::before{display:block;width:20px;height:20px;content:""}'
      + '@media (min-width:1024px){.pb-import-root [data-pb-import-node="import-node-1"]{font-size:72px;padding:32px}}'
      + '.pb-import-root [data-pb-import-node="import-node-1"]:hover{transform:translateY(-2px);padding:40px}',
    { nodes: [{ marker: 'import-node-1' }] },
  );

  assert.doesNotMatch(result.css, /display:grid|padding:24px|padding:32px|margin:10px|width:1180px|grid-template-columns|border:1px solid|border-radius:8px/);
  assert.match(result.css, /color:#f4eddf/);
  assert.match(result.css, /background-color:#071a2d/);
  assert.match(result.css, /::before\{display:block;width:20px;height:20px/);
  assert.match(result.css, /@media \(min-width:1024px\)/);
  assert.match(result.css, /font-size:72px/);
  assert.match(result.css, /:hover\{transform:translateY\(-2px\);padding:40px\}/);
  assert.equal(result.stats.removedProperties, 8);
});

test('residual CSS keeps structural rules for scanned descendants without a native layout node', () => {
  const api = compiler();
  const result = api.__test.filterResidualCss(
    '.pb-import-root [data-pb-import-node="import-node-form"]{padding:24px;background:#071a2d}'
      + '.pb-import-root [data-pb-import-node="import-node-input"]{padding:12px;width:100%;border:1px solid #d0d5dd}',
    { nodes: [{ marker: 'import-node-form' }, { marker: 'import-node-input' }] },
    { ownedMarkers: ['import-node-form'] },
  );

  assert.doesNotMatch(result.css, /padding:24px/);
  assert.match(result.css, /data-pb-import-node="import-node-input"\]\{padding:12px;width:100%;border:1px solid #d0d5dd\}/);
  assert.equal(result.stats.removedProperties, 1);
});

test('compiled Canvas adapter evaluates generated width breakpoints against Canvas width only', () => {
  const api = compiler();
  const css = [
    '@media (min-width: 1280px){.manual-only{display:block}}',
    '/* PHOENIX_STATIC_IMPORT_COMPILED_START */',
    '.pb-import-root .base{display:block}',
    '@media (min-width:640px){.pb-import-root .sm{display:grid}}',
    '@media (min-width:1024px){.pb-import-root .lg{font-size:6.2rem}}',
    '@media (min-width:1280px){.pb-import-root .xl{display:flex}}',
    '@media (prefers-reduced-motion: reduce){.pb-import-root .motion{transition:none}}',
    '/* PHOENIX_STATIC_IMPORT_COMPILED_END */',
  ].join('\n');

  const desktop1180 = api.adaptCompiledCssForCanvas(css, 1180);
  assert.match(desktop1180, /\.manual-only\{display:block\}/);
  assert.match(desktop1180, /\.pb-import-root \.sm\{display:grid\}/);
  assert.match(desktop1180, /\.pb-import-root \.lg\{font-size:6\.2rem\}/);
  assert.doesNotMatch(desktop1180, /\.pb-import-root \.xl\{display:flex\}/);
  assert.match(desktop1180, /@media \(prefers-reduced-motion: reduce\)/);

  const mobile390 = api.adaptCompiledCssForCanvas(css, 390);
  assert.match(mobile390, /\.pb-import-root \.base\{display:block\}/);
  assert.doesNotMatch(mobile390, /\.pb-import-root \.sm\{display:grid\}/);
  assert.doesNotMatch(mobile390, /\.pb-import-root \.lg\{font-size:6\.2rem\}/);
});

test('compiler contract is ephemeral and never uses source script content', () => {
  const api = compiler();

  assert.equal(typeof api.compile, 'function');
  assert.match(source, /sandbox="allow-scripts"/);
  assert.match(source, /finally/);
  assert.match(source, /removeChild/);
  assert.doesNotMatch(source, /payload\.scripts|sourceScripts\.inlineCode/);
});

test('compiler removes its temporary iframe on success and reports progress stages', async () => {
  const listeners = new Map();
  const frames = [];
  const window = {
    addEventListener(type, handler) { listeners.set(type, handler); },
    removeEventListener(type, handler) { if (listeners.get(type) === handler) listeners.delete(type); },
    dispatchMessage(event) { listeners.get('message')?.(event); },
  };
  const document = {
    body: {
      appendChild(frame) {
        frame.parentNode = this;
        frame.contentWindow = {};
        frames.push(frame);
        setTimeout(() => window.dispatchMessage({
          source: frame.contentWindow,
          data: {
            type: 'pb-static-import-compiled-css',
            requestId: frame.srcdoc.match(/requestId:("[^"]+")/)?.[1] ? JSON.parse(frame.srcdoc.match(/requestId:("[^"]+")/)[1]) : '',
            css: '.text-xl{font-size:1.25rem}',
          },
        }), 0);
      },
      removeChild(frame) { frame.parentNode = null; },
    },
    createElement() {
      return {
        style: {},
        setAttribute() {},
        addEventListener() {},
      };
    },
  };
  const context = { window, globalThis: window, document, setTimeout, clearTimeout };
  vm.runInNewContext(source, context);
  const stages = [];
  const result = await window.PhoenixStaticImportCompiler.compile({
    html: '<!doctype html><html><head></head><body><h1 class="text-xl">Compiled</h1></body></html>',
    frameworks: ['tailwind'],
    classMap: { 'text-xl': ['import-node-1'] },
    idMap: {},
    sourceCss: '',
  }, { onProgress: (stage) => stages.push(stage) });

  assert.match(result.css, /data-pb-import-node="import-node-1"/);
  assert.deepEqual(frames.map((frame) => frame.parentNode), [null]);
  assert.deepEqual(stages, ['prepare', 'load-framework', 'compile', 'extract', 'rewrite', 'validate', 'cleanup']);
  assert.equal(listeners.has('message'), false);
});

test('compiler cancellation rejects with a typed error and removes the temporary iframe', async () => {
  const listeners = new Map();
  let frame = null;
  const window = {
    addEventListener(type, handler) { listeners.set(type, handler); },
    removeEventListener(type, handler) { if (listeners.get(type) === handler) listeners.delete(type); },
  };
  const document = {
    body: {
      appendChild(value) { frame = value; value.parentNode = this; value.contentWindow = {}; },
      removeChild(value) { value.parentNode = null; },
    },
    createElement() { return { style: {}, setAttribute() {}, addEventListener() {} }; },
  };
  const context = { window, globalThis: window, document, setTimeout, clearTimeout };
  vm.runInNewContext(source, context);
  const controller = new AbortController();
  const promise = window.PhoenixStaticImportCompiler.compile({
    html: '<!doctype html><html><body><h1 class="text-xl">Compiled</h1></body></html>',
    frameworks: ['tailwind'],
    classMap: { 'text-xl': ['import-node-1'] },
    idMap: {},
  }, { signal: controller.signal });
  controller.abort();

  await assert.rejects(promise, (error) => error?.code === 'compile-cancelled');
  assert.equal(frame?.parentNode, null);
  assert.equal(listeners.has('message'), false);
});

test('compiler uses only the fixed Bootstrap 5.3.3 stylesheet URL and inlines fetched CSS', async () => {
  const listeners = new Map();
  let requestedUrl = '';
  let frame = null;
  const window = {
    addEventListener(type, handler) { listeners.set(type, handler); },
    removeEventListener(type, handler) { if (listeners.get(type) === handler) listeners.delete(type); },
    dispatchMessage(event) { listeners.get('message')?.(event); },
  };
  const document = {
    body: {
      appendChild(value) {
        frame = value;
        value.parentNode = this;
        value.contentWindow = {};
        setTimeout(() => window.dispatchMessage({
          source: value.contentWindow,
          data: {
            type: 'pb-static-import-compiled-css',
            requestId: JSON.parse(value.srcdoc.match(/requestId:("[^"]+")/)[1]),
            css: '.btn{display:inline-block}',
          },
        }), 0);
      },
      removeChild(value) { value.parentNode = null; },
    },
    createElement() { return { style: {}, setAttribute() {}, addEventListener() {} }; },
  };
  const context = { window, globalThis: window, document, setTimeout, clearTimeout };
  vm.runInNewContext(source, context);
  const result = await window.PhoenixStaticImportCompiler.compile({
    html: '<!doctype html><html><head></head><body><a class="btn">Button</a></body></html>',
    frameworks: ['bootstrap5'],
    classMap: { btn: ['import-node-1'] },
    idMap: {},
  }, {
    fetchText: async (url) => { requestedUrl = url; return '.btn{display:inline-block}'; },
  });

  assert.equal(requestedUrl, 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
  assert.match(frame.srcdoc, /data-pb-compiled-bootstrap/);
  assert.match(result.css, /data-pb-import-node="import-node-1"/);
  assert.equal(frame.parentNode, null);
});
