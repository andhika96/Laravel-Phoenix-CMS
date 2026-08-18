import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const app = readFileSync(
    `${root}/public/js/pagebuilder_elementor_v23/app.js`,
    'utf8',
);
const heroCanvas = readFileSync(
    `${root}/public/js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Canvas.vue`,
    'utf8',
);

test('editor history snapshots are deferred and coalesced after root mutations', () => {
    assert.match(app, /function scheduleSnap\(\)/);
    assert.match(app, /setTimeout\(\(\) => \{[\s\S]*?snap\(\);[\s\S]*?\}, 120\);/);
    assert.match(app, /watch\(rootNodes, scheduleSnap, \{ flush: 'post' \}\)/);
    assert.doesNotMatch(app, /watch\(rootNodes, snap, \{ deep: true \}\)/);
    assert.match(app, /function cancelScheduledSnap\(\)/);
    assert.match(app, /function undo\(\) \{[\s\S]*?cancelScheduledSnap\(\);/);
    assert.match(app, /function redo\(\) \{[\s\S]*?cancelScheduledSnap\(\);/);
});

test('toolbox prewarms only the selected widget modules before activation', () => {
    assert.match(app, /function warmWidgetModules\(type\)/);
    assert.match(app, /const paths = \[definition\?\.canvas, definition\?\.settings\]\.filter\(Boolean\)/);
    assert.match(app, /@pointerenter="warmWidgetModules\(element\.type\)"/);
    assert.match(app, /@focus="warmWidgetModules\(element\.type\)"/);
    assert.doesNotMatch(app, /@pointerdown="warmWidgetModules\(element\.type\)"/);
    assert.doesNotMatch(app, /warmWidgetModules\(element\.type\).*widgetRegistry\?\.all/);
});

test('editor does not run a broad shared-control preload after the first gesture', () => {
    assert.doesNotMatch(app, /function preloadSettingsModuleAtIdle\(/);
    assert.doesNotMatch(app, /function preloadWidgetSettingsModules\(/);
    assert.doesNotMatch(app, /function scheduleWidgetSettingsPreloadAfterFirstGesture\(/);
    assert.doesNotMatch(app, /scheduleWidgetSettingsPreloadAfterFirstGesture\(\);/);
});

test('Hero Slider editor canvas does not serialize the full reactive settings on every render', () => {
  assert.doesNotMatch(heroCanvas, /:data-hero-slider-config="configJson"/);
  assert.doesNotMatch(heroCanvas, /configJson\(\)\s*\{\s*return JSON\.stringify\(this\.settings\);\s*\}/);
});

test('history scheduling avoids deep traversal of the full editor tree', () => {
  assert.match(app, /historyInteractionEventTypes/);
  assert.match(app, /scheduleHistorySnapshotFromEvent/);
  assert.doesNotMatch(app, /watch\(rootNodes, scheduleSnap, \{ deep: true/);
});

test('programmatic media and text-editor mutations still schedule history', () => {
  assert.match(app, /function setTextEditorHtml\([\s\S]*?scheduleSnap\(\);/);
  assert.match(app, /const setUrl = \(url\) => \{[\s\S]*?scheduleSnap\(\);/);
  assert.match(app, /targetObj\[safeKey\] = \[\.\.\.existing, \.\.\.additions\];[\s\S]*?scheduleSnap\(\);/);
});

test('widget settings keep one component identity after the async module resolves', () => {
  assert.match(
    app,
    /function loadWidgetSettings\(type\) \{[\s\S]*?return _settingsCache\[type\];[\s\S]*?\n\t\}/,
  );
  assert.doesNotMatch(
    app,
    /return _sfcResolvedModules\[path\] \|\| _settingsCache\[type\];/,
  );
});
