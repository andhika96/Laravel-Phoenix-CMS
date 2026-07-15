@include('themes.arunika_v2.components.menu')

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		@stack('meta')

		<!-- Bootstrap -->
		<link href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}" rel="stylesheet">

		<!-- Fontawesome -->
		<link href="{{ url('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}" rel="stylesheet">

		<!-- Font -->
		<link href="{{ asset('storage/fonts/nunito/fonts.css?v=').time() }}" rel="stylesheet">	

		<!-- Vue Select CSS --->
		<link rel="stylesheet" href="{{ url('assets/plugins/vue/plugins/vue-select/css/vue-select.3.20.3.css') }}">

		<!-- Simplebar -->
		<link href="{{ url('assets/plugins/simplebar/6.3.1/css/simplebar.css') }}" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">
		<link href="{{ asset('assets/css/themes/arunika_v2/arunika_v2.css?v=').time() }}" rel="stylesheet">

		@stack('css')

		<title>@yield('title', site_config()->site_name) - {{ site_config()->site_name }}</title>

		<script>
		(function() 
		{
			// 1. Ambil Tema
			const savedTheme = localStorage.getItem('theme') || 'light';
			// Kita pasang di documentElement (tag <html>) karena body belum ready
			document.documentElement.setAttribute('data-bs-theme', savedTheme);

			// 2. Ambil Warna
			const savedColor = localStorage.getItem('theme-color');
			
			if (savedColor) 
			{
				document.documentElement.style.setProperty('--ph-theme-primary', savedColor);
			}
		})();
		</script>
	</head>

	<body>

		<div class="ph-app-shell d-flex w-100 h-100">
			<div class="ph-sidebar ph-no-transition" id="sidebar">

				<script>
				(function()
				{
					const savedSidebarState = localStorage.getItem('sidebar-state');
					const shouldExpand = window.innerWidth > 768 && savedSidebarState !== 'collapsed';

					if (shouldExpand)
					{
						document.getElementById('sidebar').classList.add('ph-expanded');
					}
				})();
				</script>
				
				<div class="ph-sidebar-logo-container" aria-label="{{ site_config()->site_name }}">
					<div class="ph-app-logo-icon">
						<img src="{{ asset('assets/logos/laraphoenix_onlybird_colored_2.png') }}" alt="{{ site_config()->site_name }}">
					</div>

					<span class="ph-app-logo-initial" aria-hidden="true">{{ mb_strtoupper(mb_substr(trim(site_config()->site_name), 0, 1)) }}</span>
					<span class="ph-app-logo-text">{{ site_config()->site_name }}</span>
				</div>

				<button class="ph-sidebar-toggle" id="sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true">
					<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
						<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
						<path d="M8.25 3.25V20.75"></path>
						<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
					</svg>
				</button>

				<div id="sidebar-scroll-content">
					
					<div class="list-group list-group-flush w-100">
						<div class="ph-list-group-wrapper">
							<a href="{{ url('/') }}" class="list-group-item list-group-item-action" target="_blank">
								<div class="ph-nav-icon"><i class="fal fa-link fa-fw"></i></div>
								<span class="ph-nav-text">{{ t('Visit Site') }}</span>

								<div class="ph-custom-tooltip"><span>{{ t('Visit Site') }}</span></div>
							</a>

							<a href="{{ url('dashboard') }}" class="list-group-item list-group-item-action">
								<div class="ph-nav-icon"><i class="fal fa-home fa-fw"></i></i></div>
								<span class="ph-nav-text">{{ t('Dashboard') }}</span>

								<div class="ph-custom-tooltip"><span>{{ t('Dashboard') }}</span></div>
							</a>

							<a href="{{ url('chat') }}" class="list-group-item list-group-item-action">
								<div class="ph-nav-icon"><i class="fal fa-comments fa-fw"></i></div>
								<span class="ph-nav-text">{{ t('Messages') }}</span>
								<div class="ph-custom-tooltip"><span>{{ t('Messages') }}</span></div>
							</a>
						</div>

						{!! menu_versioning() !!}
					</div>

				</div>

				<div class="ph-sidebar-footer">
					<div class="ph-sidebar-user-panel">
						<a href="{{ url('profile') }}" class="ph-sidebar-user-card">
							<span class="ph-sidebar-user-avatar">{!! get_avatar('frame', 'rounded-circle', 38) !!}</span>
							<span class="ph-sidebar-user-meta">
								<strong>{{ auth()->user()->fullname }}</strong>
								<span>{{ auth()->user()->email }}</span>
							</span>
							<i class="fas fa-chevron-right ph-sidebar-user-chevron"></i>
						</a>

						<a href="{{ url('auth/logout') }}" class="ph-sidebar-logout" title="{{ t('Logout') }}">
							<i class="fal fa-sign-out-alt fa-fw"></i>
							<span>{{ t('Logout') }}</span>
						</a>
					</div>
				</div>

			</div>

			<div class="ph-layout-right" id="ph-layout-right">
				<div class="ph-top-bar" id="ph-top-bar">
					<button class="ph-mobile-sidebar-trigger" type="button" onclick="toggleSidebar()" aria-label="Open navigation">
						<i class="fas fa-bars"></i>
					</button>

					<div class="ph-header-welcome">
						<span>{{ t('Welcome') }},</span>
						<strong>{{ auth()->user()->fullname }}</strong>
					</div>

					<label class="ph-search-container" for="ph-global-search">
						<i class="fal fa-search"></i>
						<input type="search" class="ph-search-input" id="ph-global-search" placeholder="{{ t('Find something') }}" autocomplete="off">
						<span class="ph-search-shortcut"><i class="fal fa-command"></i> K</span>
					</label>

					<div class="ph-header-actions">
						<button class="ph-btn-action-icon ph-theme-toggle" type="button" onclick="toggleTheme()" title="{{ t('Dark Mode') }}" aria-label="{{ t('Dark Mode') }}" aria-pressed="false">
							<i class="fas fa-sun ph-theme-icon"></i>
						</button>

						{{-- Help button is temporarily hidden. --}}
						{{--
						<a href="{{ url('awesome_admin') }}" class="ph-btn-action-icon ph-header-help" title="{{ t('Help') }}" aria-label="{{ t('Help') }}">
							<i class="fal fa-question-circle"></i>
						</a>
						--}}

						{{-- Real-time notification bell is temporarily hidden. --}}
						{{-- @include('components.cms-realtime-notification') --}}

						@if(checkIsAdmin())
							<a href="{{ url('awesome_admin') }}" class="ph-btn-action-icon ph-header-settings" title="{{ t('Admin Panel') }}" aria-label="{{ t('Admin Panel') }}">
								<i class="fas fa-user-secret"></i>
							</a>
						@endif
					</div>
				</div>

				<div class="ph-main-panel">
					<div class="ph-scrollable-content">
						@yield('content')
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">const site_url = '{{ url('/') }}';</script>

		@stack('js-priority')

		<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script>
		<script src="{{ url('assets/plugins/simplebar/6.3.1/js/simplebar.min.js') }}"></script>
		<script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script>
		<script src="{{ url('assets/plugins/lodash/lodash.4.17.21.min.js') }}"></script>
		<script src="{{ url('assets/plugins/sortable/sortable.1.10.2.min.js') }}"></script>

		<script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.21.global.prod.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vuejs-paginate-next/js/vuejs-paginate-next.1.0.2.umd.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vue-debounce/js/vue-debounce.5.0.0.min.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vue-draggable/js/vuedraggable.4.0.1.umd.min.js') }}"></script>
		<script src="{{ url('assets/plugins/vue/plugins/vue-select/js/vue-select.4.0.0.beta6.umd.js') }}"></script>

		<script type="text/javascript">
			const { createApp, ref, reactive, defineModel, h } = Vue;
		</script>

		@stack('js')

		<script src="{{ url('assets/js/themes/arunika_v2/arunika_v2.js?v=').time() }}"></script>
	</body>
</html>
