(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function uid(p)       { return p + '_' + Math.random().toString(36).slice(2, 9); }

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

function imageCarouselWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			carouselName: 'Image Carousel', images: [], imageResolution: 'thumbnail', customImageWidth: 150, customImageHeight: 150,
			slidesToShow: 'default', slidesToShowTablet: '', slidesToShowMobile: '', slidesToScroll: 'default', slidesToScrollTablet: '', slidesToScrollMobile: '',
			imageStretch: false, navigation: 'arrows_dots', previousArrowIcon: 'fas fa-chevron-left', previousArrowIconSource: 'library', previousArrowIconSvg: '', nextArrowIcon: 'fas fa-chevron-right', nextArrowIconSource: 'library', nextArrowIconSvg: '',
			linkType: 'none', customLinkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [], lightbox: 'default', captionType: 'none',
			lazyload: false, autoplay: true, pauseOnHover: true, pauseOnInteraction: true, autoplaySpeed: 5000, infiniteLoop: true, animationSpeed: 500, direction: 'left',
			arrowPosition: 'inside', arrowPositionTablet: '', arrowPositionMobile: '', arrowEdgeOffset: '8px', arrowEdgeOffsetTablet: '', arrowEdgeOffsetMobile: '',
			arrowButtonSize: '28px', arrowButtonSizeTablet: '', arrowButtonSizeMobile: '', arrowIconSize: '16px', arrowIconSizeTablet: '', arrowIconSizeMobile: '', arrowSize: '16px', arrowSizeTablet: '', arrowSizeMobile: '',
			arrowColor: '', arrowBackground: 'rgba(255,255,255,.88)', arrowHoverColor: '', arrowHoverBackground: 'rgba(255,255,255,.88)',
			arrowRadiusTop: '50%', arrowRadiusRight: '50%', arrowRadiusBottom: '50%', arrowRadiusLeft: '50%',
			paginationPosition: 'outside', dotSpacing: '8px', dotSpacingTablet: '', dotSpacingMobile: '', dotSize: '8px', dotSizeTablet: '', dotSizeMobile: '', dotColor: '#c4c7cf', dotActiveColor: '#69727d',
			imageVerticalAlign: 'center', imageVerticalAlignTablet: '', imageVerticalAlignMobile: '', imageSpacingMode: 'default', imageSpacing: '20px', imageSpacingTablet: '', imageSpacingMobile: '',
			imageBorderType: 'default', imageBorderWidthTop: '0px', imageBorderWidthRight: '0px', imageBorderWidthBottom: '0px', imageBorderWidthLeft: '0px', imageBorderColor: '',
			imageBorderRadiusTop: '0px', imageBorderRadiusRight: '0px', imageBorderRadiusBottom: '0px', imageBorderRadiusLeft: '0px',
			captionAlignment: 'center', captionAlignmentTablet: '', captionAlignmentMobile: '', captionColor: '',
			captionFontFamily: 'inherit', captionFontSize: '16px', captionFontSizeTablet: '', captionFontSizeMobile: '', captionFontWeight: '400',
			captionLineHeight: '1.5em', captionLineHeightTablet: '', captionLineHeightMobile: '', captionLetterSpacing: '0px', captionLetterSpacingTablet: '', captionLetterSpacingMobile: '',
			captionWordSpacing: '0px', captionWordSpacingTablet: '', captionWordSpacingMobile: '', captionTextTransform: 'none', captionFontStyle: 'normal', captionTextDecoration: 'none',
			captionTextShadow: 'none', captionSpacing: '8px', captionSpacingTablet: '', captionSpacingMobile: '',
		};
	}

const imageCarouselResolutionOptions = Object.freeze(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'custom']);

const imageCarouselNavigationOptions = Object.freeze(['arrows_dots', 'arrows', 'dots', 'none']);

const imageCarouselLinkOptions = Object.freeze(['none', 'media', 'custom']);

const imageCarouselCaptionOptions = Object.freeze(['none', 'title', 'caption', 'description']);

const imageCarouselBorderOptions = Object.freeze(['default', 'none', 'solid', 'double', 'dotted', 'dashed', 'groove']);

function normalizeImageCarouselSlideCount(value, fallback = 'default') {
		const raw = String(value ?? '').trim().toLowerCase();
		if (raw === '' && fallback === '') return '';
		if (raw === 'default') return 'default';
		const number = Number(raw);
		return Number.isInteger(number) && number >= 1 && number <= 10 ? String(number) : fallback;
	}

function normalizeImageCarouselImage(item, index) {
		const source = item && typeof item === 'object' && !Array.isArray(item) ? item : {};
		const url = String(source.url || '').trim();
		return {
			id: String(source.id || uid('carousel-image-' + index)).replace(/[^A-Za-z0-9_-]/g, '') || uid('carousel-image'),
			url,
			alt: String(source.alt || ''), title: String(source.title || ''), caption: String(source.caption || ''), description: String(source.description || ''),
			attachmentUrl: String(source.attachmentUrl || ''),
		};
	}

function normalizeImageCarouselSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const legacyArrow = (value, addition, fallback) => {
			const match = String(value || '').trim().match(/^(\d+(?:\.\d+)?)px$/i);
			return match ? `${Number(match[1]) + addition}px` : fallback;
		};
		const legacyArrowSizes = { desktop: settings.arrowSize, tablet: settings.arrowSizeTablet, mobile: settings.arrowSizeMobile };
		const missingArrowContract = { button: settings.arrowButtonSize === undefined, icon: settings.arrowIconSize === undefined, edge: settings.arrowEdgeOffset === undefined };
		const defaults = imageCarouselWidgetDefaults();
		Object.keys(defaults).forEach((key) => { if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]); });
		if (missingArrowContract.button) settings.arrowButtonSize = legacyArrow(legacyArrowSizes.desktop, 12, '28px');
		if (missingArrowContract.icon) settings.arrowIconSize = String(legacyArrowSizes.desktop || '16px');
		if (missingArrowContract.edge) settings.arrowEdgeOffset = settings.arrowPosition === 'outside' ? '0px' : '8px';
		[['Tablet', 'tablet'], ['Mobile', 'mobile']].forEach(([suffix, device]) => {
			if (settings['arrowButtonSize' + suffix] === '' && legacyArrowSizes[device]) settings['arrowButtonSize' + suffix] = legacyArrow(legacyArrowSizes[device], 12, '');
			if (settings['arrowIconSize' + suffix] === '' && legacyArrowSizes[device]) settings['arrowIconSize' + suffix] = String(legacyArrowSizes[device]);
		});
		const seenIds = new Set();
		settings.images = (Array.isArray(settings.images) ? settings.images : []).map(normalizeImageCarouselImage).filter((item) => {
			if (!item.url || seenIds.has(item.id)) return false;
			seenIds.add(item.id); return true;
		});
		settings.imageResolution = imageCarouselResolutionOptions.includes(settings.imageResolution) ? settings.imageResolution : 'thumbnail';
		settings.customImageWidth = clamp(Math.round(Number(settings.customImageWidth) || 150), 1, 4096);
		settings.customImageHeight = clamp(Math.round(Number(settings.customImageHeight) || 150), 1, 4096);
		settings.slidesToShow = normalizeImageCarouselSlideCount(settings.slidesToShow, 'default');
		settings.slidesToScroll = normalizeImageCarouselSlideCount(settings.slidesToScroll, 'default');
		['slidesToShowTablet', 'slidesToShowMobile', 'slidesToScrollTablet', 'slidesToScrollMobile'].forEach((key) => { settings[key] = normalizeImageCarouselSlideCount(settings[key], ''); });
		settings.navigation = imageCarouselNavigationOptions.includes(settings.navigation) ? settings.navigation : 'arrows_dots';
		settings.linkType = imageCarouselLinkOptions.includes(settings.linkType) ? settings.linkType : 'none';
		settings.captionType = imageCarouselCaptionOptions.includes(settings.captionType) ? settings.captionType : 'none';
		settings.lightbox = ['default', 'yes', 'no'].includes(settings.lightbox) ? settings.lightbox : 'default';
		settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
		settings.linkNofollow = !!settings.linkNofollow;
		settings.linkCustomAttributes = normalizeAttributes(settings.linkCustomAttributes);
		settings.direction = settings.direction === 'right' ? 'right' : 'left';
		settings.arrowPosition = settings.arrowPosition === 'outside' ? 'outside' : 'inside';
		['arrowPositionTablet', 'arrowPositionMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['inside', 'outside'].includes(settings[key]) ? settings[key] : ''; });
		settings.paginationPosition = settings.paginationPosition === 'inside' ? 'inside' : 'outside';
		settings.imageSpacingMode = settings.imageSpacingMode === 'custom' ? 'custom' : 'default';
		settings.imageBorderType = imageCarouselBorderOptions.includes(settings.imageBorderType) ? settings.imageBorderType : 'default';
		settings.imageVerticalAlign = ['start', 'center', 'end'].includes(settings.imageVerticalAlign) ? settings.imageVerticalAlign : 'center';
		settings.captionAlignment = ['left', 'center', 'right', 'justify'].includes(settings.captionAlignment) ? settings.captionAlignment : 'center';
		['imageVerticalAlignTablet', 'imageVerticalAlignMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['start', 'center', 'end'].includes(settings[key]) ? settings[key] : ''; });
		['captionAlignmentTablet', 'captionAlignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center', 'right', 'justify'].includes(settings[key]) ? settings[key] : ''; });
		['imageStretch', 'lazyload', 'autoplay', 'pauseOnHover', 'pauseOnInteraction', 'infiniteLoop'].forEach((key) => { settings[key] = !!settings[key]; });
		settings.autoplaySpeed = clamp(Math.round(Number(settings.autoplaySpeed) || 5000), 100, 60000);
		settings.animationSpeed = clamp(Math.round(Number(settings.animationSpeed) || 500), 0, 10000);
		settings.carouselName = String(settings.carouselName || 'Image Carousel').trim() || 'Image Carousel';
		settings.customLinkUrl = String(settings.customLinkUrl || '').trim();
		['previousArrowIcon', 'nextArrowIcon'].forEach((key) => {
			settings[key] = String(settings[key] || (key === 'previousArrowIcon' ? 'fas fa-chevron-left' : 'fas fa-chevron-right')).trim();
			settings[key + 'Source'] = settings[key + 'Source'] === 'svg' && String(settings[key + 'Svg'] || '').trim() ? 'svg' : 'library';
			settings[key + 'Svg'] = settings[key + 'Source'] === 'svg' ? String(settings[key + 'Svg'] || '').trim() : '';
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
			defaults: imageCarouselWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...imageCarouselWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeImageCarouselSettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "image_carousel",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
