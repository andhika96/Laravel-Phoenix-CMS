(function (registry) {
	'use strict';

	if (!registry) {
		throw new Error('Page Builder Elementor widget registry is not loaded.');
	}

	const allowedTags = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);
	const allowedAlignments = Object.freeze(['left', 'center', 'right', 'justify']);
	const allowedBlendModes = Object.freeze(['normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'saturation', 'color', 'difference', 'exclusion', 'hue', 'luminosity']);
	const defaults = () => ({
		text: 'Add your heading text',
		tag: 'h2',
		linkUrl: '',
		linkTarget: '',
		linkNofollow: false,
		linkCustomAttributes: [],
		dynamicBindings: { title: '', linkUrl: '' },
		align: 'left',
		alignTablet: '',
		alignMobile: '',
		color: '#101828',
		hoverColor: '',
		hoverTransitionDuration: 0.3,
		headingFontFamily: 'inherit',
		headingFontSizeMode: 'auto',
		headingFontSize: '32px',
		headingFontSizeTablet: '',
		headingFontSizeMobile: '',
		headingFontWeight: '600',
		headingTextTransform: 'none',
		headingFontStyle: 'normal',
		headingTextDecoration: 'none',
		headingLineHeight: '1.2em',
		headingLineHeightTablet: '',
		headingLineHeightMobile: '',
		headingLetterSpacing: '0px',
		headingLetterSpacingTablet: '',
		headingLetterSpacingMobile: '',
		headingWordSpacing: '0px',
		headingWordSpacingTablet: '',
		headingWordSpacingMobile: '',
		headingTextStrokeWidth: '0px',
		headingTextStrokeWidthTablet: '',
		headingTextStrokeWidthMobile: '',
		headingTextStrokeColor: '#000000',
		headingTextShadow: 'none',
		blendMode: 'normal',
		cssClass: '',
	});

	registry.register({
		type: 'heading',
		label: 'Heading',
		category: 'basic',
		icon: 'fas fa-heading',
		canvas: '/js/pagebuilder_elementor_v23/widgets/basic/heading/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/basic/heading/Settings.vue',
		toolbox: true,
		defaults,
		normalize(node) {
			const normalized = node && typeof node === 'object' ? node : {};
			const previous = normalized.settings && typeof normalized.settings === 'object' ? normalized.settings : {};
			normalized.settings = {
				...defaults(),
				...previous,
				dynamicBindings: { ...defaults().dynamicBindings, ...(previous.dynamicBindings || {}) },
				linkCustomAttributes: Array.isArray(previous.linkCustomAttributes) ? previous.linkCustomAttributes : [],
			};
			const settings = normalized.settings;
			const defaultSettings = defaults();
			settings.tag = allowedTags.includes(String(settings.tag).toLowerCase()) ? String(settings.tag).toLowerCase() : 'h2';
			const hasLegacyCustomSize = ['headingFontSize', 'headingFontSizeTablet', 'headingFontSizeMobile']
				.some((key) => settings[key] !== '' && settings[key] !== defaultSettings[key]);
			settings.headingFontSizeMode = ['auto', 'custom'].includes(settings.headingFontSizeMode)
				? settings.headingFontSizeMode
				: (hasLegacyCustomSize ? 'custom' : 'auto');
			['align', 'alignTablet', 'alignMobile'].forEach((key) => {
				if (settings[key] !== '' && !allowedAlignments.includes(settings[key])) settings[key] = key === 'align' ? 'left' : '';
			});
			settings.blendMode = allowedBlendModes.includes(settings.blendMode) ? settings.blendMode : 'normal';
			settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
			settings.linkNofollow = settings.linkNofollow === true || settings.linkNofollow === 1 || settings.linkNofollow === '1' || settings.linkNofollow === 'true';
			if (previous.fontSize && !previous.headingFontSize) settings.headingFontSize = previous.fontSize;
			if (previous.fontWeight && !previous.headingFontWeight) settings.headingFontWeight = previous.fontWeight;
			return normalized;
		},
	});
})(window.PageBuilderElementorV23Widgets);
