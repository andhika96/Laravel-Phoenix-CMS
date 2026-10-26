from pathlib import Path

root = Path(__file__).resolve().parents[3]
parts = Path(__file__).resolve().parent

def read(path):
    return path.read_bytes().decode('utf-8').replace('\r\n', '\n')

def write(path, value):
    path.write_bytes(value.replace('\r\n', '\n').replace('\n', '\r\n').encode('utf-8'))

view = root / 'resources/views/manage_article/manage_article.blade.php'
source = read(view)
assert source.count('<!-- Delete Article Modal -->') == 1
layout = read(parts / 'list-layout.blade.php')
layout = layout.replace('''                            @foreach ($article_status as $status)
                                <button type="button" class="ph-article-tab" :aria-pressed="getfilterByStatus === @js($status->code_name)" @click="filterByStatus(@js($status->code_name))" :disabled="bulkSubmitting">{{ $status->name }}<span class="ph-article-count">@{{ statusCounts[@js($status->code_name)] || 0 }}</span></button>
                            @endforeach''', '''                            <button type="button" v-for="status in articleStatuses" :key="status.code_name" class="ph-article-tab" :aria-pressed="getfilterByStatus === status.code_name" @click="filterByStatus(status.code_name)" :disabled="bulkSubmitting">@{{ status.name }}<span class="ph-article-count">@{{ statusCounts[status.code_name] || 0 }}</span></button>''')
for old, new in [("'{{ t('Uncategorized') }}'", "'Uncategorized'"), ("'{{ t('Scheduled') }}'", "'Scheduled'"), ("'{{ t('No Scheduled') }}'", "'No Scheduled'"), ("'{{ t('Belum ada ringkasan.') }}'", "'Belum ada ringkasan.'")]:
    layout = layout.replace(old, new)
write(view, layout + '            <!-- Delete Article Modal -->' + source.split('<!-- Delete Article Modal -->', 1)[1])

script = root / 'public/assets/js/vue3/manage_article/vueV3-manage-article-2026.js'
source = read(script)
source = source.replace("getTotalData: '',", "getTotalData: 0,").replace("getCurrentPage: '',", 'getCurrentPage: 1,').replace("loading: '',", 'loading: true,').replace("loadingNextPage: '',", 'loadingNextPage: false,')
source = source.replace('responsiveColDefs: [],', '''selectedArticleIds: [],
            articleStatuses: [],
            statusCounts: {},
            articlePageSize: 15,
            articleSort: 'newest',
            showArticleFilters: false,
            listError: '',
            bulkStatus: '',
            bulkSubmitting: false,
            bulkNotice: '',
            bulkFailed: false,''')
start = source.index('\twatch:')
end = source.index('\tcomponents:', start)
source = source[:start] + '''    computed:
    {
        responsiveVisibleColCount() { return 7 - this.responsiveHiddenCols.length; },
        allArticlesSelected() { return this.responseData.length > 0 && this.responseData.every(info => this.selectedArticleIds.includes(info.id)); },
        someArticlesSelected() { return this.selectedArticleIds.length > 0 && !this.allArticlesSelected; },
        activeArticleFilterCount() { return Number(this.getfilterByCategory !== '') + Number(this.getfilterByScheduled !== ''); },
        articleRangeStart() { return this.getTotalData ? (this.getCurrentPage - 1) * this.articlePageSize + 1 : 0; },
        articleRangeEnd() { return Math.min(this.getCurrentPage * this.articlePageSize, this.getTotalData); },
        bulkStatusLabel() { return this.articleStatuses.find(status => status.code_name === this.bulkStatus)?.name || ''; },
        articleCategoryLabel()
        {
            const select = document.getElementById('ph-article-category');
            return select ? [...select.options].find(option => option.value === String(this.getfilterByCategory))?.textContent || '' : '';
        },
    },
''' + source[end:]
for start, end, filename in [('\t\tlistData: async function()', '\t\tlistDataModal:', 'list-methods.js'), ('\t\tsearchData: _.debounce', '\t\tloadToastComplete:', 'list-controls.js'), ('\t\tclickSelectAll:', '\t\tckeditor5:', 'list-selection.js')]:
    a = source.index(start); b = source.index(end, a)
    source = source[:a] + read(parts / filename) + source[b:]
a = source.index('\t\t// ============================================================\n\t\t// Responsive Table')
b = source.index('\n\t},\n\tmounted()', a)
source = source[:a] + read(parts / 'list-responsive.js').rstrip() + source[b:]
source = source.replace('''\tmounted()
\t{
\t\tthis.listData();''', '''\tmounted()
\t{
        const manager = document.getElementById('ph-app-manage-article');
        this.articleStatuses = JSON.parse(manager?.getAttribute('data-statuses') || '[]');
\t\tthis.listData();''')
write(script, source)
print('Updated Manage Article view and Vue 3 list code; existing article/category modals and add/edit form methods retained.')
