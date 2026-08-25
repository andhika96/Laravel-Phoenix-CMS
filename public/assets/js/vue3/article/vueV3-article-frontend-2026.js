const ArticleFrontendPaginationControls = (() => {
    const devices = ['desktop', 'tablet', 'mobile'];
    const sides = ['top', 'right', 'bottom', 'left'];

    function parseOptions(value) {
		if (value && typeof value === 'object') return value;

        try {
            return value ? JSON.parse(value) : {};
        } catch {
            return {};
        }
    }

    function box(input) {
        const source = input && typeof input === 'object' ? input : {};
        const result = { enabled: Boolean(source.enabled) };

        devices.forEach((device) => {
            result[device] = {};
            sides.forEach((side) => { result[device][side] = source?.[device]?.[side] || '0px'; });
        });

        return result;
    }

    function normalize(value) {
        const source = parseOptions(value);
        const pagination = source?.pagination && typeof source.pagination === 'object' ? source.pagination : source;
        const frame = pagination.frame && typeof pagination.frame === 'object' ? pagination.frame : {};
        const position = ['left', 'center', 'right'].includes(pagination.position) ? pagination.position : 'right';

        return {
            show_total: pagination.show_total !== false,
            position,
            frame: {
                enabled: pagination?.frame?.enabled !== false,
                border_color: frame.border_color || '#e6e9ef',
                border_width: frame.border_width || '1px',
                radius: frame.radius || '.75rem',
                background_color: frame.background_color || '#ffffff',
            },
            padding: box(pagination.padding),
            margin: box(pagination.margin),
        };
    }

    function style(options) {
        const pagination = normalize({ pagination: options });
        const result = {
            '--article-pagination-frame-border-color': pagination.frame.border_color,
            '--article-pagination-frame-border-width': pagination.frame.border_width,
            '--article-pagination-frame-radius': pagination.frame.radius,
            '--article-pagination-frame-background': pagination.frame.background_color,
        };

        devices.forEach((device) => {
            sides.forEach((side) => {
                result[`--article-pagination-padding-${device}-${side}`] = pagination.padding[device][side];
                result[`--article-pagination-margin-${device}-${side}`] = pagination.margin[device][side];
            });
        });

        return result;
    }

    return { normalize, style };
})();

const articleFrontendRoot = document.getElementById('ph-app-article-frontend');

const ArticleFrontendOptions = {
    data() {
        return {
            currentPage: 1,
            error: '',
            filters: {
                search: '',
                category: '',
                tag: '',
            },
            isHydrated: false,
            isNavigating: false,
            limit: 12,
            listUrl: articleFrontendRoot?.dataset.listUrl || '',
            loadingData: false,
            loadingNextPage: false,
			paginationOptions: ArticleFrontendPaginationControls.normalize(articleFrontendRoot?.dataset.templateOptions),
            paginationCopy: {
                next: articleFrontendRoot?.dataset.paginationNext || 'Next',
                prev: articleFrontendRoot?.dataset.paginationPrev || 'Previous',
            },
            requestSequence: 0,
            total: 0,
            totalPage: 1,
        };
    },
    components: {
        paginate: window.VuejsPaginateNext,
    },
    computed: {
        isLoading() {
            return this.loadingData || this.loadingNextPage;
        },
        firstItem() {
            return this.total ? ((this.currentPage - 1) * this.limit) + 1 : 0;
        },
        lastItem() {
            return this.total ? Math.min(this.currentPage * this.limit, this.total) : 0;
        },
        totalLabel() {
            return Number(this.total || 0).toLocaleString('en-US', { maximumFractionDigits: 0 });
        },
		paginationClasses() {
			return [
				`article-pagination--position-${this.paginationOptions.position}`,
				this.paginationOptions.show_total ? 'article-pagination--with-total' : 'article-pagination--without-total',
				this.paginationOptions.frame.enabled ? 'article-pagination--with-frame' : 'article-pagination--without-frame',
				this.paginationOptions.padding.enabled ? 'article-pagination--padding-enabled' : 'article-pagination--padding-default',
				this.paginationOptions.margin.enabled ? 'article-pagination--margin-enabled' : 'article-pagination--margin-default',
			];
		},
		paginationStyle() {
			return ArticleFrontendPaginationControls.style(this.paginationOptions);
		},
    },
    methods: {
        handleClick(event) {
            if (event.target?.closest?.('[data-article-pagination-link]')) {
                this.navigate(event);
            }
        },
        syncFilterInput(event) {
            const input = event.target;
            if (!input?.closest?.('[data-article-filter]')) return;

            const name = String(input.name || '').trim();
            if (Object.prototype.hasOwnProperty.call(this.filters, name)) {
                this.filters[name] = String(input.value || '');
            }
        },
        handleSubmit(event) {
            if (event.target?.closest?.('[data-article-filter]')) {
                this.submitFilter(event);
            }
        },
        navigate(event) {
            event.preventDefault();
            const link = event.target?.closest?.('[data-article-pagination-link]') || event.currentTarget;
            const href = link?.href;
            if (!href) return;

            this.isNavigating = true;
            this.loadArchive(new URL(href, window.location.origin), 'page');
        },
        submitFilter(event) {
            event.preventDefault();
            const form = event.target?.closest?.('[data-article-filter]') || event.currentTarget;
            if (!form?.action) return;
            const destination = new URL(form.action, window.location.origin);
            const params = new URLSearchParams();

            Object.entries(this.filters).forEach(([name, rawValue]) => {
                const value = String(rawValue || '').trim();
                if (name && value) params.set(name, value);
            });

            this.isNavigating = true;
            destination.search = params.toString();
            this.loadArchive(destination, 'data');
        },
        async fetchList(url, requestId = null) {
            const endpoint = new URL(this.listUrl, window.location.origin);
            endpoint.search = url.search;
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Unable to load articles.');
            }

            if (requestId !== null && requestId !== this.requestSequence) {
                return false;
            }

            this.replaceListHtml(payload.html || '');
            this.isHydrated = true;
            this.total = payload.total || 0;
            this.totalPage = payload.total_page || 1;
            this.currentPage = payload.current_page || 1;
            this.limit = payload.limit || 12;
            return true;
        },
        replaceListHtml(html) {
            const nextDocument = document.implementation.createHTMLDocument('Article list');
            nextDocument.body.innerHTML = html;

            const nextList = nextDocument.body.querySelector('[data-article-vue-list-content]');
            const currentList = articleFrontendRoot?.querySelector('[data-article-vue-list-content]');
            if (!nextList || !currentList) {
                throw new Error('Unable to update article list.');
            }

            currentList.innerHTML = nextList.innerHTML;
            this.syncFilterControls();
        },
        syncFiltersFromUrl(url) {
            const params = new URL(url, window.location.origin).searchParams;
            Object.keys(this.filters).forEach((name) => {
                this.filters[name] = params.get(name) || '';
            });
            this.syncFilterControls();
        },
        syncFilterControls() {
            articleFrontendRoot?.querySelectorAll('[data-article-filter]').forEach((form) => {
                Object.entries(this.filters).forEach(([name, value]) => {
                    const control = form.elements.namedItem(name);
                    if (control && 'value' in control) control.value = value;
                });
            });
        },
        goToPage(page, sourceUrl = window.location.href) {
            const destination = new URL(sourceUrl, window.location.origin);
            if (page > 1) {
                destination.searchParams.set('page', page);
            } else {
                destination.searchParams.delete('page');
            }

            this.loadArchive(destination, 'page');
        },
        handlePopstate() {
            this.loadArchive(new URL(window.location.href), 'data', 'none');
        },
        async loadArchive(url, mode = 'data', historyMode = 'push') {
            const requestId = Number.isFinite(this.requestSequence) ? this.requestSequence + 1 : 1;
            this.requestSequence = requestId;
            this.syncFiltersFromUrl?.(url);
            this.loadingData = mode === 'data';
            this.loadingNextPage = mode === 'page';
            this.error = '';

            try {
                const applied = await this.fetchList(url, requestId);
                if (!applied || requestId !== this.requestSequence) return;

                if (historyMode === 'replace') {
                    window.history.replaceState({}, '', url);
                } else if (historyMode === 'push') {
                    window.history.pushState({}, '', url);
                }
            } catch (error) {
                if (requestId === this.requestSequence) {
                    this.error = error.message || 'Unable to load articles.';
                }
            } finally {
                if (requestId === this.requestSequence) {
                    this.loadingData = false;
                    this.loadingNextPage = false;
                    this.isNavigating = false;
                }
            }
        },
    },
    mounted() {
        this.loadArchive(new URL(window.location.href), 'data', 'replace');
        window.addEventListener('popstate', this.handlePopstate);
    },
    beforeUnmount() {
        window.removeEventListener('popstate', this.handlePopstate);
    },
};

const articlePasswordRoot = document.getElementById('ph-article-password-gate');
const ArticlePasswordGateOptions = {
    data() {
        return {
            error: articlePasswordRoot?.dataset.initialError || '',
            isSubmitting: false,
            password: '',
            showPassword: false,
            unlockUrl: articlePasswordRoot?.dataset.unlockUrl || '',
        };
    },
    methods: {
        async unlock(event) {
            event.preventDefault();
            this.error = '';
            this.isSubmitting = true;

            try {
                const response = await fetch(this.unlockUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: new URLSearchParams({ password: this.password }),
                });
                const payload = await response.json();

                if (!response.ok || !payload.success) {
                    this.error = payload.message || 'The password is incorrect. Please try again.';
                    return;
                }

                window.location.assign(payload.redirect);
            } catch {
                this.error = 'Unable to unlock this article. Please try again.';
            } finally {
                this.isSubmitting = false;
            }
        },
    },
};

const ArticleFrontendVue3 = typeof Vue !== 'undefined' && articleFrontendRoot
    ? Vue.createApp(ArticleFrontendOptions).mount('#ph-app-article-frontend')
    : null;

const ArticlePasswordGateVue3 = typeof Vue !== 'undefined' && articlePasswordRoot
    ? Vue.createApp(ArticlePasswordGateOptions).mount('#ph-article-password-gate')
    : null;

window.ArticleFrontendVue3 = ArticleFrontendVue3;
window.ArticlePasswordGateVue3 = ArticlePasswordGateVue3;
