@extends('auth.auth_layout')

@section('auth-page', 'resetPassword')
@section('auth-template', 'card')

@section('title', t('Reset Password'))

@section('content')

	<div class="page-header" id="ph-app-auth">
		<div class="page-header-image" style="background-image: url('{{ url(custom_page_theme_setting("resetPassword")["page_background_image"]) }}');"></div>
		
		<div class="container">
			<div class="d-flex justify-content-center align-items-center vh-100">
				<div class="ph-form bg-white rounded border-0 p-4 p-lg-5">
					<div id="ph-form-recovery-password-submit">
						<form action="{{ route('cms.core.auth.resetpassword.process') }}" method="post" @submit="recoveryPassword" ref="formHTML">
							<div class="row g-3 gy-4">
								<div class="col-12 form-group text-center">
									<h4 class="fw-bold">{{ t('Reset Password') }}</h4>
									<p class="text-muted">{{ t('Enter your new password to recover your account.') }}</p>

									@include('components.notice-toast')
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
	</div>
@endsection