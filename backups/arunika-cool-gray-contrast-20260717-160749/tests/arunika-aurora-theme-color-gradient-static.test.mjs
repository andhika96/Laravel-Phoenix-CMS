import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

const currentDirectory = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(currentDirectory, '..');
const layout = fs.readFileSync(path.join(projectRoot, 'resources/views/themes/arunika_aurora/cms/cms_layout.blade.php'), 'utf8');
const stylesheet = fs.readFileSync(path.join(projectRoot, 'public/assets/css/themes/arunika_aurora/arunika_aurora.css'), 'utf8');
const themeScript = fs.readFileSync(path.join(projectRoot, 'public/assets/js/themes/arunika_aurora/arunika_aurora.js'), 'utf8');
const shellStyles = stylesheet.slice(stylesheet.indexOf('/* ARUNIKA AURORA - WEIHU REFERENCE PANEL SHELL */'));

test('places the Arunika Aurora color palette immediately before the theme mode toggle', () =>
{
	const paletteIndex = layout.indexOf('ph-theme-color-picker');
	const themeToggleIndex = layout.indexOf('ph-theme-toggle');

	assert.notEqual(paletteIndex, -1, 'The header must contain the theme color palette control.');
	assert.notEqual(themeToggleIndex, -1, 'The header must retain the dark and light mode toggle.');
	assert.ok(paletteIndex < themeToggleIndex, 'The color palette must be placed to the left of the mode toggle.');
	assert.match(layout, /id="color-picker-container"/);
});

test('keeps the Arunika Aurora picker limited to colors and reuses existing persistence', () =>
{
	assert.doesNotMatch(layout, /changePattern|background-pattern|Background Pattern/i);
	assert.match(themeScript, /function changeMainColor\(color\)/);
	assert.match(themeScript, /style\.setProperty\('--ph-theme-primary', color\)/);
	assert.match(themeScript, /localStorage\.setItem\('theme-color', color\)/);
});

test('derives light mode shell gradients and hover color from the selected primary color', () =>
{
	const lightTheme = shellStyles.match(/\[data-bs-theme=light\][\s\S]*?(?=\[data-bs-theme=dark\])/u)?.[0] ?? '';

	assert.match(shellStyles, /--ph-shell-hover:\s*color-mix\(in srgb, var\(--ph-theme-primary\), white \d+%\);/u);
	assert.match(lightTheme, /--ph-sidebar-surface:[^;]*color-mix\(in srgb, var\(--ph-theme-primary\), transparent \d+%\)[^;]*;/u);
	assert.match(lightTheme, /--ph-header-surface:[^;]*color-mix\(in srgb, var\(--ph-theme-primary\), transparent \d+%\)[^;]*;/u);
});

test('derives dark mode sidebar and header gradients from the selected primary color', () =>
{
	const darkTheme = shellStyles.match(/\[data-bs-theme=dark\][\s\S]*?(?=html,)/u)?.[0] ?? '';

	assert.match(darkTheme, /--ph-sidebar-surface:[^;]*color-mix\(in srgb, var\(--ph-theme-primary\), transparent \d+%\)[^;]*;/u);
	assert.match(darkTheme, /--ph-header-surface:[^;]*color-mix\(in srgb, var\(--ph-theme-primary\), transparent \d+%\)[^;]*;/u);
});
