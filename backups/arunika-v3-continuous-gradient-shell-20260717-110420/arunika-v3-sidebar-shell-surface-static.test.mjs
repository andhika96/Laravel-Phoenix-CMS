import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_v3/arunika_v3.css'),
  'utf8',
);

test('Arunika V3 sidebar and app shell use one continuous background surface', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-app-shell\s*\{[^}]*background:\s*var\(--ph-v3-shell-gutter\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-sidebar,\s*\.ph-theme-arunika-v3\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*var\(--ph-v3-shell-gutter\);/s,
    'The V3 sidebar must use the same shell-gutter token as the app shell.',
  );
});
