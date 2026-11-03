@extends('themes.'.custom_theme('auth'))

@section('title', t('Signup'))

@section('content')
	<style>
	.ph-form
	{
		max-width: 450px;
	}

	.form-control
	{
		padding: .45rem .9rem !important;
	}

	.input-group .form-control:focus,
	.form-control.is-valid:focus,
	.form-control.is-invalid:focus
	{
		/*-webkit-clip-path: inset(-5px 0px -5px -5px) !important;
		clip-path: inset(-5px 0px -5px -5px) !important;*/
		box-shadow: none;
	}

	.input-group-focus
	{
		box-shadow: 0 0 0 0.25rem var(--ph-primary-bg-subtle);		
	}

	.input-group-focus-is-valid.is-valid
	{
		/* border-color: var(--bs-form-valid-border-color); */
		box-shadow: 0 0 0 .25rem rgba(var(--bs-success-rgb), .25);
	}

	.input-group-focus-is-invalid.is-invalid
	{
		/* border-color: var(--bs-form-invalid-border-color); */
		box-shadow: 0 0 0 .25rem rgba(var(--bs-danger-rgb), .25);
	}

	.with-no-bg-image
	{
		background-image: none !important;
	}

	.bg-holder
	{
		position: absolute;
		width: 100%;
		min-height: 100%;
		top: 0;
		left: 0;
		background-size: cover;
		background-position: center;
		overflow: hidden;
		will-change: transform, opacity, filter;
		-webkit-backface-visibility: hidden;
		backface-visibility: hidden;
		background-repeat: no-repeat;
		z-index: 0;
	}
	</style>

	<div id="ph-app-auth">
		
	@if (site_config()->is_sso_activated == 0)
		<div class="row g-0">
	@elseif (site_config()->is_sso_activated == 1)
		<div class="row vh-100 g-0">
	@endif
			<div class="col-md-6 position-relative">
				<div class="bg-holder" style="background-image: url('{{ url(custom_page_theme_setting("signup")["page_background_image"]) }}');"></div>
			</div>

			<div class="col-md-6 d-flex justify-content-center align-items-center">

				@if (site_config()->signup_closed == 0)

					<div class="d-flex justify-content-center align-items-center my-5">
						<div class="ph-form bg-white rounded border-0 p-4 p-lg-5">
							<div id="ph-form-signup-submit">
								<form action="{{ route('cms.core.auth.signup.process') }}" method="post" @submit="signup" ref="formHTML">
									<div class="row g-3 gy-4">
										<div class="col-12 form-group text-center">
											<h4 class="fw-bold">{{ t('Signup') }}</h4>
											<p class="text-muted">{{ t("Don't have an account? Create your account, it takes less than a minute") }}</p>

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

										<div class="col-12 form-group">
											<label class="form-label fw-semibold ph-fetch-checkdata-email" data-url="{{ url('auth/checkdata') }}">{{ t('Email Address') }}</label>
																				
											<div class="input-group rounded" :class="[ responseStatusCheckData.email == 'success' ? 'input-group-focus-is-valid is-valid' : (responseStatusCheckData.email == 'failed' ? 'input-group-focus-is-invalid is-invalid' : '')]">
												<input type="text" name="account[email]" class="form-control font-size-inherit" placeholder="Enter your email" id="userEmailAddress" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : ''), loadingCheckData.email == true ? 'border-end-0 with-no-bg-image' : '' ]" v-model="getQueryCheckData.email" v-on:focus="focusForm($event)" v-on:blur="blurForm" v-on:keyup="checkData('email')" required>
											
												<span v-if="loadingCheckData.email == true" class="input-group-text bg-white border-start-0" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : '') ]" v-cloak>
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

										<div class="col-12 form-group">
											<label class="form-label fw-semibold ph-fetch-checkdata-username" data-url="{{ url('auth/checkdata') }}">{{ t('Username') }}</label>
																				
											<div class="input-group rounded" :class="[ responseStatusCheckData.username == 'success' ? 'input-group-focus-is-valid is-valid' : (responseStatusCheckData.username == 'failed' ? 'input-group-focus-is-invalid is-invalid' : '')]">
												<input type="text" name="account[username]" class="form-control form-control-username font-size-inherit" placeholder="Enter the username" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : ''), loadingCheckData.username == true ? 'border-end-0 with-no-bg-image' : '' ]" id="userName" v-model="getQueryCheckData.username" v-on:focus="focusForm($event)" v-on:blur="blurForm" v-on:keyup="checkData('username')" required>
												
												<span v-if="loadingCheckData.username == true" class="input-group-text bg-white border-start-0" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : '') ]" v-cloak>
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

										<div class="col-12 form-group">
											<label class="form-label fw-semibold">{{ t('Fullname') }}</label>
											<input type="text" name="account[fullname]" class="form-control font-size-inherit" placeholder="Enter your fullname" required>
										</div>

										<div class="col-12 form-group">
											<label class="form-label fw-semibold">{{ t('Password') }}</label></a>
											<input type="password" name="account[password]" class="form-control font-size-inherit" placeholder="Enter your password" required>
										</div>

										<div class="col-12 form-group">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">

												<label class="form-check-label" for="flexCheckDefault">
													{{ t('I accept Terms and Conditions') }}
												</label>
											</div>
										</div>

										<div class="col-12 form-group m-0">
											{!! RecaptchaV3::field('signup') !!}
										</div>

										<div class="col-12 form-group text-center">
											<input type="submit" class="btn btn-larapx btn-submit-signup w-100 p-2" value="{{ t('Signup') }}">
										</div>
									</div>
								</form>
							</div>

							<div class="fs-sm my-4">
								{{ t('Already have account?') }}
								<a class="text-decoration-underline p-0 ms-1" href="{{ url('auth/login') }}">{{ t('Log In') }}</a>
							</div>

							@if (site_config()->is_sso_activated == 0)

								<div class="d-flex align-items-center my-4">
									<hr class="w-100 m-0">
									<span class="text-body-emphasis fw-medium text-nowrap mx-4">{{ t('or signup with') }}</span>
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
							<h4 class="m-0">Sorry, registration is closed</h4>
						</div>
					</div>

				@endif

			</div>
		</div>
	</div>
@endsection