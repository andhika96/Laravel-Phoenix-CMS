const ManageEventVue3 = Vue.createApp({
    data() {
        return {
            events: [],
            categories: [],
            search: '',
            filters: { publication_status: '', visibility: '', category_id: '' },
            loading: false,
			categoriesLoading: false,
			categorySaving: false,
			categoryModalMessage: '',
            page: 1,
            lastPage: 1,
            total: 0,
            selectedEvent: null,
            selectedCategory: null,
            categoryForm: { id: '', category_name: '', category_code: '', category_status: 'active' },
            notice: { message: '' },
        };
    },
    components: { paginate: VuejsPaginateNext },
        computed: {
            root() { return document.getElementById('ph-app-manage-event'); },
            listUrl() { return this.root?.dataset.listUrl || ''; },
            categoryUrl() { return this.root?.dataset.categoryUrl || ''; },
            baseUrl() { return this.root?.dataset.baseUrl || '/manage_event'; },
    },
    methods: {
        async loadEvents(page = 1) {
            this.loading = true;
            this.page = page;
            try {
                const response = await axios.get(this.listUrl, { params: { page, search: this.search, ...this.filters } });
                this.events = response.data.data || [];
                this.total = response.data.total || 0;
                this.lastPage = response.data.total_page || 1;
            } catch (error) {
                this.notice.message = error.response?.data?.message || 'Failed to load events';
            } finally {
                this.loading = false;
            }
        },
        async loadCategories() {
			this.categoriesLoading = true;
            try {
                const response = await axios.get(this.categoryUrl, { params: { limit: 100 } });
                this.categories = response.data.data || [];
            } catch (error) {
				this.categoryModalMessage = this.categoryErrorMessage(error, 'Failed to load categories');
                this.notice.message = this.categoryModalMessage;
			} finally {
				this.categoriesLoading = false;
            }
        },
		resetCategoryForm() {
			this.categoryForm = { id: '', category_name: '', category_code: '', category_status: 'active' };
			this.categoryModalMessage = '';
		},
        openCategories() {
			this.resetCategoryForm();
            this.loadCategories();
			bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryListModal')).show();
		},
		openCategoryCreate() {
			this.resetCategoryForm();
			bootstrap.Modal.getInstance(document.getElementById('eventCategoryListModal'))?.hide();
			bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryCreateModal')).show();
		},
		openCategoryEdit(category) {
			this.categoryForm = { id: category.id, category_name: category.name, category_code: category.code || '', category_status: category.status || 'active' };
			this.categoryModalMessage = '';
			bootstrap.Modal.getInstance(document.getElementById('eventCategoryListModal'))?.hide();
			bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryUpdateModal')).show();
		},
        async saveCategory() {
			const isUpdate = Boolean(this.categoryForm.id);
			const modalId = isUpdate ? 'eventCategoryUpdateModal' : 'eventCategoryCreateModal';
            const url = isUpdate ? `${this.baseUrl}/update/category` : `${this.baseUrl}/create/category`;
			this.categorySaving = true;
			this.categoryModalMessage = '';
            try {
                const payload = { ...this.categoryForm, idOrSlug: this.categoryForm.id };
                await axios.post(url, payload);
				this.resetCategoryForm();
                await this.loadCategories();
				bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
				bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryListModal')).show();
				this.notice.message = isUpdate ? 'Category updated successfully' : 'Category created successfully';
            } catch (error) {
				this.categoryModalMessage = this.categoryErrorMessage(error, 'Failed to save category');
			} finally {
				this.categorySaving = false;
            }
        },
        deleteCategory(category) {
            this.selectedCategory = category;
			this.categoryModalMessage = '';
			bootstrap.Modal.getInstance(document.getElementById('eventCategoryListModal'))?.hide();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryDeleteModal')).show();
        },
        async executeDeleteCategory() {
            if (!this.selectedCategory) return;
			this.categorySaving = true;
			this.categoryModalMessage = '';
            try {
                await axios.post(`${this.baseUrl}/delete/category/` + this.selectedCategory.id);
                bootstrap.Modal.getInstance(document.getElementById('eventCategoryDeleteModal'))?.hide();
                this.selectedCategory = null;
                await this.loadCategories();
				bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryListModal')).show();
                this.notice.message = 'Category deleted successfully';
            } catch (error) {
				this.categoryModalMessage = this.categoryErrorMessage(error, 'Failed to delete category');
			} finally {
				this.categorySaving = false;
            }
        },
		returnToCategoryList(modalId) {
			bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
			this.categoryModalMessage = '';
			this.loadCategories();
			bootstrap.Modal.getOrCreateInstance(document.getElementById('eventCategoryListModal')).show();
		},
		categoryErrorMessage(error, fallback) {
			const message = error.response?.data?.message;
			if (typeof message === 'string' && message.trim()) return message;
			if (message && typeof message === 'object') {
				return Object.values(message).flat().join(' ') || fallback;
			}
			return fallback;
		},
        openDelete(event) {
            this.selectedEvent = event;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('eventDeleteModal')).show();
        },
        async deleteEvent() {
            if (!this.selectedEvent) return;
            try {
                await axios.post(`${this.baseUrl}/delete/` + this.selectedEvent.id);
                bootstrap.Modal.getInstance(document.getElementById('eventDeleteModal'))?.hide();
                this.notice.message = 'Event deleted successfully';
                await this.loadEvents(this.page);
            } catch (error) {
                this.notice.message = error.response?.data?.message || 'Failed to delete event';
            }
        },
        eventEditUrl(id) { return `${this.baseUrl}/edit/` + id; },
        formatDate(value) {
            if (!value) return '-';
            return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
        },
        statusClass(status) {
            return { published: 'text-bg-success', draft: 'text-bg-secondary', hidden: 'text-bg-warning' }[status] || 'text-bg-secondary';
        },
		categoryStatusClass(status) {
			return { active: 'text-bg-success', inactive: 'text-bg-secondary', hide: 'text-bg-warning' }[status] || 'text-bg-secondary';
		},
    },
    mounted() {
        this.loadCategories();
        this.loadEvents();
    },
}).mount('#ph-app-manage-event');

window.ManageEventVue3 = ManageEventVue3;
