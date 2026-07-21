(function (registry) {
	'use strict';
	const defaults = () => ({ src: 'https://placehold.co/640x360', alt: 'Image', width: '100%', height: 'auto', cssClass: '' });
	registry.register({
		type: 'image', label: 'Image', category: 'basic', icon: 'far fa-image', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/basic/image/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/basic/image/Settings.vue',
		defaults,
		normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
