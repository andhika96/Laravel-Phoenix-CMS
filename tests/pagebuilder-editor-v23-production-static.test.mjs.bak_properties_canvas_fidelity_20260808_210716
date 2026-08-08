import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const app = readFileSync(resolve(root, 'public/js/pagebuilder_elementor_v23/app.js'), 'utf8');
const css = readFileSync(resolve(root, 'public/assets/css/pagebuilder_elementor_v23.css'), 'utf8');
const shell = readFileSync(resolve(root, 'resources/views/pagebuilder_elementor_v23/editor_shell.blade.php'), 'utf8');

test('production v2.3 uses the approved single-sidebar shell', () => {
    assert.match(app, /class="builder-app"/);
    assert.match(app, /class="topbar"/);
    assert.match(app, /class="workspace"/);
    assert.equal((app.match(/<aside\b[^>]*class="[^"]*side-panel left-panel[^"]*"/g) || []).length, 1);
    assert.match(app, /side-panel left-panel/);
    assert.match(app, /placeholder="Search widgets"/);
    assert.doesNotMatch(app, /tool-rail|activeTool\s*===\s*['"](?:layers|pages|global)/);
    assert.doesNotMatch(app, /side-panel right/);
});

test('production v2.3 owns the approved shell state without changing the toolbox registry', () => {
    assert.match(app, /const elementSearch = ref\(['"]{2}\);/);
    assert.match(app, /const filteredToolboxGroups = computed\(\(\) => \{/);
    assert.match(app, /\['layout', 'basic', 'general', 'pro', 'advanced'\]/);
    assert.match(app, /const leftCollapsed = ref\(false\);/);
    assert.match(app, /const previewMode = ref\(false\);/);
    assert.match(app, /leftCollapsed\.value = false;/);
    assert.match(app, /selectedNode \|\| selectedColumnContext/);
    assert.match(app, /v-for="group in filteredToolboxGroups"/);
    assert.match(app, /:list="group\.items"/);
});

test('production v2.3 keeps production canvas and editor contracts inside the new shell', () => {
    for (const marker of [
        'v-model="rootNodes"',
        '<BuilderNode',
        'columnResizeOverlay.visible',
        'toastVisible',
        'showCssEditor',
        'showTextEditorModal',
        'showIconLibraryModal',
        'modal.visible',
        'openCustomCssEditor',
        'pendingInsertTarget',
    ]) {
        assert.match(app, new RegExp(marker.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')));
    }

    assert.doesNotMatch(app, /NovaFlow|Prototype published locally|Core demo section is protected/);
});

test('production v2.3 ports the approved tokens and shell geometry', () => {
    assert.match(css, /--brand:\s*#5b4cf0/);
    assert.match(css, /grid-template-rows:\s*58px minmax\(0, 1fr\)/);
    assert.match(css, /grid-template-columns:\s*288px minmax\(440px, 1fr\)/);
    assert.match(css, /\.workspace\.preview-mode\s*\{/);
    assert.match(css, /\.element-grid\s*\{/);
    assert.match(css, /\.element-card\s*\{/);
    assert.match(css, /\.selection-summary\s*\{/);
    assert.match(css, /\.stage\s*\{/);
    assert.match(css, /\.webpage-frame\.tablet\s*\{\s*width:\s*768px;/);
    assert.match(css, /\.webpage-frame\.mobile\s*\{\s*width:\s*390px;/);
});

test('production shell loads Bootstrap Icons for shell chrome and keeps Font Awesome for widgets', () => {
    assert.match(shell, /bootstrap-icons@1\.11\.3\/font\/bootstrap-icons\.min\.css/);
    assert.match(shell, /fontawesome\/5\.15\.3\/css\/all\.min\.css/);
    assert.match(app, /class="bi bi-/);
    assert.match(app, /:class="element\.icon"/);
});

test('production v2.3 anchors page settings to the existing page-name control', () => {
    assert.match(app, /const pageSettingsOpen = ref\(false\);/);
    assert.match(app, /function openPageSettings\(\)/);
    assert.match(app, /function closePageSettings\(\)/);
    assert.match(app, /<button\b(?=[^>]*class="page-name")(?=[^>]*@click\.stop="openPageSettings")[^>]*>/);
    assert.match(app, /class="page-settings-popover"/);
    assert.match(app, /class="page-settings-popover"[\s\S]{0,3000}v-model="pageName"[\s\S]{0,3000}v-model="pageStatus"[\s\S]{0,3000}\{\{ customCssSummary \}\}[\s\S]{0,3000}@click="openCustomCssEditor"/);
});
