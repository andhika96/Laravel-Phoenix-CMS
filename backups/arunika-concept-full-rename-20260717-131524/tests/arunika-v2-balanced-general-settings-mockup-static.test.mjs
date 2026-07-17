import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';

const mockupPath = new URL('../public/mockups/site-general-settings-balanced-layout-mockup.html', import.meta.url);

assert.equal(existsSync(mockupPath), true, 'balanced General Settings mockup must exist');

const source = readFileSync(mockupPath, 'utf8');

assert.match(source, /id="siteGeneralSettingsBalancedMockup"/);
assert.match(source, /id="siteInformationLayout"[\s\S]*class="site-information-grid"/);
assert.match(source, /id="siteThumbnailCard"/);
assert.match(source, /id="siteThumbnailInput"[^>]*type="file"/);
assert.match(source, /id="typographySettingsLayout"/);
assert.match(source, /id="fontFamilyCombobox"[^>]*role="combobox"/);
assert.match(source, /id="fontFamilyOptions"[^>]*role="listbox"/);
assert.match(source, /id="fontSizeUnit"[\s\S]*<option value="px">px<\/option>[\s\S]*<option value="em">em<\/option>[\s\S]*<option value="rem">rem<\/option>[\s\S]*<\/select>/);
assert.doesNotMatch(source, /id="fontSizeUnit"[\s\S]*?<option value="pt">/);
assert.match(source, /id="typographyPreview"/);
assert.match(source, /id="resetTypographyPreview"/);
assert.match(source, /handleFontFamilySelection/);
assert.match(source, /handleFontSizeUnitChange/);
assert.match(source, /handleThumbnailSelection/);
assert.match(source, /@media \(max-width: 991\.98px\)[\s\S]*\.site-information-grid[\s\S]*grid-template-columns: 1fr/);
assert.match(source, /\.typography-settings-layout[\s\S]*grid-column: 1 \/ -1/);

console.log('Arunika v2 balanced General Settings mockup regression passed.');
