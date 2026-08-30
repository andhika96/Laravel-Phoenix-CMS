<template>
	<div :class="wrapperClass" :style="wrapperStyle" :data-pb-import-node="importNodeKey">
		<component :is="linkTag" v-bind="linkAttrs" class="el-widget-icon-link">
			<span v-if="usesShape" class="el-widget-icon-box">
				<i :class="iconClass" :style="glyphStyle" :data-pb-import-node="importNodeKey" aria-hidden="true"></i>
			</span>
			<i v-else :class="iconClass" :style="glyphStyle" :data-pb-import-node="importNodeKey" aria-hidden="true"></i>
		</component>
	</div>
</template>

<script>
const ALLOWED_ALIGNMENTS = ['left', 'center', 'right'];

export default {
	name: 'BasicIcon',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	computed: {
		settings() { return this.item.settings || {}; },
		importNodeKey() {
			const value = String(this.settings.importNodeKey || '').trim();
			return /^import-node-[A-Za-z0-9_-]+$/.test(value) ? value : null;
		},
		iconClass() {
			const raw = String(this.settings.iconClass || '').trim();
			return raw || 'far fa-star';
		},
		view() {
			const value = String(this.settings.view || 'default').trim().toLowerCase();
			return ['default', 'stacked', 'framed'].includes(value) ? value : 'default';
		},
		shape() {
			const value = String(this.settings.shape || 'circle').trim().toLowerCase();
			return ['circle', 'rounded', 'square'].includes(value) ? value : 'circle';
		},
		usesShape() { return this.view === 'stacked' || this.view === 'framed'; },
		customClass() {
			const raw = String(this.settings.cssClass || '').trim();
			if (!raw) return '';
			return raw.split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^A-Za-z0-9_-]/g, '').trim()).filter(Boolean).join(' ');
		},
		wrapperClass() {
			return ['el-widget-icon', 'is-view-' + this.view, this.usesShape ? 'is-shape-' + this.shape : '', this.customClass].filter(Boolean);
		},
		wrapperStyle() {
			const primary = this.safeColor(this.settings.primaryColor, '#6f7f94');
			const primaryHover = this.safeColor(this.settings.primaryColorHover, primary);
			const secondary = this.safeColor(this.settings.secondaryColor, '#7b8796');
			const secondaryHover = this.safeColor(this.settings.secondaryColorHover, secondary);
			const alignment = this.responsiveValue('align', 'left');
			return {
				display: 'flex',
				justifyContent: alignment === 'center' ? 'center' : (alignment === 'right' ? 'flex-end' : 'flex-start'),
				'--pb-icon-primary': primary,
				'--pb-icon-primary-hover': primaryHover,
				'--pb-icon-secondary': secondary,
				'--pb-icon-secondary-hover': secondaryHover,
				'--pb-icon-transition-duration': `${this.duration()}s`,
			};
		},
		glyphStyle() {
			return {
				fontSize: this.safeLength(this.responsiveValue('iconSize', '52px'), '52px'),
				transform: `rotate(${this.safeAngle(this.responsiveValue('iconRotate', '0deg'))})`,
			};
		},
		linkTag() { return this.linkHref ? 'a' : 'span'; },
		linkHref() { return String(this.settings.link || '').trim(); },
		customAttributes() {
			const attrs = {};
			const source = Array.isArray(this.settings.attributes) ? this.settings.attributes : [];
			source.forEach((attribute) => {
				const name = String(attribute?.name || '').trim();
				if (this.allowedAttributeName(name)) attrs[name] = String(attribute?.value ?? '');
			});
			return attrs;
		},
		linkRel() {
			const rel = [];
			if (this.settings.openInNewWindow) rel.push('noopener', 'noreferrer');
			if (this.settings.nofollow) rel.push('nofollow');
			return rel.join(' ');
		},
		linkAttrs() {
			const attrs = {};
			if (this.linkHref) attrs.href = this.linkHref;
			if (this.settings.openInNewWindow) attrs.target = '_blank';
			if (this.linkRel) attrs.rel = this.linkRel;
			Object.assign(attrs, this.customAttributes);
			return attrs;
		},
	},
	methods: {
		allowedAttributeName(name) { return /^(data-[A-Za-z0-9_.:-]+|aria-[A-Za-z0-9_.:-]+|title)$/.test(name); },
		responsiveValue(base, fallback = '') {
			const device = ['tablet', 'mobile'].includes(String(this.responsiveDevice).toLowerCase()) ? String(this.responsiveDevice).toLowerCase() : 'desktop';
			const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]);
			for (const key of keys) {
				const value = this.settings[key];
				if (value !== '' && value !== null && value !== undefined) return value;
			}
			return fallback;
		},
		safeLength(value, fallback = '0px') {
			const raw = String(value ?? '').trim();
			return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback;
		},
		safeAngle(value) {
			const raw = String(value ?? '').trim();
			return /^-?\d+(?:\.\d+)?(?:deg|grad|rad|turn)$/i.test(raw) ? raw : '0deg';
		},
		safeColor(value, fallback = 'inherit') {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback;
		},
		duration() { const value = Number(this.settings.iconTransitionDuration); return Number.isFinite(value) ? Math.min(10, Math.max(0, value)) : 0.3; },
	},
};
</script>

<style scoped>
.el-widget-icon-link { color: var(--pb-icon-primary, #6f7f94) !important; transition: color var(--pb-icon-transition-duration, .3s) ease; }
.el-widget-icon-link:hover { color: var(--pb-icon-primary-hover, #54657d) !important; }
.el-widget-icon.is-view-stacked .el-widget-icon-box { background: var(--pb-icon-secondary, #7b8796) !important; color: var(--pb-icon-primary, #6f7f94) !important; transition: background-color var(--pb-icon-transition-duration, .3s) ease, color var(--pb-icon-transition-duration, .3s) ease; }
.el-widget-icon.is-view-stacked .el-widget-icon-link:hover .el-widget-icon-box { background: var(--pb-icon-secondary-hover, #657181) !important; }
.el-widget-icon.is-view-framed .el-widget-icon-box { border-color: var(--pb-icon-secondary, #7b8796) !important; color: var(--pb-icon-primary, #6f7f94) !important; transition: border-color var(--pb-icon-transition-duration, .3s) ease, color var(--pb-icon-transition-duration, .3s) ease; }
.el-widget-icon.is-view-framed .el-widget-icon-link:hover .el-widget-icon-box { border-color: var(--pb-icon-secondary-hover, #657181) !important; }
@media (prefers-reduced-motion: reduce) { .el-widget-icon-link, .el-widget-icon .el-widget-icon-box { transition: none !important; } }
</style>
