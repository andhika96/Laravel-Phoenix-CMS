@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ t('Manage Event') }}
@endsection

@section('content')
    <div id="ph-app-manage-event" data-list-url="{{ route('cms.core.manage_event.listdata') }}" data-category-url="{{ route('cms.core.manage_event.listdata.category') }}" data-base-url="{{ url('manage_event') }}">
        <div class="mb-3">{{ Breadcrumbs::render('manage_event') }}</div>

        <div class="ph-content rounded p-3 mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <h4 class="mb-0"><i class="fad fa-calendar-star fa-fw me-1"></i> {{ t('Manage Event') }}</h4>
                </div>
                <div class="col-md-7">
                    <div class="row gx-2 gy-2 justify-content-md-end">
                        <div class="col-md-6">
                            <input v-model="search" @input="loadEvents(1)" type="search" class="form-control bg-body-tertiary" placeholder="{{ t('Search event by title') }}" aria-label="{{ t('Search event by title') }}">
                        </div>
                        <div class="col-auto"><a href="{{ route('cms.core.manage_event.add') }}" class="btn ph-btn-theme"><i class="fas fa-plus fa-fw me-1"></i>{{ t('Add Event') }}</a></div>
                        <div class="col-auto"><button type="button" class="btn ph-btn-theme-outline" @click="openCategories"><i class="fas fa-folder fa-fw me-1"></i>{{ t('Event Categories') }}</button></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ph-content rounded p-3 mb-3">
            <div class="row g-2">
                <div class="col-md-4"><select v-model="filters.publication_status" @change="loadEvents(1)" class="form-select" aria-label="{{ t('Publication status') }}"><option value="">{{ t('All publication statuses') }}</option><option value="draft">{{ t('Draft') }}</option><option value="published">{{ t('Published') }}</option><option value="hidden">{{ t('Hidden') }}</option></select></div>
                <div class="col-md-4"><select v-model="filters.visibility" @change="loadEvents(1)" class="form-select" aria-label="{{ t('Visibility') }}"><option value="">{{ t('All visibility') }}</option><option value="public">{{ t('Public') }}</option><option value="private">{{ t('Private') }}</option></select></div>
                <div class="col-md-4"><select v-model="filters.category_id" @change="loadEvents(1)" class="form-select" aria-label="{{ t('Category') }}"><option value="">{{ t('All categories') }}</option><option v-for="category in categories" :key="category.id" :value="category.id">@{{ category.name }}</option></select></div>
            </div>
        </div>

        <div class="ph-content rounded">
            <div v-if="loading" class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><div class="h6 mt-2 mb-0">{{ t('Loading') }}...</div></div>
            <div v-else-if="events.length === 0" class="text-center text-muted p-5">{{ t('No data found') }}</div>
            <div v-else class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>{{ t('Event') }}</th><th>{{ t('Category') }}</th><th>{{ t('Status') }}</th><th>{{ t('Next occurrence') }}</th><th>{{ t('Sessions') }}</th><th class="text-nowrap">{{ t('Options') }}</th></tr></thead>
                    <tbody>
                        <tr v-for="event in events" :key="event.id">
                            <td><div class="d-flex gap-2 align-items-center"><img v-if="event.thumbnail_small_url" :src="event.thumbnail_small_url" alt="" width="48" height="36" class="rounded object-fit-cover"><div><a :href="eventEditUrl(event.id)" class="fw-semibold text-decoration-none">@{{ event.title }}</a><small class="d-block text-muted">/@{{ event.uri }}</small></div></div></td>
                            <td>@{{ event.category || '-' }}</td>
                            <td><span class="badge" :class="statusClass(event.publication_status)">@{{ event.publication_status }}</span><span class="badge text-bg-light ms-1">@{{ event.visibility }}</span></td>
                            <td>@{{ formatDate(event.next_occurrence_at) }}</td>
                            <td>@{{ event.occurrence_count }}</td>
                            <td class="text-nowrap"><a :href="eventEditUrl(event.id)" class="btn btn-sm ph-btn-theme-outline me-1" :aria-label="'{{ t('Edit') }} '+event.title"><i class="fas fa-pencil-alt fa-fw"></i></a><button type="button" class="btn btn-sm btn-outline-danger" @click="openDelete(event)" :aria-label="'{{ t('Delete') }} '+event.title"><i class="fas fa-trash fa-fw"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2"><span>{{ t('Total Data') }}: @{{ total }}</span><paginate v-if="lastPage > 1" :page-count="lastPage" :click-handler="loadEvents" :prev-text="'‹'" :next-text="'›'" :container-class="'pagination ph-pagination m-0'" v-model="page"></paginate></div>
            </div>
        </div>

        <!-- Event Categories: mirror the Manage Article list/create/edit/delete flow. -->
        <Teleport to="body">
            <div class="modal fade" id="eventCategoryListModal" tabindex="-1" aria-labelledby="eventCategoryListModalLabel" aria-hidden="true">
                <div class="modal-dialog ph-modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header align-items-start">
                            <div class="modal-title">
                                <h5 id="eventCategoryListModalLabel" class="mb-1">{{ t('Event Categories') }}</h5>
                                <div class="d-block text-secondary">{{ t('You can manage your categories here') }}</div>
                            </div>
                            <a href="javascript:void(0)" class="text-secondary ms-auto" data-bs-dismiss="modal" aria-label="{{ t('Close') }}"><i class="fal fa-times-circle fs-4"></i></a>
                        </div>

                        <div class="modal-body">
                            <div class="bg-body-secondary p-3 mb-3 rounded">
                                <button type="button" class="btn ph-btn-theme-outline" @click="openCategoryCreate">{{ t('Add New Category') }}</button>
                            </div>

                            <div class="bg-body-secondary p-3 rounded">
                                <div class="fw-bold mb-2">{{ t('Event Categories') }}:</div>
                                <div v-if="categoriesLoading" class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div><div class="small mt-2">{{ t('Loading') }}...</div></div>
                                <div v-else-if="categories.length === 0" class="text-center text-muted p-3">{{ t('No data found') }}</div>
                                <ul v-else class="list-group list-group-flush">
                                    <li v-for="category in categories" :key="category.id" class="list-group-item bg-transparent d-inline-flex d-md-flex align-items-center ps-0">
                                        <i class="fas fa-calendar-star fa-fw me-2"></i>
                                        <span>@{{ category.name }}</span>
                                        <span class="badge ms-2" :class="categoryStatusClass(category.status)">@{{ category.status }}</span>
                                        <span class="ms-auto text-nowrap">
                                            <a href="javascript:void(0)" @click="openCategoryEdit(category)">{{ t('Edit') }}</a>
                                            <a href="javascript:void(0)" class="ms-2" @click="deleteCategory(category)">{{ t('Delete') }}</a>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="modal-footer"><button type="button" class="btn btn-secondary font-size-inherit" data-bs-dismiss="modal">{{ t('Close') }}</button></div>
                    </div>
                </div>
            </div>
        </Teleport>

        <Teleport to="body">
            <div class="modal fade" id="eventCategoryCreateModal" tabindex="-1" aria-labelledby="eventCategoryCreateModalLabel" aria-hidden="true">
                <div class="modal-dialog ph-modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form @submit.prevent="saveCategory">
                            <div class="modal-header"><h5 id="eventCategoryCreateModalLabel" class="modal-title">{{ t('Add New Category') }}</h5></div>
                            <div class="modal-body">
                                <div class="bg-body-secondary p-3 rounded">
                                    <div v-if="categoryModalMessage" class="alert alert-danger mb-3" role="alert">@{{ categoryModalMessage }}</div>
                                    <div class="mb-3"><label for="eventCategoryCreateName" class="form-label">{{ t('Category Name') }}</label><input id="eventCategoryCreateName" v-model="categoryForm.category_name" type="text" class="form-control" required maxlength="64"></div>
                                    <div class="mb-0"><label for="eventCategoryCreateStatus" class="form-label">{{ t('Category Status') }}</label><select id="eventCategoryCreateStatus" v-model="categoryForm.category_status" class="form-select"><option value="active">{{ t('Active') }}</option><option value="inactive">{{ t('Inactive') }}</option><option value="hide">{{ t('Hidden') }}</option></select></div>
                                </div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary font-size-inherit me-2" @click="returnToCategoryList('eventCategoryCreateModal')">{{ t('Cancel') }}</button><button type="submit" class="btn ph-btn-theme font-size-inherit" :disabled="categorySaving"><span v-if="categorySaving" class="spinner-border spinner-border-sm me-1"></span>{{ t('Create') }}</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

        <Teleport to="body">
            <div class="modal fade" id="eventCategoryUpdateModal" tabindex="-1" aria-labelledby="eventCategoryUpdateModalLabel" aria-hidden="true">
                <div class="modal-dialog ph-modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form @submit.prevent="saveCategory">
                            <div class="modal-header"><h5 id="eventCategoryUpdateModalLabel" class="modal-title">{{ t('Edit Category') }}</h5></div>
                            <div class="modal-body">
                                <div class="bg-body-secondary p-3 rounded">
                                    <div v-if="categoryModalMessage" class="alert alert-danger mb-3" role="alert">@{{ categoryModalMessage }}</div>
                                    <div class="mb-3"><label for="eventCategoryUpdateName" class="form-label">{{ t('Category Name') }}</label><input id="eventCategoryUpdateName" v-model="categoryForm.category_name" type="text" class="form-control" required maxlength="64"></div>
                                    <div class="mb-0"><label for="eventCategoryUpdateStatus" class="form-label">{{ t('Category Status') }}</label><select id="eventCategoryUpdateStatus" v-model="categoryForm.category_status" class="form-select"><option value="active">{{ t('Active') }}</option><option value="inactive">{{ t('Inactive') }}</option><option value="hide">{{ t('Hidden') }}</option></select></div>
                                </div>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary font-size-inherit me-2" @click="returnToCategoryList('eventCategoryUpdateModal')">{{ t('Cancel') }}</button><button type="submit" class="btn ph-btn-theme font-size-inherit" :disabled="categorySaving"><span v-if="categorySaving" class="spinner-border spinner-border-sm me-1"></span>{{ t('Save') }}</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

        <div class="modal fade" id="eventDeleteModal" tabindex="-1" aria-labelledby="eventDeleteModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 id="eventDeleteModalLabel" class="modal-title">{{ t('Delete Event') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ t('Close') }}"></button></div><div class="modal-body">{{ t('Do you really want to delete this event?') }} <strong>@{{ selectedEvent?.title }}</strong></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ t('Cancel') }}</button><button type="button" class="btn btn-danger" @click="deleteEvent">{{ t('Delete') }}</button></div></div></div></div>

        <Teleport to="body">
            <div class="modal fade" id="eventCategoryDeleteModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="eventCategoryDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog ph-modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body pt-5 px-5 text-center">
                            <div class="mb-4"><i class="fad fa-trash-alt fs-1 text-danger"></i></div>
                            <div id="eventCategoryDeleteModalLabel" class="h5">{{ t('Delete Category') }}</div>
                            <p class="mb-2"><strong>@{{ selectedCategory?.name }}</strong></p>
                            <div>{{ t('Do you really want to delete this category? This process cannot be undone.') }}</div>
                            <div v-if="categoryModalMessage" class="alert alert-danger mt-3 mb-0 text-start" role="alert">@{{ categoryModalMessage }}</div>
                        </div>
                        <div class="modal-footer pb-5 d-block border-0"><div class="row gx-2 justify-content-center"><div class="col-auto"><button type="button" class="btn btn-secondary font-size-inherit" @click="returnToCategoryList('eventCategoryDeleteModal')">{{ t('No, keep it') }}</button></div><div class="col-auto"><button type="button" class="btn ph-btn-theme font-size-inherit" :disabled="categorySaving" @click="executeDeleteCategory"><span v-if="categorySaving" class="spinner-border spinner-border-sm me-1"></span>{{ t('Yes, Delete') }}</button></div></div></div>
                    </div>
                </div>
            </div>
        </Teleport>

        <div v-if="notice.message" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080"><div class="toast show" role="alert"><div class="toast-header"><strong class="me-auto">{{ t('Notice') }}</strong><button type="button" class="btn-close" @click="notice.message=''" aria-label="{{ t('Close') }}"></button></div><div class="toast-body">@{{ notice.message }}</div></div></div>
    </div>
@endsection

@push('js')
    <script src="{{ url('assets/js/vue3/manage_event/vueV3-manage-event-2026.js?v=').time() }}"></script>
@endpush
