(function (registry) {
	'use strict';
	const defaults = () => ({ style: 'solid', width: '100%', thickness: '2px', thicknessUnit: 'px', color: '#d0d7e6', cssClass: '' });
	registry.register({
		type: 'divider', label: 'Divider', category: 'basic', icon: 'fas fa-minus', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/basic/divider/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/basic/divider/Settings.vue',
		defaults,
		normalize(node) {
			const incoming = node.settings || {};
			node.settings = { ...defaults(), ...incoming };
			const units = ['px', 'em', 'rem'];
			const rawThickness = String(node.settings.thickness ?? '').trim();
			const legacyUnit = rawThickness.match(/[a-z%]+$/i)?.[0]?.toLowerCase();
			if (!incoming.thicknessUnit && units.includes(legacyUnit)) node.settings.thicknessUnit = legacyUnit;
			const fallbackUnit = units.includes(node.settings.thicknessUnit) ? node.settings.thicknessUnit : 'px';
			['thickness', 'thicknessTablet', 'thicknessMobile'].forEach((key) => {
				if (node.settings[key] === '' || node.settings[key] === null || node.settings[key] === undefined) return;
				const raw = String(node.settings[key]).trim();
				if (!/[a-z%]+$/i.test(raw) && Number.isFinite(Number.parseFloat(raw))) node.settings[key] = Number.parseFloat(raw) + fallbackUnit;
			});
			return node;
		},
	});
})(window.PageBuilderElementorV23Widgets);
