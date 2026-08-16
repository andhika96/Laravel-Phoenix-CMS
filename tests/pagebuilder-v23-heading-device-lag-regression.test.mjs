import assert from 'node:assert/strict';
import test from 'node:test';
import { readFileSync } from 'node:fs';

const root = 'D:/Laragon/www/laravel-13-phoenix';
const read = (relative) => readFileSync(`${root}/${relative}`, 'utf8');

const app = read('public/js/pagebuilder_elementor_v23/app.js');

test('heading-capable v2.3 widgets use semantic tag sizes with an explicit custom-size escape hatch', () => {
    const cases = [
        [
            'basic/heading',
            'headingFontSizeMode',
            'headingFontSize',
            'safeTag',
            'resources/views/pagebuilder_elementor_v23/widgets/basic/heading.blade.php',
        ],
        [
            'advanced/accordion',
            'headerFontSizeMode',
            'headerFontSize',
            'titleTag',
            'resources/views/pagebuilder_elementor_v23/partials/render_accordion.blade.php',
        ],
        [
            'general/counter',
            'titleFontSizeMode',
            'titleFontSize',
            'safeTitleTag',
            'resources/views/pagebuilder_elementor_v23/partials/render_counter.blade.php',
        ],
        [
            'general/progress-bar',
            'titleFontSizeMode',
            'titleFontSize',
            'safeTitleTag',
            'resources/views/pagebuilder_elementor_v23/partials/render_progress_bar.blade.php',
        ],
        [
            'pro/hero-banner',
            'titleFontSizeMode',
            'titleFontSize',
            'titleTag',
            'resources/views/pagebuilder_elementor_v23/widgets/pro/hero-banner.blade.php',
        ],
        [
            'pro/hero-slider',
            'titleFontSizeMode',
            'titleFontSize',
            'titleTag',
            'resources/views/pagebuilder_elementor_v23/widgets/pro/hero-slider.blade.php',
        ],
        [
            'pro/product-color-selector',
            'titleFontSizeMode',
            'titleFontSize',
            'titleTag',
            'resources/views/pagebuilder_elementor_v23/widgets/pro/product-color-selector.blade.php',
        ],
    ];

    for (const [folder, modeKey, sizeKey, tagMarker, bladePath] of cases) {
        const canvas = read(`public/js/pagebuilder_elementor_v23/widgets/${folder}/Canvas.vue`);
        const definition = folder === 'advanced/accordion'
            ? app
            : read(`public/js/pagebuilder_elementor_v23/widgets/${folder}/definition.js`);
        const settings = read(`public/js/pagebuilder_elementor_v23/widgets/${folder}/Settings.vue`);
        const blade = read(bladePath);

        assert.match(definition, new RegExp(`${modeKey}\\s*:\\s*['"]auto['"]`), `${folder} should default to automatic tag sizing`);
        assert.match(canvas, new RegExp(`${modeKey}[^\\n]{0,220}custom|custom[^\\n]{0,220}${modeKey}`), `${folder} Canvas should honor custom font size mode`);
        assert.match(canvas, new RegExp(`${tagMarker}`), `${folder} Canvas should keep its semantic tag`);
        assert.match(settings, new RegExp(`${modeKey}|font-size-mode-key`), `${folder} Settings should expose the mode-aware size control`);
        assert.match(blade, new RegExp(`${modeKey}|TagFontSizes|titleTagFontSizes|automatic`, 'i'), `${folder} frontend renderer should share tag sizing`);
    }
});

test('shared heading-capable Pro widgets use the same semantic tag sizing contract', () => {
    const sharedCanvas = read('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue');
    const sharedSettings = read('public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue');
    const blade = read('resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php');
    const cases = [
        ['pro/slides', 'slideTitleFontSizeMode', 'slideTitleFontSize'],
        ['pro/animated-headline', 'headlineFontSizeMode', 'headlineFontSize'],
        ['pro/price-list', 'priceListTitleFontSizeMode', 'priceListTitleFontSize'],
        ['pro/price-table', 'priceTableHeaderFontSizeMode', 'priceTableHeaderFontSize'],
        ['pro/video-playlist', 'playlistNameFontSizeMode', 'videoPlaylistNameFontSize'],
    ];

    for (const [folder, modeKey, sizeKey] of cases) {
        const definition = read(`public/js/pagebuilder_elementor_v23/widgets/${folder}/definition.js`);
        assert.match(definition, new RegExp(`${modeKey}\\s*:\\s*['"]auto['"]`), `${folder} should default to automatic tag sizing`);
        assert.match(definition, new RegExp(`${sizeKey}`), `${folder} should keep a custom-size field for legacy settings`);
        assert.match(sharedCanvas, new RegExp(`${modeKey}|tagTypographyStyle`), `${folder} Canvas should use shared mode-aware tag sizing`);
        assert.match(sharedSettings, new RegExp(`${modeKey}`), `${folder} Settings should expose the shared mode-aware size control`);
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
    assert.match(app, /function insertToolAtRoot\(toolDef\)[\s\S]{0,700}isWgt\(item\.type\)[\s\S]{0,700}makeNode\('container'\)/);
});
