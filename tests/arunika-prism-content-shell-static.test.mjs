import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_prism/arunika_prism.css'),
  'utf8',
);

test('Arunika Prism uses the reference content surface color', () => {
  assert.match(css, /--ph-prism-content-surface:\s*#fefefc/i);
});

test('Arunika Prism keeps the dashboard content shell tight to the body', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-prism\s+\.ph-scrollable-content\s*\{[^}]*padding:\s*10px\s+8px\s+18px;/s,
  );
});
