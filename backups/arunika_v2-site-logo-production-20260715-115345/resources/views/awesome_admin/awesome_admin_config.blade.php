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

					<div class="ph-content rounded p-4 mb-4">
						<div class="border-bottom pb-3 mb-4">
							<h5><i class="fas fa-cog fa-fw me-1"></i> {{ t('General Settings') }}</h5>
						</div>

						<div>
							<div class="row g-5">
								<div class="col-lg-6">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label">{{ t('Site Name') }}</label>
											<input type="text" name="site_name" class="form-control font-size-inherit" value="{{ $data->site_name }}">
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Site Slogan') }}</label>
											<input type="text" name="site_slogan" class="form-control font-size-inherit" value="{{ $data->site_slogan }}">
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Site Keyword') }}</label>
											<input type="text" name="site_keyword" class="form-control font-size-inherit" value="{{ $data->site_keyword }}">
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Site Description') }}</label>
											<textarea name="site_description" class="form-control font-size-inherit" rows="3">{{ $data->site_description }}</textarea>
										</div>

										<div class="col-md-6 ph-fetch-listdata-font" data-url="{{ url('awesome_admin/config/listdata/fonts') }}">
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
										</div>

										<div class="col-md-6">
											<label class="form-label">{{ t('Font Size') }}</label>
											<input type="text" name="font_size" class="form-control font-size-inherit" value="{{ $data->font_size }}">
										</div>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label">{{ t('Site Thumbnail') }}</label>
												<img src="{{ asset('assets/images/aruna_card_1200.jpg') }}" class="img-fluid rounded mb-3">

											  <input class="form-control font-size-inherit" type="file" name="file" id="formFile">
										</div>
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