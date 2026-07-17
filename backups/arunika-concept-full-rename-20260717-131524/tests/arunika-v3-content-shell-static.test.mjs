import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_v3/arunika_v3.css'),
  'utf8',
);

test('Arunika V3 uses the reference content surface color', () => {
  assert.match(css, /--ph-v3-content-surface:\s*#fefefc/i);
});

test('Arunika V3 keeps the dashboard content shell tight to the body', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-scrollable-content\s*\{[^}]*padding:\s*10px\s+8px\s+18px;/s,
  );
});
