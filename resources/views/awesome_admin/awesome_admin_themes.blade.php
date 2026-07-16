@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Themes') }}
@endsection

@section('content')
	<style>
		[v-cloak]
		{
			display: none !important;
		}

		.theme-manager-panel
		{
			--theme-manager-primary: var(--ph-theme-primary, #6542d7);
			--theme-manager-primary-hover: var(--ph-theme-primary-hover, #5435bd);
			--theme-manager-primary-soft: var(--ph-theme-primary-subtle, rgba(101, 66, 215, .1));
			--theme-manager-text: var(--ph-text-main, #25222b);
			--theme-manager-muted: var(--ph-text-muted, #77727f);
			--theme-manager-border: var(--ph-border-color, rgba(0, 0, 0, .12));
			--theme-manager-surface: var(--ph-bg-card, var(--ph-content-bg, #fff));
			overflow: hidden;
			padding: 0 !important;
			color: var(--theme-manager-text);
		}

		.theme-manager-heading
		{
			display: flex;
			align-items: flex-start;
			justify-content: space-between;
			gap: 24px;
			padding: 26px 28px 22px;
			border-bottom: 1px solid var(--theme-manager-border);
		}

		.theme-manager-eyebrow
		{
			display: flex;
			align-items: center;
			gap: 8px;
			margin: 0 0 5px;
			color: var(--theme-manager-primary);
			font-size: 11px;
			font-weight: 800;
			letter-spacing: .09em;
			text-transform: uppercase;
		}

		.theme-manager-heading h1
		{
			margin: 0;
			font-size: clamp(22px, 2vw, 28px);
			font-weight: 700;
			line-height: 1.2;
			letter-spacing: -.025em;
		}

		.theme-manager-heading-copy > p:last-child
		{
			max-width: 650px;
			margin: 8px 0 0;
			color: var(--theme-manager-muted);
			font-size: 13px;
		}

		.theme-manager-installed-count
		{
			display: inline-flex;
			flex: 0 0 auto;
			align-items: center;
			gap: 8px;
			min-height: 34px;
			padding: 7px 11px;
			border: 1px solid var(--theme-manager-border);
			border-radius: 9px;
			color: var(--theme-manager-muted);
			background: var(--ph-bg-hover, rgba(0, 0, 0, .035));
			font-size: 12px;
			font-weight: 700;
		}

		.theme-manager-installed-count i
		{
			color: var(--theme-manager-primary);
		}

		.theme-manager-body
		{
			padding: 28px;
		}

		.theme-manager-setting-row
		{
			display: grid;
			grid-template-columns: minmax(180px, 220px) minmax(0, 1fr);
			gap: clamp(28px, 4vw, 54px);
			align-items: start;
		}

		.theme-manager-setting-copy
		{
			padding-top: 3px;
		}

		.theme-manager-setting-copy h2
		{
			margin: 0;
			font-size: 14px;
			font-weight: 700;
			line-height: 1.35;
		}

		.theme-manager-setting-copy p
		{
			margin: 6px 0 0;
			color: var(--theme-manager-muted);
			font-size: 12px;
			line-height: 1.55;
		}

		.theme-manager-grid
		{
			display: grid;
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 18px;
		}

		.theme-manager-card
		{
			position: relative;
			min-width: 0;
			overflow: hidden;
			border: 1px solid var(--theme-manager-border);
			border-radius: 12px;
			background: var(--theme-manager-surface);
			box-shadow: 0 2px 10px rgba(44, 36, 55, .04);
			cursor: pointer;
			transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
		}

		.theme-manager-card:hover
		{
			border-color: color-mix(in srgb, var(--theme-manager-primary), transparent 42%);
			box-shadow: 0 8px 22px rgba(44, 36, 55, .09);
			transform: translateY(-1px);
		}

		.theme-manager-card:focus-visible
		{
			outline: 3px solid color-mix(in srgb, var(--theme-manager-primary), transparent 78%);
			outline-offset: 3px;
		}

		.theme-manager-card.is-selected
		{
			border-color: var(--theme-manager-primary);
			box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-manager-primary), transparent 82%), 0 9px 24px rgba(58, 41, 90, .11);
		}

		.theme-manager-preview
		{
			position: relative;
			aspect-ratio: 16 / 9;
			overflow: hidden;
			border-bottom: 1px solid var(--theme-manager-border);
			background: var(--ph-bg-hover, #eceaef);
		}

		.theme-manager-preview img
		{
			display: block;
			width: 100%;
			height: 100%;
			object-fit: cover;
			object-position: top left;
			transition: transform .25s ease;
		}

		.theme-manager-card:hover .theme-manager-preview img
		{
			transform: scale(1.015);
		}

		.theme-manager-selection-mark
		{
			position: absolute;
			top: 10px;
			right: 10px;
			display: grid;
			width: 25px;
			height: 25px;
			place-items: center;
			border: 2px solid #fff;
			border-radius: 50%;
			color: #fff;
			background: var(--theme-manager-primary);
			box-shadow: 0 3px 10px rgba(44, 25, 90, .24);
			font-size: 10px;
			opacity: 0;
			transform: scale(.7);
			transition: opacity .18s ease, transform .18s ease;
		}

		.theme-manager-card.is-selected .theme-manager-selection-mark
		{
			opacity: 1;
			transform: scale(1);
		}

		.theme-manager-preview-button
		{
			position: absolute;
			left: 50%;
			bottom: 12px;
			display: inline-flex;
			align-items: center;
			gap: 7px;
			min-height: 32px;
			padding: 6px 10px;
			border: 1px solid rgba(255, 255, 255, .75);
			border-radius: 8px;
			color: #28232e;
			background: rgba(255, 255, 255, .94);
			box-shadow: 0 5px 18px rgba(25, 19, 31, .18);
			font-size: 11px;
			font-weight: 800;
			opacity: 0;
			cursor: pointer;
			transform: translate(-50%, 7px);
			transition: opacity .18s ease, transform .18s ease, background .18s ease;
		}

		.theme-manager-card:hover .theme-manager-preview-button,
		.theme-manager-card:focus-within .theme-manager-preview-button
		{
			opacity: 1;
			transform: translate(-50%, 0);
		}

		.theme-manager-card-body
		{
			padding: 15px 16px 16px;
		}

		.theme-manager-title-line
		{
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 12px;
		}

		.theme-manager-title
		{
			min-width: 0;
			margin: 0;
			font-size: 13px;
			font-weight: 800;
			letter-spacing: -.01em;
		}

		.theme-manager-status
		{
			display: inline-flex;
			flex: 0 0 auto;
			align-items: center;
			gap: 5px;
			min-height: 23px;
			padding: 3px 7px;
			border-radius: 999px;
			color: var(--theme-manager-muted);
			background: var(--ph-bg-hover, #f4f2f5);
			font-size: 10px;
			font-weight: 800;
		}

		.theme-manager-status-dot
		{
			width: 6px;
			height: 6px;
			border-radius: 50%;
			background: currentColor;
		}

		.theme-manager-card.is-active .theme-manager-status
		{
			color: #168b62;
			background: #eaf8f2;
		}

		.theme-manager-description
		{
			min-height: 38px;
			margin: 7px 0 12px;
			color: var(--theme-manager-muted);
			font-size: 11px;
			line-height: 1.55;
		}

		.theme-manager-meta
		{
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 12px;
			padding-top: 11px;
			border-top: 1px solid var(--theme-manager-border);
			color: var(--theme-manager-muted);
			font-size: 10px;
			font-weight: 700;
		}

		.theme-manager-meta span
		{
			display: inline-flex;
			align-items: center;
			gap: 5px;
		}

		.theme-manager-footer
		{
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 20px;
			margin-top: 30px;
			padding-top: 22px;
			border-top: 1px solid var(--theme-manager-border);
		}

		.theme-manager-save-note
		{
			display: flex;
			align-items: center;
			gap: 8px;
			margin: 0;
			color: var(--theme-manager-muted);
			font-size: 11px;
		}

		.theme-manager-save-note i
		{
			color: var(--theme-manager-primary);
		}

		.theme-manager-actions
		{
			display: flex;
			align-items: center;
			gap: 9px;
		}

		.theme-manager-actions .btn
		{
			min-height: 36px;
			font-size: 12px;
			font-weight: 700;
		}

		.theme-manager-modal-stage
		{
			max-height: calc(100vh - 190px);
			overflow: auto;
			padding: 18px;
			background: var(--ph-bg-body, #f4f2f5);
		}

		.theme-manager-modal-stage img
		{
			display: block;
			width: 100%;
			height: auto;
			border: 1px solid var(--theme-manager-border);
			border-radius: 10px;
			background: #fff;
			box-shadow: 0 8px 28px rgba(40, 31, 50, .11);
		}

		@media (max-width: 850px)
		{
			.theme-manager-setting-row
			{
				grid-template-columns: 1fr;
				gap: 19px;
			}

			.theme-manager-setting-copy
			{
				max-width: 620px;
			}
		}

		@media (max-width: 620px)
		{
			.theme-manager-heading
			{
				display: block;
				padding: 21px 18px 19px;
			}

			.theme-manager-installed-count
			{
				margin-top: 15px;
			}

			.theme-manager-body
			{
				padding: 22px 18px;
			}

			.theme-manager-grid
			{
				grid-template-columns: 1fr;
			}

			.theme-manager-description
			{
				min-height: 0;
			}

			.theme-manager-footer
			{
				align-items: stretch;
				flex-direction: column;
			}

			.theme-manager-actions
			{
				justify-content: flex-end;
			}

			.theme-manager-preview-button
			{
				opacity: 1;
				transform: translate(-50%, 0);
			}
		}

		@media (prefers-reduced-motion: reduce)
		{
			.theme-manager-panel *,
			.theme-manager-panel *::before,
			.theme-manager-panel *::after
			{
				transition: none !important;
			}
		}
	</style>

	<div id="ph-app-theme-manager"
		data-update-url="{{ route('cms.admin.awesome_admin.themes.update') }}"
		data-active-theme="{{ $activeThemeCode }}"
		data-themes="{{ $themes->toJson() }}">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.themes') }}
		</div>

		<div class="ph-notice" v-cloak>
			<div class="toast-container position-fixed top-0 end-0 p-3">
				<div ref="noticeToast" :class="'toast ph-notice-toast ph-callout-no-border '+responseStatus" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3200">
					<div :class="'toast-header '+responseStatus+' pe-3 pt-3 pb-1 border-0'">
						<strong class="toast-header-title toast-header-icon me-auto">{{ t('Notice') }}</strong>
						<small>{{ t('just now') }}</small>
						<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ t('Close') }}"></button>
					</div>
					<div class="toast-body p-3 text-start">@{{ responseMessage }}</div>
				</div>
			</div>
		</div>

		<form v-on:submit.prevent="saveChanges">
			<div class="ph-content rounded theme-manager-panel">
				<header class="theme-manager-heading">
					<div class="theme-manager-heading-copy">
						<p class="theme-manager-eyebrow"><i class="fas fa-palette" aria-hidden="true"></i> {{ t('Appearance') }}</p>
						<h1>{{ t('Manage Themes') }}</h1>
						<p>{{ t('Choose the visual foundation for your CMS. Review each theme before applying changes across the administration area.') }}</p>
					</div>
					<span class="theme-manager-installed-count"><i class="fas fa-layer-group" aria-hidden="true"></i> {{ $themes->count() }} {{ t('themes installed') }}</span>
				</header>

				<section class="theme-manager-body" aria-label="{{ t('Theme settings') }}">
					<div class="theme-manager-setting-row">
						<div class="theme-manager-setting-copy">
							<h2>{{ t('CMS interface theme') }}</h2>
							<p>{{ t('Select the theme used throughout your CMS dashboard. The current theme remains active until you save the new selection.') }}</p>
						</div>

						<div id="theme-grid" class="theme-manager-grid" role="radiogroup" aria-label="{{ t('Installed CMS themes') }}">
							@foreach ($themes as $theme)
								<article
									class="theme-manager-card @if ($activeThemeCode === $theme['code']) is-selected is-active @endif"
									:class="{'is-selected': isSelected('{{ $theme['code'] }}'), 'is-active': isActive('{{ $theme['code'] }}')}"
									role="radio"
									:aria-checked="String(isSelected('{{ $theme['code'] }}'))"
									tabindex="0"
									v-on:click="selectTheme('{{ $theme['code'] }}')"
									v-on:keydown.enter.prevent="selectTheme('{{ $theme['code'] }}')"
									v-on:keydown.space.prevent="selectTheme('{{ $theme['code'] }}')">
									<div class="theme-manager-preview">
										<img src="{{ asset($theme['preview_image']) }}" alt="{{ $theme['name'] }} dashboard theme preview">
										<span class="theme-manager-selection-mark" aria-hidden="true"><i class="fas fa-check"></i></span>
										<button class="theme-manager-preview-button" type="button" v-on:click.stop="openPreview('{{ $theme['code'] }}')" aria-label="{{ t('Open {1} live preview', $theme['name']) }}">
											<i class="fas fa-expand-alt" aria-hidden="true"></i> {{ t('Live preview') }}
										</button>
									</div>

									<div class="theme-manager-card-body">
										<div class="theme-manager-title-line">
											<h3 class="theme-manager-title">{{ $theme['name'] }}</h3>
											<span class="theme-manager-status">
												<span class="theme-manager-status-dot"></span>
												<span v-if="isActive('{{ $theme['code'] }}')">{{ t('Active') }}</span>
												<span v-else-if="isSelected('{{ $theme['code'] }}')">{{ t('Selected') }}</span>
												<span v-else>{{ t('Available') }}</span>
											</span>
										</div>

										<p class="theme-manager-description">{{ $theme['description'] }}</p>
										<div class="theme-manager-meta">
											<span><i class="fas fa-code-branch" aria-hidden="true"></i> {{ t('Version') }} {{ $theme['version'] }}</span>
											<span><i class="fas fa-desktop" aria-hidden="true"></i> CMS</span>
										</div>
									</div>
								</article>
							@endforeach
						</div>
					</div>

					<footer class="theme-manager-footer">
						<p class="theme-manager-save-note"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ t('Your current theme stays active until changes are saved.') }}</p>
						<div class="theme-manager-actions">
							<button type="button" class="btn btn-outline-secondary" :disabled="!hasChanges || isSubmitting" v-on:click="cancelChanges">{{ t('Cancel') }}</button>
							<button type="submit" class="btn ph-btn-theme" :disabled="!hasChanges || isSubmitting">
								<span v-if="isSubmitting"><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> {{ t('Saving') }}</span>
								<span v-else><i class="fas fa-check me-1" aria-hidden="true"></i> {{ t('Save changes') }}</span>
							</button>
						</div>
					</footer>
				</section>
			</div>
		</form>

		<Teleport to="body">
			<div class="modal fade" id="themeManagerPreviewModal" tabindex="-1" aria-labelledby="themeManagerPreviewTitle" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
					<div class="modal-content">
						<div class="modal-header">
							<div>
								<h5 class="modal-title" id="themeManagerPreviewTitle"><span v-if="previewTheme">@{{ previewTheme.name }} {{ t('preview') }}</span><span v-else>{{ t('Theme preview') }}</span></h5>
								<small class="text-muted">{{ t('Preview only. No changes are applied from this window.') }}</small>
							</div>
							<button type="button" class="btn-close" data-bs-dismiss="modal" v-on:click="closePreview" aria-label="{{ t('Close') }}"></button>
						</div>
						<div class="modal-body theme-manager-modal-stage">
							<img v-if="previewTheme" :src="previewTheme.preview_url" :alt="previewTheme.name+' enlarged dashboard preview'">
						</div>
					</div>
				</div>
			</div>
		</Teleport>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_themes/vueV3-manage-themes-2026.js?v=').time() }}"></script>
@endpushonce
