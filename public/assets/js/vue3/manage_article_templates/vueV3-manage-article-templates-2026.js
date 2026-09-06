const ArticleTemplateUnitControls = (() => {
	const unitSets = {
		spacing: ['px', 'em', 'rem', '%', 'pt'],
		border: ['px', 'em', 'rem', 'pt'],
		radius: ['px', 'em', 'rem', '%', 'pt'],
	};
	const sides = ['top', 'right', 'bottom', 'left'];
	const dimensionPattern = /^(\d+(?:\.\d+)?)(px|em|rem|%|pt)?$/i;

	function units(kind = 'spacing') {
		return unitSets[kind] || unitSets.spacing;
	}

	function max(unit) {
		if (unit === '%') return 100;
		if (unit === 'em' || unit === 'rem') return 30;
		return 400;
	}

	function step(unit) {
		return unit === 'em' || unit === 'rem' ? 0.01 : 1;
	}

	function format(value, unit) {
		const safe = Math.min(max(unit), Math.max(0, Number(value) || 0));
		const rounded = Math.round(safe * 100) / 100;
		return `${String(rounded)}${unit}`;
	}

	function parse(value, fallback = '0px', kind = 'spacing') {
		const fallbackMatch = String(fallback || '0px').trim().match(dimensionPattern);
		const candidate = String(value ?? '').trim().match(dimensionPattern);
		const allowed = units(kind);
		const unit = String(candidate?.[2] || fallbackMatch?.[2] || 'px').toLowerCase();
		const fallbackUnit = allowed.includes(String(fallbackMatch?.[2] || '').toLowerCase())
			? String(fallbackMatch?.[2]).toLowerCase()
			: allowed[0];
		const safeUnit = allowed.includes(unit) ? unit : fallbackUnit;
		const numeric = Number(candidate?.[1] ?? fallbackMatch?.[1] ?? 0);

		return { value: Math.min(max(safeUnit), Math.max(0, Number.isFinite(numeric) ? numeric : 0)), unit: safeUnit };
	}

	function withUnit(value, unit, kind = 'spacing') {
		if (!units(kind).includes(unit)) return format(parse(value, '0px', kind).value, parse(value, '0px', kind).unit);

		return format(parse(value, '0px', kind).value, unit);
	}

	function ensureBox(box, kind = 'spacing') {
		const target = box && typeof box === 'object' ? box : {};
		sides.forEach((side) => {
			target[side] = format(parse(target[side], '0px', kind).value, parse(target[side], '0px', kind).unit);
		});
		return target;
	}

	function setBoxValue(box, side, raw, linked, kind = 'spacing') {
		const target = ensureBox(box, kind);
		if (!sides.includes(side) || raw === '') return target;
		const current = parse(target[side], '0px', kind);
		const value = format(raw, current.unit);

		if (linked) sides.forEach((key) => { target[key] = value; });
		else target[side] = value;

		return target;
	}

	function setBoxUnit(box, unit, kind = 'spacing') {
		const target = ensureBox(box, kind);
		if (!units(kind).includes(unit)) return target;

		sides.forEach((side) => {
			target[side] = format(parse(target[side], '0px', kind).value, unit);
		});

		return target;
	}

	function expandRadiusTokens(tokens) {
		if (tokens.length === 1) return [tokens[0], tokens[0], tokens[0], tokens[0]];
		if (tokens.length === 2) return [tokens[0], tokens[1], tokens[0], tokens[1]];
		if (tokens.length === 3) return [tokens[0], tokens[1], tokens[2], tokens[1]];
		return tokens.slice(0, 4);
	}

	function isValidToken(value, kind = 'radius') {
		const match = String(value || '').trim().match(dimensionPattern);
		return Boolean(match && (!match[2] || units(kind).includes(String(match[2]).toLowerCase())));
	}

	function radiusValues(value, fallback = '0px', kind = 'radius') {
		const fallbackToken = String(fallback || '0px').trim().split(/\s+/)[0] || '0px';
		const rawTokens = String(value ?? '').trim().split(/\s+/).filter(Boolean);
		const tokens = rawTokens.length >= 1 && rawTokens.length <= 4 && rawTokens.every((token) => isValidToken(token, kind))
			? rawTokens
			: [fallbackToken];
		const values = expandRadiusTokens(tokens).map((token) => parse(token, fallbackToken, kind));

		return { values, unit: values[0]?.unit || units(kind)[0] };
	}

	function formatRadius(values, unit, kind = 'radius') {
		const allowed = units(kind);
		const safeUnit = allowed.includes(unit) ? unit : allowed[0];

		return values.slice(0, 4).map((item) => format(item?.value ?? item, safeUnit)).join(' ');
	}

	function setRadiusValue(value, index, raw, linked, kind = 'radius') {
		const parsed = radiusValues(value, '0px', kind);
		const safe = format(raw, parsed.unit);
		const values = parsed.values.map((item) => format(item.value, parsed.unit));

		if (!Number.isInteger(index) || index < 0 || index > 3 || raw === '') return values.join(' ');
		if (linked) return [safe, safe, safe, safe].join(' ');

		values[index] = safe;
		return values.join(' ');
	}

	function setRadiusUnit(value, unit, kind = 'radius') {
		const parsed = radiusValues(value, '0px', kind);
		const safeUnit = units(kind).includes(unit) ? unit : parsed.unit;

		return formatRadius(parsed.values, safeUnit, kind);
	}

	function safeMediaUrl(value) {
		const url = String(value || '').trim();

		if (!url || /["'()\\]/.test(url) || url.startsWith('//') || /^(?:javascript|vbscript|data):/i.test(url)) return '';

		return /^(?:https?:\/\/|\/)/i.test(url) ? url : '';
	}

	return { sides, units, max, step, format, parse, withUnit, ensureBox, setBoxValue, setBoxUnit, radiusValues, formatRadius, setRadiusValue, setRadiusUnit, safeMediaUrl };
})();

const articleTemplateRoot = document.getElementById('ph-app-manage-article-templates');
const articleTemplatePayload = articleTemplateRoot?.dataset.payload ? JSON.parse(articleTemplateRoot.dataset.payload) : {};

const ManageArticleTemplateVue3 = Vue.createApp({
    data() {
        const settings = articleTemplatePayload.settings || {};
        const copy = articleTemplatePayload.copy || {};

        return {
            surface: 'archive',
            device: 'desktop',
            deviceProfiles: {
                desktop: { label: copy.desktop || 'Desktop', width: 1440, height: 900 },
                tablet: { label: copy.tablet || 'Tablet', width: 834, height: 1112 },
                mobile: { label: copy.mobile || 'Mobile', width: 390, height: 844 },
            },
            deviceScale: 1,
            previewGutter: 48,
            previewMaxHeight: 640,
            previewResizeObserver: null,
			previewThemeObserver: null,
			previewThemeColor: '',
            templates: articleTemplatePayload.templates || { archive: {}, detail: {} },
			copy: { archiveHint: '', detailHint: '', previewTitle: 'Article template preview', scaledToFit: 'Scaled to fit', loadingPreview: 'Loading preview…', search: 'Search', categoryFilter: 'Category filter', ...copy },
			saved: {
				archive_template: settings.archive_template || 'minimal-reading-list',
				detail_template: settings.detail_template || 'focused-reader',
				archive_template_options: settings.archive_template_options || {},
				detail_template_options: settings.detail_template_options || {},
			},
            draft: {
                archive_template: settings.archive_template || 'minimal-reading-list',
                detail_template: settings.detail_template || 'focused-reader',
                archive_per_page: Number(settings.archive_per_page || 12),
				archive_template_options: settings.archive_template_options || {},
				detail_template_options: settings.detail_template_options || {},
            },
			optionsModal: { key: null, surface: null, value: null, initialJson: '', section: 'header', view: 'settings', dirty: false, dismissOpen: false },
			optionsDevice: 'desktop',
			optionBoxLinks: {},
			optionsModalTriggerId: null,
			modalPreviewUrl: '',
			modalPreviewLoading: false,
			modalPreviewError: '',
			modalPreviewScale: 1,
			modalPreviewGutter: 32,
			modalPreviewMaxHeight: 620,
			modalPreviewResizeObserver: null,
			modalPreviewTimer: null,
			modalPreviewTimeoutTimer: null,
			modalPreviewRequestSequence: 0,
			boxSides: [
				{ key: 'top', label: 'Top' },
				{ key: 'right', label: 'Right' },
				{ key: 'bottom', label: 'Bottom' },
				{ key: 'left', label: 'Left' },
			],
			radiusCorners: [
				{ key: 'top-left', label: 'Top Left' },
				{ key: 'top-right', label: 'Top Right' },
				{ key: 'bottom-right', label: 'Bottom Right' },
				{ key: 'bottom-left', label: 'Bottom Left' },
			],
			spacingBoxes: [
				{ key: 'padding', label: 'Padding' },
				{ key: 'margin', label: 'Margin' },
			],
            previewBaseUrl: articleTemplateRoot?.dataset.previewBaseUrl || '',
            placeholderThumbnail: articleTemplateRoot?.dataset.placeholderThumbnail || '',
			previewUrl: '',
			previewLoading: true,
			previewLoadTimer: null,
            saveUrl: articleTemplateRoot?.dataset.saveUrl || '',
            saving: false,
            notice: '',
            noticeType: 'success',
        };
    },
    computed: {
        activeTemplates() {
            return this.templates[this.surface] || {};
        },
        activeTemplateKey() {
            return this.surface === 'detail' ? this.draft.detail_template : this.draft.archive_template;
        },
        activeTemplate() {
            return this.activeTemplates[this.activeTemplateKey] || null;
        },
        activeDevice() {
            return this.deviceProfiles[this.device] || this.deviceProfiles.desktop;
        },
        deviceStageStyle() {
            return {
                width: `${Math.round(this.activeDevice.width * this.deviceScale)}px`,
                height: `${Math.round(this.activeDevice.height * this.deviceScale)}px`,
            };
        },
        deviceFrameStyle() {
            return {
                width: `${this.activeDevice.width}px`,
                height: `${this.activeDevice.height}px`,
                transform: `scale(${this.deviceScale})`,
            };
        },
		optionSections() {
			const options = this.optionsModal?.value || {};
			const isArchive = this.surface === 'archive';
			const isMinimal = isArchive && this.activeTemplateKey === 'minimal-reading-list';
			const sections = [{ key: 'header', label: 'Header content', icon: 'fa-align-left' }];

			if (isArchive) sections.push({ key: 'toolbar', label: 'Archive toolbar', icon: 'fa-sliders-h' });
			if (isMinimal && options.post_list) sections.push({ key: 'post-list', label: 'Post list', icon: 'fa-list-ul' });
			if (isMinimal && options.sidebar) sections.push({ key: 'sidebar', label: 'Reading list sidebar', icon: 'fa-columns' });
			if (isArchive && options.grid) sections.push({ key: 'grid', label: 'Grid columns', icon: 'fa-th-large' });
			if (isArchive && this.activeTemplateKey === 'editorial-journal' && options.editorial_journal) sections.push({ key: 'editorial-journal', label: 'Editorial Journal', icon: 'fa-newspaper' });
			if (isArchive) {
				sections.push({ key: 'thumbnail', label: 'Thumbnail', icon: 'fa-image' });
				sections.push({ key: 'pagination', label: 'Pagination', icon: 'fa-ellipsis-h' });
				sections.push({ key: 'article-title', label: 'Article title', icon: 'fa-heading' });
			}

			sections.push({ key: 'shell', label: isArchive ? 'Archive shell' : 'Detail shell', icon: 'fa-square' });

			return sections;
		},
		optionsPreviewDevice() {
			return this.deviceProfiles[this.optionsDevice] || this.deviceProfiles.desktop;
		},
		optionsPreviewStageStyle() {
			return {
				width: `${Math.round(this.optionsPreviewDevice.width * this.modalPreviewScale)}px`,
				height: `${Math.round(this.optionsPreviewDevice.height * this.modalPreviewScale)}px`,
			};
		},
		optionsPreviewFrameStyle() {
			return {
				width: `${this.optionsPreviewDevice.width}px`,
				height: `${this.optionsPreviewDevice.height}px`,
				transform: `scale(${this.modalPreviewScale})`,
			};
		},
    },
	watch: {
		'optionsModal.value': {
			deep: true,
			handler() {
				if (!this.optionsModal.value || !this.optionsModal.initialJson) return;

				this.optionsModal.dirty = JSON.stringify(this.optionsModal.value) !== this.optionsModal.initialJson;
				this.optionsModal.dismissOpen = false;
				this.scheduleModalPreview();
			},
		},
	},
	methods: {
		prepareTemplateOptions(value, surface) {
			const options = value && typeof value === 'object' ? value : {};
			const ensureFrame = (frame, enabled = false) => {
				const target = frame && typeof frame === 'object' ? frame : {};
				if (typeof target.enabled !== 'boolean') target.enabled = enabled;
				if (!target.border_color) target.border_color = '#e1e6ee';
				if (!target.border_width) target.border_width = '1px';
				if (!target.radius) target.radius = '1rem';
				if (!target.background_color) target.background_color = '#ffffff';
				return target;
			};
			const ensureBox = (box) => {
				const target = box && typeof box === 'object' ? box : {};
				if (typeof target.enabled !== 'boolean') target.enabled = false;
				['desktop', 'tablet', 'mobile'].forEach((device) => {
					target[device] = ArticleTemplateUnitControls.ensureBox(target[device], 'spacing');
				});
				return target;
			};
			const ensureRadius = (value, fallback) => {
				const parsed = ArticleTemplateUnitControls.radiusValues(value, fallback, 'radius');

				return ArticleTemplateUnitControls.formatRadius(parsed.values, parsed.unit, 'radius');
			};
			const ensureDimension = (value, fallback, kind = 'spacing') => {
				const parsed = ArticleTemplateUnitControls.parse(value, fallback, kind);

				return ArticleTemplateUnitControls.format(parsed.value, parsed.unit);
			};
			const ensureEditorialJournal = (journal) => {
				const target = journal && typeof journal === 'object' ? journal : {};
				target.lead_grid = target.lead_grid || {};
				target.lead_grid.divider = target.lead_grid.divider || {};
				if (typeof target.lead_grid.divider.enabled !== 'boolean') target.lead_grid.divider.enabled = true;
				target.lead_grid.spacing = target.lead_grid.spacing || {};
				target.lead_grid.spacing.with_divider = ensureDimension(target.lead_grid.spacing.with_divider, '2rem');
				target.lead_grid.spacing.without_divider = ensureDimension(target.lead_grid.spacing.without_divider, '2rem');
				target.thumbnail = target.thumbnail || {};
				if (typeof target.thumbnail.edge_to_edge !== 'boolean') target.thumbnail.edge_to_edge = false;
				target.card = target.card || {};
				target.card.border = target.card.border || {};
				if (typeof target.card.border.enabled !== 'boolean') target.card.border.enabled = true;
				if (!['solid', 'double', 'dotted', 'dashed', 'groove'].includes(target.card.border.type)) target.card.border.type = 'solid';
				target.card.border.width = ensureDimension(target.card.border.width, '1px', 'border');
				if (!target.card.border.color) target.card.border.color = '#e6e9ef';
				target.card.border.radius = ensureRadius(target.card.border.radius, '0.9rem');
				target.card.background = target.card.background || {};
				if (!['color', 'image'].includes(target.card.background.type)) target.card.background.type = 'color';
				if (!target.card.background.color) target.card.background.color = '#ffffff';
				target.card.background.image = ArticleTemplateUnitControls.safeMediaUrl(target.card.background.image);
				target.card.height = target.card.height || {};
				if (!['auto', 'fixed'].includes(target.card.height.mode)) target.card.height.mode = 'auto';
				['desktop', 'tablet', 'mobile'].forEach((device) => {
					target.card.height[device] = ensureDimension(target.card.height[device], '22rem');
				});
				target.read_more = target.read_more || {};
				if (typeof target.read_more.enabled !== 'boolean') target.read_more.enabled = false;
				if (!['left', 'center', 'right'].includes(target.read_more.position)) target.read_more.position = 'left';
				if (!['fas fa-arrow-right', 'fas fa-chevron-right', 'fas fa-angle-right'].includes(target.read_more.icon)) target.read_more.icon = 'fas fa-arrow-right';

				return target;
			};

			options.shell = options.shell || {};
			options.shell.padding = ensureBox(options.shell.padding);
			options.shell.margin = ensureBox(options.shell.margin);
			options.shell.frame = ensureFrame(options.shell.frame);

			if (surface === 'archive') {
				options.toolbar = options.toolbar || {};
				options.toolbar.search = options.toolbar.search || {};
				if (!['attached', 'soft', 'underline'].includes(options.toolbar.search.type)) options.toolbar.search.type = 'attached';
				const searchRadius = ArticleTemplateUnitControls.parse(options.toolbar.search.radius, '0.75rem', 'radius');
				options.toolbar.search.radius = ArticleTemplateUnitControls.format(searchRadius.value, searchRadius.unit);
				const searchGap = ArticleTemplateUnitControls.parse(options.toolbar.search.gap, '0.75rem', 'spacing');
				options.toolbar.search.gap = ArticleTemplateUnitControls.format(searchGap.value, searchGap.unit);
				if (!['fas fa-search', 'fas fa-sliders-h', 'fas fa-arrow-right'].includes(options.toolbar.search.icon)) options.toolbar.search.icon = 'fas fa-search';
				['input_background_color', 'input_text_color', 'input_border_color', 'button_background_color', 'button_text_color', 'button_hover_background_color', 'button_hover_text_color', 'button_active_background_color', 'button_active_text_color'].forEach((key) => {
					if (typeof options.toolbar.search[key] !== 'string') options.toolbar.search[key] = '';
				});
				options.thumbnail = options.thumbnail || {};
				if (!['background', 'asset'].includes(options.thumbnail.mode)) options.thumbnail.mode = 'background';
				if (!['cover', 'contain'].includes(options.thumbnail.fit)) options.thumbnail.fit = 'cover';
				const thumbnailFallback = this.activeTemplateKey === 'minimal-reading-list' ? '9.3rem' : (this.activeTemplateKey === 'editorial-journal' ? '12.5rem' : '5.625rem');
				options.thumbnail.height = ensureDimension(options.thumbnail.height, thumbnailFallback);
				if (!options.thumbnail.background_color) options.thumbnail.background_color = '#f2f4f7';
				options.thumbnail.frame = ensureFrame(options.thumbnail.frame);
				options.pagination = options.pagination || {};
				if (!['underline', 'boxed', 'soft'].includes(options.pagination.type)) options.pagination.type = 'boxed';
				options.pagination.range = options.pagination.range || {};
				const paginationRangeDefaults = { desktop: 3, tablet: 3, mobile: 2 };
				Object.entries(paginationRangeDefaults).forEach(([device, fallback]) => {
					const numeric = Number(options.pagination.range[device]);
					options.pagination.range[device] = Number.isFinite(numeric)
						? Math.min(9, Math.max(1, Math.round(numeric)))
						: fallback;
				});
				const paginationRadius = ArticleTemplateUnitControls.parse(options.pagination.item_radius, '0.45rem', 'radius');
				options.pagination.item_radius = ArticleTemplateUnitControls.format(paginationRadius.value, paginationRadius.unit);
				const paginationGap = ArticleTemplateUnitControls.parse(options.pagination.item_gap, '0.45rem', 'spacing');
				options.pagination.item_gap = ArticleTemplateUnitControls.format(paginationGap.value, paginationGap.unit);
				['item_background_color', 'item_text_color', 'item_border_color', 'item_hover_background_color', 'item_hover_text_color', 'item_active_background_color', 'item_active_text_color'].forEach((key) => {
					if (typeof options.pagination[key] !== 'string') options.pagination[key] = '';
				});
				if (!['fas fa-chevron-left', 'fas fa-angle-left', 'fas fa-arrow-left'].includes(options.pagination.previous_icon)) options.pagination.previous_icon = 'fas fa-chevron-left';
				if (!['fas fa-chevron-right', 'fas fa-angle-right', 'fas fa-arrow-right'].includes(options.pagination.next_icon)) options.pagination.next_icon = 'fas fa-chevron-right';
				if (typeof options.pagination.show_total !== 'boolean') options.pagination.show_total = true;
				if (!['left', 'center', 'right'].includes(options.pagination.position)) options.pagination.position = 'right';
				options.pagination.frame = ensureFrame(options.pagination.frame, true);
				options.pagination.padding = ensureBox(options.pagination.padding);
				options.pagination.margin = ensureBox(options.pagination.margin);
				options.article_title = options.article_title || {};
				if (!['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(options.article_title.tag)) options.article_title.tag = 'h4';
				if (this.activeTemplateKey === 'minimal-reading-list') {
					options.toolbar = options.toolbar || {};
					options.toolbar.category = options.toolbar.category || {};
					if (typeof options.toolbar.category.enabled !== 'boolean') options.toolbar.category.enabled = true;
					if (!['select', 'button-list'].includes(options.toolbar.category.mode)) options.toolbar.category.mode = 'button-list';
					options.sidebar = options.sidebar || {};
					if (typeof options.sidebar.enabled !== 'boolean') options.sidebar.enabled = true;
					options.sidebar.categories = options.sidebar.categories || {};
					if (typeof options.sidebar.categories.enabled !== 'boolean') options.sidebar.categories.enabled = true;
					if (!['static', 'sticky'].includes(options.sidebar.categories.position)) options.sidebar.categories.position = 'static';
					options.sidebar.popular = options.sidebar.popular || {};
					if (typeof options.sidebar.popular.enabled !== 'boolean') options.sidebar.popular.enabled = true;
					if (!['static', 'sticky'].includes(options.sidebar.popular.position)) options.sidebar.popular.position = 'static';
					options.post_list = options.post_list || {};
					const postListGap = ArticleTemplateUnitControls.parse(options.post_list.item_gap, '0.75rem', 'spacing');
					options.post_list.item_gap = ArticleTemplateUnitControls.format(postListGap.value, postListGap.unit);
				}
				if (this.activeTemplateKey === 'editorial-journal') options.editorial_journal = ensureEditorialJournal(options.editorial_journal);
			}

			return options;
		},
		optionPath(path, fallback = null) {
			return String(path).split('.').reduce((value, key) => (value && typeof value === 'object' && key in value ? value[key] : fallback), this.optionsModal.value);
		},
		setOptionPath(path, value) {
			const keys = String(path).split('.');
			let target = this.optionsModal.value;
			keys.slice(0, -1).forEach((key) => {
				if (!target[key] || typeof target[key] !== 'object') target[key] = {};
				target = target[key];
			});
			target[keys.at(-1)] = value;
		},
		unitChoices(kind = 'spacing') {
			return ArticleTemplateUnitControls.units(kind);
		},
		dimensionValue(path, kind = 'spacing') {
			return ArticleTemplateUnitControls.parse(this.optionPath(path, '0px'), '0px', kind).value;
		},
		dimensionUnit(path, kind = 'spacing') {
			return ArticleTemplateUnitControls.parse(this.optionPath(path, '0px'), '0px', kind).unit;
		},
		dimensionMax(path, kind = 'spacing') {
			return ArticleTemplateUnitControls.max(this.dimensionUnit(path, kind));
		},
		dimensionStep(path, kind = 'spacing') {
			return ArticleTemplateUnitControls.step(this.dimensionUnit(path, kind));
		},
		paginationRangeValue(device) {
			const fallback = device === 'mobile' ? 2 : 3;
			const value = Number(this.optionPath(`pagination.range.${device}`, fallback));

			return Number.isFinite(value) ? Math.min(9, Math.max(1, Math.round(value))) : fallback;
		},
		setPaginationRange(device, raw) {
			if (!['desktop', 'tablet', 'mobile'].includes(device) || raw === '') return;

			const value = Number(raw);
			if (!Number.isFinite(value)) return;

			this.setOptionPath(`pagination.range.${device}`, Math.min(9, Math.max(1, Math.round(value))));
		},
		setDimensionValue(path, raw, kind = 'spacing') {
			if (raw === '') return;
			this.setOptionPath(path, ArticleTemplateUnitControls.format(raw, this.dimensionUnit(path, kind)));
		},
		setDimensionUnit(path, unit, kind = 'spacing') {
			this.setOptionPath(path, ArticleTemplateUnitControls.withUnit(this.optionPath(path, '0px'), unit, kind));
		},
		mediaUrl(value) {
			return ArticleTemplateUnitControls.safeMediaUrl(value);
		},
		editorialJournalBackgroundPreviewStyle() {
			const url = this.mediaUrl(this.optionPath('editorial_journal.card.background.image', ''));

			return url ? { backgroundImage: `url("${url.replace(/"/g, '%22')}")` } : {};
		},
		openCkFinder(targetObj, propName) {
			if (!targetObj || !propName) return false;
			const ckf = window.CKFinder;
			if (!ckf || typeof ckf.popup !== 'function') return false;
			const safeKey = String(propName);
			const basePath = new URL('/assets/plugins/ckfinder/', window.location.origin).toString();
			const connectorPath = new URL('/assets/plugins/ckfinder/core/connector/php/connector.php', window.location.origin).toString();
			const setUrl = (url) => {
				targetObj[safeKey] = ArticleTemplateUnitControls.safeMediaUrl(url);
				this.scheduleModalPreview();
			};

			ckf.popup({
				basePath,
				connectorPath,
				chooseFiles: true,
				onInit: (finder) => {
					finder.on('files:choose', (evt) => {
						const file = evt?.data?.files?.first ? evt.data.files.first() : null;
						if (!file || typeof file.getUrl !== 'function') return;
						setUrl(file.getUrl());
					});
					finder.on('file:choose:resizedImage', (evt) => {
						const resizedUrl = evt?.data?.resizedUrl;
						if (resizedUrl) setUrl(resizedUrl);
					});
				},
			});

			return true;
		},
		chooseMedia(targetObj, propName, promptLabel = 'Paste image URL') {
			if (!targetObj || !propName) return;
			if (this.openCkFinder(targetObj, propName)) return;
			const current = ArticleTemplateUnitControls.safeMediaUrl(targetObj[propName]);
			const nextUrl = window.prompt(promptLabel, current);
			if (nextUrl === null) return;
			targetObj[propName] = ArticleTemplateUnitControls.safeMediaUrl(nextUrl);
			this.scheduleModalPreview();
		},
		clearMedia(targetObj, propName) {
			if (!targetObj || !propName) return;
			targetObj[propName] = '';
			this.scheduleModalPreview();
		},
		chooseEditorialJournalBackground() {
			const background = this.optionPath('editorial_journal.card.background', {});
			this.chooseMedia(background, 'image', 'Paste image URL');
		},
		clearEditorialJournalBackground() {
			const background = this.optionPath('editorial_journal.card.background', {});
			this.clearMedia(background, 'image');
		},
		radiusValue(path, index) {
			return ArticleTemplateUnitControls.radiusValues(this.optionPath(path, '1rem'), '1rem', 'radius').values[index]?.value ?? 0;
		},
		radiusUnit(path) {
			return ArticleTemplateUnitControls.radiusValues(this.optionPath(path, '1rem'), '1rem', 'radius').unit;
		},
		radiusMax(path) {
			return ArticleTemplateUnitControls.max(this.radiusUnit(path));
		},
		radiusStep(path) {
			return ArticleTemplateUnitControls.step(this.radiusUnit(path));
		},
		radiusLinkKey(path) {
			return `${this.optionsModal.surface || this.surface}:radius:${path}`;
		},
		isRadiusLinked(path) {
			return this.optionBoxLinks[this.radiusLinkKey(path)] !== false;
		},
		toggleRadiusLinked(path) {
			const key = this.radiusLinkKey(path);
			this.optionBoxLinks[key] = !this.isRadiusLinked(path);
		},
		setRadiusValue(path, index, raw) {
			if (raw === '') return;
			this.setOptionPath(path, ArticleTemplateUnitControls.setRadiusValue(this.optionPath(path, '1rem'), index, raw, this.isRadiusLinked(path), 'radius'));
		},
		setRadiusUnit(path, unit) {
			this.setOptionPath(path, ArticleTemplateUnitControls.setRadiusUnit(this.optionPath(path, '1rem'), unit, 'radius'));
		},
		boxValue(path, device, side, kind = 'spacing') {
			return ArticleTemplateUnitControls.parse(this.optionPath(`${path}.${device}.${side}`, '0px'), '0px', kind).value;
		},
		boxUnit(path, device, kind = 'spacing') {
			return ArticleTemplateUnitControls.parse(this.optionPath(`${path}.${device}.top`, '0px'), '0px', kind).unit;
		},
		boxMax(path, device, kind = 'spacing') {
			return ArticleTemplateUnitControls.max(this.boxUnit(path, device, kind));
		},
		boxStep(path, device, kind = 'spacing') {
			return ArticleTemplateUnitControls.step(this.boxUnit(path, device, kind));
		},
		boxLinkKey(path, device) {
			return `${this.optionsModal.surface || this.surface}:${path}:${device}`;
		},
		isBoxLinked(path, device) {
			return this.optionBoxLinks[this.boxLinkKey(path, device)] !== false;
		},
		toggleBoxLinked(path, device) {
			const key = this.boxLinkKey(path, device);
			this.optionBoxLinks[key] = !this.isBoxLinked(path, device);
		},
		setBoxValue(path, device, side, raw, kind = 'spacing') {
			const box = this.optionPath(`${path}.${device}`, {});
			ArticleTemplateUnitControls.setBoxValue(box, side, raw, this.isBoxLinked(path, device), kind);
			this.setOptionPath(`${path}.${device}`, box);
		},
		setBoxUnit(path, device, unit, kind = 'spacing') {
			const box = this.optionPath(`${path}.${device}`, {});
			ArticleTemplateUnitControls.setBoxUnit(box, unit, kind);
			this.setOptionPath(`${path}.${device}`, box);
		},
		activeTemplateOptions() {
			const options = this.surface === 'detail'
				? this.draft.detail_template_options
				: this.draft.archive_template_options;

			return options?.[this.activeTemplateKey] || null;
		},
		activeThemeColor() {
			if (typeof window === 'undefined' || !document.documentElement || typeof window.getComputedStyle !== 'function') return '';

			const color = window.getComputedStyle(document.documentElement).getPropertyValue('--ph-theme-primary').trim();

			return /^#[0-9a-f]{6}$/i.test(color) ? color : '';
		},
		buildPreviewUrl(surface, template, templateOptions = null, previewDevice = null) {
			const previewUrl = this.previewBaseUrl
				.replace('__SURFACE__', surface)
				.replace('__TEMPLATE__', template);
			const themeColor = this.activeThemeColor?.() || '';
			const params = new URLSearchParams();

			if (themeColor) params.set('theme_color', themeColor);
			if (templateOptions) params.set('template_options', JSON.stringify(templateOptions));
			if (['desktop', 'tablet', 'mobile'].includes(previewDevice)) params.set('preview_device', previewDevice);

			return params.toString() ? `${previewUrl}?${params.toString()}` : previewUrl;
		},
        rebuildPreview() {
			if (this.previewLoadTimer) {
				window.clearTimeout(this.previewLoadTimer);
				this.previewLoadTimer = null;
			}
			this.previewLoading = true;
			const templateOptions = this.activeTemplateOptions?.() || null;
			this.previewThemeColor = this.activeThemeColor?.() || '';
			this.previewUrl = this.buildPreviewUrl(this.surface, this.activeTemplateKey, templateOptions, this.device);
        },
		cloneOptions(value) {
			return JSON.parse(JSON.stringify(value || {}));
		},
		createOptionsSession(value, surface, key) {
			const clone = this.cloneOptions(value);

			return {
				key,
				surface,
				value: clone,
				initialJson: JSON.stringify(clone),
				section: 'header',
				view: 'settings',
				dirty: false,
				dismissOpen: false,
			};
		},
		clearModalPreviewTimers() {
			if (this.modalPreviewTimer) {
				window.clearTimeout(this.modalPreviewTimer);
				this.modalPreviewTimer = null;
			}
			if (this.modalPreviewTimeoutTimer) {
				window.clearTimeout(this.modalPreviewTimeoutTimer);
				this.modalPreviewTimeoutTimer = null;
			}
		},
		scheduleModalPreview() {
			this.clearModalPreviewTimers();
			if (!this.optionsModal?.value) return;

			this.modalPreviewTimer = window.setTimeout(() => {
				this.modalPreviewTimer = null;
				this.rebuildModalPreview();
			}, 350);
		},
		rebuildModalPreview() {
			const modal = this.optionsModal;
			if (!modal?.key || !modal.value) return;

			this.clearModalPreviewTimers();
			this.modalPreviewLoading = true;
			this.modalPreviewError = '';
			this.modalPreviewRequestSequence = Number.isFinite(this.modalPreviewRequestSequence)
				? this.modalPreviewRequestSequence + 1
				: 1;
			this.modalPreviewUrl = this.buildPreviewUrl(modal.surface, modal.key, modal.value, this.optionsDevice);
			const requestId = this.modalPreviewRequestSequence;

			this.modalPreviewTimeoutTimer = window.setTimeout(() => {
				if (requestId !== this.modalPreviewRequestSequence) return;

				this.modalPreviewLoading = false;
				this.modalPreviewError = 'Preview timed out. Try again.';
				this.modalPreviewTimeoutTimer = null;
			}, 8000);
		},
		onModalPreviewLoad(event) {
			const source = event?.target?.getAttribute?.('src') || '';
			if (source && this.modalPreviewUrl && source !== this.modalPreviewUrl) return;
			const framePath = event?.target?.contentWindow?.location?.pathname || '';
			const frameDocument = event?.target?.contentDocument;
			if (framePath === '/auth/login' || frameDocument?.querySelector?.('.ph-app-auth')) {
				this.clearModalPreviewTimers();
				this.modalPreviewLoading = false;
				this.modalPreviewError = 'Preview is unavailable. Check your session and retry.';
				return;
			}

			this.clearModalPreviewTimers();
			this.modalPreviewLoading = false;
			this.modalPreviewError = '';
		},
		onModalPreviewError(event) {
			const source = event?.target?.getAttribute?.('src') || '';
			if (source && this.modalPreviewUrl && source !== this.modalPreviewUrl) return;

			this.clearModalPreviewTimers();
			this.modalPreviewLoading = false;
			this.modalPreviewError = 'Unable to load the preview.';
		},
		retryModalPreview() {
			this.rebuildModalPreview();
		},
		setOptionsSection(section) {
			if (!this.optionsModal?.value || !this.optionSections.some((item) => item.key === section)) return;

			this.optionsModal.section = section;
			this.optionsModal.dismissOpen = false;
			this.$nextTick(() => {
				this.initColorisPicker();
				this.$refs.optionsPanelViewport?.scrollTo?.({ top: 0, left: 0, behavior: 'auto' });
			});
		},
		handleOptionsTabKeydown(event, index) {
			const keys = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1, Home: 'first', End: 'last' };
			const action = keys[event.key];
			if (!action) return;

			event.preventDefault();
			const sections = this.optionSections;
			const nextIndex = action === 'first'
				? 0
				: action === 'last'
					? sections.length - 1
					: (index + action + sections.length) % sections.length;
			const nextSection = sections[nextIndex];
			if (!nextSection) return;

			this.setOptionsSection(nextSection.key);
			this.$nextTick(() => document.getElementById(`article-template-option-tab-${nextSection.key}`)?.focus?.());
		},
		setOptionsView(view) {
			this.optionsModal.view = view === 'preview' ? 'preview' : 'settings';
			if (this.optionsModal.view === 'preview') this.$nextTick(() => this.fitOptionsPreview());
		},
		selectOptionsDevice(device) {
			if (!this.deviceProfiles[device]) return;

			this.optionsDevice = device;
			this.$nextTick(() => {
				this.fitOptionsPreview();
				if (this.optionsModal?.value) this.rebuildModalPreview();
			});
		},
		fitOptionsPreview() {
			const viewportWidth = Number(this.$refs?.optionsPreviewViewport?.clientWidth || 0);
			if (!viewportWidth) return;

			this.modalPreviewScale = Math.min(
				1,
				Math.max(0, viewportWidth - this.modalPreviewGutter) / this.optionsPreviewDevice.width,
				this.modalPreviewMaxHeight / this.optionsPreviewDevice.height,
			);
		},
		onOptionsModalShown() {
			this.fitOptionsPreview();
			this.$nextTick(() => {
				this.initColorisPicker();
				this.fitOptionsPreview();
				this.$refs.optionsPanelViewport?.scrollTo?.({ top: 0, left: 0, behavior: 'auto' });
				document.getElementById(`article-template-option-tab-${this.optionsModal.section}`)?.focus?.();
			});
			if (typeof ResizeObserver === 'undefined' || !this.$refs.optionsPreviewViewport) return;

			this.modalPreviewResizeObserver?.disconnect();
			this.modalPreviewResizeObserver = new ResizeObserver(() => this.fitOptionsPreview());
			this.modalPreviewResizeObserver.observe(this.$refs.optionsPreviewViewport);
		},
		onOptionsModalHidden() {
			const triggerId = this.optionsModalTriggerId;

			this.clearModalPreviewTimers();
			this.modalPreviewResizeObserver?.disconnect();
			this.modalPreviewResizeObserver = null;
			this.modalPreviewUrl = '';
			this.modalPreviewError = '';
			this.optionsModal = { key: null, surface: null, value: null, initialJson: '', section: 'header', view: 'settings', dirty: false, dismissOpen: false };
			this.optionsModalTriggerId = null;
			this.$nextTick(() => triggerId && document.getElementById(triggerId)?.focus?.());
		},
		initColorisPicker() {
			if (typeof window.Coloris !== 'function') return;

			window.Coloris({
				el: '#modalArticleTemplateOptions .article-template-coloris',
				theme: 'pill',
				formatToggle: true,
				closeButton: true,
				clearButton: true,
			});
		},
		scheduleColorisInit() {
			this.$nextTick(() => this.initColorisPicker());
		},
		openTemplateOptions() {
			this.optionsModalTriggerId = document.activeElement?.id || 'article-template-options-trigger';
			const value = this.prepareTemplateOptions(this.cloneOptions(this.activeTemplateOptions()), this.surface);
			this.optionsModal = this.createOptionsSession(value, this.surface, this.activeTemplateKey);
			this.optionsDevice = 'desktop';
			this.optionBoxLinks = {};
			this.$nextTick(() => {
				this.initColorisPicker();
				this.rebuildModalPreview();
				bootstrap.Modal.getOrCreateInstance(document.getElementById('modalArticleTemplateOptions')).show();
			});
		},
		closeTemplateOptions() {
			if (this.optionsModal?.dirty) {
				this.optionsModal.dismissOpen = true;
				return;
			}

			this.discardTemplateOptions();
		},
		requestCloseTemplateOptions() {
			this.closeTemplateOptions();
		},
		keepEditing() {
			if (this.optionsModal) this.optionsModal.dismissOpen = false;
		},
		discardTemplateOptions() {
			const modal = document.getElementById('modalArticleTemplateOptions');
			if (modal && typeof bootstrap !== 'undefined') {
				bootstrap.Modal.getOrCreateInstance(modal).hide();
				return;
			}

			this.onOptionsModalHidden();
		},
		applyTemplateOptions() {
			if (!this.optionsModal.key || !this.optionsModal.value) return;

			const property = this.optionsModal.surface === 'detail' ? 'detail_template_options' : 'archive_template_options';
			this.draft[property][this.optionsModal.key] = this.cloneOptions(this.optionsModal.value);
			this.rebuildPreview();
			this.optionsModal.dirty = false;
			this.optionsModal.dismissOpen = false;
			this.closeTemplateOptions();
		},
		columnChoices(device) {
			return device === 'desktop' ? [1, 2, 3, 4] : (device === 'tablet' ? [1, 2, 3] : [1, 2]);
		},
		syncPreviewTheme() {
			const themeColor = this.activeThemeColor?.() || '';

			if (themeColor === this.previewThemeColor) return;

			this.rebuildPreview();
			if (this.optionsModal?.value) this.rebuildModalPreview();
		},
        onPreviewLoad() {
			if (this.previewLoadTimer) window.clearTimeout(this.previewLoadTimer);
			this.previewLoadTimer = window.setTimeout(() => {
				this.previewLoading = false;
				this.previewLoadTimer = null;
			}, 180);
		},
        setSurface(surface) {
            this.surface = surface === 'detail' ? 'detail' : 'archive';
            this.rebuildPreview();
        },
        selectDevice(device) {
            if (!this.deviceProfiles[device]) return;

            this.device = device;
            this.$nextTick(() => {
				this.fitPreview();
				this.rebuildPreview();
			});
        },
        fitPreview() {
            const viewportWidth = Number(this.$refs?.previewViewport?.clientWidth || 0);

            if (!viewportWidth) return;

            this.deviceScale = Math.min(
                1,
                Math.max(0, viewportWidth - this.previewGutter) / this.activeDevice.width,
                this.previewMaxHeight / this.activeDevice.height,
            );
        },
        selectTemplate(key) {
            if (!this.activeTemplates[key]) return;
            if (this.surface === 'detail') this.draft.detail_template = key;
            else this.draft.archive_template = key;
            this.rebuildPreview();
        },
        isPersistedDefault(key) {
			return this.surface === 'detail'
				? this.saved.detail_template === key
                : this.saved.archive_template === key;
        },
        usePlaceholderThumbnail(event) {
            const image = event?.target;

            if (!image || !this.placeholderThumbnail || image.dataset.placeholderApplied === 'true') return;

            image.dataset.placeholderApplied = 'true';
            image.src = this.placeholderThumbnail;
        },
        async save() {
            this.saving = true;
            this.notice = '';
            try {
                const response = await axios.post(this.saveUrl, this.draft, { headers: { Accept: 'application/json' } });
				const saved = response.data.data || {};
				this.saved.archive_template = saved.archive_template || this.draft.archive_template;
				this.saved.detail_template = saved.detail_template || this.draft.detail_template;
				this.saved.archive_template_options = saved.archive_template_options || this.draft.archive_template_options;
				this.saved.detail_template_options = saved.detail_template_options || this.draft.detail_template_options;
                this.notice = response.data.message || 'Article templates saved successfully';
                this.noticeType = 'success';
            } catch (error) {
                const message = error.response?.data?.message;
                this.notice = typeof message === 'string' ? message : Object.values(message || {}).flat().join(' ') || 'Unable to save article templates';
                this.noticeType = 'danger';
            } finally {
                this.saving = false;
            }
        },
    },
    mounted() {
        this.rebuildPreview();

		if (typeof MutationObserver !== 'undefined' && document.documentElement) {
			this.previewThemeObserver = new MutationObserver(() => this.syncPreviewTheme());
			this.previewThemeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['style'] });
		}

        this.$nextTick(() => {
			const optionsModal = document.getElementById('modalArticleTemplateOptions');
			optionsModal?.addEventListener('shown.bs.modal', this.onOptionsModalShown);
			optionsModal?.addEventListener('hidden.bs.modal', this.onOptionsModalHidden);
            this.fitPreview();

            if (typeof ResizeObserver === 'undefined' || !this.$refs.previewViewport) return;

            this.previewResizeObserver = new ResizeObserver(() => this.fitPreview());
            this.previewResizeObserver.observe(this.$refs.previewViewport);
        });
    },
    beforeUnmount() {
		if (this.previewLoadTimer) window.clearTimeout(this.previewLoadTimer);
		this.clearModalPreviewTimers();
        this.previewResizeObserver?.disconnect();
		this.previewThemeObserver?.disconnect();
		this.modalPreviewResizeObserver?.disconnect();
		const optionsModal = document.getElementById('modalArticleTemplateOptions');
		optionsModal?.removeEventListener('shown.bs.modal', this.onOptionsModalShown);
		optionsModal?.removeEventListener('hidden.bs.modal', this.onOptionsModalHidden);
    },
}).mount('#ph-app-manage-article-templates');

window.ManageArticleTemplateVue3 = ManageArticleTemplateVue3;
