<template>
	<div :class="rootClass" :style="rootStyle">
		<div v-if="isAccordionPreview" class="el-widget-tabs__accordion" data-pb-interactive="true" @click.stop>
			<template v-for="tab in tabItems" :key="'accordion-' + tab.id">
				<button
					type="button"
					class="el-widget-tabs__tab el-widget-tabs__accordion-title"
					:class="{ 'is-active': activeTab && activeTab.id===tab.id, 'has-icon': hasIcon(tab) }"
					:style="tabButtonStyle"
					data-pb-interactive="true"
					@click.stop="activateTab(tab.id)"
				>
					<img v-if="iconSource(tab)==='svg'" class="el-widget-tabs__icon-svg" :src="iconSvgDataUri(tab)" alt="" aria-hidden="true">
					<i v-else-if="iconSource(tab)==='library'" :class="iconClassFor(tab)" aria-hidden="true"></i>
					<span>{{ tabTitle(tab) }}</span>
				</button>
				<div v-if="activeTab && activeTab.id===tab.id" class="el-widget-tabs__pane el-widget-tabs__pane--accordion" :data-tab-panel="tab.id" data-pb-interactive="true" @click.stop>
					<slot></slot>
				</div>
			</template>
		</div>
		<div v-else class="el-widget-tabs__nav" :style="navStyle" data-pb-interactive="true" @click.stop>
			<button
				v-for="tab in tabItems"
				:key="tab.id"
				type="button"
				class="el-widget-tabs__tab"
				:class="{ 'is-active': activeTab && activeTab.id===tab.id, 'has-icon': hasIcon(tab) }"
				:style="tabButtonStyle"
				data-pb-interactive="true"
				@click.stop="activateTab(tab.id)"
			>
				<img v-if="iconSource(tab)==='svg'" class="el-widget-tabs__icon-svg" :src="iconSvgDataUri(tab)" alt="" aria-hidden="true">
				<i v-else-if="iconSource(tab)==='library'" :class="iconClassFor(tab)" aria-hidden="true"></i>
				<span>{{ tabTitle(tab) }}</span>
			</button>
		</div>
		<div v-if="!isAccordionPreview" class="el-widget-tabs__pane" :data-tab-panel="activeTab ? activeTab.id : ''" data-pb-interactive="true" @click.stop>
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: 'GeneralTabs',
	props: {
		item: {
			type: Object,
			required: true,
		},
		responsiveDevice: {
			type: String,
			default: 'desktop',
		},
		dynamicContext: { type: Object, default: () => ({}) },
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		tabItems() {
			return Array.isArray(this.item.tabItems) ? this.item.tabItems : [];
		},
		activeTab() {
			if (!this.tabItems.length) return null;
			const activeId = String(this.settings.activeTabId || '').trim();
			return this.tabItems.find((tab) => String(tab.id || '') === activeId) || this.tabItems[0];
		},
		direction() {
			const raw = String(this.responsiveValue('direction', 'row')).trim().toLowerCase();
			return ['row', 'row-reverse', 'column', 'column-reverse'].includes(raw) ? raw : 'row';
		},
		justify() {
			const raw = String(this.responsiveValue('justify', 'flex-start')).trim().toLowerCase();
			return ['flex-start', 'center', 'flex-end', 'stretch'].includes(raw) ? raw : 'flex-start';
		},
		alignTitle() {
			const raw = String(this.responsiveValue('alignTitle', 'center')).trim().toLowerCase();
			return ['left', 'center', 'right'].includes(raw) ? raw : 'center';
		},
		breakpoint() {
			const raw = String(this.settings.breakpoint || 'mobile').trim().toLowerCase();
			return ['mobile', 'tablet', 'none'].includes(raw) ? raw : 'mobile';
		},
		isAccordionPreview() {
			const device = String(this.responsiveDevice || 'desktop').trim().toLowerCase();
			if (this.breakpoint === 'none') return false;
			if (this.breakpoint === 'tablet') return device === 'tablet' || device === 'mobile';
			return device === 'mobile';
		},
		tabWidthCss() {
			const raw = String(this.settings.tabWidth || '').trim();
			if (!raw) return '';
			const num = Number(raw);
			if (!Number.isFinite(num) || num <= 0) return '';
			const unit = String(this.settings.tabWidthUnit || 'px').trim() === '%' ? '%' : 'px';
			return num + unit;
		},
		customClass() {
			const raw = String(this.settings.cssClass || '').trim();
			if (!raw) return '';
			return raw
				.split(/\s+/)
				.map((token) => token.replace(/^\.+/, '').trim())
				.filter(Boolean)
				.join(' ');
		},
		rootClass() {
			return [
				'el-widget-tabs',
				'is-direction-' + this.direction,
				'is-breakpoint-' + this.breakpoint,
				this.responsiveValue('horizontalScroll', false) ? 'is-scroll-enabled' : '',
				this.isAccordionPreview ? 'is-accordion-preview' : '',
				this.justify === 'stretch' ? 'is-justify-stretch' : '',
				'align-title-' + this.alignTitle,
				this.customClass,
			].filter(Boolean);
		},
		rootStyle() {
			const scrollEnabled = !!this.responsiveValue('horizontalScroll', false);
			const style = {
				'--pb-tabs-nav-wrap': scrollEnabled ? 'nowrap' : 'wrap',
				'--pb-tabs-nav-overflow-x': scrollEnabled ? 'auto' : 'visible',
				'--pb-tabs-gap': this.cssLength(this.responsiveValue('tabsGap', '8px'), '8px'),
				'--pb-tabs-content-distance': this.cssLength(this.responsiveValue('tabsContentDistance', '16px'), '16px'),
				'--pb-tabs-title-font-family': this.fontFamilyValue('tabsTitleFontFamily', 'inherit'),
				'--pb-tabs-title-font-size': this.cssLength(this.responsiveValue('tabsTitleFontSize', '14px'), '14px'),
				'--pb-tabs-title-font-weight': this.fontWeightValue('tabsTitleFontWeight', '500'),
				'--pb-tabs-title-line-height': this.lineHeightValue('tabsTitleLineHeight', '1.3em'),
				'--pb-tabs-title-letter-spacing': this.cssLength(this.responsiveValue('tabsTitleLetterSpacing', '0px'), '0px'),
				'--pb-tabs-title-word-spacing': this.cssLength(this.responsiveValue('tabsTitleWordSpacing', '0px'), '0px'),
				'--pb-tabs-title-text-transform': this.enumValue('tabsTitleTextTransform', ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'),
				'--pb-tabs-title-font-style': this.enumValue('tabsTitleFontStyle', ['normal', 'italic', 'oblique'], 'normal'),
				'--pb-tabs-title-text-decoration': this.enumValue('tabsTitleTextDecoration', ['none', 'underline', 'overline', 'line-through'], 'none'),
				'--pb-tabs-icon-size': this.cssLength(this.responsiveValue('tabsIconSize', '14px'), '14px'),
				'--pb-tabs-icon-spacing': this.cssLength(this.responsiveValue('tabsIconSpacing', '10px'), '10px'),
				'--pb-tabs-icon-direction': this.enumValue('tabsIconPosition', ['row', 'row-reverse', 'column', 'column-reverse'], 'row'),
				'--pb-tabs-content-background': this.backgroundValue('tabsContent', 'transparent'),
				'--pb-tabs-content-text-color': this.cssColor(this.responsiveValue('tabsContentTextColor', 'inherit'), 'inherit'),
				'--pb-tabs-content-border-style': this.enumValue('tabsContentBorderType', ['none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'none'),
				'--pb-tabs-content-border-width': this.boxValue('tabsContentBorderWidth', '0px'),
				'--pb-tabs-content-border-color': this.cssColor(this.responsiveValue('tabsContentBorderColor', 'transparent'), 'transparent'),
				'--pb-tabs-content-border-radius': this.boxValue('tabsContentBorderRadius', '0px'),
				'--pb-tabs-content-padding': this.boxValue('tabsContentPadding', '0px'),
				'--pb-tabs-content-font-family': this.fontFamilyValue('tabsContentFontFamily', 'inherit'),
				'--pb-tabs-content-font-size': this.cssLength(this.responsiveValue('tabsContentFontSize', '16px'), '16px'),
				'--pb-tabs-content-font-weight': this.fontWeightValue('tabsContentFontWeight', 'inherit'),
				'--pb-tabs-content-line-height': this.lineHeightValue('tabsContentLineHeight', '1.5em'),
				'--pb-tabs-content-letter-spacing': this.cssLength(this.responsiveValue('tabsContentLetterSpacing', '0px'), '0px'),
				'--pb-tabs-content-word-spacing': this.cssLength(this.responsiveValue('tabsContentWordSpacing', '0px'), '0px'),
				'--pb-tabs-content-text-transform': this.enumValue('tabsContentTextTransform', ['none', 'uppercase', 'lowercase', 'capitalize'], 'none'),
				'--pb-tabs-content-font-style': this.enumValue('tabsContentFontStyle', ['normal', 'italic', 'oblique'], 'normal'),
				'--pb-tabs-content-text-decoration': this.enumValue('tabsContentTextDecoration', ['none', 'underline', 'overline', 'line-through'], 'none'),
			};

			['Normal', 'Hover', 'Active'].forEach((state) => {
				const key = state.toLowerCase();
				style[`--pb-tabs-${key}-text-color`] = this.cssColor(this.responsiveValue(`tabs${state}TextColor`, key === 'active' ? '#ffffff' : '#4f5f78'), key === 'active' ? '#ffffff' : '#4f5f78');
				style[`--pb-tabs-${key}-icon-color`] = this.cssColor(this.responsiveValue(`tabs${state}IconColor`, key === 'active' ? '#ffffff' : '#4f5f78'), key === 'active' ? '#ffffff' : '#4f5f78');
				style[`--pb-tabs-${key}-background`] = this.backgroundValue(`tabs${state}`, key === 'active' ? '#4f5ec9' : '#f3f5fa');
				style[`--pb-tabs-${key}-border-style`] = this.enumValue(`tabs${state}BorderType`, ['default', 'none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'solid') === 'default' ? 'solid' : this.enumValue(`tabs${state}BorderType`, ['none', 'solid', 'double', 'dotted', 'dashed', 'groove'], 'solid');
				style[`--pb-tabs-${key}-border-width`] = this.boxValue(`tabs${state}BorderWidth`, '1px');
				style[`--pb-tabs-${key}-border-color`] = this.cssColor(this.responsiveValue(`tabs${state}BorderColor`, '#dde3ef'), '#dde3ef');
				style[`--pb-tabs-${key}-border-radius`] = this.boxValue(`tabs${state}BorderRadius`, '0px');
				style[`--pb-tabs-${key}-padding`] = this.boxValue(`tabs${state}Padding`, '14px 20px');
				style[`--pb-tabs-${key}-box-shadow`] = this.boxShadowValue(`tabs${state}`);
				style[`--pb-tabs-${key}-text-shadow`] = this.safeShadow(this.settings[`tabs${state}TextShadow`]);
				style[`--pb-tabs-${key}-stroke-width`] = this.cssLength(this.responsiveValue(`tabs${state}TextStrokeWidth`, '0px'), '0px');
				style[`--pb-tabs-${key}-stroke-color`] = this.cssColor(this.responsiveValue(`tabs${state}TextStrokeColor`, 'currentColor'), 'currentColor');
			});

			return style;
		},
		navStyle() {
			return {
				justifyContent: this.justify === 'stretch' ? 'flex-start' : this.justify,
			};
		},
		tabButtonStyle() {
			const style = {
				textAlign: this.alignTitle,
			};
			if (this.tabWidthCss && (this.direction === 'row' || this.direction === 'row-reverse')) {
				style.flex = '0 0 ' + this.tabWidthCss;
				style.width = this.tabWidthCss;
			}
			return style;
		},
	},
	methods: {
		responsiveValue(base, fallback = '') {
			const suffixes = this.responsiveDevice === 'mobile' ? ['Mobile', 'Tablet', ''] : (this.responsiveDevice === 'tablet' ? ['Tablet', ''] : ['']);
			for (const suffix of suffixes) {
				const value = this.settings[base + suffix];
				if (value !== '' && value !== null && value !== undefined) return value;
			}
			return fallback;
		},
		hasResponsiveValue(base) {
			const suffixes = this.responsiveDevice === 'mobile' ? ['Mobile', 'Tablet', ''] : (this.responsiveDevice === 'tablet' ? ['Tablet', ''] : ['']);
			return suffixes.some((suffix) => {
				const value = this.settings[base + suffix];
				return value !== '' && value !== null && value !== undefined;
			});
		},
		cssLength(value, fallback = '0px') {
			const raw = String(value ?? '').trim();
			if (!raw) return fallback;
			const tokens = raw.split(/\s+/);
			if (tokens.length < 1 || tokens.length > 4) return fallback;
			const normalized = tokens.map((token) => /^-?\d+(?:\.\d+)?$/.test(token) ? `${token}px` : (/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)$/i.test(token) ? token : ''));
			return normalized.every(Boolean) ? normalized.join(' ') : fallback;
		},
		cssColor(value, fallback = 'inherit') {
			const raw = String(value ?? '').trim();
			return raw && /^[#a-z0-9(),.%\s-]+$/i.test(raw) ? raw : fallback;
		},
		boxValue(base, fallback) {
			const sides = ['Top', 'Right', 'Bottom', 'Left'];
			if (!sides.some((side) => this.hasResponsiveValue(base + side))) return this.cssLength(this.responsiveValue(base, fallback), fallback);
			return sides.map((side) => this.cssLength(this.responsiveValue(base + side, fallback), fallback)).join(' ');
		},
		fontFamilyValue(base, fallback = 'inherit') {
			const raw = String(this.responsiveValue(base, fallback)).trim();
			return raw === 'inherit' || /^[A-Za-z0-9 _,'"-]+(?:\s*,\s*[A-Za-z0-9 _,'"-]+)*$/.test(raw) ? raw : fallback;
		},
		fontWeightValue(base, fallback = 'inherit') {
			const raw = String(this.responsiveValue(base, fallback)).trim();
			return raw === 'inherit' || /^(?:[1-9]00|normal|bold|lighter|bolder)$/.test(raw) ? raw : fallback;
		},
		lineHeightValue(base, fallback = '1.4em') {
			const raw = String(this.responsiveValue(base, fallback)).trim();
			return /^(?:normal|\d+(?:\.\d+)?(?:px|%|em|rem)?)$/i.test(raw) ? raw : fallback;
		},
		enumValue(base, values, fallback) {
			const raw = String(this.responsiveValue(base, fallback)).trim().toLowerCase();
			return values.includes(raw) ? raw : fallback;
		},
		backgroundValue(prefix, fallback = 'transparent') {
			if (String(this.settings[prefix + 'BackgroundType'] || 'classic') !== 'gradient') return this.cssColor(this.settings[prefix + 'BackgroundColor'], fallback);
			const one = this.cssColor(this.settings[prefix + 'GradientColorOne'], fallback);
			const two = this.cssColor(this.settings[prefix + 'GradientColorTwo'], fallback);
			if (this.settings[prefix + 'GradientType'] === 'radial') return `radial-gradient(at ${String(this.settings[prefix + 'GradientPosition'] || 'center center')}, ${one}, ${two})`;
			const angle = Math.min(360, Math.max(0, Number(this.settings[prefix + 'GradientAngle']) || 180));
			return `linear-gradient(${angle}deg, ${one}, ${two})`;
		},
		boxShadowValue(prefix) {
			if (!this.settings[prefix + 'BoxShadowEnabled']) return 'none';
			const parts = ['X', 'Y', 'Blur', 'Spread'].map((field) => this.cssLength(this.settings[prefix + 'BoxShadow' + field], '0px'));
			return `${parts.join(' ')} ${this.cssColor(this.settings[prefix + 'BoxShadowColor'], 'rgba(0,0,0,.2)')}${this.settings[prefix + 'BoxShadowInset'] ? ' inset' : ''}`;
		},
		safeShadow(value) { const raw=String(value||'none').trim(); return raw==='none'||/^-?\d+(?:\.\d+)?(?:px|em|rem)?\s+-?\d+(?:\.\d+)?(?:px|em|rem)?\s+\d+(?:\.\d+)?(?:px|em|rem)?(?:\s+[#a-z0-9(),.%\s-]+)?$/i.test(raw)?raw:'none'; },
		activateTab(tabId) {
			if (!this.item.settings || typeof this.item.settings !== 'object') return;
			this.item.settings.activeTabId = tabId;
		},
		resolveItemDynamic(tab, field, fallback) { const binding=String(tab?.dynamicBindings?.[field]||''); return binding&&Object.prototype.hasOwnProperty.call(this.dynamicContext,binding)&&this.dynamicContext[binding]!=null?this.dynamicContext[binding]:fallback; },
		tabTitle(tab) { return String(this.resolveItemDynamic(tab, 'title', tab?.title || 'Tab')); },
		iconSource(tab) { const value=String(tab?.iconSource||'').toLowerCase(); if(value==='svg'&&String(tab?.iconSvg||'').trim())return'svg';if(value==='library'&&String(tab?.iconClass||'').trim())return'library';return'none'; },
		hasIcon(tab) { return this.iconSource(tab) !== 'none'; },
		iconClassFor(tab) { return String(tab?.iconClass || '').trim(); },
		iconSvgDataUri(tab) { const markup=String(tab?.iconSvg||'').trim(); return markup.startsWith('<svg')?'data:image/svg+xml;charset=UTF-8,'+encodeURIComponent(markup):''; },
	},
};
</script>
