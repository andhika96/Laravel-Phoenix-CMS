@extends('themes.'.custom_theme('auth'))

@section('title', t('Login'))

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
			<div class="col-md-6 d-flex justify-content-center align-items-center">
				<div class="ph-form bg-white rounded border-0 p-4 p-lg-5">
					<div id="ph-form-login-submit">
						<form action="{{ route('cms.core.auth.login.authenticate') }}" method="post" @submit="login" ref="formHTML">
							<div class="row g-3 gy-4">
								<div class="col-12 form-group text-center">
									<h4 class="fw-bold">{{ t('Log In') }}</h4>
									<p class="text-muted">{{ t('Enter your email address and password to the dashboard.') }}</p>

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
									<label class="form-label fw-semibold">{{ t('Email Address') }}</label>
									<input type="text" name="email" class="form-control font-size-inherit" placeholder="{{ t('Enter your email') }}">
								</div>

								<div class="col-12 form-group">
									<label class="form-label fw-semibold">{{ t('Password') }}</label> <a href="{{ url('auth/forgotpassword') }}" class="text-muted float-end text-decoration-none"><small>{{ t('Forgot your password?') }}</small></a>
									<input type="password" name="password" class="form-control font-size-inherit" placeholder="{{ t('Enter your password') }}">
								</div>

								<div class="col-12 form-group">
									<div class="form-check">
										<input name="remember_me" class="form-check-input" type="checkbox" id="flexCheckDefault">

										<label class="form-check-label" for="flexCheckDefault">
											{{ t('Remember me') }}
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
									@csrf
									<input type="submit" class="btn btn-larapx btn-submit-login w-100 p-2" value="{{ t('Log In') }}">
								</div>
							</div>
						</form>
					</div>

					@if (site_config()->signup_closed == 0)

						<div class="fs-sm my-4">
							Don't have an account?
							<a class="text-decoration-underline p-0 ms-1" href="{{ url('auth/signup') }}">{{ t('Create an account') }}</a>
						</div>

					@endif

					@if (site_config()->is_sso_activated == 0)

						<div class="d-flex align-items-center my-4">
							<hr class="w-100 m-0">
							<span class="text-body-emphasis fw-medium text-nowrap mx-4">{{ t('or continue with') }}</span>
							<hr class="w-100 m-0">
						</div>

						<div class="row g-2">
							<div class="col-12 col-lg-6">
								<button type="button" class="btn btn-outline-larapx w-100 py-2">
									<i class="fab fa-google ms-1 me-1"></i>
									Google
								</button>
							</div>

							<div class="col-12 col-lg-6">
								<button type="button" class="btn btn-outline-larapx w-100 py-2">
									<i class="fab fa-facebook-f ms-1 me-1"></i>
									Facebook
								</button>
							</div>

							<div class="col-12 col-lg-6">
								<button type="button" class="btn btn-outline-larapx w-100 py-2">
									<i class="fab fa-apple ms-1 me-1"></i>
									Apple
								</button>
							</div>

							<div class="col-12 col-lg-6">
								<button type="button" class="btn btn-outline-larapx w-100 py-2">
									<i class="fab fa-windows ms-1 me-1"></i>
									Microsoft
								</button>
							</div>
						</div>		

					@endif			
				</div>				
			</div>

			<div class="col-md-6 position-relative">
				<div class="bg-holder" style="background-image: url('{{ url(custom_page_theme_setting("login")["page_background_image"]) }}');"></div>
			</div>
		</div>
	</div>
@endsection