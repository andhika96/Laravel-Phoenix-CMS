@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Account Settings') }}
@endsection

@section('content')
	<style>
	.cr-boundary
	{
		border-radius: 50%;
	}	
	</style>
	
	<div id="ph-app-account" class="bg-white rounded p-4">
		<div id="ph-form-submit-data">
			<form action="{{ route('cms.core.account.updateProfilePicture') }}" method="post" ref="formHTML" auto-refresh="false" auto-block-button="false" auto-block-button-mobile="false" custom-button-value="{{ t('Save') }}" @submit.prevent="submitDataProfilePicture">
				
				<div class="ph-notice" v-cloak>
					<div aria-live="polite" aria-atomic="true" class="position-relative">
						<div class="toast-container position-fixed top-0 end-0 p-3">
							<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
								<div :class="'toast-header '+responseStatusToast+' px-3 pt-3 pb-1 border-0'">
									<strong class="me-auto">Notice</strong>
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

				<div class="croppie rounded-circle h-auto"></div>

				<div class="mb-3">
					<input class="form-control upload" type="file" id="formFile">
				</div>

				<div class="form-group text-end">
					@{{ customButtonValue }}

					<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Save') }}">
				</div>
			</form>
		</div>
	</div>
@endsection

@pushonce('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">
@endpushonce

@pushonce('js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
	<script src="{{ url('assets/js/account/vue3/vueV3-account-2026.js?v=').time() }}"></script>
@endpushonce