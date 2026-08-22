<template>
	<div class="pb-counter" :class="[positionClass, customClass]" :style="rootStyle">
		<div class="pb-counter__number" :style="numberStyle"><span v-if="prefix" class="pb-counter__prefix">{{ prefix }}</span><span>{{ formattedNumber }}</span><span v-if="suffix" class="pb-counter__suffix">{{ suffix }}</span></div>
		<component v-if="title" :is="safeTitleTag" class="pb-counter__title" :style="titleStyle">{{ title }}</component>
	</div>
</template>

<script>
const TITLE_TAGS = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);
const TITLE_TAG_FONT_SIZES = Object.freeze({ h1: '40px', h2: '34px', h3: '29px', h4: '24px', h5: '20px', h6: '16px', div: '16px', span: '16px', p: '16px' });
const POSITIONS = Object.freeze(['above', 'below', 'left', 'right']);
const NUMBER_POSITIONS = Object.freeze(['left', 'center', 'right', 'stretch']);

export default {
	name: 'GeneralCounter',
	props: { item: { type: Object, required: true }, responsiveDevice: { type: String, default: 'desktop' } },
	data() { return { displayedNumber: 0, animationFrame: 0 }; },
	computed: {
		settings() { return this.item.settings || {}; },
		title() { return String(this.settings.title || ''); },
		prefix() { return String(this.settings.numberPrefix || ''); },
		suffix() { return String(this.settings.numberSuffix || ''); },
		safeTitleTag() { const tag = String(this.settings.titleTag || 'div').toLowerCase(); return TITLE_TAGS.includes(tag) ? tag : 'div'; },
		position() { const value = String(this.responsiveValue('titlePosition', 'below')).toLowerCase(); return POSITIONS.includes(value) ? value : 'below'; },
		numberPosition() { const value = String(this.responsiveValue('numberPosition', 'center')).toLowerCase(); return NUMBER_POSITIONS.includes(value) ? value : 'center'; },
		titleAlign() { const value = String(this.responsiveValue('titleAlign', 'center')).toLowerCase(); return ['left', 'center', 'right'].includes(value) ? value : 'center'; },
		positionClass() { return `pb-counter--position-${this.position}`; },
		customClass() { return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^a-zA-Z0-9_-]/g, '')).filter(Boolean).join(' '); },
		rootStyle() {
			const horizontal = this.numberPosition === 'left' ? 'flex-start' : (this.numberPosition === 'right' ? 'flex-end' : (this.numberPosition === 'stretch' ? 'stretch' : 'center'));
			return { display: 'flex', flexDirection: ['left', 'right'].includes(this.position) ? (this.position === 'right' ? 'row-reverse' : 'row') : 'column', alignItems: ['left', 'right'].includes(this.position) ? 'center' : horizontal, textAlign: this.titleAlign, gap: this.cssSize(this.responsiveValue('titleGap', '8px'), '8px'), width: this.numberPosition === 'stretch' ? '100%' : undefined };
		},
		numberStyle() { return { ...this.typographyStyle('number', { color: this.safeColor(this.settings.numberColor, '#101828'), WebkitTextStrokeWidth: this.cssSize(this.responsiveValue('numberTextStrokeWidth', '0px'), '0px'), WebkitTextStrokeColor: this.safeColor(this.settings.numberTextStrokeColor, 'currentColor'), textShadow: this.safeShadow(this.settings.numberTextShadow) }), textAlign: this.numberPosition === 'stretch' ? 'center' : this.numberPosition }; },
		titleStyle() { return { ...this.typographyStyle('title', { color: this.safeColor(this.settings.titleColor, '#344054'), WebkitTextStrokeWidth: this.cssSize(this.responsiveValue('titleTextStrokeWidth', '0px'), '0px'), WebkitTextStrokeColor: this.safeColor(this.settings.titleTextStrokeColor, 'currentColor'), textShadow: this.safeShadow(this.settings.titleTextShadow), fontSize: this.settings.titleFontSizeMode === 'custom' ? this.cssSize(this.responsiveValue('titleFontSize', '16px'), '16px') : (TITLE_TAG_FONT_SIZES[this.safeTitleTag] || '16px') }), alignSelf: this.position === 'above' || this.position === 'below' ? this.flexAlignment(this.titleAlign) : undefined }; },
		formattedNumber() {
			const number = Number.isFinite(Number(this.displayedNumber)) ? Number(this.displayedNumber) : 0;
			if (!this.settings.thousandSeparator) return String(Math.round(number * 100) / 100);
			let output = number.toLocaleString('en-US', { maximumFractionDigits: 2 });
			if (this.settings.separator === 'dot') output = output.replace(/,/g, '.');
			if (this.settings.separator === 'space') output = output.replace(/,/g, ' ');
			return output;
		},
		animationKey() { return `${this.settings.startingNumber}|${this.settings.endingNumber}|${this.settings.animationDuration}|${this.settings.thousandSeparator}|${this.settings.separator}`; },
	},
	watch: { animationKey: { immediate: true, handler() { this.startAnimation(); } } },
	beforeUnmount() { cancelAnimationFrame(this.animationFrame); },
	methods: {
		startAnimation() {
			cancelAnimationFrame(this.animationFrame);
			const start = Number(this.settings.startingNumber) || 0;
			const end = Number(this.settings.endingNumber) || 0;
			const duration = Math.max(0, Math.min(10000, Number(this.settings.animationDuration) || 0));
			if (!duration) { this.displayedNumber = end; return; }
			const startedAt = performance.now();
			const tick = (now) => {
				const progress = Math.min(1, (now - startedAt) / duration);
				const eased = 1 - Math.pow(1 - progress, 3);
				this.displayedNumber = start + ((end - start) * eased);
				if (progress < 1) this.animationFrame = requestAnimationFrame(tick);
			};
			this.animationFrame = requestAnimationFrame(tick);
		},
		responsiveValue(base, fallback = '') { const device = ['tablet', 'mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of keys) { if (this.settings[key] !== '' && this.settings[key] !== null && this.settings[key] !== undefined) return this.settings[key]; } return fallback; },
		cssSize(value, fallback = '') { const raw = String(value ?? '').trim(); return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback; },
		safeColor(value, fallback = 'inherit') { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback; },
		safeShadow(value) { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : 'none'; },
		flexAlignment(value) { return value === 'left' ? 'flex-start' : (value === 'right' ? 'flex-end' : 'center'); },
		typographyStyle(prefix, additions = {}) { return { fontFamily: String(this.settings[prefix + 'FontFamily'] || 'inherit'), fontSize: this.cssSize(this.responsiveValue(prefix + 'FontSize', prefix === 'number' ? '48px' : '16px'), prefix === 'number' ? '48px' : '16px'), fontWeight: String(this.settings[prefix + 'FontWeight'] || '400'), lineHeight: this.cssSize(this.responsiveValue(prefix + 'LineHeight', prefix === 'number' ? '1.2em' : '1.4em'), prefix === 'number' ? '1.2em' : '1.4em'), letterSpacing: this.cssSize(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'), wordSpacing: this.cssSize(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'), textTransform: ['none', 'uppercase', 'lowercase', 'capitalize'].includes(this.settings[prefix + 'TextTransform']) ? this.settings[prefix + 'TextTransform'] : 'none', fontStyle: ['normal', 'italic', 'oblique'].includes(this.settings[prefix + 'FontStyle']) ? this.settings[prefix + 'FontStyle'] : 'normal', textDecoration: ['none', 'underline', 'overline', 'line-through'].includes(this.settings[prefix + 'TextDecoration']) ? this.settings[prefix + 'TextDecoration'] : 'none', ...additions }; },
	},
};
</script>

<style scoped>
.pb-counter { min-width: 0; width: 100%; }
.pb-counter__number, .pb-counter__title { margin: 0; }
.pb-counter__number { display: inline-flex; align-items: baseline; justify-content: center; gap: .08em; }
.pb-counter--position-left .pb-counter__number, .pb-counter--position-right .pb-counter__number { flex: 0 0 auto; }
.pb-counter--position-left .pb-counter__title, .pb-counter--position-right .pb-counter__title { min-width: 0; }
@media (prefers-reduced-motion: reduce) { .pb-counter__number { transition: none; } }
</style>
