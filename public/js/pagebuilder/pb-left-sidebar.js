/**
 * pb-left-sidebar.js
 * Logic Vue untuk Left Sidebar — Widget Library & Page Settings
 * Diload sebelum main app.js, expose via window.PBLeftSidebar
 * 
 * Berisi: tools (widget definitions), showLeftSidebar, cloneItem,
 *         showWidgetModal, openWidgetPicker, addWidgetFromPicker
 */
window.PBLeftSidebar = (function () {
	const { ref } = Vue;

	function setup() {

		// ============================================================
		// WIDGET & LAYOUT DEFINITIONS (Static Data)
		// ============================================================
		const tools = 
{
	containers: 
	[
		{ type: 'container', classDesktop: 'container', classTablet: 'container', classMobile: 'container-fluid', classFHD: 'container', class4K: 'container', label: 'Fixed Width', styles: { bgColor: '#ffffff', minHeight: 'auto', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover' } }, 
		{ type: 'container', classDesktop: 'container-fluid', classTablet: 'container-fluid', classMobile: 'container-fluid', classFHD: 'container-fluid', class4K: 'container-fluid', label: 'Full Width', styles: { bgColor: '#ffffff', minHeight: 'auto' } }
	],
	rows: 
	[
		{ type: 'row', label: '1 Column', cols: ['col-xl-12'] }, 
		{ type: 'row', label: '2 Columns', cols: ['col-xl-6', 'col-xl-6'] }, 
		{ type: 'row', label: '3 Columns', cols: ['col-xl-4', 'col-xl-4', 'col-xl-4'] },
		{ type: 'row', label: '4 Columns', cols: ['col-xl-3', 'col-xl-3', 'col-xl-3', 'col-xl-3'] },
		{ type: 'row', label: '5 Columns', cols: ['col-xl', 'col-xl', 'col-xl', 'col-xl', 'col-xl'] },
		{ type: 'row', label: '6 Columns', cols: ['col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2'] }
	],
	widgets: 
	[			
		{ type: 'text', label: 'Text', icon: 'fas fa-paragraph', content: '<p>Lorem ipsum dolor sit amet...</p>' },
		{ type: 'button', label: 'Button', icon: 'fas fa-square', text: 'Click Me', href: '#', newTab: false, customClass: '', iconType: 'none', iconClass: 'fas fa-arrow-right', iconSrc: '', iconPos: 'start' },
		{ type: 'image', label: 'Image', icon: 'far fa-image', src: 'https://placehold.co/200x200', widthVal: '100', widthUnit: '%', heightVal: '', heightUnit: 'auto' },
		{ type: 'video', label: 'Video', icon: 'fas fa-film', videoType: 'youtube', videoSrc: '', youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', autoplay: false, controls: true, loop: false, muted: false, width: '100', aspectRatio: '16/9', customClass: '' },
		{ type: 'card', label: 'Card', icon: 'far fa-id-card', cardTitle: 'Card Title', titleClass: '', truncTitleMode: 'off', truncTitleLimit: 30, cardText: 'Some quick example text to build on the card title and make up the bulk of the card\'s content.', textClass: '', truncTextMode: 'off', truncTextLimit: 100, truncTextLines: 3, src: 'https://placehold.co/200x200', imgClass: '', btnText: 'Go somewhere', btnLink: '#', newTab: false, cardStyleClass: '', imgWidthVal: '100', imgWidthUnit: '%', imgHeightVal: '', imgHeightUnit: 'auto', btnClass: '', btnIconType: 'none', btnIconClass: 'fas fa-arrow-right', btnIconSrc: '', btnIconPos: 'start' },
		{ type: 'media', label: 'Media Object', icon: 'fas fa-photo-video', src: 'https://placehold.co/120x120', imgClass: '', heading: 'Media Heading', headingClass: '', truncHeadingMode: 'off', truncHeadingLimit: 30, content: 'Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.', contentClass: '', truncContentMode: 'off', truncContentLimit: 100, truncContentLines: 3, imagePos: 'start', align: 'start', imgWidthVal: '64', imgWidthUnit: 'px', imgHeightVal: '', imgHeightUnit: 'auto', mediaBtnText: '', mediaBtnLink: '', mediaBtnClass: 'btn btn-primary btn-sm', mediaBtnPos: 'below', mediaBtnNewTab: false },		
		{
			type: 'list',
			label: 'List Group',
			icon: 'fas fa-list',
			listType: 'ul',
			styleType: 'icon',
			commonIcon: 'fas fa-check text-success',
			commonImage: '',
			flush: false,
			noBorder: false, // <-- Opsi Baru: Full No Border
			numbered: false,
			customClass: '',
			listSize: 'default', // <-- Opsi Baru: default, h6, h5, h4, h3, h2, h1, custom
		    customSizeValue: '16', // <-- Opsi Baru: Nilai ukuran custom
		    customSizeUnit: 'px', // <-- Opsi Baru: Satuan ukuran custom
			items: [
				{ 
					text: 'List item 1', 
					active: true, 
					disabled: false,
					badge: false, 
					badgeText: 'New', 
					badgeType: 'bg-primary',
					customBadgeCss: '', // <-- Opsi Baru: Input Custom Badge
					inputType: 'none',
					showMarker: true, // <-- Opsi Baru: Tampilkan list marker saat input
					markerPosition: 'after', // <-- Opsi Baru: 'before' atau 'after'
					inputChecked: false,
					customCss: ''
				},
				{ 
					text: 'List item 2', 
					active: false, 
					disabled: false,
					badge: true, 
					badgeText: '14', 
					badgeType: 'bg-danger',
					customBadgeCss: '', // <-- Opsi Baru: Input Custom Badge
					inputType: 'none',
					showMarker: true, // <-- Opsi Baru: Tampilkan list marker saat input
					markerPosition: 'after', // <-- Opsi Baru: 'before' atau 'after'
					inputChecked: false,
					customCss: '' 
				}
			]
		},
		{ type: 'accordion', label: 'Accordion', icon: 'fas fa-list', items: [{ header: 'Accordion Item #1', content: 'Body content 1' }, { header: 'Accordion Item #2', content: 'Body content 2' }] },
		{ type: 'table', label: 'Table', icon: 'fas fa-table', tableData: { headers: ['Head 1', 'Head 2'], rows: [['Data 1', 'Data 2']] } },
		{ type: 'spacer', label: 'Spacer', icon: 'fas fa-arrows-alt-v', height: 50, unit: 'px', customClass: '' },
		{ type: 'divider', label: 'Divider', icon: 'fas fa-minus', style: 'solid', width: 100, widthUnit: '%', align: 'center', color: '#000000', thickness: 1, gap: 15, customClass: '' },
		{ type: 'heading', label: 'Heading', icon: 'fas fa-heading', text: 'Add Your Heading Text Here', link: '', htmlTag: 'h2', alignment: 'left', color: '', fontSize: '', fontSizeUnit: 'px', fontWeight: '', customClass: '' },
		{ type: 'media_list', label: 'Media List', icon: 'fas fa-photo-video', viewMode: 'grid', colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6, borderStyle: 'card', roundedCorners: true, textPos: 'below', textWrapperClass: '', titleClass: '', descClass: '', hoverAnim: false, minHeight: 0, truncTitleMode: 'off', truncTitleLimit: 30, truncDescMode: 'off', truncDescLimit: 100, truncDescLines: 3, overlayColor: 'rgba(0, 0, 0, 0.6)', textColor: '#000000', overlayTextColor: '#ffffff', gap: 3, enableSplide: false, splideAutoplay: true, splideInterval: 3000, splideAutoHeight: true, splidePerMove: 1, splideType: 'loop', splideDirection: 'ltr', splideArrows: true, splidePagination: true, splideArrowType: 'default', splideArrowLeftIcon: 'fas fa-chevron-left', splideArrowRightIcon: 'fas fa-chevron-right', splideArrowPosY: 50, splideArrowPosX: 0, items: [ { type: 'image', imgMode: 'default', imgClass: '', bgHeight: 200, bgSize: 'cover', bgRepeat: 'no-repeat', bgPos: 'center', src: 'https://placehold.co/600x400', aspectRatio: '16/9', title: 'Image Title 1', desc: 'Description here...', videoType: 'youtube', youtubeUrl: '', videoSrc: '', videoClass: '' }, { type: 'video', aspectRatio: '16/9', videoType: 'youtube', youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', src: '', title: 'Video Title 2', desc: 'Video description...', videoSrc: '', videoClass: '' } ], customClass: '' },
		{
			label: 'Collapse', 
			icon: 'fas fa-layer-group',
			category: 'basic',
			type: 'collapse', 
			collapseId: 'collapse-' + Math.random().toString(36).substr(2, 9),
			triggerType: 'button',
			triggerText: 'Klik untuk Buka',
			triggerBtnColor: 'btn-primary',
			customBtnClass: '', 
			content: '<p>Tulis konten collapse Anda di sini...</p>',
			isOpen: false,
			direction: 'down',
			animation: 'fadeIn',
			customClass: '',
			customCardClass: '' 
		},
		{
			type: 'nav_tabs',
			label: 'Nav Tabs',
			icon: 'fas fa-folder-open',
			navStyle: 'tabs',
			alignment: 'horizontal',
			fillJustify: 'none',
			mobileScroll: false,
			activeTabId: '',
			customId: '',
			customClass: '',
			navClass: '',
			navItemClass: '',
			items: [
				{
					id: 'tab-' + Math.random().toString(36).substr(2, 9),
					customId: '',
					customClass: '',
					navItemClass: '',
					paneClass: '',
					label: 'Home',
					icon: 'fas fa-home',
					itemType: 'button', // Opsi: button (buka konten), link (buka url), dropdown
					url: '#',
					newTab: false,
					disabled: false,
					body: '<p>Ini adalah konten untuk tab Home. Anda bisa mengeditnya di panel setting.</p>',
					dropdownId: '',
					dropdownMenuClass: '',
					dropdownItems: []
				},
				{
					id: 'tab-' + Math.random().toString(36).substr(2, 9),
					customId: '',
					customClass: '',
					navItemClass: '',
					paneClass: '',
					label: 'Profile',
					icon: 'fas fa-user',
					itemType: 'button',
					url: '#',
					newTab: false,
					disabled: false,
					body: '<p>Ini adalah konten untuk tab Profile.</p>',
					dropdownId: '',
					dropdownMenuClass: '',
					dropdownItems: []
				},
				{
					id: 'tab-' + Math.random().toString(36).substr(2, 9),
					customId: '',
					customClass: '',
					navItemClass: '',
					paneClass: '',
					label: 'Options',
					icon: 'fas fa-cog',
					itemType: 'dropdown',
					url: '#',
					newTab: false,
					disabled: false,
					body: '',
					dropdownId: '',
					dropdownMenuClass: '',
					dropdownItems: [
						{ label: 'Action 1', url: '#', newTab: false, disabled: false, itemClass: '' },
						{ label: 'Action 2', url: '#', newTab: false, disabled: false, itemClass: '' }
					]
				}
			]
		},
	],
	advanced: 
	[
		{ 
			type: 'dynamic_post_list', 
			label: 'Dynamic Post List', 
			icon: 'fas fa-newspaper',
			
			// Data Config
			dataType: 'article',
			selectedCats: [],
			selectedStatus: 'publish',
			
			// View Config
			viewMode: 'grid',
			gridCols: 3, 
			colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6,
			
			// 1. IMAGE SETTINGS
			imgMode: 'standard', // 'standard' or 'background'
			bgHeight: 200, // Default height for bg mode
			bgSize: 'cover', // cover, contain, custom
			bgSizeVal: 100,
			bgSizeUnit: '%',
			bgPos: 'center',
			bgRepeat: 'no-repeat',

			imgWidthVal: 250, // Default request 250px
			imgWidthUnit: 'px',
			imgHeightVal: 200,
			imgHeightUnit: 'px',

			// 2. BUTTON SETTINGS
			showBtn: false, // Default false (pakai stretched link)
			btnText: 'Read More',
			btnWrapperClass: '',
			btnClass: 'btn btn-primary',
			btnPos: 'below', // 'below' or 'side'
			btnVerticalPos: 'auto',
			btnIconType: 'none',
			btnIconClass: 'fas fa-arrow-right',
			btnIconSrc: '',
			btnIconPos: 'end', // 'start' or 'end'

			// --- TAMBAHAN WARNA (DEFAULT HITAM) ---
			textColor: '#000000', // Default Text Color
			overlayColor: 'rgba(0, 0, 0, 0.4)',
			overlayTextColor: '#ffffff',
			// -------------------------------------

			// Toggles
			showDate: true,
			showCategory: true,
			showExcerpt: true,
			
			// Pagination
			paginationType: 'number',
			usePagination: true,
			perPage: 6,

			// NEW: Pagination Style Settings
			paginationClass: 'mt-4', // Class container

			// Settings for Number Pagination
			pageItemClass: '', // Class for li/a
			pageIconType: 'class', // class/image
			pagePrevIcon: 'fas fa-angle-left',
			pagePrevImg: '',
			pageNextIcon: 'fas fa-angle-right',
			pageNextImg: '',

			// Settings for Simple/Cursor Pagination
			navBtnClass: 'btn-outline-primary',
			navAlign: 'center', // center, between
			navShowIcon: true,
			navIconType: 'class',
			navPrevIcon: 'fas fa-arrow-left',
			navPrevImg: '',
			navNextIcon: 'fas fa-arrow-right',
			navNextImg: '',
			
			// Slider Config
			enableSplide: false,
			splideAutoplay: true,
			splideInterval: 3000,
			splidePerMove: 1,
			splideType: 'loop',
			splideDirection: 'ltr',
			splideArrows: true,
			splidePagination: true,
			splideArrowType: 'default',
			splideArrowLeftIcon: 'fas fa-chevron-left',
			splideArrowRightIcon: 'fas fa-chevron-right',
			splideArrowPosY: 50,
			splideArrowPosX: 0,
			
			// Styles
			textPos: 'below', // Default Text Position
			imagePos: 'start',
			textAlign: 'start',
			verticalAlign: 'center',
			gap: 3,
			roundedCorners: true,
			borderStyle: 'card',
			customClass: '',
			listContentWrapperClass: '',
			
			// Truncate Defaults
			truncTitleMode: 'off', truncTitleLimit: 30,
			truncDescMode: 'off', truncDescLimit: 100, truncDescLines: 3,
			
			// Style Options
			minHeight: 0
		}
	]
};;

		// ============================================================
		// LEFT SIDEBAR STATE
		// ============================================================
		const storedSidebar = localStorage.getItem('pb_show_left_sidebar');
		const showLeftSidebar = ref(storedSidebar === 'false' ? false : true);

		const toggleLeftSidebar = () => {
			showLeftSidebar.value = !showLeftSidebar.value;
			localStorage.setItem('pb_show_left_sidebar', showLeftSidebar.value);
		};

		// ============================================================
		// ID UTILITY — dipakai juga oleh main app (onDropTo*)
		// ============================================================
		const regenerateIds = (obj) => {
			obj.id = Math.random().toString(36).substr(2, 9);
			if (obj.children) obj.children.forEach(regenerateIds);
		};

		// ============================================================
		// CLONE ITEM — untuk VueDraggable :clone pada sidebar
		// ============================================================
		const cloneItem = (origin) => {
			const item = JSON.parse(JSON.stringify(origin));
			regenerateIds(item);

			if (item.type === 'container') {
				item.children = [];
				if (!item.classMobile)  item.classMobile  = 'container-fluid';
				if (!item.classTablet)  item.classTablet  = 'container-fluid';
				if (!item.classDesktop) item.classDesktop = 'container';
				if (!item.classFHD)     item.classFHD     = 'container';
				if (!item.class4K)      item.class4K      = 'container';
			}

			if (item.type === 'row') {
				item.children = item.cols.map(w => {
					let colNum = '';
					if (w.includes('-xl-'))      colNum = w.split('-xl-')[1];
					else if (w.includes('-lg-')) colNum = w.split('-lg-')[1];
					else if (w.includes('-md-')) colNum = w.split('-md-')[1];
					else if (w.includes('col-') && w !== 'col') colNum = w.split('col-')[1];

					return {
						id: Math.random().toString(36).substr(2, 9),
						type: 'col',
						widthMobile:  'col-12',
						widthTablet:  colNum ? 'col-md-'  + colNum : 'col-md',
						widthDesktop: w,
						widthFHD:     colNum ? 'col-xxl-' + colNum : 'col-xxl',
						width4K:      colNum ? 'col-3xl-' + colNum : 'col-3xl',
						children: [],
						customClass: ''
					};
				});

				item.gutter = ''; item.gutterTablet = ''; item.gutterDesktop = '';
				item.gutterFHD = ''; item.gutter4K = '';
				item.widthMobile = 'w-100'; item.widthTablet = 'w-md-100';
				item.widthDesktop = 'w-xl-100'; item.widthFHD = 'w-xxl-100'; item.width4K = 'w-3xl-100';
			}

			if (item.type === 'image' && !item.widthUnit) {
				item.widthVal = '100'; item.widthUnit = '%';
				item.heightVal = ''; item.heightUnit = 'auto';
			}

			return item;
		};

		// ============================================================
		// WIDGET PICKER MODAL
		// ============================================================
		const showWidgetModal  = ref(false);
		const targetWidgetList = ref(null);

		const openWidgetPicker = (targetArray) => {
			targetWidgetList.value = targetArray;
			showWidgetModal.value  = true;
		};

		const addWidgetFromPicker = (type) => {
			if (!targetWidgetList.value) return;

			const allWidgets   = [...tools.widgets, ...tools.advanced];
			const defaultWidget = allWidgets.find(w => w.type === type);

			if (defaultWidget) {
				const newWidget  = JSON.parse(JSON.stringify(defaultWidget));
				newWidget.id     = Date.now();
				targetWidgetList.value.push(newWidget);
			}

			showWidgetModal.value  = false;
			targetWidgetList.value = null;
		};

		// ============================================================
		// RETURN — semua yang dibutuhkan template & main app
		// ============================================================
		return {
			tools,
			showLeftSidebar,
			toggleLeftSidebar,
			regenerateIds,
			cloneItem,
			showWidgetModal,
			targetWidgetList,
			openWidgetPicker,
			addWidgetFromPicker,
		};
	}

	return { setup };
})();
