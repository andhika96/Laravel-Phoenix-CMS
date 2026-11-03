@extends('themes.'.custom_theme('auth'))

@section('title', t('Forgot Password'))

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

	.input-group.form-group-no-border.input-group-focus .input-group-text,
	.input-group.form-group-no-border .form-control:focus 
	{
		background-color: rgba(255, 255, 255, 0.32) !important;
		box-shadow: none !important;
	}

	.input-group.form-group-no-border .input-group-text 
	{
		border: 0;
		color: #FFFFFF;
		background-color: rgba(255, 255, 255, 0.15);
		-webkit-transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
		transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out, background-color 0.2s ease-in-out;
	}

	.input-group.form-group-no-border .input-group-text 
	{
		color: rgba(255, 255, 255, 0.8);
	}

	.input-group > .input-group-text 
	{
		border-top-right-radius: 0 !important;
		border-bottom-right-radius: 0 !important;
	}
	</style>

	<div class="page-header" id="ph-app-auth">
		<div class="page-header-image" style="background-image: url('{{ url(custom_page_theme_setting("forgotPassword")["page_background_image"]) }}');"></div>
		
		<div class="container vh-100">
			<div class="d-flex justify-content-center align-items-center vh-100">
				<div class="ph-form position-relative rounded border-0 p-4 p-lg-5">
					<div id="ph-form-recovery-password-submit">
						<form action="{{ route('cms.core.auth.forgotpassword.process') }}" method="post" @submit="recoveryPassword" ref="formHTML" custom-button-class="rounded-pill">
							<div class="position-absolute text-start h4" style="top: 1.5rem;left: 1.5rem">
								<a href="{{ url('auth/login') }}" class="text-white"><i class="fas fa-long-arrow-left"></i></a>
							</div>

							<div class="row g-3 gy-4 mt-0">
								<div class="col-12 form-group text-center">
									<h4 class="fw-bold">{{ t('Forgot Password') }}</h4>
									<p class="text-white">{{ t("Enter your email address and we will send you an email with instructions to reset your password.") }}</p>
								
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

								<div class="col-12 input-group form-group-no-border input-group-md">
									<span class="input-group-text rounded-pill"><i class="fas fa-at ms-1"></i></span>
									<input type="text" name="email" class="form-control form-control-default-view rounded-pill ms-0" placeholder="{{ t('Enter your email') }}">
								</div>								

								<div class="col-12 form-group text-center">
									<input type="submit" class="btn btn-larapx btn-submit-recovery-password rounded-pill w-100 p-2" value="{{ t('Submit') }}">
								</div>
							</div>
						</form>
					</div>				
				</div>
			</div>
		</div>
	</div>
@endsection