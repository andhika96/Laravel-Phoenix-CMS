(function (registry) {
	'use strict';
	const defaults = () => ({ height: '32px', cssClass: '' });
	registry.register({type: 'spacer',defaults,normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
			return node;
		}});
})(window.PageBuilderElementorV24Widgets);
