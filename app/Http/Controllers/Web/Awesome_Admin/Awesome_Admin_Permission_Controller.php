<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Awesome_Admin\SubmitPermissionRequest;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Permissions;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Awesome_Admin_Permission_Controller extends Controller
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
		return view('awesome_admin.awesome_admin_permission');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(SubmitPermissionRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			if ($request->validated())
			{
				$role = Permission::create(['name' => $request->input('permission_name')]);

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

	/**
	 * Display the specified resource.
	 */
	public function show(Request $request)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Request $request)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(SubmitPermissionRequest $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			if ($request->validated())
			{
				$permission = Permission::find($request->route('idOrSlug'));

				if ($permission)
				{
					$permission->name = $request->input('permission_name');
					$permission->save(); 

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

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();	

		try
		{
			$permission = Permission::find($request->route('idOrSlug'));

			if ($permission !== null)
			{
				$permission->delete(); 

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
		$permission = Permissions::get();

		if (count($permission) > 0)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $permission]);
		}
	
		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}

	public function detailData($role_id)
	{
		$permission = Permissions::find($role_id);

		if ($permission !== null)
		{
			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $permission]);
		}

		return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
	}
}
