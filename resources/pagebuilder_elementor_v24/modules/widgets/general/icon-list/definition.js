(function (registry) {
	'use strict';
	const widgetAdvancedDefaults = () => registry.advancedDefaults();
	const normalizeWidgetAdvancedSettings = (settings) => registry.normalizeAdvanced(settings);

function uid(p)       { return p + '_' + Math.random().toString(36).slice(2, 9); }

function jclone(v)    { return JSON.parse(JSON.stringify(v)); }

function fontAwesomeStylePrefix(style) {
		if (style === 'brands') return 'fab';
		if (style === 'light') return 'fal';
		if (style === 'duotone') return 'fad';
		if (style === 'solid') return 'fas';
		return 'far';
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

function iconListWidgetDefaults() {
		return {
			...widgetAdvancedDefaults(),
			layout: 'traditional',
			items: [
				{ id: uid('ili'), text: 'List Item #1', iconStyle: 'solid', iconName: 'check', iconClass: 'fas fa-check', linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [] },
				{ id: uid('ili'), text: 'List Item #2', iconStyle: 'solid', iconName: 'times', iconClass: 'fas fa-times', linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [] },
				{ id: uid('ili'), text: 'List Item #3', iconStyle: 'solid', iconName: 'dot-circle', iconClass: 'fas fa-dot-circle', linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [] },
			],
			applyLinkOn: 'full_width',
			spaceBetween: '15px', spaceBetweenTablet: '', spaceBetweenMobile: '',
			alignment: 'start', alignmentTablet: '', alignmentMobile: '',
			divider: false, dividerStyle: 'solid', dividerWeight: '1px', dividerWidth: '100%', dividerHeight: '100%', dividerColor: '#dddddd',
			iconColor: '', iconColorHover: '', iconTransitionDuration: 0.3,
			iconSize: '14px', iconSizeTablet: '', iconSizeMobile: '', iconGap: '20px', iconGapTablet: '', iconGapMobile: '',
			iconHorizontalAlignment: '', iconHorizontalAlignmentTablet: '', iconHorizontalAlignmentMobile: '', iconVerticalAlignment: '', iconVerticalAlignmentTablet: '', iconVerticalAlignmentMobile: '',
			iconVerticalOffset: '0px', iconVerticalOffsetTablet: '', iconVerticalOffsetMobile: '',
			textColor: '', textColorHover: '', textTransitionDuration: 0.3,
			textFontFamily: 'inherit', textFontSize: '16px', textFontSizeTablet: '', textFontSizeMobile: '', textFontWeight: '400',
			textLineHeight: '1.5em', textLineHeightTablet: '', textLineHeightMobile: '', textLetterSpacing: '0px', textLetterSpacingTablet: '', textLetterSpacingMobile: '',
			textWordSpacing: '0px', textWordSpacingTablet: '', textWordSpacingMobile: '', textTextTransform: 'none', textFontStyle: 'normal', textTextDecoration: 'none', textTextShadow: 'none',
		};
	}

function normalizeIconListSettings(settings) {
		if (!settings || typeof settings !== 'object') return settings;
		const defaults = iconListWidgetDefaults();
		Object.keys(defaults).forEach((key) => { if (settings[key] === undefined) settings[key] = cloneSettingValue(defaults[key]); });
		settings.layout = ['traditional', 'inline'].includes(settings.layout) ? settings.layout : 'traditional';
		settings.applyLinkOn = ['full_width', 'inline'].includes(settings.applyLinkOn) ? settings.applyLinkOn : 'full_width';
		const seenIds = new Set();
		settings.items = (Array.isArray(settings.items) ? settings.items : defaults.items).map((item, index) => {
			const source = item && typeof item === 'object' ? item : {};
			let id = String(source.id || '').trim().replace(/[^A-Za-z0-9_-]/g, '');
			if (!id || seenIds.has(id)) id = uid('ili');
			seenIds.add(id);
			const parsed = parseIconWidgetClassParts(source.iconClass);
			const style = ['regular', 'solid', 'brands', 'light', 'duotone'].includes(source.iconStyle) ? source.iconStyle : (parsed.style || 'solid');
			const name = String(source.iconName || parsed.name || 'check').trim().toLowerCase().replace(/^fa-/, '').replace(/[^a-z0-9-]/g, '') || 'check';
			return { id, text: String(source.text == null ? ('List Item #' + (index + 1)) : source.text), iconStyle: style, iconName: name, iconClass: iconWidgetClassName(style, name), linkUrl: String(source.linkUrl || '').trim(), linkTarget: source.linkTarget === '_blank' ? '_blank' : '', linkNofollow: !!source.linkNofollow, linkCustomAttributes: normalizeAttributes(source.linkCustomAttributes) };
		});
		if (!settings.items.length) settings.items = cloneSettingValue(defaults.items);
		settings.alignment = ['start', 'center', 'end'].includes(settings.alignment) ? settings.alignment : 'start';
		['alignmentTablet', 'alignmentMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['start', 'center', 'end'].includes(settings[key]) ? settings[key] : ''; });
		settings.divider = !!settings.divider;
		settings.dividerStyle = ['solid', 'double', 'dotted', 'dashed'].includes(settings.dividerStyle) ? settings.dividerStyle : 'solid';
		settings.iconHorizontalAlignment = ['', 'left', 'center', 'right'].includes(settings.iconHorizontalAlignment) ? settings.iconHorizontalAlignment : '';
		settings.iconVerticalAlignment = ['', 'flex-start', 'center', 'flex-end'].includes(settings.iconVerticalAlignment) ? settings.iconVerticalAlignment : '';
		['iconHorizontalAlignmentTablet', 'iconHorizontalAlignmentMobile'].forEach((key) => { settings[key] = ['', 'left', 'center', 'right'].includes(settings[key]) ? settings[key] : ''; });
		['iconVerticalAlignmentTablet', 'iconVerticalAlignmentMobile'].forEach((key) => { settings[key] = ['', 'flex-start', 'center', 'flex-end'].includes(settings[key]) ? settings[key] : ''; });
		settings.iconTransitionDuration = clamp(Number(settings.iconTransitionDuration) || 0, 0, 10);
		settings.textTransitionDuration = clamp(Number(settings.textTransitionDuration) || 0, 0, 10);
		normalizeWidgetAdvancedSettings(settings);
		return settings;
	}

function clamp(v, min, max) {
		return Math.min(max, Math.max(min, v));
	}

function normalizeAttributes(attrs) {
		if (!Array.isArray(attrs)) return [];
		return attrs
			.map(attr => ({
				name: String(attr && (attr.name || attr.key) ? (attr.name || attr.key) : '').trim(),
				value: attr && attr.value != null ? String(attr.value) : '',
			}))
			.filter(attr => attr.name);
	}

function cloneSettingValue(value) {
		if (Array.isArray(value) || (value && typeof value === 'object')) return jclone(value);
		return value;
	}

	const implementation = {
			defaults: iconListWidgetDefaults,
			normalize(node) {
				const normalized = node && typeof node === 'object' ? node : {};
				normalized.settings = { ...iconListWidgetDefaults(), ...(normalized.settings || {}) };
				normalizeIconListSettings(normalized.settings);
				return normalized;
			},
		};
	registry.register({
		type: "icon_list",
		defaults: implementation.defaults,
		normalize: implementation.normalize,
		...(typeof implementation.createNode === 'function' ? { createNode: implementation.createNode } : {}),
	});
})(window.PageBuilderElementorV24Widgets);
