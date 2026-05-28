	createApp({
		components: { draggable, 'ckeditor-component': CkEditorComponent, 'preview-viewer': PreviewViewer },
		setup() {
			const previewMode = ref(false);
			const previewType = ref('fhd'); 
			const showRightSidebar = ref(false);
			const userId = ref(0);
			const pageId = ref(0);
			const pageUri = ref('');
			const pageName = ref('');
			const pageStatus = ref('draft');
			const layout = ref([]);
			const customCss = ref('');
			const showCssEditor = ref(false);
			const cssEditorFullscreen = ref(document.cookie.split('; ').find(r => r.startsWith('pb_css_fullscreen='))?.split('=')[1] === 'true');
			const baseUrl = APP_BASE_URL;
			const theme = ref('light');
			const activeItem = ref(null);
			const activeType = ref('');
			const activeViewMode = ref('fhd');

			// Toast notification
			const toastEl = ref(null);
			const toastMessage = ref('');
			const toastStatus = ref('bg-success');

			const showToast = (message, isSuccess = true) => {
				toastMessage.value = message;
				toastStatus.value = isSuccess ? 'bg-success' : 'bg-danger';
				nextTick(() => {
					const el = toastEl.value;
					if (el && window.bootstrap) {
						const toast = bootstrap.Toast.getOrCreateInstance(el);
						toast.show();
					}
				});
			};
			const history = ref([]);
			const historyIndex = ref(-1);
			const isTimeTraveling = ref(false); 

			const toggleTheme = () => { theme.value = theme.value === 'light' ? 'dark' : 'light'; document.documentElement.setAttribute('data-bs-theme', theme.value); };
			const toggleRightSidebar = () => { showRightSidebar.value = !showRightSidebar.value; };

			const tools = 
			{
				containers:
				[
					{ type: 'container', classDesktop: 'container', classTablet: 'container', classMobile: 'container-fluid', classFHD: 'container', class4K: 'container', label: 'Fixed Width', bgTarget: 'section', sectionStyles: { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' }, styles: { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' } },
					{ type: 'container', classDesktop: 'container-fluid', classTablet: 'container-fluid', classMobile: 'container-fluid', classFHD: 'container-fluid', class4K: 'container-fluid', label: 'Full Width', bgTarget: 'section', sectionStyles: { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' }, styles: { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' } }
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
			};

			const setActive = (item, type) => { activeItem.value = item; activeType.value = type; showRightSidebar.value = true; }; 
			const deselectAll = () => { activeItem.value = null; activeType.value = ''; showRightSidebar.value = false; };

			// Simpan state fullscreen CSS editor ke cookie
			watch(cssEditorFullscreen, (val) => 
			{
				// Simpan cookie selama 30 hari
				const expires = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toUTCString();
				document.cookie = `pb_css_fullscreen=${val}; expires=${expires}; path=/`;
			});

			const saveState = () => { if (isTimeTraveling.value) return; if (historyIndex.value < history.value.length - 1) history.value = history.value.slice(0, historyIndex.value + 1); history.value.push(JSON.stringify(layout.value)); historyIndex.value++; };
			let debounceTimer;
			watch(layout, () => { if (!isTimeTraveling.value) { clearTimeout(debounceTimer); debounceTimer = setTimeout(saveState, 500); } }, { deep: true });
			
			const undo = () => { if (historyIndex.value > 0) { isTimeTraveling.value = true; historyIndex.value--; layout.value = JSON.parse(history.value[historyIndex.value]); nextTick(() => isTimeTraveling.value = false); } };
			const redo = () => { if (historyIndex.value < history.value.length - 1) { isTimeTraveling.value = true; historyIndex.value++; layout.value = JSON.parse(history.value[historyIndex.value]); nextTick(() => isTimeTraveling.value = false); } };

			const regenerateIds = (obj) => { obj.id = Math.random().toString(36).substr(2, 9); if(obj.children) obj.children.forEach(regenerateIds); };

			// --- GANTI FUNCTION CLONEITEM LAMA DENGAN INI ---
			const cloneItem = (origin) => 
			{
				const item = JSON.parse(JSON.stringify(origin));
				regenerateIds(item);
				
				// Logic Container
				if (item.type === 'container')
				{
					item.children = [];
					if ( ! item.classMobile) item.classMobile = 'container-fluid';
					if ( ! item.classTablet) item.classTablet = 'container-fluid';
					if ( ! item.classDesktop) item.classDesktop = 'container';
					if ( ! item.classFHD) item.classFHD = 'container';
					if ( ! item.class4K) item.class4K = 'container';
					if ( ! item.bgTarget) item.bgTarget = 'section';
					if ( ! item.sectionStyles) item.sectionStyles = { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' };
					if ( ! item.styles) item.styles = { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' };
				}

				// Logic Row (INI BAGIAN YANG DI-UPDATE)
				if (item.type === 'row') 
				{ 
					// AUTO RESPONSIVE LOGIC: Membaca angka kolom desktop (misal col-xl-6)
					// dan menerapkannya ke breakpoint lain.
					item.children = item.cols.map(w => 
					{
						let colNum = '';
						
						// Ekstrak angka dari class (misal ambil '6' dari 'col-xl-6')
						if (w.includes('-xl-')) 
						{
							colNum = w.split('-xl-')[1];
						} 
						else if (w.includes('-lg-')) 
						{
							colNum = w.split('-lg-')[1];
						} 
						else if (w.includes('-md-')) 
						{
							colNum = w.split('-md-')[1];
						} 
						else if (w.includes('col-') && w !== 'col') 
						{
							colNum = w.split('col-')[1];
						}

						return { 
							id: Math.random().toString(36).substr(2, 9), 
							type: 'col', 
							// Mobile default 12 (full) agar tidak gepeng
							widthMobile: 'col-12', 
							// Tablet mengikuti desktop tapi pakai prefix md
							widthTablet: colNum ? 'col-md-' + colNum : 'col-md', 
							// Desktop sesuai sidebar
							widthDesktop: w, 
							// FHD mengikuti desktop tapi pakai prefix xxl
							widthFHD: colNum ? 'col-xxl-' + colNum : 'col-xxl', 
							// 4K mengikuti desktop tapi pakai prefix 3xl
							width4K: colNum ? 'col-3xl-' + colNum : 'col-3xl', 
							children: [], 
							customClass: '' 
						};
					}); 
					
					// Reset gutter dan width row container
					item.gutter = ''; item.gutterTablet = ''; item.gutterDesktop = ''; item.gutterFHD = ''; item.gutter4K = ''; 
					item.widthMobile = 'w-100'; item.widthTablet = 'w-md-100'; item.widthDesktop = 'w-xl-100'; item.widthFHD = 'w-xxl-100'; item.width4K = 'w-3xl-100';
				}

				if (item.type === 'image' && !item.widthUnit) { item.widthVal = '100'; item.widthUnit = '%'; item.heightVal = ''; item.heightUnit = 'auto'; }
				return item;
			};

			const duplicateItem = (arr, index) => { const clone = JSON.parse(JSON.stringify(arr[index])); regenerateIds(clone); arr.splice(index + 1, 0, clone); };
			const removeItem = (arr, i) => { if (activeItem.value && arr[i] && arr[i].id === activeItem.value.id) { activeItem.value = null; activeType.value = ''; } arr.splice(i, 1); };
			const removeColumn = (row, i) => { if (activeItem.value && row.children[i].id === activeItem.value.id) { activeItem.value = null; activeType.value = ''; } row.children.splice(i, 1); };
			const addColumn = (row) => row.children.push({ id: Math.random().toString(36).substr(2, 9), type:'col', widthMobile:'col-12', widthTablet:'col-md', widthDesktop:'col-xl', widthFHD:'col-xxl', width4K:'col-3xl', children:[], customClass:'' });

			// --- GANTI FUNCTION onDropToContainer DENGAN VERSI FINAL INI ---            
			const onDropToContainer = (evt, targetCont) => {
				const addedIndex = evt.newIndex;
				const rawItem = targetCont.children[addedIndex];

				if ( ! rawItem) return; 

				// SKENARIO 1: WIDGET (Text, Image, Button, dll) -> Bungkus Row & Col
				if (rawItem.type !== 'row') {
					
					// 1. DEEP COPY (PENTING! Agar data widget tidak hilang/corrupt)
					const savedWidget = JSON.parse(JSON.stringify(rawItem));
					savedWidget.id = Math.random().toString(36).substr(2, 9);

					// 2. Buat Struktur Wrapper Lengkap
					const newRow = {
						id: Math.random().toString(36).substr(2, 9),
						type: 'row',
						label: 'Wrapper Row',
						widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100',
						gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '',
						customClass: '',
						children: [
							{
								id: Math.random().toString(36).substr(2, 9),
								type: 'col',
								widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12',
								customClass: '',
								children: [ savedWidget ] // Masukkan widget clone ke sini
							}
						] 
					};

					// 3. Replace item mentah dengan Wrapper
					targetCont.children.splice(addedIndex, 1, newRow);
				} 
				
				// SKENARIO 2: NESTED ROW -> Normalisasi Row
				else {
					rawItem.widthMobile = 'w-100';
					rawItem.widthTablet = 'w-md-100';
					rawItem.widthDesktop = 'w-xl-100';
					rawItem.widthFHD = 'w-xxl-100';
					rawItem.width4K = 'w-3xl-100';
					rawItem.gutter = ''; rawItem.gutterTablet = ''; rawItem.gutterDesktop = ''; 
					rawItem.gutterFHD = ''; rawItem.gutter4K = '';
					regenerateIds(rawItem);
				}
			};

			// 2. UPDATE FUNCTION: Handle Drop ke ROW (Bisa dari Sidebar / dari Nested Row)
			const onDropToRow = (evt, targetRow) => { 
				const addedIndex = evt.newIndex; 
				const addedItem = targetRow.children[addedIndex]; 

				// KASUS A: Yang didrop adalah Nested Row (Un-nesting)
				// Logika: Row dibuang, Column-nya diambil jadi anak Row ini
				if (addedItem.type === 'row') { 
					targetRow.children.splice(addedIndex, 1); // Hapus Row pembungkus
					const newCols = addedItem.children; // Ambil isinya (cols)
					newCols.forEach(col => regenerateIds(col)); // ID Baru
					targetRow.children.splice(addedIndex, 0, ...newCols); // Masukkan cols
				} 
				
				// KASUS B: Yang didrop adalah WIDGET (Text, Image, Button, dll)
				// Logika: Widget tidak boleh telanjang di Row. Bungkus dengan Column.
				else if (addedItem.type !== 'col') {
					
					// Buat Column Pembungkus Baru
					const wrapperCol = {
						id: Math.random().toString(36).substr(2, 9),
						type: 'col',
						// Set width default agar widget terlihat (misal col-12 atau col-md)
						widthMobile: 'col-12', 
						widthTablet: 'col-md', 
						widthDesktop: 'col-xl', 
						widthFHD: 'col-xxl', 
						width4K: 'col-3xl',
						children: [ addedItem ], // Masukkan Widget ke dalam Column ini
						customClass: ''
					};

					// Ganti item widget mentah dengan Column Wrapper
					targetRow.children.splice(addedIndex, 1, wrapperCol);
				}
				
				// KASUS C: Yang didrop adalah Column (Move dari row lain) -> Biarkan saja
			};

			// --- UPDATE FUNCTION INI DI BAGIAN JAVASCRIPT SETUP() ---
			const onDropToColumn = (evt, targetCol) => 
			{
				const addedIndex = evt.newIndex;
				const addedItem = targetCol.children[addedIndex];

				// Jika item yang masuk adalah ROW (dari sidebar atau pindahan row lain)
				// Kita harus ubah dia menjadi NESTED ROW (Full Width)
				if (addedItem && addedItem.type === 'row')
				{
					// Force width menjadi 100% agar pas di dalam column
					addedItem.widthMobile = 'w-100';
					addedItem.widthTablet = 'w-md-100';
					addedItem.widthDesktop = 'w-xl-100';
					addedItem.widthFHD = 'w-xxl-100';
					addedItem.width4K = 'w-3xl-100';
					
					// Reset Gutter
					addedItem.gutter = ''; 
					addedItem.gutterTablet = ''; 
					addedItem.gutterDesktop = '';
					addedItem.gutterFHD = ''; 
					addedItem.gutter4K = '';

					// Jika ini row baru (belum punya ID), generate ID
					if ( ! addedItem.id) regenerateIds(addedItem);
				}
				
				// Jika yang masuk adalah WIDGET biasa, biarkan saja. 
				// VueDraggable akan menanganinya secara otomatis.
			};
			
			const togglePreview = () => { previewMode.value = !previewMode.value; if(previewMode.value) { activeItem.value = null; activeType.value = ''; previewType.value = 'fhd'; } };
			const openCkFinder = (targetObj, propName) => { if (typeof CKFinder !== 'undefined') CKFinder.popup({ chooseFiles: true, onInit: (finder) => finder.on('files:choose', (evt) => targetObj[propName] = evt.data.files.first().getUrl()) }); else alert("CKFinder not configured"); };

			const saveJson = () => 
			{
				const csrfToken = document.querySelector('meta[name="csrf-token"]');
				if (csrfToken) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;

				const payload = 
				{
					idOrSlug	: 	pageId.value,
					pageId		: 	pageId.value,
					pageUri		: 	pageUri.value,
					pageName 	: 	pageName.value,
					pageStatus 	: 	pageStatus.value,
					layout 		:	JSON.stringify(layout.value),
					customCss 	: 	customCss.value
				};

				const config = 
				{
					headers: 
					{
						'Content-Type': 'multipart/form-data',
						'X-Requested-With': 'XMLHttpRequest'
					}
				};

				if (PAGE_MODE === 'update' && PAGE_URI)
				{
					// Mode UPDATE — kirim ke endpoint update dengan ID
					axios.post('/pagebuilder/update/'+PAGE_URI, payload, config)
					.then(response =>
					{
						showToast('Page saved successfully!', true);
						console.log(response);
					})
					.catch(error =>
					{
						showToast('Error saving page. Please try again.', false);
						console.log(error);
					});
				}
				else
				{
					// Mode CREATE — kirim ke endpoint store
					axios.post('/pagebuilder/store', payload)
					.then(response =>
					{
						showToast('Page created successfully!', true);
						console.log(response);
					})
					.catch(error =>
					{
						showToast('Error creating page. Please try again.', false);
						console.log(error);
					});
				}
			};

			// Helper: Tab key support di CSS textarea
			const handleCssTab = (e) => 
			{
				const textarea = e.target;
				const start = textarea.selectionStart;
				const end = textarea.selectionEnd;
				const spaces = '  '; // 2 spasi sebagai tab
				customCss.value = customCss.value.substring(0, start) + spaces + customCss.value.substring(end);
				nextTick(() => 
				{
					textarea.selectionStart = textarea.selectionEnd = start + spaces.length;
				});
			};
			
			const addListItem = (w) => w.items.push({ text: 'New Item' }); 
			const removeListItem = (w, i) => w.items.splice(i, 1);
			const addAccordionItem = (w) => w.items.push({ header: 'New', content: 'Content' }); 
			const removeAccordionItem = (w, i) => w.items.splice(i, 1);
			const addTableRow = (w) => w.tableData.rows.push(new Array(w.tableData.headers.length).fill('d')); 
			const addTableCol = (w) => { w.tableData.headers.push('H'); w.tableData.rows.forEach(r => r.push('d')); }; 
			const removeTableCol = (w, idx) => { if(w.tableData.headers.length <=1) return; w.tableData.headers.splice(idx, 1); w.tableData.rows.forEach(r => r.splice(idx, 1)); }; 
			const removeTableRow = (w, idx) => { w.tableData.rows.splice(idx, 1); };

			// 1. Definisikan Fungsi addNestedRow (Letakkan di dekat fungsi addColumn)
			const addNestedRow = (targetCol) => 
			{
				// Membuat object Row baru
				const newRow = {
					id: Math.random().toString(36).substr(2, 9),
					type: 'row',
					cols: [], // tidak dipakai utk render tapi utk struktur awal
					// Default properties untuk Row (sama seperti cloneItem)
					gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '',
					widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100',
					customClass: '',
					children: [
						// Langsung isi dengan 1 kolom agar terlihat
						{
							id: Math.random().toString(36).substr(2, 9),
							type: 'col',
							widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12',
							children: [],
							customClass: ''
						}
					]
				};
				// Masukkan Row baru ke dalam children dari Column yang diklik
				targetCol.children.push(newRow);
			};

			const containerGroup = {
				name: 'section',
				put: (to, from, dragEl) => {
					// 1. BLOKIR WIDGET (Sidebar & Canvas)
					if (from.options.group.name === 'widget') return false;
					if (dragEl.classList.contains('widget-ui')) return false;
					
					// 2. BLOKIR CONTAINER (BARU)
					// Container berasal dari group 'root'. Kita tolak agar tidak masuk ke dalam Container lain (Nesting Container).
					if (from.options.group.name === 'root') return false;
					if (dragEl.classList.contains('container-ui')) return false;

					// Selain itu (Row Sidebar atau Nested Row) BOLEH MASUK
					return true;
				}
			};

			const rowGroup = {
				name: 'row-cols',
				put: (to, from, dragEl) => {
					// 1. BLOKIR WIDGET (Sidebar & Canvas)
					if (from.options.group.name === 'widget') return false;
					if (dragEl.classList.contains('widget-ui')) return false;

					// 2. BLOKIR CONTAINER (BARU - SOLUSI MASALAH ANDA)
					// Mencegah Container masuk ke Row dan dianggap sebagai Column
					if (from.options.group.name === 'root') return false;
					if (dragEl.classList.contains('container-ui')) return false;

					// Selain itu (Row, Nested Row, atau Column Reorder) BOLEH MASUK
					return true;
				}
			};

			// --- TAMBAHKAN LOGIKA DETEKSI DEVICE INI DI DALAM SETUP() ---
			
			// Default false, nanti diupdate saat mounted
			const isMobileDevice = ref(false);

			const checkDeviceWidth = () => {
				// Batasnya 768px (Ukuran Tablet Portrait / HP Besar).
				// Jika lebar window kurang dari 768px, maka dianggap Mobile (True)
				isMobileDevice.value = window.innerWidth < 768;
			};

			const storedSidebar = localStorage.getItem('pb_show_left_sidebar');
			const showLeftSidebar = ref(storedSidebar === 'false' ? false : true); 
			const toggleLeftSidebar = () => {
				showLeftSidebar.value = !showLeftSidebar.value;
				localStorage.setItem('pb_show_left_sidebar', showLeftSidebar.value);
				// Force canvasFrameStyle recompute setelah sidebar animation selesai
				setTimeout(() => { windowWidth.value = window.innerWidth; }, 320);
			};


			// const showLeftSidebar = ref(true); // State untuk Sidebar Kiri
			// const toggleLeftSidebar = () => { showLeftSidebar.value = !showLeftSidebar.value; };
			// --- LOGIC DRAGGABLE POPUP ---

			// State Posisi Popup (Default di kanan atas)
			const popupPos = ref({ top: 85, left: window.innerWidth - 447 }); 

			// Variabel temp untuk dragging
			let isDragging = false;
			let dragOffset = { x: 0, y: 0 };

			// 1. Mulai Drag (MouseDown pada Header)
			const startDrag = (e) => 
			{
				isDragging = true;
				// Hitung jarak cursor dari pojok kiri-atas popup agar tidak 'lompat'
				dragOffset.x = e.clientX - popupPos.value.left;
				dragOffset.y = e.clientY - popupPos.value.top;
				
				// Attach event listener ke window agar drag mulus meski cursor keluar elemen
				window.addEventListener('mousemove', onDrag);
				window.addEventListener('mouseup', stopDrag);
			};

			// 2. Proses Drag (MouseMove)
			const onDrag = (e) => {
				if ( ! isDragging) return;
				
				// Update posisi berdasarkan pergerakan mouse
				popupPos.value.left = e.clientX - dragOffset.x;
				popupPos.value.top = e.clientY - dragOffset.y;
			};

			// 3. Stop Drag (MouseUp)
			const stopDrag = () => {
				isDragging = false;
				window.removeEventListener('mousemove', onDrag);
				window.removeEventListener('mouseup', stopDrag);
			};

			// --- LOGIC QUICK ADD & WIDGET PICKER ---

			const showWidgetModal = ref(false);
			const targetWidgetList = ref(null); // Menyimpan referensi array tujuan (children)

			// 1. Fungsi Buka Modal Pemilih Widget
			const openWidgetPicker = (targetArray) => {
				targetWidgetList.value = targetArray; // Simpan referensi array column tempat widget akan ditambah
				showWidgetModal.value = true;
			};

			// 2. Fungsi Eksekusi Tambah Widget dari Modal
			// FIX: ADD WIDGET (DEEP CLONE)
			const addWidgetFromPicker = (type) => {
				if ( ! targetWidgetList.value) return;
				
				// PERBAIKAN: Cari di semua list (Widgets + Advanced)
				const allWidgets = [...tools.widgets, ...tools.advanced];

				// Cari template default widget dari tools
				// const defaultWidget = tools.widgets.find(w => w.type === type);
				const defaultWidget = allWidgets.find(w => w.type === type);

				if (defaultWidget) {
					// PENTING: Gunakan JSON parse/stringify untuk Deep Clone
					// Ini memastikan semua properti dalam (styles, settings) ikut ter-copy
					const newWidget = JSON.parse(JSON.stringify(defaultWidget));
					
					newWidget.id = Date.now(); // Generate ID unik baru
					targetWidgetList.value.push(newWidget);
				}
				
				showWidgetModal.value = false;
				targetWidgetList.value = null;
			};

			const quickAddContainer = () => {
				layout.value.push({
					id: Date.now(),
					type: 'container',
					fluid: false,
					classMobile: 'container-fluid',
					classTablet: 'container',
					classDesktop: 'container',
					classFHD: 'container',
					class4K: 'container',
					bgTarget: 'section',
					sectionStyles:
					{
						bgColor: '',
						bgImage: '',
						bgSize: 'cover',
						bgPos: 'center',
						bgRepeat: 'no-repeat',
						minHeight: 'auto'
					},
					styles:
					{
						bgColor: '',
						bgImage: '',
						bgSize: 'cover',
						bgPos: 'center',
						bgRepeat: 'no-repeat',
						paddingTop: '3rem',
						paddingBottom: '3rem',
						marginTop: '0',
						marginBottom: '0',
						minHeight: 'auto'
					},
					customClass: '',
					children: []
				});
				
				// Auto scroll ke bawah
				setTimeout(() => 
				{
					const canvas = document.getElementById('canvasArea');
					if (canvas) window.scrollTo(0, document.body.scrollHeight);

				}, 100);
			};

			const quickAddRow = (container) => 
			{
				if ( ! container.children || ! Array.isArray(container.children)) 
				{
					container.children = [];
				}

				const newRow = 
				{
					id: 'row-' + Date.now(),
					type: 'row',
					gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '',
					widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100',
					customClass: '',
					children: [{ 
						id: 'col-' + Date.now() + 1, 
						type: 'column',
						span: 12, 
						widthMobile: 'col-12', 
						widthTablet: 'col-md-12', 
						widthDesktop: 'col-xl-12', 
						widthFHD: 'col-xxl-12', 
						width4K: 'col-3xl-12',
						customClass: '',
						children: [] 
					}]
				};

				container.children = [...container.children, newRow];
			};

			// Masukkan ke return object setup()
			const getCurrentDataType = (type) => DATA_TYPES.find(d => d.data === type);

			// ============================================================
			// GLOBAL DESELECT — klik di luar elemen aktif → reset activeItem
			// ============================================================
			const handleGlobalDeselect = (e) => {
				// Jangan deselect jika klik di dalam elemen-elemen ini:
				const keepActiveSelectors = [
					'.ui-box',              // container / row
					'.col-ui',              // column
					'.widget-ui',           // widget
					'.col-handle',          // label "Column"
					'.col-actions',         // tombol aksi kolom
					'.action-btn',          // tombol aksi container/row
					'.widget-btn',          // tombol aksi widget
					'.widget-overlay',      // toolbar widget
					'.sidebar-right',       // floating properties panel
					'.popup-body',          // isi popup properties
					'.widget-picker-modal', // modal pilih widget
					'.css-editor-modal',    // modal CSS editor
					'.modal',               // modal bootstrap lainnya
				];
				const clickedInside = keepActiveSelectors.some(sel =>
					e.target.closest(sel)
				);
				if (!clickedInside) {
					activeItem.value = null;
					activeType.value = '';
					showRightSidebar.value = false;
				}
			};

			// Pasang pendengar event saat aplikasi dimuat
			onMounted(() => {
				checkDeviceWidth(); // Cek saat pertama load
				window.addEventListener('resize', checkDeviceWidth); // Cek saat layar di-resize
				document.addEventListener('click', handleGlobalDeselect);
				
				// Load data dari database jika mode UPDATE
				if (PAGE_MODE === 'update' && PAGE_DATA !== null) 
				{
					try 
					{
						pageId.value = PAGE_DATA.id || '';
						pageUri.value = PAGE_DATA.uri || '';
						pageName.value = PAGE_DATA.page_name || '';
						pageStatus.value = PAGE_DATA.status || '';
					} 
					catch(e) 
					{
						pageId.value = 0;
						pageUri.value = '';
						pageName.value = '';
						pageStatus.value = '';
					}

					try
					{
						// Load layout
						const rawLayout = PAGE_DATA.layout;
						const parsed = typeof rawLayout === 'string'
							? (rawLayout.trim() !== '' ? JSON.parse(rawLayout) : [])
							: (Array.isArray(rawLayout) ? rawLayout : []);

						// Migrasi data lama: pastikan container punya sectionStyles & bgTarget
						parsed.forEach(cont => {
							if (cont.type === 'container') {
								if ( ! cont.bgTarget) cont.bgTarget = 'section';
								if ( ! cont.sectionStyles) cont.sectionStyles = { bgColor: cont.styles ? (cont.styles.bgColor || '') : '', bgImage: cont.styles ? (cont.styles.bgImage || '') : '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' };
								if ( ! cont.styles) cont.styles = { bgColor: '', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover', minHeight: 'auto' };
							}
						});

						layout.value = parsed;
					}
					catch(e)
					{
						console.warn('Gagal parse layout JSON:', e);
						layout.value = [];
					}

					try 
					{
						// Load custom CSS
						customCss.value = PAGE_DATA.custom_css || '';
					} 
					catch(e) 
					{
						customCss.value = '';
					}
				}

				saveState(); // (Ini kode lama anda)
			});

			// Bersihkan event saat aplikasi ditutup (Good Practice)
			onBeforeUnmount(() => {
				window.removeEventListener('resize', checkDeviceWidth);
				document.removeEventListener('click', handleGlobalDeselect);
				if (editorInstance) editorInstance.destroy(); // (Kode lama ckeditor)
			});


			// ============================================================
			// WYSIWYG: canvasFrameStyle — mengatur lebar canvas per viewport
			// ============================================================
			// windowWidth reaktif — update saat sidebar toggle atau resize
			const windowWidth = ref(window.innerWidth);
			window.addEventListener('resize', () => { windowWidth.value = window.innerWidth; });

			const canvasFrameStyle = computed(() => {
				const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
				const bg = isDark ? '#0f172a' : '#ffffff';
				const vt = previewType.value;

				// Touch reactive deps dulu agar computed selalu rerun saat sidebar toggle/resize
				const _w = windowWidth.value; // eslint-disable-line no-unused-vars
				const _s = showLeftSidebar.value; // eslint-disable-line no-unused-vars

				// Desktop, FHD, 4K — canvas mengisi penuh, tidak di-scale
				if (vt === 'fhd' || vt === 'desktop' || vt === '4k') {
					return {
						width: '100%',
						minHeight: 'calc(100vh - 60px)',
						backgroundColor: bg,
						boxShadow: 'none',
						flexShrink: 0,
						alignSelf: 'stretch'
					};
				}

				// Mobile / Tablet — fixed width, scale jika tidak muat
				// windowWidth.value diakses agar computed reaktif terhadap resize & sidebar toggle
				const sidebarW = showLeftSidebar.value ? 280 : 0;
				const availableWidth = (windowWidth.value - sidebarW - 40);
				const widthMap = { mobile: 375, tablet: 768 };
				const targetWidth = widthMap[vt] || 768;
				const scale = targetWidth > availableWidth ? availableWidth / targetWidth : 1;

				return {
					width: targetWidth + 'px',
					minHeight: 'calc(100vh - 60px)',
					backgroundColor: bg,
					transform: scale < 1 ? `scale(${scale})` : 'none',
					transformOrigin: 'top center',
					boxShadow: '0 0 40px rgba(0,0,0,0.18)',
					marginTop: '30px',
					marginBottom: scale < 1 ? (targetWidth * (scale - 1) * 0.5) + 'px' : '60px',
					flexShrink: 0
				};
			});

			// ============================================================
			// WYSIWYG: Widget components (sama persis dengan PreviewViewer)
			// ============================================================
			const WidgetImageC = { props: ['item', 'filterClass'], template: `<img :src="item.src" class="img-fluid rounded" :class="filterClass ? filterClass(item.customClass) : ''" :style="{ width: item.widthUnit === 'auto' ? 'auto' : item.widthVal + item.widthUnit, height: item.heightUnit === 'auto' ? 'auto' : item.heightVal + item.heightUnit, objectFit: 'cover' }">` };
			const WidgetButtonC = { props: ['item', 'filterClass'], template: `<a :href="item.href || '#'" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" :class="filterClass ? filterClass(item.customClass) : ''"><i v-if="item.iconType === 'class' && item.iconPos === 'start'" :class="item.iconClass"></i><span v-text="item.text"></span><i v-if="item.iconType === 'class' && item.iconPos === 'end'" :class="item.iconClass"></i></a>` };
			const WidgetHeadingC = { props: ['item', 'filterClass'], template: `<component :is="item.htmlTag || 'h2'" :class="filterClass ? filterClass(item.customClass) : ''" :style="{ textAlign: item.alignment, color: item.color, fontSize: item.fontSize ? item.fontSize + item.fontSizeUnit : null, fontWeight: item.fontWeight, margin: 0 }"><a v-if="item.link" :href="item.link" style="color:inherit;text-decoration:none;">@{{ item.text }}</a><span v-else>@{{ item.text }}</span></component>` };
			const WidgetSpacerC = { props: ['item', 'filterClass'], template: `<div :style="{height: item.height + (item.unit || 'px')}" :class="filterClass ? filterClass(item.customClass) : ''"></div>` };
			const WidgetDividerC = { props: ['item', 'filterClass'], template: `<div :class="['d-flex','w-100','align-items-center', item.align==='center'?'justify-content-center':(item.align==='end'?'justify-content-end':'justify-content-start'), filterClass ? filterClass(item.customClass) : '']" :style="{paddingTop:item.gap+'px',paddingBottom:item.gap+'px'}"><div :style="{width:item.width+(item.widthUnit||'%'),borderTopWidth:item.thickness+'px',borderTopStyle:item.style,borderTopColor:item.color}"></div></div>` };
			const WidgetCardC = { props: ['item', 'filterClass'], methods: { tr(t,l){if(!t)return '';return t.length<=l?t:t.substring(0,l)+'...'} }, template: `<div class="card" :class="[item.cardStyleClass, filterClass ? filterClass(item.customClass) : '']"><img v-if="item.src" :src="item.src" class="card-img-top" :style="{height:item.imgHeightUnit==='auto'?'auto':item.imgHeightVal+item.imgHeightUnit,objectFit:'cover'}"><div class="card-body"><h5 class="card-title" v-text="item.cardTitle"></h5><p class="card-text" v-text="item.cardText"></p><a v-if="item.btnLink" :href="item.btnLink" class="btn btn-primary" :class="item.btnClass" v-text="item.btnText"></a></div></div>` };
			const WidgetListC = { props: ['item', 'filterClass'], template: `<component :is="item.listType" class="list-group" :class="[item.listType==='ol'?'list-group-numbered':'', item.flush?'list-group-flush':'', filterClass ? filterClass(item.customClass) : '']"><li v-for="(sub,idx) in item.items" :key="idx" class="list-group-item d-flex justify-content-between align-items-center" :class="[sub.active?'active':'', sub.disabled?'disabled':'']"><span v-html="sub.text"></span><span v-if="sub.badge" :class="['badge rounded-pill', sub.badgeType]" v-text="sub.badgeText"></span></li></component>` };
			const WidgetTableC = { props: ['item', 'filterClass'], template: `<div class="table-responsive" :class="filterClass ? filterClass(item.customClass) : ''"><table class="table table-bordered"><thead><tr><th v-for="h in item.tableData.headers" v-text="h"></th></tr></thead><tbody><tr v-for="r in item.tableData.rows"><td v-for="c in r" v-text="c"></td></tr></tbody></table></div>` };
			const WidgetAccordionC = { props: ['item', 'filterClass'], template: `<div class="accordion" :id="'acc_'+item.id" :class="filterClass ? filterClass(item.customClass) : ''"><div v-for="(sub,idx) in item.items" :key="idx" class="accordion-item"><h2 class="accordion-header"><button class="accordion-button" :class="{collapsed:idx!==0}" type="button" data-bs-toggle="collapse" :data-bs-target="'#col_'+item.id+'_'+idx" v-text="sub.header"></button></h2><div :id="'col_'+item.id+'_'+idx" class="accordion-collapse collapse" :class="{show:idx===0}"><div class="accordion-body" v-text="sub.content"></div></div></div></div>` };
			const WidgetMediaC = { props: ['item', 'filterClass'], methods:{tr(t,l){if(!t)return '';return t.length<=l?t:t.substring(0,l)+'...'}}, template: `<div class="d-flex" :class="[item.imagePos==='end'?'flex-row-reverse text-end':'text-start','align-items-'+item.align, filterClass ? filterClass(item.customClass) : '']"><img :src="item.src" class="flex-shrink-0" :class="[item.imagePos==='end'?'ms-3':'me-3',item.imgClass]" :style="{width:item.imgWidthUnit==='auto'?'auto':item.imgWidthVal+item.imgWidthUnit,height:item.imgHeightUnit==='auto'?'auto':item.imgHeightVal+item.imgHeightUnit,objectFit:'cover'}"><div class="flex-grow-1" style="min-width:0;"><h5 :class="item.headingClass" v-text="item.heading"></h5><div :class="item.contentClass" v-text="item.content"></div></div></div>` };
			const WidgetVideoC = { props: ['item', 'filterClass'], template: `<div :class="filterClass ? filterClass(item.customClass) : ''" :style="{width:(item.width||100)+'%'}"><div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;background:#000;"><iframe v-if="!item.videoType||item.videoType==='youtube'" style="position:absolute;top:0;left:0;width:100%;height:100%;" :src="'https://www.youtube.com/embed/'+(item.youtubeUrl?(item.youtubeUrl.includes('v=')?item.youtubeUrl.split('v=')[1].split('&')[0]:item.youtubeUrl.split('/').pop()):'dQw4w9WgXcQ')" frameborder="0" allowfullscreen></iframe><video v-if="item.videoType==='file'" :src="item.videoSrc" controls style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;"></video></div></div>` };
			// Collapse widget
			const WidgetCollapseC = { props: ['item', 'filterClass'], template: `
				<div :class="filterClass ? filterClass(item.customClass) : ''">
					<button v-if="item.triggerType === 'button'"
						:class="['btn', item.triggerBtnColor, filterClass ? filterClass(item.customBtnClass) : '']"
						type="button" data-bs-toggle="collapse"
						:data-bs-target="'#' + item.collapseId + '-canvas'"
						:aria-expanded="item.isOpen ? 'true' : 'false'">
						@{{ item.triggerText }}
					</button>
					<a v-else :href="'#' + item.collapseId + '-canvas'"
						:class="[filterClass ? filterClass(item.customLinkClass) : '']"
						data-bs-toggle="collapse"
						:aria-expanded="item.isOpen ? 'true' : 'false'">
						@{{ item.triggerText }}
					</a>
					<div :id="item.collapseId + '-canvas'"
						:class="['collapse', item.direction === 'horizontal' ? 'collapse-horizontal' : '', item.isOpen ? 'show' : '']">
						<div :class="['collapse-content', filterClass ? filterClass(item.customContentClass) : '']"
							v-html="item.content"></div>
					</div>
				</div>` };

			// Nav Tabs widget — reactive: watch item.activeTabId dari panel settings
			const WidgetNavTabsC = { props: ['item', 'filterClass'],
				data() { return { activeId: '' }; },
				mounted() {
					const f = (this.item.items || []).find(t => t.itemType === 'button' && !t.disabled);
					this.activeId = this.item.activeTabId || (f ? f.id : '');
				},
				watch: {
					'item.activeTabId'(val) { if (val) this.activeId = val; },
					'item.items': { deep: true, handler(items) {
						if (this.item.activeTabId) { this.activeId = this.item.activeTabId; return; }
						const f = (items || []).find(t => t.itemType === 'button' && !t.disabled);
						if (f && !this.activeId) this.activeId = f.id;
					}}
				},
				template: `<div :class="filterClass ? filterClass(item.customClass) : ''">
					<ul :class="['nav', item.tabStyle || 'nav-tabs', item.tabJustify || '']" role="tablist">
						<template v-for="tab in item.items" :key="tab.id">
							<li v-if="tab.itemType === 'button'" class="nav-item" role="presentation">
								<button :class="['nav-link', activeId === tab.id ? 'active' : '', tab.disabled ? 'disabled' : '']"
									@click.prevent="!tab.disabled && (activeId = tab.id)"
									type="button">
									<i v-if="tab.icon" :class="tab.icon + ' me-1'"></i>@{{ tab.label }}
								</button>
							</li>
						</template>
					</ul>
					<div class="tab-content mt-2">
						<template v-for="tab in item.items" :key="'c-'+tab.id">
							<div v-if="tab.itemType === 'button'"
								:class="['tab-pane', activeId === tab.id ? 'active show' : '']"
								v-html="tab.content"></div>
						</template>
					</div>
				</div>` };

			// Media List widget — canvas version (uses same field names as default data)
			const WidgetMediaListC = {
				props: ['item', 'filterClass'],
				methods: {
					getColClass(item) {
						const d = item.colsDesktop || 3;
						return 'col-12 col-md-' + Math.floor(12 / d);
					},
					getYtId(url) {
						if (!url) return '';
						return url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop();
					}
				},
				template: `<div :class="filterClass ? filterClass(item.customClass) : ''">
					<div :class="['row', 'g-' + (item.gap !== undefined ? item.gap : 3)]">
						<div v-for="(sub, idx) in item.items" :key="idx" :class="getColClass(item)">
							<div class="position-relative overflow-hidden h-100"
								:class="[item.borderStyle === 'card' ? 'bg-white border' : '', item.roundedCorners ? 'rounded' : '']">
								<!-- Video item -->
								<template v-if="sub.type === 'video'">
									<div v-if="sub.videoType === 'youtube' && sub.youtubeUrl"
										:class="['ratio', 'ratio-' + (sub.aspectRatio || '16/9').replace('/', 'x')]">
										<iframe :src="'https://www.youtube.com/embed/' + getYtId(sub.youtubeUrl)"
											frameborder="0" allowfullscreen></iframe>
									</div>
									<div v-else-if="sub.videoSrc"
										:class="['ratio', 'ratio-' + (sub.aspectRatio || '16/9').replace('/', 'x')]">
										<video :src="sub.videoSrc" controls style="object-fit:cover;"></video>
									</div>
									<div v-else class="ratio ratio-16x9 bg-dark d-flex align-items-center justify-content-center">
										<i class="fas fa-play-circle fa-3x text-white opacity-50"></i>
									</div>
								</template>
								<!-- Image item — imgMode: default=img tag, bg=background div -->
								<template v-else>
									<div v-if="sub.imgMode === 'bg'"
										:style="{ height: (sub.bgHeight || item.imgHeight || 200) + 'px', backgroundImage: 'url(' + sub.src + ')', backgroundSize: sub.bgSize || 'cover', backgroundPosition: sub.bgPos || 'center', backgroundRepeat: sub.bgRepeat || 'no-repeat' }">
									</div>
									<img v-else :src="sub.src"
										class="w-100 d-block"
										:style="{ height: (item.imgHeight || 200) + 'px', objectFit: 'cover' }">
								</template>
								<!-- Text: overlay or below -->
								<div v-if="item.textPos === 'overlay'"
									class="position-absolute bottom-0 w-100 p-2"
									:style="{ backgroundColor: item.overlayColor || 'rgba(0,0,0,0.6)', color: item.overlayTextColor || '#fff' }">
									<h6 class="fw-bold mb-1" :class="item.titleClass" v-text="sub.title"></h6>
									<p class="small mb-0" :class="item.descClass" v-text="sub.desc"></p>
								</div>
								<div v-else class="p-2">
									<h6 class="fw-bold mb-1" :class="item.titleClass" v-text="sub.title"></h6>
									<p class="small text-muted mb-0" :class="item.descClass" v-text="sub.desc"></p>
								</div>
							</div>
						</div>
					</div>
				</div>` };

			// Placeholder untuk widget yang belum didukung
			const WidgetPlaceholderC = { props: ['item', 'filterClass'], template: `<div class="p-3 border rounded bg-light text-center text-muted"><i class="fas fa-puzzle-piece me-1"></i><small>@{{ item.label || item.type }}</small></div>` };

			const getWidgetTemplate = (item) => {
				if (!item) return WidgetPlaceholderC;
				switch(item.type) {
					case 'image': return WidgetImageC;
					case 'button': return WidgetButtonC;
					case 'heading': return WidgetHeadingC;
					case 'spacer': return WidgetSpacerC;
					case 'divider': return WidgetDividerC;
					case 'card': return WidgetCardC;
					case 'list': return WidgetListC;
					case 'table': return WidgetTableC;
					case 'accordion': return WidgetAccordionC;
					case 'media': return WidgetMediaC;
					case 'video': return WidgetVideoC;
					case 'collapse': return WidgetCollapseC;
					case 'nav_tabs': return WidgetNavTabsC;
					case 'media_list': return WidgetMediaListC;
					default: return WidgetPlaceholderC;
				}
			};

			const filterResponsiveClasses = (classString) => {
				if (!classString) return '';
				const vt = previewType.value;
				return classString.split(' ').filter(cls => {
					if (vt === 'mobile') { if (/(-(sm|md|lg|xl|xxl)-)/.test(cls)) return false; }
					else if (vt === 'tablet') { if (/(-(lg|xl|xxl)-)/.test(cls)) return false; }
					else if (vt === 'desktop') { if (/(-(xxl)-)/.test(cls)) return false; }
					return true;
				}).join(' ');
			};

			const getContainerClass = (cont) => {
				const map = { mobile: cont.classMobile||'container-fluid', tablet: cont.classTablet||'container-fluid', desktop: cont.classDesktop||'container', fhd: cont.classFHD||'container', '4k': cont.class4K||'container' };
				return map[previewType.value] || 'container';
			};

			// Helper: style wrapper section (full width background)
			const getSectionWrapperStyle = (cont) => {
				const target = cont.bgTarget || 'section';
				const ss = cont.sectionStyles || {};
				if (target === 'container') {
					return { width: '100%' };
				}
				const style = {
					width: '100%',
					backgroundPosition: ss.bgPos || 'center',
					backgroundRepeat: ss.bgRepeat || 'no-repeat',
					backgroundSize: ss.bgSize || 'cover',
				};
				if (ss.bgColor) style.backgroundColor = ss.bgColor;
				if (ss.bgImage) style.backgroundImage = 'url(' + ss.bgImage + ')';
				if (ss.minHeight && ss.minHeight !== 'auto') style.minHeight = ss.minHeight;
				console.log('[PB] sectionWrapper id=' + cont.id + ' target=' + target + ' bgColor=' + ss.bgColor, style);
				return style;
			};

			// Helper: style container inner (inner only background)
			const getContainerStyle = (cont) => {
				const target = cont.bgTarget || 'section';
				const s = cont.styles || {};
				if (target === 'section') {
					return { minHeight: s.minHeight && s.minHeight !== 'auto' ? s.minHeight : '' };
				}
				return {
					backgroundColor: s.bgColor || '',
					backgroundImage: s.bgImage ? 'url(' + s.bgImage + ')' : '',
					backgroundPosition: s.bgPos || 'center',
					backgroundRepeat: s.bgRepeat || 'no-repeat',
					backgroundSize: s.bgSize || 'cover',
					minHeight: s.minHeight && s.minHeight !== 'auto' ? s.minHeight : ''
				};
			};

			const getRowClasses = (row) => {
				const map = { mobile: (row.gutter||'')+' '+(row.widthMobile||'w-100'), tablet: (row.gutterTablet||'')+' '+(row.widthTablet||'w-100'), desktop: (row.gutterDesktop||'')+' '+(row.widthDesktop||'w-100'), fhd: (row.gutterFHD||'')+' '+(row.widthFHD||'w-100'), '4k': (row.gutter4K||'')+' '+(row.width4K||'w-100') };
				return map[previewType.value] || '';
			};

			const getColClasses = (col) => {
				const map = { mobile: col.widthMobile||'col-12', tablet: col.widthTablet||'col-md', desktop: col.widthDesktop||'col-xl', fhd: col.widthFHD||'col-xxl', '4k': col.width4K||'col-3xl' };
				return map[previewType.value] || 'col-12';
			};

			return { 
				layout, tools, previewMode, previewType, togglePreview, cloneItem, removeItem, 
				removeColumn, addColumn, addNestedRow, 
				saveJson, duplicateItem, openCkFinder, 
				undo, redo, canUndo: computed(() => historyIndex.value > 0), canRedo: computed(() => history.value.length > historyIndex.value + 1), 
				theme, toggleTheme, baseUrl, activeItem, activeType, setActive, deselectAll, 
				onDropToRow, showRightSidebar, toggleRightSidebar, addListItem, removeListItem, 
				addAccordionItem, removeAccordionItem, addTableRow, addTableCol, removeTableCol, removeTableRow, activeViewMode, 
				onDropToContainer, onDropToRow, onDropToColumn, containerGroup, rowGroup, isMobileDevice, 
				showLeftSidebar, toggleLeftSidebar, popupPos, startDrag, showWidgetModal, 
				openWidgetPicker, addWidgetFromPicker, quickAddContainer, quickAddRow, dataTypesConfig: DATA_TYPES, getCurrentDataType, 
				customCss, showCssEditor, cssEditorFullscreen, handleCssTab, pageId, pageUri, pageName, pageStatus,
				canvasFrameStyle, getWidgetTemplate, filterResponsiveClasses,
				getContainerClass, getRowClasses, getColClasses,
				getSectionWrapperStyle, getContainerStyle,
				toastEl, toastMessage, toastStatus
			};
		}
	}).use(window.VueSplide).mount('#app');
