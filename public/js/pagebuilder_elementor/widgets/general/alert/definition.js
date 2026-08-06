(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const shared = () => window.PageBuilderElementorComplexWidgetRuntime?.image_box?.defaults?.() || {};
	const defaults = () => ({
		...shared(), type: 'info', title: 'This is an alert', description: 'Click here to learn more.', dismissIcon: true, dismissIconSource: 'library', dismissIconStyle: 'solid', dismissIconName: 'times', dismissIconClass: 'fas fa-times', dismissIconSvg: '',
		backgroundColor: '#eaf4ff', borderColor: '#b6d7fe', borderWidth: '4px',
		titleColor: '#1d4ed8', titleFontFamily: 'inherit', titleFontSize: '18px', titleFontSizeTablet: '', titleFontSizeMobile: '', titleFontWeight: '700', titleTextTransform: 'none', titleFontStyle: 'normal', titleTextDecoration: 'none', titleLineHeight: '1.3em', titleLineHeightTablet: '', titleLineHeightMobile: '', titleLetterSpacing: '0px', titleLetterSpacingTablet: '', titleLetterSpacingMobile: '', titleWordSpacing: '0px', titleWordSpacingTablet: '', titleWordSpacingMobile: '', titleTextShadow: 'none',
		descriptionColor: '#344054', descriptionFontFamily: 'inherit', descriptionFontSize: '14px', descriptionFontSizeTablet: '', descriptionFontSizeMobile: '', descriptionFontWeight: '400', descriptionTextTransform: 'none', descriptionFontStyle: 'normal', descriptionTextDecoration: 'none', descriptionLineHeight: '1.5em', descriptionLineHeightTablet: '', descriptionLineHeightMobile: '', descriptionLetterSpacing: '0px', descriptionLetterSpacingTablet: '', descriptionLetterSpacingMobile: '', descriptionWordSpacing: '0px', descriptionWordSpacingTablet: '', descriptionWordSpacingMobile: '', descriptionTextShadow: 'none',
		dismissColor: '#344054', dismissColorHover: '#101828', dismissTransitionDuration: 0.3, dismissSize: '16px', dismissVerticalPosition: 'top', dismissHorizontalPosition: 'right',
	});
	registry.register({
		type: 'alert', label: 'Alert', category: 'general', icon: 'fas fa-exclamation-triangle', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/general/alert/Canvas.vue', settings: '/js/pagebuilder_elementor/widgets/general/alert/Settings.vue', defaults,
		normalize(node) {
			const previous = node.settings && typeof node.settings === 'object' ? node.settings : {};
			node.settings = { ...defaults(), ...previous };
			node.settings.type = ['info','success','warning','danger'].includes(node.settings.type) ? node.settings.type : 'info';
			node.settings.dismissIcon = !!node.settings.dismissIcon;
			node.settings.dismissVerticalPosition = ['top','middle','bottom'].includes(node.settings.dismissVerticalPosition) ? node.settings.dismissVerticalPosition : 'top';
			node.settings.dismissHorizontalPosition = ['left','right'].includes(node.settings.dismissHorizontalPosition) ? node.settings.dismissHorizontalPosition : 'right';
			node.settings.dismissTransitionDuration = Number.isFinite(Number(node.settings.dismissTransitionDuration)) ? Math.max(0, Math.min(10, Number(node.settings.dismissTransitionDuration))) : 0.3;
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
