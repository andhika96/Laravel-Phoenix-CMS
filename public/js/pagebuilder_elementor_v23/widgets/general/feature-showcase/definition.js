(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.feature_showcase;
	registry.register({
		type: 'feature_showcase', label: 'Feature Showcase', category: 'general', icon: 'fas fa-layer-group', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/feature-showcase/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/general/feature-showcase/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorV23Widgets);
