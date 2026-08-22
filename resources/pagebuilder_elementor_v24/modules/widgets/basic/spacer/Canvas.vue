<template>
	<div :class="['el-widget-spacer', customClass]" :style="spacerStyle"></div>
</template>

<script>
export default {
	name: 'BasicSpacer',
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
		spacerStyle() {
			return {
				height: this.responsiveValue('height', '32px'),
			};
		},
	},
	methods: {
		responsiveValue(base, fallback = '') {
			const settings = this.item.settings || {};
			const device = String(this.responsiveDevice || 'desktop').toLowerCase();
			const key = device === 'tablet' ? base + 'Tablet' : (device === 'mobile' ? base + 'Mobile' : base);
			const value = settings[key];
			if (device === 'mobile' && (value === '' || value === null || value === undefined)) {
				const tabletValue = settings[base + 'Tablet'];
				if (tabletValue !== '' && tabletValue !== null && tabletValue !== undefined) return tabletValue;
			}
			if (device !== 'desktop' && (value === '' || value === null || value === undefined)) {
				const desktopValue = settings[base];
				return (desktopValue === '' || desktopValue === null || desktopValue === undefined) ? fallback : desktopValue;
			}
			return (value === '' || value === null || value === undefined) ? fallback : value;
		},
	},
};
</script>
