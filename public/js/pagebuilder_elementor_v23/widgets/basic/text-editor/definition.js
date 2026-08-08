(function (registry) {
	'use strict';
	const defaults = () => ({
		html: '<p>Edit this text.</p>', cssClass: '', align: 'left', alignTablet: '', alignMobile: '',
		paragraphSpacing: '1em', paragraphSpacingTablet: '', paragraphSpacingMobile: '',
		textEditorTextColor: '#475467', textEditorTextColorHover: '#344054', textEditorLinkColor: '#4f46e5', textEditorLinkColorHover: '#3730a3', textEditorTransitionDuration: 0.3,
		textEditorFontFamily: 'inherit', textEditorFontSize: '16px', textEditorFontSizeTablet: '', textEditorFontSizeMobile: '', textEditorFontWeight: '400', textEditorTextTransform: 'none', textEditorFontStyle: 'normal', textEditorTextDecoration: 'none', textEditorLineHeight: '1.5em', textEditorLineHeightTablet: '', textEditorLineHeightMobile: '', textEditorLetterSpacing: '0px', textEditorLetterSpacingTablet: '', textEditorLetterSpacingMobile: '', textEditorWordSpacing: '0px', textEditorWordSpacingTablet: '', textEditorWordSpacingMobile: '', textEditorTextShadow: 'none',
	});
	registry.register({
		type: 'text_editor', label: 'Text Editor', category: 'basic', icon: 'fas fa-edit', toolbox: true,
		canvas: '/js/pagebuilder_elementor_v23/widgets/basic/text-editor/Canvas.vue',
		settings: '/js/pagebuilder_elementor_v23/widgets/basic/text-editor/Settings.vue',
		defaults,
		normalize(node) {
			node.settings = { ...defaults(), ...(node.settings || {}) };
		node.settings.align = ['left', 'center', 'right', 'justify'].includes(node.settings.align) ? node.settings.align : 'left';
		['alignTablet', 'alignMobile'].forEach((key) => {
			node.settings[key] = node.settings[key] === '' || ['left', 'center', 'right', 'justify'].includes(node.settings[key]) ? node.settings[key] : '';
		});
		node.settings.textEditorTransitionDuration = Number.isFinite(Number(node.settings.textEditorTransitionDuration)) ? Math.max(0, Math.min(10, Number(node.settings.textEditorTransitionDuration))) : 0.3;
			return node;
		},
	});
})(window.PageBuilderElementorV23Widgets);
