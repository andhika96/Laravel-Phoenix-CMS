import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_v3/arunika_v3.css'),
  'utf8',
);

test('Arunika V3 app shell paints one continuous sidebar gradient without an edge seam', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-app-shell\s*\{[^}]*background:\s*var\(--ph-sidebar-surface\);/s,
    'The V3 app shell must own the sidebar gradient so it is painted only once.',
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-sidebar,\s*\.ph-theme-arunika-v3\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*transparent;[^}]*border-right:\s*0;/s,
    'The V3 sidebar must reveal the parent gradient and have no right edge.',
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-layout-right\s*\{[^}]*border-left:\s*0;[^}]*box-shadow:\s*none;/s,
    'The V3 content canvas must not add a left border or shadow seam.',
  );
});

test('Arunika V3 decouples sidebar hover contrast from the ambient gradient', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s*\{[^}]*--ph-v3-sidebar-hover:\s*color-mix\(in srgb, var\(--ph-theme-primary\), white 94%\);[^}]*--ph-v3-sidebar-hover-shadow:\s*0 2px 8px rgba\(42, 35, 57, 0\.06\);/s,
  );
  assert.match(
    css,
    /html\[data-bs-theme=dark\]\s+\.ph-theme-arunika-v3\s*\{[^}]*--ph-v3-sidebar-hover:\s*rgba\(255, 255, 255, 0\.09\);[^}]*--ph-v3-sidebar-hover-shadow:\s*0 2px 8px rgba\(0, 0, 0, 0\.18\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-sidebar\s+\.list-group-item-action:hover,\s*\.ph-theme-arunika-v3\s+\.ph-sidebar\s+\.list-group-item-action:focus\s*\{[^}]*background:\s*var\(--ph-v3-sidebar-hover\);[^}]*box-shadow:\s*var\(--ph-v3-sidebar-hover-shadow\);/s,
  );
});
