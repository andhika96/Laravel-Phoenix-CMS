<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Awesome_Admin\SubmitRoleRequest;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Sub_Menu_JSON;
use App\Models\Awesome_Admin\Parent_Menu_JSON;
use App\Models\Awesome_Admin\Custom_Permissions;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Awesome_Admin_Role_Controller extends Controller
{
	public function __construct(Account $user)
	{
		if ( ! $user->isAdmin())
		{
			abort(403);
		}
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		if (site_config()->management_menu == 'v1')
		{
			return view('awesome_admin.awesome_admin_role');
		}
		elseif (site_config()->management_menu == 'v2')
		{
			return view('awesome_admin.awesome_admin_role_v2');
		}
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function createV2()
	{
		if (site_config()->management_menu == 'v2')
		{
			return view('awesome_admin.awesome_admin_role_create_v2', ['menus' => get_all_menus(), 'permissions' => $this->listDataPermissionV2()]);
		}

		abort(403);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(SubmitRoleRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			if ($request->validated())
			{
				$permissions = explode(",", $request->input('permissions'));

				$role = Role::create(['name' => $request->input('role_name')]);
				$role->givePermissionTo($permissions);

				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Data successfully created')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully created'));
				}
			}   
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()
				
				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	public function storeV2(SubmitRoleRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	
		
		try
		{
			$create_role = Role::create(['name' => $request->input('role_name')]);

			if ($create_role)
			{
				if ($request->input('menu_vars_custom'))
				{
					foreach ($request->input('menu_vars_custom') as $key0 => $value0) 
					{
						if (isset($value0['parent_type']) && $value0['parent_type'] == 'single')
						{
							$new_data['category_code']	= $value0['category_code'];
							$new_data['parent_code']	= $value0['parent_code'];
							$new_data['menu_type']		= $value0['parent_type'];
							$new_data['menu_name']		= $value0['parent_name'];
							$new_data['menu_link']		= $value0['parent_link'];
							$new_data['permissions']	= isset($request->input('menu_vars')[$key0]['parent_permissions']) ? json_encode($request->input('menu_vars')[$key0]['parent_permissions']) : null;

							// Custom_Permissions::create($new_data);
							Custom_Permissions::updateOrCreate(['role_id' => $create_role->id, 'menu_code' => $value0['parent_code']], $new_data);
						}
						elseif (isset($value0['parent_type']) && $value0['parent_type'] == 'parent')
						{
							foreach ($value0['submenu_list'] as $key1 => $value1) 
							{
								$new_data['category_code']	= $value1['category_code'];
								$new_data['parent_code']	= $value1['parent_code'];
								$new_data['menu_type']		= $value1['submenu_type'];
								$new_data['menu_name']		= $value1['submenu_name'];
								$new_data['menu_link']		= $value1['submenu_link'];
								$new_data['permissions']	= isset($request->input('menu_vars')[$key0]['parent_submenu']['list'][$key1][$value1['submenu_code']]['submenu_permissions']) ? json_encode($request->input('menu_vars')[$key0]['parent_submenu']['list'][$key1][$value1['submenu_code']]['submenu_permissions']) : null;
							
								// Custom_Permissions::create($new_data);
								// Custom_Permissions::updateOrCreate(['role_id' => $create_role->id, 'parent_code' => isset($value0['parent_code']) ? $value0['parent_code'] : '', 'menu_code' => $value1['submenu_code']], $new_data);
								Custom_Permissions::updateOrCreate(['role_id' => $create_role->id, 'menu_code' => $value1['submenu_code']], $new_data);
							}
						}
					}

					DB::commit();
				}
			
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'redirect_url'	=> url('awesome_admin/roles'),
						'message'		=> t('Data successfully created')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully created'));
				}
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()
				
				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Display the specified resource.
	 */
	public function show()
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit()
	{
		//
	}

	public function editV2(string $idOrSlug)
	{
		if (site_config()->management_menu == 'v2')
		{
			$role = Role::find($idOrSlug);

			if ($role)
			{
				return view('awesome_admin.awesome_admin_role_edit_v2', ['role' => $role, 'menus' => get_all_menus(), 'permissions' => $this->listDataPermissionV2()]);
			}

			abort(404);
		}

		abort(403);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(SubmitRoleRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			if ($request->validated())
			{
				$role = Role::find($request->route('idOrSlug'));

				if ($role)
				{
					$permissions = explode(",", $request->input('permissions'));

					$role->name = $request->input('role_name');
					$role->syncPermissions($permissions);
					$role->save(); 

					DB::commit();

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('Data successfully updated')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('Data successfully updated'));
					}
				}
				else
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> false,
							'status'		=> 'failed',
							'message'		=> t('Data not found')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('failed', t('Data not found'));
					}

					// DB Rollback to get data back if fail to saving
					DB::rollBack();
				}				
			}  
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()
				
				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	public function updateV2(SubmitRoleRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			if ($request->validated())
			{
				$role = Role::find($request->route('idOrSlug'));

				if ($role)
				{				
					if ($request->input('menu_vars_custom'))
					{
						foreach ($request->input('menu_vars_custom') as $key0 => $value0) 
						{
							if (isset($value0['parent_type']) && $value0['parent_type'] == 'single')
							{
								$new_data['category_code']	= $value0['category_code'];
								$new_data['parent_code']	= $value0['parent_code'];								
								$new_data['menu_type']		= $value0['parent_type'];
								$new_data['menu_name']		= $value0['parent_name'];
								$new_data['menu_link']		= $value0['parent_link'];
								$new_data['permissions']	= isset($request->input('menu_vars')[$key0]['parent_permissions']) ? json_encode($request->input('menu_vars')[$key0]['parent_permissions']) : null;

								// Custom_Permissions::create($new_data);
								Custom_Permissions::updateOrCreate(['role_id' => $role->id, 'menu_code' => $value0['parent_code']], $new_data);
							}
							elseif (isset($value0['parent_type']) && $value0['parent_type'] == 'parent')
							{
								if (isset($value0['submenu_list']) && is_array($value0['submenu_list']) && count($value0['submenu_list']) > 0)
								{
									foreach ($value0['submenu_list'] as $key1 => $value1) 
									{
										$new_data['category_code']	= $value1['category_code'];
										$new_data['parent_code']	= $value1['parent_code'];
										$new_data['menu_type']		= $value1['submenu_type'];
										$new_data['menu_name']		= $value1['submenu_name'];
										$new_data['menu_link']		= $value1['submenu_link'];
										$new_data['permissions']	= isset($request->input('menu_vars')[$key0]['parent_submenu']['list'][$key1][$value1['submenu_code']]['submenu_permissions']) ? json_encode($request->input('menu_vars')[$key0]['parent_submenu']['list'][$key1][$value1['submenu_code']]['submenu_permissions']) : null;
									
										// Custom_Permissions::create($new_data);
										Custom_Permissions::updateOrCreate(['role_id' => $role->id, 'menu_code' => $value1['submenu_code']], $new_data);
									}
								}
							}
						}

						// Save new role name
						$role->name = $request->input('role_name');
						$role->save(); 

						// DB Commit if data successfully saved
						DB::commit();
					}
				
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'redirect_url'	=> url('awesome_admin/roles'),
							'message'		=> t('Data successfully updated')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('Data successfully updated'));
					}
				}
				else
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> false,
							'status'		=> 'failed',
							'message'		=> t('Data not found')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('failed', t('Data not found'));
					}

					// DB Rollback to get data back if fail to saving
					DB::rollBack();
				}
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			$role = Role::find($request->route('idOrSlug'));

			if ($role)
			{
				$role->delete(); 

				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Data successfully deleted')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully deleted'));
				}
			}
			else
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> false,
						'status'		=> 'failed',
						'message'		=> t('Data not found')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('failed', t('Data not found'));
				}

				// DB Rollback to get data back if fail to saving
				DB::rollBack();
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()
				
				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroyV2(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			$role = Role::find($request->route('idOrSlug'));

			if ($role)
			{
				$custom_permissions = Custom_Permissions::where('role_id', $request->route('idOrSlug'))->get();

				if ($custom_permissions)
				{
					$custom_permissions->each->delete();

					$role->delete(); 
				}

				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Data successfully deleted')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully deleted'));
				}
			}
			else
			{
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> false,
						'status'		=> 'failed',
						'message'		=> t('Data not found')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('failed', t('Data not found'));
				}

				// DB Rollback to get data back if fail to saving
				DB::rollBack();
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> false,
					'status' 	=> 'failed',
					'message'	=> $th->getMessage()
				
				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->with('failed', $th->getMessage());
			}

			DB::rollBack();
		} 
		finally 
		{
			return $response;
		}
	}

	public function listData()
	{
		$roles = Roles::get();

		$output = [];

		foreach ($roles as $key => $value) 
		{
			$accountCount = Account::with('roles')->get()->filter(fn ($user) => $user->roles->where('name', $value['name'])->toArray())->count();

			$output[$key]['id'] = $value['id'];
			$output[$key]['name'] = $value['name'];
			$output[$key]['guard_name'] = $value['guard_name'];
			$output[$key]['total_account'] = number_format($accountCount, 0, ".", ",");
			$output[$key]['updated_at'] = $value['updated_at'];
			$output[$key]['created_at'] = $value['created_at'];
		}

		if (count($roles) > 0)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $output]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}

	public function listDataPermission()
	{
		$roles = Permission::get();

		if (count($roles) > 0)
		{
			foreach ($roles as $key => $value) 
			{
				$new_output[$key] = $value['name'];
			}

			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $new_output]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}

	public function listDataPermissionV2()
	{
		$permission_allowed = ['read data', 'add data', 'edit data', 'delete data'];

		$permisions = Permission::get();

		foreach ($permisions as $key => $value) 
		{
			/*
			if (in_array($value['name'], $permission_allowed))
			{
				$new_output[$key] = $value['name'];
			}
			*/

			$new_output[$key] = $value['name'];
		}

		// $newArray = array_merge(array_splice($new_output, -1), $new_output);

		return $new_output;
		// return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $new_output]);
	}

	public function listDataCustomPermissions(string $idOrSlug, string $menuCode)
	{	
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

			return $output['view data'];
		}

		return null;
	}

	public function detailData($role_id)
	{
		$role = Role::find($role_id);

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

			foreach ($role->toArray() as $key => $value) 
			{
				$new_output[$key] = $value;
			}

			$new_output['permissions'] = $permissions;

			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $new_output]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}
}
