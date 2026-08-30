(function (root) {
	'use strict';

	const VIEWPORT_SUFFIXES = Object.freeze({ mobile: 'Mobile', tablet: 'Tablet', desktop: '' });
	const RESPONSIVE_VIEWPORTS = Object.freeze(['mobile', 'tablet', 'desktop']);
	const SIDES = Object.freeze(['Top', 'Right', 'Bottom', 'Left']);
	const BORDER_SIDES = Object.freeze([
		['Top', 'top'], ['Right', 'right'], ['Bottom', 'bottom'], ['Left', 'left'],
	]);
	const OBJECT_FITS = new Set(['fill', 'cover', 'contain', 'scale-down', 'none']);
	const ALIGNMENTS = new Set(['left', 'center', 'right', 'justify', 'stretch']);
	const BORDER_TYPES = new Set(['none', 'solid', 'double', 'dotted', 'dashed', 'groove']);

	function safeValue(value, fallback = '') {
		const output = String(value == null ? '' : value).trim();
		if (!output || output.length > 256 || /[<>{};\x00-\x1F\x7F]|(?:javascript:|vbscript:|expression\s*\()/i.test(output)) return fallback;
		return output;
	}

	function safeLength(value) {
		const output = safeValue(value);
		return /^-?(?:\d+(?:\.\d+)?|\.\d+)(?:px|pt|pc|in|cm|mm|q|em|rem|ex|ch|vw|vh|vmin|vmax|%)?$/i.test(output)
			|| /^(?:auto|none|max-content|min-content|fit-content|normal)$/i.test(output)
			? output
			: '';
	}

	function safeColor(value) {
		const output = safeValue(value);
		return /^(?:transparent|currentcolor|#[0-9a-f]{3,8}|(?:rgb|hsl|hwb|lab|lch|oklab|oklch)a?\([^)]*\)|[a-z]+)$/i.test(output) ? output : '';
	}

	function safeFontFamily(value) {
		const output = safeValue(value);
		return output && /^[A-Za-z0-9 _,.'"-]+$/.test(output) ? output : '';
	}

	function safeNumber(value) {
		const output = Number(value);
		return Number.isFinite(output) ? output : null;
	}

	function responsiveKey(base, viewport) {
		return base + (VIEWPORT_SUFFIXES[viewport] || '');
	}

	function setResponsive(settings, base, viewport, value) {
		if (!settings || !RESPONSIVE_VIEWPORTS.includes(viewport) || value === '' || value === null || value === undefined) return;
		settings[responsiveKey(base, viewport)] = value;
	}

	function computedFor(info, viewport) {
		return info && info.computed && typeof info.computed[viewport] === 'object' ? info.computed[viewport] : null;
	}

	function applyResponsiveBox(settings, computed, viewport) {
		if (!settings || !computed) return;
		SIDES.forEach((side) => {
			const padding = safeLength(computed['padding' + side]);
			const margin = safeLength(computed['margin' + side]);
			if (padding !== '') setResponsive(settings, 'padding' + side, viewport, padding);
			if (margin !== '') setResponsive(settings, 'margin' + side, viewport, margin);
		});
		const paddingUnit = String(computed.paddingTop || '').match(/[a-z%]+$/i)?.[0]?.toLowerCase();
		if (paddingUnit && ['px', 'em', 'rem', '%', 'vw', 'vh'].includes(paddingUnit)) setResponsive(settings, 'paddingUnit', viewport, paddingUnit);
		const marginUnit = String(computed.marginTop || '').match(/[a-z%]+$/i)?.[0]?.toLowerCase();
		if (marginUnit && ['px', 'em', 'rem', '%', 'vw', 'vh', 'auto'].includes(marginUnit)) setResponsive(settings, 'marginUnit', viewport, marginUnit);
	}

	function applyResponsiveBorder(settings, computed, viewport, prefix = '') {
		if (!settings || !computed) return;
		const topStyle = safeValue(computed.borderTopStyle, 'none').toLowerCase();
		const topWidth = safeLength(computed.borderTopWidth);
		const topColor = safeColor(computed.borderTopColor);
		if (prefix === '') {
			if (BORDER_TYPES.has(topStyle)) setResponsive(settings, 'borderType', viewport, topStyle);
			if (topWidth !== '') setResponsive(settings, 'borderWidth', viewport, topWidth);
			if (topColor !== '') setResponsive(settings, 'borderColor', viewport, topColor);
		} else {
			if (BORDER_TYPES.has(topStyle)) setResponsive(settings, prefix + 'BorderType', viewport, topStyle);
			if (topWidth !== '') setResponsive(settings, prefix + 'BorderWidth', viewport, topWidth);
			if (topColor !== '') setResponsive(settings, prefix + 'BorderColor', viewport, topColor);
		}
		const radiusKeys = [
			['borderRadiusTL', 'borderTopLeftRadius'],
			['borderRadiusTR', 'borderTopRightRadius'],
			['borderRadiusBR', 'borderBottomRightRadius'],
			['borderRadiusBL', 'borderBottomLeftRadius'],
		];
		radiusKeys.forEach(([key, sourceKey]) => {
			const radius = safeLength(computed[sourceKey]);
			if (radius !== '') setResponsive(settings, prefix + key, viewport, radius);
		});
	}

	function trackCount(template) {
		const source = safeValue(template);
		if (!source || source === 'none') return 1;
		let count = 0;
		let token = '';
		let depth = 0;
		for (let index = 0; index < source.length; index += 1) {
			const character = source[index];
			if (character === '(') depth += 1;
			if (character === ')') depth = Math.max(0, depth - 1);
			if (/\s/.test(character) && depth === 0) {
				if (token) { count += token.toLowerCase().startsWith('repeat(') ? Number(token.match(/^repeat\((\d+)/i)?.[1] || 1) : 1; token = ''; }
				continue;
			}
			token += character;
		}
		if (token) count += token.toLowerCase().startsWith('repeat(') ? Number(token.match(/^repeat\((\d+)/i)?.[1] || 1) : 1;
		return Math.max(1, Math.min(12, count || 1));
	}

	function applyResponsiveLayout(settings, computed, viewport) {
		if (!settings || !computed) return;
		const display = safeValue(computed.display).toLowerCase();
		if (display === 'grid' || display === 'inline-grid') {
			settings.displayType = 'grid';
			setResponsive(settings, 'gridColumns', viewport, trackCount(computed.gridTemplateColumns));
			const template = safeValue(computed.gridTemplateColumns);
			if (template && template !== 'none' && !/^repeat\(\s*\d+\s*,\s*minmax\(0px,\s*1fr\)\s*\)$/i.test(template)) setResponsive(settings, 'gridTemplateColumns', viewport, template);
			setResponsive(settings, 'gridColumnGap', viewport, safeLength(computed.columnGap));
			setResponsive(settings, 'gridRowGap', viewport, safeLength(computed.rowGap));
			setResponsive(settings, 'gridJustifyItems', viewport, safeValue(computed.justifyItems));
			setResponsive(settings, 'gridAlignItems', viewport, safeValue(computed.alignItems));
		} else if (display === 'flex' || display === 'inline-flex') {
			settings.displayType = 'flex';
			setResponsive(settings, 'direction', viewport, safeValue(computed.flexDirection));
			setResponsive(settings, 'flexWrap', viewport, safeValue(computed.flexWrap));
			setResponsive(settings, 'justifyContent', viewport, safeValue(computed.justifyContent));
			setResponsive(settings, 'alignItems', viewport, safeValue(computed.alignItems));
			setResponsive(settings, 'alignContent', viewport, safeValue(computed.alignContent));
			setResponsive(settings, 'flexColumnGap', viewport, safeLength(computed.columnGap));
			setResponsive(settings, 'flexRowGap', viewport, safeLength(computed.rowGap));
		}
		const width = safeLength(computed.width);
		const maxWidth = safeLength(computed.maxWidth);
		const minHeight = safeLength(computed.minHeight);
		if (width !== '') setResponsive(settings, 'containerWidth', viewport, width);
		if (maxWidth !== '') setResponsive(settings, 'maxWidth', viewport, maxWidth);
		if (minHeight !== '') setResponsive(settings, 'minHeight', viewport, minHeight);
		const position = safeValue(computed.position).toLowerCase();
		if (['static', 'relative', 'absolute', 'fixed', 'sticky'].includes(position)) setResponsive(settings, 'position', viewport, position === 'static' ? 'default' : position);
		const overflow = safeValue(computed.overflow).toLowerCase();
		if (overflow) setResponsive(settings, 'overflow', viewport, overflow);
	}

	function applyResponsiveVisual(settings, computed, viewport) {
		if (!settings || !computed) return;
		const color = safeColor(computed.color);
		const backgroundColor = safeColor(computed.backgroundColor);
		if (color !== '') {
			if (settings.headingFontSize !== undefined || settings.headingFontFamily !== undefined) setResponsive(settings, 'color', viewport, color);
			else if (settings.textEditorFontFamily !== undefined || settings.textEditorFontSize !== undefined) setResponsive(settings, 'textEditorTextColor', viewport, color);
		}
		if (backgroundColor && backgroundColor !== 'transparent' && settings.bgColor !== undefined) {
			setResponsive(settings, 'bgColor', viewport, backgroundColor);
			setResponsive(settings, 'bgType', viewport, 'classic');
		}
		applyResponsiveBorder(settings, computed, viewport);
	}

	function applyResponsivePosition(settings, computed, viewport) {
		if (!settings || !computed) return;
		const position = safeValue(computed.position).toLowerCase();
		if (['static', 'relative', 'absolute', 'fixed', 'sticky'].includes(position)) settings.position = position === 'static' ? 'default' : position;
		const left = safeLength(computed.left);
		const right = safeLength(computed.right);
		const top = safeLength(computed.top);
		const bottom = safeLength(computed.bottom);
		if (settings.position !== 'default') {
			if (right && right !== 'auto' && (!left || left === 'auto')) {
				settings.horizontalOrientation = 'right';
				setResponsive(settings, 'positionX', viewport, right);
			} else if (left && left !== 'auto') {
				settings.horizontalOrientation = 'left';
				setResponsive(settings, 'positionX', viewport, left);
			}
			if (bottom && bottom !== 'auto' && (!top || top === 'auto')) {
				settings.verticalOrientation = 'bottom';
				setResponsive(settings, 'positionY', viewport, bottom);
			} else if (top && top !== 'auto') {
				settings.verticalOrientation = 'top';
				setResponsive(settings, 'positionY', viewport, top);
			}
		}
		const zIndex = safeValue(computed.zIndex);
		if (/^-?\d+$/.test(zIndex)) setResponsive(settings, 'zIndex', viewport, zIndex);
	}

	function applyResponsiveVisibility(settings, computed, viewport) {
		if (!settings || !computed) return;
		const display = safeValue(computed.display).toLowerCase();
		if (!display) return;
		const key = viewport === 'desktop' ? 'hideDesktop' : ('hide' + (VIEWPORT_SUFFIXES[viewport] || ''));
		settings[key] = display === 'none';
	}

	function applyHeading(settings, computed, viewport) {
		if (!settings || !computed) return;
		const fontFamily = safeFontFamily(computed.fontFamily);
		const fontSize = safeLength(computed.fontSize);
		const lineHeight = safeLength(computed.lineHeight);
		const letterSpacing = safeLength(computed.letterSpacing);
		const wordSpacing = safeLength(computed.wordSpacing);
		if (fontFamily) settings.headingFontFamily = fontFamily;
		if (fontSize) { settings.headingFontSizeMode = 'custom'; setResponsive(settings, 'headingFontSize', viewport, fontSize); }
		if (safeValue(computed.fontWeight)) settings.headingFontWeight = safeValue(computed.fontWeight);
		if (lineHeight) setResponsive(settings, 'headingLineHeight', viewport, lineHeight);
		if (letterSpacing) setResponsive(settings, 'headingLetterSpacing', viewport, letterSpacing);
		if (wordSpacing) setResponsive(settings, 'headingWordSpacing', viewport, wordSpacing);
		const align = safeValue(computed.textAlign).toLowerCase();
		if (ALIGNMENTS.has(align)) setResponsive(settings, 'align', viewport, align);
		['textTransform', 'fontStyle', 'textDecoration'].forEach((field) => {
			const value = safeValue(computed[field]);
			if (value) settings['heading' + field[0].toUpperCase() + field.slice(1)] = value;
		});
		const color = safeColor(computed.color);
		if (color) settings.color = color;
	}

	function applyTextEditor(settings, computed, viewport) {
		if (!settings || !computed) return;
		const fontFamily = safeFontFamily(computed.fontFamily);
		const fontSize = safeLength(computed.fontSize);
		const lineHeight = safeLength(computed.lineHeight);
		const letterSpacing = safeLength(computed.letterSpacing);
		const wordSpacing = safeLength(computed.wordSpacing);
		if (fontFamily) settings.textEditorFontFamily = fontFamily;
		if (fontSize) setResponsive(settings, 'textEditorFontSize', viewport, fontSize);
		if (safeValue(computed.fontWeight)) settings.textEditorFontWeight = safeValue(computed.fontWeight);
		if (lineHeight) setResponsive(settings, 'textEditorLineHeight', viewport, lineHeight);
		if (letterSpacing) setResponsive(settings, 'textEditorLetterSpacing', viewport, letterSpacing);
		if (wordSpacing) setResponsive(settings, 'textEditorWordSpacing', viewport, wordSpacing);
		const align = safeValue(computed.textAlign).toLowerCase();
		if (ALIGNMENTS.has(align)) setResponsive(settings, 'align', viewport, align);
		const color = safeColor(computed.color);
		if (color) setResponsive(settings, 'textEditorTextColor', viewport, color);
	}

	function applyImage(settings, computed, viewport) {
		if (!settings || !computed) return;
		['width', 'maxWidth', 'height'].forEach((field) => {
			const value = safeLength(computed[field]);
			if (value) setResponsive(settings, field, viewport, value);
		});
		const objectFit = safeValue(computed.objectFit).toLowerCase();
		if (OBJECT_FITS.has(objectFit)) setResponsive(settings, 'objectFit', viewport, objectFit === 'none' ? 'default' : objectFit);
		const objectPosition = safeValue(computed.objectPosition);
		if (objectPosition) setResponsive(settings, 'objectPosition', viewport, objectPosition);
		const opacity = safeNumber(computed.opacity);
		if (opacity !== null) setResponsive(settings, 'imageNormalOpacity', viewport, Math.max(0, Math.min(1, opacity)));
		applyResponsiveBorder(settings, computed, viewport, 'image');
	}

	function applyButton(settings, computed, viewport) {
		if (!settings || !computed) return;
		const fontFamily = safeFontFamily(computed.fontFamily);
		const fontSize = safeLength(computed.fontSize);
		const lineHeight = safeLength(computed.lineHeight);
		const color = safeColor(computed.color);
		const backgroundColor = safeColor(computed.backgroundColor);
		if (fontFamily) settings.buttonFontFamily = fontFamily;
		if (fontSize) setResponsive(settings, 'buttonFontSize', viewport, fontSize);
		if (lineHeight) setResponsive(settings, 'buttonLineHeight', viewport, lineHeight);
		if (safeValue(computed.fontWeight)) settings.buttonFontWeight = safeValue(computed.fontWeight);
		if (color) setResponsive(settings, 'buttonTextColor', viewport, color);
		if (backgroundColor && backgroundColor !== 'transparent') setResponsive(settings, 'buttonBackgroundColor', viewport, backgroundColor);
		const padding = ['Top', 'Right', 'Bottom', 'Left'].map((side) => safeLength(computed['padding' + side]));
		if (padding.every(Boolean)) setResponsive(settings, 'buttonPadding', viewport, padding.join(' '));
		const radius = safeLength(computed.borderTopLeftRadius);
		if (radius) setResponsive(settings, 'buttonBorderRadius', viewport, radius);
		applyResponsiveBorder(settings, computed, viewport, 'button');
	}

	function applyIcon(settings, computed, viewport) {
		if (!settings || !computed) return;
		const size = safeLength(computed.fontSize);
		const color = safeColor(computed.color);
		if (size) setResponsive(settings, 'iconSize', viewport, size);
		if (color) setResponsive(settings, 'primaryColor', viewport, color);
		const align = safeValue(computed.textAlign).toLowerCase();
		if (ALIGNMENTS.has(align)) setResponsive(settings, 'align', viewport, align);
	}

	function applyDivider(settings, computed, viewport) {
		if (!settings || !computed) return;
		const width = safeLength(computed.width);
		if (width) setResponsive(settings, 'width', viewport, width);
		const height = safeLength(computed.height);
		if (height && height !== '0px') setResponsive(settings, 'thickness', viewport, height);
		const color = safeColor(computed.borderTopColor || computed.backgroundColor);
		if (color) setResponsive(settings, 'color', viewport, color);
	}

	function applyNodeSnapshot(node, info) {
		if (!node || !node.settings || !info) return;
		const type = String(node.type || '').toLowerCase();
		RESPONSIVE_VIEWPORTS.forEach((viewport) => {
			const computed = computedFor(info, viewport);
			if (!computed) return;
			applyResponsiveBox(node.settings, computed, viewport);
			applyResponsivePosition(node.settings, computed, viewport);
			applyResponsiveVisibility(node.settings, computed, viewport);
			if (type === 'container' || type === 'container_fluid' || type === 'grid' || type === 'row_grid') applyResponsiveLayout(node.settings, computed, viewport);
			if (type === 'heading') applyHeading(node.settings, computed, viewport);
			if (type === 'text_editor') applyTextEditor(node.settings, computed, viewport);
			if (type === 'image') applyImage(node.settings, computed, viewport);
			if (type === 'button') applyButton(node.settings, computed, viewport);
			if (type === 'icon') applyIcon(node.settings, computed, viewport);
			if (type === 'divider') applyDivider(node.settings, computed, viewport);
			applyResponsiveVisual(node.settings, computed, viewport);
		});
	}

	function collectNodes(nodes, output = []) {
		(Array.isArray(nodes) ? nodes : []).forEach((node) => {
			if (!node || typeof node !== 'object') return;
			output.push(node);
			collectNodes(node.children, output);
			(Array.isArray(node.columns) ? node.columns : []).forEach((column) => collectNodes(column && column.children, output));
			['tabItems', 'accordionItems'].forEach((key) => (Array.isArray(node[key]) ? node[key] : []).forEach((item) => collectNodes(item && item.children, output)));
		});
		return output;
	}

	function applyComputedSnapshot(layout, snapshot) {
		const byMarker = new Map();
		(Array.isArray(snapshot && snapshot.nodes) ? snapshot.nodes : []).forEach((info) => {
			const marker = String(info && info.marker || '').trim();
			if (marker) byMarker.set(marker, info);
		});
		collectNodes(layout).forEach((node) => {
			const marker = String(node.settings?.importNodeKey || '').trim();
			if (byMarker.has(marker)) applyNodeSnapshot(node, byMarker.get(marker));
		});
		return layout;
	}

	function replaceFallbackSections(layout, sections, createNode) {
		if (!Array.isArray(layout) || typeof createNode !== 'function') return layout;
		const fallbackByMarker = new Map((Array.isArray(sections) ? sections : [])
			.filter((section) => section && section.fallback)
			.map((section) => [String(section.marker || '').trim(), section])
			.filter(([marker]) => marker));
		const walk = (list) => {
			if (!Array.isArray(list)) return;
			for (let index = 0; index < list.length; index += 1) {
				const node = list[index];
				if (!node || typeof node !== 'object') continue;
				const marker = String(node.settings?.importNodeKey || '').trim();
				const section = fallbackByMarker.get(marker);
				if (section) {
					list[index] = createNode(section, node);
					continue;
				}
				walk(node.children);
				(Array.isArray(node.columns) ? node.columns : []).forEach((column) => walk(column && column.children));
				['tabItems', 'accordionItems'].forEach((key) => (Array.isArray(node[key]) ? node[key] : []).forEach((item) => walk(item && item.children)));
			}
		};
		walk(layout);
		return layout;
	}

	root.PhoenixStaticImportNative = {
		applyComputedSnapshot,
		collectNodes,
		replaceFallbackSections,
		__test: { applyNodeSnapshot, collectNodes, computedFor, replaceFallbackSections, safeColor, safeLength, trackCount },
	};
})(typeof window !== 'undefined' ? window : globalThis);
