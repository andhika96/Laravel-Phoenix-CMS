        getTableWidth: function()
        {
            const wrapper = document.getElementById('ph-article-table-wrapper');
            return wrapper ? wrapper.clientWidth : 0;
        },
        recalcResponsive: function()
        {
            const width = this.getTableWidth();
            if (!width) return;
            this.responsiveHiddenCols = [
                ...(width <= 940 ? [2] : []),
                ...(width <= 740 ? [5] : []),
                ...(width <= 600 ? [4] : []),
            ];
        },
        toggleExpandRow: function(id)
        {
            this.responsiveExpandedRows = { ...this.responsiveExpandedRows, [id]: !this.responsiveExpandedRows[id] };
        },
        isColHidden: function(idx)
        {
            return this.responsiveHiddenCols.includes(idx);
        },
