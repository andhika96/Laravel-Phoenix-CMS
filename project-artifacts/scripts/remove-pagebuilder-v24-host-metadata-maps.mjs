import { parse } from '@babel/parser';
import { readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';

const path = resolve(process.cwd(), 'public/js/pagebuilder_elementor_v24/app.js');
let source = await readFile(path, 'utf8');
const ast = parse(source, { sourceType: 'script' });
const iife = ast.program.body[0]?.expression?.callee;
const body = iife?.body?.body || [];
const replacements = [];

for (const node of body) {
  if (node.type === 'VariableDeclaration') {
    const names = node.declarations.map((entry) => entry.id?.name).filter(Boolean);
    if (names.some((name) => ['BASE_NODE_LABELS', 'NODE_LABEL_ICONS'].includes(name))) {
      replacements.push({ start: node.start, end: node.end, source: '' });
    }
  }
  if (node.type === 'FunctionDeclaration' && node.id?.name === 'baseNodeLabel') {
    replacements.push({
      start: node.start,
      end: node.end,
      source: "function baseNodeLabel(type, fallback = 'Widget') { return moduleDefinition(type)?.label || fallback; }",
    });
  }
  if (node.type === 'FunctionDeclaration' && node.id?.name === 'nodeLabelIcon') {
    replacements.push({
      start: node.start,
      end: node.end,
      source: `function nodeLabelIcon(nodeOrType) {
\t\tconst node = nodeOrType && typeof nodeOrType === 'object' ? nodeOrType : null;
\t\tif (node && isCont(node.type) && (node.settings?.displayType || 'flex') === 'grid') {
\t\t\treturn moduleDefinition(defaultGridType())?.icon || moduleDefinition(node.type)?.icon || 'fas fa-cube';
\t\t}
\t\tconst type = node ? node.type : nodeOrType;
\t\treturn moduleDefinition(type)?.icon || 'fas fa-cube';
\t}`,
    });
  }
}

for (const replacement of replacements.sort((left, right) => right.start - left.start)) {
  source = source.slice(0, replacement.start) + replacement.source + source.slice(replacement.end);
}
source = source.replace(
  "if (type === 'heading') normalizeWidgetAdvancedSettings(node.settings);",
  "if (hasModuleCapability(type, 'advanced-normalization')) normalizeWidgetAdvancedSettings(node.settings);",
);
parse(source, { sourceType: 'script' });
await writeFile(path, source, 'utf8');
console.log('Removed host label/icon maps and switched node metadata to the module catalog.');
