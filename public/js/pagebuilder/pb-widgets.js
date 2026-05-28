			const WidgetVideo = { props: ['item', 'filterClass'], setup(props) { const playerRef = ref(null); let playerInstance = null; const getYoutubeId = (url) => { if (!url) return 'dQw4w9WgXcQ'; return url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop(); }; onMounted(() => { if (playerRef.value) { const options = { controls: props.item.controls ? ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'] : [], autoplay: props.item.autoplay, loop: { active: props.item.loop }, muted: props.item.muted, youtube: { noCookie: true, rel: 0, showinfo: 0, iv_load_policy: 3, modestbranding: 1 }, ratio: (props.item.aspectRatio === '16/9' ? '16:9' : (props.item.aspectRatio === '4/3' ? '4:3' : (props.item.aspectRatio === '1/1' ? '1:1' : '21:9'))) }; playerInstance = new Plyr(playerRef.value, options); } }); onBeforeUnmount(() => { if (playerInstance) { playerInstance.destroy(); } }); return { playerRef, getYoutubeId }; }, template: `<div :class="filterClass(item.customClass)" :style="{ width: (item.width || 100) + '%' }"> <div class="ratio"> <div v-if="!item.videoType || item.videoType === 'youtube'" class="plyr__video-embed" ref="playerRef"> <iframe :src="'https://www.youtube.com/embed/'+getYoutubeId(item.youtubeUrl)+'?origin='+baseUrl+'&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe> </div> <video v-if="item.videoType === 'file'" ref="playerRef" class="plyr" playsinline :controls="item.controls" :data-poster="item.poster" style="width: 100%; height: 100%; object-fit: cover;"> <source :src="item.videoSrc" type="video/mp4"> </video> </div> </div>` };
			const WidgetButton = { props: ['item', 'filterClass'], template: `<a :href="item.href || '#'" :target="item.newTab ? '_blank' : ''" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" :class="filterClass(item.customClass)"><i v-if="item.iconType === 'class' && item.iconPos === 'start'" :class="item.iconClass"></i><img v-if="item.iconType === 'image' && item.iconPos === 'start' && item.iconSrc" :src="item.iconSrc" style="width:20px;height:20px;object-fit:contain;"><span v-text="item.text"></span><i v-if="item.iconType === 'class' && item.iconPos === 'end'" :class="item.iconClass"></i><img v-if="item.iconType === 'image' && item.iconPos === 'end' && item.iconSrc" :src="item.iconSrc" style="width: 20px;height: 20px;object-fit: contain;"></a>` };
			const WidgetList = { props: ['item', 'filterClass'], template: `<component :is="item.listType" class="list-group" :class="[item.listType === 'ol' ? 'list-group-numbered' : '', item.flush ? 'list-group-flush' : '', item.noBorder ? 'border-0' : '', item.listSize !== 'default' && item.listSize !== 'custom' ? item.listSize : '', filterClass(item.customClass)]" :style="item.listSize === 'custom' && item.customSizeValue ? 'font-size: ' + item.customSizeValue + item.customSizeUnit + ';' : ''"><li v-for="(sub, idx) in item.items" :key="idx" class="list-group-item d-flex justify-content-between align-items-center" :class="[sub.active ? 'active ' + (sub.activeCss ? filterClass(sub.activeCss) : '') : '', sub.disabled ? 'disabled' : '', sub.customCss ? filterClass(sub.customCss) : '', item.noBorder ? 'border-0' : '', (sub.inputType !== 'none' && item.listType === 'ol') ? 'hide-marker-ol' : '']"><label class="d-flex align-items-center mb-0 me-auto" :class="item.listType === 'ol' && sub.inputType === 'none' ? 'ms-2' : ''" :style="sub.inputType !== 'none' && !sub.disabled ? 'cursor:pointer; flex-grow:1;' : 'flex-grow:1;'"><input v-if="sub.inputType === 'checkbox'" type="checkbox" class="form-check-input mt-0" :class="sub.markerPosition === 'before' ? 'order-2 ms-3 me-2' : 'order-1 me-3'" v-model="sub.inputChecked" :disabled="sub.disabled"><input v-else-if="sub.inputType === 'radio'" type="radio" :name="'list_radio_' + (item.id || _uid)" class="form-check-input mt-0" :class="sub.markerPosition === 'before' ? 'order-2 ms-3 me-2' : 'order-1 me-3'" :checked="sub.inputChecked" @change="sub.inputChecked = $event.target.checked" :disabled="sub.disabled"><div v-if="item.listType === 'ul' && (sub.inputType === 'none' || sub.showMarker)" class="d-flex align-items-center" :class="sub.markerPosition === 'before' && sub.inputType !== 'none' ? 'order-1' : (sub.inputType !== 'none' ? 'order-2 me-2' : 'me-2')"><i v-if="item.styleType === 'icon'" :class="item.commonIcon"></i><img v-else-if="item.styleType === 'image'" :src="item.commonImage" style="width:1.25em;"><i v-else class="fas fa-circle" style="font-size:0.4em; vertical-align:middle"></i></div><span v-if="item.listType === 'ol' && sub.inputType !== 'none' && sub.showMarker" class="fw-bold" :class="sub.markerPosition === 'before' ? 'order-1' : 'order-2 me-2'" v-text="(idx + 1) + '.'"></span><span v-html="sub.text" class="order-3" :class="{'text-muted': sub.disabled, 'text-decoration-line-through': sub.disabled}"></span></label><span v-if="sub.badge" :class="['badge rounded-pill', sub.badgeType === 'custom' ? sub.customBadgeCss : sub.badgeType]" v-text="sub.badgeText"></span></li></component>` };
			const WidgetCard = { props: ['item', 'filterClass'], methods: { truncate(text, limit) { if (!text) return ''; if (text.length <= limit) return text; return text.substring(0, limit) + '...'; } }, template: `<div class="card" :class="[item.cardStyleClass, filterClass(item.customClass)]"> <img v-if="item.src" :src="item.src" class="card-img-top" :class="item.imgClass" :style="{width: item.imgWidthUnit === 'auto' ? 'auto' : item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightUnit === 'auto' ? 'auto' : item.imgHeightVal + item.imgHeightUnit, objectFit: 'cover' }"> <div class="card-body"> <h5 class="card-title" :class="[{'text-truncate': item.truncTitleMode === 'auto'}, item.titleClass]" v-text="item.truncTitleMode === 'chars' ? truncate(item.cardTitle, item.truncTitleLimit) : item.cardTitle"> </h5> <p class="card-text" :class="item.textClass" :style="item.truncTextMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncTextLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncTextMode === 'chars' ? truncate(item.cardText, item.truncTextLimit) : item.cardText"> </p> <a v-if="item.btnLink" :href="item.btnLink" :target="item.newTab ? '_blank' : ''" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" :class="item.btnClass"> <i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass"></i> <img v-if="item.btnIconType === 'image' && item.btnIconPos === 'start' && item.btnIconSrc" :src="item.btnIconSrc" style="width:20px;height:20px;object-fit:contain;"> <span v-text="item.btnText"></span> <i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass"></i> <img v-if="item.btnIconType === 'image' && item.btnIconPos === 'end' && item.btnIconSrc" :src="item.btnIconSrc" style="width:20px;height:20px;object-fit:contain;"> </a> </div> </div>` };
			const WidgetMedia = { props: ['item', 'filterClass'], methods: { truncate(text, limit) { if (!text) return ''; if (text.length <= limit) return text; return text.substring(0, limit) + '...'; } }, template: `<div class="d-flex" :class="[item.imagePos === 'end' ? 'flex-row-reverse text-end' : 'text-start', 'align-items-' + item.align, filterClass(item.customClass)]"> <img :src="item.src" class="flex-shrink-0" :class="[item.imagePos === 'end' ? 'ms-3' : 'me-3', item.imgClass]" :style="{width: item.imgWidthUnit === 'auto' ? 'auto' : item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightUnit === 'auto' ? 'auto' : item.imgHeightVal + item.imgHeightUnit, objectFit: 'cover' }"> <div class="flex-grow-1" style="min-width: 0;"> <h5 :class="[{'text-truncate': item.truncHeadingMode === 'auto'}, item.headingClass]" v-text="item.truncHeadingMode === 'chars' ? truncate(item.heading, item.truncHeadingLimit) : item.heading"></h5> <div :class="item.contentClass" :style="item.truncContentMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncContentLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncContentMode === 'chars' ? truncate(item.content, item.truncContentLimit) : item.content"></div> <div v-if="item.mediaBtnText && item.mediaBtnLink && (!item.mediaBtnPos || item.mediaBtnPos === 'below')" class="mt-2"> <a :href="item.mediaBtnLink" :target="item.mediaBtnNewTab ? '_blank' : ''" :class="item.mediaBtnClass || 'btn btn-primary btn-sm'">@{{ item.mediaBtnText }}</a> </div> </div> <div v-if="item.mediaBtnText && item.mediaBtnLink && item.mediaBtnPos === 'side'" class="flex-shrink-0 align-self-center" :class="item.imagePos === 'end' ? 'me-3' : 'ms-3'"> <a :href="item.mediaBtnLink" :target="item.mediaBtnNewTab ? '_blank' : ''" :class="item.mediaBtnClass || 'btn btn-primary btn-sm'">@{{ item.mediaBtnText }}</a> </div> </div>` };
			const WidgetAccordion = { props: ['item', 'filterClass'], template: `<div class="accordion" :id="'acc_' + item.id" :class="filterClass(item.customClass)"><div v-for="(sub, idx) in item.items" :key="idx" class="accordion-item"><h2 class="accordion-header" :id="'heading_' + item.id + '_' + idx"><button class="accordion-button" :class="{collapsed: idx!==0}" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse_' + item.id + '_' + idx" v-text="sub.header"></button></h2><div :id="'collapse_' + item.id + '_' + idx" class="accordion-collapse collapse" :class="{show: idx===0}" :data-bs-parent="'#acc_' + item.id"><div class="accordion-body" v-text="sub.content"></div></div></div></div>` };
			const WidgetTable = { props: ['item', 'filterClass'], template: `<div class="table-responsive" :class="filterClass(item.customClass)"><table class="table table-bordered"><thead><tr><th v-for="h in item.tableData.headers" v-text="h"></th></tr></thead><tbody><tr v-for="r in item.tableData.rows"><td v-for="c in r" v-text="c"></td></tr></tbody></table></div>` };			
			const WidgetSpacer = { props: ['item', 'filterClass'], template: `<div :style="{height: item.height + (item.unit || 'px')}" :class="filterClass(item.customClass)"></div>` };
			const WidgetDivider = { props: ['item', 'filterClass'], template: `<div :class="['d-flex', 'w-100', 'align-items-center', item.align === 'center' ? 'justify-content-center' : (item.align === 'end' ? 'justify-content-end' : 'justify-content-start'), filterClass(item.customClass)]" :style="{paddingTop: item.gap+'px', paddingBottom: item.gap+'px'}"><div :style="{width: item.width + (item.widthUnit || '%'), borderTopWidth: item.thickness+'px', borderTopStyle: item.style, borderTopColor: item.color}"></div></div>` };
			const WidgetHeading = { props: ['item', 'filterClass'], template: `<component :is="item.htmlTag || 'h2'" :class="filterClass(item.customClass)" :style="{ textAlign: item.alignment, color: item.color, fontSize: item.fontSize ? item.fontSize + item.fontSizeUnit : null, fontWeight: item.fontWeight, margin: 0 }"><a v-if="item.link" :href="item.link" style="color: inherit; text-decoration: none;">@{{ item.text }}</a><span v-else>@{{ item.text }}</span></component> `};

			const WidgetMediaList = {
					props: ['item', 'filterClass', 'viewType'],
					setup(props) {
						// --- STATE ---
						const currentSlide = Vue.ref(0);
						const sliderViewport = Vue.ref(null);
						const sliderHeight = Vue.ref('auto');
						let autoplayTimer = null;
						let plyrInstances = []; 

						// Drag States
						const isDragging = Vue.ref(false);
						const startPos = Vue.ref(0);
						const currentTranslate = Vue.ref(0);

						// --- HELPERS ---
						const getYoutubeId = (url) => {
							if (!url) return '';
							return url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop();
						};

						const truncate = (text, limit) => {
							if (!text) return '';
							if (text.length <= limit) return text;
							return text.substring(0, limit) + '...';
						};

						const getColClass = () => {
							const i = props.item;
							const v = props.viewType;
							if (v) {
								if (v === 'mobile') return 'col-' + (12 / (i.colsMobile || 1)) + ' mb-3';
								if (v === 'tablet') return 'col-' + (12 / (i.colsTablet || 2)) + ' mb-3';
								if (v === 'desktop') return 'col-' + (12 / (i.colsDesktop || 3)) + ' mb-3';
								if (v === 'fhd') return 'col-' + (12 / (i.colsFHD || 4)) + ' mb-3';
								if (v === '4k') return 'col-' + (12 / (i.cols4K || 6)) + ' mb-3';
							}
							return 'col-' + (12 / (i.colsMobile || 1)) + ' col-md-' + (12 / (i.colsTablet || 2)) + ' col-xl-' + (12 / (i.colsDesktop || 3)) + ' col-xxl-' + (12 / (i.colsFHD || 4)) + ' col-3xl-' + (12 / (i.cols4K || 6)) + ' mb-3';
						};

						// --- COMPUTED PROPERTIES ---

						// 1. Active Columns
						const activeColumns = Vue.computed(() => {
							if (props.viewType) {
								if (props.viewType === 'mobile') return props.item.colsMobile || 1;
								if (props.viewType === 'tablet') return props.item.colsTablet || 2;
								if (props.viewType === 'desktop') return props.item.colsDesktop || 3;
								if (props.viewType === 'fhd') return props.item.colsFHD || 4;
								if (props.viewType === '4k') return props.item.cols4K || 6;
							}

							const w = window.innerWidth;
							if (w < 768) return props.item.colsMobile || 1;
							if (w < 1200) return props.item.colsTablet || 2;
							if (w < 1400) return props.item.colsDesktop || 3;
							if (w < 2560) return props.item.colsFHD || 4;
							return props.item.cols4K || 6;
						});

						// 2. Track Style
						const trackStyle = Vue.computed(() => {
							const cols = activeColumns.value;
							const totalItems = props.item.items.length;
							const idx = currentSlide.value;
							
							if (props.item.splideType === 'fade') {
								return { position: 'relative', width: '100%', height: sliderHeight.value };
							}

							const movePct = (100 / totalItems) * idx; 
							const isVertical = props.item.splideDirection === 'ttb';
							
							const dragOffset = isDragging.value ? currentTranslate.value : 0;
							const transitionStyle = isDragging.value ? 'none' : 'transform 0.5s ease-in-out';

							if (!isVertical) {
								return {
									transform: `translateX(calc(-${movePct}% + ${dragOffset}px))`,
									display: 'flex', flexWrap: 'nowrap',
									width: `${(totalItems / cols) * 100}%`,
									transition: transitionStyle,
									cursor: isDragging.value ? 'grabbing' : 'grab'
								};
							} else {
								return {
									transform: `translateY(calc(-${movePct}% + ${dragOffset}px))`,
									display: 'flex', flexDirection: 'column',
									height: `${(totalItems / cols) * 100}%`,
									transition: transitionStyle,
									cursor: isDragging.value ? 'grabbing' : 'grab'
								};
							}
						});

						// 3. Pagination Dots (UPDATED FOR FADE GRID)
						const paginationDots = Vue.computed(() => {
							const total = props.item.items.length;
							const cols = activeColumns.value;
							const type = props.item.splideType;

							// Logic khusus Fade: Dots mewakili "Halaman", bukan Item individual
							if (type === 'fade') {
								const totalPages = Math.ceil(total / cols);
								// Mengembalikan array index awal setiap halaman: [0, 3, 6...] jika cols=3
								return Array.from({length: totalPages}, (_, i) => i * cols);
							}

							// Logic Slide Standard
							const move = props.item.splidePerMove || 1;
							const maxIndex = Math.max(0, total - cols);
							const dots = [];
							
							for (let i = 0; i <= maxIndex; i += move) {
								dots.push(i);
							}
							
							if (dots.length > 0 && dots[dots.length - 1] < maxIndex) {
								dots.push(maxIndex);
							} else if (dots.length === 0 && total > 0) {
								dots.push(0);
							}

							return dots;
						});

						// --- METHODS ---

						// 1. Slide Style (UPDATED FOR FADE GRID)
						const getSlideStyle = (index) => {
							const cols = activeColumns.value;
							const totalItems = props.item.items.length;
							const gapVal = [0, 4, 8, 16, 24, 48][props.item.gap] || 16;
							const pad = gapVal / 2;

							if (props.item.splideType === 'fade') {
								// Hitung Halaman
								const itemPage = Math.floor(index / cols);       // Item ini ada di halaman ke berapa?
								const activePage = Math.floor(currentSlide.value / cols); // Halaman berapa yang sedang aktif?
								
								const isVisible = itemPage === activePage;
								
								// Hitung Posisi Grid dalam Halaman
								const colIndex = index % cols; // Urutan ke berapa dalam kolom (0, 1, 2...)
								const widthPct = 100 / cols;   // Lebar per item (misal 33.33%)

								return {
									position: 'absolute', 
									top: 0, 
									left: `${colIndex * widthPct}%`, // Geser ke samping sesuai urutan kolom
									width: `${widthPct}%`,
									height: 'auto', 
									opacity: isVisible ? 1 : 0, 
									zIndex: isVisible ? 2 : 1,
									visibility: isVisible ? 'visible' : 'hidden', 
									transition: 'opacity 0.8s ease-in-out',
									padding: `0 ${pad}px`, 
									boxSizing: 'border-box',
									pointerEvents: isVisible ? 'auto' : 'none' // Agar item tersembunyi tidak bisa diklik
								};
							}

							// Standard Slide
							if (!props.item.splideDirection || props.item.splideDirection === 'ltr') {
								return { 
									width: `${100 / totalItems}%`, 
									flexShrink: 0, 
									padding: `0 ${pad}px`, 
									boxSizing: 'border-box' 
								};
							} else {
								return { 
									width: '100%', 
									height: `${100 / totalItems}%`, 
									flexShrink: 0, 
									padding: `${pad}px 0`, 
									boxSizing: 'border-box' 
								};
							}
						};

						// 2. Update Dimensions
						const updateDimensions = () => {
							if(props.item.splideDirection === 'ltr' && props.item.splideType !== 'fade') {
								sliderHeight.value = 'auto';
								return;
							}

							setTimeout(() => {
								if (!sliderViewport.value) return;
								const items = sliderViewport.value.querySelectorAll('.media-inner-card');
								let maxH = 0;
								items.forEach(el => maxH = Math.max(maxH, el.offsetHeight));
								
								if (maxH > 0) {
									const cols = activeColumns.value; 
									const gapVal = [0, 4, 8, 16, 24, 48][props.item.gap] || 16;
									
									if (props.item.splideType === 'fade') {
										sliderHeight.value = (maxH + gapVal) + 'px'; 
									} else {
										const totalH = (maxH * cols) + (gapVal * cols); 
										sliderHeight.value = totalH + 'px';
									}
								} else {
									sliderHeight.value = (props.item.minHeight || 400) + 'px';
								}
							}, 400); 
						};

						// 3. VIDEO & AUTOPLAY MANAGER
						const manageActiveSlide = () => {
							stopAutoplay();
							plyrInstances.forEach(p => p.pause());

							const currentItemData = props.item.items[currentSlide.value];
							
							if (currentItemData && currentItemData.type === 'video') {
								const activeSlideEl = sliderViewport.value ? sliderViewport.value.querySelectorAll('.media-list-item')[currentSlide.value] : null;
								if(activeSlideEl) {
									const videoContainer = activeSlideEl.querySelector('.plyr');
									if(videoContainer) {
										const player = plyrInstances.find(p => p.elements.container === videoContainer);
										if(player) {
											if (props.item.splideAutoplay) {
												player.play();
												player.off('ended');
												player.once('ended', () => { nextSlide(); });
											} 
											return; 
										}
									}
								}
							}
							startAutoplay();
						};

						const startAutoplay = () => {
							stopAutoplay();
							if (props.item.splideAutoplay) {
								autoplayTimer = setInterval(nextSlide, props.item.splideInterval || 3000);
							}
						};

						const stopAutoplay = () => {
							if (autoplayTimer) clearInterval(autoplayTimer);
						};

						// 4. Navigation (UPDATED FOR FADE GRID)
						const getMaxIndex = () => {
							if (props.item.splideType === 'fade') return props.item.items.length - 1;
							const cols = activeColumns.value;
							return Math.max(0, props.item.items.length - cols);
						};

						const nextSlide = () => {
							const maxIndex = getMaxIndex();
							const total = props.item.items.length;
							
							// Logic Fade Grid: Pindah per HALAMAN (per cols)
							if (props.item.splideType === 'fade') {
								const cols = activeColumns.value;
								let nextIdx = currentSlide.value + cols;
								
								// Jika sudah di halaman terakhir (atau lebih), loop ke awal
								if (nextIdx >= total) nextIdx = 0;
								
								currentSlide.value = nextIdx;
							} 
							else { 
								// Logic Slide Biasa
								const move = props.item.splidePerMove || 1;
								if (props.item.splideType === 'loop') {
									if (currentSlide.value >= maxIndex) currentSlide.value = 0; 
									else currentSlide.value = Math.min(maxIndex, currentSlide.value + move);
								} else {
									currentSlide.value = Math.min(maxIndex, currentSlide.value + move);
								}
							}
						};

						const prevSlide = () => {
							const total = props.item.items.length;
							const maxIndex = getMaxIndex();

							if (props.item.splideType === 'fade') {
								const cols = activeColumns.value;
								let prevIdx = currentSlide.value - cols;

								// Jika mundur dari 0, ke halaman terakhir yg valid
								if (prevIdx < 0) {
									// Cari index awal dari halaman terakhir
									const remainder = total % cols;
									// Jika remainder 0, berarti full page. Jika ada sisa, halaman terakhir isinya sisa itu.
									prevIdx = remainder === 0 ? total - cols : total - remainder;
								}

								currentSlide.value = prevIdx;
							} 
							else {
								const move = props.item.splidePerMove || 1;
								if (props.item.splideType === 'loop') {
									if (currentSlide.value <= 0) currentSlide.value = maxIndex;
									else currentSlide.value = Math.max(0, currentSlide.value - move);
								} else {
									currentSlide.value = Math.max(0, currentSlide.value - move);
								}
							}
						};

						const goToSlide = (idx) => {
							// Validasi batas index
							if (props.item.splideType !== 'fade') {
								const maxIndex = getMaxIndex();
								if(idx > maxIndex) idx = maxIndex;
							}
							currentSlide.value = idx;
						};

						// 5. Drag Handlers
						const getPosition = (e) => {
							const isVertical = props.item.splideDirection === 'ttb';
							return isVertical 
								? (e.touches ? e.touches[0].clientY : e.clientY)
								: (e.touches ? e.touches[0].clientX : e.clientX);
						};

						const onDragStart = (e) => {
							if (props.item.splideType === 'fade') return;
							isDragging.value = true;
							startPos.value = getPosition(e);
							currentTranslate.value = 0;
							stopAutoplay();
						};

						const onDragMove = (e) => {
							if (!isDragging.value) return;
							const currentPos = getPosition(e);
							currentTranslate.value = currentPos - startPos.value;
						};

						const onDragEnd = () => {
							if (!isDragging.value) return;
							isDragging.value = false;
							const threshold = 50;
							if (currentTranslate.value < -threshold) nextSlide();
							else if (currentTranslate.value > threshold) prevSlide();
							currentTranslate.value = 0;
						};

						// LIFECYCLE
						Vue.onMounted(() => {
							plyrInstances = [];
							const players = document.querySelectorAll('.plyr-list-' + props.item.id);
							players.forEach((el, index) => {
								const wrapper = el.closest('.media-list-video-container');
								const dataIndex = wrapper ? parseInt(wrapper.getAttribute('data-index')) : index;
								
								let ratioSetting = el.getAttribute('data-ratio') || '16/9';
								let configRatio = ratioSetting.replace('/', ':');
								
								const p = new Plyr(el, {
									ratio: configRatio,
									controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
									youtube: { noCookie: true, rel: 0, modestbranding: 1 },
									autopause: false,
									mediaIndex: dataIndex 
								});
								
								p.on('play', () => stopAutoplay());
								plyrInstances.push(p);
							});

							updateDimensions();
							setTimeout(manageActiveSlide, 500); 
							window.addEventListener('resize', updateDimensions);
						});

						Vue.watch(currentSlide, () => {
							manageActiveSlide();
						});

						Vue.watch(() => [
							props.item.items.length, 
							props.item.colsDesktop, props.item.colsTablet, props.item.colsMobile, props.item.colsFHD, props.item.cols4K, 
							props.item.splideDirection, props.item.gap, props.viewType, props.item.splideType
						], () => {
							updateDimensions();
							currentSlide.value = 0; 
						}, { deep: true });

						Vue.watch(() => [props.item.splideAutoplay, props.item.splideInterval], () => {
							manageActiveSlide();
						});

						Vue.onBeforeUnmount(() => {
							stopAutoplay();
							window.removeEventListener('resize', updateDimensions);
							plyrInstances.forEach(p => p.destroy());
						});

						return { getYoutubeId, truncate, getColClass, sliderViewport, trackStyle, getSlideStyle, sliderHeight, nextSlide, prevSlide, goToSlide, currentSlide, paginationDots, onDragStart, onDragMove, onDragEnd };
					},
					template: `
					<div :class="filterClass(item.customClass)">
						
						<div v-if="!item.enableSplide" class="row" :class="'g-' + item.gap">
							<div v-for="(sub, idx) in item.items" :key="idx" :class="getColClass()">
								<div class="media-list-item media-inner-card h-100 position-relative overflow-hidden" 
									:class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners}" 
									:style="{ minHeight: item.minHeight > 0 ? item.minHeight + 'px' : 'auto' }">
									
									<div class="media-wrapper position-relative" style="width:100%;" 
										:style="{ aspectRatio: (sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default' || sub.bgHeight)) ? 'auto' : (sub.aspectRatio || '16/9'), height: (sub.type === 'image' && sub.imgMode === 'bg' && sub.bgHeight) ? sub.bgHeight + 'px' : 'auto' }">
										<img v-if="sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default')" :src="sub.src || 'https://placehold.co/600x400'" :class="sub.imgClass" style="width:100%; height:100%; object-fit:cover; display:block;">
										<div v-if="sub.type === 'image' && sub.imgMode === 'bg'" :class="sub.imgClass" :style="{ backgroundImage: 'url(' + (sub.src || 'https://placehold.co/600x400') + ')', backgroundSize: sub.bgSize || 'cover', backgroundRepeat: sub.bgRepeat || 'no-repeat', backgroundPosition: sub.bgPos || 'center', width: '100%', height: '100%' }"></div>
										<div v-if="sub.type === 'video'" class="media-list-video-container" :class="sub.videoClass" :data-index="idx" style="height:100%;"><div v-if="sub.videoType === 'youtube'" :class="'plyr__video-embed plyr-list-' + item.id" :data-ratio="sub.aspectRatio"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(sub.youtubeUrl) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div><video v-if="sub.videoType === 'file'" :class="'plyr plyr-list-' + item.id" :data-ratio="sub.aspectRatio" playsinline controls style="width: 100%; height: 100%; object-fit: cover;"><source :src="sub.videoSrc" type="video/mp4"></video></div>
										<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="[{'hover-fade': item.hoverAnim}, item.textWrapperClass]" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
									</div>
									<div v-if="item.textPos === 'below'" class="p-3" :class="[{'bg-white border': item.borderStyle === 'text', 'rounded': item.borderStyle === 'text' && item.roundedCorners}, item.textWrapperClass]" :style="{ color: item.textColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
								</div>
							</div>
						</div>

						<div v-else class="custom-slider position-relative" ref="sliderViewport" :style="{ overflow: 'hidden', width: '100%', height: sliderHeight, userSelect: 'none' }" @mousedown="onDragStart" @touchstart="onDragStart" @mousemove="onDragMove" @touchmove="onDragMove" @mouseup="onDragEnd" @touchend="onDragEnd" @mouseleave="onDragEnd">
							
							<div class="custom-slider-track" :style="trackStyle">
								<div v-for="(sub, idx) in item.items" :key="idx" :style="getSlideStyle(idx)">
									<div class="media-list-item media-inner-card h-100 position-relative overflow-hidden" :class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners}" :style="{ minHeight: item.minHeight > 0 ? item.minHeight + 'px' : 'auto' }">
										<div class="media-wrapper position-relative" style="width:100%;" 
											:style="{ aspectRatio: (sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default' || sub.bgHeight)) ? 'auto' : (sub.aspectRatio || '16/9'), height: (sub.type === 'image' && sub.imgMode === 'bg' && sub.bgHeight) ? sub.bgHeight + 'px' : 'auto' }">
											<img v-if="sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default')" :src="sub.src || 'https://placehold.co/600x400'" :class="sub.imgClass" style="width:100%; height:100%; object-fit:cover; display:block; pointer-events: none;">
											<div v-if="sub.type === 'image' && sub.imgMode === 'bg'" :class="sub.imgClass" :style="{ backgroundImage: 'url(' + (sub.src || 'https://placehold.co/600x400') + ')', backgroundSize: sub.bgSize || 'cover', backgroundRepeat: sub.bgRepeat || 'no-repeat', backgroundPosition: sub.bgPos || 'center', width: '100%', height: '100%' }"></div>
											<div v-if="sub.type === 'video'" class="media-list-video-container" :class="sub.videoClass" :data-index="idx" style="height:100%; pointer-events: auto;">
												<div v-if="sub.videoType === 'youtube'" :class="'plyr__video-embed plyr-list-' + item.id" :data-ratio="sub.aspectRatio"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(sub.youtubeUrl) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div>
												<video v-if="sub.videoType === 'file'" :class="'plyr plyr-list-' + item.id" :data-ratio="sub.aspectRatio" playsinline controls style="width: 100%; height: 100%; object-fit: cover;"><source :src="sub.videoSrc" type="video/mp4"></video>
											</div>
											<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="[{'hover-fade': item.hoverAnim}, item.textWrapperClass]" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
										</div>

										<div v-if="item.textPos === 'below'" class="p-3" :class="[{'bg-white border': item.borderStyle === 'text', 'rounded': item.borderStyle === 'text' && item.roundedCorners}, item.textWrapperClass]" :style="{ color: item.textColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
									</div>
								</div>
							</div>

							<div v-if="item.splideArrows" class="slider-arrows">
								<button type="button" @click.stop="prevSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', left: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }">
									<i :class="(item.splideArrowType === 'custom' ? item.splideArrowLeftIcon : 'fas fa-chevron-left') || 'fas fa-chevron-left'"></i>
								</button>

								<button type="button" @click.stop="nextSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', right: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }">
									<i :class="(item.splideArrowType === 'custom' ? item.splideArrowRightIcon : 'fas fa-chevron-right') || 'fas fa-chevron-right'"></i>
								</button>
							</div>

							<div v-if="item.splidePagination" class="slider-pagination d-flex justify-content-center gap-2 mt-3 position-absolute w-100" style="bottom: 10px; z-index: 9;">
								<button v-for="idx in paginationDots" :key="'dot-'+idx" 
										@click.stop="goToSlide(idx)"
										class="border-0 rounded-circle transition-all"
										:style="{ width: '10px', height: '10px', backgroundColor: currentSlide === idx ? (item.textColor || '#000') : '#ccc', transform: currentSlide === idx ? 'scale(1.2)' : 'scale(1)', opacity: currentSlide === idx ? 1 : 0.5, cursor: 'pointer' }">
								</button>
							</div>

						</div>
					</div>`
			};

			const WidgetDynamicPostList = {
				props: ['item', 'filterClass', 'viewType'],
				setup(props) {
					// STATE DATA
					const fetchedData = Vue.ref([]);
					const totalPages = Vue.ref(0);
					const currentPage = Vue.ref(1);
					const isLoading = Vue.ref(false);
					
					// STATE CURSOR
					const cursorNext = Vue.ref(null);
					const cursorPrev = Vue.ref(null);
					
					// SLIDER STATES
					const currentSlide = Vue.ref(0);
					const sliderViewport = Vue.ref(null);
					const sliderHeight = Vue.ref('auto');
					let autoplayTimer = null;
					let plyrInstances = []; 
					const isDragging = Vue.ref(false);
					const startPos = Vue.ref(0);
					const currentTranslate = Vue.ref(0);

					const config = Vue.computed(() => {
						const found = DATA_TYPES.find(d => d.data === props.item.dataType);
						return found || { field: {}, label: 'Unknown' };
					});

					// HELPERS
					const formatDate = (dateString) => {
						if(!dateString) return '';
						const date = new Date(dateString);
						return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
					};

					const stripHtml = (html) => {
						if (!html) return '';
						let tmp = document.createElement("DIV");
						tmp.innerHTML = html;
						let text = tmp.textContent || tmp.innerText || "";
						return text.trim();
					};

					const truncate = (text, limit) => {
						if (!text) return '';
						const cleanText = stripHtml(text);
						if (cleanText.length <= limit) return cleanText;
						return cleanText.substring(0, limit) + '...';
					};

					const getYoutubeId = (url) => (url && url.includes('v=')) ? url.split('v=')[1].split('&')[0] : (url ? url.split('/').pop() : '');

					// STYLE LOGIC
					const getBgStyle = (imgUrl, isGrid = false) => {
						if (props.item.imgMode !== 'background') return {};
						let size = props.item.bgSize === 'custom' ? `${props.item.bgSizeVal}${props.item.bgSizeUnit}` : props.item.bgSize;
						let width = '100%'; let height = '100%'; 
						if (isGrid) {
							width = props.item.imgWidthVal ? props.item.imgWidthVal + props.item.imgWidthUnit : '100%';
							height = props.item.imgHeightVal ? props.item.imgHeightVal + props.item.imgHeightUnit : (props.item.bgHeight ? props.item.bgHeight + 'px' : '200px');
						}
						return { backgroundImage: `url('${imgUrl || 'https://placehold.co/400x300'}')`, backgroundSize: size, backgroundPosition: props.item.bgPos, backgroundRepeat: props.item.bgRepeat, width: width, height: height };
					};

					const getCardStyle = () => {
						let style = {};
						const minH = parseInt(props.item.minHeight);
						if (minH > 0) style.minHeight = minH + 'px';
						style.display = 'flex'; style.flexDirection = 'column';
						return style;
					};

					// ALIGNMENT HELPER (NEW)
					const getPaginationAlign = () => {
						const align = props.item.navAlign || 'center'; // Default center
						const map = {
							'start': 'justify-content-start',
							'center': 'justify-content-center',
							'end': 'justify-content-end',
							'between': 'justify-content-between'
						};
						return map[align] || 'justify-content-center';
					};

					const getSmartPadding = (userClass) => (/\bp(?:[xytrblse])?-/.test(userClass || '')) ? '' : 'p-3'; 
					const getSmartButtonPadding = (userClass) => (/\bp(?:[xytrblse])?-/.test(userClass || '')) ? '' : 'p-0'; 
					const getBorderTextClasses = () => props.item.borderStyle === 'text' ? { 'border': true, 'rounded': props.item.roundedCorners } : {};
					const getListAlignClass = () => ({ 'start': 'align-items-start', 'center': 'align-items-center', 'end': 'align-items-end' }[props.item.verticalAlign] || 'align-items-center');

					const activeColumns = Vue.computed(() => {
						let w = window.innerWidth;
						if (props.viewType === 'mobile') w = 375; else if (props.viewType === 'tablet') w = 768; else if (props.viewType === 'desktop') w = 1200; else if (props.viewType === 'fhd') w = 1400; else if (props.viewType === '4k') w = 2560;
						if (w < 768) return props.item.colsMobile || 1; if (w < 1200) return props.item.colsTablet || 2; if (w < 1400) return props.item.colsDesktop || 3; if (w < 2560) return props.item.colsFHD || 4; return props.item.cols4K || 6;
					});

					// FETCH DATA
					const fetchData = async (input = 1) => {
						if(!props.item.dataType) return;
						isLoading.value = true; 
						const isCursorMode = props.item.paginationType === 'cursor';
						if (!isCursorMode) { currentPage.value = typeof input === 'number' ? input : 1; }

						const params = {
							type: props.item.dataType,
							category: props.item.selectedCats && props.item.selectedCats.length > 0 ? props.item.selectedCats.join(',') : '',
							status: props.item.selectedStatus || 'publish',
							field: Object.keys(config.value.field).length > 0 ? Object.values(config.value.field).join(',') : '*',
							perPage: props.item.perPage || 12,
							paginationType: props.item.paginationType || 'number'
						};

						if (isCursorMode) { if (typeof input === 'string') { params.cursor = input; } } else { params.page = currentPage.value; }
						
						try {
							const response = await axios.get('/home/listdata', { params });
							if(response.data.success || response.data.status === 'success') {
								fetchedData.value = response.data.data;
								if (isCursorMode) {
									const meta = response.data.meta || response.data; 
									cursorNext.value = meta.next_cursor || response.data.next_cursor || null;
									cursorPrev.value = meta.prev_cursor || response.data.prev_cursor || null;
								} else {
									totalPages.value = response.data.last_page || response.data.total_page || 1;
								}
							} else { fetchedData.value = []; }
						} catch (error) { fetchedData.value = []; } finally {
							isLoading.value = false;
							if(!props.item.usePagination && props.item.enableSplide) { setTimeout(() => { updateDimensions(); currentSlide.value = 0; }, 200); }
						}
					};

					// SLIDER LOGIC
					const trackStyle = Vue.computed(() => {
						if (props.item.splideType === 'fade') return { position: 'relative', width: '100%', height: sliderHeight.value };
						const cols = activeColumns.value; const idx = currentSlide.value; const dragOffset = isDragging.value ? currentTranslate.value : 0; const transition = isDragging.value ? 'none' : 'transform 0.5s ease-in-out'; const itemPct = 100 / cols; const movePct = itemPct * idx;
						if (props.item.splideDirection !== 'ttb') { return { transform: `translateX(calc(-${movePct}% + ${dragOffset}px))`, display: 'flex', flexWrap: 'nowrap', width: '100%', transition, cursor: isDragging.value ? 'grabbing' : 'grab', alignItems: 'flex-start' }; } 
						else { return { transform: `translateY(calc(-${movePct}% + ${dragOffset}px))`, display: 'flex', flexDirection: 'column', height: '100%', transition, cursor: isDragging.value ? 'grabbing' : 'grab' }; }
					});

					const getSlideStyle = (index) => {
						const cols = activeColumns.value; const pad = ([0, 4, 8, 16, 24, 48][props.item.gap] || 16) / 2;
						if (props.item.splideType === 'fade') {
							const itemPage = Math.floor(index / cols); const activePage = Math.floor(currentSlide.value / cols); const isVisible = itemPage === activePage; const colIndex = index % cols; const widthPct = 100 / cols;
							return { position: 'absolute', top: 0, left: `${colIndex * widthPct}%`, width: `${widthPct}%`, height: 'auto', opacity: isVisible ? 1 : 0, zIndex: isVisible ? 2 : 1, transition: 'opacity 0.6s ease-in-out', padding: `0 ${pad}px`, boxSizing: 'border-box', pointerEvents: isVisible ? 'auto' : 'none' };
						}
						const size = 100 / cols + '%';
						if (!props.item.splideDirection || props.item.splideDirection === 'ltr') return { width: size, flexShrink: 0, padding: `0 ${pad}px`, boxSizing: 'border-box' }; else return { width: '100%', height: size, flexShrink: 0, padding: `${pad}px 0`, boxSizing: 'border-box' };
					};

					const paginationDots = Vue.computed(() => {
						const total = fetchedData.value.length; const cols = activeColumns.value; 
						if (props.item.splideType === 'fade') { const tp = Math.ceil(total / cols); return Array.from({length: tp}, (_, i) => i * cols); }
						const move = props.item.splidePerMove || 1; const max = Math.max(0, total - cols); const dots = [];
						for (let i = 0; i <= max; i += move) dots.push(i);
						if (dots.length > 0 && dots[dots.length - 1] < max) dots.push(max); if (dots.length === 0 && total > 0) return [0]; return dots;
					});

					const updateDimensions = () => {
						setTimeout(() => {
							if (!sliderViewport.value) return;
							const userMinH = parseInt(props.item.minHeight) || 0; const gapVal = [0, 4, 8, 16, 24, 48][props.item.gap] || 16; const items = sliderViewport.value.querySelectorAll('.db-card'); const itemsArr = Array.from(items); const cols = activeColumns.value; let maxH = 0;
							if (props.item.splideDirection === 'ttb') { items.forEach(el => maxH = Math.max(maxH, el.offsetHeight)); sliderHeight.value = ((Math.max(maxH, userMinH) * cols) + (gapVal * cols)) + 'px'; return; }
							if (props.item.splideAutoHeight) { const startIndex = currentSlide.value; const totalItems = itemsArr.length; for (let i = 0; i < cols; i++) { const targetIndex = startIndex + i; if (targetIndex < totalItems && itemsArr[targetIndex]) maxH = Math.max(maxH, itemsArr[targetIndex].offsetHeight); } } else { items.forEach(el => maxH = Math.max(maxH, el.offsetHeight)); }
							const finalHeight = Math.max(maxH, userMinH); sliderHeight.value = finalHeight > 0 ? (finalHeight + gapVal) + 'px' : 'auto';
						}, 300);
					};

					const getMaxIndex = () => { if (props.item.splideType === 'fade') return fetchedData.value.length - 1; const cols = activeColumns.value; return Math.max(0, fetchedData.value.length - cols); };
					const nextSlide = () => { const move = props.item.splidePerMove || 1; const max = getMaxIndex(); if (props.item.splideType === 'fade') { const cols = activeColumns.value; let next = currentSlide.value + cols; if (next >= fetchedData.value.length) next = 0; currentSlide.value = next; } else if (props.item.splideType === 'loop') { currentSlide.value = (currentSlide.value >= max) ? 0 : Math.min(max, currentSlide.value + move); } else { currentSlide.value = Math.min(max, currentSlide.value + move); } };
					const prevSlide = () => { const move = props.item.splidePerMove || 1; const max = getMaxIndex(); if (props.item.splideType === 'fade') { const cols = activeColumns.value; let prev = currentSlide.value - cols; if (prev < 0) { const rem = fetchedData.value.length % cols; prev = rem === 0 ? fetchedData.value.length - cols : fetchedData.value.length - rem; } currentSlide.value = prev; } else if (props.item.splideType === 'loop') { currentSlide.value = (currentSlide.value <= 0) ? max : Math.max(0, currentSlide.value - move); } else { currentSlide.value = Math.max(0, currentSlide.value - move); } };
					const goToSlide = (idx) => { currentSlide.value = idx; };
					
					const startAutoplay = () => { stopAutoplay(); if (props.item.enableSplide && props.item.splideAutoplay && props.item.viewMode === 'grid') autoplayTimer = setInterval(nextSlide, props.item.splideInterval || 3000); };
					const stopAutoplay = () => { if (autoplayTimer) clearInterval(autoplayTimer); };
					const onDragStart = (e) => { if (props.item.splideType === 'fade') return; isDragging.value = true; startPos.value = props.item.splideDirection === 'ttb' ? (e.touches ? e.touches[0].clientY : e.clientY) : (e.touches ? e.touches[0].clientX : e.clientX); currentTranslate.value = 0; stopAutoplay(); };
					const onDragMove = (e) => { if (!isDragging.value) return; const currentPos = props.item.splideDirection === 'ttb' ? (e.touches ? e.touches[0].clientY : e.clientY) : (e.touches ? e.touches[0].clientX : e.clientX); currentTranslate.value = currentPos - startPos.value; };
					const onDragEnd = () => { if (!isDragging.value) return; isDragging.value = false; if (currentTranslate.value < -50) nextSlide(); else if (currentTranslate.value > 50) prevSlide(); currentTranslate.value = 0; startAutoplay(); };
					const getColClass = () => { const i = props.item; const v = props.viewType; if (v) { if (v === 'mobile') return 'col-' + (12 / (i.colsMobile || 1)) + ' mb-3'; if (v === 'tablet') return 'col-' + (12 / (i.colsTablet || 2)) + ' mb-3'; if (v === 'desktop') return 'col-' + (12 / (i.colsDesktop || 3)) + ' mb-3'; if (v === 'fhd') return 'col-' + (12 / (i.colsFHD || 4)) + ' mb-3'; if (v === '4k') return 'col-' + (12 / (i.cols4K || 6)) + ' mb-3'; } return 'col-' + (12 / (i.colsMobile || 1)) + ' col-md-' + (12 / (i.colsTablet || 2)) + ' col-xl-' + (12 / (i.colsDesktop || 3)) + ' col-xxl-' + (12 / (i.colsFHD || 4)) + ' col-3xl-' + (12 / (i.cols4K || 6)) + ' mb-3'; };

					// Watchers
					Vue.watch(() => [props.item.dataType, props.item.selectedCats, props.item.selectedStatus, props.item.perPage, props.item.paginationType], () => { fetchData(1); }, { deep: true });
					Vue.watch(() => props.item.viewMode, (newMode) => { if (newMode === 'list') stopAutoplay(); else { if (props.item.enableSplide) startAutoplay(); setTimeout(updateDimensions, 300); } });
					Vue.watch(() => [fetchedData.value.length, props.item.colsDesktop, props.item.splideDirection, props.item.gap, props.item.splideAutoHeight, props.item.minHeight], () => { if(!props.item.usePagination && props.item.enableSplide && props.item.viewMode === 'grid') { updateDimensions(); currentSlide.value = 0; } }, { deep: true });
					Vue.watch(() => [props.item.splideAutoplay, props.item.splideInterval], () => { if(props.item.enableSplide && props.item.splideAutoplay) startAutoplay(); else stopAutoplay(); });
					Vue.watch(currentSlide, () => { 
						stopAutoplay(); plyrInstances.forEach(p => p.pause());
						const cols = activeColumns.value; const start = currentSlide.value;
						if(sliderViewport.value) {
							const slides = sliderViewport.value.querySelectorAll('.media-list-item');
							if(slides[start]) { const videoEl = slides[start].querySelector('.plyr'); if(videoEl) { const player = plyrInstances.find(p => p.elements.container === videoEl); if(player && props.item.splideAutoplay) { player.play(); player.off('ended'); player.once('ended', () => nextSlide()); return; } } }
						}
						startAutoplay();
						if (props.item.enableSplide) updateDimensions();
					});

					Vue.onMounted(() => { fetchData(); window.addEventListener('resize', updateDimensions); if(props.item.enableSplide && props.item.viewMode === 'grid') startAutoplay(); });
					Vue.onBeforeUnmount(() => { stopAutoplay(); window.removeEventListener('resize', updateDimensions); plyrInstances.forEach(p => p.destroy()); });

					return { 
						fetchedData, totalPages, currentPage, isLoading, config, formatDate, truncate, stripHtml, fetchData,
						getColClass, sliderViewport, trackStyle, getSlideStyle, sliderHeight, nextSlide, prevSlide, goToSlide, currentSlide, paginationDots, onDragStart, onDragMove, onDragEnd, getYoutubeId, getListAlignClass, getBorderTextClasses, getBgStyle, getSmartPadding, getSmartButtonPadding, getCardStyle, cursorNext, cursorPrev,
						getPaginationAlign // Return helper function
					};
				},
				template: `
				<div :class="filterClass(item.customClass)">
					<div v-if="isLoading" class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
					<div v-else-if="!fetchedData || fetchedData.length === 0" class="text-center p-4 text-muted border border-dashed"><small>No Data Found</small></div>
					
					<div v-else>
						<div v-if="item.viewMode === 'list'" class="d-flex flex-column" :class="'gap-' + (item.gap !== undefined ? item.gap : 3)">
							<div v-for="(data, idx) in fetchedData" :key="idx" 
								class="d-flex w-100 overflow-hidden position-relative" 
								:class="[
									item.imagePos === 'end' ? 'flex-row-reverse' : 'flex-row', 
									getListAlignClass(), 
									{'border bg-white': item.borderStyle === 'card'}, 
									{'rounded': item.roundedCorners && item.borderStyle === 'card'}, 
									item.borderStyle === 'border-bottom' ? 'pb-' + (item.gap !== undefined ? item.gap : 3) : '',
									{'border-bottom': item.borderStyle === 'border-bottom' && idx < fetchedData.length - 1},
									item.textWrapperClass
								]">
								<div class="flex-shrink-0 position-relative bg-light overflow-hidden" :class="{'rounded': item.roundedCorners}" :style="{ width: item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightVal + item.imgHeightUnit }">
									<img v-if="item.imgMode !== 'background'" :src="data[config.field.thumb_l] || 'https://placehold.co/400x300'" style="width:100%; height:100%; object-fit:cover;">
									<div v-else :style="getBgStyle(data[config.field.thumb_l], false)"></div>
								</div>
								<div class="flex-grow-1 min-width-0" :class="[item.imagePos === 'end' ? 'me-3 pe-3' : 'ms-3', 'text-' + (item.textAlign || 'start'), getBorderTextClasses(true), getSmartPadding(item.listContentWrapperClass), item.listContentWrapperClass]" :style="{ color: item.textColor }">
									<div class="mb-2" v-if="item.showCategory || item.showDate"><span v-if="item.showCategory && data[config.field.category]" class="badge bg-primary me-2">@{{ data[config.field.category] }}</span><small v-if="item.showDate && data[config.field.date]" class="text-muted"><i class="far fa-calendar me-1"></i> @{{ formatDate(data[config.field.date]) }}</small></div>
									<h5 class="fw-bold mb-1" :class="[item.titleClass, {'d-block text-truncate': item.truncTitleMode === 'auto'}]"><a :href="config.detailUrl + data[config.field.id]" class="text-decoration-none text-reset">@{{ item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title]) }}</a></h5>
									<p v-if="item.showExcerpt && data[config.field.content]" class="mb-2" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p>
									<div v-if="item.showBtn && item.btnPos === 'below'" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass, {'mt-auto': item.btnVerticalPos === 'bottom'}]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div>
									<a v-if="!item.showBtn" :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a>
								</div>
								<div v-if="item.showBtn && item.btnPos === 'side'" class="flex-shrink-0 d-flex align-items-center" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass]" style="position: relative; z-index: 2;"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass"></i><span v-if="item.btnText" :class="{'ms-2': item.btnIconPos === 'start', 'me-2': item.btnIconPos === 'end'}">@{{ item.btnText }}</span><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass"></i></a></div>
							</div>
						</div>

						<div v-else>
							<div v-if="!item.usePagination && item.enableSplide" class="custom-slider position-relative" ref="sliderViewport" :style="{ overflow: 'hidden', width: '100%', height: sliderHeight, transition: 'height 0.3s ease', userSelect: 'none' }" @mousedown="onDragStart" @touchstart="onDragStart" @mousemove="onDragMove" @touchmove="onDragMove" @mouseup="onDragEnd" @touchend="onDragEnd" @mouseleave="onDragEnd">
								<div class="custom-slider-track" :style="trackStyle">
									<div v-for="(data, idx) in fetchedData" :key="idx" :style="getSlideStyle(idx)">
										<div class="media-list-item db-card position-relative overflow-hidden" :class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners, 'border-0': item.borderStyle === 'none'}" :style="getCardStyle()">
											<div class="media-wrapper position-relative" style="width:100%;" :style="{ aspectRatio: item.textPos === 'overlay' ? '16/9' : '' }">
												<template v-if="config.data !== 'video'"><img v-if="item.imgMode !== 'background'" :src="data[config.field.thumb_l] || 'https://placehold.co/400x300'" style="width:100%; height:100%; object-fit:cover; pointer-events:none; display:block;"><div v-else :style="getBgStyle(data[config.field.thumb_l], true)"></div></template>
												<div v-if="config.data === 'video' || data.type === 'video'" class="media-list-video-container" style="height:100%; pointer-events: auto;" :data-index="idx"><div class="plyr-dynamic plyr__video-embed" style="width:100%; height:100%;"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(data[config.field.content]) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div></div>
												<div v-if="item.showCategory && data[config.field.category]" class="position-absolute top-0 start-0 p-2"><span class="badge bg-primary">@{{ data[config.field.category] }}</span></div>
												<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="{'hover-fade': item.hoverAnim}" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor, position: 'absolute', bottom: 0, width: '100%', padding: '15px' }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartPadding(item.btnWrapperClass), item.btnWrapperClass]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
											</div>
											<div v-if="item.textPos === 'below'" :class="[item.textWrapperClass, getBorderTextClasses(false), getSmartPadding(item.textWrapperClass), {'bg-white': item.borderStyle === 'text'}, {'rounded': item.borderStyle === 'text' && item.roundedCorners}, 'd-flex flex-column flex-grow-1']" :style="{ color: item.textColor }"><small v-if="item.showDate" class="text-muted d-block mb-2"><i class="far fa-calendar me-1"></i> @{{ formatDate(data[config.field.date]) }}</small><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-2" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass, {'mt-auto': item.btnVerticalPos === 'bottom'}]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
										</div>
									</div>
								</div>
								<div v-if="item.splideArrows" class="slider-arrows"><button type="button" @click.stop="prevSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', left: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }"><i :class="(item.splideArrowType === 'custom' ? item.splideArrowLeftIcon : 'fas fa-chevron-left') || 'fas fa-chevron-left'"></i></button><button type="button" @click.stop="nextSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', right: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }"><i :class="(item.splideArrowType === 'custom' ? item.splideArrowRightIcon : 'fas fa-chevron-right') || 'fas fa-chevron-right'"></i></button></div>
								<div v-if="item.splidePagination" class="slider-pagination d-flex justify-content-center gap-2 mt-3 position-absolute w-100" style="bottom: 10px; z-index: 9;"><button v-for="idx in paginationDots" :key="'dot-'+idx" @click.stop="goToSlide(idx)" class="border-0 rounded-circle transition-all" :style="{ width: '10px', height: '10px', backgroundColor: currentSlide === idx ? (item.textColor || '#000') : '#ccc', transform: currentSlide === idx ? 'scale(1.2)' : 'scale(1)', opacity: currentSlide === idx ? 1 : 0.5, cursor: 'pointer' }"></button></div>
							</div>

							<div v-else class="row" :class="'g-' + item.gap">
								<div v-for="(data, idx) in fetchedData" :key="idx" :class="getColClass()">
									<div class="media-list-item position-relative overflow-hidden" :class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners, 'border-0': item.borderStyle === 'none'}" :style="getCardStyle()">
										<div class="media-wrapper position-relative" style="width:100%;" :style="{ aspectRatio: item.textPos === 'overlay' ? '16/9' : '' }">
											<template v-if="config.data !== 'video'"><img v-if="item.imgMode !== 'background'" :src="data[config.field.thumb_l] || 'https://placehold.co/400x300'" style="width:100%; height:100%; object-fit:cover; pointer-events:none; display:block;"><div v-else :style="getBgStyle(data[config.field.thumb_l], true)"></div></template>
											<div v-if="config.data === 'video' || data.type === 'video'" class="media-list-video-container" style="height:100%; pointer-events: auto;" :data-index="idx"><div class="plyr-dynamic plyr__video-embed" style="width:100%; height:100%;"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(data[config.field.content]) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div></div>
											<div v-if="item.showCategory && data[config.field.category]" class="position-absolute top-0 start-0 p-2"><span class="badge bg-primary">@{{ data[config.field.category] }}</span></div>
											<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="{'hover-fade': item.hoverAnim}" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor, position: 'absolute', bottom: 0, width: '100%', padding: '15px' }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartPadding(item.btnWrapperClass), item.btnWrapperClass]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
										</div>
										<div v-if="item.textPos === 'below'" :class="[item.textWrapperClass, getBorderTextClasses(false), getSmartPadding(item.textWrapperClass), {'bg-white': item.borderStyle === 'text'}, {'rounded': item.borderStyle === 'text' && item.roundedCorners}, 'd-flex flex-column flex-grow-1']" :style="{ color: item.textColor }"><small v-if="item.showDate" class="text-muted d-block mb-2"><i class="far fa-calendar me-1"></i> @{{ formatDate(data[config.field.date]) }}</small><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-2" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass, {'mt-auto': item.btnVerticalPos === 'bottom'}]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
									</div>
								</div>
							</div>
						</div>

						<div v-if="item.usePagination" class="mt-4 d-flex w-100" :class="[item.paginationClass, getPaginationAlign()]">
							
							<nav v-if="!item.paginationType || item.paginationType === 'number'" aria-label="Page navigation" v-show="totalPages > 1">
								<ul class="pagination mb-0">
									<li class="page-item" :class="{ disabled: currentPage === 1 }">
										<button class="page-link" :class="item.pageItemClass" @click="fetchData(currentPage - 1)" aria-label="Previous">
											<span v-if="item.pageIconType === 'image' && item.pagePrevImg"><img :src="item.pagePrevImg" style="height:1em;"></span>
											<i v-else :class="item.pagePrevIcon || 'fas fa-angle-left'"></i>
										</button>
									</li>
									<li v-for="p in totalPages" :key="p" class="page-item" :class="{ active: currentPage === p }">
										<button class="page-link" :class="item.pageItemClass" @click="fetchData(p)">@{{ p }}</button>
									</li>
									<li class="page-item" :class="{ disabled: currentPage === totalPages }">
										<button class="page-link" :class="item.pageItemClass" @click="fetchData(currentPage + 1)" aria-label="Next">
											<span v-if="item.pageIconType === 'image' && item.pageNextImg"><img :src="item.pageNextImg" style="height:1em;"></span>
											<i v-else :class="item.pageNextIcon || 'fas fa-angle-right'"></i>
										</button>
									</li>
								</ul>
							</nav>

							<div v-else-if="item.paginationType === 'simple' || item.paginationType === 'cursor'" class="d-flex w-100" :class="getPaginationAlign()">
								
								<button class="btn" :class="item.navBtnClass || 'btn-outline-primary'" 
									:disabled="item.paginationType === 'cursor' ? !cursorPrev : currentPage === 1" 
									@click="fetchData(item.paginationType === 'cursor' ? cursorPrev : currentPage - 1)">
									
									<span v-if="item.navShowIcon" class="me-2">
										<img v-if="item.navIconType === 'image' && item.navPrevImg" :src="item.navPrevImg" style="height:1em; vertical-align:middle;">
										<i v-else :class="item.navPrevIcon || 'fas fa-arrow-left'"></i>
									</span>
									Previous
								</button>

								<button class="btn" :class="item.navBtnClass || 'btn-outline-primary'" 
									:disabled="item.paginationType === 'cursor' ? !cursorNext : currentPage === totalPages" 
									@click="fetchData(item.paginationType === 'cursor' ? cursorNext : currentPage + 1)">
									
									Next
									<span v-if="item.navShowIcon" class="ms-2">
										<img v-if="item.navIconType === 'image' && item.navNextImg" :src="item.navNextImg" style="height:1em; vertical-align:middle;">
										<i v-else :class="item.navNextIcon || 'fas fa-arrow-right'"></i>
									</span>
								</button>
							</div>

						</div>
					</div>
				</div>
				`
			};

			const WidgetCollapse = {
				props: ['item', 'filterClass'],
				template: `
					<div :class="['w-100', filterClass(item.customClass)]">
						<button v-if="item.triggerType === 'button'" 
								:class="['btn', item.triggerBtnColor, filterClass(item.customBtnClass)]" 
								type="button" 
								data-bs-toggle="collapse" 
								:data-bs-target="'#' + item.collapseId + '-preview'" 
								:aria-expanded="item.isOpen ? 'true' : 'false'" 
								:aria-controls="item.collapseId + '-preview'">
							@{{ item.triggerText }}
						</button>
						
						<a v-else-if="item.triggerType === 'link'" 
						   :class="['text-decoration-none', item.triggerBtnColor, filterClass(item.customBtnClass)]" 
						   data-bs-toggle="collapse" 
						   :href="'#' + item.collapseId + '-preview'" 
						   role="button" 
						   :aria-expanded="item.isOpen ? 'true' : 'false'" 
						   :aria-controls="item.collapseId + '-preview'"
						   style="cursor: pointer; font-weight: 500;">
							@{{ item.triggerText }}
						</a>

						<div :class="[
								'collapse', 
								item.isOpen ? 'show' : '', 
								item.direction === 'horizontal' ? 'collapse-horizontal' : '',
								item.direction === 'up' ? 'collapse-up' : '',
								'mt-2'
							 ]" 
							 :id="item.collapseId + '-preview'">
							
							<div :class="['card card-body', 'anim-' + item.animation, filterClass(item.customCardClass)]" 
								 :style="item.direction === 'horizontal' ? 'width: 300px;' : ''">
								<div v-html="item.content"></div>
							</div>
						</div>
					</div>
				`
			};

			const WidgetNavTabs = {
			  props: ['item', 'filterClass'],
			  data() { return { openDropdownId: null, ddTop: 0, ddLeft: 0 }; },
			  mounted() {
			    this._clickOutside = e => {
			      if (this.openDropdownId && !this.$el.contains(e.target)) this.openDropdownId = null;
			    };
			    document.addEventListener('click', this._clickOutside, true);
			  },
			  beforeUnmount() {
			    document.removeEventListener('click', this._clickOutside, true);
			  },
			  methods: {
			    getActiveId() {
			      const f = this.item.items.find(t => t.itemType === 'button' && !t.disabled);
			      return this.item.activeTabId || (f ? f.id : '');
			    },
			    toggleDd(tab, e) {
			      if (this.openDropdownId === tab.id) { this.openDropdownId = null; return; }
			      const r = e.currentTarget.getBoundingClientRect();
			      this.ddTop = r.bottom + window.scrollY;
			      this.ddLeft = r.left + window.scrollX;
			      this.openDropdownId = tab.id;
			    }
			  },
			  template: `<div :id="item.customId || ('nav-widget-' + item.id)" :class="filterClass(item.customClass)"><div :class="item.alignment === 'vertical' ? 'd-flex align-items-start gap-3' : ''"><div :style="item.mobileScroll && item.alignment !== 'vertical' ? 'overflow-x: auto; -webkit-overflow-scrolling: touch; max-width: 100%; padding-bottom: 8px;' : ''"><ul class="nav" :class="[item.navStyle === 'tabs' ? 'nav-tabs' : (item.navStyle === 'pills' ? 'nav-pills' : 'nav-underline'), item.alignment === 'vertical' ? 'flex-column' : '', item.fillJustify === 'fill' ? 'nav-fill' : (item.fillJustify === 'justify' ? 'nav-justified' : ''), item.mobileScroll && item.alignment !== 'vertical' ? 'flex-nowrap' : '', filterClass(item.navClass)]" role="tablist" :aria-orientation="item.alignment === 'vertical' ? 'vertical' : 'horizontal'"><li v-for="(tab, idx) in item.items" :key="idx" class="nav-item" :class="[{ 'dropdown': tab.itemType === 'dropdown' }, filterClass(item.navItemClass), filterClass(tab.navItemClass)]" role="presentation"><button v-if="tab.itemType === 'button'" class="nav-link" :class="[{ 'active': tab.id === getActiveId(), 'disabled': tab.disabled }, filterClass(tab.customClass)]" :style="item.mobileScroll && item.alignment !== 'vertical' ? 'white-space: nowrap;' : ''" :id="(tab.customId || tab.id) + '-tab'" data-bs-toggle="tab" :data-bs-target="'#' + (tab.customId || tab.id) + '-pane'" type="button" role="tab" :aria-controls="(tab.customId || tab.id) + '-pane'" :aria-selected="tab.id === getActiveId()" :disabled="tab.disabled"><i v-if="tab.icon" :class="tab.icon + ' me-2'"></i><span v-text="tab.label"></span></button><a v-else-if="tab.itemType === 'link'" class="nav-link" :class="[{ 'disabled': tab.disabled }, filterClass(tab.customClass)]" :style="item.mobileScroll && item.alignment !== 'vertical' ? 'white-space: nowrap;' : ''" :href="tab.url" :target="tab.newTab ? '_blank' : '_self'" :disabled="tab.disabled"><i v-if="tab.icon" :class="tab.icon + ' me-2'"></i><span v-text="tab.label"></span></a><template v-else-if="tab.itemType === 'dropdown'"><a class="nav-link dropdown-toggle" href="#" role="button" :class="[{ 'show': openDropdownId === tab.id, 'disabled': tab.disabled }, filterClass(tab.customClass)]" :style="item.mobileScroll && item.alignment !== 'vertical' ? 'white-space: nowrap;' : ''" :id="tab.dropdownId || ('dropdown-' + tab.id)" @click.prevent="toggleDd(tab, $event)"><i v-if="tab.icon" :class="tab.icon + ' me-2'"></i><span v-text="tab.label"></span></a><Teleport to="body"><ul v-if="openDropdownId === tab.id" class="dropdown-menu show" :class="filterClass(tab.dropdownMenuClass)" :style="{ position: 'absolute', top: ddTop + 'px', left: ddLeft + 'px', zIndex: 9999, minWidth: '10rem' }"><li v-for="(dropItem, dIdx) in tab.dropdownItems" :key="dIdx"><a class="dropdown-item" :class="[{ 'disabled': dropItem.disabled }, filterClass(dropItem.itemClass)]" :href="dropItem.url" :target="dropItem.newTab ? '_blank' : '_self'" v-text="dropItem.label"></a></li></ul></Teleport></template></li></ul></div><div class="tab-content" :class="item.alignment === 'vertical' ? 'flex-grow-1 w-100' : 'mt-3'" v-if="item.items.some(t => t.itemType === 'button')"><template v-for="(tab, idx) in item.items" :key="'pane-'+idx"><div v-if="tab.itemType === 'button'" class="tab-pane fade" :class="[{ 'show active': tab.id === getActiveId() }, filterClass(tab.paneClass)]" :id="(tab.customId || tab.id) + '-pane'" role="tabpanel" :aria-labelledby="(tab.customId || tab.id) + '-tab'" tabindex="0" v-html="tab.body"></div></template></div></div></div>`
			};

			const getWidgetTemplate = (item) => 
			{
				switch (item.type) 
				{
					case 'heading': return WidgetHeading;
					case 'text': return WidgetText;
					case 'image': return WidgetImage;
					case 'video': return WidgetVideo;
					case 'button': return WidgetButton;
					case 'list': return WidgetList;
					case 'card': return WidgetCard;
					case 'media': return WidgetMedia;
					case 'accordion': return WidgetAccordion;
					case 'table': return WidgetTable;
					case 'spacer': return WidgetSpacer;
					case 'divider': return WidgetDivider;
					case 'media_list': return WidgetMediaList;
					case 'dynamic_post_list': return WidgetDynamicPostList;
					case 'collapse': return WidgetCollapse;
					case 'nav_tabs': return WidgetNavTabs;
					default: return WidgetText;
				}
			};

			return { getContainerClass, getRowClasses, getColClasses, filterResponsiveClasses, scaleStyle, getWidgetTemplate };
		}
	};

