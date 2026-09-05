import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import test from 'node:test';

const viewPath = 'resources/views/awesome_admin/awesome_admin_themes.blade.php';
const scriptPath = 'public/assets/js/vue3/manage_themes/vueV3-manage-themes-2026.js';
const mockupPath = 'public/mockups/theme-manager-interactive-mockup.html';

test('production theme manager keeps the approved interactive contract', () => {
    assert.equal(existsSync(viewPath), true, 'Theme Manager Blade view must exist');
    assert.equal(existsSync(scriptPath), true, 'Theme Manager Vue script must exist');

    const view = readFileSync(viewPath, 'utf8');
    const script = readFileSync(scriptPath, 'utf8');

    assert.match(view, /id="ph-app-theme-manager"/);
    assert.match(view, /role="radiogroup"/);
    assert.match(view, /is-selected/);
    assert.match(view, /is-active/);
    assert.match(view, /Live preview/);
    assert.match(view, /Save changes/);
    assert.match(view, /Cancel/);
    assert.match(
        view,
        /<Teleport to="body">[\s\S]*id="themeManagerPreviewModal"[\s\S]*<\/Teleport>/,
        'Preview modal must escape the CMS shell stacking context so it renders above the Bootstrap backdrop',
    );
    assert.doesNotMatch(view, /Browse installed themes/);
    assert.doesNotMatch(view, /\bv-text=/, 'Vue 3.5 production compiler rejects the rendered v-text directives on this page');
    assert.match(script, /selectedThemeCode/);
    assert.match(script, /activeThemeCode/);
    assert.match(script, /cancelChanges/);
    assert.match(script, /saveChanges/);
    assert.match(script, /closePreview/);
    assert.match(script, /axios\.post/);
    assert.match(script, /bootstrap\.Modal/);
});

test('Awesome Admin menu and temporary production previews are present', () => {
    const menu = readFileSync('resources/views/awesome_admin/awesome_admin.blade.php', 'utf8');
    const mockup = readFileSync(mockupPath, 'utf8');

    assert.match(menu, /cms\.admin\.awesome_admin\.themes/);
    assert.match(menu, /Manage Themes/);
    assert.doesNotMatch(mockup, /Browse installed themes/);
    assert.equal(existsSync('public/assets/images/themes/previews/arunika-aurora-theme-preview.png'), true);
    assert.equal(existsSync('public/assets/images/themes/previews/arunika-prism-theme-preview.png'), true);
    assert.equal(existsSync('public/assets/images/themes/previews/arunika-lucent-theme-preview.png'), true);
    assert.equal(existsSync('public/assets/images/themes/previews/arunika-equinox-theme-preview.png'), true);
    assert.equal(existsSync('public/assets/images/themes/previews/arunika-mosaic-theme-preview.png'), false);
});

test('production theme previews use the compact Manage Appearance sizing contract', () => {
    const view = readFileSync(viewPath, 'utf8');

    assert.match(
        view,
        /\.theme-manager-grid\s*\{[^}]*grid-template-columns:\s*repeat\(auto-fill,\s*minmax\(200px,\s*240px\)\);[^}]*gap:\s*14px;[^}]*justify-content:\s*start;/s,
        'Theme cards should form a compact multi-column list instead of two oversized columns.',
    );
    assert.match(
        view,
        /\.theme-manager-preview\s*\{[^}]*height:\s*130px;[^}]*aspect-ratio:\s*auto;/s,
        'Theme preview images should match the 130px preview height used by Manage Appearance.',
    );
});
