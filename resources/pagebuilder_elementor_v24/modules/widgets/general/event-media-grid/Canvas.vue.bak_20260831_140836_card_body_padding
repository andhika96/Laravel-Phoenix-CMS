<template>
	<div class="el-widget-event-media-grid pb-advanced-widget pb-event-media-grid" :class="rootClasses" :style="rootStyle" data-event-media-grid :data-responsive-device="responsiveDeviceClass">
		<p v-if="footerText && footerPosition==='top'" class="event-media-grid__footer" :style="footerStyle">{{footerText}}</p>
		<div class="event-media-grid__cards" :style="cardsStyle">
			<article v-for="(card,index) in cards" :key="card.id || index" class="event-media-grid__card" :class="cardClasses(card)" :style="cardStyle(card)">
				<div v-if="card.showImage" class="event-media-grid__image" :style="imageFrameStyle(card)">
					<img v-if="card.imagePresentation==='element' && safeImageUrl(card.imageUrl)" class="event-media-grid__image-element" :src="safeImageUrl(card.imageUrl)" :alt="card.imageAlt || card.title || ''" :style="imageStyle">
					<div v-else-if="card.imagePresentation==='background' && safeImageUrl(card.imageUrl)" class="event-media-grid__image-background" :style="imageBackgroundStyle(card)" role="img" :aria-label="card.imageAlt || card.title || 'Image'"></div>
					<div v-else class="event-media-grid__image-placeholder" :style="placeholderStyle" aria-hidden="true"><i class="far fa-image"></i></div>
				</div>
				<div class="event-media-grid__body" :style="cardBodyStyle">
					<div v-if="card.showIcon || card.title" class="event-media-grid__title-group" :style="iconTitleGroupStyle">
						<div v-if="card.showIcon" class="event-media-grid__icon" :style="iconStyle(card)">
							<span v-if="safeIconSvg(card)" class="event-media-grid__icon-svg" v-html="safeIconSvg(card)" aria-hidden="true"></span>
							<i v-else class="event-media-grid__icon-glyph" :class="safeIconClass(card)" aria-hidden="true"></i>
						</div>
						<h3 v-if="card.title" class="event-media-grid__title" :style="titleStyle(card)">{{card.title}}</h3>
					</div>
					<p v-if="card.description" class="event-media-grid__description" :style="descriptionStyle(card)">{{card.description}}</p>
				</div>
			</article>
		</div>
		<p v-if="footerText && footerPosition==='bottom'" class="event-media-grid__footer" :style="footerStyle">{{footerText}}</p>
	</div>
</template>

<script>
const GRID_COLUMNS = Array.from({ length: 12 }, (_, index) => String(index + 1));
const CONTENT_WIDTH_MODES = ['max', 'full'];
const FOOTER_POSITIONS = ['top', 'bottom'];
const ALIGNMENTS = ['left', 'center', 'right'];
const HEIGHT_MODES = ['auto', 'custom'];
const OBJECT_FITS = ['contain', 'cover', 'fill', 'none'];
const BORDER_TYPES = ['none', 'solid', 'dashed', 'dotted', 'double', 'groove'];
const SURFACES = ['inherit', 'light', 'dark', 'accent', 'custom'];

export default {
	name: 'EventMediaGridCanvas',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
		dynamicContext: { type: Object, default: () => ({}) },
	},
	computed: {
		settings() { return this.item.settings || {}; },
		responsiveDeviceClass() { return ['desktop', 'tablet', 'mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; },
		footerText() { return String(this.settings.footerText || ''); },
		footerPosition() { const value = String(this.responsiveValue('footerPosition', 'bottom')); return FOOTER_POSITIONS.includes(value) ? value : 'bottom'; },
		footerAlign() { const value = String(this.responsiveValue('footerAlign', 'left')); return ALIGNMENTS.includes(value) ? value : 'left'; },
		footerGap() { return this.cssDimension(this.responsiveValue('footerGap', '28px'), '28px'); },
		columns() { const value = String(this.responsiveValue('gridColumns', '3')); return GRID_COLUMNS.includes(value) ? value : '3'; },
		contentWidthMode() { const value = String(this.responsiveValue('gridContentWidthMode', 'max')); return CONTENT_WIDTH_MODES.includes(value) ? value : 'max'; },
		contentMaxWidth() { return this.cssDimension(this.responsiveValue('gridContentMaxWidth', '1636px'), '1636px'); },
		columnGap() { return this.cssDimension(this.responsiveValue('columnGap', '22px'), '22px'); },
		rowGap() { return this.cssDimension(this.responsiveValue('rowGap', '22px'), '22px'); },
		cardHeightMode() { const value = String(this.responsiveValue('cardHeightMode', 'custom')); return HEIGHT_MODES.includes(value) ? value : 'custom'; },
		cardHeight() { return this.cssDimension(this.responsiveValue('cardHeight', '472px'), '472px'); },
		cardAlignment() { const value = String(this.responsiveValue('cardAlignment', 'left')); return ALIGNMENTS.includes(value) ? value : 'left'; },
		cardContentGap() { return this.cssDimension(this.responsiveValue('cardContentGap', '24px'), '24px'); },
		iconTitleGap() { return this.cssDimension(this.responsiveValue('iconTitleGap', '32px'), '32px'); },
		cardIconSize() { return this.cssDimension(this.responsiveValue('cardIconSize', '38px'), '38px'); },
		imageWidth() { return this.cssDimension(this.responsiveValue('imageWidth', '100%'), '100%'); },
		imageHeight() { return this.cssDimension(this.responsiveValue('imageHeight', '195px'), '195px'); },
		imageObjectFit() { const value = String(this.settings.imageObjectFit || 'cover'); return OBJECT_FITS.includes(value) ? value : 'cover'; },
		cards() { return (Array.isArray(this.settings.cards) ? this.settings.cards : []).slice(0, 12).filter((card) => card && typeof card === 'object'); },
		rootClasses() { return ['grid-columns-' + this.columns, 'content-width-mode-' + this.contentWidthMode, 'card-height-mode-' + this.cardHeightMode, 'footer-position-' + this.footerPosition, 'footer-align-' + this.footerAlign]; },
		contentWidthStyle() { return { width: '100%', maxWidth: this.contentWidthMode === 'full' ? '100%' : this.contentMaxWidth, marginLeft: 'auto', marginRight: 'auto' }; },
		rootStyle() { return { boxSizing: 'border-box' }; },
		cardsStyle() { return { ...this.contentWidthStyle, display: 'grid', gridTemplateColumns: 'repeat(' + this.columns + ', minmax(0, 1fr))', columnGap: this.columnGap, rowGap: this.rowGap }; },
		footerStyle() { return { ...this.contentWidthStyle, marginTop: this.footerPosition === 'bottom' ? this.footerGap : '0', marginBottom: this.footerPosition === 'top' ? this.footerGap : '0', textAlign: this.footerAlign, color: this.safeColor(this.settings.footerColor, '#aab6c8'), fontFamily: this.safeFontFamily(this.settings.footerFontFamily), fontSize: this.cssDimension(this.responsiveValue('footerFontSize', '16px'), '16px'), fontWeight: this.safeWeight(this.settings.footerFontWeight, '400'), lineHeight: this.cssDimension(this.responsiveValue('footerLineHeight', '1.6em'), '1.6em'), letterSpacing: this.cssDimension(this.responsiveValue('footerLetterSpacing', '0px'), '0px'), wordSpacing: this.cssDimension(this.responsiveValue('footerWordSpacing', '0px'), '0px'), textTransform: this.safeEnum(this.settings.footerTextTransform, ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'), fontStyle: this.safeEnum(this.settings.footerFontStyle, ['normal', 'italic', 'oblique'], 'normal'), textDecoration: this.safeEnum(this.settings.footerTextDecoration, ['none', 'underline', 'overline', 'line-through'], 'none'), overflowWrap: 'anywhere' }; },
		iconTitleGroupStyle() { return { display: 'flex', flexDirection: 'column', minWidth: '0', maxWidth: '100%', gap: this.iconTitleGap }; },
		imageStyle() { return this.imageVisualStyle(); },
		placeholderStyle() { return { width: this.imageWidth, height: this.imageHeight, boxSizing: 'border-box', background: 'rgba(148,163,184,.10)', border: '1px dashed rgba(170,182,200,.45)', color: '#aab6c8' }; },
	},
	methods: {
		responsiveValue(base, fallback = '') { const device = ['tablet', 'mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; const keys = device === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (device === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of keys) { const value = this.settings[key]; if (value !== '' && value !== null && value !== undefined) return value; } return fallback; },
		safeEnum(value, allowed, fallback) { return allowed.includes(String(value)) ? String(value) : fallback; },
		flexAlignment(value) { return value === 'right' ? 'flex-end' : (value === 'center' ? 'center' : 'flex-start'); },
		cssDimension(value, fallback = '0px') { const raw = String(value ?? '').trim(); return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh|deg)?$/i.test(raw) ? raw : fallback; },
		safeColor(value, fallback = 'inherit') { const raw = String(value || '').trim(); return raw && /^[#a-z0-9(),.%\s/-]+$/i.test(raw) ? raw : fallback; },
		safeFontFamily(value) { const raw = String(value || '').trim(); return raw && !/[;{}<>"']/g.test(raw) ? raw.slice(0, 160) : 'inherit'; },
		safeWeight(value, fallback) { const raw = String(value || '').trim(); return /^(?:inherit|normal|bold|[1-9]00)$/.test(raw) ? raw : fallback; },
		safeImageUrl(value) { const raw = String(value || '').trim(); if (!raw || raw.startsWith('//') || /[\u0000-\u001f\u007f]/.test(raw) || /^(?:javascript|vbscript|data):/i.test(raw)) return ''; return /^(?:https?:\/\/|\/)/i.test(raw) ? raw : ''; },
		safeSvg(value) { const raw = String(value || '').trim(); if (!raw || raw.length > 20000 || !/^<svg\b/i.test(raw) || /<script\b|<foreignObject\b|<iframe\b|<object\b|<embed\b|<a\b|\bon[a-z]+\s*=|javascript:/i.test(raw)) return ''; return raw; },
		safeIconClass(card) { const raw = String(card?.iconClass || '').trim(); return /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(raw) ? raw : 'fal fa-star'; },
		safeIconSvg(card) { return card?.iconSource === 'svg' ? this.safeSvg(card.iconSvg) : ''; },
		imageFrameStyle() { return { display: 'flex', alignItems: 'stretch', justifyContent: this.flexAlignment(this.cardAlignment), width: '100%', minWidth: '0', maxWidth: '100%', boxSizing: 'border-box', overflow: 'hidden' }; },
		imageVisualStyle() { const borderType = this.safeEnum(this.settings.imageBorderType, BORDER_TYPES, 'none'); const padding = {}; const margin = {}; for (const side of ['Top', 'Right', 'Bottom', 'Left']) { padding['padding' + side] = this.cssDimension(this.responsiveValue('imagePadding' + side, '0px'), '0px'); margin['margin' + side] = this.cssDimension(this.responsiveValue('imageMargin' + side, '0px'), '0px'); } return { display: 'block', width: this.imageWidth, height: this.imageHeight, maxWidth: '100%', objectFit: this.imageObjectFit, boxSizing: 'border-box', borderStyle: borderType, borderWidth: borderType === 'none' ? '0' : this.cssDimension(this.responsiveValue('imageBorderWidth', '0px'), '0px'), borderColor: this.safeColor(this.settings.imageBorderColor, '#3a413f'), borderRadius: this.cssDimension(this.responsiveValue('imageBorderRadius', '0px'), '0px'), ...padding, ...margin }; },
		imageBackgroundStyle(card) { return { ...this.imageVisualStyle(), backgroundImage: 'url("' + this.safeImageUrl(card.imageUrl).replace(/"/g, '%22') + '")', backgroundPosition: this.safeEnum(this.settings.imageBackgroundPosition, ['center center', 'top center', 'bottom center', 'center left', 'center right'], 'center center'), backgroundSize: this.safeEnum(this.settings.imageBackgroundSize, ['cover', 'contain', 'auto', '100% 100%'], 'cover'), backgroundRepeat: this.safeEnum(this.settings.imageBackgroundRepeat, ['no-repeat', 'repeat', 'repeat-x', 'repeat-y'], 'no-repeat') }; },
		cardClasses(card) { const palette = this.surfacePalette(card); return ['surface-' + palette.surface, 'card-alignment-' + this.cardAlignment, 'card-height-mode-' + this.cardHeightMode]; },
		cardStyle(card) { const palette = this.surfacePalette(card); const borderType = this.safeEnum(this.settings.cardBorderType, BORDER_TYPES, 'solid'); const borderWidth = ['Top', 'Right', 'Bottom', 'Left'].map((side) => this.cssDimension(this.responsiveValue('cardBorderWidth' + side, '1px'), '1px')).join(' '); const radius = ['TL', 'TR', 'BR', 'BL'].map((corner) => this.cssDimension(this.responsiveValue('cardRadius' + corner, '0px'), '0px')).join(' '); return { display: 'flex', flexDirection: 'column', alignItems: 'stretch', minWidth: '0', maxWidth: '100%', minHeight: this.cardHeightMode === 'custom' ? this.cardHeight : '0', boxSizing: 'border-box', overflow: 'hidden', textAlign: this.cardAlignment, borderStyle: borderType, borderWidth, borderColor: palette.border, borderRadius: radius, backgroundColor: palette.background, '--event-media-grid-card-background-hover': this.safeColor(this.settings.cardBackgroundColorHover, palette.background), '--event-media-grid-card-border-hover': this.safeColor(this.settings.cardBorderColorHover, palette.border), '--event-media-grid-card-border-type-hover': this.safeEnum(this.settings.cardBorderTypeHover, BORDER_TYPES, borderType), '--event-media-grid-card-icon-color': palette.icon, '--event-media-grid-card-title-color': palette.title, '--event-media-grid-card-description-color': palette.description }; },
		cardBodyStyle() { const padding = {}; for (const side of ['Top', 'Right', 'Bottom', 'Left']) padding['padding' + side] = this.cssDimension(this.responsiveValue('cardPadding' + side, '0px'), '0px'); return { display: 'flex', flex: '1 1 auto', flexDirection: 'column', minWidth: '0', maxWidth: '100%', gap: this.cardContentGap, padding, overflowWrap: 'anywhere' }; },
		iconStyle(card) { return { color: this.surfacePalette(card).icon, fontSize: this.cardIconSize, lineHeight: '1' }; },
		titleStyle(card) { return this.typographyStyle('cardTitle', { color: this.surfacePalette(card).title }); },
		descriptionStyle(card) { return this.typographyStyle('cardDescription', { color: this.surfacePalette(card).description }); },
		typographyStyle(prefix, additions = {}) { const defaults = prefix === 'cardTitle' ? { size: '28px', line: '1.5em', weight: '400' } : { size: '18px', line: '1.8em', weight: '400' }; return { fontFamily: this.safeFontFamily(this.settings[prefix + 'FontFamily']), fontSize: this.cssDimension(this.responsiveValue(prefix + 'FontSize', defaults.size), defaults.size), fontWeight: this.safeWeight(this.settings[prefix + 'FontWeight'], defaults.weight), lineHeight: this.cssDimension(this.responsiveValue(prefix + 'LineHeight', defaults.line), defaults.line), letterSpacing: this.cssDimension(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'), wordSpacing: this.cssDimension(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'), textTransform: this.safeEnum(this.settings[prefix + 'TextTransform'], ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'), fontStyle: this.safeEnum(this.settings[prefix + 'FontStyle'], ['normal', 'italic', 'oblique'], 'normal'), textDecoration: this.safeEnum(this.settings[prefix + 'TextDecoration'], ['none', 'underline', 'overline', 'line-through'], 'none'), whiteSpace: 'pre-line', overflowWrap: 'anywhere', ...additions }; },
		surfacePalette(card) { const surface = SURFACES.includes(card?.surface) ? card.surface : 'inherit'; const presets = { inherit: { background: this.safeColor(this.settings.cardBackgroundColor, '#0a1e33'), border: this.safeColor(this.settings.cardBorderColor, '#3a413f'), icon: this.safeColor(this.settings.cardIconColor, '#d8ad5e'), title: this.safeColor(this.settings.cardTitleColor, '#f4efe4'), description: this.safeColor(this.settings.cardDescriptionColor, '#aab6c8') }, light: { background: '#f8f4ea', border: '#d8d2c4', icon: '#a37a2f', title: '#081d30', description: '#526173' }, dark: { background: '#091d31', border: '#3a413f', icon: '#d8ad5e', title: '#f4efe4', description: '#aab6c8' }, accent: { background: '#d8ad5e', border: '#d8ad5e', icon: '#081d30', title: '#081d30', description: '#26374a' }, custom: { background: this.safeColor(card?.customBackgroundColor, this.safeColor(this.settings.cardBackgroundColor, '#0a1e33')), border: this.safeColor(card?.customBorderColor, this.safeColor(this.settings.cardBorderColor, '#3a413f')), icon: this.safeColor(this.settings.cardIconColor, '#d8ad5e'), title: this.safeColor(this.settings.cardTitleColor, '#f4efe4'), description: this.safeColor(this.settings.cardDescriptionColor, '#aab6c8') } }; const preset = presets[surface]; return { surface, background: preset.background, border: preset.border, icon: this.safeColor(card?.iconColor, preset.icon), title: this.safeColor(card?.titleColor, preset.title), description: this.safeColor(card?.descriptionColor, preset.description) }; },
	},
};
</script>

<style scoped>
.pb-event-media-grid{display:flex;flex-direction:column;width:100%;min-width:0;max-width:100%;box-sizing:border-box;overflow-wrap:anywhere}.event-media-grid__cards{display:grid;min-width:0;max-width:100%;box-sizing:border-box}.event-media-grid__card{transition:background-color .25s ease,border-color .25s ease}.event-media-grid__card:hover{background-color:var(--event-media-grid-card-background-hover);border-color:var(--event-media-grid-card-border-hover);border-style:var(--event-media-grid-card-border-type-hover)}.event-media-grid__image{min-width:0}.event-media-grid__image-element,.event-media-grid__image-background{display:block;max-width:100%}.event-media-grid__image-background{background-color:rgba(148,163,184,.10);background-repeat:no-repeat}.event-media-grid__image-placeholder{display:flex;align-items:center;justify-content:center;max-width:100%;font-size:32px}.event-media-grid__body,.event-media-grid__title-group{min-width:0;max-width:100%;overflow-wrap:anywhere}.event-media-grid__icon{display:block;flex:0 0 auto}.event-media-grid__icon-glyph{display:inline-flex;align-items:center;justify-content:center;line-height:1}.event-media-grid__icon-svg{display:inline-flex;align-items:center;justify-content:center;line-height:1}.event-media-grid__icon-svg :deep(svg){display:block;width:1em;height:1em;max-width:100%;fill:currentColor}.event-media-grid__title,.event-media-grid__description,.event-media-grid__footer{display:block;max-width:100%;margin:0;overflow-wrap:anywhere}.event-media-grid__footer{box-sizing:border-box}@media(prefers-reduced-motion:reduce){.event-media-grid__card{transition:none}}
</style>
