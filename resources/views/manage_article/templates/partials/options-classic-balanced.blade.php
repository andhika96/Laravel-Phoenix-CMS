@foreach (['mosaic-classic' => ['key' => 'mosaic_classic', 'label' => 'Mosaic Classic'], 'balanced-card-grid' => ['key' => 'balanced_grid', 'label' => 'Balanced Card Grid']] as $layoutTemplate => $layoutDefinition)
    <section v-if="optionsModal.section === '{{ $layoutTemplate }}' && surface === 'archive' && activeTemplateKey === '{{ $layoutTemplate }}' && optionsModal.value.{{ $layoutDefinition['key'] }}" id="article-template-option-panel-{{ $layoutTemplate }}" role="tabpanel" aria-labelledby="article-template-option-tab-{{ $layoutTemplate }}" tabindex="-1" class="article-template-options-section">
        <div class="article-template-options-section__heading"><span><i class="fas fa-th-large" aria-hidden="true"></i></span><div><h5>{{ t($layoutDefinition['label']) }}</h5><small>{{ t('Customize the layout, spacing, and story details.') }}</small></div></div>
        <div class="article-template-options-fields article-template-editorial-journal-fields">
            @if ($layoutTemplate === 'mosaic-classic')
                <fieldset class="article-template-editorial-journal-group">
                    <legend>{{ t('Lead and side stories') }}</legend>
                    <label>{{ t('Lead story layout') }}<select class="form-select" v-model="optionsModal.value.mosaic_classic.lead_layout"><option value="headline-first">{{ t('Headline above image') }}</option><option value="image-first">{{ t('Image above headline') }}</option></select></label>
                    <label for="classic-side-count">{{ t('Side story count') }}</label>
                    <input id="classic-side-count" class="form-control" type="number" min="0" max="24" step="1" v-model.number="optionsModal.value.mosaic_classic.side_count" aria-describedby="classic-side-count-help">
                    <small id="classic-side-count-help">{{ t('0 shows a full-width lead followed by the regular grid. The count uses posts already on this page.') }}</small>
                    <template v-if="optionsModal.value.mosaic_classic.side_count > 0">
                        <label>{{ t('Side story position') }}<select class="form-select" v-model="optionsModal.value.mosaic_classic.side_position"><option value="right">{{ t('Right') }}</option><option value="left">{{ t('Left') }}</option></select></label>
                        <label>{{ t('Lead and side proportion') }}<select class="form-select" v-model="optionsModal.value.mosaic_classic.lead_ratio"><option value="67-33">67:33</option><option value="60-40">60:40</option><option value="50-50">50:50</option></select></label>
                        <label>{{ t('Side story layout') }}<select class="form-select" v-model="optionsModal.value.mosaic_classic.side_layout"><option value="list">{{ t('Compact list') }}</option><option value="stacked">{{ t('Image above title') }}</option></select></label>
                        <label v-if="optionsModal.value.mosaic_classic.side_layout === 'list'">{{ t('Side thumbnail position') }}<select class="form-select" v-model="optionsModal.value.mosaic_classic.side_image_position"><option value="right">{{ t('Right') }}</option><option value="left">{{ t('Left') }}</option></select></label>
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.mosaic_classic.side_numbers"><span class="form-check-label">{{ t('Show story numbers') }}</span></label>
                    </template>
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.mosaic_classic.divider"><span class="form-check-label">{{ t('Show dividers') }}</span></label>
                </fieldset>
                <fieldset class="article-template-editorial-journal-group">
                    <legend>{{ t('Highlights and latest articles') }}</legend>
                    <label>{{ t('Highlight post count') }}<input class="form-control" type="number" min="0" max="24" step="1" v-model.number="optionsModal.value.mosaic_classic.highlight_count"></label>
                    <small>{{ t('Posts after the side stories become image highlights. Use 0 to skip highlights; remaining posts appear under Latest Articles.') }}</small>
                    <div class="article-template-device-tabs" role="group" aria-label="{{ t('Highlight device') }}"><button v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" type="button" :class="{ active: optionsDevice === device }" :aria-pressed="optionsDevice === device" @click="selectOptionsDevice(device)">@{{ device }}</button></div>
                    <label>{{ t('Highlight columns') }}<select class="form-select" v-model.number="optionsModal.value.mosaic_classic.highlight_columns[optionsDevice]"><option v-for="column in columnChoices(optionsDevice)" :key="column" :value="column">@{{ column }} {{ t('columns') }}</option></select></label>
                    <label>{{ t('Latest articles heading') }}<input class="form-control" type="text" maxlength="80" v-model="optionsModal.value.mosaic_classic.latest_title"></label>
                    <small>{{ t('Leave the heading empty to hide it.') }}</small>
                </fieldset>
            @else
                <fieldset class="article-template-editorial-journal-group">
                    <legend>{{ t('Card appearance') }}</legend>
                    <label>{{ t('Card style') }}<select class="form-select" v-model="optionsModal.value.balanced_grid.card_style"><option value="outline">{{ t('Outlined') }}</option><option value="flat">{{ t('Open / flat') }}</option><option value="elevated">{{ t('Soft shadow') }}</option></select></label>
                    <label>{{ t('Text alignment') }}<select class="form-select" v-model="optionsModal.value.balanced_grid.text_align"><option value="left">{{ t('Left') }}</option><option value="center">{{ t('Center') }}</option></select></label>
                    @foreach (['card_padding' => 'Card padding', 'card_radius' => 'Card radius'] as $field => $label)
                        <label>{{ t($label) }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('balanced_grid.{{ $field }}', '{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')" :step="dimensionStep('balanced_grid.{{ $field }}', '{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')" :value="dimensionValue('balanced_grid.{{ $field }}', '{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')" @input="setDimensionValue('balanced_grid.{{ $field }}', $event.target.value, '{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')"><select class="form-select" aria-label="{{ t($label.' unit') }}" :value="dimensionUnit('balanced_grid.{{ $field }}', '{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')" @change="setDimensionUnit('balanced_grid.{{ $field }}', $event.target.value, '{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')"><option v-for="unit in unitChoices('{{ $field === 'card_radius' ? 'radius' : 'spacing' }}')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                    @endforeach
                </fieldset>
            @endif
            <fieldset class="article-template-editorial-journal-group">
                <legend>{{ t('Responsive spacing') }}</legend>
                <div class="article-template-device-tabs" role="group" aria-label="{{ t('Device') }}"><button v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" type="button" :class="{ active: optionsDevice === device }" :aria-pressed="optionsDevice === device" @click="selectOptionsDevice(device)"><i :class="device === 'desktop' ? 'fas fa-desktop' : (device === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ device }}</button></div>
                @foreach (($layoutTemplate === 'mosaic-classic' ? ['gap' => 'Story gap', 'lead_height' => 'Lead image height', 'side_height' => 'Side image height', 'highlight_height' => 'Highlight image height'] : ['gap' => 'Grid gap']) as $field => $label)
                    <label>{{ t($label) }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('{{ $layoutDefinition['key'] }}.{{ $field }}.'+optionsDevice, '{{ $field === 'gap' ? 'spacing' : 'border' }}')" :step="dimensionStep('{{ $layoutDefinition['key'] }}.{{ $field }}.'+optionsDevice, '{{ $field === 'gap' ? 'spacing' : 'border' }}')" :value="dimensionValue('{{ $layoutDefinition['key'] }}.{{ $field }}.'+optionsDevice, '{{ $field === 'gap' ? 'spacing' : 'border' }}')" @input="setDimensionValue('{{ $layoutDefinition['key'] }}.{{ $field }}.'+optionsDevice, $event.target.value, '{{ $field === 'gap' ? 'spacing' : 'border' }}')"><select class="form-select" aria-label="{{ t($label.' unit') }}" :value="dimensionUnit('{{ $layoutDefinition['key'] }}.{{ $field }}.'+optionsDevice, '{{ $field === 'gap' ? 'spacing' : 'border' }}')" @change="setDimensionUnit('{{ $layoutDefinition['key'] }}.{{ $field }}.'+optionsDevice, $event.target.value, '{{ $field === 'gap' ? 'spacing' : 'border' }}')"><option v-for="unit in unitChoices('{{ $field === 'gap' ? 'spacing' : 'border' }}')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                @endforeach
                @if ($layoutTemplate === 'mosaic-classic')<small>{{ t('Image heights use px, em, rem, or pt because the story container has an automatic height.') }}</small>@endif
                <small>{{ t('Set the regular grid columns in Grid columns. Set regular thumbnail height and fit in Thumbnail.') }}</small>
            </fieldset>
            <fieldset class="article-template-editorial-journal-group">
                <legend>{{ t('Story details') }}</legend>
                @if ($layoutTemplate === 'mosaic-classic')
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.mosaic_classic.show_author"><span class="form-check-label">{{ t('Show author') }}</span></label>
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.mosaic_classic.show_reading_time"><span class="form-check-label">{{ t('Show reading time') }}</span></label>
                @endif
                @foreach (['show_category' => 'Show category', 'show_excerpt' => 'Show excerpt', 'show_date' => 'Show date', 'show_read_more' => 'Show Read article link'] as $field => $label)
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.{{ $layoutDefinition['key'] }}.{{ $field }}"><span class="form-check-label">{{ t($label) }}</span></label>
                @endforeach
                @if ($layoutTemplate === 'balanced-card-grid')
                    <label v-if="optionsModal.value.balanced_grid.show_excerpt">{{ t('Excerpt lines') }}<input class="form-control" type="number" min="1" max="5" step="1" v-model.number="optionsModal.value.balanced_grid.excerpt_lines"></label>
                @endif
            </fieldset>
        </div>
    </section>
@endforeach
