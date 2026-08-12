import assert from 'node:assert/strict';
import { existsSync } from 'node:fs';
import { readFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import vm from 'node:vm';
import { compile } from '@vue/compiler-dom';
import { parse } from '@vue/compiler-sfc';
import { renderToString } from '@vue/server-renderer';
import * as Vue from 'vue';

globalThis.window ??= globalThis;
globalThis.window.matchMedia ??= () => ({
  matches: false,
  addEventListener() {},
  removeEventListener() {},
});

const testDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(testDir, '..');

async function source(relativePath) {
  return readFile(join(rootDir, relativePath), 'utf8');
}

async function loadSfc(relativePath) {
  const filename = join(rootDir, relativePath);
  const contents = await readFile(filename, 'utf8');
  const { descriptor, errors } = parse(contents, { filename });
  assert.deepEqual(errors, []);
  const component = Function(descriptor.script.content.replace(/export\s+default/, 'return'))();
  component.render = Function('Vue', compile(descriptor.template.content, { mode: 'function' }).code)(Vue);
  return component;
}

function editorFor(settingsTab) {
  const EmptyControl = { template: '<div></div>' };
  return {
    settingsTab,
    responsiveDevice: 'desktop',
    responsiveDevices: [],
    widgetAdvancedControls: EmptyControl,
    linkControl: EmptyControl,
    typographyControl: { template: '<div>Typography</div>' },
    textStrokeControl: { template: '<div>Text Stroke</div>' },
    textShadowControl: { template: '<div>Text Shadow</div>' },
    fontFamilies: [],
    chooseMedia() {},
    openProIconLibrary() {},
    chooseProIconSvg() {},
    setResponsiveDevice() {},
    openControlResponsiveMenu() {},
    applyResponsiveDevice() {},
    responsiveDeviceLabel: () => 'Desktop',
    responsiveDeviceIcon: () => 'fas fa-desktop',
    isControlResponsiveMenuOpen: () => false,
    deviceOptionLabel: () => '',
    activeResponsiveKey: (key) => key,
    setResponsiveSetting(target, key, value) { target[key] = value; },
    sizeControlDisplayValue: (node, key, fallback) => Number.parseFloat(node.settings[key] || fallback) || 0,
    sizeControlUnit: (node, key, fallback) => String(node.settings[key] || fallback).match(/[a-z%]+$/i)?.[0] || 'px',
    onSizeControlInput() {},
    setSizeControlUnit() {},
    fontAwesomeStyleLabel: () => 'Brands',
  };
}

const codeSettings = {
  language: 'javascript',
  code: 'const unsafe = "<script>alert(1)</script>";\nreturn unsafe;',
  lineNumbers: true,
  copyButton: true,
  highlightLines: '2',
  wordWrap: true,
  theme: 'light',
  height: '320px',
  fontSize: '15px',
  codeTextColor: '#111827',
  codeBackground: '#ffffff',
  codePaddingTop: '20px',
  codePaddingRight: '20px',
  codePaddingBottom: '20px',
  codePaddingLeft: '20px',
  codeRadiusTop: '8px',
  codeRadiusRight: '8px',
  codeRadiusBottom: '8px',
  codeRadiusLeft: '8px',
  lineNumberColor: '#667085',
  lineNumberBackground: '#f2f4f7',
  gutterWidth: '34px',
  highlightLineColor: '#e0e7ff',
  highlightLineBorderColor: '#6979f8',
  copyButtonTextColor: '#ffffff',
  copyButtonBackground: '#6979f8',
  copyButtonTextColorHover: '#ffffff',
  copyButtonBackgroundHover: '#5868e8',
  copyButtonPaddingTop: '8px',
  copyButtonPaddingRight: '12px',
  copyButtonPaddingBottom: '8px',
  copyButtonPaddingLeft: '12px',
  copyButtonRadiusTop: '4px',
  copyButtonRadiusRight: '4px',
  copyButtonRadiusBottom: '4px',
  copyButtonRadiusLeft: '4px',
};

test('Code Highlight registers as a Pro widget with complete defaults and normalization', async () => {
  const definitionPath = join(rootDir, 'public/js/pagebuilder_elementor_v23/widgets/pro/code-highlight/definition.js');
  assert.equal(existsSync(definitionPath), true, 'code-highlight definition must exist');

  const context = { window: {} };
  vm.runInNewContext(await source('public/js/pagebuilder_elementor_v23/widget-registry.js'), context);
  vm.runInNewContext(await source('public/js/pagebuilder_elementor_v23/widgets/pro/code-highlight/definition.js'), context);

  const definition = context.window.PageBuilderElementorV23Widgets.get('code_highlight');
  const defaults = definition.defaults();

  assert.equal(definition.category, 'pro');
  assert.equal(definition.label, 'Code Highlight');
  assert.equal(defaults.language, 'javascript');
  assert.equal(defaults.lineNumbers, true);
  assert.equal(defaults.copyButton, true);
  assert.equal(defaults.theme, 'dark');
  assert.equal(defaults.height, '300px');
  assert.equal(defaults.fontSize, '14px');
  assert.equal(typeof defaults.code, 'string');
  assert.ok('codeTextColor' in defaults);
  assert.ok('gutterWidth' in defaults);
  assert.ok('copyButtonBackgroundHover' in defaults);

  const settingsSource = await source('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue');
  const languageBlock = settingsSource.match(/codeLanguageOptions\(\)\s*\{([\s\S]*?)\n\s*\},\n\s*codeThemeOptions/)[1];
  assert.ok((languageBlock.match(/option\(/g) || []).length >= 50, 'Language should expose Elementor\'s 50+ language choices');
  for (const language of ['markup', 'html', 'xml', 'svg', 'mathml', 'css', 'javascript', 'actionscript', 'php', 'python', 'json', 'yaml', 'bash', 'sql', 'jsx', 'tsx']) {
    assert.match(languageBlock, new RegExp(`option\\("${language}"`));
  }

  const normalized = definition.normalize({ settings: {
    language: 'not-a-language',
    theme: 'not-a-theme',
    height: 'expression(alert(1))',
    fontSize: 'bad',
    lineNumbers: 0,
    copyButton: 1,
    highlightLines: '2-4, 8, x',
  } });
  assert.equal(normalized.settings.language, 'javascript');
  assert.equal(normalized.settings.theme, 'dark');
  assert.equal(normalized.settings.height, '300px');
  assert.equal(normalized.settings.fontSize, '14px');
  assert.equal(normalized.settings.lineNumbers, false);
  assert.equal(normalized.settings.copyButton, true);
  assert.equal(normalized.settings.highlightLines, '2-4, 8');
});

test('Code Highlight settings expose the complete Content, Style, and Advanced mapping', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue');

  const contentHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'code_highlight', settings: codeSettings },
    editor: editorFor('content'),
  }));
  for (const label of ['Language', 'Code', 'Line Numbers', 'Copy to Clipboard', 'Highlight Lines', 'Word Wrap', 'Theme', 'Height', 'Font Size']) {
    assert.match(contentHtml, new RegExp(label));
  }

  const styleHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'code_highlight', settings: codeSettings },
    editor: editorFor('style'),
  }));
  for (const label of ['Text Color', 'Background Color', 'Typography', 'Padding', 'Border Radius', 'Gutter Width', 'Highlighted Lines', 'Border Color', 'Text Color \\(Hover\\)', 'Background Color \\(Hover\\)']) {
    assert.match(styleHtml, new RegExp(label));
  }

  const advancedHtml = await renderToString(Vue.createSSRApp(component, {
    node: { type: 'code_highlight', settings: codeSettings },
    editor: editorFor('advanced'),
  }));
  assert.match(advancedHtml, /Advanced/);
});

test('Code Highlight canvas renders escaped syntax lines, highlight state, theme, and copy contract', async () => {
  const component = await loadSfc('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
  const html = await renderToString(Vue.createSSRApp(component, {
    item: { id: 'code-highlight-test', type: 'code_highlight', settings: codeSettings },
    responsiveDevice: 'desktop',
  }));

  assert.match(html, /data-code-highlight/);
  assert.match(html, /language-javascript/);
  assert.match(html, /pb-pro-code-highlight__line-number/);
  assert.match(html, /pb-pro-code-highlight__line is-highlighted/);
  assert.match(html, /data-code-copy/);
  assert.match(html, /data-code-source/);
  assert.match(html, /aria-live="polite"/);
  assert.match(html, /&lt;script&gt;alert\(1\)&lt;\/script&gt;/);
  assert.doesNotMatch(html, /<script>alert\(1\)<\/script>/);
  assert.match(html, /--code-highlight-height:320px/);
  assert.match(html, /--code-highlight-font-size:15px/);
});

test('Code Highlight is wired through the v2.3 registry, labels, Advanced gate, Blade, and runtime', async () => {
  const config = await source('config/pagebuilder_elementor_v23_widgets.php');
  const app = await source('public/js/pagebuilder_elementor_v23/app.js');
  const blade = await source('resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');
  const runtime = await source('public/js/pagebuilder_elementor_v23/frontend-runtime.js');

  assert.match(config, /'code_highlight'\s*=>[\s\S]*?'category'\s*=>\s*'pro'/);
  assert.match(app, /code_highlight:\s*['"]Code Highlight['"]/);
  assert.match(app, /code_highlight:\s*['"]fas fa-code['"]/);
  assert.match(app, /code_highlight.*includes\(this\.node\.type\)/s);
  assert.match(blade, /@case\('code_highlight'\)/);
  assert.match(blade, /pb-pro-code-highlight/);
  assert.match(blade, /data-code-source/);
  assert.match(runtime, /initProCodeHighlight/);
  assert.match(runtime, /navigator\.clipboard\.writeText/);
});
