        clickSelectAll: function(event)
        {
            this.selectedArticleIds = event.target.checked ? this.responseData.map(info => info.id) : [];
        },
        clickPaginate: function(page)
        {
            if (this.bulkSubmitting) return;
            this.getPage = page;
            return this.listData();
        },
        isArticleScheduled: function(info)
        {
            return info.scheduled === true || info.scheduled === 'true' || info.scheduled === 1 || info.scheduled === '1';
        },
        openBulkConfirmation: function()
        {
            if (!this.selectedArticleIds.length || !this.bulkStatus || this.bulkSubmitting || this.loadingNextPage) return;
            this.bulkNotice = '';
            this.bulkFailed = false;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('ph-article-bulk-confirm')).show();
        },
        submitArticleBulk: async function()
        {
            if (!this.selectedArticleIds.length || !this.bulkStatus || this.bulkSubmitting) return;
            this.bulkSubmitting = true;
            this.bulkNotice = '';
            this.bulkFailed = false;
            const form = new FormData(this.$refs.formHTML);
            form.set('mode', 'bulk_update');
            try
            {
                const response = await axios.post(this.$refs.formHTML.action, form, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } });
                if (response.data.status !== 'success') throw new Error(response.data.message || 'Perubahan status gagal.');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('ph-article-bulk-confirm')).hide();
                this.bulkNotice = response.data.message;
                this.bulkStatus = '';
                await this.listData();
            }
            catch (error)
            {
                this.bulkFailed = true;
                this.bulkNotice = error.response?.data?.message || error.message || 'Perubahan status gagal. Silakan coba lagi.';
            }
            finally
            {
                this.bulkSubmitting = false;
            }
        },

