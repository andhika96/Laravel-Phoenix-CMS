@php
	$authPage = trim($__env->yieldContent('auth-page', 'login'));
	$authStyles = ['login' => 'login', 'signup' => 'signup', 'forgotPassword' => 'forgot-password', 'resetPassword' => 'reset-password'];
	$authPage = array_key_exists($authPage, $authStyles) ? $authPage : 'login';
	$authSettings = custom_page_theme_setting($authPage);
	$authPalette = \App\Support\AuthAppearance::palette($authSettings['page_primary_color'] ?? null);
	$siteTypography = app(\App\Support\SiteTypography::class)->resolve(site_config());
@endphp

<!doctype html>
	<html lang="en" style="--ph-font-family: '{{ $siteTypography['fontFamilyName'] }}', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; --ph-font-size: {{ $siteTypography['fontSize'] }};">
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			
			@stack('meta')
			
			@yield('csrf')

			<meta name="csrf-token" content="{{ csrf_token() }}">

			<!-- Bootstrap CSS -->
			<link href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}" rel="stylesheet">

			<!-- Fontawesome -->
			<link href="{{ url('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}" rel="stylesheet">

			<!-- Nunito Lato CSS -->
			<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap"> -->

			<!-- Custom CSS -->
			<link href="{{ asset('storage/fonts/'.$siteTypography['fontFamilyCode'].'/fonts.css?v=').time() }}" rel="stylesheet">

			<link href="{{ asset('assets/css/auth/auth-base.css?v=').filemtime(public_path('assets/css/auth/auth-base.css')) }}" rel="stylesheet">
			<link href="{{ asset('assets/css/auth/'.$authStyles[$authPage].'.css?v=').filemtime(public_path('assets/css/auth/'.$authStyles[$authPage].'.css')) }}" rel="stylesheet">

			@stack('css')

			<style>
				body.ph-auth-page
				{
					--page-header-background: {{ $authSettings['page_color_nuances'] ?? '#1FA6759F' }};
					--auth-primary: {{ $authPalette['primary'] }};
					--auth-primary-hover: {{ $authPalette['hover'] }};
					--auth-primary-active: {{ $authPalette['active'] }};
					--auth-primary-disabled: {{ $authPalette['disabled'] }};
					--auth-primary-text: {{ $authPalette['text'] }};
					--auth-primary-hover-text: {{ $authPalette['hover_text'] }};
					--auth-primary-active-text: {{ $authPalette['active_text'] }};
					--auth-primary-rgb: {{ $authPalette['rgb'] }};
				}
			</style>

			@if (site_config()->enable_recaptcha_signin == 0 &&
				 site_config()->recaptcha_site_key !== null &&
				 site_config()->recaptcha_secret_key !== null)

					{!! RecaptchaV3::initJs() !!}

			@endif

			<title>{{ site_config()->site_name }} | @yield('title')</title>
		</head>
		
		<body class="ph-auth-page" data-auth-page="{{ $authPage }}" data-auth-layout="@yield('auth-template')">
			@yield('content')
			
			<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script>
			<script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}"></script>

			<script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script>
			<script src="{{ url('assets/plugins/lodash/lodash.4.17.21.min.js') }}"></script>

			<script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.17.global.prod.min.js') }}"></script>		
			<script src="{{ url('assets/plugins/vue/plugins/vue-debounce/js/vue-debounce.5.0.0.min.js') }}"></script>	

			<script type="text/javascript">
				const { createApp, ref, reactive, defineModel } = Vue;
				const reCaptchaSiteKey = '{{ (site_config()->recaptcha_site_key !== null) ? site_config()->recaptcha_site_key : env('RECAPTCHAV3_SITEKEY', '') }}';
			</script>

			@stack('js')

			<script src="{{ url('assets/js/vue3/auth/vueV3-auth-2026.js?v=').time() }}"></script>
	</body>
</html>
