<!doctype html>
	<html lang="en">
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
			<link href="{{ asset('storage/fonts/nunito/fonts.css?v=').time() }}" rel="stylesheet">	

			<link href="{{ asset('assets/css/themes/calm_green/phoenix-cms-calm-green.css?v=').time() }}" rel="stylesheet">
			<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">	

			@stack('css')

			<style>
				.grecaptcha-badge2
				{ 
					visibility: hidden !important; 
				}

				:root 
				{
					/* Default Header Background Color  */

					/* --page-header-background: #EC777B; */
					/* --page-header-background: ##1FA6759F;*/
					--page-header-background: {{ custom_page_theme_setting(getCurrentURIForPageTheme(4))["page_color_nuances"]  }};
				}				
			</style>

			@if (site_config()->enable_recaptcha_signin == 0 &&
				 site_config()->recaptcha_site_key !== null &&
				 site_config()->recaptcha_secret_key !== null)

					{!! RecaptchaV3::initJs() !!}

			@endif

			<title>{{ site_config()->site_name }} | @yield('title')</title>
		</head>
		
		<body>
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

			<script src="{{ url('assets/js/c/vue3/vueV3-auth-2025.js?v=').time() }}"></script>
	</body>
</html>