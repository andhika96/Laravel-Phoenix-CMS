<template>
	<div :id="domId" :class="rootClass" :style="wrapperStyle" v-bind="customAttrs">
		<slot></slot>
	</div>
	<component :is="'style'" v-if="styleBlock">{{ styleBlock }}</component>
</template>

<script>
export default {
	name: 'LayoutGrid',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		domId() {
			return 'pb-node-' + (this.item.id || '');
		},
		rootClass() {
			const classes = ['el-layout-grid'];
			if (this.settings.cssClass) classes.push(this.settings.cssClass);
			if (this.isTruthy(this.settings.hideDesktop)) classes.push('pb-hide-desktop');
			if (this.isTruthy(this.settings.hideTablet)) classes.push('pb-hide-tablet');
			if (this.isTruthy(this.settings.hideMobile)) classes.push('pb-hide-mobile');
			if (this.settings.entranceAnimation) classes.push('pb-entrance-anim', 'pb-anim-' + this.settings.entranceAnimation);
			if (this.isTruthy(this.settings.scrollingEffects)) {
				classes.push('pb-motion-scroll');
				if (this.isDisabled(this.settings.scrollApplyDesktop)) classes.push('pb-scroll-off-desktop');
				if (this.isDisabled(this.settings.scrollApplyTablet)) classes.push('pb-scroll-off-tablet');
				if (this.isDisabled(this.settings.scrollApplyMobile)) classes.push('pb-scroll-off-mobile');
			}
			if (this.isTruthy(this.settings.mouseEffects)) {
				classes.push('pb-motion-mouse');
				if (this.isDisabled(this.settings.mouseApplyDesktop)) classes.push('pb-mouse-off-desktop');
				if (this.isDisabled(this.settings.mouseApplyTablet)) classes.push('pb-mouse-off-tablet');
				if (this.isDisabled(this.settings.mouseApplyMobile)) classes.push('pb-mouse-off-mobile');
			}
			if ((this.settings.sticky || 'none') !== 'none') {
				if (this.isDisabled(this.settings.stickyOnDesktop)) classes.push('pb-sticky-off-desktop');
				if (this.isDisabled(this.settings.stickyOnTablet)) classes.push('pb-sticky-off-tablet');
				if (this.isDisabled(this.settings.stickyOnMobile)) classes.push('pb-sticky-off-mobile');
			}
			if (this.isTruthy(this.settings.animateWithAI)) classes.push('pb-motion-ai');

			return classes;
		},
		customAttrs() {
			const attrs = {};

			(this.settings.attributes || []).forEach((attr) => {
				const name = String(attr && attr.name ? attr.name : '').trim();

				if (/^[A-Za-z_:][A-Za-z0-9:_.-]*$/.test(name)) {
					attrs[name] = attr && attr.value != null ? String(attr.value) : '';
				}
			});

			if (this.settings.cssId) {
				attrs['data-css-id'] = this.settings.cssId;
			}

			return attrs;
		},
		scopedCss() {
			const css = String(this.settings.customCssCode || '').trim();
			return css && this.item.id ? css.replace(/\bselector\b/g, '#' + this.domId) : '';
		},
		responsiveCss() {
			if (!this.item.id) return '';

			const rootSelector = '#' + this.domId;
			const gridSelector = rootSelector + ' > .el-grid-columns';
			const tabletRootRules = [];
			const mobileRootRules = [];
			const tabletGridRules = [];
			const mobileGridRules = [];

			this.appendResponsiveEdgeRules(tabletRootRules, 'padding', 'padding', 'tablet', false);
			this.appendResponsiveEdgeRules(tabletRootRules, 'margin', 'margin', 'tablet', true);
			this.appendResponsiveEdgeRules(mobileRootRules, 'padding', 'padding', 'mobile', false);
			this.appendResponsiveEdgeRules(mobileRootRules, 'margin', 'margin', 'mobile', true);
			this.appendResponsiveGridRules(tabletGridRules, 'tablet');
			this.appendResponsiveGridRules(mobileGridRules, 'mobile');

			const blocks = [];

			if (tabletRootRules.length || tabletGridRules.length) {
				const css = [];
				if (tabletRootRules.length) css.push(rootSelector + '{' + tabletRootRules.join(';') + '}');
				if (tabletGridRules.length) css.push(gridSelector + '{' + tabletGridRules.join(';') + '}');
				blocks.push('@media (max-width: 1024px){' + css.join('') + '}');
			}

			if (mobileRootRules.length || mobileGridRules.length) {
				const css = [];
				if (mobileRootRules.length) css.push(rootSelector + '{' + mobileRootRules.join(';') + '}');
				if (mobileGridRules.length) css.push(gridSelector + '{' + mobileGridRules.join(';') + '}');
				blocks.push('@media (max-width: 767px){' + css.join('') + '}');
			}

			return blocks.join('\n');
		},
		styleBlock() {
			return [this.responsiveCss, this.scopedCss].filter(Boolean).join('\n');
		},
		wrapperStyle() {
			const s = this.settings;
			const style = {
				width: '100%',
				flex: '1 1 100%',
				minWidth: '0',
				boxSizing: 'border-box',
				paddingTop: this.toCssSize(s.paddingTop, '0'),
				paddingRight: this.toCssSize(s.paddingRight, '0'),
				paddingBottom: this.toCssSize(s.paddingBottom, '0'),
				paddingLeft: this.toCssSize(s.paddingLeft, '0'),
				marginTop: this.toCssSpace(s.marginTop, '0'),
				marginRight: this.toCssSpace(s.marginRight, '0'),
				marginBottom: this.toCssSpace(s.marginBottom, '0'),
				marginLeft: this.toCssSpace(s.marginLeft, '0'),
				borderRadius: this.borderRadius(s),
				boxShadow: this.shadowValue(s),
			};

			Object.assign(style, this.backgroundStyles(s));

			if (s.borderType && s.borderType !== 'none') {
				style.border = this.toCssSize(s.borderWidth, '1px') + ' ' + s.borderType + ' ' + (s.borderColor || '#000000');
			}

			if (s.zIndex !== '' && s.zIndex != null) {
				style.zIndex = s.zIndex;
			}

			this.applyAdvancedLayoutStyle(style, s);

			return style;
		},
	},
	methods: {
		clamp(value, min, max) {
			return Math.min(max, Math.max(min, Number(value) || min));
		},
		responsiveSuffix(device) {
			if (device === 'tablet') return 'Tablet';
			if (device === 'mobile') return 'Mobile';
			return '';
		},
		responsiveSetting(base, device) {
			return this.settings[base + this.responsiveSuffix(device)];
		},
		appendResponsiveEdgeRules(rules, cssPrefix, settingPrefix, device, allowAuto) {
			[
				['top', 'Top'],
				['right', 'Right'],
				['bottom', 'Bottom'],
				['left', 'Left'],
			].forEach(([cssSide, settingSide]) => {
				const raw = this.responsiveSetting(settingPrefix + settingSide, device);

				if (raw === '' || raw == null) return;

				rules.push(cssPrefix + '-' + cssSide + ':' + (allowAuto ? this.toCssSpace(raw, '0') : this.toCssSize(raw, '0')));
			});
		},
		appendResponsiveGridRules(rules, device) {
			const columns = this.responsiveSetting('columns', device);
			const gridRows = this.responsiveSetting('gridRows', device);
			const columnGap = this.responsiveSetting('columnGap', device);
			const rowGap = this.responsiveSetting('rowGap', device);

			if (columns !== '' && columns != null) {
				rules.push('grid-template-columns:' + this.gridColumnsTemplate(columns));
			}

			if (gridRows !== '' && gridRows != null) {
				const rows = this.gridRowsTemplate(gridRows);
				rules.push('grid-template-rows:' + (rows || 'none'));
			}

			if (columnGap !== '' && columnGap != null) {
				rules.push('column-gap:' + this.toCssSize(columnGap, '20px'));
			}

			if (rowGap !== '' && rowGap != null) {
				rules.push('row-gap:' + this.toCssSize(rowGap, '20px'));
			}
		},
		gridColumnsTemplate(value) {
			return 'repeat(' + this.clamp(value, 1, 12) + ', minmax(0, 1fr))';
		},
		gridRowsTemplate(value) {
			const out = String(value == null ? '' : value).trim();

			if (!out || out.toLowerCase() === 'auto') return '';
			if (/^\d+$/.test(out)) return 'repeat(' + Math.max(1, Number(out)) + ', minmax(0, auto))';

			return out;
		},
		toCssSize(value, fallback = '') {
			if (value === null || value === undefined || value === '') return fallback;
			if (typeof value === 'number') return value === 0 ? '0' : value + 'px';

			const out = String(value).trim();

			if (!out) return fallback;
			if (/^-?\d+(\.\d+)?$/.test(out)) return out === '0' ? '0' : out + 'px';

			return out;
		},
		toCssSpace(value, fallback = '0') {
			const out = String(value == null ? '' : value).trim();

			if (!out) return fallback;
			if (out.toLowerCase() === 'auto') return 'auto';

			return this.toCssSize(out, fallback);
		},
		colorWithOpacity(color, opacity) {
			const raw = String(color || '').trim();
			const alpha = Number(opacity);

			if (!raw) return 'transparent';
			if (!Number.isFinite(alpha) || alpha >= 1) return raw;
			if (raw.startsWith('rgba(')) return raw.replace(/rgba\((.+),\s*[\d.]+\)/, 'rgba($1, ' + alpha + ')');
			if (raw.startsWith('rgb(')) return raw.replace('rgb(', 'rgba(').replace(')', ', ' + alpha + ')');

			const hex = raw.replace('#', '');
			const full = hex.length === 3 ? hex.split('').map((part) => part + part).join('') : hex;

			if (/^[0-9a-fA-F]{6}$/.test(full)) {
				const r = parseInt(full.slice(0, 2), 16);
				const g = parseInt(full.slice(2, 4), 16);
				const b = parseInt(full.slice(4, 6), 16);

				return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
			}

			return raw;
		},
		backgroundStyles(settings) {
			if (settings.bgType === 'color') {
				return {
					backgroundColor: this.colorWithOpacity(settings.bgColor || '#ffffff', settings.bgOpacity == null ? 1 : settings.bgOpacity),
				};
			}

			if (settings.bgType === 'gradient') {
				const start = settings.bgGradientStart || '#ffffff';
				const end = settings.bgGradientEnd || '#000000';
				const position = Number(settings.bgGradientPosition == null ? 50 : settings.bgGradientPosition);

				return {
					backgroundImage: settings.bgGradientType === 'radial'
						? 'radial-gradient(circle, ' + start + ' 0%, ' + end + ' ' + position + '%)'
						: 'linear-gradient(' + (Number(settings.bgGradientAngle) || 90) + 'deg, ' + start + ' 0%, ' + end + ' ' + position + '%)',
				};
			}

			if (settings.bgType === 'image' && settings.bgImage) {
				return {
					backgroundImage: 'url("' + settings.bgImage + '")',
					backgroundSize: settings.bgSize === 'stretch' ? '100% 100%' : (settings.bgSize || 'cover'),
					backgroundPosition: settings.bgPosition || 'center center',
					backgroundRepeat: settings.bgRepeat || 'no-repeat',
					backgroundAttachment: settings.bgAttachment || 'scroll',
				};
			}

			return {};
		},
		borderRadius(settings) {
			if (settings.borderRadius) return this.toCssSize(settings.borderRadius, '0');

			return [
				this.toCssSize(settings.borderRadiusTL, '0'),
				this.toCssSize(settings.borderRadiusTR, '0'),
				this.toCssSize(settings.borderRadiusBR, '0'),
				this.toCssSize(settings.borderRadiusBL, '0'),
			].join(' ');
		},
		shadowValue(settings) {
			if (settings.shadowEnabled) {
				return [
					this.toCssSize(settings.shadowH, '0'),
					this.toCssSize(settings.shadowV, '0'),
					this.toCssSize(settings.shadowBlur, '0'),
					this.toCssSize(settings.shadowSpread, '0'),
					this.colorWithOpacity(settings.shadowColor || '#000000', settings.shadowOpacity == null ? 0.3 : settings.shadowOpacity),
				].join(' ');
			}

			return settings.boxShadow || 'none';
		},
		isTruthy(value) {
			return value === true || value === 'true' || value === 1 || value === '1';
		},
		isDisabled(value) {
			return value === false || value === 'false' || value === 0 || value === '0';
		},
		buildTransform(settings) {
			const transforms = [];
			const offsetX = this.toCssSpace(settings.transformOffsetX, '');
			const offsetY = this.toCssSpace(settings.transformOffsetY, '');
			const rotate = this.toCssSpace(settings.transformRotate, '');
			const scaleX = String(settings.transformScaleX == null ? '' : settings.transformScaleX).trim();
			const scaleY = String(settings.transformScaleY == null ? '' : settings.transformScaleY).trim();
			const skewX = this.toCssSpace(settings.transformSkewX, '');
			const skewY = this.toCssSpace(settings.transformSkewY, '');

			if (offsetX || offsetY) transforms.push('translate(' + (offsetX || '0') + ', ' + (offsetY || '0') + ')');
			if (rotate) transforms.push('rotate(' + rotate + ')');
			if (scaleX || scaleY) transforms.push('scale(' + (scaleX || '1') + ', ' + (scaleY || '1') + ')');
			if (skewX || skewY) transforms.push('skew(' + (skewX || '0') + ', ' + (skewY || '0') + ')');

			return transforms.join(' ');
		},
		applyPosition(style, settings) {
			const stickyMode = settings.sticky || 'none';
			const chosenPosition = settings.position || 'default';
			if (stickyMode !== 'none') {
				style.position = 'sticky';
				const stickyOffset = this.toCssSpace(settings.stickyOffset, '0');
				if (stickyMode === 'top') style.top = stickyOffset || '0';
				if (stickyMode === 'bottom') style.bottom = stickyOffset || '0';
				return;
			}

			if (chosenPosition !== 'default') {
				style.position = chosenPosition;
			}
		},
		applyAdvancedLayoutStyle(style, settings) {
			if (settings.overflow) {
				style.overflow = settings.overflow;
			}

			this.applyPosition(style, settings);

			if (settings.positionTop !== '' && settings.positionTop != null) {
				style.top = this.toCssSpace(settings.positionTop, 'auto');
			}
			if (settings.positionRight !== '' && settings.positionRight != null) {
				style.right = this.toCssSpace(settings.positionRight, 'auto');
			}
			if (settings.positionBottom !== '' && settings.positionBottom != null) {
				style.bottom = this.toCssSpace(settings.positionBottom, 'auto');
			}
			if (settings.positionLeft !== '' && settings.positionLeft != null) {
				style.left = this.toCssSpace(settings.positionLeft, 'auto');
			}

			const transform = this.buildTransform(settings);
			if (transform) {
				style.transform = transform;
			}

			if (settings.zIndex !== '' && settings.zIndex != null) {
				if (!style.position || style.position === 'static') {
					style.position = 'relative';
				}
				style.zIndex = settings.zIndex;
			}
		},
	},
};
</script>
