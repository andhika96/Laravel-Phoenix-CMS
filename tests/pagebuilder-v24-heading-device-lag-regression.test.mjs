import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const read = (relative) => readFileSync(`${root}/${relative}`, 'utf8');

const app = read('public/js/pagebuilder_elementor_v24/app.js');

test('heading-capable v2.4 widgets use semantic tag sizes with an explicit custom-size escape hatch', () => {
    const cases = [
        [
            'widgets/basic/heading',
            'headingFontSizeMode',
            'headingFontSize',
            'safeTag',
        ],
        [
            'widgets/general/accordion',
            'headerFontSizeMode',
            'headerFontSize',
            'titleTag',
        ],
        [
            'widgets/general/counter',
            'titleFontSizeMode',
            'titleFontSize',
            'safeTitleTag',
        ],
        [
            'widgets/general/progress-bar',
            'titleFontSizeMode',
            'titleFontSize',
            'safeTitleTag',
        ],
        [
            'widgets/pro/hero-banner',
            'titleFontSizeMode',
            'titleFontSize',
            'titleTag',
        ],
        [
            'widgets/pro/hero-slider',
            'titleFontSizeMode',
            'titleFontSize',
            'titleTag',
        ],
        [
            'widgets/pro/product-color-selector',
            'titleFontSizeMode',
            'titleFontSize',
            'titleTag',
        ],
    ];

    for (const [folder, modeKey, sizeKey, tagMarker] of cases) {
        const moduleRoot = `resources/pagebuilder_elementor_v24/modules/${folder}`;
        const canvas = read(`${moduleRoot}/Canvas.vue`);
        const definition = read(`${moduleRoot}/definition.js`);
        const settings = read(`${moduleRoot}/Settings.vue`);
        const blade = read(`${moduleRoot}/frontend.blade.php`);

        assert.match(definition, new RegExp(`${modeKey}\\s*:\\s*['"]auto['"]`), `${folder} should default to automatic tag sizing`);
        assert.match(canvas, new RegExp(`${modeKey}[^\\n]{0,220}custom|custom[^\\n]{0,220}${modeKey}`), `${folder} Canvas should honor custom font size mode`);
        assert.match(canvas, new RegExp(`${tagMarker}`), `${folder} Canvas should keep its semantic tag`);
        assert.match(settings, new RegExp(`${modeKey}|font-size-mode-key`), `${folder} Settings should expose the mode-aware size control`);
        assert.match(blade, new RegExp(`${modeKey}|TagFontSizes|titleTagFontSizes|automatic`, 'i'), `${folder} frontend renderer should share tag sizing`);
    }
});

test('heading-capable Pro modules use the same semantic tag sizing contract', () => {
    const cases = [
        ['pro/slides', 'slideTitleFontSizeMode', 'slideTitleFontSize'],
        ['pro/animated-headline', 'headlineFontSizeMode', 'headlineFontSize'],
        ['pro/price-list', 'priceListTitleFontSizeMode', 'priceListTitleFontSize'],
        ['pro/price-table', 'priceTableHeaderFontSizeMode', 'priceTableHeaderFontSize'],
        ['pro/video-playlist', 'playlistNameFontSizeMode', 'videoPlaylistNameFontSize'],
    ];

    for (const [folder, modeKey, sizeKey] of cases) {
        const moduleRoot = `resources/pagebuilder_elementor_v24/modules/widgets/${folder}`;
        const definition = read(`${moduleRoot}/definition.js`);
        const canvas = read(`${moduleRoot}/Canvas.vue`);
        const settings = read(`${moduleRoot}/Settings.vue`);
        const blade = read(`${moduleRoot}/frontend.blade.php`);
        assert.match(definition, new RegExp(`${modeKey}\\s*:\\s*['"]auto['"]`), `${folder} should default to automatic tag sizing`);
        assert.match(definition, new RegExp(`${sizeKey}`), `${folder} should keep a custom-size field for legacy settings`);
        assert.match(canvas, new RegExp(`${modeKey}|tagTypographyStyle`), `${folder} Canvas should use module mode-aware tag sizing`);
        assert.match(settings, new RegExp(`${modeKey}`), `${folder} Settings should expose the module mode-aware size control`);
        assert.match(blade, new RegExp(`${modeKey}|tagTypographyStyle`, 'i'), `${folder} frontend renderer should use shared tag sizing`);
    }
});

test('responsive device switcher uses a distinct tablet glyph', () => {
    assert.match(app, /title="Tablet"[^>]*><i class="bi bi-tablet-landscape"><\/i>/);
    assert.doesNotMatch(app, /title="Tablet"[^>]*><i class="bi bi-tablet"><\/i>/);
});

test('widget loading shares the SFC cache, avoids broad post-gesture settings compilation, and wraps root widgets synchronously', () => {
    assert.match(app, /defineAsyncComponent\(\(\)\s*=>\s*loadSfcModule\(path\)/);
    assert.doesNotMatch(app, /const modulePaths = \[\.\.\.new Set\(\[[\s\S]*widgetRegistry\?\.all\(\)/);
    assert.doesNotMatch(app, /if \(isWgt\(item\.type\)\) \{\s*nextTick\(/);
    assert.doesNotMatch(app, /const saved = jclone\(live\)/);
    assert.match(app, /function insertToolAtRoot\(toolDef\)[\s\S]{0,700}isWgt\(item\.type\)[\s\S]{0,700}makeNode\(defaultContainerType\(\)\)/);
});
