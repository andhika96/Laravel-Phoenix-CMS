<template>
	<div class="pb-progress-bar" :class="customClass">
		<component :is="safeTitleTag" v-if="title" class="pb-progress-bar__title" :style="titleStyle">{{ title }}</component>
		<div class="pb-progress-bar__track" role="progressbar" :aria-valuenow="percentage" aria-valuemin="0" aria-valuemax="100" :style="trackStyle">
			<div class="pb-progress-bar__fill" :style="fillStyle"><span v-if="showPercentage || innerText" class="pb-progress-bar__inner-text">{{ innerText || `${percentage}%` }}</span></div>
		</div>
	</div>
</template>

<script>
const TITLE_TAGS = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);
const TITLE_TAG_FONT_SIZES = Object.freeze({ h1: '40px', h2: '34px', h3: '29px', h4: '24px', h5: '20px', h6: '16px', div: '14px', span: '14px', p: '14px' });
export default {
	name: 'GeneralProgressBar', props: { item: { type: Object, required: true }, responsiveDevice: { type: String, default: 'desktop' } },
	computed: {
		settings() { return this.item.settings || {}; }, title() { return String(this.settings.title || ''); }, innerText() { return String(this.settings.innerText || ''); },
		percentage() { const number = Number(this.settings.percentage); return Number.isFinite(number) ? Math.max(0, Math.min(100, number)) : 0; }, showPercentage() { return !!this.settings.displayPercentage; },
		safeTitleTag() { const tag = String(this.settings.titleTag || 'div').toLowerCase(); return TITLE_TAGS.includes(tag) ? tag : 'div'; },
		customClass() { return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^a-zA-Z0-9_-]/g, '')).filter(Boolean).join(' '); },
		trackStyle() { return { backgroundColor: this.safeColor(this.settings.backgroundColor, '#eef1f4') }; },
		fillStyle() { return { width: `${this.percentage}%`, backgroundColor: this.safeColor(this.settings.progressColor, '#69727d'), color: this.safeColor(this.settings.innerTextColor, '#fff') }; },
		titleStyle() { return this.typographyStyle('title', { color: this.safeColor(this.settings.titleColor, '#344054'), textShadow: this.safeShadow(this.settings.titleTextShadow), fontSize: this.settings.titleFontSizeMode === 'custom' ? this.cssSize(this.responsiveValue('titleFontSize', '14px'), '14px') : (TITLE_TAG_FONT_SIZES[this.safeTitleTag] || '14px') }); },
	},
	methods: {
		responsiveValue(base, fallback = '') { const device = ['tablet', 'mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of keys) { if (this.settings[key] !== '' && this.settings[key] !== null && this.settings[key] !== undefined) return this.settings[key]; } return fallback; },
		cssSize(value, fallback = '') { const raw = String(value ?? '').trim(); return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback; }, safeColor(value, fallback = 'inherit') { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback; }, safeShadow(value) { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : 'none'; },
		typographyStyle(prefix, additions = {}) { return { fontFamily: String(this.settings[prefix + 'FontFamily'] || 'inherit'), fontSize: this.cssSize(this.responsiveValue(prefix + 'FontSize', '14px'), '14px'), fontWeight: String(this.settings[prefix + 'FontWeight'] || '600'), lineHeight: this.cssSize(this.responsiveValue(prefix + 'LineHeight', '1.4em'), '1.4em'), letterSpacing: this.cssSize(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'), wordSpacing: this.cssSize(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'), textTransform: ['none', 'uppercase', 'lowercase', 'capitalize'].includes(this.settings[prefix + 'TextTransform']) ? this.settings[prefix + 'TextTransform'] : 'none', fontStyle: ['normal', 'italic', 'oblique'].includes(this.settings[prefix + 'FontStyle']) ? this.settings[prefix + 'FontStyle'] : 'normal', textDecoration: ['none', 'underline', 'overline', 'line-through'].includes(this.settings[prefix + 'TextDecoration']) ? this.settings[prefix + 'TextDecoration'] : 'none', ...additions }; },
	},
};
</script>

<style scoped>
.pb-progress-bar { width: 100%; min-width: 0; }
.pb-progress-bar__title { display: block; margin: 0 0 8px; }
.pb-progress-bar__track { position: relative; width: 100%; min-height: 14px; overflow: hidden; border-radius: 999px; }
.pb-progress-bar__fill { min-height: 14px; display: flex; align-items: center; justify-content: flex-end; padding: 0 8px; border-radius: inherit; transition: width .6s ease; box-sizing: border-box; }
.pb-progress-bar__inner-text { font-size: 11px; line-height: 1; white-space: nowrap; }
@media (prefers-reduced-motion: reduce) { .pb-progress-bar__fill { transition: none; } }
</style>
