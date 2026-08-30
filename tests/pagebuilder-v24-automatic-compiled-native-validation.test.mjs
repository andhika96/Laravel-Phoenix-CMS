import assert from 'node:assert/strict';
import { existsSync, readFileSync, unlinkSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { spawnSync } from 'node:child_process';
import test from 'node:test';

const root = process.cwd();
const cli = join(root, 'tools', 'pagebuilder-v24', 'automatic-compiled-native-measure.mjs');

function measure(html, suffix) {
    const input = join(root, 'storage', 'framework', `pb-v24-validation-${suffix}-${process.pid}.html`);
    const output = join(root, 'storage', 'framework', `pb-v24-validation-${suffix}-${process.pid}.json`);
    writeFileSync(input, html);
    const result = spawnSync(process.execPath, [
        cli,
        '--input', input,
        '--viewports', JSON.stringify([{ name: 'desktop', width: 1180, height: 900 }]),
        '--output', output,
    ], { cwd: root, encoding: 'utf8' });
    unlinkSync(input);
    assert.equal(result.status, 0, result.stderr || result.stdout);
    assert.equal(existsSync(output), true);
    const snapshot = JSON.parse(readFileSync(output, 'utf8'));
    unlinkSync(output);
    return snapshot;
}

test('same viewport measurement exposes wrong track ratio and image sizing as pixel deltas', () => {
    const source = measure(`<!doctype html><style>#root{display:grid;grid-template-columns:42% 58%;gap:32px;width:1000px}#media{width:100%;height:320px}</style><section id="root"><div id="copy">Copy</div><img id="media" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" /></section>`, 'source');
    const target = measure(`<!doctype html><style>#root{display:grid;grid-template-columns:50% 50%;gap:32px;width:1000px}#media{width:100%;height:250px}</style><section id="root"><div id="copy">Copy</div><img id="media" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" /></section>`, 'target');
    const sourceCopy = source.nodes.find((node) => node.id === 'copy').rectByViewport.desktop;
    const targetCopy = target.nodes.find((node) => node.id === 'copy').rectByViewport.desktop;
    const sourceMedia = source.nodes.find((node) => node.id === 'media').rectByViewport.desktop;
    const targetMedia = target.nodes.find((node) => node.id === 'media').rectByViewport.desktop;

    assert.ok(Math.abs(sourceCopy.width - targetCopy.width) > 1);
    assert.ok(Math.abs(sourceMedia.width - targetMedia.width) > 1);
    assert.ok(Math.abs(sourceMedia.height - targetMedia.height) > 1);
});

test('same source geometry remains within the one-pixel browser-rounding tolerance', () => {
    const html = '<!doctype html><style>#root{display:block;width:640px;padding:24px;border:1px solid #000}#copy{height:80px}</style><section id="root"><div id="copy">Copy</div></section>';
    const first = measure(html, 'same-a');
    const second = measure(html, 'same-b');
    const left = first.nodes.find((node) => node.id === 'copy').rectByViewport.desktop;
    const right = second.nodes.find((node) => node.id === 'copy').rectByViewport.desktop;
    for (const property of ['x', 'y', 'width', 'height']) assert.ok(Math.abs(left[property] - right[property]) <= 1, `${property} drift exceeded tolerance`);
});

test('repeated measurement of the same source is deterministic for the same viewport input', () => {
    const html = '<!doctype html><style>#root{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:20px}#root>div{height:60px}</style><section id="root"><div>One</div><div>Two</div><div>Three</div></section>';
    const first = measure(html, 'deterministic-a');
    const second = measure(html, 'deterministic-b');
    assert.deepEqual(first, second);
});

console.log('Automatic Compiled Native validation measurement contract passed.');
