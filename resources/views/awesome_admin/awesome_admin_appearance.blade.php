@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Appearance Settings') }}
@endsection

@section('content')
	<style>
	.radio-card
	{
		border: 2px solid rgba(0, 0, 0, 0.1);
		border-radius: 10px;
		width: 100%;
		height: 130px;
		padding: .25rem;
		transition: all 0.3s;
		position: relative;
	}

	.radio-card:hover 
	{
		border: 2px solid var(--ph-theme-primary);
		cursor: pointer;
	}

	.radio-card-check
	{
		display: none;
		position: absolute;
		left: 0.75rem;
		bottom: 0.85rem;
		z-index: 1;
	}

	.radio-card-check i 
	{
		font-size: 1.6rem;
		color: var(--ph-theme-primary);
	}

	.text-center 
	{
		text-align: center;
	}

	.radio-card-icon img
	{
		width: 80px;
	}

	.radio-card-label2
	{
		margin-top: 1rem;
		font-weight: 600;
		font-size: 1.2rem;
	}

	.radio-card-label-description
	{
		margin-top: 0.5rem;
		color: rgba(0, 0, 0, 0.7);
	}

	.radio-card.selected
	{
		border: 2px solid #016787;
	}

	.radio-card.selected 
	{
		border: 2px solid var(--ph-theme-primary);
	}

	.radio-card.selected .radio-card-check
	{
		display: inline-flex;
	}

	.bg-image 
	{
		position: absolute;
		background-size: cover;
		background-position: center center;
		width: 100%;
		height: 100%;
		border-radius: 8px;
		/* z-index: -1; */
	}

	.clr-field button 
	{
		top: 49%;
		border-top-right-radius: var(--bs-border-radius);
		border-bottom-right-radius: var(--bs-border-radius);
	}
	</style>

	<div id="ph-app-appearance-config">
		<div class="mb-3">		
			{{ Breadcrumbs::render('awesome_admin.appearance') }}
		</div>

		<div id="ph-form-config-appearance">
			<form action="{{ route('cms.admin.awesome_admin.appearance.update') }}" method="post" ref="formHTML" @submit.prevent="submitData">
			
				<div class="ph-notice" v-cloak>
					<div aria-live="polite" aria-atomic="true" class="position-relative">
						<div class="toast-container position-fixed top-0 end-0 p-3">
							<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatus" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
								<div :class="'toast-header '+responseStatus+' pe-3 pt-3 pb-1 border-0'">
									<strong class="toast-header-title toast-header-icon me-auto">{{ t('Notice') }}</strong>
									<small>{{ t('just now') }}</small>
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

				<div class="ph-content p-3 mb-3 rounded">
					<div class="row g-3">
						<div class="col-md-6 d-flex align-items-center">
							<h4 class="mb-0">{{ t('Manage Appearance') }}</h4>
						</div>
					</div>
				</div>

				<div class="ph-content p-4 rounded">

					@foreach($list_page_theme_settings as $item)
						<div class="ph-fetch-listdata-{{ $item->uri }} border-bottom pb-4 mb-4" data-url="{{ url('awesome_admin/appearance/pagethemesetting/'.$item->uri) }}">

							<div class="row g-2 mb-5 mb-lg-4">
								<div class="col-md-3">
									<div class="h6 mb-1">{{ t('Color Nuances') }}</div>
								</div>

								<div class="col-md-2">
									<div v-if="loadDataAppearance['{!! $item->uri !!}'] == true" class="placeholder-glow">
										<input type="text" class="form-control placeholder opacity-50" style="background-color: currentcolor" aria-disabled="true" disabled>
									</div>

									<div v-else v-cloak>
										<span v-if="responseStatusMultiple['{!! $item->uri !!}'] == 'failed'">
											<input type="text" class="form-control" :placeholder="responseMessageMultiple['{!! $item->uri !!}']" aria-disabled="true" disabled>
										</span>

										<span v-else>
											<input type="text" v-model="selectedColorNuances['{!! $item->uri !!}']" value="{{ $page_theme_settings[$item->uri]['page_color_nuances'] }}" class="form-control coloris arv7-color-{{ $item->uri }}-page" @if (page_theme($item->page_theme)['is_active_color_nuances'] == 1) disabled @endif>
										</span>
									</div>
								</div>

								<div class="col d-flex align-items-center">
									<span v-if="isWithColorNuances['{!! $item->uri !!}'] == 1" class="text-danger" v-cloak>({{ t("This theme doesn't support color nuances") }})</span>
								</div>

							</div>

							<div class="row g-2 mb-5 mb-lg-4">
								<div class="col-md-3">
									<div class="h6 mb-1">{{ t('Background Image') }}</div>
								</div>

								<div class="col-md-8">
									<input type="file" name="background_image[{{ $item->uri }}]" class="form-control" id="formFileBackgroundImage">
								</div>
							</div>

							<div class="row g-2">
								<div class="col-md-3">
									<div class="h6 mb-1">{{ t('Interface {1} Theme', ucwords($item->page_name)) }}</div>
									<p>{{ t('Select or customize your UI theme') }}</p>
								</div>

								<div class="col-md-8">
									<div class="row g-3 gy-lg-0 gx-lg-3">

										@foreach ($page_themes[$item->uri] as $theme)
											<div class="col-md-3 d-flex align-items-center">
												<div class="radio-card radio-card-{{ $item->uri }} radio-card-{{ $item->uri }}-{{ $theme->theme_code }} @if ($page_theme_settings[$item->uri]['page_theme'] == $theme->theme_code) selected  @endif" v-on:click="selectAppearance('{{ $item->uri }}', '{{ $theme->theme_code }}')">
													<div class="radio-card-check">
														<span class="fa-layers fa-fw fa-2x">
															<i class="fas fa-circle text-white"></i>
															<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
														</span>
													</div>

													<div class="position-relative h-100">
														<div class="bg-image" style="background-image: url('{{ asset($theme->theme_preview_image) }}');"></div>
														
														<input type="hidden" class="is_active_color_nuances" is-with-color-nuances="{{ $theme->is_active_color_nuances }}">

														@if ($page_theme_settings[$item->uri]['page_theme'] == $theme->theme_code)

															<input type="hidden" class="selected_class" selected-class="radio-card-{{ $item->uri }}-{{ $theme->theme_code }}">
															<input type="hidden" class="selected_class_uri" selected-class-uri="{{ $item->uri }}">
													
														@endif
													</div>
												</div>
											</div>
										@endforeach

									</div>
								</div>
							</div>
						</div>

					@endforeach

					<div class="text-end">
						<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Submit') }}">
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@pushonce('css')
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css">
@endpushonce

@pushonce('js-priority')
	<script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}" fetchpriority="high" async></script>
@endpushonce

@pushonce('js')
	<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
	<script src="{{ url('assets/js/vue3/manage_appearance/vueV3-manage-appearance-2026.js?v=').time() }}"></script>
@endpushonce