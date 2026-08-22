import assert from 'node:assert/strict';
import test from 'node:test';
import { readdirSync, readFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { parse as parseTemplate } from '@vue/compiler-dom';
import { parse as parseSfc } from '@vue/compiler-sfc';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../resources/pagebuilder_elementor_v24/modules');

function findSettingsFiles(directory) {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) return findSettingsFiles(path);
    return entry.name === 'Settings.vue' ? [path] : [];
  });
}

function inertTemplateLocations(source, path) {
  const { descriptor, errors } = parseSfc(source, { filename: path });
  assert.deepEqual(errors, [], `${path} should parse as an SFC`);

  const locations = [];
  const visit = (node) => {
    if (node?.type === 1 && node.tag === 'template' && node.props.length === 0) {
      locations.push(node.loc.start.line);
    }
    for (const child of node?.children || []) visit(child);
  };

  visit(parseTemplate(descriptor.template.content));
  return locations;
}

function tabControlSummary(source, path) {
  const { descriptor, errors } = parseSfc(source, { filename: path });
  assert.deepEqual(errors, [], `${path} should parse as an SFC`);

  const summaries = [];
  const controlTags = new Set(['button', 'component', 'details', 'input', 'select', 'summary', 'textarea']);
  const visit = (node) => {
    if (node?.type === 1) {
      const condition = node.props.find((prop) => prop.type === 7 && ['if', 'show'].includes(prop.name));
      const tab = condition?.exp?.content?.match(/editor\.settingsTab\s*===\s*['"](content|layout|style|advanced)['"]/)?.[1];
      if (tab) {
        let controls = 0;
        const countControls = (child) => {
          if (child?.type === 1 && (child.tagType === 1 || controlTags.has(child.tag))) controls += 1;
          for (const grandchild of child?.children || []) countControls(grandchild);
        };
        for (const child of node.children || []) countControls(child);
        summaries.push({ tab, controls, line: node.loc.start.line });
      }
    }
    for (const child of node?.children || []) visit(child);
  };

  visit(parseTemplate(descriptor.template.content));
  return summaries;
}

test('v2.4 widget settings mount only the active editor tab', () => {
  const settingsFiles = findSettingsFiles(root);
  const tabbedSettings = settingsFiles.filter((path) => {
    return readFileSync(path, 'utf8').includes('editor.settingsTab');
  });

  assert.ok(tabbedSettings.length > 0, 'expected tabbed Settings.vue files');

  for (const path of tabbedSettings) {
    const source = readFileSync(path, 'utf8');
    const conditionalTabs = [...source.matchAll(/v-(if|show)="editor\.settingsTab\s*===\s*['"](?:content|layout|style|advanced)['"]"/g)];
    assert.ok(conditionalTabs.length >= 2, `${path} should expose at least two editor tabs`);
    assert.ok(conditionalTabs.every((match) => match[1] === 'if'), `${path} should conditionally mount every editor tab`);
    assert.doesNotMatch(source, /v-show="editor\.settingsTab\s*===\s*['"](?:content|layout|style|advanced)['"]"/, `${path} keeps hidden tabs mounted`);
  }
});

test('v2.4 settings never wrap visible controls in inert native template elements', () => {
  const settingsFiles = findSettingsFiles(root);

  for (const path of settingsFiles) {
    const source = readFileSync(path, 'utf8');
    assert.deepEqual(
      inertTemplateLocations(source, path),
      [],
      `${path} contains a plain nested <template> whose controls have zero layout height in the browser`,
    );
  }
});

test('every declared v2.4 Settings tab owns renderable controls', () => {
  const settingsFiles = findSettingsFiles(root);

  for (const path of settingsFiles) {
    const source = readFileSync(path, 'utf8');
    for (const panel of tabControlSummary(source, path)) {
      assert.ok(
        panel.controls > 0,
        `${path}:${panel.line} declares an empty ${panel.tab} tab`,
      );
    }
  }
});
