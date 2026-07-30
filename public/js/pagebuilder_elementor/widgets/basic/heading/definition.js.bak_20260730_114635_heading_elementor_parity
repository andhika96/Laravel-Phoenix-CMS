(function (registry) {
	'use strict';

	if (!registry) {
		throw new Error('Page Builder Elementor widget registry is not loaded.');
	}

	const allowedTags = Object.freeze(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p']);
	const defaults = () => ({
		text: 'Add your heading text',
		tag: 'h2',
		align: 'left',
		color: '#101828',
		cssClass: '',
	});

	registry.register({
		type: 'heading',
		label: 'Heading',
		category: 'basic',
		icon: 'fas fa-heading',
		canvas: '/js/pagebuilder_elementor/widgets/basic/heading/Canvas.vue',
		settings: '/js/pagebuilder_elementor/widgets/basic/heading/Settings.vue',
		toolbox: true,
		defaults,
		normalize(node) {
			const normalized = node && typeof node === 'object' ? node : {};
			normalized.settings = { ...defaults(), ...(normalized.settings || {}) };
			normalized.settings.tag = allowedTags.includes(normalized.settings.tag) ? normalized.settings.tag : 'h2';
			return normalized;
		},
	});
})(window.PageBuilderElementorWidgets);