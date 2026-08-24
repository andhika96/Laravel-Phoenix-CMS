const EventVue3 = Vue.createApp({
    data() {
        const root = document.getElementById('ph-app-event');
        return { root, listUrl: root?.dataset.listUrl || '', detailBaseUrl: root?.dataset.detailBaseUrl || '/event', events: [], search: '', loading: false, page: 1, lastPage: 1 };
    },
    components: { paginate: VuejsPaginateNext },
    methods: {
        async loadEvents(page = 1) {
            this.loading = true;
            this.page = page;
            try {
                const response = await axios.get(this.listUrl, { params: { page, search: this.search } });
                this.events = response.data.data || [];
                this.lastPage = response.data.total_page || 1;
            } finally {
                this.loading = false;
            }
        },
        detailUrl(uri) { return this.detailBaseUrl + '/' + encodeURIComponent(uri); },
        formatDate(value) { return value ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)) : 'Schedule not set'; },
        summaryText(value) {
            return new DOMParser().parseFromString(String(value || ''), 'text/html').body.textContent || '';
        },
    },
    mounted() { this.loadEvents(); },
}).mount('#ph-app-event');

window.EventVue3 = EventVue3;
