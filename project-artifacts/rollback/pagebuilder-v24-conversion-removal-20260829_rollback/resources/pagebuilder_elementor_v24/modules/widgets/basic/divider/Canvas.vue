<template>
	<div :class="['el-widget-divider', customClass]" :style="wrapStyle" :data-pb-import-node="importNodeKey">
		<hr :style="lineStyle" :data-pb-import-node="importNodeKey">
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
		importNodeKey() {
			const value = String(this.item.settings?.importNodeKey || '').trim();
			return /^import-node-[A-Za-z0-9_-]+$/.test(value) ? value : null;
		},
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
			const rawThickness = String(this.responsiveValue('thickness', '2px')).trim();
			const parsedThickness = Number.parseFloat(rawThickness);
			const thickness = Number.isFinite(parsedThickness) ? parsedThickness : 2;
			const legacyUnit = rawThickness.match(/[a-z%]+$/i)?.[0]?.toLowerCase();
			const thicknessUnit = ['px', 'em', 'rem'].includes(legacyUnit)
				? legacyUnit
				: (['px', 'em', 'rem'].includes(this.item.settings?.thicknessUnit) ? this.item.settings.thicknessUnit : 'px');
			return {
				margin: 0,
				border: 'none',
				borderTop: thickness + thicknessUnit + ' ' + styleType + ' ' + (this.item.settings?.color || '#d0d7e6'),
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
