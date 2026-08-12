<template>
	<div class="el-widget-google-maps pb-google-maps" :class="customClass" :style="rootStyle" data-basic-google-maps>
		<div v-if="location" class="pb-google-maps__frame" :style="frameStyle">
			<iframe :src="mapUrl" title="Google Maps" loading="lazy" allowfullscreen="" :style="iframeStyle"></iframe>
		</div>
		<div v-else class="pb-google-maps__empty" data-google-maps-empty role="img" aria-label="Choose a map location"><i class="fas fa-map-marker-alt" aria-hidden="true"></i><span>Enter a location to display the map.</span></div>
	</div>
</template>

<script>
const FILTER_DEFAULTS = Object.freeze({ blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 });

export default {
	name: 'BasicGoogleMaps',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	computed: {
		settings() { return this.item.settings || {}; },
		location() { return String(this.settings.location || '').trim(); },
		zoom() { return this.clamp(this.settings.zoom, 1, 20, 14); },
		mapUrl() {
			if (!this.location) return '';
			return 'https://www.google.com/maps?q=' + encodeURIComponent(this.location) + '&z=' + this.zoom + '&output=embed';
		},
		height() { return this.cssSize(this.responsiveValue('height', '400px'), '400px'); },
		rootStyle() {
			return {
				'--pb-google-maps-hover-filter': this.filterCss(this.settings.mapHoverFilter),
				'--pb-google-maps-transition-duration': `${this.duration(this.settings.transitionDuration)}s`,
			};
		},
		frameStyle() {
			return {
				height: this.height,
				filter: this.filterCss(this.settings.mapNormalFilter),
			};
		},
		iframeStyle() { return { width: '100%', height: '100%', border: '0' }; },
		customClass() {
			return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').trim()).filter(Boolean).join(' ');
		},
	},
	methods: {
		responsiveValue(base, fallback = '') {
			const device = String(this.responsiveDevice || 'desktop').toLowerCase();
			const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]);
			for (const key of keys) {
				const value = this.settings[key];
				if (value !== '' && value !== null && value !== undefined) return value;
			}
			return fallback;
		},
		clamp(value, min, max, fallback) {
			const number = Number(value);
			return Number.isFinite(number) ? Math.min(max, Math.max(min, Math.round(number))) : fallback;
		},
		cssSize(value, fallback = '') {
			const raw = String(value ?? '').trim();
			if (/^\d+(?:\.\d+)?$/i.test(raw)) return raw + 'px';
			return /^(?:\d+(?:\.\d+)?)(?:px|%|em|rem|vh|vw)$/i.test(raw) ? raw : fallback;
		},
		filterCss(filters) {
			const source = filters && typeof filters === 'object' ? filters : FILTER_DEFAULTS;
			const value = (key, min, max, fallback) => {
				const raw = source[key];
				const number = raw === '' || raw === null || raw === undefined ? fallback : Number(raw);
				return Number.isFinite(number) ? Math.min(max, Math.max(min, number)) : fallback;
			};
			return `blur(${value('blur', 0, 100, 0)}px) brightness(${value('brightness', 0, 200, 100)}%) contrast(${value('contrast', 0, 200, 100)}%) saturate(${value('saturation', 0, 200, 100)}%) hue-rotate(${value('hue', 0, 360, 0)}deg)`;
		},
		duration(value) {
			const number = Number(value);
			return Number.isFinite(number) ? Math.min(10, Math.max(0, number)) : 0.3;
		},
	},
};
</script>

<style scoped>
.pb-google-maps { width: 100%; min-width: 0; }
.pb-google-maps__frame { overflow: hidden; transition: filter var(--pb-google-maps-transition-duration, .3s) ease; }
.pb-google-maps__frame:hover { filter: var(--pb-google-maps-hover-filter); }
.pb-google-maps__frame iframe { display: block; width: 100%; height: 100%; border: 0; }
.pb-google-maps__empty { min-height: 220px; display: grid; place-items: center; align-content: center; gap: 8px; padding: 24px; background: #eef1f4; color: #98a2b3; text-align: center; }
.pb-google-maps__empty i { font-size: 36px; }
.pb-google-maps__empty span { font-size: 12px; }
@media (prefers-reduced-motion: reduce) { .pb-google-maps__frame { transition: none; } }
</style>
