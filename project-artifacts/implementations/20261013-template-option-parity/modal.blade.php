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
                                <section v-if="optionsModal.section === 'display'" id="article-template-option-panel-display" role="tabpanel" aria-labelledby="article-template-option-tab-display" class="article-template-options-section">
 <div class="article-template-options-section__heading"><div><h5>{{ t('Visible elements') }}</h5><small>{{ t('Choose which article details appear in this template.') }}</small></div></div>
 <div class="article-template-options-fields">
 @foreach (['category' => 'Show category', 'date' => 'Show date', 'author' => 'Show author / editor', 'reading_time' => 'Show reading time', 'excerpt' => 'Show excerpt', 'read_more' => 'Show Read more link', 'navigation' => 'Show previous and next articles'] as $field => $label)
 <label v-if="optionsModal.surface === 'detail' ? {{ in_array($field, ['date', 'author', 'navigation']) ? 'true' : 'false' }} : {{ in_array($field, ['category','date','excerpt']) ? 'true' : ($field === 'reading_time' ? "optionsModal.key === 'minimal-reading-list'" : ($field === 'read_more' ? "optionsModal.key !== 'mosaic-magazine'" : 'false')) }}" class="form-check form-switch"><input type="checkbox" class="form-check-input" v-model="optionsModal.value.display.{{ $field }}"><span class="form-check-label">{{ t($label) }}</span></label>
 @endforeach
 </div></section>
 <section v-if="optionsModal.section === 'header'" id="article-template-option-panel-header" role="tabpanel" aria-labelledby="article-template-option-tab-header" tabindex="-1" class="article-template-options-section">
                                    <div class="article-template-options-section__heading"><span>1</span><div><h5>{{ t('Header content') }}</h5><small>{{ t('Control each visible header element for this template.') }}</small></div></div>
                                    <div class="article-template-header-fields">
                                        <template v-for="field in ['eyebrow', 'title', 'description']" :key="field">
                                            <div class="article-template-header-field">
                                                <label class="form-check form-switch article-template-header-field__label"><span class="form-check-label text-capitalize">@{{ field }}</span><input class="form-check-input" type="checkbox" v-model="optionsModal.value.header[field].enabled"></label>
                                                <div class="article-template-header-field__control">
                                                    <template v-if="surface === 'archive'">
                                                        <textarea v-if="field === 'description'" :aria-label="field" class="form-control" rows="3" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></textarea>
                                                        <input v-else :aria-label="field" class="form-control" type="text" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled">
                                                    </template>
                                                    <template v-else>
                                                        <select v-if="field !== 'title'" :aria-label="field" class="form-select" v-model="optionsModal.value.header[field].mode" :disabled="!optionsModal.value.header[field].enabled"><option value="dynamic">{{ t('Dynamic Article data') }}</option><option value="custom">{{ t('Custom text') }}</option></select>
                                                        <textarea v-if="field !== 'title' && optionsModal.value.header[field].mode === 'custom'" :aria-label="field" class="form-control" rows="3" v-model="optionsModal.value.header[field].text" :disabled="!optionsModal.value.header[field].enabled"></textarea>
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
                                <section v-if="optionsModal.section === 'grid' && surface === 'archive' && optionsModal.value.grid" id="article-template-option-panel-grid" role="tabpanel" aria-labelledby="article-template-option-tab-grid" tabindex="-1" class="article-template-options-section"><div class="article-template-options-section__heading"><span>3</span><div><h5>{{ t('Grid columns') }}</h5><small>{{ t('Set the Article grid for each device.') }}</small></div></div><div class="article-template-device-tabs" role="group" aria-label="{{ t('Device') }}"><button v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" type="button" :class="{ active: optionsDevice === device }" :aria-pressed="optionsDevice === device" @click="selectOptionsDevice(device)"><i :class="device === 'desktop' ? 'fas fa-desktop' : (device === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ device }}</button></div><div class="article-template-columns" role="group" aria-label="{{ t('Grid columns') }}"><button v-for="column in columnChoices(optionsDevice)" :key="column" type="button" :class="{ active: optionsModal.value.grid[optionsDevice] === column }" :aria-pressed="optionsModal.value.grid[optionsDevice] === column" @click="optionsModal.value.grid[optionsDevice] = column">@{{ column }} {{ t('columns') }}</button></div></section>
                                @include('manage_article.templates.partials.options-mosaic-magazine')
                                @include('manage_article.templates.partials.options-classic-balanced')
                                @include('manage_article.templates.partials.options-styling')
                                        </div>
                                    </div>
                                    <aside class="article-template-options-preview" aria-label="{{ t('Template preview') }}">
                                        <div class="article-template-options-preview__header"><div><strong>{{ t('Template preview') }}</strong><span class="article-template-options-preview__badge">{{ t('Illustrative') }}</span></div><div class="article-template-options-preview__devices" role="group" aria-label="{{ t('Preview device') }}"><button v-for="deviceName in ['desktop', 'tablet', 'mobile']" :key="'options-preview-'+deviceName" type="button" :class="{ active: optionsDevice === deviceName }" :aria-pressed="optionsDevice === deviceName" :aria-label="deviceProfiles[deviceName].label" @click="selectOptionsDevice(deviceName)"><i :class="deviceName === 'desktop' ? 'fas fa-desktop' : (deviceName === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i><span>@{{ deviceProfiles[deviceName].label }}</span></button></div></div>
                                        <div ref="optionsPreviewViewport" class="article-template-options-preview__viewport" :class="{ 'is-loading': modalPreviewLoading }" :aria-busy="modalPreviewLoading ? 'true' : 'false'"><div v-if="modalPreviewLoading" class="article-template-options-preview__status" role="status" aria-live="polite"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span>@{{ copy.loadingPreview }}</div><div v-if="modalPreviewError" class="article-template-options-preview__error ph-callout-danger ph-callout-no-border" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i><span>@{{ modalPreviewError }}</span><button type="button" class="btn btn-sm ph-btn-theme-outline" @click="retryModalPreview">{{ t('Retry') }}</button></div><div class="article-template-options-preview__stage" :style="optionsPreviewStageStyle"><iframe v-if="modalPreviewUrl" :src="modalPreviewUrl" :style="optionsPreviewFrameStyle" :title="activeTemplate?.label || copy.previewTitle" @load="onModalPreviewLoad" v-on:error="onModalPreviewError"></iframe></div></div>
                                        <small class="article-template-options-preview__note"><i class="fas fa-info-circle" aria-hidden="true"></i>{{ t('Sample content · live draft preview') }}</small>
                                    </aside>
                                </div>
                            </div>
                            <div class="modal-footer" v-if="optionsModal.value"><div v-if="optionsModal.dismissOpen" class="article-template-options-dismiss ph-callout-warning ph-callout-no-border" role="alert"><span>{{ t('Discard unsaved changes?') }}</span><div><button type="button" class="btn btn-sm btn-secondary" @click="keepEditing">{{ t('Keep editing') }}</button><button type="button" class="btn btn-sm btn-danger" @click="discardTemplateOptions">{{ t('Discard changes') }}</button></div></div><div class="article-template-options-footer-main"><span class="article-template-options-footer-note"><i class="fas fa-info-circle" aria-hidden="true"></i>{{ t('Changes are applied to the template draft. Save Template persists them.') }}</span><div class="article-template-options-footer-actions"><button type="button" class="btn btn-secondary" @click="requestCloseTemplateOptions">{{ t('Cancel') }}</button><button type="button" class="btn ph-btn-theme" @click="applyTemplateOptions">{{ t('Apply changes') }}</button></div></div></div>
                        </div>
                    </div>
                </div>
                <div class="ph-notice article-template-notice" v-cloak>
                    <div aria-live="polite" aria-atomic="true" class="position-relative">
                        <div class="toast-container position-fixed top-0 end-0 p-3">
                            <div id="article-template-notice" :class="'toast ph-notice-toast ph-callout-no-border ph-callout-' + noticeType" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                                <div :class="'toast-header ph-callout-' + noticeType + ' px-3 pt-3 pb-1 border-0'">
                                    <strong class="me-auto">{{ t('Notice') }}</strong>
                                    <small>{{ t('just now') }}</small>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ t('Close') }}"></button>
                                </div>
                                <div class="toast-body p-3 text-start">@{{ notice }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>