import assert from 'node:assert/strict';
import { existsSync, readFileSync, unlinkSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { spawnSync } from 'node:child_process';
import test from 'node:test';

const root = process.cwd();
const cli = join(root, 'tools', 'pagebuilder-v24', 'automatic-compiled-native-measure.mjs');
const fixture = join(root, 'tests', 'Fixtures', 'PageBuilderElementorV24', 'CompiledNative', 'automatic-two-column-grid.html');
const responsiveFixture = join(root, 'tests', 'Fixtures', 'PageBuilderElementorV24', 'CompiledNative', 'automatic-responsive-collapse.html');

function runMeasurement(input, suffix) {
  const output = join(root, 'storage', 'framework', `pb-v24-measurement-${suffix}-${process.pid}.json`);
  const result = spawnSync(process.execPath, [
    cli,
    '--input', input,
    '--viewports', JSON.stringify([
      { name: 'desktop', width: 1180, height: 900 },
      { name: 'tablet', width: 768, height: 1024 },
      { name: 'mobile', width: 390, height: 900 },
    ]),
    '--output', output,
  ], { cwd: root, encoding: 'utf8' });

  assert.equal(result.status, 0, result.stderr || result.stdout);
  assert.equal(existsSync(output), true, 'measurement CLI must write its output file');
  const snapshot = JSON.parse(readFileSync(output, 'utf8'));
  unlinkSync(output);
  return snapshot;
}

function runRawMeasurement(input, suffix) {
  const output = join(root, 'storage', 'framework', `pb-v24-measurement-${suffix}-${process.pid}.json`);
  const result = spawnSync(process.execPath, [
    cli,
    '--input', input,
    '--viewports', JSON.stringify([{ name: 'desktop', width: 1180, height: 900 }]),
    '--output', output,
  ], { cwd: root, encoding: 'utf8' });
  if (existsSync(output)) unlinkSync(output);
  return result;
}

test('browser measurement captures computed box model, grid evidence, and source identity', () => {
  const snapshot = runMeasurement(fixture, 'grid');
  assert.equal(snapshot.version, 1);
  assert.deepEqual(snapshot.viewports.map(({ name }) => name), ['desktop', 'tablet', 'mobile']);

  const hero = snapshot.nodes.find((node) => node.id === 'hero');
  assert.ok(hero, 'the section id must be represented in the source index');
  assert.equal(hero.tag, 'section');
  assert.deepEqual(hero.classList, ['hero']);
  assert.equal(hero.computedStyle.display, 'grid');
  assert.equal(hero.computedStyle.boxSizing, 'content-box');
  assert.equal(hero.computedStyle.paddingTop, '80px');
  assert.equal(hero.computedStyle.paddingRight, '64px');
  assert.equal(hero.computedStyle.borderTopWidth, '1px');
  assert.match(hero.computedStyle.gridTemplateColumns, /px/);
  assert.equal(typeof hero.rectByViewport.desktop.width, 'number');
  assert.equal(typeof hero.rectByViewport.desktop.height, 'number');
  assert.equal(typeof hero.scrollSizeByViewport.desktop.height, 'number');
  assert.ok(Array.isArray(hero.children));
});

test('browser measurement records responsive computed grid tracks instead of assuming a column count', () => {
  const snapshot = runMeasurement(responsiveFixture, 'responsive');
  const responsive = snapshot.nodes.find((node) => node.id === 'responsive');
  assert.ok(responsive);
  assert.equal(responsive.computedStyle.display, 'grid');

  const desktopTracks = responsive.computedStyleByViewport.desktop.gridTemplateColumns.split(/\s+/).filter(Boolean);
  const tabletTracks = responsive.computedStyleByViewport.tablet.gridTemplateColumns.split(/\s+/).filter(Boolean);
  const mobileTracks = responsive.computedStyleByViewport.mobile.gridTemplateColumns.split(/\s+/).filter(Boolean);
  assert.equal(desktopTracks.length, 3);
  assert.equal(tabletTracks.length, 2);
  assert.equal(mobileTracks.length, 1);
});

test('browser measurement records visible before and after pseudo-element evidence', () => {
  const input = join(root, 'storage', 'framework', `pb-v24-pseudo-${process.pid}.html`);
  writeFileSync(input, '<!doctype html><html><head><style>#card::before{content:"";position:absolute;left:4px;top:6px;width:12px;height:14px;background:red}</style></head><body><section id="card">Card</section></body></html>');
  const snapshot = runMeasurement(input, 'pseudo');
  unlinkSync(input);
  const card = snapshot.nodes.find((node) => node.id === 'card');
  assert.equal(card.pseudoElementsByViewport.desktop.before.position, 'absolute');
  assert.equal(card.pseudoElementsByViewport.desktop.before.width, '12px');
  assert.equal(card.pseudoElementsByViewport.desktop.after, null);
});

test('browser measurement fails closed when a critical stylesheet cannot be loaded', () => {
  const input = join(root, 'storage', 'framework', `pb-v24-missing-style-${process.pid}.html`);
  writeFileSync(input, '<!doctype html><html><head><link rel="stylesheet" href="missing-critical.css"></head><body><main>Broken CSS</main></body></html>');
  const result = runRawMeasurement(input, 'missing-style');
  unlinkSync(input);

  assert.notEqual(result.status, 0);
  assert.match(result.stderr, /stylesheet|resource|load/i);
});
