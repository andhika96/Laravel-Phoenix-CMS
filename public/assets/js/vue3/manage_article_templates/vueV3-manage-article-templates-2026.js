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

	return { sides, units, max, step, format, parse, withUnit, ensureBox, setBoxValue, setBoxUnit };
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
			optionsModal: { key: null, surface: null, value: null },
			optionsDevice: 'desktop',
			optionBoxLinks: {},
			boxSides: [
				{ key: 'top', label: 'Top' },
				{ key: 'right', label: 'Right' },
				{ key: 'bottom', label: 'Bottom' },
				{ key: 'left', label: 'Left' },
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

			options.shell = options.shell || {};
			options.shell.padding = ensureBox(options.shell.padding);
			options.shell.margin = ensureBox(options.shell.margin);
			options.shell.frame = ensureFrame(options.shell.frame);

			if (surface === 'archive') {
				options.thumbnail = options.thumbnail || {};
				if (!['background', 'asset'].includes(options.thumbnail.mode)) options.thumbnail.mode = 'background';
				if (!['cover', 'contain'].includes(options.thumbnail.fit)) options.thumbnail.fit = 'cover';
				if (!options.thumbnail.background_color) options.thumbnail.background_color = '#f2f4f7';
				options.thumbnail.frame = ensureFrame(options.thumbnail.frame);
				options.pagination = options.pagination || {};
				if (typeof options.pagination.show_total !== 'boolean') options.pagination.show_total = true;
				if (!['left', 'center', 'right'].includes(options.pagination.position)) options.pagination.position = 'right';
				options.pagination.frame = ensureFrame(options.pagination.frame, true);
				options.pagination.padding = ensureBox(options.pagination.padding);
				options.pagination.margin = ensureBox(options.pagination.margin);
				options.article_title = options.article_title || {};
				if (!['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(options.article_title.tag)) options.article_title.tag = 'h4';
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
		setDimensionValue(path, raw, kind = 'spacing') {
			if (raw === '') return;
			this.setOptionPath(path, ArticleTemplateUnitControls.format(raw, this.dimensionUnit(path, kind)));
		},
		setDimensionUnit(path, unit, kind = 'spacing') {
			this.setOptionPath(path, ArticleTemplateUnitControls.withUnit(this.optionPath(path, '0px'), unit, kind));
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
        rebuildPreview() {
			if (this.previewLoadTimer) {
				window.clearTimeout(this.previewLoadTimer);
				this.previewLoadTimer = null;
			}
			this.previewLoading = true;
            const previewUrl = this.previewBaseUrl
                .replace('__SURFACE__', this.surface)
                .replace('__TEMPLATE__', this.activeTemplateKey);
			const themeColor = this.activeThemeColor?.() || '';
			const templateOptions = this.activeTemplateOptions?.() || null;
			const params = new URLSearchParams();

			if (themeColor) params.set('theme_color', themeColor);
			if (templateOptions) params.set('template_options', JSON.stringify(templateOptions));

			this.previewThemeColor = themeColor;
			this.previewUrl = params.toString() ? `${previewUrl}?${params.toString()}` : previewUrl;
        },
		cloneOptions(value) {
			return JSON.parse(JSON.stringify(value || {}));
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
			this.optionsModal = {
				key: this.activeTemplateKey,
				surface: this.surface,
				value: this.prepareTemplateOptions(this.cloneOptions(this.activeTemplateOptions()), this.surface),
			};
			this.optionsDevice = 'desktop';
			this.optionBoxLinks = {};
			this.$nextTick(() => {
				this.initColorisPicker();
				bootstrap.Modal.getOrCreateInstance(document.getElementById('modalArticleTemplateOptions')).show();
			});
		},
		closeTemplateOptions() {
			bootstrap.Modal.getOrCreateInstance(document.getElementById('modalArticleTemplateOptions')).hide();
			this.optionsModal = { key: null, surface: null, value: null };
		},
		applyTemplateOptions() {
			if (!this.optionsModal.key || !this.optionsModal.value) return;

			const property = this.optionsModal.surface === 'detail' ? 'detail_template_options' : 'archive_template_options';
			this.draft[property][this.optionsModal.key] = this.cloneOptions(this.optionsModal.value);
			this.rebuildPreview();
			this.closeTemplateOptions();
		},
		columnChoices(device) {
			return device === 'desktop' ? [1, 2, 3, 4] : (device === 'tablet' ? [1, 2, 3] : [1, 2]);
		},
		syncPreviewTheme() {
			const themeColor = this.activeThemeColor?.() || '';

			if (themeColor === this.previewThemeColor) return;

			this.rebuildPreview();
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
            this.$nextTick(() => this.fitPreview());
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
            this.fitPreview();

            if (typeof ResizeObserver === 'undefined' || !this.$refs.previewViewport) return;

            this.previewResizeObserver = new ResizeObserver(() => this.fitPreview());
            this.previewResizeObserver.observe(this.$refs.previewViewport);
        });
    },
    beforeUnmount() {
		if (this.previewLoadTimer) window.clearTimeout(this.previewLoadTimer);
        this.previewResizeObserver?.disconnect();
		this.previewThemeObserver?.disconnect();
    },
}).mount('#ph-app-manage-article-templates');

window.ManageArticleTemplateVue3 = ManageArticleTemplateVue3;
