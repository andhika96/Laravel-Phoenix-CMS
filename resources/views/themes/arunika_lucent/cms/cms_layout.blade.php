@include('themes.arunika_lucent.components.menu')

@php
	$siteConfig = site_config();
	$siteName = $siteConfig->site_name;
	$siteTypography = app(\App\Support\SiteTypography::class)->resolve($siteConfig);
	$currentUserRole = auth()->user()->getRoleNames()->first() ?: t('Account');
@endphp

<!DOCTYPE html>
<html lang="en" style="--ph-font-family: '{{ $siteTypography['fontFamilyName'] }}', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; --ph-font-size: {{ $siteTypography['fontSize'] }};">
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
		<link id="arunikaActiveFontStylesheet" data-font-base-url="{{ asset('storage/fonts') }}" href="{{ asset('storage/fonts/'.$siteTypography['fontFamilyCode'].'/fonts.css?v=').time() }}" rel="stylesheet">

		<!-- Vue Select CSS --->
		<link rel="stylesheet" href="{{ url('assets/plugins/vue/plugins/vue-select/css/vue-select.3.20.3.css') }}">

		<!-- Simplebar -->
		<link href="{{ url('assets/plugins/simplebar/6.3.1/css/simplebar.css') }}" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">
		<link href="{{ asset('assets/css/themes/arunika_lucent/arunika_lucent.css?v=').time() }}" rel="stylesheet">
		<link href="{{ asset('assets/css/theme-responsive-typography.css?v=').time() }}" rel="stylesheet">

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
				const isCoolGray = savedColor.toUpperCase() === '#C7CCD8';
				const colorRoot = document.documentElement;

				colorRoot.style.setProperty('--ph-theme-primary', isCoolGray ? '#667085' : savedColor);
				colorRoot.style.setProperty('--ph-theme-surface-tint', savedColor);

				if (isCoolGray)
				{
					colorRoot.dataset.phThemeColor = 'cool-gray';
				}
				else
				{
					delete colorRoot.dataset.phThemeColor;
				}
			}
		})();
		</script>
	</head>

	<body class="ph-theme-arunika-lucent">
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
				
				<div class="ph-lucent-sidebar-account" aria-label="{{ t('Account') }}">
					<div class="dropdown ph-sidebar-profile">
						<button class="ph-sidebar-user-card" type="button" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false" aria-label="{{ t('Open profile menu') }}">
							<span class="ph-sidebar-user-avatar">{!! get_avatar('frame', 'rounded-circle', 38) !!}</span>
							<span class="ph-sidebar-user-meta">
								<strong>{{ auth()->user()->fullname }}</strong>
								<span>{{ $currentUserRole }}</span>
							</span>
							<i class="fal fa-ellipsis-v ph-lucent-account-menu" aria-hidden="true"></i>
						</button>

						<div class="dropdown-menu ph-header-profile-menu ph-sidebar-profile-menu">
							<div class="ph-profile-menu-user">
								<span class="ph-profile-menu-avatar">{!! get_avatar('frame', 'rounded-circle', 36) !!}</span>
								<span class="ph-profile-menu-identity">
									<strong>{{ auth()->user()->fullname }}</strong>
									<span>{{ $currentUserRole }}</span>
								</span>
							</div>

							<div class="ph-profile-menu-section">
								<a class="dropdown-item" href="{{ url('profile') }}">
									<i class="fal fa-user-circle fa-fw"></i>
									<span>{{ t('Profile') }}</span>
								</a>

								<a class="dropdown-item" href="{{ url('account') }}">
									<i class="fal fa-cog fa-fw"></i>
									<span>{{ t('Settings') }}</span>
								</a>

								<button class="dropdown-item ph-profile-theme-toggle ph-theme-toggle" type="button" onclick="toggleTheme()" aria-label="{{ t('Dark Mode') }}" aria-pressed="false">
									<i class="fas fa-sun fa-fw ph-theme-icon"></i>
									<span>{{ t('Dark Mode') }}</span>
									<span class="ph-profile-switch" aria-hidden="true"><span></span></span>
								</button>

								<button class="dropdown-item ph-profile-color-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ph-profile-theme-colors" aria-expanded="false" aria-controls="ph-profile-theme-colors">
									<i class="fal fa-palette fa-fw"></i>
									<span>{{ t('Theme Color') }}</span>
									<i class="fal fa-chevron-right ph-profile-color-chevron" aria-hidden="true"></i>
								</button>

								<div class="collapse ph-profile-color-collapse" id="ph-profile-theme-colors">
									<div class="ph-profile-color-section">
										<span class="ph-profile-color-label">{{ t('Choose Theme Color') }}</span>
										<div class="row g-2" id="color-picker-container"></div>
									</div>
								</div>
							</div>

							<div class="ph-profile-menu-section ph-profile-menu-footer">
								<a class="dropdown-item text-danger" href="{{ url('auth/logout') }}">
									<i class="fal fa-sign-out-alt fa-fw"></i>
									<span>{{ t('Logout') }}</span>
								</a>
							</div>
						</div>
						</div>

						<button class="ph-lucent-sidebar-toggle" id="ph-lucent-sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="{{ t('Hide sidebar') }}" aria-expanded="true" title="{{ t('Hide sidebar') }}">
							<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
								<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
								<path d="M8.25 3.25V20.75"></path>
								<path d="M16 8.75L12.75 12L16 15.25"></path>
							</svg>
						</button>
					<button class="ph-mobile-sidebar-close" type="button" onclick="toggleSidebar()" aria-label="{{ t('Close navigation') }}">
						<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
							<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
							<path d="M8.25 3.25V20.75"></path>
							<path d="M16 8.75L12.75 12L16 15.25"></path>
						</svg>
					</button>
				</div>

				<div id="sidebar-scroll-content">
					
					<div class="list-group list-group-flush w-100">
						<div class="ph-list-group-wrapper">
						<a href="{{ url('dashboard') }}" class="list-group-item list-group-item-action">
								<div class="ph-nav-icon"><i class="fal fa-home fa-fw"></i></i></div>
								<span class="ph-nav-text">{{ t('Dashboard') }}</span>

								<div class="ph-custom-tooltip"><span>{{ t('Dashboard') }}</span></div>
						</a>

						<a href="{{ url('/') }}" class="list-group-item list-group-item-action" target="_blank">
								<div class="ph-nav-icon"><i class="fal fa-external-link fa-fw"></i></div>
								<span class="ph-nav-text">{{ t('View site') }}</span>

								<div class="ph-custom-tooltip"><span>{{ t('View site') }}</span></div>
						</a>

						</div>

						{!! menu_versioning() !!}
					</div>

				</div>

				<div class="ph-lucent-sidebar-utilities" aria-label="{{ t('Administration and settings') }}">
					@if(checkIsAdmin())
						<a class="ph-lucent-sidebar-admin" href="{{ url('awesome_admin') }}">
							<i class="fal fa-user-secret" aria-hidden="true"></i>
							<span>{{ t('Awesome Admin') }}</span>
						</a>
					@endif
					<a href="{{ url('account') }}">
						<i class="fal fa-cog" aria-hidden="true"></i>
						<span>{{ t('Settings') }}</span>
					</a>
				</div>

			</div>

			<div class="ph-layout-right" id="ph-layout-right">
				<div class="ph-top-bar" id="ph-top-bar">
					<button class="ph-mobile-sidebar-trigger" type="button" onclick="toggleSidebar()" aria-label="Open navigation" aria-expanded="false">
						<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
							<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
							<path d="M8.25 3.25V20.75"></path>
							<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
						</svg>
					</button>

					<div class="ph-header-nav-control">
						<button class="ph-sidebar-toggle" id="sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true">
							<svg class="ph-sidebar-toggle-icon" viewBox="0 0 24 24" aria-hidden="true">
								<rect x="2.75" y="2.75" width="18.5" height="18.5" rx="4"></rect>
								<path d="M8.25 3.25V20.75"></path>
								<path class="ph-sidebar-toggle-chevron" d="M16 8.75L12.75 12L16 15.25"></path>
							</svg>
						</button>
						<span class="ph-header-divider" aria-hidden="true"></span>
					</div>

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
						<div class="ph-header-notification is-hidden" aria-hidden="true">
							@include('components.cms-realtime-notification')
						</div>

						@if(checkIsAdmin())
							<a href="{{ url('awesome_admin') }}" class="ph-btn-action-icon ph-header-awesome-admin" title="{{ t('Awesome Admin') }}" aria-label="{{ t('Open Awesome Admin') }}">
								<i class="fal fa-user-secret"></i>
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

		<script src="{{ url('assets/js/themes/arunika_lucent/arunika_lucent.js?v=').time() }}"></script>
	</body>
</html>

