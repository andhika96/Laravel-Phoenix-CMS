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
			copy: { archiveHint: '', detailHint: '', previewTitle: 'Article template preview', scaledToFit: 'Scaled to fit', loadingPreview: 'Loading preview…', ...copy },
			saved: {
				archive_template: settings.archive_template || 'minimal-reading-list',
				detail_template: settings.detail_template || 'focused-reader',
			},
            draft: {
                archive_template: settings.archive_template || 'minimal-reading-list',
                detail_template: settings.detail_template || 'focused-reader',
                archive_per_page: Number(settings.archive_per_page || 12),
            },
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

			this.previewThemeColor = themeColor;
			this.previewUrl = themeColor
				? `${previewUrl}${previewUrl.includes('?') ? '&' : '?'}theme_color=${encodeURIComponent(themeColor)}`
				: previewUrl;
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
