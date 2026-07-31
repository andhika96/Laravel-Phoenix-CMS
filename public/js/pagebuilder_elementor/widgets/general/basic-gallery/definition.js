(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorComplexWidgetRuntime?.basic_gallery;
	registry.register({
		type: 'basic_gallery', label: 'Basic Gallery', category: 'general', icon: 'fas fa-th', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/general/basic-gallery/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/general/basic-gallery/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorWidgets);