(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const shared = () => window.PageBuilderElementorComplexWidgetRuntime?.image_box?.defaults?.() || {};
	const defaults = () => ({
		...shared(),
		ratingScale: 5,
		rating: 5,
		iconSource: 'library',
		iconStyle: 'solid',
		iconName: 'star',
		iconClass: 'fas fa-star',
		iconSvg: '',
		alignment: 'left', alignmentTablet: '', alignmentMobile: '',
		iconSize: '18px', iconSizeTablet: '', iconSizeMobile: '',
		iconSpacing: '4px', iconSpacingTablet: '', iconSpacingMobile: '',
		markedColor: '#f0ad4e',
		unmarkedColor: '#ccd2dc',
	});
	registry.register({
		type: 'rating', label: 'Rating', category: 'general', icon: 'fas fa-star-half-alt', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/general/rating/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/general/rating/Settings.vue',
		defaults,
		normalize(node) {
			const settings = node.settings = { ...defaults(), ...(node.settings || {}) };
			settings.ratingScale = Math.max(1, Math.min(10, Math.round(Number(settings.ratingScale) || 5)));
			settings.rating = Math.max(0, Math.min(settings.ratingScale, Math.round((Number(settings.rating) || 0) * 2) / 2));
			settings.iconSource = settings.iconSource === 'svg' && String(settings.iconSvg || '').trim() ? 'svg' : 'library';
			settings.iconStyle = String(settings.iconStyle || 'solid');
			settings.iconName = String(settings.iconName || 'star').replace(/^fa-/, '') || 'star';
			settings.iconClass = settings.iconSource === 'svg' ? '' : String(settings.iconClass || 'fas fa-star');
			settings.alignment = ['left', 'center', 'right'].includes(settings.alignment) ? settings.alignment : 'left';
			['alignmentTablet', 'alignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center', 'right'].includes(settings[key]) ? settings[key] : ''; });
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
