import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';

const mockupPath = 'public/mockups/site-logo-upload-config-mockup.html';

assert.ok(existsSync(mockupPath), 'the standalone Site Logo upload mockup should exist');

const mockup = readFileSync(mockupPath, 'utf8');

for (const id of [
    'generalSettingsSection',
    'logoSettingsSection',
    'siteNameInput',
    'siteSloganInput',
    'siteKeywordInput',
    'siteDescriptionInput',
    'siteThumbnailInput',
    'logoFileInput',
    'logoDropzone',
    'chooseLogoButton',
    'removeLogo',
    'logoWidthControls',
    'logoWidthValue',
    'logoWidthUnit',
    'logoWidthReadout',
    'saveMockup',
    'sidebarPreview',
    'expandedPreview',
    'collapsedPreview',
]) {
    assert.match(mockup, new RegExp(`id=["']${id}["']`), `mockup should expose #${id}`);
}

const generalSettingsIndex = mockup.indexOf('id="generalSettingsSection"');
const logoSettingsIndex = mockup.indexOf('id="logoSettingsSection"');
assert.ok(generalSettingsIndex >= 0 && logoSettingsIndex > generalSettingsIndex, 'Logo Settings should appear directly after General Settings');
assert.match(mockup, /id=["']generalSettingsSection["'][^>]*class=["'][^"']*ph-content[^"']*rounded[^"']*p-4[^"']*mb-4/, 'General Settings should use the real Site Config section shell');
assert.match(mockup, /id=["']logoSettingsSection["'][^>]*class=["'][^"']*ph-content[^"']*rounded[^"']*p-4[^"']*mb-4/, 'Logo Settings should use the same Site Config section shell');
assert.match(mockup, /fas fa-cog fa-fw me-1[\s\S]*?General Settings/, 'General Settings should retain the Site Config heading pattern');
assert.match(mockup, /fas fa-image fa-fw me-1[\s\S]*?Logo Settings/, 'Logo Settings should use the matching icon heading pattern');
assert.doesNotMatch(mockup, /id=["']useSampleLogo["']|Use sample logo|SAMPLE_LOGO/, 'the production-oriented mockup should not retain the sample-logo helper');
assert.match(mockup, /accept=["']image\/png,image\/jpeg,image\/webp,image\/svg\+xml["']/, 'file input should limit visible choices to supported image formats');
assert.match(mockup, /2 \* 1024 \* 1024/, 'file validation should enforce the 2 MB limit');
assert.match(mockup, /FileReader/, 'the selected local logo should be previewed with FileReader');
assert.match(mockup, /dragover/, 'the upload zone should support drag and drop');
assert.match(mockup, /logoState\.src = ''/, 'removing a logo should clear the logo source');
assert.match(mockup, /id=["']logoWidthValue["'][^>]*value=["']100["']/, 'logo width should default to 100');
assert.match(mockup, /id=["']logoWidthValue["'][^>]*max=["']100["']/, 'percent logo width should expose a maximum of 100');
assert.match(mockup, /id=["']logoWidthUnit["'][\s\S]*?<option value=["']%["'] selected>%<\/option>[\s\S]*?<option value=["']px["']>px<\/option>[\s\S]*?<option value=["']em["']>em<\/option>[\s\S]*?<option value=["']rem["']>rem<\/option>[\s\S]*?<option value=["']pt["']>pt<\/option>/, 'logo width should expose the approved units with percent selected');
assert.match(mockup, /const DEFAULT_LOGO_WIDTH_PX = 34;/, '100 percent should scale from the original 34px sidebar logo width');
assert.match(mockup, /function logoWidthMaxForUnit\(unit\)[\s\S]*?return unit === '%' \? 100 : 500;/, 'percent should cap at 100 while absolute units retain the larger limit');
assert.match(mockup, /function resolveLogoWidth\(\)/, 'mockup should resolve the selected value and unit');
assert.match(mockup, /elements\.sidebarLogo\.style\.width = resolvedWidth\.cssValue;/, 'resolved logo width should be applied to the sidebar preview');
assert.match(mockup, /function handleLogoWidthValueInput\(\)[\s\S]*?Math\.min\(maxValue, Math\.max\(1, parsedValue\)\)/, 'manual percent input should be clamped before rendering');
assert.match(mockup, /elements\.logoWidthValue\.addEventListener\('input', handleLogoWidthValueInput\)/, 'logo width value should validate and update the preview live');
assert.match(mockup, /elements\.logoWidthUnit\.addEventListener\('change', handleLogoWidthUnitChange\)/, 'switching units should update the preview live');
assert.match(mockup, /prototype only/i, 'the page should be clearly labelled as a non-production prototype');

const inlineScripts = [...mockup.matchAll(/<script>([\s\S]*?)<\/script>/g)].map((match) => match[1]);
assert.ok(inlineScripts.length > 0, 'mockup should include its interactive script');
for (const script of inlineScripts) {
    assert.doesNotThrow(() => new Function(script), 'mockup inline JavaScript should parse');
}

console.log('Arunika Aurora Site Logo upload mockup regression passed.');
