import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_canvas/arunika_canvas.css'),
  'utf8',
);

test('Arunika Canvas app shell paints one continuous sidebar gradient without an edge seam', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-canvas\s+\.ph-app-shell\s*\{[^}]*background:\s*var\(--ph-sidebar-surface\);/s,
    'The Canvas app shell must own the sidebar gradient so it is painted only once.',
  );
  assert.match(
    css,
    /\.ph-theme-arunika-canvas\s+\.ph-sidebar,\s*\.ph-theme-arunika-canvas\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*background:\s*transparent;[^}]*border-right:\s*0;/s,
    'The Canvas sidebar must reveal the parent gradient and have no right edge.',
  );
  assert.match(
    css,
    /\.ph-theme-arunika-canvas\s+\.ph-layout-right\s*\{[^}]*border-left:\s*0;[^}]*box-shadow:\s*none;/s,
    'The Canvas content canvas must not add a left border or shadow seam.',
  );
});

test('Arunika Canvas decouples sidebar hover contrast from the ambient gradient', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-canvas\s*\{[^}]*--ph-canvas-sidebar-hover:\s*color-mix\(in srgb, var\(--ph-theme-primary\), white 94%\);[^}]*--ph-canvas-sidebar-hover-shadow:\s*0 2px 8px rgba\(42, 35, 57, 0\.06\);/s,
  );
  assert.match(
    css,
    /html\[data-bs-theme=dark\]\s+\.ph-theme-arunika-canvas\s*\{[^}]*--ph-canvas-sidebar-hover:\s*rgba\(255, 255, 255, 0\.09\);[^}]*--ph-canvas-sidebar-hover-shadow:\s*0 2px 8px rgba\(0, 0, 0, 0\.18\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-canvas\s+\.ph-sidebar\s+\.list-group-item-action:hover,\s*\.ph-theme-arunika-canvas\s+\.ph-sidebar\s+\.list-group-item-action:focus\s*\{[^}]*background:\s*var\(--ph-canvas-sidebar-hover\);[^}]*box-shadow:\s*var\(--ph-canvas-sidebar-hover-shadow\);/s,
  );
});
