        searchData: _.debounce(function()
        {
            this.refreshArticleList();
        }, 300),
        refreshArticleList: function()
        {
            if (this.bulkSubmitting) return;
            this.getPage = 1;
            this.getCurrentPage = 1;
            return this.listData();
        },
        filterByStatus: function(value)
        {
            if (this.bulkSubmitting) return;
            this.getfilterByStatus = typeof value === 'string' ? value : value.target.value;
            return this.refreshArticleList();
        },
        resetArticleFilters: function()
        {
            if (this.bulkSubmitting) return;
            this.getQuerySearchUser = '';
            this.getfilterByStatus = '';
            this.getfilterByCategory = '';
            this.getfilterByScheduled = '';
            return this.refreshArticleList();
        },
        sortArticles: function(column)
        {
            if (this.bulkSubmitting) return;
            this.articleSort = column === 'title'
                ? (this.articleSort === 'title_asc' ? 'title_desc' : 'title_asc')
                : (this.articleSort === 'newest' ? 'oldest' : 'newest');
            return this.refreshArticleList();
        },
        loadDataComplete: function()
        {
            this.loading = false;
            this.loadingNextPage = false;
            this.$nextTick(() =>
            {
                const wrapper = document.getElementById('ph-article-table-wrapper');
                if (!wrapper) return;
                if (this._responsiveWrapper !== wrapper)
                {
                    this._resizeObserver?.disconnect();
                    this._responsiveWrapper = wrapper;
                    if (typeof ResizeObserver !== 'undefined')
                    {
                        this._resizeObserver = new ResizeObserver(() => this.recalcResponsive());
                        this._resizeObserver.observe(wrapper);
                    }
                    else if (!this._responsiveResizeHandler)
                    {
                        this._responsiveResizeHandler = () => this.recalcResponsive();
                        window.addEventListener('resize', this._responsiveResizeHandler);
                    }
                }
                this.recalcResponsive();
            });
        },

