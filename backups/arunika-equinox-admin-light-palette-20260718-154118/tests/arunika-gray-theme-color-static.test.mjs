import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import path from 'node:path';
import test from 'node:test';

const coolGray = '#C7CCD8';
const themes = ['arunika_mosaic', 'arunika_aurora', 'arunika_prism', 'arunika_equinox'];

for (const theme of themes) {
    test(`${theme} exposes cool gray as the eighth and final theme color`, () => {
        const scriptPath = path.join(
            process.cwd(),
            `public/assets/js/themes/${theme}/${theme}.js`,
        );
        const script = readFileSync(scriptPath, 'utf8');
        const paletteDeclaration = script.match(/let colorMainList\s*=\s*\[([^\]]+)]\s*;/);

        assert.notEqual(paletteDeclaration, null, `Missing colorMainList in ${theme}`);

        const colors = paletteDeclaration[1].match(/#[0-9A-F]{6}/gi) ?? [];
        const coolGrayOccurrences = colors.filter((color) => color.toUpperCase() === coolGray).length;

        assert.equal(colors.length, 8, `${theme} must expose exactly eight colors`);
        assert.equal(coolGrayOccurrences, 1, `${theme} must expose cool gray exactly once`);
        assert.equal(colors.at(-1)?.toUpperCase(), coolGray, `${theme} must place cool gray last`);
    });
}
