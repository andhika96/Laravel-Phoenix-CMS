import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';

const mockupPath = 'public/mockups/site-typography-preview-config-mockup.html';

assert.ok(existsSync(mockupPath), 'the standalone typography preview mockup should exist');

const mockup = readFileSync(mockupPath, 'utf8');

for (const id of [
    'generalSettingsSection',
    'fontFamilyInput',
    'fontSizeInput',
    'fontSizeUnit',
    'typographyPreview',
    'typographyPreviewMeta',
    'typographyPreviewHeading',
    'typographyPreviewBody',
    'typographyPreviewNav',
    'resetTypographyPreview',
]) {
    assert.match(mockup, new RegExp(`id=["']${id}["']`), `mockup should expose #${id}`);
}

const fontControlsIndex = mockup.indexOf('id="typographyControls"');
const previewIndex = mockup.indexOf('id="typographyPreview"');

assert.ok(fontControlsIndex >= 0 && previewIndex > fontControlsIndex, 'compact preview should appear directly below the font controls');
assert.match(mockup, /id=["']generalSettingsSection["'][^>]*class=["'][^"']*ph-content[^"']*rounded[^"']*p-4[^"']*mb-4/, 'General Settings should retain the Site Config section shell');
assert.match(mockup, /id=["']fontFamilyInput["'][\s\S]*?<option value=["']Nunito["'] selected>Nunito<\/option>/, 'Nunito should be selected by default');
assert.match(mockup, /id=["']fontSizeInput["'][^>]*type=["']number["'][^>]*value=["']14["']/, 'font size should default to 14');
assert.match(mockup, /id=["']fontSizeUnit["'][\s\S]*?<option value=["']px["'] selected>px<\/option>[\s\S]*?<option value=["']em["']>em<\/option>[\s\S]*?<option value=["']rem["']>rem<\/option>/, 'font size should expose px, em, and rem with px selected');
assert.doesNotMatch(mockup, /id=["']fontSizeUnit["'][\s\S]*?<option value=["'](?:%|pt)["']>/, 'font size should not expose percent or point units');
assert.match(mockup, /--preview-font-family:\s*Nunito/, 'preview should expose a font-family CSS variable');
assert.match(mockup, /--preview-font-size:\s*14px/, 'preview should expose a font-size CSS variable');
assert.match(mockup, /\.typography-preview-title\s*>\s*div\s*>\s*span\s*\{/, 'preview subtitle styles should be scoped away from the icon wrapper');
assert.doesNotMatch(mockup, /\.typography-preview-title\s+span\s*\{/, 'preview subtitle styles should not override every span in the header');
assert.match(mockup, /\.typography-preview-icon\s+i\s*\{[\s\S]*?line-height:\s*1;/, 'font icon should have an explicit centered line height');
assert.match(mockup, /function renderTypographyPreview\(\)/, 'mockup should define a typography render function');
assert.match(mockup, /elements\.preview\.style\.setProperty\('--preview-font-family'/, 'selected family should be applied to the compact preview');
assert.match(mockup, /elements\.preview\.style\.setProperty\('--preview-font-size'/, 'selected size should be applied to the compact preview');
assert.match(mockup, /elements\.fontFamily\.addEventListener\('change', renderTypographyPreview\)/, 'font family should update the preview live');
assert.match(mockup, /elements\.fontSize\.addEventListener\('input', renderTypographyPreview\)/, 'font size should update the preview live');
assert.match(mockup, /elements\.fontUnit\.addEventListener\('change', handleFontUnitChange\)/, 'font size unit should update the preview live');
assert.match(mockup, /function handleFontUnitChange\(\)[\s\S]*?normalizedFontSize\(activeFontUnit\)/, 'unit conversion should resolve the value using the previous active unit');
assert.match(mockup, /elements\.reset\.addEventListener\('click', resetTypographyPreview\)/, 'reset control should restore the default preview');
assert.match(mockup, /prototype only/i, 'the page should be clearly labelled as a non-production prototype');

const inlineScripts = [...mockup.matchAll(/<script>([\s\S]*?)<\/script>/g)].map((match) => match[1]);

assert.ok(inlineScripts.length > 0, 'mockup should include its interactive script');

for (const script of inlineScripts) {
    assert.doesNotThrow(() => new Function(script), 'mockup inline JavaScript should parse');
}

console.log('Arunika v2 typography preview mockup regression passed.');
