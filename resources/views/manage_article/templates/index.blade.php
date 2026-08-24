@extends('themes.'.custom_theme('cms'))

@section('title')
    {{ t('Manage Article Templates') }}
@endsection

@section('content')
    <div id="ph-app-manage-article-templates"
        data-save-url="{{ route('cms.core.manage_article.templates.update') }}"
        data-preview-base-url="{{ url('manage_article/templates/preview/__SURFACE__/__TEMPLATE__') }}"
        data-placeholder-thumbnail="{{ asset('assets/images/article/article-image-placeholder.svg') }}"
        data-payload="{{ json_encode(['settings' => ['archive_template' => $settings->archive_template, 'detail_template' => $settings->detail_template, 'archive_per_page' => (int) $settings->archive_per_page], 'templates' => ['archive' => $archiveTemplates, 'detail' => $detailTemplates], 'copy' => ['archiveHint' => t('Choose an archive template to manage and preview.'), 'detailHint' => t('Choose a detail template to manage and preview.'), 'previewTitle' => t('Article template preview'), 'desktop' => t('Desktop'), 'tablet' => t('Tablet'), 'mobile' => t('Mobile'), 'scaledToFit' => t('Scaled to fit'), 'loadingPreview' => t('Loading preview')]]) }}">
        <div class="mb-3">{{ Breadcrumbs::render('manage_article.templates') }}</div>
        <section class="article-template-manager ph-content rounded">
            <header class="article-template-manager__header"><div><h1>{{ t('Manage Article Templates') }}</h1><p>{{ t('Choose how article archive and detail pages are displayed.') }}</p></div><button type="button" class="btn ph-btn-theme" :disabled="saving" @click="save"><span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>{{ t('Save Template') }}</button></header>
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
        </section>
    </div>
@endsection

@push('css')
    <link href="{{ url('assets/css/article/article-template-manager-2026.css?v=').time() }}" rel="stylesheet">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/vue@3.5.21/dist/vue.global.prod.js" crossorigin="anonymous"></script>
    <script src="{{ url('assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js?v=').time() }}"></script>
@endpush
