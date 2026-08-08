(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const shared = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
	const imageItem = (id) => ({ id, url: '', alt: '' });
	const defaults = () => ({
		...shared(), content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', imageUrl: '', imageAlt: '', imageResolution: 'full', customImageWidth: 300, customImageHeight: 300,
		name: 'John Doe', title: 'Designer', linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [], imagePosition: 'top', imagePositionTablet: '', imagePositionMobile: '', alignment: 'center', alignmentTablet: '', alignmentMobile: '',
		contentColor: '#526173', contentFontFamily: 'inherit', contentFontSize: '16px', contentFontSizeTablet: '', contentFontSizeMobile: '', contentFontWeight: '400', contentTextTransform: 'none', contentFontStyle: 'normal', contentTextDecoration: 'none', contentLineHeight: '1.5em', contentLineHeightTablet: '', contentLineHeightMobile: '', contentLetterSpacing: '0px', contentLetterSpacingTablet: '', contentLetterSpacingMobile: '', contentWordSpacing: '0px', contentWordSpacingTablet: '', contentWordSpacingMobile: '', contentTextShadow: 'none',
		nameColor: '#344054', nameFontFamily: 'inherit', nameFontSize: '18px', nameFontSizeTablet: '', nameFontSizeMobile: '', nameFontWeight: '600', nameTextTransform: 'none', nameFontStyle: 'normal', nameTextDecoration: 'none', nameLineHeight: '1.3em', nameLineHeightTablet: '', nameLineHeightMobile: '', nameLetterSpacing: '0px', nameLetterSpacingTablet: '', nameLetterSpacingMobile: '', nameWordSpacing: '0px', nameWordSpacingTablet: '', nameWordSpacingMobile: '', nameTextShadow: 'none',
		titleColor: '#7a8699', titleFontFamily: 'inherit', titleFontSize: '14px', titleFontSizeTablet: '', titleFontSizeMobile: '', titleFontWeight: '400', titleTextTransform: 'none', titleFontStyle: 'normal', titleTextDecoration: 'none', titleLineHeight: '1.4em', titleLineHeightTablet: '', titleLineHeightMobile: '', titleLetterSpacing: '0px', titleLetterSpacingTablet: '', titleLetterSpacingMobile: '', titleWordSpacing: '0px', titleWordSpacingTablet: '', titleWordSpacingMobile: '', titleTextShadow: 'none',
		imageSize: '80px', imageSizeTablet: '', imageSizeMobile: '', imageBorderType: 'none', imageBorderWidth: '1px', imageBorderColor: '#d0d7e6', imageBorderRadius: '50%', imageBorderRadiusTablet: '', imageBorderRadiusMobile: '',
	});
	registry.register({
		type: 'testimonial', label: 'Testimonial', category: 'general', icon: 'fas fa-quote-left', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/testimonial/Canvas.vue', settings: '/js/pagebuilder_elementor_v23/widgets/general/testimonial/Settings.vue', defaults,
		normalize(node) {
			const previous = node.settings && typeof node.settings === 'object' ? node.settings : {};
			node.settings = { ...defaults(), ...previous };
			node.settings.imagePosition = ['top','left','right'].includes(node.settings.imagePosition) ? node.settings.imagePosition : 'top';
			node.settings.alignment = ['left','center','right','justify'].includes(node.settings.alignment) ? node.settings.alignment : 'center';
			['imagePositionTablet','imagePositionMobile'].forEach((key) => { node.settings[key] = node.settings[key] === '' || ['top','left','right'].includes(node.settings[key]) ? node.settings[key] : ''; });
			['alignmentTablet','alignmentMobile'].forEach((key) => { node.settings[key] = node.settings[key] === '' || ['left','center','right','justify'].includes(node.settings[key]) ? node.settings[key] : ''; });
			node.settings.imageResolution = ['thumbnail','medium','medium_large','large','1536x1536','2048x2048','full','custom'].includes(node.settings.imageResolution) ? node.settings.imageResolution : 'full';
			node.settings.customImageWidth = Math.max(1, Math.min(4096, Number(node.settings.customImageWidth) || 300));
			node.settings.customImageHeight = Math.max(1, Math.min(4096, Number(node.settings.customImageHeight) || 300));
			return node;
		},
	});
})(window.PageBuilderElementorV23Widgets);
