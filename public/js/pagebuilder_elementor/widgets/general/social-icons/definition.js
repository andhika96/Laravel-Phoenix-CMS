(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const shared = () => window.PageBuilderElementorComplexWidgetRuntime?.image_box?.defaults?.() || {};
	const item = (id, iconName, iconClass, link) => ({ id, iconSource: 'library', iconStyle: 'brands', iconName, iconClass, iconSvg: '', linkUrl: link, linkTarget: '_blank', linkNofollow: false, linkCustomAttributes: [], colorMode: 'official', primaryColor: '#1877f2', secondaryColor: '#ffffff' });
	const defaults = () => ({
		...shared(), items: [item('social-1', 'facebook-f', 'fab fa-facebook-f', 'https://facebook.com/'), item('social-2', 'twitter', 'fab fa-twitter', 'https://twitter.com/'), item('social-3', 'instagram', 'fab fa-instagram', 'https://instagram.com/')],
		shape: 'rounded', columns: 'auto', alignment: 'left',
		colorMode: 'official', primaryColor: '#405de6', secondaryColor: '#ffffff', colorModeHover: 'custom', primaryColorHover: '#4f46e5', secondaryColorHover: '#ffffff',
		size: '18px', padding: '10px', spacing: '8px', rowsGap: '8px', borderType: 'none', borderWidth: '1px', borderColor: '#d0d7e6', borderRadius: '4px', hoverAnimation: 'none', transitionDuration: 0.3,
	});
	registry.register({
		type: 'social_icons', label: 'Social Icons', category: 'general', icon: 'fas fa-share-alt', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/general/social-icons/Canvas.vue', settings: '/js/pagebuilder_elementor/widgets/general/social-icons/Settings.vue', defaults,
		normalize(node) {
			const previous = node.settings && typeof node.settings === 'object' ? node.settings : {};
			node.settings = { ...defaults(), ...previous };
			node.settings.items = (Array.isArray(previous.items) && previous.items.length ? previous.items : defaults().items).map((entry, index) => ({ ...item(`social-${index + 1}`, 'star', 'fas fa-star', '#'), ...entry, id: String(entry?.id || `social-${index + 1}`) }));
			node.settings.shape = ['rounded','square','circle'].includes(node.settings.shape) ? node.settings.shape : 'rounded';
			node.settings.columns = ['auto','1','2','3','4','5','6'].includes(String(node.settings.columns)) ? String(node.settings.columns) : 'auto';
			node.settings.alignment = ['left','center','right'].includes(node.settings.alignment) ? node.settings.alignment : 'left';
			node.settings.hoverAnimation = ['none','grow','shrink','pulse','pulse-grow','pulse-shrink','rotate'].includes(node.settings.hoverAnimation) ? node.settings.hoverAnimation : 'none';
			node.settings.transitionDuration = Number.isFinite(Number(node.settings.transitionDuration)) ? Math.max(0, Math.min(10, Number(node.settings.transitionDuration))) : 0.3;
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
