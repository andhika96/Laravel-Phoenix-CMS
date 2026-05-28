<?php

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Status;
use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Language;
use App\Models\Awesome_Admin\Site_Config;
use App\Models\Awesome_Admin\SMTP_Service;
use App\Models\Awesome_Admin\Sub_Menu_JSON;
use App\Models\Awesome_Admin\Parent_Menu_JSON;
use App\Models\Awesome_Admin\Category_Menu_JSON;
use App\Models\Awesome_Admin\Custom_Permissions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/*
 * Translate
 * You can translate language by using this function per word
 *
 * Use: {{ t('Hello World!') }}
 *
 * Attribute arguments is optional
 *
 * return String
 */

function t($str, $att1 = '', $att2 = '', $att3 = '', $att4 = '')
{
	$dbstr = addslashes($str);

	$res = Language::where('lang_from', $dbstr)->where('lang', current_language())->limit(1)->first();

	if ( ! $res)
	{
		$insert_lang = 
		[
			'lang_from'	=> $dbstr,
			'lang_to'	=> '',
			'lang'		=> current_language()
		];

		$response = Language::create($insert_lang);
	}
	else
	{
		if (strlen($res->lang_to)) 
		{
			$str = $res->lang_to;
		}
		else 
		{
			$str = $res->lang_from;
		}
	}

	return str_replace(['{1}', '{2}', '{3}', '{4}'], [$att1, $att2, $att3, $att4], $str);
}

/*
 * Site Config
 * Get setting site config by using this function
 * 
 * Use: {{ site_config()->site_name }}
 *
 * return Array
 */

function site_config()
{
	$site_config = Site_Config::find(1);

	return $site_config;
}

/*
 * SMTP Service
 * Get main setting SMTP Service 
 *
 * Use: {{ smtp_service()->smtp_service }}
 *
 * return Array
 */

function smtp_service()
{
	$smtp_service = SMTP_Service::with(['smtp_setting'])->find(1);

	return $smtp_service->smtp_setting;
}

/*
 * Current Languange
 * Get current language user by using this function
 *
 * return String
 */

function current_language()
{
	if (request()->cookie('phoenix_language'))
	{
		$language = request()->cookie('phoenix_language');
	}
	else
	{
		$language = 'english';
	}

	return $language;
}

function get_all_menus()
{
	$output 	= [];
	$user 		= new Account();
	$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	$i = 0;
	foreach ($parentMenu as $key0 => $value0) 
	{
		$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			foreach ($parentMenuVars_decode as $key1 => $value1) 
			{
				$output[$i]['index'] 				= $i;
				$output[$i]['is_for_parent_menu'] 	= $value1['is_for_parent_menu'];
				$output[$i]['category_code'] 		= $value1['category_code'];
				$output[$i]['parent_code'] 			= $value1['parent_code'];
				$output[$i]['parent_icon'] 			= $value1['parent_icon'];
				$output[$i]['parent_name'] 			= $value1['parent_name'];
				$output[$i]['parent_link'] 			= $value1['parent_link'];
				$output[$i]['parent_type'] 			= $value1['parent_type'];
				$output[$i]['parent_roles'] 		= $value1['parent_roles'];
				$output[$i]['parent_permissions'] 	= $value1['parent_permissions'];

				if (is_array($value1['parent_permissions']))
				{
					foreach ($value1['parent_permissions'] as $key_parent_permissions0 => $value_parent_permissions0) 
					{
						$output[$i]['parent_permissions_alt'][$value_parent_permissions0] = $value_parent_permissions0;
					}
				}

				$output[$i]['parent_icon_type'] 	= $value1['parent_icon_type'];
				$output[$i]['parent_icon_path'] 	= $value1['parent_icon_path'];
				$output[$i]['parent_icon_url'] 		= $value1['parent_icon_url'];
				$output[$i]['parent_icon_custom'] 	= $value1['parent_icon_custom'];

				if ($value1['is_for_parent_menu'] == 'parent')
				{
					$subMenu = Sub_Menu_JSON::where('parent_code', $output[$i]['parent_code'])->limit(1)->get();

					/*
					if ($subMenu)
					{
						// $output_submenu = $subMenu;

						// $output[$i]['parent_submenu']['list']	= $output_submenu;	

						// $i2 = 0;	

						foreach ($subMenu as $key2 => $value2) 
						{
							$subMenuVars_decode = json_decode($value2['menu_vars'], true);

							// $output[$i]['parent_submenu']['list'] = $subMenuVars_decode;

							foreach ($subMenuVars_decode as $key3 => $value3) 
							{
								$output_submenu[$output[$i]['parent_code']][$key3] = $value3;
							}

							// foreach ($subMenuVars_decode as $key_submenu0 => $value_submenu0) 
							// {
							// 	$output_submenu[$key_submenu0] = $value_submenu0;							
							// }

							$output[$i]['parent_submenu']['list'] = $output_submenu[$output[$i]['parent_code']];
						}

						// $output[$i]['parent_submenu']['list'] = $output_submenu;
									
					}
					*/

					if ($subMenu)
					{
						foreach ($subMenu as $key2 => $value2) 
						{
							$i2 = 0;

							$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);
							
							foreach ($subMenuVars_decode as $key_submenu0 => $value_submenu0) 
							{
								$output_submenu[$output[$i]['parent_code']][$i2]['index'] 				= $i2;
								$output_submenu[$output[$i]['parent_code']][$i2]['parent_code'] 		= $output[$i]['parent_code'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_code'] 		= $value_submenu0['submenu_code'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon'] 		= $value_submenu0['submenu_icon'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_name'] 		= $value_submenu0['submenu_name'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_link'] 		= $value_submenu0['submenu_link'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_type'] 		= $value_submenu0['submenu_type'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_roles'] 		= $value_submenu0['submenu_roles'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_url'] 	= $value_submenu0['submenu_icon_url'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_path'] 	= $value_submenu0['submenu_icon_path'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_type'] 	= $value_submenu0['submenu_icon_type'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_icon_custom'] = $value_submenu0['submenu_icon_custom'];
								$output_submenu[$output[$i]['parent_code']][$i2]['submenu_permissions'] = $value_submenu0['submenu_permissions'];
								
								if (is_array($value_submenu0['submenu_permissions']))
								{
									foreach ($value_submenu0['submenu_permissions'] as $key_submenu_permissions0 => $value_submenu_permissions0) 
									{
										$output_submenu[$output[$i]['parent_code']][$i2]['submenu_permissions_alt'][$value_submenu_permissions0] = $value_submenu_permissions0;
									}
								}

								$i2++;
							}

							$output[$i]['parent_submenu']['code']	= $value2['parent_code'];
							$output[$i]['parent_submenu']['name']	= $value2['parent_name'];
							$output[$i]['parent_submenu']['list']	= $output_submenu[$output[$i]['parent_code']];
						}
					}
				}
			
				$i++;
			}
		}
	}

	return $output;
}

/*
 * Get Menus
 * Get all menu for user based on their roles
 *
 * return String
 */

function get_menus()
{
	$output 	= [];
	$user 		= new Account();
	$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	$i = 0;
	foreach ($parentMenu as $key0 => $value0) 
	{
		$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			foreach ($parentMenuVars_decode as $key1 => $value1) 
			{
				$checkAccessUserRoles = array_intersect($user->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

				if (count($checkAccessUserRoles) > 0)
				{
					$output[$i]['is_for_parent_menu'] 	= $value1['is_for_parent_menu'];
					$output[$i]['parent_code'] 			= $value1['parent_code'];
					$output[$i]['parent_name'] 			= $value1['parent_name'];
					$output[$i]['parent_link'] 			= $value1['parent_link'];
					$output[$i]['parent_type'] 			= $value1['parent_type'];
					$output[$i]['parent_roles'] 		= $value1['parent_roles'];
					$output[$i]['parent_icon_type'] 	= $value1['parent_icon_type'];
					$output[$i]['parent_icon_path'] 	= $value1['parent_icon_path'];
					$output[$i]['parent_icon_custom'] 	= $value1['parent_icon_custom'];

					if ($value1['is_for_parent_menu'] == 'parent')
					{
						$subMenu = Sub_Menu_JSON::where('parent_code', $value1['parent_code'])->get();

						if ($subMenu)
						{
							foreach ($subMenu as $key2 => $value2) 
							{
								$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

								$output[$i]['parent_submenu']['code']	= $value2['parent_code'];
								$output[$i]['parent_submenu']['name']	= $value2['parent_name'];
								$output[$i]['parent_submenu']['list']	= $subMenuVars_decode;
							}
						}
					}
				
					$i++;
				}
			}
		}
	}

	return $output;
}

function get_menus_with_category()
{
	$output 			= [];
	$categoryOutput 	= [];
	$user 			= new Account();
	$categoryMenu 	= Category_Menu_JSON::where('menu_page', 'awesome_admin')->get();
	$parentMenu 	= Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	$i = 0;
	foreach ($categoryMenu as $key0 => $value0) 
	{
		$categoryMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($categoryMenuVars_decode) > 0)
		{
			foreach ($categoryMenuVars_decode as $key1 => $value1) 
			{				
				$categoryOutput[$i]['category_code'] 	= $value1['category_code'];
				$categoryOutput[$i]['category_name'] 	= $value1['category_name'];
				$categoryOutput[$i]['category_roles'] 	= $value1['category_roles'];

				foreach ($parentMenu as $key2 => $value2) 
				{
					$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

					if (count($parentMenuVars_decode) > 0)
					{
						$i2 = 0;
						foreach ($parentMenuVars_decode as $key3 => $value3) 
						{
							$checkAccessUserRoles2 = array_intersect($user->getCurrentUserRoles()->toArray(), $value3['parent_roles']);

							if (count($checkAccessUserRoles2) > 0)
							{
								if ($value3['category_code'] == $value1['category_code'])
								{
									// $categoryOutput[$i]['parent_menu'][$key3] = $value3;

									$categoryOutput[$i]['parent_menu'][$i2]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_code'] 			= $value3['parent_code'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_name'] 			= $value3['parent_name'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_link'] 			= $value3['parent_link'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_type'] 			= $value3['parent_type'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_roles'] 		= $value3['parent_roles'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_icon_type'] 	= $value3['parent_icon_type'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_icon_path'] 	= $value3['parent_icon_path'];
									$categoryOutput[$i]['parent_menu'][$i2]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
								
									if ($value3['is_for_parent_menu'] == 'parent')
									{
										$subMenu = Sub_Menu_JSON::where('parent_code', $value3['parent_code'])->get();

										if ($subMenu)
										{
											foreach ($subMenu as $key4 => $value4) 
											{
												$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

												$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['code']	= $value4['parent_code'];
												$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['name']	= $value4['parent_name'];
												$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['list']	= $subMenuVars_decode;
											}
										}
									}

									$i2++;
								}
							}
						}
					}
				}

				$i++;
			}
		}
	}

	return $categoryOutput;
}

function get_menus_with_category_new_rc()
{
	$output 			= [];
	$categoryOutput 	= [];
	$user 			= new Account();
	$categoryMenu 	= Category_Menu_JSON::where('menu_page', 'awesome_admin')->get();
	$parentMenu 	= Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	$i = 0;
	foreach ($categoryMenu as $key0 => $value0) 
	{
		$categoryMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($categoryMenuVars_decode) > 0)
		{
			foreach ($categoryMenuVars_decode as $key1 => $value1) 
			{			
				if (checkTotalAccessViewMenuPermissionForMenuCategoryV1($value1['category_code']))
				{	
					$categoryOutput[$i]['category_code'] 	= $value1['category_code'];
					$categoryOutput[$i]['category_name'] 	= $value1['category_name'];
					$categoryOutput[$i]['category_roles'] 	= $value1['category_roles'];

					foreach ($parentMenu as $key2 => $value2) 
					{
						$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

						if (count($parentMenuVars_decode) > 0)
						{
							$i2 = 0;

							foreach ($parentMenuVars_decode as $key3 => $value3) 
							{
								if ($value3['category_code'] == $value1['category_code'] && checkUserViewMenuAccessPermissionForMenuV1($value3['parent_roles']) == true)
								{
									if (checkTotalAccessViewMenuPermissionForMenuV1($value3['parent_code'], $value3['is_for_parent_menu']) > 0)
									{
										$categoryOutput[$i]['parent_menu'][$i2]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_code'] 			= $value3['parent_code'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_name'] 			= $value3['parent_name'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_link'] 			= $value3['parent_link'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_type'] 			= $value3['parent_type'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_roles'] 		= $value3['parent_roles'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_icon_type'] 	= $value3['parent_icon_type'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_icon_path'] 	= $value3['parent_icon_path'];
										$categoryOutput[$i]['parent_menu'][$i2]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
									
										if ($value3['is_for_parent_menu'] == 'parent')
										{
											$subMenu = Sub_Menu_JSON::where('parent_code', $value3['parent_code'])->get();

											if ($subMenu)
											{
												foreach ($subMenu as $key4 => $value4) 
												{
													$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

													$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['code']	= $value4['parent_code'];
													$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['name']	= $value4['parent_name'];
													
													if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
													{
														$i3 = 0;

														foreach ($subMenuVars_decode as $key5 => $value5) 
														{
															if (checkUserViewMenuAccessPermissionForMenuV1($value5['submenu_roles']) == true)
															{
																$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
																$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
																$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
																$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
																$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
																$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
																$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
															
																$i3++;
															}
														}
													}

													if (isset($submenu[$value4['parent_code']]))
													{
														$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
													}
													else
													{
														$categoryOutput[$i]['parent_menu'][$i2]['parent_submenu']['list'] = [];
													}
												}
											}
										}

										$i2++;
									}
								}
							}
						}
					}

					$i++;
				}
			}
		}
	}

	return $categoryOutput;
}

function get_menus_with_category_new_rcV2()
{
	$output 			= [];
	$categoryOutput 	= [];
	$user 			= new Account();
	$categoryMenu 	= Category_Menu_JSON::where('menu_page', 'awesome_admin')->get();
	$parentMenu 	= Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	// Menu list with category
	$i11 = 0;
	foreach ($categoryMenu as $key0 => $value0) 
	{
		$categoryMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($categoryMenuVars_decode) > 0)
		{
			foreach ($categoryMenuVars_decode as $key1 => $value1) 
			{			
				if (checkTotalAccessViewMenuPermissionForMenuCategoryV1($value1['category_code']))
				{	
					$categoryOutput['categorized'][$i11]['category_code'] 	= $value1['category_code'];
					$categoryOutput['categorized'][$i11]['category_name'] 	= $value1['category_name'];
					$categoryOutput['categorized'][$i11]['category_roles'] 	= $value1['category_roles'];

					foreach ($parentMenu as $key2 => $value2) 
					{
						$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

						if (count($parentMenuVars_decode) > 0)
						{
							$i2 = 0;

							foreach ($parentMenuVars_decode as $key3 => $value3) 
							{
								if ($value3['category_code'] == $value1['category_code'] && checkUserViewMenuAccessPermissionForMenuV1($value3['parent_roles']) == true)
								{
									if (checkTotalAccessViewMenuPermissionForMenuV1($value3['parent_code'], $value3['is_for_parent_menu']) > 0)
									{
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_code'] 			= $value3['parent_code'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_name'] 			= $value3['parent_name'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_link'] 			= $value3['parent_link'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_type'] 			= $value3['parent_type'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_roles'] 		= $value3['parent_roles'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_type'] 	= $value3['parent_icon_type'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_path'] 	= $value3['parent_icon_path'];
										$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
									
										if ($value3['is_for_parent_menu'] == 'parent')
										{
											$subMenu = Sub_Menu_JSON::where('parent_code', $value3['parent_code'])->get();

											if ($subMenu)
											{
												foreach ($subMenu as $key4 => $value4) 
												{
													$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

													$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['code']	= $value4['parent_code'];
													$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['name']	= $value4['parent_name'];
													
													if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
													{
														$i3 = 0;

														foreach ($subMenuVars_decode as $key5 => $value5) 
														{
															if (checkUserViewMenuAccessPermissionForMenuV1($value5['submenu_roles']) == true)
															{
																$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
																$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
																$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
																$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
																$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
																$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
																$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
																$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
															
																$i3++;
															}
														}
													}

													if (isset($submenu[$value4['parent_code']]))
													{
														$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
													}
													else
													{
														$categoryOutput['categorized'][$i11]['parent_menu'][$i2]['parent_submenu']['list'] = [];
													}
												}
											}
										}

										$i2++;
									}
								}
							}
						}
					}

					$i11++;
				}
			}
		}
	}

	// Menu list without category
	$i12 = 0;
	foreach ($parentMenu as $key2 => $value2) 
	{
		$parentMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			$i2 = 0;

			foreach ($parentMenuVars_decode as $key3 => $value3) 
			{
				if ($value3['category_code'] == '' && checkUserViewMenuAccessPermissionForMenuV1($value3['parent_roles']) == true)
				{
					if (checkTotalAccessViewMenuPermissionForMenuV1($value3['parent_code'], $value3['is_for_parent_menu']) > 0)
					{
						$categoryOutput['uncategorized'][$i12]['is_for_parent_menu'] 	= $value3['is_for_parent_menu'];
						$categoryOutput['uncategorized'][$i12]['parent_code'] 			= $value3['parent_code'];
						$categoryOutput['uncategorized'][$i12]['parent_name'] 			= $value3['parent_name'];
						$categoryOutput['uncategorized'][$i12]['parent_link'] 			= $value3['parent_link'];
						$categoryOutput['uncategorized'][$i12]['parent_type'] 			= $value3['parent_type'];
						$categoryOutput['uncategorized'][$i12]['parent_roles'] 			= $value3['parent_roles'];
						$categoryOutput['uncategorized'][$i12]['parent_icon_type'] 		= $value3['parent_icon_type'];
						$categoryOutput['uncategorized'][$i12]['parent_icon_path'] 		= $value3['parent_icon_path'];
						$categoryOutput['uncategorized'][$i12]['parent_icon_custom'] 	= $value3['parent_icon_custom'];
					
						if ($value3['is_for_parent_menu'] == 'parent')
						{
							$subMenu = Sub_Menu_JSON::where('parent_code', $value3['parent_code'])->get();

							if ($subMenu)
							{
								foreach ($subMenu as $key4 => $value4) 
								{
									$subMenuVars_decode = json_decode($value4['menu_vars'], TRUE);

									$categoryOutput['uncategorized'][$i12]['parent_submenu']['code']	= $value4['parent_code'];
									$categoryOutput['uncategorized'][$i12]['parent_submenu']['name']	= $value4['parent_name'];
									
									if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
									{
										$i3 = 0;

										foreach ($subMenuVars_decode as $key5 => $value5) 
										{
											if (checkUserViewMenuAccessPermissionForMenuV1($value5['submenu_roles']) == true)
											{
												$submenu[$value4['parent_code']][$i3]['parent_code']			= $value4['parent_code'];
												$submenu[$value4['parent_code']][$i3]['submenu_code']			= $value5['submenu_code'];
												$submenu[$value4['parent_code']][$i3]['submenu_icon']			= $value5['submenu_icon'];
												$submenu[$value4['parent_code']][$i3]['submenu_link']			= $value5['submenu_link'];
												$submenu[$value4['parent_code']][$i3]['submenu_name']			= $value5['submenu_name'];
												$submenu[$value4['parent_code']][$i3]['submenu_type']			= $value5['submenu_type'];
												$submenu[$value4['parent_code']][$i3]['submenu_roles']			= $value5['submenu_roles'];
												$submenu[$value4['parent_code']][$i3]['submenu_icon_url']		= $value5['submenu_icon_url'];
												$submenu[$value4['parent_code']][$i3]['submenu_icon_path']		= $value5['submenu_icon_path'];
												$submenu[$value4['parent_code']][$i3]['submenu_icon_type']		= $value5['submenu_icon_type'];
												$submenu[$value4['parent_code']][$i3]['submenu_icon_custom']	= $value5['submenu_icon_custom'];
												$submenu[$value4['parent_code']][$i3]['submenu_permissions']	= $value5['submenu_permissions'];
											
												$i3++;
											}
										}
									}

									if (isset($submenu[$value4['parent_code']]))
									{
										$categoryOutput['uncategorized'][$i12]['parent_submenu']['list'] = $submenu[$value4['parent_code']];
									}
									else
									{
										$categoryOutput['uncategorized'][$i12]['parent_submenu']['list'] = [];
									}
								}
							}
						}

						$i2++;
					}

					$i12++;
				}


			}
		}
	}

	return $categoryOutput;
}

/*
 * Get Menus
 * Get all menu for user based on their roles
 *
 * return String
 */

function menu_for_user()
{
	$output = '<ul class="list-group list-group-flush">';

	foreach (get_menus() as $key0 => $value0) 
	{
		if (isset($value0['parent_icon_type']) && $value0['parent_icon_type'] == 'upload_file')
		{
			if (isset($value0['parent_icon_path']) && $value0['parent_icon_path'] !== '')
			{
				$parent_icon_menu = '<img src="'.url(getImageURL($value0['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
			}
			else
			{
				$parent_icon_menu = '';
			}
		}
		elseif (isset($value0['parent_icon_type']) && $value0['parent_icon_type'] == 'custom_input')
		{
			if (isset($value0['parent_icon_custom']) && $value0['parent_icon_custom'] !== '')
			{
				$parent_icon_menu = $value0['parent_icon_custom'];
			}
			else
			{
				$parent_icon_menu = '';
			}
		}
		else
		{
			$parent_icon_menu = '';
		}

		if ($value0['is_for_parent_menu'] == 'parent')
		{
			if (isset($value0['parent_submenu']) && is_array($value0['parent_submenu']['list']) && count($value0['parent_submenu']['list']) > 0)
			{
				$output .= '
				<a href="javascript:void(0)" class="list-group-item list-group-item-action collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value0['parent_code'].'" role="button" aria-expanded="false" aria-controls="Collapse'.$value0['parent_code'].'">
					<span class="text-truncate">'.$parent_icon_menu.' '.$value0['parent_name'].'</span>
				</a>

				<div class="multi-collapse list-group-sub collapse" id="Collapse'.$value0['parent_code'].'">
					<div class="list-group list-group-flush">';

					foreach ($value0['parent_submenu']['list'] as $key1 => $value1) 
					{
						$output .= '
						<a href="'.url($value1['submenu_link']).'" class="list-group-item list-group-item-action ps-5">
							'.$value1['submenu_name'].'
						</a>';
					}
					
				$output .= '
					</div>
				</div>';
			}
			else
			{
				$output .= '
				<a href="'.url($value0['parent_link']).'" class="list-group-item list-group-item-action" target="_blank">
					'.$parent_icon_menu.' '.$value0['parent_name'].'
				</a>';
			}
		}
		elseif ($value0['is_for_parent_menu'] == 'single')
		{
			$output .= '
				<a href="'.url($value0['parent_link']).'" class="list-group-item list-group-item-action" target="_blank">
					'.$parent_icon_menu.' '.$value0['parent_name'].'
				</a>';
		}
	}

	$output .= '</ul>';

	return $output;
}

function menu_for_user_with_category()
{
	$output = '';

	foreach (get_menus_with_category() as $key0 => $value0) 
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

			<ul class="list-group list-group-flush">';

			if (isset($value0['parent_menu']))
			{
				foreach ($value0['parent_menu'] as $key1 => $value1) 
				{
					if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
					{
						if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
						{
							$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
						}
						else
						{
							$parent_icon_menu = '';
						}
					}
					elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
					{
						if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
						{
							$parent_icon_menu = $value1['parent_icon_custom'];
						}
						else
						{
							$parent_icon_menu = '';
						}
					}
					else
					{
						$parent_icon_menu = '';
					}

					if ($value1['is_for_parent_menu'] == 'parent')
					{
						if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
						{
							$output .= '
							<a href="javascript:void(0)" class="list-group-item list-group-item-action collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
								<span class="text-truncate">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
							</a>

							<div class="multi-collapse list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
								<div class="list-group list-group-flush">';

								foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
								{
									$output .= '
									<a href="'.url($value1['submenu_link']).'" class="list-group-item list-group-item-action ps-5">
										'.$value1['submenu_name'].'
									</a>';
								}
								
							$output .= '
								</div>
							</div>';
						}
						else
						{
							$output .= '
							<a href="'.url($value1['parent_link']).'" class="list-group-item list-group-item-action" target="_blank">
								'.$parent_icon_menu.' '.$value1['parent_name'].'
							</a>';
						}
					}
					elseif ($value1['is_for_parent_menu'] == 'single')
					{
						$output .= '
							<a href="'.url($value1['parent_link']).'" class="list-group-item list-group-item-action" target="_blank">
								'.$parent_icon_menu.' '.$value1['parent_name'].'
							</a>';
					}
				}
			}
			else
			{
				$output .= '
					<a class="list-group-item list-group-item-action text-danger">
						No Menu
					</a>';			
			}

			$output .= '</ul>';
		}
	}

	return $output;
}

function menu_for_user_with_category_new_rc()
{
	$output = '';

	foreach (get_menus_with_category_new_rc() as $key0 => $value0) 
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

			<ul class="list-group list-group-flush">';

			if (isset($value0['parent_menu']))
			{
				foreach ($value0['parent_menu'] as $key1 => $value1) 
				{
					if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
					{
						if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
						{
							$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
						}
						else
						{
							$parent_icon_menu = '';
						}
					}
					elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
					{
						if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
						{
							$parent_icon_menu = $value1['parent_icon_custom'];
						}
						else
						{
							$parent_icon_menu = '';
						}
					}
					else
					{
						$parent_icon_menu = '';
					}

					if ($value1['is_for_parent_menu'] == 'parent')
					{
						if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
						{
							$output .= '
							<a href="javascript:void(0)" class="list-group-item list-group-item-action collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
								<span class="text-truncate">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
							</a>

							<div class="multi-collapse list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
								<div class="list-group list-group-flush">';

								foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
								{
									$output .= '
									<a href="'.url($value1['submenu_link']).'" class="list-group-item list-group-item-action ps-5">
										'.$value1['submenu_name'].'
									</a>';
								}
								
							$output .= '
								</div>
							</div>';
						}
						else
						{
							$output .= '
							<a href="'.url($value1['parent_link']).'" class="list-group-item list-group-item-action" target="_blank">
								'.$parent_icon_menu.' '.$value1['parent_name'].'
							</a>';
						}
					}
					elseif ($value1['is_for_parent_menu'] == 'single')
					{
						$output .= '
							<a href="'.url($value1['parent_link']).'" class="list-group-item list-group-item-action" target="_blank">
								'.$parent_icon_menu.' '.$value1['parent_name'].'
							</a>';
					}
				}
			}
			else
			{
				$output .= '
					<a class="list-group-item list-group-item-action text-danger">
						No Menu
					</a>';			
			}

			$output .= '</ul>';
		}
	}

	return $output;
}

function menu_for_user_with_category_new_rcV2()
{
	$output = '';

	// Menu list with category
	if (isset(get_menus_with_category_new_rcV2()['categorized']) &&
		count(get_menus_with_category_new_rcV2()['categorized']) > 0)
	{
		foreach (get_menus_with_category_new_rcV2()['categorized'] as $key0 => $value0) 
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

				<ul class="list-group ph-list-group-menu list-group-flush">';

				if (isset($value0['parent_menu']))
				{
					foreach ($value0['parent_menu'] as $key1 => $value1) 
					{
						if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
						{
							if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
							{
								$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
							}
							else
							{
								$parent_icon_menu = '';
							}
						}
						elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
						{
							if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
							{
								$parent_icon_menu = $value1['parent_icon_custom'];
							}
							else
							{
								$parent_icon_menu = '';
							}
						}
						else
						{
							$parent_icon_menu = '';
						}

						if ($value1['is_for_parent_menu'] == 'parent')
						{
							if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
							{
								$output .= '
								<a href="javascript:void(0)" class="list-group-item ph-list-group-item list-group-item-action collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
									<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
								</a>

								<div class="multi-collapse ph-list-group-submenu list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
									<div class="list-group list-group-flush">';

									foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
									{
										$output .= '
										<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action ps-5">
											<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'" style="width: calc(100% - 8px)">'.$value1['submenu_name'].'</span>
										</a>';
									}
									
								$output .= '
									</div>
								</div>';
							}
							else
							{
								$output .= '
								<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action" target="_blank">
									<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
								</a>';
							}
						}
						elseif ($value1['is_for_parent_menu'] == 'single')
						{
							$output .= '
								<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action" target="_blank">
									<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
								</a>';
						}
					}
				}
				else
				{
					$output .= '
						<a class="list-group-item list-group-item-action text-danger">
							No Menu
						</a>';			
				}

				$output .= '</ul>';
			}
		}
	}
	
	// Menu list without category
	if (isset(get_menus_with_category_new_rcV2()['uncategorized']) &&
		count(get_menus_with_category_new_rcV2()['uncategorized']) > 0)
	{
		$output .= '
		<div class="arv7-title row">
			<div class="col-auto fw-bold">
				Uncategorized
			</div>

			<div class="col ps-0">
				<hr class="navbar-vertical-divider mb-0">
			</div>
		</div>

		<ul class="list-group ph-list-group-menu list-group-flush">';

		// if (isset($value0['parent_menu']))
		// {
			// foreach ($value0['parent_menu'] as $key1 => $value1) 
			foreach (get_menus_with_category_new_rcV2()['uncategorized'] as $key1 => $value1) 
			{
				if (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'upload_file')
				{
					if (isset($value1['parent_icon_path']) && $value1['parent_icon_path'] !== '')
					{
						$parent_icon_menu = '<img src="'.url(getImageURL($value1['parent_icon_path'], 'icons/parent_menu')).'" class="img-fluid" style="width: 25px">';
					}
					else
					{
						$parent_icon_menu = '';
					}
				}
				elseif (isset($value1['parent_icon_type']) && $value1['parent_icon_type'] == 'custom_input')
				{
					if (isset($value1['parent_icon_custom']) && $value1['parent_icon_custom'] !== '')
					{
						$parent_icon_menu = $value1['parent_icon_custom'];
					}
					else
					{
						$parent_icon_menu = '';
					}
				}
				else
				{
					$parent_icon_menu = '';
				}

				if ($value1['is_for_parent_menu'] == 'parent')
				{
					if (isset($value1['parent_submenu']) && is_array($value1['parent_submenu']['list']) && count($value1['parent_submenu']['list']) > 0)
					{
						$output .= '
						<a href="javascript:void(0)" class="list-group-item ph-list-group-item list-group-item-action collapsed" data-bs-toggle="collapse" data-bs-target="#Collapse'.$value1['parent_code'].'" v-on:click="collapseMenu(\'Collapse'.$value1['parent_code'].'\')" role="button" aria-expanded="false" aria-controls="Collapse'.$value1['parent_code'].'">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>

						<div class="multi-collapse ph-list-group-submenu list-group-sub collapse" id="Collapse'.$value1['parent_code'].'">
							<div class="list-group list-group-flush">';

							foreach ($value1['parent_submenu']['list'] as $key1 => $value1) 
							{
								$output .= '
								<a href="'.url($value1['submenu_link']).'" class="list-group-item ph-list-group-subitem list-group-item-action" target="_blank">
									<span id="subMenuText_'.$value1['submenu_code'].'" class="text-truncate d-inline-block" style="width: calc(100% - 8px)" data-bs-offset="0, 33" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">'.$value1['submenu_name'].'</span>
								</a>';
							}
							
						$output .= '
							</div>
						</div>';
					}
					else
					{
						$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action" target="_blank">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['submenu_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>';
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'single')
				{
					$output .= '
						<a href="'.url($value1['parent_link']).'" class="list-group-item ph-list-group-item list-group-item-action" target="_blank">
							<span class="text-truncate d-inline-block" style="width: calc(100% - 25px)" data-bs-offset="0, 50" data-bs-placement="right" data-bs-title="'.$value1['parent_name'].'">'.$parent_icon_menu.' '.$value1['parent_name'].'</span>
						</a>';
				}
			}
		// }
		// else
		// {
		// 	$output .= '
		// 		<a class="list-group-item list-group-item-action text-danger">
		// 			No Menu
		// 		</a>';			
		// }

		$output .= '</ul>';
	}

	return $output;
}

/**
 * Create a "Random" String
 *
 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
 * @param	int	number of characters
 * @return	string
 */

function random_string($type = 'alnum', $len = 8)
{
	switch ($type)
	{
		case 'basic':
			return mt_rand();
		case 'alnum':
		case 'numeric':
		case 'nozero':
		case 'alpha':
			switch ($type)
			{
				case 'alpha':
					$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case 'alnum':
					$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case 'numeric':
					$pool = '0123456789';
					break;
				case 'nozero':
					$pool = '123456789';
					break;
			}
			return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
		case 'unique': // todo: remove in 3.1+
		case 'md5':
			return md5(uniqid(mt_rand()));
		case 'encrypt': // todo: remove in 3.1+
		case 'sha1':
			return sha1(uniqid(mt_rand(), TRUE));
	}
}

/**
 * Add's _1 to a string or increment the ending number to allow _2, _3, etc
 *
 * @param	string	required
 * @param	string	What should the duplicate number be appended with
 * @param	string	Which number should be used for the first dupe increment
 * @return	string
 */

function increment_string($str, $separator = '_', $first = 1)
{
	preg_match('/(.+)' . preg_quote($separator, '/') . '([0-9]+)$/', $str, $match);
	return isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $str . $separator . $first;
}

/*
 * Check User Page Access Roles
 * We check roles user is same as role page or not
 *
 * return Boolean
 */

function checkUserPageAccessRoles()
{
	$user 		= new Account();
	$currentURI = Route::current()->uri();
	$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	foreach ($parentMenu as $key0 => $value0) 
	{
		$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			foreach ($parentMenuVars_decode as $key1 => $value1) 
			{
				if ($value1['parent_link'] == $currentURI)
				{
					$checkAccessUserRoles = array_intersect($user->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

					if (count($checkAccessUserRoles) > 0)
					{
						return true;
					}

					abort(403);
				}
				/*
				else
				{
					$subMenu = Sub_Menu_JSON::where('parent_code', $value1['parent_code'])->get();

					if ($subMenu)
					{
						foreach ($subMenu as $key2 => $value2) 
						{
							$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

							if (count($subMenuVars_decode) > 0)
							{
								foreach ($subMenuVars_decode as $key3 => $value3) 
								{
									if ($value3['submenu_link'] == $currentURI)
									{
										$checkAccessUserRoles2 = array_intersect($user->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

										if (count($checkAccessUserRoles2) > 0)
										{
											return true;
										}

										abort(403);											
									}
								}
							}
						}
					}
				}
				*/
			}
		}
	}
}

// ------------------------ Menu Feature v1 Start ------------------ //

function checkUserViewPageAccessPermissionsV1()
{
	$userAccount = new Account();
	$getCurrentURI = Route::current()->uri();
	$getCurrentRole = $userAccount->getCurrentUserRoles()[0];

	$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	foreach ($parentMenu as $key0 => $value0) 
	{
		$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			foreach ($parentMenuVars_decode as $key1 => $value1) 
			{
				if ($value1['is_for_parent_menu'] == 'single' && $value1['parent_link'] == $getCurrentURI)
				{
					$checkAccessUserRoles = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

					if (count($checkAccessUserRoles) > 0)
					{
						getUserViewPageAccessPermissionsV1($getCurrentRole);
					}
					else
					{
						abort(403);
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'parent')
				{
					$subMenu = Sub_Menu_JSON::where('parent_code', $value1['parent_code'])->get();

					if ($subMenu)
					{
						foreach ($subMenu as $key2 => $value2) 
						{
							$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

							if (count($subMenuVars_decode) > 0)
							{
								foreach ($subMenuVars_decode as $key3 => $value3) 
								{
									if ($value3['submenu_link'] == $getCurrentURI)
									{
										$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

										if (count($checkAccessUserRoles2) > 0)
										{
											getUserViewPageAccessPermissionsV1($getCurrentRole);
										}
										else
										{
											abort(403);											
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function checkUserDynamicPageAccessPermissionsV1($permission)
{
	$userAccount = new Account();
	$getCurrentURI = Route::current()->uri();
	$getCurrentRole = $userAccount->getCurrentUserRoles()[0];

	$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	foreach ($parentMenu as $key0 => $value0) 
	{
		$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			foreach ($parentMenuVars_decode as $key1 => $value1) 
			{
				if ($value1['is_for_parent_menu'] == 'single' && $value1['parent_link'] == $getCurrentURI)
				{
					$checkAccessUserRoles = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

					if (count($checkAccessUserRoles) > 0)
					{
						getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);
					}
					else
					{
						abort(403);
					}
				}
				elseif ($value1['is_for_parent_menu'] == 'parent')
				{
					$subMenu = Sub_Menu_JSON::where('parent_code', $value1['parent_code'])->get();

					if ($subMenu)
					{
						foreach ($subMenu as $key2 => $value2) 
						{
							$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

							if (count($subMenuVars_decode) > 0)
							{
								foreach ($subMenuVars_decode as $key3 => $value3) 
								{
									if ($value3['submenu_link'] == $getCurrentURI)
									{
										$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

										if (count($checkAccessUserRoles2) > 0)
										{
											getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);
										}
										else
										{
											abort(403);											
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

function getUserViewPageAccessPermissionsV1($getCurrentRole)
{
	$output = [];
	$role = Role::findByName($getCurrentRole);

	if ($role !== null)
	{
		if (count($role->getAllPermissions()) !== 0)
		{
			foreach ($role->getAllPermissions() as $key => $value) 
			{
				$output[$value['name']] = $value['name'];
			}

			if (isset($output['read data']))
			{
				return true;
			}
		}
	}

	abort(403);
}

function getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission)
{
	$output = [];
	$role = Role::findByName($getCurrentRole);

	if ($role !== null)
	{
		if (count($role->getAllPermissions()) !== 0)
		{
			foreach ($role->getAllPermissions() as $key => $value) 
			{
				$output[$value['name']] = $value['name'];
			}

			if (isset($output[$permission]))
			{
				return true;
			}
		}
	}

	abort(403);
}

function checkTotalAccessViewMenuPermissionForMenuCategoryV1($category_code)
{
	$increment 	= 0;
	$user 		= new Account();
	$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

	$category_code_static = 'c3TCIKzEkquxl7Ltog2Iuw';

	foreach ($parentMenu as $key0 => $value0) 
	{
		$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

		if (count($parentMenuVars_decode) > 0)
		{
			foreach ($parentMenuVars_decode as $key1 => $value1) 
			{	
				if ($value1['category_code'] == $category_code && checkUserViewMenuAccessPermissionForMenuV1($value1['parent_roles']) == true)
				{
					if (checkTotalAccessViewMenuPermissionForMenuV1($value1['parent_code'], $value1['is_for_parent_menu']) > 0)
					{
						$increment += 1;
					}

					if ($value1['is_for_parent_menu'] == 'parent')
					{
						$subMenu = Sub_Menu_JSON::where('parent_code', $value1['parent_code'])->get();

						if ($subMenu)
						{		
							foreach ($subMenu as $key2 => $value2) 
							{
								$subMenuVars_decode = json_decode($value2['menu_vars'], TRUE);

								if (is_array($subMenuVars_decode))
								{
									foreach ($subMenuVars_decode as $key3 => $value3) 
									{
										if (checkUserViewMenuAccessPermissionForMenuV1($value3['submenu_roles']) == true)
										{
											$increment += 1;
										}
									}
								}
							}
						}
					}
				}
			}
		}		
	}

	return $increment;
}

function checkTotalAccessViewMenuPermissionForMenuV1($parent_code, $menu_type)
{
	$increment = 0;
	$user = new Account();

	$parent_code_static = 'FbdIsyyI8ndPZoH9AeEzlR';

	if ($menu_type == 'parent')
	{
		$subMenu = Sub_Menu_JSON::where('parent_code', $parent_code)->get();

		if ($subMenu)
		{		
			foreach ($subMenu as $key0 => $value0) 
			{
				$subMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

				if (is_array($subMenuVars_decode))
				{
					foreach ($subMenuVars_decode as $key1 => $value1) 
					{
						if (checkUserViewMenuAccessPermissionForMenuV1($value1['submenu_roles']) == true)
						{
							$increment += 1;
						}
					}
				}
			}
		}
	}
	elseif ($menu_type == 'single')
	{
		$increment += 1;
	}
	
	return $increment;
}

function checkUserViewMenuAccessPermissionForMenuV1($role_name)
{
	$user = new Account();
	$role_name_static = ['Administrator', 'Super Admin'];

	$checkAccessUserRolesInSubmenu = array_intersect($user->getCurrentUserRoles()->toArray(), $role_name);

	if (is_array($checkAccessUserRolesInSubmenu) && count($checkAccessUserRolesInSubmenu) > 0)
	{
		$role = Role::findByName($checkAccessUserRolesInSubmenu[0]);

		if ($role !== null)
		{
			if (count($role->getAllPermissions()) !== 0)
			{
				foreach ($role->getAllPermissions() as $key => $value) 
				{
					$permissions[$key] = $value['name'];
				}
			}
			else
			{
				$permissions = [];
			}

			if (is_array($permissions) && count($permissions) > 0)
			{
				foreach ($permissions as $key => $value) 
				{
					$output[$value] = $value;
				}

				if (isset($output['read data']))
				{
					return true;
				}
			}

			return false;

			// if ($user->isAdmin())
			// {
			// 	return true;
			// }
			// else
			// {
			// 	return false;
			// }
		}
	}
	else
	{
		return false;

		// if ($user->isAdmin())
		// {
		// 	return true;
		// }
		// else
		// {
		// 	return false;
		// }
	}
}

// ------------------------ Menu Feature v1 End ------------------ //

// ------------------------ Menu Feature v2 Start ------------------ //

// ------------------------ Menu Feature v2 End ------------------ //

function checkIsAdmin()
{
	$user = new Account();

	return $user->isAdmin();
}

function getStatusAccount($key)
{
	$getData = Account_Status::find($key);

	if ($getData)
	{
		return '<span class="'.$getData->class_name.'">'.$getData->name.'</span>';
	}

	return null;
}

function formatSizeUnits($bytes)
{
	if ($bytes < 1024) 
	{
		return $bytes.' B';
	} 
	elseif ($bytes < 1048576) 
	{
		return round($bytes / 1024, 2).' KB';
	}
	elseif ($bytes < 1073741824) 
	{
		return round($bytes / 1048576, 2).' MB';
	}
	elseif ($bytes < 1099511627776) 
	{
		return round($bytes / 1073741824, 2).' GB';
	}
	elseif ($bytes < 1125899906842624) 
	{
		return round($bytes / 1099511627776, 2).' TB';
	}
	elseif ($bytes < 1152921504606846976) 
	{
		return round($bytes / 1125899906842624, 2).' PB';
	}
	elseif ($bytes < 1180591620717411303424) 
	{
		return round($bytes / 1152921504606846976, 2).' EB';
	}
	elseif ($bytes < 1208925819614629174706176) 
	{
		return round($bytes / 1180591620717411303424, 2).' ZB';
	}
	else 
	{
		return round($bytes / 1208925819614629174706176, 2).' YB';
	}
}

function formatSizeUnitsOnlyNumber($bytes)
{
	if ($bytes >= 1024) // KB
	{
		$bytes = $bytes / 1024;
	}
	elseif ($bytes > 1) // Bytes
	{
		$bytes = $bytes;
	}
	elseif ($bytes == 1) // Byte
	{
		$bytes = $bytes;
	}
	else
	{
		$bytes = 0;
	}

	return $bytes;
}

function getImageURL($fileImage = '', $pathUrl = '')
{
	if ($pathUrl !== '')
	{
		if (Storage::disk('public')->exists($pathUrl.'/'.$fileImage))
		{
			return Storage::url($pathUrl.'/'.$fileImage);
		}
	}

	return null;
}

function getDataCustomPermissions(string $idOrSlug, string $menuCode)
{
	$output = [];

	$custom_permissions = Custom_Permissions::where('role_id', $idOrSlug)->where('menu_code', $menuCode)->get();

	if ($custom_permissions !== null)
	{
		foreach ($custom_permissions as $key0 => $value0) 
		{
			$json_decode = json_decode($value0['permissions'], true);

			if (is_array($json_decode))
			{
				foreach ($json_decode as $key1 => $value1) 
				{
					$output[$value1] = $value1;
				}
			}
		}

		return $output;
	}

	return false;
}

?>