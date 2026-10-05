(function () {
  if (!window.Vue) return;

  const root = document.getElementById('ph-app-workflow-list');
  if (!root) return;

  const initial = window.WorkflowListBootstrap || {};
  const { createApp } = window.Vue;

  const app = createApp({
    data() {
      const params = new URLSearchParams(window.location.search);
      return {
        items: [],
        loading: false,
        loaded: false,
        error: '',
        notice: '',
        search: params.get('search') || '',
        category: params.get('category') || '',
        status: params.get('status') || '',
        page: Number(params.get('page') || 1),
        lastPage: 1,
        total: 0,
      };
    },
    computed: {
      urls() {
        return initial.urls || {};
      },
      permissions() {
        return initial.permissions || {};
      },
      canWrite() {
        return this.permissions.write !== false;
      },
      hasFilters() {
        return Boolean(this.search || this.category || this.status);
      },
    },
    methods: {
      async load(page = 1) {
        if (!this.urls.data) return;
        this.loading = true;
        this.error = '';
        this.page = page;
        try {
          const response = await axios.get(this.urls.data, { params: this.queryParams(page) });
          const payload = response.data || {};
          this.items = Array.isArray(payload.data) ? payload.data : [];
          this.page = Number(payload.current_page || payload.meta?.current_page || page);
          this.lastPage = Number(payload.last_page || payload.meta?.last_page || 1);
          this.total = Number(payload.total || payload.meta?.total || this.items.length);
          this.loaded = true;
          this.syncUrl();
        } catch (error) {
          this.error = this.errorMessage(error, 'Unable to load workflows.');
          this.loaded = true;
        } finally {
          this.loading = false;
        }
      },
      queryParams(page = this.page) {
        return { page, search: this.search || undefined, category: this.category || undefined, status: this.status || undefined };
      },
      applyFilters() {
        this.load(1);
      },
      resetFilters() {
        this.search = '';
        this.category = '';
        this.status = '';
        this.load(1);
      },
      syncUrl() {
        const params = new URLSearchParams();
        Object.entries(this.queryParams(this.page)).forEach(([key, value]) => {
          if (value !== undefined && value !== '') params.set(key, String(value));
        });
        const query = params.toString();
        window.history.replaceState({}, '', `${window.location.pathname}${query ? `?${query}` : ''}`);
      },
      editUrl(item) {
        const base = this.urls.base || this.urls.index || '/workflow';
        return `${String(base).replace(/\/$/, '')}/${encodeURIComponent(item.id)}/edit`;
      },
      createUrl() {
        return this.urls.create || `${String(this.urls.base || '/workflow').replace(/\/$/, '')}/create`;
      },
      statusClass(status) {
        return {
          draft: 'text-bg-secondary',
          published: 'text-bg-success',
          archived: 'text-bg-dark',
        }[status] || 'text-bg-light';
      },
      categoryLabel(category) {
        return { general: 'General', approval: 'Approval', form: 'Form', automation: 'Automation', diagram: 'Diagram' }[category] || category || 'General';
      },
      formatDate(value) {
        if (!value) return '-';
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? String(value) : new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
      },
      errorMessage(error, fallback) {
        const payload = error?.response?.data;
        if (typeof payload?.message === 'string') return payload.message;
        if (payload?.errors && typeof payload.errors === 'object') return Object.values(payload.errors).flat().join(' ');
        return fallback;
      },
    },
    mounted() {
      this.load(this.page);
    },
  });

  window.WorkflowListVue = app.mount(root);
})();
