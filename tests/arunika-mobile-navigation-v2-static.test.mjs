import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const controllerPath = 'public/assets/js/themes/arunika-mobile-navigation-v2.js';
const themes = ['prism', 'aurora', 'lucent', 'equinox'];

test('Vue 3 CDN mobile navigation controller exposes the isolated drawer API', () => {
  assert.equal(existsSync(path.join(root, controllerPath)), true, 'mobile controller must exist');
  const source = readFileSync(path.join(root, controllerPath), 'utf8');

  assert.match(source, /Vue\.createApp\(/);
  assert.match(source, /Vue\.ref\(/);
  assert.match(source, /Vue\.onMounted\(/);
  assert.match(source, /Vue\.onBeforeUnmount\(/);
  assert.match(source, /window\.PhoenixMobileNavigation\s*=/);
  assert.match(source, /isMobile\s*[:(]/);
  assert.match(source, /open\s*\(/);
  assert.match(source, /close\s*\(/);
  assert.match(source, /toggle\s*\(/);
  assert.match(source, /syncViewport\s*\(/);
  assert.match(source, /(?:aria-label=["']Dismiss navigation backdrop|setAttribute\(['"]aria-label['"],\s*['"]Dismiss navigation backdrop)/);
  assert.match(source, /\.inert\s*=/);
  assert.match(source, /focus\(\)/);
  assert.doesNotMatch(source, /localStorage\.setItem\(['"]sidebar-state/);
});

test('every CMS theme loads the mobile controller once with an isolated mount root', () => {
  for (const theme of themes) {
    const layoutPath = path.join(root, `resources/views/themes/arunika_${theme}/cms/cms_layout.blade.php`);
    const layout = readFileSync(layoutPath, 'utf8');

    assert.match(layout, /data-ph-mobile-theme=["']arunika_/);
    assert.equal((layout.match(/id=["']ph-mobile-nav-controller["']/g) ?? []).length, 1);
    assert.equal((layout.match(/arunika-mobile-navigation-v2\.js/g) ?? []).length, 1);
  }
});

test('theme toggle functions delegate mobile state to the Vue controller', () => {
  for (const theme of themes) {
    const scriptPath = path.join(root, `public/assets/js/themes/arunika_${theme}/arunika_${theme}.js`);
    const script = readFileSync(scriptPath, 'utf8');

    assert.match(script, /window\.PhoenixMobileNavigation\?\.isMobile\(\)/, `${theme} toggle should delegate on mobile`);
    assert.match(script, /window\.PhoenixMobileNavigation\.toggle\(\)/, `${theme} toggle should call controller`);
  }
});

console.log('Arunika mobile navigation V2 static contract loaded.');
