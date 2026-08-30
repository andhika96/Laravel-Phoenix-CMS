<template>
	<div class="el-widget-event-highlights-grid pb-advanced-widget pb-event-highlights-grid" :class="rootClasses" :style="rootStyle" data-event-highlights-grid :data-responsive-device="responsiveDeviceClass">
		<div class="event-highlights-grid__header" :style="headerStyle">
			<div class="event-highlights-grid__header-content">
				<div class="event-highlights-grid__header-text" :style="headerTextStyle">
					<h2 v-if="textOrder==='heading-first' && heading" class="event-highlights-grid__header-heading" :class="headingClasses" :style="headingStyle">{{heading}}</h2>
					<p v-if="textOrder==='heading-first' && subheading" class="event-highlights-grid__header-subheading" :class="subheadingClasses" :style="subheadingStyle">{{subheading}}</p>
					<p v-if="textOrder!=='heading-first' && subheading" class="event-highlights-grid__header-subheading" :class="subheadingClasses" :style="subheadingStyle">{{subheading}}</p>
					<h2 v-if="textOrder!=='heading-first' && heading" class="event-highlights-grid__header-heading" :class="headingClasses" :style="headingStyle">{{heading}}</h2>
				</div>
			</div>
			<div v-if="showLink" class="event-highlights-grid__header-cta" :style="headerCtaStyle">
				<a v-if="linkText && safeLinkUrl" class="event-highlights-grid__header-link" :style="headerLinkStyle" v-bind="linkAttributes"><span>{{linkText}}</span><span v-if="showArrow" class="event-highlights-grid__arrow" aria-hidden="true">→</span></a>
				<span v-else-if="linkText" class="event-highlights-grid__header-link" :style="headerLinkStyle"><span>{{linkText}}</span><span v-if="showArrow" class="event-highlights-grid__arrow" aria-hidden="true">→</span></span>
			</div>
		</div>

		<div class="event-highlights-grid__cards" :style="cardsStyle">
			<article v-for="(card,index) in cards" :key="card.id || index" class="event-highlights-grid__card" :class="cardClasses(card)" :style="cardStyle(card)">
				<div class="event-highlights-grid__media" :style="mediaWrapperStyle(card)">
					<span v-if="safeMediaSvg(card)" class="event-highlights-grid__media-svg" v-html="safeMediaSvg(card)" aria-hidden="true"></span>
					<i v-else-if="card.mediaMode==='icon'" class="event-highlights-grid__media-icon" :class="safeIconClass(card)" :style="mediaIconStyle(card)" aria-hidden="true"></i>
					<img v-else-if="card.mediaMode==='image' && safeImageUrl(card.imageUrl)" class="event-highlights-grid__media-image" :src="safeImageUrl(card.imageUrl)" :alt="card.imageAlt || card.title || ''" :style="mediaImageStyle">
					<span v-else-if="card.mediaMode==='image'" class="event-highlights-grid__media-placeholder" aria-hidden="true"><i class="far fa-image"></i></span>
					<span v-else-if="card.mediaMode==='text'" class="event-highlights-grid__media-text" :style="mediaTextStyle(card)">{{card.mediaText}}</span>
				</div>
				<div class="event-highlights-grid__card-body" :style="cardBodyStyle">
					<h3 v-if="card.title" class="event-highlights-grid__title" :style="titleStyle(card)">{{card.title}}</h3>
					<p v-if="card.description" class="event-highlights-grid__description" :style="descriptionStyle(card)">{{card.description}}</p>
				</div>
			</article>
		</div>
	</div>
</template>

<script>
const DIRECTIONS = ['row', 'column'];
const TEXT_ORDERS = ['heading-first', 'subheading-first'];
const VERTICAL_POSITIONS = ['top', 'center', 'bottom'];
const HORIZONTAL_ALIGNMENTS = ['left', 'center', 'right'];
const GRID_COLUMNS = ['1', '2', '3', '4', '5'];
const CONTENT_WIDTH_MODES = ['max', 'full'];
const WIDTH_MODES = ['equal', 'soft-min'];
const HEIGHT_MODES = ['auto', 'custom'];
const MEDIA_POSITIONS = ['top', 'left', 'right'];
const ALIGNMENTS = ['left', 'center', 'right'];
const MEDIA_MODES = ['icon', 'image', 'text'];
const SURFACES = ['inherit', 'light', 'dark', 'accent', 'custom'];
const BORDER_TYPES = ['none', 'solid', 'dashed', 'dotted', 'double', 'groove'];
const OBJECT_FITS = ['contain', 'cover', 'fill', 'none'];
const TEXT_BOX_KEYS = Object.freeze({
	heading: { mode: 'headingBorderMode', widthMode: 'headingBorderWidthMode', type: 'headingBorderType', thickness: 'headingBorderThickness', color: 'headingBorderColor', radius: 'headingBorderRadius', padding: 'headingPadding', margin: 'headingMargin' },
	subheading: { mode: 'subheadingBorderMode', widthMode: 'subheadingBorderWidthMode', type: 'subheadingBorderType', thickness: 'subheadingBorderThickness', color: 'subheadingBorderColor', radius: 'subheadingBorderRadius', padding: 'subheadingPadding', margin: 'subheadingMargin' },
});

export default {
	name: 'GeneralEventHighlightsGrid',
	props: {
		item: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
		dynamicContext: { type: Object, default: () => ({}) },
	},
	computed: {
		settings() { return this.item.settings || {}; },
		heading() { return String(this.resolveDynamicValue('heading', this.settings.heading || '')); },
		subheading() { return String(this.resolveDynamicValue('subheading', this.settings.subheading || '')); },
		linkText() { return String(this.resolveDynamicValue('linkText', this.settings.linkText || '')); },
		showLink() { return this.settings.showLink !== false && this.settings.showLink !== 'false' && this.settings.showLink !== 0 && this.settings.showLink !== '0'; },
		textOrder() { const value = String(this.settings.textOrder || 'subheading-first'); return TEXT_ORDERS.includes(value) ? value : 'subheading-first'; },
		showArrow() { return this.settings.showArrow !== false && this.settings.showArrow !== 'false' && this.settings.showArrow !== 0 && this.settings.showArrow !== '0'; },
		responsiveDeviceClass() { return ['desktop', 'tablet', 'mobile'].includes(this.responsiveDevice) ? this.responsiveDevice : 'desktop'; },
		direction() { const value = String(this.responsiveValue('layoutDirection', 'row')); return DIRECTIONS.includes(value) ? value : 'row'; },
		verticalPosition() { const value = String(this.responsiveValue('linkVerticalPosition', 'bottom')); return VERTICAL_POSITIONS.includes(value) ? value : 'bottom'; },
		horizontalAlign() { const value = String(this.responsiveValue('linkHorizontalAlign', 'right')); return HORIZONTAL_ALIGNMENTS.includes(value) ? value : 'right'; },
		textGap() { return this.cssDimension(this.responsiveValue('textGap', '12px'), '12px'); },
		columns() { const value = String(this.responsiveValue('gridColumns', '5')); return GRID_COLUMNS.includes(value) ? value : '5'; },
		contentWidthMode() { const value = String(this.responsiveValue('gridContentWidthMode', 'max')); return CONTENT_WIDTH_MODES.includes(value) ? value : 'max'; },
		contentMaxWidth() { return this.cssDimension(this.responsiveValue('gridContentMaxWidth', '1636px'), '1636px'); },
		columnGap() { return this.cssDimension(this.responsiveValue('columnGap', '20px'), '20px'); },
		rowGap() { return this.cssDimension(this.responsiveValue('rowGap', '20px'), '20px'); },
		headerCardsGap() { return this.cssDimension(this.responsiveValue('headerCardsGap', '64px'), '64px'); },
		cardWidthMode() { const value = String(this.responsiveValue('cardWidthMode', 'equal')); return WIDTH_MODES.includes(value) ? value : 'equal'; },
		cardMinWidth() { return this.cssDimension(this.responsiveValue('cardMinWidth', '180px'), '180px'); },
		cardHeightMode() { const value = String(this.responsiveValue('cardHeightMode', 'custom')); return HEIGHT_MODES.includes(value) ? value : 'custom'; },
		cardHeight() { return this.cssDimension(this.responsiveValue('cardHeight', '385px'), '385px'); },
		cardMediaPosition() { const value = String(this.responsiveValue('cardMediaPosition', 'top')); return MEDIA_POSITIONS.includes(value) ? value : 'top'; },
		cardAlignment() { const value = String(this.responsiveValue('cardAlignment', 'left')); return ALIGNMENTS.includes(value) ? value : 'left'; },
		cardMediaGap() { return this.cssDimension(this.responsiveValue('cardMediaGap', '32px'), '32px'); },
		cardContentGap() { return this.cssDimension(this.responsiveValue('cardContentGap', '24px'), '24px'); },
		cardMediaSize() { return this.cssDimension(this.responsiveValue('cardMediaSize', '38px'), '38px'); },
		cardImageWidth() { return this.cssDimension(this.responsiveValue('cardImageWidth', '64px'), '64px'); },
		cardImageHeight() { return this.cssDimension(this.responsiveValue('cardImageHeight', '64px'), '64px'); },
		cardImageObjectFit() { const value = String(this.settings.cardImageObjectFit || 'contain'); return OBJECT_FITS.includes(value) ? value : 'contain'; },
		cards() { return (Array.isArray(this.settings.cards) ? this.settings.cards : []).slice(0, 5).filter((card) => card && typeof card === 'object'); },
		safeLinkUrl() { return this.safeLink(this.resolveDynamicValue('linkUrl', this.settings.linkUrl || '')); },
		linkAttributes() {
			const attrs = {};
			if (this.safeLinkUrl) attrs.href = this.safeLinkUrl;
			if (this.settings.linkTarget === '_blank') { attrs.target = '_blank'; attrs.rel = 'noopener noreferrer'; }
			if (this.settings.linkNofollow) attrs.rel = [attrs.rel, 'nofollow'].filter(Boolean).join(' ');
			Object.assign(attrs, this.safeCustomAttributes);
			return attrs;
		},
		safeCustomAttributes() {
			const output = {};
			(Array.isArray(this.settings.linkCustomAttributes) ? this.settings.linkCustomAttributes : []).forEach((attribute) => {
				const key = String(attribute?.key || attribute?.name || '').trim();
				if (/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i.test(key)) output[key] = String(attribute?.value ?? '');
			});
			return output;
		},
		rootClasses() { return ['header-direction-' + this.direction, 'header-link-position-' + this.verticalPosition, 'header-link-align-' + this.horizontalAlign, 'text-order-' + this.textOrder, 'grid-columns-' + this.columns, 'card-width-mode-' + this.cardWidthMode, 'card-height-mode-' + this.cardHeightMode]; },
		headingClasses() { return this.textBoxClasses('heading'); },
		subheadingClasses() { return this.textBoxClasses('subheading'); },
		rootStyle() {
			return {
				'--event-highlights-grid-header-cards-gap': this.headerCardsGap,
				// AdvancedControls applies widget padding/background/border on the BuilderNode shell.
				// Keeping those styles out of the inner widget prevents double spacing in Canvas.
				boxSizing: 'border-box',
			};
		},
		contentWidthStyle() { return { width: '100%', maxWidth: this.contentWidthMode === 'full' ? '100%' : this.contentMaxWidth, marginLeft: 'auto', marginRight: 'auto' }; },
		headerStyle() { return { ...this.contentWidthStyle, flexDirection: this.direction, gap: this.textGap }; },
		headerTextStyle() { return { gap: this.textGap }; },
		headerCtaStyle() { return this.direction === 'row' ? { alignSelf: this.flexAlignment(this.verticalPosition) } : { alignSelf: this.flexAlignment(this.horizontalAlign), textAlign: this.horizontalAlign }; },
		headingStyle() { return this.textBoxStyle('heading', this.typographyStyle('heading', { color: this.safeColor(this.settings.headingColor, '#f4efe4') })); },
		subheadingStyle() { return this.textBoxStyle('subheading', this.typographyStyle('subheading', { color: this.safeColor(this.settings.subheadingColor, '#d8ad5e') })); },
		headerLinkStyle() { return this.typographyStyle('link', { color: this.safeColor(this.settings.linkColor, '#d8ad5e'), display: 'inline-flex', alignItems: 'center', gap: '.55em', maxWidth: '100%', overflowWrap: 'anywhere', textDecoration: 'none' }); },
		cardsStyle() { return { ...this.contentWidthStyle, display: 'grid', gridTemplateColumns: 'repeat(' + this.columns + ', minmax(0, 1fr))', columnGap: this.columnGap, rowGap: this.rowGap, marginTop: this.headerCardsGap }; },
		cardBodyStyle() { return { gap: this.cardContentGap }; },
		mediaImageStyle() { return { width: this.cardImageWidth, height: this.cardImageHeight, objectFit: this.cardImageObjectFit }; },
	},
	methods: {
		resolveDynamicValue(field, fallback) {
			const binding = String(this.settings.dynamicBindings?.[field] || '');
			return binding && Object.prototype.hasOwnProperty.call(this.dynamicContext, binding) && this.dynamicContext[binding] != null ? this.dynamicContext[binding] : fallback;
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
		flexAlignment(value) { return value === 'left' || value === 'top' ? 'flex-start' : (value === 'right' || value === 'bottom' ? 'flex-end' : 'center'); },
		cssDimension(value, fallback = '0px') {
			const raw = String(value ?? '').trim();
			return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh|deg)?$/i.test(raw) ? raw : fallback;
		},
		safeColor(value, fallback = 'inherit') {
			const raw = String(value || '').trim();
			return raw && /^[#a-z0-9(),.%\s/-]+$/i.test(raw) ? raw : fallback;
		},
		safeFontFamily(value) {
			const raw = String(value || '').trim();
			return raw && !/[;{}<>"']/g.test(raw) ? raw.slice(0, 160) : 'inherit';
		},
		safeLink(value) {
			const raw = String(value || '').trim();
			if (!raw || raw.startsWith('//') || /[\u0000-\u001f\u007f]/.test(raw) || /^(?:javascript|vbscript|data):/i.test(raw)) return '';
			return /^(?:https?:\/\/|mailto:|tel:|\/|#)/i.test(raw) ? raw : '';
		},
		safeImageUrl(value) {
			const raw = String(value || '').trim();
			if (!raw || raw.startsWith('//') || /[\u0000-\u001f\u007f]/.test(raw) || /^(?:javascript|vbscript|data):/i.test(raw)) return '';
			return /^(?:https?:\/\/|\/)/i.test(raw) ? raw : '';
		},
		safeSvg(value) {
			const raw = String(value || '').trim();
			if (!raw || raw.length > 20000 || !/^<svg\b/i.test(raw) || /<script\b|<foreignObject\b|<iframe\b|<object\b|<embed\b|<a\b|\bon[a-z]+\s*=|javascript:/i.test(raw)) return '';
			return raw;
		},
		safeIconClass(card) {
			const raw = String(card?.mediaIconClass || '').trim();
			if (/^(?:fas|far|fab|fal|fad)\s+fa-golf-ball-tee$/i.test(raw)) return 'fal fa-golf-ball';
			return /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(raw) ? raw : 'fal fa-star';
		},
		safeMediaSvg(card) { return card?.mediaMode === 'icon' && card?.mediaIconSource === 'svg' ? this.safeSvg(card.mediaIconSvg) : ''; },
		textBoxClasses(prefix) {
			const keys = TEXT_BOX_KEYS[prefix] || TEXT_BOX_KEYS.heading;
			const modeValue = String(this.responsiveValue(keys.mode, 'none'));
			const widthValue = String(this.responsiveValue(keys.widthMode, 'content'));
			const mode = ['none', 'box', 'underline'].includes(modeValue) ? modeValue : 'none';
			const width = ['content', 'full'].includes(widthValue) ? widthValue : 'content';
			return ['border-mode-' + mode, 'border-width-mode-' + width];
		},
		typographyStyle(prefix, additions = {}) {
			const defaults = prefix === 'heading' ? { size: '56px', lineHeight: '1.05em' } : (prefix === 'cardTitle' ? { size: '28px', lineHeight: '1.5em' } : (prefix === 'cardDescription' ? { size: '18px', lineHeight: '1.8em' } : { size: '14px', lineHeight: '1.2em' }));
			const fallbackWeight = prefix === 'cardDescription' ? '400' : '700';
			const weight = String(this.settings[prefix + 'FontWeight'] || fallbackWeight);
			return {
				fontFamily: this.safeFontFamily(this.settings[prefix + 'FontFamily']),
				fontSize: this.cssDimension(this.responsiveValue(prefix + 'FontSize', defaults.size), defaults.size),
				fontWeight: /^(?:inherit|normal|bold|[1-9]00)$/.test(weight) ? weight : '400',
				lineHeight: this.cssDimension(this.responsiveValue(prefix + 'LineHeight', defaults.lineHeight), defaults.lineHeight),
				letterSpacing: this.cssDimension(this.responsiveValue(prefix + 'LetterSpacing', '0px'), '0px'),
				wordSpacing: this.cssDimension(this.responsiveValue(prefix + 'WordSpacing', '0px'), '0px'),
				textTransform: ['none', 'uppercase', 'lowercase', 'capitalize'].includes(this.settings[prefix + 'TextTransform']) ? this.settings[prefix + 'TextTransform'] : 'none',
				fontStyle: ['normal', 'italic', 'oblique'].includes(this.settings[prefix + 'FontStyle']) ? this.settings[prefix + 'FontStyle'] : 'normal',
				textDecoration: ['none', 'underline', 'overline', 'line-through'].includes(this.settings[prefix + 'TextDecoration']) ? this.settings[prefix + 'TextDecoration'] : 'none',
				...additions,
			};
		},
		textBoxStyle(prefix, additions = {}) {
			const keys = TEXT_BOX_KEYS[prefix] || TEXT_BOX_KEYS.heading;
			const modeValue = String(this.responsiveValue(keys.mode, 'none'));
			const widthValue = String(this.responsiveValue(keys.widthMode, 'content'));
			const typeValue = String(this.responsiveValue(keys.type, 'solid'));
			const mode = ['none', 'box', 'underline'].includes(modeValue) ? modeValue : 'none';
			const width = ['content', 'full'].includes(widthValue) ? widthValue : 'content';
			const type = ['solid', 'dashed', 'dotted', 'double', 'groove'].includes(typeValue) ? typeValue : 'solid';
			const thickness = this.cssDimension(this.responsiveValue(keys.thickness, '1px'), '1px');
			return {
				boxSizing: 'border-box',
				width: width === 'full' ? '100%' : 'max-content',
				maxWidth: '100%',
				paddingTop: this.cssDimension(this.responsiveValue(keys.padding + 'Top', '0px'), '0px'),
				paddingRight: this.cssDimension(this.responsiveValue(keys.padding + 'Right', '0px'), '0px'),
				paddingBottom: this.cssDimension(this.responsiveValue(keys.padding + 'Bottom', '0px'), '0px'),
				paddingLeft: this.cssDimension(this.responsiveValue(keys.padding + 'Left', '0px'), '0px'),
				marginTop: this.cssDimension(this.responsiveValue(keys.margin + 'Top', '0px'), '0px'),
				marginRight: this.cssDimension(this.responsiveValue(keys.margin + 'Right', '0px'), '0px'),
				marginBottom: this.cssDimension(this.responsiveValue(keys.margin + 'Bottom', '0px'), '0px'),
				marginLeft: this.cssDimension(this.responsiveValue(keys.margin + 'Left', '0px'), '0px'),
				borderStyle: mode === 'none' ? 'none' : type,
				borderColor: mode === 'none' ? 'transparent' : this.safeColor(this.responsiveValue(keys.color, '#d8ad5e'), '#d8ad5e'),
				borderWidth: mode === 'box' ? thickness : (mode === 'underline' ? '0 0 ' + thickness + ' 0' : '0'),
				borderRadius: mode === 'none' ? '0' : this.cssDimension(this.responsiveValue(keys.radius, '0px'), '0px'),
				...additions,
			};
		},
		surfacePalette(card) {
			const surface = SURFACES.includes(card?.surface) ? card.surface : 'inherit';
			const presets = {
				inherit: { background: this.safeColor(this.settings.cardBackgroundColor, '#0a1e33'), border: this.safeColor(this.settings.cardBorderColor, '#3a413f'), media: this.safeColor(this.settings.cardMediaColor, '#d8ad5e'), title: this.safeColor(this.settings.cardTitleColor, '#f4efe4'), description: this.safeColor(this.settings.cardDescriptionColor, '#aab6c8') },
				light: { background: '#f8f4ea', border: '#d8d2c4', media: '#a37a2f', title: '#081d30', description: '#526173' },
				dark: { background: '#091d31', border: '#3a413f', media: '#d8ad5e', title: '#f4efe4', description: '#aab6c8' },
				accent: { background: '#d8ad5e', border: '#d8ad5e', media: '#081d30', title: '#081d30', description: '#26374a' },
				custom: { background: this.safeColor(card?.customBackgroundColor, this.safeColor(this.settings.cardBackgroundColor, '#0a1e33')), border: this.safeColor(card?.customBorderColor, this.safeColor(this.settings.cardBorderColor, '#3a413f')), media: this.safeColor(this.settings.cardMediaColor, '#d8ad5e'), title: this.safeColor(this.settings.cardTitleColor, '#f4efe4'), description: this.safeColor(this.settings.cardDescriptionColor, '#aab6c8') },
			};
			const preset = presets[surface];
			return {
				surface,
				background: this.safeColor(card?.surface === 'custom' ? card.customBackgroundColor : '', preset.background),
				border: this.safeColor(card?.surface === 'custom' ? card.customBorderColor : '', preset.border),
				media: this.safeColor(card?.mediaColor, preset.media),
				title: this.safeColor(card?.titleColor, preset.title),
				description: this.safeColor(card?.descriptionColor, preset.description),
			};
		},
		cardClasses(card) { const palette = this.surfacePalette(card); return ['surface-' + palette.surface, 'media-position-' + this.cardMediaPosition, 'card-alignment-' + this.cardAlignment, 'card-width-mode-' + this.cardWidthMode, 'card-height-mode-' + this.cardHeightMode]; },
		cardStyle(card) {
			const palette = this.surfacePalette(card);
			const borderType = BORDER_TYPES.includes(this.settings.cardBorderType) ? this.settings.cardBorderType : 'solid';
			const borderWidth = ['Top', 'Right', 'Bottom', 'Left'].map((side) => this.cssDimension(this.responsiveValue('cardBorderWidth' + side, '1px'), '1px')).join(' ');
			const radius = ['TL', 'TR', 'BR', 'BL'].map((corner) => this.cssDimension(this.responsiveValue('cardRadius' + corner, '0px'), '0px')).join(' ');
			const padding = {};
			['Top', 'Right', 'Bottom', 'Left'].forEach((side) => { padding['padding' + side] = this.cssDimension(this.responsiveValue('cardPadding' + side, '40px'), '40px'); });
			return {
				flexDirection: this.cardMediaPosition === 'right' ? 'row-reverse' : (this.cardMediaPosition === 'left' ? 'row' : 'column'),
				alignItems: this.cardMediaPosition === 'top' ? this.flexAlignment(this.cardAlignment) : 'stretch',
				textAlign: this.cardAlignment,
				minWidth: this.cardWidthMode === 'soft-min' ? 'min(100%, ' + this.cardMinWidth + ')' : '0',
				minHeight: this.cardHeightMode === 'custom' ? this.cardHeight : '0',
				borderStyle: borderType,
				borderWidth,
				borderColor: palette.border,
				backgroundColor: palette.background,
				borderRadius: radius,
				gap: this.cardMediaGap,
				'--event-highlights-grid-card-background-hover': this.safeColor(this.settings.cardBackgroundColorHover, palette.background),
				'--event-highlights-grid-card-border-hover': this.safeColor(this.settings.cardBorderColorHover, palette.border),
				'--event-highlights-grid-card-border-type-hover': BORDER_TYPES.includes(this.settings.cardBorderTypeHover) ? this.settings.cardBorderTypeHover : borderType,
				'--event-highlights-grid-card-media-color': palette.media,
				'--event-highlights-grid-card-title-color': palette.title,
				'--event-highlights-grid-card-description-color': palette.description,
				...padding,
			};
		},
		mediaWrapperStyle(card) { return { color: this.surfacePalette(card).media }; },
		mediaIconStyle(card) { return { fontSize: this.cardMediaSize, color: this.surfacePalette(card).media }; },
		mediaTextStyle(card) { return { ...this.typographyStyle('cardMedia', { color: this.surfacePalette(card).media, fontSize: this.cardMediaSize }), fontSize: this.cardMediaSize }; },
		titleStyle(card) { return this.typographyStyle('cardTitle', { color: this.surfacePalette(card).title }); },
		descriptionStyle(card) { return this.typographyStyle('cardDescription', { color: this.surfacePalette(card).description }); },
	},
};
</script>

<style scoped>
.pb-event-highlights-grid{display:flex;flex-direction:column;width:100%;min-width:0;max-width:100%;box-sizing:border-box;overflow-wrap:anywhere}.event-highlights-grid__header{display:flex;align-items:stretch;width:100%;min-width:0;max-width:100%}.event-highlights-grid__header-content{flex:1 1 auto;min-width:0;max-width:100%}.event-highlights-grid__header-text{display:flex;flex-direction:column;min-width:0;max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__header-heading,.event-highlights-grid__header-subheading{display:block;max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__header-cta{display:flex;flex:0 1 auto;min-width:0;max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__header-link{max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__arrow{display:inline-block;flex:0 0 auto;line-height:1}.event-highlights-grid__cards{display:grid;width:100%;min-width:0;max-width:100%;box-sizing:border-box}.event-highlights-grid__card{display:flex;min-width:0;max-width:100%;box-sizing:border-box;overflow-wrap:anywhere;transition:background-color .25s ease,border-color .25s ease}.event-highlights-grid__card:hover{background-color:var(--event-highlights-grid-card-background-hover);border-color:var(--event-highlights-grid-card-border-hover);border-style:var(--event-highlights-grid-card-border-type-hover)}.event-highlights-grid__media{display:flex;flex:0 0 auto;align-items:center;justify-content:center;min-width:0;max-width:100%;line-height:1}.event-highlights-grid__media-icon{display:inline-flex;align-items:center;justify-content:center;line-height:1}.event-highlights-grid__media-svg{display:inline-flex;align-items:center;justify-content:center;max-width:100%;line-height:1}.event-highlights-grid__media-svg :deep(svg){display:block;width:1em;height:1em;max-width:100%;fill:currentColor}.event-highlights-grid__media-image{display:block;max-width:100%;border:0}.event-highlights-grid__media-placeholder{display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;color:inherit;opacity:.65}.event-highlights-grid__media-text{display:block;max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__card-body{display:flex;flex:1 1 auto;flex-direction:column;min-width:0;max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__title,.event-highlights-grid__description{display:block;max-width:100%;overflow-wrap:anywhere}.event-highlights-grid__title,.event-highlights-grid__description{margin-top:0;margin-right:0;margin-bottom:0;margin-left:0}@media(prefers-reduced-motion:reduce){.event-highlights-grid__card{transition:none}}
</style>
