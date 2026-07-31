import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';

const canvasPath = new URL('../public/js/pagebuilder_elementor/widgets/general/icon-list/Canvas.vue', import.meta.url);
const settingsPath = new URL('../public/js/pagebuilder_elementor/widgets/general/icon-list/Settings.vue', import.meta.url);

function componentFromVueFile(path) {
	const source = fs.readFileSync(path, 'utf8');
	const match = source.match(/<script>([\s\S]*?)<\/script>/);
	assert.ok(match, `Missing <script> in ${path.pathname}`);

	return new Function(match[1].replace('export default', 'return'))();
}

function canvasContext(settings, responsiveDevice = 'desktop') {
	const component = componentFromVueFile(canvasPath);
	const context = { settings, responsiveDevice };
	for (const [name, method] of Object.entries(component.methods)) {
		context[name] = method.bind(context);
	}
	return context;
}

test('Icon List Vue components compile as JavaScript', () => {
	assert.equal(componentFromVueFile(canvasPath).name, 'GeneralIconList');
	assert.equal(componentFromVueFile(settingsPath).name, 'IconListWidgetSettings');
});

test('canvas resolves responsive values and mapped style controls', () => {
	const context = canvasContext({
		layout: 'traditional',
		applyLinkOn: 'inline',
		alignment: 'start',
		alignmentTablet: 'center',
		spaceBetween: '4px',
		spaceBetweenTablet: '12px',
		iconSize: '14px',
		iconSizeTablet: '24px',
		iconGap: '8px',
		iconVerticalOffset: '0px',
		iconVerticalOffsetTablet: '-3px',
		iconHorizontalAlignment: 'left',
		iconHorizontalAlignmentTablet: 'right',
		iconVerticalAlignment: 'center',
		iconVerticalAlignmentTablet: 'flex-start',
		divider: true,
		dividerStyle: 'dashed',
		dividerWeight: '2px',
		dividerWidth: '75%',
		dividerColor: '#123456',
		iconColor: '#111111',
		iconColorHover: '#222222',
		iconTransitionDuration: 0.4,
		textColor: '#333333',
		textColorHover: '#444444',
		textTransitionDuration: 0.5,
		textFontFamily: 'Inter',
		textFontSize: '16px',
		textFontSizeTablet: '19px',
		textFontWeight: '700',
		textLineHeight: '1.7em',
		textLetterSpacing: '1px',
		textWordSpacing: '2px',
		textTextTransform: 'uppercase',
		textFontStyle: 'italic',
		textTextDecoration: 'underline',
		textTextShadow: '1px 1px 2px #000000',
	}, 'tablet');

	assert.deepEqual(context.rootClasses(), {
		'is-inline': false,
		'has-divider': true,
		'pb-icon-list--apply-inline': true,
	});
	assert.equal(context.rootStyle()['--pb-icon-list-align'], 'center');
	assert.equal(context.rootStyle()['--pb-icon-list-space-between'], '12px');
	assert.equal(context.rootStyle()['--pb-icon-list-divider-size'], '75%');
	assert.equal(context.rootStyle()['--pb-icon-list-icon-size'], '24px');
	assert.equal(context.itemStyle().justifyContent, 'center');
	assert.equal(context.itemStyle().alignItems, 'flex-start');
	assert.equal(context.iconStyle().justifyContent, 'flex-end');
	assert.equal(context.iconStyle().transform, 'translateY(-3px)');
	assert.equal(context.textStyle().fontSize, '19px');
	assert.equal(context.textStyle().fontWeight, '700');
	assert.equal(context.textStyle().textTransform, 'uppercase');
});

test('canvas sanitizes links, rel values, and custom attributes', () => {
	const context = canvasContext({});
	const item = {
		linkUrl: 'https://example.com/docs',
		linkTarget: '_blank',
		linkNofollow: true,
		linkCustomAttributes: [
			{ name: 'data-track', value: 'docs' },
			{ name: 'aria-label', value: 'Documentation' },
			{ name: 'onclick', value: 'alert(1)' },
		],
	};

	assert.equal(context.linkFor(item), 'https://example.com/docs');
	assert.equal(context.linkFor({ linkUrl: 'javascript:alert(1)' }), '');
	assert.equal(context.linkFor({ linkUrl: '//unsafe.example' }), '');
	assert.equal(context.relFor(item), 'nofollow noopener noreferrer');
	assert.deepEqual(context.attributesFor(item), {
		'data-track': 'docs',
		'aria-label': 'Documentation',
	});
});
