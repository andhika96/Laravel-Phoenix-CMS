(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function uid(p)       { return p + '_' + Math.random().toString(36).slice(2, 9); }

function tabsItemDefaults(index = 0) {
		return {
			id: uid('tab'),
			title: 'Tab #' + (index + 1),
			iconSource: 'none',
			iconClass: '',
			iconSvg: '',
			activeIconClass: '',
			cssId: '',
			dynamicBindings: { title: '', cssId: '' },
			children: [],
		};
	}

function tabsWidgetDefaultItems() {
		return [tabsItemDefaults(0), tabsItemDefaults(1), tabsItemDefaults(2)];
	}

function tabsWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			direction: 'row',
			directionTablet: '',
			directionMobile: '',
			justify: 'flex-start',
			justifyTablet: '',
			justifyMobile: '',
			alignTitle: 'center',
			alignTitleTablet: '',
			alignTitleMobile: '',
			tabWidth: '',
			tabWidthUnit: 'px',
			horizontalScroll: false,
			horizontalScrollTablet: '',
			horizontalScrollMobile: '',
			breakpoint: 'mobile',
			activeTabId: '',
			tabsGap: '8px', tabsGapTablet: '', tabsGapMobile: '',
			tabsContentDistance: '16px', tabsContentDistanceTablet: '', tabsContentDistanceMobile: '',
			tabsNavBorderType: 'none',
			tabsNavBorderWidth: '0px',
			tabsNavBorderColor: 'transparent',
			tabsNormalTextColor: '#4f5f78',
			tabsNormalTextShadow: 'none', tabsNormalTextStrokeWidth: '0px', tabsNormalTextStrokeColor: '#4f5f78', tabsNormalIconColor: '#4f5f78',
			tabsNormalBackgroundType: 'classic', tabsNormalGradientColorOne: '#f3f5fa', tabsNormalGradientColorTwo: '#ffffff', tabsNormalGradientType: 'linear', tabsNormalGradientAngle: 180, tabsNormalGradientPosition: 'center center',
			tabsNormalBoxShadowEnabled: false, tabsNormalBoxShadowColor: 'rgba(0,0,0,.2)', tabsNormalBoxShadowX: '0px', tabsNormalBoxShadowY: '0px', tabsNormalBoxShadowBlur: '0px', tabsNormalBoxShadowSpread: '0px', tabsNormalBoxShadowInset: false,
			tabsNormalBackgroundColor: '#f3f5fa',
			tabsNormalBorderType: 'solid',
			tabsNormalBorderWidth: '1px',
			tabsNormalBorderColor: '#dde3ef',
			tabsNormalBorderRadius: '0px',
			tabsNormalPadding: '14px 20px',
			tabsHoverTextColor: '#4f5ec9',
			tabsHoverTextShadow: 'none', tabsHoverTextStrokeWidth: '0px', tabsHoverTextStrokeColor: '#4f5ec9', tabsHoverIconColor: '#4f5ec9',
			tabsHoverBackgroundType: 'classic', tabsHoverGradientColorOne: '#eef1ff', tabsHoverGradientColorTwo: '#ffffff', tabsHoverGradientType: 'linear', tabsHoverGradientAngle: 180, tabsHoverGradientPosition: 'center center',
			tabsHoverBoxShadowEnabled: false, tabsHoverBoxShadowColor: 'rgba(0,0,0,.2)', tabsHoverBoxShadowX: '0px', tabsHoverBoxShadowY: '0px', tabsHoverBoxShadowBlur: '0px', tabsHoverBoxShadowSpread: '0px', tabsHoverBoxShadowInset: false,
			tabsHoverBackgroundColor: '#eef1ff',
			tabsHoverBorderType: 'solid',
			tabsHoverBorderWidth: '1px',
			tabsHoverBorderColor: '#c9d3f3',
			tabsHoverBorderRadius: '0px',
			tabsHoverPadding: '14px 20px',
			tabsActiveTextColor: '#ffffff',
			tabsActiveTextShadow: 'none', tabsActiveTextStrokeWidth: '0px', tabsActiveTextStrokeColor: '#ffffff', tabsActiveIconColor: '#ffffff',
			tabsActiveBackgroundType: 'classic', tabsActiveGradientColorOne: '#4f5ec9', tabsActiveGradientColorTwo: '#7c8cff', tabsActiveGradientType: 'linear', tabsActiveGradientAngle: 180, tabsActiveGradientPosition: 'center center',
			tabsActiveBoxShadowEnabled: false, tabsActiveBoxShadowColor: 'rgba(0,0,0,.2)', tabsActiveBoxShadowX: '0px', tabsActiveBoxShadowY: '0px', tabsActiveBoxShadowBlur: '0px', tabsActiveBoxShadowSpread: '0px', tabsActiveBoxShadowInset: false,
			tabsActiveBackgroundColor: '#4f5ec9',
			tabsActiveBorderType: 'solid',
			tabsActiveBorderWidth: '1px',
			tabsActiveBorderColor: '#4f5ec9',
			tabsActiveBorderRadius: '0px',
			tabsActivePadding: '14px 20px',
			tabsTitleFontFamily: 'inherit',
			tabsTitleFontSize: '14px',
			tabsTitleFontWeight: '500',
			tabsTitleTextTransform: 'none',
			tabsTitleFontStyle: 'normal',
			tabsTitleTextDecoration: 'none',
			tabsTitleLineHeight: '1.3em',
			tabsTitleLetterSpacing: '0px',
			tabsTitleWordSpacing: '0px',
			tabsIconSize: '14px',
			tabsIconSizeTablet: '', tabsIconSizeMobile: '',
			tabsIconSpacing: '10px',
			tabsIconSpacingTablet: '', tabsIconSpacingMobile: '',
			tabsIconPosition: 'row', tabsIconPositionTablet: '', tabsIconPositionMobile: '',
			tabsContentBackgroundType: 'classic', tabsContentGradientColorOne: '#ffffff', tabsContentGradientColorTwo: '#f3f5fa', tabsContentGradientType: 'linear', tabsContentGradientAngle: 180, tabsContentGradientPosition: 'center center',
			tabsContentBackgroundColor: 'transparent',
			tabsContentTextColor: 'inherit',
			tabsContentBorderType: 'none',
			tabsContentBorderWidth: '0px',
			tabsContentBorderColor: 'transparent',
			tabsContentBorderRadius: '0px',
			tabsContentPadding: '0px',
			tabsContentFontFamily: 'inherit',
			tabsContentFontSize: '16px',
			tabsContentFontWeight: 'inherit',
			tabsContentTextTransform: 'none',
			tabsContentFontStyle: 'normal',
			tabsContentTextDecoration: 'none',
			tabsContentLineHeight: '1.5em',
			tabsContentLetterSpacing: '0px',
			tabsContentWordSpacing: '0px',
			cssClass: '',
		};
	}

	const implementation = {
			defaults: tabsWidgetDefaults,
			createNode(node) {
				node.tabItems = tabsWidgetDefaultItems();
				node.settings.activeTabId = node.tabItems[0].id;
				return node;
			},
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...tabsWidgetDefaults(), ...(normalized.settings || {}) };
				return normalized;
			},
		};
	registry.register({
		type: "tabs",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
