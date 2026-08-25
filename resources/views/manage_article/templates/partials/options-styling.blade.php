<section v-if="surface === 'archive'" class="article-template-options-section">
    <div class="article-template-options-section__heading"><span>4</span><div><strong>{{ t('Thumbnail') }}</strong><small>{{ t('Choose how every archive thumbnail is rendered and framed.') }}</small></div></div>
    <div class="article-template-options-fields">
        <div class="article-template-option-row article-template-option-row--three">
            <label>{{ t('Display mode') }}<select class="form-select" v-model="optionsModal.value.thumbnail.mode"><option value="background">{{ t('Background image') }}</option><option value="asset">{{ t('Full asset image') }}</option></select></label>
            <label>{{ t('Image fit') }}<select class="form-select" v-model="optionsModal.value.thumbnail.fit"><option value="cover">Cover</option><option value="contain">Contain</option></select></label>
            <label>{{ t('Background color') }}<input class="form-control article-template-coloris" type="text" v-model="optionsModal.value.thumbnail.background_color"></label>
        </div>
        <div class="article-template-option-row article-template-option-row--frame">
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.thumbnail.frame.enabled" @change="scheduleColorisInit"><span class="form-check-label">{{ t('Override thumbnail frame') }}</span></label>
            <div v-if="optionsModal.value.thumbnail.frame.enabled" class="article-template-frame-fields">
                <label>{{ t('Border color') }}<input class="form-control article-template-coloris" type="text" v-model="optionsModal.value.thumbnail.frame.border_color"></label>
                <label>{{ t('Border width') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('thumbnail.frame.border_width', 'border')" :step="dimensionStep('thumbnail.frame.border_width', 'border')" :value="dimensionValue('thumbnail.frame.border_width', 'border')" @input="setDimensionValue('thumbnail.frame.border_width', $event.target.value, 'border')"><select class="form-select" :value="dimensionUnit('thumbnail.frame.border_width', 'border')" @change="setDimensionUnit('thumbnail.frame.border_width', $event.target.value, 'border')"><option v-for="unit in unitChoices('border')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                <label>{{ t('Border radius') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('thumbnail.frame.radius', 'radius')" :step="dimensionStep('thumbnail.frame.radius', 'radius')" :value="dimensionValue('thumbnail.frame.radius', 'radius')" @input="setDimensionValue('thumbnail.frame.radius', $event.target.value, 'radius')"><select class="form-select" :value="dimensionUnit('thumbnail.frame.radius', 'radius')" @change="setDimensionUnit('thumbnail.frame.radius', $event.target.value, 'radius')"><option v-for="unit in unitChoices('radius')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
            </div>
        </div>
    </div>
</section>

<section v-if="surface === 'archive'" class="article-template-options-section">
    <div class="article-template-options-section__heading"><span>5</span><div><strong>{{ t('Pagination') }}</strong><small>{{ t('Control total data, alignment, footer frame, and responsive spacing.') }}</small></div></div>
    <div class="article-template-options-fields">
        <div class="article-template-option-row article-template-option-row--toolbar">
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.pagination.show_total"><span class="form-check-label">{{ t('Show Total Data') }}</span></label>
            <div class="btn-group article-template-position" role="group" aria-label="{{ t('Pagination position') }}"><button v-for="position in ['left', 'center', 'right']" :key="position" type="button" class="btn" :class="optionsModal.value.pagination.position === position ? 'btn-primary' : 'btn-outline-secondary'" @click="optionsModal.value.pagination.position = position">@{{ position }}</button></div>
        </div>
        <div class="article-template-option-row article-template-option-row--frame">
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.pagination.frame.enabled" @change="scheduleColorisInit"><span class="form-check-label">{{ t('Show footer frame') }}</span></label>
            <div v-if="optionsModal.value.pagination.frame.enabled" class="article-template-frame-fields">
                <label>{{ t('Border color') }}<input class="form-control article-template-coloris" type="text" v-model="optionsModal.value.pagination.frame.border_color"></label>
                <label>{{ t('Border width') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('pagination.frame.border_width', 'border')" :step="dimensionStep('pagination.frame.border_width', 'border')" :value="dimensionValue('pagination.frame.border_width', 'border')" @input="setDimensionValue('pagination.frame.border_width', $event.target.value, 'border')"><select class="form-select" :value="dimensionUnit('pagination.frame.border_width', 'border')" @change="setDimensionUnit('pagination.frame.border_width', $event.target.value, 'border')"><option v-for="unit in unitChoices('border')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                <label>{{ t('Border radius') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('pagination.frame.radius', 'radius')" :step="dimensionStep('pagination.frame.radius', 'radius')" :value="dimensionValue('pagination.frame.radius', 'radius')" @input="setDimensionValue('pagination.frame.radius', $event.target.value, 'radius')"><select class="form-select" :value="dimensionUnit('pagination.frame.radius', 'radius')" @change="setDimensionUnit('pagination.frame.radius', $event.target.value, 'radius')"><option v-for="unit in unitChoices('radius')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                <label>{{ t('Background color') }}<input class="form-control article-template-coloris" type="text" v-model="optionsModal.value.pagination.frame.background_color"></label>
            </div>
        </div>
        <template v-for="box in spacingBoxes" :key="'pagination-'+box.key">
            <div class="article-template-box-heading"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.pagination[box.key].enabled"><span class="form-check-label">@{{ box.label }}</span></label></div>
            <div v-if="optionsModal.value.pagination[box.key].enabled" class="article-template-box-control">
                <div class="article-template-device-tabs article-template-device-tabs--compact"><button v-for="deviceName in ['desktop', 'tablet', 'mobile']" :key="box.key+'-'+deviceName" type="button" :class="{ active: optionsDevice === deviceName }" @click="optionsDevice = deviceName"><i :class="deviceName === 'desktop' ? 'fas fa-desktop' : (deviceName === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ deviceName }}</button></div>
                <div class="article-template-box-control__inputs"><label v-for="side in boxSides" :key="side.key"><input class="form-control" type="number" min="0" :max="boxMax('pagination.'+box.key, optionsDevice)" :step="boxStep('pagination.'+box.key, optionsDevice)" :value="boxValue('pagination.'+box.key, optionsDevice, side.key)" @input="setBoxValue('pagination.'+box.key, optionsDevice, side.key, $event.target.value)"><span>@{{ side.label }}</span></label><button type="button" class="btn article-template-box-control__link" :class="{ active: isBoxLinked('pagination.'+box.key, optionsDevice) }" :title="isBoxLinked('pagination.'+box.key, optionsDevice) ? 'Unlink values' : 'Link values'" @click="toggleBoxLinked('pagination.'+box.key, optionsDevice)"><i class="fas" :class="isBoxLinked('pagination.'+box.key, optionsDevice) ? 'fa-link' : 'fa-unlink'"></i></button></div>
                <label class="article-template-box-control__unit">{{ t('Unit') }}<select class="form-select" :value="boxUnit('pagination.'+box.key, optionsDevice)" @change="setBoxUnit('pagination.'+box.key, optionsDevice, $event.target.value)"><option v-for="unit in unitChoices()" :key="unit" :value="unit">@{{ unit }}</option></select></label>
            </div>
        </template>
    </div>
</section>

<section v-if="surface === 'archive'" class="article-template-options-section">
    <div class="article-template-options-section__heading"><span>6</span><div><strong>{{ t('Article title tag') }}</strong><small>{{ t('Set the semantic heading level for all archive item titles.') }}</small></div></div>
    <label class="article-template-select-label">{{ t('Heading level') }}<select class="form-select" v-model="optionsModal.value.article_title.tag"><option v-for="tag in ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']" :key="tag" :value="tag">@{{ tag.toUpperCase() }}</option></select></label>
</section>

<section class="article-template-options-section">
    <div class="article-template-options-section__heading"><span>@{{ surface === 'archive' ? 7 : 2 }}</span><div><strong>@{{ surface === 'archive' ? 'Archive shell' : 'Detail shell' }}</strong><small>{{ t('Apply responsive spacing and a global frame without changing the template layout.') }}</small></div></div>
    <div class="article-template-options-fields">
        <template v-for="box in spacingBoxes" :key="'shell-'+box.key">
            <div class="article-template-box-heading"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.shell[box.key].enabled"><span class="form-check-label">@{{ box.label }}</span></label></div>
            <div v-if="optionsModal.value.shell[box.key].enabled" class="article-template-box-control">
                <div class="article-template-device-tabs article-template-device-tabs--compact"><button v-for="deviceName in ['desktop', 'tablet', 'mobile']" :key="'shell-'+box.key+'-'+deviceName" type="button" :class="{ active: optionsDevice === deviceName }" @click="optionsDevice = deviceName"><i :class="deviceName === 'desktop' ? 'fas fa-desktop' : (deviceName === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ deviceName }}</button></div>
                <div class="article-template-box-control__inputs"><label v-for="side in boxSides" :key="side.key"><input class="form-control" type="number" min="0" :max="boxMax('shell.'+box.key, optionsDevice)" :step="boxStep('shell.'+box.key, optionsDevice)" :value="boxValue('shell.'+box.key, optionsDevice, side.key)" @input="setBoxValue('shell.'+box.key, optionsDevice, side.key, $event.target.value)"><span>@{{ side.label }}</span></label><button type="button" class="btn article-template-box-control__link" :class="{ active: isBoxLinked('shell.'+box.key, optionsDevice) }" :title="isBoxLinked('shell.'+box.key, optionsDevice) ? 'Unlink values' : 'Link values'" @click="toggleBoxLinked('shell.'+box.key, optionsDevice)"><i class="fas" :class="isBoxLinked('shell.'+box.key, optionsDevice) ? 'fa-link' : 'fa-unlink'"></i></button></div>
                <label class="article-template-box-control__unit">{{ t('Unit') }}<select class="form-select" :value="boxUnit('shell.'+box.key, optionsDevice)" @change="setBoxUnit('shell.'+box.key, optionsDevice, $event.target.value)"><option v-for="unit in unitChoices()" :key="unit" :value="unit">@{{ unit }}</option></select></label>
            </div>
        </template>
        <div class="article-template-option-row article-template-option-row--frame">
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.shell.frame.enabled" @change="scheduleColorisInit"><span class="form-check-label">{{ t('Override shell frame') }}</span></label>
            <div v-if="optionsModal.value.shell.frame.enabled" class="article-template-frame-fields">
                <label>{{ t('Border color') }}<input class="form-control article-template-coloris" type="text" v-model="optionsModal.value.shell.frame.border_color"></label>
                <label>{{ t('Border width') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('shell.frame.border_width', 'border')" :step="dimensionStep('shell.frame.border_width', 'border')" :value="dimensionValue('shell.frame.border_width', 'border')" @input="setDimensionValue('shell.frame.border_width', $event.target.value, 'border')"><select class="form-select" :value="dimensionUnit('shell.frame.border_width', 'border')" @change="setDimensionUnit('shell.frame.border_width', $event.target.value, 'border')"><option v-for="unit in unitChoices('border')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                <label>{{ t('Border radius') }}<span class="article-template-unit-control"><input class="form-control" type="number" min="0" :max="dimensionMax('shell.frame.radius', 'radius')" :step="dimensionStep('shell.frame.radius', 'radius')" :value="dimensionValue('shell.frame.radius', 'radius')" @input="setDimensionValue('shell.frame.radius', $event.target.value, 'radius')"><select class="form-select" :value="dimensionUnit('shell.frame.radius', 'radius')" @change="setDimensionUnit('shell.frame.radius', $event.target.value, 'radius')"><option v-for="unit in unitChoices('radius')" :key="unit" :value="unit">@{{ unit }}</option></select></span></label>
                <label>{{ t('Background color') }}<input class="form-control article-template-coloris" type="text" v-model="optionsModal.value.shell.frame.background_color"></label>
            </div>
        </div>
    </div>
</section>
