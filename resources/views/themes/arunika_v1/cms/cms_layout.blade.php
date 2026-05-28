@include('themes.arunika_v1.components.menu')

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
		<link href="{{ asset('assets/css/themes/arunika_v1/arunika_v1.css?v=').time() }}" rel="stylesheet">

		@stack('css')

		<title>Arunika Themes v1</title>

		<script>
		(function() 
		{
			// 1. Ambil Tema
			const savedTheme = localStorage.getItem('theme') || 'dark';
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

		<div class="d-flex w-100 h-100">
			<div class="ph-sidebar ph-no-transition" id="sidebar">

				<script> if (localStorage.getItem('sidebar-state') === 'expanded') { document.getElementById('sidebar').classList.add('ph-expanded'); } </script>
				
				<div class="ph-sidebar-logo-container" onclick="toggleSidebar()">
					<div class="ph-app-logo-icon">
						<img src="{{ asset('assets/logos/laraphoenix_onlybird_colored_2.png') }}" style="width: 30px">
					</div>

					<span class="ph-app-logo-text">{{ site_config()->site_name }}</span>
				</div>

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
					<div class="ph-list-group-wrapper">
						<a href="{{ url('awesome_admin') }}" class="list-group-item list-group-item-action">
							<div class="ph-nav-icon"><i class="fad fa-user-secret"></i></div>
							<span class="ph-nav-text">{{ t('Awesome Admin') }}</span>

							<div class="ph-custom-tooltip"><span>{{ t('Awesome Admin') }}</span></div>
						</a>
					</div>
				</div>

			</div>

			<div class="ph-layout-right" id="ph-layout-right">
				<div class="ph-top-bar" id="ph-top-bar">
					<div class="ph-header-btn" onclick="toggleSidebar()" title="Toggle Sidebar"><i class="fas fa-bars"></i></div>
					
					<div class="dropdown">
						<div class="ph-header-btn" data-bs-toggle="dropdown" title="Change Theme Color"><i class="fas fa-palette"></i></div>
						
						<div class="dropdown-menu p-3" style="min-width: 260px;">
							<h6 class="dropdown-header px-0 text-start" style="color: var(--ph-text-muted);">Choose Theme Color</h6>
							<div class="row g-2" id="color-picker-container"></div>

							<hr class="dropdown-divider bg-secondary opacity-25">

							<h6 class="dropdown-header px-0 text-start" style="color: var(--ph-text-muted);">Background Pattern</h6>
							
							<div class="d-flex gap-2">
								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('none')" title="No Pattern">
									<i class="fas fa-ban"></i>
								</button>

								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('winter')" title="Winter Snow">
									<i class="far fa-snowflake"></i>
								</button>
								
								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('christmas')" title="Christmas">
									<i class="fas fa-gift"></i>
								</button>
								
								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('eid')" title="Idul Fitri">
									<i class="fas fa-star-and-crescent"></i>
								</button>

								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('newyear')" title="New Year">
									<i class="fas fa-glass-cheers"></i>
								</button>

								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('valentine')" title="Valentine">
									<i class="fas fa-heart text-danger"></i>
								</button>

								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('imlek')" title="Imlek">
									<i class="fas fa-coins text-warning"></i>
								</button>

								<button class="btn btn-sm btn-outline-secondary flex-fill" onclick="changePattern('independence')" title="Independence Day">
									<i class="fas fa-flag text-danger"></i>
								</button>
							</div>
						</div>
					</div>

					<div class="ph-header-btn" onclick="toggleTheme()" title="Switch Theme"><i class="fas fa-moon" id="theme-icon"></i></div>

					<div class="ph-search-container d-none">
						<i class="fas fa-search text-secondary" style="font-size: 0.8rem;"></i>
						<input type="text" class="ph-search-input" placeholder="Search">
					</div>

					<div class="d-flex align-items-center ms-auto gap-3">
						<div class="ph-header-actions">

							{{-- Real-time Notification Bell --}}
							@include('components.cms-realtime-notification')

						    <div class="dropdown">
						        <button class="ph-btn-action-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						            <i class="fas fa-ellipsis-h"></i>
						        </button>
						        
						        <ul class="dropdown-menu ph-dropdown-menu-teams dropdown-menu-end">
						            <li><a class="dropdown-item" href="{{ url('account') }}"><i class="fad fa-cog me-2"></i> {{ t('Account Settings') }}</a></li>
						            <li><hr class="dropdown-divider"></li>
						            <li><a class="dropdown-item text-danger" href="{{ url('auth/logout') }}"><i class="fad fa-sign-out-alt me-2"></i> {{ t('Logout') }}</a></li>
						        </ul>
						    </div>

						    <div class="dropdown">
						        <button class="ph-btn-profile-wrapper" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						            <div class="ph-profile-initials">
						            	{!! get_avatar('frame', 'rounded-circle',  38) !!}
						            </div>
						            
						            <div class="ph-status-badge">
						            	<i class="fas fa-check"></i>
						            </div>
						        </button>

						        <div class="dropdown-menu ph-dropdown-menu-teams dropdown-menu-end p-0">
						            <div class="ph-profile-card">
						                <div class="ph-profile-card-header">
						                	<span class="ph-role-badge">{{ current_role() }}</span>
						                </div>
						                
						                <div class="ph-profile-card-body">
						                    <div class="ph-profile-avatar-lg">
						                    	{!! get_avatar('frame', 'rounded-circle',  64) !!}
						                    </div>

						                    <div class="ph-profile-info">
						                        <h5 class="mb-1">{{ auth()->user()->fullname }}</h5>
						                        <p>{{ auth()->user()->email }}</p>
						                        <a href="{{ url('profile') }}" class="ph-ms-account-link">My Profile <i class="fas fa-external-link-alt" style="font-size: 0.7em;"></i></a>
						                    </div>
						                </div>
						            </div>
						        </div>
						    </div>

						</div>
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

		<script src="{{ url('assets/js/themes/arunika_v1/arunika_v1.js?v=').time() }}"></script>
	</body>
</html>