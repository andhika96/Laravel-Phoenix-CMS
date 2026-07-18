import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_canvas/arunika_canvas.css'),
  'utf8',
);

test('Arunika Canvas uses the reference content surface color', () => {
  assert.match(css, /--ph-canvas-content-surface:\s*#fefefc/i);
});

test('Arunika Canvas keeps the dashboard content shell tight to the body', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-canvas\s+\.ph-scrollable-content\s*\{[^}]*padding:\s*10px\s+8px\s+18px;/s,
  );
});
