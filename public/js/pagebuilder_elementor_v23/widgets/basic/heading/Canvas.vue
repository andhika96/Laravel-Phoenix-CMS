<template>
	<component :is="safeTag" :class="['el-widget-heading', 'elementor-heading-title', customClass]" :style="titleStyle">
		<a v-if="safeLinkUrl" class="heading-title-link" :href="safeLinkUrl" :target="linkTarget" :rel="linkRel" v-bind="safeCustomAttributes">{{ text }}</a>
		<template v-else>{{ text }}</template>
	</component>
</template>

<script>
const ALLOWED_HEADING_TAGS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];
const HEADING_TAG_FONT_SIZES = Object.freeze({ h1: '40px', h2: '34px', h3: '29px', h4: '24px', h5: '20px', h6: '16px', div: '29px', span: '29px', p: '29px' });
const ALLOWED_ALIGNMENTS = ['left', 'center', 'right', 'justify'];
const ALLOWED_BLEND_MODES = ['normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'saturation', 'color', 'difference', 'exclusion', 'hue', 'luminosity'];

export default {
	name: 'BasicHeadingCanvas',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
		dynamicContext: { type: Object, default: () => ({}) },
	},
	computed: {
		settings() { return this.item.settings || {}; },
		safeTag() {
			const tag = String(this.item.settings?.tag || 'h2').toLowerCase();
			return ALLOWED_HEADING_TAGS.includes(tag) ? tag : 'h2';
		},
		customClass() {
			const value = String(this.item.settings?.cssClass ?? '').trim();
			if (!value) return '';
			return value.split(/\s+/).map((token) => token.replace(/^\.+/, '').trim()).filter(Boolean).join(' ');
		},
		text() { return String(this.resolveDynamicValue('title', this.settings.text || 'Heading')); },
		safeLinkUrl() {
			const url = String(this.resolveDynamicValue('linkUrl', this.settings.linkUrl || '')).trim();
			if (!url || url.startsWith('//')) return '';
			return /^(https?:|mailto:|tel:)/i.test(url) || url.startsWith('/') || url.startsWith('#') ? url : '';
		},
		linkTarget() { return this.settings.linkTarget === '_blank' ? '_blank' : null; },
		linkRel() {
			const rel = [];
			if (this.settings.linkNofollow) rel.push('nofollow');
			if (this.linkTarget === '_blank') rel.push('noopener', 'noreferrer');
			return [...new Set(rel)].join(' ') || null;
		},
		safeCustomAttributes() {
			const output = {};
			const allowed = /^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i;
			(Array.isArray(this.settings.linkCustomAttributes) ? this.settings.linkCustomAttributes : []).forEach((attribute) => {
				const key = String(attribute?.key || attribute?.name || '').trim();
				if (allowed.test(key)) output[key] = String(attribute?.value ?? '');
			});
			return output;
		},
		titleStyle() {
			const alignment = String(this.responsiveValue('align', 'left')).toLowerCase();
			const blendMode = ALLOWED_BLEND_MODES.includes(this.settings.blendMode) ? this.settings.blendMode : 'normal';
			return {
				margin: 0,
				textAlign: ALLOWED_ALIGNMENTS.includes(alignment) ? alignment : 'left',
				color: this.safeColor(this.settings.color, '#101828'),
				fontFamily: String(this.settings.headingFontFamily || 'inherit'),
				fontSize: this.settings.headingFontSizeMode === 'custom'
					? this.cssSize(this.responsiveValue('headingFontSize', '32px'), '32px')
					: (HEADING_TAG_FONT_SIZES[this.safeTag] || '32px'),
				fontWeight: String(this.settings.headingFontWeight || '600'),
				lineHeight: this.cssSize(this.responsiveValue('headingLineHeight', '1.2em'), '1.2em'),
				letterSpacing: this.cssSize(this.responsiveValue('headingLetterSpacing', '0px'), '0px'),
				wordSpacing: this.cssSize(this.responsiveValue('headingWordSpacing', '0px'), '0px'),
				textTransform: ['none', 'uppercase', 'lowercase', 'capitalize'].includes(this.settings.headingTextTransform) ? this.settings.headingTextTransform : 'none',
				fontStyle: ['normal', 'italic', 'oblique'].includes(this.settings.headingFontStyle) ? this.settings.headingFontStyle : 'normal',
				textDecoration: ['none', 'underline', 'overline', 'line-through'].includes(this.settings.headingTextDecoration) ? this.settings.headingTextDecoration : 'none',
				WebkitTextStrokeWidth: this.cssSize(this.responsiveValue('headingTextStrokeWidth', '0px'), '0px'),
				WebkitTextStrokeColor: this.safeColor(this.settings.headingTextStrokeColor, 'currentColor'),
				textShadow: this.safeShadow(this.settings.headingTextShadow),
				mixBlendMode: blendMode,
				'--pb-heading-hover-color': this.safeColor(this.settings.hoverColor, this.settings.color || '#101828'),
				'--pb-heading-hover-duration': `${this.duration(this.settings.hoverTransitionDuration)}s`,
			};
		},
	},
	methods: {
		resolveDynamicValue(field, fallback) {
			const binding = String(this.settings.dynamicBindings?.[field] || '');
			if (!binding || !Object.prototype.hasOwnProperty.call(this.dynamicContext, binding)) return fallback;
			const value = this.dynamicContext[binding];
			return value === null || value === undefined ? fallback : value;
		},
		responsiveValue(base, fallback = '') {
			const device = ['tablet', 'mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop';
			const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]);
			for (const key of keys) {
				const value = this.settings[key];
				if (value !== '' && value !== null && value !== undefined) return value;
			}
			return fallback;
		},
		cssSize(value, fallback = '') {
			const raw = String(value ?? '').trim();
			return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback;
		},
		safeColor(value, fallback = 'inherit') {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback;
		},
		safeShadow(value) {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : 'none';
		},
		duration(value) {
			const number = Number(value);
			return Number.isFinite(number) ? Math.min(10, Math.max(0, number)) : 0.3;
		},
	},
};
</script>

<style scoped>
.elementor-heading-title { width: 100%; }
.heading-title-link { color: inherit; text-decoration: inherit; transition: color var(--pb-heading-hover-duration, .3s) ease; }
.heading-title-link:hover { color: var(--pb-heading-hover-color, currentColor); }
@media (prefers-reduced-motion: reduce) { .heading-title-link { transition: none; } }
</style>
