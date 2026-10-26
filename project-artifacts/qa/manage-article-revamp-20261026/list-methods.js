        listData: async function()
        {
            const source = document.querySelector('.ph-fetch-listdata-article');
            if (!source) return;
            const requestId = this._articleRequestId = (this._articleRequestId || 0) + 1;
            const params = new URLSearchParams({ page: this.getPage, per_page: this.articlePageSize, sort: this.articleSort });
            for (const [key, value] of Object.entries({ search: this.getQuerySearchUser.trim(), filter_by_status: this.getfilterByStatus, filter_by_category: this.getfilterByCategory, filter_by_scheduled: this.getfilterByScheduled }))
            {
                if (value !== '') params.set(key, value);
            }
            this.loading = !this._articleListLoaded;
            this.loadingNextPage = true;
            this.listError = '';
            this.selectedArticleIds = [];
            this.responsiveExpandedRows = {};
            try
            {
                const response = await axios.get(source.getAttribute('data-url'), { params });
                if (requestId !== this._articleRequestId) return;
                const data = response.data;
                if (!Array.isArray(data.data)) throw new Error('Invalid article list response');
                const lastPage = Math.max(1, Number(data.total_page) || 1);
                if (this.getPage > lastPage)
                {
                    this.getPage = lastPage;
                    return this.listData();
                }
                this.responseData = data.data;
                this.getTotalData = Number(data.total) || 0;
                this.getCurrentPage = Number(data.current_page) || 1;
                this.getPage = this.getCurrentPage;
                this.pageCount = lastPage;
                this.statusCounts = data.status_counts || { all: this.getTotalData };
                this.responseStatus = data.status;
                this.responseMessage = data.message;
                this._articleListLoaded = true;
            }
            catch (error)
            {
                if (requestId !== this._articleRequestId) return;
                this.listError = error.response?.data?.message || 'Daftar artikel gagal dimuat. Silakan coba lagi.';
            }
            finally
            {
                if (requestId === this._articleRequestId) this.loadDataComplete();
            }
        },

