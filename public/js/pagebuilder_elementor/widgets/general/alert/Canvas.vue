<template>
	<div v-if="!dismissed" class="pb-alert" :class="[typeClass, customClass]" :style="rootStyle" role="alert">
		<div class="pb-alert__body">
			<div v-if="title" class="pb-alert__title" :style="titleStyle">{{ title }}</div>
			<div v-if="description" class="pb-alert__description" :style="descriptionStyle" v-html="safeDescription"></div>
		</div>
		<button v-if="dismissIcon" type="button" class="pb-alert__dismiss" :style="dismissStyle" aria-label="Dismiss alert" @click.stop="dismissed = true">
			<span v-if="dismissIconSource === 'svg' && safeSvg" v-html="safeSvg"></span><i v-else :class="dismissIconClass" aria-hidden="true"></i>
		</button>
	</div>
</template>

<script>
export default {
	name: 'GeneralAlert', props: { item: { type: Object, required: true }, responsiveDevice: { type: String, default: 'desktop' } }, data() { return { dismissed: false }; },
	computed: {
		settings() { return this.item.settings || {}; }, title() { return String(this.settings.title || ''); }, description() { return String(this.settings.description || ''); }, dismissIcon() { return !!this.settings.dismissIcon; }, dismissIconSource() { return this.settings.dismissIconSource === 'svg' ? 'svg' : 'library'; }, dismissIconClass() { return String(this.settings.dismissIconClass || 'fas fa-times'); }, typeClass() { return `pb-alert--${['info','success','warning','danger'].includes(this.settings.type) ? this.settings.type : 'info'}`; }, customClass() { return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^a-zA-Z0-9_-]/g, '')).filter(Boolean).join(' '); }, safeDescription() { return this.sanitizeHtml(this.description); }, safeSvg() { return this.sanitizeSvg(this.settings.dismissIconSvg); },
		rootStyle() { return { '--pb-alert-bg': this.safeColor(this.settings.backgroundColor, '#eaf4ff'), '--pb-alert-border': this.safeColor(this.settings.borderColor, '#b6d7fe'), '--pb-alert-border-width': this.cssSize(this.settings.borderWidth, '4px'), '--pb-alert-dismiss-color': this.safeColor(this.settings.dismissColor, '#344054'), '--pb-alert-dismiss-hover': this.safeColor(this.settings.dismissColorHover, '#101828'), '--pb-alert-dismiss-duration': `${this.duration(this.settings.dismissTransitionDuration)}s` }; },
		titleStyle() { return this.typographyStyle('title', { color: this.safeColor(this.settings.titleColor, '#1d4ed8'), textShadow: this.safeShadow(this.settings.titleTextShadow) }); }, descriptionStyle() { return this.typographyStyle('description', { color: this.safeColor(this.settings.descriptionColor, '#344054'), textShadow: this.safeShadow(this.settings.descriptionTextShadow) }); }, dismissStyle() { return { fontSize: this.cssSize(this.settings.dismissSize, '16px'), alignSelf: this.dismissVerticalPosition === 'middle' ? 'center' : (this.dismissVerticalPosition === 'bottom' ? 'flex-end' : 'flex-start'), order: this.dismissHorizontalPosition === 'left' ? '-1' : 'initial' }; }, dismissVerticalPosition() { return ['top','middle','bottom'].includes(this.settings.dismissVerticalPosition) ? this.settings.dismissVerticalPosition : 'top'; }, dismissHorizontalPosition() { return this.settings.dismissHorizontalPosition === 'left' ? 'left' : 'right'; },
	},
	methods: {
		cssSize(value, fallback = '') { const raw = String(value ?? '').trim(); return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback; }, safeColor(value, fallback = 'inherit') { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback; }, safeShadow(value) { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : 'none'; }, duration(value) { const number = Number(value); return Number.isFinite(number) ? Math.max(0, Math.min(10, number)) : 0.3; },
		typographyStyle(prefix, additions = {}) { const size = prefix === 'title' ? '18px' : '14px'; const line = prefix === 'title' ? '1.3em' : '1.5em'; return { fontFamily: String(this.settings[prefix + 'FontFamily'] || 'inherit'), fontSize: this.cssSize(this.responsiveValue(prefix + 'FontSize', size), size), fontWeight: String(this.settings[prefix + 'FontWeight'] || '400'), lineHeight: this.cssSize(this.responsiveValue(prefix + 'LineHeight', line), line), letterSpacing: this.cssSize(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'), wordSpacing: this.cssSize(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'), textTransform: ['none','uppercase','lowercase','capitalize'].includes(this.settings[prefix + 'TextTransform']) ? this.settings[prefix + 'TextTransform'] : 'none', fontStyle: ['normal','italic','oblique'].includes(this.settings[prefix + 'FontStyle']) ? this.settings[prefix + 'FontStyle'] : 'normal', textDecoration: ['none','underline','overline','line-through'].includes(this.settings[prefix + 'TextDecoration']) ? this.settings[prefix + 'TextDecoration'] : 'none', ...additions }; },
		responsiveValue(base, fallback = '') { const device = ['tablet','mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of keys) { if (this.settings[key] !== '' && this.settings[key] !== null && this.settings[key] !== undefined) return this.settings[key]; } return fallback; }, sanitizeHtml(value) { const raw = String(value || ''); if (typeof document === 'undefined') return raw.replace(/<[^>]+>/g, ''); const template = document.createElement('template'); template.innerHTML = raw; const allowed = new Set(['P','BR','STRONG','EM','B','I','A','UL','OL','LI']); template.content.querySelectorAll('*').forEach((element) => { if (!allowed.has(element.tagName)) { element.replaceWith(...Array.from(element.childNodes)); return; } Array.from(element.attributes).forEach((attribute) => { const name = attribute.name.toLowerCase(); const val = String(attribute.value || ''); if (name.startsWith('on') || (name === 'href' && !/^(?:https?:|mailto:|tel:|\/|#)/i.test(val))) element.removeAttribute(attribute.name); }); }); return template.innerHTML; }, sanitizeSvg(value) { const raw = String(value || '').trim(); if (!raw || typeof DOMParser === 'undefined') return ''; const doc = new DOMParser().parseFromString(raw, 'image/svg+xml'); const root = doc.documentElement; if (!root || root.nodeName.toLowerCase() !== 'svg' || doc.querySelector('parsererror')) return ''; root.querySelectorAll('*').forEach((element) => Array.from(element.attributes).forEach((attribute) => { const name = attribute.name.toLowerCase(); if (name.startsWith('on') || name === 'style' || name.includes('href')) element.removeAttribute(attribute.name); })); return root.outerHTML; },
	},
};
</script>

<style scoped>
.pb-alert { display: flex; align-items: stretch; gap: 14px; width: 100%; min-width: 0; box-sizing: border-box; padding: 16px 18px; background: var(--pb-alert-bg); border-left: var(--pb-alert-border-width) solid var(--pb-alert-border); }
.pb-alert__body { flex: 1 1 auto; min-width: 0; }
.pb-alert__title, .pb-alert__description { margin: 0; }
.pb-alert__description :deep(p) { margin: 0 0 6px; }
.pb-alert__description :deep(p:last-child) { margin-bottom: 0; }
.pb-alert__dismiss { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 28px; height: 28px; padding: 0; border: 0; background: transparent; color: var(--pb-alert-dismiss-color); cursor: pointer; transition: color var(--pb-alert-dismiss-duration) ease, opacity var(--pb-alert-dismiss-duration) ease; }
.pb-alert__dismiss:hover, .pb-alert__dismiss:focus-visible { color: var(--pb-alert-dismiss-hover); }
.pb-alert__dismiss span, .pb-alert__dismiss span :deep(svg) { width: 1em; height: 1em; fill: currentColor; }
:global(.pb-node-alert .pb-preview) { pointer-events: auto; }
.pb-alert--success { --pb-alert-bg: #ecfdf3; --pb-alert-border: #86efac; }
.pb-alert--warning { --pb-alert-bg: #fffbeb; --pb-alert-border: #fcd34d; }
.pb-alert--danger { --pb-alert-bg: #fef2f2; --pb-alert-border: #fca5a5; }
@media (prefers-reduced-motion: reduce) { .pb-alert__dismiss { transition: none; } }
</style>
