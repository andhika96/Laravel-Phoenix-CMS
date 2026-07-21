<template>
	<div class="el-widget-accordion" :class="rootClass" :style="rootStyle" data-pb-interactive="true" @click.stop>
		<div v-for="item in items" :key="item.id" class="el-widget-accordion__item" :class="{ 'is-active': isVisuallyExpanded(item.id) }">
			<component :is="titleTag" class="el-widget-accordion__heading">
				<button
					type="button"
					class="el-widget-accordion__header"
					:id="headerId(item.id)"
					:aria-expanded="isVisuallyExpanded(item.id) ? 'true' : 'false'"
					:aria-controls="panelId(item.id)"
					data-pb-interactive="true"
					@click.stop="toggleItem(item)"
				>
					<span v-if="iconPosition==='start' && iconSource(item.id)!=='none'" class="el-widget-accordion__icon" aria-hidden="true">
						<img v-if="iconSource(item.id)==='svg'" :src="svgIconDataUri(item.id)" alt="">
						<i v-else :class="iconClass(item.id)"></i>
					</span>
					<span class="el-widget-accordion__title">{{ item.title || 'Item' }}</span>
					<span v-if="iconPosition==='end' && iconSource(item.id)!=='none'" class="el-widget-accordion__icon" aria-hidden="true">
						<img v-if="iconSource(item.id)==='svg'" :src="svgIconDataUri(item.id)" alt="">
						<i v-else :class="iconClass(item.id)"></i>
					</span>
				</button>
			</component>
			<div
				ref="panels"
				class="el-widget-accordion__panel"
				:class="panelClass(item.id)"
				:id="panelId(item.id)"
				:data-accordion-item-id="item.id"
				:aria-labelledby="headerId(item.id)"
				:aria-hidden="isVisuallyExpanded(item.id) ? 'false' : 'true'"
				:style="panelStyle(item.id)"
				@animationend="onPanelAnimationEnd(item.id, $event)"
			>
				<div class="el-widget-accordion__panel-inner">
					<slot name="panel" :item="item" :expanded="isVisuallyExpanded(item.id)"></slot>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'AdvancedAccordion',
	props: {
		item: { type: Object, required: true },
		expandedItemIds: { type: Array, default: () => [] },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	emits: ['toggle-item'],
	data() {
		return {
			panelHeights: {},
			panelAnimationStates: {},
			panelAnimationMetrics: {},
			pendingToggleItemId: '',
			skipNextExpandedWatch: false,
		};
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		items() {
			return Array.isArray(this.item.accordionItems) ? this.item.accordionItems : [];
		},
		titleTag() {
			const tag = String(this.settings.titleTag || 'div').toLowerCase();
			return ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].includes(tag) ? tag : 'div';
		},
		iconPosition() {
			const suffix = this.responsiveDevice === 'tablet' ? 'Tablet' : (this.responsiveDevice === 'mobile' ? 'Mobile' : '');
			const value = String(this.settings['iconPosition' + suffix] || this.settings.iconPosition || 'start').toLowerCase();
			return value === 'end' ? 'end' : 'start';
		},
		animationDuration() {
			const duration = Number(this.settings.animationDuration);
			return Number.isFinite(duration) ? Math.min(5000, Math.max(0, duration)) : 400;
		},
		rootClass() {
			const classes = String(this.settings.cssClass || '').trim().split(/\s+/).filter(Boolean);
			classes.push('is-item-position-' + this.itemPosition);
			classes.push('is-icon-position-' + this.iconPosition);
			return classes;
		},
		rootStyle() {
			const style = {
				'--accordion-animation-duration': this.animationDuration + 'ms',
				'--accordion-item-gap': this.cssToken(this.responsiveValue('accordionItemGap', '0px'), '0px'),
				'--accordion-content-distance': this.cssToken(this.responsiveValue('accordionContentDistance', '0px'), '0px'),
				'--accordion-border-radius': this.cssToken(this.responsiveValue('accordionBorderRadius', '0px'), '0px'),
				'--accordion-padding': this.cssToken(this.responsiveValue('accordionPadding', '0px'), '0px'),
				'--accordion-header-font-family': String(this.settings.headerFontFamily || 'inherit'),
				'--accordion-header-font-size': this.cssToken(this.responsiveValue('headerFontSize', '16px'), '16px'),
				'--accordion-header-font-weight': String(this.settings.headerFontWeight || '600'),
				'--accordion-header-line-height': this.lineHeightToken(this.responsiveValue('headerLineHeight', '1.4'), '1.4'),
				'--accordion-header-letter-spacing': this.cssToken(this.responsiveValue('headerLetterSpacing', '0px'), '0px'),
				'--accordion-header-word-spacing': this.cssToken(this.responsiveValue('headerWordSpacing', '0px'), '0px'),
				'--accordion-header-text-transform': String(this.settings.headerTextTransform || 'none'),
				'--accordion-header-font-style': String(this.settings.headerFontStyle || 'normal'),
				'--accordion-header-text-decoration': String(this.settings.headerTextDecoration || 'none'),
				'--accordion-icon-size': this.cssToken(this.responsiveValue('headerIconSize', '16px'), '16px'),
				'--accordion-icon-spacing': this.cssToken(this.responsiveValue('headerIconSpacing', '12px'), '12px'),
				'--accordion-content-background': this.contentBackground(),
				'--accordion-content-border-style': this.borderStyle(this.settings.contentBorderType),
				'--accordion-content-border-width': this.cssToken(this.settings.contentBorderWidth, '0px'),
				'--accordion-content-border-color': String(this.settings.contentBorderColor || 'transparent'),
				'--accordion-content-radius': this.cssToken(this.responsiveValue('contentBorderRadius', '0px'), '0px'),
				'--accordion-content-padding': this.cssToken(this.responsiveValue('contentPadding', '20px'), '20px'),
			};
			['Normal', 'Hover', 'Active'].forEach((suffix) => {
				const state = suffix.toLowerCase();
				style['--accordion-background-' + state] = this.stateBackground(suffix);
				style['--accordion-border-style-' + state] = this.borderStyle(this.settings['accordionBorderType' + suffix]);
				style['--accordion-border-width-' + state] = this.cssToken(this.settings['accordionBorderWidth' + suffix], '0px');
				style['--accordion-border-color-' + state] = String(this.settings['accordionBorderColor' + suffix] || 'transparent');
				style['--accordion-header-' + state + '-title-color'] = String(this.settings['headerTitleColor' + suffix] || 'currentColor');
				style['--accordion-header-' + state + '-text-shadow'] = String(this.settings['headerTextShadow' + suffix] || 'none');
				style['--accordion-header-' + state + '-stroke-width'] = this.cssToken(this.settings['headerTextStrokeWidth' + suffix], '0px');
				style['--accordion-header-' + state + '-stroke-color'] = String(this.settings['headerTextStrokeColor' + suffix] || 'currentColor');
				style['--accordion-header-' + state + '-icon-color'] = String(this.settings['headerIconColor' + suffix] || 'currentColor');
			});
			return style;
		},
		itemPosition() {
			const value = String(this.responsiveValue('itemPosition', 'stretch')).toLowerCase();
			return ['start', 'center', 'end', 'stretch'].includes(value) ? value : 'stretch';
		},
	},
	watch: {
		expandedItemIds: {
			deep: true,
			handler() {
				if (this.skipNextExpandedWatch) {
					this.skipNextExpandedWatch = false;
					return;
				}
				this.setInitialPanelHeights();
			},
		},
	},
	mounted() {
		this.setInitialPanelHeights();
	},
	methods: {
		isExpanded(itemId) {
			return this.expandedItemIds.map(String).includes(String(itemId));
		},
		isVisuallyExpanded(itemId) {
			const state = this.panelAnimationStates[String(itemId)] || '';
			if (state === 'opening') return true;
			if (state === 'closing') return false;
			return this.isExpanded(itemId);
		},
		headerId(itemId) {
			return 'pb-accordion-header-' + String(this.item.id || 'node') + '-' + String(itemId || 'item');
		},
		panelId(itemId) {
			return 'pb-accordion-panel-' + String(this.item.id || 'node') + '-' + String(itemId || 'item');
		},
		iconClass(itemId) {
			return this.isVisuallyExpanded(itemId)
				? String(this.settings.collapseIconClass || 'fas fa-minus')
				: String(this.settings.expandIconClass || 'fas fa-plus');
		},
		iconSource(itemId) {
			const role = this.isVisuallyExpanded(itemId) ? 'collapse' : 'expand';
			const value = String(this.settings[role + 'IconSource'] || 'library').toLowerCase();
			return ['none', 'library', 'svg'].includes(value) ? value : 'library';
		},
		svgIconDataUri(itemId) {
			const role = this.isVisuallyExpanded(itemId) ? 'collapse' : 'expand';
			const markup = String(this.settings[role + 'IconSvg'] || '').trim();
			return markup ? 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(markup) : '';
		},
		responsiveValue(base, fallback = '') {
			const suffix = this.responsiveDevice === 'tablet' ? 'Tablet' : (this.responsiveDevice === 'mobile' ? 'Mobile' : '');
			const value = this.settings[base + suffix];
			if (suffix && (value === '' || value == null)) return this.settings[base] ?? fallback;
			return value === '' || value == null ? fallback : value;
		},
		cssToken(value, fallback = '0px') {
			const raw = String(value == null ? '' : value).trim();
			if (!raw) return fallback;
			if (/^calc\([^;{}]+\)$/.test(raw)) return raw;
			const tokens = raw.split(/\s+/);
			if (tokens.length < 1 || tokens.length > 4) return fallback;
			const normalized = tokens.map((token) => {
				if (/^-?\d+(?:\.\d+)?$/.test(token)) return token + 'px';
				return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)$/.test(token) ? token : '';
			});
			return normalized.every(Boolean) ? normalized.join(' ') : fallback;
		},
		lineHeightToken(value, fallback = '1.4') {
			const raw = String(value == null ? '' : value).trim();
			return /^(?:normal|\d+(?:\.\d+)?(?:px|%|em|rem)?)$/i.test(raw) ? raw : fallback;
		},
		borderStyle(value) {
			const raw = String(value || '').toLowerCase();
			return ['solid', 'double', 'dotted', 'dashed', 'groove'].includes(raw) ? raw : 'none';
		},
		gradient(prefix, suffix = '') {
			const first = String(this.settings[prefix + 'GradientColorOne' + suffix] || '#ffffff');
			const second = String(this.settings[prefix + 'GradientColorTwo' + suffix] || '#f4f6f8');
			const firstLocation = Math.min(100, Math.max(0, Number(this.settings[prefix + 'GradientLocationOne' + suffix]) || 0));
			const secondLocation = Math.min(100, Math.max(0, Number(this.settings[prefix + 'GradientLocationTwo' + suffix]) || 100));
			const type = String(this.settings[prefix + 'GradientType' + suffix] || 'linear');
			if (type === 'radial') {
				const position = String(this.settings[prefix + 'GradientPosition' + suffix] || 'center center').replace(/[^a-z\s-]/gi, '') || 'center center';
				return `radial-gradient(at ${position}, ${first} ${firstLocation}%, ${second} ${secondLocation}%)`;
			}
			const angle = Math.min(360, Math.max(0, Number(this.settings[prefix + 'GradientAngle' + suffix]) || 180));
			return `linear-gradient(${angle}deg, ${first} ${firstLocation}%, ${second} ${secondLocation}%)`;
		},
		stateBackground(suffix) {
			return this.settings['accordionBackgroundType' + suffix] === 'gradient'
				? this.gradient('accordion', suffix)
				: String(this.settings['accordionBackgroundColor' + suffix] || 'transparent');
		},
		contentBackground() {
			return this.settings.contentBackgroundType === 'gradient'
				? this.gradient('content')
				: String(this.settings.contentBackgroundColor || 'transparent');
		},
		toggleItem(item) {
			if (!item) return;
			const itemId = String(item.id || '');
			const expanding = !this.isExpanded(itemId);
			const transitions = [{ itemId, expanding }];
			if (expanding && this.settings.maxExpanded !== 'multiple') {
				this.expandedItemIds.map(String).filter((id) => id !== itemId).forEach((id) => {
					transitions.push({ itemId: id, expanding: false });
				});
			}
			const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
			if (this.animationDuration <= 0 || reduced) {
				this.$emit('toggle-item', item.id);
				return;
			}
			this.pendingToggleItemId = itemId;
			this.startPanelTransitions(transitions);
		},
		panelStyle(itemId) {
			const key = String(itemId);
			const value = this.panelHeights[key];
			const metrics = this.panelAnimationMetrics[key] || {};
			return {
				height: value == null ? (this.isExpanded(itemId) ? 'auto' : '0px') : value,
				'--accordion-panel-start-height': metrics.start || '0px',
				'--accordion-panel-target-height': metrics.target || '0px',
			};
		},
		panelClass(itemId) {
			const state = this.panelAnimationStates[String(itemId)] || '';
			return {
				'is-active': this.isVisuallyExpanded(itemId),
				'is-opening': state === 'opening',
				'is-closing': state === 'closing',
			};
		},
		panelElements() {
			const queried = this.$el ? Array.from(this.$el.querySelectorAll('.el-widget-accordion__panel')) : [];
			if (queried.length) return queried;
			const refs = this.$refs.panels;
			if (Array.isArray(refs) && refs.length) return refs;
			if (refs) return [refs];
			return [];
		},
		setInitialPanelHeights() {
			this.$nextTick(() => {
				const panels = this.panelElements();
				const next = {};
				panels.forEach((panel) => {
					const itemId = String(panel?.dataset?.accordionItemId || '');
					if (!itemId) return;
					next[itemId] = this.isExpanded(itemId) ? 'auto' : '0px';
				});
				this.panelHeights = next;
			});
		},
		startPanelTransitions(transitions = []) {
			const panels = this.panelElements();
			const nextHeights = { ...this.panelHeights };
			const nextStates = { ...this.panelAnimationStates };
			const nextMetrics = { ...this.panelAnimationMetrics };
			const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
			transitions.forEach(({ itemId, expanding }) => {
				const panel = panels.find((candidate) => String(candidate?.dataset?.accordionItemId || '') === String(itemId));
				if (!panel) return;
				const key = String(itemId);
				const startHeight = expanding ? '0px' : Math.max(0, panel.getBoundingClientRect().height) + 'px';
				const targetHeight = expanding ? panel.scrollHeight + 'px' : '0px';
				nextMetrics[key] = { start: startHeight, target: targetHeight };
				if (this.animationDuration <= 0 || reduced) {
					nextStates[key] = '';
					nextHeights[key] = expanding ? 'auto' : '0px';
					return;
				}
				nextStates[key] = expanding ? 'opening' : 'closing';
				nextHeights[key] = targetHeight;
			});
			this.panelAnimationMetrics = nextMetrics;
			this.panelAnimationStates = nextStates;
			this.panelHeights = nextHeights;
		},
		onPanelAnimationEnd(itemId, event) {
			const animationName = String(event?.animationName || '');
			if (!event || event.target !== event.currentTarget || (!animationName.includes('pb-accordion-open') && !animationName.includes('pb-accordion-close'))) return;
			const key = String(itemId);
			const shouldCommit = key === String(this.pendingToggleItemId || '');
			const endingState = this.panelAnimationStates[key] || '';
			this.panelAnimationStates = { ...this.panelAnimationStates, [key]: '' };
			this.panelHeights = { ...this.panelHeights, [key]: endingState === 'opening' ? 'auto' : '0px' };
			if (shouldCommit) {
				this.pendingToggleItemId = '';
				this.skipNextExpandedWatch = true;
				this.$emit('toggle-item', itemId);
			}
		},
	},
};
</script>

<style scoped>
.el-widget-accordion__panel {
	overflow: hidden;
}

.el-widget-accordion__panel.is-opening {
	animation: pb-accordion-open var(--accordion-animation-duration, 400ms) ease forwards;
}

.el-widget-accordion__panel.is-closing {
	animation: pb-accordion-close var(--accordion-animation-duration, 400ms) ease forwards;
}

@keyframes pb-accordion-open {
	from { height: var(--accordion-panel-start-height, 0); }
	to { height: var(--accordion-panel-target-height, 0); }
}

@keyframes pb-accordion-close {
	from { height: var(--accordion-panel-start-height, 0); }
	to { height: var(--accordion-panel-target-height, 0); }
}

.el-widget-accordion {
	width: 100%;
	padding: var(--accordion-padding, 0);
}

.el-widget-accordion__item {
	margin-bottom: var(--accordion-item-gap, 0);
	background: var(--accordion-background-normal, #fff);
	border-style: var(--accordion-border-style-normal, solid);
	border-width: var(--accordion-border-width-normal, 1px);
	border-color: var(--accordion-border-color-normal, #d5dae3);
	border-radius: var(--accordion-border-radius, 0);
	overflow: hidden;
}

.el-widget-accordion__item:last-child {
	margin-bottom: 0;
}

.el-widget-accordion__item:hover {
	background: var(--accordion-background-hover, var(--accordion-background-normal, #fff));
	border-style: var(--accordion-border-style-hover, var(--accordion-border-style-normal, solid));
	border-width: var(--accordion-border-width-hover, var(--accordion-border-width-normal, 1px));
	border-color: var(--accordion-border-color-hover, var(--accordion-border-color-normal, #d5dae3));
}

.el-widget-accordion__item.is-active {
	background: var(--accordion-background-active, var(--accordion-background-normal, #fff));
	border-style: var(--accordion-border-style-active, var(--accordion-border-style-normal, solid));
	border-width: var(--accordion-border-width-active, var(--accordion-border-width-normal, 1px));
	border-color: var(--accordion-border-color-active, var(--accordion-border-color-normal, #d5dae3));
}

.el-widget-accordion__heading {
	margin: 0;
	font: inherit;
}

.el-widget-accordion__header {
	appearance: none;
	width: 100%;
	min-height: 52px;
	padding: 14px 16px;
	border: 0;
	background: transparent;
	display: flex;
	align-items: center;
	gap: var(--accordion-icon-spacing, 12px);
	font-family: var(--accordion-header-font-family, inherit);
	font-size: var(--accordion-header-font-size, 16px);
	font-weight: var(--accordion-header-font-weight, 600);
	line-height: var(--accordion-header-line-height, 1.4);
	letter-spacing: var(--accordion-header-letter-spacing, 0);
	word-spacing: var(--accordion-header-word-spacing, 0);
	text-transform: var(--accordion-header-text-transform, none);
	font-style: var(--accordion-header-font-style, normal);
	text-decoration: var(--accordion-header-text-decoration, none);
	color: var(--accordion-header-normal-title-color, currentColor);
	text-shadow: var(--accordion-header-normal-text-shadow, none);
	-webkit-text-stroke: var(--accordion-header-normal-stroke-width, 0) var(--accordion-header-normal-stroke-color, currentColor);
	cursor: pointer;
}

.el-widget-accordion__item:hover .el-widget-accordion__header {
	color: var(--accordion-header-hover-title-color, var(--accordion-header-normal-title-color, currentColor));
	text-shadow: var(--accordion-header-hover-text-shadow, none);
	-webkit-text-stroke: var(--accordion-header-hover-stroke-width, 0) var(--accordion-header-hover-stroke-color, currentColor);
}

.el-widget-accordion__item.is-active .el-widget-accordion__header {
	color: var(--accordion-header-active-title-color, var(--accordion-header-normal-title-color, currentColor));
	text-shadow: var(--accordion-header-active-text-shadow, none);
	-webkit-text-stroke: var(--accordion-header-active-stroke-width, 0) var(--accordion-header-active-stroke-color, currentColor);
}

.el-widget-accordion__title {
	flex: 1 1 auto;
}

.is-item-position-start .el-widget-accordion__title { text-align: left; }
.is-item-position-center .el-widget-accordion__title { text-align: center; }
.is-item-position-end .el-widget-accordion__title { text-align: right; }
.is-item-position-stretch .el-widget-accordion__title { text-align: left; }

.el-widget-accordion__icon {
	flex: 0 0 auto;
	width: var(--accordion-icon-size, 16px);
	height: var(--accordion-icon-size, 16px);
	display: inline-flex;
	align-items: center;
	justify-content: center;
	font-size: var(--accordion-icon-size, 16px);
	color: var(--accordion-header-normal-icon-color, currentColor);
}

.el-widget-accordion__icon img {
	display: block;
	width: 100%;
	height: 100%;
	object-fit: contain;
}

.el-widget-accordion__item:hover .el-widget-accordion__icon {
	color: var(--accordion-header-hover-icon-color, var(--accordion-header-normal-icon-color, currentColor));
}

.el-widget-accordion__item.is-active .el-widget-accordion__icon {
	color: var(--accordion-header-active-icon-color, var(--accordion-header-normal-icon-color, currentColor));
}

.el-widget-accordion__panel-inner {
	margin-top: var(--accordion-content-distance, 0);
	padding: var(--accordion-content-padding, 20px);
	background: var(--accordion-content-background, #fff);
	border-style: var(--accordion-content-border-style, none);
	border-width: var(--accordion-content-border-width, 0);
	border-color: var(--accordion-content-border-color, transparent);
	border-radius: var(--accordion-content-radius, 0);
}

@media (prefers-reduced-motion: reduce) {
	.el-widget-accordion__panel {
		transition-duration: 0ms;
	}
}
</style>
