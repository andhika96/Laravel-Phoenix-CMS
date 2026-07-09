@php
	$frontendMenuItems = \App\Support\FrontendMenuBuilder::items();
@endphp

<header class="navbar navbar-expand-lg ph-header ph-fe-header bg-body-tertiary">
	<div class="container">
		<a class="navbar-brand ph-fe-brand" href="{{ url('/') }}">{{ site_config()->site_name }}</a>
		
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#frontendHeaderMenu" aria-controls="frontendHeaderMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="frontendHeaderMenu">
			<ul class="navbar-nav ph-fe-navbar ms-auto">
				@foreach ($frontendMenuItems as $item)
					@php
						$dropdownConfig = $item['dropdown_config'];
						$dropdownType = $dropdownConfig['dropdown_type'];
						$dropdownBox = $dropdownConfig['config_json']['dropdown'];
						$bootstrapBox = $dropdownConfig['config_json']['bootstrap'];
						$megaBox = $dropdownConfig['config_json']['mega'];
						$submenus = $item['submenus'];
						$visibleSubmenus = array_slice($submenus, 0, (int) $megaBox['max_items']);
						$hasDropdown = $dropdownType !== 'none' && count($submenus) > 0;
						$arrowClass = $dropdownBox['show_arrow'] ? 'ph-fe-dropdown-has-arrow' : '';
					@endphp

					@if ($hasDropdown && $dropdownType == 'bootstrap')
						<li class="nav-item dropdown ph-fe-menu-item" style="--ph-fe-dropdown-margin: {{ (int) $dropdownBox['margin_top'] }}px;">
							<a class="nav-link dropdown-toggle ph-fe-link" href="{{ $item['parent_url'] }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								@include('themes.partials.frontend_menu_icon', ['iconUrl' => $item['icon_url'], 'iconHtml' => $item['icon_html'], 'className' => 'ph-fe-parent-icon'])
								<span>{{ $item['parent_name'] }}</span>
							</a>

							<ul class="dropdown-menu ph-fe-dropdown-menu ph-fe-bootstrap-dropdown ph-fe-dropdown-align-{{ $dropdownBox['align'] }} {{ $arrowClass }}" style="--ph-fe-bootstrap-width: {{ (int) $bootstrapBox['width'] }}px; --ph-fe-dropdown-arrow-size: {{ (int) $dropdownBox['arrow_size'] }}px;">
								@foreach ($visibleSubmenus as $submenu)
									@php
										$submenuHasIcon = $submenu['icon_url'] !== '' || $submenu['icon_html'] !== '';
									@endphp

									<li>
										<a class="dropdown-item ph-fe-bootstrap-item" href="{{ $submenu['submenu_url'] }}">
											<span class="ph-fe-bootstrap-icon-slot {{ $submenuHasIcon ? '' : 'is-empty' }}">
												@include('themes.partials.frontend_menu_icon', ['iconUrl' => $submenu['icon_url'], 'iconHtml' => $submenu['icon_html'], 'className' => 'ph-fe-submenu-icon'])
											</span>
											<span>{{ $submenu['submenu_name'] }}</span>
										</a>
									</li>
								@endforeach
							</ul>
						</li>
					@elseif ($hasDropdown && $dropdownType == 'mega')
						<li class="nav-item dropdown ph-fe-menu-item" style="--ph-fe-dropdown-margin: {{ (int) $dropdownBox['margin_top'] }}px;">
							<a class="nav-link dropdown-toggle ph-fe-link" href="{{ $item['parent_url'] }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								@include('themes.partials.frontend_menu_icon', ['iconUrl' => $item['icon_url'], 'iconHtml' => $item['icon_html'], 'className' => 'ph-fe-parent-icon'])
								<span>{{ $item['parent_name'] }}</span>
							</a>

							<div class="dropdown-menu ph-fe-dropdown-menu ph-fe-mega-menu ph-fe-mega-{{ $dropdownConfig['mega_layout'] }} ph-fe-dropdown-align-{{ $dropdownBox['align'] }} {{ $arrowClass }}" style="--ph-fe-mega-width: {{ (int) $dropdownBox['width'] }}px; --ph-fe-mega-columns: {{ (int) $megaBox['columns'] }}; --ph-fe-dropdown-arrow-size: {{ (int) $dropdownBox['arrow_size'] }}px;">
								@if ($dropdownConfig['mega_layout'] == 'featured' && count($visibleSubmenus) > 0)
									@php
										$featuredIndex = min(count($visibleSubmenus) - 1, max(0, (int) $megaBox['featured_index']));
										$featuredItem = $visibleSubmenus[$featuredIndex];
									@endphp

									<a class="ph-fe-featured-menu-item" href="{{ $featuredItem['submenu_url'] }}">
										@if ($megaBox['show_images'])
											<span class="ph-fe-featured-media">
												@include('themes.partials.frontend_menu_icon', ['iconUrl' => $featuredItem['icon_url'], 'iconHtml' => $featuredItem['icon_html'], 'className' => 'ph-fe-featured-icon'])
											</span>
										@endif

										<span class="ph-fe-featured-body">
											<span class="ph-fe-submenu-title ph-fe-text-{{ $megaBox['title_position'] }}">{{ $featuredItem['submenu_name'] }}</span>

											@if ($megaBox['show_description'])
												<span class="ph-fe-submenu-desc ph-fe-text-{{ $megaBox['description_position'] }}">{{ $featuredItem['submenu_description'] ?: $featuredItem['submenu_link'] }}</span>
											@endif
										</span>
									</a>
								@endif

								<div class="ph-fe-mega-grid">
									@foreach ($visibleSubmenus as $submenu)
										<a class="ph-fe-mega-item ph-fe-image-{{ $megaBox['image_position'] }}" href="{{ $submenu['submenu_url'] }}">
											@if ($megaBox['show_images'])
												<span class="ph-fe-mega-media">
													@include('themes.partials.frontend_menu_icon', ['iconUrl' => $submenu['icon_url'], 'iconHtml' => $submenu['icon_html'], 'className' => 'ph-fe-submenu-icon'])
												</span>
											@endif

											<span class="ph-fe-mega-body">
												@if ($dropdownConfig['mega_layout'] == 'category_grid' && $item['category_name'] !== '')
													<span class="ph-fe-category-label">{{ $item['category_name'] }}</span>
												@endif

												<span class="ph-fe-submenu-title ph-fe-text-{{ $megaBox['title_position'] }}">{{ $submenu['submenu_name'] }}</span>

												@if ($megaBox['show_description'])
													<span class="ph-fe-submenu-desc ph-fe-text-{{ $megaBox['description_position'] }}">{{ $submenu['submenu_description'] ?: $submenu['submenu_link'] }}</span>
												@endif
											</span>
										</a>
									@endforeach
								</div>
							</div>
						</li>
					@else
						<li class="nav-item ph-fe-menu-item">
							<a class="nav-link ph-fe-link" href="{{ $item['parent_url'] }}">
								@include('themes.partials.frontend_menu_icon', ['iconUrl' => $item['icon_url'], 'iconHtml' => $item['icon_html'], 'className' => 'ph-fe-parent-icon'])
								<span>{{ $item['parent_name'] }}</span>
							</a>
						</li>
					@endif
				@endforeach
			</ul>
		</div>
	</div>
</header>
