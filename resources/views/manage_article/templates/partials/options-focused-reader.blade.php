<section v-if="optionsModal.section === 'focused-reader' && surface === 'detail' && activeTemplateKey === 'focused-reader' && optionsModal.value.focused_reader" id="article-template-option-panel-focused-reader" role="tabpanel" aria-labelledby="article-template-option-tab-focused-reader" tabindex="-1" class="article-template-options-section">
    <div class="article-template-options-section__heading"><div><h5>{{ t('Focused Reader') }}</h5><small>{{ t('Article content uses the same styles as CKEditor across all detail templates. Adjust the template header, layout, cover and navigation below.') }}</small></div></div>
    <div class="article-template-device-tabs" role="group" aria-label="{{ t('Device') }}">
        <button v-for="device in ['desktop', 'tablet', 'mobile']" :key="device" type="button" :class="{ active: optionsDevice === device }" :aria-pressed="optionsDevice === device" @click="selectOptionsDevice(device)"><i :class="device === 'desktop' ? 'fas fa-desktop' : (device === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ device }}</button>
    </div>
    <div class="article-template-reader-fields">
        <fieldset><legend>{{ t('Layout') }}</legend><div class="article-template-options-fields">
            @foreach (['reading_width' => ['Reading column max width', 480, 900], 'cover_width' => ['Cover max width', 480, 1200], 'title_width' => ['Title max width', 400, 1000]] as $field => $spec)
                <label>{{ t($spec[0]) }} (px)<input class="form-control" type="number" min="{{ $spec[1] }}" max="{{ $spec[2] }}" step="1" v-model.number="optionsModal.value.focused_reader.{{ $field }}"></label>
            @endforeach
            <label>{{ t('Header alignment') }} · @{{ optionsDevice }}<select class="form-select" v-model="optionsModal.value.focused_reader.alignment[optionsDevice]"><option value="left">{{ t('Left') }}</option><option value="center">{{ t('Center') }}</option></select></label>
        </div><small>{{ t('Widths are maximums and shrink to fit the screen. Use Detail shell for page padding and margins.') }}</small></fieldset>
        @foreach (collect(\App\Support\Article\ArticleTemplateOptions::FOCUSED_READER_FIELDS)->groupBy(fn ($spec) => $spec[0], preserveKeys: true) as $group => $fields)
            @continue($group === 'Article content')
            <fieldset><legend>{{ t($group) }} · @{{ optionsDevice }}</legend><div class="article-template-options-fields">
                @foreach ($fields as $field => $spec)
                    @continue(str_ends_with($field, '_size') || in_array($field, ['paragraph_gap', 'heading_gap']))
                    <label>{{ t($spec[1]) }}{{ $spec[6] ? ' ('.$spec[6].')' : '' }}<input class="form-control" type="number" min="{{ $spec[3] }}" max="{{ $spec[4] }}" step="{{ $spec[5] }}" v-model.number="optionsModal.value.focused_reader.{{ $field }}[optionsDevice]"></label>
                @endforeach
                @if ($group === 'Metadata')
                    <label>{{ t('Position') }}<select class="form-select" v-model="optionsModal.value.focused_reader.metadata_position"><option value="after-summary">{{ t('Below summary') }}</option><option value="before-summary">{{ t('Above summary') }}</option></select></label>
                    <label>{{ t('Separator') }}<select class="form-select" v-model="optionsModal.value.focused_reader.metadata_separator"><option value="none">{{ t('None') }}</option><option value="dot">{{ t('Dot') }}</option><option value="line">{{ t('Line') }}</option></select></label>
                @elseif ($group === 'Cover')
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.focused_reader.cover_enabled"><span class="form-check-label">{{ t('Show cover') }}</span></label>
                    <label>{{ t('Aspect ratio') }}<select class="form-select" v-model="optionsModal.value.focused_reader.cover_ratio[optionsDevice]"><option value="auto">{{ t('Original') }}</option><option value="5/2">5:2</option><option value="16/9">16:9</option><option value="3/2">3:2</option><option value="4/3">4:3</option></select></label>
                    <label>{{ t('Cover width') }}<select class="form-select" v-model="optionsModal.value.focused_reader.cover_width_mode"><option value="wide">{{ t('Wide') }}</option><option value="reading">{{ t('Match reading column') }}</option></select></label>
                    <label>{{ t('Image fit') }}<select class="form-select" v-model="optionsModal.value.focused_reader.cover_fit"><option value="cover">{{ t('Cover') }}</option><option value="contain">{{ t('Contain') }}</option></select></label>
                    <label>{{ t('Crop position') }}<select class="form-select" v-model="optionsModal.value.focused_reader.cover_position">@foreach (['center', 'top', 'bottom', 'left', 'right'] as $position)<option value="{{ $position }}">{{ t(ucfirst($position)) }}</option>@endforeach</select></label>
                    <label class="article-template-reader-wide">{{ t('Template caption (optional, applies to all articles)') }}<input class="form-control" type="text" maxlength="240" v-model="optionsModal.value.focused_reader.cover_caption"></label>
                @elseif ($group === 'Article navigation')
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.focused_reader.navigation_thumbnails"><span class="form-check-label">{{ t('Show thumbnails') }}</span></label>
                    <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.focused_reader.navigation_divider"><span class="form-check-label">{{ t('Show divider') }}</span></label>
                @endif
            </div></fieldset>
        @endforeach
    </div>
</section>
