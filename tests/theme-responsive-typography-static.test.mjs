import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const root = process.cwd();
const typographyPath = path.join(root, 'public/assets/css/theme-responsive-typography.css');
const themeLayouts = [
    'resources/views/themes/arunika_aurora/cms/cms_layout.blade.php',
    'resources/views/themes/arunika_equinox/cms/cms_layout.blade.php',
    'resources/views/themes/arunika_mosaic/cms/cms_layout.blade.php',
    'resources/views/themes/arunika_prism/cms/cms_layout.blade.php',
    'resources/views/themes/calm_green/cms/cms_layout.blade.php',
    'resources/views/themes/default/cms/cms_layout.blade.php',
].map((file) => path.join(root, file));

const read = (filePath) => readFileSync(filePath, 'utf8');

test('every CMS theme loads the shared File Manager V2 responsive typography asset', () => {
    assert.equal(
        existsSync(typographyPath),
        true,
        'The shared responsive typography stylesheet must exist.',
    );

    for (const layoutPath of themeLayouts) {
        assert.equal(existsSync(layoutPath), true, `Missing ${path.relative(root, layoutPath)}`);
        assert.match(
            read(layoutPath),
            /assets\/css\/theme-responsive-typography\.css/,
            `${path.relative(root, layoutPath)} must load the shared responsive typography stylesheet.`,
        );
    }
});

test('the shared asset preserves the File Manager V2 RFS curves in content and overlays', () => {
    const css = read(typographyPath);

    assert.match(css, /--ph-fmv2-rfs-h1:\s*calc\(1\.375rem \+ 1\.5vw\);/);
    assert.match(css, /--ph-fmv2-rfs-h2:\s*calc\(1\.325rem \+ \.9vw\);/);
    assert.match(css, /--ph-fmv2-rfs-h3:\s*calc\(1\.3rem \+ \.6vw\);/);
    assert.match(css, /--ph-fmv2-rfs-h4:\s*calc\(1\.275rem \+ \.3vw\);/);
    assert.match(css, /@media \(min-width: 1200px\)/);
    assert.match(css, /\.ph-content/);
    assert.match(css, /\.modal/);
    assert.match(css, /\.dropdown-menu/);
    assert.match(css, /\.collapse/);
    assert.match(css, /\.collapsing/);
    assert.match(css, /\.accordion/);
});

test('wide, short viewports use compact heading caps without overriding theme-owned title styles', () => {
    const css = read(typographyPath);

    assert.match(css, /@media \(min-width: 1200px\) and \(max-height: 1000px\)/);
    assert.match(
        css,
        /--ph-fmv2-rfs-h1:\s*clamp\(1\.75rem, calc\(1\.375rem \+ 1\.25vw\), 2rem\);/,
    );
    assert.match(
        css,
        /body :where\(\.ph-content, \.ph-scrollable-content, \.arv7-main-side, \.arv7-main-content, \.arv7-content, main\) :where\(h1, \.h1\)/,
    );
    assert.doesNotMatch(css, /body :is\(\.ph-content/);
});

test('compact desktop derives operational and sidebar text from Site Config', () => {
    const css = read(typographyPath);

    assert.match(css, /--ph-adaptive-font-size:\s*var\(--ph-font-size, 1rem\);/);
    assert.match(css, /--ph-adaptive-font-size:\s*min\(var\(--ph-font-size, 1rem\), max\(12px, calc\(var\(--ph-font-size, 1rem\) - 1\.5px\)\)\);/);
    assert.match(css, /body\s*\{\s*font-size:\s*var\(--ph-adaptive-font-size\);/);
    assert.match(css, /\.form-control/);
    assert.match(css, /\.ph-sidebar/);
    assert.match(css, /\.arv7-sidebar/);
    assert.match(css, /\.ph-nav-text/);
    assert.match(css, /\.arv7-parent-menu-name/);
});

test('operational controls inherit the computed Site Config size for px, em, and rem', () => {
    const css = read(typographyPath);

    assert.match(
        css,
        /:where\(\.form-control, \.form-select, \.btn, \.dropdown-item, \.table, \.form-label, \.page-link(?:, \.input-group-text, \.form-check-label, \.form-check-input)?\)\s*\{\s*font-size:\s*inherit !important;/,
    );
    assert.match(
        css,
        /:where\(\.list-group-item, \.ph-nav-text, \.arv7-parent-menu-name, \.arv7-menu-name\)\s*\{\s*font-size:\s*inherit !important;/,
    );
});

test('input groups bypass Bootstrap one-rem text and honor font-size-normal', () => {
    const css = read(typographyPath);

    assert.match(
        css,
        /:where\(\.form-control, \.form-select, \.btn, \.dropdown-item, \.table, \.form-label, \.page-link, \.input-group-text, \.form-check-label, \.form-check-input\)\s*\{\s*font-size:\s*inherit !important;/,
    );
    assert.match(
        css,
        /body \.font-size-normal\s*\{\s*font-size:\s*var\(--ph-adaptive-font-size\) !important;/,
    );
});

test('profile dropdown uses the compact Site Config scale while preserving its hierarchy', () => {
    const css = read(typographyPath);

    assert.match(
        css,
        /--ph-profile-menu-font-size:\s*min\(13px, max\(11px, calc\(var\(--ph-adaptive-font-size\) - 1px\)\)\);/,
    );
    assert.match(
        css,
        /body \.ph-header-profile-menu \.dropdown-item\s*\{\s*font-size:\s*var\(--ph-profile-menu-font-size\) !important;/,
    );
    assert.match(
        css,
        /body \.ph-header-profile-menu \.ph-profile-menu-identity strong\s*\{\s*font-size:\s*min\(13px, calc\(var\(--ph-profile-menu-font-size\) \+ 1px\)\) !important;/,
    );
    assert.match(
        css,
        /body \.ph-header-profile-menu :where\(\.ph-profile-menu-identity span, \.ph-profile-color-label\)\s*\{\s*font-size:\s*min\(11px, max\(10px, calc\(var\(--ph-profile-menu-font-size\) - 1px\)\)\) !important;/,
    );
});
