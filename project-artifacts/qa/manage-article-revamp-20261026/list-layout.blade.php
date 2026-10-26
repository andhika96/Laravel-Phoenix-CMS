@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ t('Manage Article') }}
@endsection

@section('content')
    <link rel="stylesheet" href="{{ url('assets/css/article/manage-article-2026.css') }}?v={{ filemtime(public_path('assets/css/article/manage-article-2026.css')) }}">
    <div>
        <div id="ph-app-manage-article" class="ph-article-manager" data-statuses="{{ $article_status->map->only(['id', 'code_name', 'name'])->values()->toJson() }}">
            <header class="ph-article-header">
                <div><h4>{{ t('Manage Article') }}</h4><p class="ph-article-subtitle">{{ t('Ruang kerja untuk cerita dan ide tim Anda.') }}</p></div>
                <div class="ph-article-header-actions">
                    <a href="{{ route('cms.core.manage_article.add') }}" class="btn ph-btn-theme font-size-inherit"><i class="fas fa-plus fa-fw me-1" aria-hidden="true"></i>{{ t('Add Post') }}</a>
                    <div class="dropdown">
                        <button type="button" class="btn ph-btn-theme-outline font-size-inherit" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ t('Menu pengelolaan artikel') }}"><i class="fas fa-ellipsis-h fa-fw" aria-hidden="true"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="{{ route('cms.core.manage_article.templates') }}" class="dropdown-item"><i class="far fa-layer-group fa-fw me-2" aria-hidden="true"></i>{{ t('Article Templates') }}</a></li>
                            <li><button type="button" class="dropdown-item ar-fetch-detail-data-category-modalRead-categoryList" data-bs-toggle="modal" data-bs-target="#ph-submit-data-category-modalRead" data-url="{{ url('manage_article/listdata/category') }}" @click="showModalCategory('modalRead', 'categoryList', false)"><i class="far fa-folder-open fa-fw me-2" aria-hidden="true"></i>{{ t('Category List') }}</button></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div id="ph-form-submit-data" class="ph-fetch-listdata-article" data-url="{{ route('cms.core.manage_article.listdata') }}">
                <form action="{{ route('cms.core.manage_article.bulk_update') }}" method="post" ref="formHTML" @submit.prevent="openBulkConfirmation">
                    @csrf
                    <div class="ph-content ph-article-board" v-cloak :aria-busy="loadingNextPage || bulkSubmitting">
                        <div class="ph-article-tabs" role="group" aria-label="{{ t('Filter status publikasi') }}">
                            <button type="button" class="ph-article-tab" :aria-pressed="getfilterByStatus === ''" @click="filterByStatus('')" :disabled="bulkSubmitting">{{ t('All') }}<span class="ph-article-count">@{{ statusCounts.all || 0 }}</span></button>
                            @foreach ($article_status as $status)
                                <button type="button" class="ph-article-tab" :aria-pressed="getfilterByStatus === @js($status->code_name)" @click="filterByStatus(@js($status->code_name))" :disabled="bulkSubmitting">{{ $status->name }}<span class="ph-article-count">@{{ statusCounts[@js($status->code_name)] || 0 }}</span></button>
                            @endforeach
                        </div>
                        <div class="ph-article-toolbar">
                            <label class="ph-article-search"><span class="visually-hidden">{{ t('Cari judul artikel') }}</span><i class="far fa-search" aria-hidden="true"></i><input type="search" name="search_article" class="form-control font-size-inherit" placeholder="{{ t('Search article by Title') }}" v-model="getQuerySearchUser" @input="searchData" :disabled="bulkSubmitting"></label>
                            <button type="button" class="btn ph-btn-theme-outline font-size-inherit" @click="showArticleFilters = !showArticleFilters" :aria-expanded="showArticleFilters" aria-controls="ph-article-filters"><i class="far fa-sliders-h fa-fw me-1" aria-hidden="true"></i>{{ t('Filter') }}<span v-if="activeArticleFilterCount"> (@{{ activeArticleFilterCount }})</span></button>
                            <select class="form-select font-size-inherit" v-model="articleSort" @change="refreshArticleList" :disabled="bulkSubmitting" aria-label="{{ t('Urutkan artikel') }}"><option value="newest">{{ t('Terbaru dahulu') }}</option><option value="oldest">{{ t('Terlama dahulu') }}</option><option value="title_asc">{{ t('Judul A-Z') }}</option><option value="title_desc">{{ t('Judul Z-A') }}</option></select>
                        </div>
                        <div id="ph-article-filters" class="ph-article-filter-panel" v-show="showArticleFilters">
                            <div><label for="ph-article-category" class="form-label">{{ t('Category') }}</label><select id="ph-article-category" class="form-select font-size-inherit" v-model="getfilterByCategory" @change="refreshArticleList" :disabled="bulkSubmitting"><option value="">{{ t('All') }}</option>@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                            <div><label for="ph-article-scheduled" class="form-label">{{ t('Scheduled') }}</label><select id="ph-article-scheduled" class="form-select font-size-inherit" v-model="getfilterByScheduled" @change="refreshArticleList" :disabled="bulkSubmitting"><option value="">{{ t('All') }}</option><option value="true">{{ t('Scheduled') }}</option><option value="false">{{ t('No Scheduled') }}</option></select></div>
                            <button type="button" class="btn btn-outline-secondary font-size-inherit" @click="resetArticleFilters" :disabled="bulkSubmitting">{{ t('Hapus filter') }}</button>
                        </div>
                        <div class="ph-article-chips" v-if="activeArticleFilterCount">
                            <button type="button" v-if="getfilterByCategory" class="btn btn-sm btn-outline-secondary" @click="getfilterByCategory = ''; refreshArticleList()" :disabled="bulkSubmitting">@{{ articleCategoryLabel }} <i class="far fa-times ms-1" aria-hidden="true"></i><span class="visually-hidden">{{ t('Hapus filter kategori') }}</span></button>
                            <button type="button" v-if="getfilterByScheduled" class="btn btn-sm btn-outline-secondary" @click="getfilterByScheduled = ''; refreshArticleList()" :disabled="bulkSubmitting">@{{ getfilterByScheduled === 'true' ? '{{ t('Scheduled') }}' : '{{ t('No Scheduled') }}' }} <i class="far fa-times ms-1" aria-hidden="true"></i><span class="visually-hidden">{{ t('Hapus filter jadwal') }}</span></button>
                        </div>
                        <div class="ph-article-bulk" v-if="selectedArticleIds.length">
                            <strong aria-live="polite">@{{ selectedArticleIds.length }} {{ t('artikel dipilih') }} · {{ t('halaman ini') }}</strong>
                            <button type="button" class="btn btn-sm btn-link text-body-secondary" @click="selectedArticleIds = []" :disabled="bulkSubmitting">{{ t('Cancel') }}</button>
                            <select name="changeStatus" class="form-select font-size-inherit" v-model="bulkStatus" :disabled="bulkSubmitting" aria-label="{{ t('Change Status') }}"><option value="">{{ t('Change Status') }}...</option>@foreach ($article_status as $status)<option value="{{ $status->code_name }}">{{ $status->name }}</option>@endforeach</select>
                            <button type="submit" class="btn ph-btn-theme font-size-inherit" :disabled="!bulkStatus || bulkSubmitting || loadingNextPage">{{ t('Terapkan') }}</button>
                        </div>
                        <div v-if="bulkNotice" class="m-3 alert" :class="bulkFailed ? 'alert-danger' : 'alert-success'" role="status">@{{ bulkNotice }}</div>
                        <div v-if="loading" class="ph-article-list-message" role="status"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>{{ t('Loading') }}...</div>
                        <div v-else-if="listError" class="ph-article-list-message" role="alert"><p>@{{ listError }}</p><button type="button" class="btn ph-btn-theme-outline font-size-inherit" @click="listData">{{ t('Coba lagi') }}</button></div>
                        <div v-else>
                            <div v-if="loadingNextPage" class="ph-article-refresh" role="status"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>{{ t('Memperbarui daftar') }}...</div>
                            <div v-if="!responseData.length" class="ph-article-list-message"><h6>{{ t('Artikel tidak ditemukan') }}</h6><p>{{ t('Coba kata kunci lain atau hapus filter aktif.') }}</p><button type="button" class="btn ph-btn-theme-outline font-size-inherit" @click="resetArticleFilters">{{ t('Hapus pencarian dan filter') }}</button></div>
                            <div id="ph-article-table-wrapper" v-show="responseData.length">
                                <table class="table table-hover ph-article-table">
                                    <caption class="visually-hidden">{{ t('Kolom pertama membuka detail, kolom kedua memilih artikel.') }}</caption>
                                    <thead><tr>
                                        <th scope="col" class="ph-article-expand-col" data-col-idx="7"><span class="visually-hidden">{{ t('Detail') }}</span></th>
                                        <th scope="col" class="ph-article-check-col" data-col-idx="0"><label class="ph-article-check-target"><input id="clickSelectAll" type="checkbox" class="form-check-input" :checked="allArticlesSelected" :indeterminate="someArticlesSelected" @change="clickSelectAll($event)" :disabled="loadingNextPage || bulkSubmitting" aria-label="{{ t('Pilih semua artikel di halaman ini') }}"></label></th>
                                        <th scope="col" data-col-idx="1" :aria-sort="articleSort === 'title_asc' ? 'ascending' : (articleSort === 'title_desc' ? 'descending' : 'none')"><button type="button" class="btn btn-link p-0 text-body-secondary text-decoration-none font-size-inherit" @click="sortArticles('title')">{{ t('Title') }} <i class="far fa-sort-alt ms-1" aria-hidden="true"></i></button></th>
                                        <th scope="col" class="ph-article-author-col" data-col-idx="2" v-show="!isColHidden(2)">{{ t('Author') }}</th>
                                        <th scope="col" class="ph-article-status-col" data-col-idx="4" v-show="!isColHidden(4)">{{ t('Status') }}</th>
                                        <th scope="col" class="ph-article-date-col" data-col-idx="5" v-show="!isColHidden(5)" :aria-sort="articleSort === 'newest' ? 'descending' : (articleSort === 'oldest' ? 'ascending' : 'none')"><button type="button" class="btn btn-link p-0 text-body-secondary text-decoration-none font-size-inherit" @click="sortArticles('date')">{{ t('Date') }} <i class="far fa-sort-alt ms-1" aria-hidden="true"></i></button></th>
                                        <th scope="col" class="ph-article-actions-col" data-col-idx="6">{{ t('Options') }}</th>
                                    </tr></thead>
                                    <tbody><template v-for="info in responseData" :key="info.id">
                                        <tr :class="{'ph-dtr-expanded-row': responsiveExpandedRows[info.id], 'ph-article-selected': selectedArticleIds.includes(info.id)}" :data-article-id="info.id">
                                            <td class="ph-article-expand-col"><button type="button" class="ph-article-expand" @click="toggleExpandRow(info.id)" :aria-expanded="!!responsiveExpandedRows[info.id]" :aria-controls="'ph-article-detail-' + info.id" :aria-label="(responsiveExpandedRows[info.id] ? '{{ t('Tutup detail') }} ' : '{{ t('Buka detail') }} ') + info.title_full"><i class="far fa-fw" :class="responsiveExpandedRows[info.id] ? 'fa-chevron-down' : 'fa-chevron-right'" aria-hidden="true"></i></button></td>
                                            <td class="ph-article-check-col"><label class="ph-article-check-target"><input type="checkbox" name="getSelected[]" class="form-check-input checkids" v-model="selectedArticleIds" :value="info.id" :disabled="loadingNextPage || bulkSubmitting" :aria-label="'{{ t('Pilih') }} ' + info.title_full"></label></td>
                                            <td><a :href="'{{ url('manage_article/edit') }}/' + info.id" class="ph-article-title">@{{ info.title_full || info.title }}</a><div class="ph-article-meta">@{{ info.category_name || '{{ t('Uncategorized') }}' }}<span class="ph-article-mobile-author"> · @{{ info.author_full || info.author }}</span></div><div v-if="isColHidden(4)" class="ph-article-mobile-status">@include('manage_article.partials.list-status')<span v-if="isArticleScheduled(info)" class="ph-article-schedule"><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ t('Scheduled') }}</span></div></td>
                                            <td class="ph-article-author-col" v-show="!isColHidden(2)">@{{ info.author_full || info.author }}</td>
                                            <td class="ph-article-status-col" v-show="!isColHidden(4)">@include('manage_article.partials.list-status')<span v-if="isArticleScheduled(info)" class="ph-article-schedule"><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ t('Scheduled') }}</span></td>
                                            <td class="ph-article-date-col" v-show="!isColHidden(5)"><time :datetime="info.created_at">@{{ info.created_date_display }}</time><div class="ph-article-meta">@{{ info.created_time_display }} WIB</div></td>
                                            <td class="ph-article-actions-col"><div class="ph-article-row-actions"><a :href="'{{ url('manage_article/edit') }}/' + info.id" class="btn btn-link text-body-secondary text-decoration-none" :aria-label="'{{ t('Edit') }} ' + info.title_full">{{ t('Edit') }}</a><div class="dropdown"><button type="button" class="btn btn-link text-body-secondary" data-bs-toggle="dropdown" data-bs-boundary="viewport" :aria-label="'{{ t('Options') }} ' + info.title_full" aria-expanded="false"><i class="far fa-ellipsis-h fa-fw" aria-hidden="true"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a :href="'{{ url('article') }}/' + encodeURIComponent(info.uri || info.id)" class="dropdown-item" target="_blank" rel="noopener">{{ t('View article') }}</a></li><li><button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#ph-submit-data-article-modalDelete" :class="'ar-fetch-detail-data-article-modalDelete-' + info.id" data-url="{{ url('manage_article/detaildata') }}" @click="showModal('modalDelete', info.id, true)">{{ t('Delete') }}</button></li></ul></div></div></td>
                                        </tr>
                                        <tr v-if="responsiveExpandedRows[info.id]" class="ph-dtr-child-row" :id="'ph-article-detail-' + info.id"><td :colspan="responsiveVisibleColCount"><section class="ph-article-detail" :aria-label="'{{ t('Detail artikel') }} ' + info.title_full"><div class="ph-article-detail-heading"><span>{{ t('Detail artikel') }}</span><span>#@{{ info.id }}</span></div><div class="ph-article-detail-grid"><div><div class="ph-article-detail-label">{{ t('Ringkasan') }}</div><p>@{{ info.excerpt || '{{ t('Belum ada ringkasan.') }}' }}</p><div class="ph-article-detail-label">{{ t('Alamat artikel') }}</div><p><a :href="'{{ url('article') }}/' + encodeURIComponent(info.uri || info.id)" target="_blank" rel="noopener">/article/@{{ info.uri || info.id }}</a></p></div><dl><div><dt>{{ t('Status publikasi') }}</dt><dd>@include('manage_article.partials.list-status')</dd></div><div><dt>{{ t('Penjadwalan') }}</dt><dd>@{{ isArticleScheduled(info) ? '{{ t('Scheduled') }}' : '{{ t('No Scheduled') }}' }}</dd></div><div><dt>{{ t('Author') }}</dt><dd>@{{ info.author_full || info.author }}</dd></div><div><dt>{{ t('Category') }}</dt><dd>@{{ info.category_name || '{{ t('Uncategorized') }}' }}</dd></div><div class="ph-article-detail-wide"><dt>{{ t('Tanggal dibuat') }}</dt><dd>@{{ info.created_at_readable }} WIB</dd></div></dl></div></section></td></tr>
                                    </template></tbody>
                                </table>
                            </div>
                        </div>
                        <footer class="ph-article-footer"><div class="ph-article-page-info"><span role="status" aria-live="polite">@{{ articleRangeStart }}–@{{ articleRangeEnd }} {{ t('dari') }} @{{ Number(getTotalData).toLocaleString('id-ID') }} {{ t('artikel') }}</span><label class="d-flex align-items-center gap-2"><span class="visually-hidden">{{ t('Artikel per halaman') }}</span><select class="form-select form-select-sm font-size-inherit" v-model.number="articlePageSize" @change="refreshArticleList" :disabled="bulkSubmitting"><option :value="5">5</option><option :value="10">10</option><option :value="15">15</option><option :value="25">25</option><option :value="50">50</option></select><span>/ {{ t('halaman') }}</span></label></div><paginate v-if="pageCount > 1" :page-count="pageCount" :page-range="3" :margin-pages="1" :click-handler="clickPaginate" :prev-text="'<i class=\'far fa-chevron-left\' aria-hidden=\'true\'></i><span class=\'visually-hidden\'>{{ t('Previous page') }}</span>'" :next-text="'<i class=\'far fa-chevron-right\' aria-hidden=\'true\'></i><span class=\'visually-hidden\'>{{ t('Next page') }}</span>'" :container-class="'pagination ph-pagination'" v-model="getCurrentPage"></paginate></footer>
                    </div>
                </form>
            </div>
            <Teleport to="body">
                <div class="modal fade" id="ph-article-bulk-confirm" tabindex="-1" aria-labelledby="ph-article-bulk-title" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered ph-modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="ph-article-bulk-title">{{ t('Change Status') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" :disabled="bulkSubmitting" aria-label="{{ t('Close') }}"></button></div><div class="modal-body"><p>{{ t('Ubah status artikel yang dipilih?') }}</p><strong>@{{ selectedArticleIds.length }} {{ t('artikel') }} → @{{ bulkStatusLabel }}</strong><div v-if="bulkFailed && bulkNotice" class="alert alert-danger mt-3 mb-0" role="alert">@{{ bulkNotice }}</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="bulkSubmitting">{{ t('Cancel') }}</button><button type="button" class="btn ph-btn-theme" @click="submitArticleBulk" :disabled="bulkSubmitting"><span v-if="bulkSubmitting" class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>{{ t('Ya, ubah') }}</button></div></div></div>
                </div>
            </Teleport>

