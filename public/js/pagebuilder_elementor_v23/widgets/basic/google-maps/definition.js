(function (registry) {
	'use strict';

	if (!registry) {
		throw new Error('Page Builder Elementor widget registry is not loaded.');
	}

	const filterDefaults = Object.freeze({ blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 });
	const defaults = () => ({
		location: 'New York, NY',
		zoom: 14,
		height: '400px',
		heightTablet: '',
		heightMobile: '',
		mapNormalFilter: { ...filterDefaults },
		mapHoverFilter: { ...filterDefaults },
		transitionDuration: 0.3,
		cssClass: '',
	});

	function boundedNumber(value, min, max, fallback) {
		if (value === '' || value === null || value === undefined) return fallback;
		const number = Number(value);
		return Number.isFinite(number) ? Math.min(max, Math.max(min, number)) : fallback;
	}

	function normalizeZoom(value, fallback = 14) {
		const number = boundedNumber(value, 1, 20, fallback);
		return number === '' ? fallback : Math.round(number);
	}

	function normalizeHeight(value, fallback = '') {
		if (value === '' || value === null || value === undefined) return fallback;
		if (typeof value === 'number' && Number.isFinite(value)) return Math.max(0, Math.round(value)) + 'px';
		const raw = String(value).trim();
		if (/^\d+(?:\.\d+)?$/i.test(raw)) return raw + 'px';
		return /^(?:\d+(?:\.\d+)?)(?:px|%|em|rem|vh|vw)$/i.test(raw) ? raw : fallback;
	}

	function normalizeFilter(value) {
		const source = value && typeof value === 'object' && !Array.isArray(value) ? value : {};
		return {
			blur: Math.round(boundedNumber(source.blur, 0, 100, filterDefaults.blur)),
			brightness: Math.round(boundedNumber(source.brightness, 0, 200, filterDefaults.brightness)),
			contrast: Math.round(boundedNumber(source.contrast, 0, 200, filterDefaults.contrast)),
			saturation: Math.round(boundedNumber(source.saturation, 0, 200, filterDefaults.saturation)),
			hue: Math.round(boundedNumber(source.hue, 0, 360, filterDefaults.hue)),
		};
	}

	registry.register({
		type: 'google_maps',
		label: 'Google Maps',
		category: 'basic',
		icon: 'fas fa-map-marker-alt',
		toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/basic/google-maps/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/basic/google-maps/Settings.vue',
		defaults,
		normalize(node) {
			const normalized = node && typeof node === 'object' ? node : {};
			const previous = normalized.settings && typeof normalized.settings === 'object' ? normalized.settings : {};
			normalized.settings = { ...defaults(), ...previous };
			const settings = normalized.settings;
			settings.location = String(settings.location || '').trim();
			settings.zoom = normalizeZoom(settings.zoom);
			settings.height = normalizeHeight(settings.height, '400px');
			settings.heightTablet = normalizeHeight(settings.heightTablet);
			settings.heightMobile = normalizeHeight(settings.heightMobile);
			settings.mapNormalFilter = normalizeFilter(settings.mapNormalFilter);
			settings.mapHoverFilter = normalizeFilter(settings.mapHoverFilter);
			settings.transitionDuration = boundedNumber(settings.transitionDuration, 0, 10, 0.3);
			settings.cssClass = String(settings.cssClass || '').trim().replace(/[^a-zA-Z0-9_\-\s]/g, ' ');
			return normalized;
		},
	});
})(window.PageBuilderElementorV23Widgets);
