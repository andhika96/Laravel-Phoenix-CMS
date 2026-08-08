(function (global) {
	'use strict';

	const definitions = new Map();
	const requiredFields = ['type', 'label', 'category', 'icon', 'canvas', 'settings', 'defaults', 'normalize'];

	function clone(value) {
		return value == null ? value : JSON.parse(JSON.stringify(value));
	}

	function assertDefinition(definition) {
		if (!definition || typeof definition !== 'object') {
			throw new TypeError('Page Builder Elementor widget definition must be an object.');
		}

		for (const field of requiredFields) {
			if (definition[field] == null || definition[field] === '') {
				throw new TypeError('Page Builder Elementor widget definition is missing "' + field + '".');
			}
		}

		if (typeof definition.defaults !== 'function' || typeof definition.normalize !== 'function') {
			throw new TypeError('Widget defaults and normalize fields must be functions.');
		}
	}

	function register(definition) {
		assertDefinition(definition);
		const type = String(definition.type).trim();

		if (definitions.has(type)) {
			throw new Error('Duplicate Page Builder Elementor widget type: ' + type);
		}

		const registered = Object.freeze({
			...definition,
			type,
			toolbox: definition.toolbox !== false,
			defaults() {
				return clone(definition.defaults()) || {};
			},
		});

		definitions.set(type, registered);
		return registered;
	}

	function get(type) {
		return definitions.get(String(type || '').trim()) || null;
	}

	function all() {
		return Array.from(definitions.values());
	}

	function toolbox() {
		return all()
			.filter((definition) => definition.toolbox)
			.reduce((groups, definition) => {
				const category = String(definition.category || 'basic');
				(groups[category] ||= []).push({
					type: definition.type,
					label: definition.label,
					icon: definition.icon,
				});
				return groups;
			}, {});
	}

	global.PageBuilderElementorV23Widgets = Object.freeze({ register, get, all, toolbox });
})(window);
