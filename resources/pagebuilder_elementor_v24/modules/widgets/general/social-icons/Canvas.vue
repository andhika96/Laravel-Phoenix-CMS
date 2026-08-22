<template>
	<div class="pb-social-icons" :class="[shapeClass, alignmentClass, customClass]" :style="rootStyle">
		<a v-for="entry in items" :key="entry.id" class="pb-social-icons__item" :href="safeUrl(entry.linkUrl)" :target="entry.linkTarget === '_blank' ? '_blank' : null" :rel="linkRel(entry)" :style="itemStyle(entry)" :aria-label="entry.iconName || 'Social link'" v-bind="customAttributes(entry)">
			<span v-if="entry.iconSource === 'svg' && safeSvg(entry.iconSvg)" class="pb-social-icons__svg" v-html="safeSvg(entry.iconSvg)"></span>
			<i v-else :class="entry.iconClass || 'fas fa-star'" aria-hidden="true"></i>
		</a>
	</div>
</template>

<script>
const OFFICIAL_COLORS = Object.freeze({ facebook: ['#1877f2', '#fff'], 'facebook-f': ['#1877f2', '#fff'], twitter: ['#1da1f2', '#fff'], instagram: ['#e1306c', '#fff'], linkedin: ['#0a66c2', '#fff'], youtube: ['#ff0000', '#fff'], pinterest: ['#bd081c', '#fff'], github: ['#181717', '#fff'], whatsapp: ['#25d366', '#fff'] });
export default {
	name: 'GeneralSocialIcons', props: { item: { type: Object, required: true } },
	computed: {
		settings() { return this.item.settings || {}; }, items() { return Array.isArray(this.settings.items) ? this.settings.items : []; }, shapeClass() { return `pb-social-icons--shape-${['rounded','square','circle'].includes(this.settings.shape) ? this.settings.shape : 'rounded'}`; }, alignmentClass() { return `pb-social-icons--align-${['left','center','right'].includes(this.settings.alignment) ? this.settings.alignment : 'left'}`; }, customClass() { return String(this.settings.cssClass || '').split(/\s+/).map((token) => token.replace(/^\.+/, '').replace(/[^a-zA-Z0-9_-]/g, '')).filter(Boolean).join(' '); },
		rootStyle() { const columns = String(this.settings.columns || 'auto'); const borderType = ['none','solid','double','dotted','dashed'].includes(this.settings.borderType) ? this.settings.borderType : 'none'; return { '--pb-social-size': this.cssSize(this.settings.size, '18px'), '--pb-social-padding': this.cssSize(this.settings.padding, '10px'), '--pb-social-spacing': this.cssSize(this.settings.spacing, '8px'), '--pb-social-row-gap': this.cssSize(this.settings.rowsGap, '8px'), '--pb-social-border-width': this.cssSize(this.settings.borderWidth, '1px'), '--pb-social-border-style': borderType, '--pb-social-border-color': this.safeColor(this.settings.borderColor, '#d0d7e6'), '--pb-social-border-radius': this.cssSize(this.settings.borderRadius, '4px'), '--pb-social-duration': `${this.duration(this.settings.transitionDuration)}s`, gridTemplateColumns: columns === 'auto' ? 'repeat(auto-fit, minmax(40px, max-content))' : `repeat(${Math.max(1, Math.min(6, Number(columns) || 1))}, minmax(0, max-content))` }; },
	},
	methods: {
		cssSize(value, fallback = '') { const raw = String(value ?? '').trim(); return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback; }, safeColor(value, fallback = 'inherit') { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback; }, duration(value) { const number = Number(value); return Number.isFinite(number) ? Math.max(0, Math.min(10, number)) : 0.3; },
		iconName(entry) { return String(entry.iconName || '').toLowerCase(); }, itemStyle(entry) { const official = OFFICIAL_COLORS[this.iconName(entry)] || [this.settings.primaryColor || '#405de6', this.settings.secondaryColor || '#fff']; const primary = entry.colorMode === 'custom' ? this.safeColor(entry.primaryColor, official[0]) : official[0]; const secondary = entry.colorMode === 'custom' ? this.safeColor(entry.secondaryColor, official[1]) : official[1]; return { '--pb-social-primary': primary, '--pb-social-secondary': secondary, '--pb-social-hover-primary': this.safeColor(this.settings.primaryColorHover, '#4f46e5'), '--pb-social-hover-secondary': this.safeColor(this.settings.secondaryColorHover, '#fff'), '--pb-social-hover-animation': this.settings.hoverAnimation || 'none' }; }, safeUrl(value) { const raw = String(value || '').trim(); return raw && !raw.startsWith('//') && (/^(?:https?:|mailto:|tel:)/i.test(raw) || raw.startsWith('/') || raw.startsWith('#')) ? raw : '#'; }, linkRel(entry) { return entry.linkTarget === '_blank' ? 'noopener noreferrer' : null; }, customAttributes(entry) { const output = {}; const allowed = /^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i; (Array.isArray(entry.linkCustomAttributes) ? entry.linkCustomAttributes : []).forEach((attribute) => { const key = String(attribute?.key || attribute?.name || '').trim(); if (allowed.test(key)) output[key] = String(attribute?.value ?? ''); }); return output; }, safeSvg(value) { const raw = String(value || '').trim(); if (!raw || typeof DOMParser === 'undefined') return ''; const doc = new DOMParser().parseFromString(raw, 'image/svg+xml'); const root = doc.documentElement; if (!root || root.nodeName.toLowerCase() !== 'svg' || doc.querySelector('parsererror')) return ''; root.querySelectorAll('*').forEach((element) => { Array.from(element.attributes).forEach((attribute) => { const name = attribute.name.toLowerCase(); if (name.startsWith('on') || name === 'style' || name.includes('href')) element.removeAttribute(attribute.name); }); }); return root.outerHTML; },
	},
};
</script>

<style scoped>
.pb-social-icons { display: grid; align-items: center; justify-content: start; gap: var(--pb-social-row-gap) var(--pb-social-spacing); width: 100%; }
.pb-social-icons--align-center { justify-content: center; }
.pb-social-icons--align-right { justify-content: end; }
.pb-social-icons__item { display: inline-flex; align-items: center; justify-content: center; width: max-content; min-width: calc(var(--pb-social-size) + (var(--pb-social-padding) * 2)); min-height: calc(var(--pb-social-size) + (var(--pb-social-padding) * 2)); padding: var(--pb-social-padding); box-sizing: border-box; color: var(--pb-social-secondary); background: var(--pb-social-primary); border: var(--pb-social-border-width) var(--pb-social-border-style) var(--pb-social-border-color); border-radius: var(--pb-social-border-radius); text-decoration: none; font-size: var(--pb-social-size); line-height: 1; transition: color var(--pb-social-duration) ease, background-color var(--pb-social-duration) ease, transform var(--pb-social-duration) ease; }
.pb-social-icons--shape-circle .pb-social-icons__item { border-radius: 50%; }
.pb-social-icons--shape-square .pb-social-icons__item { border-radius: 0; }
.pb-social-icons__item:hover, .pb-social-icons__item:focus-visible { color: var(--pb-social-hover-secondary); background: var(--pb-social-hover-primary); animation: none; transform: translateY(-1px); }
.pb-social-icons__svg, .pb-social-icons__svg :deep(svg) { width: 1em; height: 1em; fill: currentColor; }
@keyframes pb-social-icons-pulse { 50% { transform: scale(1.08); } }
@media (prefers-reduced-motion: reduce) { .pb-social-icons__item { transition: none; } }
</style>
