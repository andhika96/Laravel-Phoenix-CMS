(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorComplexWidgetRuntime?.accordion;
	registry.register({
		type: 'accordion', label: 'Accordion', category: 'general', icon: 'fas fa-bars', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/advanced/accordion/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/advanced/accordion/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		createNode(node) { return runtime()?.createNode?.(node) || node; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorWidgets);
