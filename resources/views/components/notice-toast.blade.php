<teleport to="body">
	<div class="ph-notice" v-cloak>
		<div aria-live="polite" aria-atomic="true" class="position-relative">
			<div class="toast-container position-fixed top-0 end-0 p-3">
				<div ref="noticeToast" :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
					<div :class="'toast-header '+responseStatusToast+' pe-3 pt-3 pb-1 border-0'">
						<strong class="toast-header-title toast-header-icon me-auto">{{ t('Notice') }}</strong>
						<small>{{ t('just now') }}</small>
						<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ t('Close') }}" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
					</div>
					<div class="toast-body p-3 text-start">
						<div v-if="isArrayMessageAfterSubmit == 1">
							<ul class="ps-3 m-0"><li v-for="(item, index) in responseMessageAfterSubmit" :key="index">@{{ Array.isArray(item) ? item.join(' ') : item }}</li></ul>
						</div>
						<div v-else>@{{ responseMessageAfterSubmit }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</teleport>
