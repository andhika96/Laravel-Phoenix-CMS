(function (registry) {
	'use strict';
	const defaults = () => ({ html: '<p>Edit this text.</p>', cssClass: '' });
	registry.register({
		type: 'text_editor', label: 'Text Editor', category: 'basic', icon: 'fas fa-edit', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/basic/text-editor/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/basic/text-editor/Settings.vue',
		defaults,
		normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
			return node;
		},
	});
})(window.PageBuilderElementorWidgets);
