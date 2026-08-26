import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import test from 'node:test';

const root = path.resolve(import.meta.dirname, '..');
const read = (relative) => fs.readFileSync(path.join(root, relative), 'utf8');

function loadRegistry() {
    const context = { window: {} };
    vm.runInNewContext(read('public/js/pagebuilder_elementor_v24/widget-registry.js'), context);
    return context.window.PageBuilderElementorV24Widgets;
}

function walk(directory, filename) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const target = path.join(directory, entry.name);
        return entry.isDirectory() ? walk(target, filename) : entry.name === filename ? [target] : [];
    });
}

test('v2.4 shared Advanced defaults expose responsive Full Bleed without changing legacy defaults', () => {
    const registry = loadRegistry();
    const defaults = registry.advancedDefaults();
    assert.equal(defaults.fullBleed, false);
    assert.equal(defaults.fullBleedTablet, '');
    assert.equal(defaults.fullBleedMobile, '');

    const normalized = registry.normalizeAdvanced({ fullBleed: 'true', fullBleedTablet: '0', fullBleedMobile: '' });
    assert.equal(normalized.fullBleed, true);
    assert.equal(normalized.fullBleedTablet, false);
    assert.equal(normalized.fullBleedMobile, '');
});

test('v2.4 Advanced spacing keeps the shared form and accepts screen plus print units', () => {
    const shared = read('resources/pagebuilder_elementor_v24/shared/AdvancedControls.vue');
    assert.match(shared, /Full Bleed/);
    assert.match(shared, /v-if="advancedProfile === 'widget'"[^>]*class="pb-advanced-field pb-advanced-responsive-field"/);
    assert.match(shared, /effectiveResponsiveValue\('fullBleed'/);
    assert.match(shared, /ResponsiveDeviceControl/);
    assert.match(shared, /const SPACING_DIMENSION_UNITS = \['px', '%', 'em', 'rem', 'vw', 'vh', 'pt'\]/);
    assert.match(shared, /\(px\|%\|em\|rem\|pt\|vw\|vh\|deg\)/);
    assert.doesNotMatch(shared, /FullBleedControl/);
});

test('v2.4 Canvas makes the editor gutter responsive instead of hard-coding it per widget', () => {
    const app = read('public/js/pagebuilder_elementor_v24/app.js');
    const css = read('public/assets/css/pagebuilder_elementor_v24.css');
    assert.match(app, /widgetPreviewShellStyle\(\)/);
    assert.match(app, /class="pb-preview"[^>]*:style="widgetPreviewShellStyle"/);
    assert.match(css, /\.pb-preview\s*\{[\s\S]*padding:\s*var\(--pb-preview-padding,\s*6px\)/);
});

test('every v2.4 widget renderer uses the shared Advanced resolver at its own root', () => {
    const widgetRoot = path.join(root, 'resources', 'pagebuilder_elementor_v24', 'modules', 'widgets');
    const frontendFiles = walk(widgetRoot, 'frontend.blade.php');
    assert.equal(frontendFiles.length, 46);
    for (const file of frontendFiles) {
        const source = fs.readFileSync(file, 'utf8');
        assert.match(source, /WidgetAdvancedStyleResolver::class/, path.relative(root, file));
    }
});
