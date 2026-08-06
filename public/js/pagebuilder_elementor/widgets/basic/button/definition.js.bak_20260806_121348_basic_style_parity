(function (registry) {
	'use strict';
	const defaults = () => ({ text: 'Click here', url: '#', newTab: false, align: 'left', className: 'btn btn-primary' });
	registry.register({
		type: 'button', label: 'Button', category: 'basic', icon: 'fas fa-link', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/basic/button/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/basic/button/Settings.vue',
		defaults,
		normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
			node.settings.newTab = !!node.settings.newTab;
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
