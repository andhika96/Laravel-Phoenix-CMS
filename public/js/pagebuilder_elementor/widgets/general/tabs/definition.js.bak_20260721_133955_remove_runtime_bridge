(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorComplexWidgetRuntime?.tabs;
	registry.register({
		type: 'tabs', label: 'Tabs', category: 'general', icon: 'far fa-folder', toolbox: true,
		canvas: '/js/pagebuilder_elementor/widgets/general/tabs/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/general/tabs/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		createNode(node) { return runtime()?.createNode?.(node) || node; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorWidgets);
