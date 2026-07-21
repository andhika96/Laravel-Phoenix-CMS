<template>
	<img :src="src" :alt="alt" :class="customClass" :style="imageStyle">
</template>

<script>
export default {
	name: 'BasicImage',
	props: {
		item: {
			type: Object,
			required: true,
		},
		responsiveDevice: {
			type: String,
			default: 'desktop',
		},
	},
	computed: {
		customClass() {
			const value = String(this.item.settings?.cssClass ?? '').trim();
			if (!value) return '';
			return value
				.split(/\s+/)
				.map((token) => token.replace(/^\.+/, '').trim())
				.filter(Boolean)
				.join(' ');
		},
		src() {
			return this.item.settings?.src || 'https://placehold.co/640x360';
		},
		alt() {
			return this.item.settings?.alt || 'Image';
		},
		imageStyle() {
			return {
				width: this.responsiveValue('width', '100%'),
				height: this.responsiveValue('height', 'auto'),
				display: 'block',
			};
		},
	},
	methods: {
		responsiveValue(base, fallback = '') {
			const settings = this.item.settings || {};
			const device = String(this.responsiveDevice || 'desktop').toLowerCase();
			const key = device === 'tablet' ? base + 'Tablet' : (device === 'mobile' ? base + 'Mobile' : base);
			const value = settings[key];
			if (device !== 'desktop' && (value === '' || value === null || value === undefined)) {
				const desktopValue = settings[base];
				return (desktopValue === '' || desktopValue === null || desktopValue === undefined) ? fallback : desktopValue;
			}
			return (value === '' || value === null || value === undefined) ? fallback : value;
		},
	},
};
</script>
