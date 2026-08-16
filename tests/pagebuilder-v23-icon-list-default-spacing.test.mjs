import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';

const root = new URL('../', import.meta.url);
const read = (relativePath) => fs.readFileSync(new URL(relativePath, root), 'utf8');

test('Icon List v2.3 keeps the requested default spacing across editor and frontend layers', () => {
	const app = read('public/js/pagebuilder_elementor_v23/app.js');
	const settings = read('public/js/pagebuilder_elementor_v23/widgets/general/icon-list/Settings.vue');
	const canvas = read('public/js/pagebuilder_elementor_v23/widgets/general/icon-list/Canvas.vue');
	const frontend = read('resources/views/pagebuilder_elementor_v23/partials/render_icon_list.blade.php');

	assert.match(app, /spaceBetween: '15px', spaceBetweenTablet: '', spaceBetweenMobile: ''/);
	assert.match(app, /iconSize: '14px', iconSizeTablet: '', iconSizeMobile: '', iconGap: '20px', iconGapTablet: '', iconGapMobile: ''/);
	assert.match(settings, /label="Space Between" base="spaceBetween"[^>]*fallback="15px"/);
	assert.match(settings, /label="Gap" base="iconGap"[^>]*fallback="20px"/);
	assert.match(canvas, /responsiveValue\('spaceBetween','15px'\)/);
	assert.match(canvas, /responsiveValue\('iconGap','20px'\)/);
	assert.match(frontend, /responsive\('spaceBetween', \$suffix, '15px'\)/);
	assert.match(frontend, /responsive\('iconGap', \$suffix, '20px'\)/);
});
