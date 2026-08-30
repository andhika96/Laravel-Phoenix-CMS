(function (registry) {
	'use strict';
	const defaults = () => ({
		srcdoc: '',
		title: 'Imported page exact preview',
		height: '1200px',
		importMode: 'exact',
	});
	registry.register({ type: 'static_html', defaults, normalize(node) {
		const settings = node.settings = { ...defaults(), ...(node.settings || {}) };
		settings.srcdoc = String(settings.srcdoc || '');
		settings.title = String(settings.title || 'Imported page exact preview').slice(0, 255);
		const height = String(settings.height || '1200px').trim();
		settings.height = /^\d{2,5}px$/.test(height) ? height : '1200px';
		settings.importMode = 'exact';
		return node;
	}});
})(window.PageBuilderElementorV24Widgets);
