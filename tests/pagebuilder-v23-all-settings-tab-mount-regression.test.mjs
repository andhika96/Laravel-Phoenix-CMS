import assert from 'node:assert/strict';
import test from 'node:test';
import { readdirSync, readFileSync } from 'node:fs';
import { join } from 'node:path';

const root = 'D:/Laragon/www/laravel-13-phoenix/public/js/pagebuilder_elementor_v23/widgets';

function findSettingsFiles(directory) {
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const path = join(directory, entry.name);
    if (entry.isDirectory()) return findSettingsFiles(path);
    return entry.name === 'Settings.vue' ? [path] : [];
  });
}

test('v2.3 widget settings mount only the active editor tab', () => {
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
