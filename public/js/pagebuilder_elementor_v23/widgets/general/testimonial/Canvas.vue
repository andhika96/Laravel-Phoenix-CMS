<template>
	<div class="pb-testimonial" :class="[positionClass, customClass]" :style="rootStyle">
		<div class="pb-testimonial__media" :style="mediaStyle">
			<a v-if="safeLinkUrl && imageUrl" :href="safeLinkUrl" :target="linkTarget" :rel="linkRel" class="pb-testimonial__image-link"><img class="pb-testimonial__image" :src="imageUrl" :alt="imageAlt" :style="imageStyle"></a>
			<img v-else-if="imageUrl" class="pb-testimonial__image" :src="imageUrl" :alt="imageAlt" :style="imageStyle">
			<div v-else class="pb-testimonial__empty-image" role="img" aria-label="Choose an image"><i class="far fa-user" aria-hidden="true"></i></div>
		</div>
		<div class="pb-testimonial__body">
			<div class="pb-testimonial__content" :style="contentStyle" v-html="safeContent"></div>
			<a v-if="safeLinkUrl && name" :href="safeLinkUrl" :target="linkTarget" :rel="linkRel" class="pb-testimonial__name-link"><span class="pb-testimonial__name" :style="nameStyle">{{ name }}</span></a>
			<span v-else-if="name" class="pb-testimonial__name" :style="nameStyle">{{ name }}</span>
			<span v-if="title" class="pb-testimonial__title" :style="titleStyle">{{ title }}</span>
		</div>
	</div>
</template>

<script>
const POSITIONS = Object.freeze(['top', 'left', 'right']);
const ALIGNMENTS = Object.freeze(['left', 'center', 'right', 'justify']);
export default {
	name: 'GeneralTestimonial', props: { item: { type: Object, required: true }, responsiveDevice: { type: String, default: 'desktop' } },
	computed: {
		settings() { return this.item.settings || {}; }, imageUrl() { const raw = String(this.settings.imageUrl || '').trim(); return /^(?:https?:|data:image\/|\/)/i.test(raw) && !raw.startsWith('//') ? raw : ''; }, imageAlt() { return String(this.settings.imageAlt || ''); }, content() { return String(this.settings.content || ''); }, name() { return String(this.settings.name || ''); }, title() { return String(this.settings.title || ''); },
		position() { const value = String(this.responsiveValue('imagePosition', 'top')).toLowerCase(); return POSITIONS.includes(value) ? value : 'top'; }, alignment() { const value = String(this.responsiveValue('alignment', 'center')).toLowerCase(); return ALIGNMENTS.includes(value) ? value : 'center'; }, positionClass() { return `pb-testimonial--position-${this.position}`; },
		customClass() { return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^a-zA-Z0-9_-]/g, '')).filter(Boolean).join(' '); }, safeContent() { return this.sanitizeHtml(this.content); },
		rootStyle() { return { display: 'flex', flexDirection: this.position === 'right' ? 'row-reverse' : (this.position === 'left' ? 'row' : 'column'), alignItems: this.position === 'top' ? this.flexAlignment(this.alignment) : 'center', textAlign: this.alignment, gap: this.position === 'top' ? '16px' : '20px' }; },
		mediaStyle() { return { flex: '0 0 auto' }; }, imageStyle() { const borderType = ['none','solid','double','dotted','dashed','groove'].includes(this.settings.imageBorderType) ? this.settings.imageBorderType : 'none'; return { width: this.cssSize(this.responsiveValue('imageSize', '80px'), '80px'), height: this.cssSize(this.responsiveValue('imageSize', '80px'), '80px'), objectFit: 'cover', borderStyle: borderType, borderWidth: borderType === 'none' ? '0' : this.cssSize(this.settings.imageBorderWidth, '1px'), borderColor: this.safeColor(this.settings.imageBorderColor, '#d0d7e6'), borderRadius: this.cssSize(this.responsiveValue('imageBorderRadius', '50%'), '50%'), display: 'block' }; },
		contentStyle() { return this.typographyStyle('content', { color: this.safeColor(this.settings.contentColor, '#526173'), textShadow: this.safeShadow(this.settings.contentTextShadow) }); }, nameStyle() { return this.typographyStyle('name', { color: this.safeColor(this.settings.nameColor, '#344054'), textShadow: this.safeShadow(this.settings.nameTextShadow) }); }, titleStyle() { return this.typographyStyle('title', { color: this.safeColor(this.settings.titleColor, '#7a8699'), textShadow: this.safeShadow(this.settings.titleTextShadow) }); },
		safeLinkUrl() { const raw = String(this.settings.linkUrl || '').trim(); return raw && !raw.startsWith('//') && (/^(?:https?:|mailto:|tel:)/i.test(raw) || raw.startsWith('/') || raw.startsWith('#')) ? raw : ''; }, linkTarget() { return this.settings.linkTarget === '_blank' ? '_blank' : null; }, linkRel() { const rel = []; if (this.linkTarget === '_blank') rel.push('noopener', 'noreferrer'); if (this.settings.linkNofollow) rel.push('nofollow'); return [...new Set(rel)].join(' ') || null; },
	},
	methods: {
		responsiveValue(base, fallback = '') { const device = ['tablet','mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of keys) { if (this.settings[key] !== '' && this.settings[key] !== null && this.settings[key] !== undefined) return this.settings[key]; } return fallback; }, cssSize(value, fallback = '') { const raw = String(value ?? '').trim(); return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback; }, safeColor(value, fallback = 'inherit') { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback; }, safeShadow(value) { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : 'none'; }, flexAlignment(value) { return value === 'left' ? 'flex-start' : (value === 'right' ? 'flex-end' : (value === 'justify' ? 'stretch' : 'center')); },
		typographyStyle(prefix, additions = {}) { const size = prefix === 'content' ? '16px' : (prefix === 'name' ? '18px' : '14px'); const line = prefix === 'content' ? '1.5em' : (prefix === 'name' ? '1.3em' : '1.4em'); return { fontFamily: String(this.settings[prefix + 'FontFamily'] || 'inherit'), fontSize: this.cssSize(this.responsiveValue(prefix + 'FontSize', size), size), fontWeight: String(this.settings[prefix + 'FontWeight'] || '400'), lineHeight: this.cssSize(this.responsiveValue(prefix + 'LineHeight', line), line), letterSpacing: this.cssSize(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'), wordSpacing: this.cssSize(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'), textTransform: ['none','uppercase','lowercase','capitalize'].includes(this.settings[prefix + 'TextTransform']) ? this.settings[prefix + 'TextTransform'] : 'none', fontStyle: ['normal','italic','oblique'].includes(this.settings[prefix + 'FontStyle']) ? this.settings[prefix + 'FontStyle'] : 'normal', textDecoration: ['none','underline','overline','line-through'].includes(this.settings[prefix + 'TextDecoration']) ? this.settings[prefix + 'TextDecoration'] : 'none', ...additions }; },
		sanitizeHtml(value) { const raw = String(value || ''); if (typeof document === 'undefined') return raw.replace(/<[^>]+>/g, ''); const template = document.createElement('template'); template.innerHTML = raw; const allowed = new Set(['P','BR','STRONG','EM','B','I','A','UL','OL','LI']); template.content.querySelectorAll('*').forEach((element) => { if (!allowed.has(element.tagName)) { element.replaceWith(...Array.from(element.childNodes)); return; } Array.from(element.attributes).forEach((attribute) => { const name = attribute.name.toLowerCase(); const val = String(attribute.value || ''); if (name.startsWith('on') || (name === 'href' && !/^(?:https?:|mailto:|tel:|\/|#)/i.test(val))) element.removeAttribute(attribute.name); }); }); return template.innerHTML; },
	},
};
</script>

<style scoped>
.pb-testimonial { width: 100%; min-width: 0; }
.pb-testimonial__media { display: flex; justify-content: center; }
.pb-testimonial__image-link { display: block; color: inherit; }
.pb-testimonial__empty-image { width: 80px; height: 80px; display: grid; place-items: center; border: 1px dashed #cbd5e1; border-radius: 50%; background: #f8fafc; color: #98a2b3; font-size: 26px; }
.pb-testimonial__body { min-width: 0; display: flex; flex: 1 1 auto; flex-direction: column; gap: 5px; }
.pb-testimonial__content, .pb-testimonial__name, .pb-testimonial__title { margin: 0; }
.pb-testimonial__content :deep(p) { margin: 0 0 8px; }
.pb-testimonial__content :deep(p:last-child) { margin-bottom: 0; }
.pb-testimonial__name-link { color: inherit; text-decoration: none; }
.pb-testimonial--position-left .pb-testimonial__media, .pb-testimonial--position-right .pb-testimonial__media { align-self: flex-start; }
@media (max-width: 767px) { .pb-testimonial--position-left, .pb-testimonial--position-right { flex-direction: column !important; align-items: center !important; text-align: center !important; } }
</style>
