(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.icon_list;
	registry.register({
		type: 'icon_list', label: 'Icon List', category: 'general', icon: 'fas fa-list-ul', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/icon-list/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/general/icon-list/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorV23Widgets);