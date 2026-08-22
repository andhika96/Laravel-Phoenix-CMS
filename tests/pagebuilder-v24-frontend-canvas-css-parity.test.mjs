import assert from 'node:assert/strict';
import path from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';
import { readPageBuilderV24FrontendStyles } from './helpers/pagebuilder-v24-module-source.mjs';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
test('v2.4 frontend CSS adapts Canvas structure without copying scoped component styles globally', async () => {
    const css = readPageBuilderV24FrontendStyles(root);

    assert.doesNotMatch(css, /Canvas frontend parity:/);
    assert.match(css, /html,\s*body\s*\{[^}]*margin:\s*0/s);
    assert.match(css, /\.pb-hero-banner\s*\{[^}]*position:\s*relative/s);
    assert.match(css, /\.pb-hero-banner__picture\s*\{[^}]*position:\s*absolute/s);
});
