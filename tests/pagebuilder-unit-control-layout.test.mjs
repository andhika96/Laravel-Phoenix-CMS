import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';

const cssPath = new URL('../public/assets/css/pagebuilder_elementor.css', import.meta.url);
const sidesControlPaths = [
	new URL('../public/js/pagebuilder_elementor/widgets/general/icon-box/Settings.vue', import.meta.url),
	new URL('../public/js/pagebuilder_elementor/widgets/general/basic-gallery/Settings.vue', import.meta.url),
	new URL('../public/js/pagebuilder_elementor/widgets/general/image-carousel/Settings.vue', import.meta.url),
];

test('responsive side-control unit selects align to the trailing edge of the label row', () => {
	const css = fs.readFileSync(cssPath, 'utf8');
	const directUnitRule = css.match(/\.pb-label-row\.pb-label-row-device\s*>\s*\.pb-label-tools\s*>\s*\.pb-mini-unit\s*\{([\s\S]*?)\}/);

	assert.ok(directUnitRule, 'the shared responsive label row should style direct unit selects');
	assert.match(directUnitRule[1], /margin-left\s*:\s*auto\s*;/, 'the unit select should be pushed to the trailing edge');
	assert.match(directUnitRule[1], /flex\s*:\s*0\s+0\s+auto\s*;/, 'the unit select should keep its intrinsic width');
});

test('all widget side controls use the shared label-tools/unit-select structure', () => {
	for (const path of sidesControlPaths) {
		const source = fs.readFileSync(path, 'utf8');
		assert.match(
			source,
			/pb-label-row pb-label-row-device[\s\S]{0,260}pb-label-tools[\s\S]{0,260}pb-mini-unit/,
			`${path.pathname} should keep its unit select inside pb-label-tools`,
		);
	}
});

