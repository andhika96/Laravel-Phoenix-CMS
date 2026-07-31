(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorComplexWidgetRuntime?.image_carousel;
	registry.register({
		type: 'image_carousel', label: 'Image Carousel', category: 'general', icon: 'fas fa-images', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/general/image-carousel/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/general/image-carousel/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorWidgets);
