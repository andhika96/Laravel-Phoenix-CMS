(function (registry) {
	'use strict';

	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');

	const clone = (value) => value && typeof value === 'object' ? JSON.parse(JSON.stringify(value)) : value;
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);
	const GRID_COLUMNS = Array.from({ length: 12 }, (_, index) => String(index + 1));
	const CONTENT_WIDTH_MODES = ['max', 'full'];
	const FOOTER_POSITIONS = ['top', 'bottom'];
	const ALIGNMENTS = ['left', 'center', 'right'];
	const HEIGHT_MODES = ['auto', 'custom'];
	const IMAGE_SOURCES = ['ckfinder', 'url'];
	const IMAGE_PRESENTATIONS = ['element', 'background'];
	const ICON_STYLES = ['solid', 'regular', 'brands', 'light', 'duotone'];
	const SURFACES = ['inherit', 'light', 'dark', 'accent', 'custom'];
	const BORDER_TYPES = ['none', 'solid', 'dashed', 'dotted', 'double', 'groove'];
	const OBJECT_FITS = ['contain', 'cover', 'fill', 'none'];

	function normalizeDimension(value, fallback) {
		const raw = String(value == null ? '' : value).trim();
		return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh|deg)?$/i.test(raw) ? raw : fallback;
	}

	function normalizeColor(value, fallback = '') {
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

	function normalizeBoolean(value, fallback = false) {
		if (value === undefined || value === null || value === '') return fallback;
		return value === true || value === 1 || ['true', '1', 'yes'].includes(String(value).trim().toLowerCase());
	}

	function normalizeUrl(value) {
		const raw = String(value == null ? '' : value).trim();
		if (!raw || raw.startsWith('//') || /[\u0000-\u001f\u007f]/.test(raw)) return '';
		if (/^(?:javascript|vbscript|data):/i.test(raw)) return '';
		return /^(?:https?:\/\/|\/)/i.test(raw) ? raw : '';
	}

	function normalizeSvg(value) {
		const raw = String(value == null ? '' : value).trim();
		if (!raw || raw.length > 20000 || !/^<svg\b/i.test(raw)) return '';
		if (/<script\b|<foreignObject\b|<iframe\b|<object\b|<embed\b|<a\b|\bon[a-z]+\s*=|javascript:/i.test(raw)) return '';
		return raw;
	}

	function normalizeAttributes(value) {
		if (!Array.isArray(value)) return [];
		return value.map((entry) => ({
			key: String(entry?.key || entry?.name || '').trim(),
			value: String(entry?.value == null ? '' : entry.value),
		})).filter((entry) => entry.key);
	}

	function normalizeResponsiveEnum(settings, base, allowed, fallback) {
		settings[base] = allowed.includes(String(settings[base] || '').trim()) ? String(settings[base]).trim() : fallback;
		for (const suffix of ['Tablet', 'Mobile']) {
			const key = base + suffix;
			const value = String(settings[key] == null ? '' : settings[key]).trim();
			settings[key] = value === '' || allowed.includes(value) ? value : '';
		}
	}

	function normalizeResponsiveDimension(settings, base, fallback) {
		settings[base] = normalizeDimension(settings[base], fallback);
		for (const suffix of ['Tablet', 'Mobile']) {
			const key = base + suffix;
			const value = String(settings[key] == null ? '' : settings[key]).trim();
			settings[key] = value === '' ? '' : normalizeDimension(value, '');
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
			const fallback = key === 'FontStyle' ? 'normal' : 'none';
			settings[prefix + key] = allowed.includes(settings[prefix + key]) ? settings[prefix + key] : fallback;
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

	function cardDefaults(index) {
		const presets = [
			['users', 'fal fa-users', 'Pairs Scramble', '60% lower handicap\n40% higher handicap'],
			['award', 'fal fa-award', 'Qualification', 'Winners and runners-up qualify for the Dubai Grand Finale.'],
			['money-bill', 'fal fa-money-bill', 'IDR 300M', 'Winners: IDR 200M\nRunners-up: IDR 100M'],
			['plane', 'fal fa-plane', 'Dubai Package', 'Return airfare, hotel stay and Grand Finale entry.'],
			['car', 'fal fa-car', 'Hole-in-One', 'Lexus ES 350 Hybrid — luxury, performance and prestige.'],
			['glass-cheers', 'fal fa-glass-cheers', 'Novelty Awards', 'Exciting on-course challenges and special recognition throughout the championship.'],
		];
		const preset = presets[index] || ['star', 'fal fa-star', 'Card ' + (index + 1), 'Add a short description.'];
		return {
			id: 'event-media-grid-card-' + (index + 1),
			showImage: true,
			imageSource: 'ckfinder', imageUrl: '', imageAlt: '', imagePresentation: 'element',
			showIcon: true,
			iconStyle: 'light', iconName: preset[0], iconClass: preset[1], iconSource: 'library', iconSvg: '',
			title: preset[2], description: preset[3],
			iconColor: '', titleColor: '', descriptionColor: '',
			surface: 'inherit', customBackgroundColor: '', customBorderColor: '',
		};
	}

	function eventMediaGridDefaults() {
		return {
			...widgetAdvancedDefaults(),
			advancedBackgroundType: 'classic', advancedBackgroundColor: '#091d31',
			advancedBorderType: 'none', advancedBorderWidth: '0px', advancedBorderColor: '#091d31',
			paddingTop: '28px', paddingRight: '28px', paddingBottom: '28px', paddingLeft: '28px',
			paddingTopTablet: '24px', paddingRightTablet: '24px', paddingBottomTablet: '24px', paddingLeftTablet: '24px',
			paddingTopMobile: '20px', paddingRightMobile: '20px', paddingBottomMobile: '20px', paddingLeftMobile: '20px',

			footerText: 'Prize information is subject to final event terms and conditions.',
			footerPosition: 'bottom', footerPositionTablet: '', footerPositionMobile: '',
			footerAlign: 'left', footerAlignTablet: '', footerAlignMobile: '',
			footerGap: '28px', footerGapTablet: '24px', footerGapMobile: '20px',
			footerFontFamily: 'inherit', footerFontSize: '16px', footerFontSizeTablet: '', footerFontSizeMobile: '', footerFontWeight: '400', footerColor: '#aab6c8',
			footerLineHeight: '1.6em', footerLineHeightTablet: '', footerLineHeightMobile: '', footerLetterSpacing: '0px', footerLetterSpacingTablet: '', footerLetterSpacingMobile: '', footerWordSpacing: '0px', footerWordSpacingTablet: '', footerWordSpacingMobile: '', footerTextTransform: 'none', footerFontStyle: 'normal', footerTextDecoration: 'none', footerTextShadow: 'none',

			gridColumns: '3', gridColumnsTablet: '2', gridColumnsMobile: '1',
			gridContentWidthMode: 'max', gridContentWidthModeTablet: '', gridContentWidthModeMobile: '',
			gridContentMaxWidth: '1636px', gridContentMaxWidthTablet: '', gridContentMaxWidthMobile: '',
			columnGap: '22px', columnGapTablet: '18px', columnGapMobile: '16px',
			rowGap: '22px', rowGapTablet: '18px', rowGapMobile: '16px',

			cardHeightMode: 'custom', cardHeightModeTablet: '', cardHeightModeMobile: 'auto',
			cardHeight: '472px', cardHeightTablet: '420px', cardHeightMobile: '',
			cardAlignment: 'left', cardAlignmentTablet: '', cardAlignmentMobile: '',
			cardContentGap: '24px', cardContentGapTablet: '20px', cardContentGapMobile: '16px',
			iconTitleGap: '32px', iconTitleGapTablet: '28px', iconTitleGapMobile: '24px',
			cardIconSize: '38px', cardIconSizeTablet: '', cardIconSizeMobile: '',
			cardIconColor: '#d8ad5e', cardTitleColor: '#f4efe4', cardDescriptionColor: '#aab6c8',
			cardTitleFontFamily: 'Georgia, serif', cardTitleFontSize: '28px', cardTitleFontSizeTablet: '', cardTitleFontSizeMobile: '', cardTitleFontWeight: '400', cardTitleLineHeight: '1.5em', cardTitleLineHeightTablet: '', cardTitleLineHeightMobile: '', cardTitleLetterSpacing: '0px', cardTitleLetterSpacingTablet: '', cardTitleLetterSpacingMobile: '', cardTitleWordSpacing: '0px', cardTitleWordSpacingTablet: '', cardTitleWordSpacingMobile: '', cardTitleTextTransform: 'none', cardTitleFontStyle: 'normal', cardTitleTextDecoration: 'none', cardTitleTextShadow: 'none',
			cardDescriptionFontFamily: 'inherit', cardDescriptionFontSize: '18px', cardDescriptionFontSizeTablet: '', cardDescriptionFontSizeMobile: '', cardDescriptionFontWeight: '400', cardDescriptionLineHeight: '1.8em', cardDescriptionLineHeightTablet: '', cardDescriptionLineHeightMobile: '', cardDescriptionLetterSpacing: '0px', cardDescriptionLetterSpacingTablet: '', cardDescriptionLetterSpacingMobile: '', cardDescriptionWordSpacing: '0px', cardDescriptionWordSpacingTablet: '', cardDescriptionWordSpacingMobile: '', cardDescriptionTextTransform: 'none', cardDescriptionFontStyle: 'normal', cardDescriptionTextDecoration: 'none', cardDescriptionTextShadow: 'none',

			imageWidth: '100%', imageWidthTablet: '', imageWidthMobile: '', imageHeight: '195px', imageHeightTablet: '180px', imageHeightMobile: '160px', imageObjectFit: 'cover', imageBackgroundPosition: 'center center', imageBackgroundSize: 'cover', imageBackgroundRepeat: 'no-repeat',
			imagePaddingTop: '0px', imagePaddingRight: '0px', imagePaddingBottom: '0px', imagePaddingLeft: '0px', imagePaddingTopTablet: '', imagePaddingRightTablet: '', imagePaddingBottomTablet: '', imagePaddingLeftTablet: '', imagePaddingTopMobile: '', imagePaddingRightMobile: '', imagePaddingBottomMobile: '', imagePaddingLeftMobile: '',
			imageMarginTop: '0px', imageMarginRight: '0px', imageMarginBottom: '0px', imageMarginLeft: '0px', imageMarginTopTablet: '', imageMarginRightTablet: '', imageMarginBottomTablet: '', imageMarginLeftTablet: '', imageMarginTopMobile: '', imageMarginRightMobile: '', imageMarginBottomMobile: '', imageMarginLeftMobile: '',
			imageBorderType: 'none', imageBorderWidth: '0px', imageBorderColor: '#3a413f', imageBorderRadius: '0px', imageBorderRadiusTablet: '', imageBorderRadiusMobile: '',

			cardBackgroundColor: '#0a1e33', cardBackgroundColorHover: 'rgba(216,173,94,.06)',
			cardBorderType: 'solid', cardBorderTypeHover: 'solid', cardBorderColor: '#3a413f', cardBorderColorHover: '#d8ad5e',
			cardBorderWidthTop: '1px', cardBorderWidthRight: '1px', cardBorderWidthBottom: '1px', cardBorderWidthLeft: '1px', cardBorderWidthTopTablet: '', cardBorderWidthRightTablet: '', cardBorderWidthBottomTablet: '', cardBorderWidthLeftTablet: '', cardBorderWidthTopMobile: '', cardBorderWidthRightMobile: '', cardBorderWidthBottomMobile: '', cardBorderWidthLeftMobile: '',
			cardRadiusTL: '0px', cardRadiusTR: '0px', cardRadiusBR: '0px', cardRadiusBL: '0px', cardRadiusTLTablet: '', cardRadiusTRTablet: '', cardRadiusBRTablet: '', cardRadiusBLTablet: '', cardRadiusTLMobile: '', cardRadiusTRMobile: '', cardRadiusBRMobile: '', cardRadiusBLMobile: '',
			cardPaddingTop: '40px', cardPaddingRight: '32px', cardPaddingBottom: '40px', cardPaddingLeft: '32px', cardPaddingTopTablet: '32px', cardPaddingRightTablet: '28px', cardPaddingBottomTablet: '32px', cardPaddingLeftTablet: '28px', cardPaddingTopMobile: '24px', cardPaddingRightMobile: '20px', cardPaddingBottomMobile: '24px', cardPaddingLeftMobile: '20px',
			cards: [0, 1, 2, 3, 4, 5].map(cardDefaults),
		};
	}

	function normalizeCard(card, index, usedIds) {
		const defaults = cardDefaults(index);
		const source = card && typeof card === 'object' && !Array.isArray(card) ? card : {};
		let id = String(source.id || defaults.id).trim().replace(/[^A-Za-z0-9_-]/g, '-') || defaults.id;
		const originalId = id;
		let suffix = 2;
		while (usedIds.has(id)) id = originalId + '-' + suffix++;
		usedIds.add(id);
		const iconClass = /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(String(source.iconClass || '').trim()) ? String(source.iconClass).trim() : defaults.iconClass;
		const iconSvg = normalizeSvg(source.iconSvg);
		return {
			...defaults, ...source, id,
			showImage: normalizeBoolean(source.showImage, defaults.showImage),
			imageSource: IMAGE_SOURCES.includes(source.imageSource) ? source.imageSource : defaults.imageSource,
			imageUrl: normalizeUrl(source.imageUrl), imageAlt: String(source.imageAlt == null ? '' : source.imageAlt).trim(),
			imagePresentation: IMAGE_PRESENTATIONS.includes(source.imagePresentation) ? source.imagePresentation : defaults.imagePresentation,
			showIcon: normalizeBoolean(source.showIcon, defaults.showIcon),
			iconStyle: ICON_STYLES.includes(source.iconStyle) ? source.iconStyle : defaults.iconStyle,
			iconName: String(source.iconName || defaults.iconName).trim().replace(/^fa-/, '').replace(/[^a-z0-9-]/gi, '') || defaults.iconName,
			iconClass,
			iconSource: source.iconSource === 'svg' && iconSvg ? 'svg' : 'library', iconSvg,
			title: String(source.title == null ? defaults.title : source.title),
			description: String(source.description == null ? defaults.description : source.description),
			iconColor: normalizeColor(source.iconColor, ''), titleColor: normalizeColor(source.titleColor, ''), descriptionColor: normalizeColor(source.descriptionColor, ''),
			surface: SURFACES.includes(source.surface) ? source.surface : defaults.surface,
			customBackgroundColor: normalizeColor(source.customBackgroundColor, ''), customBorderColor: normalizeColor(source.customBorderColor, ''),
		};
	}

	function normalizeEventMediaGridSettings(settings) {
		const defaults = eventMediaGridDefaults();
		Object.keys(defaults).forEach((key) => { if (settings[key] === undefined) settings[key] = clone(defaults[key]); });
		settings.footerText = String(settings.footerText == null ? '' : settings.footerText);
		normalizeResponsiveEnum(settings, 'footerPosition', FOOTER_POSITIONS, 'bottom');
		normalizeResponsiveEnum(settings, 'footerAlign', ALIGNMENTS, 'left');
		normalizeResponsiveDimension(settings, 'footerGap', '28px');
		normalizeResponsiveEnum(settings, 'gridColumns', GRID_COLUMNS, '3');
		normalizeResponsiveEnum(settings, 'gridContentWidthMode', CONTENT_WIDTH_MODES, 'max');
		normalizeResponsiveDimension(settings, 'gridContentMaxWidth', '1636px');
		normalizeResponsiveDimension(settings, 'columnGap', '22px'); normalizeResponsiveDimension(settings, 'rowGap', '22px');
		normalizeResponsiveEnum(settings, 'cardHeightMode', HEIGHT_MODES, 'custom'); normalizeResponsiveDimension(settings, 'cardHeight', '472px');
		normalizeResponsiveEnum(settings, 'cardAlignment', ALIGNMENTS, 'left'); normalizeResponsiveDimension(settings, 'cardContentGap', '24px'); normalizeResponsiveDimension(settings, 'iconTitleGap', '32px'); normalizeResponsiveDimension(settings, 'cardIconSize', '38px');
		normalizeResponsiveDimension(settings, 'imageWidth', '100%'); normalizeResponsiveDimension(settings, 'imageHeight', '195px'); normalizeResponsiveEnum(settings, 'imageObjectFit', OBJECT_FITS, 'cover');
		settings.imageBackgroundPosition = ['center center', 'top center', 'bottom center', 'center left', 'center right'].includes(settings.imageBackgroundPosition) ? settings.imageBackgroundPosition : 'center center';
		settings.imageBackgroundSize = ['cover', 'contain', 'auto', '100% 100%'].includes(settings.imageBackgroundSize) ? settings.imageBackgroundSize : 'cover';
		settings.imageBackgroundRepeat = ['no-repeat', 'repeat', 'repeat-x', 'repeat-y'].includes(settings.imageBackgroundRepeat) ? settings.imageBackgroundRepeat : 'no-repeat';
		normalizeBoxSpacing(settings, 'image'); normalizeResponsiveEnum(settings, 'imageBorderType', BORDER_TYPES, 'none'); normalizeResponsiveDimension(settings, 'imageBorderWidth', '0px'); normalizeResponsiveDimension(settings, 'imageBorderRadius', '0px');
		normalizeTypography(settings, 'footer', { fontFamily: 'inherit', fontsize: '16px', lineheight: '1.6em', letterspacing: '0px', wordspacing: '0px', fontWeight: '400', color: '#aab6c8' });
		normalizeTypography(settings, 'cardTitle', { fontFamily: 'Georgia, serif', fontsize: '28px', lineheight: '1.5em', letterspacing: '0px', wordspacing: '0px', fontWeight: '400', color: '#f4efe4' });
		normalizeTypography(settings, 'cardDescription', { fontFamily: 'inherit', fontsize: '18px', lineheight: '1.8em', letterspacing: '0px', wordspacing: '0px', fontWeight: '400', color: '#aab6c8' });
		settings.cardIconColor = normalizeColor(settings.cardIconColor, '#d8ad5e'); settings.cardTitleColor = normalizeColor(settings.cardTitleColor, '#f4efe4'); settings.cardDescriptionColor = normalizeColor(settings.cardDescriptionColor, '#aab6c8');
		settings.cardBackgroundColor = normalizeColor(settings.cardBackgroundColor, '#0a1e33'); settings.cardBackgroundColorHover = normalizeColor(settings.cardBackgroundColorHover, 'transparent'); settings.cardBorderColor = normalizeColor(settings.cardBorderColor, '#3a413f'); settings.cardBorderColorHover = normalizeColor(settings.cardBorderColorHover, '#d8ad5e');
		settings.cardBorderType = BORDER_TYPES.includes(settings.cardBorderType) ? settings.cardBorderType : 'solid'; settings.cardBorderTypeHover = BORDER_TYPES.includes(settings.cardBorderTypeHover) ? settings.cardBorderTypeHover : 'solid';
		for (const side of ['Top', 'Right', 'Bottom', 'Left']) { const borderKey = 'cardBorderWidth' + side; settings[borderKey] = normalizeDimension(settings[borderKey], '1px'); }
		for (const corner of ['TL', 'TR', 'BR', 'BL']) settings['cardRadius' + corner] = normalizeDimension(settings['cardRadius' + corner], '0px');
		normalizeBoxSpacing(settings, 'card');
		const incoming = Array.isArray(settings.cards) ? settings.cards : [];
		const sourceCards = incoming.length ? incoming.slice(0, 12) : defaults.cards;
		const usedIds = new Set(); settings.cards = sourceCards.map((card, index) => normalizeCard(card, index, usedIds));
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

	registry.register({
		type: 'event_media_grid',
		defaults: eventMediaGridDefaults,
		normalize(node) {
			const normalized = node && typeof node === 'object' ? node : {};
			normalized.settings = { ...eventMediaGridDefaults(), ...(normalized.settings || {}) };
			normalizeEventMediaGridSettings(normalized.settings);
			return normalized;
		},
		editor: { iconTargets: { cardIcon: { prefix: 'icon', collection: 'cards' } } },
	});
})(window.PageBuilderElementorV24Widgets);
