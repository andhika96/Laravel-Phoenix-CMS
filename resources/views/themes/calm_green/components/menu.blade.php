@php
if ( ! function_exists('menu_v1'))
{
	function menu_v1()
	{
		$output = '';

		// Menu list with category
		if (isset(get_menus_with_category_v1()['categorized']) &&
			count(get_menus_with_category_v1()['categorized']) > 0)
		{
			foreach (get_menus_with_category_v1()['categorized'] as $key0 => $value0) 
			{
				if (isset($value0['parent_menu']))
				{
					$output .= '
					<div class="arv7-title row">
						<div class="col-auto fw-bold">
							'.$value0['category_name'].'
						</div>

						<div class="col ps-0">
							<hr class="navbar-vertical-divider mb-0">
						</div>
					</div>

					<hr class="arv7-divider-line-category">

					<ul class="list-group ph-list-group-menu list-group-flush">';

					if (isset($value0['parent_menu']))
					{
						foreach ($value0['parent_menu'] as $key1 => $value1) 
						{
							$is_with_icon = '';
							$is_with_icon_margin = '';

							if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
							{
								if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
								{
									$is_with_icon = 'ph-list-group-item-with-icon';
									$is_with_icon_margin = 'me-2';

									$parent_menu_icon = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
								}
								else
								{
									$parent_menu_icon = '<div class="arv7-initial-menu-name bg-light text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
								}
							}
							elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
							{
								if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
								{
									$is_with_icon = 'ph-list-group-item-with-icon';
									$is_with_icon_margin = 'me-2';

									$parent_menu_icon = $value1['parent_icon_custom'];
								}
								else
								{
									$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
								}
							}
							else
							{
								$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
							}

							if ($value1['is_for_parent_menu'] == 'parent')
							{
								if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
								{
									$output .= '
									<a href="javascript:void(0)" class="list-group-item list-group-item-action ph-list-group-item '.$is_with_icon.' ph-list-group-item-collapse d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
										<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
											'.$parent_menu_icon.'
										</span>

										<span class="arv7-parent-menu-name text-truncate" style="width: calc(100% - 55px)">
											'.$value1['parent_name'].'
										</span>
									</a>

									<div class="multi-collapse collapse position-relative" id="Collapse'.$value1['parent_code'].'">
										<div class="ph-list-group-subitem-container">
											<div class="list-group ph-list-group-submenu list-group-flush py-2">
												<a class="list-group-item ph-list-group-subitem ph-list-group-item-title list-group-item-action d-flex align-items-center">
													<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$value1['parent_name'].'</span>
												</a>';

											foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
											{
												$submenu_icon = '<i class="fad fa-circle-notch arv7-default-icon-menu fa-fw me-2"></i>';

												if (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'upload_file')
												{
													if (isset($value1['submenu_icon_path']) && $value1['submenu_icon_path'] !== '')
													{
														$submenu_icon = '<img src="'.url(getImageURL($value1['submenu_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
													}
												}
												elseif (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'custom_input')
												{
													if (isset($value1['submenu_icon_custom']) && $value1['submenu_icon_custom'] !== '')
													{
														$is_submenu_icon_exist = 'ph-list-group-item-with-icon';
														$submenu_icon = $value1['submenu_icon_custom'];
													}
												}

												$output .= '
												<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center" target="_blank">
													<span class="arv7-submenu-icon d-inline-flex justify-content-center align-self-center">
														'.$submenu_icon.'
													</span>

													<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">
														'.$value1['submenu_name'].'
													</span>
												</a>';
											}
											
										$output .= '
											</div>
										</div>
									</div>';
								}
								else
								{
									$output .= '
									<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
										<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
											'.$parent_menu_icon.'
										</span>

										<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
											'.$value1['parent_name'].'
										</span>
									</a>

									<div class="multi-collapse collapse position-relative">									
										<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
											<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
												<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">'.$value1['parent_name'].'</span>
											</a>
										</div>
									</div>';
								}
							}
							elseif ($value1['is_for_parent_menu'] == 'single')
							{
								$output .= '
								<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
									<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
										'.$parent_menu_icon.'
									</span>

									<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
										'.$value1['parent_name'].'
									</span>
								</a>

								<div class="multi-collapse collapse position-relative">									
									<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
										<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
											<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">
												'.$value1['parent_name'].'
											</span>
										</a>
									</div>
								</div>';
							}
						}
					}
					else
					{
						$output .= '
							<a class="list-group-item list-group-item-action text-danger">
								'.t('No Menu').'
							</a>';			
					}

					$output .= '</ul>';
				}
			}
		}
		
		// Menu list without category
		if (isset(get_menus_with_category_v1()['uncategorized']) &&
			count(get_menus_with_category_v1()['uncategorized']) > 0)
		{
			$output .= '
			<div class="arv7-title row">
				<div class="col-auto fw-bold">
					'.t('Uncategorized').'
				</div>

				<div class="col ps-0">
					<hr class="navbar-vertical-divider mb-0">
				</div>
			</div>

			<hr class="arv7-divider-line-category">

			<ul class="list-group ph-list-group-menu list-group-flush position-relative">';

			foreach (get_menus_with_category_v1()['uncategorized'] as $key1 => $value1) 
			{
				$is_with_icon = '';
				$is_with_icon_margin = '';

				if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
				{
					if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
					{
						$is_with_icon = 'ph-list-group-item-with-icon';
						$is_with_icon_margin = 'me-2';

						$parent_menu_icon = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
					}
					else
					{
						$parent_menu_icon = '<div class="arv7-initial-menu-name bg-light text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
					}
				}
				elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
				{
					if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
					{
						$is_with_icon = 'ph-list-group-item-with-icon';
						$is_with_icon_margin = 'me-2';

						$parent_menu_icon = $value1['parent_icon_custom'];
					}
					else
					{
						$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
					}
				}
				else
				{
					$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
				}

				if ($value1['is_for_parent_menu'] == 'parent')
				{
					if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
					{
						$output .= '
						<a href="javascript:void(0)" class="list-group-item list-group-item-action ph-list-group-item '.$is_with_icon.' ph-list-group-item-collapse d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
							<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
								'.$parent_menu_icon.'
							</span>

							<span class="arv7-parent-menu-name text-truncate" style="width: calc(100% - 55px)">
								'.$value1['parent_name'].'
							</span>
						</a>

						<div class="multi-collapse collapse position-relative" id="Collapse'.$value1['parent_code'].'">
							<div class="ph-list-group-subitem-container">
								<div class="list-group ph-list-group-submenu list-group-flush py-2">
									<a class="list-group-item ph-list-group-subitem ph-list-group-item-title list-group-item-action d-flex align-items-center">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$value1['parent_name'].'</span>
									</a>';

								foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
								{
									$submenu_icon = '<i class="fad fa-circle-notch arv7-default-icon-menu fa-fw me-2"></i>';

									if (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'upload_file')
									{
										if (isset($value1['submenu_icon_path']) && $value1['submenu_icon_path'] !== '')
										{
											$submenu_icon = '<img src="'.url(getImageURL($value1['submenu_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
										}
									}
									elseif (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'custom_input')
									{
										if (isset($value1['submenu_icon_custom']) && $value1['submenu_icon_custom'] !== '')
										{
											$submenu_icon = $value1['submenu_icon_custom'];
										}
									}

									$output .= '
									<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center" target="_blank">
										<span class="arv7-submenu-icon d-inline-flex justify-content-center align-self-center">
											'.$submenu_icon.'
										</span>

										<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">
											'.$value1['submenu_name'].'
										</span>
									</a>';
								}
								
							$output .= '
								</div>
							</div>
						</div>';
					}
					else
					{
						$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
							<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
								'.$parent_menu_icon.'
							</span>

							<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
								'.$value1['parent_name'].'
							</span>
						</a>

						<div class="multi-collapse collapse position-relative">									
							<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
								<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
									<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">'.$value1['parent_name'].'</span>
								</a>
							</div>
						</div>';
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'single')
				{
					$output .= '
					<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
						<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
							'.$parent_menu_icon.'
						</span>

						<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
							'.$value1['parent_name'].'
						</span>
					</a>

					<div class="multi-collapse collapse position-relative">									
						<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
							<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
								<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">
									'.$value1['parent_name'].'
								</span>
							</a>
						</div>
					</div>';
				}
				else
				{
					$output .= '
						<a class="list-group-item list-group-item-action text-danger">
							'.t('No Menu').'
						</a>';			
				}
			}

			$output .= '</ul>';
		}

		return $output;
	}
}

if ( ! function_exists('menu_v2'))
{
	function menu_v2()
	{
		$output = '';

		// Menu list with category
		if (isset(get_menus_with_category_v2()['categorized']) &&
			count(get_menus_with_category_v2()['categorized']) > 0)
		{
			foreach (get_menus_with_category_v2()['categorized'] as $key0 => $value0) 
			{
				if (isset($value0['parent_menu']))
				{
					$output .= '
					<div class="arv7-title row">
						<div class="col-auto fw-bold">
							'.$value0['category_name'].'
						</div>

						<div class="col ps-0">
							<hr class="navbar-vertical-divider mb-0">
						</div>
					</div>

					<hr class="arv7-divider-line-category">

					<ul class="list-group ph-list-group-menu list-group-flush">';

					if (isset($value0['parent_menu']))
					{
						foreach ($value0['parent_menu'] as $key1 => $value1) 
						{
							$is_with_icon = '';
							$is_with_icon_margin = '';

							if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
							{
								if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
								{
									$is_with_icon = 'ph-list-group-item-with-icon';
									$is_with_icon_margin = 'me-2';

									$parent_menu_icon = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
								}
								else
								{
									$parent_menu_icon = '<div class="arv7-initial-menu-name bg-light text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
								}
							}
							elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
							{
								if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
								{
									$is_with_icon = 'ph-list-group-item-with-icon';
									$is_with_icon_margin = 'me-2';

									$parent_menu_icon = $value1['parent_icon_custom'];
								}
								else
								{
									$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
								}
							}
							else
							{
								$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
							}

							if ($value1['is_for_parent_menu'] == 'parent')
							{
								if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
								{
									$output .= '
									<a href="javascript:void(0)" class="list-group-item list-group-item-action ph-list-group-item '.$is_with_icon.' ph-list-group-item-collapse d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
										<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
											'.$parent_menu_icon.'
										</span>

										<span class="arv7-parent-menu-name text-truncate" style="width: calc(100% - 55px)">
											'.$value1['parent_name'].'
										</span>
									</a>

									<div class="multi-collapse collapse position-relative" id="Collapse'.$value1['parent_code'].'">
										<div class="ph-list-group-subitem-container">
											<div class="list-group ph-list-group-submenu list-group-flush py-2">
												<a class="list-group-item ph-list-group-subitem ph-list-group-item-title list-group-item-action d-flex align-items-center">
													<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$value1['parent_name'].'</span>
												</a>';

											foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
											{
												$submenu_icon = '<i class="fad fa-circle-notch arv7-default-icon-menu fa-fw me-2"></i>';

												if (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'upload_file')
												{
													if (isset($value1['submenu_icon_path']) && $value1['submenu_icon_path'] !== '')
													{
														$submenu_icon = '<img src="'.url(getImageURL($value1['submenu_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
													}
												}
												elseif (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'custom_input')
												{
													if (isset($value1['submenu_icon_custom']) && $value1['submenu_icon_custom'] !== '')
													{
														$is_submenu_icon_exist = 'ph-list-group-item-with-icon';
														$submenu_icon = $value1['submenu_icon_custom'];
													}
												}

												$output .= '
												<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center" target="_blank">
													<span class="arv7-submenu-icon d-inline-flex justify-content-center align-self-center">
														'.$submenu_icon.'
													</span>

													<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">
														'.$value1['submenu_name'].'
													</span>
												</a>';
											}
											
										$output .= '
											</div>
										</div>
									</div>';
								}
								else
								{
									$output .= '
									<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
										<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
											'.$parent_menu_icon.'
										</span>

										<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
											'.$value1['parent_name'].'
										</span>
									</a>

									<div class="multi-collapse collapse position-relative">									
										<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
											<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
												<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">'.$value1['parent_name'].'</span>
											</a>
										</div>
									</div>';
								}
							}
							elseif ($value1['is_for_parent_menu'] == 'single')
							{
								$output .= '
								<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
									<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
										'.$parent_menu_icon.'
									</span>

									<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
										'.$value1['parent_name'].'
									</span>
								</a>

								<div class="multi-collapse collapse position-relative">									
									<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
										<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
											<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">
												'.$value1['parent_name'].'
											</span>
										</a>
									</div>
								</div>';
							}
						}
					}
					else
					{
						$output .= '
							<a class="list-group-item list-group-item-action text-danger">
								'.t('No Menu').'
							</a>';			
					}

					$output .= '</ul>';
				}
			}
		}
		
		// Menu list without category
		if (isset(get_menus_with_category_v2()['uncategorized']) &&
			count(get_menus_with_category_v2()['uncategorized']) > 0)
		{
			$output .= '
			<div class="arv7-title row">
				<div class="col-auto fw-bold">
					'.t('Uncategorized').'
				</div>

				<div class="col ps-0">
					<hr class="navbar-vertical-divider mb-0">
				</div>
			</div>

			<hr class="arv7-divider-line-category">

			<ul class="list-group ph-list-group-menu list-group-flush position-relative">';

			foreach (get_menus_with_category_v2()['uncategorized'] as $key1 => $value1) 
			{
				$is_with_icon = '';
				$is_with_icon_margin = '';

				if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
				{
					if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
					{
						$is_with_icon = 'ph-list-group-item-with-icon';
						$is_with_icon_margin = 'me-2';

						$parent_menu_icon = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
					}
					else
					{
						$parent_menu_icon = '<div class="arv7-initial-menu-name bg-light text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
					}
				}
				elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
				{
					if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
					{
						$is_with_icon = 'ph-list-group-item-with-icon';
						$is_with_icon_margin = 'me-2';

						$parent_menu_icon = $value1['parent_icon_custom'];
					}
					else
					{
						$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
					}
				}
				else
				{
					$parent_menu_icon = '<div class="arv7-initial-menu-name text-center fw-bold">'.mb_substr($value1['parent_name'], 0, 1).'</div>';
				}

				if ($value1['is_for_parent_menu'] == 'parent')
				{
					if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
					{
						$output .= '
						<a href="javascript:void(0)" class="list-group-item list-group-item-action ph-list-group-item '.$is_with_icon.' ph-list-group-item-collapse d-flex align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
							<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
								'.$parent_menu_icon.'
							</span>

							<span class="arv7-parent-menu-name text-truncate" style="width: calc(100% - 55px)">
								'.$value1['parent_name'].'
							</span>
						</a>

						<div class="multi-collapse collapse position-relative" id="Collapse'.$value1['parent_code'].'">
							<div class="ph-list-group-subitem-container">
								<div class="list-group ph-list-group-submenu list-group-flush py-2">
									<a class="list-group-item ph-list-group-subitem ph-list-group-item-title list-group-item-action d-flex align-items-center">
										<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$value1['parent_name'].'</span>
									</a>';

								foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
								{
									$submenu_icon = '<i class="fad fa-circle-notch arv7-default-icon-menu fa-fw me-2"></i>';

									if (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'upload_file')
									{
										if (isset($value1['submenu_icon_path']) && $value1['submenu_icon_path'] !== '')
										{
											$submenu_icon = '<img src="'.url(getImageURL($value1['submenu_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
										}
									}
									elseif (isset($value1['submenu_icon_type']) && $value1['submenu_icon_type'] == 'custom_input')
									{
										if (isset($value1['submenu_icon_custom']) && $value1['submenu_icon_custom'] !== '')
										{
											$submenu_icon = $value1['submenu_icon_custom'];
										}
									}

									$output .= '
									<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center" target="_blank">
										<span class="arv7-submenu-icon d-inline-flex justify-content-center align-self-center">
											'.$submenu_icon.'
										</span>

										<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">
											'.$value1['submenu_name'].'
										</span>
									</a>';
								}
								
							$output .= '
								</div>
							</div>
						</div>';
					}
					else
					{
						$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
							<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
								'.$parent_menu_icon.'
							</span>

							<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
								'.$value1['parent_name'].'
							</span>
						</a>

						<div class="multi-collapse collapse position-relative">									
							<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
								<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
									<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">'.$value1['parent_name'].'</span>
								</a>
							</div>
						</div>';
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'single')
				{
					$output .= '
					<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item '.$is_with_icon.' list-group-item-action d-flex align-items-center">
						<span class="arv7-parent-menu-icon d-inline-flex justify-content-center align-self-center '.$is_with_icon_margin.'">
							'.$parent_menu_icon.'
						</span>

						<span class="arv7-parent-menu-name text-truncate d-inline-block" style="width: calc(100% - 25px)">
							'.$value1['parent_name'].'
						</span>
					</a>

					<div class="multi-collapse collapse position-relative">									
						<div class="list-group ph-list-group-submenu-single list-group-flush py-2">
							<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action d-flex align-items-center mb-0">
								<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)">
									'.$value1['parent_name'].'
								</span>
							</a>
						</div>
					</div>';
				}
				else
				{
					$output .= '
						<a class="list-group-item list-group-item-action text-danger">
							'.t('No Menu').'
						</a>';			
				}
			}

			$output .= '</ul>';
		}

		return $output;
	}
}

if ( ! function_exists('menu_versioning'))
{
	function menu_versioning()
	{
		if (site_config()->management_menu == 'v1')
		{
			return menu_v1();
		}
		elseif (site_config()->management_menu == 'v2')
		{
			return menu_v2();
		}

		return false;
	}
}
@endphp