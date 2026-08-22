(function (global) {
	'use strict';

	const definitions = new Map();
	const requiredFields = ['type', 'defaults', 'normalize'];
	let moduleCatalog = null;

	function clone(value) {
		return value == null ? value : JSON.parse(JSON.stringify(value));
	}

	function advancedDefaults() {
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
			advancedBackgroundType: 'none', advancedBackgroundColor: '', advancedBackgroundImage: '', advancedBackgroundPosition: 'center center', advancedBackgroundPositionX: '50%', advancedBackgroundPositionY: '50%', advancedBackgroundAttachment: 'scroll', advancedBackgroundRepeat: 'no-repeat', advancedBackgroundSize: 'cover', advancedBackgroundCustomSize: '100%',
			advancedGradientColorOne: '#ffffff', advancedGradientLocationOne: 0, advancedGradientColorTwo: '#000000', advancedGradientLocationTwo: 100, advancedGradientType: 'linear', advancedGradientAngle: 180, advancedGradientPosition: 'center center',
			advancedBackgroundTypeHover: 'none', advancedBackgroundColorHover: '', advancedBackgroundImageHover: '', advancedBackgroundPositionHover: 'center center', advancedBackgroundPositionXHover: '50%', advancedBackgroundPositionYHover: '50%', advancedBackgroundAttachmentHover: 'scroll', advancedBackgroundRepeatHover: 'no-repeat', advancedBackgroundSizeHover: 'cover', advancedBackgroundCustomSizeHover: '100%',
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
			widthMode: 'default', customWidth: '', alignSelf: 'auto', orderMode: 'default', order: '', sizeMode: 'none', flexGrow: 0, flexShrink: 1, gridColumnSpan: 1, gridRowSpan: 1,
			positionX: '0px', positionY: '0px', zIndex: '', stickyOffset: '0px',
			transformRotate: '0deg', transformPerspective: '0px', transformRotateX: '0deg', transformRotateY: '0deg', transformOffsetX: '0px', transformOffsetY: '0px', transformScale: 1, transformSkewX: '0deg', transformSkewY: '0deg', transformFlipHorizontal: false, transformFlipVertical: false,
			transformRotateHover: '0deg', transformPerspectiveHover: '0px', transformRotateXHover: '0deg', transformRotateYHover: '0deg', transformOffsetXHover: '0px', transformOffsetYHover: '0px', transformScaleHover: 1, transformSkewXHover: '0deg', transformSkewYHover: '0deg', transformFlipHorizontalHover: false, transformFlipVerticalHover: false,
			advancedBorderRadius: '0px', advancedBorderRadiusHover: '0px',
			maskSize: 'fit', maskScale: 100, maskPosition: 'center center', maskPositionX: '50%', maskPositionY: '50%', maskRepeat: 'no-repeat',
		};
		for (const [key, value] of Object.entries(responsiveDefaults)) {
			defaults[key] = value;
			defaults[key + 'Tablet'] = '';
			defaults[key + 'Mobile'] = '';
		}
		return defaults;
	}

	function normalizeAdvanced(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = advancedDefaults();
		for (const [key, value] of Object.entries(defaults)) {
			if (settings[key] === undefined) settings[key] = clone(value);
		}
		settings.displayConditions = Array.isArray(settings.displayConditions) ? settings.displayConditions : [];
		settings.attributes = Array.isArray(settings.attributes)
			? settings.attributes
				.map((attribute) => ({
					name: String(attribute?.name || attribute?.key || '').trim(),
					value: attribute?.value == null ? '' : String(attribute.value),
				}))
				.filter((attribute) => attribute.name)
			: [];
		settings.cacheMode = ['default', 'inactive', 'active'].includes(settings.cacheMode) ? settings.cacheMode : 'default';
		const widthModes = ['default', 'full', 'inline', 'custom'];
		settings.widthMode = widthModes.includes(settings.widthMode) ? settings.widthMode : 'default';
		for (const key of ['widthModeTablet', 'widthModeMobile']) {
			const value = settings[key];
			settings[key] = value === '' || value == null ? '' : (widthModes.includes(value) ? value : '');
		}
		settings.position = ['default', 'absolute', 'fixed'].includes(settings.position) ? settings.position : 'default';
		settings.animateWithAI = false;
		return settings;
	}

	function assertDefinition(definition) {
		if (!definition || typeof definition !== 'object') {
			throw new TypeError('Page Builder Elementor widget definition must be an object.');
		}

		for (const field of requiredFields) {
			if (definition[field] == null || definition[field] === '') {
				throw new TypeError('Page Builder Elementor widget definition is missing "' + field + '".');
			}
		}

		if (typeof definition.defaults !== 'function' || typeof definition.normalize !== 'function') {
			throw new TypeError('Widget defaults and normalize fields must be functions.');
		}
	}

	function configure(catalog) {
		if (definitions.size) {
			throw new Error('Page Builder Elementor module catalog must be configured before definitions register.');
		}

		if (!catalog || typeof catalog !== 'object' || Array.isArray(catalog)) {
			throw new TypeError('Page Builder Elementor module catalog must be an object.');
		}

		const configured = new Map();
		for (const [catalogType, rawEntry] of Object.entries(catalog)) {
			if (!rawEntry || typeof rawEntry !== 'object' || Array.isArray(rawEntry)) {
				throw new TypeError('Page Builder Elementor module catalog entry must be an object: ' + catalogType);
			}

			const type = String(rawEntry.type || catalogType).trim();
			if (type !== catalogType || !/^[a-z][a-z0-9_]*$/.test(type)) {
				throw new TypeError('Invalid Page Builder Elementor module catalog type: ' + catalogType);
			}

			for (const field of ['label', 'category', 'icon']) {
				if (rawEntry[field] == null || rawEntry[field] === '') {
					throw new TypeError('Page Builder Elementor module catalog entry is missing "' + field + '": ' + type);
				}
			}

			if (!rawEntry.assets || typeof rawEntry.assets.canvas !== 'string' || typeof rawEntry.assets.settings !== 'string') {
				throw new TypeError('Page Builder Elementor module catalog entry has invalid assets: ' + type);
			}

			configured.set(type, Object.freeze({
				...clone(rawEntry),
				type,
				toolbox: rawEntry.toolbox !== false,
			}));
		}

		moduleCatalog = configured;
	}

	function register(definition) {
		assertDefinition(definition);
		const type = String(definition.type).trim();
		const catalogEntry = moduleCatalog?.get(type) || null;

		if (moduleCatalog && !catalogEntry) {
			throw new Error('Page Builder Elementor widget type is not present in the module catalog: ' + type);
		}

		if (definitions.has(type)) {
			throw new Error('Duplicate Page Builder Elementor widget type: ' + type);
		}
		const advancedCapabilities = Array.isArray(catalogEntry?.advanced?.capabilities)
			? catalogEntry.advanced.capabilities
			: [];
		const ownsCanonicalAdvanced = !!catalogEntry?.advanced
			&& !advancedCapabilities.includes('minimal-advanced')
			&& !advancedCapabilities.includes('legacy-layout');

		const registered = Object.freeze({
			...definition,
			...(catalogEntry || {}),
			type,
			canvas: catalogEntry?.assets.canvas,
			settings: catalogEntry?.assets.settings,
			toolbox: catalogEntry?.toolbox === true,
			defaults() {
				const moduleDefaults = clone(definition.defaults()) || {};
				return ownsCanonicalAdvanced ? { ...advancedDefaults(), ...moduleDefaults } : moduleDefaults;
			},
			normalize(node) {
				const normalized = definition.normalize(node);
				if (ownsCanonicalAdvanced && normalized?.settings) normalizeAdvanced(normalized.settings);
				return normalized;
			},
		});

		definitions.set(type, registered);
		return registered;
	}

	function get(type) {
		return definitions.get(String(type || '').trim()) || null;
	}

	function all() {
		return Array.from(definitions.values());
	}

	function toolbox() {
		return all()
			.filter((definition) => definition.toolbox)
			.reduce((groups, definition) => {
				const category = String(definition.category || 'basic');
				(groups[category] ||= []).push({
					type: definition.type,
					label: definition.label,
					icon: definition.icon,
				});
				return groups;
			}, {});
	}

	global.PageBuilderElementorV24Widgets = Object.freeze({ configure, register, get, all, toolbox, advancedDefaults, normalizeAdvanced });
})(window);
