(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const shared = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
	const defaults = () => ({
		...shared(), title: 'Web Design', titleTag: 'div', percentage: 75, displayPercentage: true, innerText: '',
		progressColor: '#69727d', backgroundColor: '#eef1f4', innerTextColor: '#ffffff', titleColor: '#344054',
		titleFontFamily: 'inherit', titleFontSizeMode: 'auto', titleFontSize: '14px', titleFontSizeTablet: '', titleFontSizeMobile: '', titleFontWeight: '600', titleTextTransform: 'none', titleFontStyle: 'normal', titleTextDecoration: 'none', titleLineHeight: '1.4em', titleLineHeightTablet: '', titleLineHeightMobile: '', titleLetterSpacing: '0px', titleLetterSpacingTablet: '', titleLetterSpacingMobile: '', titleWordSpacing: '0px', titleWordSpacingTablet: '', titleWordSpacingMobile: '', titleTextShadow: 'none',
	});
	registry.register({
		type: 'progress_bar', label: 'Progress Bar', category: 'general', icon: 'fas fa-tasks', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/progress-bar/Canvas.vue', settings: '/js/pagebuilder_elementor_v23/widgets/general/progress-bar/Settings.vue', defaults,
		normalize(node) {
			const previous = node.settings && typeof node.settings === 'object' ? node.settings : {};
			node.settings = { ...defaults(), ...previous };
			node.settings.percentage = Number.isFinite(Number(node.settings.percentage)) ? Math.max(0, Math.min(100, Number(node.settings.percentage))) : 75;
			node.settings.displayPercentage = !!node.settings.displayPercentage;
			node.settings.titleTag = ['h1','h2','h3','h4','h5','h6','div','span','p'].includes(String(node.settings.titleTag).toLowerCase()) ? String(node.settings.titleTag).toLowerCase() : 'div';
			const legacyTitleSize = ['titleFontSize', 'titleFontSizeTablet', 'titleFontSizeMobile'].some((key) => node.settings[key] !== '' && node.settings[key] !== defaults()[key]);
			node.settings.titleFontSizeMode = ['auto', 'custom'].includes(node.settings.titleFontSizeMode) ? node.settings.titleFontSizeMode : (legacyTitleSize ? 'custom' : 'auto');
			return node;
		},
	});
})(window.PageBuilderElementorV23Widgets);
