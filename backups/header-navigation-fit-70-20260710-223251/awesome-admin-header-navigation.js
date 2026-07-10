const editor = document.getElementById('headerNavigationEditor');
		const editorOptions = window.headerNavigationEditorOptions || {};
		const previewViewports = {
			desktop: { label: 'Desktop', width: 1440, height: 900 },
			tablet: { label: 'Tablet', width: 820, height: 1180 },
			mobile: { label: 'Mobile', width: 430, height: 932 }
		};

		const sampleMenus = [
			{ label: 'Home', url: '#home', active: true },
			{ label: 'Solutions', url: '#solutions' },
			{ label: 'Products', url: '#products' },
			{ label: 'Company', url: '#company' },
			{ label: 'Contact', url: '#contact' },
			{ label: 'Partners', url: '#partners' },
			{ label: 'Blog', url: '#blog' },
			{ label: 'Support', url: '#support' },
			{ label: 'Pricing', url: '#pricing' }
		];

		let currentConfig = null;

		const state = {
			menus: sampleMenus.map((item, index) => ({ ...item, active: index === 0 })),
			logoPosition: 'left',
			menuPosition: 'left',
			containerMode: 'container',
			headerBehavior: 'stay',
			transparentColorMode: 'auto',
			linkShape: 'default',
			leafDirection: 'forward'
		};

		const els = {
			root: editor,
			previewFrame: document.getElementById('previewFrame'),
			previewViewport: document.getElementById('previewViewport'),
			previewScaleStatus: document.getElementById('previewScaleStatus'),
			header: document.getElementById('siteHeader'),
			headerInner: document.getElementById('headerInner'),
			headerContainer: document.getElementById('headerContainer'),
			deviceStage: document.getElementById('deviceStage'),
			jsonOutput: document.getElementById('jsonOutput')
		};

		const controls = {
			headerBg: document.getElementById('headerBg'),
			scrolledBg: document.getElementById('scrolledBg'),
			headerText: document.getElementById('headerText'),
			linkColor: document.getElementById('linkColor'),
			linkHover: document.getElementById('linkHover'),
			linkHoverBorder: document.getElementById('linkHoverBorder'),
			linkHoverBorderLinked: document.getElementById('linkHoverBorderLinked'),
			linkFocus: document.getElementById('linkFocus'),
			linkFocusBorder: document.getElementById('linkFocusBorder'),
			linkFocusBorderLinked: document.getElementById('linkFocusBorderLinked'),
			linkActive: document.getElementById('linkActive'),
			linkActiveBorder: document.getElementById('linkActiveBorder'),
			linkActiveBorderLinked: document.getElementById('linkActiveBorderLinked'),
			linkShadowEnabled: document.getElementById('linkShadowEnabled'),
			linkShadowUnit: document.getElementById('linkShadowUnit'),
			linkShadowX: document.getElementById('linkShadowX'),
			linkShadowY: document.getElementById('linkShadowY'),
			linkShadowBlur: document.getElementById('linkShadowBlur'),
			linkShadowSpread: document.getElementById('linkShadowSpread'),
			linkShadowColor: document.getElementById('linkShadowColor'),
			linkShadowInset: document.getElementById('linkShadowInset'),
			innerBg: document.getElementById('innerBg'),
			transparentStart: document.getElementById('transparentStart'),
			transparentColorPanel: document.getElementById('transparentColorPanel'),
			transparentCustomFields: document.getElementById('transparentCustomFields'),
			transparentText: document.getElementById('transparentText'),
			transparentHover: document.getElementById('transparentHover'),
			transparentFocus: document.getElementById('transparentFocus'),
			transparentActive: document.getElementById('transparentActive'),
			animateScroll: document.getElementById('animateScroll'),
			scrollSim: document.getElementById('scrollSim'),
			headerHeight: document.getElementById('headerHeight'),
			headerHeightUnit: document.getElementById('headerHeightUnit'),
			headerRadiusTop: document.getElementById('headerRadiusTop'),
			headerRadiusRight: document.getElementById('headerRadiusRight'),
			headerRadiusBottom: document.getElementById('headerRadiusBottom'),
			headerRadiusLeft: document.getElementById('headerRadiusLeft'),
			headerRadiusUnit: document.getElementById('headerRadiusUnit'),
			headerRadiusLinked: document.getElementById('headerRadiusLinked'),
			headerPaddingTop: document.getElementById('headerPaddingTop'),
			headerPaddingRight: document.getElementById('headerPaddingRight'),
			headerPaddingBottom: document.getElementById('headerPaddingBottom'),
			headerPaddingLeft: document.getElementById('headerPaddingLeft'),
			headerPaddingUnit: document.getElementById('headerPaddingUnit'),
			headerPaddingLinked: document.getElementById('headerPaddingLinked'),
			navRadiusTop: document.getElementById('navRadiusTop'),
			navRadiusRight: document.getElementById('navRadiusRight'),
			navRadiusBottom: document.getElementById('navRadiusBottom'),
			navRadiusLeft: document.getElementById('navRadiusLeft'),
			navRadiusUnit: document.getElementById('navRadiusUnit'),
			navRadiusLinked: document.getElementById('navRadiusLinked'),
			leafDirectionControl: document.getElementById('leafDirectionControl'),
			containerMarginTop: document.getElementById('containerMarginTop'),
			containerMarginRight: document.getElementById('containerMarginRight'),
			containerMarginBottom: document.getElementById('containerMarginBottom'),
			containerMarginLeft: document.getElementById('containerMarginLeft'),
			containerMarginUnit: document.getElementById('containerMarginUnit'),
			containerMarginLinked: document.getElementById('containerMarginLinked'),
			deviceMode: document.getElementById('deviceMode')
		};

		const boxControls = {
			headerRadius: {
				sides: ['headerRadiusTop', 'headerRadiusRight', 'headerRadiusBottom', 'headerRadiusLeft'],
				unit: 'headerRadiusUnit',
				linked: 'headerRadiusLinked',
				defaults: ['18', '18', '18', '18']
			},
			headerPadding: {
				sides: ['headerPaddingTop', 'headerPaddingRight', 'headerPaddingBottom', 'headerPaddingLeft'],
				unit: 'headerPaddingUnit',
				linked: 'headerPaddingLinked',
				defaults: ['10', '24', '10', '24']
			},
			navRadius: {
				sides: ['navRadiusTop', 'navRadiusRight', 'navRadiusBottom', 'navRadiusLeft'],
				unit: 'navRadiusUnit',
				linked: 'navRadiusLinked',
				defaults: ['0', '0', '0', '0']
			},
			containerMargin: {
				sides: ['containerMarginTop', 'containerMarginRight', 'containerMarginBottom', 'containerMarginLeft'],
				unit: 'containerMarginUnit',
				linked: 'containerMarginLinked',
				defaults: ['0', '0', '0', '0']
			}
		};

		const linkedColorControls = {
			active: {
				source: 'linkActive',
				target: 'linkActiveBorder',
				linked: 'linkActiveBorderLinked',
				fallback: '#e01d24',
				label: 'active'
			},
			hover: {
				source: 'linkHover',
				target: 'linkHoverBorder',
				linked: 'linkHoverBorderLinked',
				fallback: '#e01d24',
				label: 'hover'
			},
			focus: {
				source: 'linkFocus',
				target: 'linkFocusBorder',
				linked: 'linkFocusBorderLinked',
				fallback: '#c4121a',
				label: 'focus'
			}
		};

		const linkRadiusProfiles = {
			default: null,
			leafForward: null,
			leafReverse: null
		};

		const responsiveModes = ['desktop', 'tablet', 'mobile'];
		const responsiveDeviceModes = responsiveModes;
		const responsiveDeviceMeta = {
			desktop: { icon: 'fas fa-desktop', label: 'Desktop' },
			tablet: { icon: 'fas fa-tablet-alt', label: 'Tablet Portrait' },
			mobile: { icon: 'fas fa-mobile-alt', label: 'Mobile Portrait' }
		};
		let globalResponsiveMode = 'desktop';
		let openResponsiveMenu = null;
		const responsiveBoxProfiles = {};

		function setVariable(name, value) {
			els.root.style.setProperty(name, value);
		}

		function unitValue(inputId, fallback = '0') {
			const numberValue = controls[inputId].value === '' ? fallback : controls[inputId].value;
			const unit = controls[`${inputId}Unit`].value;
			return `${numberValue}${unit}`;
		}

		function cloneBoxProfile(profile) {
			return {
				values: profile.values.slice(),
				unit: profile.unit,
				linked: profile.linked
			};
		}

		function createBoxProfile(name, values = boxControls[name].defaults, unit = 'px', linked = isBoxLinked(name)) {
			return {
				values: values.slice(),
				unit,
				linked
			};
		}

		function readBoxProfileFromControls(name) {
			const config = boxControls[name];
			return {
				values: config.sides.map((id, index) => controls[id].value === '' ? config.defaults[index] : controls[id].value),
				unit: controls[config.unit].value,
				linked: isBoxLinked(name)
			};
		}

		function writeBoxProfileToControls(name, profile) {
			controls[boxControls[name].unit].value = profile.unit;
			setBoxLinked(name, profile.linked);
			setBoxValues(name, profile.values);
		}

		function normalizeResponsiveMode(mode) {
			return responsiveModes.includes(mode) ? mode : 'desktop';
		}

		function responsiveModeMeta(mode = globalResponsiveMode) {
			return responsiveDeviceMeta[normalizeResponsiveMode(mode)] || responsiveDeviceMeta.desktop;
		}

		function createResponsiveBoxState(profile, mode = globalResponsiveMode) {
			return {
				mode: normalizeResponsiveMode(mode),
				all: cloneBoxProfile(profile),
				desktop: null,
				tablet: null,
				mobile: null
			};
		}

		function cloneResponsiveBoxState(stateValue) {
			return {
				mode: normalizeResponsiveMode(stateValue.mode),
				all: cloneBoxProfile(stateValue.all),
				desktop: stateValue.desktop ? cloneBoxProfile(stateValue.desktop) : null,
				tablet: stateValue.tablet ? cloneBoxProfile(stateValue.tablet) : null,
				mobile: stateValue.mobile ? cloneBoxProfile(stateValue.mobile) : null
			};
		}

		function initResponsiveBoxProfiles() {
			Object.keys(boxControls).forEach(name => {
				responsiveBoxProfiles[name] = createResponsiveBoxState(readBoxProfileFromControls(name));
			});
			syncAllResponsiveBoxControls();
		}

		function activeResponsiveMode(name) {
			return normalizeResponsiveMode(responsiveBoxProfiles[name]?.mode || globalResponsiveMode);
		}

		function saveActiveBoxProfile(name) {
			const store = responsiveBoxProfiles[name];
			if (!store) return;
			const mode = activeResponsiveMode(name);
			store.mode = mode;
			store[mode] = readBoxProfileFromControls(name);
			syncResponsiveBoxControl(name);
		}

		function profileForMode(name, mode) {
			const store = responsiveBoxProfiles[name];
			if (!store) return readBoxProfileFromControls(name);
			const safeMode = normalizeResponsiveMode(mode);
			return store[safeMode] || store.all;
		}

		function effectiveBoxProfile(name, device = controls.deviceMode.value) {
			const mode = responsiveDeviceModes.includes(device) ? device : globalResponsiveMode;
			return profileForMode(name, mode);
		}

		function profileCssValues(name, profile) {
			const config = boxControls[name];
			return config.sides.map((id, index) => {
				const value = profile.values[index] === '' ? config.defaults[index] : profile.values[index];
				return `${value}${profile.unit}`;
			});
		}

		function boxControlValues(name) {
			return profileCssValues(name, effectiveBoxProfile(name));
		}

		function boxControlCss(name) {
			return boxControlValues(name).join(' ');
		}

		function boxProfileJson(name, profile) {
			if (!profile) return null;
			const [top, right, bottom, left] = profileCssValues(name, profile);
			return {
				top,
				right,
				bottom,
				left,
				unit: profile.unit,
				linked: profile.linked
			};
		}

		function boxControlJson(name) {
			const effective = effectiveBoxProfile(name);
			const effectiveJson = boxProfileJson(name, effective);
			const store = responsiveBoxProfiles[name];
			return {
				...effectiveJson,
				mode: activeResponsiveMode(name),
				preview_device: controls.deviceMode.value,
				responsive: {
					all: boxProfileJson(name, store?.all),
					desktop: boxProfileJson(name, store?.desktop),
					tablet: boxProfileJson(name, store?.tablet),
					mobile: boxProfileJson(name, store?.mobile)
				}
			};
		}

		function setBoxVariablePrefix(prefix, name) {
			const [top, right, bottom, left] = boxControlValues(name);
			setVariable(`${prefix}-top`, top);
			setVariable(`${prefix}-right`, right);
			setVariable(`${prefix}-bottom`, bottom);
			setVariable(`${prefix}-left`, left);
		}

		function isBoxLinked(name) {
			return controls[boxControls[name].linked].classList.contains('is-active');
		}

		function syncLinkedBoxValues(name, sourceId) {
			if (!isBoxLinked(name)) return;

			const sourceValue = controls[sourceId].value;
			boxControls[name].sides.forEach(id => {
				if (id !== sourceId) controls[id].value = sourceValue;
			});
		}

		function setBoxValues(name, values) {
			boxControls[name].sides.forEach((id, index) => {
				controls[id].value = values[index];
			});
		}

		function setBoxLinked(name, active) {
			const button = controls[boxControls[name].linked];
			button.classList.toggle('is-active', active);
			button.setAttribute('aria-pressed', String(active));
			const icon = button.querySelector('i');
			if (icon) {
				icon.classList.toggle('fa-link', active);
				icon.classList.toggle('fa-unlink', !active);
			}
		}

		function syncResponsiveBoxControl(name) {
			const store = responsiveBoxProfiles[name];
			if (!store) return;

			const activeMode = activeResponsiveMode(name);
			const meta = responsiveModeMeta(activeMode);
			const isOpen = openResponsiveMenu === name;

			document.querySelectorAll(`.responsive-mode-control[data-responsive-control="${name}"]`).forEach(group => {
				group.classList.toggle('is-open', isOpen);

				const trigger = group.querySelector('[data-responsive-trigger]');
				if (trigger) {
					const icon = trigger.querySelector('i');
					if (icon) icon.className = meta.icon;
					trigger.classList.toggle('has-override', Boolean(store[activeMode]));
					trigger.setAttribute('aria-expanded', String(isOpen));
					trigger.setAttribute('aria-label', `Responsive device: ${meta.label}`);
					trigger.setAttribute('title', `Responsive: ${meta.label}`);
				}

				const menu = group.querySelector('.responsive-mode-menu');
				if (menu) menu.hidden = !isOpen;

				group.querySelectorAll('button[data-responsive-mode]').forEach(button => {
					const mode = normalizeResponsiveMode(button.dataset.responsiveMode);
					const active = activeMode === mode;
					const hasOverride = Boolean(store[mode]);
					button.classList.toggle('is-active', active);
					button.classList.toggle('has-override', hasOverride);
					button.setAttribute('aria-selected', String(active));
				});
			});
		}

		function syncAllResponsiveBoxControls() {
			Object.keys(boxControls).forEach(syncResponsiveBoxControl);
		}

		function closeResponsiveBoxMenu() {
			if (!openResponsiveMenu) return;
			openResponsiveMenu = null;
			syncAllResponsiveBoxControls();
		}

		function toggleResponsiveBoxMenu(name) {
			openResponsiveMenu = openResponsiveMenu === name ? null : name;
			syncAllResponsiveBoxControls();
		}

		function saveAllActiveBoxProfiles() {
			Object.keys(boxControls).forEach(saveActiveBoxProfile);
			if (responsiveBoxProfiles.navRadius) {
				linkRadiusProfiles[currentLinkRadiusProfileKey()] = cloneResponsiveBoxState(responsiveBoxProfiles.navRadius);
			}
		}

		function switchResponsiveBoxMode(name, mode) {
			const nextMode = normalizeResponsiveMode(mode);
			saveAllActiveBoxProfiles();
			globalResponsiveMode = nextMode;
			controls.deviceMode.value = nextMode;
			Object.keys(boxControls).forEach(controlName => {
				const store = responsiveBoxProfiles[controlName];
				if (!store) return;
				store.mode = nextMode;
				writeBoxProfileToControls(controlName, profileForMode(controlName, nextMode));
			});
			openResponsiveMenu = null;
			syncAllResponsiveBoxControls();
		}

		function currentLinkRadiusProfileKey(shape = state.linkShape, direction = state.leafDirection) {
			if (shape !== 'leaf') return 'default';
			return direction === 'reverse' ? 'leafReverse' : 'leafForward';
		}

		function readLinkRadiusProfile() {
			saveActiveBoxProfile('navRadius');
			return cloneResponsiveBoxState(responsiveBoxProfiles.navRadius);
		}

		function defaultLinkRadiusProfile(key) {
			if (key === 'leafReverse') {
				return createResponsiveBoxState(createBoxProfile('navRadius', ['28', '0', '28', '0'], 'px', false));
			}

			if (key === 'leafForward') {
				return createResponsiveBoxState(createBoxProfile('navRadius', ['0', '28', '0', '28'], 'px', false));
			}

			return createResponsiveBoxState(createBoxProfile('navRadius', ['0', '0', '0', '0'], 'px', true));
		}

		function writeLinkRadiusProfile(profile) {
			responsiveBoxProfiles.navRadius = cloneResponsiveBoxState(profile);
			const mode = activeResponsiveMode('navRadius');
			writeBoxProfileToControls('navRadius', profileForMode('navRadius', mode));
			syncResponsiveBoxControl('navRadius');
		}

		function saveCurrentLinkRadiusProfile() {
			linkRadiusProfiles[currentLinkRadiusProfileKey()] = readLinkRadiusProfile();
		}

		function loadLinkRadiusProfile(key = currentLinkRadiusProfileKey()) {
			if (!linkRadiusProfiles[key]) {
				linkRadiusProfiles[key] = defaultLinkRadiusProfile(key);
			}
			writeLinkRadiusProfile(linkRadiusProfiles[key]);
		}

		function syncCurrentLinkRadiusProfile() {
			saveActiveBoxProfile('navRadius');
			linkRadiusProfiles[currentLinkRadiusProfileKey()] = cloneResponsiveBoxState(responsiveBoxProfiles.navRadius);
		}

		function switchLinkRadiusProfile(nextShape, nextDirection = state.leafDirection) {
			saveCurrentLinkRadiusProfile();
			state.linkShape = nextShape;
			state.leafDirection = nextDirection;
			loadLinkRadiusProfile();
			syncLinkShapeControls();
		}

		function applyLinkShapePreset() {
			loadLinkRadiusProfile();
		}

		function syncLinkShapeControls() {
			controls.leafDirectionControl.hidden = state.linkShape !== 'leaf';
		}

		function colorValue(inputId, fallback = '#000000') {
			const value = controls[inputId].value.trim();
			if (value === '') return 'transparent';
			const isHex = /^#([0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/.test(value);
			const isFunctional = /^(rgb|rgba|hsl|hsla)\(/i.test(value);
			const isKeyword = value.toLowerCase() === 'transparent';
			return isHex || isFunctional || isKeyword ? value : fallback;
		}

		function clampNumber(value, min, max) {
			return Math.min(max, Math.max(min, value));
		}

		function parseAlpha(value) {
			if (!value) return 1;
			const normalized = value.trim();
			if (normalized.endsWith('%')) return clampNumber(Number(normalized.slice(0, -1)) / 100, 0, 1);
			return clampNumber(Number(normalized), 0, 1);
		}

		function parseColorChannel(value) {
			const normalized = value.trim();
			if (normalized.endsWith('%')) return Math.round(clampNumber(Number(normalized.slice(0, -1)), 0, 100) * 2.55);
			return clampNumber(Number(normalized), 0, 255);
		}

		function hslToRgb(hue, saturation, lightness) {
			const normalizedHue = (((hue % 360) + 360) % 360) / 360;
			const s = clampNumber(saturation, 0, 100) / 100;
			const l = clampNumber(lightness, 0, 100) / 100;

			if (s === 0) {
				const channel = Math.round(l * 255);
				return { r: channel, g: channel, b: channel };
			}

			const hueToChannel = (p, q, t) => {
				let next = t;
				if (next < 0) next += 1;
				if (next > 1) next -= 1;
				if (next < 1 / 6) return p + (q - p) * 6 * next;
				if (next < 1 / 2) return q;
				if (next < 2 / 3) return p + (q - p) * (2 / 3 - next) * 6;
				return p;
			};

			const q = l < .5 ? l * (1 + s) : l + s - (l * s);
			const p = (2 * l) - q;
			return {
				r: Math.round(hueToChannel(p, q, normalizedHue + 1 / 3) * 255),
				g: Math.round(hueToChannel(p, q, normalizedHue) * 255),
				b: Math.round(hueToChannel(p, q, normalizedHue - 1 / 3) * 255)
			};
		}

		function parseCssColor(value) {
			const normalized = value.trim().toLowerCase();
			if (normalized === 'transparent') return { r: 255, g: 255, b: 255, a: 0 };

			const hex = normalized.match(/^#([0-9a-f]{6})([0-9a-f]{2})?$/i);
			if (hex) {
				return {
					r: parseInt(hex[1].slice(0, 2), 16),
					g: parseInt(hex[1].slice(2, 4), 16),
					b: parseInt(hex[1].slice(4, 6), 16),
					a: hex[2] ? parseInt(hex[2], 16) / 255 : 1
				};
			}

			const rgb = normalized.match(/^rgba?\((.+)\)$/i);
			if (rgb) {
				const parts = rgb[1].replace(/\//g, ',').split(',').map(part => part.trim()).filter(Boolean);
				if (parts.length >= 3) {
					return {
						r: parseColorChannel(parts[0]),
						g: parseColorChannel(parts[1]),
						b: parseColorChannel(parts[2]),
						a: parseAlpha(parts[3])
					};
				}
			}

			const hsl = normalized.match(/^hsla?\((.+)\)$/i);
			if (hsl) {
				const parts = hsl[1].replace(/\//g, ',').split(',').map(part => part.trim()).filter(Boolean);
				if (parts.length >= 3) {
					const rgbColor = hslToRgb(Number(parts[0].replace('deg', '')), Number(parts[1].replace('%', '')), Number(parts[2].replace('%', '')));
					return { ...rgbColor, a: parseAlpha(parts[3]) };
				}
			}

			return { r: 224, g: 29, b: 36, a: 1 };
		}

		function compositeOnWhite(color) {
			if (color.a >= 1) return color;
			return {
				r: Math.round((color.r * color.a) + (255 * (1 - color.a))),
				g: Math.round((color.g * color.a) + (255 * (1 - color.a))),
				b: Math.round((color.b * color.a) + (255 * (1 - color.a))),
				a: 1
			};
		}

		function relativeLuminance(color) {
			const channel = value => {
				const normalized = value / 255;
				return normalized <= .03928 ? normalized / 12.92 : ((normalized + .055) / 1.055) ** 2.4;
			};

			return (.2126 * channel(color.r)) + (.7152 * channel(color.g)) + (.0722 * channel(color.b));
		}

		function contrastRatio(a, b) {
			const lighter = Math.max(a, b);
			const darker = Math.min(a, b);
			return (lighter + .05) / (darker + .05);
		}

		function readableTextColor(background) {
			const bg = compositeOnWhite(parseCssColor(background));
			const bgLuminance = relativeLuminance(bg);
			const whiteContrast = contrastRatio(1, bgLuminance);
			const darkContrast = contrastRatio(relativeLuminance({ r: 16, g: 24, b: 40 }), bgLuminance);
			return whiteContrast >= darkContrast ? '#ffffff' : '#101828';
		}

		function transparentModeIsCustom() {
			return state.transparentColorMode === 'custom';
		}

		function transparentTextValue() {
			return transparentModeIsCustom()
				? colorValue('transparentText', '#ffffff')
				: '#ffffff';
		}

		function transparentStateColor(stateName) {
			const map = {
				hover: { custom: 'transparentHover', source: 'linkHover', fallback: '#e01d24' },
				focus: { custom: 'transparentFocus', source: 'linkFocus', fallback: '#c4121a' },
				active: { custom: 'transparentActive', source: 'linkActive', fallback: '#e01d24' }
			};
			const config = map[stateName];
			const inputId = transparentModeIsCustom() ? config.custom : config.source;
			return colorValue(inputId, config.fallback);
		}

		function transparentColorConfig() {
			const hover = transparentStateColor('hover');
			const focus = transparentStateColor('focus');
			const active = transparentStateColor('active');
			return {
				mode: state.transparentColorMode,
				header_text: transparentTextValue(),
				link: transparentTextValue(),
				hover,
				hover_text: readableTextColor(hover),
				hover_border: hover,
				focus,
				focus_text: readableTextColor(focus),
				focus_border: focus,
				active,
				active_text: readableTextColor(active),
				active_border: active
			};
		}

		function applyTransparentColors() {
			const transparent = transparentColorConfig();
			setVariable('--transparent-header-text', transparent.header_text);
			setVariable('--transparent-link-color', transparent.link);
			setVariable('--transparent-hover-bg', transparent.hover);
			setVariable('--transparent-hover-text', transparent.hover_text);
			setVariable('--transparent-hover-border', transparent.hover_border);
			setVariable('--transparent-focus-bg', transparent.focus);
			setVariable('--transparent-focus-text', transparent.focus_text);
			setVariable('--transparent-focus-border', transparent.focus_border);
			setVariable('--transparent-active-bg', transparent.active);
			setVariable('--transparent-active-text', transparent.active_text);
			setVariable('--transparent-active-border', transparent.active_border);
		}

		function syncTransparentColorControls() {
			const enabled = scrollEffectsEnabled() && controls.transparentStart.checked;
			const custom = enabled && transparentModeIsCustom();
			controls.transparentColorPanel.hidden = !enabled;
			controls.transparentCustomFields.hidden = !custom;

			controls.transparentColorPanel.querySelectorAll('[data-control="transparentColorMode"] button').forEach(button => {
				button.disabled = !enabled;
				button.setAttribute('aria-disabled', String(!enabled));
			});

			[
				'transparentText',
				'transparentHover',
				'transparentFocus',
				'transparentActive'
			].forEach(id => setControlDisabled(controls[id], !custom));
		}

		function isTransparentColor(inputId) {
			const value = colorValue(inputId, 'transparent').replace(/\s+/g, '').toLowerCase();
			return value === 'transparent'
				|| value === '#00000000'
				|| value === 'rgba(0,0,0,0)'
				|| value === 'hsla(0,0%,0%,0)';
		}

		function updateColorisThumbnail(input) {
			const swatch = input.closest('.clr-field')?.querySelector('button');
			if (!swatch) return;
			swatch.style.backgroundColor = input.value || 'transparent';
			swatch.style.color = input.value || 'transparent';
		}

		function isLinkedColor(name) {
			return controls[linkedColorControls[name].linked].classList.contains('is-active');
		}

		function syncLinkedColorControl(name) {
			const config = linkedColorControls[name];
			const linked = isLinkedColor(name);
			if (linked) {
				controls[config.target].value = controls[config.source].value;
				updateColorisThumbnail(controls[config.target]);
			}

			setControlDisabled(controls[config.target], linked);
			controls[config.linked].setAttribute('aria-pressed', String(linked));
			controls[config.linked].setAttribute('aria-label', linked
				? `Unlink ${config.label} border color from ${config.label} link color`
				: `Link ${config.label} border color to ${config.label} link color`);

			const icon = controls[config.linked].querySelector('i');
			if (icon) {
				icon.classList.toggle('fa-link', linked);
				icon.classList.toggle('fa-unlink', !linked);
			}
		}

		function syncLinkedColorControls() {
			Object.keys(linkedColorControls).forEach(syncLinkedColorControl);
		}

		function linkedColorValue(name) {
			const config = linkedColorControls[name];
			return isLinkedColor(name)
				? colorValue(config.source, config.fallback)
				: colorValue(config.target, config.fallback);
		}

		function shadowUnitValue(inputId, fallback = '0') {
			const value = controls[inputId].value === '' ? fallback : controls[inputId].value;
			return `${value}${controls.linkShadowUnit.value}`;
		}

		function linkShadowValue() {
			if (!controls.linkShadowEnabled.checked) return 'none';

			const inset = controls.linkShadowInset.checked ? 'inset ' : '';
			return `${inset}${shadowUnitValue('linkShadowX')} ${shadowUnitValue('linkShadowY', '8')} ${shadowUnitValue('linkShadowBlur', '18')} ${shadowUnitValue('linkShadowSpread')} ${colorValue('linkShadowColor', '#e01d242e')}`;
		}

		function syncLinkShadowControls() {
			const disabled = !controls.linkShadowEnabled.checked;
			controls.linkShadowEnabled.closest('.shadow-control')?.classList.toggle('is-disabled', disabled);
			[
				'linkShadowUnit',
				'linkShadowX',
				'linkShadowY',
				'linkShadowBlur',
				'linkShadowSpread',
				'linkShadowColor',
				'linkShadowInset'
			].forEach(id => setControlDisabled(controls[id], disabled));
			[
				'linkShadowX',
				'linkShadowY',
				'linkShadowBlur',
				'linkShadowSpread'
			].forEach(id => {
				document.querySelectorAll(`.box-stepper-btn[data-box-stepper-target="${id}"]`).forEach(button => {
					button.disabled = disabled;
					button.setAttribute('aria-disabled', String(disabled));
				});
			});
		}

		function linkShadowJson() {
			return {
				enabled: controls.linkShadowEnabled.checked,
				x: shadowUnitValue('linkShadowX'),
				y: shadowUnitValue('linkShadowY', '8'),
				blur: shadowUnitValue('linkShadowBlur', '18'),
				spread: shadowUnitValue('linkShadowSpread'),
				color: colorValue('linkShadowColor', '#e01d242e'),
				inset: controls.linkShadowInset.checked,
				value: linkShadowValue()
			};
		}

		function scrollEffectsEnabled() {
			return state.headerBehavior !== 'stay' && controls.animateScroll.checked;
		}

		function escapeHtml(value) {
			return String(value || '')
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}

		function safePreviewUrl(value) {
			const url = String(value || '').trim();
			return /^(#|\/|https?:\/\/)/i.test(url) ? url : '#';
		}

		function safePreviewImageUrl(value) {
			const url = String(value || '').trim();
			return /^(\/|https?:\/\/)/i.test(url) ? url : '';
		}

		function safeIconClassFromHtml(value) {
			const match = String(value || '').match(/<i[^>]*class=(?:"|')([^"']+)(?:"|')[^>]*>/i);
			return match ? match[1].replace(/[^A-Za-z0-9 _-]/g, '').replace(/\s+/g, ' ').trim() : '';
		}

		function dropdownIndicatorMarkup(item) {
			if (!item.hasDropdown || !item.showArrow) return '';

			const iconUrl = item.toggleIconType === 'upload_file' ? safePreviewImageUrl(item.toggleIconUrl) : '';
			if (iconUrl) {
				return `<span class="menu-dropdown-indicator" aria-hidden="true"><img src="${escapeHtml(iconUrl)}" alt=""></span>`;
			}

			const iconClass = item.toggleIconType === 'custom_input' ? safeIconClassFromHtml(item.toggleIconHtml) : '';
			if (iconClass) {
				return `<span class="menu-dropdown-indicator" aria-hidden="true"><i class="${escapeHtml(iconClass)}"></i></span>`;
			}

			return '<span class="menu-dropdown-indicator" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>';
		}

		function setControlDisabled(control, disabled) {
			control.disabled = disabled;
			control.setAttribute('aria-disabled', String(disabled));

			const colorisControl = control.closest('.coloris-control');
			if (colorisControl) {
				colorisControl.classList.toggle('is-disabled', disabled);
				const trigger = colorisControl.querySelector('.clr-field button');
				if (trigger) trigger.disabled = disabled;
			}

			const switchControl = control.closest('.form-check');
			if (switchControl) {
				switchControl.classList.toggle('is-disabled', disabled);
			}
		}

		function syncScrollControls() {
			const isStay = state.headerBehavior === 'stay';
			const canUseScrolledState = scrollEffectsEnabled();

			setControlDisabled(controls.animateScroll, isStay);
			setControlDisabled(controls.transparentStart, !canUseScrolledState);
			setControlDisabled(controls.scrolledBg, !canUseScrolledState);
			controls.scrollSim.disabled = !canUseScrolledState;
			syncTransparentColorControls();

			if (!canUseScrolledState) {
				controls.scrollSim.value = 0;
				els.deviceStage.scrollTop = 0;
			}
		}

		function menuMarkup(items, extraClass = '') {
			return `<ul class="header-menu ${extraClass}">${items.map(item => (
				`<li><a href="${escapeHtml(safePreviewUrl(item.url))}" class="${item.active ? 'is-active' : ''}${item.hasDropdown ? ' has-dropdown' : ''}"><span class="menu-label">${escapeHtml(item.label)}</span>${dropdownIndicatorMarkup(item)}</a></li>`
			)).join('')}</ul>`;
		}

		function logoMarkup() {
			return `<a href="#" class="header-logo"><span class="logo-mark">PH</span><span>Phoenix CMS</span></a>`;
		}

		function actionsMarkup() {
			return `<div class="header-actions"><button class="btn-demo" type="button">Get started</button><button class="hamburger" type="button" aria-label="Open menu"><i class="fas fa-bars"></i></button></div>`;
		}

		function splitMenus() {
			const total = state.menus.length;
			const leftCount = Math.floor(total / 2);
			return {
				left: state.menus.slice(0, leftCount),
				right: state.menus.slice(leftCount)
			};
		}

		function renderHeader() {
			const logo = logoMarkup();
			const menu = menuMarkup(state.menus);
			const actions = actionsMarkup();
			let html = '';
			let layout = 'layout-left';

			if (state.logoPosition === 'center' && state.menuPosition === 'center') {
				const split = splitMenus();
				html = `${menuMarkup(split.left, 'menu-left')}${logo}${menuMarkup(split.right, 'menu-right')}`;
				layout = 'layout-center';
			} else if (state.logoPosition === 'left' && state.menuPosition === 'left') {
				html = `${logo}${menu}${actions}`;
				layout = 'layout-left';
			} else if (state.logoPosition === 'left' && state.menuPosition === 'center') {
				html = `${logo}${menu}${actions}`;
				layout = 'layout-menu-center';
			} else if (state.logoPosition === 'left' && state.menuPosition === 'right') {
				html = `${logo}${menu}${actions}`;
				layout = 'layout-right';
			} else if (state.logoPosition === 'center' && state.menuPosition === 'left') {
				html = `${menu}${logo}${actions}`;
				layout = 'layout-logo-center-menu-left';
			} else if (state.logoPosition === 'center' && state.menuPosition === 'right') {
				html = `${actions}${logo}${menu}`;
				layout = 'layout-logo-center-menu-right';
			} else if (state.logoPosition === 'right' && state.menuPosition === 'right') {
				html = `${actions}${menu}${logo}`;
				layout = 'layout-right';
			} else if (state.logoPosition === 'right' && state.menuPosition === 'center') {
				html = `${actions}${menu}${logo}`;
				layout = 'layout-menu-center';
			} else {
				html = `${menu}${actions}${logo}`;
				layout = 'layout-left';
			}

			els.headerInner.className = `header-inner ${layout}`;
			els.headerInner.innerHTML = html;
		}

		function updateHeaderClasses() {
			syncScrollControls();
			const canUseScrolledState = scrollEffectsEnabled();

			els.header.classList.toggle('is-transparent', canUseScrolledState && controls.transparentStart.checked);
			els.header.classList.toggle('inner-bg', controls.innerBg.checked);
			els.header.classList.toggle('full-bg', !controls.innerBg.checked);
			els.header.classList.toggle('is-scrolled-transparent', canUseScrolledState && isTransparentColor('scrolledBg'));
			els.header.classList.toggle('is-fixed', state.headerBehavior === 'fixed' || state.headerBehavior === 'sticky');
			els.header.classList.toggle('is-stay', state.headerBehavior === 'stay');
			els.headerContainer.classList.toggle('is-fluid', state.containerMode === 'fluid');
			els.deviceStage.classList.toggle('mobile-mode', controls.deviceMode.value === 'mobile');
			els.deviceStage.classList.toggle('tablet-mode', controls.deviceMode.value === 'tablet');
			if (!controls.animateScroll.checked) {
				els.header.style.transition = 'none';
				els.headerInner.style.transition = 'none';
			} else {
				els.header.style.transition = '';
				els.headerInner.style.transition = '';
			}
		}

		function applyColorsAndSizes() {
			syncLinkedColorControls();
			syncLinkShadowControls();
			setVariable('--header-bg', colorValue('headerBg', '#ffffff'));
			setVariable('--scrolled-bg', colorValue('scrolledBg', '#ffffff'));
			setVariable('--header-text', colorValue('headerText', '#101828'));
			setVariable('--link-color', colorValue('linkColor', '#273142'));
			setVariable('--link-hover', colorValue('linkHover', '#e01d24'));
			setVariable('--link-hover-text', readableTextColor(colorValue('linkHover', '#e01d24')));
			setVariable('--link-hover-border', linkedColorValue('hover'));
			setVariable('--link-focus', colorValue('linkFocus', '#c4121a'));
			setVariable('--link-focus-text', readableTextColor(colorValue('linkFocus', '#c4121a')));
			setVariable('--link-focus-border', linkedColorValue('focus'));
			setVariable('--link-active', colorValue('linkActive', '#e01d24'));
			setVariable('--link-active-text', readableTextColor(colorValue('linkActive', '#e01d24')));
			setVariable('--link-active-border', linkedColorValue('active'));
			setVariable('--link-active-shadow', linkShadowValue());
			applyTransparentColors();
			setVariable('--header-height', unitValue('headerHeight', '76'));
			setVariable('--header-radius', boxControlCss('headerRadius'));
			setVariable('--header-padding', boxControlCss('headerPadding'));
			setVariable('--nav-radius', boxControlCss('navRadius'));
			setBoxVariablePrefix('--header-container-margin', 'containerMargin');
		}

		function updateScrolledState() {
			const scrolled = scrollEffectsEnabled() && (Number(controls.scrollSim.value) > 8 || els.deviceStage.scrollTop > 32);
			els.header.classList.toggle('is-scrolled', scrolled);
		}

		function updateJson() {
			const split = state.logoPosition === 'center' && state.menuPosition === 'center'
				? { logo_between_menu: true, left_menu_count: Math.floor(state.menus.length / 2), right_menu_count: state.menus.length - Math.floor(state.menus.length / 2) }
				: { logo_between_menu: false };

			const config = {
				source: editorOptions.previewUrl,
				colors: {
					header_background: colorValue('headerBg', '#ffffff'),
					scrolled_background: colorValue('scrolledBg', '#ffffff'),
					header_text: colorValue('headerText', '#101828'),
					link: colorValue('linkColor', '#273142'),
					link_hover: colorValue('linkHover', '#e01d24'),
					link_hover_text: readableTextColor(colorValue('linkHover', '#e01d24')),
					link_hover_border: linkedColorValue('hover'),
					link_hover_border_linked: isLinkedColor('hover'),
					link_focus: colorValue('linkFocus', '#c4121a'),
					link_focus_text: readableTextColor(colorValue('linkFocus', '#c4121a')),
					link_focus_border: linkedColorValue('focus'),
					link_focus_border_linked: isLinkedColor('focus'),
					link_active: colorValue('linkActive', '#e01d24'),
					link_active_text: readableTextColor(colorValue('linkActive', '#e01d24')),
					link_active_border: linkedColorValue('active'),
					link_active_border_linked: isLinkedColor('active'),
					transparent: transparentColorConfig()
				},
				layout: {
					logo_position: state.logoPosition,
					menu_position: state.menuPosition,
					container: state.containerMode,
					background_follows_container: controls.innerBg.checked,
					...split
				},
				behavior: {
					position: state.headerBehavior,
					transparent_before_scroll: controls.transparentStart.checked,
					transparent_color_mode: state.transparentColorMode,
					animate_on_scroll: controls.animateScroll.checked,
					uses_scrolled_background: scrollEffectsEnabled()
				},
				effects: {
					link_shadow: linkShadowJson()
				},
				sizing: {
					height: unitValue('headerHeight', '76'),
					header_radius: boxControlJson('headerRadius'),
					header_padding: boxControlJson('headerPadding'),
					link_shape: state.linkShape,
					leaf_direction: state.linkShape === 'leaf' ? state.leafDirection : null,
					link_radius: boxControlJson('navRadius'),
					container_margin: boxControlJson('containerMargin')
				}
			};
			currentConfig = config;
			els.jsonOutput.textContent = JSON.stringify(config, null, 2);
		}

		function stepNumericInput(input, delta) {
			if (!input || input.disabled) return;

			const step = Number(input.step) || 1;
			const current = Number(input.value || 0);
			const min = input.min === '' ? -Infinity : Number(input.min);
			const max = input.max === '' ? Infinity : Number(input.max);
			const decimals = (String(step).split('.')[1] || '').length;
			const next = Math.min(max, Math.max(min, current + (step * delta)));

			input.value = decimals ? next.toFixed(decimals) : String(Math.round(next));
			input.dispatchEvent(new Event('input', { bubbles: true }));
			input.focus();
		}

		function initNumberSteppers() {
			document.querySelectorAll('.stepper-btn[data-stepper-target]').forEach(button => {
				button.addEventListener('click', () => {
					stepNumericInput(controls[button.dataset.stepperTarget], Number(button.dataset.stepperDelta) || 0);
				});
			});
		}

		function initBoxSteppers() {
			document.querySelectorAll('.box-stepper-btn[data-box-stepper-target]').forEach(button => {
				button.addEventListener('click', () => {
					stepNumericInput(controls[button.dataset.boxStepperTarget], Number(button.dataset.boxStepperDelta) || 0);
				});
			});
		}

		function initBoxControls() {
			Object.entries(boxControls).forEach(([name, config]) => {
				config.sides.forEach(id => {
					const input = controls[id];
					input.addEventListener('input', () => {
						syncLinkedBoxValues(name, id);
						saveActiveBoxProfile(name);
						if (name === 'navRadius') syncCurrentLinkRadiusProfile();
						updateAll();
					});
					input.addEventListener('change', () => {
						syncLinkedBoxValues(name, id);
						saveActiveBoxProfile(name);
						if (name === 'navRadius') syncCurrentLinkRadiusProfile();
						updateAll();
					});
				});

				controls[config.unit].addEventListener('change', () => {
					saveActiveBoxProfile(name);
					if (name === 'navRadius') syncCurrentLinkRadiusProfile();
					updateAll();
				});
				controls[config.linked].addEventListener('click', () => {
					const button = controls[config.linked];
					const active = !button.classList.contains('is-active');
					setBoxLinked(name, active);
					if (active) syncLinkedBoxValues(name, config.sides[0]);
					saveActiveBoxProfile(name);
					if (name === 'navRadius') syncCurrentLinkRadiusProfile();
					updateAll();
				});
			});
		}

		function initResponsiveBoxModeControls() {
			document.querySelectorAll('.responsive-mode-control[data-responsive-control]').forEach(group => {
				group.addEventListener('click', event => {
					const trigger = event.target.closest('[data-responsive-trigger]');
					if (trigger) {
						event.stopPropagation();
						toggleResponsiveBoxMenu(group.dataset.responsiveControl);
						return;
					}

					const button = event.target.closest('button[data-responsive-mode]');
					if (!button) return;
					event.stopPropagation();
					const name = group.dataset.responsiveControl;
					switchResponsiveBoxMode(name, button.dataset.responsiveMode);
					updateAll();
				});
			});

			document.addEventListener('click', event => {
				if (event.target.closest('.responsive-mode-control')) return;
				closeResponsiveBoxMenu();
			});

			document.addEventListener('keydown', event => {
				if (event.key === 'Escape') closeResponsiveBoxMenu();
			});
		}

		function initLinkedColorControls() {
			Object.entries(linkedColorControls).forEach(([name, config]) => {
				controls[config.linked].addEventListener('click', () => {
					const active = !controls[config.linked].classList.contains('is-active');
					controls[config.linked].classList.toggle('is-active', active);
					if (active) {
						controls[config.target].value = controls[config.source].value;
						updateColorisThumbnail(controls[config.target]);
					}
					updateAll();
				});
			});
		}

		function initColorisPicker() {
			if (!window.Coloris) return;

			Coloris({
				el: '.coloris-field',
				theme: 'pill',
				themeMode: 'dark',
				format: 'hex',
				formatToggle: true,
				alpha: true,
				clearButton: true,
				closeButton: true,
				swatches: [
					'#ffffff',
					'#101828',
					'#273142',
					'#e01d24',
					'#e01d242e',
					'#c4121a',
					'#667085',
					'#f8fafc',
					'#111827'
				],
				onChange: () => updateAll()
			});
		}


		function dimensionParts(value, fallbackUnit = 'px') {
			const match = String(value || '').trim().match(/^(-?\d+(?:\.\d+)?)(px|%|em|rem|pt)$/i);
			return match
				? { value: match[1], unit: match[2].toLowerCase() }
				: { value: '0', unit: fallbackUnit };
		}

		function storedProfileToBox(name, profile) {
			if (!profile || typeof profile !== 'object') return null;
			const unit = ['px', '%', 'em', 'rem', 'pt'].includes(profile.unit) ? profile.unit : 'px';
			const values = ['top', 'right', 'bottom', 'left'].map((side, index) => {
				const parsed = dimensionParts(profile[side] || `${boxControls[name].defaults[index]}${unit}`, unit);
				return parsed.value;
			});
			return createBoxProfile(name, values, unit, Boolean(profile.linked));
		}

		function applyStoredBox(name, storedBox) {
			if (!storedBox || typeof storedBox !== 'object') return;
			const responsive = storedBox.responsive || {};
			const all = storedProfileToBox(name, responsive.all || storedBox)
				|| createBoxProfile(name, boxControls[name].defaults, 'px', isBoxLinked(name));

			responsiveBoxProfiles[name] = {
				mode: normalizeResponsiveMode(storedBox.mode || storedBox.preview_device || 'desktop'),
				all,
				desktop: storedProfileToBox(name, responsive.desktop),
				tablet: storedProfileToBox(name, responsive.tablet),
				mobile: storedProfileToBox(name, responsive.mobile)
			};
		}

		function setSegmentedValue(controlName, value) {
			editor.querySelectorAll(`.segmented[data-control="${controlName}"] button[data-value]`).forEach(button => {
				button.classList.toggle('is-active', button.dataset.value === value);
			});
		}

		function setLinkedColorState(name, linked) {
			const config = linkedColorControls[name];
			const button = controls[config.linked];
			button.classList.toggle('is-active', Boolean(linked));
			button.setAttribute('aria-pressed', String(Boolean(linked)));
		}

		function applyStoredConfig(config) {
			if (!config || typeof config !== 'object') return;

			const colors = config.colors || {};
			const transparent = colors.transparent || {};
			const layout = config.layout || {};
			const behavior = config.behavior || {};
			const shadow = config.effects?.link_shadow || {};
			const sizing = config.sizing || {};

			controls.headerBg.value = colors.header_background || '#ffffff';
			controls.scrolledBg.value = colors.scrolled_background || '#ffffff';
			controls.headerText.value = colors.header_text || '#101828';
			controls.linkColor.value = colors.link || '#273142';
			controls.linkHover.value = colors.link_hover || '#e01d24';
			controls.linkHoverBorder.value = colors.link_hover_border || controls.linkHover.value;
			controls.linkFocus.value = colors.link_focus || '#c4121a';
			controls.linkFocusBorder.value = colors.link_focus_border || controls.linkFocus.value;
			controls.linkActive.value = colors.link_active || '#e01d24';
			controls.linkActiveBorder.value = colors.link_active_border || controls.linkActive.value;
			controls.transparentText.value = transparent.header_text || '#ffffff';
			controls.transparentHover.value = transparent.hover || controls.linkHover.value;
			controls.transparentFocus.value = transparent.focus || controls.linkFocus.value;
			controls.transparentActive.value = transparent.active || controls.linkActive.value;

			setLinkedColorState('hover', colors.link_hover_border_linked !== false);
			setLinkedColorState('focus', colors.link_focus_border_linked !== false);
			setLinkedColorState('active', colors.link_active_border_linked !== false);

			state.logoPosition = layout.logo_position || 'left';
			state.menuPosition = layout.menu_position || 'left';
			state.containerMode = layout.container || 'container';
			state.headerBehavior = behavior.position || 'stay';
			state.transparentColorMode = behavior.transparent_color_mode || transparent.mode || 'auto';
			state.linkShape = sizing.link_shape || 'default';
			state.leafDirection = sizing.leaf_direction || 'forward';

			controls.innerBg.checked = Boolean(layout.background_follows_container);
			controls.transparentStart.checked = Boolean(behavior.transparent_before_scroll);
			controls.animateScroll.checked = Boolean(behavior.animate_on_scroll);

			setSegmentedValue('logoPosition', state.logoPosition);
			setSegmentedValue('menuPosition', state.menuPosition);
			setSegmentedValue('containerMode', state.containerMode);
			setSegmentedValue('headerBehavior', state.headerBehavior);
			setSegmentedValue('transparentColorMode', state.transparentColorMode);
			setSegmentedValue('linkShape', state.linkShape);
			setSegmentedValue('leafDirection', state.leafDirection);

			const height = dimensionParts(sizing.height || '76px');
			controls.headerHeight.value = height.value;
			controls.headerHeightUnit.value = height.unit;

			const shadowX = dimensionParts(shadow.x || '0px');
			controls.linkShadowEnabled.checked = Boolean(shadow.enabled);
			controls.linkShadowUnit.value = shadowX.unit;
			controls.linkShadowX.value = shadowX.value;
			controls.linkShadowY.value = dimensionParts(shadow.y || '8px', shadowX.unit).value;
			controls.linkShadowBlur.value = dimensionParts(shadow.blur || '18px', shadowX.unit).value;
			controls.linkShadowSpread.value = dimensionParts(shadow.spread || '0px', shadowX.unit).value;
			controls.linkShadowColor.value = shadow.color || '#e01d242e';
			controls.linkShadowInset.checked = Boolean(shadow.inset);

			applyStoredBox('headerRadius', sizing.header_radius);
			applyStoredBox('headerPadding', sizing.header_padding);
			applyStoredBox('navRadius', sizing.link_radius);
			applyStoredBox('containerMargin', sizing.container_margin);

			globalResponsiveMode = normalizeResponsiveMode(sizing.header_radius?.mode || sizing.header_padding?.mode || 'desktop');
			controls.deviceMode.value = globalResponsiveMode;

			Object.keys(boxControls).forEach(name => {
				const store = responsiveBoxProfiles[name];
				if (!store) return;
				store.mode = globalResponsiveMode;
				writeBoxProfileToControls(name, profileForMode(name, globalResponsiveMode));
			});

			linkRadiusProfiles[currentLinkRadiusProfileKey()] = cloneResponsiveBoxState(responsiveBoxProfiles.navRadius);
			syncAllResponsiveBoxControls();
			editor.querySelectorAll('[data-coloris]').forEach(updateColorisThumbnail);
		}

		function setSaveStatus(status, message) {
			const element = document.getElementById('headerNavigationSaveStatus');
			if (!element) return;
			element.classList.toggle('is-success', status === 'success');
			element.classList.toggle('is-failed', status === 'failed');
			const icon = status === 'success' ? 'fa-check-circle' : status === 'failed' ? 'fa-exclamation-circle' : 'fa-database';
			element.innerHTML = `<i class="fas ${icon}"></i> `;
			element.append(document.createTextNode(String(message || '')));
		}

		async function saveHeaderNavigation() {
			const button = document.getElementById('saveHeaderNavigation');
			const active = document.getElementById('headerNavigationActive');
			if (!button || !currentConfig || !editorOptions.updateUrl) return;

			button.disabled = true;
			button.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i> Saving';
			setSaveStatus('saving', 'Saving changes');

			try {
				const response = await fetch(editorOptions.updateUrl, {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': editorOptions.csrfToken
					},
					body: JSON.stringify({
						is_active: active.checked,
						config_json: currentConfig
					})
				});
				const json = await response.json();
				if (!response.ok || !json.success) {
					const message = typeof json.message === 'string' ? json.message : JSON.stringify(json.message);
					throw new Error(message || 'Unable to save header navigation settings');
				}
				setSaveStatus('success', 'Saved to database');
			} catch (error) {
				setSaveStatus('failed', error.message || 'Save failed');
			} finally {
				button.disabled = false;
				button.innerHTML = '<i class="fas fa-save me-1"></i> Save Settings';
			}
		}

		function updatePreviewScale() {
			const viewport = previewViewports[controls.deviceMode.value] || previewViewports.desktop;
			const frameWidth = els.previewFrame.clientWidth;
			const frameHeight = els.previewFrame.clientHeight;
			if (!frameWidth || !frameHeight) return;

			const scale = Math.max(0.5, Math.min(1, frameWidth / viewport.width, frameHeight / viewport.height));
			const scaledWidth = viewport.width * scale;
			const scaledHeight = viewport.height * scale;

			els.previewViewport.style.width = `${viewport.width}px`;
			els.previewViewport.style.height = `${viewport.height}px`;
			els.previewViewport.style.left = `${Math.max(0, (frameWidth - scaledWidth) / 2)}px`;
			els.previewViewport.style.top = `${Math.max(0, (frameHeight - scaledHeight) / 2)}px`;
			els.previewViewport.style.setProperty('--preview-scale', scale.toFixed(4));
			els.previewScaleStatus.textContent = `${viewport.label} ${viewport.width}×${viewport.height}px · Fit ${Math.round(scale * 100)}%`;
		}

		function updateAll() {
			syncLinkShapeControls();
			applyColorsAndSizes();
			updateHeaderClasses();
			renderHeader();
			updatePreviewScale();
			updateScrolledState();
			updateJson();
		}

		document.querySelectorAll('.segmented').forEach(group => {
			group.addEventListener('click', event => {
				const button = event.target.closest('button[data-value]');
				if (!button) return;
				group.querySelectorAll('button').forEach(item => item.classList.remove('is-active'));
				button.classList.add('is-active');
				if (group.dataset.control === 'linkShape') {
					switchLinkRadiusProfile(button.dataset.value, state.leafDirection);
				} else if (group.dataset.control === 'leafDirection') {
					switchLinkRadiusProfile(state.linkShape, button.dataset.value);
				} else {
					state[group.dataset.control] = button.dataset.value;
				}
				updateAll();
			});
		});

		[
			'headerBg', 'scrolledBg', 'headerText', 'linkColor', 'linkHover', 'linkHoverBorder',
			'linkFocus', 'linkFocusBorder', 'linkActive', 'linkActiveBorder', 'linkShadowEnabled',
			'linkShadowUnit', 'linkShadowX', 'linkShadowY', 'linkShadowBlur', 'linkShadowSpread',
			'linkShadowColor', 'linkShadowInset', 'innerBg', 'transparentStart', 'transparentText',
			'transparentHover', 'transparentFocus', 'transparentActive', 'animateScroll', 'scrollSim',
			'headerHeight', 'headerHeightUnit'
		].forEach(id => {
			controls[id].addEventListener('input', updateAll);
			controls[id].addEventListener('change', updateAll);
		});

		controls.deviceMode.addEventListener('change', () => {
			switchResponsiveBoxMode(null, controls.deviceMode.value);
			updateAll();
		});

		initResponsiveBoxProfiles();
		initNumberSteppers();
		initBoxSteppers();
		initBoxControls();
		initResponsiveBoxModeControls();
		initLinkedColorControls();
		initColorisPicker();
		applyStoredConfig(editorOptions.config || {});
		document.getElementById('saveHeaderNavigation')?.addEventListener('click', saveHeaderNavigation);

		const previewResizeObserver = typeof ResizeObserver === 'function'
			? new ResizeObserver(updatePreviewScale)
			: null;
		previewResizeObserver?.observe(els.previewFrame);
		window.addEventListener('resize', updatePreviewScale);

		els.deviceStage.addEventListener('scroll', () => {
			controls.scrollSim.value = scrollEffectsEnabled()
				? Math.min(100, Math.round((els.deviceStage.scrollTop / 320) * 100))
				: 0;
			updateScrolledState();
			updateJson();
		});

		document.getElementById('resetPreview').addEventListener('click', () => {
			els.deviceStage.scrollTo({ top: 0, behavior: 'smooth' });
			controls.scrollSim.value = 0;
			updateAll();
		});

		async function loadPreviewMenus() {
			try {
				const response = await fetch(editorOptions.previewUrl, { headers: { 'Accept': 'application/json' } });
				if (!response.ok) throw new Error(`Menu endpoint returned ${response.status}`);
				const json = await response.json();
				const data = Array.isArray(json) ? json : Array.isArray(json.data) ? json.data : [];
				const mapped = data.map((item, index) => ({
					label: item.parent_name || item.menu_name || item.name || `Menu ${index + 1}`,
					url: item.parent_url || item.parent_link || item.menu_link || '#',
					active: index === 0,
					hasDropdown: item.has_dropdown === true,
					dropdownType: ['bootstrap', 'mega'].includes(item.dropdown_type) ? item.dropdown_type : 'none',
					submenuCount: Math.max(0, Number(item.submenu_count || 0)),
					showArrow: item.show_arrow !== false,
					toggleIconType: item.toggle_icon_type || '',
					toggleIconUrl: item.toggle_icon_url || '',
					toggleIconHtml: item.toggle_icon_html || ''
				})).filter(item => item.label);
				sampleMenus.splice(0, sampleMenus.length, ...mapped);
				state.menus = sampleMenus.map((item, index) => ({ ...item, active: index === 0 }));
			} catch (error) {
				setSaveStatus('failed', 'Menu preview unavailable');
			} finally {
				updateAll();
			}
		}

		updateAll();
		loadPreviewMenus();
