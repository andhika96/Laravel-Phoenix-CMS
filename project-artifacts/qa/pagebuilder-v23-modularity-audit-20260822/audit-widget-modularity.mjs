import { execFileSync } from 'node:child_process';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { dirname, join, relative, resolve } from 'node:path';
import vm from 'node:vm';

const root = process.cwd();
const publicRoot = join(root, 'public');
const widgetRoot = join(publicRoot, 'js/pagebuilder_elementor_v23/widgets');
const registryPath = join(publicRoot, 'js/pagebuilder_elementor_v23/widget-registry.js');
const appPath = join(publicRoot, 'js/pagebuilder_elementor_v23/app.js');
const proRendererPath = join(root, 'resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');

const config = JSON.parse(execFileSync('php', [
  '-r',
  '$catalog=require "config/pagebuilder_elementor_v23_widgets.php"; echo json_encode($catalog, JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);',
], { cwd: root, encoding: 'utf8' }));

const normalize = (value) => String(value || '').replaceAll('\\', '/').replace(/^\/+/, '');
const publicFile = (path) => join(publicRoot, normalize(path));
const viewFile = (view) => join(root, 'resources/views', `${String(view).replaceAll('.', '/')}.blade.php`);

function definitionsUnder(directory) {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    if (entry.name.includes('.bak')) return [];
    const path = join(directory, entry.name);
    if (entry.isDirectory()) return definitionsUnder(path);
    return entry.name === 'definition.js' ? [normalize(relative(publicRoot, path))] : [];
  });
}

const registrySource = readFileSync(registryPath, 'utf8');
const appSource = readFileSync(appPath, 'utf8');
const proRendererSource = readFileSync(proRendererPath, 'utf8');
const configEntries = Object.entries(config);

function executeDefinitions(excludedType = '') {
  const context = vm.createContext({
    window: {},
    console: { log() {}, warn() {}, error() {} },
    setTimeout,
    clearTimeout,
  });
  context.window.window = context.window;
  vm.runInContext(registrySource, context, { filename: normalize(relative(root, registryPath)) });

  const errors = [];
  for (const [type, module] of configEntries) {
    if (type === excludedType || !existsSync(publicFile(module.definition))) continue;
    try {
      vm.runInContext(readFileSync(publicFile(module.definition), 'utf8'), context, {
        filename: normalize(module.definition),
      });
    } catch (error) {
      errors.push({ type, message: String(error?.message || error) });
    }
  }

  const registry = context.window.PageBuilderElementorV23Widgets;
  const toolbox = registry.toolbox();
  return {
    types: registry.all().map((entry) => entry.type),
    toolboxTypes: Object.values(toolbox).flat().map((entry) => entry.type),
    errors,
  };
}

const toolboxSeedBlock = appSource.match(/const toolbox\s*=\s*\{([\s\S]*?)\};\s*const registeredToolbox/)?.[1] || '';
const hardcodedToolboxTypes = [...toolboxSeedBlock.matchAll(/\btype\s*:\s*['"]([^'"]+)['"]/g)]
  .map((match) => match[1]);
const baselineRegistry = executeDefinitions();
const baselineSidebar = new Set([...hardcodedToolboxTypes, ...baselineRegistry.toolboxTypes]);

const perType = configEntries.map(([type, module]) => {
  const omitted = executeDefinitions(type);
  const sidebarWithoutDefinition = new Set([...hardcodedToolboxTypes, ...omitted.toolboxTypes]);
  const definitionDir = dirname(normalize(module.definition));
  const canvasDir = dirname(normalize(module.canvas));
  const settingsDir = dirname(normalize(module.settings));

  return {
    type,
    category: module.category,
    toolbox: module.toolbox !== false,
    definition: normalize(module.definition),
    definitionExists: existsSync(publicFile(module.definition)),
    canvasExists: existsSync(publicFile(module.canvas)),
    settingsExists: existsSync(publicFile(module.settings)),
    viewExists: existsSync(viewFile(module.view)),
    jsSelfContained: definitionDir === canvasDir && definitionDir === settingsDir,
    sidebarVisibleBaseline: baselineSidebar.has(type),
    sidebarVisibleWithoutDefinition: sidebarWithoutDefinition.has(type),
    omissionExecutionErrors: omitted.errors,
  };
});

const configDefinitionPaths = new Set(configEntries.map(([, module]) => normalize(module.definition)));
const diskDefinitionPaths = definitionsUnder(widgetRoot).sort();
const configTypes = new Set(configEntries.map(([type]) => type));
const registeredTypes = new Set(baselineRegistry.types);
const proCases = [...proRendererSource.matchAll(/@case\(['"]([^'"]+)['"]\)/g)].map((match) => match[1]);
const viewUsage = Object.entries(configEntries.reduce((groups, [type, module]) => {
  (groups[module.view] ||= []).push(type);
  return groups;
}, {})).map(([view, types]) => ({ view, types }));

const result = {
  generatedAt: new Date().toISOString(),
  projectRoot: root,
  counts: {
    configModules: configEntries.length,
    diskDefinitions: diskDefinitionPaths.length,
    registeredModules: baselineRegistry.types.length,
    toolboxModules: perType.filter((entry) => entry.toolbox).length,
    baselineSidebarModules: baselineSidebar.size,
    jsSelfContainedModules: perType.filter((entry) => entry.jsSelfContained).length,
    sharedCanvasOrSettingsModules: perType.filter((entry) => !entry.jsSelfContained).length,
  },
  hardcodedToolboxTypes,
  folderRemovalSidebarFailures: perType
    .filter((entry) => entry.toolbox && entry.sidebarVisibleWithoutDefinition)
    .map((entry) => entry.type),
  configDefinitionsMissingOnDisk: [...configDefinitionPaths].filter((path) => !existsSync(publicFile(path))),
  diskDefinitionsMissingFromConfig: diskDefinitionPaths.filter((path) => !configDefinitionPaths.has(path)),
  configTypesMissingFromRegistry: [...configTypes].filter((type) => !registeredTypes.has(type)),
  registryTypesMissingFromConfig: [...registeredTypes].filter((type) => !configTypes.has(type)),
  baselineDefinitionExecutionErrors: baselineRegistry.errors,
  editorShellDiscovery: {
    source: 'resources/views/pagebuilder_elementor_v23/editor_shell.blade.php',
    iteratesStaticConfig: true,
    filtersMissingDefinitionFiles: false,
    autoDiscoversWidgetFolders: false,
  },
  frontend: {
    configDrivenViewSelection: true,
    sharedViewGroups: viewUsage.filter((entry) => entry.types.length > 1),
    proRendererCases: proCases,
  },
  perType,
};

console.log(JSON.stringify(result, null, 2));
