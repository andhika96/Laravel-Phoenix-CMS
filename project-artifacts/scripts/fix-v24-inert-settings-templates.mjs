import { readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { join, relative, resolve } from 'node:path';
import { parse as parseTemplate } from '@vue/compiler-dom';
import { parse as parseSfc } from '@vue/compiler-sfc';

const projectRoot = resolve(import.meta.dirname, '../..');
const modulesRoot = join(projectRoot, 'resources/pagebuilder_elementor_v24/modules');

function settingsFiles(directory) {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) return settingsFiles(path);
    return entry.name === 'Settings.vue' ? [path] : [];
  });
}

function inertTemplates(templateSource) {
  const matches = [];
  const visit = (node) => {
    if (node?.type === 1 && node.tag === 'template' && node.props.length === 0) {
      matches.push(node);
    }
    for (const child of node?.children || []) visit(child);
  };
  visit(parseTemplate(templateSource));
  return matches;
}

const changed = [];
for (const path of settingsFiles(modulesRoot)) {
  const source = readFileSync(path, 'utf8');
  const { descriptor, errors } = parseSfc(source, { filename: path });
  if (errors.length) throw new Error(`${relative(projectRoot, path)} did not parse before rewrite`);

  const nodes = inertTemplates(descriptor.template.content);
  if (!nodes.length) continue;

  const ranges = nodes.flatMap((node) => {
    const openLength = node.loc.source.indexOf('>') + 1;
    const closeOffset = node.loc.source.lastIndexOf('</template>');
    if (openLength <= 0 || closeOffset < 0) {
      throw new Error(`Unable to locate inert wrapper boundaries in ${relative(projectRoot, path)}`);
    }
    const base = descriptor.template.loc.start.offset + node.loc.start.offset;
    return [
      [base, base + openLength],
      [base + closeOffset, base + closeOffset + '</template>'.length],
    ];
  });

  let updated = source;
  for (const [start, end] of ranges.sort((a, b) => b[0] - a[0])) {
    updated = updated.slice(0, start) + updated.slice(end);
  }
  writeFileSync(path, updated, 'utf8');
  changed.push({ path: relative(projectRoot, path), wrappersRemoved: nodes.length });
}

console.log(JSON.stringify({ changed }, null, 2));
