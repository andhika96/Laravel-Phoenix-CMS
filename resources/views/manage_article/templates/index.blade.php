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
            <header class="article-template-manager__header"><div><h4>{{ t('Manage Article Templates') }}</h4><p>{{ t('Choose how article archive and detail pages are displayed.') }}</p></div><div class="article-template-manager__header-actions"><button id="article-template-options-trigger" type="button" class="btn ph-btn-theme-outline" @click="openTemplateOptions"><i class="fas fa-sliders-h me-1" aria-hidden="true"></i>{{ t('Template Options') }}</button><button type="button" class="btn ph-btn-theme" :disabled="saving" @click="save"><span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>{{ t('Save Template') }}</button></div></header>
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
                <div class="modal fade article-template-options-modal" id="modalArticleTemplateOptions" tabindex="-1" aria-labelledby="modalArticleTemplateOptionsLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" @keydown.esc.prevent="requestCloseTemplateOptions">
                    <div class="modal-dialog ph-modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="article-template-options-title"><span class="article-template-options-title__icon" aria-hidden="true"><i class="fas fa-sliders-h"></i></span><div><h5 class="modal-title" id="modalArticleTemplateOptionsLabel">{{ t('Template Options') }}</h5><p>@{{ activeTemplate?.label }}</p></div></div>
                                <div class="article-template-options-header-actions"><span class="article-template-options-surface"><i class="fas" :class="surface === 'archive' ? 'fa-archive' : 'fa-file-alt'" aria-hidden="true"></i>@{{ surface === 'archive' ? 'Archive template' : 'Detail template' }}</span><button type="button" class="btn-close" aria-label="{{ t('Close') }}" @click="requestCloseTemplateOptions"></button></div>
                            </div>
                            <div class="modal-body" v-if="optionsModal.value">
                                <div class="article-template-options-view-switch" role="group" aria-label="{{ t('Template options view') }}">
                                    <button type="button" :class="{ active: optionsModal.view === 'settings' }" :aria-pressed="optionsModal.view === 'settings'" @click="setOptionsView('settings')"><i class="fas fa-sliders-h" aria-hidden="true"></i>{{ t('Settings') }}</button>
                                    <button type="button" :class="{ active: optionsModal.view === 'preview' }" :aria-pressed="optionsModal.view === 'preview'" @click="setOptionsView('preview')"><i class="fas fa-desktop" aria-hidden="true"></i>{{ t('Preview') }}</button>
                                </div>
                                <div class="article-template-options-layout" :class="{ 'is-preview-view': optionsModal.view === 'preview' }">
                                    <nav class="article-template-options-nav" aria-label="{{ t('Customize template') }}">
                                        <span class="article-template-options-nav__eyebrow">{{ t('Customize') }}</span>
                                        <div role="tablist" aria-orientation="vertical">
                                            <button v-for="(section, index) in optionSections" :key="section.key" type="button" role="tab" class="article-template-options-nav__item" :id="'article-template-option-tab-' + section.key" :class="{ active: optionsModal.section === section.key }" :aria-selected="optionsModal.section === section.key ? 'true' : 'false'" :aria-controls="'article-template-option-panel-' + section.key" :tabindex="optionsModal.section === section.key ? 0 : -1" @click="setOptionsSection(section.key)" @keydown="handleOptionsTabKeydown($event, index)"><i class="fas" :class="section.icon" aria-hidden="true"></i><span>@{{ section.label }}</span></button>
                                        </div>
                                    </nav>
                                    <div class="article-template-options-settings">
                                        <label class="article-template-options-section-picker">{{ t('Customize') }}<select :value="optionsModal.section" @change="setOptionsSection($event.target.value)"><option v-for="section in optionSections" :key="section.key" :value="section.key">@{{ section.label }}</option></select></label>
                                        <div ref="optionsPanelViewport" class="article-template-options-panel">
                                <section v-if="optionsModal.section === 'header'" id="article-template-option-panel-header" role="tabpanel" aria-labelledby="article-template-option-tab-header" tabindex="-1" class="article-template-options-section">
                                    <div class="article-template-options-section__heading"><span>1</span><div><h5>{{ t('Header content') }}</h5><small>{{ t('Control each visible header element for this template.') }}</small></div></div>
                                    <div class="article-template-header-fields">
                                        <template v-for="field in ['eyebrow', 'title', 'description']" :key="field">
                                            <div class="article-template-header-field">
                                                <label class="form-check form-switch article-template-header-field__label"><span class="form-check-label text-capitalize">@{{ field }}</span><input class="form-check-input" type="checkbox" v-model="optionsModal.value.header[field].enabled"></label>
                                                <div class="article-template-header-field__control">
                                                    <template v-if="surface === 'archive'">
                                                        <textarea v-if="field === 'description'" :aria-label="field" class="form-control" rows="10" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></textarea>
                                                        <input v-else :aria-label="field" class="form-control" type="text" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled">
                                                    </template>
                                                    <template v-else>
                                                        <select v-if="field !== 'title'" :aria-label="field" class="form-select" v-model="optionsModal.value.header[field].mode" :disabled="!optionsModal.value.header[field].enabled"><option value="dynamic">{{ t('Dynamic Article data') }}</option><option value="custom">{{ t('Custom text') }}</option></select>
                                                        <textarea v-if="field !== 'title' && optionsModal.value.header[field].mode === 'custom'" :aria-label="field" class="form-control" rows="10" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></textarea>
                                                        <small v-if="field === 'title'" class="article-template-header-field__help">{{ t('Uses the Article title dynamically.') }}</small>
                                                    </template>
                                                </div>
                                                <small v-if="surface === 'archive' && field === 'eyebrow'" class="article-template-header-field__help">{{ t('A short label above the title.') }}</small>
                                            </div>
                                        </template>
                                    </div>
                                </section>
                                <section v-if="optionsModal.section === 'toolbar' && surface === 'archive'" id="article-template-option-panel-toolbar" role="tabpanel" aria-labelledby="article-template-option-tab-toolbar" tabindex="-1" class="article-template-options-section">
                                    <div class="article-template-options-section__heading"><span>2</span><div><h5>{{ t('Archive toolbar') }}</h5><small>{{ t('Enable controls and place them on the archive toolbar.') }}</small></div></div>
                                    <div class="article-template-options-fields">
                                        <template v-for="field in ['search', 'category']" :key="field">
                                            <div class="article-template-option-row article-template-option-row--toolbar">
                                                <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.toolbar[field].enabled"><span class="form-check-label">@{{ field === 'category' ? copy.categoryFilter : copy.search }}</span></label>
                                                <div v-if="optionsModal.value.toolbar[field].enabled && (field !== 'category' || optionsModal.value.toolbar.category.enabled)" class="article-template-toolbar-option-controls" :class="{ 'article-template-toolbar-option-controls--category': field === 'category' }">
                                                    <template v-if="field === 'category' && activeTemplateKey === 'minimal-reading-list'">
                                                        <label v-if="optionsModal.value.toolbar.category.enabled" class="article-template-reading-list-control article-template-category-position">{{ t('Position') }}<select class="form-select" v-model="optionsModal.value.toolbar.category.position"><option value="left">{{ t('Left') }}</option><option value="center">{{ t('Center') }}</option><option value="right">{{ t('Right') }}</option></select></label>
                                                        <label v-if="optionsModal.value.toolbar.category.enabled" class="article-template-reading-list-control">{{ t('Category filter style') }}<select class="form-select" v-model="optionsModal.value.toolbar.category.mode"><option value="button-list">{{ t('Button list') }}</option><option value="select">{{ t('Form select') }}</option></select></label>
                                                    </template>
                                                    <div v-else class="btn-group article-template-position" role="group"><button v-for="position in ['left', 'center', 'right']" :key="position" type="button" class="btn" :class="optionsModal.value.toolbar[field].position === position ? 'btn-primary' : 'btn-outline-secondary'" @click="optionsModal.value.toolbar[field].position = position">@{{ position }}</button></div>
                                                    <div v-if="field === 'search'" class="article-template-search-style-fields">
                                                        <label>{{ t('Search model') }}<select class="form-select" v-model="optionsModal.value.toolbar.search.type"><option value="attached">{{ t('Attached Classic') }}</option><option value="soft">{{ t('Soft Field') }}</option><option value="underline">{{ t('Minimal Underline') }}</option></select></label>
                                                        <label>{{ t('Search button gap') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('toolbar.search.gap')" :step="dimensionStep('toolbar.search.gap')" :value="dimensionValue('toolbar.search.gap')" @input="setDimensionValue('toolbar.search.gap', $event.target.value)"><select class="form-select" :value="dimensionUnit('toolbar.search.gap')" @change="setDimensionUnit('toolbar.search.gap', $event.target.value)"><option v-for="unit in unitChoices()" :key="'search-gap-'+unit" :value="unit">@{{ unit }}</option></select></span></label>
                                                        <label>{{ t('Search border radius') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('toolbar.search.radius', 'radius')" :step="dimensionStep('toolbar.search.radius', 'radius')" :value="dimensionValue('toolbar.search.radius', 'radius')" @input="setDimensionValue('toolbar.search.radius', $event.target.value, 'radius')"><select class="form-select" :value="dimensionUnit('toolbar.search.radius', 'radius')" @change="setDimensionUnit('toolbar.search.radius', $event.target.value, 'radius')"><option v-for="unit in unitChoices('radius')" :key="'search-radius-'+unit" :value="unit">@{{ unit }}</option></select></span></label>
                                                        <label>{{ t('Search icon') }}<select class="form-select" v-model="optionsModal.value.toolbar.search.icon"><option value="fas fa-search">{{ t('Search') }}</option><option value="fas fa-sliders-h">{{ t('Sliders') }}</option><option value="fas fa-arrow-right">{{ t('Arrow right') }}</option></select></label>
                                                        <label>{{ t('Input background') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme default" v-model="optionsModal.value.toolbar.search.input_background_color"></label>
                                                        <label>{{ t('Input text') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme default" v-model="optionsModal.value.toolbar.search.input_text_color"></label>
                                                        <label>{{ t('Button background') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme accent" v-model="optionsModal.value.toolbar.search.button_background_color"></label>
                                                        <label>{{ t('Button text') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme default" v-model="optionsModal.value.toolbar.search.button_text_color"></label>
                                                        <label>{{ t('Button hover background') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme surface" v-model="optionsModal.value.toolbar.search.button_hover_background_color"></label>
                                                        <label>{{ t('Button hover text') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme default" v-model="optionsModal.value.toolbar.search.button_hover_text_color"></label>
                                                        <label>{{ t('Button active background') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme accent" v-model="optionsModal.value.toolbar.search.button_active_background_color"></label>
                                                        <label>{{ t('Button active text') }}<input class="form-control article-template-coloris" type="text" placeholder="Theme default" v-model="optionsModal.value.toolbar.search.button_active_text_color"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </section>
                                <section v-if="optionsModal.section === 'post-list' && surface === 'archive' && activeTemplateKey === 'minimal-reading-list' && optionsModal.value.post_list" id="article-template-option-panel-post-list" role="tabpanel" aria-labelledby="article-template-option-tab-post-list" tabindex="-1" class="article-template-options-section">
                                    <div class="article-template-options-section__heading"><span>3</span><div><h5>{{ t('Post list') }}</h5><small>{{ t('Set the vertical spacing between posts in the reading list.') }}</small></div></div>
                                    <div class="article-template-options-fields">
                                        <div class="article-template-option-row"><label class="article-template-reading-list-control">{{ t('Post list spacing') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('post_list.item_gap')" :step="dimensionStep('post_list.item_gap')" :value="dimensionValue('post_list.item_gap')" @input="setDimensionValue('post_list.item_gap', $event.target.value)"><select class="form-select" :value="dimensionUnit('post_list.item_gap')" @change="setDimensionUnit('post_list.item_gap', $event.target.value)"><option v-for="unit in unitChoices()" :key="unit" :value="unit">@{{ unit }}</option></select></span></label></div>
                                    </div>
                                </section>
                                <section v-if="optionsModal.section === 'sidebar' && surface === 'archive' && activeTemplateKey === 'minimal-reading-list' && optionsModal.value.sidebar" id="article-template-option-panel-sidebar" role="tabpanel" aria-labelledby="article-template-option-tab-sidebar" tabindex="-1" class="article-template-options-section">
                                    <div class="article-template-options-section__heading"><span>4</span><div><h5>{{ t('Reading list sidebar') }}</h5><small>{{ t('Show or hide the Categories and Popular Posts panels beside the reading list.') }}</small></div></div>
                                    <div class="article-template-options-fields">
                                        <div class="article-template-option-row"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.sidebar.enabled"><span class="form-check-label">{{ t('Show sidebar') }}</span></label></div>
                                        <div v-if="optionsModal.value.sidebar.enabled" class="article-template-sidebar-options">
                                            <div class="article-template-sidebar-option">
                                                <label class="form-check form-switch"><span class="form-check-label">{{ t('Categories') }}</span><input class="form-check-input" type="checkbox" v-model="optionsModal.value.sidebar.categories.enabled"></label>
                                                <label v-if="optionsModal.value.sidebar.categories.enabled" class="article-template-reading-list-control">{{ t('Categories position') }}<select class="form-select" v-model="optionsModal.value.sidebar.categories.position"><option value="static">{{ t('Stay') }}</option><option value="sticky">{{ t('Sticky') }}</option></select></label>
                                            </div>
                                            <div class="article-template-sidebar-option">
                                                <label class="form-check form-switch"><span class="form-check-label">{{ t('Popular Posts') }}</span><input class="form-check-input" type="checkbox" v-model="optionsModal.value.sidebar.popular.enabled"></label>
                                                <label v-if="optionsModal.value.sidebar.popular.enabled" class="article-template-reading-list-control">{{ t('Popular Posts position') }}<select class="form-select" v-model="optionsModal.value.sidebar.popular.position"><option value="static">{{ t('Stay') }}</option><option value="sticky">{{ t('Sticky') }}</option></select></label>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section v-if="optionsModal.section === 'grid' && surface === 'archive' && optionsModal.value.grid" id="article-template-option-panel-grid" role="tabpanel" aria-labelledby="article-template-option-tab-grid" tabindex="-1" class="article-template-options-section"><div class="article-template-options-section__heading"><span>3</span><div><h5>{{ t('Grid columns') }}</h5><small>{{ t('Set the Article grid for each device.') }}</small></div></div><div class="article-template-device-tabs"><button v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" type="button" :class="{ active: optionsDevice === device }" @click="selectOptionsDevice(device)"><i :class="device === 'desktop' ? 'fas fa-desktop' : (device === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ device }}</button></div><div class="article-template-columns"><button v-for="column in columnChoices(optionsDevice)" :key="column" type="button" :class="{ active: optionsModal.value.grid[optionsDevice] === column }" @click="optionsModal.value.grid[optionsDevice] = column">@{{ column }} {{ t('columns') }}</button></div></section>
                                @include('manage_article.templates.partials.options-styling')
                                        </div>
                                    </div>
                                    <aside class="article-template-options-preview" aria-label="{{ t('Template preview') }}">
                                        <div class="article-template-options-preview__header"><div><strong>{{ t('Template preview') }}</strong><span class="article-template-options-preview__badge">{{ t('Illustrative') }}</span></div><div class="article-template-options-preview__devices" role="group" aria-label="{{ t('Preview device') }}"><button v-for="deviceName in ['desktop', 'tablet', 'mobile']" :key="'options-preview-'+deviceName" type="button" :class="{ active: optionsDevice === deviceName }" :aria-pressed="optionsDevice === deviceName" :aria-label="deviceProfiles[deviceName].label" @click="selectOptionsDevice(deviceName)"><i :class="deviceName === 'desktop' ? 'fas fa-desktop' : (deviceName === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i><span>@{{ deviceProfiles[deviceName].label }}</span></button></div></div>
                                        <div ref="optionsPreviewViewport" class="article-template-options-preview__viewport" :class="{ 'is-loading': modalPreviewLoading }" :aria-busy="modalPreviewLoading ? 'true' : 'false'"><div v-if="modalPreviewLoading" class="article-template-options-preview__status" role="status" aria-live="polite"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span>@{{ copy.loadingPreview }}</div><div v-if="modalPreviewError" class="article-template-options-preview__error" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i><span>@{{ modalPreviewError }}</span><button type="button" class="btn btn-sm ph-btn-theme-outline" @click="retryModalPreview">{{ t('Retry') }}</button></div><div class="article-template-options-preview__stage" :style="optionsPreviewStageStyle"><iframe v-if="modalPreviewUrl" :src="modalPreviewUrl" :style="optionsPreviewFrameStyle" :title="activeTemplate?.label || copy.previewTitle" @load="onModalPreviewLoad" v-on:error="onModalPreviewError"></iframe></div></div>
                                        <small class="article-template-options-preview__note"><i class="fas fa-info-circle" aria-hidden="true"></i>{{ t('Sample content · live draft preview') }}</small>
                                    </aside>
                                </div>
                            </div>
                            <div class="modal-footer" v-if="optionsModal.value"><div v-if="optionsModal.dismissOpen" class="article-template-options-dismiss" role="alert"><span>{{ t('Discard unsaved changes?') }}</span><div><button type="button" class="btn btn-sm btn-secondary" @click="keepEditing">{{ t('Keep editing') }}</button><button type="button" class="btn btn-sm btn-danger" @click="discardTemplateOptions">{{ t('Discard changes') }}</button></div></div><div class="article-template-options-footer-main"><span class="article-template-options-footer-note"><i class="fas fa-info-circle" aria-hidden="true"></i>{{ t('Changes are applied to the template draft. Save Template persists them.') }}</span><div class="article-template-options-footer-actions"><button type="button" class="btn btn-secondary" @click="requestCloseTemplateOptions">{{ t('Cancel') }}</button><button type="button" class="btn ph-btn-theme" @click="applyTemplateOptions">{{ t('Apply changes') }}</button></div></div></div>
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
