import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const themes = ['arunika_mosaic', 'arunika_aurora', 'arunika_prism', 'arunika_equinox'];

for (const theme of themes) {
    const script = readFileSync(
        path.join(process.cwd(), `public/assets/js/themes/${theme}/${theme}.js`),
        'utf8',
    );
    const layout = readFileSync(
        path.join(process.cwd(), `resources/views/themes/${theme}/cms/cms_layout.blade.php`),
        'utf8',
    );
    const stylesheet = readFileSync(
        path.join(process.cwd(), `public/assets/css/themes/${theme}/${theme}.css`),
        'utf8',
    );

    test(`${theme} maps cool gray to separate surface and interactive tokens`, () => {
        assert.match(script, /const coolGrayThemeColor\s*=\s*'#C7CCD8';/);
        assert.match(script, /const coolGrayInteractiveColor\s*=\s*'#667085';/);
        assert.match(script, /function applyMainColor\(color\)/);
        assert.match(script, /--ph-theme-primary/);
        assert.match(script, /--ph-theme-surface-tint/);
        assert.match(script, /dataset\.phThemeColor/);
        assert.match(script, /localStorage\.setItem\('theme-color', color\)/);
    });

    test(`${theme} restores the cool-gray mapping before first paint`, () => {
        assert.match(layout, /savedColor\.toUpperCase\(\)\s*===\s*'#C7CCD8'/);
        assert.match(layout, /isCoolGray\s*\?\s*'#667085'\s*:\s*savedColor/);
        assert.match(layout, /--ph-theme-surface-tint/);
        assert.match(layout, /dataset\.phThemeColor\s*=\s*'cool-gray'/);
    });

    test(`${theme} keeps surface tint separate and exposes mode-aware gray hover`, () => {
        assert.match(stylesheet, /--ph-theme-surface-tint:\s*var\(--ph-theme-primary\);/);
        assert.match(
            stylesheet,
            /html\[data-bs-theme=light\]\[data-ph-theme-color="cool-gray"\][\s\S]*--ph-theme-hover-surface:\s*#E4E7EC;/,
        );
        assert.match(
            stylesheet,
            /html\[data-bs-theme=dark\]\[data-ph-theme-color="cool-gray"\][\s\S]*--ph-theme-hover-surface:\s*rgba\(199,\s*204,\s*216,\s*0\.16\);/,
        );

        const surfaceTintUses = stylesheet.match(/var\(--ph-theme-surface-tint\)/g) ?? [];
        const hoverSurfaceUses = stylesheet.match(/var\(--ph-theme-hover-surface,/g) ?? [];

        assert.ok(surfaceTintUses.length >= 3, `${theme} must use the surface tint in its backgrounds`);
        assert.ok(hoverSurfaceUses.length >= 2, `${theme} must use the accessible hover token`);
    });
}
