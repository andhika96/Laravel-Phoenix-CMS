(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

function featureShowcaseMetricDefaults(index = 0) {
		const presets = [
			{ label: 'Wheelbase', value: '2680', unit: 'MM' },
			{ label: 'Max Horsepower', value: '114', unit: 'PS' },
			{ label: 'Max Torque', value: '150', unit: 'NM' },
		];
		return { ...(presets[index] || presets[0]) };
	}

function featureShowcaseImageDefaults() {
		return { imageSource: 'ckfinder', url: '', alt: '' };
	}

function featureShowcaseWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			template: 'specifications_metrics',
			title: '', subtitle: '', description: '',
			metrics: [0, 1, 2].map((index) => featureShowcaseMetricDefaults(index)),
			images: [featureShowcaseImageDefaults(), featureShowcaseImageDefaults(), featureShowcaseImageDefaults()],
			textPosition: 'left', textPositionTablet: '', textPositionMobile: '',
			imagePosition: 'right', imagePositionTablet: '', imagePositionMobile: '',
			textAlign: 'left', textAlignTablet: '', textAlignMobile: '',
			verticalAlign: 'center', verticalAlignTablet: '', verticalAlignMobile: '',
			horizontalAlign: 'center', horizontalAlignTablet: '', horizontalAlignMobile: '',
			metricColumns: '2', metricColumnsTablet: '', metricColumnsMobile: '',
			contentWidth: '42%', contentWidthTablet: '', contentWidthMobile: '',
			imageWidth: '58%', imageWidthTablet: '', imageWidthMobile: '',
			imageHeight: '420px', imageHeightTablet: '340px', imageHeightMobile: '220px',
			threeMetricPosition: 'left', threeMetricPositionTablet: '', threeMetricPositionMobile: '',
			featuredImagePosition: '1', featuredImagePositionTablet: '', featuredImagePositionMobile: '',
			tallImagePosition: 'left', tallImagePositionTablet: '', tallImagePositionMobile: '',
			mediaOrder: ['1', '2', '3'],
			containerWidth: '1140px', containerWidthTablet: '', containerWidthMobile: '', gap: '24px', gapTablet: '', gapMobile: '', imageRadius: '45px', imageRadiusTablet: '', imageRadiusMobile: '',
			backgroundColor: '#ffffff', performanceBackgroundColor: '#212529',
			headingGradientStart: '#0099d5', headingGradientEnd: '#2be581',
			titleColor: '', subtitleColor: '', descriptionColor: '', metricLabelColor: '', metricValueColor: '', metricUnitColor: '',
			titleFontFamily: 'inherit', titleFontSize: '48px', titleFontSizeTablet: '', titleFontSizeMobile: '', titleFontWeight: '700', titleLineHeight: '1.2em', titleLineHeightTablet: '', titleLineHeightMobile: '', titleLetterSpacing: '0px', titleLetterSpacingTablet: '', titleLetterSpacingMobile: '', titleWordSpacing: '0px', titleWordSpacingTablet: '', titleWordSpacingMobile: '', titleTextTransform: 'none', titleFontStyle: 'normal', titleTextDecoration: 'none', titleTextShadow: 'none',
			descriptionFontFamily: 'inherit', descriptionFontSize: '16px', descriptionFontSizeTablet: '', descriptionFontSizeMobile: '', descriptionFontWeight: '400', descriptionLineHeight: '1.5em', descriptionLineHeightTablet: '', descriptionLineHeightMobile: '', descriptionLetterSpacing: '0px', descriptionLetterSpacingTablet: '', descriptionLetterSpacingMobile: '', descriptionWordSpacing: '0px', descriptionWordSpacingTablet: '', descriptionWordSpacingMobile: '', descriptionTextTransform: 'none', descriptionFontStyle: 'normal', descriptionTextDecoration: 'none', descriptionTextShadow: 'none',
			subtitleFontFamily: 'inherit', subtitleFontSize: '20px', subtitleFontSizeTablet: '', subtitleFontSizeMobile: '', subtitleFontWeight: '400', subtitleLineHeight: '1.5em', subtitleLineHeightTablet: '', subtitleLineHeightMobile: '', subtitleLetterSpacing: '0px', subtitleLetterSpacingTablet: '', subtitleLetterSpacingMobile: '', subtitleWordSpacing: '0px', subtitleWordSpacingTablet: '', subtitleWordSpacingMobile: '', subtitleTextTransform: 'none', subtitleFontStyle: 'normal', subtitleTextDecoration: 'none', subtitleTextShadow: 'none',
			metricLabelFontFamily: 'inherit', metricLabelFontSize: '20px', metricLabelFontSizeTablet: '', metricLabelFontSizeMobile: '', metricLabelFontWeight: '400', metricLabelLineHeight: '1.3em', metricLabelLineHeightTablet: '', metricLabelLineHeightMobile: '', metricLabelLetterSpacing: '0px', metricLabelLetterSpacingTablet: '', metricLabelLetterSpacingMobile: '', metricLabelWordSpacing: '0px', metricLabelWordSpacingTablet: '', metricLabelWordSpacingMobile: '', metricLabelTextTransform: 'none', metricLabelFontStyle: 'normal', metricLabelTextDecoration: 'none', metricLabelTextShadow: 'none',
			metricValueFontFamily: 'inherit', metricValueFontSize: '74px', metricValueFontSizeTablet: '', metricValueFontSizeMobile: '', metricValueFontWeight: '700', metricValueLineHeight: '1em', metricValueLineHeightTablet: '', metricValueLineHeightMobile: '', metricValueLetterSpacing: '0px', metricValueLetterSpacingTablet: '', metricValueLetterSpacingMobile: '', metricValueWordSpacing: '0px', metricValueWordSpacingTablet: '', metricValueWordSpacingMobile: '', metricValueTextTransform: 'none', metricValueFontStyle: 'normal', metricValueTextDecoration: 'none', metricValueTextShadow: 'none',
			metricUnitFontFamily: 'inherit', metricUnitFontSize: '20px', metricUnitFontSizeTablet: '', metricUnitFontSizeMobile: '', metricUnitFontWeight: '400', metricUnitLineHeight: '1em', metricUnitLineHeightTablet: '', metricUnitLineHeightMobile: '', metricUnitLetterSpacing: '0px', metricUnitLetterSpacingTablet: '', metricUnitLetterSpacingMobile: '', metricUnitWordSpacing: '0px', metricUnitWordSpacingTablet: '', metricUnitWordSpacingMobile: '', metricUnitTextTransform: 'none', metricUnitFontStyle: 'normal', metricUnitTextDecoration: 'none', metricUnitTextShadow: 'none',
		};
	}

function normalizeFeatureShowcaseMediaUrl(value) {
		const url = String(value == null ? '' : value).trim();
		if (!url || /[\u0000-\u001f\u007f]/.test(url) || /^(?:javascript|vbscript|data):/i.test(url) || url.startsWith('//')) return '';
		return /^(?:https?:\/\/|\/)/i.test(url) ? url : '';
	}

function normalizeFeatureShowcaseMedia(item) {
		const source = item && typeof item === 'object' && !Array.isArray(item) ? item : {};
		return { imageSource: source.imageSource === 'url' ? 'url' : 'ckfinder', url: normalizeFeatureShowcaseMediaUrl(source.url), alt: String(source.alt == null ? '' : source.alt).trim() };
	}

function normalizeFeatureShowcaseLength(value, fallback) {
		const raw = String(value == null ? '' : value).trim();
		return /^(?:\d+(?:\.\d+)?)(?:px|%|rem|em|vw|vh|auto)$/i.test(raw) ? raw : fallback;
	}

function normalizeFeatureShowcaseSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = featureShowcaseWidgetDefaults();
		Object.keys(defaults).forEach((key) => { if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]); });
		const templates = ['specifications_metrics', 'specifications_hero', 'performance_collage', 'exterior_gallery', 'feature_image'];
		const positions = ['top', 'bottom', 'left', 'right'];
		const alignments = ['left', 'center', 'right'];
		const verticalAlignments = ['start', 'center', 'end'];
		settings.template = templates.includes(settings.template) ? settings.template : 'specifications_metrics';
		settings.title = String(settings.title == null ? '' : settings.title);
		settings.subtitle = String(settings.subtitle == null ? '' : settings.subtitle);
		settings.description = String(settings.description == null ? '' : settings.description);
		settings.metrics = (Array.isArray(settings.metrics) ? settings.metrics : []).slice(0, 4).map((metric) => {
			const source = metric && typeof metric === 'object' && !Array.isArray(metric) ? metric : {};
			return { label: String(source.label == null ? '' : source.label), value: String(source.value == null ? '' : source.value), unit: String(source.unit == null ? '' : source.unit) };
		});
		if (settings.metrics.length === 0) settings.metrics = [0, 1, 2].map((index) => featureShowcaseMetricDefaults(index));
		while (settings.metrics.length < 1) settings.metrics.push(featureShowcaseMetricDefaults(settings.metrics.length));
		settings.images = (Array.isArray(settings.images) ? settings.images : []).slice(0, 3).map(normalizeFeatureShowcaseMedia);
		while (settings.images.length < 3) settings.images.push(featureShowcaseImageDefaults());
		settings.textPosition = positions.includes(settings.textPosition) ? settings.textPosition : defaults.textPosition;
		['textPositionTablet', 'textPositionMobile'].forEach((responsiveKey) => { settings[responsiveKey] = settings[responsiveKey] === '' || positions.includes(settings[responsiveKey]) ? settings[responsiveKey] : ''; });
		settings.imagePosition = positions.includes(settings.imagePosition) ? settings.imagePosition : defaults.imagePosition;
		settings.imagePositionTablet = settings.imagePositionTablet === '' || positions.includes(settings.imagePositionTablet) ? settings.imagePositionTablet : '';
		const mobileImagePosition = settings.imagePositionMobile;
		settings.imagePositionMobile = mobileImagePosition === ''
			? ''
			: (mobileImagePosition === 'left' ? 'top' : (mobileImagePosition === 'right' ? 'bottom' : (['top', 'bottom'].includes(mobileImagePosition) ? mobileImagePosition : '')));
		settings.textAlign = alignments.includes(settings.textAlign) ? settings.textAlign : 'left';
		settings.verticalAlign = verticalAlignments.includes(settings.verticalAlign) ? settings.verticalAlign : 'center';
		settings.horizontalAlign = alignments.includes(settings.horizontalAlign) ? settings.horizontalAlign : 'center';
		['textAlign', 'verticalAlign', 'horizontalAlign'].forEach((key) => {
			const allowed = ['textAlign', 'horizontalAlign'].includes(key) ? alignments : verticalAlignments;
			[`${key}Tablet`, `${key}Mobile`].forEach((responsiveKey) => { settings[responsiveKey] = settings[responsiveKey] === '' || allowed.includes(settings[responsiveKey]) ? settings[responsiveKey] : ''; });
		});
		settings.metricColumns = ['1', '2'].includes(String(settings.metricColumns)) ? String(settings.metricColumns) : '2';
		['metricColumnsTablet', 'metricColumnsMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['1', '2'].includes(String(settings[key])) ? String(settings[key]) : ''; });
		settings.threeMetricPosition = ['left', 'center'].includes(settings.threeMetricPosition) ? settings.threeMetricPosition : 'left';
		['threeMetricPositionTablet', 'threeMetricPositionMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center'].includes(settings[key]) ? settings[key] : ''; });
		settings.featuredImagePosition = ['1', '2', '3'].includes(String(settings.featuredImagePosition)) ? String(settings.featuredImagePosition) : '1';
		settings.tallImagePosition = ['left', 'right'].includes(settings.tallImagePosition) ? settings.tallImagePosition : 'left';
		['featuredImagePositionTablet', 'featuredImagePositionMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['1', '2', '3'].includes(String(settings[key])) ? String(settings[key]) : ''; });
		['tallImagePositionTablet', 'tallImagePositionMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'right'].includes(settings[key]) ? settings[key] : ''; });
		const order = Array.isArray(settings.mediaOrder) ? settings.mediaOrder.map((value) => String(value)) : [];
		settings.mediaOrder = [...new Set(order.filter((value) => ['1', '2', '3'].includes(value)))];
		['1', '2', '3'].forEach((value) => { if (!settings.mediaOrder.includes(value)) settings.mediaOrder.push(value); });
		settings.containerWidth = normalizeFeatureShowcaseLength(settings.containerWidth, '1140px');
		settings.gap = normalizeFeatureShowcaseLength(settings.gap, '24px');
		settings.imageRadius = normalizeFeatureShowcaseLength(settings.imageRadius, '45px');
		settings.imageHeight = normalizeFeatureShowcaseLength(settings.imageHeight, defaults.imageHeight);
		['imageHeightTablet', 'imageHeightMobile'].forEach((responsiveKey) => { settings[responsiveKey] = settings[responsiveKey] === '' ? '' : normalizeFeatureShowcaseLength(settings[responsiveKey], defaults[responsiveKey]); });
		['contentWidth', 'imageWidth'].forEach((key) => {
			settings[key] = normalizeFeatureShowcaseLength(settings[key], defaults[key]);
			[`${key}Tablet`, `${key}Mobile`].forEach((responsiveKey) => { settings[responsiveKey] = settings[responsiveKey] === '' ? '' : normalizeFeatureShowcaseLength(settings[responsiveKey], ''); });
		});
		['backgroundColor', 'performanceBackgroundColor', 'headingGradientStart', 'headingGradientEnd', 'titleColor', 'subtitleColor', 'descriptionColor', 'metricLabelColor', 'metricValueColor', 'metricUnitColor'].forEach((key) => { settings[key] = String(settings[key] == null ? defaults[key] || '' : settings[key]).trim(); });
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

function responsiveSuffix(device) {
		if (device === 'tablet') return 'Tablet';
		if (device === 'mobile') return 'Mobile';
		return '';
	}

function responsiveKey(base, device) {
		return base + responsiveSuffix(device);
	}

function cloneSettingValue(value) {
		if (Array.isArray(value) || (value && typeof value === 'object')) return jclone(value);
		return value;
	}

	const implementation = {
			defaults: featureShowcaseWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...featureShowcaseWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeFeatureShowcaseSettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "feature_showcase",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
