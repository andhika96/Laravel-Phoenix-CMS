(function (registry) {
	'use strict';

	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');

	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);
	const clone = (value) => value && typeof value === 'object' ? JSON.parse(JSON.stringify(value)) : value;

	const DIRECTIONS = ['row', 'column'];
	const TEXT_ORDERS = ['heading-first', 'subheading-first'];
	const VERTICAL_POSITIONS = ['top', 'center', 'bottom'];
	const HORIZONTAL_ALIGNMENTS = ['left', 'center', 'right'];
	const BORDER_MODES = ['none', 'box', 'underline'];
	const BORDER_WIDTH_MODES = ['content', 'full'];
	const BORDER_TYPES = ['solid', 'dashed', 'dotted', 'double', 'groove'];

	function normalizeDimension(value, fallback) {
		const raw = String(value == null ? '' : value).trim();
		return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback;
	}

	function normalizeColor(value, fallback) {
		const raw = String(value == null ? '' : value).trim();
		return raw && !/[;{}<>]/.test(raw) && /^[#a-z0-9(),.%\s/-]+$/i.test(raw) ? raw : fallback;
	}

	function normalizeFontFamily(value, fallback) {
		const raw = String(value == null ? '' : value).trim();
		return raw && !/[;{}<>"']/g.test(raw) ? raw.slice(0, 160) : fallback;
	}

	function normalizeWeight(value, fallback) {
		const raw = String(value == null ? '' : value).trim().toLowerCase();
		return /^(?:inherit|normal|bold|[1-9]00)$/.test(raw) ? raw : fallback;
	}

	function normalizeAttributes(value) {
		if (!Array.isArray(value)) return [];
		return value.map((entry) => ({
			key: String(entry?.key || entry?.name || '').trim(),
			value: String(entry?.value == null ? '' : entry.value),
		})).filter((entry) => entry.key);
	}

	function normalizeLinkUrl(value) {
		const raw = String(value == null ? '' : value).trim();
		if (!raw || raw.startsWith('//') || /[\u0000-\u001f\u007f]/.test(raw)) return '';
		if (/^(?:javascript|vbscript|data):/i.test(raw)) return '';
		return /^(?:https?:\/\/|mailto:|tel:|\/|#)/i.test(raw) ? raw : '';
	}

	function normalizeResponsiveEnum(settings, base, allowed, fallback) {
		settings[base] = allowed.includes(String(settings[base] || '').trim()) ? String(settings[base]).trim() : fallback;
		for (const suffix of ['Tablet', 'Mobile']) {
			const key = base + suffix;
			const value = String(settings[key] == null ? '' : settings[key]).trim();
			settings[key] = value === '' || allowed.includes(value) ? value : '';
		}
	}

	function normalizeTypography(settings, prefix, fallback) {
		settings[prefix + 'FontFamily'] = normalizeFontFamily(settings[prefix + 'FontFamily'], fallback.fontFamily);
		settings[prefix + 'FontWeight'] = normalizeWeight(settings[prefix + 'FontWeight'], fallback.fontWeight);
		settings[prefix + 'Color'] = normalizeColor(settings[prefix + 'Color'], fallback.color);
		for (const key of ['FontSize', 'LineHeight', 'LetterSpacing', 'WordSpacing']) {
			settings[prefix + key] = normalizeDimension(settings[prefix + key], fallback[key.toLowerCase()] || '0px');
			for (const suffix of ['Tablet', 'Mobile']) {
				const responsiveKey = prefix + key + suffix;
				const value = String(settings[responsiveKey] == null ? '' : settings[responsiveKey]).trim();
				settings[responsiveKey] = value === '' ? '' : normalizeDimension(value, '');
			}
		}
		for (const key of ['TextTransform', 'FontStyle', 'TextDecoration']) {
			const allowed = key === 'TextTransform'
				? ['none', 'uppercase', 'lowercase', 'capitalize']
				: (key === 'FontStyle' ? ['normal', 'italic', 'oblique'] : ['none', 'underline', 'overline', 'line-through']);
			settings[prefix + key] = allowed.includes(settings[prefix + key]) ? settings[prefix + key] : 'none';
		}
	}

	function normalizeTextBox(settings, prefix) {
		const modeKey = prefix + 'BorderMode';
		const widthModeKey = prefix + 'BorderWidthMode';
		const typeKey = prefix + 'BorderType';
		settings[modeKey] = BORDER_MODES.includes(settings[modeKey]) ? settings[modeKey] : 'none';
		settings[widthModeKey] = BORDER_WIDTH_MODES.includes(settings[widthModeKey]) ? settings[widthModeKey] : 'content';
		settings[typeKey] = BORDER_TYPES.includes(settings[typeKey]) ? settings[typeKey] : 'solid';
		settings[prefix + 'BorderThickness'] = normalizeDimension(settings[prefix + 'BorderThickness'], '1px');
		settings[prefix + 'BorderColor'] = normalizeColor(settings[prefix + 'BorderColor'], '#d8ad5e');
		settings[prefix + 'BorderRadius'] = normalizeDimension(settings[prefix + 'BorderRadius'], '0px');
		for (const suffix of ['Tablet', 'Mobile']) {
			for (const key of ['BorderMode', 'BorderWidthMode', 'BorderType']) {
				const responsiveKey = prefix + key + suffix;
				const allowed = key === 'BorderMode' ? BORDER_MODES : (key === 'BorderWidthMode' ? BORDER_WIDTH_MODES : BORDER_TYPES);
				const value = String(settings[responsiveKey] == null ? '' : settings[responsiveKey]).trim();
				settings[responsiveKey] = value === '' || allowed.includes(value) ? value : '';
			}
			for (const key of ['BorderThickness', 'BorderColor', 'BorderRadius']) {
				const responsiveKey = prefix + key + suffix;
				const value = String(settings[responsiveKey] == null ? '' : settings[responsiveKey]).trim();
				settings[responsiveKey] = value === '' ? '' : (key === 'BorderColor' ? normalizeColor(value, '') : normalizeDimension(value, ''));
			}
		}
	}

	function normalizeBoxSpacing(settings, prefix) {
		for (const kind of ['Padding', 'Margin']) {
			for (const side of ['Top', 'Right', 'Bottom', 'Left']) {
				const base = prefix + kind + side;
				settings[base] = normalizeDimension(settings[base], '0px');
				for (const suffix of ['Tablet', 'Mobile']) {
					const key = base + suffix;
					const value = String(settings[key] == null ? '' : settings[key]).trim();
					settings[key] = value === '' ? '' : normalizeDimension(value, '');
				}
			}
		}
	}

	function eventHighlightWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			heading: 'Five reasons to be on the first tee.',
			subheading: 'EVENT HIGHLIGHTS',
			textOrder: 'subheading-first',
			linkText: 'REGISTER INTEREST',
			linkUrl: '',
			linkTarget: '',
			linkNofollow: false,
			linkCustomAttributes: [],
			showArrow: true,
			layoutDirection: 'row',
			layoutDirectionTablet: '',
			layoutDirectionMobile: 'column',
			linkVerticalPosition: 'center',
			linkVerticalPositionTablet: '',
			linkVerticalPositionMobile: '',
			linkHorizontalAlign: 'right',
			linkHorizontalAlignTablet: '',
			linkHorizontalAlignMobile: '',
			textGap: '12px',
			textGapTablet: '',
			textGapMobile: '',

			headingFontFamily: 'Georgia, serif',
			headingFontSize: '56px', headingFontSizeTablet: '42px', headingFontSizeMobile: '32px',
			headingFontWeight: '400', headingColor: '#f4efe4',
			headingLineHeight: '1.05em', headingLineHeightTablet: '', headingLineHeightMobile: '',
			headingLetterSpacing: '0px', headingLetterSpacingTablet: '', headingLetterSpacingMobile: '',
			headingWordSpacing: '0px', headingWordSpacingTablet: '', headingWordSpacingMobile: '',
			headingTextTransform: 'none', headingFontStyle: 'normal', headingTextDecoration: 'none', headingTextShadow: 'none',

			subheadingFontFamily: 'inherit',
			subheadingFontSize: '14px', subheadingFontSizeTablet: '', subheadingFontSizeMobile: '',
			subheadingFontWeight: '700', subheadingColor: '#d8ad5e',
			subheadingLineHeight: '1.2em', subheadingLineHeightTablet: '', subheadingLineHeightMobile: '',
			subheadingLetterSpacing: '3px', subheadingLetterSpacingTablet: '', subheadingLetterSpacingMobile: '',
			subheadingWordSpacing: '0px', subheadingWordSpacingTablet: '', subheadingWordSpacingMobile: '',
			subheadingTextTransform: 'uppercase', subheadingFontStyle: 'normal', subheadingTextDecoration: 'none', subheadingTextShadow: 'none',

			linkFontFamily: 'inherit',
			linkFontSize: '14px', linkFontSizeTablet: '', linkFontSizeMobile: '',
			linkFontWeight: '700', linkColor: '#d8ad5e',
			linkLineHeight: '1.2em', linkLineHeightTablet: '', linkLineHeightMobile: '',
			linkLetterSpacing: '2px', linkLetterSpacingTablet: '', linkLetterSpacingMobile: '',
			linkWordSpacing: '0px', linkWordSpacingTablet: '', linkWordSpacingMobile: '',
			linkTextTransform: 'uppercase', linkFontStyle: 'normal', linkTextDecoration: 'none', linkTextShadow: 'none',

			headingBorderMode: 'none', headingBorderModeTablet: '', headingBorderModeMobile: '',
			headingBorderWidthMode: 'content', headingBorderWidthModeTablet: '', headingBorderWidthModeMobile: '',
			headingBorderType: 'solid', headingBorderTypeTablet: '', headingBorderTypeMobile: '',
			headingBorderThickness: '1px', headingBorderThicknessTablet: '', headingBorderThicknessMobile: '',
			headingBorderColor: '#d8ad5e', headingBorderColorTablet: '', headingBorderColorMobile: '',
			headingBorderRadius: '0px', headingBorderRadiusTablet: '', headingBorderRadiusMobile: '',
			subheadingBorderMode: 'none', subheadingBorderModeTablet: '', subheadingBorderModeMobile: '',
			subheadingBorderWidthMode: 'content', subheadingBorderWidthModeTablet: '', subheadingBorderWidthModeMobile: '',
			subheadingBorderType: 'solid', subheadingBorderTypeTablet: '', subheadingBorderTypeMobile: '',
			subheadingBorderThickness: '1px', subheadingBorderThicknessTablet: '', subheadingBorderThicknessMobile: '',
			subheadingBorderColor: '#d8ad5e', subheadingBorderColorTablet: '', subheadingBorderColorMobile: '',
			subheadingBorderRadius: '0px', subheadingBorderRadiusTablet: '', subheadingBorderRadiusMobile: '',

			headingPaddingTop: '0px', headingPaddingRight: '0px', headingPaddingBottom: '0px', headingPaddingLeft: '0px',
			headingPaddingTopTablet: '', headingPaddingRightTablet: '', headingPaddingBottomTablet: '', headingPaddingLeftTablet: '',
			headingPaddingTopMobile: '', headingPaddingRightMobile: '', headingPaddingBottomMobile: '', headingPaddingLeftMobile: '',
			headingMarginTop: '0px', headingMarginRight: '0px', headingMarginBottom: '0px', headingMarginLeft: '0px',
			headingMarginTopTablet: '', headingMarginRightTablet: '', headingMarginBottomTablet: '', headingMarginLeftTablet: '',
			headingMarginTopMobile: '', headingMarginRightMobile: '', headingMarginBottomMobile: '', headingMarginLeftMobile: '',
			subheadingPaddingTop: '0px', subheadingPaddingRight: '0px', subheadingPaddingBottom: '0px', subheadingPaddingLeft: '0px',
			subheadingPaddingTopTablet: '', subheadingPaddingRightTablet: '', subheadingPaddingBottomTablet: '', subheadingPaddingLeftTablet: '',
			subheadingPaddingTopMobile: '', subheadingPaddingRightMobile: '', subheadingPaddingBottomMobile: '', subheadingPaddingLeftMobile: '',
			subheadingMarginTop: '0px', subheadingMarginRight: '0px', subheadingMarginBottom: '0px', subheadingMarginLeft: '0px',
			subheadingMarginTopTablet: '', subheadingMarginRightTablet: '', subheadingMarginBottomTablet: '', subheadingMarginLeftTablet: '',
			subheadingMarginTopMobile: '', subheadingMarginRightMobile: '', subheadingMarginBottomMobile: '', subheadingMarginLeftMobile: '',

			advancedBackgroundType: 'classic',
			advancedBackgroundColor: '#081d30',
			advancedBorderType: 'none',
			advancedBorderWidth: '0px',
			advancedBorderColor: '#081d30',
			paddingTop: '28px', paddingRight: '32px', paddingBottom: '28px', paddingLeft: '32px',
		};
	}

	function normalizeEventHighlightSettings(settings) {
		const defaults = eventHighlightWidgetDefaults();
		Object.keys(defaults).forEach((key) => {
			if (settings[key] === undefined) settings[key] = clone(defaults[key]);
		});
		settings.heading = String(settings.heading == null ? '' : settings.heading);
		settings.subheading = String(settings.subheading == null ? '' : settings.subheading);
		settings.linkText = String(settings.linkText == null ? '' : settings.linkText);
		settings.linkUrl = normalizeLinkUrl(settings.linkUrl);
		settings.linkTarget = settings.linkTarget === '_blank' ? '_blank' : '';
		settings.linkNofollow = !!settings.linkNofollow;
		settings.linkCustomAttributes = normalizeAttributes(settings.linkCustomAttributes);
		settings.showArrow = settings.showArrow !== false && settings.showArrow !== 'false' && settings.showArrow !== 0 && settings.showArrow !== '0';

		normalizeResponsiveEnum(settings, 'textOrder', TEXT_ORDERS, 'subheading-first');
		normalizeResponsiveEnum(settings, 'layoutDirection', DIRECTIONS, 'row');
		normalizeResponsiveEnum(settings, 'linkVerticalPosition', VERTICAL_POSITIONS, 'center');
		normalizeResponsiveEnum(settings, 'linkHorizontalAlign', HORIZONTAL_ALIGNMENTS, 'right');
		settings.textGap = normalizeDimension(settings.textGap, '12px');
		for (const suffix of ['Tablet', 'Mobile']) {
			const key = 'textGap' + suffix;
			settings[key] = String(settings[key] == null ? '' : settings[key]).trim() === '' ? '' : normalizeDimension(settings[key], '');
		}

		normalizeTypography(settings, 'heading', { fontFamily: 'Georgia, serif', fontsize: '56px', lineheight: '1.05em', letterspacing: '0px', wordspacing: '0px', fontWeight: '400', color: '#f4efe4' });
		normalizeTypography(settings, 'subheading', { fontFamily: 'inherit', fontsize: '14px', lineheight: '1.2em', letterspacing: '3px', wordspacing: '0px', fontWeight: '700', color: '#d8ad5e' });
		normalizeTypography(settings, 'link', { fontFamily: 'inherit', fontsize: '14px', lineheight: '1.2em', letterspacing: '2px', wordspacing: '0px', fontWeight: '700', color: '#d8ad5e' });
		normalizeTextBox(settings, 'heading');
		normalizeTextBox(settings, 'subheading');
		normalizeBoxSpacing(settings, 'heading');
		normalizeBoxSpacing(settings, 'subheading');
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

	registry.register({
		type: 'event_highlight',
		defaults: eventHighlightWidgetDefaults,
		normalize(node) {
			const normalized = node && typeof node === 'object' ? node : {};
			normalized.settings = { ...eventHighlightWidgetDefaults(), ...(normalized.settings || {}) };
			normalizeEventHighlightSettings(normalized.settings);
			return normalized;
		},
	});
})(window.PageBuilderElementorV24Widgets);
