(function (registry) {
	'use strict';
	const defaults = () => ({ style: 'solid', width: '100%', thickness: 2, color: '#d0d7e6', cssClass: '' });
	registry.register({
		type: 'divider', label: 'Divider', category: 'basic', icon: 'fas fa-minus', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/basic/divider/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/basic/divider/Settings.vue',
		defaults,
		normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
