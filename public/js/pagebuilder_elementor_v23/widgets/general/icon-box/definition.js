(function (registry) {
	'use strict';
	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');
	const runtime = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.icon_box;
	registry.register({
		type: 'icon_box', label: 'Icon Box', category: 'general', icon: 'far fa-star', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/general/icon-box/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/general/icon-box/Settings.vue',
		defaults() { return runtime()?.defaults?.() || {}; },
		normalize(node) { return runtime()?.normalize?.(node) || node; },
	});
})(window.PageBuilderElementorV23Widgets);
