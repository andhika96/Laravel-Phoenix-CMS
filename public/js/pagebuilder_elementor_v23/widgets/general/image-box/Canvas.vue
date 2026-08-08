<template>
	<div class="pb-image-box" :class="[positionClass, customClass]" :style="boxStyle">
		<div class="pb-image-box__media">
			<a v-if="safeLinkUrl" class="pb-image-box__image-link" :href="safeLinkUrl" :target="linkTarget" :rel="linkRel" v-bind="safeCustomAttributes">
				<img v-if="imageUrl" class="pb-image-box__image" :src="imageUrl" :alt="imageAlt" :style="imageStyle">
				<div v-else class="pb-image-box__empty-media" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>
			</a>
			<template v-else>
				<img v-if="imageUrl" class="pb-image-box__image" :src="imageUrl" :alt="imageAlt" :style="imageStyle">
				<div v-else class="pb-image-box__empty-media" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>
			</template>
		</div>

		<div class="pb-image-box__content">
			<a v-if="safeLinkUrl && title" class="pb-image-box__title-link" :href="safeLinkUrl" :target="linkTarget" :rel="linkRel" v-bind="safeCustomAttributes">
				<component :is="safeTitleTag" class="pb-image-box__title" :style="titleStyle">{{ title }}</component>
			</a>
			<component v-else-if="title" :is="safeTitleTag" class="pb-image-box__title" :style="titleStyle">{{ title }}</component>
			<p v-if="description" class="pb-image-box__description" :style="descriptionStyle">{{ description }}</p>
		</div>
	</div>
</template>

<script>
const TITLE_TAGS = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);
const TITLE_TAG_FONT_SIZES = Object.freeze({ h1: '40px', h2: '34px', h3: '29px', h4: '24px', h5: '20px', h6: '16px', div: '29px', span: '29px', p: '29px' });
const POSITIONS = Object.freeze(['top', 'left', 'right']);
const ALIGNMENTS = Object.freeze(['left', 'center', 'right', 'justify']);
const BORDER_TYPES = Object.freeze(['none', 'solid', 'double', 'dotted', 'dashed', 'groove']);

export default {
	name: 'GeneralImageBox',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
		dynamicContext: { type: Object, default: () => ({}) },
	},
	data() { return { resolvedImageUrl: '', imageRenditionRequest: 0 }; },
	computed: {
		settings() { return this.item.settings || {}; },
		rawImageUrl() { return String(this.resolveDynamicValue('imageUrl', this.settings.imageUrl || '')).trim(); },
		imageUrl() { return this.resolvedImageUrl || this.rawImageUrl; },
		imageRenditionKey() { return `${this.rawImageUrl}|${String(this.settings.imageResolution || 'full')}|${Number(this.settings.customImageWidth) || 150}|${Number(this.settings.customImageHeight) || 150}`; },
		imageAlt() { return String(this.settings.imageAlt || ''); },
		title() { return String(this.resolveDynamicValue('title', this.settings.title || '')); },
		description() { return String(this.resolveDynamicValue('description', this.settings.description || '')); },
		safeTitleTag() {
			const tag = String(this.settings.titleTag || 'h3').toLowerCase();
			return TITLE_TAGS.includes(tag) ? tag : 'h3';
		},
		automaticTitleFontSize() { return TITLE_TAG_FONT_SIZES[this.safeTitleTag] || '29px'; },
		position() {
			const value = String(this.responsiveValue('imagePosition', 'top')).toLowerCase();
			return POSITIONS.includes(value) ? value : 'top';
		},
		alignment() {
			const value = String(this.responsiveValue('alignment', 'center')).toLowerCase();
			return ALIGNMENTS.includes(value) ? value : 'center';
		},
		positionClass() { return `pb-image-box--position-${this.position}`; },
		customClass() {
			return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^a-zA-Z0-9_-]/g, '')).filter(Boolean).join(' ');
		},
		safeLinkUrl() {
			const url = String(this.resolveDynamicValue('linkUrl', this.settings.linkUrl || '')).trim();
			if (!url) return '';
			if (/^(https?:|mailto:|tel:)/i.test(url) || url.startsWith('/') || url.startsWith('#')) return url;
			return '';
		},
		linkTarget() { return this.settings.linkTarget === '_blank' ? '_blank' : null; },
		linkRel() {
			const rel = [];
			if (this.linkTarget === '_blank') rel.push('noopener', 'noreferrer');
			if (this.settings.linkNofollow) rel.push('nofollow');
			return [...new Set(rel)].join(' ') || null;
		},
		safeCustomAttributes() {
			const output = {};
			const allowed = /^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i;
			(Array.isArray(this.settings.linkCustomAttributes) ? this.settings.linkCustomAttributes : []).forEach((attribute) => {
				const key = String(attribute?.key || '').trim();
				if (!allowed.test(key)) return;
				output[key] = String(attribute?.value ?? '');
			});
			return output;
		},
		boxStyle() {
			const imageSpacing = this.cssSize(this.responsiveValue('imageSpacing', '15px'), '15px');
			return {
				display: 'flex',
				flexDirection: this.position === 'top' ? 'column' : (this.position === 'right' ? 'row-reverse' : 'row'),
				alignItems: this.position === 'top' ? this.flexAlignment(this.alignment) : 'center',
				textAlign: this.alignment,
				'--pb-image-box-image-spacing': imageSpacing,
				'--pb-image-box-content-spacing': this.cssSize(this.responsiveValue('contentSpacing', '0px'), '0px'),
				'--pb-image-box-media-justify': this.position === 'top' ? this.flexAlignment(this.alignment) : 'center',
				'--pb-image-box-hover-filter': this.filterCss(this.settings.imageHoverFilter),
				'--pb-image-box-hover-opacity': String(this.opacity(this.settings.imageHoverOpacity, 1)),
				'--pb-image-box-hover-transition': `${this.duration(this.settings.imageHoverTransition)}s`,
			};
		},
		imageStyle() {
			const borderType = BORDER_TYPES.includes(this.settings.imageBorderType) ? this.settings.imageBorderType : 'none';
			return {
				width: this.cssSize(this.responsiveValue('imageWidth', '30%'), '30%'),
				maxWidth: '100%',
				display: 'block',
				borderStyle: borderType,
				borderWidth: borderType === 'none' ? '0' : this.cssSize(this.settings.imageBorderWidth, '1px'),
				borderColor: this.safeColor(this.settings.imageBorderColor, '#000000'),
				borderRadius: this.cssSize(this.responsiveValue('imageBorderRadius', '0px'), '0px'),
				filter: this.filterCss(this.settings.imageNormalFilter),
				opacity: this.opacity(this.settings.imageNormalOpacity, 1),
				transition: `filter ${this.duration(this.settings.imageHoverTransition)}s ease, opacity ${this.duration(this.settings.imageHoverTransition)}s ease`,
			};
		},
		titleStyle() {
			return this.typographyStyle('title', {
				fontSize: this.settings.titleFontSizeMode === 'custom'
					? this.cssSize(this.responsiveValue('titleFontSize', '29px'), '29px')
					: this.automaticTitleFontSize,
				color: this.safeColor(this.settings.titleColor, 'inherit'),
				WebkitTextStrokeWidth: this.cssSize(this.responsiveValue('titleTextStrokeWidth', '0px'), '0px'),
				WebkitTextStrokeColor: this.safeColor(this.settings.titleTextStrokeColor, 'currentColor'),
				textShadow: this.safeShadow(this.settings.titleTextShadow),
			});
		},
		descriptionStyle() {
			return this.typographyStyle('description', {
				color: this.safeColor(this.settings.descriptionColor, 'inherit'),
				textShadow: this.safeShadow(this.settings.descriptionTextShadow),
			});
		},
	},
	watch: {
		imageRenditionKey: {
			immediate: true,
			handler() { this.resolveImageRendition(); },
		},
	},
	methods: {
		resolveDynamicValue(field, fallback) {
			const binding = String(this.settings.dynamicBindings?.[field] || '');
			if (!binding || !Object.prototype.hasOwnProperty.call(this.dynamicContext, binding)) return fallback;
			const value = this.dynamicContext[binding];
			return value === null || value === undefined ? fallback : value;
		},
		async resolveImageRendition() {
			const requestId = ++this.imageRenditionRequest;
			const sourceUrl = this.rawImageUrl;
			this.resolvedImageUrl = sourceUrl;
			const endpoint = String(window.PAGE_BUILDER_ELEMENTOR_V23_CONTEXT?.imageRenditionUrl || '');
			if (!sourceUrl || !endpoint || !window.axios) return;
			try {
				const size = this.settings.imageResolution || 'full';
				const params = { url: sourceUrl, size };
				if (size === 'custom') {
					params.width = Number(this.settings.customImageWidth) || 150;
					params.height = Number(this.settings.customImageHeight) || 150;
				}
				const response = await window.axios.get(endpoint, { params });
				if (requestId === this.imageRenditionRequest) this.resolvedImageUrl = String(response.data?.url || sourceUrl);
			} catch (_) {
				if (requestId === this.imageRenditionRequest) this.resolvedImageUrl = sourceUrl;
			}
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
		flexAlignment(value) {
			return value === 'left' ? 'flex-start' : (value === 'right' ? 'flex-end' : (value === 'justify' ? 'stretch' : 'center'));
		},
		cssSize(value, fallback = '') {
			const raw = String(value ?? '').trim();
			return /^-?\d+(?:\.\d+)?(?:px|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback;
		},
		safeColor(value, fallback = 'inherit') {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback;
		},
		safeShadow(value) {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : 'none';
		},
		opacity(value, fallback) {
			const number = Number(value);
			return Number.isFinite(number) ? Math.min(1, Math.max(0, number)) : fallback;
		},
		duration(value) {
			const number = Number(value);
			return Number.isFinite(number) ? Math.min(10, Math.max(0, number)) : 0.3;
		},
		filterCss(filters) {
			const source = filters && typeof filters === 'object' ? filters : {};
			const blur = Math.min(100, Math.max(0, Number(source.blur) || 0));
			const brightness = Math.min(200, Math.max(0, Number(source.brightness ?? 100) || 0));
			const contrast = Math.min(200, Math.max(0, Number(source.contrast ?? 100) || 0));
			const saturation = Math.min(200, Math.max(0, Number(source.saturation ?? 100) || 0));
			const hue = Math.min(360, Math.max(0, Number(source.hue) || 0));
			return `blur(${blur}px) brightness(${brightness}%) contrast(${contrast}%) saturate(${saturation}%) hue-rotate(${hue}deg)`;
		},
		typographyStyle(prefix, additions = {}) {
			return {
				fontFamily: String(this.settings[prefix + 'FontFamily'] || 'inherit'),
				fontSize: this.cssSize(this.responsiveValue(prefix + 'FontSize', prefix === 'title' ? '29px' : '16px'), prefix === 'title' ? '29px' : '16px'),
				fontWeight: String(this.settings[prefix + 'FontWeight'] || '400'),
				lineHeight: this.cssSize(this.responsiveValue(prefix + 'LineHeight', prefix === 'title' ? '1.2em' : '1.5em'), prefix === 'title' ? '1.2em' : '1.5em'),
				letterSpacing: this.cssSize(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'),
				wordSpacing: this.cssSize(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'),
				textTransform: ['none', 'uppercase', 'lowercase', 'capitalize'].includes(this.settings[prefix + 'TextTransform']) ? this.settings[prefix + 'TextTransform'] : 'none',
				fontStyle: ['normal', 'italic', 'oblique'].includes(this.settings[prefix + 'FontStyle']) ? this.settings[prefix + 'FontStyle'] : 'normal',
				textDecoration: ['none', 'underline', 'overline', 'line-through'].includes(this.settings[prefix + 'TextDecoration']) ? this.settings[prefix + 'TextDecoration'] : 'none',
				...additions,
			};
		},
	},
};
</script>

<style scoped>
.pb-image-box { width: 100%; min-width: 0; }
.pb-image-box__media { flex: 0 0 auto; display: flex; justify-content: var(--pb-image-box-media-justify, center); min-width: 0; }
.pb-image-box__image-link { display: block; width: fit-content; max-width: 100%; color: inherit; }
.pb-image-box__image { height: auto; object-fit: cover; }
.pb-image-box__empty-media { width: min(100%, 320px); aspect-ratio: 16 / 9; display: grid; place-items: center; border: 1px solid #d8dee8; border-radius: 4px; background: #f2f4f7; color: #98a2b3; font-size: 44px; }
.pb-image-box__content { min-width: 0; flex: 1 1 auto; }
.pb-image-box__title-link { color: inherit; text-decoration: none; }
.pb-image-box__title { margin: 0 0 var(--pb-image-box-content-spacing); }
.pb-image-box__description { margin: 0; }
.pb-image-box--position-top .pb-image-box__media { width: 100%; margin-bottom: var(--pb-image-box-image-spacing); }
.pb-image-box--position-left .pb-image-box__media { margin-right: var(--pb-image-box-image-spacing); }
.pb-image-box--position-right .pb-image-box__media { margin-left: var(--pb-image-box-image-spacing); }
.pb-image-box:hover .pb-image-box__image { filter: var(--pb-image-box-hover-filter); opacity: var(--pb-image-box-hover-opacity); }
@media (prefers-reduced-motion: reduce) { .pb-image-box__image { transition: none !important; } }
</style>
