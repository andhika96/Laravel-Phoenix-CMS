@extends('auth.auth_layout')

@section('auth-page', 'forgotPassword')
@section('auth-template', 'default')

@section('title', t('Forgot Password'))

@section('content')

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
								
									@include('components.notice-toast')
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