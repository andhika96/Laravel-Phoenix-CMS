<template>
	<component :is="rootTag" :id="domId" :class="rootClass" :style="containerStyle" v-bind="customAttrs">
		<slot></slot>
	</component>
	<component :is="'style'" v-if="styleBlock">{{ styleBlock }}</component>
</template>

<script>
export default {
	name: 'LayoutContainerFluid',
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
		rootTag() {
			const raw = String(this.settings.htmlTag || 'default').trim().toLowerCase();
			const allowed = ['div', 'section', 'header', 'main', 'article', 'aside', 'footer', 'nav'];
			if (raw === 'default' || !raw) return 'div';
			return allowed.includes(raw) ? raw : 'div';
		},
		rootClass() {
			const classes = ['el-layout-container-fluid'];
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

			const selector = '#' + this.domId;
			const tabletRules = [];
			const mobileRules = [];

			this.appendResponsiveEdgeRules(tabletRules, 'padding', 'padding', 'tablet', false);
			this.appendResponsiveEdgeRules(tabletRules, 'margin', 'margin', 'tablet', true);
			this.appendResponsiveEdgeRules(mobileRules, 'padding', 'padding', 'mobile', false);
			this.appendResponsiveEdgeRules(mobileRules, 'margin', 'margin', 'mobile', true);

			const blocks = [];

			if (tabletRules.length) {
				blocks.push('@media (max-width: 1024px){' + selector + '{' + tabletRules.join(';') + '}}');
			}

			if (mobileRules.length) {
				blocks.push('@media (max-width: 767px){' + selector + '{' + mobileRules.join(';') + '}}');
			}

			return blocks.join('\n');
		},
		styleBlock() {
			return [this.responsiveCss, this.hoverCss, this.shapeDividerCss, this.scopedCss].filter(Boolean).join('\n');
		},
		hoverCss() {
			if (!this.item.id) return '';
			const hoverStyle = this.backgroundStyles(this.settings, 'Hover');
			if (!Object.keys(hoverStyle).length) return '';
			const transitionDuration = Math.max(0, Number(this.settings.bgTransitionDuration || 300));
			const selector = '#' + this.domId;
			return selector + '{transition:background-color ' + transitionDuration + 'ms ease, opacity ' + transitionDuration + 'ms ease;}\n'
				+ selector + ':hover{' + this.styleObjectToCss(hoverStyle, true) + '}';
		},
		shapeDividerCss() {
			if (!this.item.id) return '';

			const selector = '#' + this.domId;
			const topRule = this.buildShapeDividerRule('top');
			const bottomRule = this.buildShapeDividerRule('bottom');
			const rules = [];

			if (topRule) rules.push(selector + '::before{' + topRule + '}');
			if (bottomRule) rules.push(selector + '::after{' + bottomRule + '}');

			return rules.join('\n');
		},
		containerStyle() {
			const s = this.settings;
			const fullMode = s.contentWidth === 'full' || s.contentWidth === 'fluid';
			// Arsitektur baru: Container Fluid adalah wrapper block-level.
			// Layout flex/grid diterapkan di el-cont-columns (slot inner) via contColumnsStyle di BuilderNode.
			const style = {
				display: 'block',
				boxSizing: 'border-box',
				width: fullMode ? this.toCssSize(s.containerWidth || '100%', '100%') : '100%',
			};

			Object.assign(style, this.backgroundStyles(s, ''));

			if (s.borderType && s.borderType !== 'none') {
				style.border = this.toCssSize(s.borderWidth, '1px') + ' ' + s.borderType + ' ' + (s.borderColor || '#000000');
			}

			style.borderRadius = this.borderRadius(s);
			style.boxShadow = this.shadowValue(s);
			style.paddingTop = this.toCssSize(s.paddingTop, '0');
			style.paddingRight = this.toCssSize(s.paddingRight, '0');
			style.paddingBottom = this.toCssSize(s.paddingBottom, '0');
			style.paddingLeft = this.toCssSize(s.paddingLeft, '0');
			style.marginTop = this.toCssSpace(s.marginTop, '0');
			style.marginRight = this.toCssSpace(s.marginRight, '0');
			style.marginBottom = this.toCssSpace(s.marginBottom, '0');
			style.marginLeft = this.toCssSpace(s.marginLeft, '0');
			style.minHeight = this.toCssSize(s.minHeight, 'auto');
			if (!fullMode) {
				style.maxWidth = this.toCssSize(s.maxWidth, 'auto');
			}

			this.applyAdvancedLayoutStyle(style, s);
			if (this.hasAnyShapeDivider(s) && (!style.position || style.position === 'static')) {
				style.position = 'relative';
			}

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
		backgroundStyles(settings, suffix = "") {
			const base = this.backgroundLayer(settings, 'bg', suffix);
			const overlay = this.backgroundLayer(settings, 'bgOverlay', suffix);
			const layers = [];
			if (overlay) layers.push(overlay);
			if (base) layers.push(base);
			if (!layers.length) return {};

			const style = {
				backgroundImage: layers.map((layer) => layer.image).join(', '),
				backgroundSize: layers.map((layer) => layer.size).join(', '),
				backgroundPosition: layers.map((layer) => layer.position).join(', '),
				backgroundRepeat: layers.map((layer) => layer.repeat).join(', '),
				backgroundAttachment: layers.map((layer) => layer.attachment).join(', '),
			};
			if (layers.length > 1 && overlay && overlay.blendMode && overlay.blendMode !== 'normal') {
				style.backgroundBlendMode = overlay.blendMode + ', normal';
			}
			return style;
		},
		styleObjectToCss(style, important = false) {
			return Object.entries(style)
				.map(([key, value]) => {
					const cssValue = important ? String(value) + ' !important' : value;
					return key.replace(/[A-Z]/g, (m) => '-' + m.toLowerCase()) + ':' + cssValue;
				})
				.join(';');
		},
		shapeDividerType(settings, side) {
			const prefix = side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
			return String(settings[prefix + 'Type'] || 'none').toLowerCase();
		},
		hasAnyShapeDivider(settings) {
			return this.shapeDividerType(settings, 'top') !== 'none' || this.shapeDividerType(settings, 'bottom') !== 'none';
		},
		shapeDividerClipPath(type, side) {
			if (type === 'triangle') {
				return side === 'top'
					? 'polygon(0 100%, 50% 0, 100% 100%)'
					: 'polygon(0 0, 50% 100%, 100% 0)';
			}
			if (type === 'curve') {
				return side === 'top'
					? 'ellipse(75% 100% at 50% 100%)'
					: 'ellipse(75% 100% at 50% 0%)';
			}
			return side === 'top'
				? 'polygon(0 100%, 100% 0, 100% 100%)'
				: 'polygon(0 0, 100% 0, 0 100%)';
		},
		buildShapeDividerRule(side) {
			const settings = this.settings || {};
			const prefix = side === 'bottom' ? 'shapeDividerBottom' : 'shapeDividerTop';
			const type = this.shapeDividerType(settings, side);

			if (type === 'none') return '';

			const color = String(settings[prefix + 'Color'] || '#ffffff').trim() || '#ffffff';
			const rawWidth = String(settings[prefix + 'Width'] == null ? '' : settings[prefix + 'Width']).trim();
			const rawHeight = String(settings[prefix + 'Height'] == null ? '' : settings[prefix + 'Height']).trim();
			const width = rawWidth
				? (/^-?\d+(\.\d+)?$/.test(rawWidth) ? rawWidth + '%' : rawWidth)
				: '100%';
			const height = rawHeight
				? (/^-?\d+(\.\d+)?$/.test(rawHeight) ? rawHeight + 'px' : rawHeight)
				: '60px';
			const flip = this.isTruthy(settings[prefix + 'Flip']);
			const bringToFront = this.isTruthy(settings[prefix + 'Front']);
			const clipPath = this.shapeDividerClipPath(type, side);
			const transform = 'translateX(-50%)' + (flip ? ' scaleX(-1)' : '');

			return [
				'content:""',
				'position:absolute',
				'left:50%',
				side === 'top' ? 'top:0' : 'bottom:0',
				'width:' + width,
				'height:' + height,
				'background:' + color,
				'pointer-events:none',
				'transform:' + transform,
				'transform-origin:center center',
				'clip-path:' + clipPath,
				'z-index:' + (bringToFront ? 4 : 0),
			].join(';');
		},
		stateSetting(settings, base, suffix = '') {
			const key = base + suffix;
			if (settings[key] !== undefined && settings[key] !== null && settings[key] !== '') return settings[key];
			return settings[base];
		},
		backgroundLayer(settings, prefix, suffix = '') {
			const type = String(this.stateSetting(settings, prefix + 'Type', suffix) || 'none').toLowerCase();
			if (type === 'none') return null;
			if (type === 'color') {
				const color = this.colorWithOpacity(this.stateSetting(settings, prefix + 'Color', suffix) || '#ffffff', this.stateSetting(settings, prefix + 'Opacity', suffix));
				return {
					image: 'linear-gradient(0deg, ' + color + ', ' + color + ')',
					size: '100% 100%',
					position: 'center center',
					repeat: 'no-repeat',
					attachment: 'scroll',
					blendMode: this.stateSetting(settings, prefix + 'BlendMode', suffix) || 'normal',
				};
			}
			if (type === 'gradient') {
				const gType = String(this.stateSetting(settings, prefix + 'GradientType', suffix) || 'linear').toLowerCase();
				const start = this.stateSetting(settings, prefix + 'GradientStart', suffix) || '#ffffff';
				const end = this.stateSetting(settings, prefix + 'GradientEnd', suffix) || '#000000';
				const position = Number(this.stateSetting(settings, prefix + 'GradientPosition', suffix) ?? 50);
				const angle = Number(this.stateSetting(settings, prefix + 'GradientAngle', suffix) ?? 90);
				return {
					image: gType === 'radial'
						? 'radial-gradient(circle, ' + start + ' 0%, ' + end + ' ' + position + '%)'
						: 'linear-gradient(' + angle + 'deg, ' + start + ' 0%, ' + end + ' ' + position + '%)',
					size: 'cover',
					position: 'center center',
					repeat: 'no-repeat',
					attachment: 'scroll',
					blendMode: this.stateSetting(settings, prefix + 'BlendMode', suffix) || 'normal',
				};
			}
			const image = String(this.stateSetting(settings, prefix + 'Image', suffix) || '').trim();
			if (!image) return null;
			return {
				image: 'url("' + image + '")',
				size: this.stateSetting(settings, prefix + 'Size', suffix) === 'stretch' ? '100% 100%' : (this.stateSetting(settings, prefix + 'Size', suffix) || 'cover'),
				position: this.stateSetting(settings, prefix + 'Position', suffix) || 'center center',
				repeat: this.stateSetting(settings, prefix + 'Repeat', suffix) || 'no-repeat',
				attachment: this.stateSetting(settings, prefix + 'Attachment', suffix) || 'scroll',
				blendMode: this.stateSetting(settings, prefix + 'BlendMode', suffix) || 'normal',
			};
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
			if (settings.alignSelf && settings.alignSelf !== 'auto') {
				style.alignSelf = settings.alignSelf;
			}

			if (settings.order !== '' && settings.order != null) {
				const order = Number(settings.order);
				if (Number.isFinite(order)) {
					style.order = order;
				}
			}

			if (settings.sizeMode === 'grow') {
				style.flex = '1 1 0';
			} else if (settings.sizeMode === 'shrink') {
				style.flex = '0 1 auto';
			} else if (settings.sizeMode === 'custom') {
				style.flex = '0 0 ' + this.toCssSize(settings.containerWidth || settings.maxWidth, 'auto');
			}

			if (settings.overflow && settings.overflow !== 'default') {
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
		gridRowsTemplate(value) {
			const out = String(value == null ? '' : value).trim();

			if (!out || out.toLowerCase() === 'auto') return '';
			if (/^\d+$/.test(out)) return 'repeat(' + Math.max(1, Number(out)) + ', minmax(0, auto))';

			return out;
		},
	},
};
</script>

