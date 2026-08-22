import { parse } from '@babel/parser';
import { readdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, join, resolve } from 'node:path';

const projectRoot = process.cwd();
const appPath = resolve(projectRoot, 'public/js/pagebuilder_elementor_v24/app.js');
const moduleRoot = resolve(projectRoot, 'resources/pagebuilder_elementor_v24/modules');
let appSource = await readFile(appPath, 'utf8');
const ast = parse(appSource, { sourceType: 'script' });
const iife = ast.program.body[0]?.expression?.callee;
if (!iife || !['FunctionExpression', 'ArrowFunctionExpression'].includes(iife.type)) {
  throw new Error('Expected app.js to be an IIFE.');
}

const body = iife.body.body;
const declarations = new Map();
for (const node of body) {
  if (node.type === 'FunctionDeclaration' && node.id?.name) {
    declarations.set(node.id.name, node);
  }
  if (node.type === 'VariableDeclaration') {
    for (const declarator of node.declarations) {
      if (declarator.id?.type === 'Identifier') declarations.set(declarator.id.name, node);
    }
  }
}

let runtimeStatement = null;
let runtimeObject = null;
for (const node of body) {
  const expression = node.type === 'ExpressionStatement' ? node.expression : null;
  const assignment = expression?.type === 'AssignmentExpression' ? expression : null;
  const left = assignment?.left;
  const isRuntime = left?.type === 'MemberExpression'
    && !left.computed
    && left.object?.name === 'window'
    && left.property?.name === 'PageBuilderElementorV24ComplexWidgetRuntime';
  const object = assignment?.right?.type === 'CallExpression' ? assignment.right.arguments?.[0] : null;
  if (isRuntime && object?.type === 'ObjectExpression') {
    runtimeStatement = node;
    runtimeObject = object;
    break;
  }
}
if (!runtimeStatement || !runtimeObject) throw new Error('Complex widget runtime object not found.');

function keyName(property) {
  return property.key?.name || property.key?.value || '';
}

function dependencyClosure(seedSource, excluded) {
  const selectedNodes = new Map();
  const queue = [seedSource];
  while (queue.length) {
    const source = queue.shift();
    for (const [name, node] of declarations) {
      if (excluded.has(name) || selectedNodes.has(node.start)) continue;
      if (!new RegExp(`\\b${name}\\b`).test(source)) continue;
      const declarationSource = appSource.slice(node.start, node.end);
      selectedNodes.set(node.start, node);
      queue.push(declarationSource);
    }
  }
  return [...selectedNodes.values()].sort((left, right) => left.start - right.start);
}

async function manifests(directory = moduleRoot) {
  const result = [];
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) result.push(...await manifests(path));
    else if (entry.name === 'module.json') result.push(path);
  }
  return result;
}

const moduleByType = new Map();
for (const path of await manifests()) {
  const manifest = JSON.parse(await readFile(path, 'utf8'));
  moduleByType.set(manifest.type, { manifest, directory: dirname(path) });
}

const excluded = new Set(['widgetAdvancedDefaults', 'normalizeWidgetAdvancedSettings']);
const ownedRuntimeTypes = new Set();
for (const property of runtimeObject.properties) {
  const type = keyName(property);
  const module = moduleByType.get(type);
  if (!type || !module || property.type !== 'ObjectProperty') throw new Error(`Unsupported runtime property: ${type}`);
  const implementationSource = appSource.slice(property.value.start, property.value.end);
  const dependencyNodes = dependencyClosure(implementationSource, excluded);
  const dependencySource = dependencyNodes.map((node) => appSource.slice(node.start, node.end)).join('\n\n');
  const definition = `(function (registry) {
\t'use strict';
\tconst widgetAdvancedDefaults = () => registry.advancedDefaults();
\tconst normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

${dependencySource}

\tconst implementation = ${implementationSource};
\tregistry.register({
\t\ttype: ${JSON.stringify(type)},
\t\tdefaults: implementation.defaults,
\t\tnormalize: implementation.normalize,
\t\t...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
\t});
})(window.PageBuilderElementorV24Widgets);
`;
  parse(definition, { sourceType: 'script' });
  await writeFile(join(module.directory, module.manifest.assets.definition), definition, 'utf8');
  ownedRuntimeTypes.add(type);
}

for (const [type, module] of moduleByType) {
  if (ownedRuntimeTypes.has(type)) continue;
  const path = join(module.directory, module.manifest.assets.definition);
  const original = await readFile(path, 'utf8');
  const source = original.replace(
    /window\.PageBuilderElementorV24ComplexWidgetRuntime\?\.image_box\?\.defaults\?\.\(\)\s*\|\|\s*\{\}/g,
    'registry.advancedDefaults()',
  );
  parse(source, { sourceType: 'script' });
  if (source !== original) await writeFile(path, source, 'utf8');
}

const widgetDefaultsNode = declarations.get('widgetAdvancedDefaults');
const normalizeAdvancedNode = declarations.get('normalizeWidgetAdvancedSettings');
const replacements = [
  {
    start: runtimeStatement.start,
    end: runtimeStatement.end,
    source: '// Module-specific defaults and normalizers are owned by their module definitions.',
  },
  {
    start: widgetDefaultsNode.start,
    end: widgetDefaultsNode.end,
    source: 'function widgetAdvancedDefaults() { return widgetRegistry.advancedDefaults(); }',
  },
  {
    start: normalizeAdvancedNode.start,
    end: normalizeAdvancedNode.end,
    source: 'function normalizeWidgetAdvancedSettings(settings) { return widgetRegistry.normalizeAdvanced(settings); }',
  },
].sort((left, right) => right.start - left.start);

for (const replacement of replacements) {
  appSource = appSource.slice(0, replacement.start) + replacement.source + appSource.slice(replacement.end);
}
parse(appSource, { sourceType: 'script' });
await writeFile(appPath, appSource, 'utf8');

console.log(`Extracted editor defaults/normalizers for ${ownedRuntimeTypes.size} complex modules and removed the host runtime map.`);
