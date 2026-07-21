(function (registry) {
	'use strict';
	const defaults = () => ({ height: '32px', cssClass: '' });
	registry.register({
		type: 'spacer', label: 'Spacer', category: 'basic', icon: 'fas fa-arrows-alt-v', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/basic/spacer/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/basic/spacer/Settings.vue',
		defaults,
		normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
