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

class ArunaPermission
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next, string $permission): Response
	{
		$userAccount 		= new Account();
		$getPreviousURI 	= trim(url()->previousPath(), '/');
		$getCurrentURI 		= Route::current()->uri();
		$getCurrentRole 	= $userAccount->getCurrentUserRoles()[0];
		$getEachCurrentURI	= explode("/", $getCurrentURI);

		// $customPermissionsWithPreviousURI2 = Custom_Permissions::where('role_id', $userAccount->getCurrentUserRoleById())->where('menu_link', $getPreviousURI)->first();

		// dd($permission);

		if (site_config()->management_menu == 'v1')
		{
			$parentMenu = Parent_Menu_JSON::where('menu_page', 'awesome_admin')->get();

			foreach ($parentMenu as $key0 => $value0) 
			{
				$parentMenuVars_decode = json_decode($value0['menu_vars'], TRUE);

				if (is_array($parentMenuVars_decode) && count($parentMenuVars_decode) > 0)
				{
					// if ($permission == strtolower('submit data'))
					if ($permission == strtolower('add data') || $permission == strtolower('edit data') || $permission == strtolower('delete data'))
					{
						// Get previous URL and create request
						$createRequestFromPreviousURL = Request::create(url()->previous());

						// Get list of middlware from previous URL
						$getMiddlewareFromRoute = Route::getRoutes()->match($createRequestFromPreviousURL)->action['middleware'];
						
						// Create regex code for getting permission middlware with the value
						$patternPermission = "/(\w+):(\w+\ \w+)/";

						foreach ($getMiddlewareFromRoute as $key => $value) 
						{
							// Gather permission data from middleware with regex
							if (preg_match($patternPermission, $value, $matches))
							{
								// Get value from permission middleware from route in previous url
								$newVariablePermissionFromPreviousRouteInURL = explode(':', $matches[0]);
							}
						}

						foreach ($parentMenuVars_decode as $key1 => $value1) 
						{
							if ($value1['is_for_parent_menu'] == 'single' && $value1['parent_link'] == $getPreviousURI)
							{
								$checkAccessUserRoles = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value1['parent_roles']);

								if (is_array($checkAccessUserRoles) && count($checkAccessUserRoles) > 0)
								{
									if (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission) == false)
									{	
										$isDenied = 1;				
									}
									elseif (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $newVariablePermissionFromPreviousRouteInURL[1]) == false)
									{
										$isDenied = 1;
									}
									else
									{
										$isDenied = 0;
									}

									if ($isDenied == 1)
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
												if (Str::contains($getPreviousURI, $value3['submenu_link']) == true)
												{
													$checkAccessUserRoles2 = array_intersect($userAccount->getCurrentUserRoles()->toArray(), $value3['submenu_roles']);

													if (is_array($checkAccessUserRoles2) && count($checkAccessUserRoles2) > 0)
													{
														if (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $permission) == false)
														{	
															$isDenied = 1;				
														}
														elseif (getUserDynamicPageAccessPermissionsV1($getCurrentRole, $newVariablePermissionFromPreviousRouteInURL[1]) == false)
														{
															$isDenied = 1;
														}
														else
														{
															$isDenied = 0;
														}
																						
														if ($isDenied == 1)
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
					else
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
		}
		elseif (site_config()->management_menu == 'v2')
		{
			/*
			 * Checking value permission is submit data or not
			 * if permission is submit data we process first to get previous url
			 * and extract the middleware to get value from the permission
			 * for form submit, if permission is not submit data we pass the first checking
			 */

			// if ($permission == strtolower('submit data') && $permission == strtolower('edit data'))
			if ($permission == 'add data' || $permission == 'edit data' || $permission == 'delete data')
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

					// Get previous URL and create request
					$createRequestFromPreviousURL = Request::create(url()->previous());

					// Get list of middlware from previous URL
					$getMiddlewareFromRoute = Route::getRoutes()->match($createRequestFromPreviousURL)->action['middleware'];
					
					// Create regex code for getting permission middlware with the value
					$patternPermission = "/(\w+):(\w+\ \w+)/";

					foreach ($getMiddlewareFromRoute as $key => $value) 
					{
						// Gather permission data from middleware with regex
						if (preg_match($patternPermission, $value, $matches))
						{
							// Get value from permission middleware from route in previous url
							$newVariablePermissionFromPreviousRouteInURL = explode(':', $matches[0]);
						}
					}

					if ( ! isset($output[$permission]))
					{	
						$isDenied = 1;				
					}
					elseif ( ! isset($output[$newVariablePermissionFromPreviousRouteInURL[1]]))
					{
						$isDenied = 1;
					}
					else
					{
						$isDenied = 0;
					}

					if ($isDenied == 1)
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
			else
			{
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
			}
		}
		
		return $next($request);
	}
}
