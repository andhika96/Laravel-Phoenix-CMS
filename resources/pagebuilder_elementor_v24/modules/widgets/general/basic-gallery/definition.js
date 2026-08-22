(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function uid(p)       { return p + '_' + Math.random().toString(36).slice(2, 9); }

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

const imageCarouselResolutionOptions = Object.freeze(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'custom']);

const imageCarouselBorderOptions = Object.freeze(['default', 'none', 'solid', 'double', 'dotted', 'dashed', 'groove']);

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

function basicGalleryWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			images: [], imageResolution: 'thumbnail', customImageWidth: 150, customImageHeight: 150,
			columns: '4', columnsTablet: '2', columnsMobile: '1',
			captionType: 'caption', linkType: 'media', lightbox: 'default', orderBy: 'default',
			gapMode: 'default', gap: '10px', gapTablet: '', gapMobile: '',
			imageBorderType: 'default', imageBorderWidthTop: '0px', imageBorderWidthRight: '0px', imageBorderWidthBottom: '0px', imageBorderWidthLeft: '0px', imageBorderColor: '',
			imageBorderRadiusTop: '0px', imageBorderRadiusRight: '0px', imageBorderRadiusBottom: '0px', imageBorderRadiusLeft: '0px',
			imageBorderRadiusTopTablet: '', imageBorderRadiusRightTablet: '', imageBorderRadiusBottomTablet: '', imageBorderRadiusLeftTablet: '',
			imageBorderRadiusTopMobile: '', imageBorderRadiusRightMobile: '', imageBorderRadiusBottomMobile: '', imageBorderRadiusLeftMobile: '',
			captionAlignment: 'center', captionAlignmentTablet: '', captionAlignmentMobile: '', captionColor: '',
			captionFontFamily: 'inherit', captionFontSize: '16px', captionFontSizeTablet: '', captionFontSizeMobile: '', captionFontWeight: '400',
			captionLineHeight: '1.5em', captionLineHeightTablet: '', captionLineHeightMobile: '', captionLetterSpacing: '0px', captionLetterSpacingTablet: '', captionLetterSpacingMobile: '',
			captionWordSpacing: '0px', captionWordSpacingTablet: '', captionWordSpacingMobile: '', captionTextTransform: 'none', captionFontStyle: 'normal', captionTextDecoration: 'none',
			captionTextShadow: 'none', captionSpacing: '8px', captionSpacingTablet: '', captionSpacingMobile: '',
		};
	}

function normalizeBasicGalleryColumnCount(value, fallback) {
		const number = Number(String(value ?? '').trim());
		return Number.isInteger(number) && number >= 1 && number <= 10 ? String(number) : fallback;
	}

function normalizeBasicGallerySettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = basicGalleryWidgetDefaults();
		Object.keys(defaults).forEach((key) => { if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]); });
		const seenIds = new Set();
		settings.images = (Array.isArray(settings.images) ? settings.images : []).map(normalizeImageCarouselImage).filter((item) => {
			if (!item.url || seenIds.has(item.id)) return false;
			seenIds.add(item.id); return true;
		});
		settings.imageResolution = imageCarouselResolutionOptions.includes(settings.imageResolution) ? settings.imageResolution : 'thumbnail';
		settings.customImageWidth = clamp(Math.round(Number(settings.customImageWidth) || 150), 1, 4096);
		settings.customImageHeight = clamp(Math.round(Number(settings.customImageHeight) || 150), 1, 4096);
		settings.columns = normalizeBasicGalleryColumnCount(settings.columns, '4');
		settings.columnsTablet = normalizeBasicGalleryColumnCount(settings.columnsTablet, '2');
		settings.columnsMobile = normalizeBasicGalleryColumnCount(settings.columnsMobile, '1');
		settings.captionType = ['none', 'caption'].includes(settings.captionType) ? settings.captionType : 'caption';
		settings.linkType = ['none', 'media', 'attachment'].includes(settings.linkType) ? settings.linkType : 'media';
		settings.lightbox = ['default', 'yes', 'no'].includes(settings.lightbox) ? settings.lightbox : 'default';
		settings.orderBy = settings.orderBy === 'random' ? 'random' : 'default';
		settings.gapMode = ['default', 'no_gap', 'narrow', 'extended', 'wide', 'custom'].includes(settings.gapMode) ? settings.gapMode : 'default';
		settings.imageBorderType = imageCarouselBorderOptions.includes(settings.imageBorderType) ? settings.imageBorderType : 'default';
		settings.captionAlignment = ['left', 'center', 'right', 'justify'].includes(settings.captionAlignment) ? settings.captionAlignment : 'center';
		['captionAlignmentTablet', 'captionAlignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center', 'right', 'justify'].includes(settings[key]) ? settings[key] : ''; });
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

function clamp(v, min, max) {
		return Math.min(max, Math.max(min, v));
	}

function cloneSettingValue(value) {
		if (Array.isArray(value) || (value && typeof value === 'object')) return jclone(value);
		return value;
	}

	const implementation = {
			defaults: basicGalleryWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...basicGalleryWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeBasicGallerySettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "basic_gallery",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
