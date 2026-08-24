const ArticleFrontendOptions = {
    data() {
        return { isNavigating: false };
    },
    methods: {
        navigate(event) {
            event.preventDefault();
            const href = event.currentTarget?.href;
            if (!href) return;

            this.isNavigating = true;
            document.getElementById('ph-app-article-frontend')?.setAttribute('aria-busy', 'true');
            window.location.assign(href);
        },
        submitFilter(event) {
            event.preventDefault();
            const form = event.currentTarget;
            const destination = new URL(form.action, window.location.origin);
            const params = new URLSearchParams();

            Array.from(form.elements || []).forEach((field) => {
                const name = String(field.name || '').trim();
                const value = String(field.value || '').trim();
                if (name && value) params.set(name, value);
            });

            this.isNavigating = true;
            document.getElementById('ph-app-article-frontend')?.setAttribute('aria-busy', 'true');
            destination.search = params.toString();
            window.location.assign(destination.toString());
        },
    },
    mounted() {
        document.querySelectorAll('[data-article-filter]').forEach((form) => form.addEventListener('submit', this.submitFilter));
        document.querySelectorAll('[data-article-pagination-link]').forEach((link) => link.addEventListener('click', this.navigate));
    },
};

const ArticleFrontendVue3 = typeof Vue !== 'undefined'
    ? Vue.createApp(ArticleFrontendOptions).mount('#ph-article-vue-bridge')
    : null;

window.ArticleFrontendVue3 = ArticleFrontendVue3;
