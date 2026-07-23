<template>
	<div :class="['el-widget-image', customClass]"><img :src="src" :alt="alt" :class="customClass" :style="imageStyle"></div>
</template>

<script>
export default {
	name: 'BasicImage',
	props: { item: { type: Object, required: true }, responsiveDevice: { type: String, default: 'desktop' } },
	computed: {
		customClass() {
			const value = String(this.item.settings?.cssClass ?? '').trim();
			if (!value) return '';
			return value.split(/\s+/).map((token) => token.replace(/^\.+/, '').trim()).filter(Boolean).join(' ');
		},
		src() { return String(this.item.settings?.src ?? ''); },
		alt() { return String(this.item.settings?.alt ?? ''); },
		imageStyle() { return { width: this.responsiveValue('width', '100%'), height: this.responsiveValue('height', 'auto'), display: 'block' }; },
	},
	methods: {
		responsiveValue(base, fallback = '') {
			const settings = this.item.settings || {};
			const device = String(this.responsiveDevice || 'desktop').toLowerCase();
			const key = device === 'tablet' ? base + 'Tablet' : (device === 'mobile' ? base + 'Mobile' : base);
			let value = settings[key];
			if (device === 'mobile' && (value === '' || value === null || value === undefined)) {
				const tabletValue = settings[base + 'Tablet'];
				if (tabletValue !== '' && tabletValue !== null && tabletValue !== undefined) return tabletValue;
			}
			if (device !== 'desktop' && (value === '' || value === null || value === undefined)) value = settings[base];
			return (value === '' || value === null || value === undefined) ? fallback : value;
		},
	},
};
</script>