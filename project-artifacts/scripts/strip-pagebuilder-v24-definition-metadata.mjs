import { parse } from '@babel/parser';
import { readdir, readFile, writeFile } from 'node:fs/promises';
import { join } from 'node:path';

const root = join(process.cwd(), 'resources', 'pagebuilder_elementor_v24', 'modules');
const metadataKeys = new Set(['label', 'category', 'icon', 'toolbox', 'canvas', 'settings']);

async function definitions(directory) {
  const result = [];
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) result.push(...await definitions(path));
    else if (entry.name === 'definition.js') result.push(path);
  }
  return result;
}

function visit(node, callback) {
  if (!node || typeof node !== 'object') return;
  callback(node);
  for (const [key, value] of Object.entries(node)) {
    if (['loc', 'start', 'end', 'extra'].includes(key)) continue;
    if (Array.isArray(value)) value.forEach((child) => visit(child, callback));
    else if (value && typeof value === 'object' && typeof value.type === 'string') visit(value, callback);
  }
}

function propertyName(property) {
  if (property.type !== 'ObjectProperty' && property.type !== 'ObjectMethod') return '';
  if (property.computed) return '';
  return property.key?.name || property.key?.value || '';
}

let changed = 0;
for (const file of await definitions(root)) {
  const source = await readFile(file, 'utf8');
  const ast = parse(source, { sourceType: 'script', errorRecovery: false });
  let registration = null;
  visit(ast.program, (node) => {
    if (registration || node.type !== 'CallExpression') return;
    const callee = node.callee;
    const property = callee?.type === 'MemberExpression' && !callee.computed
      ? callee.property?.name
      : null;
    if (property === 'register' && node.arguments?.[0]?.type === 'ObjectExpression') {
      registration = node.arguments[0];
    }
  });

  if (!registration) throw new Error(`No registry.register object found: ${file}`);
  const kept = registration.properties.filter((property) => !metadataKeys.has(propertyName(property)));
  const removed = registration.properties.length - kept.length;
  if (!removed) continue;
  const replacement = `{${kept.map((property) => source.slice(property.start, property.end)).join(',')}}`;
  const next = source.slice(0, registration.start) + replacement + source.slice(registration.end);
  parse(next, { sourceType: 'script', errorRecovery: false });
  await writeFile(file, next, 'utf8');
  changed++;
}

console.log(`Stripped duplicated manifest metadata from ${changed} module definitions.`);
