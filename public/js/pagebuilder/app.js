/**
 * Page Builder — app.js
 * Vue 3 CDN, No Build Tools
 * Modular rewrite — EComposer-style UI
 */

(function () {
'use strict';

const { createApp, ref, reactive, computed, watch, nextTick, onMounted, onBeforeUnmount } = Vue;
const draggable = window.vuedraggable;

/* ============================================================
   UTILITY
   ============================================================ */
const t = (str) => str; // i18n stub — replace with real translations if needed

/* ============================================================
   DATA TYPES CONFIG (Dynamic Post List)
   ============================================================ */
const DATA_TYPES = [
	{
		data: 'article', label: 'Article',
		field: { id: 'id', title: 'title', content: 'content', thumb_s: 'thumb_s', thumb_l: 'thumb_l', author: 'author', date: 'created_at', category: 'category' },
		categories: ['uncategorized', 'daily', 'weekly', 'monthly'],
		status: ['publish', 'private'], detailUrl: 'articles/detail/'
	},
	{
		data: 'event', label: 'Event',
		field: { id: 'id', title: 'title', content: 'content', thumb_s: 'thumb_s', thumb_l: 'thumb_l', location: 'location', author: 'author', date: 'start_date', end_date: 'end_date' },
		categories: ['uncategorized', 'today', 'ongoing', 'upcoming'],
		status: ['publish', 'private'], detailUrl: 'events/detail/'
	},
	{
		data: 'testimoni', label: 'Testimoni',
		field: { id: 'id', title: 'title', content: 'content', thumb_s: 'thumb_s', thumb_l: 'thumb_l', position: 'position', date: 'created_at' },
		categories: ['uncategorized', 'google', 'facebook', 'instagram'],
		status: ['publish'], detailUrl: ''
	}
];

/* ============================================================
   CKEDITOR COMPONENT
   ============================================================ */
const CkEditorComponent = {
	props: ['modelValue', 'baseUrl'],
	template: `<div><div ref="editorRef"></div></div>`,
	setup(props, { emit }) {
		const editorRef = ref(null);
		let editorInstance = null;

		watch(() => props.modelValue, (newVal) => {
			if (editorInstance && newVal !== editorInstance.getData()) {
				editorInstance.setData(newVal || '');
			}
		});

		onMounted(() => {
			if (typeof ClassicEditor === 'undefined') return;
			ClassicEditor.create(editorRef.value, {
				toolbar: {
					items: ['heading','|','fontFamily','fontSize','fontColor','fontBackgroundColor','|','bold','italic','underline','strikethrough','|','link','bulletedList','numberedList','|','alignment','|','CKFinder','imageUpload','blockQuote','insertTable','mediaEmbed','horizontalLine','|','sourceEditing','htmlEmbed','|','undo','redo'],
					shouldNotGroupWhenFull: true
				},
				language: 'en',
				ckfinder: {
					openerMethod: 'modal',
					uploadUrl: (props.baseUrl || '/') + 'assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json'
				}
			}).then(editor => {
				editorInstance = editor;
				editor.setData(props.modelValue || '');
				editor.model.document.on('change:data', () => {
					emit('update:modelValue', editor.getData());
				});
				editor.ui.view.element.addEventListener('mousedown', (e) => e.stopPropagation());
			}).catch(console.error);
		});

		onBeforeUnmount(() => { if (editorInstance) editorInstance.destroy(); });
		return { editorRef };
	}
};

/* ============================================================
   WIDGET COMPONENTS (for PreviewViewer)
   ============================================================ */
const WidgetText = {
	props: ['item', 'filterClass'],
	template: `<div v-html="item.content" :class="filterClass(item.customClass)"></div>`
};

const WidgetHeading = {
	props: ['item', 'filterClass'],
	template: `<component :is="item.htmlTag || 'h2'" :class="filterClass(item.customClass)"
		:style="{ textAlign: item.alignment, color: item.color,
			fontSize: item.fontSize ? item.fontSize + item.fontSizeUnit : null,
			fontWeight: item.fontWeight, margin: 0 }">
		<a v-if="item.link" :href="item.link" style="color:inherit;text-decoration:none;">{{ item.text }}</a>
		<span v-else>{{ item.text }}</span>
	</component>`
};

const WidgetImage = {
	props: ['item', 'filterClass'],
	template: `<img :src="item.src" class="img-fluid" :class="filterClass(item.customClass)"
		:style="{ width: item.widthUnit === 'auto' ? 'auto' : item.widthVal + item.widthUnit,
			height: item.heightUnit === 'auto' ? 'auto' : item.heightVal + item.heightUnit, objectFit: 'cover' }">`
};

const WidgetButton = {
	props: ['item', 'filterClass'],
	template: `<a :href="item.href || '#'" :target="item.newTab ? '_blank' : ''"
		class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2"
		:class="filterClass(item.customClass)">
		<i v-if="item.iconType === 'class' && item.iconPos === 'start'" :class="item.iconClass"></i>
		<img v-if="item.iconType === 'image' && item.iconPos === 'start' && item.iconSrc" :src="item.iconSrc" style="width:20px;height:20px;object-fit:contain;">
		<span>{{ item.text }}</span>
		<i v-if="item.iconType === 'class' && item.iconPos === 'end'" :class="item.iconClass"></i>
		<img v-if="item.iconType === 'image' && item.iconPos === 'end' && item.iconSrc" :src="item.iconSrc" style="width:20px;height:20px;object-fit:contain;">
	</a>`
};

const WidgetCard = {
	props: ['item', 'filterClass'],
	methods: {
		truncate(text, limit) { if (!text || text.length <= limit) return text || ''; return text.substring(0, limit) + '...'; }
	},
	template: `<div class="card" :class="[item.cardStyleClass, filterClass(item.customClass)]">
		<img v-if="item.src" :src="item.src" class="card-img-top" :class="item.imgClass"
			:style="{width: item.imgWidthUnit === 'auto' ? 'auto' : item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightUnit === 'auto' ? 'auto' : item.imgHeightVal + item.imgHeightUnit, objectFit:'cover'}">
		<div class="card-body">
			<h5 class="card-title" :class="[{'text-truncate': item.truncTitleMode === 'auto'}, item.titleClass]">
				{{ item.truncTitleMode === 'chars' ? truncate(item.cardTitle, item.truncTitleLimit) : item.cardTitle }}
			</h5>
			<p class="card-text" :class="item.textClass"
				:style="item.truncTextMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncTextLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}">
				{{ item.truncTextMode === 'chars' ? truncate(item.cardText, item.truncTextLimit) : item.cardText }}
			</p>
			<a v-if="item.btnLink" :href="item.btnLink" :target="item.newTab ? '_blank' : ''"
				class="btn btn-primary" :class="item.btnClass">{{ item.btnText }}</a>
		</div>
	</div>`
};

const WidgetMedia = {
	props: ['item', 'filterClass'],
	methods: {
		truncate(text, limit) { if (!text || text.length <= limit) return text || ''; return text.substring(0, limit) + '...'; }
	},
	template: `<div class="d-flex" :class="[item.imagePos === 'end' ? 'flex-row-reverse text-end' : 'text-start', 'align-items-' + item.align, filterClass(item.customClass)]">
		<img :src="item.src" class="flex-shrink-0" :class="[item.imagePos === 'end' ? 'ms-3' : 'me-3', item.imgClass]"
			:style="{width: item.imgWidthUnit === 'auto' ? 'auto' : item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightUnit === 'auto' ? 'auto' : item.imgHeightVal + item.imgHeightUnit, objectFit:'cover'}">
		<div class="flex-grow-1" style="min-width:0;">
			<h5 :class="[{'text-truncate': item.truncHeadingMode === 'auto'}, item.headingClass]">
				{{ item.truncHeadingMode === 'chars' ? truncate(item.heading, item.truncHeadingLimit) : item.heading }}
			</h5>
			<div :class="item.contentClass"
				:style="item.truncContentMode === 'auto' ? { display:'-webkit-box', '-webkit-line-clamp': item.truncContentLines||3, '-webkit-box-orient':'vertical', overflow:'hidden'} : {}">
				{{ item.truncContentMode === 'chars' ? truncate(item.content, item.truncContentLimit) : item.content }}
			</div>
			<div v-if="item.mediaBtnText && item.mediaBtnLink && (!item.mediaBtnPos || item.mediaBtnPos === 'below')" class="mt-2">
				<a :href="item.mediaBtnLink" :target="item.mediaBtnNewTab ? '_blank' : ''" :class="item.mediaBtnClass || 'btn btn-primary btn-sm'">{{ item.mediaBtnText }}</a>
			</div>
		</div>
	</div>`
};

const WidgetList = {
	props: ['item', 'filterClass'],
	template: `<component :is="item.listType" class="list-group"
		:class="[item.listType === 'ol' ? 'list-group-numbered' : '', item.flush ? 'list-group-flush' : '', item.noBorder ? 'border-0' : '', filterClass(item.customClass)]"
		:style="item.listSize === 'custom' && item.customSizeValue ? 'font-size:' + item.customSizeValue + item.customSizeUnit + ';' : ''">
		<li v-for="(sub, idx) in item.items" :key="idx"
			class="list-group-item d-flex justify-content-between align-items-center"
			:class="[sub.active ? 'active' : '', sub.disabled ? 'disabled' : '', item.noBorder ? 'border-0' : '']">
			<span v-html="sub.text"></span>
			<span v-if="sub.badge" :class="['badge rounded-pill', sub.badgeType]" v-text="sub.badgeText"></span>
		</li>
	</component>`
};

const WidgetAccordion = {
	props: ['item', 'filterClass'],
	template: `<div class="accordion" :id="'acc_' + item.id" :class="filterClass(item.customClass)">
		<div v-for="(sub, idx) in item.items" :key="idx" class="accordion-item">
			<h2 class="accordion-header">
				<button class="accordion-button" :class="{collapsed: idx !== 0}" type="button"
					data-bs-toggle="collapse" :data-bs-target="'#col_' + item.id + '_' + idx">{{ sub.header }}</button>
			</h2>
			<div :id="'col_' + item.id + '_' + idx" class="accordion-collapse collapse" :class="{show: idx === 0}" :data-bs-parent="'#acc_' + item.id">
				<div class="accordion-body" v-html="sub.content"></div>
			</div>
		</div>
	</div>`
};

const WidgetTable = {
	props: ['item', 'filterClass'],
	template: `<div class="table-responsive" :class="filterClass(item.customClass)">
		<table class="table table-bordered">
			<thead><tr><th v-for="h in item.tableData.headers" :key="h">{{ h }}</th></tr></thead>
			<tbody><tr v-for="(row, ri) in item.tableData.rows" :key="ri"><td v-for="(cell, ci) in row" :key="ci">{{ cell }}</td></tr></tbody>
		</table>
	</div>`
};

const WidgetSpacer = {
	props: ['item', 'filterClass'],
	template: `<div :style="{height: item.height + (item.unit || 'px')}" :class="filterClass(item.customClass)"></div>`
};

const WidgetDivider = {
	props: ['item', 'filterClass'],
	template: `<div class="w-100 d-flex" :class="[item.align === 'center' ? 'justify-content-center' : (item.align === 'end' ? 'justify-content-end' : 'justify-content-start'), filterClass(item.customClass)]"
		:style="{paddingTop: item.gap+'px', paddingBottom: item.gap+'px'}">
		<div :style="{width: item.width + (item.widthUnit || '%'), borderTopWidth: item.thickness+'px', borderTopStyle: item.style, borderTopColor: item.color}"></div>
	</div>`
};

const WidgetCollapse = {
	props: ['item', 'filterClass'],
	setup(props) {
		const isOpen = ref(props.item.isOpen || false);
		return { isOpen };
	},
	template: `<div :class="filterClass(item.customClass)">
		<button v-if="item.triggerType === 'button'" :class="['btn', item.triggerBtnColor, item.customBtnClass]" @click="isOpen = !isOpen">
			{{ item.triggerText }} <i class="fas" :class="isOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
		</button>
		<div v-show="isOpen">
			<div class="card mt-2" :class="item.customCardClass"><div class="card-body" v-html="item.content"></div></div>
		</div>
	</div>`
};

const WidgetNavTabs = {
	props: ['item', 'filterClass'],
	setup(props) {
		const activeTab = ref(props.item.items && props.item.items.length ? props.item.items[0].id : '');
		return { activeTab };
	},
	template: `<div :class="filterClass(item.customClass)">
		<ul class="nav" :class="['nav-' + (item.navStyle || 'tabs'), item.navClass]">
			<li v-for="tab in item.items" :key="tab.id" class="nav-item" :class="item.navItemClass">
				<button class="nav-link" :class="[{active: activeTab === tab.id}, tab.navItemClass]" @click="activeTab = tab.id">
					<i v-if="tab.icon" :class="tab.icon + ' me-1'"></i>{{ tab.label }}
				</button>
			</li>
		</ul>
		<div class="tab-content mt-2">
			<div v-for="tab in item.items" :key="tab.id" class="tab-pane" :class="{active: activeTab === tab.id, show: activeTab === tab.id}">
				<div v-html="tab.body"></div>
			</div>
		</div>
	</div>`
};

// WidgetMediaList and WidgetDynamicPostList are complex — keep from original
// These are loaded from the global scope (defined inline in old file)
// For new modular structure, we reuse the same component definitions

const WidgetMediaList = {
	props: ['item', 'filterClass', 'viewType'],
	setup(props) {
		const currentSlide = Vue.ref(0);
		const sliderViewport = Vue.ref(null);
		const sliderHeight = Vue.ref('auto');
		let autoplayTimer = null;
		const isDragging = Vue.ref(false);
		const startPos = Vue.ref(0);
		const currentTranslate = Vue.ref(0);
		const getYoutubeId = (url) => { if (!url) return ''; return url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop(); };
		const truncate = (text, limit) => { if (!text || text.length <= limit) return text || ''; return text.substring(0, limit) + '...'; };
		const activeColumns = Vue.computed(() => {
			const i = props.item; const v = props.viewType;
			if (v === 'mobile') return i.colsMobile || 1; if (v === 'tablet') return i.colsTablet || 2;
			if (v === 'desktop') return i.colsDesktop || 3; if (v === 'fhd') return i.colsFHD || 4;
			if (v === '4k') return i.cols4K || 6;
			const w = window.innerWidth;
			if (w < 768) return i.colsMobile || 1; if (w < 1200) return i.colsTablet || 2;
			if (w < 1400) return i.colsDesktop || 3; if (w < 2560) return i.colsFHD || 4;
			return i.cols4K || 6;
		});
		const getColClass = () => {
			const i = props.item; const v = props.viewType;
			if (v) { if (v === 'mobile') return 'col-' + (12 / (i.colsMobile || 1)); if (v === 'tablet') return 'col-' + (12 / (i.colsTablet || 2)); if (v === 'desktop') return 'col-' + (12 / (i.colsDesktop || 3)); if (v === 'fhd') return 'col-' + (12 / (i.colsFHD || 4)); if (v === '4k') return 'col-' + (12 / (i.cols4K || 6)); }
			return 'col-' + (12 / (i.colsMobile || 1)) + ' col-md-' + (12 / (i.colsTablet || 2)) + ' col-xl-' + (12 / (i.colsDesktop || 3)) + ' col-xxl-' + (12 / (i.colsFHD || 4));
		};
		const getMaxIndex = () => { const cols = activeColumns.value; return Math.max(0, props.item.items.length - cols); };
		const nextSlide = () => { const max = getMaxIndex(); currentSlide.value = (currentSlide.value >= max) ? 0 : Math.min(max, currentSlide.value + (props.item.splidePerMove || 1)); };
		const prevSlide = () => { const max = getMaxIndex(); currentSlide.value = (currentSlide.value <= 0) ? max : Math.max(0, currentSlide.value - (props.item.splidePerMove || 1)); };
		const goToSlide = (idx) => { currentSlide.value = idx; };
		const startAutoplay = () => { stopAutoplay(); if (props.item.enableSplide && props.item.splideAutoplay) autoplayTimer = setInterval(nextSlide, props.item.splideInterval || 3000); };
		const stopAutoplay = () => { if (autoplayTimer) clearInterval(autoplayTimer); };
		const trackStyle = Vue.computed(() => {
			const cols = activeColumns.value; const idx = currentSlide.value; const total = props.item.items.length;
			const movePct = (100 / total) * idx; const dragOffset = isDragging.value ? currentTranslate.value : 0; const transition = isDragging.value ? 'none' : 'transform 0.5s ease-in-out';
			return { transform: `translateX(calc(-${movePct}% + ${dragOffset}px))`, display: 'flex', flexWrap: 'nowrap', width: `${(total / cols) * 100}%`, transition, cursor: isDragging.value ? 'grabbing' : 'grab' };
		});
		const paginationDots = Vue.computed(() => {
			const total = props.item.items.length; const cols = activeColumns.value; const max = Math.max(0, total - cols); const dots = []; for (let i = 0; i <= max; i++) dots.push(i); return dots;
		});
		const onDragStart = (e) => { isDragging.value = true; startPos.value = e.touches ? e.touches[0].clientX : e.clientX; currentTranslate.value = 0; stopAutoplay(); };
		const onDragMove = (e) => { if (!isDragging.value) return; currentTranslate.value = (e.touches ? e.touches[0].clientX : e.clientX) - startPos.value; };
		const onDragEnd = () => { if (!isDragging.value) return; isDragging.value = false; if (currentTranslate.value < -50) nextSlide(); else if (currentTranslate.value > 50) prevSlide(); currentTranslate.value = 0; startAutoplay(); };
		Vue.onMounted(() => { if (props.item.enableSplide && props.item.splideAutoplay) startAutoplay(); });
		Vue.onBeforeUnmount(() => stopAutoplay());
		return { currentSlide, sliderViewport, sliderHeight, activeColumns, getColClass, trackStyle, paginationDots, nextSlide, prevSlide, goToSlide, onDragStart, onDragMove, onDragEnd, truncate, getYoutubeId };
	},
	template: `<div :class="filterClass(item.customClass)">
		<div v-if="!item.enableSplide || item.viewMode !== 'grid'" class="row" :class="'g-' + (item.gap || 3)">
			<div v-for="(media, idx) in item.items" :key="idx" :class="getColClass()">
				<div class="position-relative overflow-hidden" :class="[item.borderStyle === 'card' ? 'card' : '', item.roundedCorners ? 'rounded' : '']">
					<div v-if="media.type === 'image' || !media.type">
						<img v-if="item.imgMode === 'standard' || !item.imgMode" :src="media.src" class="w-100" style="object-fit:cover;">
						<div v-else :style="{height: item.bgHeight + 'px', backgroundImage: 'url(' + media.src + ')', backgroundSize: item.bgSize, backgroundPosition: item.bgPos, backgroundRepeat: item.bgRepeat}"></div>
					</div>
					<div v-if="item.textPos === 'below' || !item.textPos" class="p-3">
						<h6 class="fw-bold mb-1" :class="item.titleClass">{{ truncate(media.title, item.truncTitleLimit || 30) }}</h6>
						<p class="small text-muted mb-0" :class="item.descClass">{{ truncate(media.desc, item.truncDescLimit || 100) }}</p>
					</div>
				</div>
			</div>
		</div>
		<div v-else class="position-relative overflow-hidden" ref="sliderViewport"
			@mousedown="onDragStart" @mousemove="onDragMove" @mouseup="onDragEnd" @mouseleave="onDragEnd"
			@touchstart="onDragStart" @touchmove="onDragMove" @touchend="onDragEnd">
			<div :style="trackStyle">
				<div v-for="(media, idx) in item.items" :key="idx" :style="{width: (100 / activeColumns) + '%', padding: '0 6px', flexShrink: 0}">
					<div class="card h-100">
						<img v-if="media.src && media.type !== 'video'" :src="media.src" class="card-img-top" style="height:180px;object-fit:cover;">
						<div class="card-body p-2">
							<h6 class="small fw-bold mb-1">{{ media.title }}</h6>
							<p class="small text-muted mb-0" style="font-size:11px;">{{ media.desc }}</p>
						</div>
					</div>
				</div>
			</div>
			<div v-if="item.splidePagination" class="d-flex justify-content-center gap-1 mt-2">
				<button v-for="(dot, idx) in paginationDots" :key="idx"
					@click="goToSlide(dot)" class="btn btn-sm rounded-circle p-0"
					:class="currentSlide === dot ? 'btn-primary' : 'btn-outline-secondary'"
					style="width:8px;height:8px;min-width:8px;"></button>
			</div>
			<button v-if="item.splideArrows && item.items.length > activeColumns" class="btn btn-sm btn-light border position-absolute start-0 top-50 translate-middle-y ms-1" @click="prevSlide">
				<i :class="item.splideArrowLeftIcon || 'fas fa-chevron-left'"></i>
			</button>
			<button v-if="item.splideArrows && item.items.length > activeColumns" class="btn btn-sm btn-light border position-absolute end-0 top-50 translate-middle-y me-1" @click="nextSlide">
				<i :class="item.splideArrowRightIcon || 'fas fa-chevron-right'"></i>
			</button>
		</div>
	</div>`
};

const WidgetDynamicPostList = {
	props: ['item', 'filterClass', 'viewType'],
	setup(props) {
		const fetchedData = Vue.ref([]);
		const totalPages = Vue.ref(0);
		const currentPage = Vue.ref(1);
		const isLoading = Vue.ref(false);
		const config = Vue.computed(() => {
			const found = DATA_TYPES.find(d => d.data === props.item.dataType);
			return found || DATA_TYPES[0];
		});
		const formatDate = (dateString) => {
			if (!dateString) return '';
			const date = new Date(dateString);
			return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
		};
		const stripHtml = (html) => { if (!html) return ''; const div = document.createElement('div'); div.innerHTML = html; return div.textContent || div.innerText || ''; };
		const truncate = (text, limit) => { if (!text) return ''; const cleanText = stripHtml(text); if (cleanText.length <= limit) return cleanText; return cleanText.substring(0, limit) + '...'; };
		const getColClass = () => {
			const i = props.item; const v = props.viewType;
			if (v === 'mobile') return 'col-' + (12 / (i.colsMobile || 1));
			if (v === 'tablet') return 'col-' + (12 / (i.colsTablet || 2));
			if (v === 'desktop') return 'col-' + (12 / (i.colsDesktop || 3));
			if (v === 'fhd') return 'col-' + (12 / (i.colsFHD || 4));
			return 'col-' + (12 / (i.colsMobile || 1)) + ' col-md-' + (12 / (i.colsTablet || 2)) + ' col-xl-' + (12 / (i.colsDesktop || 3)) + ' col-xxl-' + (12 / (i.colsFHD || 4));
		};
		const fetchData = async (page = 1) => {
			isLoading.value = true;
			try {
				const params = { data: props.item.dataType, page, per_page: props.item.perPage || 6, status: props.item.selectedStatus || 'publish' };
				const response = await axios.get('/home/listdata', { params });
				if (response.data.data) { fetchedData.value = response.data.data; totalPages.value = response.data.meta?.last_page || Math.ceil((response.data.meta?.total || 0) / (props.item.perPage || 6)); }
				else if (Array.isArray(response.data)) { fetchedData.value = response.data; }
			} catch (e) { console.warn('DynamicPostList fetch error:', e); fetchedData.value = []; }
			isLoading.value = false;
		};
		Vue.onMounted(() => fetchData());
		Vue.watch(() => [props.item.dataType, props.item.selectedStatus, props.item.perPage], () => fetchData(1), { deep: true });
		return { fetchedData, totalPages, currentPage, isLoading, config, formatDate, truncate, getColClass, fetchData };
	},
	template: `<div :class="filterClass(item.customClass)">
		<div v-if="isLoading" class="text-center p-4"><div class="spinner-border spinner-border-sm text-primary"></div></div>
		<div v-else>
			<div class="row" :class="'g-' + (item.gap || 3)">
				<div v-for="(post, idx) in fetchedData" :key="idx" :class="getColClass()">
					<div class="card h-100" :class="[item.borderStyle === 'card' ? '' : 'border-0', item.roundedCorners ? 'rounded' : '']">
						<img v-if="post[config.field.thumb_s]" :src="post[config.field.thumb_s]" class="card-img-top" style="height:180px;object-fit:cover;">
						<div class="card-body">
							<div v-if="item.showCategory || item.showDate" class="mb-2 d-flex gap-2 align-items-center">
								<span v-if="item.showCategory && post[config.field.category]" class="badge bg-primary">{{ post[config.field.category] }}</span>
								<small v-if="item.showDate && post[config.field.date]" class="text-muted">{{ formatDate(post[config.field.date]) }}</small>
							</div>
							<h6 class="card-title fw-bold mb-2">{{ truncate(post[config.field.title], item.truncTitleLimit || 60) }}</h6>
							<p v-if="item.showExcerpt" class="card-text text-muted small">{{ truncate(post[config.field.content], item.truncDescLimit || 120) }}</p>
						</div>
					</div>
				</div>
			</div>
			<div v-if="item.usePagination && totalPages > 1" class="d-flex justify-content-center mt-4 gap-2">
				<button class="btn btn-sm btn-outline-primary" :disabled="currentPage <= 1" @click="currentPage--; fetchData(currentPage)"><i class="fas fa-chevron-left"></i></button>
				<span class="btn btn-sm btn-primary">{{ currentPage }} / {{ totalPages }}</span>
				<button class="btn btn-sm btn-outline-primary" :disabled="currentPage >= totalPages" @click="currentPage++; fetchData(currentPage)"><i class="fas fa-chevron-right"></i></button>
			</div>
		</div>
	</div>`
};

/* ============================================================
   PREVIEW VIEWER COMPONENT
   ============================================================ */
const PreviewViewer = {
	props: ['data', 'viewType', 'customCss'],
	setup(props) {
		const getContainerClass = (cont) => {
			const map = { mobile: cont.classMobile || 'container-fluid', tablet: cont.classTablet || 'container-fluid', desktop: cont.classDesktop || 'container', fhd: cont.classFHD || 'container', '4k': cont.class4K || 'container' };
			return map[props.viewType] || 'container';
		};
		const getRowClasses = (row) => {
			const map = { mobile: (row.gutter || '') + ' ' + (row.widthMobile || 'w-100'), tablet: (row.gutterTablet || '') + ' ' + (row.widthTablet || 'w-md-100'), desktop: (row.gutterDesktop || '') + ' ' + (row.widthDesktop || 'w-xl-100'), fhd: (row.gutterFHD || '') + ' ' + (row.widthFHD || 'w-xxl-100'), '4k': (row.gutter4K || '') + ' ' + (row.width4K || 'w-3xl-100') };
			return map[props.viewType] || '';
		};
		const getColClasses = (col) => {
			const map = { mobile: col.widthMobile || 'col-12', tablet: col.widthTablet || 'col-md', desktop: col.widthDesktop || 'col-xl', fhd: col.widthFHD || 'col-xxl', '4k': col.width4K || 'col-3xl' };
			return map[props.viewType] || 'col';
		};
		const filterResponsiveClasses = (classString) => {
			if (!classString) return '';
			return classString.split(' ').filter(cls => {
				if (props.viewType === 'mobile') { if (/-(?:sm|md|lg|xl|xxl)-/.test(cls)) return false; }
				else if (props.viewType === 'tablet') { if (/-(?:lg|xl|xxl)-/.test(cls)) return false; }
				else if (props.viewType === 'desktop') { if (/-xxl-/.test(cls)) return false; }
				return true;
			}).join(' ');
		};
		const scaleStyle = computed(() => {
			const widthMap = { mobile: 375, tablet: 768, desktop: 1366, fhd: 1920, '4k': 2560 };
			const targetWidth = widthMap[props.viewType] || 1366;
			const windowWidth = window.innerWidth - 60;
			const scale = targetWidth > windowWidth ? windowWidth / targetWidth : 1;
			return { width: targetWidth + 'px', minHeight: '100%', backgroundColor: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#0f172a' : '#ffffff', position: 'absolute', top: '20px', left: '50%', transform: `translateX(-50%) scale(${scale})`, transformOrigin: 'top center', boxShadow: '0 0 50px rgba(0,0,0,0.15)', paddingBottom: '60px' };
		});
		const getWidgetComponent = (item) => {
			const map = { text: WidgetText, heading: WidgetHeading, image: WidgetImage, button: WidgetButton, card: WidgetCard, media: WidgetMedia, list: WidgetList, accordion: WidgetAccordion, table: WidgetTable, spacer: WidgetSpacer, divider: WidgetDivider, video: WidgetNavTabs, media_list: WidgetMediaList, dynamic_post_list: WidgetDynamicPostList, collapse: WidgetCollapse, nav_tabs: WidgetNavTabs };
			return map[item.type] || WidgetText;
		};
		return { getContainerClass, getRowClasses, getColClasses, filterResponsiveClasses, scaleStyle, getWidgetComponent };
	},
	components: { WidgetText, WidgetHeading, WidgetImage, WidgetButton, WidgetCard, WidgetMedia, WidgetList, WidgetAccordion, WidgetTable, WidgetSpacer, WidgetDivider, WidgetMediaList, WidgetDynamicPostList, WidgetCollapse, WidgetNavTabs },
	template: `<div style="position:relative;width:100%;min-height:calc(100vh - 56px);overflow:auto;background:var(--canvas-bg);">
		<component v-if="customCss" :is="'style'">{{ customCss }}</component>
		<div :style="scaleStyle">
			<div v-for="cont in data" :key="cont.id"
				:class="[getContainerClass(cont), filterResponsiveClasses(cont.customClass)]"
				:style="{ backgroundColor: cont.styles && cont.styles.bgColor ? cont.styles.bgColor : '', backgroundImage: cont.styles && cont.styles.bgImage ? 'url(' + cont.styles.bgImage + ')' : '', backgroundPosition: cont.styles && cont.styles.bgPos || 'center', backgroundRepeat: cont.styles && cont.styles.bgRepeat || 'no-repeat', backgroundSize: cont.styles && cont.styles.bgSize || 'cover', minHeight: cont.styles && cont.styles.minHeight !== 'auto' ? cont.styles.minHeight : '' }">
				<div v-for="row in cont.children" :key="row.id"
					class="row" :class="[getRowClasses(row), filterResponsiveClasses(row.customClass)]">
					<div v-for="col in row.children" :key="col.id"
						:class="[getColClasses(col), filterResponsiveClasses(col.customClass)]">
						<div v-for="item in col.children" :key="item.id">
							<div v-if="item.type === 'row'" class="row" :class="getRowClasses(item)">
								<div v-for="nCol in item.children" :key="nCol.id" :class="getColClasses(nCol)">
									<div v-for="nItem in nCol.children" :key="nItem.id">
										<component :is="getWidgetComponent(nItem)" :item="nItem" :filter-class="filterResponsiveClasses" :view-type="viewType"></component>
									</div>
								</div>
							</div>
							<component v-else :is="getWidgetComponent(item)" :item="item" :filter-class="filterResponsiveClasses" :view-type="viewType"></component>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>`
};

/* ============================================================
   MAIN VUE APP
   ============================================================ */
createApp({
	components: {
		draggable,
		'ckeditor-component': CkEditorComponent,
		'preview-viewer': PreviewViewer
	},

	setup() {
		/* ── Core State ─────────────────────────────────────── */
		const previewMode      = ref(false);
		const showRightSidebar = ref(false);
		const isSaving         = ref(false);
		const userId           = ref(0);
		const pageId           = ref(0);
		const pageUri          = ref('');
		const pageName         = ref('');
		const pageStatus       = ref('draft');
		const layout           = ref([]);
		const customCss        = ref('');
		const showCssEditor    = ref(false);
		const cssEditorFullscreen = ref(
			document.cookie.split('; ').find(r => r.startsWith('pb_css_fullscreen='))?.split('=')[1] === 'true'
		);
		const baseUrl          = typeof APP_BASE_URL !== 'undefined' ? APP_BASE_URL : '/';
		const theme            = ref('light');
		const activeItem       = ref(null);
		const activeType       = ref('');
		const activeViewMode   = ref('fhd');
		const isMobileDevice   = ref(false);
		const history          = ref([]);
		const historyIndex     = ref(-1);
		const isTimeTraveling  = ref(false);

		/* ── UI State (New) ─────────────────────────────────── */
		const sidebarActiveTab    = ref('elements');   // 'elements' | 'navigator'
		const widgetSearch        = ref('');
		const openCategories      = reactive({ pageSettings: false, layouts: true, grid: true, basic: true, dynamic: true });
		const designPanelTab      = ref('content');    // 'content' | 'design'
		const openPropSections    = reactive({
			contWidth: true, contAppearance: true,
			rowWidth: true, colWidth: true,
			widgetCommon: true,
			wHeading: true, wImage: true, wButton: true, wVideo: true, wSpacer: true, wDivider: true, wCard: true, wAccordion: true, wTable: true, wDynamic: true,
			spacing: true, width: true, position: true, zindex: true
		});
		const isPanelDragging     = ref(false);
		const showWidgetModal     = ref(false);
		const targetWidgetList    = ref(null);

		/* ── Design Panel State ─────────────────────────────── */
		const spacingUnit    = ref('px');
		const spacingLinked  = ref(false);
		const designSpacing  = reactive({ marginTop: 0, marginRight: 0, marginBottom: 0, marginLeft: 0, paddingTop: 0, paddingRight: 0, paddingBottom: 0, paddingLeft: 0 });
		const designWidth    = reactive({ type: 'auto', value: 100, unit: '%' });
		const designPosition = reactive({ type: '', hOrient: 'left', hValue: 0, hUnit: 'px', vOrient: 'top', vValue: 0, vUnit: 'px' });
		const designZIndex   = ref(0);

		/* ── Toast State ────────────────────────────────────── */
		const responseStatusToast         = ref('ph-callout-success');
		const responseMessageAfterSubmit  = ref('');
		const isArrayMessageAfterSubmit   = ref(0);

		const showToast = (status, message) => {
			responseStatusToast.value = status === 'success' ? 'ph-callout-success' : 'ph-callout-danger';
			if (Array.isArray(message)) { responseMessageAfterSubmit.value = message; isArrayMessageAfterSubmit.value = 1; }
			else { responseMessageAfterSubmit.value = message; isArrayMessageAfterSubmit.value = 0; }
			nextTick(() => {
				const toastEl = document.getElementById('pbToast');
				if (toastEl && typeof bootstrap !== 'undefined') {
					const t = bootstrap.Toast.getOrCreateInstance(toastEl);
					t.show();
				}
			});
		};

		/* ── Widget Tools Data ──────────────────────────────── */
		const tools = {
			containers: [
				{ type: 'container', classDesktop: 'container', classTablet: 'container', classMobile: 'container-fluid', classFHD: 'container', class4K: 'container', label: 'Fixed Width', styles: { bgColor: '#ffffff', minHeight: 'auto', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover' } },
				{ type: 'container', classDesktop: 'container-fluid', classTablet: 'container-fluid', classMobile: 'container-fluid', classFHD: 'container-fluid', class4K: 'container-fluid', label: 'Full Width', styles: { bgColor: '#ffffff', minHeight: 'auto', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover' } }
			],
			rows: [
				{ type: 'row', label: '1 Column',  cols: ['col-xl-12'] },
				{ type: 'row', label: '2 Columns', cols: ['col-xl-6', 'col-xl-6'] },
				{ type: 'row', label: '3 Columns', cols: ['col-xl-4', 'col-xl-4', 'col-xl-4'] },
				{ type: 'row', label: '4 Columns', cols: ['col-xl-3', 'col-xl-3', 'col-xl-3', 'col-xl-3'] },
				{ type: 'row', label: '5 Columns', cols: ['col-xl', 'col-xl', 'col-xl', 'col-xl', 'col-xl'] },
				{ type: 'row', label: '6 Columns', cols: ['col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2'] }
			],
			widgets: [
				{ type: 'text',       label: 'Text',         icon: 'fas fa-paragraph',   content: '<p>Lorem ipsum dolor sit amet...</p>' },
				{ type: 'heading',    label: 'Heading',      icon: 'fas fa-heading',     text: 'Add Your Heading Here', link: '', htmlTag: 'h2', alignment: 'left', color: '', fontSize: '', fontSizeUnit: 'px', fontWeight: '', customClass: '' },
				{ type: 'button',     label: 'Button',       icon: 'fas fa-square',      text: 'Click Me', href: '#', newTab: false, customClass: '', iconType: 'none', iconClass: 'fas fa-arrow-right', iconSrc: '', iconPos: 'end' },
				{ type: 'image',      label: 'Image',        icon: 'far fa-image',       src: 'https://placehold.co/600x400', widthVal: '100', widthUnit: '%', heightVal: '', heightUnit: 'auto', customClass: '' },
				{ type: 'video',      label: 'Video',        icon: 'fas fa-film',        videoType: 'youtube', videoSrc: '', youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', autoplay: false, controls: true, loop: false, muted: false, width: '100', aspectRatio: '16/9', customClass: '' },
				{ type: 'card',       label: 'Card',         icon: 'far fa-id-card',     cardTitle: 'Card Title', cardText: 'Some quick example text.', src: 'https://placehold.co/600x400', btnText: 'Go somewhere', btnLink: '#', newTab: false, cardStyleClass: '', imgWidthVal: '100', imgWidthUnit: '%', imgHeightVal: '', imgHeightUnit: 'auto', btnClass: 'btn-primary', btnIconType: 'none', btnIconClass: '', btnIconSrc: '', btnIconPos: 'end', titleClass: '', textClass: '', imgClass: '', truncTitleMode: 'off', truncTitleLimit: 30, truncTextMode: 'off', truncTextLimit: 100, truncTextLines: 3, customClass: '' },
				{ type: 'media',      label: 'Media Object', icon: 'fas fa-photo-video', src: 'https://placehold.co/120x120', imgClass: '', heading: 'Media Heading', headingClass: '', content: 'Media description text.', contentClass: '', imagePos: 'start', align: 'start', imgWidthVal: '64', imgWidthUnit: 'px', imgHeightVal: '', imgHeightUnit: 'auto', mediaBtnText: '', mediaBtnLink: '', mediaBtnClass: 'btn btn-primary btn-sm', mediaBtnPos: 'below', mediaBtnNewTab: false, truncHeadingMode: 'off', truncHeadingLimit: 30, truncContentMode: 'off', truncContentLimit: 100, truncContentLines: 3, customClass: '' },
				{ type: 'list',       label: 'List Group',   icon: 'fas fa-list',        listType: 'ul', styleType: 'icon', commonIcon: 'fas fa-check text-success', flush: false, noBorder: false, customClass: '', listSize: 'default', customSizeValue: '16', customSizeUnit: 'px', items: [{ text: 'List item 1', active: true, disabled: false, badge: false, badgeText: 'New', badgeType: 'bg-primary', customBadgeCss: '', inputType: 'none', showMarker: true, markerPosition: 'after', inputChecked: false, customCss: '' }, { text: 'List item 2', active: false, disabled: false, badge: true, badgeText: '14', badgeType: 'bg-danger', customBadgeCss: '', inputType: 'none', showMarker: true, markerPosition: 'after', inputChecked: false, customCss: '' }] },
				{ type: 'accordion',  label: 'Accordion',    icon: 'fas fa-layer-group', items: [{ header: 'Accordion Item #1', content: 'Body content 1' }, { header: 'Accordion Item #2', content: 'Body content 2' }], customClass: '' },
				{ type: 'table',      label: 'Table',        icon: 'fas fa-table',       tableData: { headers: ['Head 1', 'Head 2'], rows: [['Data 1', 'Data 2']] }, customClass: '' },
				{ type: 'spacer',     label: 'Spacer',       icon: 'fas fa-arrows-alt-v', height: 50, unit: 'px', customClass: '' },
				{ type: 'divider',    label: 'Divider',      icon: 'fas fa-minus',       style: 'solid', width: 100, widthUnit: '%', align: 'center', color: '#dee2e6', thickness: 1, gap: 15, customClass: '' },
				{ type: 'collapse',   label: 'Collapse',     icon: 'fas fa-layer-group', collapseId: 'collapse-' + Math.random().toString(36).substr(2, 9), triggerType: 'button', triggerText: 'Click to Open', triggerBtnColor: 'btn-primary', customBtnClass: '', content: '<p>Collapse content here...</p>', isOpen: false, direction: 'down', animation: 'fadeIn', customClass: '', customCardClass: '' },
				{ type: 'media_list', label: 'Media List',   icon: 'fas fa-photo-video', viewMode: 'grid', colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6, borderStyle: 'card', roundedCorners: true, textPos: 'below', gap: 3, enableSplide: false, splideAutoplay: true, splideInterval: 3000, splidePerMove: 1, splideType: 'loop', splideDirection: 'ltr', splideArrows: true, splidePagination: true, splideArrowLeftIcon: 'fas fa-chevron-left', splideArrowRightIcon: 'fas fa-chevron-right', imgMode: 'standard', bgHeight: 200, bgSize: 'cover', bgPos: 'center', bgRepeat: 'no-repeat', truncTitleMode: 'off', truncTitleLimit: 30, truncDescMode: 'off', truncDescLimit: 100, truncDescLines: 3, items: [{ type: 'image', src: 'https://placehold.co/600x400', title: 'Image Title 1', desc: 'Description here...', videoType: 'youtube', youtubeUrl: '', videoSrc: '' }, { type: 'image', src: 'https://placehold.co/600x400', title: 'Image Title 2', desc: 'Description here...', videoType: 'youtube', youtubeUrl: '', videoSrc: '' }], customClass: '' },
				{ type: 'nav_tabs',   label: 'Nav Tabs',     icon: 'fas fa-folder-open', navStyle: 'tabs', alignment: 'horizontal', fillJustify: 'none', mobileScroll: false, activeTabId: '', customId: '', customClass: '', navClass: '', navItemClass: '', items: [{ id: 'tab-' + Math.random().toString(36).substr(2,9), label: 'Home', icon: 'fas fa-home', itemType: 'button', url: '#', newTab: false, disabled: false, body: '<p>Tab Home content.</p>', dropdownId: '', dropdownMenuClass: '', dropdownItems: [] }, { id: 'tab-' + Math.random().toString(36).substr(2,9), label: 'Profile', icon: 'fas fa-user', itemType: 'button', url: '#', newTab: false, disabled: false, body: '<p>Tab Profile content.</p>', dropdownId: '', dropdownMenuClass: '', dropdownItems: [] }] }
			],
			advanced: [
				{ type: 'dynamic_post_list', label: 'Dynamic Post List', icon: 'fas fa-newspaper', dataType: 'article', selectedCats: [], selectedStatus: 'publish', viewMode: 'grid', gridCols: 3, colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6, imgMode: 'standard', bgHeight: 200, bgSize: 'cover', bgPos: 'center', bgRepeat: 'no-repeat', imgWidthVal: 250, imgWidthUnit: 'px', imgHeightVal: 200, imgHeightUnit: 'px', showBtn: false, btnText: 'Read More', btnClass: 'btn btn-primary', btnPos: 'below', showDate: true, showCategory: true, showExcerpt: true, paginationType: 'number', usePagination: true, perPage: 6, textPos: 'below', gap: 3, roundedCorners: true, borderStyle: 'card', customClass: '', truncTitleMode: 'off', truncTitleLimit: 60, truncDescMode: 'off', truncDescLimit: 120, truncDescLines: 3, minHeight: 0, enableSplide: false, splideAutoplay: true, splideInterval: 3000, splidePerMove: 1, splideType: 'loop', splideDirection: 'ltr', splideArrows: true, splidePagination: true, splideArrowLeftIcon: 'fas fa-chevron-left', splideArrowRightIcon: 'fas fa-chevron-right' }
			]
		};

		/* ── Filtered Widgets (search) ──────────────────────── */
		const filteredBasicWidgets = computed(() => {
			if (!widgetSearch.value) return tools.widgets;
			return tools.widgets.filter(w => w.label.toLowerCase().includes(widgetSearch.value.toLowerCase()));
		});
		const filteredDynamicWidgets = computed(() => {
			if (!widgetSearch.value) return tools.advanced;
			return tools.advanced.filter(w => w.label.toLowerCase().includes(widgetSearch.value.toLowerCase()));
		});

		/* ── Category Toggle ────────────────────────────────── */
		const toggleCategory = (key) => { openCategories[key] = !openCategories[key]; };
		const togglePropSection = (key) => { openPropSections[key] = !openPropSections[key]; };

		/* ── Theme ──────────────────────────────────────────── */
		const toggleTheme = () => {
			theme.value = theme.value === 'light' ? 'dark' : 'light';
			document.documentElement.setAttribute('data-bs-theme', theme.value);
		};

		/* ── Sidebar ────────────────────────────────────────── */
		const storedSidebar = localStorage.getItem('pb_show_left_sidebar');
		const showLeftSidebar = ref(storedSidebar === 'false' ? false : true);
		const toggleLeftSidebar = () => { showLeftSidebar.value = !showLeftSidebar.value; localStorage.setItem('pb_show_left_sidebar', showLeftSidebar.value); };
		const toggleRightSidebar = () => { showRightSidebar.value = !showRightSidebar.value; };

		/* ── CSS Editor Cookie ──────────────────────────────── */
		watch(cssEditorFullscreen, (val) => {
			const expires = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toUTCString();
			document.cookie = `pb_css_fullscreen=${val}; expires=${expires}; path=/`;
		});

		/* ── Active Element ─────────────────────────────────── */
		const setActive = (item, type) => {
			if (previewMode.value) return;
			activeItem.value = item;
			activeType.value = type;
			showRightSidebar.value = true;
		};
		const deselectAll = () => { /* Keep activeItem for panel */ };

		/* ── History (Undo/Redo) ────────────────────────────── */
		const saveState = () => {
			if (isTimeTraveling.value) return;
			if (historyIndex.value < history.value.length - 1) history.value = history.value.slice(0, historyIndex.value + 1);
			history.value.push(JSON.stringify(layout.value));
			historyIndex.value++;
		};
		let debounceTimer;
		watch(layout, () => {
			if (!isTimeTraveling.value) { clearTimeout(debounceTimer); debounceTimer = setTimeout(saveState, 500); }
		}, { deep: true });
		const undo = () => { if (historyIndex.value > 0) { isTimeTraveling.value = true; historyIndex.value--; layout.value = JSON.parse(history.value[historyIndex.value]); nextTick(() => isTimeTraveling.value = false); } };
		const redo = () => { if (historyIndex.value < history.value.length - 1) { isTimeTraveling.value = true; historyIndex.value++; layout.value = JSON.parse(history.value[historyIndex.value]); nextTick(() => isTimeTraveling.value = false); } };
		const canUndo = computed(() => historyIndex.value > 0);
		const canRedo = computed(() => history.value.length > historyIndex.value + 1);

		/* ── Clone & ID Helpers ─────────────────────────────── */
		const regenerateIds = (obj) => { obj.id = Math.random().toString(36).substr(2, 9); if (obj.children) obj.children.forEach(regenerateIds); };
		const cloneItem = (origin) => {
			const item = JSON.parse(JSON.stringify(origin));
			regenerateIds(item);
			if (item.type === 'container') {
				item.children = [];
				if (!item.classMobile) item.classMobile = 'container-fluid';
				if (!item.classTablet) item.classTablet = 'container-fluid';
				if (!item.classDesktop) item.classDesktop = 'container';
				if (!item.classFHD) item.classFHD = 'container';
				if (!item.class4K) item.class4K = 'container';
				if (!item.styles) item.styles = { bgColor: '#ffffff', minHeight: 'auto', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover' };
			}
			if (item.type === 'row') {
				item.children = item.cols.map(w => {
					let colNum = '';
					if (w.includes('-xl-')) colNum = w.split('-xl-')[1];
					else if (w.includes('-lg-')) colNum = w.split('-lg-')[1];
					else if (w.includes('col-') && w !== 'col') colNum = w.split('col-')[1];
					return { id: Math.random().toString(36).substr(2, 9), type: 'col', widthMobile: 'col-12', widthTablet: colNum ? 'col-md-' + colNum : 'col-md', widthDesktop: w, widthFHD: colNum ? 'col-xxl-' + colNum : 'col-xxl', width4K: colNum ? 'col-3xl-' + colNum : 'col-3xl', children: [], customClass: '' };
				});
				item.gutter = ''; item.gutterTablet = ''; item.gutterDesktop = ''; item.gutterFHD = ''; item.gutter4K = '';
				item.widthMobile = 'w-100'; item.widthTablet = 'w-md-100'; item.widthDesktop = 'w-xl-100'; item.widthFHD = 'w-xxl-100'; item.width4K = 'w-3xl-100';
			}
			if (item.type === 'image' && !item.widthUnit) { item.widthVal = '100'; item.widthUnit = '%'; item.heightVal = ''; item.heightUnit = 'auto'; }
			return item;
		};
		const duplicateItem = (arr, index) => { const clone = JSON.parse(JSON.stringify(arr[index])); regenerateIds(clone); arr.splice(index + 1, 0, clone); };
		const removeItem = (arr, i) => { if (activeItem.value && arr[i] && arr[i].id === activeItem.value.id) { activeItem.value = null; activeType.value = ''; } arr.splice(i, 1); };
		const removeColumn = (row, i) => { if (activeItem.value && row.children[i].id === activeItem.value.id) { activeItem.value = null; activeType.value = ''; } row.children.splice(i, 1); };
		const addColumn = (row) => row.children.push({ id: Math.random().toString(36).substr(2, 9), type: 'col', widthMobile: 'col-12', widthTablet: 'col-md', widthDesktop: 'col-xl', widthFHD: 'col-xxl', width4K: 'col-3xl', children: [], customClass: '' });
		const addNestedRow = (targetCol) => {
			const newRow = { id: Math.random().toString(36).substr(2, 9), type: 'row', cols: [], gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '', widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100', customClass: '', children: [{ id: Math.random().toString(36).substr(2, 9), type: 'col', widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12', children: [], customClass: '' }] };
			targetCol.children.push(newRow);
		};

		/* ── Drop Handlers ──────────────────────────────────── */
		const onDropToContainer = (evt, targetCont) => {
			const addedIndex = evt.newIndex;
			const rawItem = targetCont.children[addedIndex];
			if (!rawItem) return;
			if (rawItem.type !== 'row') {
				const savedWidget = JSON.parse(JSON.stringify(rawItem));
				savedWidget.id = Math.random().toString(36).substr(2, 9);
				const newRow = { id: Math.random().toString(36).substr(2, 9), type: 'row', label: 'Wrapper Row', widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100', gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '', customClass: '', children: [{ id: Math.random().toString(36).substr(2, 9), type: 'col', widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12', customClass: '', children: [savedWidget] }] };
				targetCont.children.splice(addedIndex, 1, newRow);
			} else {
				rawItem.widthMobile = 'w-100'; rawItem.widthTablet = 'w-md-100'; rawItem.widthDesktop = 'w-xl-100'; rawItem.widthFHD = 'w-xxl-100'; rawItem.width4K = 'w-3xl-100';
				rawItem.gutter = ''; rawItem.gutterTablet = ''; rawItem.gutterDesktop = ''; rawItem.gutterFHD = ''; rawItem.gutter4K = '';
				regenerateIds(rawItem);
			}
		};
		const onDropToRow = (evt, targetRow) => {
			const addedIndex = evt.newIndex;
			const addedItem = targetRow.children[addedIndex];
			if (addedItem.type === 'row') { targetRow.children.splice(addedIndex, 1); const newCols = addedItem.children; newCols.forEach(col => regenerateIds(col)); targetRow.children.splice(addedIndex, 0, ...newCols); }
			else if (addedItem.type !== 'col') { const wrapperCol = { id: Math.random().toString(36).substr(2, 9), type: 'col', widthMobile: 'col-12', widthTablet: 'col-md', widthDesktop: 'col-xl', widthFHD: 'col-xxl', width4K: 'col-3xl', children: [addedItem], customClass: '' }; targetRow.children.splice(addedIndex, 1, wrapperCol); }
		};
		const onDropToColumn = (evt, targetCol) => {
			const addedIndex = evt.newIndex;
			const addedItem = targetCol.children[addedIndex];
			if (addedItem && addedItem.type === 'row') { addedItem.widthMobile = 'w-100'; addedItem.widthTablet = 'w-md-100'; addedItem.widthDesktop = 'w-xl-100'; addedItem.widthFHD = 'w-xxl-100'; addedItem.width4K = 'w-3xl-100'; addedItem.gutter = ''; addedItem.gutterTablet = ''; addedItem.gutterDesktop = ''; addedItem.gutterFHD = ''; addedItem.gutter4K = ''; if (!addedItem.id) regenerateIds(addedItem); }
		};

		/* ── Drag Groups ────────────────────────────────────── */
		const containerGroup = { name: 'section', put: (to, from, dragEl) => { if (from.options.group.name === 'widget') return false; if (dragEl.classList.contains('widget-ui')) return false; if (from.options.group.name === 'root') return false; if (dragEl.classList.contains('container-ui')) return false; return true; } };
		const rowGroup = { name: 'row-cols', put: (to, from, dragEl) => { if (from.options.group.name === 'widget') return false; if (dragEl.classList.contains('widget-ui')) return false; if (from.options.group.name === 'root') return false; if (dragEl.classList.contains('container-ui')) return false; return true; } };

		/* ── Widget Picker ──────────────────────────────────── */
		const openWidgetPicker = (targetArray) => { targetWidgetList.value = targetArray; showWidgetModal.value = true; };
		const addWidgetFromPicker = (type) => {
			if (!targetWidgetList.value) return;
			const allWidgets = [...tools.widgets, ...tools.advanced];
			const defaultWidget = allWidgets.find(w => w.type === type);
			if (defaultWidget) { const newWidget = JSON.parse(JSON.stringify(defaultWidget)); newWidget.id = Date.now(); targetWidgetList.value.push(newWidget); }
			showWidgetModal.value = false;
			targetWidgetList.value = null;
		};

		/* ── Quick Add ──────────────────────────────────────── */
		const quickAddContainer = () => {
			layout.value.push({ id: Date.now(), type: 'container', classMobile: 'container-fluid', classTablet: 'container', classDesktop: 'container', classFHD: 'container', class4K: 'container', styles: { bgColor: '', bgImage: '', bgSize: 'cover', bgPos: 'center', bgRepeat: 'no-repeat', minHeight: 'auto' }, customClass: '', children: [] });
			nextTick(() => { const canvas = document.getElementById('canvasArea'); if (canvas) canvas.scrollTop = canvas.scrollHeight; });
		};
		const quickAddRow = (container) => {
			if (!container.children || !Array.isArray(container.children)) container.children = [];
			container.children.push({ id: 'row-' + Date.now(), type: 'row', gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '', widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100', customClass: '', children: [{ id: 'col-' + Date.now(), type: 'col', widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12', customClass: '', children: [] }] });
		};

		/* ── Design Panel Drag ──────────────────────────────── */
		const popupPos = reactive({ top: 80, left: window.innerWidth - 280 });
		let isDraggingPanel = false;
		let dragOffset = { x: 0, y: 0 };
		const startDrag = (e) => {
			if (!isPanelDragging.value) { isPanelDragging.value = true; popupPos.top = 80; popupPos.left = window.innerWidth - 280; }
			isDraggingPanel = true;
			dragOffset.x = e.clientX - popupPos.left;
			dragOffset.y = e.clientY - popupPos.top;
			window.addEventListener('mousemove', onDrag);
			window.addEventListener('mouseup', stopDragPanel);
		};
		const onDrag = (e) => { if (!isDraggingPanel) return; popupPos.left = e.clientX - dragOffset.x; popupPos.top = e.clientY - dragOffset.y; };
		const stopDragPanel = () => { isDraggingPanel = false; window.removeEventListener('mousemove', onDrag); window.removeEventListener('mouseup', stopDragPanel); };

		/* ── Apply Design Panel Spacing/Width/Position ──────── */
		const applySpacing = () => {
			if (!activeItem.value) return;
			const s = designSpacing;
			const u = spacingUnit.value;
			if (!activeItem.value.styles) activeItem.value.styles = {};
			activeItem.value.styles.marginTop = s.marginTop + u;
			activeItem.value.styles.marginRight = s.marginRight + u;
			activeItem.value.styles.marginBottom = s.marginBottom + u;
			activeItem.value.styles.marginLeft = s.marginLeft + u;
			activeItem.value.styles.paddingTop = s.paddingTop + u;
			activeItem.value.styles.paddingRight = s.paddingRight + u;
			activeItem.value.styles.paddingBottom = s.paddingBottom + u;
			activeItem.value.styles.paddingLeft = s.paddingLeft + u;
			const el = document.querySelector('.widget-ui.active, .col-ui.active, .row-ui.active, .container-ui.active');
			if (el) Object.assign(el.style, { marginTop: s.marginTop + u, marginRight: s.marginRight + u, marginBottom: s.marginBottom + u, marginLeft: s.marginLeft + u, paddingTop: s.paddingTop + u, paddingRight: s.paddingRight + u, paddingBottom: s.paddingBottom + u, paddingLeft: s.paddingLeft + u });
		};
		const applyWidth = () => {
			if (!activeItem.value) return;
			if (!activeItem.value.styles) activeItem.value.styles = {};
			if (designWidth.type === 'auto') activeItem.value.styles.width = 'auto';
			else if (designWidth.type === 'full') activeItem.value.styles.width = '100%';
			else activeItem.value.styles.width = designWidth.value + designWidth.unit;
		};
		const applyPosition = () => {
			if (!activeItem.value || !designPosition.type) return;
			if (!activeItem.value.styles) activeItem.value.styles = {};
			activeItem.value.styles.position = designPosition.type;
			activeItem.value.styles[designPosition.hOrient] = designPosition.hValue + designPosition.hUnit;
			activeItem.value.styles[designPosition.vOrient] = designPosition.vValue + designPosition.vUnit;
		};
		const applyZIndex = () => {
			if (!activeItem.value) return;
			if (!activeItem.value.styles) activeItem.value.styles = {};
			activeItem.value.styles.zIndex = designZIndex.value;
		};

		/* ── Preview ────────────────────────────────────────── */
		const togglePreview = () => {
			previewMode.value = !previewMode.value;
			if (previewMode.value) {
				// Enter preview: deselect & hide editing chrome
				activeItem.value = null;
				activeType.value = '';
				showRightSidebar.value = false;
			} else {
				// Exit preview: restore left sidebar
				showLeftSidebar.value = true;
			}
		};

		/* ── CKFinder ───────────────────────────────────────── */
		const openCkFinder = (targetObj, propName) => {
			if (typeof CKFinder !== 'undefined') CKFinder.popup({ chooseFiles: true, onInit: (finder) => finder.on('files:choose', (evt) => { targetObj[propName] = evt.data.files.first().getUrl(); }) });
			else alert('CKFinder not configured');
		};

		/* ── Save ───────────────────────────────────────────── */
		const saveJson = async () => {
			isSaving.value = true;
			const csrfToken = document.querySelector('meta[name="csrf-token"]');
			if (csrfToken) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;

			const payload = { idOrSlug: pageId.value, pageId: pageId.value, pageUri: pageUri.value, pageName: pageName.value, pageStatus: pageStatus.value, layout: JSON.stringify(layout.value), customCss: customCss.value };
			const config = { headers: { 'Content-Type': 'multipart/form-data', 'X-Requested-With': 'XMLHttpRequest' } };

			try {
				const url = (typeof PAGE_MODE !== 'undefined' && PAGE_MODE === 'update' && typeof PAGE_URI !== 'undefined' && PAGE_URI)
					? '/pagebuilder/update/' + PAGE_URI
					: '/pagebuilder/store';
				const response = await axios.post(url, payload, config);
				const status = response.data.status || (response.data.success ? 'success' : 'error');
				showToast(status, response.data.message || 'Saved successfully!');
				if (status === 'success' && response.data.uri && typeof PAGE_MODE !== 'undefined' && PAGE_MODE === 'create') {
					setTimeout(() => window.location.href = '/pagebuilder/edit/' + response.data.uri, 1200);
				}
			} catch (error) {
				const msg = error.response?.data?.message || error.message || 'Save failed.';
				showToast('error', msg);
			} finally {
				isSaving.value = false;
			}
		};

		/* ── CSS Tab helper ─────────────────────────────────── */
		const handleCssTab = (e) => {
			const textarea = e.target;
			const start = textarea.selectionStart;
			const end = textarea.selectionEnd;
			const spaces = '  ';
			customCss.value = customCss.value.substring(0, start) + spaces + customCss.value.substring(end);
			nextTick(() => { textarea.selectionStart = textarea.selectionEnd = start + spaces.length; });
		};

		/* ── List/Accordion/Table Helpers ───────────────────── */
		const addListItem = (w) => w.items.push({ text: 'New Item', active: false, disabled: false, badge: false, badgeText: '', badgeType: 'bg-primary', customBadgeCss: '', inputType: 'none', showMarker: true, markerPosition: 'after', inputChecked: false, customCss: '' });
		const removeListItem = (w, i) => w.items.splice(i, 1);
		const addAccordionItem = (w) => w.items.push({ header: 'New Item', content: 'Content here' });
		const removeAccordionItem = (w, i) => w.items.splice(i, 1);
		const addTableRow = (w) => w.tableData.rows.push(new Array(w.tableData.headers.length).fill(''));
		const addTableCol = (w) => { w.tableData.headers.push('Head'); w.tableData.rows.forEach(r => r.push('')); };
		const removeTableCol = (w, idx) => { if (w.tableData.headers.length <= 1) return; w.tableData.headers.splice(idx, 1); w.tableData.rows.forEach(r => r.splice(idx, 1)); };
		const removeTableRow = (w, idx) => w.tableData.rows.splice(idx, 1);

		/* ── Device width check ─────────────────────────────── */
		const checkDeviceWidth = () => { isMobileDevice.value = window.innerWidth < 768; };

		/* ── Keyboard Shortcuts ─────────────────────────────── */
		const handleKeydown = (e) => {
			if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.contentEditable === 'true') return;
			if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undo(); }
			if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); redo(); }
			if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveJson(); }
		};

		/* ── getCurrentDataType helper ──────────────────────── */
		const getCurrentDataType = (type) => DATA_TYPES.find(d => d.data === type);

		/* ── Lifecycle ──────────────────────────────────────── */
		onMounted(() => {
			checkDeviceWidth();
			window.addEventListener('resize', checkDeviceWidth);
			window.addEventListener('keydown', handleKeydown);

			if (typeof PAGE_MODE !== 'undefined' && PAGE_MODE === 'update' && typeof PAGE_DATA !== 'undefined' && PAGE_DATA !== null) {
				try { pageId.value = PAGE_DATA.id || ''; pageUri.value = PAGE_DATA.uri || ''; pageName.value = PAGE_DATA.page_name || ''; pageStatus.value = PAGE_DATA.status || 'draft'; } catch (e) {}
				try { const rawLayout = PAGE_DATA.layout; layout.value = typeof rawLayout === 'string' ? (rawLayout.trim() !== '' ? JSON.parse(rawLayout) : []) : (Array.isArray(rawLayout) ? rawLayout : []); } catch (e) { console.warn('Layout parse error:', e); layout.value = []; }
				try { customCss.value = PAGE_DATA.custom_css || ''; } catch (e) {}
			}

			saveState();
		});

		onBeforeUnmount(() => {
			window.removeEventListener('resize', checkDeviceWidth);
			window.removeEventListener('keydown', handleKeydown);
		});

		/* ── Return ─────────────────────────────────────────── */
		return {
			// Core
			layout, tools, previewMode, togglePreview, cloneItem, duplicateItem, removeItem, removeColumn,
			addColumn, addNestedRow, saveJson, isSaving, openCkFinder,
			// History
			undo, redo, canUndo, canRedo,
			// Theme
			theme, toggleTheme,
			// State
			baseUrl, activeItem, activeType, setActive, deselectAll, activeViewMode, isMobileDevice,
			// Sidebar
			showLeftSidebar, toggleLeftSidebar, showRightSidebar, toggleRightSidebar,
			sidebarActiveTab, widgetSearch, openCategories, toggleCategory,
			filteredBasicWidgets, filteredDynamicWidgets,
			// Page settings
			pageId, pageUri, pageName, pageStatus,
			// CSS Editor
			customCss, showCssEditor, cssEditorFullscreen, handleCssTab,
			// Design Panel
			designPanelTab, openPropSections, togglePropSection, isPanelDragging,
			spacingUnit, spacingLinked, designSpacing, designWidth, designPosition, designZIndex,
			applySpacing, applyWidth, applyPosition, applyZIndex,
			popupPos, startDrag,
			// Widget Picker
			showWidgetModal, openWidgetPicker, addWidgetFromPicker, targetWidgetList,
			// Quick Add
			quickAddContainer, quickAddRow,
			// Drop handlers
			onDropToContainer, onDropToRow, onDropToColumn, containerGroup, rowGroup,
			// Widget helpers
			addListItem, removeListItem, addAccordionItem, removeAccordionItem,
			addTableRow, addTableCol, removeTableCol, removeTableRow,
			// Toast
			responseStatusToast, responseMessageAfterSubmit, isArrayMessageAfterSubmit,
			// Data types
			dataTypesConfig: DATA_TYPES, getCurrentDataType,
		};
	}

}).use(window.VueSplide || { install() {} }).mount('#pb-app');

})();
