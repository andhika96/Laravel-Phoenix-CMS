<template>
	<div :class="customClass" :style="wrapStyle">
		<hr :style="lineStyle">
	</div>
</template>

<script>
export default {
	name: 'BasicDivider',
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
		wrapStyle() {
			return {
				textAlign: this.item.settings?.align || 'center',
			};
		},
		lineStyle() {
			const styleType = this.item.settings?.style || 'solid';
			return {
				margin: 0,
				border: 'none',
				borderTop: (this.item.settings?.thickness || 2) + 'px ' + styleType + ' ' + (this.item.settings?.color || '#d0d7e6'),
				width: this.responsiveValue('width', '100%'),
				display: 'inline-block',
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
