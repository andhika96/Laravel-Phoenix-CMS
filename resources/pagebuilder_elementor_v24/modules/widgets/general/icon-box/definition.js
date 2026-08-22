(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

function fontAwesomeStylePrefix(style) {
		if (style === 'brands') return 'fab';
		if (style === 'light') return 'fal';
		if (style === 'duotone') return 'fad';
		if (style === 'solid') return 'fas';
		return 'far';
	}

function parseIconWidgetClassParts(iconClass) {
		const tokens = String(iconClass || '').trim().split(/\s+/).filter(Boolean);
		let style = '';
		let name = '';
		tokens.forEach((token) => {
			if (token === 'fas') style = 'solid';
			else if (token === 'far') style = 'regular';
			else if (token === 'fab') style = 'brands';
			else if (token === 'fal') style = 'light';
			else if (token === 'fad') style = 'duotone';
			else if (token.startsWith('fa-')) name = token.slice(3);
		});
		return { style, name };
	}

function iconWidgetClassName(style, name) {
		const iconName = String(name || '').trim() || 'star';
		return fontAwesomeStylePrefix(style) + ' fa-' + iconName;
	}

const imageBoxTitleTagOptions = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);

const imageBoxPositionOptions = Object.freeze(['top', 'left', 'right']);

const imageBoxAlignmentOptions = Object.freeze(['left', 'center', 'right', 'justify']);

function iconBoxWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			iconStyle: 'regular', iconName: 'star', iconClass: 'far fa-star', view: 'default', shape: 'circle',
			title: 'This is the heading',
			description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [], titleTag: 'h3', titleFontSizeMode: 'auto', dynamicBindings: {},
			iconPosition: 'top', iconPositionTablet: '', iconPositionMobile: '',
			alignment: 'center', alignmentTablet: '', alignmentMobile: '',
			iconSpacing: '15px', iconSpacingTablet: '', iconSpacingMobile: '',
			contentSpacing: '0px', contentSpacingTablet: '', contentSpacingMobile: '',
			primaryColor: '#69727d', primaryColorHover: '', secondaryColor: '#ffffff', secondaryColorHover: '',
			iconSize: '50px', iconSizeTablet: '', iconSizeMobile: '',
			iconPadding: '0px', iconPaddingTablet: '', iconPaddingMobile: '',
			iconRotate: '0deg', iconRotateTablet: '', iconRotateMobile: '',
			iconBorderWidthTop: '1px', iconBorderWidthRight: '1px', iconBorderWidthBottom: '1px', iconBorderWidthLeft: '1px',
			iconBorderWidthTopTablet: '', iconBorderWidthRightTablet: '', iconBorderWidthBottomTablet: '', iconBorderWidthLeftTablet: '',
			iconBorderWidthTopMobile: '', iconBorderWidthRightMobile: '', iconBorderWidthBottomMobile: '', iconBorderWidthLeftMobile: '',
			iconBorderRadiusTop: '0px', iconBorderRadiusRight: '0px', iconBorderRadiusBottom: '0px', iconBorderRadiusLeft: '0px',
			iconBorderRadiusTopTablet: '', iconBorderRadiusRightTablet: '', iconBorderRadiusBottomTablet: '', iconBorderRadiusLeftTablet: '',
			iconBorderRadiusTopMobile: '', iconBorderRadiusRightMobile: '', iconBorderRadiusBottomMobile: '', iconBorderRadiusLeftMobile: '',
			hoverAnimation: 'none',
			titleColor: '', titleFontFamily: 'inherit', titleFontSize: '29px', titleFontSizeTablet: '', titleFontSizeMobile: '', titleFontWeight: '400',
			titleLineHeight: '1.2em', titleLineHeightTablet: '', titleLineHeightMobile: '', titleLetterSpacing: '0px', titleLetterSpacingTablet: '', titleLetterSpacingMobile: '',
			titleWordSpacing: '0px', titleWordSpacingTablet: '', titleWordSpacingMobile: '', titleTextTransform: 'none', titleFontStyle: 'normal', titleTextDecoration: 'none',
			titleTextStrokeWidth: '0px', titleTextStrokeWidthTablet: '', titleTextStrokeWidthMobile: '', titleTextStrokeColor: '#000000', titleTextShadow: 'none',
			descriptionColor: '', descriptionFontFamily: 'inherit', descriptionFontSize: '16px', descriptionFontSizeTablet: '', descriptionFontSizeMobile: '', descriptionFontWeight: '400',
			descriptionLineHeight: '1.5em', descriptionLineHeightTablet: '', descriptionLineHeightMobile: '', descriptionLetterSpacing: '0px', descriptionLetterSpacingTablet: '', descriptionLetterSpacingMobile: '',
			descriptionWordSpacing: '0px', descriptionWordSpacingTablet: '', descriptionWordSpacingMobile: '', descriptionTextTransform: 'none', descriptionFontStyle: 'normal', descriptionTextDecoration: 'none', descriptionTextShadow: 'none',
		};
	}

const iconBoxHoverAnimations = Object.freeze(['none', 'grow', 'shrink', 'pulse', 'pulse-grow', 'pulse-shrink', 'push', 'pop', 'bounce-in', 'bounce-out', 'rotate', 'grow-rotate', 'float', 'sink', 'bob', 'hang', 'skew', 'skew-forward', 'skew-backward', 'wobble-vertical', 'wobble-horizontal', 'wobble-to-bottom-right', 'wobble-to-top-right', 'wobble-top', 'wobble-bottom', 'wobble-skew', 'buzz', 'buzz-out']);

function normalizeIconBoxSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const hadTitleFontSizeMode = settings.titleFontSizeMode !== undefined;
		const legacyTitleFontSize = String(settings.titleFontSize ?? '').trim();
		const defaults = iconBoxWidgetDefaults();
		Object.keys(defaults).forEach((key) => { if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]); });
		const parsed = parseIconWidgetClassParts(settings.iconClass);
		const style = String(settings.iconStyle || parsed.style || 'regular').trim().toLowerCase();
		settings.iconStyle = ['regular', 'solid', 'brands', 'light', 'duotone'].includes(style) ? style : 'regular';
		settings.iconName = String(settings.iconName || parsed.name || 'star').trim().toLowerCase().replace(/^fa-/, '').replace(/[^a-z0-9-]/g, '') || 'star';
		settings.iconClass = iconWidgetClassName(settings.iconStyle, settings.iconName);
		settings.view = ['default', 'stacked', 'framed'].includes(settings.view) ? settings.view : 'default';
		settings.shape = ['circle', 'rounded', 'square'].includes(settings.shape) ? settings.shape : 'circle';
		settings.titleTag = imageBoxTitleTagOptions.includes(settings.titleTag) ? settings.titleTag : 'h3';
		settings.titleFontSizeMode = hadTitleFontSizeMode
			? (['auto', 'custom'].includes(settings.titleFontSizeMode) ? settings.titleFontSizeMode : 'auto')
			: (legacyTitleFontSize && legacyTitleFontSize !== '29px' ? 'custom' : 'auto');
		settings.iconPosition = imageBoxPositionOptions.includes(settings.iconPosition) ? settings.iconPosition : 'top';
		settings.alignment = imageBoxAlignmentOptions.includes(settings.alignment) ? settings.alignment : 'center';
		['iconPositionTablet', 'iconPositionMobile'].forEach((key) => { settings[key] = settings[key] === '' || imageBoxPositionOptions.includes(settings[key]) ? settings[key] : ''; });
		['alignmentTablet', 'alignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || imageBoxAlignmentOptions.includes(settings[key]) ? settings[key] : ''; });
		settings.hoverAnimation = iconBoxHoverAnimations.includes(settings.hoverAnimation) ? settings.hoverAnimation : 'none';
		settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
		settings.linkNofollow = !!settings.linkNofollow;
		settings.linkCustomAttributes = normalizeAttributes(settings.linkCustomAttributes);
		settings.dynamicBindings = settings.dynamicBindings && typeof settings.dynamicBindings === 'object' && !Array.isArray(settings.dynamicBindings) ? { ...settings.dynamicBindings } : {};
		['title', 'description', 'linkUrl'].forEach((key) => { settings[key] = String(settings[key] == null ? '' : settings[key]); });
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

function normalizeAttributes(attrs) {
		if (!Array.isArray(attrs)) return [];
		return attrs
			.map(attr => ({
				name: String(attr && (attr.name || attr.key) ? (attr.name || attr.key) : '').trim(),
				value: attr && attr.value != null ? String(attr.value) : '',
			}))
			.filter(attr => attr.name);
	}

function cloneSettingValue(value) {
		if (Array.isArray(value) || (value && typeof value === 'object')) return jclone(value);
		return value;
	}

	const implementation = {
			defaults: iconBoxWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...iconBoxWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeIconBoxSettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "icon_box",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
