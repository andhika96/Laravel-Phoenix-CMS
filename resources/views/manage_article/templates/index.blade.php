@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ t('Manage Article Templates') }}
@endsection

@section('content')
    <div id="ph-app-manage-article-templates"
        data-save-url="{{ route('cms.core.manage_article.templates.update') }}"
        data-preview-base-url="{{ url('manage_article/templates/preview/__SURFACE__/__TEMPLATE__') }}"
        data-placeholder-thumbnail="{{ asset('assets/images/article/article-image-placeholder.svg') }}"
        data-payload="{{ json_encode(['settings' => ['archive_template' => $settings->archive_template, 'detail_template' => $settings->detail_template, 'archive_per_page' => (int) $settings->archive_per_page, 'archive_template_options' => $archiveTemplateOptions, 'detail_template_options' => $detailTemplateOptions], 'templates' => ['archive' => $archiveTemplates, 'detail' => $detailTemplates], 'copy' => ['archiveHint' => t('Choose an archive template to manage and preview.'), 'detailHint' => t('Choose a detail template to manage and preview.'), 'previewTitle' => t('Article template preview'), 'desktop' => t('Desktop'), 'tablet' => t('Tablet'), 'mobile' => t('Mobile'), 'scaledToFit' => t('Scaled to fit'), 'loadingPreview' => t('Loading preview'), 'search' => t('Search'), 'categoryFilter' => t('Category filter')]]) }}">
        <div class="mb-3">{{ Breadcrumbs::render('manage_article.templates') }}</div>
        <section class="article-template-manager ph-content rounded">
            <header class="article-template-manager__header"><div><h4>{{ t('Manage Article Templates') }}</h4><p>{{ t('Choose how article archive and detail pages are displayed.') }}</p></div><div class="article-template-manager__header-actions"><button type="button" class="btn ph-btn-theme-outline" @click="openTemplateOptions"><i class="fas fa-sliders-h me-1" aria-hidden="true"></i>{{ t('Template Options') }}</button><button type="button" class="btn ph-btn-theme" :disabled="saving" @click="save"><span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>{{ t('Save Template') }}</button></div></header>
            <div v-if="notice" class="alert" :class="noticeType === 'success' ? 'alert-success' : 'alert-danger'" role="status">@{{ notice }}</div>
            <div class="article-template-manager__body">
                <aside class="article-template-manager__picker">
                    <div class="article-template-manager__tabs" role="tablist"><button type="button" :class="{ active: surface === 'archive' }" @click="setSurface('archive')">{{ t('Archive Templates') }}</button><button type="button" :class="{ active: surface === 'detail' }" @click="setSurface('detail')">{{ t('Detail Templates') }}</button></div>
                    <p class="article-template-manager__hint"><span v-if="surface === 'archive'">@{{ copy.archiveHint }}</span><span v-else>@{{ copy.detailHint }}</span></p>
                    <div class="article-template-manager__cards">
                        <button v-for="(template, key) in activeTemplates" :key="key" type="button" class="article-template-card" :class="{ selected: activeTemplateKey === key }" @click="selectTemplate(key)">
                            <img class="article-template-card__thumbnail" :src="template.preview_image" :alt="template.label + ' preview'" v-on:error="usePlaceholderThumbnail">
                            <span class="article-template-card__content"><strong>@{{ template.label }}</strong><small>@{{ template.description }}</small><span class="article-template-card__best-for">{{ t('Best for') }}: @{{ template.best_for }}</span><em v-if="isPersistedDefault(key)">{{ t('Default') }}</em><em v-else-if="activeTemplateKey === key">{{ t('Selected') }}</em></span>
                            <i v-if="activeTemplateKey === key" class="fas fa-check-circle article-template-card__check"></i>
                        </button>
                    </div>
                    <label v-if="surface === 'archive'" class="article-template-manager__per-page">{{ t('Articles per page') }}<select v-model.number="draft.archive_per_page"><option :value="12">12</option><option :value="18">18</option><option :value="24">24</option></select></label>
                </aside>
                <section class="article-template-manager__preview">
                    <div class="article-template-manager__preview-bar">
                        <div><strong>{{ t('Preview') }}: @{{ activeTemplate?.label }}</strong><small>{{ t('Live preview of the selected template.') }}</small></div>
                        <div class="article-template-manager__preview-controls">
                            <div class="article-template-manager__device-meta" aria-live="polite"><strong>@{{ activeDevice.label }} · @{{ activeDevice.width }} × @{{ activeDevice.height }}</strong><small>@{{ copy.scaledToFit }}</small></div>
                            <div class="article-template-manager__devices" role="group" aria-label="{{ t('Preview device') }}">
                                <button type="button" :class="{ active: device === 'desktop' }" :aria-pressed="device === 'desktop'" @click="selectDevice('desktop')" aria-label="{{ t('Desktop preview') }}"><i class="fas fa-desktop"></i></button>
                                <button type="button" :class="{ active: device === 'tablet' }" :aria-pressed="device === 'tablet'" @click="selectDevice('tablet')" aria-label="{{ t('Tablet preview') }}"><i class="fas fa-tablet-alt"></i></button>
                                <button type="button" :class="{ active: device === 'mobile' }" :aria-pressed="device === 'mobile'" @click="selectDevice('mobile')" aria-label="{{ t('Mobile preview') }}"><i class="fas fa-mobile-alt"></i></button>
                            </div>
                        </div>
                    </div>
                    <div ref="previewViewport" class="article-template-manager__iframe-wrap" :class="['device-'+device, { 'is-loading': previewLoading }]" :aria-busy="previewLoading ? 'true' : 'false'"><div v-if="previewLoading" class="article-template-manager__preview-loading" role="status" aria-live="polite"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>@{{ copy.loadingPreview }}</span></div><div class="article-template-manager__device-stage" :style="deviceStageStyle"><iframe :src="previewUrl" :style="deviceFrameStyle" :title="activeTemplate?.label || copy.previewTitle" v-on:load="onPreviewLoad"></iframe></div></div>
                </section>
            </div>
            <Teleport to="body">
                <div class="modal fade" id="modalArticleTemplateOptions" tabindex="-1" aria-labelledby="modalArticleTemplateOptionsLabel" aria-hidden="true">
                    <div class="modal-dialog ph-modal-dialog modal-dialog-centered article-template-options-modal">
                        <div class="modal-content">
                            <div class="modal-header"><div><h5 class="modal-title" id="modalArticleTemplateOptionsLabel">{{ t('Template Options') }}</h5><p>@{{ activeTemplate?.label }}</p></div><button type="button" class="btn-close" aria-label="{{ t('Close') }}" @click="closeTemplateOptions"></button></div>
                            <div class="modal-body" v-if="optionsModal.value">
                                <section class="article-template-options-section"><div class="article-template-options-section__heading"><span>1</span><div><strong>{{ t('Header content') }}</strong><small>{{ t('Control each visible header element for this template.') }}</small></div></div><div class="article-template-options-fields"><template v-for="field in ['eyebrow', 'title', 'description']" :key="field"><div class="article-template-option-row"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.header[field].enabled"><span class="form-check-label text-capitalize">@{{ field }}</span></label><template v-if="surface === 'archive'"><textarea v-if="field === 'description'" class="form-control" rows="2" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></textarea><input v-else class="form-control" type="text" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></template><template v-else><select v-if="field !== 'title'" class="form-select" v-model="optionsModal.value.header[field].mode" :disabled="!optionsModal.value.header[field].enabled"><option value="dynamic">{{ t('Dynamic Article data') }}</option><option value="custom">{{ t('Custom text') }}</option></select><textarea v-if="field !== 'title' && optionsModal.value.header[field].mode === 'custom'" class="form-control" rows="2" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></textarea><small v-if="field === 'title'">{{ t('Uses the Article title dynamically.') }}</small></template></div></template></div></section>
                                <section v-if="surface === 'archive'" class="article-template-options-section"><div class="article-template-options-section__heading"><span>2</span><div><strong>{{ t('Archive toolbar') }}</strong><small>{{ t('Enable controls and place them on the archive toolbar.') }}</small></div></div><div class="article-template-options-fields"><template v-for="field in ['search', 'category']" :key="field"><div class="article-template-option-row article-template-option-row--toolbar"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.toolbar[field].enabled"><span class="form-check-label">@{{ field === 'category' ? copy.categoryFilter : copy.search }}</span></label><div class="btn-group article-template-position" role="group"><button v-for="position in ['left', 'center', 'right']" :key="position" type="button" class="btn" :class="optionsModal.value.toolbar[field].position === position ? 'btn-primary' : 'btn-outline-secondary'" @click="optionsModal.value.toolbar[field].position = position">@{{ position }}</button></div></div></template></div></section>
                                <section v-if="surface === 'archive' && optionsModal.value.grid" class="article-template-options-section"><div class="article-template-options-section__heading"><span>3</span><div><strong>{{ t('Grid columns') }}</strong><small>{{ t('Set the Article grid for each device.') }}</small></div></div><div class="article-template-device-tabs"><button v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" type="button" :class="{ active: optionsDevice === device }" @click="optionsDevice = device"><i :class="device === 'desktop' ? 'fas fa-desktop' : (device === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ device }}</button></div><div class="article-template-columns"><button v-for="column in columnChoices(optionsDevice)" :key="column" type="button" :class="{ active: optionsModal.value.grid[optionsDevice] === column }" @click="optionsModal.value.grid[optionsDevice] = column">@{{ column }} {{ t('columns') }}</button></div></section>
                                @include('manage_article.templates.partials.options-styling')
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-secondary" @click="closeTemplateOptions">{{ t('Cancel') }}</button><button type="button" class="btn ph-btn-theme" @click="applyTemplateOptions">{{ t('Apply changes') }}</button></div>
                        </div>
                    </div>
                </div>
            </Teleport>
        </section>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/vendor/coloris/coloris.min.css?v=').time() }}" rel="stylesheet">
    <link href="{{ url('assets/css/article/article-template-manager-2026.css?v=').time() }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('assets/vendor/coloris/coloris.min.js?v=').time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.5.21/dist/vue.global.prod.js" crossorigin="anonymous"></script>
    <script src="{{ url('assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js?v=').time() }}"></script>
@endpush
