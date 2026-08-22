import { readdirSync, readFileSync } from 'node:fs';
import { dirname, join, relative, resolve } from 'node:path';
import vm from 'node:vm';

const projectRoot = resolve(import.meta.dirname, '../..');
const moduleRoot = join(projectRoot, 'resources/pagebuilder_elementor_v24/modules');

function manifests(directory = moduleRoot) {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) return manifests(path);
    return entry.name === 'module.json'
      ? [{ path, directory, manifest: JSON.parse(readFileSync(path, 'utf8')) }]
      : [];
  });
}

const modules = manifests().sort((left, right) => left.manifest.type.localeCompare(right.manifest.type));
const context = { window: {} };
vm.runInNewContext(readFileSync(join(projectRoot, 'public/js/pagebuilder_elementor_v24/widget-registry.js'), 'utf8'), context);
context.window.PageBuilderElementorV24Widgets.configure(Object.fromEntries(modules.map(({ manifest }) => [manifest.type, manifest])));
for (const module of modules) {
  vm.runInNewContext(readFileSync(join(module.directory, module.manifest.assets.definition), 'utf8'), context);
}

const advancedKeys = new Set(Object.keys(context.window.PageBuilderElementorV24Widgets.advancedDefaults()));
const responsiveSuffix = /(?:Tablet|Mobile)$/;
const compoundSuffixes = [
  'FontFamily', 'FontSize', 'FontWeight', 'LineHeight', 'LetterSpacing', 'WordSpacing',
  'TextTransform', 'FontStyle', 'TextDecoration', 'TextShadow', 'TextStrokeWidth', 'TextStrokeColor',
  'PaddingTop', 'PaddingRight', 'PaddingBottom', 'PaddingLeft',
  'MarginTop', 'MarginRight', 'MarginBottom', 'MarginLeft',
  'RadiusTop', 'RadiusRight', 'RadiusBottom', 'RadiusLeft',
];

function referenced(key, source) {
  if (source.includes(key)) return true;
  const baseKey = key.replace(responsiveSuffix, '');
  if (baseKey !== key && source.includes(baseKey)) return true;
  for (const suffix of compoundSuffixes) {
    if (!baseKey.endsWith(suffix)) continue;
    const prefix = baseKey.slice(0, -suffix.length);
    if (prefix && source.includes(prefix) && source.includes(suffix)) return true;
  }
  return false;
}

function settingControlTokens(source) {
  const tokens = new Set();
  const patterns = [
    /v-model(?:\.[\w-]+)*="(?:node\.settings|settings|s)\.([A-Za-z_$][\w$]*)/g,
    /v-model(?:\.[\w-]+)*="(?:item|field|slide|review|button|entry|hotspot|metric)\.([A-Za-z_$][\w$]*)/g,
    /(?<![\w$])(?:node\.settings|settings|s)\.([A-Za-z_$][\w$]*)\s*=\s*/g,
    /\b(?:base|setting-key|url-key|target-key|prefix)=["']([A-Za-z_$][\w$]*)["']/g,
    /(?:activeValue|responsiveValue|setResponsiveSetting|activeResponsiveKey)\(\s*["']([A-Za-z_$][\w$]*)["']/g,
  ];
  for (const pattern of patterns) {
    for (const match of source.matchAll(pattern)) tokens.add(match[1]);
  }
  return [...tokens].sort();
}

const rows = modules.map((module) => {
  const definition = context.window.PageBuilderElementorV24Widgets.get(module.manifest.type);
  const defaults = definition.defaults();
  const ownsCanonicalAdvanced = module.manifest.advanced
    && !(module.manifest.advanced.capabilities || []).includes('minimal-advanced')
    && !(module.manifest.advanced.capabilities || []).includes('legacy-layout');
  const keys = Object.keys(defaults).filter((key) => !(ownsCanonicalAdvanced && advancedKeys.has(key)));
  const settings = readFileSync(join(module.directory, module.manifest.assets.settings), 'utf8');
  const renderSources = [
    readFileSync(join(module.directory, module.manifest.assets.canvas), 'utf8'),
    readFileSync(join(module.directory, module.manifest.assets.view), 'utf8'),
    module.manifest.assets.runtime ? readFileSync(join(module.directory, module.manifest.assets.runtime), 'utf8') : '',
  ].join('\n');
  const controlTokens = settingControlTokens(settings);
  return {
    type: module.manifest.type,
    category: module.manifest.category,
    module: relative(projectRoot, module.directory).replaceAll('\\', '/'),
    defaultKeys: keys.length,
    controlTokens: controlTokens.length,
    missingSettings: keys.filter((key) => !referenced(key, settings)),
    missingCanvasOrFrontend: keys.filter((key) => !referenced(key, renderSources)),
    unboundControlTokens: controlTokens.filter((key) => !referenced(key, renderSources)),
  };
});

const summary = {
  modules: rows.length,
  moduleDefaultKeys: rows.reduce((sum, row) => sum + row.defaultKeys, 0),
  settingControlTokens: rows.reduce((sum, row) => sum + row.controlTokens, 0),
  modulesWithSettingsGaps: rows.filter((row) => row.missingSettings.length).length,
  modulesWithRenderGaps: rows.filter((row) => row.missingCanvasOrFrontend.length).length,
  modulesWithUnboundControls: rows.filter((row) => row.unboundControlTokens.length).length,
};

console.log(JSON.stringify({ summary, rows }, null, 2));
