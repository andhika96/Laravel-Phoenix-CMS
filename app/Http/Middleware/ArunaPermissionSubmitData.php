<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Sub_Menu_JSON;
use App\Models\Awesome_Admin\Parent_Menu_JSON;
use App\Models\Awesome_Admin\Category_Menu_JSON;
use App\Models\Awesome_Admin\Custom_Permissions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ArunaPermissionSubmitData
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next, string $permission): Response
	{
		// dd($permission);

		$userAccount 		= new Account();
		$getPreviousURI 	= trim(url()->previousPath(), '/');
		$getCurrentURI 		= Route::current()->uri();
		$getCurrentRole 	= $userAccount->getCurrentUserRoles()[0];
		$getEachCurrentURI	= explode("/", $getCurrentURI);

		if (site_config()->management_menu == 'v1')
		{
			$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

			foreach ($parentMenu as $key0 => $value0) 
			{
				$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

				if (is_array($parentMenuVars_decode) && count($parentMenuVars_decode) > 0)
				{
					foreach ($parentMenuVars_decode as $key1 => $value1) 
					{
						if ($value1['is_for_parent_menu'] == 'single' && $value1['parent_link'] == $getCurrentURI)
						{
							$checkAccessUserRoles = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

							if (is_array($checkAccessUserRoles) && count($checkAccessUserRoles) > 0)
							{
								if (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission) == false)
								{
									if ($request->wantsJson()) 
									{
										return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
									}
									else
									{
										return abort(403);
									}
								}
							}
							else
							{
								return abort(403);
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

									if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
									{
										foreach ($subMenuVars_decode as $key3 => $value3) 
										{
											// if ($value3['submenu_link'] == $getCurrentURI)
											if (Str::contains($getCurrentURI, $value3['submenu_link']) == true)
											{
												$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

												if (is_array($checkAccessUserRoles2) && count($checkAccessUserRoles2) > 0)
												{
													// getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);
												
													if (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission) == false)
													{
														if ($request->wantsJson()) 
														{
															return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
														}
														else
														{
															return abort(403);
														}
													}
												}
												else
												{
													return abort(403);
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
		elseif (site_config()->management_menu == 'v2')
		{
			// dd($getCurrentURI);

			/*
			 * First if we cannot find permission in the current uri in database
			 * We find permission in the previous uri / url in database
			 * This code work for submitting data when the URL POST is different from url page form
			 */

			// dd($getPreviousURI);

			$customPermissions = Custom_Permissions::where('role_id', $userAccount->getCurrentUserRoleById())->where('menu_link', $getCurrentURI)->first();

			if ($customPermissions)
			{
				$customPermissionVars_decode = json_decode($customPermissions['permissions'], true);

				if (is_array($customPermissionVars_decode) && count($customPermissionVars_decode) > 0)
				{
					foreach ($customPermissionVars_decode as $key => $value) 
					{
						$output[$value] = $value;
					}
				}

				if ( ! isset($output[$permission]))
				{
					if ($request->wantsJson()) 
					{
						return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
					}
					else
					{
						return abort(403);
					}
				}
			}
			else
			{
				$customPermissionsWithPreviousURI = Custom_Permissions::where('role_id', $userAccount->getCurrentUserRoleById())->where('menu_link', $getPreviousURI)->first();

				if ($customPermissionsWithPreviousURI)
				{
					$customPermissionsWithPreviousURIVars_decode = json_decode($customPermissionsWithPreviousURI['permissions'], true);

					if (is_array($customPermissionsWithPreviousURIVars_decode) && count($customPermissionsWithPreviousURIVars_decode) > 0)
					{
						foreach ($customPermissionsWithPreviousURIVars_decode as $key => $value) 
						{
							$output[$value] = $value;
						}
					}

					if ( ! isset($output[$permission]))
					{
						if ($request->wantsJson()) 
						{
							return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
						}
						else
						{
							return abort(403);
						}
					}					
				}
			}

			// Disable for a while - 07082025
			// else
			// {
			// 	$custom_permissions2 = Custom_Permissions::where('role_id', $userAccount->getCurrentUserRoleById())->where('menu_link', $getPreviousURI)->get();		

			// 	foreach ($custom_permissions2 as $key0 => $value0) 
			// 	{
			// 		$json_decode = json_decode($value0['permissions'], true);

			// 		if (is_array($json_decode))
			// 		{
			// 			foreach ($json_decode as $key1 => $value1) 
			// 			{
			// 				$output[$value1] = $value1;
			// 			}
			// 		}
			// 	}

			// 	if ( ! isset($output[$permission]))
			// 	{
			// 		if ($request->wantsJson()) 
			// 		{
			// 			return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
			// 		}
			// 		else
			// 		{
			// 			return abort(403);
			// 		}
			// 	}
			// }
		}
		
		// $this->checkDynamicUserAccessV1($permission);

		return $next($request);
	}

	public function checkDynamicUserAccessV1(string $permission)
	{
		$request = new Request();		
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
							// getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);

							if (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission) == false)
							{
								// return abort(403);

								if ($request->wantsJson()) 
								{
									return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
								}
								else
								{
									return abort(403);
								}
							}
						}
						else
						{
							return abort(403);
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
										// if ($value3['submenu_link'] == $getCurrentURI)
										if (Str::contains($getCurrentURI, $value3['submenu_link']) == true)
										{
											$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

											if (count($checkAccessUserRoles2) > 0)
											{
												// getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission);
											
												if (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission) == false)
												{
													if ($request->wantsJson()) 
													{
														return response()->json(['success' => false, 'status' => 'failed', 'message' => t('You do not have permission to do this')]);
													}
													else
													{
														return abort(403);
													}
												}
											}
											else
											{
												return abort(403);                                         
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

	public function checkDynamicUserAccessV2(string $permission)
	{
		$user 		= new Account();
		$currentURI = Route::current()->uri();
		$getEachURI = explode("/", $currentURI);

		// $custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('menu_link', $getEachURI[0])->get();
		
		if ($getEachURI[0] == 'settings')
		{
			$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('menu_link', $getEachURI[0].'/'.$getEachURI[1])->get();
		}
		else
		{
			$custom_permissions = Custom_Permissions::where('role_id', $user->getCurrentUserRoleById())->where('menu_link', $getEachURI[0])->get();		
		}

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

			if (isset($output[$permission]))
			{
				return true;
			}
		}

		if ($user->isAdmin())
		{
			return true;
		}
		else
		{
			abort(403, 'Forbidden');
		}
	}
}
