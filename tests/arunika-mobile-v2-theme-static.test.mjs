import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const themes = ['prism', 'aurora', 'lucent', 'equinox'];

const read = (file) => readFileSync(path.join(root, file), 'utf8');

test('every Arunika CMS theme has an isolated mobile V2 stylesheet and marker', () => {
  for (const theme of themes) {
    const layoutPath = `resources/views/themes/arunika_${theme}/cms/cms_layout.blade.php`;
    const cssPath = `public/assets/css/themes/arunika_${theme}/mobile-v2.css`;
    const layout = read(layoutPath);
    const css = read(cssPath);

    assert.equal(existsSync(path.join(root, cssPath)), true, `${theme} mobile CSS must exist`);
    assert.match(layout, new RegExp(`data-ph-mobile-theme="arunika_${theme}"`));
    assert.match(layout, new RegExp(`themes/arunika_${theme}/mobile-v2\\.css`));
    assert.match(layout, /id="ph-mobile-nav-controller"/);
    assert.match(css, /@media\s*\(max-width:\s*768px\)/);
    assert.match(css, new RegExp(`data-ph-mobile-theme="arunika_${theme}"`));
    assert.match(css, /ph-dashboard-projection-title/);
    assert.match(css, /ph-dashboard-projection-chart/);
  }
});

test('theme-specific mobile V2 contracts preserve distinct visual identities', () => {
  const aurora = read('public/assets/css/themes/arunika_aurora/mobile-v2.css');
  const prism = read('public/assets/css/themes/arunika_prism/mobile-v2.css');
  const equinox = read('public/assets/css/themes/arunika_equinox/mobile-v2.css');
  const equinoxDesktop = read('public/assets/css/themes/arunika_equinox/arunika_equinox.css');
  const lucent = read('public/assets/css/themes/arunika_lucent/mobile-v2.css');

  assert.match(aurora, /arunika-aurora-sidebar-wave\.png/);
  assert.match(prism, /border:\s*1px solid var\(--ph-sidebar-border\)/);
  assert.match(equinoxDesktop, /arunika-equinox-sidebar-landscape\.png/);
  assert.doesNotMatch(equinox, /arunika-equinox-sidebar-landscape\.png|\.ph-sidebar::after/);
  assert.match(equinox, /ph-dashboard-stat-icon[\s\S]*display:\s*grid/);
  assert.doesNotMatch(lucent, /ph-mobile-brand|ph-lucent-leaf-mark|ph-header-notification|cmsNotifBell/);
  assert.match(lucent, /grid-template-columns:\s*repeat\(2/);
  assert.match(lucent, /ph-mobile-header-avatar/);
});

console.log('Arunika mobile V2 theme CSS contracts loaded.');
