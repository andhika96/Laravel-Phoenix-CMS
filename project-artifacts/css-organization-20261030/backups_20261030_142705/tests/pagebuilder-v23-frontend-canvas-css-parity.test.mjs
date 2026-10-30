import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import path from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const frontendCssPath = path.join(root, 'public/assets/css/frontend_elementor_v23.css');

test('v2.3 frontend CSS adapts Canvas structure without copying scoped component styles globally', async () => {
    const css = await readFile(frontendCssPath, 'utf8');

    assert.doesNotMatch(css, /Canvas frontend parity:/);
    assert.match(css, /html,\s*body\s*\{[^}]*margin:\s*0/s);
    assert.match(css, /\.pb-hero-banner\s*\{[^}]*position:\s*relative/s);
    assert.match(css, /\.pb-hero-banner__picture,[^{]+\{[^}]*position:\s*absolute/s);
});
