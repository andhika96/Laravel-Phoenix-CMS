import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_v3/arunika_v3.css'),
  'utf8',
);

test('Arunika V3 gives the right content panel a rounded outer frame', () => {
  assert.match(css, /--ph-v3-shell-gutter:\s*#f2f2ef/i);
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-layout-right\s*\{[^}]*background:\s*var\(--ph-v3-shell-gutter\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-main-panel\s*\{[^}]*margin:\s*8px\s+8px\s+8px\s+0;[^}]*border:\s*1px\s+solid\s+var\(--ph-v3-border\);[^}]*border-radius:\s*12px;[^}]*overflow:\s*hidden;/s,
  );
});
