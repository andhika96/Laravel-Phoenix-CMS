@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Site Config') }}
@endsection

@push('css')
	@foreach($getListFontCss as $fontCss)
		{!! nl2br($fontCss) !!}
	@endforeach

	<style>
	.vs__dropdown-toggle
	{
		padding: 0 0 2px;
	}

	.vs__selected,
	.vs__search,
	.vs__actions
	{
		margin: 0;
	}

	.site-information-grid
	{
		display: grid;
		grid-template-columns: minmax(0, 1fr) minmax(360px, 1fr);
		gap: 42px;
		align-items: start;
	}

	.site-information-grid > section
	{
		min-width: 0;
	}

	.site-settings-subheading
	{
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 16px;
		margin-bottom: 17px;
	}

	.site-settings-subheading strong
	{
		display: block;
		font-size: 13px;
		font-weight: 800;
	}

	.site-settings-subheading span
	{
		display: block;
		margin-top: 3px;
		color: var(--bs-secondary-color);
		font-size: 11px;
		line-height: 1.45;
	}

	.site-information-fields
	{
		display: grid;
		gap: 16px;
	}

	.site-thumbnail-card
	{
		padding: 15px;
		border: 1px solid #ded9e2;
		border-radius: 11px;
		background: #faf9fc;
	}

	.site-thumbnail-dropzone
	{
		position: relative;
		display: block;
		overflow: hidden;
		aspect-ratio: 16 / 9;
		border: 1px solid #d8d3dc;
		border-radius: 9px;
		background: #0fa1dd;
		cursor: pointer;
	}

	.site-thumbnail-dropzone.is-dragging,
	.site-thumbnail-dropzone:focus-visible
	{
		box-shadow: 0 0 0 4px rgba(101, 66, 215, .18);
		outline: none;
	}

	.site-thumbnail-dropzone img
	{
		display: block;
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.site-thumbnail-overlay
	{
		position: absolute;
		inset: auto 12px 12px;
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		padding: 9px 11px;
		border: 1px solid rgba(255, 255, 255, .45);
		border-radius: 8px;
		color: #fff;
		background: rgba(27, 21, 32, .78);
		backdrop-filter: blur(7px);
		font-size: 11px;
	}

	.site-thumbnail-overlay strong
	{
		display: block;
		font-size: 12px;
	}

	.site-thumbnail-actions
	{
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 12px;
		margin-top: 13px;
	}

	.site-thumbnail-file-name
	{
		min-width: 0;
		overflow: hidden;
		color: var(--bs-secondary-color);
		font-size: 11px;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.site-thumbnail-buttons
	{
		display: flex;
		flex: 0 0 auto;
		gap: 7px;
	}

	.site-typography-settings-layout
	{
		grid-column: 1 / -1;
		padding-top: 25px;
		border-top: 1px solid #ddd9e0;
	}

	.site-typography-heading-copy
	{
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.site-typography-heading-icon
	{
		display: inline-grid;
		place-items: center;
		width: 34px;
		height: 34px;
		flex: 0 0 auto;
		border: 1px solid #e0d9e9;
		border-radius: 9px;
		color: var(--ph-theme-primary);
		background: rgba(101, 66, 215, .08);
	}

	.site-settings-subheading .site-typography-heading-icon
	{
		display: inline-grid;
		margin-top: 0;
		font-size: inherit;
		line-height: normal;
	}

	.site-typography-heading-icon i
	{
		display: block;
		width: 1em;
		font-size: 16px;
		line-height: 1;
		text-align: center;
	}

	.site-typography-controls
	{
		display: grid;
		grid-template-columns: minmax(280px, 1fr) minmax(240px, 330px);
		gap: 16px;
		margin-bottom: 17px;
	}

	.site-logo-settings-grid
	{
		align-items: start;
	}

	.site-logo-upload-zone
	{
		display: grid;
		grid-template-columns: 82px minmax(0, 1fr);
		gap: 18px;
		align-items: center;
		min-height: 130px;
		padding: 19px;
		border: 1.5px dashed #c9c1d2;
		border-radius: 12px;
		background: #fbfafd;
		cursor: pointer;
		transition: border-color .16s ease, background-color .16s ease, box-shadow .16s ease;
	}

	.site-logo-upload-zone:hover,
	.site-logo-upload-zone.is-dragging,
	.site-logo-upload-zone:focus-visible
	{
		border-color: var(--ph-theme-primary);
		background: #f8f5ff;
		box-shadow: 0 0 0 3px rgba(101, 66, 215, .09);
		outline: none;
	}

	.site-logo-upload-visual
	{
		display: grid;
		place-items: center;
		width: 82px;
		height: 82px;
		overflow: hidden;
		border: 1px solid #ded8e5;
		border-radius: 12px;
		background: #fff;
	}

	.site-logo-upload-placeholder
	{
		color: var(--ph-theme-primary);
		font-size: 25px;
	}

	.site-logo-upload-preview
	{
		display: block;
		width: 100%;
		height: 100%;
		padding: 10px;
		object-fit: contain;
	}

	.site-logo-upload-copy strong
	{
		display: block;
		margin-bottom: 5px;
		font-size: 14px;
	}

	.site-logo-upload-copy span
	{
		display: block;
		color: var(--bs-secondary-color);
		font-size: 12px;
		line-height: 1.55;
	}

	.site-logo-upload-actions
	{
		display: flex;
		flex-wrap: wrap;
		gap: 9px;
		margin-top: 14px;
	}

	.site-logo-file-meta
	{
		display: grid;
		grid-template-columns: minmax(0, 1fr) auto;
		gap: 12px;
		align-items: center;
		margin-top: 13px;
		padding: 11px 13px;
		border: 1px solid #e5e1e8;
		border-radius: 9px;
		background: #faf9fb;
		font-size: 12px;
	}

	.site-logo-file-meta strong
	{
		display: block;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.site-logo-resize-card
	{
		display: grid;
		grid-template-columns: minmax(0, 1fr) minmax(210px, 260px);
		gap: 18px;
		align-items: center;
		margin-top: 14px;
		padding: 14px;
		border: 1px solid #e3dfe7;
		border-radius: 10px;
		background: #faf9fc;
	}

	.site-logo-resize-copy strong
	{
		display: block;
		margin-bottom: 4px;
		font-size: 13px;
	}

	.site-logo-resize-copy span
	{
		display: block;
		color: var(--bs-secondary-color);
		font-size: 12px;
		line-height: 1.45;
	}

	.site-logo-resize-fields
	{
		display: grid;
		grid-template-columns: minmax(0, 1fr) 84px;
		gap: 8px;
	}

	.site-logo-width-readout
	{
		grid-column: 1 / -1;
		color: #5f5668;
		font-size: 11px;
		font-weight: 700;
		text-align: right;
	}

	.site-logo-preview-panel
	{
		position: sticky;
		top: 24px;
		overflow: hidden;
		border: 1px solid #ded9e2;
		border-radius: 12px;
		background: #fff;
		box-shadow: 0 16px 45px rgba(42, 33, 54, .055);
	}

	.site-logo-preview-toolbar
	{
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 20px;
		padding: 24px 26px 20px;
		border-bottom: 1px solid #ece9ee;
	}

	.site-logo-preview-controls
	{
		display: inline-flex;
		padding: 3px;
		border: 1px solid #ddd8e2;
		border-radius: 9px;
		background: #f5f3f7;
	}

	.site-logo-preview-controls button
	{
		min-height: 31px;
		padding: 5px 10px;
		border: 0;
		border-radius: 6px;
		color: var(--bs-secondary-color);
		background: transparent;
		font-size: 12px;
		font-weight: 800;
	}

	.site-logo-preview-controls button.is-active
	{
		color: var(--bs-body-color);
		background: #fff;
		box-shadow: 0 1px 5px rgba(41, 33, 51, .1);
	}

	.site-logo-preview-stage
	{
		display: grid;
		place-items: center;
		min-height: 590px;
		padding: 24px;
		background: #27242c;
	}

	.site-logo-sidebar-preview
	{
		width: 256px;
		height: 540px;
		overflow: hidden;
		border-right: 1px solid #d9d5dd;
		border-radius: 2px;
		background: linear-gradient(180deg, #f7f8fc 0%, #f5edf7 100%);
		box-shadow: 0 20px 50px rgba(0, 0, 0, .24);
		transition: width .22s ease;
	}

	.site-logo-sidebar-preview.is-collapsed
	{
		width: 76px;
	}

	.site-logo-sidebar-brand
	{
		display: flex;
		height: 76px;
		align-items: center;
		gap: 10px;
		padding: 0 22px;
		border-bottom: 1px solid rgba(216, 211, 220, .25);
	}

	.site-logo-sidebar-preview.is-collapsed .site-logo-sidebar-brand
	{
		justify-content: center;
		padding: 0;
	}

	.site-logo-sidebar-image
	{
		display: block;
		flex: 0 0 auto;
		width: 34px;
		height: auto;
		max-width: 96px;
		max-height: 50px;
		object-fit: contain;
		transition: width .18s ease;
	}

	.site-logo-sidebar-name
	{
		min-width: 0;
		overflow: hidden;
		color: var(--bs-body-color);
		font-size: 15px;
		font-weight: 800;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.site-logo-sidebar-preview.is-collapsed .site-logo-sidebar-name
	{
		display: none;
	}

	.site-logo-sidebar-preview.is-collapsed .site-logo-sidebar-image
	{
		max-width: 56px;
		max-height: 50px;
	}

	.site-logo-sidebar-menu
	{
		padding: 20px 0;
	}

	.site-logo-sidebar-menu-item
	{
		display: flex;
		align-items: center;
		gap: 12px;
		min-height: 42px;
		margin: 0 14px 6px;
		padding: 0 14px;
		border-radius: 9px;
		color: var(--bs-body-color);
		font-size: 14px;
	}

	.site-logo-sidebar-menu-item.is-active
	{
		color: var(--ph-theme-primary);
		background: rgba(255, 255, 255, .86);
		box-shadow: 0 5px 16px rgba(60, 44, 78, .05);
	}

	.site-logo-sidebar-menu-item i
	{
		width: 17px;
		text-align: center;
		font-size: 15px;
	}

	.site-logo-sidebar-category
	{
		margin: 16px 0 15px;
		padding: 15px 28px 8px;
		border-top: 1px solid #d8d3dc;
		font-size: 11px;
		font-weight: 900;
		letter-spacing: .03em;
		text-transform: uppercase;
	}

	.site-logo-sidebar-preview.is-collapsed .site-logo-sidebar-menu-item
	{
		justify-content: center;
		width: 48px;
		margin-inline: auto;
		padding: 0;
	}

	.site-logo-sidebar-preview.is-collapsed .site-logo-sidebar-menu-item span,
	.site-logo-sidebar-preview.is-collapsed .site-logo-sidebar-category
	{
		display: none;
	}

	.site-logo-preview-note
	{
		padding: 14px 18px;
		border-top: 1px solid #ece9ee;
		color: var(--bs-secondary-color);
		background: #fff;
		font-size: 12px;
	}

	.site-logo-preview-note strong
	{
		color: var(--bs-body-color);
	}

	.site-typography-size-unit
	{
		max-width: 92px;
	}

	.site-typography-field-help
	{
		margin: 6px 0 0;
		color: var(--bs-secondary-color);
		font-size: 11px;
	}

	.site-typography-preview
	{
		overflow: hidden;
		border: 1px solid #ded9e2;
		border-radius: 12px;
		background: #fff;
		box-shadow: 0 14px 36px rgba(42, 33, 54, .045);
	}

	.site-typography-preview-header
	{
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 18px;
		padding: 15px 17px;
		border-bottom: 1px solid #ebe7ed;
		background: #fcfbfd;
	}

	.site-typography-preview-title
	{
		display: flex;
		align-items: center;
		gap: 10px;
		min-width: 0;
	}

	.site-typography-preview-icon
	{
		display: inline-grid;
		place-items: center;
		width: 34px;
		height: 34px;
		flex: 0 0 auto;
		border: 1px solid #e0d9e9;
		border-radius: 9px;
		color: var(--ph-theme-primary);
		background: rgba(101, 66, 215, .08);
	}

	.site-typography-preview-icon i
	{
		display: block;
		width: 1em;
		font-size: 15px;
		line-height: 1;
		text-align: center;
	}

	.site-typography-preview-title strong
	{
		display: block;
		font-size: 13px;
		font-weight: 800;
	}

	.site-typography-preview-title > div > span
	{
		display: block;
		margin-top: 2px;
		color: var(--bs-secondary-color);
		font-size: 11px;
	}

	.site-typography-preview-actions
	{
		display: flex;
		align-items: center;
		gap: 9px;
	}

	.site-typography-preview-meta
	{
		display: inline-flex;
		align-items: center;
		min-height: 30px;
		padding: 5px 10px;
		border: 1px solid #ded8e5;
		border-radius: 999px;
		color: #504757;
		background: #fff;
		font-size: 11px;
		font-weight: 800;
		white-space: nowrap;
	}

	.site-typography-preview-reset
	{
		display: inline-grid;
		place-items: center;
		width: 32px;
		height: 32px;
		padding: 0;
		border: 1px solid #dcd6e1;
		border-radius: 8px;
		color: #5e5664;
		background: #fff;
		transition: border-color .15s ease, color .15s ease, background .15s ease;
	}

	.site-typography-preview-reset:hover
	{
		border-color: #bfb4ca;
		color: var(--ph-theme-primary);
		background: rgba(101, 66, 215, .04);
	}

	.site-typography-preview-canvas
	{
		display: grid;
		grid-template-columns: minmax(0, 1fr) 148px;
		gap: 22px;
		align-items: stretch;
		padding: 22px;
		background: linear-gradient(135deg, rgba(101, 66, 215, .035), transparent 55%), #fff;
		font-family: var(--site-typography-font-family);
		font-size: var(--site-typography-font-size);
	}

	.site-typography-preview-copy
	{
		min-width: 0;
		padding: 3px 0;
	}

	.site-typography-preview-eyebrow
	{
		margin: 0 0 .7em;
		color: var(--ph-theme-primary);
		font-size: .72em;
		font-weight: 900;
		letter-spacing: .09em;
		text-transform: uppercase;
	}

	.site-typography-preview-copy h6
	{
		margin: 0;
		color: #1e1a22;
		font-family: inherit;
		font-size: 1.72em;
		font-weight: 800;
		letter-spacing: -.025em;
		line-height: 1.22;
	}

	.site-typography-preview-copy > p:not(.site-typography-preview-eyebrow)
	{
		margin: .85em 0 0;
		color: #645d69;
		font-family: inherit;
		font-size: 1em;
		line-height: 1.65;
	}

	.site-typography-preview-navigation
	{
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: .55em;
		margin-top: 1.2em;
	}

	.site-typography-preview-navigation span,
	.site-typography-preview-navigation button
	{
		min-height: 2.45em;
		padding: .55em .85em;
		border: 1px solid #e1dce5;
		border-radius: .62em;
		color: #38313e;
		background: #fff;
		font-family: inherit;
		font-size: .86em;
		font-weight: 700;
	}

	.site-typography-preview-navigation span:first-child
	{
		border-color: #d7cff1;
		color: var(--ph-theme-primary);
		background: rgba(101, 66, 215, .08);
	}

	.site-typography-preview-navigation button
	{
		border-color: var(--ph-theme-primary);
		color: #fff;
		background: var(--ph-theme-primary);
	}

	.site-typography-preview-glyph
	{
		display: grid;
		place-items: center;
		min-height: 160px;
		padding: 16px;
		border: 1px solid #e3dee7;
		border-radius: 11px;
		color: #29232e;
		background: #faf9fc;
		text-align: center;
	}

	.site-typography-preview-glyph strong
	{
		display: block;
		font-family: inherit;
		font-size: 4em;
		font-weight: 800;
		letter-spacing: -.08em;
		line-height: .95;
	}

	.site-typography-preview-glyph span
	{
		display: block;
		margin-top: 12px;
		color: var(--bs-secondary-color);
		font-family: inherit;
		font-size: .75em;
		font-weight: 700;
		letter-spacing: .04em;
	}

	@media (max-width: 991.98px)
	{
		.site-information-grid
		{
			grid-template-columns: 1fr;
		}

		.site-typography-settings-layout
		{
			grid-column: auto;
		}

		.site-logo-preview-panel
		{
			position: static;
		}
	}

	@media (max-width: 767.98px)
	{
		.site-typography-controls
		{
			grid-template-columns: 1fr;
		}
	}

	@media (max-width: 575.98px)
	{
		.site-settings-subheading,
		.site-thumbnail-actions
		{
			align-items: flex-start;
			flex-direction: column;
		}

		.site-thumbnail-buttons
		{
			width: 100%;
		}

		.site-thumbnail-buttons .btn
		{
			flex: 1;
		}

		.site-typography-preview-header
		{
			align-items: flex-start;
			flex-direction: column;
		}

		.site-typography-preview-actions
		{
			width: 100%;
			justify-content: space-between;
		}

		.site-typography-preview-canvas
		{
			grid-template-columns: 1fr;
			padding: 18px;
		}

		.site-logo-upload-zone
		{
			grid-template-columns: 60px minmax(0, 1fr);
			gap: 13px;
			padding: 14px;
		}

		.site-logo-upload-visual
		{
			width: 60px;
			height: 60px;
		}

		.site-logo-resize-card
		{
			grid-template-columns: 1fr;
			gap: 11px;
		}

		.site-logo-preview-toolbar
		{
			flex-direction: column;
			padding-inline: 18px;
		}

		.site-logo-preview-stage
		{
			min-height: 570px;
			padding: 15px;
		}
	}
	</style>
@endpush('css')

@section('content')
	<div>
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.config') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Site Settings') }}</h4>
				</div>
			</div>
		</div>

		<div id="ph-app-site-config">
			<div class="ph-fetch-listdata" id="ph-form-config-data" data-url="{{ url('awesome_admin/config/listdata') }}">
				<form action="{{ route('cms.admin.awesome_admin.config.update') }}" method="post" ref="formHTML" @submit.prevent="submitData">
					
					<div class="ph-notice" v-cloak>
						<div aria-live="polite" aria-atomic="true" class="position-relative">
							<div class="toast-container position-fixed top-0 end-0 p-3">
								<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatus" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
									<div :class="'toast-header '+responseStatus+' pe-3 pt-3 pb-1 border-0'">
										<strong class="toast-header-title toast-header-icon me-auto">Notice</strong>
										<small>just now</small>
										<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
									</div>
								
									<div class="toast-body p-3 text-start">
										<div v-if="isArrayMessageAfterSubmit == 1">
											<ul class="ps-3 m-0">
												<li v-for="(item, index) in responseMessageAfterSubmit">
													@{{ item[0] }}
												</li>
											</ul>
										</div>

										<div v-else>
											@{{ responseMessageAfterSubmit }}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					@php
						$siteThumbnailName = filled($data->site_thumbnail) ? $data->site_thumbnail : 'aruna_card_1200.jpg';
						$siteThumbnailUrl = filled($data->site_thumbnail) && Storage::exists('public/thumbnail/'.$data->site_thumbnail)
							? asset('storage/thumbnail/'.$data->site_thumbnail)
							: asset('assets/images/aruna_card_1200.jpg');
					@endphp

					<div id="generalSettingsSection" class="ph-content rounded p-4 mb-4">
						<div class="border-bottom pb-3 mb-4">
							<h5><i class="fas fa-cog fa-fw me-1"></i> {{ t('General Settings') }}</h5>
						</div>

						<div id="siteInformationLayout" class="site-information-grid">
							<section aria-labelledby="siteInformationTitle">
								<div class="site-settings-subheading">
									<div>
										<strong id="siteInformationTitle">{{ t('Site Information') }}</strong>
										<span>{{ t('Identity and metadata shown across the website.') }}</span>
									</div>
								</div>

								<div class="site-information-fields">
									<div>
											<label class="form-label">{{ t('Site Name') }}</label>
											<input type="text" name="site_name" ref="siteName" class="form-control font-size-inherit" value="{{ $data->site_name }}" v-on:input="updateSiteLogoName($event)">
									</div>

									<div>
											<label class="form-label">{{ t('Site Slogan') }}</label>
											<input type="text" name="site_slogan" class="form-control font-size-inherit" value="{{ $data->site_slogan }}">
									</div>

									<div>
											<label class="form-label">{{ t('Site Keyword') }}</label>
											<input type="text" name="site_keyword" class="form-control font-size-inherit" value="{{ $data->site_keyword }}">
									</div>

									<div>
											<label class="form-label">{{ t('Site Description') }}</label>
											<textarea name="site_description" class="form-control font-size-inherit" rows="3">{{ $data->site_description }}</textarea>
									</div>
								</div>
							</section>

							<section id="siteThumbnailCard" ref="siteThumbnailSettings" data-thumbnail-url="{{ $siteThumbnailUrl }}" data-thumbnail-name="{{ $siteThumbnailName }}" aria-labelledby="siteThumbnailTitle">
								<div class="site-settings-subheading">
									<div>
										<strong id="siteThumbnailTitle">{{ t('Site Thumbnail') }}</strong>
										<span>{{ t('Used for social sharing and content previews.') }}</span>
									</div>
								</div>

								<div class="site-thumbnail-card">
									<input id="siteThumbnailInput" ref="siteThumbnailInput" type="file" name="file" accept="image/png,image/jpeg,image/webp" hidden v-on:change="handleSiteThumbnailInput($event)">
									<div id="siteThumbnailDropzone" class="site-thumbnail-dropzone" :class="{'is-dragging': siteThumbnail.isDragging}" role="button" tabindex="0" aria-controls="siteThumbnailInput" v-on:click="chooseSiteThumbnail" v-on:keydown.enter.prevent="chooseSiteThumbnail" v-on:keydown.space.prevent="chooseSiteThumbnail" v-on:dragenter.prevent="siteThumbnail.isDragging = true" v-on:dragover.prevent="siteThumbnail.isDragging = true" v-on:dragleave.prevent="siteThumbnail.isDragging = false" v-on:drop.prevent.stop="handleSiteThumbnailDrop($event)">
										<img id="siteThumbnailPreview" :src="siteThumbnail.src || '{{ $siteThumbnailUrl }}'" alt="{{ t('Site thumbnail preview') }}">
										<span class="site-thumbnail-overlay">
											<span><strong>{{ t('Change thumbnail') }}</strong>{{ t('Click or drop an image here') }}</span>
											<i class="far fa-image fa-lg" aria-hidden="true"></i>
										</span>
									</div>

									<div class="site-thumbnail-actions">
										<span id="siteThumbnailFileName" class="site-thumbnail-file-name">@{{ siteThumbnail.name }} · @{{ siteThumbnail.isNew ? 'Ready to upload' : 'Current image' }}</span>
										<div class="site-thumbnail-buttons">
											<button id="chooseSiteThumbnail" type="button" class="btn btn-outline-secondary btn-sm font-size-inherit" v-on:click="chooseSiteThumbnail"><i class="far fa-folder-open me-1"></i> {{ t('Browse') }}</button>
											<button id="resetSiteThumbnail" type="button" class="btn btn-outline-secondary btn-sm font-size-inherit" v-on:click="resetSiteThumbnailPreview"><i class="far fa-undo me-1"></i> {{ t('Reset') }}</button>
										</div>
									</div>
								</div>
							</section>

							<section id="typographySettingsLayout" class="site-typography-settings-layout" aria-labelledby="typographySettingsTitle">
								<div class="site-settings-subheading">
									<div class="site-typography-heading-copy">
										<span class="site-typography-heading-icon"><i class="fas fa-font-case" aria-hidden="true"></i></span>
										<div>
											<strong id="typographySettingsTitle">{{ t('Typography Settings') }}</strong>
											<span>{{ t('Choose the default font family and base size for Arunika Aurora.') }}</span>
										</div>
									</div>
									<span class="badge rounded-pill text-bg-light border"><i class="far fa-eye me-1"></i> {{ t('Live preview') }}</span>
								</div>

								<div class="site-typography-controls">
									<div class="ph-fetch-listdata-font" data-url="{{ url('awesome_admin/config/listdata/fonts') }}">
											<label class="form-label">{{ t('Font Family') }}</label>

											<v-select label="name" :reduce="name => name.code" v-model="responseData.font_family" :options="responseDataFont" :components="{Deselect}">
												<template #open-indicator="{ attributes }">
													<span v-bind="attributes"><i class="fal fa-angle-down fa-lg mx-1" style="font-size: 1.5rem;vertical-align: top;"></i></span>
												</template>

												<template #selected-option="{ name, data }">
													<span :style="'font-family: '+name+''">@{{ name }}</span>
												</template>

												<template #option="{ data, name }">
													<span :style="'font-family: '+name+''">@{{ name }}</span>
												</template>

												<template #no-options="{ search, searching, loading }">
													<div class="px-3 py-2 text-center">Data not found.</div>
												</template>
											</v-select>

											<input type="hidden" name="font_family" class="form-control font-size-inherit" :value="responseData.font_family">
										<p class="site-typography-field-help">{{ t('Select a font family to preview it below.') }}</p>
									</div>

									<div>
											<label class="form-label">{{ t('Font Size') }}</label>
											<div class="input-group">
												<input type="number" name="font_size" class="form-control font-size-inherit" aria-label="{{ t('Font size value') }}" v-model.number="responseData.font_size" :min="siteTypographyFontSizeLimits(responseData.font_size_unit).min" :max="siteTypographyFontSizeLimits(responseData.font_size_unit).max" :step="siteTypographyFontSizeLimits(responseData.font_size_unit).step">
												<select name="font_size_unit" class="form-select font-size-inherit site-typography-size-unit" aria-label="{{ t('Font size unit') }}" v-model="responseData.font_size_unit" v-on:change="handleSiteTypographyUnitChange">
													<option value="px">px</option>
													<option value="em">em</option>
													<option value="rem">rem</option>
												</select>
											</div>
											<p class="site-typography-field-help">{{ t('Default 14px. Unit changes preserve the same visual size.') }}</p>
									</div>
								</div>

								<article id="siteTypographyPreview" class="site-typography-preview" aria-labelledby="siteTypographyPreviewTitle" :style="siteTypographyPreviewStyle()">
												<header class="site-typography-preview-header">
													<div class="site-typography-preview-title">
														<div>
															<strong id="siteTypographyPreviewTitle">{{ t('Typography Preview') }}</strong>
															<span>{{ t('Font family and size update automatically.') }}</span>
														</div>
													</div>

													<div class="site-typography-preview-actions">
														<span class="site-typography-preview-meta" aria-live="polite">@{{ siteTypographyMeta() }}</span>
														<button type="button" class="site-typography-preview-reset" title="{{ t('Reset preview') }}" aria-label="{{ t('Reset typography preview') }}" v-on:click="resetSiteTypographyPreview">
															<i class="fas fa-undo-alt" aria-hidden="true"></i>
														</button>
													</div>
												</header>

												<div class="site-typography-preview-canvas">
													<div class="site-typography-preview-copy">
														<p class="site-typography-preview-eyebrow">Arunika Aurora typography</p>
														<h6>{{ t('Simple, beautiful, and comfortable to read.') }}</h6>
														<p>{{ t('Preview how headings, body text, navigation labels, and interface actions feel together before saving the global site typography.') }}</p>

														<nav class="site-typography-preview-navigation" aria-label="{{ t('Typography preview navigation') }}">
															<span>{{ t('Dashboard') }}</span>
															<span>{{ t('Articles') }}</span>
															<span>{{ t('Messages') }}</span>
															<button type="button">{{ t('Read more') }}</button>
														</nav>
													</div>

													<div class="site-typography-preview-glyph" aria-hidden="true">
														<div>
															<strong>Aa</strong>
															<span>0123456789</span>
														</div>
													</div>
												</div>
											</article>
							</section>
						</div>
					</div>

					@php
						$hasCustomSiteLogo = filled($data->site_logo);
						$siteLogoUrl = $hasCustomSiteLogo
							? route('cms.admin.awesome_admin.config.logo', ['fileName' => $data->site_logo])
							: asset('assets/logos/laraphoenix_onlybird_colored_2.png');
						$siteLogoWidthValue = old('site_logo_width_value', $data->site_logo_width_value ?? 100);
						$siteLogoWidthUnit = old('site_logo_width_unit', $data->site_logo_width_unit ?? '%');
					@endphp

					<div id="logoSettingsSection" class="ph-content rounded p-4 mb-4">
						<div class="border-bottom pb-3 mb-4">
							<h5><i class="fas fa-image fa-fw me-1"></i> {{ t('Logo Settings') }}</h5>
						</div>

						<div id="siteLogoSettings"
							ref="siteLogoSettings"
							data-logo-url="{{ $siteLogoUrl }}"
							data-default-logo-url="{{ asset('assets/logos/laraphoenix_onlybird_colored_2.png') }}"
							data-logo-name="{{ $hasCustomSiteLogo ? $data->site_logo : '' }}"
							data-logo-width-value="{{ $siteLogoWidthValue }}"
							data-logo-width-unit="{{ $siteLogoWidthUnit }}"
							data-has-custom-logo="{{ $hasCustomSiteLogo ? '1' : '0' }}">
							<div class="row g-5 site-logo-settings-grid">
								<div class="col-lg-6">
									<label class="form-label fw-semibold">{{ t('Site Logo') }} <span class="text-secondary fw-normal">({{ t('optional') }})</span></label>
									<input id="siteLogoInput" ref="siteLogoInput" type="file" name="site_logo" accept="image/png,image/jpeg,image/webp" hidden v-on:change="handleSiteLogoInput($event)">
									<input id="removeSiteLogoInput" type="hidden" name="remove_site_logo" :value="siteLogo.remove">

									<div id="siteLogoDropZone" class="site-logo-upload-zone" :class="{'is-dragging': siteLogo.isDragging}" role="button" tabindex="0" aria-controls="siteLogoInput" v-on:click="chooseSiteLogo" v-on:dragenter.prevent="siteLogo.isDragging = true" v-on:dragover.prevent="siteLogo.isDragging = true" v-on:dragleave.prevent="siteLogo.isDragging = false" v-on:drop.prevent.stop="handleSiteLogoDrop($event)" v-on:keydown.enter.prevent="chooseSiteLogo" v-on:keydown.space.prevent="chooseSiteLogo">
										<div class="site-logo-upload-visual">
											<img id="siteLogoUploadPreview" class="site-logo-upload-preview" :src="siteLogo.src" alt="{{ t('Site logo preview') }}">
										</div>
										<div class="site-logo-upload-copy">
											<strong>{{ t('Drop your logo here or browse') }}</strong>
											<span>PNG, JPG, or WebP · maximum 5 MB<br>{{ t('Recommended: square or horizontal logo with a transparent background.') }}</span>
										</div>
									</div>

									<div class="site-logo-upload-actions">
										<button id="chooseSiteLogo" type="button" class="btn ph-btn-theme font-size-inherit" v-on:click="chooseSiteLogo">
											<i class="fal fa-upload me-1"></i> {{ t('Choose Logo') }}
										</button>
										<button id="removeSiteLogo" type="button" class="btn btn-outline-danger font-size-inherit" :disabled="! siteLogo.hasCustomLogo" v-on:click="removeSiteLogo">
											<i class="fal fa-trash-alt me-1"></i> {{ t('Remove Logo') }}
										</button>
									</div>

									<div id="siteLogoFileMeta" class="site-logo-file-meta" aria-live="polite">
										<div>
											<strong id="siteLogoFileName">@{{ siteLogo.name }}</strong>
											<span id="siteLogoFileDetails" class="text-body-secondary">@{{ siteLogo.details }}</span>
										</div>
										<span>@{{ siteLogo.hasCustomLogo ? 'Custom' : 'Default' }}</span>
									</div>

									<div id="siteLogoResizeCard" class="site-logo-resize-card">
										<div class="site-logo-resize-copy">
											<strong>{{ t('Logo Width') }}</strong>
											<span>{{ t("100% follows the logo's original sidebar width and is the maximum percentage value.") }}</span>
										</div>
										<div class="site-logo-resize-fields">
											<input id="siteLogoWidthValue" type="number" name="site_logo_width_value" class="form-control font-size-inherit" aria-label="{{ t('Logo width value') }}" v-model.number="siteLogo.widthValue" min="1" :max="logoWidthMaxForUnit(siteLogo.widthUnit)" :step="['em', 'rem'].includes(siteLogo.widthUnit) ? '0.1' : '1'" v-on:input="normalizeSiteLogoWidth">
											<select id="siteLogoWidthUnit" name="site_logo_width_unit" class="form-select font-size-inherit" aria-label="{{ t('Logo width unit') }}" v-model="siteLogo.widthUnit" v-on:change="normalizeSiteLogoWidth">
												@foreach(['%', 'px', 'em', 'rem', 'pt'] as $unit)
													<option value="{{ $unit }}">{{ $unit }}</option>
												@endforeach
											</select>
											<span id="siteLogoWidthReadout" class="site-logo-width-readout">@{{ siteLogo.widthValue }}@{{ siteLogo.widthUnit }} · @{{ siteLogoCssWidth() }} preview</span>
										</div>
									</div>

									<p class="form-text mt-3 mb-0">{{ t('When no custom logo is uploaded, Arunika Aurora uses the default Phoenix logo.') }}</p>
								</div>

								<div class="col-lg-6">
									<div class="site-logo-preview-panel">
										<div class="site-logo-preview-toolbar">
											<div>
												<strong class="d-block">{{ t('Sidebar Preview') }}</strong>
												<span class="text-body-secondary small">{{ t('Review the logo in both Arunika Aurora states.') }}</span>
											</div>
											<div class="site-logo-preview-controls" aria-label="{{ t('Sidebar preview width') }}">
												<button id="expandedSiteLogoPreview" type="button" :class="{'is-active': ! siteLogo.isCollapsed}" :aria-pressed="! siteLogo.isCollapsed" v-on:click="setSiteLogoPreviewMode(false)">{{ t('Expanded') }}</button>
												<button id="collapsedSiteLogoPreview" type="button" :class="{'is-active': siteLogo.isCollapsed}" :aria-pressed="siteLogo.isCollapsed" v-on:click="setSiteLogoPreviewMode(true)">{{ t('Collapsed') }}</button>
											</div>
										</div>

										<div class="site-logo-preview-stage">
											<div id="siteLogoSidebarPreview" class="site-logo-sidebar-preview" :class="{'is-collapsed': siteLogo.isCollapsed}">
												<div id="siteLogoSidebarBrand" class="site-logo-sidebar-brand has-logo">
													<img id="siteLogoSidebarImage" class="site-logo-sidebar-image" :src="siteLogo.src" :style="{'width': siteLogoCssWidth()}" alt="{{ t('Sidebar logo preview') }}">
													<span id="siteLogoSidebarName" class="site-logo-sidebar-name">@{{ siteLogo.siteName }}</span>
												</div>
												<div class="site-logo-sidebar-menu" aria-hidden="true">
													<div class="site-logo-sidebar-menu-item"><i class="fas fa-link"></i><span>{{ t('Visit Site') }}</span></div>
													<div class="site-logo-sidebar-menu-item is-active"><i class="fas fa-home"></i><span>{{ t('Dashboard') }}</span></div>
													<div class="site-logo-sidebar-menu-item"><i class="far fa-comments"></i><span>{{ t('Messages') }}</span></div>
													<div class="site-logo-sidebar-category">{{ t('All Menus') }}</div>
													<div class="site-logo-sidebar-menu-item"><i class="far fa-newspaper"></i><span>{{ t('Manage Articles') }}</span></div>
													<div class="site-logo-sidebar-menu-item"><i class="fas fa-puzzle-piece"></i><span>{{ t('Manage Cover Image') }}</span></div>
													<div class="site-logo-sidebar-menu-item"><i class="far fa-file-alt"></i><span>{{ t('File Manager') }}</span></div>
													<div class="site-logo-sidebar-category">{{ t('Advanced Menus') }}</div>
													<div class="site-logo-sidebar-menu-item"><i class="far fa-star"></i><span>{{ t('Log') }}</span></div>
												</div>
											</div>
										</div>
										<div class="site-logo-preview-note"><strong>{{ t('Active logo:') }}</strong> @{{ siteLogo.hasCustomLogo ? 'custom upload' : 'default Phoenix logo' }}.</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="ph-content rounded p-4 mb-4">
						<div class="border-bottom pb-3 mb-4">
							<h5><i class="fas fa-shield fa-fw me-1"></i> {{ t('Privacy & Security Settings') }}</h5>
						</div>

						<div>
							<div class="row g-5">
								<div class="col-lg-6">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label">{{ t('Management Menu Version') }}</label>

											<div class="input-group">
												<select name="management_menu" class="form-select font-size-inherit" aria-label="Default select example">
													<option selected>{{ t('Select Version') }}</option>
													<option value="v1" @if ($data->management_menu == 'v1') selected @endif>{{ t('Management Menu v1') }}</option>
													<option value="v2" @if ($data->management_menu == 'v2') selected @endif>{{ t('Management Menu v2') }}</option>
												</select>

												<button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#informationManagementMenuModal"><i class="fas fa-info-circle fa-fw fa-lg"></i></button>
											</div>

											<!-- Management Menu Modal -->
											<div class="modal fade" id="informationManagementMenuModal" tabindex="-1" aria-labelledby="informationManagementMenuModalLabel" aria-hidden="true">
												<div class="modal-dialog ph-modal-dialog modal-dialog-centered modal-dialog-scrollable">
													<div class="modal-content">
														<div class="modal-header">
															<h1 class="modal-title fs-5" id="informationManagementMenuModalLabel"><i class="fas fa-info-circle fa-fw fa-lg me-1"></i> {{ t('Information') }}</h1>
															<a href="javascript:void(0)" class="text-secondary ms-auto" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle fs-4"></i></a>
														</div>

														<div class="modal-body">
															<div class="bg-body-secondary p-3 rounded">
																<div class="mb-4">
																	<h6>{{ t('Management Menu v1') }}</h6>
																	<p>{{ t('Management Menu v1 uses the built-in feature of Spatie Laravel Permission, where permissions must be assigned to Roles first for Menus and Users.') }}</p>
																</div>

																<div>
																	<h6>{{ t('Management Menu v2') }}</h6>
																	<p>{{ t('Management Menu v2 is a custom feature that allows permissions to be directly assigned to the menu when creating a Role.') }}</p>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>

										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Site Registration Settings') }}</label>

											<select name="signup_closed" class="form-select font-size-inherit" aria-label="Default select example">
												<option selected>{{ t('Select') }}</option>
												<option value="0" @if ($data->signup_closed == 0) selected @endif>Open - Accepting new members</option>
												<option value="1" @if ($data->signup_closed == 1) selected @endif>Close - Not accepting new members</option>
											</select>
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Site Maintenance Settings') }}</label>

											<select name="offline_mode" class="form-select font-size-inherit" aria-label="Default select example" v-on:change="offlineReasonForm($event)">
												<option selected>{{ t('Select') }}</option>
												<option value="0" @if ($data->offline_mode == 0) selected @endif>Active</option>
												<option value="1" @if ($data->offline_mode == 1) selected @endif>Inactive</option>
											</select>

											<div class="ph-box-offline-reason">
												<div class="mt-3">
													<label class="form-label">{{ t('Offline Reason') }}</label>
													<textarea name="offline_reason" rows="5" placeholder="Offline Reason" class="form-control font-size-inherit" :disabled="showForm.offlineReasonForm == true ? false : true">{{ $data->offline_reason }}</textarea>
												</div>
											</div>
										</div>

										
									</div>
								</div>

								<div class="col-lg-6">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label">{{ t('Time Rate Limit Global in Second') }}</label>

											<input type="text" name="time_ratelimit_global" class="form-control" placeholder="{{ t('Please input time in second') }}" v-on:keypress="inputOnlyNumber" value="{{ $data->time_ratelimit_global }}" aria-label="Text input for time rate limit global in second">

											<div id="rateLimitLoginHelpBlock" class="form-text">
												{{ t('You can set time in second for duration rate limit') }}
											</div>
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Enable Rate Limit Login') }}</label>

											<div class="input-group">
												<div class="input-group-text">
													<input name="enable_ratelimit_login" class="form-check-input mt-0" type="checkbox" id="checkEnableRateLimitLogin" @if ($data->enable_ratelimit_login == 0) checked @endif aria-label="Checkbox for enable rate limit login">
												
													<label class="form-check-label font-size-normal ms-2" for="checkEnableRateLimitLogin">
														{{ t('Enable') }}
													</label>
												</div>
	
												<input type="text" name="amount_ratelimit_login" class="form-control" placeholder="{{ t('Please input integer 10-999 or until 3 digits') }}" v-on:keypress="inputOnlyNumber" value="{{ $data->amount_ratelimit_login }}" aria-label="Text input with checkbox and type integer, etc. 10-99">
											</div>

											<div id="rateLimitLoginHelpBlock" class="form-text">
												{{ t('You can limit login requests per IP Address per minute if a user fails to login') }}
											</div>
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Enable Rate Limit Signup') }}</label>

											<div class="input-group">
												<div class="input-group-text">
													<input name="enable_ratelimit_signup" class="form-check-input mt-0" type="checkbox" value="" id="checkEnableRateLimitSignup" @if ($data->enable_ratelimit_signup == 0) checked @endif aria-label="Checkbox for enable rate limit signup">
												
													<label class="form-check-label font-size-normal ms-2" for="checkEnableRateLimitSignup">
														{{ t('Enable') }}
													</label>
												</div>
	
												<input type="text" name="amount_ratelimit_signup" class="form-control" placeholder="{{ t('Please input integer 10-999 or until 3 digits') }}" v-on:keypress="inputOnlyNumber" value="{{ $data->amount_ratelimit_signup }}" aria-label="Text input with checkbox and type integer, etc. 10-99">
											</div>

											<div id="rateLimitSignupHelpBlock" class="form-text">
												{{ t('You can limit signup requests per IP Address per minute if a user fails to login') }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="ph-content rounded p-4">
						<div class="border-bottom pb-3 mb-4">
							<h5><i class="fas fa-user-lock fa-fw me-1"></i> {{ t('reCAPTCHA Settings') }}</h5>
						</div>

						<div>
							<div class="row g-5">
								<div class="col-lg-6">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label">{{ t('reCAPTCHA Site Key') }}</label>
											<input type="text" name="recaptcha_site_key" class="form-control font-size-inherit" placeholder="{{ t('Please input reCAPTCHA Site Key') }}" value="{{ $data->recaptcha_site_key }}">
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('reCAPTCHA Secret Key') }}</label>
											<input type="text" name="recaptcha_secret_key" class="form-control font-size-inherit" placeholder="{{ t('Please input reCAPTCHA Secret Key') }}" value="{{ $data->recaptcha_secret_key }}">
										</div>
										
									</div>
								</div>

								<div class="col-lg-6">
									<div class="row g-3">

										<div class="col-12">
											<label class="form-label">{{ t('Enable reCAPTCHA Login') }}</label>

											<select name="enable_recaptcha_signin" class="form-select font-size-inherit" aria-label="Select enable reCAPTCHA signin">
												<option selected>{{ t('Select') }}</option>
												<option value="0" @if ($data->enable_recaptcha_signin == 0) selected @endif>Active</option>
												<option value="1" @if ($data->enable_recaptcha_signin == 1) selected @endif>Inactive</option>
											</select>

											<div id="enableRECAPTCHASigninHelpBlock" class="form-text">
												{{ t('You must setup reCAPTCHA key first before activate this option') }}
											</div>
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Enable reCAPTCHA Signup') }}</label>

											<select name="enable_recaptcha_signup" class="form-select font-size-inherit" aria-label="Select enable reCAPTCHA signup">
												<option selected>{{ t('Select') }}</option>
												<option value="0" @if ($data->enable_recaptcha_signup == 0) selected @endif>Active</option>
												<option value="1" @if ($data->enable_recaptcha_signup == 1) selected @endif>Inactive</option>
											</select>

											<div id="enableRECAPTCHASignupHelpBlock" class="form-text">
												{{ t('You must setup reCAPTCHA key first before activate this option') }}
											</div>
										</div>
									</div>
								</div>

								<div class="col-12 mt-3 text-end">
									<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Submit') }}">
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_config/vueV3-manage-config-2026.js?v=').time() }}"></script>
@endpushonce
