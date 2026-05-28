@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Profile') }}
@endsection

@section('content')
	<style>
	.cr-boundary
	{
		border-radius: 50%;
	}	
	</style>

	<div id="ph-app-account" class="ph-content rounded mb-4 p-4 p-lg-5">
		<div id="ph-form-submit-data" class="d-flex align-items-center">

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

			<div class="flex-shrink-0" style="max-width: 300px">
				<div v-if="editProfilePicture == false" class="position-relative">	
					{!! get_avatar('frame', 'rounded-circle', 130) !!}
					
					<div class="position-absolute" style="top: 5.5rem;left: auto;right: 0rem;">
						<a href="javascript:void(0)" class="d-flex align-items-center justify-content-center" v-on:click="changeProfilePictureUser">
							<div class="ph-content border rounded-circle d-flex align-items-center justify-content-center" style="width: 35px;height: 35px;background-color: var(--ph-bg-panel);">
								<i class="fas fa-camera-alt fs-6"></i>
							</div>
						</a>
					</div>
				</div>

				<div v-else-if="editProfilePicture == true" class="position-relative" v-cloak>
					<div v-if="loadingChangeProfilePicture == true">
						{!! get_avatar('frame', 'rounded-circle', 130) !!}

						<div class="bg-secondary-subtle rounded-circle position-absolute d-flex align-items-center justify-content-center" style="top: 0;left: 0;width: 130px;height: 130px;" v-cloak>
							<div class="spinner-border text-danger" role="status">
								<span class="visually-hidden">{{ t('Loading')}} ...</span>
							</div>
						</div>
					</div>

					<div v-else>
						<div>
							<form action="{{ route('cms.core.account.updateProfilePicture') }}" method="post" ref="formHTML" auto-refresh="false" auto-block-button="false" auto-block-button-mobile="false" custom-class="w-100" custom-button-value="{{ t('Save') }}" @submit.prevent="submitDataProfilePicture">
								
								<div class="croppie rounded-circle h-auto"></div>

								<div class="row g-2">
									<div class="col-12">
										<input class="form-control upload" type="file" id="formFile">
									</div>

									<div class="col-md-6">
										<a href="javascript:void()" v-on:click="changeProfilePictureUserCancel" class="btn ph-btn-theme-outline w-100">{{ t('Cancel') }}</a>
									</div>

									<div class="col-md-6">
										<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit w-100" value="{{ t('Save') }}">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
	
			<div class="flex-grow-1 ms-4">
				<h4 class="mb-1">{{ $data->fullname }}</h4>
				<div class="fs-6 fw-semibold text-secondary">{{ current_role() }}</div>
			</div>
		</div>
	</div>

	<div class="ph-content rounded p-4">
		<div class="pb-3 mb-3 border-bottom">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center justify-content-start">
					<h5 class="mb-0">{{ t('Personal Information') }}</h5>
				</div>

				<div class="col-md-6 d-flex align-items-center justify-content-start justify-content-lg-end">
					<a href="{{ url('account') }}" class="btn ph-btn-theme-outline">{{ t('Edit Profile') }}</a>
				</div>
			</div>
		</div>

		<div class="row g-3 g-lg-4">
			<div class="col-md-4">
				<label for="staticEmailAddress" class="col-form-label text-secondary fw-bold">{{ t('Email Address') }}</label>
				<div id="staticEmailAddress" class="fw-semibold">{{ $data->email }}</div>
			</div>

			<div class="col-md-4">
				<label for="staticUsername" class="col-form-label text-secondary fw-bold">{{ t('Username') }}</label>
				<div id="staticUsername" class="fw-semibold">{{ $data->username }}</div>
			</div>

			<div class="col-md-4">
				<label for="staticGender" class="col-form-label text-secondary fw-bold">{{ t('Gender') }}</label>
				<div id="staticGender" class="fw-semibold">{{ get_gender($data_information->gender) }}</div>
			</div>

			<div class="col-md-4">
				<label for="staticBirthdate" class="col-form-label text-secondary fw-bold">{{ t('Birthdate') }}</label>
				<div id="staticBirthdate" class="fw-semibold">{{ $data_information->birthdate }}</div>
			</div>

			<div class="col-md-4">
				<label for="staticPhoneNumber" class="col-form-label text-secondary fw-bold">{{ t('Phone Number') }}</label>
				<div id="staticPhoneNumber" class="fw-semibold">{{ $data_information->phone_number }}</div>
			</div>

			<div class="col-md-4">
				<label for="staticAbout" class="col-form-label text-secondary fw-bold">{{ t('About') }}</label>
				<div id="staticAbout" class="fw-semibold">{{ $data_information->about }}</div>
			</div>
		</div>
	</div>
@endsection

@pushonce('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">
@endpushonce

@pushonce('js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
	<script src="{{ url('assets/js/c/vue3/vueV3-account-2025.js?v=').time() }}"></script>
@endpushonce