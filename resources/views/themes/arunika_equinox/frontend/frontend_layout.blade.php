<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
		
		@stack('meta')

		@yield('csrf')

		<meta name="csrf-token" content="{{ csrf_token() }}">

		<link href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="{{ asset('storage/fonts/nunito/fonts.css?v=').time() }}" rel="stylesheet">	

		<link href="{{ asset('assets/css/extending-css-bootstrap-5.css?v=').time() }}" rel="stylesheet">
		<link href="{{ asset('assets/css/themes/calm_green/phoenix-cms-calm-green.css?v=').time() }}" rel="stylesheet">
		<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">	
		<link href="{{ asset('assets/css/frontend-menu-dropdown.css?v=').time() }}" rel="stylesheet">

		@stack('css')

		<title>{{ site_config()->site_name }} | @yield('title')</title>

		<style>
			.ph-header ul.ph-first-list-menu li a,
			.ph-header ul.ph-second-list-menu li a 
			{
				margin: 0px 2.5px 0px 2.5px;
			}

			.ph-header ul.ph-first-list-menu li a.btn,
			.ph-header ul.ph-second-list-menu li a.btn
			{
				padding-left: 1rem;
				padding-right: 1rem;
			}
		</style>
	</head>

	<body>
		@include('themes.partials.frontend_menu_dropdown')

		@yield('content')
		
		<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script>
		<script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}"></script>

		<script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script>
		<script src="{{ url('assets/plugins/lodash/lodash.4.17.21.min.js') }}"></script>
		<script src="{{ url('assets/plugins/sortable/sortable.1.10.2.min.js') }}"></script>

		<script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.17.global.prod.min.js') }}"></script>			
		<script src="{{ url('assets/plugins/vue/plugins/vuejs-paginate-next/js/vuejs-paginate-next.1.0.2.umd.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vue-debounce/js/vue-debounce.5.0.0.min.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vue-draggable/js/vuedraggable.4.0.1.umd.min.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vue-select/js/vue-select.4.0.0.beta6.umd.js') }}"></script>

		<script type="text/javascript">
			const { createApp, ref, reactive, defineModel } = Vue;
		</script>

		@stack('js')
	</body>
</html>
