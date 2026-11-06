<span class="ph-article-status" :data-code="event.publication_status">
    <i class="far fa-fw" :class="event.publication_status === 'published' ? 'fa-check-circle' : (event.publication_status === 'hidden' ? 'fa-eye-slash' : 'fa-file-alt')" aria-hidden="true"></i>
    <span>@{{ statusLabel(event.publication_status) }}</span>
</span>
