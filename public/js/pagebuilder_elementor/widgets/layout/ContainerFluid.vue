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
		responsiveDevice: {
			type: String,
			default: 'desktop',
		},
	},
	data() {
		return {
			scrollPreviewProgress: 1,
			mousePreviewX: 0,
			mousePreviewY: 0,
			mousePreviewActive: false,
			previewCanvasLeft: 0,
			previewCanvasTop: 0,
			previewCanvasRightOffset: 0,
			previewCanvasBottomOffset: 0,
			previewCanvasWidth: 0,
			previewCanvasHeight: 0,
			previewViewportTarget: null,
			previewScrollHandler: null,
			previewResizeHandler: null,
			previewMouseMoveHandler: null,
			previewMouseLeaveHandler: null,
			previewRaf: 0,
		};
	},
	mounted() {
		this.syncPreviewBindings();
	},
	updated() {
		this.$nextTick(() => {
			this.syncPreviewBindings();
		});
	},
	beforeUnmount() {
		this.teardownPreviewBindings();
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
			const widthValue = this.responsiveSetting('containerWidth', s.containerWidth || '100%');
			const maxWidthValue = this.responsiveSetting('maxWidth', s.maxWidth || 'auto');
			const minHeightValue = this.responsiveSetting('minHeight', s.minHeight || 'auto');
			// Arsitektur baru: Container Fluid adalah wrapper block-level.
			// Layout flex/grid diterapkan di el-cont-columns (slot inner) via contColumnsStyle di BuilderNode.
			const style = {
				display: 'block',
				boxSizing: 'border-box',
				width: fullMode ? this.toCssSize(widthValue, '100%') : '100%',
			};

			Object.assign(style, this.backgroundStyles(s, ''));

			if (s.borderType && s.borderType !== 'none') {
				style.border = this.toCssSize(s.borderWidth, '1px') + ' ' + s.borderType + ' ' + (s.borderColor || '#000000');
			}

			style.borderRadius = this.borderRadius(s);
			style.boxShadow = this.shadowValue(s);
			style.paddingTop = this.toCssSize(this.responsiveSetting('paddingTop', s.paddingTop), '0');
			style.paddingRight = this.toCssSize(this.responsiveSetting('paddingRight', s.paddingRight), '0');
			style.paddingBottom = this.toCssSize(this.responsiveSetting('paddingBottom', s.paddingBottom), '0');
			style.paddingLeft = this.toCssSize(this.responsiveSetting('paddingLeft', s.paddingLeft), '0');
			style.marginTop = this.toCssSpace(this.responsiveSetting('marginTop', s.marginTop), '0');
			style.marginRight = this.toCssSpace(this.responsiveSetting('marginRight', s.marginRight), '0');
			style.marginBottom = this.toCssSpace(this.responsiveSetting('marginBottom', s.marginBottom), '0');
			style.marginLeft = this.toCssSpace(this.responsiveSetting('marginLeft', s.marginLeft), '0');
			style.minHeight = this.toCssSize(minHeightValue, 'auto');
			if (fullMode) {
				style.maxWidth = '100%';
			} else {
				const maxWidthCss = this.toCssSize(maxWidthValue, 'auto');
				style.maxWidth = maxWidthCss === 'auto' ? 'auto' : 'min(' + maxWidthCss + ', 100%)';
			}

			this.applyAdvancedLayoutStyle(style, s);
			if (this.hasAnyShapeDivider(s) && (!style.position || style.position === 'static')) {
				style.position = 'relative';
			}
			this.applyEditorPreviewEffects(style, s);

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
		responsiveSettingForDevice(base, device) {
			return this.settings[base + this.responsiveSuffix(device)];
		},
		responsiveSetting(base, fallback = '') {
			const device = this.responsiveDevice || 'desktop';
			const key = base + this.responsiveSuffix(device);
			const value = this.settings[key];
			if (device !== 'desktop' && (value === '' || value === null || value === undefined)) {
				const desktopValue = this.settings[base];
				return desktopValue === null || desktopValue === undefined || desktopValue === '' ? fallback : desktopValue;
			}
			if (value === '' || value === null || value === undefined) {
				const desktopValue = this.settings[base];
				return desktopValue === null || desktopValue === undefined || desktopValue === '' ? fallback : desktopValue;
			}
			return value;
		},
		appendResponsiveEdgeRules(rules, cssPrefix, settingPrefix, device, allowAuto) {
			[
				['top', 'Top'],
				['right', 'Right'],
				['bottom', 'Bottom'],
				['left', 'Left'],
			].forEach(([cssSide, settingSide]) => {
				const raw = this.responsiveSettingForDevice(settingPrefix + settingSide, device);

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
		currentDevice() {
			const device = String(this.responsiveDevice || 'desktop').toLowerCase();
			return ['desktop', 'tablet', 'mobile'].includes(device) ? device : 'desktop';
		},
		currentDeviceSuffix() {
			const device = this.currentDevice();
			if (device === 'tablet') return 'Tablet';
			if (device === 'mobile') return 'Mobile';
			return 'Desktop';
		},
		deviceToggleEnabled(settings, prefix, fallback = true) {
			const value = settings[prefix + this.currentDeviceSuffix()];
			if (value === '' || value === null || value === undefined) return fallback;
			return !this.isDisabled(value);
		},
		isHiddenOnCurrentDevice(settings = this.settings) {
			return this.isTruthy(settings['hide' + this.currentDeviceSuffix()]);
		},
		isStickyActiveForCurrentDevice(settings = this.settings) {
			return (settings.sticky || 'none') !== 'none' && this.deviceToggleEnabled(settings, 'stickyOn', true);
		},
		isScrollActiveForCurrentDevice(settings = this.settings) {
			return this.isTruthy(settings.scrollingEffects) && this.deviceToggleEnabled(settings, 'scrollApply', true);
		},
		isMouseActiveForCurrentDevice(settings = this.settings) {
			return this.isTruthy(settings.mouseEffects) && this.deviceToggleEnabled(settings, 'mouseApply', true);
		},
		clampRatio(value, min = 0, max = 1) {
			const numeric = Number(value);
			if (!Number.isFinite(numeric)) return min;
			return Math.min(max, Math.max(min, numeric));
		},
		resolveScrollViewport(settings = this.settings) {
			if (String(settings.scrollRelativeTo || 'default').toLowerCase() === 'viewport') {
				return window;
			}
			return this.$el && typeof this.$el.closest === 'function'
				? (this.$el.closest('.pb-canvas-wrap') || window)
				: window;
		},
		resolveEditorCanvasRect() {
			const deviceClass = '.pb-canvas.is-' + this.currentDevice();
			const canvas = (this.$el && typeof this.$el.closest === 'function'
				? this.$el.closest('.pb-canvas')
				: null)
				|| document.querySelector(deviceClass)
				|| document.querySelector('.pb-canvas');
			if (!canvas || typeof canvas.getBoundingClientRect !== 'function') return null;
			const rect = canvas.getBoundingClientRect();
			const styles = window.getComputedStyle(canvas);
			const leftInset = (parseFloat(styles.borderLeftWidth) || 0) + (parseFloat(styles.paddingLeft) || 0);
			const rightInset = (parseFloat(styles.borderRightWidth) || 0) + (parseFloat(styles.paddingRight) || 0);
			const topInset = (parseFloat(styles.borderTopWidth) || 0) + (parseFloat(styles.paddingTop) || 0);
			const bottomInset = (parseFloat(styles.borderBottomWidth) || 0) + (parseFloat(styles.paddingBottom) || 0);
			const contentLeft = rect.left + leftInset;
			const contentTop = rect.top + topInset;
			const contentRight = rect.right - rightInset;
			const contentBottom = rect.bottom - bottomInset;
			return {
				left: contentLeft,
				top: contentTop,
				right: contentRight,
				bottom: contentBottom,
				width: Math.max(0, contentRight - contentLeft),
				height: Math.max(0, contentBottom - contentTop),
			};
		},
		updateEditorCanvasMetrics() {
			const rect = this.resolveEditorCanvasRect();
			if (!rect) return;
			this.previewCanvasLeft = rect.left;
			this.previewCanvasTop = rect.top;
			this.previewCanvasWidth = rect.width;
			this.previewCanvasHeight = rect.height;
			this.previewCanvasRightOffset = Math.max(0, window.innerWidth - rect.right);
			this.previewCanvasBottomOffset = Math.max(0, window.innerHeight - rect.bottom);
		},
		resolvePreviewLength(value, base) {
			const raw = String(value == null ? '' : value).trim().toLowerCase();
			if (!raw || raw === 'auto') return null;
			if (/^-?\d+(\.\d+)?%$/.test(raw)) {
				return (Number(base) || 0) * (parseFloat(raw) / 100);
			}
			if (/^-?\d+(\.\d+)?px$/.test(raw)) {
				return parseFloat(raw);
			}
			if (/^-?\d+(\.\d+)?$/.test(raw)) {
				return parseFloat(raw);
			}
			return null;
		},
		teardownPreviewBindings() {
			if (this.previewRaf) {
				cancelAnimationFrame(this.previewRaf);
				this.previewRaf = 0;
			}
			if (this.previewViewportTarget && this.previewScrollHandler) {
				const scrollTarget = this.previewViewportTarget === window ? window : this.previewViewportTarget;
				scrollTarget.removeEventListener('scroll', this.previewScrollHandler);
			}
			if (this.previewResizeHandler) {
				window.removeEventListener('resize', this.previewResizeHandler);
			}
			if (this.$el && this.previewMouseMoveHandler) {
				this.$el.removeEventListener('mousemove', this.previewMouseMoveHandler);
			}
			if (this.$el && this.previewMouseLeaveHandler) {
				this.$el.removeEventListener('mouseleave', this.previewMouseLeaveHandler);
			}
			this.previewViewportTarget = null;
			this.previewScrollHandler = null;
			this.previewResizeHandler = null;
			this.previewMouseMoveHandler = null;
			this.previewMouseLeaveHandler = null;
			this.scrollPreviewProgress = 1;
			this.mousePreviewX = 0;
			this.mousePreviewY = 0;
			this.mousePreviewActive = false;
		},
		syncPreviewBindings() {
			this.teardownPreviewBindings();
			if (!this.$el) return;
			this.updateEditorCanvasMetrics();

			this.previewResizeHandler = () => {
				if (this.previewRaf) cancelAnimationFrame(this.previewRaf);
				this.previewRaf = requestAnimationFrame(() => {
					this.previewRaf = 0;
					this.updateEditorCanvasMetrics();
					if (this.isScrollActiveForCurrentDevice()) {
						this.updateScrollPreview();
					}
				});
			};
			window.addEventListener('resize', this.previewResizeHandler, { passive: true });

			if (this.isScrollActiveForCurrentDevice()) {
				this.previewViewportTarget = this.resolveScrollViewport();
				this.previewScrollHandler = () => {
					if (this.previewRaf) cancelAnimationFrame(this.previewRaf);
					this.previewRaf = requestAnimationFrame(() => {
						this.previewRaf = 0;
						this.updateScrollPreview();
					});
				};
				const scrollTarget = this.previewViewportTarget === window ? window : this.previewViewportTarget;
				scrollTarget.addEventListener('scroll', this.previewScrollHandler, { passive: true });
				this.updateScrollPreview();
			}

			if (this.isMouseActiveForCurrentDevice()) {
				this.previewMouseMoveHandler = (event) => this.updateMousePreview(event);
				this.previewMouseLeaveHandler = () => {
					this.mousePreviewX = 0;
					this.mousePreviewY = 0;
					this.mousePreviewActive = false;
				};
				this.$el.addEventListener('mousemove', this.previewMouseMoveHandler);
				this.$el.addEventListener('mouseleave', this.previewMouseLeaveHandler);
			}
		},
		updateScrollPreview() {
			if (!this.$el || !this.isScrollActiveForCurrentDevice()) {
				this.scrollPreviewProgress = 1;
				return;
			}

			const target = this.previewViewportTarget || this.resolveScrollViewport();
			const rect = this.$el.getBoundingClientRect();
			const viewportRect = target === window
				? { top: 0, height: window.innerHeight }
				: target.getBoundingClientRect();
			const elementCenter = (rect.top - viewportRect.top) + (rect.height / 2);
			const rawProgress = elementCenter / Math.max(1, viewportRect.height);
			const start = this.clampRatio(Number(this.settings.scrollViewportStart ?? 0) / 100, 0, 1);
			const end = this.clampRatio(Number(this.settings.scrollViewportEnd ?? 100) / 100, 0, 1);
			const range = Math.max(0.01, end - start);
			this.scrollPreviewProgress = this.clampRatio((rawProgress - start) / range, 0, 1);
		},
		updateMousePreview(event) {
			if (!this.$el || !this.isMouseActiveForCurrentDevice()) return;

			const relativeTo = String(this.settings.mouseRelativeTo || 'default').toLowerCase();
			const directionFactor = String(this.settings.mouseDirection || 'direct').toLowerCase() === 'opposite' ? -1 : 1;
			let normalizedX = 0;
			let normalizedY = 0;

			if (relativeTo === 'viewport') {
				normalizedX = ((event.clientX / Math.max(1, window.innerWidth)) - 0.5) * 2;
				normalizedY = ((event.clientY / Math.max(1, window.innerHeight)) - 0.5) * 2;
			} else {
				const rect = this.$el.getBoundingClientRect();
				normalizedX = (((event.clientX - rect.left) / Math.max(1, rect.width)) - 0.5) * 2;
				normalizedY = (((event.clientY - rect.top) / Math.max(1, rect.height)) - 0.5) * 2;
			}

			this.mousePreviewX = this.clampRatio(normalizedX * directionFactor, -1, 1);
			this.mousePreviewY = this.clampRatio(normalizedY * directionFactor, -1, 1);
			this.mousePreviewActive = true;
		},
		buildScrollPreviewStyle(settings) {
			if (!this.isScrollActiveForCurrentDevice(settings)) return {};

			const type = String(settings.scrollEffectType || 'vertical').toLowerCase();
			const direction = String(settings.scrollDirection || 'up').toLowerCase();
			const speed = this.clamp(Number(settings.scrollSpeed) || 0, 0, 10);
			const progress = this.clampRatio(this.scrollPreviewProgress, 0, 1);
			const dynamicIntensity = (1 - progress) * speed;
			const previewIntensity = speed * 0.35;
			const intensity = type === 'transparency' ? dynamicIntensity : Math.max(dynamicIntensity, previewIntensity);
			const sign = (direction === 'down' || direction === 'right' || direction === 'out') ? 1 : -1;

			if (type === 'horizontal') {
				return { transform: 'translateX(' + (sign * intensity * 8) + 'px)' };
			}
			if (type === 'transparency') {
				return { opacity: direction === 'in' ? this.clampRatio(progress, 0.12, 1) : this.clampRatio(1 - progress * 0.88, 0.12, 1) };
			}
			if (type === 'blur') {
				const blur = Math.max(direction === 'in' ? (progress * speed * 1.6) : (intensity * 1.6), speed * 0.45);
				return { filter: 'blur(' + blur.toFixed(2) + 'px)' };
			}
			if (type === 'rotate') {
				return { transform: 'rotate(' + (sign * intensity * 6) + 'deg)' };
			}
			if (type === 'scale') {
				const delta = intensity * 0.018;
				return { transform: 'scale(' + (direction === 'in' ? (1 - delta) : (1 + delta)).toFixed(4) + ')' };
			}

			return { transform: 'translateY(' + (sign * intensity * 8) + 'px)' };
		},
		buildMousePreviewStyle(settings) {
			if (!this.isMouseActiveForCurrentDevice(settings) || !this.mousePreviewActive) return {};

			const type = String(settings.mouseEffectType || 'track').toLowerCase();
			const speed = this.clamp(Number(settings.mouseSpeed) || 0, 0, 10);
			const x = this.mousePreviewX;
			const y = this.mousePreviewY;

			if (type === 'tilt') {
				return { transform: 'perspective(1000px) rotateX(' + (-y * speed * 2.2).toFixed(2) + 'deg) rotateY(' + (x * speed * 2.2).toFixed(2) + 'deg)' };
			}
			if (type === 'parallax') {
				return { transform: 'translate(' + (x * speed * 4).toFixed(2) + 'px, ' + (y * speed * 4).toFixed(2) + 'px) scale(1.01)' };
			}

			return { transform: 'translate(' + (x * speed * 3).toFixed(2) + 'px, ' + (y * speed * 3).toFixed(2) + 'px)' };
		},
		applyEditorPreviewEffects(style, settings) {
			if (this.isHiddenOnCurrentDevice(settings)) {
				style.display = 'none';
				return;
			}
			if (style.position === 'fixed') {
				const canvasWidth = this.previewCanvasWidth || this.resolveEditorCanvasRect()?.width || 0;
				const canvasHeight = this.previewCanvasHeight || this.resolveEditorCanvasRect()?.height || 0;
				const canvasLeft = this.previewCanvasLeft || this.resolveEditorCanvasRect()?.left || 0;
				const canvasTop = this.previewCanvasTop || this.resolveEditorCanvasRect()?.top || 0;
				const canvasRightOffset = this.previewCanvasRightOffset || Math.max(0, window.innerWidth - (this.resolveEditorCanvasRect()?.right || window.innerWidth));
				const canvasBottomOffset = this.previewCanvasBottomOffset || Math.max(0, window.innerHeight - (this.resolveEditorCanvasRect()?.bottom || window.innerHeight));
				const widthPx = this.resolvePreviewLength(style.width, canvasWidth);
				const maxWidthPx = this.resolvePreviewLength(style.maxWidth, canvasWidth);
				const leftInsetPx = this.resolvePreviewLength(style.left, canvasWidth);
				const rightInsetPx = this.resolvePreviewLength(style.right, canvasWidth);
				const topInsetPx = this.resolvePreviewLength(style.top, canvasHeight);
				const bottomInsetPx = this.resolvePreviewLength(style.bottom, canvasHeight);
				const hasExplicitHorizontalInset = leftInsetPx != null || rightInsetPx != null;
				const hasExplicitVerticalInset = topInsetPx != null || bottomInsetPx != null;
				if (!hasExplicitHorizontalInset && !hasExplicitVerticalInset) {
					style.position = 'absolute';
					style.left = '0px';
					style.right = 'auto';
					style.top = '0px';
					style.bottom = 'auto';
				} else {
				let resolvedWidthPx = widthPx;
				if (leftInsetPx != null && rightInsetPx != null) {
					resolvedWidthPx = canvasWidth - leftInsetPx - rightInsetPx;
				} else if (resolvedWidthPx == null) {
					if (leftInsetPx != null && rightInsetPx == null) resolvedWidthPx = canvasWidth - leftInsetPx;
					else if (rightInsetPx != null && leftInsetPx == null) resolvedWidthPx = canvasWidth - rightInsetPx;
					else resolvedWidthPx = canvasWidth;
				}
				if (maxWidthPx != null) {
					resolvedWidthPx = Math.min(resolvedWidthPx, maxWidthPx);
				}
				style.width = Math.max(0, resolvedWidthPx) + 'px';
				if (maxWidthPx != null) {
					style.maxWidth = Math.max(0, maxWidthPx) + 'px';
				}
				if (leftInsetPx != null) {
					style.left = (canvasLeft + leftInsetPx) + 'px';
					style.right = 'auto';
				} else if (rightInsetPx != null) {
					style.left = Math.max(0, canvasLeft + canvasWidth - rightInsetPx - resolvedWidthPx) + 'px';
					style.right = 'auto';
				} else {
					style.left = canvasLeft + 'px';
					style.right = 'auto';
				}
				if (topInsetPx != null) {
					style.top = (canvasTop + topInsetPx) + 'px';
					style.bottom = 'auto';
				} else if (bottomInsetPx != null) {
					style.top = 'auto';
					style.bottom = 'calc(' + canvasBottomOffset + 'px + ' + style.bottom + ')';
				} else {
					style.top = canvasTop + 'px';
					style.bottom = 'auto';
				}
				}
			}

			const transforms = [];
			if (style.transform && style.transform !== 'none') transforms.push(style.transform);

			const scrollPreview = this.buildScrollPreviewStyle(settings);
			const mousePreview = this.buildMousePreviewStyle(settings);

			if (scrollPreview.transform) transforms.push(scrollPreview.transform);
			if (mousePreview.transform) transforms.push(mousePreview.transform);
			if (scrollPreview.opacity !== undefined) style.opacity = String(scrollPreview.opacity);

			const filters = [];
			if (style.filter && style.filter !== 'none') filters.push(style.filter);
			if (scrollPreview.filter) filters.push(scrollPreview.filter);
			if (mousePreview.filter) filters.push(mousePreview.filter);

			if (transforms.length) style.transform = transforms.join(' ');
			if (filters.length) style.filter = filters.join(' ');

			if (this.isScrollActiveForCurrentDevice(settings) || this.isMouseActiveForCurrentDevice(settings)) {
				style.transition = 'transform 180ms ease, opacity 180ms ease, filter 180ms ease, box-shadow 180ms ease, background-color 180ms ease';
			}
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
			if (stickyMode !== 'none' && this.isStickyActiveForCurrentDevice(settings)) {
				style.position = 'sticky';
				const stickyOffset = this.toCssSpace(settings.stickyOffset, '0');
				const stickyEffectsOffset = this.toCssSpace(settings.stickyEffectsOffset, '0');
				const finalStickyOffset = stickyEffectsOffset && stickyEffectsOffset !== '0'
					? 'calc(' + (stickyOffset || '0') + ' + ' + stickyEffectsOffset + ')'
					: (stickyOffset || '0');
				if (stickyMode === 'top') style.top = finalStickyOffset;
				if (stickyMode === 'bottom') style.bottom = finalStickyOffset;
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
