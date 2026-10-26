<span class="ph-article-status" :data-code="info.status_code">
    <i class="far fa-fw" :class="info.status_code === 'publish' ? 'fa-check-circle' : (info.status_code === 'pending' ? 'fa-clock' : 'fa-file-alt')" aria-hidden="true"></i>
    <span v-if="info.status_name">@{{ info.status_name }}</span><span v-else>{{ t('Unknown') }}</span>
</span>
