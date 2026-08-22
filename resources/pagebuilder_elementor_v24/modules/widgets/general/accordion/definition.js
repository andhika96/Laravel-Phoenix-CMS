(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function uid(p)       { return p + '_' + Math.random().toString(36).slice(2, 9); }

function accordionItemDefaults(index = 0) {
		return {
			id: uid('accordion_item'),
			title: 'Item #' + (index + 1),
			cssId: '',
			children: [],
		};
	}

function accordionWidgetDefaultItems() {
		return [accordionItemDefaults(0), accordionItemDefaults(1), accordionItemDefaults(2)];
	}

function accordionWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			itemPosition: 'stretch',
			itemPositionTablet: '',
			itemPositionMobile: '',
			iconPosition: 'start',
			iconPositionTablet: '',
			iconPositionMobile: '',
			expandIconSource: 'library',
			expandIconClass: 'fas fa-plus',
			expandIconSvg: '',
			collapseIconSource: 'library',
			collapseIconClass: 'fas fa-minus',
			collapseIconSvg: '',
			titleTag: 'div',
			faqSchema: false,
			defaultState: 'first-expanded',
			maxExpanded: 'one',
			animationDuration: 400,
			accordionItemGap: '0px',
			accordionItemGapTablet: '',
			accordionItemGapMobile: '',
			accordionContentDistance: '0px',
			accordionContentDistanceTablet: '',
			accordionContentDistanceMobile: '',
			accordionBorderRadius: '0px',
			accordionBorderRadiusTablet: '',
			accordionBorderRadiusMobile: '',
			accordionPadding: '0px',
			accordionPaddingTablet: '',
			accordionPaddingMobile: '',
			headerFontFamily: 'inherit',
			headerFontSizeMode: 'auto',
			headerFontSize: '16px',
			headerFontSizeTablet: '',
			headerFontSizeMobile: '',
			headerFontWeight: '600',
			headerLineHeight: '1.4em',
			headerLineHeightTablet: '',
			headerLineHeightMobile: '',
			headerLetterSpacing: '0px',
			headerLetterSpacingTablet: '',
			headerLetterSpacingMobile: '',
			headerWordSpacing: '0px',
			headerWordSpacingTablet: '',
			headerWordSpacingMobile: '',
			headerTextTransform: 'none',
			headerFontStyle: 'normal',
			headerTextDecoration: 'none',
			headerIconSize: '15px',
			headerIconSizeTablet: '',
			headerIconSizeMobile: '',
			headerIconSpacing: '10px',
			headerIconSpacingTablet: '',
			headerIconSpacingMobile: '',
			contentBackgroundType: 'classic',
			contentBackgroundColor: '#ffffff',
			contentGradientColorOne: '#ffffff',
			contentGradientLocationOne: 0,
			contentGradientColorTwo: '#f4f6f8',
			contentGradientLocationTwo: 100,
			contentGradientType: 'linear',
			contentGradientAngle: 180,
			contentGradientPosition: 'center center',
			contentBorderType: 'default',
			contentBorderWidth: '0px',
			contentBorderColor: '#d5dae3',
			contentBorderRadius: '0px',
			contentBorderRadiusTablet: '',
			contentBorderRadiusMobile: '',
			contentPadding: '0px',
			contentPaddingTablet: '',
			contentPaddingMobile: '',
			cssClass: '',
			...accordionStateDefaults('Normal', '#ffffff', '#1f2937', '#667085'),
			...accordionStateDefaults('Hover', '#f8fafc', '#344054', '#475467'),
			...accordionStateDefaults('Active', '#f2f4f7', '#101828', '#344054'),
		};
	}

function accordionStateDefaults(suffix, backgroundColor, titleColor, iconColor) {
		return {
			['accordionBackgroundType' + suffix]: 'classic',
			['accordionBackgroundColor' + suffix]: backgroundColor,
			['accordionGradientColorOne' + suffix]: backgroundColor,
			['accordionGradientLocationOne' + suffix]: 0,
			['accordionGradientColorTwo' + suffix]: '#eef2f6',
			['accordionGradientLocationTwo' + suffix]: 100,
			['accordionGradientType' + suffix]: 'linear',
			['accordionGradientAngle' + suffix]: 180,
			['accordionGradientPosition' + suffix]: 'center center',
			['accordionBorderType' + suffix]: 'default',
			['accordionBorderWidth' + suffix]: '1px',
			['accordionBorderColor' + suffix]: '#d5dae3',
			['accordionBoxShadowEnabled' + suffix]: false,
			['accordionBoxShadowColor' + suffix]: 'rgba(0,0,0,.2)',
			['accordionBoxShadowX' + suffix]: '0px',
			['accordionBoxShadowY' + suffix]: '0px',
			['accordionBoxShadowBlur' + suffix]: '0px',
			['accordionBoxShadowSpread' + suffix]: '0px',
			['accordionBoxShadowInset' + suffix]: false,
			['headerTitleColor' + suffix]: titleColor,
			['headerTextShadow' + suffix]: 'none',
			['headerTextStrokeWidth' + suffix]: '0px',
			['headerTextStrokeColor' + suffix]: titleColor,
			['headerIconColor' + suffix]: iconColor,
		};
	}

	const implementation = {
			defaults: accordionWidgetDefaults,
			createNode(node) { node.accordionItems = accordionWidgetDefaultItems(); return node; },
			normalize: (node) => node,
		};
	registry.register({
		type: "accordion",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
