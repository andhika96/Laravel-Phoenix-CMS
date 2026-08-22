import { readdirSync, readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

export const pageBuilderV24Root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');

export function pageBuilderV24Modules(root = pageBuilderV24Root) {
  const records = [];
  const visit = (directory) => {
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
      const path = resolve(directory, entry.name);
      if (entry.isDirectory()) visit(path);
      else if (entry.name === 'module.json') {
        records.push({
          directory,
          manifestPath: path,
          manifest: JSON.parse(readFileSync(path, 'utf8')),
        });
      }
    }
  };
  visit(resolve(root, 'resources/pagebuilder_elementor_v24/modules'));
  return records.sort((left, right) => left.manifest.type.localeCompare(right.manifest.type));
}

export function pageBuilderV24Module(type, root = pageBuilderV24Root) {
  const record = pageBuilderV24Modules(root).find((entry) => entry.manifest.type === type);
  if (!record) throw new Error(`Unknown Page Builder v2.4 module: ${type}`);
  return record;
}

export function readPageBuilderV24ModuleAsset(type, asset, root = pageBuilderV24Root) {
  const record = pageBuilderV24Module(type, root);
  const relativePath = record.manifest.assets?.[asset];
  if (!relativePath) return '';
  return readFileSync(resolve(record.directory, relativePath), 'utf8');
}

export function readPageBuilderV24ModuleStyles(root = pageBuilderV24Root) {
  return pageBuilderV24Modules(root)
    .map((record) => record.manifest.assets?.styles
      ? readFileSync(resolve(record.directory, record.manifest.assets.styles), 'utf8')
      : '')
    .filter(Boolean)
    .join('\n');
}

export function readPageBuilderV24EditorStyles(root = pageBuilderV24Root) {
  return [
    readFileSync(resolve(root, 'public/assets/css/pagebuilder_elementor_v24.css'), 'utf8'),
    readPageBuilderV24ModuleStyles(root),
  ].join('\n');
}

export function readPageBuilderV24FrontendStyles(root = pageBuilderV24Root) {
  return [
    readFileSync(resolve(root, 'public/assets/css/frontend_elementor_v24.css'), 'utf8'),
    readPageBuilderV24ModuleStyles(root),
  ].join('\n');
}

export function pageBuilderV24Catalog(root = pageBuilderV24Root) {
  return Object.fromEntries(pageBuilderV24Modules(root).map(({ manifest }) => [manifest.type, manifest]));
}

export function pageBuilderV24CapabilityPrelude(root = pageBuilderV24Root) {
  return `
    const __moduleCatalog = ${JSON.stringify(pageBuilderV24Catalog(root))};
    function moduleDefinition(type) { return __moduleCatalog[String(type || '').trim()] || null; }
    function moduleCapabilities(type) { return moduleDefinition(type)?.capabilities || []; }
    function hasModuleCapability(type, capability) { return moduleCapabilities(type).includes(capability); }
    function moduleTypeForCapability(capability) {
      return Object.values(__moduleCatalog).find((definition) => moduleCapabilities(definition.type).includes(capability))?.type || '';
    }
    function defaultContainerType() { return moduleTypeForCapability('structure-container'); }
    function defaultGridType() { return moduleTypeForCapability('structure-grid'); }
    function isCont(type) { return hasModuleCapability(type, 'structure-container'); }
    function isGrid(type) { return hasModuleCapability(type, 'structure-grid'); }
  `;
}
