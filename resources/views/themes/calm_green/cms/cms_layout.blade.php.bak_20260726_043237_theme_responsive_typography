@include('themes.calm_green.components.menu')

<!doctype html>
	<html lang="en" data-bs-theme="light">
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

			<!--- Simplebar CSS --->
			<link rel="stylesheet" href="{{ url('assets/plugins/simplebar/6.3.1/css/simplebar.css') }}">

			<!--- Vue Select CSS --->
			<link rel="stylesheet" href="{{ url('assets/plugins/vue/plugins/vue-select/css/vue-select.3.20.3.css') }}">

			<!--- VectorMAP CSS --->
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">

			<!--- Leaflet CSS --->
			<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
			<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" crossorigin="" />

			<!-- Custom CSS -->
			<link href="{{ asset('storage/fonts/nunito/fonts.css?v=').time() }}" rel="stylesheet">	

			<link href="{{ asset('assets/css/extending-css-bootstrap-5.css?v=').time() }}" rel="stylesheet">
			<link href="{{ asset('assets/css/themes/calm_green/phoenix-cms-calm-green.css?v=').time() }}" rel="stylesheet">
			<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">
			<link href="{{ asset('assets/css/themes/calm_green/aruna-admin-v7-calm-green.css?v=').time() }}" rel="stylesheet">

			@stack('css')

			<title>{{ site_config()->site_name }} | @yield('title')</title>

			<noscript>
				<style>
				/*
				 * Reinstate scrolling for non-JS clients
				 */
			
				.simplebar-content-wrapper 
				{
					overflow-x: visible;
					overflow-y: auto;
				}

				[data-simplebar] 
				{
					overflow: auto;
				}
				
				</style>
			</noscript>
		</head>
		
		<body>
			<!--- Ardev v6 Container Theme --->
			<div class="arv7-container {{ getVerticalSidebarMenuCollapse()['container'] }} position-relative">

				<!--- List Menu for Desktop View --->
				<div class="arv7-sidebar {{ getVerticalSidebarMenuCollapse()['sidebar'] }}" id="ph-app-sidebar-menu">
					<div class="arv7-brand fw-bold position-relative">
						<div class="d-flex align-items-center justify-content-center">	
							<div class="arv7-brand-text">{{ mb_substr(site_config()->site_name, 0, 1) }}</div>
						</div>

						<div class="d-flex align-items-center justify-content-start">
							<img src="{{ asset('assets/logos/laraphoenix_colored.png') }}" class="arv7-logo img-fuid" style="width: 150px">
							
							<a href="javascript:void(0)" style="padding: .5rem .25rem" class="btn btn-collapse bg-white border ms-auto d-flex align-items-center" :class="[isCollapsedSidebarMenu == true ? 'd-none' : '']" v-on:click="collapseVerticalSidebar">
								<i class="far fa-angle-left fa-fw"></i>
							</a>
						</div>

						<div class="position-absolute arv7-parent-button-collapsed" :class="[isCollapsedSidebarMenu == false ? 'd-none' : '']" style="top: 1.2rem;right: -0.8rem;">
							<a href="javascript:void(0)" style="padding: .5rem .25rem" class="btn bg-white border d-flex align-items-center" v-on:click="collapseVerticalSidebar">
								<i class="far fa-angle-right fa-fw"></i>
							</a>
						</div>
					</div>

					<div class="arv7-menu px-2 mb-0 overflow-y-visible" data-simplebar>
						<div class="arv7-title row d-none">
							<div class="col-auto fw-bold">
								{{ t('All') }}
							</div>

							<div class="col ps-0">
								<hr class="navbar-vertical-divider mb-0">
							</div>
						</div>

						<div class="list-group ph-list-group-menu list-group-flush">
							<a href="{{ url('/') }}" class="list-group-item ph-list-group-item ph-list-group-item-with-icon list-group-item-action d-flex align-items-center align-self-center" target="_blank">
								<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center"><i class="fal fa-link fa-fw"></i></span>
								<span class="arv7-parent-menu-name d-inline-block ms-2">{{ t('Visit Site') }}</span>
							</a>

							<div class="multi-collapse collapse position-relative">									
								<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
									<a href="{{ url('/') }}" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">{{ t('Visit Site') }}</span>
									</a>
								</div>
							</div>

							<a href="{{ url('dashboard') }}" class="list-group-item ph-list-group-item ph-list-group-item-with-icon list-group-item-action d-flex align-items-center align-self-center">
								<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center"><i class="fal fa-home fa-fw"></i></span>
								<span class="arv7-parent-menu-name d-inline-block ms-2">{{ t('Dashboard') }}</span>
							</a>

							<div class="multi-collapse collapse position-relative">									
								<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
									<a href="{{ url('dashboard') }}" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">{{ t('Dashboard') }}</span>
									</a>
								</div>
							</div>
						</div>

						{!! menu_versioning() !!}

						@if (checkIsAdmin())
							<div class="arv7-title row">
								<div class="col-auto fw-bold">
									{{ t('Admin') }}
								</div>

								<div class="col ps-0">
									<hr class="navbar-vertical-divider mb-0">
								</div>
							</div>

							<hr class="arv7-divider-line-category">

							<ul class="list-group ph-list-group-menu list-group-flush">
								<a href="{{ url('awesome_admin') }}" class="list-group-item ph-list-group-item ph-list-group-item-with-icon list-group-item-action d-flex align-items-center">
									<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center me-2">
										<i class="fal fa-user-secret fa-fw"></i>
									</span>

									<span class="arv7-parent-menu-name text-truncate" style="width: calc(100% - 55px)">
										<span class="arv7-parent-menu-name">{{ t('Admin Panel') }}</span>
									</span>
								</a>

								<div class="multi-collapse collapse position-relative">									
									<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
										<a href="{{ url('awesome_admin') }}" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
											<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">{{ t('Admin Panel') }}</span>
										</a>
									</div>
								</div>
							</ul>
						@endif
					</div>

					<div class="arv7-menu-footer px-2 py-2" id="CollapseMenuStatus">
						<ul class="list-group list-group-flush mt-auto">

							<a href="javascript:void(0)" class="d-none list-group-item list-group-item-action ph-list-group-item-footer d-flex align-items-center bg-white mb-0" :class="[isCollapsedSidebarMenu == true ? 'justify-content-center' : '']" v-on:click="collapseVerticalSidebar">
								<span class="arv7-icon-menu-name arv7-icon-collapse-view">
									<span v-if="isCollapsedSidebarMenu == false"><i class="far fa-arrow-from-right fa-fw fa-lg"></i></span>
									<span v-else-if="isCollapsedSidebarMenu == true" v-cloak><i class="far fa-arrow-from-left fa-fw fa-lg"></i></span>
								</span>

								<span class="arv7-parent-menu-name ms-2">{{ t('Collapsed View') }}</span>
							</a>

							<a href="{{ url('profile') }}" class="list-group-item list-group-item-action d-flex align-items-center mb-0">
								<div class="d-inline-flex align-items-center justify-content-center w-100">
									<div class="arv7-icon-menu-avatar flex-shrink-0">
										@if (getIsVerticalSidebarMenuCollapse() == true)
											{!! get_avatar('frame', 'rounded-circle',  30) !!}
										@else
											{!! get_avatar('frame', 'rounded-circle',  40) !!}
										@endif
									</div>
									
									<div class="arv7-parent-menu-name flex-grow-1 ms-2">
										<div class="fw-bold mb-0">{{ auth()->user()->fullname }}</div>
										<div class="text-muted">{{ current_role() }}</div>
									</div>
								</div>
							</a>

							<a href="{{ url('auth/logout') }}" class="list-group-item list-group-item-action ph-list-group-item-footer ph-list-group-item-footer-logout d-flex align-items-center mb-0" :class="[isCollapsedSidebarMenu == true ? 'justify-content-center' : '']">
								<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center me-2">
									<i class="far fa-sign-out fa-fw fa-lg"></i>
								</span>

								<span class="arv7-parent-menu-name ms-2">{{ t('Logout') }}</span>
							</a>
						</ul>
					</div>
				</div>
				<!--- End of List Menu for Desktop View --->

				<!--- List Menu for Mobile View --->
				<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
					<div class="offcanvas-header py-4">
						<div class="arv7-brand p-0 d-flex align-items-center justify-content-center h-auto">
							{{ site_config()->site_name }}
						</div>

						<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
					</div>

					<div class="offcanvas-body py-0">
						<div class="arv7-menu-mobile px-0 py-0" data-simplebar>
							<div class="arv7-title row">
								<div class="col-auto fw-bold">
									{{ t('All') }}
								</div>

								<div class="col ps-0">
									<hr class="navbar-vertical-divider mb-0">
								</div>
							</div>

							<div class="list-group ph-list-group-menu list-group-flush">
								<a href="{{ url('/') }}" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center" target="_blank">
									<i class="fal fa-external-link fa-fw"></i>
									<span class="arv7-menu-name">{{ t('Visit Site') }}</span>
								</a>

								<a href="{{ url('dashboard') }}" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center">
									<i class="fal fa-tachometer-alt fa-fw"></i>
									<span class="arv7-menu-name">{{ t('Dashboard') }}</span>
								</a>
							</div>

							{!! menu_versioning() !!}

							@if (checkIsAdmin())
								<div class="arv7-title row">
									<div class="col-auto fw-bold">
										{{ t('Admin') }}
									</div>

									<div class="col ps-0">
										<hr class="navbar-vertical-divider mb-0">
									</div>
								</div>

								<div class="list-group ph-list-group-menu list-group-flush">
									<a href="{{ url('/awesome_admin') }}" class="list-group-item ph-list-group-item list-group-item-action d-flex align-items-center">
										<i class="fal fa-user-secret fa-fw"></i>
										<span class="arv7-menu-name">{{ t('Admin Panel') }}</span>
									</a>
								</div>
							@endif
						</div>
					</div>
				</div>
				<!--- End of List Menu for Mobile View --->

				<!--- Main Side for Main Content --->
				<div class="arv7-main-side">
					<!--- Main Header Side For Desktop --->
					<div class="arv7-sideheader navbar navbar-expand-lg p-2 d-none">
						<div class="container">
							<div class="row gx-lg-2 w-100">
								<div class="col-md-6 d-flex align-items-center">
									<!-- Empty -->
								</div>

								<div class="col-md-6 d-flex align-items-center justify-content-end">
									<div class="collapse navbar-collapse" id="navbarSupportedContent">
										<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
											<li class="nav-item d-flex align-items-center dropdown">
												<a class="nav-link dropdown-toggle text-decoration-none position-relative px-3" data-bs-toggle="dropdown" aria-expanded="false" aria-current="page" href="#">
													<i class="fad fa-envelope fa-fw fs-5"></i>

													<div class="position-absolute" style="top: .1rem;right: .75rem">
														<i class="fas fa-circle fa-fw text-danger" style="font-size: 8.5px"></i>
													</div>
												</a>

												<div class="list-wrapper">
													<div class="dropdown-menu dropdown-menu-end shadow-sm p-3" data-bs-popper="static" style="min-width: 350px;margin-top: -1.3rem;">
														<div class="h6 border-bottom mb-0 pb-3">{{ t('Message') }}</div>
													
														<div class="list-group list-group-flush">
															<a href="#!" class="list-group-item text-decoration-none px-0 py-3">
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<img src="https://placehold.co/45/E01D24/FFF" alt="Placehold.Co" class="rounded-circle">
																	</div>
																
																	<div class="flex-grow-1 ms-3" style="max-width: 250px">
																		<div class="font-size-normal fw-bold text-truncate">Andhika Adhitia N</div>
																		<div class="font-size-normal text-truncate">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</div>
																	</div>
																</div>																
															</a>

															<a href="#!" class="list-group-item text-decoration-none px-0 py-3">
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<img src="https://placehold.co/45/E01D24/FFF" alt="Placehold.Co" class="rounded-circle">
																	</div>
																
																	<div class="flex-grow-1 ms-3" style="max-width: 250px">
																		<div class="font-size-normal fw-bold text-truncate">Andhika Adhitia N</div>
																		<div class="font-size-normal text-truncate">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</div>
																	</div>
																</div>																
															</a>

															<a href="#!" class="list-group-item text-decoration-none px-0 py-3">
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<img src="https://placehold.co/45/E01D24/FFF" alt="Placehold.Co" class="rounded-circle">
																	</div>
																
																	<div class="flex-grow-1 ms-3" style="max-width: 250px">
																		<div class="font-size-normal fw-bold text-truncate">Andhika Adhitia N</div>
																		<div class="font-size-normal text-truncate">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</div>
																	</div>
																</div>																
															</a>
														</div>
													</div>
												</div>
											</li>

											<li class="nav-item d-flex align-items-center dropdown">
												<a class="nav-link dropdown-toggle text-decoration-none position-relative px-3" data-bs-toggle="dropdown" aria-expanded="false" aria-current="page" href="#">
													<i class="fad fa-bell fa-fw fs-5"></i>

													<div class="position-absolute" style="top: .15rem;right: 1rem">
														<i class="fas fa-circle fa-fw text-danger" style="font-size: 8.5px"></i>
													</div>
												</a>

												<div class="list-wrapper">
													<div class="dropdown-menu dropdown-menu-end shadow-sm p-3" data-bs-popper="static" style="min-width: 350px;margin-top: -1.3rem;">
														<div class="h6 border-bottom mb-2 pb-3">{{ t('Notification') }}</div>
													
														<div class="list-group list-group-notification-dropdown list-group-flush">
															<a href="#!" class="list-group-item text-decoration-none border-0 px-0">
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<i class="fad fa-bells fa-lg" style="color: #E01D24"></i>
																	</div>
																
																	<div class="flex-grow-1 ms-3" style="max-width: 250px">
																		<div class="font-size-normal fw-bold text-truncate">Andhika Adhitia N</div>
																		<div class="font-size-normal text-truncate">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</div>
																	</div>
																</div>																
															</a>

															<a href="#!" class="list-group-item text-decoration-none border-0 px-0">
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<i class="fad fa-star fa-lg" style="color: #E01D24"></i>
																	</div>
																
																	<div class="flex-grow-1 ms-3" style="max-width: 250px">
																		<div class="font-size-normal fw-bold text-truncate">Andhika Adhitia N</div>
																		<div class="font-size-normal text-truncate">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</div>
																	</div>
																</div>																
															</a>

															<a href="#!" class="list-group-item text-decoration-none border-0 px-0">
																<div class="d-flex align-items-center">
																	<div class="flex-shrink-0">
																		<i class="fad fa-check fa-lg" style="color: #E01D24"></i>
																	</div>
																
																	<div class="flex-grow-1 ms-3" style="max-width: 250px">
																		<div class="font-size-normal fw-bold text-truncate">Andhika Adhitia N</div>
																		<div class="font-size-normal text-truncate">Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet</div>
																	</div>
																</div>																
															</a>
														</div>

														<div class="text-center border-top font-size-normal mt-3 pt-3">
															<a href="{{ url('notification') }}" class="text-decoration-none">
																{{ t('See All') }}
															</a>
														</div>
													</div>
												</div>
											</li>

											<li class="nav-item d-flex align-items-center ms-2">
												<div class="py-1 dropdown">
													<a href="javascript:void(0)" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
														<div class="d-flex align-items-center">
															<div class="flex-shrink-0">
																{!! get_avatar('frame', 'rounded-circle', 45) !!}
															</div>
															
															<div class="flex-grow-1 ms-3">
																<div class="fw-bold mb-0">{{ auth()->user()->fullname }}</div>
																<div>{{ current_role() }}</div>
															</div>
														</div>
													</a>

													<div class="list-wrapper">
														<ul class="dropdown-menu dropdown-menu-end shadow-sm p-1" data-bs-popper="static" style="min-width: 210px;margin-top: -1.3rem;">
															<li><a class="dropdown-item font-size-normal p-2" href="{{ url('profile') }}"><i class="fas fa-user fa-fw me-1"></i> {{ t('View Profile') }}</a></li>
															<li><a class="dropdown-item font-size-normal p-2" href="{{ url('account') }}"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Account Settings') }}</a></li>
															<li><hr class="dropdown-divider"></li>
															<li><a class="dropdown-item font-size-normal p-2" href="{{ url('auth/logout') }}"><i class="fas fa-sign-out fa-fw me-1"></i> {{ t('Logout') }}</a></li>
														</ul>
													</div>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--- End of Main Header Side For Desktop --->

					<!--- Main Header Side For Mobile --->
					<div class="arv7-sideheader navbar navbar-expand-lg navbar-light bg-white shadow-sm p-2 d-flex d-lg-none">
						<div class="container-fluid">
							<ul class="navbar-nav arv7-button-bars">
								<li class="nav-item dropdown">
									<a class="nav-link" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample"><i class="far fa-bars fs-3"></i></a>
								</li>
							</ul>

							<ul class="navbar-nav ms-auto">
								<li class="nav-item dropdown">
									<a href="javascript:void(0)" class="nav-link dropdown-toggle" aria-current="page" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false"><i class="fad fa-user-circle fs-1"></i></a>

									<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
										<li><a class="dropdown-item font-size-normal" href="{{ url('account') }}"><i class="fas fa-cog fa-fw me-1"></i> Account Settings</a></li>
										<li><a class="dropdown-item font-size-normal" href="{{ url('auth/logout') }}"><i class="fas fa-sign-out fa-fw me-1"></i> Logout</a></li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
					<!--- End of Main Header Side For Mobile --->

					<div class="arv7-content-side mb-0">
						@yield('content')
					</div>
				</div>
				<!--- End of Main Side for Main Content --->
			</div>

			<script type="text/javascript">const site_url = '{{ url('/') }}';</script>

			@stack('js-priority')

			<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script>
			<!-- <script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}"></script> -->

			<script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script>
			<script src="{{ url('assets/plugins/lodash/lodash.4.17.21.min.js') }}"></script>
			<script src="{{ url('assets/plugins/sortable/sortable.1.10.2.min.js') }}"></script>
			<script src="{{ url('assets/plugins/echarts/5.5.1/dist/echarts.min.js') }}"></script>

			<script src="{{ url('assets/plugins/simplebar/6.3.1/js/simplebar.min.js') }}"></script>

			<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
			<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

			<!-- <script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.21.global.dev.js') }}"></script> -->
			<script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.21.global.prod.js') }}"></script>
			<!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script> -->
			<script src="{{ url('assets/plugins/vue/plugins/vuejs-paginate-next/js/vuejs-paginate-next.1.0.2.umd.js') }}"></script>
			<script src="{{ url('assets/plugins/vue/plugins/vue-debounce/js/vue-debounce.5.0.0.min.js') }}"></script>
			<script src="{{ url('assets/plugins/vue/plugins/vue-draggable/js/vuedraggable.4.0.1.umd.min.js') }}"></script>
			<script src="{{ url('assets/plugins/vue/plugins/vue-select/js/vue-select.4.0.0.beta6.umd.js') }}"></script>

			<script type="text/javascript">
				const { createApp, ref, reactive, defineModel, h } = Vue;
			</script>

			@stack('js')

			<script src="{{ url('assets/js/c/themes/calm_green/vueV3-calm-green-theme-2025.js?v=').time() }}"></script>
			<script src="{{ url('assets/js/c/vue3/vueV3-awesome-admin-2025.js?v=').time() }}"></script>

			<script type="text/javascript">

				function isTextTruncatedMenu(element) 
				{
					return element.offsetWidth < element.scrollWidth;
				}

				function isTextTruncatedSubmenu(element) 
				{
					return element.offsetWidth < element.scrollWidth;
				}

				let isItemMenuTruncated = [];
				let isItemSubmenuTruncated = [];
				
				const hasCollapseMenuClass = document.getElementsByClassName('arv7-container')[0].classList.contains('arv7-with-vertical-menu-collapsed');
				
				// const listGroupItemMenu = document.querySelectorAll(".ph-list-group-item span");
				// const listGroupItemSubmenu = document.querySelectorAll(".ph-list-group-subitem span");

				/*
				const listGroupItemMenu = document.querySelectorAll(".arv7-parent-menu-name");

				console.log(listGroupItemMenu);

				for (const itemMenu of listGroupItemMenu)
				{
					isItemMenuTruncated = isTextTruncatedMenu(itemMenu);

					if (isItemMenuTruncated) 
					{
						itemMenu.parentElement.setAttribute('data-bs-toggle', 'tooltip');

						console.log(itemMenu);
					}
				}
				*/

				// for (const itemMenu of listGroupItemMenu)
				// {
				// 	isItemMenuTruncated = isTextTruncatedMenu(itemMenu);

				// 	if (isItemMenuTruncated) 
				// 	{
				// 		if (getComputedStyle(itemMenu.querySelector('.arv7-parent-menu-name'), null).display !== 'none')
				// 		{
				// 			itemMenu.setAttribute('data-bs-toggle', 'tooltip');
				// 		}
				// 	}
				// }

				const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
				const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
			</script>
	</body>
</html>