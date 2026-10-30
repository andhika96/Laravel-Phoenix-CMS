import test from 'node:test';
import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const here = dirname(fileURLToPath(import.meta.url));
const root = resolve(here, '..');
const mockupPath = resolve(root, 'public/mockups/theme-manager-interactive-mockup.html');

test('theme manager mockup exposes the approved theme selection contract', () => {
    assert.equal(existsSync(mockupPath), true, 'theme manager mockup file should exist');

    const html = readFileSync(mockupPath, 'utf8');

    assert.match(html, /data-theme-code="arunika_mosaic"/);
    assert.match(html, /data-theme-code="arunika_aurora"/);
    assert.match(html, /assets\/theme-manager\/arunika-mosaic-theme-preview\.png/);
    assert.match(html, /assets\/theme-manager\/arunika-aurora-theme-preview\.png/);
    assert.match(html, /id="cancelThemeChanges"/);
    assert.match(html, /id="saveThemeChanges"/);
    assert.match(html, /id="themePreviewDialog"/);
    assert.match(html, /id="themeSuccessToast"/);
    assert.match(html, /activeThemeCode:\s*'arunika_aurora'/);
    assert.match(html, /pendingThemeCode:\s*'arunika_aurora'/);
    assert.match(html, /function saveThemeChanges\(\)/);
    assert.match(html, /function cancelThemeChanges\(\)/);
    assert.match(html, /function openThemePreview\(themeCode\)/);
});

test('theme manager preview assets exist', () => {
    const assets = [
        'public/mockups/assets/theme-manager/arunika-mosaic-theme-preview.png',
        'public/mockups/assets/theme-manager/arunika-aurora-theme-preview.png',
    ];

    for (const asset of assets) {
        assert.equal(existsSync(resolve(root, asset)), true, `${asset} should exist`);
    }
});
