<section v-if="optionsModal.section === 'mosaic-magazine' && surface === 'archive' && activeTemplateKey === 'mosaic-magazine' && optionsModal.value.mosaic_magazine" id="article-template-option-panel-mosaic-magazine" role="tabpanel" aria-labelledby="article-template-option-tab-mosaic-magazine" tabindex="-1" class="article-template-options-section">
    <div class="article-template-options-section__heading"><span><i class="fas fa-th-large" aria-hidden="true"></i></span><div><h5>{{ t('Mosaic Magazine') }}</h5><small>{{ t('Arrange the posts between the hero and the regular grid.') }}</small></div></div>
    <div class="article-template-options-fields">
        <label>{{ t('Posts after hero') }}<input class="form-control" type="number" min="0" max="24" step="1" v-model.number="optionsModal.value.mosaic_magazine.supporting_count" aria-describedby="mosaic-supporting-count-help"><small id="mosaic-supporting-count-help">{{ t('0 sends every post after the hero directly to the regular grid. This does not change posts per page.') }}</small></label>
        <template v-if="optionsModal.value.mosaic_magazine.supporting_count > 0">
            <label class="form-check form-switch"><input class="form-check-input" type="checkbox" v-model="optionsModal.value.mosaic_magazine.match_grid"><span class="form-check-label">{{ t('Match regular grid columns') }}</span></label>
            <p v-if="optionsModal.value.mosaic_magazine.match_grid" class="text-muted">{{ t('Columns follow Grid columns for each device.') }}</p>
            <div v-else>
                <div class="article-template-device-tabs" role="group" aria-label="{{ t('Device') }}"><button v-for="device in ['desktop', 'tablet', 'mobile']" :key="'mosaic-'+device" type="button" :class="{ active: optionsDevice === device }" :aria-pressed="optionsDevice === device" @click="selectOptionsDevice(device)"><i :class="device === 'desktop' ? 'fas fa-desktop' : (device === 'tablet' ? 'fas fa-tablet-alt' : 'fas fa-mobile-alt')" aria-hidden="true"></i>@{{ device }}</button></div>
                <label>{{ t('Columns after hero') }}<select class="form-select" v-model.number="optionsModal.value.mosaic_magazine.columns[optionsDevice]"><option v-for="column in columnChoices(optionsDevice)" :key="'mosaic-column-'+column" :value="column">@{{ column }} {{ t('columns') }}</option></select></label>
            </div>
            <label>{{ t('Two-column proportion') }}<select class="form-select" v-model="optionsModal.value.mosaic_magazine.proportion" :disabled="(optionsModal.value.mosaic_magazine.match_grid ? optionsModal.value.grid[optionsDevice] : optionsModal.value.mosaic_magazine.columns[optionsDevice]) !== 2"><option value="equal">{{ t('Equal') }}</option><option value="asymmetric">{{ t('Asymmetric 60:40') }}</option></select><small>{{ t('60:40 applies only on devices with two columns. Other column counts remain equal.') }}</small></label>
        </template>
    </div>
</section>
