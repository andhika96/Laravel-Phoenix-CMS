import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';

const typographyPath = new URL('../public/js/pagebuilder_elementor/widgets/shared/TypographyControl.vue', import.meta.url);

test('TypographyControl keeps editor labels and compact inputs at the readable 12px scale', () => {
	const source = fs.readFileSync(typographyPath, 'utf8');

	assert.match(source, /\.pb-typography-family-field > label \{[^}]*font-size: 12px;/);
	assert.match(source, /\.pb-font-family-group \{[^}]*font-size: 11px;/);
	assert.match(source, /\.pb-typography-select-field \{[^}]*font-size: 12px;/);
	assert.match(source, /\.pb-typography-select-field \.pb-select \{[^}]*font-size: 12px;/);
	assert.match(source, /:deep\(\.pb-typography-dimension-head\) \{[^}]*font-size: 12px;/);
	assert.match(source, /:deep\(\.pb-typography-dimension-tools \.pb-mini-unit\) \{[^}]*font-size: 12px;/);
	assert.match(source, /:deep\(\.pb-typography-range-row \.pb-input\) \{[^}]*font-size: 12px;/);
	assert.match(source, /:deep\(\.pb-control-device-menu button\) \{[^}]*font-size: 12px;/);
});
