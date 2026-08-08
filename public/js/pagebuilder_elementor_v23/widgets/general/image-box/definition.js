(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box;
	registry.register({
		type: 'image_box', label: 'Image Box', category: 'general', icon: 'far fa-image', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/image-box/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/general/image-box/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorV23Widgets);
