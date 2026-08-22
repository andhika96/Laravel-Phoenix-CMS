(function (registry) {
	'use strict';
	const defaults = () => ({
		iconStyle: 'regular', iconName: 'star', iconClass: 'far fa-star', view: 'default', shape: 'circle',
		link: '', openInNewWindow: false, nofollow: false, attributes: [], cssClass: '', align: 'left', alignTablet: '', alignMobile: '',
		primaryColor: '#6f7f94', primaryColorHover: '#54657d', secondaryColor: '#7b8796', secondaryColorHover: '#657181',
		iconSize: '52px', iconSizeTablet: '', iconSizeMobile: '', iconRotate: '0deg', iconRotateTablet: '', iconRotateMobile: '', iconTransitionDuration: 0.3,
	});
	const stylePrefix = (style) => ({ brands: 'fab', light: 'fal', duotone: 'fad', solid: 'fas' }[style] || 'far');
	registry.register({type: 'icon',defaults,normalize(node) {
			const settings = node.settings = { ...defaults(), ...(node.settings || {}) };
			const tokens = String(settings.iconClass || '').trim().split(/\s+/).filter(Boolean);
			const parsedStyle = tokens.includes('fas') ? 'solid' : tokens.includes('fab') ? 'brands' : tokens.includes('fal') ? 'light' : tokens.includes('fad') ? 'duotone' : 'regular';
			const parsedName = (tokens.find((token) => token.startsWith('fa-')) || 'fa-star').slice(3);
			settings.iconStyle = ['regular', 'solid', 'brands', 'light', 'duotone'].includes(settings.iconStyle) ? settings.iconStyle : parsedStyle;
			settings.iconName = String(settings.iconName || parsedName || 'star').trim().toLowerCase().replace(/^fa-/, '') || 'star';
			settings.iconClass = stylePrefix(settings.iconStyle) + ' fa-' + settings.iconName;
			settings.view = ['default', 'stacked', 'framed'].includes(settings.view) ? settings.view : 'default';
			settings.shape = ['circle', 'rounded', 'square'].includes(settings.shape) ? settings.shape : 'circle';
			settings.align = ['left', 'center', 'right'].includes(settings.align) ? settings.align : 'left';
			['alignTablet', 'alignMobile'].forEach((key) => { settings[key] = settings[key] === '' || ['left', 'center', 'right'].includes(settings[key]) ? settings[key] : ''; });
			settings.iconTransitionDuration = Number.isFinite(Number(settings.iconTransitionDuration)) ? Math.max(0, Math.min(10, Number(settings.iconTransitionDuration))) : 0.3;
			settings.link = String(settings.link || '').trim();
			settings.openInNewWindow = !!settings.openInNewWindow;
			settings.nofollow = !!settings.nofollow;
			settings.attributes = Array.isArray(settings.attributes) ? settings.attributes : [];
			settings.cssClass = String(settings.cssClass || '').trim();
			return node;
		}});
})(window.PageBuilderElementorV24Widgets);
