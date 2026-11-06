@extends('auth.auth_layout')

@section('auth-page', 'resetPassword')
@section('auth-template', 'split_right')

@section('title', t('Reset Password'))

@section('content')

	<div id="ph-app-auth">

	@if (site_config()->is_sso_activated == 0)
		<div class="row g-0">
	@elseif (site_config()->is_sso_activated == 1)
		<div class="row vh-100 g-0">
	@endif
			<div class="col-md-6 d-flex justify-content-center align-items-center">
				<div class="d-flex justify-content-center align-items-center vh-100">
					<div class="ph-form bg-white rounded border-0 p-4 p-lg-5">
						<div id="ph-form-recovery-password-submit">
							<form action="{{ route('cms.core.auth.resetpassword.process') }}" method="post" @submit="recoveryPassword" ref="formHTML">
								<div class="row g-3 gy-4">
									<div class="col-12 form-group text-center">
										<h4 class="fw-bold">{{ t('Reset Password') }}</h4>
										<p class="text-muted">{{ t('Enter your new password to recover your account.') }}</p>

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
										<label class="form-label fw-semibold">{{ t('Password') }}</label></a>
										<input type="password" name="password" class="form-control font-size-inherit" placeholder="{{ t('Enter your new password') }}">
									</div>

									<div class="col-12 form-group">
										<label class="form-label fw-semibold">{{ t('Re-type Password') }}</label></a>
										<input type="password" name="password_confirmation" class="form-control font-size-inherit" placeholder="{{ t('Enter re-type password') }}">
									</div>

									<div class="col-12 form-group text-center">
										<input type="hidden" name="code" value="{{ $data['recovery_code'] }}">
										<input type="submit" class="btn btn-larapx btn-submit-recovery-password w-100 p-2" value="{{ t('Submit') }}">
									</div>
								</div>
							</form>
						</div>			
					</div>
				</div>
			</div>

			<div class="col-md-6 position-relative">
				<div class="bg-holder" style="background-image: url('{{ url(custom_page_theme_setting("resetPassword")["page_background_image"]) }}');"></div>
			</div>
		</div>
	</div>
@endsection