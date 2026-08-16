(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const shared = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
	const length = (value, fallback = '') => {
		const raw = String(value ?? '').trim();
		return raw === '' || /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)$/i.test(raw) ? raw : fallback;
	};
	const defaults = () => ({
		...shared(),
		startingNumber: 0,
		endingNumber: 100,
		numberPrefix: '',
		numberSuffix: '',
		animationDuration: 2000,
		thousandSeparator: true,
		separator: 'default',
		title: 'Counter',
		titleTag: 'div',
		titlePosition: 'below', titlePositionTablet: '', titlePositionMobile: '',
		titleAlign: 'center', titleAlignTablet: '', titleAlignMobile: '',
		titleGap: '8px', titleGapTablet: '', titleGapMobile: '',
		numberPosition: 'center', numberPositionTablet: '', numberPositionMobile: '',
		numberColor: '#101828', numberTextStrokeWidth: '0px', numberTextStrokeWidthTablet: '', numberTextStrokeWidthMobile: '', numberTextStrokeColor: 'currentColor', numberTextShadow: 'none',
		numberFontFamily: 'inherit', numberFontSize: '48px', numberFontSizeTablet: '', numberFontSizeMobile: '', numberFontWeight: '700', numberTextTransform: 'none', numberFontStyle: 'normal', numberTextDecoration: 'none', numberLineHeight: '1.2em', numberLineHeightTablet: '', numberLineHeightMobile: '', numberLetterSpacing: '0px', numberLetterSpacingTablet: '', numberLetterSpacingMobile: '', numberWordSpacing: '0px', numberWordSpacingTablet: '', numberWordSpacingMobile: '',
		titleColor: '#344054', titleTextStrokeWidth: '0px', titleTextStrokeWidthTablet: '', titleTextStrokeWidthMobile: '', titleTextStrokeColor: 'currentColor', titleTextShadow: 'none',
		titleFontFamily: 'inherit', titleFontSize: '16px', titleFontSizeTablet: '', titleFontSizeMobile: '', titleFontWeight: '500', titleTextTransform: 'none', titleFontStyle: 'normal', titleTextDecoration: 'none', titleLineHeight: '1.4em', titleLineHeightTablet: '', titleLineHeightMobile: '', titleLetterSpacing: '0px', titleLetterSpacingTablet: '', titleLetterSpacingMobile: '', titleWordSpacing: '0px', titleWordSpacingTablet: '', titleWordSpacingMobile: '',
	});
	registry.register({
		type: 'counter', label: 'Counter', category: 'general', icon: 'fas fa-sort-numeric-up', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/counter/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/general/counter/Settings.vue',
		defaults,
		normalize(node) {
			const previous = node.settings && typeof node.settings === 'object' ? node.settings : {};
			node.settings = { ...defaults(), ...previous };
			['startingNumber', 'endingNumber'].forEach((key) => { node.settings[key] = Number.isFinite(Number(node.settings[key])) ? Number(node.settings[key]) : defaults()[key]; });
			node.settings.animationDuration = Number.isFinite(Number(node.settings.animationDuration)) ? Math.max(0, Math.min(10000, Number(node.settings.animationDuration))) : 2000;
			node.settings.thousandSeparator = !!node.settings.thousandSeparator;
			node.settings.separator = ['default', 'dot', 'space'].includes(node.settings.separator) ? node.settings.separator : 'default';
			node.settings.titleTag = ['h1','h2','h3','h4','h5','h6','div','span','p'].includes(String(node.settings.titleTag).toLowerCase()) ? String(node.settings.titleTag).toLowerCase() : 'div';
			node.settings.titlePosition = ['above','below','left','right'].includes(node.settings.titlePosition) ? node.settings.titlePosition : 'below';
			node.settings.numberPosition = ['left','center','right','stretch'].includes(node.settings.numberPosition) ? node.settings.numberPosition : 'center';
			['titlePositionTablet','titlePositionMobile'].forEach((key) => { node.settings[key] = node.settings[key] === '' || ['above','below','left','right'].includes(node.settings[key]) ? node.settings[key] : ''; });
			['titleAlign','titleAlignTablet','titleAlignMobile'].forEach((key) => { node.settings[key] = node.settings[key] === '' || ['left','center','right'].includes(node.settings[key]) ? node.settings[key] : (key === 'titleAlign' ? 'center' : ''); });
			['numberPositionTablet','numberPositionMobile'].forEach((key) => { node.settings[key] = node.settings[key] === '' || ['left','center','right','stretch'].includes(node.settings[key]) ? node.settings[key] : ''; });
			['titleGap','titleGapTablet','titleGapMobile'].forEach((key) => { node.settings[key] = length(node.settings[key], key === 'titleGap' ? '8px' : ''); });
			return node;
		},
	});
})(window.PageBuilderElementorV23Widgets);
