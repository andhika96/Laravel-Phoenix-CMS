import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const css = readFileSync(
  path.join(process.cwd(), 'public/assets/css/themes/arunika_v3/arunika_v3.css'),
  'utf8',
);

test('Arunika V3 wraps the header and page content in one rounded right canvas', () => {
  assert.match(css, /--ph-v3-shell-gutter:\s*#f2f2ef/i);
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-app-shell\s*\{[^}]*background:\s*var\(--ph-sidebar-surface\);/s,
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-layout-right\s*\{[^}]*height:\s*auto;[^}]*margin:\s*15px\s+15px\s+15px\s+0;[^}]*border-radius:\s*12px;[^}]*overflow:\s*hidden\s*!important;[^}]*background:\s*var\(--ph-v3-content-surface\);/s,
  );
});

test('Arunika V3 keeps the sidebar edge borderless like the reference', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-sidebar,\s*\.ph-theme-arunika-v3\s+\.ph-sidebar\.ph-expanded\s*\{[^}]*border-right:\s*0;/s,
  );
});

test('Arunika V3 aligns the sidebar logo area with the right canvas top offset', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-layout-right\s*\{[^}]*margin:\s*15px\s+15px\s+15px\s+0;/s,
    'The right canvas must retain its approved 15px top offset.',
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-sidebar-logo-container\s*\{[^}]*position:\s*relative;[^}]*height:\s*var\(--ph-v3-header-height\);[^}]*min-height:\s*var\(--ph-v3-header-height\);[^}]*margin-top:\s*15px;/s,
    'The whole logo area must start at the same 15px top boundary.',
  );
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-sidebar:not\(\.ph-expanded\)\s+\.ph-app-logo-initial\s*\{[^}]*top:\s*50%;[^}]*left:\s*50%;/s,
    'The collapsed initial must center inside the offset logo container instead of the sidebar viewport.',
  );
});

test('Arunika V3 main panel does not create a second inner frame', () => {
  assert.match(
    css,
    /\.ph-theme-arunika-v3\s+\.ph-main-panel\s*\{[^}]*margin:\s*0;[^}]*border:\s*0;[^}]*border-radius:\s*0;[^}]*box-shadow:\s*none;/s,
  );
});
