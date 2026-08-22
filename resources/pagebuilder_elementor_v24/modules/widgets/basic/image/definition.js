(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

function imageBoxFilterDefaults() {
		return { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 };
	}

function basicImageWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			src: 'https://placehold.co/640x360', imageSource: 'ckfinder', alt: 'Image',
			imageResolution: 'large', customImageWidth: '', customImageHeight: '', attachmentCaption: '',
			captionType: 'none', customCaption: '',
			linkType: 'none', customLinkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [], lightbox: 'default',
			dynamicBindings: {},
			alignment: 'center', alignmentTablet: '', alignmentMobile: '',
			width: '100%', widthTablet: '', widthMobile: '', maxWidth: '100%', maxWidthTablet: '', maxWidthMobile: '',
			height: 'auto', heightTablet: '', heightMobile: '', objectFit: 'default', objectFitTablet: '', objectFitMobile: '',
			objectPosition: 'center center', objectPositionTablet: '', objectPositionMobile: '',
			imageNormalFilter: imageBoxFilterDefaults(), imageHoverFilter: imageBoxFilterDefaults(), imageNormalOpacity: 1, imageHoverOpacity: 1,
			imageHoverTransition: 0.3, imageHoverAnimation: 'none', imageBorderType: 'default',
			imageBorderWidth: '1px', imageBorderWidthTablet: '', imageBorderWidthMobile: '', imageBorderColor: '#000000',
			imageBorderRadius: '0px', imageBorderRadiusTablet: '', imageBorderRadiusMobile: '',
			imageBoxShadowEnabled: false, imageBoxShadowColor: 'rgba(0,0,0,.25)', imageBoxShadowX: '0px', imageBoxShadowY: '0px', imageBoxShadowBlur: '10px', imageBoxShadowSpread: '0px',
			captionAlignment: 'center', captionAlignmentTablet: '', captionAlignmentMobile: '', captionColor: '', captionBackgroundColor: '',
			captionFontFamily: 'inherit', captionFontSize: '16px', captionFontSizeTablet: '', captionFontSizeMobile: '', captionFontWeight: '400',
			captionLineHeight: '1.5em', captionLineHeightTablet: '', captionLineHeightMobile: '', captionLetterSpacing: '0px', captionLetterSpacingTablet: '', captionLetterSpacingMobile: '',
			captionWordSpacing: '0px', captionWordSpacingTablet: '', captionWordSpacingMobile: '', captionTextTransform: 'none', captionFontStyle: 'normal', captionTextDecoration: 'none',
			captionTextShadow: 'none', captionSpacing: '8px', captionSpacingTablet: '', captionSpacingMobile: '',
		};
	}

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

const basicImageResolutionOptions = Object.freeze(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'custom']);

const basicImageHoverAnimations = Object.freeze(['none', 'grow', 'shrink', 'pulse', 'pulse-grow', 'pulse-shrink', 'push', 'pop', 'bounce-in', 'bounce-out', 'rotate', 'grow-rotate', 'float', 'sink', 'bob', 'hang', 'skew', 'skew-forward', 'skew-backward', 'wobble-vertical', 'wobble-horizontal', 'wobble-to-bottom-right', 'wobble-to-top-right', 'wobble-top', 'wobble-bottom', 'wobble-skew', 'buzz', 'buzz-out']);

function normalizeBasicImageSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = basicImageWidgetDefaults();
		Object.keys(defaults).forEach((key) => {
			if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]);
		});
		settings.imageSource = settings.imageSource === 'url' ? 'url' : 'ckfinder';
		settings.imageResolution = basicImageResolutionOptions.includes(settings.imageResolution) ? settings.imageResolution : 'large';
		['customImageWidth', 'customImageHeight'].forEach((key) => {
			const raw = String(settings[key] ?? '').trim();
			settings[key] = raw === '' ? '' : clamp(Math.round(Number(raw) || 1), 1, 4096);
		});
		settings.captionType = ['none', 'attachment', 'custom'].includes(settings.captionType) ? settings.captionType : 'none';
		settings.linkType = ['none', 'media', 'custom'].includes(settings.linkType) ? settings.linkType : 'none';
		settings.lightbox = ['default', 'yes', 'no'].includes(settings.lightbox) ? settings.lightbox : 'default';
		settings.alignment = ['left', 'center', 'right'].includes(settings.alignment) ? settings.alignment : 'center';
		['alignmentTablet', 'alignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center', 'right'].includes(settings[key]) ? settings[key] : ''; });
		settings.objectFit = ['default', 'fill', 'cover', 'contain', 'scale-down'].includes(settings.objectFit) ? settings.objectFit : 'default';
		['objectFitTablet', 'objectFitMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['default', 'fill', 'cover', 'contain', 'scale-down'].includes(settings[key]) ? settings[key] : ''; });
		const positions = ['center center', 'center left', 'center right', 'top center', 'top left', 'top right', 'bottom center', 'bottom left', 'bottom right'];
		settings.objectPosition = positions.includes(settings.objectPosition) ? settings.objectPosition : 'center center';
		['objectPositionTablet', 'objectPositionMobile'].forEach((key) => { settings[key] = settings[key] === '' || positions.includes(settings[key]) ? settings[key] : ''; });
		settings.imageNormalFilter = normalizeImageBoxFilter(settings.imageNormalFilter);
		settings.imageHoverFilter = normalizeImageBoxFilter(settings.imageHoverFilter);
		settings.imageNormalOpacity = clamp(Number(settings.imageNormalOpacity), 0, 1);
		settings.imageHoverOpacity = clamp(Number(settings.imageHoverOpacity), 0, 1);
		settings.imageHoverTransition = clamp(Number(settings.imageHoverTransition) || 0.3, 0, 10);
		settings.imageHoverAnimation = basicImageHoverAnimations.includes(settings.imageHoverAnimation) ? settings.imageHoverAnimation : 'none';
		settings.imageBorderType = ['default', 'none', 'solid', 'double', 'dotted', 'dashed', 'groove'].includes(settings.imageBorderType) ? settings.imageBorderType : 'default';
		settings.imageBoxShadowEnabled = !!settings.imageBoxShadowEnabled;
		settings.captionAlignment = ['left', 'center', 'right', 'justify'].includes(settings.captionAlignment) ? settings.captionAlignment : 'center';
		['captionAlignmentTablet', 'captionAlignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center', 'right', 'justify'].includes(settings[key]) ? settings[key] : ''; });
		settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
		settings.linkNofollow = !!settings.linkNofollow;
		settings.linkCustomAttributes = normalizeAttributes(settings.linkCustomAttributes);
		settings.dynamicBindings = settings.dynamicBindings && typeof settings.dynamicBindings === 'object' && !Array.isArray(settings.dynamicBindings) ? { ...settings.dynamicBindings } : {};
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
			defaults: basicImageWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...basicImageWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeBasicImageSettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "image",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
