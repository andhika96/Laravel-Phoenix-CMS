/**
 * Page Builder — Widget Registry
 * Global registry accessible via window.widgetRegistry
 * Option 3: Centralised registration, no build tools needed
 */

(function () {
'use strict';

/* ============================================================
   WIDGET REGISTRY
   Central store for all widget definitions, grouped by category.
   Each widget entry describes:
     - type       : unique string identifier used in layout JSON
     - label      : display name shown in the sidebar
     - icon       : Font Awesome icon class
     - category   : 'basic' | 'dynamic'
     - defaults   : plain object — the default data merged into new instances
   ============================================================ */

const registry = {
	_widgets: [],

	register(definition) {
		if (!definition.type) { console.warn('[WidgetRegistry] Missing type:', definition); return; }
		const exists = this._widgets.findIndex(w => w.type === definition.type);
		if (exists >= 0) { this._widgets[exists] = definition; } else { this._widgets.push(definition); }
	},

	getAll()                { return this._widgets; },
	getByType(type)         { return this._widgets.find(w => w.type === type) || null; },
	getByCategory(cat)      { return this._widgets.filter(w => w.category === cat); },
	getDefaults(type)       { const w = this.getByType(type); return w ? JSON.parse(JSON.stringify(w.defaults)) : null; },
};

/* ============================================================
   BASIC WIDGETS
   ============================================================ */

registry.register({
	type: 'text', label: 'Text', icon: 'fas fa-paragraph', category: 'basic',
	defaults: { content: '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>', customClass: '' }
});

registry.register({
	type: 'heading', label: 'Heading', icon: 'fas fa-heading', category: 'basic',
	defaults: { text: 'Add Your Heading Here', link: '', htmlTag: 'h2', alignment: 'left', color: '', fontSize: '', fontSizeUnit: 'px', fontWeight: '', customClass: '' }
});

registry.register({
	type: 'button', label: 'Button', icon: 'fas fa-square', category: 'basic',
	defaults: { text: 'Click Me', href: '#', newTab: false, customClass: '', iconType: 'none', iconClass: 'fas fa-arrow-right', iconSrc: '', iconPos: 'end' }
});

registry.register({
	type: 'image', label: 'Image', icon: 'far fa-image', category: 'basic',
	defaults: { src: 'https://placehold.co/600x400', widthVal: '100', widthUnit: '%', heightVal: '', heightUnit: 'auto', customClass: '' }
});

registry.register({
	type: 'video', label: 'Video', icon: 'fas fa-film', category: 'basic',
	defaults: { videoType: 'youtube', videoSrc: '', youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', autoplay: false, controls: true, loop: false, muted: false, width: '100', aspectRatio: '16/9', customClass: '' }
});

registry.register({
	type: 'card', label: 'Card', icon: 'far fa-id-card', category: 'basic',
	defaults: { cardTitle: 'Card Title', cardText: 'Some quick example text.', src: 'https://placehold.co/600x400', btnText: 'Go somewhere', btnLink: '#', newTab: false, cardStyleClass: '', imgWidthVal: '100', imgWidthUnit: '%', imgHeightVal: '', imgHeightUnit: 'auto', btnClass: 'btn-primary', btnIconType: 'none', btnIconClass: '', btnIconSrc: '', btnIconPos: 'end', titleClass: '', textClass: '', imgClass: '', truncTitleMode: 'off', truncTitleLimit: 30, truncTextMode: 'off', truncTextLimit: 100, truncTextLines: 3, customClass: '' }
});

registry.register({
	type: 'media', label: 'Media Object', icon: 'fas fa-photo-video', category: 'basic',
	defaults: { src: 'https://placehold.co/120x120', imgClass: '', heading: 'Media Heading', headingClass: '', content: 'Media description text.', contentClass: '', imagePos: 'start', align: 'start', imgWidthVal: '64', imgWidthUnit: 'px', imgHeightVal: '', imgHeightUnit: 'auto', mediaBtnText: '', mediaBtnLink: '', mediaBtnClass: 'btn btn-primary btn-sm', mediaBtnPos: 'below', mediaBtnNewTab: false, truncHeadingMode: 'off', truncHeadingLimit: 30, truncContentMode: 'off', truncContentLimit: 100, truncContentLines: 3, customClass: '' }
});

registry.register({
	type: 'list', label: 'List Group', icon: 'fas fa-list', category: 'basic',
	defaults: { listType: 'ul', styleType: 'icon', commonIcon: 'fas fa-check text-success', flush: false, noBorder: false, customClass: '', listSize: 'default', customSizeValue: '16', customSizeUnit: 'px', items: [{ text: 'List item 1', active: true, disabled: false, badge: false, badgeText: 'New', badgeType: 'bg-primary', customBadgeCss: '', inputType: 'none', showMarker: true, markerPosition: 'after', inputChecked: false, customCss: '' }, { text: 'List item 2', active: false, disabled: false, badge: true, badgeText: '14', badgeType: 'bg-danger', customBadgeCss: '', inputType: 'none', showMarker: true, markerPosition: 'after', inputChecked: false, customCss: '' }] }
});

registry.register({
	type: 'accordion', label: 'Accordion', icon: 'fas fa-layer-group', category: 'basic',
	defaults: { items: [{ header: 'Accordion Item #1', content: 'Body content 1' }, { header: 'Accordion Item #2', content: 'Body content 2' }], customClass: '' }
});

registry.register({
	type: 'table', label: 'Table', icon: 'fas fa-table', category: 'basic',
	defaults: { tableData: { headers: ['Head 1', 'Head 2'], rows: [['Data 1', 'Data 2']] }, customClass: '' }
});

registry.register({
	type: 'spacer', label: 'Spacer', icon: 'fas fa-arrows-alt-v', category: 'basic',
	defaults: { height: 50, unit: 'px', customClass: '' }
});

registry.register({
	type: 'divider', label: 'Divider', icon: 'fas fa-minus', category: 'basic',
	defaults: { style: 'solid', width: 100, widthUnit: '%', align: 'center', color: '#dee2e6', thickness: 1, gap: 15, customClass: '' }
});

registry.register({
	type: 'collapse', label: 'Collapse', icon: 'fas fa-layer-group', category: 'basic',
	defaults: { triggerType: 'button', triggerText: 'Click to Open', triggerBtnColor: 'btn-primary', customBtnClass: '', content: '<p>Collapse content here...</p>', isOpen: false, direction: 'down', animation: 'fadeIn', customClass: '', customCardClass: '' }
});

registry.register({
	type: 'nav_tabs', label: 'Nav Tabs', icon: 'fas fa-folder-open', category: 'basic',
	defaults: {
		navStyle: 'tabs', alignment: 'horizontal', fillJustify: 'none', mobileScroll: false, activeTabId: '', customId: '', customClass: '', navClass: '', navItemClass: '',
		items: [
			{ id: 'tab-a1', label: 'Home', icon: 'fas fa-home', itemType: 'button', url: '#', newTab: false, disabled: false, body: '<p>Tab Home content.</p>', dropdownId: '', dropdownMenuClass: '', dropdownItems: [] },
			{ id: 'tab-a2', label: 'Profile', icon: 'fas fa-user', itemType: 'button', url: '#', newTab: false, disabled: false, body: '<p>Tab Profile content.</p>', dropdownId: '', dropdownMenuClass: '', dropdownItems: [] }
		]
	}
});

registry.register({
	type: 'media_list', label: 'Media List', icon: 'fas fa-photo-video', category: 'basic',
	defaults: { viewMode: 'grid', colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6, borderStyle: 'card', roundedCorners: true, textPos: 'below', gap: 3, enableSplide: false, splideAutoplay: true, splideInterval: 3000, splidePerMove: 1, splideType: 'loop', splideDirection: 'ltr', splideArrows: true, splidePagination: true, splideArrowLeftIcon: 'fas fa-chevron-left', splideArrowRightIcon: 'fas fa-chevron-right', imgMode: 'standard', bgHeight: 200, bgSize: 'cover', bgPos: 'center', bgRepeat: 'no-repeat', truncTitleMode: 'off', truncTitleLimit: 30, truncDescMode: 'off', truncDescLimit: 100, truncDescLines: 3, items: [{ type: 'image', src: 'https://placehold.co/600x400', title: 'Image Title 1', desc: 'Description here...', videoType: 'youtube', youtubeUrl: '', videoSrc: '' }, { type: 'image', src: 'https://placehold.co/600x400', title: 'Image Title 2', desc: 'Description here...', videoType: 'youtube', youtubeUrl: '', videoSrc: '' }], customClass: '' }
});

/* ============================================================
   DYNAMIC WIDGETS
   ============================================================ */

registry.register({
	type: 'dynamic_post_list', label: 'Dynamic Post List', icon: 'fas fa-newspaper', category: 'dynamic',
	defaults: { dataType: 'article', selectedCats: [], selectedStatus: 'publish', viewMode: 'grid', gridCols: 3, colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6, imgMode: 'standard', bgHeight: 200, bgSize: 'cover', bgPos: 'center', bgRepeat: 'no-repeat', imgWidthVal: 250, imgWidthUnit: 'px', imgHeightVal: 200, imgHeightUnit: 'px', showBtn: false, btnText: 'Read More', btnClass: 'btn btn-primary', btnPos: 'below', showDate: true, showCategory: true, showExcerpt: true, paginationType: 'number', usePagination: true, perPage: 6, textPos: 'below', gap: 3, roundedCorners: true, borderStyle: 'card', customClass: '', truncTitleMode: 'off', truncTitleLimit: 60, truncDescMode: 'off', truncDescLimit: 120, truncDescLines: 3, minHeight: 0, enableSplide: false, splideAutoplay: true, splideInterval: 3000, splidePerMove: 1, splideType: 'loop', splideDirection: 'ltr', splideArrows: true, splidePagination: true, splideArrowLeftIcon: 'fas fa-chevron-left', splideArrowRightIcon: 'fas fa-chevron-right' }
});

/* ============================================================
   EXPOSE GLOBALLY
   ============================================================ */
window.widgetRegistry = registry;

})();
