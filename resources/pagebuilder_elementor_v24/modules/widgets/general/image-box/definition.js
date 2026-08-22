(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

function imageBoxFilterDefaults() {
		return { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 };
	}

function imageBoxWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			imageUrl: '',
			imageAlt: '',
			imageResolution: 'full',
			customImageWidth: 150,
			customImageHeight: 150,
			title: 'This is the heading',
			description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			linkUrl: '',
			linkTarget: '',
			linkNofollow: false,
			linkCustomAttributes: [],
			titleTag: 'h3',
			titleFontSizeMode: 'auto',
			dynamicBindings: {},
			imagePosition: 'top',
			imagePositionTablet: '',
			imagePositionMobile: '',
			alignment: 'center',
			alignmentTablet: '',
			alignmentMobile: '',
			imageSpacing: '15px',
			imageSpacingTablet: '',
			imageSpacingMobile: '',
			contentSpacing: '0px',
			contentSpacingTablet: '',
			contentSpacingMobile: '',
			imageWidth: '30%',
			imageWidthTablet: '',
			imageWidthMobile: '',
			imageBorderType: 'none',
			imageBorderWidth: '1px',
			imageBorderColor: '#000000',
			imageBorderRadius: '0px',
			imageBorderRadiusTablet: '',
			imageBorderRadiusMobile: '',
			imageNormalFilter: imageBoxFilterDefaults(),
			imageHoverFilter: imageBoxFilterDefaults(),
			imageNormalOpacity: 1,
			imageHoverOpacity: 1,
			imageHoverTransition: 0.3,
			titleColor: '',
			titleFontFamily: 'inherit',
			titleFontSize: '29px',
			titleFontSizeTablet: '',
			titleFontSizeMobile: '',
			titleFontWeight: '400',
			titleLineHeight: '1.2em',
			titleLineHeightTablet: '',
			titleLineHeightMobile: '',
			titleLetterSpacing: '0px',
			titleLetterSpacingTablet: '',
			titleLetterSpacingMobile: '',
			titleWordSpacing: '0px',
			titleWordSpacingTablet: '',
			titleWordSpacingMobile: '',
			titleTextTransform: 'none',
			titleFontStyle: 'normal',
			titleTextDecoration: 'none',
			titleTextStrokeWidth: '0px',
			titleTextStrokeWidthTablet: '',
			titleTextStrokeWidthMobile: '',
			titleTextStrokeColor: '#000000',
			titleTextShadow: 'none',
			descriptionColor: '',
			descriptionFontFamily: 'inherit',
			descriptionFontSize: '16px',
			descriptionFontSizeTablet: '',
			descriptionFontSizeMobile: '',
			descriptionFontWeight: '400',
			descriptionLineHeight: '1.5em',
			descriptionLineHeightTablet: '',
			descriptionLineHeightMobile: '',
			descriptionLetterSpacing: '0px',
			descriptionLetterSpacingTablet: '',
			descriptionLetterSpacingMobile: '',
			descriptionWordSpacing: '0px',
			descriptionWordSpacingTablet: '',
			descriptionWordSpacingMobile: '',
			descriptionTextTransform: 'none',
			descriptionFontStyle: 'normal',
			descriptionTextDecoration: 'none',
			descriptionTextShadow: 'none',
		};
	}

const imageBoxResolutionOptions = Object.freeze(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'custom']);

const imageBoxTitleTagOptions = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);

const imageBoxPositionOptions = Object.freeze(['top', 'left', 'right']);

const imageBoxAlignmentOptions = Object.freeze(['left', 'center', 'right', 'justify']);

const imageBoxBorderTypeOptions = Object.freeze(['none', 'solid', 'double', 'dotted', 'dashed', 'groove']);

function normalizeImageBoxFilter(value) {
		const source = value && typeof value === 'object' && !Array.isArray(value) ? value : {};
		return {
			blur: clamp(Number(source.blur) || 0, 0, 100),
			brightness: clamp(Number(source.brightness == null ? 100 : source.brightness) || 0, 0, 200),
			contrast: clamp(Number(source.contrast == null ? 100 : source.contrast) || 0, 0, 200),
			saturation: clamp(Number(source.saturation == null ? 100 : source.saturation) || 0, 0, 200),
			hue: clamp(Number(source.hue) || 0, 0, 360),
		};
	}

function normalizeImageBoxSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const hadTitleFontSizeMode = settings.titleFontSizeMode !== undefined;
		const legacyTitleFontSize = String(settings.titleFontSize ?? '').trim();
		const defaults = imageBoxWidgetDefaults();
		Object.keys(defaults).forEach((key) => {
			if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]);
		});
		settings.imageResolution = imageBoxResolutionOptions.includes(settings.imageResolution) ? settings.imageResolution : 'full';
		settings.customImageWidth = clamp(Math.round(Number(settings.customImageWidth) || 150), 1, 4096);
		settings.customImageHeight = clamp(Math.round(Number(settings.customImageHeight) || 150), 1, 4096);
		settings.titleTag = imageBoxTitleTagOptions.includes(settings.titleTag) ? settings.titleTag : 'h3';
		settings.titleFontSizeMode = hadTitleFontSizeMode
			? (['auto', 'custom'].includes(settings.titleFontSizeMode) ? settings.titleFontSizeMode : 'auto')
			: (legacyTitleFontSize && legacyTitleFontSize !== '29px' ? 'custom' : 'auto');
		settings.imagePosition = imageBoxPositionOptions.includes(settings.imagePosition) ? settings.imagePosition : 'top';
		settings.alignment = imageBoxAlignmentOptions.includes(settings.alignment) ? settings.alignment : 'center';
		['imagePositionTablet', 'imagePositionMobile'].forEach((key) => {
			settings[key] = settings[key] === '' || imageBoxPositionOptions.includes(settings[key]) ? settings[key] : '';
		});
		['alignmentTablet', 'alignmentMobile'].forEach((key) => {
			settings[key] = settings[key] === '' || imageBoxAlignmentOptions.includes(settings[key]) ? settings[key] : '';
		});
		settings.imageBorderType = imageBoxBorderTypeOptions.includes(settings.imageBorderType) ? settings.imageBorderType : 'none';
		settings.imageNormalFilter = normalizeImageBoxFilter(settings.imageNormalFilter);
		settings.imageHoverFilter = normalizeImageBoxFilter(settings.imageHoverFilter);
		settings.imageNormalOpacity = clamp(Number(settings.imageNormalOpacity), 0, 1);
		settings.imageHoverOpacity = clamp(Number(settings.imageHoverOpacity), 0, 1);
		settings.imageHoverTransition = clamp(Number(settings.imageHoverTransition) || 0.3, 0, 10);
		settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
		settings.linkNofollow = !!settings.linkNofollow;
		settings.linkCustomAttributes = normalizeAttributes(settings.linkCustomAttributes);
		settings.dynamicBindings = settings.dynamicBindings && typeof settings.dynamicBindings === 'object' && !Array.isArray(settings.dynamicBindings)
			? { ...settings.dynamicBindings }
			: {};
		['imageUrl', 'imageAlt', 'title', 'description', 'linkUrl'].forEach((key) => {
			settings[key] = String(settings[key] == null ? '' : settings[key]);
		});
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

function clamp(v, min, max) {
		return Math.min(max, Math.max(min, v));
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
			defaults: imageBoxWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...imageBoxWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeImageBoxSettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "image_box",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
