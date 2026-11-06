@extends('auth.auth_layout')

@section('auth-page', 'forgotPassword')
@section('auth-template', 'card')

@section('title', t('Forgot Password'))

@section('content')

	<div class="page-header" id="ph-app-auth">
		<div class="page-header-image" style="background-image: url('{{ url(custom_page_theme_setting("forgotPassword")["page_background_image"]) }}');"></div>
		
		<div class="container vh-100">
			<div class="d-flex justify-content-center align-items-center vh-100">
				<div class="ph-form position-relative bg-white rounded border-0 p-4 p-lg-5">
					<div id="ph-form-recovery-password-submit">
						<form action="{{ route('cms.core.auth.forgotpassword.process') }}" method="post" @submit="recoveryPassword" ref="formHTML">
							<div class="position-absolute text-start h4" style="top: 1.5rem;left: 1.5rem">
								<a href="{{ url('auth/login') }}" class="text-dark"><i class="fas fa-long-arrow-left"></i></a>
							</div>

							<div class="row g-3 gy-4 mt-0">
								<div class="col-12 form-group text-center">
									<h4 class="fw-bold">{{ t('Forgot Password') }}</h4>
									<p class="text-muted">{{ t("Enter your email address and we will send you an email with instructions to reset your password.") }}</p>
								
									@include('components.notice-toast')
								</div>

								<div class="col-12 form-group">
									<label class="form-label fw-semibold">{{ t('Email Address') }}</label>
									<input type="text" name="email" class="form-control font-size-inherit" placeholder="{{ t('Enter your email') }}">
								</div>

								<div class="col-12 form-group text-center">
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