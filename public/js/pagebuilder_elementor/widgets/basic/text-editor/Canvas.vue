<template>
	<div :class="['el-widget-text-editor', customClass]" :style="editorStyle" v-html="html"></div>
</template>

<script>
const ALLOWED_ALIGNMENTS = ['left', 'center', 'right', 'justify'];

export default {
	name: 'BasicTextEditor',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	computed: {
		settings() { return this.item.settings || {}; },
		customClass() {
			const value = String(this.settings.cssClass ?? '').trim();
			if (!value) return '';
			return value.split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^A-Za-z0-9_-]/g, '').trim()).filter(Boolean).join(' ');
		},
		html() { return String(this.item.settings?.html ?? ''); },
		editorStyle() {
		const duration = this.transitionDuration();
		return {
			textAlign: ALLOWED_ALIGNMENTS.includes(this.responsiveValue('align', 'left')) ? this.responsiveValue('align', 'left') : 'left',
			color: this.safeColor(this.settings.textEditorTextColor, '#475467'),
			fontFamily: this.safeFontFamily(this.settings.textEditorFontFamily),
			fontSize: this.safeLength(this.responsiveValue('textEditorFontSize', '16px'), '16px'),
			fontWeight: this.safeFontWeight(this.settings.textEditorFontWeight, '400'),
			lineHeight: this.safeLength(this.responsiveValue('textEditorLineHeight', '1.5em'), '1.5em'),
			letterSpacing: this.safeLength(this.responsiveValue('textEditorLetterSpacing', '0px'), '0px'),
			wordSpacing: this.safeLength(this.responsiveValue('textEditorWordSpacing', '0px'), '0px'),
			textTransform: ['none', 'uppercase', 'lowercase', 'capitalize'].includes(this.settings.textEditorTextTransform) ? this.settings.textEditorTextTransform : 'none',
			fontStyle: ['normal', 'italic', 'oblique'].includes(this.settings.textEditorFontStyle) ? this.settings.textEditorFontStyle : 'normal',
			textDecoration: ['none', 'underline', 'overline', 'line-through'].includes(this.settings.textEditorTextDecoration) ? this.settings.textEditorTextDecoration : 'none',
			textShadow: this.safeShadow(this.settings.textEditorTextShadow),
			'--pb-text-editor-link-color': this.safeColor(this.settings.textEditorLinkColor, '#4f46e5'),
			'--pb-text-editor-hover-color': this.safeColor(this.settings.textEditorTextColorHover, this.settings.textEditorTextColor || '#475467'),
			'--pb-text-editor-link-hover-color': this.safeColor(this.settings.textEditorLinkColorHover, this.settings.textEditorLinkColor || '#4f46e5'),
			'--pb-text-editor-paragraph-spacing': this.safeLength(this.responsiveValue('paragraphSpacing', '1em'), '1em'),
			'--pb-text-editor-transition-duration': `${duration}s`,
		};
	},
	},
	methods: {
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
		safeColor(value, fallback = 'inherit') {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback;
		},
		safeShadow(value) { return this.safeColor(value, 'none'); },
		safeFontFamily(value) {
			const raw = String(value || 'inherit').trim();
			return raw && /^[A-Za-z0-9 _,.'"-]+$/.test(raw) ? raw : 'inherit';
		},
		safeFontWeight(value, fallback) { return /^(?:normal|bold|[1-9]00)$/.test(String(value || '')) ? String(value) : fallback; },
		transitionDuration() { const value = Number(this.settings.textEditorTransitionDuration); return Number.isFinite(value) ? Math.min(10, Math.max(0, value)) : 0.3; },
	},
};
</script>

<style scoped>
.el-widget-text-editor :deep(p) { margin: 0 0 var(--pb-text-editor-paragraph-spacing, 1em); }
.el-widget-text-editor :deep(p:last-child) { margin-bottom: 0; }
.el-widget-text-editor :deep(a) { color: var(--pb-text-editor-link-color, #4f46e5); transition: color var(--pb-text-editor-transition-duration, .3s) ease; }
.el-widget-text-editor :deep(a:hover) { color: var(--pb-text-editor-link-hover-color, #3730a3); }
.el-widget-text-editor:hover { color: var(--pb-text-editor-hover-color, currentColor); }
@media (prefers-reduced-motion: reduce) { .el-widget-text-editor :deep(a) { transition: none; } }
</style>
