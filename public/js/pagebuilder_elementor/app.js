(function () {
	'use strict';
	console.log('[PB] app.js loaded v3-refactor - ' + new Date().toISOString());

	// Pola dari builder lama yang sudah terbukti bekerja:
	// 1. Sidebar: <draggable pull="clone" put=false :clone="cloneItem">
	// 2. Canvas root: <draggable group="pb-root">
	// 3. Container children: <draggable group="{name:'pb-container', put:...}" @add="onAddContainer">
	// 4. Grid column children: <draggable group="{name:'pb-col', put:['pb-widget','pb-col']}" @add="onAddCol">
	// 5. TIDAK ADA native drag event di node canvas

	const { createApp, ref, computed, defineAsyncComponent, watch, onMounted, onBeforeUnmount, nextTick } = Vue;
	const draggable = window.vuedraggable;
	const loader    = window['vue3-sfc-loader'];

	const sfcOptions = {
		moduleCache: { vue: Vue },
		getFile(url) {
			const requestUrl = url + (url.includes('?') ? '&' : '?') + 'pbv=20260719-12';
			return fetch(requestUrl, { cache: 'no-store' }).then(r => {
				if (!r.ok) throw new Error(url);
				return r.text();
			});
		},
		addStyle(css) {
			const s = Object.assign(document.createElement('style'), { textContent: css });
			document.head.insertBefore(s, document.head.querySelector('style'));
		},
		log(type, msg) { console[type](msg); },
	};
	const WidgetAdvancedControls = defineAsyncComponent(() => loader.loadModule(
		'/js/pagebuilder_elementor/widgets/shared/AdvancedControls.vue',
		sfcOptions
	));
	const TypographyControl = defineAsyncComponent(() => loader.loadModule(
		'/js/pagebuilder_elementor/widgets/shared/TypographyControl.vue',
		sfcOptions
	));

	const widgetMap = {
		container:      '/js/pagebuilder_elementor/widgets/layout/Container.vue',
		container_fluid:'/js/pagebuilder_elementor/widgets/layout/ContainerFluid.vue',
		row_grid:       '/js/pagebuilder_elementor/widgets/layout/RowGrid.vue',
		grid:           '/js/pagebuilder_elementor/widgets/layout/Grid.vue',
		heading:        '/js/pagebuilder_elementor/widgets/basic/Heading.vue',
		text_editor:    '/js/pagebuilder_elementor/widgets/basic/TextEditor.vue',
		image:          '/js/pagebuilder_elementor/widgets/basic/Image.vue',
		image_box:      '/js/pagebuilder_elementor/widgets/general/ImageBox.vue',
		video:          '/js/pagebuilder_elementor/widgets/basic/Video.vue',
		icon:           '/js/pagebuilder_elementor/widgets/basic/Icon.vue',
		button:         '/js/pagebuilder_elementor/widgets/basic/Button.vue',
		divider:        '/js/pagebuilder_elementor/widgets/basic/Divider.vue',
		spacer:         '/js/pagebuilder_elementor/widgets/basic/Spacer.vue',
		tabs:           '/js/pagebuilder_elementor/widgets/general/Tabs.vue',
		accordion:      '/js/pagebuilder_elementor/widgets/advanced/Accordion.vue',
	};

	const _wcache = {};
	function loadWidget(type) {
		if (!_wcache[type]) {
			const path = widgetMap[type];
			_wcache[type] = path
				? defineAsyncComponent(() => loader.loadModule(path, sfcOptions))
				: { template: '<div style="color:red">??' + type + '</div>' };
		}
		return _wcache[type];
	}

	function uid(p)       { return p + '_' + Math.random().toString(36).slice(2, 9); }
	function jclone(v)    { return JSON.parse(JSON.stringify(v)); }
	function isCont(t)    { return t === 'container' || t === 'container_fluid'; }
	function isGrid(t)    { return t === 'row_grid' || t === 'grid'; }
	function isTabs(t)    { return t === 'tabs'; }
	function isAccordion(t) { return t === 'accordion'; }
	function isWgt(t)     { return !isCont(t) && !isGrid(t); }
	function toEmbed(url) {
		if (!url || url.includes('embed/')) return url || '';
		const m = url.match(/youtu\.be\/([^?&]+)/) || url.match(/[?&]v=([^&]+)/);
		return m ? 'https://www.youtube.com/embed/' + m[1] : url;
	}
	function normalizeVideoSourceType(value) {
		const raw = String(value || '').trim().toLowerCase();
		if (raw === 'file') return 'self_hosted';
		return ['youtube', 'vimeo', 'dailymotion', 'self_hosted', 'videopress'].includes(raw) ? raw : 'youtube';
	}
	function isHostedVideoSourceType(value) {
		const source = normalizeVideoSourceType(value);
		return source === 'self_hosted' || source === 'videopress';
	}
	function toPositiveInteger(value) {
		if (value === '' || value === null || value === undefined) return '';
		const num = Number(value);
		if (!Number.isFinite(num) || num < 0) return '';
		return Math.round(num);
	}
	function videoDefaults() {
		return {
			sourceType: 'youtube',
			youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
			youtubeEmbed: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
			vimeoUrl: 'https://vimeo.com/235215203',
			dailymotionUrl: 'https://www.dailymotion.com/video/x84sh87',
			fileUrl: '',
			externalUrl: false,
			startTime: '',
			endTime: '',
			autoplay: false,
			mute: false,
			loop: false,
			playerControls: true,
			captions: false,
			privacyMode: false,
			lazyLoad: false,
			suggestedVideos: 'current_channel',
			introTitle: true,
			introPortrait: true,
			introByline: true,
			controlsColor: '',
			videoInfo: true,
			logo: true,
			downloadButton: true,
			preload: 'metadata',
			poster: '',
			imageOverlay: false,
			overlayImage: '',
			ratio: '16/9',
			cssClass: '',
		};
	}
	const FONT_AWESOME_5_ICON_METADATA_URL = '/assets/plugins/fontawesome/5.15.3/metadata/icons.json';
	const FONT_AWESOME_5_ICON_GROUPS = Object.freeze([
		{ key: 'all', label: 'All Icons', style: null, icon: 'fas fa-bars' },
		{ key: 'regular', label: 'Font Awesome - Regular', style: 'regular', icon: 'far fa-image' },
		{ key: 'solid', label: 'Font Awesome - Solid', style: 'solid', icon: 'fas fa-star' },
		{ key: 'brands', label: 'Font Awesome - Brands', style: 'brands', icon: 'fab fa-font-awesome-flag' },
		{ key: 'light', label: 'Font Awesome - Light', style: 'light', icon: 'fal fa-star' },
		{ key: 'duotone', label: 'Font Awesome - Duotone', style: 'duotone', icon: 'fad fa-star' },
	]);
	const FONT_AWESOME_5_STYLE_LABELS = Object.freeze({
		regular: 'Regular',
		solid: 'Solid',
		brands: 'Brands',
		light: 'Light',
		duotone: 'Duotone',
	});
	const ICON_WIDGET_VIEW_OPTIONS = Object.freeze([
		{ value: 'default', label: 'Default' },
		{ value: 'stacked', label: 'Stacked' },
		{ value: 'framed', label: 'Framed' },
	]);
	const ICON_WIDGET_SHAPE_OPTIONS = Object.freeze([
		{ value: 'circle', label: 'Circle' },
		{ value: 'rounded', label: 'Rounded' },
		{ value: 'square', label: 'Square' },
	]);
	const TABS_WIDGET_BREAKPOINT_OPTIONS = Object.freeze([
		{ value: 'mobile', label: 'Mobile Portrait (< 767px)' },
		{ value: 'tablet', label: 'Tablet Portrait (< 1024px)' },
		{ value: 'none', label: 'None' },
	]);
	const TABS_WIDGET_WIDTH_UNITS = Object.freeze(['px', '%']);
	function fontAwesomeStylePrefix(style) {
		if (style === 'brands') return 'fab';
		if (style === 'light') return 'fal';
		if (style === 'duotone') return 'fad';
		if (style === 'solid') return 'fas';
		return 'far';
	}
	function fontAwesomeStyleLabel(style) {
		return FONT_AWESOME_5_STYLE_LABELS[String(style || '').trim().toLowerCase()] || 'Regular';
	}
	function humanizeIconName(name) {
		const raw = String(name || '').trim();
		if (!raw) return 'Star';
		return raw
			.split('-')
			.filter(Boolean)
			.map((part) => part.charAt(0).toUpperCase() + part.slice(1))
			.join(' ');
	}
	function iconWidgetDefaults() {
		return {
			iconStyle: 'regular',
			iconName: 'star',
			iconClass: 'far fa-star',
			view: 'default',
			shape: 'circle',
			link: '',
			openInNewWindow: false,
			nofollow: false,
			attributes: [],
			cssClass: '',
		};
	}
	function parseIconWidgetClassParts(iconClass) {
		const tokens = String(iconClass || '').trim().split(/\s+/).filter(Boolean);
		let style = '';
		let name = '';
		tokens.forEach((token) => {
			if (token === 'fas') style = 'solid';
			else if (token === 'far') style = 'regular';
			else if (token === 'fab') style = 'brands';
			else if (token === 'fal') style = 'light';
			else if (token === 'fad') style = 'duotone';
			else if (token.startsWith('fa-')) name = token.slice(3);
		});
		return { style, name };
	}
	function iconWidgetClassName(style, name) {
		const iconName = String(name || '').trim() || 'star';
		return fontAwesomeStylePrefix(style) + ' fa-' + iconName;
	}
	function normalizeIconWidgetSettings(settings) {
		if (!settings || typeof settings !== 'object') return;
		const defaults = iconWidgetDefaults();
		Object.keys(defaults).forEach((key) => {
			if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]);
		});
		const parsed = parseIconWidgetClassParts(settings.iconClass);
		const style = String(settings.iconStyle || parsed.style || defaults.iconStyle).trim().toLowerCase();
		const allowedStyle = ['regular', 'solid', 'brands', 'light', 'duotone'].includes(style) ? style : defaults.iconStyle;
		const name = String(settings.iconName || parsed.name || defaults.iconName).trim().toLowerCase().replace(/^fa-/, '') || defaults.iconName;
		const view = String(settings.view || defaults.view).trim().toLowerCase();
		const shape = String(settings.shape || defaults.shape).trim().toLowerCase();
		settings.iconStyle = allowedStyle;
		settings.iconName = name;
		settings.iconClass = iconWidgetClassName(allowedStyle, name);
		settings.view = ['default', 'stacked', 'framed'].includes(view) ? view : defaults.view;
		settings.shape = ['circle', 'rounded', 'square'].includes(shape) ? shape : defaults.shape;
		settings.link = String(settings.link || '').trim();
		settings.openInNewWindow = !!settings.openInNewWindow;
		settings.nofollow = !!settings.nofollow;
		settings.attributes = normalizeAttributes(settings.attributes);
		settings.cssClass = String(settings.cssClass || '').trim();
	}
	function buildFontAwesomeIconLibrary(metadata) {
		const out = [];
		if (!metadata || typeof metadata !== 'object') return out;
		Object.entries(metadata).forEach(([name, meta]) => {
			if (!meta || typeof meta !== 'object') return;
			const styles = Array.isArray(meta.styles) ? meta.styles : [];
			styles.forEach((style) => {
				const safeStyle = String(style || '').trim().toLowerCase();
				if (!['regular', 'solid', 'brands', 'light', 'duotone'].includes(safeStyle)) return;
				const safeName = String(name || '').trim().toLowerCase();
				if (!safeName) return;
				const label = String(meta.label || '').trim() || humanizeIconName(safeName);
				out.push({
					id: safeStyle + ':' + safeName,
					style: safeStyle,
					name: safeName,
					label,
					className: iconWidgetClassName(safeStyle, safeName),
					searchText: (label + ' ' + safeName + ' ' + safeStyle).toLowerCase(),
				});
			});
		});
		return out.sort((a, b) => {
			if (a.label === b.label) return a.style.localeCompare(b.style);
			return a.label.localeCompare(b.label);
		});
	}
	function tabsItemDefaults(index = 0) {
		return {
			id: uid('tab'),
			title: 'Tab #' + (index + 1),
			iconClass: '',
			activeIconClass: '',
			cssId: '',
			children: [],
		};
	}
	function tabsWidgetDefaultItems() {
		return [tabsItemDefaults(0), tabsItemDefaults(1), tabsItemDefaults(2)];
	}
	function tabsWidgetDefaults() {
		return {
			direction: 'row',
			justify: 'flex-start',
			alignTitle: 'center',
			tabWidth: '',
			tabWidthUnit: 'px',
			horizontalScroll: false,
			breakpoint: 'mobile',
			activeTabId: '',
			cssClass: '',
		};
	}
	function accordionItemDefaults(index = 0) {
		return {
			id: uid('accordion_item'),
			title: 'Item #' + (index + 1),
			cssId: '',
			children: [],
		};
	}
	function accordionWidgetDefaultItems() {
		return [accordionItemDefaults(0), accordionItemDefaults(1), accordionItemDefaults(2)];
	}
	function widgetAdvancedDefaults() {
		const defaults = {
			widthMode: 'default',
			position: 'default', horizontalOrientation: 'left', verticalOrientation: 'top',
			cssId: '', cssClass: '',
			displayConditions: [], cacheMode: 'default',
			animateWithAI: false,
			scrollingEffects: false,
			verticalScrollEnabled: false, verticalScrollDirection: 'up', verticalScrollSpeed: 4, verticalScrollViewportStart: 0, verticalScrollViewportEnd: 100,
			horizontalScrollEnabled: false, horizontalScrollDirection: 'left', horizontalScrollSpeed: 4, horizontalScrollViewportStart: 0, horizontalScrollViewportEnd: 100,
			transparencyEnabled: false, transparencyDirection: 'fade-in', transparencyLevel: 5, transparencyViewportStart: 0, transparencyViewportEnd: 100,
			blurEnabled: false, blurDirection: 'fade-in', blurLevel: 5, blurViewportStart: 0, blurViewportEnd: 100,
			rotateEnabled: false, rotateDirection: 'left', rotateSpeed: 4, rotateViewportStart: 0, rotateViewportEnd: 100,
			scaleEnabled: false, scaleDirection: 'up', scaleSpeed: 4, scaleViewportStart: 0, scaleViewportEnd: 100,
			scrollApplyDesktop: true, scrollApplyTablet: true, scrollApplyMobile: true, effectsRelativeTo: 'default',
			mouseEffects: false, mouseTrackEnabled: false, mouseTrackDirection: 'direct', mouseTrackSpeed: 1,
			tilt3dEnabled: false, tilt3dDirection: 'direct', tilt3dSpeed: 1,
			sticky: 'none', stickyOnDesktop: true, stickyOnTablet: true, stickyOnMobile: true, stickyEffectsOffset: 0, stickyAnchorOffset: 0, stickyStayInColumn: false,
			entranceAnimation: '', entranceDuration: 'normal', entranceDelay: 0,
			transformState: 'normal', transformRotate: '0deg', transformRotateX: '0deg', transformRotateY: '0deg', transformPerspective: '0px', transformScale: 1,
			transformSkewX: '0deg', transformSkewY: '0deg', transformFlipHorizontal: false, transformFlipVertical: false,
			transformRotateHover: '0deg', transformRotateXHover: '0deg', transformRotateYHover: '0deg', transformPerspectiveHover: '0px', transformScaleHover: 1,
			transformSkewXHover: '0deg', transformSkewYHover: '0deg', transformFlipHorizontalHover: false, transformFlipVerticalHover: false,
			transformOriginX: 'center', transformOriginY: 'center', transformHoverDuration: 0.3,
			advancedBackgroundType: 'none', advancedBackgroundColor: '', advancedBackgroundImage: '', advancedBackgroundPosition: 'center center', advancedBackgroundAttachment: 'scroll', advancedBackgroundRepeat: 'no-repeat', advancedBackgroundSize: 'cover',
			advancedGradientColorOne: '#ffffff', advancedGradientLocationOne: 0, advancedGradientColorTwo: '#000000', advancedGradientLocationTwo: 100, advancedGradientType: 'linear', advancedGradientAngle: 180, advancedGradientPosition: 'center center',
			advancedBackgroundTypeHover: 'none', advancedBackgroundColorHover: '', advancedBackgroundImageHover: '', advancedBackgroundPositionHover: 'center center', advancedBackgroundAttachmentHover: 'scroll', advancedBackgroundRepeatHover: 'no-repeat', advancedBackgroundSizeHover: 'cover',
			advancedGradientColorOneHover: '#ffffff', advancedGradientLocationOneHover: 0, advancedGradientColorTwoHover: '#000000', advancedGradientLocationTwoHover: 100, advancedGradientTypeHover: 'linear', advancedGradientAngleHover: 180, advancedGradientPositionHover: 'center center', advancedBackgroundHoverDuration: 0.3,
			advancedBorderType: 'none', advancedBorderWidth: '0px', advancedBorderColor: '#000000', advancedBoxShadowEnabled: false, advancedBoxShadowColor: 'rgba(0,0,0,.2)', advancedBoxShadowX: '0px', advancedBoxShadowY: '4px', advancedBoxShadowBlur: '16px', advancedBoxShadowSpread: '0px', advancedBoxShadowInset: false,
			advancedBorderTypeHover: 'none', advancedBorderWidthHover: '0px', advancedBorderColorHover: '#000000', advancedBoxShadowEnabledHover: false, advancedBoxShadowColorHover: 'rgba(0,0,0,.2)', advancedBoxShadowXHover: '0px', advancedBoxShadowYHover: '4px', advancedBoxShadowBlurHover: '16px', advancedBoxShadowSpreadHover: '0px', advancedBoxShadowInsetHover: false, advancedBorderHoverDuration: 0.3,
			maskEnabled: false, maskShape: 'circle', maskCustomImage: '', maskCustomSvg: '',
			attributes: [], customCssCode: '',
			hideDesktop: false, hideTablet: false, hideMobile: false,
		};
		const responsiveDefaults = {
			marginTop: '0px', marginRight: '0px', marginBottom: '0px', marginLeft: '0px',
			paddingTop: '0px', paddingRight: '0px', paddingBottom: '0px', paddingLeft: '0px',
			widthMode: 'default', customWidth: '', alignSelf: 'auto', orderMode: 'default', order: '', sizeMode: 'none', flexGrow: 0, flexShrink: 1,
			positionX: '0px', positionY: '0px', zIndex: '', stickyOffset: '0px',
			transformOffsetX: '0px', transformOffsetY: '0px', transformOffsetXHover: '0px', transformOffsetYHover: '0px',
			advancedBorderRadius: '0px', advancedBorderRadiusHover: '0px',
			maskSize: 'fit', maskScale: 100, maskPosition: 'center center', maskPositionX: '50%', maskPositionY: '50%', maskRepeat: 'no-repeat',
		};
		Object.entries(responsiveDefaults).forEach(([key, value]) => {
			defaults[key] = value;
			defaults[key + 'Tablet'] = '';
			defaults[key + 'Mobile'] = '';
		});
		return defaults;
	}
	function normalizeWidgetAdvancedSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = widgetAdvancedDefaults();
		Object.keys(defaults).forEach((key) => {
			if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]);
		});
		settings.displayConditions = Array.isArray(settings.displayConditions) ? settings.displayConditions : [];
		settings.attributes = normalizeAttributes(settings.attributes);
		settings.cacheMode = ['default', 'inactive', 'active'].includes(settings.cacheMode) ? settings.cacheMode : 'default';
		const widthModes = ['default', 'full', 'inline', 'custom'];
		settings.widthMode = widthModes.includes(settings.widthMode) ? settings.widthMode : 'default';
		['widthModeTablet', 'widthModeMobile'].forEach((key) => {
			const value = settings[key];
			settings[key] = value === '' || value == null ? '' : (widthModes.includes(value) ? value : '');
		});
		settings.position = ['default', 'absolute', 'fixed'].includes(settings.position) ? settings.position : 'default';
		settings.animateWithAI = false;
		return settings;
	}
	function widgetAdvancedPreviewStyle(settings, device) {
		const s = settings || {};
		const safeDevice = device === 'tablet' || device === 'mobile' ? device : 'desktop';
		const cascadeDevices = safeDevice === 'mobile' ? ['mobile', 'tablet', 'desktop'] : (safeDevice === 'tablet' ? ['tablet', 'desktop'] : ['desktop']);
		const get = (base, fallback = '') => {
			for (const candidateDevice of cascadeDevices) {
				const value = s[responsiveKey(base, candidateDevice)];
				if (value !== '' && value != null) return value;
			}
			return fallback;
		};
		const style = {
			marginTop: cssSpace(get('marginTop', '0px'), '0'), marginRight: cssSpace(get('marginRight', '0px'), '0'),
			marginBottom: cssSpace(get('marginBottom', '0px'), '0'), marginLeft: cssSpace(get('marginLeft', '0px'), '0'),
			paddingTop: cssSpace(get('paddingTop', '0px'), '0'), paddingRight: cssSpace(get('paddingRight', '0px'), '0'),
			paddingBottom: cssSpace(get('paddingBottom', '0px'), '0'), paddingLeft: cssSpace(get('paddingLeft', '0px'), '0'),
			alignSelf: get('alignSelf', 'auto'),
			borderRadius: cssSize(get('advancedBorderRadius', '0px'), '0'),
			transition: `background ${Number(s.advancedBackgroundHoverDuration) || 0.3}s ease, border ${Number(s.advancedBorderHoverDuration) || 0.3}s ease, box-shadow ${Number(s.advancedBorderHoverDuration) || 0.3}s ease, transform ${Number(s.transformHoverDuration) || 0.3}s ease`,
		};
		const hidden = safeDevice === 'desktop' ? s.hideDesktop : (safeDevice === 'tablet' ? s.hideTablet : s.hideMobile);
		if (hidden === true || hidden === 'true' || hidden === 1 || hidden === '1') style.display = 'none';
		const rawWidthMode = get('widthMode', 'default');
		const widthMode = ['default', 'full', 'inline', 'custom'].includes(rawWidthMode) ? rawWidthMode : 'default';
		if (widthMode === 'full') style.width = '100%';
		else if (widthMode === 'inline') style.width = 'fit-content';
		else if (widthMode === 'custom') style.width = cssSize(get('customWidth', ''), 'auto');
		else style.width = 'auto';
		const orderMode = get('orderMode', 'default');
		if (orderMode === 'start') style.order = -9999;
		if (orderMode === 'end') style.order = 9999;
		if (orderMode === 'custom' && Number.isFinite(Number(get('order', '')))) style.order = Number(get('order'));
		const sizeMode = get('sizeMode', 'none');
		if (sizeMode === 'grow') style.flex = '1 1 0';
		if (sizeMode === 'shrink') style.flex = '0 1 auto';
		if (sizeMode === 'custom') style.flex = `${Number(get('flexGrow', 0)) || 0} ${Number(get('flexShrink', 1)) || 1} auto`;
		if (['absolute', 'fixed'].includes(s.position)) {
			style.position = s.position;
			style[s.horizontalOrientation === 'right' ? 'right' : 'left'] = cssSpace(get('positionX', '0px'), '0');
			style[s.verticalOrientation === 'bottom' ? 'bottom' : 'top'] = cssSpace(get('positionY', '0px'), '0');
		}
		if (s.sticky === 'top' || s.sticky === 'bottom') {
			style.position = 'sticky';
			style[s.sticky] = cssSpace(get('stickyOffset', '0px'), '0');
		}
		if (get('zIndex', '') !== '') style.zIndex = Number(get('zIndex')) || 0;
		const backgroundValue = (hover = false) => {
			const suffix = hover ? 'Hover' : '';
			const type = s['advancedBackgroundType' + suffix] || 'none';
			if (type === 'classic') {
				const image = String(s['advancedBackgroundImage' + suffix] || '').trim();
				if (image) return `url("${image.replace(/["\\]/g, '')}")`;
				return String(s['advancedBackgroundColor' + suffix] || 'transparent');
			}
			if (type === 'gradient') {
				const one = s['advancedGradientColorOne' + suffix] || '#fff';
				const two = s['advancedGradientColorTwo' + suffix] || '#000';
				const oneLoc = clamp(Number(s['advancedGradientLocationOne' + suffix]) || 0, 0, 100);
				const twoLoc = clamp(Number(s['advancedGradientLocationTwo' + suffix]) || 100, 0, 100);
				return s['advancedGradientType' + suffix] === 'radial'
					? `radial-gradient(circle, ${one} ${oneLoc}%, ${two} ${twoLoc}%)`
					: `linear-gradient(${clamp(Number(s['advancedGradientAngle' + suffix]) || 180, 0, 360)}deg, ${one} ${oneLoc}%, ${two} ${twoLoc}%)`;
			}
			return 'none';
		};
		const normalBackground = backgroundValue(false);
		if (normalBackground !== 'none') {
			if (String(normalBackground).startsWith('#') || String(normalBackground).startsWith('rgb') || normalBackground === 'transparent') style.backgroundColor = normalBackground;
			else style.backgroundImage = normalBackground;
		}
		style['--pb-advanced-hover-background'] = backgroundValue(true);
		const borderType = ['solid', 'double', 'dotted', 'dashed', 'groove'].includes(s.advancedBorderType) ? s.advancedBorderType : 'none';
		style.borderStyle = borderType;
		style.borderWidth = borderType === 'none' ? '0' : cssSize(s.advancedBorderWidth, '1px');
		style.borderColor = String(s.advancedBorderColor || 'transparent');
		if (s.advancedBoxShadowEnabled) style.boxShadow = `${cssSize(s.advancedBoxShadowX, '0')} ${cssSize(s.advancedBoxShadowY, '0')} ${cssSize(s.advancedBoxShadowBlur, '0')} ${cssSize(s.advancedBoxShadowSpread, '0')} ${s.advancedBoxShadowColor || 'rgba(0,0,0,.2)'}${s.advancedBoxShadowInset ? ' inset' : ''}`;
		const transform = [
			`perspective(${cssSize(s.transformPerspective, '0px')})`, `translate(${cssSpace(get('transformOffsetX', '0px'), '0')}, ${cssSpace(get('transformOffsetY', '0px'), '0')})`,
			`rotate(${cssSize(s.transformRotate, '0deg')})`, `rotateX(${cssSize(s.transformRotateX, '0deg')})`, `rotateY(${cssSize(s.transformRotateY, '0deg')})`,
			`scale(${Number(s.transformScale) || 1})`, `skew(${cssSize(s.transformSkewX, '0deg')}, ${cssSize(s.transformSkewY, '0deg')})`,
			s.transformFlipHorizontal ? 'scaleX(-1)' : '', s.transformFlipVertical ? 'scaleY(-1)' : '',
		].filter(Boolean).join(' ');
		style['--pb-advanced-transform'] = transform;
		style.transform = 'var(--pb-advanced-transform)';
		const hoverTransform = [
			`perspective(${cssSize(s.transformPerspectiveHover, '0px')})`, `translate(${cssSpace(get('transformOffsetXHover', '0px'), '0')}, ${cssSpace(get('transformOffsetYHover', '0px'), '0')})`,
			`rotate(${cssSize(s.transformRotateHover, '0deg')})`, `rotateX(${cssSize(s.transformRotateXHover, '0deg')})`, `rotateY(${cssSize(s.transformRotateYHover, '0deg')})`,
			`scale(${Number(s.transformScaleHover) || 1})`, `skew(${cssSize(s.transformSkewXHover, '0deg')}, ${cssSize(s.transformSkewYHover, '0deg')})`,
			s.transformFlipHorizontalHover ? 'scaleX(-1)' : '', s.transformFlipVerticalHover ? 'scaleY(-1)' : '',
		].filter(Boolean).join(' ');
		style['--pb-advanced-hover-transform'] = hoverTransform;
		style['--pb-advanced-hover-border-style'] = ['solid', 'double', 'dotted', 'dashed', 'groove'].includes(s.advancedBorderTypeHover) ? s.advancedBorderTypeHover : 'none';
		style['--pb-advanced-hover-border-width'] = cssSize(s.advancedBorderWidthHover, '0');
		style['--pb-advanced-hover-border-color'] = String(s.advancedBorderColorHover || 'transparent');
		style.transformOrigin = `${s.transformOriginX || 'center'} ${s.transformOriginY || 'center'}`;
		if (s.maskEnabled) {
			const maskImage = s.maskShape === 'custom' && s.maskCustomImage ? `url("${String(s.maskCustomImage).replace(/["\\]/g, '')}")` : 'radial-gradient(circle, #000 60%, transparent 61%)';
			style.maskImage = maskImage; style.WebkitMaskImage = maskImage;
			const maskSize = get('maskSize', 'fit');
			style.maskSize = style.WebkitMaskSize = maskSize === 'fill' ? 'cover' : (maskSize === 'custom' ? `${Number(get('maskScale', 100)) || 100}%` : 'contain');
			style.maskPosition = style.WebkitMaskPosition = get('maskPosition', 'center center') === 'custom' ? `${get('maskPositionX', '50%')} ${get('maskPositionY', '50%')}` : get('maskPosition', 'center center');
			style.maskRepeat = style.WebkitMaskRepeat = get('maskRepeat', 'no-repeat');
		}
		return style;
	}
	function accordionWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			itemPosition: 'stretch',
			itemPositionTablet: '',
			itemPositionMobile: '',
			iconPosition: 'start',
			iconPositionTablet: '',
			iconPositionMobile: '',
			expandIconSource: 'library',
			expandIconClass: 'fas fa-plus',
			expandIconSvg: '',
			collapseIconSource: 'library',
			collapseIconClass: 'fas fa-minus',
			collapseIconSvg: '',
			titleTag: 'div',
			faqSchema: false,
			defaultState: 'first-expanded',
			maxExpanded: 'one',
			animationDuration: 400,
			accordionItemGap: '0px',
			accordionItemGapTablet: '',
			accordionItemGapMobile: '',
			accordionContentDistance: '0px',
			accordionContentDistanceTablet: '',
			accordionContentDistanceMobile: '',
			accordionBorderRadius: '0px',
			accordionBorderRadiusTablet: '',
			accordionBorderRadiusMobile: '',
			accordionPadding: '0px',
			accordionPaddingTablet: '',
			accordionPaddingMobile: '',
			headerFontFamily: 'inherit',
			headerFontSize: '16px',
			headerFontSizeTablet: '',
			headerFontSizeMobile: '',
			headerFontWeight: '600',
			headerLineHeight: '1.4em',
			headerLineHeightTablet: '',
			headerLineHeightMobile: '',
			headerLetterSpacing: '0px',
			headerLetterSpacingTablet: '',
			headerLetterSpacingMobile: '',
			headerWordSpacing: '0px',
			headerWordSpacingTablet: '',
			headerWordSpacingMobile: '',
			headerTextTransform: 'none',
			headerFontStyle: 'normal',
			headerTextDecoration: 'none',
			headerIconSize: '16px',
			headerIconSizeTablet: '',
			headerIconSizeMobile: '',
			headerIconSpacing: '12px',
			headerIconSpacingTablet: '',
			headerIconSpacingMobile: '',
			contentBackgroundType: 'classic',
			contentBackgroundColor: '#ffffff',
			contentGradientColorOne: '#ffffff',
			contentGradientLocationOne: 0,
			contentGradientColorTwo: '#f4f6f8',
			contentGradientLocationTwo: 100,
			contentGradientType: 'linear',
			contentGradientAngle: 180,
			contentGradientPosition: 'center center',
			contentBorderType: 'none',
			contentBorderWidth: '0px',
			contentBorderColor: '#d5dae3',
			contentBorderRadius: '0px',
			contentBorderRadiusTablet: '',
			contentBorderRadiusMobile: '',
			contentPadding: '20px',
			contentPaddingTablet: '',
			contentPaddingMobile: '',
			cssClass: '',
			...accordionStateDefaults('Normal', '#ffffff', '#1f2937', '#667085'),
			...accordionStateDefaults('Hover', '#f8fafc', '#344054', '#475467'),
			...accordionStateDefaults('Active', '#f2f4f7', '#101828', '#344054'),
		};
	}
	function imageBoxFilterDefaults() {
		return { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 };
	}
	function imageBoxWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			imageUrl: '',
			imageAlt: '',
			imageResolution: 'full',
			title: 'This is the heading',
			description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
			linkUrl: '',
			linkTarget: '',
			linkNofollow: false,
			linkCustomAttributes: [],
			titleTag: 'h3',
			dynamicBindings: {},
			imagePosition: 'top',
			imagePositionTablet: '',
			imagePositionMobile: '',
			alignment: 'center',
			alignmentTablet: '',
			alignmentMobile: '',
			imageSpacing: '15px',
			imageSpacingTablet: '',
			imageSpacingMobile: '',
			contentSpacing: '0px',
			contentSpacingTablet: '',
			contentSpacingMobile: '',
			imageWidth: '30%',
			imageWidthTablet: '',
			imageWidthMobile: '',
			imageBorderType: 'none',
			imageBorderWidth: '1px',
			imageBorderColor: '#000000',
			imageBorderRadius: '0px',
			imageBorderRadiusTablet: '',
			imageBorderRadiusMobile: '',
			imageNormalFilter: imageBoxFilterDefaults(),
			imageHoverFilter: imageBoxFilterDefaults(),
			imageNormalOpacity: 1,
			imageHoverOpacity: 1,
			imageHoverTransition: 0.3,
			titleColor: '',
			titleFontFamily: 'inherit',
			titleFontSize: '29px',
			titleFontSizeTablet: '',
			titleFontSizeMobile: '',
			titleFontWeight: '400',
			titleLineHeight: '1.2em',
			titleLineHeightTablet: '',
			titleLineHeightMobile: '',
			titleLetterSpacing: '0px',
			titleLetterSpacingTablet: '',
			titleLetterSpacingMobile: '',
			titleWordSpacing: '0px',
			titleWordSpacingTablet: '',
			titleWordSpacingMobile: '',
			titleTextTransform: 'none',
			titleFontStyle: 'normal',
			titleTextDecoration: 'none',
			titleTextStrokeWidth: '0px',
			titleTextStrokeColor: '#000000',
			titleTextShadow: 'none',
			descriptionColor: '',
			descriptionFontFamily: 'inherit',
			descriptionFontSize: '16px',
			descriptionFontSizeTablet: '',
			descriptionFontSizeMobile: '',
			descriptionFontWeight: '400',
			descriptionLineHeight: '1.5em',
			descriptionLineHeightTablet: '',
			descriptionLineHeightMobile: '',
			descriptionLetterSpacing: '0px',
			descriptionLetterSpacingTablet: '',
			descriptionLetterSpacingMobile: '',
			descriptionWordSpacing: '0px',
			descriptionWordSpacingTablet: '',
			descriptionWordSpacingMobile: '',
			descriptionTextTransform: 'none',
			descriptionFontStyle: 'normal',
			descriptionTextDecoration: 'none',
			descriptionTextShadow: 'none',
		};
	}
	const imageBoxResolutionOptions = Object.freeze(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full']);
	const imageBoxTitleTagOptions = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);
	const imageBoxPositionOptions = Object.freeze(['top', 'left', 'right']);
	const imageBoxAlignmentOptions = Object.freeze(['left', 'center', 'right', 'justify']);
	const imageBoxBorderTypeOptions = Object.freeze(['none', 'solid', 'double', 'dotted', 'dashed', 'groove']);
	function normalizeImageBoxFilter(value) {
		const source = value && typeof value === 'object' && !Array.isArray(value) ? value : {};
		return {
			blur: clamp(Number(source.blur) || 0, 0, 100),
			brightness: clamp(Number(source.brightness == null ? 100 : source.brightness) || 0, 0, 200),
			contrast: clamp(Number(source.contrast == null ? 100 : source.contrast) || 0, 0, 200),
			saturation: clamp(Number(source.saturation == null ? 100 : source.saturation) || 0, 0, 200),
			hue: clamp(Number(source.hue) || 0, 0, 360),
		};
	}
	function normalizeImageBoxSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = imageBoxWidgetDefaults();
		Object.keys(defaults).forEach((key) => {
			if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]);
		});
		settings.imageResolution = imageBoxResolutionOptions.includes(settings.imageResolution) ? settings.imageResolution : 'full';
		settings.titleTag = imageBoxTitleTagOptions.includes(settings.titleTag) ? settings.titleTag : 'h3';
		settings.imagePosition = imageBoxPositionOptions.includes(settings.imagePosition) ? settings.imagePosition : 'top';
		settings.alignment = imageBoxAlignmentOptions.includes(settings.alignment) ? settings.alignment : 'center';
		['imagePositionTablet', 'imagePositionMobile'].forEach((key) => {
			settings[key] = settings[key] === '' || imageBoxPositionOptions.includes(settings[key]) ? settings[key] : '';
		});
		['alignmentTablet', 'alignmentMobile'].forEach((key) => {
			settings[key] = settings[key] === '' || imageBoxAlignmentOptions.includes(settings[key]) ? settings[key] : '';
		});
		settings.imageBorderType = imageBoxBorderTypeOptions.includes(settings.imageBorderType) ? settings.imageBorderType : 'none';
		settings.imageNormalFilter = normalizeImageBoxFilter(settings.imageNormalFilter);
		settings.imageHoverFilter = normalizeImageBoxFilter(settings.imageHoverFilter);
		settings.imageNormalOpacity = clamp(Number(settings.imageNormalOpacity), 0, 1);
		settings.imageHoverOpacity = clamp(Number(settings.imageHoverOpacity), 0, 1);
		settings.imageHoverTransition = clamp(Number(settings.imageHoverTransition) || 0.3, 0, 10);
		settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
		settings.linkNofollow = !!settings.linkNofollow;
		settings.linkCustomAttributes = normalizeAttributes(settings.linkCustomAttributes);
		settings.dynamicBindings = settings.dynamicBindings && typeof settings.dynamicBindings === 'object' && !Array.isArray(settings.dynamicBindings)
			? { ...settings.dynamicBindings }
			: {};
		['imageUrl', 'imageAlt', 'title', 'description', 'linkUrl'].forEach((key) => {
			settings[key] = String(settings[key] == null ? '' : settings[key]);
		});
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}
	function accordionStateDefaults(suffix, backgroundColor, titleColor, iconColor) {
		return {
			['accordionBackgroundType' + suffix]: 'classic',
			['accordionBackgroundColor' + suffix]: backgroundColor,
			['accordionGradientColorOne' + suffix]: backgroundColor,
			['accordionGradientLocationOne' + suffix]: 0,
			['accordionGradientColorTwo' + suffix]: '#eef2f6',
			['accordionGradientLocationTwo' + suffix]: 100,
			['accordionGradientType' + suffix]: 'linear',
			['accordionGradientAngle' + suffix]: 180,
			['accordionGradientPosition' + suffix]: 'center center',
			['accordionBorderType' + suffix]: 'solid',
			['accordionBorderWidth' + suffix]: '1px',
			['accordionBorderColor' + suffix]: '#d5dae3',
			['headerTitleColor' + suffix]: titleColor,
			['headerTextShadow' + suffix]: 'none',
			['headerTextStrokeWidth' + suffix]: '0px',
			['headerTextStrokeColor' + suffix]: titleColor,
			['headerIconColor' + suffix]: iconColor,
		};
	}
	const ACCORDION_STYLE_STATES = Object.freeze([
		{ value: 'normal', label: 'Normal' },
		{ value: 'hover', label: 'Hover' },
		{ value: 'active', label: 'Active' },
	]);
	const ACCORDION_BORDER_TYPES = Object.freeze(['default', 'none', 'solid', 'double', 'dotted', 'dashed', 'groove']);
	const ACCORDION_GRADIENT_TYPES = Object.freeze(['linear', 'radial']);
	function normalizeTabsDirection(value) {
		const raw = String(value || '').trim().toLowerCase();
		return ['row', 'row-reverse', 'column', 'column-reverse'].includes(raw) ? raw : 'row';
	}
	function normalizeTabsJustify(value) {
		const raw = String(value || '').trim().toLowerCase();
		return ['flex-start', 'center', 'flex-end', 'stretch'].includes(raw) ? raw : 'flex-start';
	}
	function normalizeTabsAlignTitle(value) {
		const raw = String(value || '').trim().toLowerCase();
		return ['left', 'center', 'right'].includes(raw) ? raw : 'center';
	}
	function normalizeTabsBreakpoint(value) {
		const raw = String(value || '').trim().toLowerCase();
		return ['mobile', 'tablet', 'none'].includes(raw) ? raw : 'mobile';
	}
	function normalizeTabsWidthUnit(value) {
		const raw = String(value || '').trim().toLowerCase();
		return raw === '%' ? '%' : 'px';
	}
	function normalizeTabsItemClass(value) {
		return String(value || '').trim();
	}
	function normalizeTabsCssId(value) {
		return String(value || '').trim().replace(/\s+/g, '-');
	}
	function normalizeTabsWidthValue(value) {
		if (value === '' || value === null || value === undefined) return '';
		const num = Number(value);
		if (!Number.isFinite(num) || num <= 0) return '';
		return String(Math.round(num * 100) / 100);
	}
	function tabsRowDirection(value) {
		const direction = normalizeTabsDirection(value);
		return direction === 'row' || direction === 'row-reverse';
	}
	function tabsIconClassForItem(item, active = false) {
		if (!item || typeof item !== 'object') return '';
		if (active && String(item.activeIconClass || '').trim()) return String(item.activeIconClass || '').trim();
		return String(item.iconClass || '').trim();
	}
	function isInteractiveCanvasTarget(target) {
		if (!target || typeof target.closest !== 'function') return false;
		return !!target.closest('[data-pb-interactive="true"]');
	}
	function isNestedCanvasDropTarget(target) {
		if (!target || typeof target.closest !== 'function') return false;
		return !!target.closest('[data-pb-nested-dropzone="true"]');
	}
	function findNestedCanvasDropTargetFromEvent(event, parentEl = null) {
		const target = event && event.target && typeof event.target.closest === 'function'
			? event.target.closest('[data-pb-nested-dropzone="true"]')
			: null;
		if (target && (!parentEl || (target !== parentEl && parentEl.contains(target)))) return target;

		const x = Number(event && event.clientX);
		const y = Number(event && event.clientY);
		if (!Number.isFinite(x) || !Number.isFinite(y) || !document.elementsFromPoint) return null;

		const elements = document.elementsFromPoint(x, y);
		for (const el of elements) {
			if (!el || el === parentEl || typeof el.closest !== 'function') continue;
			const nested = el.closest('[data-pb-nested-dropzone="true"]');
			if (!nested || nested === parentEl) continue;
			if (!parentEl || parentEl.contains(nested)) return nested;
		}
		return null;
	}
	function clamp(v, min, max) {
		return Math.min(max, Math.max(min, v));
	}
	function cssSize(value, fallback = '') {
		if (value === null || value === undefined || value === '') return fallback;
		if (typeof value === 'number') return value === 0 ? '0' : value + 'px';
		const out = String(value).trim();
		if (!out) return fallback;
		if (/^-?\d+(\.\d+)?$/.test(out)) return out === '0' ? '0' : out + 'px';
		return out;
	}
	function cssSpace(value, fallback = '0') {
		if (value === null || value === undefined || value === '') return fallback;
		const out = String(value).trim();
		if (!out) return fallback;
		if (out.toLowerCase() === 'auto') return 'auto';
		return cssSize(out, fallback);
	}
	function colorWithOpacity(color, opacity) {
		const raw = String(color || '').trim();
		if (!raw) return 'transparent';
		const alpha = Number(opacity);
		if (!Number.isFinite(alpha) || alpha >= 1) return raw;
		if (raw.startsWith('rgba(')) return raw.replace(/rgba\((.+),\s*[\d.]+\)/, 'rgba($1, ' + alpha + ')');
		if (raw.startsWith('rgb(')) return raw.replace('rgb(', 'rgba(').replace(')', ', ' + alpha + ')');
		const hex = raw.replace('#', '');
		const full = hex.length === 3 ? hex.split('').map(part => part + part).join('') : hex;
		if (/^[0-9a-fA-F]{6}$/.test(full)) {
			const r = parseInt(full.slice(0, 2), 16);
			const g = parseInt(full.slice(2, 4), 16);
			const b = parseInt(full.slice(4, 6), 16);
			return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + alpha + ')';
		}
		return raw;
	}
	function borderRadiusValue(settings) {
		if (settings.borderRadius) return cssSize(settings.borderRadius, '0');
		return [
			cssSize(settings.borderRadiusTL, '0'),
			cssSize(settings.borderRadiusTR, '0'),
			cssSize(settings.borderRadiusBR, '0'),
			cssSize(settings.borderRadiusBL, '0'),
		].join(' ');
	}
	function shadowValue(settings) {
		if (settings.shadowEnabled) {
			return [
				cssSize(settings.shadowH, '0'),
				cssSize(settings.shadowV, '0'),
				cssSize(settings.shadowBlur, '0'),
				cssSize(settings.shadowSpread, '0'),
				colorWithOpacity(settings.shadowColor || '#000000', settings.shadowOpacity == null ? 0.15 : settings.shadowOpacity),
			].join(' ');
		}
		return settings.boxShadow || 'none';
	}
	function gridRowsTemplate(value) {
		const out = String(value == null ? '' : value).trim();
		if (!out || out.toLowerCase() === 'auto') return '';
		if (/^\d+$/.test(out)) return 'repeat(' + Math.max(1, Number(out)) + ', minmax(0, auto))';
		return out;
	}
	function containerGridRowsTemplate(value) {
		const out = String(value == null ? '' : value).trim();
		if (!out || out.toLowerCase() === 'auto') return '';
		if (/^\d+$/.test(out)) return 'repeat(' + Math.max(1, Number(out)) + ', minmax(68px, auto))';
		const legacyFr = out.match(/^(\d+(?:\.\d+)?)fr$/i);
		if (legacyFr) return 'repeat(' + Math.max(1, Number(legacyFr[1])) + ', minmax(68px, auto))';
		return out;
	}
	function containerGridRowsCount(value) {
		const raw = String(value == null ? '' : value).trim();
		if (!raw || raw.toLowerCase() === 'auto') return 1;
		const repeatMatch = raw.match(/^repeat\(\s*(\d+)\s*,/i);
		if (repeatMatch) return clamp(Number(repeatMatch[1]), 1, 12);
		const frMatch = raw.match(/^(\d+(?:\.\d+)?)fr$/i);
		if (frMatch) return clamp(Number(frMatch[1]), 1, 12);
		const numericMatch = raw.match(/^(\d+(?:\.\d+)?)/);
		if (numericMatch) return clamp(Number(numericMatch[1]), 1, 12);
		return 1;
	}
	function normalizeAttributes(attrs) {
		if (!Array.isArray(attrs)) return [];
		return attrs
			.map(attr => ({
				name: String(attr && attr.name ? attr.name : '').trim(),
				value: attr && attr.value != null ? String(attr.value) : '',
			}))
			.filter(attr => attr.name);
	}
	function responsiveSuffix(device) {
		if (device === 'tablet') return 'Tablet';
		if (device === 'mobile') return 'Mobile';
		return '';
	}
	function responsiveKey(base, device) {
		return base + responsiveSuffix(device);
	}
	function cloneSettingValue(value) {
		if (Array.isArray(value) || (value && typeof value === 'object')) return jclone(value);
		return value;
	}
	function seedResponsiveSettings(settings, force = false) {
		if (!settings || typeof settings !== 'object') return;
		const bases = new Set();
		Object.keys(settings).forEach((key) => {
			if (key.endsWith('Tablet')) bases.add(key.slice(0, -6));
			if (key.endsWith('Mobile')) bases.add(key.slice(0, -6));
		});
		bases.forEach((base) => {
			if (!Object.prototype.hasOwnProperty.call(settings, base)) return;
			const baseValue = settings[base];
			['Tablet', 'Mobile'].forEach((suffix) => {
				const key = base + suffix;
				if (!Object.prototype.hasOwnProperty.call(settings, key)) return;
				const current = settings[key];
				if (force || current === '' || current === null || current === undefined) {
					settings[key] = cloneSettingValue(baseValue);
				}
			});
		});
	}
	function containerDefaults(type = 'container') {
		const isFluid = type === 'container_fluid';
		return {
			displayType: 'flex',
			contentWidth: 'full',
			containerWidth: '100%',
			maxWidth: 'auto',
			minHeight: 'auto',
			containerWidthTablet: '',
			containerWidthMobile: '',
			maxWidthTablet: '',
			maxWidthMobile: '',
			minHeightTablet: '',
			minHeightMobile: '',
			direction: 'row',
			directionTablet: '',
			directionMobile: '',
			justifyContent: 'flex-start',
			justifyContentTablet: '',
			justifyContentMobile: '',
			alignItems: 'flex-start',
			alignItemsTablet: '',
			alignItemsMobile: '',
			alignContent: 'stretch',
			alignContentTablet: '',
			alignContentMobile: '',
			alignSelf: 'auto',
			alignSelfTablet: '',
			alignSelfMobile: '',
			order: '',
			orderTablet: '',
			orderMobile: '',
			sizeMode: 'default',
			sizeModeTablet: '',
			sizeModeMobile: '',
			gap: '0',
			flexRowGap: '0',
			flexColumnGap: '0',
			flexWrap: 'nowrap',
			flexRowGapTablet: '',
			flexRowGapMobile: '',
			flexColumnGapTablet: '',
			flexColumnGapMobile: '',
			flexWrapTablet: '',
			flexWrapMobile: '',
			containerGapLinked: true,
			gridColumns: 3,
			gridColumnsTablet: '',
			gridColumnsMobile: '',
			gridRows: '1',
			gridRowsTablet: '',
			gridRowsMobile: '',
			gridColumnGap: '10px',
			gridRowGap: '10px',
			gridColumnGapTablet: '',
			gridColumnGapMobile: '',
			gridRowGapTablet: '',
			gridRowGapMobile: '',
			gridOutline: true,
			gridJustifyItems: 'stretch',
			gridAlignItems: 'start',
			gridJustifyItemsTablet: '',
			gridJustifyItemsMobile: '',
			gridAlignItemsTablet: '',
			gridAlignItemsMobile: '',
			autoFlow: 'row',
			autoFlowTablet: '',
			autoFlowMobile: '',
			overflow: 'default',
			htmlTag: 'default',
			position: 'default',
			positionTop: '',
			positionRight: '',
			positionBottom: '',
			positionLeft: '',
			cssId: '',
			hideDesktop: false,
			hideTablet: false,
			hideMobile: false,
			sticky: 'none',
			stickyOffset: '',
			stickyEffectsOffset: '',
			stickyOnDesktop: true,
			stickyOnTablet: true,
			stickyOnMobile: true,
			entranceAnimation: '',
			animateWithAI: false,
			scrollingEffects: false,
			scrollEffectType: 'vertical',
			scrollDirection: 'up',
			scrollSpeed: 4,
			scrollViewportStart: 0,
			scrollViewportEnd: 100,
			scrollRelativeTo: 'default',
			scrollApplyDesktop: true,
			scrollApplyTablet: true,
			scrollApplyMobile: true,
			mouseEffects: false,
			mouseEffectType: 'track',
			mouseDirection: 'direct',
			mouseSpeed: 4,
			mouseRelativeTo: 'default',
			mouseApplyDesktop: true,
			mouseApplyTablet: true,
			mouseApplyMobile: true,
			transformRotate: '',
			transformOffsetX: '',
			transformOffsetY: '',
			transformScaleX: '',
			transformScaleY: '',
			transformSkewX: '',
			transformSkewY: '',
			bgType: 'none',
			bgState: 'normal',
			bgColor: '#ffffff',
			bgOpacity: 1,
			bgGradientType: 'linear',
			bgGradientAngle: 90,
			bgGradientStart: '#ffffff',
			bgGradientEnd: '#000000',
			bgGradientPosition: 50,
			bgImage: '',
			bgSize: 'cover',
			bgPosition: 'center center',
			bgRepeat: 'no-repeat',
			bgAttachment: 'scroll',
			bgTypeHover: 'none',
			bgColorHover: '#ffffff',
			bgOpacityHover: 1,
			bgGradientTypeHover: 'linear',
			bgGradientAngleHover: 90,
			bgGradientStartHover: '#ffffff',
			bgGradientEndHover: '#000000',
			bgGradientPositionHover: 50,
			bgImageHover: '',
			bgSizeHover: 'cover',
			bgPositionHover: 'center center',
			bgRepeatHover: 'no-repeat',
			bgAttachmentHover: 'scroll',
			bgTransitionDuration: 300,
			bgOverlayType: 'none',
			bgOverlayColor: '#000000',
			bgOverlayOpacity: 0.5,
			bgOverlayGradientType: 'linear',
			bgOverlayGradientAngle: 180,
			bgOverlayGradientStart: '#000000',
			bgOverlayGradientEnd: '#ffffff',
			bgOverlayGradientPosition: 100,
			bgOverlayImage: '',
			bgOverlaySize: 'cover',
			bgOverlayPosition: 'center center',
			bgOverlayRepeat: 'no-repeat',
			bgOverlayAttachment: 'scroll',
			bgOverlayBlendMode: 'normal',
			bgOverlayTypeHover: 'none',
			bgOverlayColorHover: '#000000',
			bgOverlayOpacityHover: 0.5,
			bgOverlayGradientTypeHover: 'linear',
			bgOverlayGradientAngleHover: 180,
			bgOverlayGradientStartHover: '#000000',
			bgOverlayGradientEndHover: '#ffffff',
			bgOverlayGradientPositionHover: 100,
			bgOverlayImageHover: '',
			bgOverlaySizeHover: 'cover',
			bgOverlayPositionHover: 'center center',
			bgOverlayRepeatHover: 'no-repeat',
			bgOverlayAttachmentHover: 'scroll',
			bgOverlayBlendModeHover: 'normal',
			borderHoverInitialized: false,
			shapeDividerTopEnabled: false,
			shapeDividerTopType: 'none',
			shapeDividerTopColor: '#ffffff',
			shapeDividerTopWidth: '100%',
			shapeDividerTopHeight: '60px',
			shapeDividerTopFlip: false,
			shapeDividerTopNegative: false,
			shapeDividerTopFront: false,
			shapeDividerBottomEnabled: false,
			shapeDividerBottomType: 'none',
			shapeDividerBottomColor: '#ffffff',
			shapeDividerBottomWidth: '100%',
			shapeDividerBottomHeight: '60px',
			shapeDividerBottomFlip: false,
			shapeDividerBottomNegative: false,
			shapeDividerBottomFront: false,
			shapeDividerSide: 'top',
			borderType: 'none',
			borderWidth: '1',
			borderColor: '#000000',
			borderTypeHover: '',
			borderWidthHover: '',
			borderColorHover: '',
			borderRadiusTL: '0',
			borderRadiusTR: '0',
			borderRadiusBR: '0',
			borderRadiusBL: '0',
			borderRadiusLinked: false,
			shadowHoverInitialized: false,
			shadowEnabled: false,
			shadowH: '0',
			shadowV: '0',
			shadowBlur: '0',
			shadowSpread: '0',
			shadowColor: '#000000',
			shadowOpacity: 0.3,
			shadowEnabledHover: '',
			shadowHHover: '',
			shadowVHover: '',
			shadowBlurHover: '',
			shadowSpreadHover: '',
			shadowColorHover: '',
			shadowOpacityHover: '',
			paddingTop: '0',
			paddingRight: '0',
			paddingBottom: '0',
			paddingLeft: '0',
			paddingTopTablet: '',
			paddingRightTablet: '',
			paddingBottomTablet: '',
			paddingLeftTablet: '',
			paddingTopMobile: '',
			paddingRightMobile: '',
			paddingBottomMobile: '',
			paddingLeftMobile: '',
			paddingUnit: '',
			paddingUnitTablet: '',
			paddingUnitMobile: '',
			paddingLinked: false,
			marginTop: '0',
			marginRight: '0',
			marginBottom: '0',
			marginLeft: '0',
			marginTopTablet: '',
			marginRightTablet: '',
			marginBottomTablet: '',
			marginLeftTablet: '',
			marginTopMobile: '',
			marginRightMobile: '',
			marginBottomMobile: '',
			marginLeftMobile: '',
			marginUnit: '',
			marginUnitTablet: '',
			marginUnitMobile: '',
			marginLinked: false,
			zIndex: '',
			cssClass: '',
			customCssCode: '',
			attributes: [],
		};
	}
	function gridDefaults(type = 'grid') {
		return {
			columns: type === 'row_grid' ? 1 : 3,
			columnsTablet: '',
			columnsMobile: '',
			gridTemplateColumns: '',
			columnGap: '20px',
			rowGap: '20px',
			columnGapTablet: '',
			rowGapTablet: '',
			columnGapMobile: '',
			rowGapMobile: '',
			gapLinked: false,
			gridAutoHeight: true,
			gridRows: 'auto',
			gridRowsTablet: '',
			gridRowsMobile: '',
			autoFlow: 'row',
			overflow: 'visible',
			position: 'default',
			positionTop: '',
			positionRight: '',
			positionBottom: '',
			positionLeft: '',
			cssId: '',
			hideDesktop: false,
			hideTablet: false,
			hideMobile: false,
			sticky: 'none',
			stickyOffset: '',
			stickyEffectsOffset: '',
			stickyOnDesktop: true,
			stickyOnTablet: true,
			stickyOnMobile: true,
			entranceAnimation: '',
			animateWithAI: false,
			scrollingEffects: false,
			scrollEffectType: 'vertical',
			scrollDirection: 'up',
			scrollSpeed: 4,
			scrollViewportStart: 0,
			scrollViewportEnd: 100,
			scrollRelativeTo: 'default',
			scrollApplyDesktop: true,
			scrollApplyTablet: true,
			scrollApplyMobile: true,
			mouseEffects: false,
			mouseEffectType: 'track',
			mouseDirection: 'direct',
			mouseSpeed: 4,
			mouseRelativeTo: 'default',
			mouseApplyDesktop: true,
			mouseApplyTablet: true,
			mouseApplyMobile: true,
			transformRotate: '',
			transformOffsetX: '',
			transformOffsetY: '',
			transformScaleX: '',
			transformScaleY: '',
			transformSkewX: '',
			transformSkewY: '',
			bgType: 'none',
			bgColor: '#ffffff',
			bgOpacity: 1,
			bgGradientType: 'linear',
			bgGradientAngle: 90,
			bgGradientStart: '#ffffff',
			bgGradientEnd: '#000000',
			bgGradientPosition: 50,
			bgImage: '',
			bgSize: 'cover',
			bgPosition: 'center center',
			bgRepeat: 'no-repeat',
			bgAttachment: 'scroll',
			borderType: 'none',
			borderWidth: '1',
			borderColor: '#000000',
			borderRadiusTL: '0',
			borderRadiusTR: '0',
			borderRadiusBR: '0',
			borderRadiusBL: '0',
			borderRadiusLinked: false,
			shadowEnabled: false,
			shadowH: '0',
			shadowV: '0',
			shadowBlur: '0',
			shadowSpread: '0',
			shadowColor: '#000000',
			shadowOpacity: 0.3,
			paddingTop: '0',
			paddingRight: '0',
			paddingBottom: '0',
			paddingLeft: '0',
			paddingTopTablet: '',
			paddingRightTablet: '',
			paddingBottomTablet: '',
			paddingLeftTablet: '',
			paddingTopMobile: '',
			paddingRightMobile: '',
			paddingBottomMobile: '',
			paddingLeftMobile: '',
			paddingUnit: '',
			paddingUnitTablet: '',
			paddingUnitMobile: '',
			paddingLinked: false,
			marginTop: '0',
			marginRight: '0',
			marginBottom: '0',
			marginLeft: '0',
			marginTopTablet: '',
			marginRightTablet: '',
			marginBottomTablet: '',
			marginLeftTablet: '',
			marginTopMobile: '',
			marginRightMobile: '',
			marginBottomMobile: '',
			marginLeftMobile: '',
			marginUnit: '',
			marginUnitTablet: '',
			marginUnitMobile: '',
			marginLinked: false,
			zIndex: '',
			cssClass: '',
			customCssCode: '',
			attributes: [],
		};
	}

	const BASE_NODE_LABELS = Object.freeze({
		container: 'Container',
		container_fluid: 'Container Fluid',
		row_grid: 'Row Grid',
		grid: 'Grid',
		heading: 'Heading',
		text_editor: 'Text Editor',
		image: 'Image',
		video: 'Video',
		button: 'Button',
		icon: 'Icon',
		divider: 'Divider',
		spacer: 'Spacer',
		tabs: 'Tabs',
		accordion: 'Accordion',
	});

	const NODE_LABEL_ICONS = Object.freeze({
		container: 'fas fa-cube',
		container_fluid: 'fas fa-cube',
		row_grid: 'fas fa-th-large',
		grid: 'fas fa-th-large',
		heading: 'fas fa-heading',
		text_editor: 'fas fa-edit',
		image: 'far fa-image',
		video: 'fas fa-video',
		button: 'fas fa-link',
		icon: 'far fa-star',
		divider: 'fas fa-minus',
		spacer: 'fas fa-arrows-alt-v',
		tabs: 'far fa-folder',
		accordion: 'fas fa-bars',
	});

	function baseNodeLabel(type, fallback = 'Widget') {
		return BASE_NODE_LABELS[type] || fallback;
	}

	function displayNodeLabel(node) {
		if (!node) return '';
		const base = baseNodeLabel(node.type, String(node.label || 'Widget').trim() || 'Widget');
		const suffix = String(node.labelSuffix || '').trim();
		return suffix ? (base + ' ' + suffix) : base;
	}

	function nodeLabelIcon(type) {
		return NODE_LABEL_ICONS[type] || 'fas fa-cube';
	}

	function makeNode(type) {
		const id = uid('n');
		switch (type) {
			case 'container': {
				const s = containerDefaults('container');
				const cols = Number(s.gridColumns || 3);
				return { id, type, label:'Container', labelSuffix:'', settings:s,
					columns: Array.from({length: cols}, () => ({id:uid('c'), children:[]})),
					children:[] };
			}
			case 'container_fluid': {
				const s = containerDefaults('container_fluid');
				const cols = Number(s.gridColumns || 3);
				return { id, type, label:'Container Fluid', labelSuffix:'', settings:s,
					columns: Array.from({length: cols}, () => ({id:uid('c'), children:[]})),
					children:[] };
			}
			case 'row_grid': {
				const s = gridDefaults(type);
				const cols = clamp(Number(s.columns || 1), 1, 12);
				return { id, type, label:'Row Grid', labelSuffix:'', settings:s,
					columns: Array.from({length: cols}, () => ({id:uid('c'), children:[]})) };
			}
			case 'grid': {
				const s = gridDefaults(type);
				const cols = clamp(Number(s.columns || 1), 1, 12);
				return { id, type, label:'Grid', labelSuffix:'', settings:s,
					columns: Array.from({length: cols}, () => ({id:uid('c'), children:[]})) };
			}
			case 'heading':        return { id, type, label:'Heading', labelSuffix:'',        settings:{ text:'Add your heading text', tag:'h2', align:'left', color:'#101828', cssClass:'' } };
			case 'text_editor':    return { id, type, label:'Text Editor', labelSuffix:'',    settings:{ html:'<p>Edit this text.</p>', cssClass:'' } };
			case 'image':          return { id, type, label:'Image', labelSuffix:'',          settings:{ src:'https://placehold.co/640x360', alt:'Image', width:'100%', height:'auto', cssClass:'' } };
			case 'image_box':
				return { id, type, label:'Image Box', labelSuffix:'', settings: imageBoxWidgetDefaults() };
			case 'video':          return { id, type, label:'Video', labelSuffix:'',          settings:videoDefaults() };
			case 'button':         return { id, type, label:'Button', labelSuffix:'',         settings:{ text:'Click here', url:'#', newTab:false, align:'left', className:'btn btn-primary' } };
			case 'icon':           return { id, type, label:'Icon', labelSuffix:'',           settings:iconWidgetDefaults() };
			case 'divider':        return { id, type, label:'Divider', labelSuffix:'',        settings:{ style:'solid', width:'100%', thickness:2, color:'#d0d7e6', cssClass:'' } };
			case 'spacer':         return { id, type, label:'Spacer', labelSuffix:'',         settings:{ height:'32px', cssClass:'' } };
			case 'tabs': {
				const settings = tabsWidgetDefaults();
				const tabItems = tabsWidgetDefaultItems();
				settings.activeTabId = tabItems[0].id;
				return { id, type, label:'Tabs', labelSuffix:'', settings, tabItems };
			}
			case 'accordion':
				return {
					id,
					type,
					label: 'Accordion',
					labelSuffix: '',
					settings: accordionWidgetDefaults(),
					accordionItems: accordionWidgetDefaultItems(),
				};
			default: return null;
		}
	}

	// cloneItem untuk :clone prop pada sidebar draggable (persis pola builder lama)
	function cloneItem(origin) {
		const item = jclone(origin);
		item.id = uid('n');
		if (isCont(item.type)) {
			item.children = [];
			const cols = clamp(Number(item.settings?.gridColumns || 1), 1, 12);
			item.columns = Array.from({length: cols}, () => ({id: uid('c'), children: []}));
		}
		if (isGrid(item.type)) {
			const cols = Number(item.settings?.columns || 1);
			item.columns = Array.from({length: cols}, () => ({id: uid('c'), children: []}));
		}
		return item;
	}

	// ── CkEditorField ─────────────────────────────────────────────────────────
	const CkEditorField = {
		name: 'CkEditorField',
		props: { modelValue: { type: String, default: '' } },
		emits: ['update:modelValue'],
		setup(props, ctx) {
			const elId = uid('cke');
			let inst = null;
			onMounted(() => {
				const el = document.getElementById(elId);
				if (!el || !window.ClassicEditor) return;
				window.ClassicEditor.create(el).then(ed => {
					inst = ed;
					ed.setData(props.modelValue || '');
					ed.model.document.on('change:data', () => ctx.emit('update:modelValue', ed.getData()));
				}).catch(console.error);
			});
			watch(() => props.modelValue, v => { if (inst && v !== inst.getData()) inst.setData(v || ''); });
			onBeforeUnmount(() => { if (inst) { inst.destroy(); inst = null; } });
			return { elId };
		},
		template: '<textarea :id="elId"></textarea>',
	};

	// ── BuilderNode ───────────────────────────────────────────────────────────
	const BuilderNode = {
		name: 'BuilderNode',
		components: { draggable },
		props: {
			node:        { type: Object,   required: true },
			selectedId:  { type: String,   default: '' },
			selectedColumnNodeId: { type: String, default: '' },
			selectedColumnId: { type: String, default: '' },
			hoveredId:   { type: String,   default: '' },
			responsiveDevice: { type: String, default: 'desktop' },
			// Handlers dari app
			onAddContainer: { type: Function, required: true },
			onAddCol:       { type: Function, required: true },
			onSelect:       { type: Function, required: true },
			onSelectColumn: { type: Function, required: true },
			onSetHover:     { type: Function, required: true },
			onClearHover:   { type: Function, required: true },
			onRemove:       { type: Function, required: true },
			onDuplicate:    { type: Function, required: true },
			onDragStart:    { type: Function, required: true },
			onDragEnd:      { type: Function, required: true },
			onStartColumnResize: { type: Function, required: true },
			onOpenModal:    { type: Function, required: true },
			onShowToolbox:  { type: Function, required: true },
			pendingInsertTarget: { type: Object, default: null },
			onRerouteTabsDrop: { type: Function, default: null },
			onAccordionRuntimeForNode: { type: Function, default: null },
			onToggleAccordionItem: { type: Function, default: null },
			onRerouteAccordionDrop: { type: Function, default: null },
			onTrackDropzonePointer: { type: Function, default: null },
		},
		emits: [],
		data() {
			return {
				outOfFlowShellHeight: 0,
				outOfFlowShellTarget: null,
				outOfFlowShellObserver: null,
				outOfFlowShellRaf: 0,
				columnLabelOffsetRaf: 0,
				columnLabelResizeObserver: null,
				columnLabelWindowResizeHandler: null,
			};
		},
		mounted() {
			this.syncOutOfFlowShellPlaceholder();
			this.syncColumnLabelOffsetBinding();
		},
		updated() {
			this.$nextTick(() => {
				this.syncOutOfFlowShellPlaceholder();
				this.syncColumnLabelOffsetBinding();
			});
		},
		beforeUnmount() {
			this.teardownOutOfFlowShellPlaceholder();
			this.teardownColumnLabelOffsetBinding();
		},
		computed: {
			isCont()  { return isCont(this.node.type); },
			isGrid()  { return isGrid(this.node.type); },
			isTabsNode() { return isTabs(this.node.type); },
			isAccordionNode() { return isAccordion(this.node.type); },
			isWidgetNode() { return !isCont(this.node.type) && !isGrid(this.node.type); },
			label()   {
				return displayNodeLabel(this.node);
			},
			labelIcon() {
				return nodeLabelIcon(this.node.type);
			},
			isVisualActive() {
				const hovered = String(this.hoveredId || '').trim();
				if (hovered && hovered !== this.node.id) return false;
				return this.selectedId === this.node.id;
			},
			isToolbarVisible() {
				const focusId = this.hoveredId || this.selectedId || '';
				return focusId === this.node.id;
			},
			isFlexColumnEditor() {
				const s = this.node.settings || {};
				if (!this.isCont || (s.displayType || 'flex') !== 'flex') return false;
				const direction = this.nodeResponsiveValue('direction', s.direction || 'row') || 'row';
				return direction === 'row' || direction === 'row-reverse';
			},
			isFlexRowResizable() {
				const s = this.node.settings || {};
				if (!this.isFlexColumnEditor) return false;
				const direction = this.nodeResponsiveValue('direction', s.direction || 'row') || 'row';
				const flexWrap = this.nodeResponsiveValue('flexWrap', s.flexWrap || 'nowrap') || 'nowrap';
				return direction === 'row' && flexWrap === 'nowrap' && Array.isArray(this.node.columns) && this.node.columns.length > 1;
			},
			isOutOfFlowLayoutNode() {
				const settings = this.node && this.node.settings ? this.node.settings : {};
				const stickyMode = String(settings.sticky || 'none').trim().toLowerCase();
				const chosenPosition = String(settings.position || 'default').trim().toLowerCase();
				return (this.isCont || this.isGrid)
					&& stickyMode === 'none'
					&& (chosenPosition === 'absolute' || chosenPosition === 'fixed');
			},
			nodeShellId() {
				const raw = String(this.node?.settings?.cssId || '').trim();
				if (this.isAccordionNode && /^[A-Za-z][A-Za-z0-9_-]*$/.test(raw)) return raw;
				if (!this.isCont) return null;
				return raw || null;
			},
			nodeAdvancedClasses() {
				if (!this.isAccordionNode) return [];
				const s = this.node.settings || {};
				const classes = ['pb-has-advanced'];
				String(s.cssClass || '').trim().split(/\s+/).filter(Boolean).forEach((token) => {
					const safe = token.replace(/[^A-Za-z0-9_-]/g, '');
					if (safe) classes.push(safe);
				});
				if (s.entranceAnimation) classes.push('pb-advanced-entrance', 'pb-anim-' + String(s.entranceAnimation).replace(/[^A-Za-z0-9_-]/g, ''));
				if (s.scrollingEffects) classes.push('pb-motion-scroll');
				if (s.mouseEffects) classes.push('pb-motion-mouse');
				return classes;
			},
			nodeShellStyle() {
				const s = this.node.settings || {};
				const device = this.responsiveDevice || 'desktop';
				if (this.isAccordionNode) return widgetAdvancedPreviewStyle(s, device);
				if (!this.isCont) return {};
				const currentHideValue = device === 'tablet'
					? s.hideTablet
					: (device === 'mobile' ? s.hideMobile : s.hideDesktop);
				const isCurrentDeviceHidden = currentHideValue === true
					|| currentHideValue === 'true'
					|| currentHideValue === 1
					|| currentHideValue === '1';
				const style = {
					marginTop: cssSpace(this.nodeResponsiveValue('marginTop', s.marginTop), '0'),
					marginRight: cssSpace(this.nodeResponsiveValue('marginRight', s.marginRight), '0'),
					marginBottom: cssSpace(this.nodeResponsiveValue('marginBottom', s.marginBottom), '0'),
					marginLeft: cssSpace(this.nodeResponsiveValue('marginLeft', s.marginLeft), '0'),
				};

				if (isCurrentDeviceHidden) {
					style.display = 'none';
					return style;
				}

				const alignSelf = this.nodeResponsiveValue('alignSelf', s.alignSelf || 'auto');
				if (alignSelf && alignSelf !== 'auto') {
					style.alignSelf = alignSelf;
				}

				const orderValue = this.nodeResponsiveValue('order', s.order ?? '');
				if (orderValue !== '' && orderValue != null) {
					const order = Number(orderValue);
					if (Number.isFinite(order)) {
						style.order = order;
					}
				}

				const sizeMode = this.nodeResponsiveValue('sizeMode', s.sizeMode || 'default');
				if (sizeMode === 'grow') {
					style.flex = '1 1 0';
				} else if (sizeMode === 'shrink') {
					style.flex = '0 1 auto';
				} else if (sizeMode === 'custom') {
					const customBasis = this.nodeResponsiveValue('containerWidth', s.containerWidth)
						|| this.nodeResponsiveValue('maxWidth', s.maxWidth);
					style.flex = '0 0 ' + cssSize(customBasis, 'auto');
				}

				return style;
			},
			contentShellStyle() {
				if (!this.isOutOfFlowLayoutNode || !(this.outOfFlowShellHeight > 0)) return {};
				return {
					minHeight: this.outOfFlowShellHeight + 'px',
				};
			},
			gridCols() { return this.node.settings?.gridTemplateColumns || 'repeat(' + clamp(Number(this.node.settings?.columns || 2), 1, 12) + ', minmax(0, 1fr))'; },
			gridStyle() {
				const s = this.node.settings || {};
				const columnGap = this.nodeResponsiveValue('columnGap', s.columnGap || s.gap || '');
				const rowGap = this.nodeResponsiveValue('rowGap', s.rowGap || s.gap || '');
				const autoFlow = this.nodeResponsiveValue('autoFlow', s.autoFlow || 'row') || 'row';
				const rowsValue = this.nodeResponsiveValue('gridRows', s.gridRows);
				const style = {
					gridTemplateColumns: this.gridCols,
					columnGap: cssSize(columnGap, '20px'),
					rowGap: cssSize(rowGap, '20px'),
					gridAutoFlow: autoFlow,
				};
				const rows = gridRowsTemplate(rowsValue);
				if (rows) style.gridTemplateRows = rows;
				if (s.gridAutoHeight === false || s.gridAutoHeight === 'false' || s.gridAutoHeight === 0) {
					style.gridAutoRows = '1fr';
				}
				return style;
			},
			// group untuk container children — tolak dari pb-col dan tolak container node
			contGroup() {
				return {
					name: 'pb-container',
					put: (to, from, el) => {
						const fromGroup = from.options && from.options.group && from.options.group.name;
						// Tolak drag dari pb-col (widget pindah antar kolom tidak boleh masuk container)
						if (fromGroup === 'pb-col') return false;
						// Tolak jika yang di-drag adalah container
						if (el.dataset && el.dataset.nodeType && isCont(el.dataset.nodeType)) return false;
						return true;
					},
				};
			},
			// Style kolom untuk Container (flex/grid/block)
			contColumnsStyle() {
				const s = this.node.settings || {};
				const dt = s.displayType || 'flex';
				if (dt === 'grid') {
					const responsiveCols = clamp(Number(this.nodeResponsiveValue('gridColumns', s.gridColumns || 3) || (s.gridColumns || 3)), 1, 12);
					const useTemplate = (this.responsiveDevice || 'desktop') === 'desktop' && String(s.gridTemplateColumns || '').trim() !== '';
					const cols = useTemplate ? s.gridTemplateColumns : ('repeat(' + responsiveCols + ', minmax(0, 1fr))');
					const gridColumnGap = this.nodeResponsiveValue('gridColumnGap', s.gridColumnGap);
					const gridRowGap = this.nodeResponsiveValue('gridRowGap', s.gridRowGap);
					const autoFlow = this.nodeResponsiveValue('autoFlow', s.autoFlow || 'row') || 'row';
					const justifyItems = this.nodeResponsiveValue('gridJustifyItems', s.gridJustifyItems || 'stretch') || 'stretch';
					const alignItems = this.nodeResponsiveValue('gridAlignItems', s.gridAlignItems || 'start') || 'start';
					const rowsValue = this.nodeResponsiveValue('gridRows', s.gridRows);
					const style = {
						display: 'grid',
						gridTemplateColumns: cols,
						columnGap: cssSize(gridColumnGap, '20px'),
						rowGap: cssSize(gridRowGap, '20px'),
						gridAutoFlow: autoFlow,
						justifyItems,
						alignItems,
						width: '100%',
					};
					const rows = containerGridRowsTemplate(rowsValue); if (rows) style.gridTemplateRows = rows;
					return style;
				}
			if (dt === 'flex') {
				const direction = this.nodeResponsiveValue('direction', s.direction || 'row') || 'row';
				const flexWrap = this.nodeResponsiveValue('flexWrap', s.flexWrap || 'nowrap') || 'nowrap';
				const justifyContent = this.nodeResponsiveValue('justifyContent', s.justifyContent || 'flex-start') || 'flex-start';
				const requestedAlignItems = this.nodeResponsiveValue('alignItems', s.alignItems || 'flex-start') || 'flex-start';
				const isRowDir = direction === 'row' || direction === 'row-reverse';
				const alignItems = isRowDir ? 'stretch' : requestedAlignItems;
				const alignContent = this.nodeResponsiveValue('alignContent', s.alignContent || 'stretch') || 'stretch';
				const rowGap = this.nodeResponsiveValue('flexRowGap', s.flexRowGap || s.gap || '0');
				const columnGap = this.nodeResponsiveValue('flexColumnGap', s.flexColumnGap || s.gap || '0');
				const style = { display:'flex', flexDirection:direction, flexWrap,
					justifyContent, alignItems, alignContent,
					gap:cssSize(s.gap,'0'), rowGap:cssSize(rowGap,'0'), columnGap:cssSize(columnGap,'0'), width:'100%' };
				style['--pb-flex-column-gap'] = cssSize(columnGap, '0');
				style['--pb-col-resizer-size'] = '28px';
				style.minHeight = 'inherit';
				style.height = '100%';
					return style;
				}
				return { display:'block', width:'100%' };
			},
			// group untuk grid column
			colGroup() {
				return {
					name: 'pb-col',
					put: (to, from, el) => {
						// el = elemen yang di-drag, cek nodeType-nya
						const nodeType = String((el && el.dataset && el.dataset.nodeType) || '').trim();
						const isExistingCanvasNode = !!nodeType;
						// Tolak jika yang di-drag adalah container
						if (nodeType && isCont(nodeType)) return false;
						const sourceParentNodeType = this.getParentNodeTypeFromSortable(from);
						const targetParentNodeType = this.getParentNodeTypeFromSortable(to);
						if (isExistingCanvasNode && sourceParentNodeType && targetParentNodeType) {
							const sameOwnerFamily =
								((isCont(sourceParentNodeType) || isGrid(sourceParentNodeType) || isTabs(sourceParentNodeType))
									&& (isCont(targetParentNodeType) || isGrid(targetParentNodeType) || isTabs(targetParentNodeType)));
							if (!sameOwnerFamily) return false;
						}
						const targetIndex = this.getTargetColumnIndexFromDropzone(to);
						if (!isExistingCanvasNode && targetIndex >= 0 && this.isSequentialColumnLocked(targetIndex)) return false;
						// Terima dari sesama pb-col (reorder + pindah antar kolom)
						// Terima dari pb-container (widget yang ada di container)
						return true;
					},
				};
			},
		},
		methods: {
			queueOutOfFlowShellMeasure() {
				if (this.outOfFlowShellRaf) cancelAnimationFrame(this.outOfFlowShellRaf);
				this.outOfFlowShellRaf = requestAnimationFrame(() => {
					this.outOfFlowShellRaf = 0;
					this.measureOutOfFlowShellHeight();
				});
			},
			teardownOutOfFlowShellPlaceholder() {
				if (this.outOfFlowShellRaf) {
					cancelAnimationFrame(this.outOfFlowShellRaf);
					this.outOfFlowShellRaf = 0;
				}
				if (this.outOfFlowShellObserver) {
					this.outOfFlowShellObserver.disconnect();
					this.outOfFlowShellObserver = null;
				}
				this.outOfFlowShellTarget = null;
				this.outOfFlowShellHeight = 0;
			},
			resolveOutOfFlowShellTarget() {
				if (!this.$el || typeof this.$el.querySelector !== 'function') return null;
				return this.$el.querySelector('.pb-node-content > .el-layout-container, .pb-node-content > .el-layout-container-fluid, .pb-node-content > .el-layout-grid, .pb-node-content > .el-layout-row-grid');
			},
			measureOutOfFlowShellHeight() {
				if (!this.isOutOfFlowLayoutNode) {
					this.outOfFlowShellHeight = 0;
					return;
				}
				const target = this.resolveOutOfFlowShellTarget();
				if (!target) return;
				const rect = typeof target.getBoundingClientRect === 'function' ? target.getBoundingClientRect() : null;
				const height = Math.max(
					rect ? rect.height : 0,
					target.offsetHeight || 0,
					target.scrollHeight || 0
				);
				this.outOfFlowShellHeight = Math.max(0, Math.ceil(height));
			},
			syncOutOfFlowShellPlaceholder() {
				if (!this.isOutOfFlowLayoutNode) {
					if (this.outOfFlowShellObserver) {
						this.outOfFlowShellObserver.disconnect();
						this.outOfFlowShellObserver = null;
					}
					this.outOfFlowShellTarget = null;
					this.outOfFlowShellHeight = 0;
					return;
				}
				const target = this.resolveOutOfFlowShellTarget();
				if (!target) return;
				if (target !== this.outOfFlowShellTarget) {
					if (this.outOfFlowShellObserver) {
						this.outOfFlowShellObserver.disconnect();
						this.outOfFlowShellObserver = null;
					}
					this.outOfFlowShellTarget = target;
					if (typeof ResizeObserver === 'function') {
						this.outOfFlowShellObserver = new ResizeObserver(() => this.queueOutOfFlowShellMeasure());
						this.outOfFlowShellObserver.observe(target);
					}
				}
				this.queueOutOfFlowShellMeasure();
			},
			queueColumnLabelOffsetSync() {
				if (this.columnLabelOffsetRaf) cancelAnimationFrame(this.columnLabelOffsetRaf);
				this.columnLabelOffsetRaf = requestAnimationFrame(() => {
					this.columnLabelOffsetRaf = 0;
					this.syncColumnLabelOffsets();
				});
			},
			syncColumnLabelOffsetBinding() {
				if (!this.isFlexColumnEditor) {
					this.teardownColumnLabelOffsetBinding();
					return;
				}
				if (!this.columnLabelWindowResizeHandler) {
					this.columnLabelWindowResizeHandler = () => this.queueColumnLabelOffsetSync();
					window.addEventListener('resize', this.columnLabelWindowResizeHandler);
				}
				if (!this.columnLabelResizeObserver && typeof ResizeObserver === 'function' && this.$el) {
					this.columnLabelResizeObserver = new ResizeObserver(() => this.queueColumnLabelOffsetSync());
					this.columnLabelResizeObserver.observe(this.$el);
				}
				this.queueColumnLabelOffsetSync();
			},
			teardownColumnLabelOffsetBinding() {
				if (this.columnLabelOffsetRaf) {
					cancelAnimationFrame(this.columnLabelOffsetRaf);
					this.columnLabelOffsetRaf = 0;
				}
				if (this.columnLabelResizeObserver) {
					this.columnLabelResizeObserver.disconnect();
					this.columnLabelResizeObserver = null;
				}
				if (this.columnLabelWindowResizeHandler) {
					window.removeEventListener('resize', this.columnLabelWindowResizeHandler);
					this.columnLabelWindowResizeHandler = null;
				}
				this.resetColumnLabelOffsets();
			},
			resolveColumnLabelElements() {
				if (!this.$el || typeof this.$el.querySelector !== 'function') {
					return { containerLabel: null, columnLabels: [] };
				}
				const containerLabel = this.$el.querySelector(':scope > .pb-node-toolbar > .pb-node-label');
				const content = this.$el.querySelector(':scope > .pb-node-content');
				const columnsRoot = content
					? content.querySelector(':scope > .el-layout-container > .el-cont-columns, :scope > .el-layout-container-fluid > .el-cont-columns')
					: null;
				const columnLabels = columnsRoot
					? Array.from(columnsRoot.querySelectorAll(':scope > .pb-grid-col > .pb-grid-col-label'))
					: [];
				return { containerLabel, columnLabels };
			},
			resetColumnLabelOffsets() {
				const { columnLabels } = this.resolveColumnLabelElements();
				columnLabels.forEach((label) => {
					label.style.removeProperty('--pb-col-label-offset-x');
					label.classList.remove('is-auto-shifted');
				});
			},
			syncColumnLabelOffsets() {
				const { containerLabel, columnLabels } = this.resolveColumnLabelElements();
				columnLabels.forEach((label) => {
					label.style.removeProperty('--pb-col-label-offset-x');
					label.classList.remove('is-auto-shifted');
				});
				if (!containerLabel || !columnLabels.length || !this.isFlexColumnEditor) return;
				const containerRect = containerLabel.getBoundingClientRect();
				const guard = 10;
				columnLabels.forEach((label) => {
					const rect = label.getBoundingClientRect();
					const overlaps =
						rect.left < containerRect.right + guard &&
						rect.right > containerRect.left - guard &&
						rect.top < containerRect.bottom + guard &&
						rect.bottom > containerRect.top - guard;
					if (!overlaps) return;
					const offset = Math.min(260, Math.max(0, Math.ceil(containerRect.right - rect.left + guard)));
					label.style.setProperty('--pb-col-label-offset-x', offset + 'px');
					label.classList.add('is-auto-shifted');
				});
			},
			tabsItemsList() {
				return Array.isArray(this.node.tabItems) ? this.node.tabItems : [];
			},
			activeTabsItem() {
				const items = this.tabsItemsList();
				if (!items.length) return null;
				const activeId = String(this.node?.settings?.activeTabId || '').trim();
				return items.find((item) => item && String(item.id || '') === activeId) || items[0];
			},
			activeTabsChildren() {
				const item = this.activeTabsItem();
				return item && Array.isArray(item.children) ? item.children : [];
			},
			accordionItemsList() {
				return Array.isArray(this.node.accordionItems) ? this.node.accordionItems : [];
			},
			accordionExpandedItemIds() {
				if (!this.onAccordionRuntimeForNode) return [];
				const runtime = this.onAccordionRuntimeForNode(this.node);
				return Array.isArray(runtime?.expandedItemIds) ? runtime.expandedItemIds : [];
			},
			accordionItemChildren(itemId) {
				const item = this.accordionItemsList().find((entry) => String(entry?.id || '') === String(itemId || ''));
				return item && Array.isArray(item.children) ? item.children : [];
			},
			onNodeContentClick(node, event) {
				if (isInteractiveCanvasTarget(event && event.target)) return;
				this.onSelect(node);
			},
			passdown() {
				return {
					selectedId:     this.selectedId,
					selectedColumnNodeId: this.selectedColumnNodeId,
					selectedColumnId: this.selectedColumnId,
					hoveredId:      this.hoveredId,
					responsiveDevice: this.responsiveDevice,
					onAddContainer: this.onAddContainer,
					onAddCol:       this.onAddCol,
					onSelect:       this.onSelect,
					onSelectColumn: this.onSelectColumn,
					onSetHover:     this.onSetHover,
					onClearHover:   this.onClearHover,
					onRemove:       this.onRemove,
					onDuplicate:    this.onDuplicate,
					onDragStart:    this.onDragStart,
					onDragEnd:      this.onDragEnd,
					onStartColumnResize: this.onStartColumnResize,
					onOpenModal:    this.onOpenModal,
					onShowToolbox:  this.onShowToolbox,
					pendingInsertTarget: this.pendingInsertTarget,
					onRerouteTabsDrop: this.onRerouteTabsDrop,
					onAccordionRuntimeForNode: this.onAccordionRuntimeForNode,
					onToggleAccordionItem: this.onToggleAccordionItem,
					onRerouteAccordionDrop: this.onRerouteAccordionDrop,
					onTrackDropzonePointer: this.onTrackDropzonePointer,
				};
			},
			flexPercentBasis(rawBasis) {
				const match = String(rawBasis == null ? '' : rawBasis).trim().match(/^(\d+(?:\.\d+)?)%$/);
				if (!match) return null;
				const percent = Number(match[1]);
				return Number.isFinite(percent) && percent > 0 ? percent : null;
			},
			flexPercentBasisToken(percent) {
				const safePercent = clamp(Number(percent) || 0, 0, 100);
				const s = this.node.settings || {};
				const columnGap = this.nodeResponsiveValue('flexColumnGap', s.flexColumnGap || s.gap || '0');
				const gapToken = cssSize(columnGap, '0');
				const gapMatch = String(gapToken || '').trim().match(/^(\d+(?:\.\d+)?)px$/i);
				const colCount = Array.isArray(this.node.columns) ? this.node.columns.length : 1;
				if (gapMatch && colCount > 1 && safePercent > 0) {
					const totalGapPx = Number(gapMatch[1]) * (colCount - 1);
					const gapSharePx = Math.round(totalGapPx * (safePercent / 100) * 1000) / 1000;
					if (gapSharePx > 0) return 'calc(' + safePercent + '% - ' + gapSharePx + 'px)';
				}
				return safePercent + '%';
			},
			contColStyle(col) {
				const s = this.node.settings || {};
				const dt = s.displayType || 'flex';
				const rawBasis = String(col && col.flexBasis != null ? col.flexBasis : '').trim();
				const hasBasis = rawBasis !== '';

				if (dt === 'flex') {
					const dir = this.nodeResponsiveValue('direction', s.direction || 'row') || 'row';
					const isColumnDir = dir === 'column' || dir === 'column-reverse';
					const wrapMode = this.nodeResponsiveValue('flexWrap', s.flexWrap || 'nowrap') || 'nowrap';
					const isWrapMode = wrapMode === 'wrap' || wrapMode === 'wrap-reverse';
					const stableRowMinWidth = isWrapMode
						? '220px'
						: ((this.responsiveDevice || 'desktop') === 'mobile'
							? '56px'
							: ((this.responsiveDevice || 'desktop') === 'tablet' ? '72px' : '96px'));

					if (hasBasis) {
						if (isColumnDir) {
							return {
								flex: '0 0 ' + rawBasis,
								height: rawBasis,
								minHeight: rawBasis,
								width: '100%',
								minWidth: '0',
								boxSizing: 'border-box',
							};
						}
						const percentBasis = this.flexPercentBasis(rawBasis);
						if (percentBasis != null) {
							const basisToken = this.flexPercentBasisToken(percentBasis);
							return {
								flex: '0 0 ' + basisToken,
								flexBasis: basisToken,
								width: basisToken,
								minWidth: stableRowMinWidth,
								maxWidth: '100%',
								height: 'auto',
								alignSelf: 'stretch',
								display: 'flex',
								flexDirection: 'column',
								boxSizing: 'border-box',
							};
						}
						const rowStyle = {
							flex: '0 0 ' + rawBasis,
							width: rawBasis,
							minWidth: stableRowMinWidth,
							maxWidth: rawBasis,
							height: 'auto',
							alignSelf: 'stretch',
							display: 'flex',
							flexDirection: 'column',
							boxSizing: 'border-box',
						};
						return rowStyle;
					}

					if (isColumnDir) {
						return {
							flex: '0 0 auto',
							width: '100%',
							minWidth: '0',
							minHeight: '88px',
							boxSizing: 'border-box',
						};
					}

					const rowStyle = {
						flex: '1 1 0',
						width: 'auto',
						minWidth: stableRowMinWidth,
						height: 'auto',
						alignSelf: 'stretch',
						display: 'flex',
						flexDirection: 'column',
						boxSizing: 'border-box',
					};
					return rowStyle;
				}

				return {};
			},
			contDropzoneStyle(col) {
				const s = this.node.settings || {};
				const childCount = Array.isArray(col?.children) ? col.children.length : 0;
				const dt = s.displayType || 'flex';
				const base = { flex: '1 1 auto' };

				if (dt === 'flex') {
					const dir = this.nodeResponsiveValue('direction', s.direction || 'row') || 'row';
					const isRowDir = dir === 'row' || dir === 'row-reverse';
					if (!isRowDir) return {};
					const alignItems = String(this.nodeResponsiveValue('alignItems', s.alignItems || 'flex-start') || 'flex-start').toLowerCase();
					const mapMain = {
						'flex-start': 'flex-start',
						center: 'center',
						'flex-end': 'flex-end',
						stretch: 'flex-start',
					};
					const style = { ...base, minHeight: childCount === 0 ? '68px' : '100%' };
					if (childCount > 0) style.justifyContent = mapMain[alignItems] || 'flex-start';
					return style;
				}

				if (dt !== 'grid') return {};
				// Keep empty-column placeholders (Column label + Drop here) fixed in place.
				if (childCount === 0) return {};

				const mapMain = {
					start: 'flex-start',
					center: 'center',
					end: 'flex-end',
					stretch: 'flex-start',
				};
				const alignItems = String(this.nodeResponsiveValue('gridAlignItems', s.gridAlignItems || 'start') || 'start').toLowerCase();
				return { ...base, justifyContent: mapMain[alignItems] || 'flex-start' };
			},
			columnHasChildren(col) {
				return Array.isArray(col && col.children) && col.children.length > 0;
			},
			columnLabel(ci) {
				return 'Column ' + (Number(ci) + 1);
			},
			isColumnSelected(col) {
				return this.selectedColumnNodeId === this.node.id && this.selectedColumnId === String(col && col.id || '');
			},
			showColumnResizeHandle(ci) {
				return this.isFlexRowResizable && Number(ci) < ((this.node.columns || []).length - 1);
			},
			selectColumn(col) {
				if (!col) return;
				this.onSelectColumn(this.node, col);
			},
			startColumnResize(event, col, ci) {
				if (!col) return;
				this.onStartColumnResize(event, this.node, col, ci);
			},
			onTabDropzoneMove(evt) {
				const originalEvent = evt && evt.originalEvent ? evt.originalEvent : null;
				const parentEl = evt && evt.to ? evt.to : null;
				const trackedDropzone = this.onTrackDropzonePointer ? this.onTrackDropzonePointer(originalEvent) : null;
				if (trackedDropzone && parentEl && trackedDropzone !== parentEl && parentEl.contains(trackedDropzone)) return false;
				if (findNestedCanvasDropTargetFromEvent(originalEvent, parentEl)) return false;
				const target = originalEvent ? originalEvent.target : null;
				return !isNestedCanvasDropTarget(target);
			},
			onAddActiveTabChild(evt) {
				const children = this.activeTabsChildren();
				if (this.onRerouteTabsDrop && this.onRerouteTabsDrop(evt, children)) return;
				this.onAddCol(evt, { children }, -1, null);
			},
			onAccordionDropzoneMove(evt) {
				return this.onTabDropzoneMove(evt);
			},
			onAddAccordionItemChild(evt, item) {
				const children = this.accordionItemChildren(item?.id);
				if (this.onRerouteAccordionDrop && this.onRerouteAccordionDrop(evt, children)) return;
				this.onAddCol(evt, { children }, -1, null);
			},
			toggleAccordionItemFromPreview(itemId) {
				if (this.onToggleAccordionItem) this.onToggleAccordionItem(this.node, itemId);
			},
			isSequentialColumnLocked(colIndex) {
				const cols = Array.isArray(this.node.columns) ? this.node.columns : [];
				const idx = Number(colIndex);
				if (!Number.isFinite(idx) || idx < 0 || idx >= cols.length) return false;
				const target = cols[idx];
				// Kolom yang sudah terisi tetap bisa dipakai; lock hanya untuk kolom kosong.
				if (this.columnHasChildren(target)) return false;
				for (let i = 0; i < idx; i++) {
					if (!this.columnHasChildren(cols[i])) return true;
				}
				return false;
			},
			getTargetColumnIndexFromDropzone(sortableRef) {
				const el = sortableRef && sortableRef.el ? sortableRef.el : null;
				if (!el) return -1;
				const host = el.closest('[data-col-index]');
				if (!host) return -1;
				const raw = Number(host.dataset.colIndex);
				return Number.isFinite(raw) ? raw : -1;
			},
			getParentNodeTypeFromSortable(sortableRef) {
				const el = sortableRef && sortableRef.el ? sortableRef.el : null;
				if (!el || typeof el.closest !== 'function') return '';
				const parentNode = el.closest('[data-parent-node-type]');
				if (parentNode) return String(parentNode.getAttribute('data-parent-node-type') || '').trim();
				const hostNode = el.closest('[data-node-id]');
				return hostNode ? String(hostNode.getAttribute('data-node-type') || '').trim() : '';
			},
			contGridNodeStyle(col, childNode = null) {
				const s = this.node.settings || {};
				if ((s.displayType || 'flex') !== 'grid') return {};

				const justifyItems = String(this.nodeResponsiveValue('gridJustifyItems', s.gridJustifyItems || 'stretch') || 'stretch').toLowerCase();
				const style = {
					maxWidth: '100%',
					minWidth: '0',
					marginTop: '0',
					marginBottom: '0',
				};
				const isWidgetChild = !!childNode && !isCont(childNode.type) && !isGrid(childNode.type);

				// Keep the Balanced shell for the default "stretch" mode, but let
				// justify-items start/center/end position widgets again.
				if (isWidgetChild) {
					const shellJustifyMap = {
						start: 'flex-start',
						center: 'center',
						end: 'flex-end',
						stretch: 'flex-start',
					};
					const widgetType = String(childNode?.type || '').trim();
					const shrinkableWidgetTypes = new Set(['heading', 'button']);
					style.width = '100%';
					style.marginLeft = '0';
					style.marginRight = '0';
					style.alignSelf = 'stretch';
					style['--pb-widget-shell-justify'] = shellJustifyMap[justifyItems] || 'flex-start';
					style['--pb-widget-inner-width'] = (justifyItems === 'stretch' || !shrinkableWidgetTypes.has(widgetType)) ? '100%' : 'fit-content';
					style['--pb-widget-content-width'] = (justifyItems === 'stretch' || !shrinkableWidgetTypes.has(widgetType)) ? '100%' : 'fit-content';
				} else {
					if (justifyItems === 'center') {
						style.width = 'fit-content';
						style.marginLeft = 'auto';
						style.marginRight = 'auto';
					} else if (justifyItems === 'end') {
						style.width = 'fit-content';
						style.marginLeft = 'auto';
						style.marginRight = '0';
					} else if (justifyItems === 'start') {
						style.width = 'fit-content';
						style.marginLeft = '0';
						style.marginRight = 'auto';
					} else {
						style.width = '100%';
						style.marginLeft = '0';
						style.marginRight = '0';
					}
				}

				// Vertical placement applies only when a cell has one widget.
				const alignItems = String(this.nodeResponsiveValue('gridAlignItems', s.gridAlignItems || 'start') || 'start').toLowerCase();
				const childCount = Array.isArray(col?.children) ? col.children.length : 0;
				if (childCount <= 1) {
					if (alignItems === 'center') {
						style.marginTop = 'auto';
						style.marginBottom = 'auto';
					} else if (alignItems === 'end') {
						style.marginTop = 'auto';
						style.marginBottom = '0';
					}
				}

				return style;
			},
			contFlexNodeStyle(childNode = null) {
				const s = this.node.settings || {};
				if ((s.displayType || 'flex') !== 'flex') return {};
				const dir = this.nodeResponsiveValue('direction', s.direction || 'row') || 'row';
				const isRowDir = dir === 'row' || dir === 'row-reverse';
				if (!isRowDir) return {};

				const justifyContent = String(this.nodeResponsiveValue('justifyContent', s.justifyContent || 'flex-start') || 'flex-start').toLowerCase();
				const isWidgetChild = !!childNode && !isCont(childNode.type) && !isGrid(childNode.type);
				const style = {
					maxWidth: '100%',
					minWidth: '0',
				};

				if (!isWidgetChild) return style;

				const shellJustifyMap = {
					'flex-start': 'flex-start',
					center: 'center',
					'flex-end': 'flex-end',
					'space-between': 'flex-start',
					'space-around': 'flex-start',
					'space-evenly': 'flex-start',
					stretch: 'flex-start',
				};
				const widgetType = String(childNode?.type || '').trim();
				const shrinkableWidgetTypes = new Set(['heading', 'button']);
				const canShrink = (justifyContent === 'flex-start' || justifyContent === 'center' || justifyContent === 'flex-end')
					&& shrinkableWidgetTypes.has(widgetType);

				style.width = '100%';
				style.marginLeft = '0';
				style.marginRight = '0';
				style.alignSelf = 'stretch';
				style['--pb-widget-shell-justify'] = shellJustifyMap[justifyContent] || 'flex-start';
				style['--pb-widget-inner-width'] = canShrink ? 'fit-content' : '100%';
				style['--pb-widget-content-width'] = canShrink ? 'fit-content' : '100%';

				return style;
			},
			contChildNodeStyle(col, childNode = null) {
				const s = this.node.settings || {};
				const dt = s.displayType || 'flex';
				if (dt === 'grid') return this.contGridNodeStyle(col, childNode);
				if (dt === 'flex') return this.contFlexNodeStyle(childNode);
				return {};
			},
			nodeResponsiveValue(base, fallback = '') {
				const s = this.node.settings || {};
				const device = this.responsiveDevice || 'desktop';
				const key = responsiveKey(base, device);
				const value = s[key];
				if (device !== 'desktop' && (value === '' || value === null || value === undefined)) {
					const desktopValue = s[base];
					return (desktopValue === null || desktopValue === undefined || desktopValue === '') ? fallback : desktopValue;
				}
				if (value === null || value === undefined || value === '') return fallback;
				return value;
			},
			loadWidget,
		},
		template: `
<div
	class="pb-node"
	:id="nodeShellId || null"
	:class="['pb-node-' + node.type, nodeAdvancedClasses, { active: isVisualActive, 'is-toolbar-visible': isToolbarVisible, 'pb-grid-outline-enabled': !!node.settings?.gridOutline, 'pb-node-widget': isWidgetNode }]"
	:style="nodeShellStyle"
	:data-hover-label="label"
	:data-node-type="node.type"
	:data-node-id="node.id"
	@mouseenter.stop="onSetHover(node.id)"
	@mouseleave.stop="onClearHover(node.id, $event)"
>
	<div class="pb-node-toolbar" @click.stop>
		<div class="pb-node-label" @click.stop="onSelect(node)"><i :class="labelIcon"></i> {{ label }}</div>
		<div class="pb-node-actions" @click.stop="onSelect(node)">
			<button v-if="isCont" class="pb-node-btn" @click.stop="onOpenModal(node.type, 'root', { containerNode: node, list: [] })" title="Structure"><i class="fas fa-columns"></i></button>
			<button class="pb-node-btn" @click.stop="onDuplicate(node.id)" title="Duplicate"><i class="far fa-copy"></i></button>
			<button class="pb-node-btn remove" @click.stop="onRemove(node.id)" title="Delete"><i class="fas fa-trash"></i></button>
		</div>
	</div>

	<div class="pb-node-content" :style="contentShellStyle" @click.stop="onNodeContentClick(node, $event)">

		<!-- CONTAINER -->
		<!-- CONTAINER: merender columns sesuai displayType -->
		<template v-if="isCont">
			<component :is="loadWidget(node.type)" :item="node" :responsive-device="responsiveDevice">
				<div class="el-cont-columns" :style="contColumnsStyle">
					<div
						v-for="(col, ci) in (node.columns || [])"
						:key="col.id"
						:class="['el-grid-col', 'pb-grid-col', {
							'is-selected-col': isColumnSelected(col),
							'has-flex-col-controls': isFlexColumnEditor,
							'has-resize-handle': showColumnResizeHandle(ci)
						}]"
						:data-col-index="ci"
						:data-parent-node-id="node.id"
						:style="contColStyle(col)"
					>
						<button
							v-if="isFlexColumnEditor"
							type="button"
							class="pb-grid-col-label pb-grid-col-label-button"
							:class="{ 'is-active': isColumnSelected(col) }"
							@click.stop="selectColumn(col)"
						>
							<span class="pb-grid-col-label-badge"><i class="far fa-square"></i></span>
							<span>{{ columnLabel(ci) }}</span>
						</button>
						<button
							v-if="showColumnResizeHandle(ci)"
							type="button"
							class="pb-col-resizer"
							title="Drag to resize columns"
							@click.stop
							@mousedown.stop.prevent="startColumnResize($event, col, ci)"
						>
							<i class="fas fa-arrows-alt-h"></i>
						</button>
						<draggable
							v-model="col.children"
							item-key="id"
							:group="colGroup"
							data-pb-interactive="true"
							data-pb-nested-dropzone="true"
							:fallback-on-body="true"
							:dragover-bubble="false"
							:swap-threshold="0.65"
							:empty-insert-threshold="30"
							:data-col-index="ci"
							:data-parent-node-id="node.id"
							:data-parent-node-type="node.type"
							:style="contDropzoneStyle(col)"
							:class="['pb-dropzone', 'pb-dropzone-col', {
								'is-empty': !col.children || col.children.length === 0,
								'has-single-heading': !!(col.children && col.children.length === 1 && col.children[0] && col.children[0].type === 'heading'),
								'is-sequential-locked': isSequentialColumnLocked(ci),
								'is-pending-insert-target': pendingInsertTarget && pendingInsertTarget.type === 'column' && pendingInsertTarget.nodeId === node.id && pendingInsertTarget.colId === col.id
							}]"
							ghost-class="pb-ghost"
							dragover-class="is-drop-hover"
							@add="(e) => onAddCol(e, col, ci, node)"
							@start="onDragStart"
							@end="onDragEnd"
						>
							<template #item="{ element }">
								<BuilderNode :node="element" :style="contChildNodeStyle(col, element)" v-bind="passdown()" />
							</template>
							<template #footer>
								<div v-if="!col.children || col.children.length === 0" class="pb-dropzone-empty pb-grid-col-empty-hint">
									<button v-if="!isSequentialColumnLocked(ci)" type="button" class="pb-inline-add" data-pb-interactive="true" @click.stop.prevent="onShowToolbox({ type: 'column', nodeId: node.id, colId: col.id })">
										<i class="fas fa-plus"></i>
										<span>Add</span>
									</button>
									<div v-else class="pb-dropzone-lock-text">Fill previous column first</div>
									<div class="pb-dropzone-empty-text">{{ isSequentialColumnLocked(ci) ? 'Locked' : 'Drop here' }}</div>
								</div>
							</template>
						</draggable>
					</div>
				</div>
			</component>
		</template>

		<!-- GRID -->
		<template v-else-if="isGrid">
			<component :is="loadWidget(node.type)" :item="node">
				<div class="el-grid-columns" :style="gridStyle">
					<div v-for="(col, ci) in node.columns" :key="col.id" class="el-grid-col pb-grid-col" :data-col-index="ci">
						<draggable
							v-model="col.children"
							item-key="id"
							:group="colGroup"
							data-pb-interactive="true"
							data-pb-nested-dropzone="true"
							:fallback-on-body="true"
							:dragover-bubble="false"
							:swap-threshold="0.65"
							:empty-insert-threshold="30"
							:data-col-index="ci"
							:data-parent-node-id="node.id"
							:data-parent-node-type="node.type"
							:class="['pb-dropzone', 'pb-dropzone-col', {
								'is-empty': !col.children || col.children.length === 0,
								'has-single-heading': !!(col.children && col.children.length === 1 && col.children[0] && col.children[0].type === 'heading'),
								'is-sequential-locked': isSequentialColumnLocked(ci),
								'is-pending-insert-target': pendingInsertTarget && pendingInsertTarget.type === 'column' && pendingInsertTarget.nodeId === node.id && pendingInsertTarget.colId === col.id
							}]"
							ghost-class="pb-ghost"
							dragover-class="is-drop-hover"
							@add="(e) => onAddCol(e, col, ci, node)"
							@start="onDragStart"
							@end="onDragEnd"
						>
							<template #item="{ element }">
								<BuilderNode :node="element" v-bind="passdown()" />
							</template>
							<template #footer>
								<div v-if="!col.children || col.children.length === 0" class="pb-dropzone-empty pb-grid-col-empty-hint">
									<button v-if="!isSequentialColumnLocked(ci)" type="button" class="pb-inline-add" data-pb-interactive="true" @click.stop.prevent="onShowToolbox({ type: 'column', nodeId: node.id, colId: col.id })">
										<i class="fas fa-plus"></i>
										<span>Add</span>
									</button>
									<div v-else class="pb-dropzone-lock-text">Fill previous column first</div>
									<div class="pb-dropzone-empty-text">{{ isSequentialColumnLocked(ci) ? 'Locked' : 'Drop here' }}</div>
								</div>
							</template>
						</draggable>
					</div>
				</div>
			</component>
		</template>

		<!-- TABS -->
		<template v-else-if="isTabsNode">
			<div class="pb-preview pb-preview-tabs">
				<div class="pb-preview-inner">
					<component :is="loadWidget(node.type)" :item="node" :responsive-device="responsiveDevice">
						<draggable
							:list="activeTabsChildren()"
							item-key="id"
							:group="colGroup"
							:move="onTabDropzoneMove"
							:fallback-on-body="true"
							:dragover-bubble="false"
							:swap-threshold="0.65"
							:empty-insert-threshold="30"
							data-parent-node-type="tabs"
							:class="['pb-dropzone', 'pb-dropzone-tab', {
								'is-empty': activeTabsChildren().length === 0,
								'is-pending-insert-target': pendingInsertTarget && pendingInsertTarget.type === 'tab' && pendingInsertTarget.nodeId === node.id && pendingInsertTarget.tabId === (activeTabsItem() ? activeTabsItem().id : null)
							}]"
							ghost-class="pb-ghost"
							dragover-class="is-drop-hover"
							@add="onAddActiveTabChild"
							@start="onDragStart"
							@end="onDragEnd"
						>
							<template #item="{ element }">
								<BuilderNode :node="element" v-bind="passdown()" />
							</template>
							<template #footer>
								<div v-if="activeTabsChildren().length === 0" class="pb-dropzone-empty pb-tabs-empty-hint">
									<button type="button" class="pb-inline-add" data-pb-interactive="true" @click.stop.prevent="onOpenModal('container', 'tabs', activeTabsChildren())">
										<i class="fas fa-plus"></i>
										<span>Add</span>
									</button>
									<div class="pb-dropzone-empty-text">Drop here</div>
								</div>
							</template>
						</draggable>
					</component>
				</div>
			</div>
		</template>

		<!-- ACCORDION -->
		<template v-else-if="isAccordionNode">
			<div class="pb-preview pb-preview-accordion">
				<div class="pb-preview-inner">
					<component
						:is="loadWidget(node.type)"
						:item="node"
						:expanded-item-ids="accordionExpandedItemIds()"
						:responsive-device="responsiveDevice"
						@toggle-item="toggleAccordionItemFromPreview"
					>
						<template #panel="{ item }">
							<draggable
								:list="accordionItemChildren(item.id)"
								item-key="id"
								:group="colGroup"
								:move="onAccordionDropzoneMove"
								:fallback-on-body="true"
								:dragover-bubble="false"
								:swap-threshold="0.65"
								:empty-insert-threshold="30"
								data-parent-node-type="accordion"
								:data-parent-node-id="node.id"
								:data-accordion-item-id="item.id"
								:class="['pb-dropzone', 'pb-dropzone-accordion', {
									'is-empty': accordionItemChildren(item.id).length === 0,
									'is-pending-insert-target': pendingInsertTarget && pendingInsertTarget.type === 'accordion' && pendingInsertTarget.nodeId === node.id && pendingInsertTarget.itemId === item.id
								}]"
								ghost-class="pb-ghost"
								dragover-class="is-drop-hover"
								@add="(event) => onAddAccordionItemChild(event, item)"
								@start="onDragStart"
								@end="onDragEnd"
							>
								<template #item="{ element }">
									<BuilderNode :node="element" v-bind="passdown()" />
								</template>
								<template #footer>
									<div v-if="accordionItemChildren(item.id).length === 0" class="pb-dropzone-empty pb-accordion-empty-hint">
										<button type="button" class="pb-inline-add" data-pb-interactive="true" @click.stop.prevent="onShowToolbox({ type: 'accordion', nodeId: node.id, itemId: item.id })">
											<i class="fas fa-plus"></i>
											<span>Add</span>
										</button>
										<div class="pb-dropzone-empty-text">Drop here</div>
									</div>
								</template>
							</draggable>
						</template>
					</component>
				</div>
			</div>
		</template>

		<!-- WIDGET -->
		<template v-else>
			<div class="pb-preview">
				<div class="pb-preview-inner">
					<component :is="loadWidget(node.type)" :item="node" :responsive-device="responsiveDevice" />
				</div>
			</div>
		</template>

	</div>
</div>
		`,
	};

	// ── App ────────────────────────────────────────────────────────────────────
	const PBC = window.PAGE_BUILDER_ELEMENTOR_CONTEXT || {};

	createApp({
		components: { draggable, BuilderNode, CkEditorField, WidgetAdvancedControls, TypographyControl },
		setup() {
			const mode       = ref(PBC.mode || 'create');
			const saveUrl    = ref(PBC.saveUrl || '');
			const pd         = PBC.pageData || null;
			const pageName   = ref(pd?.page_name || 'Untitled Page');
			const pageStatus = ref(pd?.status || 'draft');
			const customCss  = ref(pd?.custom_css || '');
			const showCssEditor = ref(false);
			const cssEditorFullscreen = ref(false);
			const showTextEditorModal = ref(false);
			const textEditorModalFullscreen = ref(false);
			const customCssEditorTextarea = ref(null);
			const customCssEditorGutter = ref(null);
			const customCssGotoLine = ref('');
			const customCssSearchQuery = ref('');
			const customCssActiveLine = ref(0);
			const saveState  = ref('idle');
			const saveMsg    = ref('');
			const toastVisible = ref(false);
			const responseStatusToast = ref('ph-callout-success');
			const isArrayMessageAfterSubmit = ref(0);
			const responseMessageAfterSubmit = ref('');
			let toastTimer = null;
			let windowScrollLockFrame = 0;
			let customCssSearchTimer = null;

			function lockWindowScrollPosition() {
				if (windowScrollLockFrame) cancelAnimationFrame(windowScrollLockFrame);
				windowScrollLockFrame = requestAnimationFrame(() => {
					windowScrollLockFrame = 0;
					if (window.scrollX !== 0 || window.scrollY !== 0) {
						window.scrollTo(0, 0);
					}
				});
			}
			function keepFocusedEditorControlInPanel(event) {
				const target = event && event.target;
				const panel = document.querySelector('.pb-panel.left');
				if (!(target instanceof HTMLElement) || !panel || !panel.contains(target)) {
					lockWindowScrollPosition();
					return;
				}
				requestAnimationFrame(() => {
					const targetRect = target.getBoundingClientRect();
					const panelRect = panel.getBoundingClientRect();
					if (targetRect.top < panelRect.top) {
						panel.scrollTop -= (panelRect.top - targetRect.top) + 16;
					} else if (targetRect.bottom > panelRect.bottom) {
						panel.scrollTop += (targetRect.bottom - panelRect.bottom) + 16;
					}
					lockWindowScrollPosition();
				});
			}

			function collectMessageLines(payload, bucket) {
				if (payload === null || payload === undefined) return;
				if (Array.isArray(payload)) {
					payload.forEach(item => collectMessageLines(item, bucket));
					return;
				}
				if (typeof payload === 'object') {
					Object.values(payload).forEach(item => collectMessageLines(item, bucket));
					return;
				}
				const line = String(payload).trim();
				if (line) bucket.push(line);
			}
			function normalizeNoticeMessage(payload) {
				const lines = [];
				collectMessageLines(payload, lines);
				if (lines.length === 0) return { isArray: 0, message: 'Request failed.' };
				if (lines.length === 1) return { isArray: 0, message: lines[0] };
				return { isArray: 1, message: lines };
			}
			function closeToast() {
				toastVisible.value = false;
				if (toastTimer) {
					clearTimeout(toastTimer);
					toastTimer = null;
				}
			}
			function showSaveToast(status, payload) {
				const normalized = normalizeNoticeMessage(payload);
				responseStatusToast.value = status === 'success'
					? 'ph-callout-success'
					: status === 'info'
						? 'ph-callout-info'
						: 'ph-callout-danger';
				isArrayMessageAfterSubmit.value = normalized.isArray;
				responseMessageAfterSubmit.value = normalized.message;
				toastVisible.value = true;
				if (toastTimer) clearTimeout(toastTimer);
				toastTimer = setTimeout(() => {
					toastVisible.value = false;
					toastTimer = null;
				}, 3500);
			}
			function showUnsupportedControlNotice(label, detail = '') {
				const message = detail || (label + ' belum didukung di builder ini.');
				showSaveToast('info', message);
			}
			function customCssLines() {
				return String(customCss.value || '').split('\n');
			}
			const customCssCharCount = computed(() => String(customCss.value || '').length);
			const customCssLineCount = computed(() => customCssLines().length);
			const customCssLineNumbers = computed(() => Array.from({ length: customCssLineCount.value }, (_, index) => index + 1));
			const customCssSummary = computed(() => {
				if (!customCssCharCount.value) return 'Empty';
				return customCssCharCount.value + ' chars / ' + customCssLineCount.value + ' lines';
			});
			function syncCustomCssEditorScroll() {
				const textarea = customCssEditorTextarea.value;
				const gutter = customCssEditorGutter.value;
				if (!textarea || !gutter) return;
				gutter.scrollTop = textarea.scrollTop;
			}
			function customCssLineStartIndex(lineNumber) {
				const lines = customCssLines();
				const safeLine = Math.max(1, Math.min(Number(lineNumber) || 1, lines.length));
				let index = 0;
				for (let i = 0; i < safeLine - 1; i++) {
					index += lines[i].length + 1;
				}
				return index;
			}
			function customCssLineEndIndex(lineNumber) {
				const lines = customCssLines();
				const safeLine = Math.max(1, Math.min(Number(lineNumber) || 1, lines.length));
				return customCssLineStartIndex(safeLine) + lines[safeLine - 1].length;
			}
			function customCssLineFromIndex(index) {
				const before = String(customCss.value || '').slice(0, Math.max(0, index));
				return before.split('\n').length;
			}
			function selectCustomCssRange(start, end, lineNumber) {
				const textarea = customCssEditorTextarea.value;
				customCssActiveLine.value = Number(lineNumber) || 0;
				if (!textarea) return;
				nextTick(() => {
					const lineHeight = parseFloat(window.getComputedStyle(textarea).lineHeight) || 21;
					const targetScroll = Math.max(0, (customCssActiveLine.value - 1) * lineHeight - (textarea.clientHeight / 2) + (lineHeight * 2));
					textarea.focus();
					textarea.scrollTop = targetScroll;
					textarea.setSelectionRange(start, Math.max(start, end));
					syncCustomCssEditorScroll();
				});
			}
			function goToCustomCssLine() {
				const requested = Number.parseInt(String(customCssGotoLine.value || '').trim(), 10);
				if (!Number.isFinite(requested) || requested < 1 || requested > customCssLineCount.value) {
					customCssActiveLine.value = 0;
					showSaveToast('info', 'Line code tidak ditemukan. Total line: ' + customCssLineCount.value + '.');
					return;
				}
				selectCustomCssRange(customCssLineStartIndex(requested), customCssLineEndIndex(requested), requested);
			}
			function searchCustomCssCode(showNotFoundNotice = true) {
				const query = String(customCssSearchQuery.value || '');
				if (!query.trim()) {
					customCssActiveLine.value = 0;
					return;
				}
				const source = String(customCss.value || '');
				const index = source.toLowerCase().indexOf(query.toLowerCase());
				if (index < 0) {
					customCssActiveLine.value = 0;
					if (showNotFoundNotice) showSaveToast('info', 'Kode "' + query + '" tidak ditemukan di Custom CSS.');
					return;
				}
				const line = customCssLineFromIndex(index);
				selectCustomCssRange(index, index + query.length, line);
			}
			function scheduleCustomCssSearch(showNotFoundNotice = true) {
				if (customCssSearchTimer) clearTimeout(customCssSearchTimer);
				customCssSearchTimer = setTimeout(() => {
					customCssSearchTimer = null;
					searchCustomCssCode(showNotFoundNotice);
				}, 500);
			}
			function openCustomCssEditor() {
				showCssEditor.value = true;
				nextTick(() => {
					if (customCssEditorTextarea.value && typeof customCssEditorTextarea.value.focus === 'function') {
						customCssEditorTextarea.value.focus();
					}
					syncCustomCssEditorScroll();
				});
			}
			function closeCustomCssEditor() {
				showCssEditor.value = false;
			}
			function normalizeCustomCssBeforeApply(source) {
				const original = String(source || '');
				let value = original;
				const notices = [];
				if (/\b1important\b/i.test(value)) {
					value = value.replace(/\b1important\b/gi, '!important');
					notices.push('Typo CSS diperbaiki: gunakan "!important", bukan "1important".');
				}
				if (/\!\s+important\b/i.test(value)) {
					value = value.replace(/\!\s+important\b/gi, '!important');
					notices.push('Spasi pada "!important" dirapikan otomatis.');
				}
				return {
					value,
					changed: value !== original,
					notices,
				};
			}
			function applyCustomCssEditorChanges() {
				const normalized = normalizeCustomCssBeforeApply(customCss.value);
				if (normalized.changed) {
					customCss.value = normalized.value;
				}
				closeCustomCssEditor();
				if (normalized.notices.length) {
					showSaveToast('info', normalized.notices.join(' '));
				}
			}
			function clearCustomCss() {
				customCss.value = '';
				customCssActiveLine.value = 0;
				nextTick(() => {
					if (customCssEditorTextarea.value && typeof customCssEditorTextarea.value.focus === 'function') {
						customCssEditorTextarea.value.focus();
					}
				});
			}
			function handleCustomCssTab(event) {
				const textarea = event && event.target;
				if (!textarea || typeof textarea.selectionStart !== 'number') return;
				const start = textarea.selectionStart;
				const end = textarea.selectionEnd;
				const insert = '  ';
				customCss.value = String(customCss.value || '').slice(0, start) + insert + String(customCss.value || '').slice(end);
				nextTick(() => {
					textarea.selectionStart = textarea.selectionEnd = start + insert.length;
					syncCustomCssEditorScroll();
				});
			}
			function initColorisPlugin() {
				if (typeof window.Coloris !== 'function') return;
				window.Coloris({
					el: '.pb-coloris-input',
					theme: 'pill',
					formatToggle: true,
					closeButton: true,
					clearButton: true,
				});
			}
			function scheduleColorisInit() {
				nextTick(() => {
					initColorisPlugin();
				});
			}
			watch(customCssSearchQuery, () => scheduleCustomCssSearch(true));
			watch(customCss, () => {
				nextTick(syncCustomCssEditorScroll);
				if (String(customCssSearchQuery.value || '').trim()) scheduleCustomCssSearch(false);
			});
			onMounted(() => {
				lockWindowScrollPosition();
				window.addEventListener('scroll', lockWindowScrollPosition, { passive: true });
				window.addEventListener('focusin', keepFocusedEditorControlInPanel);
				scheduleColorisInit();
			});
			onBeforeUnmount(() => {
				if (toastTimer) clearTimeout(toastTimer);
				if (customCssSearchTimer) clearTimeout(customCssSearchTimer);
				if (windowScrollLockFrame) cancelAnimationFrame(windowScrollLockFrame);
				if (typeof activeColumnResizeCleanup === 'function') activeColumnResizeCleanup();
				window.removeEventListener('scroll', lockWindowScrollPosition);
				window.removeEventListener('focusin', keepFocusedEditorControlInPanel);
			});
			const selectedId  = ref('');
			const selectedColumnNodeId = ref('');
			const selectedColumnId = ref('');
			const hoveredId   = ref('');
			const settingsTab = ref('layout'); // 'layout' | 'style' | 'advanced'
			const responsiveDevice = ref('desktop');
			const desktopPreviewWidth = ref('1320');
			const widthPreviewMenuOpen = ref(false);
			const suppressHistory = ref(false);
			const columnResizeOverlay = ref({ visible: false, text: '', x: 0, y: 0 });
			const pendingInsertTarget = ref(null);
			const accordionRuntimeState = ref({});
			const accordionStyleState = ref('normal');
			const accordionTitleStyleState = ref('normal');
			const accordionIconStyleState = ref('normal');
			const accordionBoxLinks = ref({});
			let activeColumnResizeCleanup = null;
			const rootNodes   = ref([]);
			const responsiveDevices = [
				{ value: 'desktop', label: 'Desktop', menuLabel: 'Desktop', icon: 'fas fa-desktop' },
				{ value: 'tablet', label: 'Tablet', menuLabel: 'Tablet Portrait', icon: 'fas fa-tablet-alt' },
				{ value: 'mobile', label: 'Mobile', menuLabel: 'Mobile Portrait', icon: 'fas fa-mobile-alt' },
			];
			const fontFamilies = Array.isArray(window.PB_ELEMENTOR_FONT_FAMILIES)
				? window.PB_ELEMENTOR_FONT_FAMILIES
				: [];
			const desktopPreviewWidths = [
				{ value: '1140', label: '1140px' },
				{ value: '1320', label: '1320px' },
			];
			const controlResponsiveMenu = ref('');
			const responsiveColumnsCache = new WeakMap();
			function normalizeResponsiveDevice(device = 'desktop') {
				return (device === 'tablet' || device === 'mobile') ? device : 'desktop';
			}
			function normalizeDesktopPreviewWidth(value = '1320') {
				return String(value) === '1140' ? '1140' : '1320';
			}
			function cloneColumnsState(columns) {
				if (!Array.isArray(columns)) return [];
				return columns.map((col) => ({
					id: (col && col.id) ? col.id : uid('c'),
					flexBasis: col && Object.prototype.hasOwnProperty.call(col, 'flexBasis') ? col.flexBasis : undefined,
					children: Array.isArray(col && col.children) ? col.children.slice() : [],
				}));
			}
			function getResponsiveColumnsState(node) {
				if (!node || typeof node !== 'object') return null;
				let state = responsiveColumnsCache.get(node);
				if (!state) {
					state = {
						activeDevice: '',
						snapshots: {
							desktop: [],
							tablet: [],
							mobile: [],
						},
					};
					responsiveColumnsCache.set(node, state);
				}
				return state;
			}
			function resolveColumnsSnapshotForDevice(state, device) {
				if (!state || !state.snapshots) return [];
				const safeDevice = normalizeResponsiveDevice(device);
				const direct = state.snapshots[safeDevice];
				if (Array.isArray(direct) && direct.length) return direct;
				if (safeDevice === 'mobile') {
					const tablet = state.snapshots.tablet;
					if (Array.isArray(tablet) && tablet.length) return tablet;
				}
				if (safeDevice !== 'desktop') {
					const desktop = state.snapshots.desktop;
					if (Array.isArray(desktop) && desktop.length) return desktop;
				}
				return [];
			}
			function collectColumnChildren(columns) {
				const out = [];
				if (!Array.isArray(columns)) return out;
				columns.forEach((col) => {
					if (!col || !Array.isArray(col.children)) return;
					col.children.forEach((child) => {
						if (child && child.id) out.push(child);
					});
				});
				return out;
			}
			function reconcileColumnsContent(sourceColumns, targetColumns) {
				if (!Array.isArray(targetColumns) || !targetColumns.length) return;
				const sourceChildren = collectColumnChildren(sourceColumns);
				const sourceIds = new Set(sourceChildren.map((child) => child.id));

				targetColumns.forEach((col) => {
					if (!Array.isArray(col.children)) col.children = [];
					col.children = col.children.filter((child) => child && child.id && sourceIds.has(child.id));
				});

				const existingIds = new Set();
				targetColumns.forEach((col) => {
					(col.children || []).forEach((child) => {
						if (child && child.id) existingIds.add(child.id);
					});
				});

				const missing = sourceChildren.filter((child) => child && child.id && !existingIds.has(child.id));
				if (!missing.length) return;
				const receiver = targetColumns[0];
				if (!receiver) return;
				if (!Array.isArray(receiver.children)) receiver.children = [];
				missing.forEach((child) => receiver.children.push(child));
			}

			function responsiveMeta(device = responsiveDevice.value) {
				return responsiveDevices.find((entry) => entry.value === device) || responsiveDevices[0];
			}
			function responsiveDeviceIcon(device = responsiveDevice.value) {
				return responsiveMeta(device).icon;
			}
			function responsiveDeviceLabel(device = responsiveDevice.value) {
				const meta = responsiveMeta(device);
				return meta.menuLabel || meta.label || '';
			}
			function previewCanvasWidthLabel() {
				if (responsiveDevice.value === 'tablet') return '720px';
				if (responsiveDevice.value === 'mobile') return '540px';
				return normalizeDesktopPreviewWidth(desktopPreviewWidth.value) + 'px';
			}
			function previewCanvasStyle() {
				if (responsiveDevice.value !== 'desktop') return null;
				return {
					maxWidth: normalizeDesktopPreviewWidth(desktopPreviewWidth.value) + 'px',
				};
			}
			function toggleWidthPreviewMenu() {
				if (responsiveDevice.value !== 'desktop') return;
				widthPreviewMenuOpen.value = !widthPreviewMenuOpen.value;
			}
			function closeWidthPreviewMenu() {
				widthPreviewMenuOpen.value = false;
			}
			function selectDesktopPreviewWidth(value) {
				desktopPreviewWidth.value = normalizeDesktopPreviewWidth(value);
				widthPreviewMenuOpen.value = false;
			}
			function deviceOptionLabel(device) {
				if (!device || typeof device !== 'object') return '';
				return device.menuLabel || device.label || '';
			}
			function openControlResponsiveMenu(key) {
				const safeKey = String(key || '');
				controlResponsiveMenu.value = controlResponsiveMenu.value === safeKey ? '' : safeKey;
			}
			function closeControlResponsiveMenu() {
				controlResponsiveMenu.value = '';
			}
			function isControlResponsiveMenuOpen(key) {
				return controlResponsiveMenu.value === String(key || '');
			}
			function setResponsiveDevice(device) {
				const safeDevice = normalizeResponsiveDevice(device);
				if (safeDevice !== 'desktop') closeWidthPreviewMenu();
				if (responsiveDevice.value !== safeDevice) {
					responsiveDevice.value = safeDevice;
				}
			}
			function applyResponsiveDevice(key, device) {
				setResponsiveDevice(device);
				closeControlResponsiveMenu();
			}

			// ── History ───────────────────────────────────────────────────────
			const hist    = ref([]);
			const histIdx = ref(-1);
			const traveling = ref(false);

			function snap() {
				if (traveling.value || suppressHistory.value) return;
				const s = JSON.stringify(rootNodes.value);
				if (histIdx.value >= 0 && hist.value[histIdx.value] === s) return;
				hist.value = hist.value.slice(0, histIdx.value + 1);
				hist.value.push(s);
				histIdx.value = hist.value.length - 1;
			}
			function undo() {
				if (histIdx.value <= 0) return;
				traveling.value = true; histIdx.value--;
				rootNodes.value = norm(JSON.parse(hist.value[histIdx.value]));
				selectedId.value = '';
				nextTick(() => traveling.value = false);
			}
			function redo() {
				if (histIdx.value >= hist.value.length - 1) return;
				traveling.value = true; histIdx.value++;
				rootNodes.value = norm(JSON.parse(hist.value[histIdx.value]));
				selectedId.value = '';
				nextTick(() => traveling.value = false);
			}
			const canUndo = computed(() => histIdx.value > 0);
			const canRedo = computed(() => histIdx.value < hist.value.length - 1);

			// ── Tree ─────────────────────────────────────────────────────────
			function norm(nodes) {
				return (nodes || []).map(n => {
					const c = jclone(n);
					if (!c.id) c.id = uid('n');
					const baseLabel = baseNodeLabel(c.type, '');
					const legacyLabel = String(c.label || '').trim();
					let suffix = String(c.labelSuffix || '').trim();
					if (!suffix && baseLabel && legacyLabel && legacyLabel !== baseLabel) {
						const prefix = baseLabel + ' ';
						if (legacyLabel.toLowerCase().startsWith(prefix.toLowerCase())) {
							suffix = legacyLabel.slice(prefix.length).trim();
						} else {
							suffix = legacyLabel;
						}
					}
					if (baseLabel) c.label = baseLabel;
					if (suffix) c.labelSuffix = suffix;
					else c.labelSuffix = '';
					if (isGrid(c.type)) {
						c.settings = { ...gridDefaults(c.type), ...(c.settings || {}) };
						if (c.settings.customCss && !c.settings.cssClass) c.settings.cssClass = c.settings.customCss;
						c.settings.animateWithAI = false;
						if (c.settings.gap && !c.settings.columnGap) c.settings.columnGap = c.settings.gap;
						if (c.settings.gap && !c.settings.rowGap) c.settings.rowGap = c.settings.gap;
						if (c.settings.bgGradientStartPos != null || c.settings.bgGradientEndPos != null) {
							const startPos = Number(c.settings.bgGradientStartPos == null ? 0 : c.settings.bgGradientStartPos);
							const endPos = Number(c.settings.bgGradientEndPos == null ? 100 : c.settings.bgGradientEndPos);
							if (c.settings.bgGradientPosition == null || c.settings.bgGradientPosition === '') {
								c.settings.bgGradientPosition = clamp(Math.round((startPos + endPos) / 2), 0, 100);
							}
						}
						if (c.settings.borderRadius && !c.settings.borderRadiusTL && !c.settings.borderRadiusTR && !c.settings.borderRadiusBR && !c.settings.borderRadiusBL) {
							c.settings.borderRadiusTL = c.settings.borderRadius;
							c.settings.borderRadiusTR = c.settings.borderRadius;
							c.settings.borderRadiusBR = c.settings.borderRadius;
							c.settings.borderRadiusBL = c.settings.borderRadius;
						}
						c.settings.attributes = normalizeAttributes(c.settings.attributes);
						const targetCols = clamp(Number(c.settings.columns || (Array.isArray(c.columns) ? c.columns.length : 1) || 1), 1, 12);
						c.settings.columns = targetCols;
						if (!Array.isArray(c.columns) || !c.columns.length) c.columns = [{id:uid('c'),children:[]}];
						c.columns = c.columns.map(col => ({ id: col.id||uid('c'), children: norm(col.children||[]) }));
						while (c.columns.length < targetCols) c.columns.push({ id: uid('c'), children: [] });
						if (c.columns.length > targetCols) {
							const last = c.columns[targetCols - 1];
							c.columns.slice(targetCols).forEach(col => (col.children || []).forEach(child => last.children.push(child)));
							c.columns = c.columns.slice(0, targetCols);
						}
					}
					if (isCont(c.type)) {
						c.settings = { ...containerDefaults(c.type), ...(c.settings || {}) };
						if (c.type === 'container_fluid') c.settings.contentWidth = 'fluid';
						const legacyContRows = String(c.settings.gridRows == null ? '' : c.settings.gridRows).trim().match(/^(\d+(?:\.\d+)?)fr$/i);
						if (legacyContRows) c.settings.gridRows = String(clamp(Number(legacyContRows[1]) || 1, 1, 12));
						if ((c.settings.displayType || 'flex') === 'flex' && c.settings.alignItems === 'stretch') c.settings.alignItems = 'flex-start';
						if ((c.settings.displayType || 'flex') === 'grid' && c.settings.gridAlignItems === 'stretch') c.settings.gridAlignItems = 'start';
						if (c.settings.customCss && !c.settings.cssClass) c.settings.cssClass = c.settings.customCss;
						c.settings.animateWithAI = false;
						if (c.settings.gap && !c.settings.flexRowGap) c.settings.flexRowGap = c.settings.rowGap || c.settings.gap;
						if (c.settings.gap && !c.settings.flexColumnGap) c.settings.flexColumnGap = c.settings.columnGap || c.settings.gap;
						if (c.settings.columnGap && !c.settings.gridColumnGap) c.settings.gridColumnGap = c.settings.columnGap;
						if (c.settings.rowGap && !c.settings.gridRowGap) c.settings.gridRowGap = c.settings.rowGap;
						if (c.settings.bgGradientStartPos != null || c.settings.bgGradientEndPos != null) {
							const startPos = Number(c.settings.bgGradientStartPos == null ? 0 : c.settings.bgGradientStartPos);
							const endPos = Number(c.settings.bgGradientEndPos == null ? 100 : c.settings.bgGradientEndPos);
							if (c.settings.bgGradientPosition == null || c.settings.bgGradientPosition === '') {
								c.settings.bgGradientPosition = clamp(Math.round((startPos + endPos) / 2), 0, 100);
							}
						}
						if (c.settings.borderRadius && !c.settings.borderRadiusTL && !c.settings.borderRadiusTR && !c.settings.borderRadiusBR && !c.settings.borderRadiusBL) {
							c.settings.borderRadiusTL = c.settings.borderRadius;
							c.settings.borderRadiusTR = c.settings.borderRadius;
							c.settings.borderRadiusBR = c.settings.borderRadius;
							c.settings.borderRadiusBL = c.settings.borderRadius;
						}
						if (!Array.isArray(c.children)) c.children = [];
						c.settings.attributes = normalizeAttributes(c.settings.attributes);
						// Normalisasi columns[] Container (arsitektur baru)
						const tcContCols = clamp(Number(c.settings.gridColumns || (Array.isArray(c.columns) ? c.columns.length : 1) || 1), 1, 12);
						c.settings.gridColumns = tcContCols;
						const tcContRows = (c.settings.displayType || 'flex') === 'grid' ? containerGridRowsCount(c.settings.gridRows) : 1;
						const tcCont = Math.max(1, tcContCols * tcContRows);
						if (!Array.isArray(c.columns) || !c.columns.length) {
							if (c.children.length > 0) { c.columns = [{ id: uid('c'), children: norm(c.children) }]; c.children = []; }
							else { c.columns = []; }
						} else {
							c.columns = c.columns.map(col => ({ id: col.id||uid('c'), flexBasis: col.flexBasis, children: norm(col.children||[]) }));
						}
						while (c.columns.length < tcCont) c.columns.push({ id: uid('c'), children: [] });
						if (c.columns.length > tcCont) { const last=c.columns[tcCont-1]; c.columns.slice(tcCont).forEach(col=>(col.children||[]).forEach(ch=>last.children.push(ch))); c.columns=c.columns.slice(0,tcCont); }
					}
					if (c.type === 'video') {
						c.settings = { ...videoDefaults(), ...(c.settings || {}) };
						normalizeVideoNodeSettings(c.settings);
					}
					if (c.type === 'icon') {
						c.settings = { ...iconWidgetDefaults(), ...(c.settings || {}) };
						normalizeIconWidgetSettings(c.settings);
					}
					if (c.type === 'image_box') {
						c.settings = { ...imageBoxWidgetDefaults(), ...(c.settings || {}) };
						normalizeImageBoxSettings(c.settings);
					}
					if (c.type === 'tabs') {
						c.settings = { ...tabsWidgetDefaults(), ...(c.settings || {}) };
						c.settings.direction = normalizeTabsDirection(c.settings.direction);
						c.settings.justify = normalizeTabsJustify(c.settings.justify);
						c.settings.alignTitle = normalizeTabsAlignTitle(c.settings.alignTitle);
						c.settings.tabWidth = normalizeTabsWidthValue(c.settings.tabWidth);
						c.settings.tabWidthUnit = normalizeTabsWidthUnit(c.settings.tabWidthUnit);
						c.settings.horizontalScroll = !!c.settings.horizontalScroll;
						c.settings.breakpoint = normalizeTabsBreakpoint(c.settings.breakpoint);
						c.settings.cssClass = String(c.settings.cssClass || '').trim();
						const rawItems = Array.isArray(c.tabItems) && c.tabItems.length
							? c.tabItems
							: tabsWidgetDefaultItems();
						c.tabItems = rawItems.map((item, index) => ({
							id: item && item.id ? item.id : uid('tab'),
							title: String(item && item.title ? item.title : ('Tab #' + (index + 1))).trim() || ('Tab #' + (index + 1)),
							iconClass: normalizeTabsItemClass(item && item.iconClass),
							activeIconClass: normalizeTabsItemClass(item && item.activeIconClass),
							cssId: normalizeTabsCssId(item && item.cssId),
							children: norm((item && item.children) || []),
						}));
						if (!c.tabItems.length) {
							c.tabItems = [tabsItemDefaults(0)];
						}
						const activeTabId = String(c.settings.activeTabId || '').trim();
						c.settings.activeTabId = c.tabItems.some((item) => String(item.id || '') === activeTabId)
							? activeTabId
							: c.tabItems[0].id;
					}
			if (c.type === 'accordion') {
						c.settings = { ...accordionWidgetDefaults(), ...(c.settings || {}) };
						normalizeWidgetAdvancedSettings(c.settings);
						c.settings.faqSchema = !!c.settings.faqSchema;
						c.settings.defaultState = c.settings.defaultState === 'all-collapsed' ? 'all-collapsed' : 'first-expanded';
						c.settings.maxExpanded = c.settings.maxExpanded === 'multiple' ? 'multiple' : 'one';
						c.settings.animationDuration = clamp(Number(c.settings.animationDuration) || 400, 0, 5000);
						c.settings.cssClass = String(c.settings.cssClass || '').trim();
						const rawItems = Array.isArray(c.accordionItems) && c.accordionItems.length
							? c.accordionItems
							: accordionWidgetDefaultItems();
						c.accordionItems = rawItems.map((rawItem, index) => {
							const item = {
								id: rawItem && rawItem.id ? rawItem.id : uid('accordion_item'),
								title: String(rawItem && rawItem.title ? rawItem.title : ('Item #' + (index + 1))).trim() || ('Item #' + (index + 1)),
								cssId: normalizeTabsCssId(rawItem && rawItem.cssId),
								children: (rawItem && rawItem.children) || [],
							};
							item.children = norm(item.children || []);
							return item;
						});
						if (!c.accordionItems.length) c.accordionItems = [accordionItemDefaults(0)];
					}
					if (c.settings && typeof c.settings === 'object') {
						seedResponsiveSettings(c.settings);
					}
					if (Array.isArray(c.children)) c.children = norm(c.children);
					return c;
				});
			}
			function parse(raw) {
				if (!raw) return [];
				if (Array.isArray(raw)) return raw;
				try { const p = JSON.parse(raw); return Array.isArray(p) ? p : []; } catch { return []; }
			}

			rootNodes.value = norm(parse(pd?.vars));
			snap();
			watch(rootNodes, snap, { deep: true });

			// ── Find ─────────────────────────────────────────────────────────
			function findById(nodes, id) {
				for (const n of nodes) {
					if (n.id === id) return n;
					if (n.children) { const r = findById(n.children, id); if (r) return r; }
					if (n.columns) for (const col of n.columns) { const r = findById(col.children||[], id); if (r) return r; }
					if (n.tabItems) for (const item of n.tabItems) { const r = findById(item.children||[], id); if (r) return r; }
					if (n.accordionItems) for (const item of n.accordionItems) { const r = findById(item.children||[], id); if (r) return r; }
				}
				return null;
			}

			const selectedNode = computed(() => selectedId.value ? findById(rootNodes.value, selectedId.value) : null);
			const selectedType = computed(() => selectedNode.value?.type || '');
			const textEditorModalSummary = computed(() => {
				const html = selectedType.value === 'text_editor' ? String(selectedNode.value?.settings?.html || '') : '';
				const text = html.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
				if (!text.length) return 'Empty';
				return text.length + ' chars';
			});
			function setTextEditorHtml(value) {
				if (selectedType.value !== 'text_editor' || !selectedNode.value) return;
				if (!selectedNode.value.settings) selectedNode.value.settings = {};
				selectedNode.value.settings.html = value || '';
			}
			function openTextEditorModal() {
				if (selectedType.value !== 'text_editor') return;
				showTextEditorModal.value = true;
			}
			function closeTextEditorModal() {
				showTextEditorModal.value = false;
				textEditorModalFullscreen.value = false;
			}
			watch(selectedType, (type) => {
				if (type !== 'text_editor' && showTextEditorModal.value) closeTextEditorModal();
				if (type !== 'icon') iconLinkOptionsOpenFor.value = '';
			});
			function tabsItemsForNode(node = selectedNode.value) {
				if (!node || node.type !== 'tabs') return [];
				if (!Array.isArray(node.tabItems) || !node.tabItems.length) {
					node.tabItems = tabsWidgetDefaultItems();
				}
				const activeTabId = String(node.settings?.activeTabId || '').trim();
				if (!node.tabItems.some((item) => String(item.id || '') === activeTabId)) {
					if (!node.settings || typeof node.settings !== 'object') node.settings = {};
					node.settings.activeTabId = node.tabItems[0].id;
				}
				return node.tabItems;
			}
			function tabsActiveItem(node = selectedNode.value) {
				const items = tabsItemsForNode(node);
				if (!items.length) return null;
				const activeTabId = String(node?.settings?.activeTabId || '').trim();
				return items.find((item) => String(item.id || '') === activeTabId) || items[0];
			}
			function clearPendingInsertTarget() {
				pendingInsertTarget.value = null;
			}
			function setPendingInsertTarget(target = null) {
				pendingInsertTarget.value = target && typeof target === 'object' ? jclone(target) : null;
			}
			function findColumnById(node, colId) {
				if (!node || !Array.isArray(node.columns)) return null;
				return node.columns.find((col) => String(col && col.id || '') === String(colId || '')) || null;
			}
			function insertToolIntoPendingTarget(toolDef, target = pendingInsertTarget.value) {
				if (!toolDef || !target) return false;
				const item = toolClone(toolDef);
				if (!item) return false;
				if (isCont(item.type)) {
					showUnsupportedControlNotice('Container', 'Container belum bisa dimasukkan langsung ke target + Add ini. Gunakan Grid atau widget biasa.');
					return false;
				}
				if (target.type === 'column') {
					const ownerNode = findById(rootNodes.value, target.nodeId);
					const targetColumn = findColumnById(ownerNode, target.colId);
					const targetIndex = Array.isArray(ownerNode && ownerNode.columns) ? ownerNode.columns.findIndex((col) => String(col && col.id || '') === String(target.colId || '')) : -1;
					if (!ownerNode || !targetColumn || !Array.isArray(targetColumn.children)) return false;
					if (Number.isFinite(targetIndex) && isSequentialColumnLockedForNode(ownerNode, targetIndex)) return false;
					targetColumn.children.push(item);
					selectedId.value = item.id;
					clearPendingInsertTarget();
					return true;
				}
				if (target.type === 'tab') {
					const tabsNode = findById(rootNodes.value, target.nodeId);
					if (!tabsNode || tabsNode.type !== 'tabs' || !Array.isArray(tabsNode.tabItems)) return false;
					const targetTab = tabsNode.tabItems.find((tab) => String(tab && tab.id || '') === String(target.tabId || ''));
					if (!targetTab || !Array.isArray(targetTab.children)) return false;
					if (!tabsNode.settings || typeof tabsNode.settings !== 'object') tabsNode.settings = {};
					tabsNode.settings.activeTabId = targetTab.id;
					targetTab.children.push(item);
					selectedId.value = item.id;
					clearPendingInsertTarget();
					return true;
				}
				if (target.type === 'accordion') {
					const accordionNode = findById(rootNodes.value, target.nodeId);
					if (!accordionNode || accordionNode.type !== 'accordion' || !Array.isArray(accordionNode.accordionItems)) return false;
					const targetItem = accordionNode.accordionItems.find((entry) => String(entry?.id || '') === String(target.itemId || ''));
					if (!targetItem || !Array.isArray(targetItem.children)) return false;
					const runtime = accordionRuntimeForNode(accordionNode);
					if (!runtime.expandedItemIds.includes(String(targetItem.id))) {
						runtime.expandedItemIds = accordionNode.settings?.maxExpanded === 'multiple'
							? runtime.expandedItemIds.concat(String(targetItem.id))
							: [String(targetItem.id)];
					}
					targetItem.children.push(item);
					selectedId.value = item.id;
					clearPendingInsertTarget();
					return true;
				}
				return false;
			}
			function onToolboxItemClick(toolDef) {
				if (!pendingInsertTarget.value) return;
				insertToolIntoPendingTarget(toolDef, pendingInsertTarget.value);
			}
			function selectTabsItem(node, itemId) {
				if (!node || node.type !== 'tabs') return;
				const items = tabsItemsForNode(node);
				const match = items.find((item) => String(item.id || '') === String(itemId || ''));
				if (!match) return;
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				node.settings.activeTabId = match.id;
			}
			function addTabsItem(node = selectedNode.value) {
				if (!node || node.type !== 'tabs') return;
				const items = tabsItemsForNode(node);
				const next = tabsItemDefaults(items.length);
				items.push(next);
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				node.settings.activeTabId = next.id;
			}
			function duplicateTabsItem(node = selectedNode.value, itemId = '') {
				if (!node || node.type !== 'tabs') return;
				const items = tabsItemsForNode(node);
				const index = items.findIndex((item) => String(item.id || '') === String(itemId || ''));
				if (index < 0) return;
				const copy = jclone(items[index]);
				copy.id = uid('tab');
				copy.title = String(copy.title || ('Tab #' + (index + 1))).trim() || ('Tab #' + (index + 1));
				copy.children = norm(copy.children || []);
				(copy.children || []).forEach(regenIds);
				items.splice(index + 1, 0, copy);
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				node.settings.activeTabId = copy.id;
			}
			function removeTabsItem(node = selectedNode.value, itemId = '') {
				if (!node || node.type !== 'tabs') return;
				const items = tabsItemsForNode(node);
				if (items.length <= 1) return;
				const index = items.findIndex((item) => String(item.id || '') === String(itemId || ''));
				if (index < 0) return;
				items.splice(index, 1);
				const fallbackIndex = Math.max(0, index - 1);
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				node.settings.activeTabId = items[fallbackIndex].id;
			}
			function tabsItemSummary(item, index) {
				if (!item) return 'Tab #' + (index + 1);
				return String(item.title || '').trim() || ('Tab #' + (index + 1));
			}
			function tabsSelectedRowDirection(node = selectedNode.value) {
				return tabsRowDirection(node?.settings?.direction);
			}
			function accordionItemsForNode(node = selectedNode.value) {
				if (!node || node.type !== 'accordion') return [];
				if (!Array.isArray(node.accordionItems) || !node.accordionItems.length) {
					node.accordionItems = [accordionItemDefaults(0)];
				}
				return node.accordionItems;
			}
			function sameStringArray(left, right) {
				if (!Array.isArray(left) || !Array.isArray(right) || left.length !== right.length) return false;
				return left.every((value, index) => String(value || '') === String(right[index] || ''));
			}
			function accordionRuntimeForNode(node) {
				if (!node || node.type !== 'accordion') {
					return { editingItemId: '', expandedItemIds: [], transitioningItemIds: [] };
				}
				const items = accordionItemsForNode(node);
				const validIds = items.map((item) => String(item.id || '')).filter(Boolean);
				let runtime = accordionRuntimeState.value[node.id];
				if (!runtime) {
					runtime = {
						editingItemId: validIds[0] || '',
						expandedItemIds: node.settings?.defaultState === 'all-collapsed' ? [] : validIds.slice(0, 1),
						transitioningItemIds: [],
					};
					accordionRuntimeState.value[node.id] = runtime;
				}
				runtime.editingItemId = validIds.includes(String(runtime.editingItemId || ''))
					? String(runtime.editingItemId)
					: (validIds[0] || '');
				let normalizedExpandedItemIds = (Array.isArray(runtime.expandedItemIds) ? runtime.expandedItemIds : [])
					.map((id) => String(id || ''))
					.filter((id, index, ids) => validIds.includes(id) && ids.indexOf(id) === index);
				if (node.settings?.maxExpanded !== 'multiple' && normalizedExpandedItemIds.length > 1) {
					normalizedExpandedItemIds = normalizedExpandedItemIds.slice(-1);
				}
				if (!sameStringArray(runtime.expandedItemIds, normalizedExpandedItemIds)) {
					runtime.expandedItemIds = normalizedExpandedItemIds;
				}
				const normalizedTransitioningItemIds = (Array.isArray(runtime.transitioningItemIds) ? runtime.transitioningItemIds : [])
					.filter((id) => validIds.includes(String(id || '')));
				if (!sameStringArray(runtime.transitioningItemIds, normalizedTransitioningItemIds)) {
					runtime.transitioningItemIds = normalizedTransitioningItemIds;
				}
				return runtime;
			}
			function selectAccordionItem(node, itemId) {
				const items = accordionItemsForNode(node);
				const match = items.find((item) => String(item.id || '') === String(itemId || ''));
				if (!match) return;
				accordionRuntimeForNode(node).editingItemId = match.id;
			}
			function toggleAccordionItem(node, itemId) {
				const items = accordionItemsForNode(node);
				const match = items.find((item) => String(item.id || '') === String(itemId || ''));
				if (!match) return;
				const runtime = accordionRuntimeForNode(node);
				const safeId = String(match.id);
				if (runtime.expandedItemIds.includes(safeId)) {
					runtime.expandedItemIds = runtime.expandedItemIds.filter((id) => id !== safeId);
					return;
				}
				runtime.expandedItemIds = node.settings?.maxExpanded === 'multiple'
					? runtime.expandedItemIds.concat(safeId)
					: [safeId];
			}
			function resetAccordionRuntimeFromDefaults(node = selectedNode.value) {
				if (!node || node.type !== 'accordion') return;
				const items = accordionItemsForNode(node);
				const runtime = accordionRuntimeForNode(node);
				runtime.expandedItemIds = node.settings?.defaultState === 'all-collapsed' || !items.length
					? []
					: [items[0].id];
			}
			function addAccordionItem(node = selectedNode.value) {
				if (!node || node.type !== 'accordion') return;
				const items = accordionItemsForNode(node);
				const next = accordionItemDefaults(items.length);
				items.push(next);
				accordionRuntimeForNode(node).editingItemId = next.id;
			}
			function duplicateAccordionItem(node = selectedNode.value, itemId = '') {
				if (!node || node.type !== 'accordion') return;
				const items = accordionItemsForNode(node);
				const index = items.findIndex((item) => String(item.id || '') === String(itemId || ''));
				if (index < 0) return;
				const copy = jclone(items[index]);
				copy.id = uid('accordion_item');
				copy.children = norm(copy.children || []);
				copy.children.forEach(regenIds);
				items.splice(index + 1, 0, copy);
				accordionRuntimeForNode(node).editingItemId = copy.id;
			}
			function removeAccordionItem(node = selectedNode.value, itemId = '') {
				if (!node || node.type !== 'accordion') return;
				const items = accordionItemsForNode(node);
				if (items.length <= 1) return;
				const index = items.findIndex((item) => String(item.id || '') === String(itemId || ''));
				if (index < 0) return;
				items.splice(index, 1);
				const runtime = accordionRuntimeForNode(node);
				runtime.editingItemId = items[Math.max(0, index - 1)].id;
				runtime.expandedItemIds = runtime.expandedItemIds.filter((id) => String(id) !== String(itemId));
			}
			function accordionEditingItem(node = selectedNode.value) {
				const items = accordionItemsForNode(node);
				const editingId = accordionRuntimeForNode(node).editingItemId;
				return items.find((item) => String(item.id || '') === String(editingId || '')) || items[0] || null;
			}
			function accordionItemSummary(item, index) {
				return String(item?.title || '').trim() || ('Item #' + (index + 1));
			}
			function tabsWidthUnit(node = selectedNode.value) {
				return TABS_WIDGET_WIDTH_UNITS.includes(node?.settings?.tabWidthUnit) ? node.settings.tabWidthUnit : 'px';
			}
			function tabsWidthMax(node = selectedNode.value) {
				return tabsWidthUnit(node) === '%' ? 100 : 1200;
			}
			function tabsWidthStep(node = selectedNode.value) {
				return tabsWidthUnit(node) === '%' ? 1 : 1;
			}
			function tabsWidthValue(node = selectedNode.value) {
				const raw = normalizeTabsWidthValue(node?.settings?.tabWidth);
				if (raw === '') return tabsWidthUnit(node) === '%' ? 25 : 180;
				return clamp(Number(raw), 1, tabsWidthMax(node));
			}
			function setTabsWidthValue(node = selectedNode.value, next = '') {
				if (!node || node.type !== 'tabs') return;
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				const num = Number(next);
				node.settings.tabWidth = Number.isFinite(num) ? String(clamp(num, 1, tabsWidthMax(node))) : '';
			}
			function onTabsWidthInput(node, event) {
				if (!event || !event.target) return;
				setTabsWidthValue(node, event.target.value);
				nextTick(() => {
					event.target.value = String(tabsWidthValue(node));
				});
			}
			function setTabsWidthUnit(node = selectedNode.value, unit = 'px') {
				if (!node || node.type !== 'tabs') return;
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				const safe = TABS_WIDGET_WIDTH_UNITS.includes(unit) ? unit : 'px';
				node.settings.tabWidthUnit = safe;
				setTabsWidthValue(node, tabsWidthValue(node));
			}
			const iconLibraryGroups = FONT_AWESOME_5_ICON_GROUPS;
			const showIconLibraryModal = ref(false);
			const iconLibraryGroup = ref('all');
			const iconLibrarySearch = ref('');
			const iconLibraryIcons = ref([]);
			const iconLibrarySelected = ref(null);
			const iconLibraryTargetNodeId = ref('');
			const iconLibraryTargetSettingKey = ref('');
			const iconLibraryLoading = ref(false);
			const iconLibraryLoaded = ref(false);
			const iconLibraryError = ref('');
			const iconLinkOptionsOpenFor = ref('');
			const filteredIconLibraryIcons = computed(() => {
				const query = String(iconLibrarySearch.value || '').trim().toLowerCase();
				const groupKey = String(iconLibraryGroup.value || 'all').trim().toLowerCase();
				return iconLibraryIcons.value.filter((item) => {
					if (!item || typeof item !== 'object') return false;
					if (groupKey !== 'all' && item.style !== groupKey) return false;
					if (!query) return true;
					return String(item.searchText || '').includes(query);
				});
			});
			function iconWidgetUsesShape(node = selectedNode.value) {
				const view = String(node?.settings?.view || 'default').trim().toLowerCase();
				return view === 'stacked' || view === 'framed';
			}
			function iconWidgetCurrentLabel(node = selectedNode.value) {
				const settings = node && node.settings ? node.settings : {};
				return humanizeIconName(settings.iconName || parseIconWidgetClassParts(settings.iconClass).name || 'star');
			}
			function iconWidgetCurrentStyleLabel(node = selectedNode.value) {
				const settings = node && node.settings ? node.settings : {};
				const parsed = parseIconWidgetClassParts(settings.iconClass);
				return fontAwesomeStyleLabel(settings.iconStyle || parsed.style || 'regular');
			}
			function isIconLinkOptionsOpen(node = selectedNode.value) {
				return !!node && String(iconLinkOptionsOpenFor.value || '') === String(node.id || '');
			}
			function toggleIconLinkOptions(node = selectedNode.value) {
				if (!node || node.type !== 'icon') return;
				iconLinkOptionsOpenFor.value = isIconLinkOptionsOpen(node) ? '' : String(node.id || '');
			}
			async function ensureIconLibraryLoaded() {
				if (iconLibraryLoaded.value || iconLibraryLoading.value) return;
				iconLibraryLoading.value = true;
				iconLibraryError.value = '';
				try {
					const response = await fetch(FONT_AWESOME_5_ICON_METADATA_URL, { cache: 'no-store' });
					if (!response.ok) throw new Error('HTTP ' + response.status);
					const payload = await response.json();
					iconLibraryIcons.value = buildFontAwesomeIconLibrary(payload);
					iconLibraryLoaded.value = true;
				} catch (error) {
					console.error('[PB] failed to load icon library metadata', error);
					iconLibraryIcons.value = [];
					iconLibraryError.value = 'Icon library tidak berhasil dimuat dari paket lokal Font Awesome.';
				} finally {
					iconLibraryLoading.value = false;
				}
			}
			function syncIconLibrarySelectionFromNode(node) {
				if (!node || node.type !== 'icon') {
					iconLibrarySelected.value = null;
					return;
				}
				normalizeIconWidgetSettings(node.settings || (node.settings = {}));
				const targetStyle = String(node.settings.iconStyle || '').trim().toLowerCase();
				const targetName = String(node.settings.iconName || '').trim().toLowerCase();
				iconLibrarySelected.value = iconLibraryIcons.value.find((item) => item.style === targetStyle && item.name === targetName) || null;
			}
			async function openIconLibrary(node = selectedNode.value) {
				if (!node || node.type !== 'icon') return;
				await ensureIconLibraryLoaded();
				iconLibraryTargetNodeId.value = String(node.id || '');
				iconLibraryTargetSettingKey.value = '';
				iconLibraryGroup.value = 'all';
				iconLibrarySearch.value = '';
				syncIconLibrarySelectionFromNode(node);
				showIconLibraryModal.value = true;
			}
			function closeIconLibrary() {
				showIconLibraryModal.value = false;
				iconLibraryGroup.value = 'all';
				iconLibrarySearch.value = '';
				iconLibrarySelected.value = null;
				iconLibraryTargetNodeId.value = '';
				iconLibraryTargetSettingKey.value = '';
			}
			async function openAccordionIconLibrary(role, node = selectedNode.value) {
				if (!node || node.type !== 'accordion' || !['expand', 'collapse'].includes(role)) return;
				await ensureIconLibraryLoaded();
				const settingKey = role + 'IconClass';
				const parsed = parseIconWidgetClassParts(node.settings?.[settingKey]);
				iconLibraryTargetNodeId.value = String(node.id || '');
				iconLibraryTargetSettingKey.value = settingKey;
				iconLibraryGroup.value = 'all';
				iconLibrarySearch.value = '';
				iconLibrarySelected.value = iconLibraryIcons.value.find((item) => item.style === parsed.style && item.name === parsed.name) || null;
				showIconLibraryModal.value = true;
			}
			function selectIconLibraryItem(item) {
				iconLibrarySelected.value = item || null;
			}
			function insertSelectedIcon() {
				if (!iconLibrarySelected.value) return;
				const nodeId = String(iconLibraryTargetNodeId.value || '');
				const node = nodeId ? findById(rootNodes.value, nodeId) : selectedNode.value;
				const settingKey = String(iconLibraryTargetSettingKey.value || '');
				if (node && node.type === 'accordion' && ['expandIconClass', 'collapseIconClass'].includes(settingKey)) {
					if (!node.settings || typeof node.settings !== 'object') node.settings = {};
					node.settings[settingKey] = iconLibrarySelected.value.className;
					node.settings[settingKey.replace('Class', 'Source')] = 'library';
					closeIconLibrary();
					return;
				}
				if (!node || node.type !== 'icon') return;
				if (!node.settings || typeof node.settings !== 'object') node.settings = {};
				node.settings.iconStyle = iconLibrarySelected.value.style;
				node.settings.iconName = iconLibrarySelected.value.name;
				node.settings.iconClass = iconLibrarySelected.value.className;
				normalizeIconWidgetSettings(node.settings);
				closeIconLibrary();
			}
			function sanitizeAccordionSvgMarkup(value) {
				const source = String(value || '').trim();
				if (!source || typeof DOMParser !== 'function') return '';
				const doc = new DOMParser().parseFromString(source, 'image/svg+xml');
				const root = doc.documentElement;
				if (!root || root.nodeName.toLowerCase() !== 'svg' || doc.querySelector('parsererror')) return '';
				const allowed = new Set(['svg', 'g', 'path', 'circle', 'ellipse', 'rect', 'line', 'polyline', 'polygon', 'title', 'desc']);
				Array.from(root.querySelectorAll('*')).forEach((element) => {
					if (!allowed.has(element.nodeName.toLowerCase())) {
						element.remove();
						return;
					}
					Array.from(element.attributes).forEach((attribute) => {
						const name = attribute.name.toLowerCase();
						if (name.startsWith('on') || name === 'style' || name.includes('href')) element.removeAttribute(attribute.name);
					});
				});
				Array.from(root.attributes).forEach((attribute) => {
					const name = attribute.name.toLowerCase();
					if (name.startsWith('on') || name === 'style' || name.includes('href')) root.removeAttribute(attribute.name);
				});
				return new XMLSerializer().serializeToString(root);
			}
			function chooseAccordionSvg(role, node = selectedNode.value) {
				if (!node || node.type !== 'accordion' || !['expand', 'collapse'].includes(role)) return;
				const markup = window.prompt('Paste trusted SVG markup', String(node.settings?.[role + 'IconSvg'] || ''));
				if (markup === null) return;
				const sanitized = sanitizeAccordionSvgMarkup(markup);
				if (!sanitized) {
					showSaveToast('error', 'SVG tidak valid atau mengandung markup yang tidak didukung.');
					return;
				}
				node.settings[role + 'IconSvg'] = sanitized;
				node.settings[role + 'IconSource'] = 'svg';
			}
			function accordionStateKey(base, state = accordionStyleState.value) {
				const safeState = ['normal', 'hover', 'active'].includes(state) ? state : 'normal';
				return base + safeState.charAt(0).toUpperCase() + safeState.slice(1);
			}
			const selectedColumnContext = computed(() => {
				const nodeId = String(selectedColumnNodeId.value || selectedId.value || '').trim();
				const colId = String(selectedColumnId.value || '').trim();
				if (!nodeId || !colId) return null;
				const node = findById(rootNodes.value, nodeId);
				if (!node || !isCont(node.type) || !Array.isArray(node.columns)) return null;
				const index = node.columns.findIndex((col) => col && col.id === colId);
				if (index < 0) return null;
				const settings = node.settings || {};
				const displayType = settings.displayType || 'flex';
				const direction = getResponsiveSetting(settings, 'direction', settings.direction || 'row') || 'row';
				const flexWrap = getResponsiveSetting(settings, 'flexWrap', settings.flexWrap || 'nowrap') || 'nowrap';
				return {
					node,
					column: node.columns[index],
					index,
					total: node.columns.length,
					displayType,
					direction,
					flexWrap,
					canEditWidth: displayType === 'flex' && (direction === 'row' || direction === 'row-reverse'),
					canDragResize: displayType === 'flex' && direction === 'row' && flexWrap === 'nowrap' && node.columns.length > 1,
				};
			});

			// ── Column sync ───────────────────────────────────────────────────
			function syncCols(node, forceCount, device = 'desktop') {
				if (!node) return;
				const currentDevice = (device === 'tablet' || device === 'mobile') ? device : 'desktop';
				const isContNode = isCont(node.type);
				const isGridNode = isGrid(node.type);
				if (!isContNode && !isGridNode) return;
				const state = getResponsiveColumnsState(node);
				const s = node.settings || {};
				const colSetting = isContNode
					? getResponsiveSettingForDevice(s, 'gridColumns', currentDevice, s.gridColumns || 3)
					: getResponsiveSettingForDevice(s, 'columns', currentDevice, s.columns || 1);
				const baseCols = forceCount != null ? clamp(Number(forceCount), 1, 12) : clamp(Number(colSetting), 1, 12);
				let t = baseCols;
				if (isContNode && (s.displayType || 'flex') === 'grid') {
					const rowsValue = getResponsiveSettingForDevice(s, 'gridRows', currentDevice, s.gridRows || '1');
					const rows = containerGridRowsCount(rowsValue);
					t = Math.max(1, baseCols * rows);
				}
				if (!Array.isArray(node.columns)) node.columns = [];

				// Preserve each device layout snapshot so responsive column changes do not overwrite other devices.
				if (state && state.activeDevice && state.activeDevice !== currentDevice) {
					const sourceColumns = cloneColumnsState(node.columns);
					state.snapshots[state.activeDevice] = sourceColumns;
					const restored = resolveColumnsSnapshotForDevice(state, currentDevice);
					if (Array.isArray(restored) && restored.length) {
						node.columns = cloneColumnsState(restored);
						// Content is global across devices. Keep target snapshot in sync with latest source content.
						reconcileColumnsContent(sourceColumns, node.columns);
					}
				}

				while (node.columns.length < t) node.columns.push({id:uid('c'),children:[]});
				if (node.columns.length > t) {
					const last = node.columns[t-1];
					node.columns.slice(t).forEach(col => (col.children||[]).forEach(c => last.children.push(c)));
					node.columns = node.columns.slice(0, t);
				}

				if (state) {
					state.snapshots[currentDevice] = cloneColumnsState(node.columns);
					state.activeDevice = currentDevice;
				}
			}
			function activeResponsiveKey(base) {
				return responsiveKey(base, normalizeResponsiveDevice(responsiveDevice.value));
			}
			function getResponsiveSettingForDevice(settings, base, device = 'desktop', fallback = '') {
				if (!settings) return fallback;
				const safeDevice = (device === 'tablet' || device === 'mobile') ? device : 'desktop';
				const key = responsiveKey(base, safeDevice);
				const value = settings[key];
				if (safeDevice !== 'desktop' && (value === '' || value === null || value === undefined)) {
					const desktopValue = settings[base];
					return (desktopValue === '' || desktopValue === null || desktopValue === undefined) ? fallback : desktopValue;
				}
				if (value === '' || value === null || value === undefined) return fallback;
				return value;
			}
			function getResponsiveSetting(settings, base, fallback = '') {
				if (!settings) return fallback;
				return getResponsiveSettingForDevice(settings, base, normalizeResponsiveDevice(responsiveDevice.value), fallback);
			}
			function setResponsiveSetting(settings, base, value) {
				if (!settings) return;
				settings[activeResponsiveKey(base)] = value;
			}
			function containerResponsiveValue(settings, base, fallback = '') {
				return getResponsiveSetting(settings, base, fallback);
			}
			function setContainerResponsiveSetting(settings, base, value) {
				setResponsiveSetting(settings, base, value);
			}
			function syncResponsiveSides(settings, base, side, linked) {
				if (!linked) return;
				const sourceKey = activeResponsiveKey(base + side);
				const value = settings[sourceKey];
				['Top', 'Right', 'Bottom', 'Left'].forEach(targetSide => {
					if (targetSide !== side) settings[activeResponsiveKey(base + targetSide)] = value;
				});
			}
			function syncGridGap(settings, source) {
				if (!settings.gapLinked) return;
				if (source === 'columnGap') settings[activeResponsiveKey('rowGap')] = settings[activeResponsiveKey('columnGap')];
				if (source === 'rowGap') settings[activeResponsiveKey('columnGap')] = settings[activeResponsiveKey('rowGap')];
			}
			function parseNumberUnit(raw, fallbackUnit, allowedUnits) {
				const out = String(raw == null ? '' : raw).trim();
				if (!out || out.toLowerCase() === 'auto') {
					return { value: '', unit: fallbackUnit };
				}
				const match = out.match(/^(-?\d+(?:\.\d+)?)([a-z%]*)$/i);
				if (!match) {
					return { value: '', unit: fallbackUnit };
				}
				const value = Number(match[1]);
				const parsedUnit = (match[2] || fallbackUnit).toLowerCase();
				const unit = allowedUnits.includes(parsedUnit) ? parsedUnit : fallbackUnit;
				return { value, unit };
			}
			function toSizeToken(value, unit, emptyValue = 'auto') {
				const num = Number(value);
				if (!Number.isFinite(num) || num <= 0) return emptyValue;
				return String(num) + unit;
			}
			function roundColumnPercent(value) {
				return Math.round((Number(value) + Number.EPSILON) * 10) / 10;
			}
			function formatColumnPercent(value) {
				const safe = roundColumnPercent(value);
				return safe.toFixed(1).replace(/\.0$/, '') + '%';
			}
			function getFlexColumnPercentages(node) {
				if (!node || !Array.isArray(node.columns) || !node.columns.length) return [];
				const percents = Array.from({ length: node.columns.length }, () => 0);
				let specifiedTotal = 0;
				const fallbackIndexes = [];
				node.columns.forEach((col, index) => {
					const raw = String(col && col.flexBasis != null ? col.flexBasis : '').trim();
					const match = raw.match(/^(\d+(?:\.\d+)?)%$/);
					if (match) {
						const value = Math.max(0, Number(match[1]) || 0);
						percents[index] = value;
						specifiedTotal += value;
						return;
					}
					fallbackIndexes.push(index);
				});
				if (fallbackIndexes.length) {
					const remaining = Math.max(0, 100 - specifiedTotal);
					const share = fallbackIndexes.length ? remaining / fallbackIndexes.length : 0;
					fallbackIndexes.forEach((index) => {
						percents[index] = share;
					});
				}
				const total = percents.reduce((sum, value) => sum + value, 0);
				if (!(total > 0)) {
					const equal = 100 / node.columns.length;
					return percents.map(() => equal);
				}
				if (Math.abs(total - 100) > 0.05) {
					return percents.map((value) => (value / total) * 100);
				}
				return percents;
			}
			function companionColumnIndex(node, index) {
				if (!node || !Array.isArray(node.columns)) return -1;
				if (index < node.columns.length - 1) return index + 1;
				if (index > 0) return index - 1;
				return -1;
			}
			function resolveColumnPairMinPercent(node, index, pairIndex, pairTotal, fallbackPercent = null) {
				const fallback = Number.isFinite(Number(fallbackPercent))
					? Number(fallbackPercent)
					: Math.max(4, Math.min(14, pairTotal * 0.18));
				try {
					const currentEl = document.querySelector('.pb-grid-col[data-col-index="' + index + '"][data-parent-node-id="' + node.id + '"]');
					const pairEl = document.querySelector('.pb-grid-col[data-col-index="' + pairIndex + '"][data-parent-node-id="' + node.id + '"]');
					if (!currentEl || !pairEl) return fallback;
					const currentRect = currentEl.getBoundingClientRect();
					const pairRect = pairEl.getBoundingClientRect();
					const pairWidth = currentRect.width + pairRect.width;
					if (!(pairWidth > 0)) return fallback;
					const idealMinPx = Math.min(160, Math.max(110, pairWidth * 0.18));
					const maxAllowedMinPx = Math.max(48, (pairWidth / 2) - 24);
					const minPx = Math.max(48, Math.min(idealMinPx, maxAllowedMinPx));
					return Math.max(4, roundColumnPercent((minPx / pairWidth) * pairTotal));
				} catch (error) {
					return fallback;
				}
			}
			function applyColumnPairWidths(node, index, nextPercent, pairIndex = companionColumnIndex(node, index), options = {}) {
				if (!node || !Array.isArray(node.columns) || index < 0 || index >= node.columns.length) return;
				const safeIndex = Number(index);
				if (!Number.isFinite(safeIndex)) return;
				if (pairIndex < 0 || pairIndex >= node.columns.length || pairIndex === safeIndex) {
					node.columns[safeIndex].flexBasis = formatColumnPercent(clamp(Number(nextPercent) || 0, 1, 100));
					return;
				}
				const percents = getFlexColumnPercentages(node);
				const pairTotal = (percents[safeIndex] || 0) + (percents[pairIndex] || 0);
				const requestedMinPercent = Number(options.minPercent);
				const rawMinPercent = resolveColumnPairMinPercent(
					node,
					safeIndex,
					pairIndex,
					pairTotal,
					Number.isFinite(requestedMinPercent) ? requestedMinPercent : null
				);
				const minPercent = clamp(rawMinPercent, 4, Math.max(4, (pairTotal / 2) - 0.5));
				const safeCurrent = clamp(Number(nextPercent) || 0, minPercent, Math.max(minPercent, pairTotal - minPercent));
				const adjustedCurrent = roundColumnPercent(safeCurrent);
				node.columns[safeIndex].flexBasis = formatColumnPercent(adjustedCurrent);
				node.columns[pairIndex].flexBasis = formatColumnPercent(pairTotal - adjustedCurrent);
			}
			function columnWidthValue(ctx) {
				if (!ctx || !ctx.node || !ctx.column) return 0;
				const parsed = parseNumberUnit(ctx.column.flexBasis, '%', ['%']);
				if (parsed.value !== '') return parsed.value;
				const percents = getFlexColumnPercentages(ctx.node);
				return roundColumnPercent(percents[ctx.index] || 0);
			}
			function setSelectedColumnWidthValue(ctx, next) {
				if (!ctx || !ctx.node || !ctx.column || !ctx.canEditWidth) return;
				applyColumnPairWidths(ctx.node, ctx.index, Number(next) || 0);
			}
			function setColumnResizeOverlay(visible, text = '', x = 0, y = 0) {
				columnResizeOverlay.value = { visible, text, x, y };
			}
			function clearSelectedColumn() {
				selectedColumnNodeId.value = '';
				selectedColumnId.value = '';
			}
			function selectColumn(node, col) {
				if (!node || !col) return;
				selectedId.value = node.id;
				selectedColumnNodeId.value = node.id;
				selectedColumnId.value = col.id;
				settingsTab.value = 'layout';
			}
			function startColumnResize(event, node, col, index) {
				if (!event || !node || !col || !Array.isArray(node.columns)) return;
				const settings = node.settings || {};
				const displayType = settings.displayType || 'flex';
				const direction = getResponsiveSetting(settings, 'direction', settings.direction || 'row') || 'row';
				const flexWrap = getResponsiveSetting(settings, 'flexWrap', settings.flexWrap || 'nowrap') || 'nowrap';
				if (displayType !== 'flex' || direction !== 'row' || flexWrap !== 'nowrap') return;
				const pairIndex = companionColumnIndex(node, index);
				if (pairIndex < 0) return;
				const handleEl = event.currentTarget;
				const currentColEl = handleEl && typeof handleEl.closest === 'function' ? handleEl.closest('.pb-grid-col') : null;
				const nextColEl = currentColEl && currentColEl.nextElementSibling && currentColEl.nextElementSibling.classList.contains('pb-grid-col')
					? currentColEl.nextElementSibling
					: null;
				if (!currentColEl || !nextColEl) return;
				const currentRect = currentColEl.getBoundingClientRect();
				const nextRect = nextColEl.getBoundingClientRect();
				const pairWidth = currentRect.width + nextRect.width;
				if (!(pairWidth > 0)) return;
				const percents = getFlexColumnPercentages(node);
				const pairTotalPercent = (percents[index] || 0) + (percents[pairIndex] || 0);
				const startX = Number(event.clientX) || 0;
				const startCurrentWidth = currentRect.width;
				const idealMinPx = Math.min(160, Math.max(110, pairWidth * 0.18));
				const maxAllowedMinPx = Math.max(48, (pairWidth / 2) - 24);
				const minPx = Math.max(48, Math.min(idealMinPx, maxAllowedMinPx));
				const dragMinPercent = roundColumnPercent((minPx / pairWidth) * pairTotalPercent);

				selectColumn(node, col);
				suppressHistory.value = true;
				document.body.classList.add('pb-is-resizing-columns');
				setColumnResizeOverlay(true, formatColumnPercent(percents[index] || 0), startX + 18, currentRect.top + 24);

				let finished = false;
				const stop = () => {
					if (finished) return;
					finished = true;
					window.removeEventListener('mousemove', onMove);
					window.removeEventListener('mouseup', stop);
					window.removeEventListener('blur', stop);
					activeColumnResizeCleanup = null;
					suppressHistory.value = false;
					document.body.classList.remove('pb-is-resizing-columns');
					setColumnResizeOverlay(false);
					snap();
				};
				const onMove = (moveEvent) => {
					const deltaX = (Number(moveEvent.clientX) || 0) - startX;
					const nextCurrentWidth = clamp(startCurrentWidth + deltaX, minPx, pairWidth - minPx);
					const nextCurrentPercent = roundColumnPercent((nextCurrentWidth / pairWidth) * pairTotalPercent);
					applyColumnPairWidths(node, index, nextCurrentPercent, pairIndex, { minPercent: dragMinPercent });
					setColumnResizeOverlay(true, formatColumnPercent(columnWidthValue({ node, column: col, index })), (Number(moveEvent.clientX) || 0) + 18, currentRect.top + 24);
				};

				activeColumnResizeCleanup = stop;
				window.addEventListener('mousemove', onMove);
				window.addEventListener('mouseup', stop);
				window.addEventListener('blur', stop);
			}
			function containerWidthSource(node) {
				if (!node || !node.settings) return '';
				const s = node.settings;
				const boxedMode = s.contentWidth === 'boxed';
				return boxedMode ? getResponsiveSetting(s, 'maxWidth', '') : getResponsiveSetting(s, 'containerWidth', '');
			}
			const sizeControlUnits = ['px', 'pt', 'em', 'rem', '%'];
			const videoAspectRatioOptions = [
				{ value: '16/9', label: '16:9 (Widescreen)' },
				{ value: '4/3', label: '4:3 (Standard)' },
				{ value: '1/1', label: '1:1 (Square)' },
				{ value: '3/2', label: '3:2 (Photo)' },
				{ value: '21/9', label: '21:9 (Ultrawide)' },
				{ value: '9/16', label: '9:16 (Vertical)' },
				{ value: '4/5', label: '4:5 (Portrait)' },
			];
			const videoAspectRatioValues = new Set(videoAspectRatioOptions.map((option) => option.value));
			function normalizeVideoAspectRatio(value) {
				const ratio = String(value || '').trim();
				return videoAspectRatioValues.has(ratio) ? ratio : '16/9';
			}
			function videoAspectRatioValue(node) {
				if (!node || !node.settings) return '16/9';
				return normalizeVideoAspectRatio(getResponsiveSetting(node.settings, 'ratio', node.settings.ratio || '16/9'));
			}
			function setVideoAspectRatioValue(node, value) {
				if (!node || !node.settings) return;
				setResponsiveSetting(node.settings, 'ratio', normalizeVideoAspectRatio(value));
			}
			const videoSourceOptions = [
				{ value: 'youtube', label: 'YouTube' },
				{ value: 'vimeo', label: 'Vimeo' },
				{ value: 'dailymotion', label: 'Dailymotion' },
				{ value: 'self_hosted', label: 'Self Hosted' },
				{ value: 'videopress', label: 'VideoPress' },
			];
			const videoSuggestedVideoOptions = [
				{ value: 'current_channel', label: 'Current Video Channel' },
				{ value: 'any_video', label: 'Any Video' },
			];
			const videoPreloadOptions = [
				{ value: 'metadata', label: 'Metadata' },
				{ value: 'auto', label: 'Auto' },
				{ value: 'none', label: 'None' },
			];
			const videoToggleOptionsBySource = {
				youtube: [
					{ key: 'autoplay', label: 'Autoplay', state: 'on_off' },
					{ key: 'mute', label: 'Mute', state: 'on_off' },
					{ key: 'loop', label: 'Loop', state: 'on_off' },
					{ key: 'playerControls', label: 'Player Controls', state: 'show_hide' },
					{ key: 'captions', label: 'Captions', state: 'on_off' },
					{ key: 'privacyMode', label: 'Privacy Mode', state: 'on_off' },
					{ key: 'lazyLoad', label: 'Lazy Load', state: 'on_off' },
				],
				vimeo: [
					{ key: 'autoplay', label: 'Autoplay', state: 'on_off' },
					{ key: 'mute', label: 'Mute', state: 'on_off' },
					{ key: 'loop', label: 'Loop', state: 'on_off' },
					{ key: 'privacyMode', label: 'Privacy Mode', state: 'on_off' },
					{ key: 'introTitle', label: 'Intro Title', state: 'show_hide' },
					{ key: 'introPortrait', label: 'Intro Portrait', state: 'show_hide' },
					{ key: 'introByline', label: 'Intro Byline', state: 'show_hide' },
				],
				dailymotion: [
					{ key: 'autoplay', label: 'Autoplay', state: 'on_off' },
					{ key: 'mute', label: 'Mute', state: 'on_off' },
					{ key: 'playerControls', label: 'Player Controls', state: 'show_hide' },
					{ key: 'videoInfo', label: 'Video Info', state: 'show_hide' },
					{ key: 'logo', label: 'Logo', state: 'show_hide' },
				],
				self_hosted: [
					{ key: 'autoplay', label: 'Autoplay', state: 'on_off' },
					{ key: 'mute', label: 'Mute', state: 'on_off' },
					{ key: 'loop', label: 'Loop', state: 'on_off' },
					{ key: 'playerControls', label: 'Player Controls', state: 'show_hide' },
					{ key: 'downloadButton', label: 'Download Button', state: 'show_hide' },
				],
				videopress: [
					{ key: 'autoplay', label: 'Autoplay', state: 'on_off' },
					{ key: 'mute', label: 'Mute', state: 'on_off' },
					{ key: 'loop', label: 'Loop', state: 'on_off' },
					{ key: 'playerControls', label: 'Player Controls', state: 'show_hide' },
				],
			};
			function normalizeVideoNodeSettings(settings) {
				if (!settings || typeof settings !== 'object') return;
				const defaults = videoDefaults();
				Object.keys(defaults).forEach((key) => {
					if (!Object.prototype.hasOwnProperty.call(settings, key)) {
						settings[key] = cloneSettingValue(defaults[key]);
					}
				});
				settings.sourceType = normalizeVideoSourceType(settings.sourceType);
				settings.youtubeEmbed = toEmbed(settings.youtubeUrl || '');
				settings.ratio = normalizeVideoAspectRatio(settings.ratio);
				if (settings.ratioTablet !== '' && settings.ratioTablet !== null && settings.ratioTablet !== undefined) {
					settings.ratioTablet = normalizeVideoAspectRatio(settings.ratioTablet);
				}
				if (settings.ratioMobile !== '' && settings.ratioMobile !== null && settings.ratioMobile !== undefined) {
					settings.ratioMobile = normalizeVideoAspectRatio(settings.ratioMobile);
				}
				settings.externalUrl = !!settings.externalUrl;
				settings.startTime = toPositiveInteger(settings.startTime);
				settings.endTime = toPositiveInteger(settings.endTime);
				settings.autoplay = !!settings.autoplay;
				settings.mute = !!settings.mute;
				settings.loop = !!settings.loop;
				settings.playerControls = settings.playerControls !== false;
				settings.captions = !!settings.captions;
				settings.privacyMode = !!settings.privacyMode;
				settings.lazyLoad = !!settings.lazyLoad;
				settings.suggestedVideos = settings.suggestedVideos === 'any_video' ? 'any_video' : 'current_channel';
				settings.introTitle = settings.introTitle !== false;
				settings.introPortrait = settings.introPortrait !== false;
				settings.introByline = settings.introByline !== false;
				settings.controlsColor = String(settings.controlsColor || '').trim();
				settings.videoInfo = settings.videoInfo !== false;
				settings.logo = settings.logo !== false;
				settings.downloadButton = settings.downloadButton !== false;
				settings.preload = ['metadata', 'auto', 'none'].includes(String(settings.preload || '').toLowerCase())
					? String(settings.preload).toLowerCase()
					: 'metadata';
				settings.poster = String(settings.poster || '').trim();
				settings.imageOverlay = !!settings.imageOverlay;
				settings.overlayImage = String(settings.overlayImage || '').trim();
				settings.cssClass = String(settings.cssClass || '').trim();
			}
			function videoCurrentSource(node) {
				return normalizeVideoSourceType(node?.settings?.sourceType);
			}
			function setVideoSourceType(node, value) {
				if (!node || !node.settings) return;
				node.settings.sourceType = normalizeVideoSourceType(value);
				normalizeVideoNodeSettings(node.settings);
			}
			function videoLinkField(node) {
				const source = videoCurrentSource(node);
				if (source === 'youtube') return { key: 'youtubeUrl', label: 'Link', placeholder: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' };
				if (source === 'vimeo') return { key: 'vimeoUrl', label: 'Link', placeholder: 'https://vimeo.com/235215203' };
				if (source === 'dailymotion') return { key: 'dailymotionUrl', label: 'Link', placeholder: 'https://www.dailymotion.com/video/x84sh87' };
				if (isHostedVideoSourceType(source) && node?.settings?.externalUrl) {
					return { key: 'fileUrl', label: 'External URL', placeholder: 'https://example.com/video.mp4' };
				}
				return null;
			}
			function videoUsesHostedPicker(node) {
				return isHostedVideoSourceType(videoCurrentSource(node));
			}
			function videoShowsEndTime(node) {
				const source = videoCurrentSource(node);
				return source === 'youtube' || source === 'self_hosted';
			}
			function videoShowsPoster(node) {
				return videoCurrentSource(node) === 'self_hosted';
			}
			function videoShowsOverlay(node) {
				const source = videoCurrentSource(node);
				return source === 'youtube' || source === 'vimeo' || source === 'dailymotion' || source === 'self_hosted' || source === 'videopress';
			}
			function videoUsesControlsColor(node) {
				const source = videoCurrentSource(node);
				return source === 'vimeo' || source === 'dailymotion';
			}
			function videoToggleOptions(node) {
				return videoToggleOptionsBySource[videoCurrentSource(node)] || videoToggleOptionsBySource.youtube;
			}
			function videoSelectOptions(node) {
				const source = videoCurrentSource(node);
				if (source === 'youtube') {
					return [
						{ key: 'suggestedVideos', label: 'Suggested Videos', options: videoSuggestedVideoOptions },
					];
				}
				if (source === 'self_hosted') {
					return [
						{ key: 'preload', label: 'Preload', options: videoPreloadOptions },
					];
				}
				return [];
			}
			function videoToggleStateLabel(option, value) {
				if (option?.state === 'show_hide') return value ? 'Show' : 'Hide';
				return value ? 'On' : 'Off';
			}
			const spacingControlUnits = ['px', '%', 'em', 'rem', 'vw'];
			const sizeControlLegacyUnits = ['px', 'pt', 'em', 'rem', '%', 'vw'];
			const spacingSides = ['Top', 'Right', 'Bottom', 'Left'];
			function normalizeSpacingUnit(unit) {
				const safe = String(unit || '').trim().toLowerCase();
				return spacingControlUnits.includes(safe) ? safe : 'px';
			}
			function spacingRawUnit(node, base) {
				if (!node || !node.settings) return '';
				return String(getResponsiveSetting(node.settings, base + 'Unit', '') || '').trim().toLowerCase();
			}
			function spacingUnit(node, base) {
				if (!node || !node.settings) return 'px';
				const rawUnit = spacingRawUnit(node, base);
				if (rawUnit !== '') return normalizeSpacingUnit(rawUnit);
				for (const side of spacingSides) {
					const parsed = parseNumberUnit(getResponsiveSetting(node.settings, base + side, ''), 'px', spacingControlUnits);
					if (parsed.value !== '') return normalizeSpacingUnit(parsed.unit);
				}
				return 'px';
			}
			function spacingToken(value, unit) {
				const raw = String(value ?? '').trim();
				if (raw === '') return '';
				const num = Number(raw);
				if (!Number.isFinite(num)) return '';
				return String(num) + normalizeSpacingUnit(unit);
			}
			function spacingSideValue(node, base, side) {
				if (!node || !node.settings) return '';
				const parsed = parseNumberUnit(getResponsiveSetting(node.settings, base + side, ''), spacingUnit(node, base), spacingControlUnits);
				return parsed.value === '' ? '' : parsed.value;
			}
			function setSpacingSideValue(node, base, side, value) {
				if (!node || !node.settings) return;
				const token = spacingToken(value, spacingUnit(node, base));
				setResponsiveSetting(node.settings, base + side, token);
				if (node.settings[base + 'Linked']) {
					spacingSides.forEach((targetSide) => {
						if (targetSide !== side) setResponsiveSetting(node.settings, base + targetSide, token);
					});
				}
			}
			function onSpacingSideInput(node, base, side, event) {
				if (!event || !event.target) return;
				setSpacingSideValue(node, base, side, event.target.value);
				nextTick(() => {
					event.target.value = String(spacingSideValue(node, base, side));
				});
			}
			function setSpacingUnit(node, base, unit) {
				if (!node || !node.settings) return;
				const safe = normalizeSpacingUnit(unit);
				const currentValues = {};
				spacingSides.forEach((side) => {
					currentValues[side] = spacingSideValue(node, base, side);
				});
				setResponsiveSetting(node.settings, base + 'Unit', safe);
				spacingSides.forEach((side) => {
					if (currentValues[side] !== '') {
						setResponsiveSetting(node.settings, base + side, spacingToken(currentValues[side], safe));
					}
				});
			}
			function sizeControlMaxForUnit(unit) {
				if (unit === '%' || unit === 'vw') return 100;
				if (unit === 'em' || unit === 'rem') return 30;
				if (unit === 'pt') return 720;
				return 1600;
			}
			function sizeControlStepForUnit(unit) {
				return unit === 'em' || unit === 'rem' ? 0.1 : 1;
			}
			function sizeControlSource(node, base, fallback = '') {
				if (!node || !node.settings) return fallback;
				return getResponsiveSetting(node.settings, base, node.settings[base] || fallback);
			}
			function sizeControlParsed(node, base, fallback = '', fallbackUnit = 'px', allowedUnits = sizeControlUnits) {
				return parseNumberUnit(sizeControlSource(node, base, fallback), fallbackUnit, allowedUnits);
			}
			function normalizedSizeControlValue(value, unit, emptyValue = '') {
				if (value === '' || value === null || value === undefined) return emptyValue;
				const num = Number(value);
				if (!Number.isFinite(num)) return emptyValue;
				return clamp(num, 0, sizeControlMaxForUnit(unit));
			}
			function sizeControlDisplayValue(node, base, fallback = '', options = {}) {
				const parsed = sizeControlParsed(
					node,
					base,
					fallback,
					options.fallbackUnit || 'px',
					options.allowedUnits || sizeControlUnits
				);
				if (parsed.value === '') return '';
				return normalizedSizeControlValue(parsed.value, parsed.unit, '');
			}
			function sizeControlUnit(node, base, fallback = '', options = {}) {
				const fallbackUnit = options.fallbackUnit || 'px';
				const parsed = sizeControlParsed(
					node,
					base,
					fallback,
					fallbackUnit,
					options.allowedUnits || sizeControlUnits
				);
				return sizeControlUnits.includes(parsed.unit) ? parsed.unit : fallbackUnit;
			}
			function sizeControlMax(node, base, fallback = '', options = {}) {
				return sizeControlMaxForUnit(sizeControlUnit(node, base, fallback, options));
			}
			function sizeControlStep(node, base, fallback = '', options = {}) {
				return sizeControlStepForUnit(sizeControlUnit(node, base, fallback, options));
			}
			function sizeControlToken(value, unit, emptyToken = 'auto') {
				if (value === '' || value === null || value === undefined) return emptyToken;
				const num = Number(value);
				if (!Number.isFinite(num)) return emptyToken;
				if (num <= 0) return '0' + unit;
				return String(num) + unit;
			}
			function setSizeControlValue(node, base, next, options = {}) {
				if (!node || !node.settings) return;
				const allowEmpty = !!options.allowEmpty;
				const emptyToken = options.emptyToken || 'auto';
				const raw = String(next ?? '').trim();
				if (allowEmpty && raw === '') {
					setResponsiveSetting(node.settings, base, emptyToken);
					return;
				}
				const unit = sizeControlUnit(node, base, options.fallback || '', options);
				const value = normalizedSizeControlValue(raw, unit, allowEmpty ? '' : 0);
				if (allowEmpty && value === '') {
					setResponsiveSetting(node.settings, base, emptyToken);
					return;
				}
				setResponsiveSetting(node.settings, base, sizeControlToken(value, unit, emptyToken));
			}
			function onSizeControlInput(node, base, event, options = {}) {
				if (!event || !event.target) return;
				setSizeControlValue(node, base, event.target.value, options);
				nextTick(() => {
					event.target.value = String(sizeControlDisplayValue(node, base, options.fallback || '', options));
				});
			}
			function setSizeControlUnit(node, base, unit, options = {}) {
				if (!node || !node.settings) return;
				const safe = sizeControlUnits.includes(unit) ? unit : (options.fallbackUnit || 'px');
				const current = sizeControlDisplayValue(node, base, options.fallback || '', options);
				if (current === '' && options.allowEmpty) {
					setResponsiveSetting(node.settings, base, options.emptyToken || 'auto');
					return;
				}
				const value = normalizedSizeControlValue(current, safe, options.allowEmpty ? '' : 0);
				setResponsiveSetting(node.settings, base, sizeControlToken(value, safe, options.emptyToken || 'auto'));
			}
			function accordionResponsiveSource(node, key, fallback = '') {
				const settings = node?.settings;
				if (!settings) return fallback;
				let source = settings[key];
				if ((source === '' || source == null) && /(?:Tablet|Mobile)$/.test(key)) {
					source = settings[key.replace(/(?:Tablet|Mobile)$/, '')];
				}
				return source === '' || source == null ? fallback : source;
			}
			function accordionDimensionParsed(node, key, fallback = '0px') {
				const source = accordionResponsiveSource(node, key, fallback);
				const fallbackUnit = parseNumberUnit(fallback, 'px', sizeControlUnits).unit || 'px';
				return parseNumberUnit(source, fallbackUnit, sizeControlUnits);
			}
			function accordionDimensionUnit(node, key, fallback = '0px') {
				const parsed = accordionDimensionParsed(node, key, fallback);
				return sizeControlUnits.includes(parsed.unit) ? parsed.unit : 'px';
			}
			function accordionDimensionValue(node, key, fallback = '0px') {
				const parsed = accordionDimensionParsed(node, key, fallback);
				return parsed.value === '' ? 0 : normalizedSizeControlValue(parsed.value, parsed.unit, 0);
			}
			function accordionDimensionMax(node, key, fallback = '0px') {
				return sizeControlMaxForUnit(accordionDimensionUnit(node, key, fallback));
			}
			function accordionDimensionStep(node, key, fallback = '0px') {
				return sizeControlStepForUnit(accordionDimensionUnit(node, key, fallback));
			}
			function onAccordionDimensionInput(node, key, event, fallback = '0px') {
				if (!node?.settings || !event?.target) return;
				const unit = accordionDimensionUnit(node, key, fallback);
				const value = normalizedSizeControlValue(event.target.value, unit, 0);
				node.settings[key] = sizeControlToken(value, unit, '0' + unit);
			}
			function setAccordionDimensionUnit(node, key, unit, fallback = '0px') {
				if (!node?.settings) return;
				const safeUnit = sizeControlUnits.includes(unit) ? unit : 'px';
				const value = normalizedSizeControlValue(accordionDimensionValue(node, key, fallback), safeUnit, 0);
				node.settings[key] = sizeControlToken(value, safeUnit, '0' + safeUnit);
			}
			function accordionBoxTokens(node, key, fallback = '0px') {
				const source = String(accordionResponsiveSource(node, key, fallback)).trim() || fallback;
				const raw = source.split(/\s+/).slice(0, 4);
				const expanded = raw.length === 1
					? [raw[0], raw[0], raw[0], raw[0]]
					: (raw.length === 2
						? [raw[0], raw[1], raw[0], raw[1]]
						: (raw.length === 3 ? [raw[0], raw[1], raw[2], raw[1]] : raw));
				return expanded.map((token) => parseNumberUnit(token, 'px', sizeControlUnits));
			}
			function accordionBoxUnit(node, key, fallback = '0px') {
				const unit = accordionBoxTokens(node, key, fallback)[0]?.unit;
				return sizeControlUnits.includes(unit) ? unit : 'px';
			}
			function accordionBoxSideValue(node, key, sideIndex, fallback = '0px') {
				const parsed = accordionBoxTokens(node, key, fallback)[sideIndex] || { value: 0, unit: 'px' };
				return parsed.value === '' ? 0 : normalizedSizeControlValue(parsed.value, parsed.unit, 0);
			}
			function accordionBoxLinked(key) {
				return accordionBoxLinks.value[key] !== false;
			}
			function toggleAccordionBoxLink(key) {
				accordionBoxLinks.value = { ...accordionBoxLinks.value, [key]: !accordionBoxLinked(key) };
			}
			function writeAccordionBox(node, key, values, unit) {
				if (!node?.settings) return;
				node.settings[key] = values.map((value) => sizeControlToken(value, unit, '0' + unit)).join(' ');
			}
			function onAccordionBoxSideInput(node, key, sideIndex, event, fallback = '0px') {
				if (!node?.settings || !event?.target) return;
				const unit = accordionBoxUnit(node, key, fallback);
				const value = normalizedSizeControlValue(event.target.value, unit, 0);
				const values = [0, 1, 2, 3].map((index) => accordionBoxSideValue(node, key, index, fallback));
				if (accordionBoxLinked(key)) values.fill(value);
				else values[sideIndex] = value;
				writeAccordionBox(node, key, values, unit);
			}
			function setAccordionBoxUnit(node, key, unit, fallback = '0px') {
				const safeUnit = sizeControlUnits.includes(unit) ? unit : 'px';
				const values = [0, 1, 2, 3].map((index) => accordionBoxSideValue(node, key, index, fallback));
				writeAccordionBox(node, key, values, safeUnit);
			}
			const containerWidthUnits = sizeControlLegacyUnits;
			function containerWidthMaxForUnit(unit) {
				return sizeControlMaxForUnit(unit);
			}
			function containerWidthValue(node) {
				const parsed = parseNumberUnit(containerWidthSource(node), '%', containerWidthUnits);
				if (parsed.value === '') return 100;
				return normalizedContainerWidthValue(parsed.value, parsed.unit);
			}
			function containerWidthUnit(node) {
				const parsed = parseNumberUnit(containerWidthSource(node), '%', containerWidthUnits);
				return sizeControlUnits.includes(parsed.unit) ? parsed.unit : '%';
			}
			function containerWidthMax(node) {
				return containerWidthMaxForUnit(containerWidthUnit(node));
			}
			function containerWidthStep(node) {
				return sizeControlStepForUnit(containerWidthUnit(node));
			}
			function normalizedContainerWidthValue(value, unit) {
				const num = Number(value);
				if (!Number.isFinite(num)) return '';
				return clamp(num, 0, containerWidthMaxForUnit(unit));
			}
			function containerWidthToken(value, unit, emptyValue) {
				return toSizeToken(normalizedContainerWidthValue(value, unit), unit, emptyValue);
			}
			function setContainerWidthValue(node, next) {
				if (!node || !node.settings) return;
				const s = node.settings;
				const unit = containerWidthUnit(node);
				const token = containerWidthToken(next, unit, s.contentWidth === 'boxed' ? 'auto' : '100%');
				if (s.contentWidth === 'boxed') setResponsiveSetting(s, 'maxWidth', token);
				else setResponsiveSetting(s, 'containerWidth', token);
			}
			function onContainerWidthInput(node, event) {
				if (!event || !event.target) return;
				setContainerWidthValue(node, event.target.value);
				nextTick(() => {
					event.target.value = String(containerWidthValue(node));
				});
			}
			function setContainerWidthUnit(node, unit) {
				if (!node || !node.settings) return;
				const safe = containerWidthUnits.includes(unit) ? unit : '%';
				const value = normalizedContainerWidthValue(containerWidthValue(node), safe);
				if (node.settings.contentWidth === 'boxed') {
					setResponsiveSetting(node.settings, 'maxWidth', containerWidthToken(value, safe, 'auto'));
				} else {
					setResponsiveSetting(node.settings, 'containerWidth', containerWidthToken(value, safe, '100%'));
				}
			}
			function onContainerContentWidthChange(node) {
				if (!node || !node.settings) return;
				const s = node.settings;
				if (s.contentWidth === 'boxed') {
					const maxWidth = getResponsiveSetting(s, 'maxWidth', '');
					if (!maxWidth || String(maxWidth).trim() === '') {
						setResponsiveSetting(s, 'maxWidth', getResponsiveSetting(s, 'containerWidth', '100%') || '100%');
					}
					return;
				}
				const containerWidth = getResponsiveSetting(s, 'containerWidth', '');
				if (!containerWidth || String(containerWidth).trim() === '' || containerWidth === 'auto') {
					const maxWidth = getResponsiveSetting(s, 'maxWidth', '');
					setResponsiveSetting(s, 'containerWidth', (maxWidth && maxWidth !== 'auto') ? maxWidth : '100%');
				}
			}
			const shapeDividerTypeOptions = [
				{ value: 'none', label: 'None' },
				{ value: 'mountains', label: 'Mountains' },
				{ value: 'drops', label: 'Drops' },
				{ value: 'clouds', label: 'Clouds' },
				{ value: 'zigzag', label: 'Zigzag' },
				{ value: 'pyramids', label: 'Pyramids' },
				{ value: 'triangle', label: 'Triangle' },
				{ value: 'triangle-asymmetrical', label: 'Triangle Asymmetrical' },
				{ value: 'tilt', label: 'Tilt' },
				{ value: 'opacity-tilt', label: 'Tilt Opacity' },
				{ value: 'opacity-fan', label: 'Fan Opacity' },
				{ value: 'curve', label: 'Curve' },
				{ value: 'curve-asymmetrical', label: 'Curve Asymmetrical' },
				{ value: 'waves', label: 'Waves' },
				{ value: 'wave-brush', label: 'Waves Brush' },
				{ value: 'waves-pattern', label: 'Waves Pattern' },
				{ value: 'arrow', label: 'Arrow' },
				{ value: 'split', label: 'Split' },
				{ value: 'book', label: 'Book' },
			];
			const shapeDividerWidthTypes = new Set(['mountains', 'zigzag', 'pyramids', 'triangle', 'triangle-asymmetrical', 'opacity-tilt', 'opacity-fan', 'curve', 'curve-asymmetrical', 'waves', 'wave-brush', 'waves-pattern', 'arrow', 'split', 'book']);
			const shapeDividerFlipTypes = new Set(['mountains', 'drops', 'clouds', 'pyramids', 'triangle-asymmetrical', 'tilt', 'opacity-tilt', 'curve-asymmetrical', 'waves', 'wave-brush', 'waves-pattern']);
			const shapeDividerInvertTypes = new Set(['drops', 'clouds', 'pyramids', 'triangle', 'triangle-asymmetrical', 'curve', 'curve-asymmetrical', 'waves', 'arrow', 'split', 'book']);
			function normalizeShapeDividerType(type) {
				const raw = String(type || 'none').trim();
				if (!raw || raw === 'none') return 'none';
				if (raw === 'tilt-opacity') return 'opacity-tilt';
				if (raw === 'fan-opacity') return 'opacity-fan';
				if (raw === 'waves-brush') return 'wave-brush';
				return raw;
			}
			function shapeDividerPrefix(node) {
				const side = node?.settings?.shapeDividerSide === 'bottom' ? 'Bottom' : 'Top';
				return 'shapeDivider' + side;
			}
			function shapeDividerSetting(node, suffix, fallback = '') {
				if (!node || !node.settings) return fallback;
				const key = shapeDividerPrefix(node) + suffix;
				const value = node.settings[key];
				return value === '' || value === null || value === undefined ? fallback : value;
			}
			function setShapeDividerSetting(node, suffix, value) {
				if (!node || !node.settings) return;
				node.settings[shapeDividerPrefix(node) + suffix] = value;
			}
			function shapeDividerActiveType(node) {
				return normalizeShapeDividerType(shapeDividerSetting(node, 'Type', 'none'));
			}
			function shapeDividerHasWidth(node) {
				return shapeDividerWidthTypes.has(shapeDividerActiveType(node));
			}
			function shapeDividerHasFlip(node) {
				return shapeDividerFlipTypes.has(shapeDividerActiveType(node));
			}
			function shapeDividerHasInvert(node) {
				return shapeDividerInvertTypes.has(shapeDividerActiveType(node));
			}
			function shapeDividerWidthValue(node) {
				const parsed = parseNumberUnit(shapeDividerSetting(node, 'Width', '100%'), '%', ['%']);
				return parsed.value === '' ? 100 : clamp(Number(parsed.value) || 0, 0, 300);
			}
			function shapeDividerWidthUnit() {
				return '%';
			}
			function setShapeDividerWidthValue(node, next) {
				const value = clamp(Number(next) || 0, 0, 300);
				setShapeDividerSetting(node, 'Width', value + '%');
			}
			function setShapeDividerWidthUnit(node) {
				setShapeDividerWidthValue(node, shapeDividerWidthValue(node));
			}
			function shapeDividerHeightValue(node) {
				const parsed = parseNumberUnit(shapeDividerSetting(node, 'Height', '60px'), 'px', ['px']);
				return parsed.value === '' ? 60 : clamp(Number(parsed.value) || 0, 0, 500);
			}
			function shapeDividerHeightUnit() {
				return 'px';
			}
			function setShapeDividerHeightValue(node, next) {
				const value = clamp(Number(next) || 0, 0, 500);
				setShapeDividerSetting(node, 'Height', value + 'px');
			}
			function setShapeDividerHeightUnit(node) {
				setShapeDividerHeightValue(node, shapeDividerHeightValue(node));
			}
			function minHeightValue(node) {
				if (!node || !node.settings) return '';
				const parsed = parseNumberUnit(getResponsiveSetting(node.settings, 'minHeight', ''), 'px', ['px', 'vh']);
				return parsed.value;
			}
			function minHeightUnit(node) {
				if (!node || !node.settings) return 'px';
				const parsed = parseNumberUnit(getResponsiveSetting(node.settings, 'minHeight', ''), 'px', ['px', 'vh']);
				return parsed.unit;
			}
			function setMinHeightValue(node, next) {
				if (!node || !node.settings) return;
				const unit = minHeightUnit(node);
				setResponsiveSetting(node.settings, 'minHeight', toSizeToken(next, unit, 'auto'));
			}
			function setMinHeightUnit(node, unit) {
				if (!node || !node.settings) return;
				const safe = ['px', 'vh'].includes(unit) ? unit : 'px';
				const value = minHeightValue(node);
				setResponsiveSetting(node.settings, 'minHeight', toSizeToken(value, safe, 'auto'));
			}
			const spacerHeightUnits = sizeControlUnits;
			function spacerHeightMaxForUnit(unit) {
				return sizeControlMaxForUnit(unit);
			}
			function spacerHeightSource(node) {
				if (!node || !node.settings) return '32px';
				return getResponsiveSetting(node.settings, 'height', node.settings.height || '32px');
			}
			function normalizedSpacerHeightValue(value, unit) {
				const num = Number(value);
				if (!Number.isFinite(num)) return 32;
				return clamp(num, 0, spacerHeightMaxForUnit(unit));
			}
			function spacerHeightValue(node) {
				const parsed = parseNumberUnit(spacerHeightSource(node), 'px', spacerHeightUnits);
				if (parsed.value === '') return 32;
				return normalizedSpacerHeightValue(parsed.value, parsed.unit);
			}
			function spacerHeightUnit(node) {
				const parsed = parseNumberUnit(spacerHeightSource(node), 'px', spacerHeightUnits);
				return parsed.unit;
			}
			function spacerHeightMax(node) {
				return spacerHeightMaxForUnit(spacerHeightUnit(node));
			}
			function spacerHeightStep(node) {
				return sizeControlStepForUnit(spacerHeightUnit(node));
			}
			function spacerHeightToken(value, unit) {
				const num = Number(value);
				if (!Number.isFinite(num)) return '32px';
				if (num <= 0) return '0' + unit;
				return String(num) + unit;
			}
			function setSpacerHeightValue(node, next) {
				if (!node || !node.settings) return;
				const unit = spacerHeightUnit(node);
				const value = normalizedSpacerHeightValue(next, unit);
				setResponsiveSetting(node.settings, 'height', spacerHeightToken(value, unit));
			}
			function onSpacerHeightInput(node, event) {
				if (!event || !event.target) return;
				setSpacerHeightValue(node, event.target.value);
				nextTick(() => {
					event.target.value = String(spacerHeightValue(node));
				});
			}
			function setSpacerHeightUnit(node, unit) {
				if (!node || !node.settings) return;
				const safe = spacerHeightUnits.includes(unit) ? unit : 'px';
				const value = normalizedSpacerHeightValue(spacerHeightValue(node), safe);
				setResponsiveSetting(node.settings, 'height', spacerHeightToken(value, safe));
			}
			function containerGridColumnsValue(node) {
				if (!node || !node.settings) return 1;
				const current = containerResponsiveValue(node.settings, 'gridColumns', node.settings.gridColumns || 3);
				return clamp(Number(current) || Number(node.settings.gridColumns) || 3, 1, 12);
			}
			function setContainerGridColumnsValue(node, next) {
				if (!node || !node.settings) return;
				const device = normalizeResponsiveDevice(responsiveDevice.value);
				const value = clamp(Number(next) || 1, 1, 12);
				node.settings[responsiveKey('gridColumns', device)] = value;
				syncCols(node, null, device);
			}
			function containerGridRowsValue(node) {
				if (!node || !node.settings) return 1;
				const current = containerResponsiveValue(node.settings, 'gridRows', node.settings.gridRows || '1');
				return containerGridRowsCount(current);
			}
			function setContainerGridRowsValue(node, next) {
				if (!node || !node.settings) return;
				const value = clamp(Number(next) || 1, 1, 12);
				setContainerResponsiveSetting(node.settings, 'gridRows', String(value));
				syncCols(node, null, responsiveDevice.value);
			}
			function syncContainerGap(settings, source) {
				if (!settings || !settings.containerGapLinked) return;
				if (source === 'column') setResponsiveSetting(settings, 'flexRowGap', getResponsiveSetting(settings, 'flexColumnGap', settings.flexColumnGap || ''));
				if (source === 'row') setResponsiveSetting(settings, 'flexColumnGap', getResponsiveSetting(settings, 'flexRowGap', settings.flexRowGap || ''));
				if (source === 'gridColumn') setResponsiveSetting(settings, 'gridRowGap', getResponsiveSetting(settings, 'gridColumnGap', settings.gridColumnGap || ''));
				if (source === 'gridRow') setResponsiveSetting(settings, 'gridColumnGap', getResponsiveSetting(settings, 'gridRowGap', settings.gridRowGap || ''));
			}
			function bgStateSuffix(node) {
				const state = String(node?.settings?.bgState || 'normal').toLowerCase();
				return state === 'hover' ? 'Hover' : '';
			}
			function bgStateKey(node, base) {
				return base + bgStateSuffix(node);
			}
			function initContainerHoverStyleState(node) {
				if (!node || !node.settings) return;
				const settings = node.settings;
				if (!settings.borderHoverInitialized) {
					settings.borderTypeHover = String(settings.borderType || 'none');
					settings.borderWidthHover = String(settings.borderWidth ?? '1');
					settings.borderColorHover = String(settings.borderColor || '#000000');
					settings.borderHoverInitialized = true;
				}
				if (!settings.shadowHoverInitialized) {
					settings.shadowEnabledHover = !!settings.shadowEnabled;
					settings.shadowHHover = String(settings.shadowH ?? '0');
					settings.shadowVHover = String(settings.shadowV ?? '0');
					settings.shadowBlurHover = String(settings.shadowBlur ?? '0');
					settings.shadowSpreadHover = String(settings.shadowSpread ?? '0');
					settings.shadowColorHover = String(settings.shadowColor || '#000000');
					settings.shadowOpacityHover = settings.shadowOpacity == null ? 0.3 : settings.shadowOpacity;
					settings.shadowHoverInitialized = true;
				}
			}
			function setBgState(node, state) {
				if (!node || !node.settings) return;
				node.settings.bgState = String(state || 'normal').toLowerCase() === 'hover' ? 'hover' : 'normal';
				if (node.settings.bgState === 'hover') {
					initContainerHoverStyleState(node);
				}
			}
			function isBgHoverState(node) {
				return bgStateSuffix(node) === 'Hover';
			}
			function setBgTypeForState(node, type) {
				if (!node || !node.settings) return;
				const key = bgStateKey(node, 'bgType');
				const current = String(node.settings[key] || 'none');
				const next = String(type || 'none');
				if (isBgHoverState(node) && current === next) {
					node.settings[key] = 'none';
					return;
				}
				node.settings[key] = next;
			}
			function setBgOverlayTypeForState(node, type) {
				if (!node || !node.settings) return;
				node.settings[bgStateKey(node, 'bgOverlayType')] = type;
			}
			function firstGapValue(...candidates) {
				for (const candidate of candidates) {
					const v = String(candidate == null ? '' : candidate).trim();
					if (v) return v;
				}
				return '';
			}
			function onContainerDisplayTypeChange(node) {
				if (!node || !isCont(node.type) || !node.settings) return;
				const s = node.settings;
				const dt = s.displayType || 'flex';

				if (dt === 'flex') {
					const nextRow = firstGapValue(
						getResponsiveSetting(s, 'gridRowGap', ''),
						s.rowGap,
						getResponsiveSetting(s, 'gridColumnGap', ''),
						s.columnGap,
						s.gap,
						'0'
					);
					const nextCol = firstGapValue(
						getResponsiveSetting(s, 'gridColumnGap', ''),
						s.columnGap,
						getResponsiveSetting(s, 'gridRowGap', ''),
						s.rowGap,
						s.gap,
						nextRow,
						'0'
					);

					// Selalu sinkron saat switch mode agar user melihat value yang konsisten.
					setResponsiveSetting(s, 'flexRowGap', nextRow);
					setResponsiveSetting(s, 'flexColumnGap', nextCol);
					s.rowGap = nextRow;
					s.columnGap = nextCol;
					s.gap = nextRow === nextCol ? nextCol : '0';
					return;
				}

				if (dt === 'grid') {
					const nextRow = firstGapValue(
						getResponsiveSetting(s, 'flexRowGap', ''),
						s.rowGap,
						s.gap,
						getResponsiveSetting(s, 'flexColumnGap', ''),
						'10px'
					);
					const nextCol = firstGapValue(
						getResponsiveSetting(s, 'flexColumnGap', ''),
						s.columnGap,
						s.gap,
						getResponsiveSetting(s, 'flexRowGap', ''),
						nextRow,
						'10px'
					);

					// Selalu sinkron saat switch mode agar user melihat value yang konsisten.
					setResponsiveSetting(s, 'gridRowGap', nextRow);
					setResponsiveSetting(s, 'gridColumnGap', nextCol);
					s.rowGap = nextRow;
					s.columnGap = nextCol;
				}
			}
			function syncGridColumnsForDevice(node) {
				syncCols(node, null, responsiveDevice.value);
			}
			function syncSelectedNodeGridCells() {
				const node = selectedNode.value;
				if (!node) return;
				if (isCont(node.type) || isGrid(node.type)) {
					syncCols(node, null, responsiveDevice.value);
				}
			}
			function walkNodes(nodes, handler) {
				if (!Array.isArray(nodes) || typeof handler !== 'function') return;
				nodes.forEach((node) => {
					if (!node) return;
					handler(node);
					if (Array.isArray(node.children) && node.children.length) {
						walkNodes(node.children, handler);
					}
					if (Array.isArray(node.columns) && node.columns.length) {
						node.columns.forEach((col) => {
							if (Array.isArray(col && col.children) && col.children.length) {
								walkNodes(col.children, handler);
							}
						});
					}
					if (Array.isArray(node.tabItems) && node.tabItems.length) {
						node.tabItems.forEach((item) => {
							if (Array.isArray(item && item.children) && item.children.length) {
								walkNodes(item.children, handler);
							}
						});
					}
					if (Array.isArray(node.accordionItems) && node.accordionItems.length) {
						node.accordionItems.forEach((item) => {
							if (Array.isArray(item && item.children) && item.children.length) {
								walkNodes(item.children, handler);
							}
						});
					}
				});
			}
			function syncAllGridCellsForDevice(device = responsiveDevice.value) {
				const safeDevice = normalizeResponsiveDevice(device);
				walkNodes(rootNodes.value, (node) => {
					if (isCont(node.type) || isGrid(node.type)) {
						syncCols(node, null, safeDevice);
					}
				});
			}
			watch(selectedNode, n => { if (n && isGrid(n.type)) syncCols(n, null, responsiveDevice.value); }, { deep: true });
			// Sync jumlah cell saat Grid Container settings berubah.
			watch(
				() => [
					selectedNode.value?.settings?.displayType,
					selectedNode.value?.settings?.gridColumns,
					selectedNode.value?.settings?.gridColumnsTablet,
					selectedNode.value?.settings?.gridColumnsMobile,
					selectedNode.value?.settings?.gridRows,
					selectedNode.value?.settings?.gridRowsTablet,
					selectedNode.value?.settings?.gridRowsMobile,
				],
				() => {
					const node = selectedNode.value;
					if (node && isCont(node.type)) syncCols(node, null, responsiveDevice.value);
				}
			);
			watch(responsiveDevice, () => {
				syncAllGridCellsForDevice();
				closeWidthPreviewMenu();
			});
			watch(settingsTab, scheduleColorisInit);
			watch(
				() => [
					selectedType.value,
					selectedNode.value?.settings?.bgType,
					selectedNode.value?.settings?.bgOverlayType,
					selectedNode.value?.settings?.bgState,
					selectedNode.value?.settings?.shapeDividerTopType,
					selectedNode.value?.settings?.shapeDividerBottomType,
				],
				scheduleColorisInit
			);
			watch(selectedNode, n => {
				if (n?.type === 'video') {
					normalizeVideoNodeSettings(n.settings);
				}
			}, { deep: true });
			watch(selectedId, (nextId) => {
				settingsTab.value = selectedNode.value?.type === 'accordion' ? 'content' : 'layout';
				closeControlResponsiveMenu();
				closeWidthPreviewMenu();
				scheduleColorisInit();
				if (!nextId || nextId !== selectedColumnNodeId.value) clearSelectedColumn();
			});
			watch(selectedColumnContext, (ctx) => {
				if (!ctx && selectedColumnId.value) clearSelectedColumn();
			});

			// ── Remove / Dup ──────────────────────────────────────────────────
			function delFrom(nodes, id) {
				for (let i=0; i<nodes.length; i++) {
					if (nodes[i].id === id) { nodes.splice(i,1); return true; }
					const n = nodes[i];
					if (n.children && delFrom(n.children, id)) return true;
					if (n.columns) for (const col of n.columns) if (delFrom(col.children||[], id)) return true;
					if (n.tabItems) for (const item of n.tabItems) if (delFrom(item.children||[], id)) return true;
					if (n.accordionItems) for (const item of n.accordionItems) if (delFrom(item.children||[], id)) return true;
				}
				return false;
			}
			function regenIds(node) {
				node.id = uid('n');
				if (node.children) node.children.forEach(regenIds);
				if (node.columns) node.columns.forEach(col => { col.id=uid('c'); (col.children||[]).forEach(regenIds); });
				if (node.tabItems) node.tabItems.forEach((item, index) => {
					item.id = uid('tab');
					if (!item.title) item.title = 'Tab #' + (index + 1);
					(item.children || []).forEach(regenIds);
				});
				(node.accordionItems || []).forEach((item, index) => {
					item.id = uid('accordion_item');
					if (!item.title) item.title = 'Item #' + (index + 1);
					(item.children || []).forEach(regenIds);
				});
				if (node.type === 'tabs' && node.settings) {
					const firstItem = Array.isArray(node.tabItems) && node.tabItems.length ? node.tabItems[0] : null;
					node.settings.activeTabId = firstItem ? firstItem.id : '';
				}
			}
			function dupIn(nodes, id) {
				for (let i=0; i<nodes.length; i++) {
					if (nodes[i].id === id) {
						const c = jclone(nodes[i]); regenIds(c);
						nodes.splice(i+1, 0, c); selectedId.value = c.id; return true;
					}
					const n = nodes[i];
					if (n.children && dupIn(n.children, id)) return true;
					if (n.columns) for (const col of n.columns) if (dupIn(col.children||[], id)) return true;
					if (n.tabItems) for (const item of n.tabItems) if (dupIn(item.children||[], id)) return true;
					if (n.accordionItems) for (const item of n.accordionItems) if (dupIn(item.children||[], id)) return true;
				}
				return false;
			}
			function resolveRelatedHoverNodeId(event) {
				const nextTarget = event && event.relatedTarget;
				if (!nextTarget || typeof nextTarget.closest !== 'function') return '';
				const nextNode = nextTarget.closest('[data-node-id]');
				return nextNode ? String(nextNode.getAttribute('data-node-id') || '').trim() : '';
			}
			function setHoveredNode(id) {
				hoveredId.value = String(id || '').trim();
			}
			function clearHoveredNode(id, event) {
				if (hoveredId.value !== String(id || '').trim()) return;
				hoveredId.value = resolveRelatedHoverNodeId(event);
			}
			function removeNode(id) {
				delFrom(rootNodes.value, id);
				if (selectedId.value === id) selectedId.value = '';
				if (selectedColumnNodeId.value === id) clearSelectedColumn();
				if (hoveredId.value === id) hoveredId.value = '';
			}
			function dupNode(id)    { dupIn(rootNodes.value, id); }
			function selectNode(n)  { clearPendingInsertTarget(); clearSelectedColumn(); selectedId.value = n.id; }
			function clearSel()     { clearPendingInsertTarget(); clearSelectedColumn(); selectedId.value = ''; }
			function clearCurrentSelection() {
				if (selectedColumnContext.value) {
					clearSelectedColumn();
					return;
				}
				clearSel();
			}
			function showToolboxPanel(target = null) {
				clearSelectedColumn();
				selectedId.value = '';
				setPendingInsertTarget(target);
				closeControlResponsiveMenu();
				nextTick(() => {
					const leftPanel = document.querySelector('.pb-panel.left');
					if (!leftPanel) return;
					leftPanel.scrollTo({ top: 0, behavior: 'smooth' });
					const titles = Array.from(leftPanel.querySelectorAll('.pb-panel-title'));
					const layoutTitle = titles.find((el) => String(el.textContent || '').trim().toLowerCase() === 'layout');
					if (layoutTitle) {
						layoutTitle.scrollIntoView({ behavior: 'smooth', block: 'center' });
					}
				});
			}
			function openCkFinder(targetObj, propName) {
				if (!targetObj || !propName) return false;
				const ckf = window.CKFinder;
				if (!ckf || typeof ckf.popup !== 'function') return false;
				const safeKey = String(propName);
				const basePath = new URL('/assets/plugins/ckfinder/', window.location.origin).toString();
				const connectorPath = new URL('/assets/plugins/ckfinder/core/connector/php/connector.php', window.location.origin).toString();
				const setUrl = (url) => {
					targetObj[safeKey] = String(url || '').trim();
				};
				ckf.popup({
					basePath,
					connectorPath,
					chooseFiles: true,
					onInit: (finder) => {
						finder.on('files:choose', (evt) => {
							const file = evt?.data?.files?.first ? evt.data.files.first() : null;
							if (!file || typeof file.getUrl !== 'function') return;
							setUrl(file.getUrl());
						});
						finder.on('file:choose:resizedImage', (evt) => {
							const resizedUrl = evt?.data?.resizedUrl;
							if (resizedUrl) setUrl(resizedUrl);
						});
					},
				});
				return true;
			}
			function chooseMedia(targetObj, propName, promptLabel = 'Paste image URL') {
				if (!targetObj || !propName) return;
				const safeKey = String(propName);
				if (openCkFinder(targetObj, safeKey)) return;
				const current = String(targetObj[safeKey] || '').trim();
				const nextUrl = window.prompt(promptLabel, current);
				if (nextUrl === null) return;
				targetObj[safeKey] = String(nextUrl).trim();
			}
			function clearMedia(targetObj, propName) {
				if (!targetObj || !propName) return;
				targetObj[String(propName)] = '';
			}
			function chooseBgImage(node, key = 'bgImage') {
				if (!node || !node.settings) return;
				chooseMedia(node.settings, String(key || 'bgImage'), 'Paste image URL');
			}
			function clearBgImage(node, key = 'bgImage') {
				if (!node || !node.settings) return;
				clearMedia(node.settings, String(key || 'bgImage'));
			}

			// ── Drag state ─────────────────────────────────────────────────────
			let _dropzoneListeners = [];
			let _dropzonePointerListeners = [];
			let lastHoveredDropzoneEl = null;
			function _clearDropzoneListeners() {
				_dropzoneListeners.forEach(({ el, enter, leave }) => {
					el.removeEventListener('dragenter', enter);
					el.removeEventListener('dragleave', leave);
				});
				_dropzoneListeners = [];
				_dropzonePointerListeners.forEach(({ type, handler }) => {
					document.removeEventListener(type, handler, true);
				});
				_dropzonePointerListeners = [];
				lastHoveredDropzoneEl = null;
				document.querySelectorAll('.pb-dropzone.is-drop-hover').forEach(el => el.classList.remove('is-drop-hover'));
				document.querySelectorAll('.pb-node.is-drag-over').forEach(el => el.classList.remove('is-drag-over'));
				document.querySelectorAll('.pb-grid-col.is-drag-over-col').forEach(el => el.classList.remove('is-drag-over-col'));
			}
			function trackDropzonePointerFromEvent(event) {
				if (!event) return null;
				const x = Number(event.clientX);
				const y = Number(event.clientY);
				if (!Number.isFinite(x) || !Number.isFinite(y) || !document.elementsFromPoint) return null;
				const elements = document.elementsFromPoint(x, y);
				let fallbackDropzone = null;
				for (const el of elements) {
					if (!el || typeof el.closest !== 'function') continue;
					const nestedColumn = el.closest('[data-pb-nested-dropzone="true"].pb-dropzone-col');
					if (nestedColumn) {
						lastHoveredDropzoneEl = nestedColumn;
						return nestedColumn;
					}
					if (!fallbackDropzone) fallbackDropzone = el.closest('.pb-dropzone');
				}
				if (fallbackDropzone) lastHoveredDropzoneEl = fallbackDropzone;
				return fallbackDropzone;
			}
			function onDragStart() {
				clearPendingInsertTarget();
				hoveredId.value = '';
				document.body.classList.add('pb-is-dragging');
				const pointerTracker = (event) => { trackDropzonePointerFromEvent(event); };
				['pointermove', 'mousemove', 'dragover'].forEach((type) => {
					document.addEventListener(type, pointerTracker, true);
					_dropzonePointerListeners.push({ type, handler: pointerTracker });
				});
				nextTick(() => {
					document.querySelectorAll('.pb-dropzone').forEach(el => {
						const parentNode = el.closest('.pb-node');
						const parentCol  = el.closest('.pb-grid-col');
						const enter = (e) => {
							e.stopPropagation();
							trackDropzonePointerFromEvent(e);
							// Bersihkan semua highlight sebelumnya
							document.querySelectorAll('.pb-dropzone.is-drop-hover').forEach(z => z.classList.remove('is-drop-hover'));
							document.querySelectorAll('.pb-node.is-drag-over').forEach(n => n.classList.remove('is-drag-over'));
							document.querySelectorAll('.pb-grid-col.is-drag-over-col').forEach(c => c.classList.remove('is-drag-over-col'));
							// Tambahkan highlight ke dropzone
							el.classList.add('is-drop-hover');
							lastHoveredDropzoneEl = el;
							// Tambahkan class ke pb-node parent → label muncul seperti hover
							if (parentNode) parentNode.classList.add('is-drag-over');
							// Tambahkan class ke pb-grid-col parent → label kolom muncul
							if (parentCol) parentCol.classList.add('is-drag-over-col');
						};
						const leave = (e) => {
							// Hanya hapus jika mouse benar-benar keluar dari elemen (bukan ke child)
							if (!el.contains(e.relatedTarget)) {
								el.classList.remove('is-drop-hover');
								if (parentNode) parentNode.classList.remove('is-drag-over');
								if (parentCol) parentCol.classList.remove('is-drag-over-col');
							}
						};
						el.addEventListener('dragenter', enter);
						el.addEventListener('dragleave', leave);
						_dropzoneListeners.push({ el, enter, leave });
					});
				});
			}
			function onDragEnd() {
				document.body.classList.remove('pb-is-dragging');
				_clearDropzoneListeners();
			}

			// ── @add handlers (persis pola builder lama) ──────────────────────
			// onAddContainer: dipanggil saat item di-drop ke container.children
			function onAddContainer(evt, containerNode) {
				const idx  = evt.newIndex;
				const vmData = evt.item && evt.item._underlying_vm_;
				let item = null;
				if (vmData && vmData.id) {
					item = containerNode.children.find(c => c.id === vmData.id) || containerNode.children[idx];
				} else {
					item = containerNode.children[idx] || containerNode.children[containerNode.children.length - 1];
				}
				console.log('[PB] onAddContainer, item:', item?.type, 'idx:', idx);
				if (!item) return;

				// Widget masuk container -> bungkus dalam grid 1 kolom
				if (isWgt(item.type)) {
					nextTick(() => {
						const live = findById([containerNode], item.id) !== null
							? containerNode.children[idx]
							: containerNode.children[idx];
						if (!live) return;
						const saved = jclone(live); saved.id = uid('n');
						const g = makeNode('grid'); g.settings.columns = 1;
						g.columns = [{ id: uid('c'), children: [saved] }];
						containerNode.children.splice(idx, 1, g);
					});
					return;
				}
				// Grid -> OK, biarkan
				// Container -> tidak harusnya masuk, tapi kalau masuk hapus
				if (isCont(item.type)) { containerNode.children.splice(idx, 1); }
			}

			function columnHasChildrenForSequential(col, ignoreNodeId = '') {
				if (!col || !Array.isArray(col.children) || col.children.length === 0) return false;
				if (!ignoreNodeId) return true;
				return col.children.some((child) => child && child.id !== ignoreNodeId);
			}
			function isSequentialColumnLockedForNode(node, colIndex, ignoreNodeId = '') {
				const cols = Array.isArray(node && node.columns) ? node.columns : [];
				const idx = Number(colIndex);
				if (!Number.isFinite(idx) || idx < 0 || idx >= cols.length) return false;
				if (columnHasChildrenForSequential(cols[idx], ignoreNodeId)) return false;
				for (let i = 0; i < idx; i++) {
					if (!columnHasChildrenForSequential(cols[i], ignoreNodeId)) return true;
				}
				return false;
			}
			function rerouteTabsDropToNestedColumn(evt, tabChildren) {
				const parentEl = evt && evt.to ? evt.to : null;
				const originalEvent = evt && evt.originalEvent ? evt.originalEvent : null;
				const hoveredEl = findNestedCanvasDropTargetFromEvent(originalEvent, parentEl) || lastHoveredDropzoneEl;
				if (!hoveredEl || !parentEl || hoveredEl === parentEl) return false;
				if (!parentEl.contains(hoveredEl) || !hoveredEl.classList || !hoveredEl.classList.contains('pb-dropzone-col')) return false;

				const parentNodeId = String(hoveredEl.dataset.parentNodeId || '').trim();
				const colIndex = Number(hoveredEl.dataset.colIndex);
				if (!parentNodeId || !Number.isFinite(colIndex)) return false;

				const ownerNode = findById(rootNodes.value, parentNodeId);
				const ownerColumns = Array.isArray(ownerNode && ownerNode.columns) ? ownerNode.columns : [];
				const targetColumn = ownerColumns[colIndex];
				const targetChildren = Array.isArray(targetColumn && targetColumn.children) ? targetColumn.children : null;
				if (!ownerNode || !targetColumn || !targetChildren) return false;

				const idx = Number(evt.newIndex);
				const vmData = evt.item && evt.item._underlying_vm_;
				let item = null;
				if (vmData && vmData.id) {
					item = tabChildren.find((child) => child && child.id === vmData.id) || tabChildren[idx] || tabChildren[tabChildren.length - 1];
				} else {
					item = tabChildren[idx] || tabChildren[tabChildren.length - 1];
				}
				if (!item) return false;
				if (isSequentialColumnLockedForNode(ownerNode, colIndex, item.id)) return false;

				const currentIndex = tabChildren.indexOf(item);
				if (currentIndex >= 0) tabChildren.splice(currentIndex, 1);
				targetChildren.push(item);
				return true;
			}
			function rerouteAccordionDropToNestedColumn(evt, itemChildren) {
				return rerouteTabsDropToNestedColumn(evt, itemChildren);
			}

			// onAddCol: dipanggil saat item di-drop ke grid column
			function onAddCol(evt, col, colIndex, parentNode) {
				const idx  = evt.newIndex;
				const draggedNodeType = String((evt.item && evt.item.dataset && evt.item.dataset.nodeType) || '').trim();
				const isExistingCanvasNode = !!draggedNodeType;
				// vuedraggable@4.1.0: item ada di _underlying_vm_
				// Tapi array sudah diupdate, cari item yang cocok
				const vmData = evt.item && evt.item._underlying_vm_;
				// Cari item di col.children berdasarkan id dari vm, atau pakai newIndex
				let item = null;
				if (vmData && vmData.id) {
					item = col.children.find(c => c.id === vmData.id) || col.children[idx];
				} else {
					item = col.children[idx] || col.children[col.children.length - 1];
				}
				console.log('[PB] onAddCol idx:', idx, 'col.len:', col.children.length, 'item:', item?.type);
				if (!item) return;

				// Safety guard: cegah bypass lock berurutan walau ada jalur drop yang lolos put().
				const targetIndex = Number(colIndex);
				if (!isExistingCanvasNode && parentNode && Number.isFinite(targetIndex) && isSequentialColumnLockedForNode(parentNode, targetIndex, item.id)) {
					const targetPos = col.children.indexOf(item);
					if (targetPos >= 0) col.children.splice(targetPos, 1);
					const fromEl = evt.from;
					const cameFromColumn = !!(fromEl && fromEl.classList && fromEl.classList.contains('pb-dropzone-col'));
					const fromList = fromEl && fromEl.__draggable_component__ ? fromEl.__draggable_component__.realList : null;
					if (cameFromColumn && Array.isArray(fromList) && !fromList.some((child) => child && child.id === item.id)) {
						const oldIndex = Number(evt.oldIndex);
						if (Number.isFinite(oldIndex) && oldIndex >= 0 && oldIndex <= fromList.length) fromList.splice(oldIndex, 0, item);
						else fromList.push(item);
					}
					return;
				}

				// Grid atau widget -> OK
				// Container -> hapus
				if (isCont(item.type)) { col.children.splice(col.children.indexOf(item), 1); }
			}

			// onRootAdd: dipanggil saat item di-drop ke root
			function onRootAdd(evt) {
				const idx = evt.newIndex;
				// _underlying_vm_ adalah cara paling reliable di vuedraggable@4.1.0
				const vmData = evt.item && evt.item._underlying_vm_;
				// Cari item di rootNodes berdasarkan id dari vm
				let item = null;
				if (vmData && vmData.id) {
					item = findById(rootNodes.value, vmData.id)
						|| rootNodes.value.find(n => n.id === vmData.id)
						|| rootNodes.value[idx]
						|| rootNodes.value[rootNodes.value.length - 1];
				} else {
					item = rootNodes.value[idx] || rootNodes.value[rootNodes.value.length - 1];
				}
				console.log('[PB] onRootAdd item:', item?.type, 'id:', item?.id, 'idx:', idx, 'len:', rootNodes.value.length);
				if (!item) return;

				// Container → buka modal untuk pilih struktur kolom
				// Panggil openModal SYNCHRONOUSLY (tanpa nextTick) agar vuedraggable
				// bisa menyelesaikan animasi drop sebelum Vue re-render menghapus elemen DOM.
				if (isCont(item.type)) {
					if (item.settings) seedResponsiveSettings(item.settings);
					openModal(item.type, 'root', { containerNode: item, list: rootNodes.value }, { discardOnClose: true, pendingNodeId: item.id });
					return;
				}

				// Grid → samakan flow dengan Container Grid (parity UX dengan Elementor)
				if (isGrid(item.type)) {
					nextTick(() => {
						const live = findById(rootNodes.value, item.id) || rootNodes.value[idx];
						if (!live) return;
						const realIdx = rootNodes.value.indexOf(live);
						const gs = live.settings || {};
						const cols = clamp(Number(gs.columns || (Array.isArray(live.columns) ? live.columns.length : 3) || 3), 1, 12);

						const c = makeNode('container');
						Object.keys(c.settings || {}).forEach((key) => {
							if (Object.prototype.hasOwnProperty.call(gs, key)) c.settings[key] = gs[key];
						});

						c.id = live.id;
						c.labelSuffix = String(live.labelSuffix || '').trim();
						c.settings.displayType = 'grid';
						c.settings.gridColumns = cols;
						c.settings.gridTemplateColumns = gs.gridTemplateColumns || '';
						c.settings.gridRows = gs.gridRows || c.settings.gridRows;
						c.settings.gridColumnGap = gs.columnGap || gs.gridColumnGap || c.settings.gridColumnGap;
						c.settings.gridRowGap = gs.rowGap || gs.gridRowGap || c.settings.gridRowGap;
						c.settings.gridAlignItems = gs.gridAlignItems || c.settings.gridAlignItems || 'start';
						c.settings.autoFlow = gs.autoFlow || c.settings.autoFlow;
						seedResponsiveSettings(c.settings, true);

						const sourceColumns = Array.isArray(live.columns) ? live.columns : [];
						c.columns = Array.from({ length: cols }, (_, colIdx) => {
							const sourceCol = sourceColumns[colIdx];
							const children = Array.isArray(sourceCol && sourceCol.children)
								? sourceCol.children.map(child => jclone(child))
								: [];
							return { id: uid('c'), children };
						});

						if (sourceColumns.length > cols && c.columns.length > 0) {
							const lastCol = c.columns[c.columns.length - 1];
							sourceColumns.slice(cols).forEach((col) => {
								(col && col.children ? col.children : []).forEach((child) => {
									lastCol.children.push(jclone(child));
								});
							});
						}

						rootNodes.value.splice(realIdx, 1, c);
					});
					return;
				}

				// Widget → bungkus dalam container 1 kolom
				if (isWgt(item.type)) {
					nextTick(() => {
						const live = findById(rootNodes.value, item.id) || rootNodes.value[idx];
						if (!live) return;
						const realIdx = rootNodes.value.indexOf(live);
						const saved = jclone(live); saved.id = live.id;
						const c = makeNode('container');
						if (!c) return;
						c.settings.displayType = 'grid';
						c.settings.gridColumns = 1;
						c.settings.gridRows = '1';
						c.settings.gridColumnGap = '20px';
						c.settings.gridRowGap = '20px';
						c.settings.gridAlignItems = 'start';
						seedResponsiveSettings(c.settings, true);
						c.columns = [{ id: uid('c'), children: [saved] }];
						rootNodes.value.splice(realIdx, 1, c);
					});
					return;
				}

				rootNodes.value.splice(idx, 1);
			}

			// ── Structure modal ───────────────────────────────────────────────
			function emptyModalState() {
				return {
					visible: false,
					step: 1,
					layoutType: '',
					zone: '',
					list: null,
					discardOnClose: false,
					pendingNodeId: '',
				};
			}
			const modal = ref(emptyModalState());
			function openModal(layoutType, zone, list, options = {}) {
				modal.value = {
					visible: true,
					step: (layoutType === 'container' || layoutType === 'container_fluid') ? 1 : 2,
					layoutType,
					zone,
					list,
					discardOnClose: options.discardOnClose === true,
					pendingNodeId: String(options.pendingNodeId || ''),
				};
			}
			function discardPendingModalNode() {
				const current = modal.value || {};
				if (!current.discardOnClose || !current.pendingNodeId) return;
				const liveList = current.list && Array.isArray(current.list.list) ? current.list.list : null;
				if (!liveList) return;
				const idx = liveList.findIndex((node) => node && node.id === current.pendingNodeId);
				if (idx < 0) return;
				liveList.splice(idx, 1);
				if (selectedId.value === current.pendingNodeId) selectedId.value = '';
				if (selectedColumnNodeId.value === current.pendingNodeId) clearSelectedColumn();
				if (hoveredId.value === current.pendingNodeId) hoveredId.value = '';
			}
			function closeModal() {
				discardPendingModalNode();
				modal.value = emptyModalState();
			}
			function pickLayout(type) { modal.value.layoutType = type; modal.value.step = 2; }

			const cPresets = [
				{ cols:1, direction:'column', label:'1 Column',         flexIcon:'down' },
				{ cols:2, direction:'row',    label:'2 Columns',        flexIcon:'right' },
				{ cols:3, direction:'row',    label:'3 Columns',        flexIcon:'right' },
				{ cols:4, direction:'row',    label:'4 Columns',        flexIcon:'right' },
				{ cols:5, direction:'row',    label:'5 Columns',        flexIcon:'right' },
				{ cols:6, direction:'row',    label:'6 Columns',        flexIcon:'right' },
				{ cols:2, direction:'row',    label:'1/3 + 2/3',        flexWidths:['33%','67%'] },
				{ cols:2, direction:'row',    label:'2/3 + 1/3',        flexWidths:['67%','33%'] },
				{ cols:3, direction:'row',    label:'1/4 + 1/2 + 1/4', flexWidths:['25%','50%','25%'] },
				{ cols:3, direction:'row',    label:'1/2 + 1/4 + 1/4', flexWidths:['50%','25%','25%'] },
				{ cols:3, direction:'row',    label:'1/4 + 1/4 + 1/2', flexWidths:['25%','25%','50%'] },
				{ cols:4, direction:'row',    label:'1/4 + 1/2 + 1/8 + 1/8', flexWidths:['25%','50%','12.5%','12.5%'] },
			];
			const gPresets = [1,2,3,4,5,6];

			function applyContPreset(p) {
					const { zone, list, layoutType } = modal.value;
					const cols = p.cols || 1;
					function configureContainer(c, isGridLayout) {
						if (isGridLayout || layoutType === 'container_grid') {
							c.settings.displayType = 'grid'; c.settings.gridColumns = cols;
							c.settings.gridColumnGap = '20px'; c.settings.gridRowGap = '20px';
							c.settings.gridAlignItems = 'start';
							c.columns = Array.from({length:cols}, () => ({id:uid('c'),children:[]}));
						} else {
							c.settings.displayType = 'flex'; c.settings.direction = p.direction||'row';
							c.settings.alignItems = 'flex-start'; c.settings.flexColumnGap = '20px';
							c.settings.gridColumns = cols;
							c.settings.gridTemplateColumns = '';
							if (p.flexWidths && p.flexWidths.length) {
								c.columns = p.flexWidths.map(w => ({id:uid('c'),flexBasis:w,children:[]}));
							} else {
								c.columns = Array.from({length:cols}, () => ({id:uid('c'),children:[]}));
							}
						}
						seedResponsiveSettings(c.settings, true);
						return c;
					}
					if (zone === 'root' && list && list.containerNode) {
						configureContainer(list.containerNode, layoutType === 'container_grid');
						modal.value.discardOnClose = false;
						selectedId.value = list.containerNode.id; closeModal(); return;
					}
					if (!Array.isArray(list)) { closeModal(); return; }
					const c = makeNode('container');
					configureContainer(c, layoutType === 'container_grid');
					list.push(c); selectedId.value = c.id; closeModal();
			}
			function applyGridPreset(cols) {
					const { zone, list } = modal.value;
					if (zone === 'root' && list && list.containerNode) {
						const node = list.containerNode;
						node.settings.displayType = 'grid'; node.settings.gridColumns = cols;
						node.settings.gridColumnGap = '20px'; node.settings.gridRowGap = '20px';
						node.settings.gridAlignItems = 'start';
						seedResponsiveSettings(node.settings, true);
						node.columns = Array.from({length:cols}, () => ({id:uid('c'),children:[]}));
						modal.value.discardOnClose = false;
						selectedId.value = node.id; closeModal(); return;
					}
					if (!Array.isArray(list)) { closeModal(); return; }
					const c = makeNode('container');
					c.settings.displayType = 'grid'; c.settings.gridColumns = cols;
					c.settings.gridColumnGap = '20px'; c.settings.gridRowGap = '20px';
					c.settings.gridAlignItems = 'start';
					seedResponsiveSettings(c.settings, true);
					c.columns = Array.from({length:cols}, () => ({id:uid('c'),children:[]}));
					list.push(c); selectedId.value = c.id; closeModal();
				}

				// ── Sidebar tools & cloneItem ─────────────────────────────────────
				function toolClone(toolDef) { return makeNode(toolDef.type) || jclone(toolDef); }
				function rootHasLayoutNodes() {
					return rootNodes.value.some((node) => node && (isCont(node.type) || isGrid(node.type)));
				}
				function rootCanAcceptDirectWidgetDrop() {
					return rootNodes.value.length === 0 || !rootHasLayoutNodes();
				}
				function isSidebarWidgetDrag(el) {
					return !String(el?.dataset?.nodeType || '').trim();
				}

				const sidebarContGroup = { name:'pb-root',      pull:'clone', put:false };
				const sidebarGridGroup = { name:'pb-container', pull:'clone', put:false };
				const sidebarWgtGroup  = { name:'pb-col',       pull:'clone', put:false };

				const rootGroup = {
					name: 'pb-root',
					put: (to, from, el) => {
						const fromGroup = from.options && from.options.group && from.options.group.name;
						if (fromGroup === 'pb-col') {
							if (isSidebarWidgetDrag(el)) return true;
							return rootCanAcceptDirectWidgetDrop();
						}
						return true;
					},
				};

				const toolbox = {
				layout: [
					{ type:'container',       label:'Container',       icon:'far fa-square' },
					{ type:'grid',            label:'Grid',            icon:'fas fa-th-large' },
				],
				basic: [
					{ type:'heading',     label:'Heading',     icon:'fas fa-heading' },
					{ type:'text_editor', label:'Text Editor', icon:'fas fa-edit' },
					{ type:'image',       label:'Image',       icon:'far fa-image' },
					{ type:'video',       label:'Video',       icon:'fas fa-video' },
					{ type:'button',      label:'Button',      icon:'fas fa-link' },
					{ type:'icon',        label:'Icon',        icon:'far fa-star' },
					{ type:'divider',     label:'Divider',     icon:'fas fa-minus' },
					{ type:'spacer',      label:'Spacer',      icon:'fas fa-arrows-alt-v' },
				],
				general: [
					{ type:'tabs',        label:'Tabs',        icon:'far fa-folder' },
					{ type:'accordion',   label:'Accordion',   icon:'fas fa-bars' },
					{ type:'image_box',   label:'Image Box',   icon:'far fa-image' },
				],
				advanced: [],
			};

			// ── Save ──────────────────────────────────────────────────────────
			async function savePage() {
				if (saveState.value === 'saving') return;
				saveState.value = 'saving';
				saveMsg.value = 'Saving...';
				try {
					const normalizedCustomCss = normalizeCustomCssBeforeApply(customCss.value);
					if (normalizedCustomCss.changed) {
						customCss.value = normalizedCustomCss.value;
					}
					const res = await axios.post(saveUrl.value,
						{ pageName:pageName.value, pageStatus:pageStatus.value, customCss:normalizedCustomCss.value, layout:rootNodes.value },
						{ headers:{ 'X-CSRF-TOKEN':PBC.csrfToken, 'X-Requested-With':'XMLHttpRequest', Accept:'application/json' } }
					);
					const successMessage = res.data?.message || 'Saved';
					saveState.value = 'success';
					saveMsg.value = typeof successMessage === 'string' ? successMessage : 'Saved';
					showSaveToast('success', successMessage);
					if (mode.value==='create' && res.data?.editUrl) {
						setTimeout(() => { window.location.href = res.data.editUrl; }, 500);
					}
				} catch(e) {
					const errorPayload = e.response?.data?.message || e.message || 'Failed';
					const normalized = normalizeNoticeMessage(errorPayload);
					saveState.value = 'error';
					saveMsg.value = normalized.isArray ? normalized.message.join(' | ') : normalized.message;
					showSaveToast('failed', errorPayload);
				}
			}

			const appTitle = computed(() => mode.value==='edit' ? 'Edit Page Builder' : 'Create Page Builder');

			return {
				appTitle, toolbox, rootNodes, loadWidget,
				toolClone, sidebarContGroup, sidebarGridGroup, sidebarWgtGroup, rootGroup,
				selectedId, selectedColumnNodeId, selectedColumnId, selectedColumnContext, hoveredId, settingsTab, selectedNode, selectedType,
				responsiveDevice, responsiveDevices, fontFamilies, desktopPreviewWidth, desktopPreviewWidths, widthPreviewMenuOpen, previewCanvasWidthLabel, previewCanvasStyle, activeResponsiveKey, syncResponsiveSides, syncGridGap, syncGridColumnsForDevice,
				controlResponsiveMenu, responsiveDeviceIcon, responsiveDeviceLabel, deviceOptionLabel,
				openControlResponsiveMenu, closeControlResponsiveMenu, isControlResponsiveMenuOpen,
				setResponsiveDevice, applyResponsiveDevice, toggleWidthPreviewMenu, closeWidthPreviewMenu, selectDesktopPreviewWidth,
				containerResponsiveValue, setContainerResponsiveSetting,
				onContainerDisplayTypeChange, onContainerContentWidthChange,
				spacingControlUnits, spacingUnit, spacingSideValue, onSpacingSideInput, setSpacingUnit,
				sizeControlUnits, videoAspectRatioOptions, videoAspectRatioValue, setVideoAspectRatioValue, videoSourceOptions, videoSuggestedVideoOptions, videoPreloadOptions, videoCurrentSource, setVideoSourceType, videoLinkField, videoUsesHostedPicker, videoShowsEndTime, videoShowsPoster, videoShowsOverlay, videoUsesControlsColor, videoToggleOptions, videoSelectOptions, videoToggleStateLabel, sizeControlDisplayValue, sizeControlUnit, sizeControlMax, sizeControlStep, onSizeControlInput, setSizeControlUnit,
				containerWidthValue, containerWidthUnit, containerWidthMax, containerWidthStep, onContainerWidthInput, setContainerWidthValue, setContainerWidthUnit,
				columnWidthValue, setSelectedColumnWidthValue, columnResizeOverlay,
				minHeightValue, minHeightUnit, setMinHeightValue, setMinHeightUnit,
				spacerHeightValue, spacerHeightUnit, spacerHeightMax, spacerHeightStep, onSpacerHeightInput, setSpacerHeightValue, setSpacerHeightUnit,
				shapeDividerTypeOptions, shapeDividerHasWidth, shapeDividerHasFlip, shapeDividerHasInvert,
				shapeDividerWidthValue, shapeDividerWidthUnit, setShapeDividerWidthValue, setShapeDividerWidthUnit,
				shapeDividerHeightValue, shapeDividerHeightUnit, setShapeDividerHeightValue, setShapeDividerHeightUnit,
				containerGridColumnsValue, setContainerGridColumnsValue,
				containerGridRowsValue, setContainerGridRowsValue, syncContainerGap,
				bgStateKey, setBgState, isBgHoverState, setBgTypeForState, setBgOverlayTypeForState,
				displayNodeLabel, nodeLabelIcon,
				selectNode, selectColumn, startColumnResize, clearSel, clearCurrentSelection, setHoveredNode, clearHoveredNode, showToolboxPanel, removeNode, dupNode, syncCols, chooseBgImage, clearBgImage, chooseMedia, clearMedia,
				iconLibraryGroups, showIconLibraryModal, iconLibraryGroup, iconLibrarySearch, iconLibraryLoading, iconLibraryError, iconLibrarySelected, filteredIconLibraryIcons,
				openIconLibrary, openAccordionIconLibrary, chooseAccordionSvg, closeIconLibrary, selectIconLibraryItem, insertSelectedIcon,
				fontAwesomeStyleLabel, iconWidgetUsesShape, iconWidgetCurrentLabel, iconWidgetCurrentStyleLabel, toggleIconLinkOptions, isIconLinkOptionsOpen,
				tabsItemsForNode, tabsActiveItem, selectTabsItem, addTabsItem, duplicateTabsItem, removeTabsItem, tabsItemSummary, tabsSelectedRowDirection,
				tabsWidthValue, tabsWidthUnit, tabsWidthMax, tabsWidthStep, onTabsWidthInput, setTabsWidthValue, setTabsWidthUnit,
				accordionItemsForNode, accordionRuntimeForNode, accordionEditingItem, selectAccordionItem, toggleAccordionItem, resetAccordionRuntimeFromDefaults,
				addAccordionItem, duplicateAccordionItem, removeAccordionItem, accordionItemSummary,
				accordionStyleState, accordionTitleStyleState, accordionIconStyleState, accordionStateKey,
				accordionDimensionValue, accordionDimensionUnit, accordionDimensionMax, accordionDimensionStep, onAccordionDimensionInput, setAccordionDimensionUnit,
				accordionBoxUnit, accordionBoxSideValue, accordionBoxLinked, toggleAccordionBoxLink, onAccordionBoxSideInput, setAccordionBoxUnit,
				accordionStyleStates: ACCORDION_STYLE_STATES, accordionBorderTypes: ACCORDION_BORDER_TYPES, accordionGradientTypes: ACCORDION_GRADIENT_TYPES,
				tabsBreakpointOptions: TABS_WIDGET_BREAKPOINT_OPTIONS, tabsWidthUnits: TABS_WIDGET_WIDTH_UNITS,
				iconWidgetViewOptions: ICON_WIDGET_VIEW_OPTIONS, iconWidgetShapeOptions: ICON_WIDGET_SHAPE_OPTIONS,
				pageName, pageStatus, customCss, customCssEditorTextarea, customCssEditorGutter, showCssEditor, cssEditorFullscreen,
				showTextEditorModal, textEditorModalFullscreen, textEditorModalSummary, setTextEditorHtml, openTextEditorModal, closeTextEditorModal,
				customCssGotoLine, customCssSearchQuery, customCssActiveLine, customCssCharCount, customCssLineCount, customCssLineNumbers, customCssSummary,
				openCustomCssEditor, closeCustomCssEditor, applyCustomCssEditorChanges, clearCustomCss, handleCustomCssTab, syncCustomCssEditorScroll, goToCustomCssLine, searchCustomCssCode, savePage, saveState, saveMsg,
				toastVisible, responseStatusToast, isArrayMessageAfterSubmit, responseMessageAfterSubmit, closeToast, showUnsupportedControlNotice,
				onDragStart, onDragEnd,
				onAddContainer, onAddCol, onRootAdd,
				modal, cPresets, gPresets, applyContPreset, applyGridPreset, closeModal, pickLayout, openModal,
				canUndo, canRedo, undo, redo,
				onToolboxItemClick, pendingInsertTarget, clearPendingInsertTarget, rerouteTabsDropToNestedColumn, rerouteAccordionDropToNestedColumn, trackDropzonePointerFromEvent,
			};
		},

		template: `
<div class="pb-app">
	<div class="pb-topbar">
		<div class="pb-brand"><span class="pb-brand-badge">PB</span><span>{{ appTitle }} - Elementor Style</span></div>
		<div class="pb-top-actions">
			<button class="pb-btn icon" :disabled="!canUndo" @click="undo" title="Undo"><i class="fas fa-undo"></i></button>
			<button class="pb-btn icon" :disabled="!canRedo" @click="redo" title="Redo"><i class="fas fa-redo"></i></button>
			<button class="pb-btn primary" :class="{ 'is-loading': saveState==='saving' }" :disabled="saveState==='saving'" @click="savePage">
				<template v-if="saveState==='saving'">
					Saving
					<span class="spinner-border spinner-border-sm text-light ms-1" role="status" aria-hidden="true"></span>
				</template>
				<template v-else>
					<i class="fas fa-save"></i> Save
				</template>
			</button>
		</div>
	</div>

	<div class="pb-notice" v-cloak>
		<div aria-live="polite" aria-atomic="true" class="position-relative">
			<div class="toast-container position-fixed top-0 end-0 p-3 pb-toast-container">
				<div v-show="toastVisible" :class="'toast show pb-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true">
					<div :class="'toast-header '+responseStatusToast+' px-3 pt-3 pb-1 border-0'">
						<strong class="me-auto">Notice</strong>
						<small>just now</small>
						<button type="button" class="btn-close" aria-label="Close" @click="closeToast"></button>
					</div>
					<div class="toast-body p-3 text-start">
						<div v-if="isArrayMessageAfterSubmit === 1">
							<ul class="ps-3 m-0">
								<li v-for="(item, index) in responseMessageAfterSubmit" :key="'toast-msg-'+index">{{ item }}</li>
							</ul>
						</div>
						<div v-else>{{ responseMessageAfterSubmit }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="pb-main">
		<div class="pb-panel left">
			<div v-if="!selectedNode">
				<div v-if="pendingInsertTarget" class="pb-pending-insert-notice">
					<div class="pb-pending-insert-text">
						<i class="fas fa-plus-circle animate-pulse"></i>
						<span>Click widget to insert to target</span>
					</div>
					<button type="button" class="pb-pending-insert-cancel" @click="clearPendingInsertTarget">
						<i class="fas fa-times"></i>
					</button>
				</div>

				<div class="pb-section">
					<div class="pb-panel-title">Page</div>
					<div class="pb-form-group"><label class="pb-form-label">Page Name</label><input class="pb-input" v-model="pageName"></div>
					<div class="pb-form-group"><label class="pb-form-label">Status</label>
						<select class="pb-select" v-model="pageStatus">
							<option value="publish">Publish</option><option value="draft">Draft</option><option value="not_active">Not Active</option>
						</select>
					</div>
					<div class="pb-form-group mt-3">
						<div class="pb-label-row">
							<label class="pb-form-label mb-0">Custom CSS</label>
							<span class="pb-css-editor-summary">{{ customCssSummary }}</span>
						</div>
						<button type="button" class="pb-css-editor-trigger" @click="openCustomCssEditor">
							<span class="pb-css-editor-trigger-main"><i class="fas fa-code"></i> Custom CSS Editor</span>
							<span class="pb-css-editor-trigger-action"><i class="fas fa-external-link-alt"></i></span>
						</button>
					</div>
				</div>

				<div class="pb-section">
					<div class="pb-panel-title">Layout</div>
					<draggable
						:list="toolbox.layout"
						:group="sidebarContGroup"
						:clone="toolClone"
						item-key="type"
						class="pb-tool-grid"
						:sort="false"
						@start="onDragStart"
						@end="onDragEnd"
					>
						<template #item="{ element }">
							<div class="pb-tool-item" @click="onToolboxItemClick(element)"><i :class="element.icon"></i><span>{{ element.label }}</span></div>
						</template>
					</draggable>
				</div>

				<div class="pb-section">
					<div class="pb-panel-title">Basic</div>
					<draggable
						:list="toolbox.basic"
						:group="sidebarWgtGroup"
						:clone="toolClone"
						item-key="type"
						class="pb-tool-grid"
						:sort="false"
						@start="onDragStart"
						@end="onDragEnd"
					>
						<template #item="{ element }">
							<div class="pb-tool-item" @click="onToolboxItemClick(element)"><i :class="element.icon"></i><span>{{ element.label }}</span></div>
						</template>
					</draggable>
				</div>

				<div class="pb-section">
					<div class="pb-panel-title">General</div>
					<draggable
						:list="toolbox.general"
						:group="sidebarWgtGroup"
						:clone="toolClone"
						item-key="type"
						class="pb-tool-grid"
						:sort="false"
						@start="onDragStart"
						@end="onDragEnd"
					>
						<template #item="{ element }">
							<div class="pb-tool-item" @click="onToolboxItemClick(element)"><i :class="element.icon"></i><span>{{ element.label }}</span></div>
						</template>
					</draggable>
				</div>

				<div class="pb-section" v-if="toolbox.advanced.length">
					<div class="pb-panel-title">Advanced</div>
					<draggable
						:list="toolbox.advanced"
						:group="sidebarWgtGroup"
						:clone="toolClone"
						item-key="type"
						class="pb-tool-grid"
						:sort="false"
						@start="onDragStart"
						@end="onDragEnd"
					>
						<template #item="{ element }">
							<div class="pb-tool-item" @click="onToolboxItemClick(element)"><i :class="element.icon"></i><span>{{ element.label }}</span></div>
						</template>
					</draggable>
				</div>
			</div>

						<div v-else>
				<div class="pb-section" v-if="selectedColumnContext">
					<div class="pb-props-header">
						<button class="pb-btn icon-sm" @click="clearCurrentSelection" title="Back"><i class="fas fa-chevron-left"></i></button>
						<div class="pb-props-type-badge">
							<i class="far fa-square"></i>
							<span>Column {{ selectedColumnContext.index + 1 }}</span>
						</div>
					</div>
					<div class="pb-form-note mt-2 mb-3">Inside {{ displayNodeLabel(selectedColumnContext.node) }}</div>
					<details class="pb-collapsible" open>
						<summary>Layout</summary>
						<div class="pb-collapsible-body">
							<div class="pb-form-group">
								<div class="pb-label-row pb-label-row-device">
									<label class="pb-form-label mb-0">Width</label>
									<div class="pb-label-tools">
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('column-width')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('column-width')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'column-width-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('column-width', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
										<div class="pb-control-unit-wrap">%</div>
									</div>
								</div>
								<div class="pb-range-value-row">
									<input type="range" class="pb-range" min="1" max="99" step="0.5" :value="columnWidthValue(selectedColumnContext)" @input="setSelectedColumnWidthValue(selectedColumnContext, $event.target.value)">
									<input class="pb-input pb-input-compact" type="number" min="1" max="99" step="0.1" :value="columnWidthValue(selectedColumnContext)" @input="setSelectedColumnWidthValue(selectedColumnContext, $event.target.value)">
								</div>
								<div class="pb-form-note" v-if="selectedColumnContext.canDragResize">Tip: you can also drag the divider handle on the canvas.</div>
								<div class="pb-form-note" v-else>Direct drag resize is available for flex row containers without wrap.</div>
							</div>
						</div>
					</details>
				</div>
				<div class="pb-section" v-else>
					<div class="pb-props-header">
						<button class="pb-btn icon-sm" @click="clearCurrentSelection" title="Back"><i class="fas fa-chevron-left"></i></button>
						<div class="pb-props-type-badge">
							<i :class="nodeLabelIcon(selectedType)"></i>
							<span>{{ displayNodeLabel(selectedNode) }}</span>
						</div>
					</div>
					<div class="pb-form-group pb-element-name-group mt-2">
						<label class="pb-form-label">Element Name Suffix</label>
						<input class="pb-input pb-input-sm" v-model="selectedNode.labelSuffix" placeholder="e.g. Section 1">
					</div>

					<!-- ═══ CONTAINER / CONTAINER_FLUID TABS ═══ -->
					<template v-if="selectedType==='container'||selectedType==='container_fluid'">
						<div class="pb-layout-settings pb-layout-settings--container">
							<div class="pb-tab-nav">
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='layout'}" @click="settingsTab='layout'"><i class="far fa-square"></i><span>Layout</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='style'}" @click="settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='advanced'}" @click="settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
							</div>
						<!-- TAB LAYOUT -->
						<div v-show="settingsTab==='layout'" class="pb-tab-content pb-layout-settings__tab">
							<details class="pb-collapsible" open>
								<summary>Container</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group">
									<label class="pb-form-label">Container Layout</label>
									<select class="pb-select" v-model="selectedNode.settings.displayType" @change="onContainerDisplayTypeChange(selectedNode)">
										<option value="flex">Flexbox</option>
										<option value="grid">Grid</option>
									</select>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Content Width</label>
									<select class="pb-select" v-model="selectedNode.settings.contentWidth" @change="onContainerContentWidthChange(selectedNode)">
										<option value="full">Full Width</option>
										<option value="boxed">Boxed</option>
									</select>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Width</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-width')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-width')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-width-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-width', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" :max="containerWidthMax(selectedNode)" :step="containerWidthStep(selectedNode)" :value="containerWidthValue(selectedNode)" @input="onContainerWidthInput(selectedNode, $event)">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" :max="containerWidthMax(selectedNode)" :step="containerWidthStep(selectedNode)" :value="containerWidthValue(selectedNode)" @input="onContainerWidthInput(selectedNode, $event)">
											<select class="pb-mini-unit" :value="containerWidthUnit(selectedNode)" @change="setContainerWidthUnit(selectedNode, $event.target.value)">
												<option v-for="unit in sizeControlUnits" :key="'container-width-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Min Height</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-min-height')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-min-height')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-min-height-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-min-height', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" max="1000" step="1" :value="minHeightValue(selectedNode) || 0" @input="setMinHeightValue(selectedNode, $event.target.value)">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" max="1000" step="1" :value="minHeightValue(selectedNode)" @input="setMinHeightValue(selectedNode, $event.target.value)" placeholder="auto">
											<select class="pb-mini-unit" :value="minHeightUnit(selectedNode)" @change="setMinHeightUnit(selectedNode, $event.target.value)">
												<option value="px">px</option>
												<option value="vh">vh</option>
											</select>
										</div>
									</div>
									<div class="pb-form-note">To achieve full height Container use 100vh.</div>
								</div>
								</div>
							</details>
							<details class="pb-collapsible" v-if="selectedNode.settings.displayType==='flex'||!selectedNode.settings.displayType" open>
								<summary>Items</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Direction</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-direction')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-direction')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-direction-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-direction', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'direction','row')==='row'}"            @click="setContainerResponsiveSetting(selectedNode.settings,'direction','row')"            title="Row"><i class="fas fa-arrow-right"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'direction','row')==='column'}"         @click="setContainerResponsiveSetting(selectedNode.settings,'direction','column')"         title="Column"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'direction','row')==='row-reverse'}"    @click="setContainerResponsiveSetting(selectedNode.settings,'direction','row-reverse')"    title="Row Reverse"><i class="fas fa-arrow-left"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'direction','row')==='column-reverse'}" @click="setContainerResponsiveSetting(selectedNode.settings,'direction','column-reverse')" title="Col Reverse"><i class="fas fa-arrow-up"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Justify Content</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-justify-content')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-justify-content')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-justify-content-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-justify-content', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'justifyContent','flex-start')==='flex-start'}"    @click="setContainerResponsiveSetting(selectedNode.settings,'justifyContent','flex-start')"    title="Start"><i class="fas fa-align-left"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'justifyContent','flex-start')==='center'}"        @click="setContainerResponsiveSetting(selectedNode.settings,'justifyContent','center')"        title="Center"><i class="fas fa-align-center"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'justifyContent','flex-start')==='flex-end'}"      @click="setContainerResponsiveSetting(selectedNode.settings,'justifyContent','flex-end')"      title="End"><i class="fas fa-align-right"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'justifyContent','flex-start')==='space-between'}" @click="setContainerResponsiveSetting(selectedNode.settings,'justifyContent','space-between')" title="Space Between"><i class="fas fa-arrows-alt-h"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'justifyContent','flex-start')==='space-around'}"  @click="setContainerResponsiveSetting(selectedNode.settings,'justifyContent','space-around')"  title="Space Around"><i class="fas fa-grip-lines-vertical"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'justifyContent','flex-start')==='space-evenly'}"  @click="setContainerResponsiveSetting(selectedNode.settings,'justifyContent','space-evenly')"  title="Space Evenly"><i class="fas fa-grip-lines"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Items</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-align-items')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-align-items')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-align-items-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-align-items', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignItems','flex-start')==='flex-start'}" @click="setContainerResponsiveSetting(selectedNode.settings,'alignItems','flex-start')" title="Start"><i class="fas fa-arrow-up"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignItems','flex-start')==='center'}"    @click="setContainerResponsiveSetting(selectedNode.settings,'alignItems','center')"    title="Center"><i class="fas fa-arrows-alt-v"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignItems','flex-start')==='flex-end'}"  @click="setContainerResponsiveSetting(selectedNode.settings,'alignItems','flex-end')"  title="End"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignItems','flex-start')==='stretch'}"   @click="setContainerResponsiveSetting(selectedNode.settings,'alignItems','stretch')"   title="Stretch"><i class="fas fa-expand-arrows-alt"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label">Gaps</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-gaps')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('container-gaps')" class="pb-control-device-menu">
													<button v-for="device in responsiveDevices" :key="'container-gaps-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-gaps', device.value)">
														<i :class="device.icon"></i>
														<span>{{ deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<div class="pb-control-unit-wrap">px</div>
										</div>
									</div>
									<div class="pb-gap-row pb-gap-row-with-link">
										<div class="pb-gap-field"><input class="pb-input" :value="containerResponsiveValue(selectedNode.settings,'flexColumnGap',selectedNode.settings.flexColumnGap || '0')" @input="setContainerResponsiveSetting(selectedNode.settings,'flexColumnGap',$event.target.value); syncContainerGap(selectedNode.settings, 'column')" placeholder="20px"><span>Column</span></div>
										<div class="pb-gap-field"><input class="pb-input" :value="containerResponsiveValue(selectedNode.settings,'flexRowGap',selectedNode.settings.flexRowGap || '0')" @input="setContainerResponsiveSetting(selectedNode.settings,'flexRowGap',$event.target.value); syncContainerGap(selectedNode.settings, 'row')" placeholder="20px"><span>Row</span></div>
										<button class="pb-link-btn" @click="selectedNode.settings.containerGapLinked=!selectedNode.settings.containerGapLinked" :title="selectedNode.settings.containerGapLinked?'Unlink':'Link'"><i :class="selectedNode.settings.containerGapLinked?'fas fa-link':'fas fa-unlink'"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Wrap</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-wrap')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-wrap')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-wrap-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-wrap', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'flexWrap','nowrap')==='nowrap'}" @click="setContainerResponsiveSetting(selectedNode.settings,'flexWrap','nowrap')" title="No Wrap"><i class="fas fa-long-arrow-alt-right"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'flexWrap','nowrap')==='wrap'}" @click="setContainerResponsiveSetting(selectedNode.settings,'flexWrap','wrap')" title="Wrap"><i class="fas fa-level-down-alt"></i></button>
									</div>
									<div class="pb-form-note">Items within the container can stay in a single line (No wrap), or break into multiple lines (Wrap).</div>
								</div>
								<div class="pb-form-group" v-if="containerResponsiveValue(selectedNode.settings,'flexWrap','nowrap')==='wrap' || containerResponsiveValue(selectedNode.settings,'flexWrap','nowrap')==='wrap-reverse'">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Content</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-align-content')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-align-content')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-align-content-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-align-content', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignContent','stretch')==='flex-start'}"    @click="setContainerResponsiveSetting(selectedNode.settings,'alignContent','flex-start')"    title="Start"><i class="fas fa-align-left"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignContent','stretch')==='center'}"        @click="setContainerResponsiveSetting(selectedNode.settings,'alignContent','center')"        title="Center"><i class="fas fa-align-center"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignContent','stretch')==='flex-end'}"      @click="setContainerResponsiveSetting(selectedNode.settings,'alignContent','flex-end')"      title="End"><i class="fas fa-align-right"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignContent','stretch')==='space-between'}" @click="setContainerResponsiveSetting(selectedNode.settings,'alignContent','space-between')" title="Space Between"><i class="fas fa-arrows-alt-h"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignContent','stretch')==='space-around'}"  @click="setContainerResponsiveSetting(selectedNode.settings,'alignContent','space-around')"  title="Space Around"><i class="fas fa-grip-lines-vertical"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'alignContent','stretch')==='stretch'}"       @click="setContainerResponsiveSetting(selectedNode.settings,'alignContent','stretch')"       title="Stretch"><i class="fas fa-arrows-alt-h"></i></button>
									</div>
								</div>
								</div>
							</details>
							<details class="pb-collapsible" v-if="selectedNode.settings.displayType==='grid'" open>
								<summary>Items</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Grid Outline</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerGridOutline-' + selectedNode.id" v-model="selectedNode.settings.gridOutline"><label :for="'containerGridOutline-' + selectedNode.id"></label></div></div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Columns</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-grid-columns')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('container-grid-columns')" class="pb-control-device-menu">
													<button v-for="device in responsiveDevices" :key="'container-grid-columns-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-grid-columns', device.value)">
														<i :class="device.icon"></i>
														<span>{{ deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<div class="pb-control-unit-wrap">fr</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="1" max="12" step="1" :value="containerGridColumnsValue(selectedNode)" @input="setContainerGridColumnsValue(selectedNode, $event.target.value)">
										<input class="pb-input pb-input-compact" type="number" min="1" max="12" step="1" :value="containerGridColumnsValue(selectedNode)" @input="setContainerGridColumnsValue(selectedNode, $event.target.value)">
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Rows</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-grid-rows')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('container-grid-rows')" class="pb-control-device-menu">
													<button v-for="device in responsiveDevices" :key="'container-grid-rows-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-grid-rows', device.value)">
														<i :class="device.icon"></i>
														<span>{{ deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<div class="pb-control-unit-wrap">rows</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="1" max="12" step="1" :value="containerGridRowsValue(selectedNode)" @input="setContainerGridRowsValue(selectedNode, $event.target.value)">
										<input class="pb-input pb-input-compact" type="number" min="1" max="12" step="1" :value="containerGridRowsValue(selectedNode)" @input="setContainerGridRowsValue(selectedNode, $event.target.value)">
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label">Gaps</label>
										<div class="pb-label-tools">
											<div class="pb-control-device-wrap">
												<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-grid-gaps')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('container-grid-gaps')" class="pb-control-device-menu">
													<button v-for="device in responsiveDevices" :key="'container-grid-gaps-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-grid-gaps', device.value)">
														<i :class="device.icon"></i>
														<span>{{ deviceOptionLabel(device) }}</span>
													</button>
												</div>
											</div>
											<div class="pb-control-unit-wrap">px</div>
										</div>
									</div>
									<div class="pb-gap-row pb-gap-row-with-link">
										<div class="pb-gap-field"><input class="pb-input" :value="containerResponsiveValue(selectedNode.settings,'gridColumnGap',selectedNode.settings.gridColumnGap || '10px')" @input="setContainerResponsiveSetting(selectedNode.settings,'gridColumnGap',$event.target.value); syncContainerGap(selectedNode.settings, 'gridColumn')" placeholder="20px"><span>Column</span></div>
										<div class="pb-gap-field"><input class="pb-input" :value="containerResponsiveValue(selectedNode.settings,'gridRowGap',selectedNode.settings.gridRowGap || '10px')" @input="setContainerResponsiveSetting(selectedNode.settings,'gridRowGap',$event.target.value); syncContainerGap(selectedNode.settings, 'gridRow')" placeholder="20px"><span>Row</span></div>
										<button class="pb-link-btn" @click="selectedNode.settings.containerGapLinked=!selectedNode.settings.containerGapLinked" :title="selectedNode.settings.containerGapLinked?'Unlink':'Link'"><i :class="selectedNode.settings.containerGapLinked?'fas fa-link':'fas fa-unlink'"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Auto Flow</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-grid-auto-flow')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-grid-auto-flow')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-grid-auto-flow-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-grid-auto-flow', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<select class="pb-select" :value="containerResponsiveValue(selectedNode.settings,'autoFlow',selectedNode.settings.autoFlow || 'row')" @change="setContainerResponsiveSetting(selectedNode.settings,'autoFlow',$event.target.value)">
										<option value="row">Row</option>
										<option value="column">Column</option>
										<option value="dense">Dense</option>
									</select>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Justify Items</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-grid-justify-items')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-grid-justify-items')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-grid-justify-items-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-grid-justify-items', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridJustifyItems','stretch')==='start'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridJustifyItems','start')" title="Start"><i class="fas fa-align-left"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridJustifyItems','stretch')==='center'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridJustifyItems','center')" title="Center"><i class="fas fa-align-center"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridJustifyItems','stretch')==='end'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridJustifyItems','end')" title="End"><i class="fas fa-align-right"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridJustifyItems','stretch')==='stretch'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridJustifyItems','stretch')" title="Stretch"><i class="fas fa-arrows-alt-h"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Items</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-grid-align-items')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-grid-align-items')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-grid-align-items-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-grid-align-items', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridAlignItems','start')==='start'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridAlignItems','start')" title="Start"><i class="fas fa-arrow-up"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridAlignItems','start')==='center'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridAlignItems','center')" title="Center"><i class="fas fa-arrows-alt-v"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridAlignItems','start')==='end'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridAlignItems','end')" title="End"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings,'gridAlignItems','start')==='stretch'}" @click="setContainerResponsiveSetting(selectedNode.settings,'gridAlignItems','stretch')" title="Stretch"><i class="fas fa-expand-arrows-alt"></i></button>
									</div>
								</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Additional Options</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<label class="pb-form-label">Overflow</label>
										<select class="pb-select" v-model="selectedNode.settings.overflow">
											<option value="default">Default</option>
											<option value="visible">Visible</option>
											<option value="hidden">Hidden</option>
											<option value="auto">Auto</option>
											<option value="scroll">Scroll</option>
										</select>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">HTML Tag</label>
										<select class="pb-select" v-model="selectedNode.settings.htmlTag">
											<option value="default">Default</option>
											<option value="div">DIV</option>
											<option value="section">SECTION</option>
											<option value="header">HEADER</option>
											<option value="main">MAIN</option>
											<option value="article">ARTICLE</option>
											<option value="aside">ASIDE</option>
											<option value="footer">FOOTER</option>
											<option value="nav">NAV</option>
										</select>
									</div>
								</div>
							</details>
						</div><!-- /tab layout container -->
						<!-- TAB STYLE -->
						<div v-show="settingsTab==='style'" class="pb-tab-content pb-layout-settings__tab">
							<details class="pb-collapsible" open>
								<summary>Background</summary>
								<div class="pb-collapsible-body">
								<div class="pb-mini-tab-nav">
									<button class="pb-mini-tab" :class="{active:(selectedNode.settings.bgState||'normal')==='normal'}" @click.prevent="setBgState(selectedNode, 'normal')">Normal</button>
									<button class="pb-mini-tab" :class="{active:selectedNode.settings.bgState==='hover'}" @click.prevent="setBgState(selectedNode, 'hover')">Hover</button>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Background Type</label>
									<div class="pb-btn-group pb-icon-btn-group" :class="{ 'pb-icon-btn-group-hover': isBgHoverState(selectedNode) }">
										<button class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='color'}" @click.prevent="setBgTypeForState(selectedNode, 'color')" title="Classic">
											<i class="fas fa-brush"></i>
										</button>
										<button class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='gradient'}" @click.prevent="setBgTypeForState(selectedNode, 'gradient')" title="Gradient">
											<i class="fas fa-fill-drip"></i>
										</button>
										<button v-if="!isBgHoverState(selectedNode)" class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='image'}" @click.prevent="setBgTypeForState(selectedNode, 'image')" title="Image">
											<i class="far fa-image"></i>
										</button>
										<button v-if="!isBgHoverState(selectedNode)" class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='none'}" @click.prevent="setBgTypeForState(selectedNode, 'none')" title="None">
											<i class="fas fa-ban"></i>
										</button>
									</div>
								</div>
								<template v-if="selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='color'">
									<div class="pb-form-group">
										<label class="pb-form-label">Color</label>
										<div class="pb-color-row">
											<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgColor')]">
											<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgColor')]" placeholder="#ffffff">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round((selectedNode.settings[bgStateKey(selectedNode,'bgOpacity')] ?? 1)*100) }}%</span></label>
										<input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'bgOpacity')]">
									</div>
								</template>
								<template v-if="selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='gradient'">
									<div class="pb-form-group">
										<label class="pb-form-label">Gradient Type</label>
										<div class="pb-btn-group">
											<button class="pb-seg-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgGradientType')]==='linear'}" @click="selectedNode.settings[bgStateKey(selectedNode,'bgGradientType')]='linear'">Linear</button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgGradientType')]==='radial'}" @click="selectedNode.settings[bgStateKey(selectedNode,'bgGradientType')]='radial'">Radial</button>
										</div>
									</div>
									<div class="pb-form-group" v-if="selectedNode.settings[bgStateKey(selectedNode,'bgGradientType')]==='linear'">
										<label class="pb-form-label">Angle <span class="pb-form-hint">{{ selectedNode.settings[bgStateKey(selectedNode,'bgGradientAngle')] ?? 90 }}&deg;</span></label>
										<input type="range" class="pb-range" min="0" max="360" step="1" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'bgGradientAngle')]">
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Start Color</label>
										<div class="pb-color-row">
											<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgGradientStart')]">
											<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgGradientStart')]">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">End Color</label>
										<div class="pb-color-row">
											<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgGradientEnd')]">
											<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgGradientEnd')]">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Position <span class="pb-form-hint">{{ selectedNode.settings[bgStateKey(selectedNode,'bgGradientPosition')] ?? 50 }}%</span></label>
										<input type="range" class="pb-range" min="0" max="100" step="1" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'bgGradientPosition')]">
									</div>
								</template>
								<template v-if="selectedNode.settings[bgStateKey(selectedNode,'bgType')]==='image'">
									<div class="pb-form-group">
										<label class="pb-form-label">Image</label>
										<div class="pb-bg-media-field" :class="{ 'has-image': !!selectedNode.settings[bgStateKey(selectedNode,'bgImage')] }">
											<div class="pb-bg-media-preview" :style="selectedNode.settings[bgStateKey(selectedNode,'bgImage')] ? { backgroundImage: 'url(' + selectedNode.settings[bgStateKey(selectedNode,'bgImage')] + ')' } : {}">
												<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings[bgStateKey(selectedNode,'bgImage')] ? 'Change Image' : 'Choose Image'" @click="chooseBgImage(selectedNode, bgStateKey(selectedNode,'bgImage'))">
													<i :class="selectedNode.settings[bgStateKey(selectedNode,'bgImage')] ? 'fas fa-pen' : 'fas fa-plus'"></i>
												</button>
											</div>
											<div class="pb-bg-media-actions">
												<button type="button" class="pb-bg-media-choose" @click="chooseBgImage(selectedNode, bgStateKey(selectedNode,'bgImage'))">Choose Image</button>
												<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings[bgStateKey(selectedNode,'bgImage')]" title="Remove Image" @click="clearBgImage(selectedNode, bgStateKey(selectedNode,'bgImage'))">
													<i class="fas fa-trash-alt"></i>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgSize')]"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgPosition')]"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgRepeat')]"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgAttachment')]"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
								</template>
								<div class="pb-form-group" v-if="isBgHoverState(selectedNode)">
									<label class="pb-form-label">Transition Duration <span class="pb-form-hint">{{ selectedNode.settings.bgTransitionDuration ?? 300 }}ms</span></label>
									<input type="range" class="pb-range" min="0" max="3000" step="50" v-model.number="selectedNode.settings.bgTransitionDuration">
								</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Background Overlay</summary>
								<div class="pb-collapsible-body">
									<div class="pb-mini-tab-nav">
										<button class="pb-mini-tab" :class="{active:(selectedNode.settings.bgState||'normal')==='normal'}" @click.prevent="setBgState(selectedNode, 'normal')">Normal</button>
										<button class="pb-mini-tab" :class="{active:selectedNode.settings.bgState==='hover'}" @click.prevent="setBgState(selectedNode, 'hover')">Hover</button>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Background Type</label>
										<div class="pb-btn-group pb-icon-btn-group">
											<button class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='color'}" @click.prevent="setBgOverlayTypeForState(selectedNode, 'color')" title="Classic"><i class="fas fa-brush"></i></button>
											<button class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='gradient'}" @click.prevent="setBgOverlayTypeForState(selectedNode, 'gradient')" title="Gradient"><i class="fas fa-fill-drip"></i></button>
											<button class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='image'}" @click.prevent="setBgOverlayTypeForState(selectedNode, 'image')" title="Image"><i class="far fa-image"></i></button>
											<button class="pb-seg-btn pb-icon-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='none'}" @click.prevent="setBgOverlayTypeForState(selectedNode, 'none')" title="None"><i class="fas fa-ban"></i></button>
										</div>
									</div>
									<template v-if="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='color'">
										<div class="pb-form-group">
											<label class="pb-form-label">Color</label>
											<div class="pb-color-row">
												<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayColor')]">
												<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayColor')]" placeholder="#000000">
											</div>
										</div>
									</template>
									<template v-if="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='gradient'">
										<div class="pb-form-group">
											<label class="pb-form-label">Gradient Type</label>
											<div class="pb-btn-group">
												<button class="pb-seg-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientType')]==='linear'}" @click="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientType')]='linear'">Linear</button>
												<button class="pb-seg-btn" :class="{active:selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientType')]==='radial'}" @click="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientType')]='radial'">Radial</button>
											</div>
										</div>
										<div class="pb-form-group" v-if="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientType')]==='linear'">
											<label class="pb-form-label">Angle <span class="pb-form-hint">{{ selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientAngle')] ?? 180 }}&deg;</span></label>
											<input type="range" class="pb-range" min="0" max="360" step="1" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientAngle')]">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Start Color</label>
											<div class="pb-color-row">
												<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientStart')]">
												<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientStart')]">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">End Color</label>
											<div class="pb-color-row">
												<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientEnd')]">
												<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientEnd')]">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Position <span class="pb-form-hint">{{ selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientPosition')] ?? 100 }}%</span></label>
											<input type="range" class="pb-range" min="0" max="100" step="1" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayGradientPosition')]">
										</div>
									</template>
									<template v-if="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]==='image'">
										<div class="pb-form-group">
											<label class="pb-form-label">Image</label>
											<div class="pb-bg-media-field" :class="{ 'has-image': !!selectedNode.settings[bgStateKey(selectedNode,'bgOverlayImage')] }">
												<div class="pb-bg-media-preview" :style="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayImage')] ? { backgroundImage: 'url(' + selectedNode.settings[bgStateKey(selectedNode,'bgOverlayImage')] + ')' } : {}">
													<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayImage')] ? 'Change Image' : 'Choose Image'" @click="chooseBgImage(selectedNode, bgStateKey(selectedNode,'bgOverlayImage'))">
														<i :class="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayImage')] ? 'fas fa-pen' : 'fas fa-plus'"></i>
													</button>
												</div>
												<div class="pb-bg-media-actions">
													<button type="button" class="pb-bg-media-choose" @click="chooseBgImage(selectedNode, bgStateKey(selectedNode,'bgOverlayImage'))">Choose Image</button>
													<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings[bgStateKey(selectedNode,'bgOverlayImage')]" title="Remove Image" @click="clearBgImage(selectedNode, bgStateKey(selectedNode,'bgOverlayImage'))"><i class="fas fa-trash-alt"></i></button>
												</div>
											</div>
										</div>
										<div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlaySize')]"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
										<div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayPosition')]"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
										<div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayRepeat')]"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
										<div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayAttachment')]"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
									</template>
									<div class="pb-form-group" v-if="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]!=='none'">
										<label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round((selectedNode.settings[bgStateKey(selectedNode,'bgOverlayOpacity')] ?? 0.5) * 100) }}%</span></label>
										<input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayOpacity')]">
									</div>
									<div class="pb-form-group" v-if="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayType')]!=='none'">
										<label class="pb-form-label">Blend Mode</label>
										<select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'bgOverlayBlendMode')]">
											<option value="normal">Normal</option>
											<option value="multiply">Multiply</option>
											<option value="screen">Screen</option>
											<option value="overlay">Overlay</option>
											<option value="darken">Darken</option>
											<option value="lighten">Lighten</option>
											<option value="color-dodge">Color Dodge</option>
											<option value="saturation">Saturation</option>
											<option value="color">Color</option>
											<option value="luminosity">Luminosity</option>
										</select>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Border</summary>
								<div class="pb-collapsible-body">
									<div class="pb-mini-tab-nav">
										<button class="pb-mini-tab" :class="{active:(selectedNode.settings.bgState||'normal')==='normal'}" @click.prevent="setBgState(selectedNode, 'normal')">Normal</button>
										<button class="pb-mini-tab" :class="{active:selectedNode.settings.bgState==='hover'}" @click.prevent="setBgState(selectedNode, 'hover')">Hover</button>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="selectedNode.settings[bgStateKey(selectedNode,'borderType')]"><option value="none">None</option><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option><option value="double">Double</option></select></div>
									<template v-if="selectedNode.settings[bgStateKey(selectedNode,'borderType')]!=='none'">
										<div class="pb-form-group"><label class="pb-form-label">Border Width</label><input class="pb-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'borderWidth')]" placeholder="1px"></div>
										<div class="pb-form-group"><label class="pb-form-label">Border Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'borderColor')]"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'borderColor')]"></div></div>
									</template>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label">Border Radius</label><button class="pb-link-btn" @click="selectedNode.settings.borderRadiusLinked=!selectedNode.settings.borderRadiusLinked" :title="selectedNode.settings.borderRadiusLinked?'Unlink':'Link'"><i :class="selectedNode.settings.borderRadiusLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
										<div class="pb-four-sides">
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusTL" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTR=selectedNode.settings.borderRadiusBR=selectedNode.settings.borderRadiusBL=selectedNode.settings.borderRadiusTL)" placeholder="0"><span>Top</span></div>
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusTR" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTL=selectedNode.settings.borderRadiusBR=selectedNode.settings.borderRadiusBL=selectedNode.settings.borderRadiusTR)" placeholder="0"><span>Right</span></div>
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusBR" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTL=selectedNode.settings.borderRadiusTR=selectedNode.settings.borderRadiusBL=selectedNode.settings.borderRadiusBR)" placeholder="0"><span>Bottom</span></div>
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusBL" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTL=selectedNode.settings.borderRadiusTR=selectedNode.settings.borderRadiusBR=selectedNode.settings.borderRadiusBL)" placeholder="0"><span>Left</span></div>
										</div>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Box Shadow</summary>
								<div class="pb-collapsible-body">
									<div class="pb-mini-tab-nav">
										<button class="pb-mini-tab" :class="{active:(selectedNode.settings.bgState||'normal')==='normal'}" @click.prevent="setBgState(selectedNode, 'normal')">Normal</button>
										<button class="pb-mini-tab" :class="{active:selectedNode.settings.bgState==='hover'}" @click.prevent="setBgState(selectedNode, 'hover')">Hover</button>
									</div>
									<div class="pb-label-row"><label class="pb-form-label mb-0">Enable Box Shadow</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerShadowEnable-' + selectedNode.id + '-' + (selectedNode.settings.bgState==='hover' ? 'hover' : 'normal')" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowEnabled')]"><label :for="'containerShadowEnable-' + selectedNode.id + '-' + (selectedNode.settings.bgState==='hover' ? 'hover' : 'normal')"></label></div></div>
									<template v-if="selectedNode.settings[bgStateKey(selectedNode,'shadowEnabled')]">
										<div class="pb-four-sides mt-2">
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowH')]" placeholder="0"><span>H</span></div>
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowV')]" placeholder="0"><span>V</span></div>
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowBlur')]" placeholder="0"><span>Blur</span></div>
											<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowSpread')]" placeholder="0"><span>Spread</span></div>
										</div>
										<div class="pb-form-group mt-2"><label class="pb-form-label">Shadow Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowColor')]"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[bgStateKey(selectedNode,'shadowColor')]"></div></div>
										<div class="pb-form-group"><label class="pb-form-label">Shadow Opacity <span class="pb-form-hint">{{ Math.round((selectedNode.settings[bgStateKey(selectedNode,'shadowOpacity')] ?? 0.3)*100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="selectedNode.settings[bgStateKey(selectedNode,'shadowOpacity')]"></div>
									</template>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Shape Divider</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<label class="pb-form-label">Side</label>
										<div class="pb-btn-group">
											<button class="pb-seg-btn" :class="{active:(selectedNode.settings.shapeDividerSide||'top')==='top'}" @click="selectedNode.settings.shapeDividerSide='top'">Top</button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.shapeDividerSide==='bottom'}" @click="selectedNode.settings.shapeDividerSide='bottom'">Bottom</button>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Type</label>
										<select class="pb-select" v-model="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomType':'shapeDividerTopType')]">
											<option v-for="option in shapeDividerTypeOptions" :key="'shape-divider-' + option.value" :value="option.value">{{ option.label }}</option>
										</select>
									</div>
									<template v-if="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomType':'shapeDividerTopType')]!=='none'">
										<div class="pb-form-group">
											<label class="pb-form-label">Color</label>
											<div class="pb-color-row">
												<input type="color" class="pb-color-swatch" v-model="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomColor':'shapeDividerTopColor')]">
												<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomColor':'shapeDividerTopColor')]">
											</div>
										</div>
										<div class="pb-form-group" v-if="shapeDividerHasWidth(selectedNode)">
											<label class="pb-form-label">Width</label>
											<div class="pb-range-value-row">
												<input type="range" class="pb-range" min="0" max="300" step="1" :value="shapeDividerWidthValue(selectedNode)" @input="setShapeDividerWidthValue(selectedNode, $event.target.value)">
												<div class="pb-value-with-unit">
													<input class="pb-input pb-input-compact" type="number" min="0" max="300" step="1" :value="shapeDividerWidthValue(selectedNode)" @input="setShapeDividerWidthValue(selectedNode, $event.target.value)">
													<select class="pb-mini-unit" :value="shapeDividerWidthUnit(selectedNode)" @change="setShapeDividerWidthUnit(selectedNode, $event.target.value)">
														<option value="%">%</option>
													</select>
												</div>
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Height</label>
											<div class="pb-range-value-row">
												<input type="range" class="pb-range" min="0" max="500" step="1" :value="shapeDividerHeightValue(selectedNode)" @input="setShapeDividerHeightValue(selectedNode, $event.target.value)">
												<div class="pb-value-with-unit">
													<input class="pb-input pb-input-compact" type="number" min="0" max="500" step="1" :value="shapeDividerHeightValue(selectedNode)" @input="setShapeDividerHeightValue(selectedNode, $event.target.value)">
													<select class="pb-mini-unit" :value="shapeDividerHeightUnit(selectedNode)" @change="setShapeDividerHeightUnit(selectedNode, $event.target.value)">
														<option value="px">px</option>
													</select>
												</div>
											</div>
										</div>
										<div class="pb-form-group pb-toggle-label-row" v-if="shapeDividerHasFlip(selectedNode)">
											<label class="pb-form-label mb-0">Flip</label>
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'shapeDividerFlip-' + selectedNode.id" v-model="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomFlip':'shapeDividerTopFlip')]"><label :for="'shapeDividerFlip-' + selectedNode.id"></label></div>
										</div>
										<div class="pb-form-group pb-toggle-label-row" v-if="shapeDividerHasInvert(selectedNode)">
											<label class="pb-form-label mb-0">Invert</label>
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'shapeDividerInvert-' + selectedNode.id" v-model="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomNegative':'shapeDividerTopNegative')]"><label :for="'shapeDividerInvert-' + selectedNode.id"></label></div>
										</div>
										<div class="pb-form-group pb-toggle-label-row">
											<label class="pb-form-label mb-0">Bring to Front</label>
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'shapeDividerFront-' + selectedNode.id" v-model="selectedNode.settings[(selectedNode.settings.shapeDividerSide==='bottom'?'shapeDividerBottomFront':'shapeDividerTopFront')]"><label :for="'shapeDividerFront-' + selectedNode.id"></label></div>
										</div>
									</template>
								</div>
							</details>
						</div><!-- /tab style container -->
						<!-- TAB ADVANCED -->
						<div v-show="settingsTab==='advanced'" class="pb-tab-content pb-layout-settings__tab">
							<details class="pb-collapsible" open>
								<summary>Layout</summary>
								<div class="pb-collapsible-body">
								<div class="pb-form-group pb-spacing-control-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Margin</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-margin')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-margin')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-margin-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-margin', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
										<div class="pb-label-tools">
											<select class="pb-mini-unit pb-edge-unit-select" :value="spacingUnit(selectedNode, 'margin')" @change="setSpacingUnit(selectedNode, 'margin', $event.target.value)">
												<option v-for="unit in spacingControlUnits" :key="'container-margin-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1">
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'margin', 'Top')" @input="onSpacingSideInput(selectedNode, 'margin', 'Top', $event)" placeholder=""><span>Top</span></div>
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'margin', 'Right')" @input="onSpacingSideInput(selectedNode, 'margin', 'Right', $event)" placeholder=""><span>Right</span></div>
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'margin', 'Bottom')" @input="onSpacingSideInput(selectedNode, 'margin', 'Bottom', $event)" placeholder=""><span>Bottom</span></div>
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'margin', 'Left')" @input="onSpacingSideInput(selectedNode, 'margin', 'Left', $event)" placeholder=""><span>Left</span></div>
										<div class="pb-side-link-cell"><button class="pb-link-btn" @click="selectedNode.settings.marginLinked=!selectedNode.settings.marginLinked" :title="selectedNode.settings.marginLinked?'Unlink':'Link'"><i :class="selectedNode.settings.marginLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									</div>
								</div>
								<div class="pb-form-group pb-spacing-control-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Padding</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-padding')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-padding')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-padding-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-padding', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
										<div class="pb-label-tools">
											<select class="pb-mini-unit pb-edge-unit-select" :value="spacingUnit(selectedNode, 'padding')" @change="setSpacingUnit(selectedNode, 'padding', $event.target.value)">
												<option v-for="unit in spacingControlUnits" :key="'container-padding-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-four-sides pb-four-sides-with-link mt-1">
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'padding', 'Top')" @input="onSpacingSideInput(selectedNode, 'padding', 'Top', $event)" placeholder=""><span>Top</span></div>
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'padding', 'Right')" @input="onSpacingSideInput(selectedNode, 'padding', 'Right', $event)" placeholder=""><span>Right</span></div>
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'padding', 'Bottom')" @input="onSpacingSideInput(selectedNode, 'padding', 'Bottom', $event)" placeholder=""><span>Bottom</span></div>
										<div class="pb-side-input"><input class="pb-input" :value="spacingSideValue(selectedNode, 'padding', 'Left')" @input="onSpacingSideInput(selectedNode, 'padding', 'Left', $event)" placeholder=""><span>Left</span></div>
										<div class="pb-side-link-cell"><button class="pb-link-btn" @click="selectedNode.settings.paddingLinked=!selectedNode.settings.paddingLinked" :title="selectedNode.settings.paddingLinked?'Unlink':'Link'"><i :class="selectedNode.settings.paddingLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Align Self</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-align-self')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-align-self')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-align-self-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-align-self', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'alignSelf', 'auto')==='auto'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'alignSelf', 'auto')" title="Auto"><i class="fas fa-minus"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'alignSelf', 'auto')==='flex-start'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'alignSelf', 'flex-start')" title="Start"><i class="fas fa-arrow-up"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'alignSelf', 'auto')==='center'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'alignSelf', 'center')" title="Center"><i class="fas fa-arrows-alt-v"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'alignSelf', 'auto')==='flex-end'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'alignSelf', 'flex-end')" title="End"><i class="fas fa-arrow-down"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'alignSelf', 'auto')==='stretch'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'alignSelf', 'stretch')" title="Stretch"><i class="fas fa-expand-arrows-alt"></i></button>
									</div>
									<div class="pb-form-note">This control affects this container inside its parent layout.</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Order</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-order')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-order')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-order-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-order', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'order', '')==='-1'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'order', '-1')" title="Start"><i class="fas fa-arrow-left"></i></button>
										<button class="pb-seg-btn" :class="{active:['', null, 'default'].includes(containerResponsiveValue(selectedNode.settings, 'order', ''))}" @click="setContainerResponsiveSetting(selectedNode.settings, 'order', 'default')" title="Default"><i class="fas fa-ellipsis-v"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'order', '')==='1'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'order', '1')" title="End"><i class="fas fa-arrow-right"></i></button>
									</div>
									<div class="pb-form-note">This control affects this container inside its parent layout.</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Size</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('container-size')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('container-size')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'container-size-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('container-size', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'sizeMode', 'default')==='default'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'sizeMode', 'default')" title="Default"><i class="fas fa-ban"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'sizeMode', 'default')==='grow'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'sizeMode', 'grow')" title="Grow"><i class="fas fa-arrows-alt-h"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'sizeMode', 'default')==='shrink'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'sizeMode', 'shrink')" title="Shrink"><i class="fas fa-compress"></i></button>
										<button class="pb-seg-btn" :class="{active:containerResponsiveValue(selectedNode.settings, 'sizeMode', 'default')==='custom'}" @click="setContainerResponsiveSetting(selectedNode.settings, 'sizeMode', 'custom')" title="Custom"><i class="fas fa-ellipsis-h"></i></button>
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Position</label>
									<select class="pb-select" v-model="selectedNode.settings.position">
										<option value="default">Default</option>
										<option value="relative">Relative</option>
										<option value="absolute">Absolute</option>
										<option value="fixed">Fixed</option>
										<option value="sticky">Sticky</option>
									</select>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row">
										<label class="pb-form-label mb-0">Z-Index</label>
										<div class="pb-control-unit-wrap"></div>
									</div>
									<input class="pb-input pb-input-compact" v-model="selectedNode.settings.zIndex" type="number" placeholder="">
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">CSS ID</label>
									<input class="pb-input" v-model="selectedNode.settings.cssId" placeholder="">
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">CSS Classes</label>
									<input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="">
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row">
										<label class="pb-form-label mb-0">Display Conditions</label>
										<button class="pb-field-action-btn" type="button" title="Display Conditions" @click="showUnsupportedControlNotice('Display Conditions', 'Display Conditions panel belum tersedia di builder ini. Untuk saat ini yang aktif baru hide per device.')"><i class="fas fa-sitemap"></i></button>
									</div>
									<div class="pb-advanced-switch-grid">
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Hide Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerHideDesktop-' + selectedNode.id" v-model="selectedNode.settings.hideDesktop"><label :for="'containerHideDesktop-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Hide Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerHideTablet-' + selectedNode.id" v-model="selectedNode.settings.hideTablet"><label :for="'containerHideTablet-' + selectedNode.id"></label></div></div>
										</div>
											<div class="pb-form-group">
												<div class="pb-label-row"><label class="pb-form-label mb-0">Hide Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerHideMobile-' + selectedNode.id" v-model="selectedNode.settings.hideMobile"><label :for="'containerHideMobile-' + selectedNode.id"></label></div></div>
											</div>
										</div>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Motion Effects</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<div class="pb-inline-action-row" role="button" tabindex="0" title="Animate With AI" @click="showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')" @keydown.enter.prevent="showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')" @keydown.space.prevent="showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')">
											<label class="pb-form-label mb-0">Animate With AI</label>
										</div>
									</div>
									<div class="pb-form-group pb-toggle-label-row">
										<label class="pb-form-label mb-0">Scrolling Effects</label>
										<div class="pb-toggle-switch-wrap">
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollEffects-' + selectedNode.id" v-model="selectedNode.settings.scrollingEffects"><label :for="'containerScrollEffects-' + selectedNode.id"></label></div>
											<span class="pb-toggle-state">{{ selectedNode.settings.scrollingEffects ? 'on' : 'off' }}</span>
										</div>
									</div>
									<template v-if="selectedNode.settings.scrollingEffects">
										<div class="pb-form-group">
											<label class="pb-form-label">Scroll Effect Type</label>
											<select class="pb-select" v-model="selectedNode.settings.scrollEffectType">
												<option value="vertical">Vertical</option>
												<option value="horizontal">Horizontal</option>
												<option value="transparency">Transparency</option>
												<option value="blur">Blur</option>
												<option value="rotate">Rotate</option>
												<option value="scale">Scale</option>
											</select>
										</div>
										<div class="pb-gap-row">
											<div class="pb-gap-field">
												<label class="pb-form-label">Direction</label>
												<select class="pb-select" v-model="selectedNode.settings.scrollDirection">
													<option value="up">Up</option>
													<option value="down">Down</option>
													<option value="left">Left</option>
													<option value="right">Right</option>
													<option value="in">In</option>
													<option value="out">Out</option>
												</select>
											</div>
											<div class="pb-gap-field">
												<label class="pb-form-label">Speed</label>
												<input class="pb-input" type="number" min="0" max="10" step="0.1" v-model.number="selectedNode.settings.scrollSpeed">
											</div>
										</div>
										<div class="pb-gap-row">
											<div class="pb-gap-field">
												<label class="pb-form-label">Viewport Start</label>
												<input class="pb-input" type="number" min="0" max="100" step="1" v-model.number="selectedNode.settings.scrollViewportStart">
											</div>
											<div class="pb-gap-field">
												<label class="pb-form-label">Viewport End</label>
												<input class="pb-input" type="number" min="0" max="100" step="1" v-model.number="selectedNode.settings.scrollViewportEnd">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Relative To</label>
											<select class="pb-select" v-model="selectedNode.settings.scrollRelativeTo">
												<option value="default">Default</option>
												<option value="viewport">Viewport</option>
											</select>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollDesktop-' + selectedNode.id" v-model="selectedNode.settings.scrollApplyDesktop"><label :for="'containerScrollDesktop-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollTablet-' + selectedNode.id" v-model="selectedNode.settings.scrollApplyTablet"><label :for="'containerScrollTablet-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerScrollMobile-' + selectedNode.id" v-model="selectedNode.settings.scrollApplyMobile"><label :for="'containerScrollMobile-' + selectedNode.id"></label></div></div>
										</div>
									</template>
									<div class="pb-form-group pb-toggle-label-row">
										<label class="pb-form-label mb-0">Mouse Effects</label>
										<div class="pb-toggle-switch-wrap">
											<div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseEffects-' + selectedNode.id" v-model="selectedNode.settings.mouseEffects"><label :for="'containerMouseEffects-' + selectedNode.id"></label></div>
											<span class="pb-toggle-state">{{ selectedNode.settings.mouseEffects ? 'on' : 'off' }}</span>
										</div>
									</div>
									<template v-if="selectedNode.settings.mouseEffects">
										<div class="pb-form-group">
											<label class="pb-form-label">Mouse Effect Type</label>
											<select class="pb-select" v-model="selectedNode.settings.mouseEffectType">
												<option value="track">Track</option>
												<option value="tilt">3D Tilt</option>
												<option value="parallax">Parallax</option>
											</select>
										</div>
										<div class="pb-gap-row">
											<div class="pb-gap-field">
												<label class="pb-form-label">Direction</label>
												<select class="pb-select" v-model="selectedNode.settings.mouseDirection">
													<option value="direct">Direct</option>
													<option value="opposite">Opposite</option>
												</select>
											</div>
											<div class="pb-gap-field">
												<label class="pb-form-label">Speed</label>
												<input class="pb-input" type="number" min="0" max="10" step="0.1" v-model.number="selectedNode.settings.mouseSpeed">
											</div>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Relative To</label>
											<select class="pb-select" v-model="selectedNode.settings.mouseRelativeTo">
												<option value="default">Default</option>
												<option value="viewport">Viewport</option>
											</select>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseDesktop-' + selectedNode.id" v-model="selectedNode.settings.mouseApplyDesktop"><label :for="'containerMouseDesktop-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseTablet-' + selectedNode.id" v-model="selectedNode.settings.mouseApplyTablet"><label :for="'containerMouseTablet-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerMouseMobile-' + selectedNode.id" v-model="selectedNode.settings.mouseApplyMobile"><label :for="'containerMouseMobile-' + selectedNode.id"></label></div></div>
										</div>
									</template>
									<div class="pb-form-group">
										<label class="pb-form-label">Sticky</label>
										<select class="pb-select" v-model="selectedNode.settings.sticky">
											<option value="none">None</option>
											<option value="top">Top</option>
											<option value="bottom">Bottom</option>
										</select>
									</div>
									<template v-if="selectedNode.settings.sticky !== 'none'">
										<div class="pb-gap-row">
											<div class="pb-gap-field"><label class="pb-form-label">Sticky Offset</label><input class="pb-input" v-model="selectedNode.settings.stickyOffset" placeholder="0"></div>
											<div class="pb-gap-field"><label class="pb-form-label">Effects Offset</label><input class="pb-input" v-model="selectedNode.settings.stickyEffectsOffset" placeholder="0"></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerStickyDesktop-' + selectedNode.id" v-model="selectedNode.settings.stickyOnDesktop"><label :for="'containerStickyDesktop-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerStickyTablet-' + selectedNode.id" v-model="selectedNode.settings.stickyOnTablet"><label :for="'containerStickyTablet-' + selectedNode.id"></label></div></div>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'containerStickyMobile-' + selectedNode.id" v-model="selectedNode.settings.stickyOnMobile"><label :for="'containerStickyMobile-' + selectedNode.id"></label></div></div>
										</div>
									</template>
									<div class="pb-form-group">
										<label class="pb-form-label">Entrance Animation</label>
										<select class="pb-select" v-model="selectedNode.settings.entranceAnimation">
											<option value="">None</option>
											<option value="fadeIn">Fade In</option>
											<option value="fadeInUp">Fade In Up</option>
											<option value="fadeInDown">Fade In Down</option>
											<option value="fadeInLeft">Fade In Left</option>
											<option value="fadeInRight">Fade In Right</option>
											<option value="zoomIn">Zoom In</option>
											<option value="slideInUp">Slide In Up</option>
											<option value="slideInDown">Slide In Down</option>
											<option value="slideInLeft">Slide In Left</option>
											<option value="slideInRight">Slide In Right</option>
											<option value="bounceIn">Bounce In</option>
										</select>
									</div>
								</div>
							</details>
							<details class="pb-collapsible">
								<summary>Transform</summary>
								<div class="pb-collapsible-body">
									<div class="pb-gap-row">
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformRotate" placeholder="0deg"><span>Rotate</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformOffsetX" placeholder="0px"><span>Offset X</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformOffsetY" placeholder="0px"><span>Offset Y</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformScaleX" placeholder="1"><span>Scale X</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformScaleY" placeholder="1"><span>Scale Y</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformSkewX" placeholder="0deg"><span>Skew X</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformSkewY" placeholder="0deg"><span>Skew Y</span></div>
									</div>
								</div>
							</details>
						</div><!-- /tab advanced container -->
						</div>
					</template><!-- /container tabs -->

					<!-- ═══ GRID / ROW_GRID TABS ═══ -->
					<template v-if="selectedType==='grid'||selectedType==='row_grid'">
						<div class="pb-grid-settings pb-grid-settings--layout">
							<div class="pb-tab-nav">
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='layout'}"   @click="settingsTab='layout'"><i class="fas fa-th-large"></i><span>Layout</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='style'}"    @click="settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
								<button class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='advanced'}" @click="settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
							</div>
						<!-- TAB LAYOUT (GRID) -->
						<div v-show="settingsTab==='layout'" class="pb-tab-content pb-grid-settings__tab">
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row pb-grid-settings__section-head">
									<div class="pb-prop-section-title mb-0">Grid Layout</div>
									<div class="pb-responsive-switch">
										<button v-for="device in responsiveDevices" :key="device.value" class="pb-device-btn" :class="{active:responsiveDevice===device.value}" @click="setResponsiveDevice(device.value)"><i :class="device.icon"></i><span>{{ device.label }}</span></button>
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Columns <span class="pb-form-hint">1-12</span></label>
									<input class="pb-input" v-model.number="selectedNode.settings[activeResponsiveKey('columns')]" type="number" min="1" max="12" @input="syncGridColumnsForDevice(selectedNode)">
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Grid Auto Height</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridAutoHeight-' + selectedNode.id" v-model="selectedNode.settings.gridAutoHeight"><label :for="'gridAutoHeight-' + selectedNode.id"></label></div></div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Rows</label>
									<input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('gridRows')]" placeholder="auto or 2">
								</div>
								<div class="pb-form-group" v-if="responsiveDevice==='desktop'">
									<label class="pb-form-label">Grid Template Columns</label>
									<input class="pb-input" v-model="selectedNode.settings.gridTemplateColumns" placeholder="repeat(3, minmax(0, 1fr))">
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Column / Row Gap</label><button class="pb-link-btn" @click="selectedNode.settings.gapLinked=!selectedNode.settings.gapLinked" :title="selectedNode.settings.gapLinked?'Unlink':'Link'"><i :class="selectedNode.settings.gapLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									<div class="pb-gap-row mt-1">
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('columnGap')]" @input="syncGridGap(selectedNode.settings, 'columnGap')" placeholder="20px"><span>Column</span></div>
										<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('rowGap')]" @input="syncGridGap(selectedNode.settings, 'rowGap')" placeholder="20px"><span>Row</span></div>
									</div>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Auto Flow</label>
									<select class="pb-select" v-model="selectedNode.settings.autoFlow">
										<option value="row">Row</option>
										<option value="column">Column</option>
										<option value="dense">Dense</option>
									</select>
								</div>
							</div>
						</div>
						<!-- TAB STYLE (GRID) -->
						<div v-show="settingsTab==='style'" class="pb-tab-content pb-grid-settings__tab">
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row pb-grid-settings__section-head">
									<div class="pb-prop-section-title mb-0">Spacing</div>
									<div class="pb-responsive-switch">
										<button v-for="device in responsiveDevices" :key="device.value" class="pb-device-btn" :class="{active:responsiveDevice===device.value}" @click="setResponsiveDevice(device.value)"><i :class="device.icon"></i><span>{{ device.label }}</span></button>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label">Padding</label><button class="pb-link-btn" @click="selectedNode.settings.paddingLinked=!selectedNode.settings.paddingLinked" :title="selectedNode.settings.paddingLinked?'Unlink':'Link'"><i :class="selectedNode.settings.paddingLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									<div class="pb-four-sides mt-1">
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('paddingTop')]" @input="syncResponsiveSides(selectedNode.settings, 'padding', 'Top', selectedNode.settings.paddingLinked)" placeholder="0"><span>Top</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('paddingRight')]" @input="syncResponsiveSides(selectedNode.settings, 'padding', 'Right', selectedNode.settings.paddingLinked)" placeholder="0"><span>Right</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('paddingBottom')]" @input="syncResponsiveSides(selectedNode.settings, 'padding', 'Bottom', selectedNode.settings.paddingLinked)" placeholder="0"><span>Bottom</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('paddingLeft')]" @input="syncResponsiveSides(selectedNode.settings, 'padding', 'Left', selectedNode.settings.paddingLinked)" placeholder="0"><span>Left</span></div>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label">Margin</label><button class="pb-link-btn" @click="selectedNode.settings.marginLinked=!selectedNode.settings.marginLinked" :title="selectedNode.settings.marginLinked?'Unlink':'Link'"><i :class="selectedNode.settings.marginLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									<div class="pb-four-sides mt-1">
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('marginTop')]" @input="syncResponsiveSides(selectedNode.settings, 'margin', 'Top', selectedNode.settings.marginLinked)" placeholder="0"><span>Top</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('marginRight')]" @input="syncResponsiveSides(selectedNode.settings, 'margin', 'Right', selectedNode.settings.marginLinked)" placeholder="0"><span>Right</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('marginBottom')]" @input="syncResponsiveSides(selectedNode.settings, 'margin', 'Bottom', selectedNode.settings.marginLinked)" placeholder="0"><span>Bottom</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings[activeResponsiveKey('marginLeft')]" @input="syncResponsiveSides(selectedNode.settings, 'margin', 'Left', selectedNode.settings.marginLinked)" placeholder="0"><span>Left</span></div>
									</div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Background</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Type</label>
									<div class="pb-btn-group">
										<button class="pb-seg-btn" :class="{active:selectedNode.settings.bgType==='none'}"     @click="selectedNode.settings.bgType='none'">None</button>
										<button class="pb-seg-btn" :class="{active:selectedNode.settings.bgType==='color'}"    @click="selectedNode.settings.bgType='color'">Color</button>
										<button class="pb-seg-btn" :class="{active:selectedNode.settings.bgType==='gradient'}" @click="selectedNode.settings.bgType='gradient'">Gradient</button>
										<button class="pb-seg-btn" :class="{active:selectedNode.settings.bgType==='image'}"    @click="selectedNode.settings.bgType='image'">Image</button>
									</div>
								</div>
								<template v-if="selectedNode.settings.bgType==='color'">
									<div class="pb-form-group"><label class="pb-form-label">Background Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings.bgColor"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings.bgColor" placeholder="#ffffff"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Opacity <span class="pb-form-hint">{{ Math.round((selectedNode.settings.bgOpacity ?? 1)*100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="selectedNode.settings.bgOpacity"></div>
								</template>
								<template v-if="selectedNode.settings.bgType==='gradient'">
									<div class="pb-form-group"><label class="pb-form-label">Gradient Type</label><div class="pb-btn-group"><button class="pb-seg-btn" :class="{active:selectedNode.settings.bgGradientType==='linear'}" @click="selectedNode.settings.bgGradientType='linear'">Linear</button><button class="pb-seg-btn" :class="{active:selectedNode.settings.bgGradientType==='radial'}" @click="selectedNode.settings.bgGradientType='radial'">Radial</button></div></div>
									<div class="pb-form-group" v-if="selectedNode.settings.bgGradientType==='linear'"><label class="pb-form-label">Angle <span class="pb-form-hint">{{ selectedNode.settings.bgGradientAngle ?? 90 }}&deg;</span></label><input type="range" class="pb-range" min="0" max="360" step="1" v-model.number="selectedNode.settings.bgGradientAngle"></div>
									<div class="pb-form-group"><label class="pb-form-label">Start Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings.bgGradientStart"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings.bgGradientStart"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">End Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings.bgGradientEnd"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings.bgGradientEnd"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Position <span class="pb-form-hint">{{ selectedNode.settings.bgGradientPosition ?? 50 }}%</span></label><input type="range" class="pb-range" min="0" max="100" step="1" v-model.number="selectedNode.settings.bgGradientPosition"></div>
								</template>
								<template v-if="selectedNode.settings.bgType==='image'">
									<div class="pb-form-group">
										<label class="pb-form-label">Image</label>
										<div class="pb-bg-media-field" :class="{ 'has-image': !!selectedNode.settings.bgImage }">
											<div class="pb-bg-media-preview" :style="selectedNode.settings.bgImage ? { backgroundImage: 'url(' + selectedNode.settings.bgImage + ')' } : {}">
												<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings.bgImage ? 'Change Image' : 'Choose Image'" @click="chooseBgImage(selectedNode)">
													<i :class="selectedNode.settings.bgImage ? 'fas fa-pen' : 'fas fa-plus'"></i>
												</button>
											</div>
											<div class="pb-bg-media-actions">
												<button type="button" class="pb-bg-media-choose" @click="chooseBgImage(selectedNode)">Choose Image</button>
												<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings.bgImage" title="Remove Image" @click="clearBgImage(selectedNode)">
													<i class="fas fa-trash-alt"></i>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-form-group"><label class="pb-form-label">Image Size</label><select class="pb-select" v-model="selectedNode.settings.bgSize"><option value="cover">Cover</option><option value="contain">Contain</option><option value="auto">Auto</option><option value="stretch">Stretch</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Image Position</label><select class="pb-select" v-model="selectedNode.settings.bgPosition"><option value="center center">Center</option><option value="top center">Top</option><option value="bottom center">Bottom</option><option value="center left">Left</option><option value="center right">Right</option><option value="top left">Top Left</option><option value="top right">Top Right</option><option value="bottom left">Bottom Left</option><option value="bottom right">Bottom Right</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Background Repeat</label><select class="pb-select" v-model="selectedNode.settings.bgRepeat"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></div>
									<div class="pb-form-group"><label class="pb-form-label">Attachment</label><select class="pb-select" v-model="selectedNode.settings.bgAttachment"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></div>
								</template>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Border</div>
								<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="selectedNode.settings.borderType"><option value="none">None</option><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option><option value="double">Double</option></select></div>
								<template v-if="selectedNode.settings.borderType!=='none'">
									<div class="pb-form-group"><label class="pb-form-label">Border Width</label><input class="pb-input" v-model="selectedNode.settings.borderWidth" placeholder="1px"></div>
									<div class="pb-form-group"><label class="pb-form-label">Border Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings.borderColor"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings.borderColor"></div></div>
								</template>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label">Border Radius</label><button class="pb-link-btn" @click="selectedNode.settings.borderRadiusLinked=!selectedNode.settings.borderRadiusLinked" :title="selectedNode.settings.borderRadiusLinked?'Unlink':'Link'"><i :class="selectedNode.settings.borderRadiusLinked?'fas fa-link':'fas fa-unlink'"></i></button></div>
									<div class="pb-four-sides">
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusTL" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTR=selectedNode.settings.borderRadiusBR=selectedNode.settings.borderRadiusBL=selectedNode.settings.borderRadiusTL)" placeholder="0"><span>TL</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusTR" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTL=selectedNode.settings.borderRadiusBR=selectedNode.settings.borderRadiusBL=selectedNode.settings.borderRadiusTR)" placeholder="0"><span>TR</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusBR" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTL=selectedNode.settings.borderRadiusTR=selectedNode.settings.borderRadiusBL=selectedNode.settings.borderRadiusBR)" placeholder="0"><span>BR</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.borderRadiusBL" @input="selectedNode.settings.borderRadiusLinked&&(selectedNode.settings.borderRadiusTL=selectedNode.settings.borderRadiusTR=selectedNode.settings.borderRadiusBR=selectedNode.settings.borderRadiusBL)" placeholder="0"><span>BL</span></div>
									</div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row"><div class="pb-prop-section-title mb-0">Box Shadow</div><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridShadowEnable-' + selectedNode.id" v-model="selectedNode.settings.shadowEnabled"><label :for="'gridShadowEnable-' + selectedNode.id"></label></div></div>
								<template v-if="selectedNode.settings.shadowEnabled">
									<div class="pb-four-sides mt-2">
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.shadowH" placeholder="0"><span>H</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.shadowV" placeholder="0"><span>V</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.shadowBlur" placeholder="0"><span>Blur</span></div>
										<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.shadowSpread" placeholder="0"><span>Spread</span></div>
									</div>
									<div class="pb-form-group mt-2"><label class="pb-form-label">Shadow Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="selectedNode.settings.shadowColor"><input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings.shadowColor"></div></div>
									<div class="pb-form-group"><label class="pb-form-label">Shadow Opacity <span class="pb-form-hint">{{ Math.round((selectedNode.settings.shadowOpacity ?? 0.3)*100) }}%</span></label><input type="range" class="pb-range" min="0" max="1" step="0.01" v-model.number="selectedNode.settings.shadowOpacity"></div>
								</template>
							</div>
						</div>
						<!-- TAB ADVANCED (GRID) -->
						<div v-show="settingsTab==='advanced'" class="pb-tab-content pb-grid-settings__tab">
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Motion Effects</div>
								<div class="pb-form-group">
									<div class="pb-inline-action-row" role="button" tabindex="0" title="Animate With AI" @click="showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')" @keydown.enter.prevent="showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')" @keydown.space.prevent="showUnsupportedControlNotice('Animate With AI', 'Animate With AI belum tersedia di builder ini. Kontrolnya disamakan dengan demo tanpa toggle dan tanpa efek canvas.')">
										<label class="pb-form-label mb-0">Animate With AI</label>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Scrolling Effects</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridScrollEffects-' + selectedNode.id" v-model="selectedNode.settings.scrollingEffects"><label :for="'gridScrollEffects-' + selectedNode.id"></label></div></div>
								</div>
								<template v-if="selectedNode.settings.scrollingEffects">
									<div class="pb-form-group">
										<label class="pb-form-label">Scroll Effect Type</label>
										<select class="pb-select" v-model="selectedNode.settings.scrollEffectType">
											<option value="vertical">Vertical</option>
											<option value="horizontal">Horizontal</option>
											<option value="transparency">Transparency</option>
											<option value="blur">Blur</option>
											<option value="rotate">Rotate</option>
											<option value="scale">Scale</option>
										</select>
									</div>
									<div class="pb-gap-row">
										<div class="pb-gap-field">
											<label class="pb-form-label">Direction</label>
											<select class="pb-select" v-model="selectedNode.settings.scrollDirection">
												<option value="up">Up</option>
												<option value="down">Down</option>
												<option value="left">Left</option>
												<option value="right">Right</option>
												<option value="in">In</option>
												<option value="out">Out</option>
											</select>
										</div>
										<div class="pb-gap-field">
											<label class="pb-form-label">Speed</label>
											<input class="pb-input" type="number" min="0" max="10" step="0.1" v-model.number="selectedNode.settings.scrollSpeed">
										</div>
									</div>
									<div class="pb-gap-row">
										<div class="pb-gap-field">
											<label class="pb-form-label">Viewport Start</label>
											<input class="pb-input" type="number" min="0" max="100" step="1" v-model.number="selectedNode.settings.scrollViewportStart">
										</div>
										<div class="pb-gap-field">
											<label class="pb-form-label">Viewport End</label>
											<input class="pb-input" type="number" min="0" max="100" step="1" v-model.number="selectedNode.settings.scrollViewportEnd">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Relative To</label>
										<select class="pb-select" v-model="selectedNode.settings.scrollRelativeTo">
											<option value="default">Default</option>
											<option value="viewport">Viewport</option>
										</select>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridScrollDesktop-' + selectedNode.id" v-model="selectedNode.settings.scrollApplyDesktop"><label :for="'gridScrollDesktop-' + selectedNode.id"></label></div></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridScrollTablet-' + selectedNode.id" v-model="selectedNode.settings.scrollApplyTablet"><label :for="'gridScrollTablet-' + selectedNode.id"></label></div></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridScrollMobile-' + selectedNode.id" v-model="selectedNode.settings.scrollApplyMobile"><label :for="'gridScrollMobile-' + selectedNode.id"></label></div></div>
									</div>
								</template>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Mouse Effects</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridMouseEffects-' + selectedNode.id" v-model="selectedNode.settings.mouseEffects"><label :for="'gridMouseEffects-' + selectedNode.id"></label></div></div>
								</div>
								<template v-if="selectedNode.settings.mouseEffects">
									<div class="pb-form-group">
										<label class="pb-form-label">Mouse Effect Type</label>
										<select class="pb-select" v-model="selectedNode.settings.mouseEffectType">
											<option value="track">Track</option>
											<option value="tilt">3D Tilt</option>
											<option value="parallax">Parallax</option>
										</select>
									</div>
									<div class="pb-gap-row">
										<div class="pb-gap-field">
											<label class="pb-form-label">Direction</label>
											<select class="pb-select" v-model="selectedNode.settings.mouseDirection">
												<option value="direct">Direct</option>
												<option value="opposite">Opposite</option>
											</select>
										</div>
										<div class="pb-gap-field">
											<label class="pb-form-label">Speed</label>
											<input class="pb-input" type="number" min="0" max="10" step="0.1" v-model.number="selectedNode.settings.mouseSpeed">
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Relative To</label>
										<select class="pb-select" v-model="selectedNode.settings.mouseRelativeTo">
											<option value="default">Default</option>
											<option value="viewport">Viewport</option>
										</select>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridMouseDesktop-' + selectedNode.id" v-model="selectedNode.settings.mouseApplyDesktop"><label :for="'gridMouseDesktop-' + selectedNode.id"></label></div></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridMouseTablet-' + selectedNode.id" v-model="selectedNode.settings.mouseApplyTablet"><label :for="'gridMouseTablet-' + selectedNode.id"></label></div></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Apply On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridMouseMobile-' + selectedNode.id" v-model="selectedNode.settings.mouseApplyMobile"><label :for="'gridMouseMobile-' + selectedNode.id"></label></div></div>
									</div>
								</template>
								<div class="pb-form-group">
									<label class="pb-form-label">Sticky</label>
									<select class="pb-select" v-model="selectedNode.settings.sticky">
										<option value="none">None</option>
										<option value="top">Top</option>
										<option value="bottom">Bottom</option>
									</select>
								</div>
								<template v-if="selectedNode.settings.sticky !== 'none'">
									<div class="pb-gap-row">
										<div class="pb-gap-field"><label class="pb-form-label">Sticky Offset</label><input class="pb-input" v-model="selectedNode.settings.stickyOffset" placeholder="0"></div>
										<div class="pb-gap-field"><label class="pb-form-label">Effects Offset</label><input class="pb-input" v-model="selectedNode.settings.stickyEffectsOffset" placeholder="0"></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridStickyDesktop-' + selectedNode.id" v-model="selectedNode.settings.stickyOnDesktop"><label :for="'gridStickyDesktop-' + selectedNode.id"></label></div></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridStickyTablet-' + selectedNode.id" v-model="selectedNode.settings.stickyOnTablet"><label :for="'gridStickyTablet-' + selectedNode.id"></label></div></div>
									</div>
									<div class="pb-form-group">
										<div class="pb-label-row"><label class="pb-form-label mb-0">Sticky On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridStickyMobile-' + selectedNode.id" v-model="selectedNode.settings.stickyOnMobile"><label :for="'gridStickyMobile-' + selectedNode.id"></label></div></div>
									</div>
								</template>
								<div class="pb-form-group">
									<label class="pb-form-label">Entrance Animation</label>
									<select class="pb-select" v-model="selectedNode.settings.entranceAnimation">
										<option value="">None</option>
										<option value="fadeIn">Fade In</option>
										<option value="fadeInUp">Fade In Up</option>
										<option value="fadeInDown">Fade In Down</option>
										<option value="fadeInLeft">Fade In Left</option>
										<option value="fadeInRight">Fade In Right</option>
										<option value="zoomIn">Zoom In</option>
										<option value="slideInUp">Slide In Up</option>
										<option value="slideInDown">Slide In Down</option>
										<option value="slideInLeft">Slide In Left</option>
										<option value="slideInRight">Slide In Right</option>
										<option value="bounceIn">Bounce In</option>
									</select>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Transform</div>
								<div class="pb-gap-row">
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformRotate" placeholder="0deg"><span>Rotate</span></div>
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformOffsetX" placeholder="0px"><span>Offset X</span></div>
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformOffsetY" placeholder="0px"><span>Offset Y</span></div>
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformScaleX" placeholder="1"><span>Scale X</span></div>
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformScaleY" placeholder="1"><span>Scale Y</span></div>
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformSkewX" placeholder="0deg"><span>Skew X</span></div>
									<div class="pb-gap-field"><input class="pb-input" v-model="selectedNode.settings.transformSkewY" placeholder="0deg"><span>Skew Y</span></div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Responsive</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Hide On Desktop</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridHideDesktop-' + selectedNode.id" v-model="selectedNode.settings.hideDesktop"><label :for="'gridHideDesktop-' + selectedNode.id"></label></div></div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Hide On Tablet</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridHideTablet-' + selectedNode.id" v-model="selectedNode.settings.hideTablet"><label :for="'gridHideTablet-' + selectedNode.id"></label></div></div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row"><label class="pb-form-label mb-0">Hide On Mobile</label><div class="pb-toggle-wrap"><input type="checkbox" class="pb-toggle" :id="'gridHideMobile-' + selectedNode.id" v-model="selectedNode.settings.hideMobile"><label :for="'gridHideMobile-' + selectedNode.id"></label></div></div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-prop-section-title">Positioning</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Position</label>
									<select class="pb-select" v-model="selectedNode.settings.position">
										<option value="default">Default</option>
										<option value="relative">Relative</option>
										<option value="absolute">Absolute</option>
										<option value="fixed">Fixed</option>
										<option value="sticky">Sticky</option>
									</select>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Overflow</label>
									<select class="pb-select" v-model="selectedNode.settings.overflow">
										<option value="visible">Visible</option>
										<option value="hidden">Hidden</option>
										<option value="auto">Auto</option>
										<option value="scroll">Scroll</option>
									</select>
								</div>
								<div class="pb-four-sides mt-1">
									<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.positionTop" placeholder="auto"><span>Top</span></div>
									<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.positionRight" placeholder="auto"><span>Right</span></div>
									<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.positionBottom" placeholder="auto"><span>Bottom</span></div>
									<div class="pb-side-input"><input class="pb-input" v-model="selectedNode.settings.positionLeft" placeholder="auto"><span>Left</span></div>
								</div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-form-group"><label class="pb-form-label">Z-Index</label><input class="pb-input" v-model="selectedNode.settings.zIndex" type="number" placeholder="auto"></div>
								<div class="pb-form-group"><label class="pb-form-label">CSS ID</label><input class="pb-input" v-model="selectedNode.settings.cssId" placeholder="my-grid-id"></div>
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="my-class"></div>
								<div class="pb-form-group"><label class="pb-form-label">Custom CSS</label><textarea class="pb-textarea pb-code-editor" v-model="selectedNode.settings.customCssCode" placeholder="selector { property: value; }"></textarea></div>
							</div>
							<div class="pb-prop-section pb-grid-settings__group">
								<div class="pb-label-row"><div class="pb-prop-section-title mb-0">Custom Attributes</div><button class="pb-seg-btn pb-mini-btn" @click="selectedNode.settings.attributes=(selectedNode.settings.attributes||[]).concat({name:'',value:''})"><i class="fas fa-plus"></i></button></div>
								<div v-for="(attr,i) in (selectedNode.settings.attributes||[])" :key="i" class="pb-attr-row">
									<input class="pb-input" v-model="attr.name"  placeholder="name">
									<input class="pb-input" v-model="attr.value" placeholder="value">
									<button class="pb-btn icon-sm" @click="selectedNode.settings.attributes.splice(i,1)"><i class="fas fa-trash"></i></button>
								</div>
							</div>
						</div>
						</div>
					</template><!-- /grid tabs -->
					<template v-if="selectedType==='heading'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--heading">
							<div class="pb-widget-settings__group">
								<div class="pb-form-group"><label class="pb-form-label">Text</label><textarea class="pb-textarea" v-model="selectedNode.settings.text"></textarea></div>
								<div class="pb-form-group"><label class="pb-form-label">Tag</label><select class="pb-select" v-model="selectedNode.settings.tag"><option>h1</option><option>h2</option><option>h3</option><option>h4</option><option>h5</option><option>h6</option></select></div>
								<div class="pb-form-group"><label class="pb-form-label">Align</label><select class="pb-select" v-model="selectedNode.settings.align"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
								<div class="pb-form-group"><label class="pb-form-label">Color</label><input class="pb-input" v-model="selectedNode.settings.color"></div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-heading"></div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='text_editor'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--text-editor">
							<div class="pb-widget-settings__group">
								<div class="pb-form-group">
									<div class="pb-label-row pb-widget-settings__label-row">
										<label class="pb-form-label mb-0">Content</label>
										<button type="button" class="pb-editor-expand-btn" title="Expand editor" @click="openTextEditorModal">
											<i class="fas fa-expand-alt"></i>
											<span>Expand</span>
										</button>
									</div>
									<CkEditorField v-model="selectedNode.settings.html" />
								</div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-text"></div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='image'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--image">
							<div class="pb-widget-settings__group pb-widget-settings__group--media">
								<div class="pb-form-group">
									<label class="pb-form-label">Image</label>
									<div class="pb-bg-media-field pb-widget-settings__media-field" :class="{ 'has-image': !!selectedNode.settings.src }">
										<div class="pb-bg-media-preview" :style="selectedNode.settings.src ? { backgroundImage: 'url(' + selectedNode.settings.src + ')' } : {}">
											<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings.src ? 'Change Image' : 'Choose Image'" @click="chooseMedia(selectedNode.settings, 'src')">
												<i :class="selectedNode.settings.src ? 'fas fa-pen' : 'fas fa-plus'"></i>
											</button>
										</div>
										<div class="pb-bg-media-actions">
											<button type="button" class="pb-bg-media-choose" @click="chooseMedia(selectedNode.settings, 'src')">Choose Image</button>
											<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings.src" title="Remove Image" @click="clearMedia(selectedNode.settings, 'src')">
												<i class="fas fa-trash-alt"></i>
											</button>
										</div>
									</div>
								</div>
								<div class="pb-form-group"><label class="pb-form-label">Image URL</label><input class="pb-input" v-model="selectedNode.settings.src"></div>
								<div class="pb-form-group"><label class="pb-form-label">Alt</label><input class="pb-input" v-model="selectedNode.settings.alt"></div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--sizing">
								<div class="pb-widget-settings__section-title">Dimensions</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Width</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('image-width')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('image-width')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'image-width-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('image-width', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" :max="sizeControlMax(selectedNode, 'width', '100%')" :step="sizeControlStep(selectedNode, 'width', '100%')" :value="sizeControlDisplayValue(selectedNode, 'width', '100%') || 0" @input="onSizeControlInput(selectedNode, 'width', $event, { fallback: '100%', emptyToken: '100%' })">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" :max="sizeControlMax(selectedNode, 'width', '100%')" :step="sizeControlStep(selectedNode, 'width', '100%')" :value="sizeControlDisplayValue(selectedNode, 'width', '100%')" @input="onSizeControlInput(selectedNode, 'width', $event, { fallback: '100%', emptyToken: '100%' })">
											<select class="pb-mini-unit" :value="sizeControlUnit(selectedNode, 'width', '100%')" @change="setSizeControlUnit(selectedNode, 'width', $event.target.value, { fallback: '100%', emptyToken: '100%' })">
												<option v-for="unit in sizeControlUnits" :key="'image-width-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Height</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('image-height')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('image-height')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'image-height-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('image-height', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" :max="sizeControlMax(selectedNode, 'height', 'auto')" :step="sizeControlStep(selectedNode, 'height', 'auto')" :value="sizeControlDisplayValue(selectedNode, 'height', 'auto', { allowEmpty: true }) || 0" @input="onSizeControlInput(selectedNode, 'height', $event, { fallback: 'auto', allowEmpty: true, emptyToken: 'auto' })">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" :max="sizeControlMax(selectedNode, 'height', 'auto')" :step="sizeControlStep(selectedNode, 'height', 'auto')" :value="sizeControlDisplayValue(selectedNode, 'height', 'auto', { allowEmpty: true })" @input="onSizeControlInput(selectedNode, 'height', $event, { fallback: 'auto', allowEmpty: true, emptyToken: 'auto' })" placeholder="auto">
											<select class="pb-mini-unit" :value="sizeControlUnit(selectedNode, 'height', 'auto')" @change="setSizeControlUnit(selectedNode, 'height', $event.target.value, { fallback: 'auto', allowEmpty: true, emptyToken: 'auto' })">
												<option v-for="unit in sizeControlUnits" :key="'image-height-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
									<div class="pb-form-note">Leave empty to keep auto height.</div>
								</div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-image"></div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='video'">
						<div class="pb-video-settings">
							<div class="pb-video-settings__group pb-video-settings__group--basic">
								<div class="pb-form-group">
									<label class="pb-form-label">Source</label>
									<select class="pb-select" :value="videoCurrentSource(selectedNode)" @change="setVideoSourceType(selectedNode, $event.target.value)">
										<option v-for="option in videoSourceOptions" :key="'video-source-' + option.value" :value="option.value">{{ option.label }}</option>
									</select>
								</div>
								<div class="pb-form-group pb-toggle-label-row pb-video-settings__compact-toggle" v-if="videoUsesHostedPicker(selectedNode)">
									<label class="pb-form-label mb-0">External URL</label>
									<div class="pb-toggle-switch-wrap">
										<div class="pb-toggle-wrap">
											<input :id="'video-external-url-' + selectedNode.id" type="checkbox" class="pb-toggle" v-model="selectedNode.settings.externalUrl">
											<label :for="'video-external-url-' + selectedNode.id"></label>
										</div>
										<span class="pb-toggle-state">{{ selectedNode.settings.externalUrl ? 'On' : 'Off' }}</span>
									</div>
								</div>
								<div class="pb-form-group" v-if="videoLinkField(selectedNode)">
									<label class="pb-form-label">{{ videoLinkField(selectedNode).label }}</label>
									<input class="pb-input" v-model="selectedNode.settings[videoLinkField(selectedNode).key]" :placeholder="videoLinkField(selectedNode).placeholder">
								</div>
								<div class="pb-form-group" v-if="videoUsesHostedPicker(selectedNode) && !selectedNode.settings.externalUrl">
									<label class="pb-form-label">Choose Video File</label>
									<div class="pb-bg-media-field pb-video-settings__media-field" :class="{ 'has-image': !!selectedNode.settings.fileUrl }">
										<div class="pb-bg-media-preview">
											<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings.fileUrl ? 'Change Video' : 'Choose Video'" @click="chooseMedia(selectedNode.settings, 'fileUrl', 'Paste video URL')">
												<i :class="selectedNode.settings.fileUrl ? 'fas fa-pen' : 'fas fa-plus'"></i>
											</button>
										</div>
										<div class="pb-bg-media-actions">
											<button type="button" class="pb-bg-media-choose" @click="chooseMedia(selectedNode.settings, 'fileUrl', 'Paste video URL')">Choose Video</button>
											<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings.fileUrl" title="Remove Video" @click="clearMedia(selectedNode.settings, 'fileUrl')">
												<i class="fas fa-trash-alt"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="pb-video-settings__group pb-video-settings__group--playback">
								<div class="pb-form-group">
									<label class="pb-form-label">Start Time</label>
									<input class="pb-input" type="number" min="0" v-model.number="selectedNode.settings.startTime" placeholder="0">
									<div class="pb-form-note">Specify a start time in seconds.</div>
								</div>
								<div class="pb-form-group" v-if="videoShowsEndTime(selectedNode)">
									<label class="pb-form-label">End Time</label>
									<input class="pb-input" type="number" min="0" v-model.number="selectedNode.settings.endTime" placeholder="0">
									<div class="pb-form-note">Specify an end time in seconds.</div>
								</div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Aspect Ratio</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('video-ratio')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('video-ratio')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'video-ratio-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('video-ratio', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<select class="pb-select" :value="videoAspectRatioValue(selectedNode)" @change="setVideoAspectRatioValue(selectedNode, $event.target.value)"><option v-for="option in videoAspectRatioOptions" :key="'video-ratio-option-' + option.value" :value="option.value">{{ option.label }}</option></select>
								</div>
							</div>
							<div class="pb-video-settings__group pb-video-settings__group--options">
								<div class="pb-video-settings__section-title">Video Options</div>
								<div class="pb-video-settings__option-list">
									<template v-for="option in videoToggleOptions(selectedNode)" :key="'video-toggle-' + option.key">
										<div class="pb-video-settings__option-row">
											<div class="pb-form-group pb-toggle-label-row pb-video-settings__toggle-row">
												<label class="pb-form-label mb-0">{{ option.label }}</label>
												<div class="pb-toggle-switch-wrap">
													<div class="pb-toggle-wrap">
														<input :id="'video-toggle-' + option.key + '-' + selectedNode.id" type="checkbox" class="pb-toggle" v-model="selectedNode.settings[option.key]">
														<label :for="'video-toggle-' + option.key + '-' + selectedNode.id"></label>
													</div>
													<span class="pb-toggle-state">{{ videoToggleStateLabel(option, selectedNode.settings[option.key]) }}</span>
												</div>
											</div>
											<div class="pb-form-note pb-video-settings__toggle-note" v-if="option.key === 'autoplay'">
												Autoplay can still be affected by browser policy, especially when audio is enabled.
											</div>
											<div class="pb-form-note pb-video-settings__toggle-note" v-else-if="option.key === 'privacyMode' && (videoCurrentSource(selectedNode) === 'youtube' || videoCurrentSource(selectedNode) === 'vimeo')">
												When you turn on privacy mode, YouTube/Vimeo won't store information about visitors on your website unless they play the video.
											</div>
										</div>
									</template>
								</div>
							</div>
							<div class="pb-video-settings__group pb-video-settings__group--extras" v-if="videoSelectOptions(selectedNode).length || videoUsesControlsColor(selectedNode) || videoShowsPoster(selectedNode)">
								<div class="pb-form-group" v-for="field in videoSelectOptions(selectedNode)" :key="'video-select-' + field.key">
									<label class="pb-form-label">{{ field.label }}</label>
									<select class="pb-select" v-model="selectedNode.settings[field.key]">
										<option v-for="option in field.options" :key="'video-select-option-' + field.key + '-' + option.value" :value="option.value">{{ option.label }}</option>
									</select>
								</div>
								<div class="pb-form-group" v-if="videoUsesControlsColor(selectedNode)">
									<label class="pb-form-label">Controls Color</label>
									<input class="pb-input" v-model="selectedNode.settings.controlsColor" placeholder="#ff3366">
								</div>
								<div class="pb-form-group" v-if="videoShowsPoster(selectedNode)">
									<label class="pb-form-label">Poster</label>
									<div class="pb-bg-media-field pb-video-settings__media-field" :class="{ 'has-image': !!selectedNode.settings.poster }">
										<div class="pb-bg-media-preview" :style="selectedNode.settings.poster ? { backgroundImage: 'url(' + selectedNode.settings.poster + ')' } : {}">
											<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings.poster ? 'Change Poster' : 'Choose Poster'" @click="chooseMedia(selectedNode.settings, 'poster', 'Paste image URL')">
												<i :class="selectedNode.settings.poster ? 'fas fa-pen' : 'fas fa-plus'"></i>
											</button>
										</div>
										<div class="pb-bg-media-actions">
											<button type="button" class="pb-bg-media-choose" @click="chooseMedia(selectedNode.settings, 'poster', 'Paste image URL')">Choose Image</button>
											<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings.poster" title="Remove Poster" @click="clearMedia(selectedNode.settings, 'poster')">
												<i class="fas fa-trash-alt"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="pb-video-settings__group pb-video-settings__group--overlay" v-if="videoShowsOverlay(selectedNode)">
								<div class="pb-video-settings__section-title">Image Overlay</div>
								<div class="pb-form-group pb-toggle-label-row pb-video-settings__compact-toggle">
									<label class="pb-form-label mb-0">Image Overlay</label>
									<div class="pb-toggle-switch-wrap">
										<div class="pb-toggle-wrap">
											<input :id="'video-image-overlay-' + selectedNode.id" type="checkbox" class="pb-toggle" v-model="selectedNode.settings.imageOverlay">
											<label :for="'video-image-overlay-' + selectedNode.id"></label>
										</div>
										<span class="pb-toggle-state">{{ selectedNode.settings.imageOverlay ? 'Show' : 'Hide' }}</span>
									</div>
								</div>
								<div class="pb-form-group" v-if="selectedNode.settings.imageOverlay">
									<div class="pb-bg-media-field pb-video-settings__media-field" :class="{ 'has-image': !!selectedNode.settings.overlayImage }">
										<div class="pb-bg-media-preview" :style="selectedNode.settings.overlayImage ? { backgroundImage: 'url(' + selectedNode.settings.overlayImage + ')' } : {}">
											<button type="button" class="pb-bg-media-center-btn" :title="selectedNode.settings.overlayImage ? 'Change Overlay Image' : 'Choose Overlay Image'" @click="chooseMedia(selectedNode.settings, 'overlayImage', 'Paste image URL')">
												<i :class="selectedNode.settings.overlayImage ? 'fas fa-pen' : 'fas fa-plus'"></i>
											</button>
										</div>
										<div class="pb-bg-media-actions">
											<button type="button" class="pb-bg-media-choose" @click="chooseMedia(selectedNode.settings, 'overlayImage', 'Paste image URL')">Choose Image</button>
											<button type="button" class="pb-bg-media-remove" :disabled="!selectedNode.settings.overlayImage" title="Remove Overlay Image" @click="clearMedia(selectedNode.settings, 'overlayImage')">
												<i class="fas fa-trash-alt"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="pb-video-settings__group pb-video-settings__group--advanced">
								<div class="pb-form-group">
									<label class="pb-form-label">CSS Class</label>
									<input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-video">
								</div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='accordion'">
						<div class="pb-accordion-settings pb-widget-settings pb-widget-settings--accordion">
							<div class="pb-tab-nav">
								<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='content'}" @click="settingsTab='content'"><i class="fas fa-edit"></i><span>Content</span></button>
								<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='style'}" @click="settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
								<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:settingsTab==='advanced'}" @click="settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
							</div>

							<div v-show="settingsTab==='content'" class="pb-tab-content">
								<details class="pb-collapsible" open>
									<summary>Layout</summary>
									<div class="pb-collapsible-body">
										<div class="pb-form-group">
											<label class="pb-form-label">Items</label>
											<draggable
												v-model="selectedNode.accordionItems"
												item-key="id"
												:group="{ name: 'pb-accordion-items', pull: false, put: false }"
												handle=".pb-accordion-item-drag"
												class="pb-accordion-items-list"
											>
												<template #item="{ element: item, index }">
													<div class="pb-accordion-item-row" :class="{ active: accordionRuntimeForNode(selectedNode).editingItemId===item.id }">
														<span class="pb-accordion-item-drag" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
														<button type="button" class="pb-accordion-item-main" @click="selectAccordionItem(selectedNode, item.id)">{{ accordionItemSummary(item, index) }}</button>
														<button type="button" class="pb-accordion-item-action" title="Duplicate Item" @click="duplicateAccordionItem(selectedNode, item.id)"><i class="far fa-copy"></i></button>
														<button type="button" class="pb-accordion-item-action" title="Delete Item" :disabled="accordionItemsForNode(selectedNode).length<=1" @click="removeAccordionItem(selectedNode, item.id)"><i class="fas fa-times"></i></button>
													</div>
												</template>
											</draggable>
											<button type="button" class="pb-btn pb-accordion-add-btn" @click="addAccordionItem(selectedNode)"><i class="fas fa-plus"></i><span>Add Item</span></button>
										</div>

										<div v-if="accordionEditingItem(selectedNode)" class="pb-accordion-item-fields">
											<div class="pb-form-group"><label class="pb-form-label">Title</label><input class="pb-input" v-model="accordionEditingItem(selectedNode).title" placeholder="Item title"></div>
											<div class="pb-form-group"><label class="pb-form-label">CSS ID</label><input class="pb-input" v-model="accordionEditingItem(selectedNode).cssId" placeholder="item-one"></div>
										</div>

										<div class="pb-form-group">
											<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Item Position</label><div class="pb-control-device-wrap">
												<button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-item-position')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('accordion-item-position')" class="pb-control-device-menu">
													<button v-for="device in responsiveDevices" :key="'accordion-item-position-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-item-position', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button>
												</div>
											</div></div>
											<select class="pb-select" v-model="selectedNode.settings[activeResponsiveKey('itemPosition')]">
												<option value="">Default</option><option value="start">Start</option><option value="center">Center</option><option value="end">End</option><option value="stretch">Stretch</option>
											</select>
										</div>
										<div class="pb-form-group">
											<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Icon Position</label><div class="pb-control-device-wrap">
												<button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-icon-position')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('accordion-icon-position')" class="pb-control-device-menu">
													<button v-for="device in responsiveDevices" :key="'accordion-icon-position-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-icon-position', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button>
												</div>
											</div></div>
											<select class="pb-select" v-model="selectedNode.settings[activeResponsiveKey('iconPosition')]">
												<option value="">Default</option><option value="start">Start</option><option value="end">End</option>
											</select>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Expand Icon</label>
											<select class="pb-select" v-model="selectedNode.settings.expandIconSource">
												<option value="none">None</option><option value="library">Icon Library</option><option value="svg">Upload SVG</option>
											</select>
											<button v-if="selectedNode.settings.expandIconSource==='library'" type="button" class="pb-btn pb-icon-source-btn" @click="openAccordionIconLibrary('expand', selectedNode)"><i :class="selectedNode.settings.expandIconClass"></i><span>Choose from Library</span></button>
											<button v-if="selectedNode.settings.expandIconSource==='svg'" type="button" class="pb-btn pb-icon-source-btn" @click="chooseAccordionSvg('expand', selectedNode)"><i class="fas fa-upload"></i><span>Choose SVG</span></button>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Collapse Icon</label>
											<select class="pb-select" v-model="selectedNode.settings.collapseIconSource">
												<option value="none">None</option><option value="library">Icon Library</option><option value="svg">Upload SVG</option>
											</select>
											<button v-if="selectedNode.settings.collapseIconSource==='library'" type="button" class="pb-btn pb-icon-source-btn" @click="openAccordionIconLibrary('collapse', selectedNode)"><i :class="selectedNode.settings.collapseIconClass"></i><span>Choose from Library</span></button>
											<button v-if="selectedNode.settings.collapseIconSource==='svg'" type="button" class="pb-btn pb-icon-source-btn" @click="chooseAccordionSvg('collapse', selectedNode)"><i class="fas fa-upload"></i><span>Choose SVG</span></button>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Title HTML Tag</label>
											<select class="pb-select" v-model="selectedNode.settings.titleTag">
												<option v-for="tag in ['h1','h2','h3','h4','h5','h6','div','span','p']" :key="tag" :value="tag">{{ tag.toUpperCase() }}</option>
											</select>
										</div>
										<div class="pb-form-group pb-toggle-label-row">
											<label class="pb-form-label mb-0">FAQ Schema</label>
											<div class="pb-toggle-wrap"><input :id="'accordion-faq-'+selectedNode.id" type="checkbox" class="pb-toggle" v-model="selectedNode.settings.faqSchema"><label :for="'accordion-faq-'+selectedNode.id"></label></div>
										</div>
									</div>
								</details>

								<details class="pb-collapsible" open>
									<summary>Interactions</summary>
									<div class="pb-collapsible-body">
										<div class="pb-form-group">
											<label class="pb-form-label">Default State</label>
											<select class="pb-select" v-model="selectedNode.settings.defaultState" @change="resetAccordionRuntimeFromDefaults(selectedNode)">
												<option value="first-expanded">First Expanded</option>
												<option value="all-collapsed">All Collapsed</option>
											</select>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Max Items Expanded</label>
											<select class="pb-select" v-model="selectedNode.settings.maxExpanded" @change="accordionRuntimeForNode(selectedNode)">
												<option value="one">One</option>
												<option value="multiple">Multiple</option>
											</select>
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Animation Duration <span class="pb-form-hint">{{ selectedNode.settings.animationDuration }}ms</span></label>
											<div class="pb-range-value-row">
												<input type="range" class="pb-range" min="0" max="2000" step="50" v-model.number="selectedNode.settings.animationDuration">
												<input class="pb-input pb-input-compact" type="number" min="0" max="5000" step="50" v-model.number="selectedNode.settings.animationDuration">
											</div>
										</div>
									</div>
								</details>
							</div>

							<div v-show="settingsTab==='style'" class="pb-tab-content pb-accordion-style-settings">
								<details class="pb-collapsible" open>
									<summary>Accordion</summary>
									<div class="pb-collapsible-body">
										<div class="pb-form-group pb-accordion-dimension-control">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Space Between Items</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap">
												<button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-item-gap')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('accordion-item-gap')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-item-gap-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-item-gap', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div>
											</div><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" @change="setAccordionDimensionUnit(selectedNode, activeResponsiveKey('accordionItemGap'), $event.target.value, '0px')"><option v-for="unit in sizeControlUnits" :key="'accordion-gap-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div>
											<div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" :step="accordionDimensionStep(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('accordionItemGap'), $event, '0px')"><input class="pb-input pb-input-compact" type="number" min="0" :max="accordionDimensionMax(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" :step="accordionDimensionStep(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('accordionItemGap'), '0px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('accordionItemGap'), $event, '0px')"></div>
										</div>
										<div class="pb-form-group pb-accordion-dimension-control">
											<div class="pb-label-row"><label class="pb-form-label mb-0">Distance from Content</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap">
												<button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-content-distance')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
												<div v-if="isControlResponsiveMenuOpen('accordion-content-distance')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-content-distance-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-content-distance', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div>
											</div><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" @change="setAccordionDimensionUnit(selectedNode, activeResponsiveKey('accordionContentDistance'), $event.target.value, '0px')"><option v-for="unit in sizeControlUnits" :key="'accordion-distance-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div>
											<div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" :step="accordionDimensionStep(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('accordionContentDistance'), $event, '0px')"><input class="pb-input pb-input-compact" type="number" min="0" :max="accordionDimensionMax(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" :step="accordionDimensionStep(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('accordionContentDistance'), '0px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('accordionContentDistance'), $event, '0px')"></div>
										</div>
										<div class="pb-state-tabs">
											<button v-for="state in accordionStyleStates" :key="'accordion-state-'+state.value" type="button" :class="{active:accordionStyleState===state.value}" @click="accordionStyleState=state.value">{{ state.label }}</button>
										</div>
										<div class="pb-form-group"><label class="pb-form-label">Background Type</label><select class="pb-select" v-model="selectedNode.settings[accordionStateKey('accordionBackgroundType', accordionStyleState)]"><option value="classic">Classic</option><option value="gradient">Gradient</option></select></div>
										<template v-if="selectedNode.settings[accordionStateKey('accordionBackgroundType', accordionStyleState)]==='gradient'">
											<div class="pb-form-group"><label class="pb-form-label">First Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('accordionGradientColorOne', accordionStyleState)]"></div>
											<div class="pb-form-group"><label class="pb-form-label">First Location</label><input class="pb-input" type="number" min="0" max="100" v-model.number="selectedNode.settings[accordionStateKey('accordionGradientLocationOne', accordionStyleState)]"></div>
											<div class="pb-form-group"><label class="pb-form-label">Second Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('accordionGradientColorTwo', accordionStyleState)]"></div>
											<div class="pb-form-group"><label class="pb-form-label">Second Location</label><input class="pb-input" type="number" min="0" max="100" v-model.number="selectedNode.settings[accordionStateKey('accordionGradientLocationTwo', accordionStyleState)]"></div>
											<div class="pb-form-group"><label class="pb-form-label">Gradient Type</label><select class="pb-select" v-model="selectedNode.settings[accordionStateKey('accordionGradientType', accordionStyleState)]"><option v-for="type in accordionGradientTypes" :key="type" :value="type">{{ type }}</option></select></div>
											<div v-if="selectedNode.settings[accordionStateKey('accordionGradientType', accordionStyleState)]==='linear'" class="pb-form-group"><label class="pb-form-label">Angle</label><input class="pb-input" type="number" min="0" max="360" v-model.number="selectedNode.settings[accordionStateKey('accordionGradientAngle', accordionStyleState)]"></div>
											<div v-else class="pb-form-group"><label class="pb-form-label">Position</label><select class="pb-select" v-model="selectedNode.settings[accordionStateKey('accordionGradientPosition', accordionStyleState)]"><option value="center center">Center Center</option><option value="center top">Center Top</option><option value="center bottom">Center Bottom</option><option value="left center">Left Center</option><option value="right center">Right Center</option></select></div>
										</template>
										<div v-else class="pb-form-group"><label class="pb-form-label">Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('accordionBackgroundColor', accordionStyleState)]"></div>
										<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="selectedNode.settings[accordionStateKey('accordionBorderType', accordionStyleState)]"><option v-for="type in accordionBorderTypes" :key="type" :value="type">{{ type }}</option></select></div>
										<template v-if="!['default','none'].includes(selectedNode.settings[accordionStateKey('accordionBorderType', accordionStyleState)])">
										<div class="pb-form-group pb-accordion-dimension-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Border Width</label><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), '1px')" @change="setAccordionDimensionUnit(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), $event.target.value, '1px')"><option v-for="unit in ['px','pt','em','rem']" :key="'accordion-border-width-unit-'+unit" :value="unit">{{ unit }}</option></select></div><div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), '1px')" :step="accordionDimensionStep(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), '1px')" :value="accordionDimensionValue(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), '1px')" @input="onAccordionDimensionInput(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), $event, '1px')"><input class="pb-input pb-input-compact" type="number" min="0" :value="accordionDimensionValue(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), '1px')" @input="onAccordionDimensionInput(selectedNode, accordionStateKey('accordionBorderWidth', accordionStyleState), $event, '1px')"></div></div>
											<div class="pb-form-group"><label class="pb-form-label">Border Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('accordionBorderColor', accordionStyleState)]"></div>
										</template>
										<div class="pb-form-group pb-accordion-box-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Border Radius</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-border-radius')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button><div v-if="isControlResponsiveMenuOpen('accordion-border-radius')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-border-radius-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-border-radius', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="accordionBoxUnit(selectedNode, activeResponsiveKey('accordionBorderRadius'), '0px')" @change="setAccordionBoxUnit(selectedNode, activeResponsiveKey('accordionBorderRadius'), $event.target.value, '0px')"><option v-for="unit in sizeControlUnits" :key="'accordion-radius-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in ['Top','Right','Bottom','Left']" :key="'accordion-radius-'+side" class="pb-side-input"><input class="pb-input" type="number" min="0" :value="accordionBoxSideValue(selectedNode, activeResponsiveKey('accordionBorderRadius'), index, '0px')" @input="onAccordionBoxSideInput(selectedNode, activeResponsiveKey('accordionBorderRadius'), index, $event, '0px')"><span>{{ side }}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:accordionBoxLinked(activeResponsiveKey('accordionBorderRadius'))}" @click="toggleAccordionBoxLink(activeResponsiveKey('accordionBorderRadius'))" title="Link values"><i class="fas" :class="accordionBoxLinked(activeResponsiveKey('accordionBorderRadius')) ? 'fa-link' : 'fa-unlink'"></i></button></div></div></div>
										<div class="pb-form-group pb-accordion-box-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Padding</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-padding')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button><div v-if="isControlResponsiveMenuOpen('accordion-padding')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-padding-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-padding', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="accordionBoxUnit(selectedNode, activeResponsiveKey('accordionPadding'), '0px')" @change="setAccordionBoxUnit(selectedNode, activeResponsiveKey('accordionPadding'), $event.target.value, '0px')"><option v-for="unit in sizeControlUnits" :key="'accordion-padding-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in ['Top','Right','Bottom','Left']" :key="'accordion-padding-'+side" class="pb-side-input"><input class="pb-input" type="number" min="0" :value="accordionBoxSideValue(selectedNode, activeResponsiveKey('accordionPadding'), index, '0px')" @input="onAccordionBoxSideInput(selectedNode, activeResponsiveKey('accordionPadding'), index, $event, '0px')"><span>{{ side }}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:accordionBoxLinked(activeResponsiveKey('accordionPadding'))}" @click="toggleAccordionBoxLink(activeResponsiveKey('accordionPadding'))" title="Link values"><i class="fas" :class="accordionBoxLinked(activeResponsiveKey('accordionPadding')) ? 'fa-link' : 'fa-unlink'"></i></button></div></div></div>
									</div>
								</details>

								<details class="pb-collapsible" open>
									<summary>Header</summary>
									<div class="pb-collapsible-body">
										<div class="pb-subsection-title">Title</div>
										<TypographyControl :settings="selectedNode.settings" :responsive-device="responsiveDevice" :font-families="fontFamilies" @responsive-device="setResponsiveDevice" />
										<div class="pb-state-tabs"><button v-for="state in accordionStyleStates" :key="'title-state-'+state.value" type="button" :class="{active:accordionTitleStyleState===state.value}" @click="accordionTitleStyleState=state.value">{{ state.label }}</button></div>
										<div class="pb-form-group"><label class="pb-form-label">Title Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('headerTitleColor', accordionTitleStyleState)]"></div>
										<div class="pb-form-group"><label class="pb-form-label">Text Shadow</label><input class="pb-input" v-model="selectedNode.settings[accordionStateKey('headerTextShadow', accordionTitleStyleState)]" placeholder="0 1px 2px rgba(0,0,0,.15)"></div>
									<div class="pb-form-group pb-accordion-dimension-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Text Stroke Width</label><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), '0px')" @change="setAccordionDimensionUnit(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), $event.target.value, '0px')"><option v-for="unit in ['px','pt','em','rem']" :key="'accordion-stroke-unit-'+unit" :value="unit">{{ unit }}</option></select></div><div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), '0px')" :step="accordionDimensionStep(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), '0px')" :value="accordionDimensionValue(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), '0px')" @input="onAccordionDimensionInput(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), $event, '0px')"><input class="pb-input pb-input-compact" type="number" min="0" :value="accordionDimensionValue(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), '0px')" @input="onAccordionDimensionInput(selectedNode, accordionStateKey('headerTextStrokeWidth', accordionTitleStyleState), $event, '0px')"></div></div>
										<div class="pb-form-group"><label class="pb-form-label">Text Stroke Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('headerTextStrokeColor', accordionTitleStyleState)]"></div>
										<div class="pb-form-group pb-accordion-dimension-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Icon Size</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-icon-size')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button><div v-if="isControlResponsiveMenuOpen('accordion-icon-size')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-icon-size-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-icon-size', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, activeResponsiveKey('headerIconSize'), '16px')" @change="setAccordionDimensionUnit(selectedNode, activeResponsiveKey('headerIconSize'), $event.target.value, '16px')"><option v-for="unit in sizeControlUnits" :key="'accordion-icon-size-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, activeResponsiveKey('headerIconSize'), '16px')" :step="accordionDimensionStep(selectedNode, activeResponsiveKey('headerIconSize'), '16px')" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('headerIconSize'), '16px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('headerIconSize'), $event, '16px')"><input class="pb-input pb-input-compact" type="number" min="0" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('headerIconSize'), '16px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('headerIconSize'), $event, '16px')"></div></div>
										<div class="pb-form-group pb-accordion-dimension-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Icon Spacing</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-icon-spacing')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button><div v-if="isControlResponsiveMenuOpen('accordion-icon-spacing')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-icon-spacing-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-icon-spacing', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, activeResponsiveKey('headerIconSpacing'), '12px')" @change="setAccordionDimensionUnit(selectedNode, activeResponsiveKey('headerIconSpacing'), $event.target.value, '12px')"><option v-for="unit in sizeControlUnits" :key="'accordion-icon-spacing-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, activeResponsiveKey('headerIconSpacing'), '12px')" :step="accordionDimensionStep(selectedNode, activeResponsiveKey('headerIconSpacing'), '12px')" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('headerIconSpacing'), '12px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('headerIconSpacing'), $event, '12px')"><input class="pb-input pb-input-compact" type="number" min="0" :value="accordionDimensionValue(selectedNode, activeResponsiveKey('headerIconSpacing'), '12px')" @input="onAccordionDimensionInput(selectedNode, activeResponsiveKey('headerIconSpacing'), $event, '12px')"></div></div>
										<div class="pb-state-tabs"><button v-for="state in accordionStyleStates" :key="'icon-state-'+state.value" type="button" :class="{active:accordionIconStyleState===state.value}" @click="accordionIconStyleState=state.value">{{ state.label }}</button></div>
										<div class="pb-form-group"><label class="pb-form-label">Icon Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings[accordionStateKey('headerIconColor', accordionIconStyleState)]"></div>
									</div>
								</details>

								<details class="pb-collapsible" open>
									<summary>Content</summary>
									<div class="pb-collapsible-body">
										<div class="pb-form-group"><label class="pb-form-label">Background Type</label><select class="pb-select" v-model="selectedNode.settings.contentBackgroundType"><option value="classic">Classic</option><option value="gradient">Gradient</option></select></div>
										<template v-if="selectedNode.settings.contentBackgroundType==='gradient'">
											<div class="pb-form-group"><label class="pb-form-label">First Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings.contentGradientColorOne"></div>
											<div class="pb-form-group"><label class="pb-form-label">First Location</label><input class="pb-input" type="number" min="0" max="100" v-model.number="selectedNode.settings.contentGradientLocationOne"></div>
											<div class="pb-form-group"><label class="pb-form-label">Second Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings.contentGradientColorTwo"></div>
											<div class="pb-form-group"><label class="pb-form-label">Second Location</label><input class="pb-input" type="number" min="0" max="100" v-model.number="selectedNode.settings.contentGradientLocationTwo"></div>
											<div class="pb-form-group"><label class="pb-form-label">Gradient Type</label><select class="pb-select" v-model="selectedNode.settings.contentGradientType"><option v-for="type in accordionGradientTypes" :key="type" :value="type">{{ type }}</option></select></div>
											<div v-if="selectedNode.settings.contentGradientType==='linear'" class="pb-form-group"><label class="pb-form-label">Angle</label><input class="pb-input" type="number" min="0" max="360" v-model.number="selectedNode.settings.contentGradientAngle"></div>
											<div v-else class="pb-form-group"><label class="pb-form-label">Position</label><select class="pb-select" v-model="selectedNode.settings.contentGradientPosition"><option value="center center">Center Center</option><option value="center top">Center Top</option><option value="center bottom">Center Bottom</option><option value="left center">Left Center</option><option value="right center">Right Center</option></select></div>
										</template>
										<div v-else class="pb-form-group"><label class="pb-form-label">Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings.contentBackgroundColor"></div>
										<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="selectedNode.settings.contentBorderType"><option v-for="type in accordionBorderTypes" :key="type" :value="type">{{ type }}</option></select></div>
									<template v-if="!['default','none'].includes(selectedNode.settings.contentBorderType)"><div class="pb-form-group pb-accordion-dimension-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Border Width</label><select class="pb-mini-unit" :value="accordionDimensionUnit(selectedNode, 'contentBorderWidth', '0px')" @change="setAccordionDimensionUnit(selectedNode, 'contentBorderWidth', $event.target.value, '0px')"><option v-for="unit in ['px','pt','em','rem']" :key="'accordion-content-border-unit-'+unit" :value="unit">{{ unit }}</option></select></div><div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="accordionDimensionMax(selectedNode, 'contentBorderWidth', '0px')" :step="accordionDimensionStep(selectedNode, 'contentBorderWidth', '0px')" :value="accordionDimensionValue(selectedNode, 'contentBorderWidth', '0px')" @input="onAccordionDimensionInput(selectedNode, 'contentBorderWidth', $event, '0px')"><input class="pb-input pb-input-compact" type="number" min="0" :value="accordionDimensionValue(selectedNode, 'contentBorderWidth', '0px')" @input="onAccordionDimensionInput(selectedNode, 'contentBorderWidth', $event, '0px')"></div></div><div class="pb-form-group"><label class="pb-form-label">Border Color</label><input class="pb-input pb-coloris-input" v-model="selectedNode.settings.contentBorderColor"></div></template>
										<div class="pb-form-group pb-accordion-box-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Border Radius</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-content-radius')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button><div v-if="isControlResponsiveMenuOpen('accordion-content-radius')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-content-radius-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-content-radius', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="accordionBoxUnit(selectedNode, activeResponsiveKey('contentBorderRadius'), '0px')" @change="setAccordionBoxUnit(selectedNode, activeResponsiveKey('contentBorderRadius'), $event.target.value, '0px')"><option v-for="unit in sizeControlUnits" :key="'accordion-content-radius-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in ['Top','Right','Bottom','Left']" :key="'accordion-content-radius-'+side" class="pb-side-input"><input class="pb-input" type="number" min="0" :value="accordionBoxSideValue(selectedNode, activeResponsiveKey('contentBorderRadius'), index, '0px')" @input="onAccordionBoxSideInput(selectedNode, activeResponsiveKey('contentBorderRadius'), index, $event, '0px')"><span>{{ side }}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:accordionBoxLinked(activeResponsiveKey('contentBorderRadius'))}" @click="toggleAccordionBoxLink(activeResponsiveKey('contentBorderRadius'))" title="Link values"><i class="fas" :class="accordionBoxLinked(activeResponsiveKey('contentBorderRadius')) ? 'fa-link' : 'fa-unlink'"></i></button></div></div></div>
										<div class="pb-form-group pb-accordion-box-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Padding</label><div class="pb-accordion-dimension-tools"><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('accordion-content-padding')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button><div v-if="isControlResponsiveMenuOpen('accordion-content-padding')" class="pb-control-device-menu"><button v-for="device in responsiveDevices" :key="'accordion-content-padding-'+device.value" type="button" class="pb-control-device-item" :class="{active:responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('accordion-content-padding', device.value)"><i :class="device.icon"></i><span>{{ deviceOptionLabel(device) }}</span></button></div></div><select class="pb-mini-unit" :value="accordionBoxUnit(selectedNode, activeResponsiveKey('contentPadding'), '20px')" @change="setAccordionBoxUnit(selectedNode, activeResponsiveKey('contentPadding'), $event.target.value, '20px')"><option v-for="unit in sizeControlUnits" :key="'accordion-content-padding-unit-'+unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in ['Top','Right','Bottom','Left']" :key="'accordion-content-padding-'+side" class="pb-side-input"><input class="pb-input" type="number" min="0" :value="accordionBoxSideValue(selectedNode, activeResponsiveKey('contentPadding'), index, '20px')" @input="onAccordionBoxSideInput(selectedNode, activeResponsiveKey('contentPadding'), index, $event, '20px')"><span>{{ side }}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:accordionBoxLinked(activeResponsiveKey('contentPadding'))}" @click="toggleAccordionBoxLink(activeResponsiveKey('contentPadding'))" title="Link values"><i class="fas" :class="accordionBoxLinked(activeResponsiveKey('contentPadding')) ? 'fa-link' : 'fa-unlink'"></i></button></div></div></div>
									</div>
								</details>
							</div>

							<div v-show="settingsTab==='advanced'" class="pb-tab-content pb-accordion-advanced-settings">
								<WidgetAdvancedControls
									:node="selectedNode"
									:responsive-device="responsiveDevice"
									@responsive-device="setResponsiveDevice"
									@unavailable-ai="showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')"
								/>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='tabs'">
						<div class="pb-tabs-settings pb-widget-settings pb-widget-settings--tabs">
							<details class="pb-collapsible" open>
								<summary>Tabs</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<div class="pb-label-row">
											<label class="pb-form-label mb-0">Tabs Items</label>
										</div>
										<div class="pb-tabs-items-list">
											<div
												v-for="(item, index) in tabsItemsForNode(selectedNode)"
												:key="item.id"
												class="pb-tabs-item-row"
												:class="{ active: selectedNode.settings.activeTabId===item.id }"
											>
												<button type="button" class="pb-tabs-item-main" @click="selectTabsItem(selectedNode, item.id)">
													<span>{{ tabsItemSummary(item, index) }}</span>
												</button>
												<button type="button" class="pb-tabs-item-action" title="Duplicate Tab" @click="duplicateTabsItem(selectedNode, item.id)">
													<i class="far fa-copy"></i>
												</button>
												<button type="button" class="pb-tabs-item-action" title="Delete Tab" :disabled="tabsItemsForNode(selectedNode).length<=1" @click="removeTabsItem(selectedNode, item.id)">
													<i class="fas fa-times"></i>
												</button>
											</div>
										</div>
										<button type="button" class="pb-btn pb-tabs-add-btn" @click="addTabsItem(selectedNode)">
											<i class="fas fa-plus"></i>
											<span>Add Tab</span>
										</button>
									</div>

									<div v-if="tabsActiveItem(selectedNode)" class="pb-tabs-item-fields">
										<div class="pb-form-group">
											<label class="pb-form-label">Title</label>
											<input class="pb-input" v-model="tabsActiveItem(selectedNode).title" placeholder="Tab title">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Icon Class</label>
											<input class="pb-input" v-model="tabsActiveItem(selectedNode).iconClass" placeholder="far fa-star">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">Active Icon Class</label>
											<input class="pb-input" v-model="tabsActiveItem(selectedNode).activeIconClass" placeholder="fas fa-star">
										</div>
										<div class="pb-form-group">
											<label class="pb-form-label">CSS ID</label>
											<input class="pb-input" v-model="tabsActiveItem(selectedNode).cssId" placeholder="tab-one">
										</div>
									</div>

									<div class="pb-form-group">
										<label class="pb-form-label">Direction</label>
										<div class="pb-seg-group">
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.direction==='row'}" @click="selectedNode.settings.direction='row'" title="Row"><i class="fas fa-arrow-right"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.direction==='column'}" @click="selectedNode.settings.direction='column'" title="Column"><i class="fas fa-arrow-down"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.direction==='row-reverse'}" @click="selectedNode.settings.direction='row-reverse'" title="Row Reverse"><i class="fas fa-arrow-left"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.direction==='column-reverse'}" @click="selectedNode.settings.direction='column-reverse'" title="Column Reverse"><i class="fas fa-arrow-up"></i></button>
										</div>
									</div>
									<div class="pb-form-group pb-tabs-width-control" v-if="tabsSelectedRowDirection(selectedNode)">
										<label class="pb-form-label">Width</label>
										<div class="pb-range-value-row">
											<input type="range" class="pb-range" min="1" :max="tabsWidthMax(selectedNode)" :step="tabsWidthStep(selectedNode)" :value="tabsWidthValue(selectedNode)" @input="onTabsWidthInput(selectedNode, $event)">
											<div class="pb-value-with-unit">
												<input class="pb-input pb-input-compact" type="number" min="1" :max="tabsWidthMax(selectedNode)" :step="tabsWidthStep(selectedNode)" :value="tabsWidthValue(selectedNode)" @input="onTabsWidthInput(selectedNode, $event)">
												<select class="pb-mini-unit" :value="tabsWidthUnit(selectedNode)" @change="setTabsWidthUnit(selectedNode, $event.target.value)">
												<option v-for="unit in tabsWidthUnits" :key="'tabs-width-unit-' + unit" :value="unit">{{ unit }}</option>
												</select>
											</div>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Justify</label>
										<div class="pb-seg-group">
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.justify==='flex-start'}" @click="selectedNode.settings.justify='flex-start'" title="Start"><i class="fas fa-align-left"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.justify==='center'}" @click="selectedNode.settings.justify='center'" title="Center"><i class="fas fa-align-center"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.justify==='flex-end'}" @click="selectedNode.settings.justify='flex-end'" title="End"><i class="fas fa-align-right"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.justify==='stretch'}" @click="selectedNode.settings.justify='stretch'" title="Stretch"><i class="fas fa-align-justify"></i></button>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Align Title</label>
										<div class="pb-seg-group">
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.alignTitle==='left'}" @click="selectedNode.settings.alignTitle='left'" title="Left"><i class="fas fa-align-left"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.alignTitle==='center'}" @click="selectedNode.settings.alignTitle='center'" title="Center"><i class="fas fa-align-center"></i></button>
											<button class="pb-seg-btn" :class="{active:selectedNode.settings.alignTitle==='right'}" @click="selectedNode.settings.alignTitle='right'" title="Right"><i class="fas fa-align-right"></i></button>
										</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">CSS Class</label>
										<input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-tabs">
									</div>
								</div>
							</details>
							<details class="pb-collapsible" open>
								<summary>Additional Settings</summary>
								<div class="pb-collapsible-body">
									<div class="pb-form-group">
										<label class="pb-form-label">Horizontal Scroll</label>
										<select class="pb-select" v-model="selectedNode.settings.horizontalScroll">
											<option :value="false">Disable</option>
											<option :value="true">Enable</option>
										</select>
										<div class="pb-form-note">Scroll tabs if they don't fit into their parent container.</div>
									</div>
									<div class="pb-form-group">
										<label class="pb-form-label">Breakpoint</label>
										<select class="pb-select" v-model="selectedNode.settings.breakpoint">
											<option v-for="option in tabsBreakpointOptions" :key="'tabs-breakpoint-' + option.value" :value="option.value">{{ option.label }}</option>
										</select>
										<div class="pb-form-note">Choose at which breakpoint tabs will automatically switch to a vertical ('accordion') layout.</div>
									</div>
								</div>
							</details>
						</div>
					</template>
					<template v-if="selectedType==='icon'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--icon">
							<div class="pb-widget-settings__group">
								<div class="pb-widget-settings__section-title">Icon</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Icon</label>
									<button type="button" class="pb-icon-picker-field" @click="openIconLibrary(selectedNode)">
										<div class="pb-icon-picker-preview">
											<i :class="selectedNode.settings.iconClass || 'far fa-star'"></i>
										</div>
										<div class="pb-icon-picker-copy">
											<div class="pb-icon-picker-name">{{ iconWidgetCurrentLabel(selectedNode) }}</div>
											<div class="pb-icon-picker-style">{{ iconWidgetCurrentStyleLabel(selectedNode) }}</div>
										</div>
										<i class="fas fa-chevron-right"></i>
									</button>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">View</label>
									<select class="pb-select" v-model="selectedNode.settings.view">
										<option v-for="option in iconWidgetViewOptions" :key="'icon-view-' + option.value" :value="option.value">{{ option.label }}</option>
									</select>
								</div>
								<div class="pb-form-group" v-if="iconWidgetUsesShape(selectedNode)">
									<label class="pb-form-label">Shape</label>
									<select class="pb-select" v-model="selectedNode.settings.shape">
										<option v-for="option in iconWidgetShapeOptions" :key="'icon-shape-' + option.value" :value="option.value">{{ option.label }}</option>
									</select>
								</div>
								<div class="pb-form-group">
									<label class="pb-form-label">Link</label>
									<div class="pb-input-with-action">
										<input class="pb-input" v-model="selectedNode.settings.link" placeholder="Paste URL or type">
										<button type="button" class="pb-field-action-btn" title="Link Options" @click="toggleIconLinkOptions(selectedNode)">
											<i class="fas fa-cog"></i>
										</button>
									</div>
								</div>
								<div v-if="isIconLinkOptionsOpen(selectedNode)" class="pb-icon-link-options">
									<label class="pb-icon-link-check">
										<input type="checkbox" v-model="selectedNode.settings.openInNewWindow">
										<span>Open in new window</span>
									</label>
									<label class="pb-icon-link-check">
										<input type="checkbox" v-model="selectedNode.settings.nofollow">
										<span>Add nofollow</span>
									</label>
									<div class="pb-form-group">
										<div class="pb-label-row">
											<label class="pb-form-label mb-0">Custom Attributes</label>
											<button class="pb-seg-btn pb-mini-btn" @click="selectedNode.settings.attributes=(selectedNode.settings.attributes||[]).concat({name:'',value:''})">
												<i class="fas fa-plus"></i>
											</button>
										</div>
										<div v-for="(attr,i) in (selectedNode.settings.attributes||[])" :key="'icon-attr-'+i" class="pb-attr-row">
											<input class="pb-input" v-model="attr.name" placeholder="key">
											<input class="pb-input" v-model="attr.value" placeholder="value">
											<button class="pb-btn icon-sm" @click="selectedNode.settings.attributes.splice(i,1)"><i class="fas fa-trash"></i></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='button'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--button">
							<div class="pb-widget-settings__group">
								<div class="pb-form-group"><label class="pb-form-label">Text</label><input class="pb-input" v-model="selectedNode.settings.text"></div>
								<div class="pb-form-group"><label class="pb-form-label">URL</label><input class="pb-input" v-model="selectedNode.settings.url"></div>
								<div class="pb-form-group"><label class="pb-form-label">Align</label><select class="pb-select" v-model="selectedNode.settings.align"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--options">
								<div class="pb-widget-settings__section-title">Options</div>
								<div class="pb-form-group pb-toggle-label-row pb-widget-settings__compact-toggle">
									<label class="pb-form-label mb-0">Open New Tab</label>
									<div class="pb-toggle-switch-wrap">
										<div class="pb-toggle-wrap">
											<input :id="'button-new-tab-' + selectedNode.id" type="checkbox" class="pb-toggle" v-model="selectedNode.settings.newTab">
											<label :for="'button-new-tab-' + selectedNode.id"></label>
										</div>
										<span class="pb-toggle-state">{{ selectedNode.settings.newTab ? 'On' : 'Off' }}</span>
									</div>
								</div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.className" placeholder="custom-button"></div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='divider'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--divider">
							<div class="pb-widget-settings__group">
								<div class="pb-form-group"><label class="pb-form-label">Style</label><select class="pb-select" v-model="selectedNode.settings.style"><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option></select></div>
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Width</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('divider-width')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('divider-width')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'divider-width-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('divider-width', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" :max="sizeControlMax(selectedNode, 'width', '100%')" :step="sizeControlStep(selectedNode, 'width', '100%')" :value="sizeControlDisplayValue(selectedNode, 'width', '100%') || 0" @input="onSizeControlInput(selectedNode, 'width', $event, { fallback: '100%', emptyToken: '100%' })">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" :max="sizeControlMax(selectedNode, 'width', '100%')" :step="sizeControlStep(selectedNode, 'width', '100%')" :value="sizeControlDisplayValue(selectedNode, 'width', '100%')" @input="onSizeControlInput(selectedNode, 'width', $event, { fallback: '100%', emptyToken: '100%' })">
											<select class="pb-mini-unit" :value="sizeControlUnit(selectedNode, 'width', '100%')" @change="setSizeControlUnit(selectedNode, 'width', $event.target.value, { fallback: '100%', emptyToken: '100%' })">
												<option v-for="unit in sizeControlUnits" :key="'divider-width-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
								</div>
								<div class="pb-form-group"><label class="pb-form-label">Thickness</label><input class="pb-input" v-model.number="selectedNode.settings.thickness" type="number"></div>
								<div class="pb-form-group">
									<label class="pb-form-label">Color</label>
									<div class="pb-color-row">
										<input type="color" class="pb-color-swatch" v-model="selectedNode.settings.color">
										<input class="pb-input coloris pb-coloris-input" v-model="selectedNode.settings.color" placeholder="#d0d7e6">
									</div>
								</div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-divider"></div>
							</div>
						</div>
					</template>
					<template v-if="selectedType==='spacer'">
						<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--spacer">
							<div class="pb-widget-settings__group">
								<div class="pb-form-group">
									<div class="pb-label-row pb-label-row-device">
										<label class="pb-form-label mb-0">Height</label>
										<div class="pb-control-device-wrap">
											<button class="pb-control-device-btn" @click.stop="openControlResponsiveMenu('spacer-height')" :title="'Responsive: ' + responsiveDeviceLabel()"><i :class="responsiveDeviceIcon()"></i></button>
											<div v-if="isControlResponsiveMenuOpen('spacer-height')" class="pb-control-device-menu">
												<button v-for="device in responsiveDevices" :key="'spacer-height-' + device.value" class="pb-control-device-item" :class="{active: responsiveDevice===device.value}" @click.stop="applyResponsiveDevice('spacer-height', device.value)">
													<i :class="device.icon"></i>
													<span>{{ deviceOptionLabel(device) }}</span>
												</button>
											</div>
										</div>
									</div>
									<div class="pb-range-value-row">
										<input type="range" class="pb-range" min="0" :max="spacerHeightMax(selectedNode)" :step="spacerHeightStep(selectedNode)" :value="spacerHeightValue(selectedNode)" @input="onSpacerHeightInput(selectedNode, $event)">
										<div class="pb-value-with-unit">
											<input class="pb-input pb-input-compact" type="number" min="0" :max="spacerHeightMax(selectedNode)" :step="spacerHeightStep(selectedNode)" :value="spacerHeightValue(selectedNode)" @input="onSpacerHeightInput(selectedNode, $event)">
											<select class="pb-mini-unit" :value="spacerHeightUnit(selectedNode)" @change="setSpacerHeightUnit(selectedNode, $event.target.value)">
												<option v-for="unit in sizeControlUnits" :key="'spacer-height-unit-' + unit" :value="unit">{{ unit }}</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
								<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="selectedNode.settings.cssClass" placeholder="custom-spacer"></div>
							</div>
						</div>
					</template>
				</div>
			</div>
		</div>

		<component v-if="customCss" :is="'style'">{{ customCss }}</component>
		<div class="pb-canvas-wrap" @click="clearSel(); closeWidthPreviewMenu()">
			<div class="pb-stage-window" @click.stop="closeWidthPreviewMenu()">
				<div class="pb-stage-toolbar">
					<div class="pb-stage-device-group">
						<button class="pb-stage-device-btn" :class="{ active: responsiveDevice==='mobile' }" @click="setResponsiveDevice('mobile')" title="Mobile"><i class="fas fa-mobile-alt"></i></button>
						<button class="pb-stage-device-btn" :class="{ active: responsiveDevice==='tablet' }" @click="setResponsiveDevice('tablet')" title="Tablet"><i class="fas fa-tablet-alt"></i></button>
						<button class="pb-stage-device-btn" :class="{ active: responsiveDevice==='desktop' }" @click="setResponsiveDevice('desktop')" title="Desktop"><i class="fas fa-desktop"></i></button>
					</div>
					<div class="pb-stage-width-control" @click.stop>
						<button type="button" class="pb-stage-width-pill" :class="{ 'is-interactive': responsiveDevice==='desktop', 'is-menu-open': widthPreviewMenuOpen }" :title="responsiveDevice==='desktop' ? 'Choose desktop preview width' : 'Preview width'" @click="toggleWidthPreviewMenu">
						<i class="fas fa-arrows-alt-h"></i>
						<span>{{ previewCanvasWidthLabel() }}</span>
						<i class="fas fa-chevron-down"></i>
						</button>
						<div v-if="responsiveDevice==='desktop' && widthPreviewMenuOpen" class="pb-stage-width-menu">
							<button v-for="option in desktopPreviewWidths" :key="'desktop-width-'+option.value" type="button" class="pb-stage-width-option" :class="{ active: desktopPreviewWidth===option.value }" @click="selectDesktopPreviewWidth(option.value)">
								<span>{{ option.label }}</span>
								<i v-if="desktopPreviewWidth===option.value" class="fas fa-check"></i>
							</button>
						</div>
					</div>
				</div>

				<div class="pb-canvas" :class="'is-' + responsiveDevice" :style="previewCanvasStyle()" @click="clearSel">
					<draggable
						v-model="rootNodes"
						item-key="id"
						:group="rootGroup"
						:clone="toolClone"
						class="pb-dropzone pb-dropzone-root"
						ghost-class="pb-ghost"
						dragover-class="is-drop-hover"
						@add="onRootAdd"
						@start="onDragStart"
						@end="onDragEnd"
					>
						<template #item="{ element }">
							<BuilderNode
								:node="element"
								:selected-id="selectedId"
								:selected-column-node-id="selectedColumnNodeId"
								:selected-column-id="selectedColumnId"
								:hovered-id="hoveredId"
								:responsive-device="responsiveDevice"
								:on-add-container="onAddContainer"
								:on-add-col="onAddCol"
								:on-select="selectNode"
								:on-select-column="selectColumn"
								:on-set-hover="setHoveredNode"
								:on-clear-hover="clearHoveredNode"
								:on-remove="removeNode"
								:on-duplicate="dupNode"
								:on-drag-start="onDragStart"
								:on-drag-end="onDragEnd"
								:on-start-column-resize="startColumnResize"
								:on-open-modal="openModal"
								:on-show-toolbox="showToolboxPanel"
								:pending-insert-target="pendingInsertTarget"
								:on-reroute-tabs-drop="rerouteTabsDropToNestedColumn"
								:on-accordion-runtime-for-node="accordionRuntimeForNode"
								:on-toggle-accordion-item="toggleAccordionItem"
								:on-reroute-accordion-drop="rerouteAccordionDropToNestedColumn"
								:on-track-dropzone-pointer="trackDropzonePointerFromEvent"
							/>
						</template>
						<template #footer>
							<div v-if="rootNodes.length===0" class="pb-empty-root">
								<i class="fas fa-plus"></i>
								<div class="mt-3">Drag Container, Grid, or Widget to start building</div>
							</div>
							<div v-else class="pb-root-followup-hint">
								<i class="fas fa-plus"></i>
								<div class="pb-root-followup-text">Drag Container, Grid, or Widget here to continue building</div>
							</div>
						</template>
					</draggable>
				</div>
			</div>
		</div>
	</div>

	<div v-if="columnResizeOverlay.visible" class="pb-col-resize-overlay" :style="{ left: columnResizeOverlay.x + 'px', top: columnResizeOverlay.y + 'px' }">{{ columnResizeOverlay.text }}</div>

	<teleport to="body">
		<div v-if="showCssEditor" class="pb-css-editor-modal" @click.self="closeCustomCssEditor">
			<div class="pb-css-editor-panel" :class="{ fullscreen: cssEditorFullscreen }">
				<div class="pb-css-editor-header">
					<div class="pb-css-editor-title"><i class="fas fa-code"></i><span>Custom CSS Editor</span></div>
					<div class="pb-css-editor-actions">
						<button type="button" class="pb-css-editor-icon-btn" :title="cssEditorFullscreen ? 'Exit fullscreen' : 'Fullscreen'" @click="cssEditorFullscreen = !cssEditorFullscreen">
							<i class="fas" :class="cssEditorFullscreen ? 'fa-compress' : 'fa-expand'"></i>
						</button>
						<button type="button" class="pb-css-editor-icon-btn danger" title="Close" @click="closeCustomCssEditor"><i class="fas fa-times"></i></button>
					</div>
				</div>
				<div class="pb-css-editor-body">
					<div class="pb-css-editor-hint">
						<i class="fas fa-info-circle"></i>
						<span>Tulis CSS kustom di sini. Gunakan <code>.class-name</code> atau <code>#id-name</code> dari field CSS ID / CSS Class pada widget, column, atau container.</span>
					</div>
					<div class="pb-css-editor-toolbar">
						<form class="pb-css-editor-tool" @submit.prevent="goToCustomCssLine">
							<label>Line</label>
							<input class="pb-css-editor-tool-input is-line" type="number" min="1" :max="customCssLineCount" v-model="customCssGotoLine" placeholder="Line">
							<button type="submit" class="pb-css-editor-tool-btn" title="Go to line"><i class="fas fa-arrow-down"></i></button>
						</form>
						<div class="pb-css-editor-tool is-search">
							<label>Search</label>
							<input class="pb-css-editor-tool-input" type="search" v-model="customCssSearchQuery" placeholder="Search code...">
							<button type="button" class="pb-css-editor-tool-btn" title="Find now" @click="searchCustomCssCode(true)"><i class="fas fa-search"></i></button>
						</div>
					</div>
					<div class="pb-css-editor-code-shell">
						<div ref="customCssEditorGutter" class="pb-css-editor-gutter">
							<button
								v-for="line in customCssLineNumbers"
								:key="'custom-css-line-'+line"
								type="button"
								class="pb-css-editor-line-number"
								:class="{ active: customCssActiveLine===line }"
								@click="customCssGotoLine = line; goToCustomCssLine()"
							>{{ line }}</button>
						</div>
						<textarea
							ref="customCssEditorTextarea"
							class="pb-css-editor-textarea"
							v-model="customCss"
							placeholder=".my-custom-class {
  color: #333;
  font-size: 18px;
}

#my-section {
  background: linear-gradient(135deg, #667eea, #764ba2);
  padding: 60px 0;
}"
							@keydown.tab.prevent="handleCustomCssTab"
							@keydown.esc="closeCustomCssEditor"
							@scroll="syncCustomCssEditorScroll"
							spellcheck="false"
							autocomplete="off"
						></textarea>
					</div>
				</div>
				<div class="pb-css-editor-footer">
					<div class="pb-css-editor-count"><i class="fas fa-code"></i> {{ customCssCharCount }} chars / {{ customCssLineCount }} lines</div>
					<div class="pb-css-editor-footer-actions">
						<button type="button" class="pb-css-editor-btn danger" @click="clearCustomCss"><i class="fas fa-trash"></i> Clear</button>
						<button type="button" class="pb-css-editor-btn primary" @click="applyCustomCssEditorChanges"><i class="fas fa-check"></i> Apply & Close</button>
					</div>
				</div>
			</div>
		</div>
	</teleport>

	<teleport to="body">
		<div v-if="showTextEditorModal && selectedType==='text_editor' && selectedNode" class="pb-rte-editor-modal" @click.self="closeTextEditorModal">
			<div class="pb-rte-editor-panel" :class="{ fullscreen: textEditorModalFullscreen }">
				<div class="pb-rte-editor-header">
					<div class="pb-rte-editor-title"><i class="fas fa-edit"></i><span>Text Editor</span></div>
					<div class="pb-rte-editor-actions">
						<button type="button" class="pb-rte-editor-icon-btn" :title="textEditorModalFullscreen ? 'Exit fullscreen' : 'Fullscreen'" @click="textEditorModalFullscreen = !textEditorModalFullscreen">
							<i class="fas" :class="textEditorModalFullscreen ? 'fa-compress' : 'fa-expand'"></i>
						</button>
						<button type="button" class="pb-rte-editor-icon-btn danger" title="Close" @click="closeTextEditorModal"><i class="fas fa-times"></i></button>
					</div>
				</div>
				<div class="pb-rte-editor-body">
					<div class="pb-rte-editor-hint">
						<i class="fas fa-info-circle"></i>
						<span>Edit konten teks dengan ruang yang lebih luas. Perubahan langsung tersinkron ke preview dan editor sidebar.</span>
					</div>
					<div class="pb-rte-editor-field">
						<CkEditorField :model-value="selectedNode.settings.html" @update:modelValue="setTextEditorHtml" />
					</div>
				</div>
				<div class="pb-rte-editor-footer">
					<div class="pb-rte-editor-count"><i class="fas fa-font"></i> {{ textEditorModalSummary }}</div>
					<div class="pb-rte-editor-footer-actions">
						<button type="button" class="pb-rte-editor-btn primary" @click="closeTextEditorModal"><i class="fas fa-check"></i> Apply & Close</button>
					</div>
				</div>
			</div>
		</div>
	</teleport>

	<teleport to="body">
		<div v-if="showIconLibraryModal" class="pb-icon-library-modal" @click.self="closeIconLibrary">
			<div class="pb-icon-library-panel">
				<div class="pb-icon-library-header">
					<div class="pb-icon-library-title"><i class="fab fa-font-awesome-flag"></i><span>ICON LIBRARY</span></div>
					<button type="button" class="pb-icon-library-close" title="Close" @click="closeIconLibrary"><i class="fas fa-times"></i></button>
				</div>
				<div class="pb-icon-library-body">
					<aside class="pb-icon-library-sidebar">
						<button
							v-for="group in iconLibraryGroups"
							:key="'icon-group-' + group.key"
							type="button"
							class="pb-icon-library-group"
							:class="{ active: iconLibraryGroup===group.key }"
							@click="iconLibraryGroup=group.key"
						>
							<i :class="group.icon"></i>
							<span>{{ group.label }}</span>
						</button>
					</aside>
					<div class="pb-icon-library-content">
						<div class="pb-icon-library-search">
							<input class="pb-input" type="search" v-model="iconLibrarySearch" placeholder="Filter by name...">
							<i class="fas fa-search"></i>
						</div>
						<div v-if="iconLibraryLoading" class="pb-icon-library-state">Loading local Font Awesome library...</div>
						<div v-else-if="iconLibraryError" class="pb-icon-library-state is-error">{{ iconLibraryError }}</div>
						<div v-else-if="!filteredIconLibraryIcons.length" class="pb-icon-library-state">No icons match this filter.</div>
						<div v-else class="pb-icon-library-grid">
							<button
								v-for="item in filteredIconLibraryIcons"
								:key="item.id"
								type="button"
								class="pb-icon-library-item"
								:class="{ active: iconLibrarySelected && iconLibrarySelected.id===item.id }"
								@click="selectIconLibraryItem(item)"
								@dblclick="selectIconLibraryItem(item); insertSelectedIcon()"
							>
								<i :class="item.className"></i>
								<span>{{ item.label }}</span>
							</button>
						</div>
					</div>
				</div>
				<div class="pb-icon-library-footer">
					<div class="pb-icon-library-footer-copy" v-if="iconLibrarySelected">
						<span>{{ iconLibrarySelected.label }}</span>
						<small>{{ fontAwesomeStyleLabel(iconLibrarySelected.style) }}</small>
					</div>
					<div class="pb-icon-library-footer-copy" v-else>Select an icon from the local library.</div>
					<div class="pb-icon-library-footer-actions">
						<button type="button" class="pb-icon-library-btn" @click="closeIconLibrary">Close</button>
						<button type="button" class="pb-icon-library-btn primary" :disabled="!iconLibrarySelected" @click="insertSelectedIcon">Insert</button>
					</div>
				</div>
			</div>
		</div>
	</teleport>

	<teleport to="body">
		<div v-if="modal.visible" class="pb-modal-backdrop" @click.self="closeModal">
			<div class="pb-modal">
				<div class="pb-modal-header"><button class="pb-modal-close" @click="closeModal"><i class="fas fa-times"></i></button></div>
				<div class="pb-modal-body">
					<template v-if="modal.step===1">
						<div class="pb-modal-title">Which layout would you like to use?</div>
						<div class="pb-layout-type-picker">
							<button class="pb-layout-type-card" @click="pickLayout('container_flex')">
								<div class="pb-layout-preview pb-layout-preview-flex"><div class="pb-lp-block" style="flex:2"></div><div class="pb-lp-block" style="flex:1"></div></div>
								<span class="pb-layout-type-label">Flexbox</span>
							</button>
							<button class="pb-layout-type-card" @click="pickLayout('container_grid')">
								<div class="pb-layout-preview pb-layout-preview-grid"><div class="pb-lp-block"></div><div class="pb-lp-block"></div><div class="pb-lp-block"></div><div class="pb-lp-block"></div></div>
								<span class="pb-layout-type-label">Grid</span>
							</button>
						</div>
					</template>
					<template v-if="modal.step===2">
						<div class="pb-modal-title">Select your structure</div>
						<template v-if="modal.layoutType==='container_flex'||modal.layoutType==='container'||modal.layoutType==='container_fluid'">
							<div class="pb-preset-grid">
								<button v-for="(p,i) in cPresets" :key="i" class="pb-preset-card" :title="p.label" @click="applyContPreset(p)">
									<div class="pb-preset-visual" :class="p.direction==='column'?'is-col':'is-row'">
										<template v-if="p.flexWidths">
											<div v-for="(w,wi) in p.flexWidths" :key="wi" class="pb-preset-block" :style="{flex:'0 0 '+w,width:w,minWidth:0}"></div>
										</template>
										<template v-else-if="p.direction==='column'">
											<div class="pb-preset-block" style="width:100%;height:100%"></div>
										</template>
										<template v-else>
											<div v-for="n in p.cols" :key="n" class="pb-preset-block" style="flex:1"></div>
										</template>
									</div>
								</button>
							</div>
						</template>
						<template v-else-if="modal.layoutType==='grid'||modal.layoutType==='container_grid'">
							<div class="pb-preset-grid">
								<button v-for="cols in gPresets" :key="cols" class="pb-preset-card" :title="cols+' Column(s)'" @click="applyGridPreset(cols)">
									<div class="pb-preset-visual is-row is-dashed"><div v-for="n in cols" :key="n" class="pb-preset-block"></div></div>
								</button>
							</div>
						</template>
						<button v-if="modal.layoutType==='container_flex'||modal.layoutType==='container_grid'" class="pb-modal-back" @click="modal.step=1">
							<i class="fas fa-arrow-left"></i> Back
						</button>
					</template>
				</div>
			</div>
		</div>
	</teleport>
</div>
		`,
	}).mount('#pbElementorApp');
})();
