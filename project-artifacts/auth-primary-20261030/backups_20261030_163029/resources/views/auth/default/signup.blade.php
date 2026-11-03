@extends('themes.'.custom_theme('auth'))

@section('title', t('Signup'))

@section('content')
	<style>
	.ph-form
	{
		color: #FFFFFF;
		max-width: 450px;
	}

	.form-control
	{
		color: #FFFFFF;
		border-color: rgba(255, 255, 255, 0.5);
	}

	.form-control::placeholder
	{
		color: #FFFFFF;
	}

	.input-group.form-group-no-border .form-control 
	{
		color: #FFFFFF;
		font-size: 14px;
		border: medium none;
		padding: 0.9rem 0.2rem;
		background-color: rgba(255, 255, 255, 0.15);
		-webkit-transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
		transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
	}

	.input-group > .form-control:not(:first-child) 
	{
		border-top-left-radius: 0 !important;
		border-bottom-left-radius: 0 !important;
	}

	.input-group > .form-control:not(:last-child) 
	{
		border-left: 0;
	}

	.input-group-focus
	{
		box-shadow: none !important;
	}

	.input-group > .border-end-radius-0
	{
		border-top-right-radius: 0 !important;
		border-bottom-right-radius: 0 !important;
	}

	.input-group.form-group-no-border.input-group-focus .input-group-text,
	.input-group.form-group-no-border.input-group-focus .input-group-text-end,
	.input-group.form-group-no-border .form-control:focus 
	{
		background-color: rgba(255, 255, 255, 0.32) !important;
		box-shadow: none !important;
	}

	.input-group.form-group-no-border .input-group-text,
	.input-group.form-group-no-border .input-group-text-end 
	{
		border: 0;
		color: #FFFFFF;
		background-color: rgba(255, 255, 255, 0.15);
		-webkit-transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
		transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
	}

	.input-group.form-group-no-border .input-group-text,
	.input-group.form-group-no-border .input-group-text-end 
	{
		color: rgba(255, 255, 255, 0.8);
	}

	.input-group > .input-group-text 
	{
		border-top-right-radius: 0 !important;
		border-bottom-right-radius: 0 !important;
	}

	.input-group >.input-group-text.input-group-text-end
	{
		border-top-right-radius: 50rem !important;
		border-bottom-right-radius: 50rem !important;
		border-top-left-radius: 0 !important;
		border-bottom-left-radius: 0 !important;
	}

	.was-validated .form-control:valid, .form-control.is-valid,
	.was-validated .form-control:invalid, .form-control.is-invalid
	{
		background-position: right calc(.375em + 0.4875rem) center;
	}
	</style>

	<div class="page-header" id="ph-app-auth">
		<div class="page-header-image" style="background-image: url('{{ url(custom_page_theme_setting("signup")["page_background_image"]) }}');"></div>
		
		<div class="container">

			@if (site_config()->signup_closed == 0)

				<div class="d-flex justify-content-center align-items-center my-5">
					<div class="ph-form rounded border-0 p-4 p-lg-5">
						<div id="ph-form-signup-submit">
							<form action="{{ route('cms.core.auth.signup.process') }}" method="post" @submit="signup" ref="formHTML" custom-button-class="rounded-pill">
								<div class="row g-3 gy-4">
									<div class="col-12 form-group text-center">
										<h4 class="fw-bold">{{ t('Signup') }}</h4>
										<p class="text-white">{{ t("Don't have an account? Create your account, it takes less than a minute") }}</p>

										<div class="ph-notice" v-cloak>
											<div aria-live="polite" aria-atomic="true" class="position-relative">
												<div class="toast-container position-fixed top-0 end-0 p-3">
													<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
														<div :class="'toast-header '+responseStatusToast+' pe-3 pt-3 pb-1 border-0'">
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
									</div>

									<div class="col-12 ph-fetch-checkdata-email" data-url="{{ url('auth/checkdata') }}">
										<div class="col-12 input-group form-group-no-border input-group-md" :class="[ responseStatusCheckData.email == 'success' ? 'input-group-focus is-valid' : (responseStatusCheckData.email == 'failed' ? 'input-group-focus is-invalid' : '')]">
											<span class="input-group-text rounded-pill"><i class="fas fa-at ms-1"></i></span>
											<input type="text" name="account[email]" class="form-control form-control-default-view rounded-pill ms-0" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : ''), loadingCheckData.email == true ? 'border-end-0 border-end-radius-0 with-no-bg-image' : '' ]" id="userName" v-model="getQueryCheckData.email" v-on:keyup="checkData('email')" placeholder="{{ t('Email Address') }}" required>
										
											<span v-if="loadingCheckData.email == true" class="input-group-text input-group-text-end rounded-pill pe-3 m-0" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : '') ]" v-cloak>
												<div class="spinner-border spinner-border-sm text-primary" role="status">
													<span class="visually-hidden">{{ t('Loading') }} ...</span>
												</div>								
											</span>
										</div>

										<div v-if="responseStatusCheckData.email !== '' && loadingCheckData.email !== true" :class="[ responseStatusCheckData.email == 'success' ? 'valid-feedback d-block mt-1' : 'invalid-feedback d-block mt-1' ]" v-cloak>
											<span v-if="responseStatusCheckData.email == 'success'" class="d-none">
												<i class="far fa-check-circle fa-fw"></i>  
											</span>

											<span v-else-if="responseStatusCheckData.email == 'failed'" class="d-none">
												<i class="far fa-times fa-fw"></i> 
											</span>

											@{{ responseMessageCheckData.email }}
										</div>
									</div>

									<div class="col-12 ph-fetch-checkdata-username" data-url="{{ url('auth/checkdata') }}">
										<div class="input-group form-group-no-border input-group-md" :class="[ responseStatusCheckData.username == 'success' ? 'input-group-focus is-valid' : (responseStatusCheckData.username == 'failed' ? 'input-group-focus is-invalid' : '')]">
											<span class="input-group-text rounded-pill"><i class="fas fa-signature ms-1"></i></span>
											<input type="text" name="account[username]" class="form-control form-control-default-view rounded-pill ms-0" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : ''), loadingCheckData.username == true ? 'border-end-0 border-end-radius-0 with-no-bg-image' : '' ]" id="userName" v-model="getQueryCheckData.username" v-on:keyup="checkData('username')" placeholder="{{ t('Username') }}" required>
										
											<span v-if="loadingCheckData.username == true" class="input-group-text input-group-text-end rounded-pill pe-3 m-0" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : '') ]" v-cloak>
												<div class="spinner-border spinner-border-sm text-primary" role="status">
													<span class="visually-hidden">{{ t('Loading') }} ...</span>
												</div>								
											</span>
										</div>

										<div v-if="responseStatusCheckData.username !== '' && loadingCheckData.username !== true" :class="[ responseStatusCheckData.username == 'success' ? 'valid-feedback d-block mt-1' : 'invalid-feedback d-block mt-1' ]" v-cloak>
											<span v-if="responseStatusCheckData.username == 'success'" class="d-none">
												<i class="far fa-check-circle fa-fw"></i>  
											</span>

											<span v-else-if="responseStatusCheckData.username == 'failed'" class="d-none">
												<i class="far fa-times fa-fw"></i> 
											</span>

											@{{ responseMessageCheckData.username }}
										</div>
									</div>

									<div class="col-12 input-group form-group-no-border input-group-md">
										<span class="input-group-text rounded-pill"><i class="fas fa-signature ms-1"></i></span>
										<input type="text" name="account[fullname]" class="form-control form-control-default-view rounded-pill ms-0" placeholder="{{ t('Fullname') }}" required>
									</div>

									<div class="col-12 input-group form-group-no-border input-group-md">
										<span class="input-group-text rounded-pill"><i class="fas fa-key ms-1"></i></span>
										<input type="password" name="account[password]" class="form-control form-control-default-view rounded-pill ms-0" placeholder="{{ t('Password') }}" required>
									</div>

									<div class="col-12 form-group">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">

											<label class="form-check-label" for="flexCheckDefault">
												{{ t('I accept Terms and Conditions') }}
											</label>
										</div>
									</div>

								@if (site_config()->enable_recaptcha_signin == 0 &&
									 site_config()->recaptcha_site_key !== null &&
									 site_config()->recaptcha_secret_key !== null)

									<div class="col-12 form-group m-0">
										{!! RecaptchaV3::field('login') !!}
									</div>

								@endif

									<div class="col-12 form-group text-center">
										<input type="submit" class="btn btn-larapx btn-submit-signup rounded-pill w-100 p-2" value="{{ t('Signup') }}">
									</div>
								</div>
							</form>
						</div>

						<div class="fs-sm my-4">
							{{ t('Already have account?') }}
							<a class="text-white text-decoration-underline p-0 ms-1" href="{{ url('auth/login') }}">{{ t('Log In') }}</a>
						</div>

						@if (site_config()->is_sso_activated == 0)

							<div class="d-flex align-items-center my-4">
								<hr class="w-100 m-0">
								<span class="fw-medium text-nowrap text-white mx-4">{{ t('or signup with') }}</span>
								<hr class="w-100 m-0">
							</div>

							<div class="row g-2">
								<div class="col-12 col-lg-6">
									<button type="button" class="btn btn-outline-secondary-larapx w-100 py-2">
										<i class="fab fa-google ms-1 me-1"></i>
										Google
									</button>
								</div>

								<div class="col-12 col-lg-6">
									<button type="button" class="btn btn-outline-secondary-larapx w-100 py-2">
										<i class="fab fa-facebook-f ms-1 me-1"></i>
										Facebook
									</button>
								</div>

								<div class="col-12 col-lg-6">
									<button type="button" class="btn btn-outline-secondary-larapx w-100 py-2">
										<i class="fab fa-apple ms-1 me-1"></i>
										Apple
									</button>
								</div>

								<div class="col-12 col-lg-6">
									<button type="button" class="btn btn-outline-secondary-larapx w-100 py-2">
										<i class="fab fa-windows ms-1 me-1"></i>
										Microsoft
									</button>
								</div>
							</div>	

						@endif

					</div>
				</div>

			@elseif (site_config()->signup_closed == 1)

				<div class="d-flex justify-content-center align-items-center vh-100">
					<div class="ph-form bg-white text-dark rounded border-0 p-4 p-lg-5">
						<div class="text-center mb-3"><i class="far fa-sad-tear fa-5x"></i></div>
						<h4 class="m-0">{{ t('Sorry, registration is closed') }}</h4>
					</div>
				</div>

			@endif

		</div>
	</div>
@endsection