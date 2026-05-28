<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Permissions;
use App\Models\Awesome_Admin\Custom_Permissions;

use App\Models\Awesome_Admin\Sub_Menu_BE_JSON;
use App\Models\Awesome_Admin\Parent_Menu_BE_JSON;
use App\Models\Awesome_Admin\Category_Menu_BE_JSON;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Awesome_Admin\MenuRequest;
use App\Http\Requests\Awesome_Admin\AddMenuRequest;
use App\Http\Requests\Awesome_Admin\AddSubmenuRequest;

class Awesome_Admin_Menu_BE_Controller extends Controller
{
	protected $max_file_size = '3000'; // KB

	protected $mime_types = ['image/png', 'image/jpeg'];

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
		return view('awesome_admin.menu.backend.awesome_admin_menu_be');
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

	public function create_submenu(MenuRequest $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try
		{
			if ($request->validated())
			{
				$getParentName = $this->getParentName($request->input('parent_code'));

				$role = Sub_Menu_BE_JSON::create(['parent_code' => $request->input('parent_code'), 'parent_name' => $getParentName]);

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
	 * Store a newly created resource in storage.
	 */

	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 */

	public function show()
	{
		//
	}

	public function submenu()
	{
		return view('awesome_admin.menu.backend.awesome_admin_menu_submenu_be');
	}

	public function submenu_detail(string $idOrSlug)
	{
		$getDetailMenu = Sub_Menu_BE_JSON::find($idOrSlug);

		if ( ! $getDetailMenu)
		{			
			$getDetailMenu = Sub_Menu_BE_JSON::where('parent_code', $idOrSlug)->first();
		}

		if ($getDetailMenu)
		{
			return view('awesome_admin.menu.backend.awesome_admin_menu_submenu_detail_be', ['data' => $getDetailMenu, 'idOrSlug' => $idOrSlug]);
		}

		abort(404);
	}

	public function parentmenu()
	{
		$listCategoryMenu = json_decode($this->listDataCategoryMenuForParentMenu()->getContent(), true);

		if (isset($listCategoryMenu) && isset($listCategoryMenu['data']))
		{
			$getListCategoryMenu['data'] = $listCategoryMenu['data'];
		}
		else
		{
			$getListCategoryMenu['data'] = [];
		}

		// dd($listCategoryMenu['data']);

		return view('awesome_admin.menu.backend.awesome_admin_parent_menu_be', ['categorymenu' => $getListCategoryMenu]);
	}

	public function categorymenu()
	{
		return view('awesome_admin.menu.backend.awesome_admin_category_menu_be');
	}

	/**
	 * Show the form for editing the specified resource.
	 */

	public function edit()
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */

	public function update(Request $request)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */

	public function update_parentmenu(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			$getDetailMenuParentJSON 	= Parent_Menu_BE_JSON::find(1);
			$getJSONDecodeMenuVars 		= json_decode($request->input('menu_vars'), true);

			for ($i = 0; $i < count($getJSONDecodeMenuVars); $i++) 
			{
				if ( ! empty($_FILES['parent_icon_'.$i]['name']))
				{
					if (formatSizeUnitsOnlyNumber($request->file('parent_icon_'.$i)->getSize()) <= $this->max_file_size)
					{
						if (in_array($request->file('parent_icon_'.$i)->getMimeType(), $this->mime_types))
						{
							// Delete old image and replace with the above one
							if (Storage::disk('public')->exists('icons/parent_menu/'.$getJSONDecodeMenuVars[$i]['parent_icon_path']))
							{
								$this->deleteFile($getJSONDecodeMenuVars[$i]['parent_icon_path'], 'icons/parent_menu');
							}

							// Replace with new one
							$getJSONDecodeMenuVars[$i]['parent_icon_path'] = $this->upload_image_parentmenu($request->file(), $i);
						}
						else
						{
							throw new \Exception('Format file or mime type invalid');
						}
					}
					else
					{
						$size = Number::fileSize($this->max_file_size * 1024);

						throw new \Exception('File size is larger than allowed (Max '.$size.')');
					}
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['parent_icon_path']))
				{
					$getJSONDecodeMenuVars[$i]['parent_icon_path'] = '';
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['parent_code']) || $getJSONDecodeMenuVars[$i]['parent_code'] == '')
				{
					$getJSONDecodeMenuVars[$i]['parent_code'] = Str::random(22);
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['parent_icon_type']))
				{
					$getJSONDecodeMenuVars[$i]['parent_icon_type'] = '';
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['parent_icon_custom']))
				{
					$getJSONDecodeMenuVars[$i]['parent_icon_custom'] = str_replace('"', "'", $getJSONDecodeMenuVars[$i]['parent_icon_custom_default']);
				}

				if (isset($getJSONDecodeMenuVars[$i]['parent_icon_type']) &&
					$getJSONDecodeMenuVars[$i]['parent_icon_type'] == 'custom_input')
				{
					// Delete old image and replace with the above one
					if (Storage::disk('public')->exists('icons/parent_menu/'.$getJSONDecodeMenuVars[$i]['parent_icon_path']))
					{
						$this->deleteFile($getJSONDecodeMenuVars[$i]['parent_icon_path'], 'icons/parent_menu');
					}
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['parent_roles']))
				{
					$getJSONDecodeMenuVars[$i]['parent_roles'] = '';
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['parent_permissions']))
				{
					$getJSONDecodeMenuVars[$i]['parent_permissions'] = '';
				}

				$getValueCategoryCode = ($getJSONDecodeMenuVars[$i]['category_code'] !== '') ? $getJSONDecodeMenuVars[$i]['category_code'] : NULL;
				$customPermission = Custom_Permissions::where('parent_code', $getJSONDecodeMenuVars[$i]['parent_code'])->update(['category_code' => $getValueCategoryCode]);
			}

			$getDetailMenuParentJSON->fill(['menu_vars' => json_encode($getJSONDecodeMenuVars), 'menu_vars_backup' => $getDetailMenuParentJSON->menu_vars]);

			if ($getDetailMenuParentJSON->save())
			{
				// DB Commit if data successfully saved
				DB::commit();
			
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'   => true,
						'status'    => 'success',
						'message'   => t('Data successfully updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully updated'));
				}
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/**
	 * Update the specified resource in storage.
	 */

	public function update_submenu_detail(string $idOrSlug)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			$getDetailMenuParentJSON 	= Sub_Menu_BE_JSON::where('parent_code', $idOrSlug)->first();
			$getJSONDecodeMenuVars 		= json_decode(request()->input('menu_vars'), true);

			for ($i = 0; $i < count($getJSONDecodeMenuVars); $i++) 
			{
				if ( ! empty($_FILES['submenu_icon_'.$i]['name']))
				{
					if (formatSizeUnitsOnlyNumber(request()->file('submenu_icon_'.$i)->getSize()) <= $this->max_file_size)
					{
						if (in_array(request()->file('submenu_icon_'.$i)->getMimeType(), $this->mime_types))
						{
							// Delete old image and replace with the above one
							if (Storage::disk('public')->exists('icons/sub_menu/'.$getJSONDecodeMenuVars[$i]['submenu_icon_path']))
							{
								$this->deleteFile($getJSONDecodeMenuVars[$i]['submenu_icon_path'], 'icons/sub_menu');
							}

							// Replace with new one
							$getJSONDecodeMenuVars[$i]['submenu_icon_path'] = $this->upload_image_submenu(request()->file(), $i);
						}
						else
						{
							throw new \Exception('Format file or mime type invalid');
						}
					}
					else
					{
						$size = Number::fileSize($this->max_file_size * 1024);

						throw new \Exception('File size is larger than allowed (Max '.$size.')');
					}
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['submenu_code']) || $getJSONDecodeMenuVars[$i]['submenu_code'] == '')
				{
					$getJSONDecodeMenuVars[$i]['submenu_code'] = Str::random(22);
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['submenu_icon_path']))
				{
					$getJSONDecodeMenuVars[$i]['submenu_icon_path'] = '';
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['submenu_icon_type']))
				{
					$getJSONDecodeMenuVars[$i]['submenu_icon_type'] = '';
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['submenu_icon_custom']))
				{
					$getJSONDecodeMenuVars[$i]['submenu_icon_custom'] = str_replace('"', "'", $getJSONDecodeMenuVars[$i]['submenu_icon_custom_default']);
				}

				if (isset($getJSONDecodeMenuVars[$i]['submenu_icon_type']) &&
					$getJSONDecodeMenuVars[$i]['submenu_icon_type'] == 'custom_input')
				{
					// Delete old image and replace with the above one
					if (Storage::disk('public')->exists('icons/sub_menu/'.$getJSONDecodeMenuVars[$i]['submenu_icon_path']))
					{
						$this->deleteFile($getJSONDecodeMenuVars[$i]['submenu_icon_path'], 'icons/sub_menu');
					}
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['submenu_roles']))
				{
					$getJSONDecodeMenuVars[$i]['submenu_roles'] = '';
				}

				if ( ! isset($getJSONDecodeMenuVars[$i]['submenu_permissions']))
				{
					$getJSONDecodeMenuVars[$i]['submenu_permissions'] = '';
				}
			}

			$getDetailMenuParentJSON->fill(['menu_vars' => json_encode($getJSONDecodeMenuVars), 'menu_vars_backup' => $getDetailMenuParentJSON->menu_vars]);

			if ($getDetailMenuParentJSON->save())
			{
				// DB Commit if data successfully saved
				DB::commit();
			
				if (request()->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'   => true,
						'status'    => 'success',
						'message'   => t('Data successfully updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully updated'));
				}
			}
		}
		catch (\Throwable $th) 
		{
			if (request()->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/**
	 * Update the specified resource in storage.
	 */

	public function update_categorymenu(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			$getDetailMenuParentJSON 	= Category_Menu_BE_JSON::find(1);
			$getJSONDecodeMenuVars 		= json_decode($request->input('menu_vars'), true);

			for ($i = 0; $i < count($getJSONDecodeMenuVars); $i++) 
			{
				if ( ! isset($getJSONDecodeMenuVars[$i]['category_code']) || $getJSONDecodeMenuVars[$i]['category_code'] == '')
				{
					$getJSONDecodeMenuVars[$i]['category_code'] = Str::random(22);
				}
			}

			$getDetailMenuParentJSON->fill(['menu_vars' => json_encode($getJSONDecodeMenuVars), 'menu_vars_backup' => $getDetailMenuParentJSON->menu_vars]);

			if ($getDetailMenuParentJSON->save())
			{
				// DB Commit if data successfully saved
				DB::commit();
			
				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'   => true,
						'status'    => 'success',
						'message'   => t('Data successfully updated')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Data successfully updated'));
				}
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/**
	 * Delete Parent Menu
	 */
	
	public function delete_parentmenu(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			if (Storage::disk('public')->exists('icons/parent_menu/'.$request->input('path_file')))
			{
				Storage::disk('public')->delete('icons/parent_menu/'.$request->input('path_file'));
			}

			Custom_Permissions::where('menu_code', $request->input('menu_code'))->delete();

			// DB Commit if data successfully saved
			DB::commit();

			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'   => true,
					'status'    => 'success',
					'message'   => t('Data successfully deleted')
				]);
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Data successfully deleted'));
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/*
	 * Delete Submenu
	 */

	public function delete_submenu(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try
		{
			$detail_data = Sub_Menu_BE_JSON::where('id', $request->route('idOrSlug'))->first();

			if ($detail_data)
			{
				$detail_data->delete();

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
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/**
	 * Delete Submenu Detail
	 */
	
	public function delete_submenu_detail(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			if (Storage::disk('public')->exists('icons/sub_menu/'.$request->input('path_file')))
			{
				Storage::disk('public')->delete('icons/sub_menu/'.$request->input('path_file'));
			}

			Custom_Permissions::where('menu_code', $request->input('menu_code'))->delete();

			DB::commit();

			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'   => true,
					'status'    => 'success',
					'message'   => t('Data successfully deleted')
				]);
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Data successfully deleted'));
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/*
	 * Delete Category Menu
	 */

	public function delete_categorymenu(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> true,
					'status'	=> 'success',
					'message'	=> t('Data successfully deleted')
				]);
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Data successfully deleted'));
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
			DB::rollBack();
		}
		finally 
		{
			return $response;
		}
	}

	/*
	 * Delete Custom Menu
	 */

	public function delete_custommenu(Request $request)
	{
		// We use DB transaction for sabety
		DB::beginTransaction();	

		try 
		{
			Custom_Permissions::where('menu_code', $request->input('menu_code'))->delete();

			DB::commit();

			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success'	=> true,
					'status'	=> 'success',
					'message'	=> t('Data successfully deleted')
				]);
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Data successfully deleted'));
			}
		}
		catch (\Throwable $th) 
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> false,
					'status'	=> 'failed',
					'message' 	=> $th->getMessage()

				], 500);
			} 
			else 
			{
				$response = redirect()
					->back()
					->withInput()
					->with('error', $th->getMessage());
			}

			// DB Rollback to get data back if fail to saving
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

	public function destroy()
	{
		//
	}

	public function listDataRoles()
	{
		$roles = Roles::get();

		if (count($roles) > 0)
		{
			foreach ($roles as $key => $value) 
			{
				$new_output[$key] = $value['name'];
			}
		}

		return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $new_output]);
	}

	public function listDataPermissions()
	{
		$permissions = Permissions::get();

		if (count($permissions) > 0)
		{
			foreach ($permissions as $key => $value) 
			{
				$new_output[$key] = $value['name'];
			}
		}

		return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $new_output]);
	}

	public function listDataCategoryMenu()
	{
		$menu_categorylist = Category_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($menu_categorylist as $key0 => $value0) 
		{
			if ($value0['menu_vars'] !== 'null')
			{
				$menu_vars_decode = json_decode($value0['menu_vars'], true);
			}
			else
			{
				$menu_vars_decode = [];
			}
		}

		if (is_array($menu_vars_decode) && count($menu_vars_decode) > 0)
		{
			foreach ($menu_vars_decode as $key => $value) 
			{
				$menu_vars_decode2[$key] = $value;
			}
		}
		else
		{
			$menu_vars_decode2 = [];
		}

		return response()->json($menu_vars_decode2);
	}

	public function listDataParentMenu()
	{
		$menu_parentlist = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($menu_parentlist as $key0 => $value0) 
		{
			if ($value0['menu_vars'] !== 'null')
			{
				$menu_vars_decode = json_decode($value0['menu_vars'], true);
			}
			else
			{
				$menu_vars_decode = [];
			}
		}

		if (is_array($menu_vars_decode) && count($menu_vars_decode) > 0)
		{
			foreach ($menu_vars_decode as $key => $value) 
			{
				$menu_vars_decode2[$key] = $value;

				if (isset($menu_vars_decode2[$key]['parent_icon_path']) && $menu_vars_decode2[$key]['parent_icon_path'] !== '')
				{
					$menu_vars_decode2[$key]['parent_icon_url'] = url($this->getImageURL($menu_vars_decode2[$key]['parent_icon_path']));
				}
				else
				{
					$menu_vars_decode2[$key]['parent_icon_url'] = '';
					$menu_vars_decode2[$key]['parent_icon_path'] = '';
				}
			}
		}
		else
		{
			$menu_vars_decode2 = [];
		}

		return response()->json($menu_vars_decode2);
	}

	public function listDataParentMenuForSubmenu()
	{
		$menu_parentlist = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($menu_parentlist as $key0 => $value0) 
		{
			if ($value0['menu_vars'] !== 'null')
			{
				$menu_vars_decode = json_decode($value0['menu_vars'], true);
			}
			else
			{
				$menu_vars_decode = [];
			}
		}

		if (is_array($menu_vars_decode) && count($menu_vars_decode) > 0)
		{
			$menu_vars_decode2 = [];
			
			foreach ($menu_vars_decode as $key => $value) 
			{
				if ($menu_vars_decode[$key]['is_for_parent_menu'] == 'parent')
				{
					$menu_vars_decode2[$key] = $value;

					if (isset($menu_vars_decode2[$key]['parent_icon_path']) && $menu_vars_decode2[$key]['parent_icon_path'] !== '')
					{
						$menu_vars_decode2[$key]['parent_icon_url'] = url($this->getImageURL($menu_vars_decode2[$key]['parent_icon_path']));
					}
					else
					{
						$menu_vars_decode2[$key]['parent_icon_url'] = '';
						$menu_vars_decode2[$key]['parent_icon_path'] = '';
					}
				}
			}

			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $menu_vars_decode2]);
		}
		else
		{
			$menu_vars_decode2 = [];

			return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
		}
	}

	public function listDataCategoryMenuForParentMenu()
	{
		$menu_categorylist = Category_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($menu_categorylist as $key0 => $value0) 
		{
			if ($value0['menu_vars'] !== 'null')
			{
				$menu_vars_decode = json_decode($value0['menu_vars'], true);
			}
			else
			{
				$menu_vars_decode = [];
			}
		}

		if (is_array($menu_vars_decode) && count($menu_vars_decode) > 0)
		{
			foreach ($menu_vars_decode as $key => $value) 
			{
				$menu_vars_decode2[$key] = $value;
			}

			return response()->json(['success' => true, 'status' => 'success', 'message' => 'Data found', 'data' => $menu_vars_decode2]);
			// return response()->json($menu_vars_decode2);
		}
		else
		{
			$menu_vars_decode2 = [];

			return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
		}
	}

	public function listDataSubmenu()
	{		
		$menulist = Sub_Menu_BE_JSON::get();

		if ($menulist)
		{
			if (count($menulist) > 0)
			{
				return response()->json(['success' => true, 'status' => 'success', 'message' => 'Menu found', 'data' => $menulist]);
			}
			else
			{
				return response()->json(['success' => false, 'status' => 'failed', 'message' => 'No Menu']);
			}

		}
		else
		{
			return response()->json(['success' => false, 'status' => 'failed', 'message' => 'Data not found']);
		}
	}

	public function listDataSubmenuDetail(Request $request)
	{
		$menulist = Sub_Menu_BE_JSON::where('parent_code', $request->route('idOrSlug'))->get();

		foreach ($menulist as $key0 => $value0) 
		{
			if ($value0['menu_vars'] !== 'null')
			{
				$menu_vars_decode = json_decode($value0['menu_vars'], true);
			}
			else
			{
				$menu_vars_decode = [];
			}
		}

		if (is_array($menu_vars_decode) && count($menu_vars_decode) > 0)
		{
			foreach ($menu_vars_decode as $key => $value) 
			{
				$menu_vars_decode2[$key] = $value;

				if (isset($menu_vars_decode2[$key]['submenu_icon_path']) && $menu_vars_decode2[$key]['submenu_icon_path'] !== '')
				{
					$menu_vars_decode2[$key]['submenu_icon_url'] = url($this->getImageURL($menu_vars_decode2[$key]['submenu_icon_path'], 'icons/sub_menu'));
				}
				else
				{
					$menu_vars_decode2[$key]['submenu_icon_url'] = '';
					$menu_vars_decode2[$key]['submenu_icon_path'] = '';
				}
			}
		}
		else
		{
			$menu_vars_decode2 = [];
		}

		return response()->json($menu_vars_decode2);
	}

	public function listDataRoutes()
	{
		$routeList = Route::getRoutes();
		$usesBlacklist = ['submenu', 'edit', 'listdata', 'listData', 'detailData', 'setLanguage'];
		$output = [];
		$i = 0;

		foreach ($routeList as $value)
		{
			$getFirstRouteName = explode('.', $value->getName());
			$getUses = explode('@', $value->getAction('uses'));
			$getFilteredUses = isset($getUses[1]) ? $getUses[1] : '';

			// if ( ! in_array($getFilteredUses, $usesBlacklist) &&
			if ( ! preg_match('/'.implode('|', $usesBlacklist).'/i', $getFilteredUses, $match) &&
				$getFirstRouteName[0] == 'cms' && $getFirstRouteName[1] !== 'core' &&
				$value->methods()[0] == 'GET')
			{
				// $output[$i] = $value->uri().' => '.$getFilteredUses;
				$output[$i] = ['name' => $getFilteredUses, 'uri' => $value->uri()];

				$i++;
			}
		}

		return response()->json(['status' => 'success', 'message' => 'Data found', 'data' => $output]);
	}

	public function upload_image_parentmenu(array $attr, string $index_file)
	{
		if ( ! empty($attr['parent_icon_'.$index_file]))
		{
			$file = $attr['parent_icon_'.$index_file];
			$path = 'parent_menu';

			if (is_string($attr['parent_icon_'.$index_file]))
			{
				if ( ! preg_match('/^data:image\/(\w+);base64,/', $file))
				{
					return false;
				}

				$imageData 		= substr($file, strpos($file, ',') + 1);
				$decodedImage 	= base64_decode($imageData, true);

				if ( ! $decodedImage || ! $this->isValidImage($decodedImage))
				{
					return false;
				}

				$uploadedFile 	= Helper::decodeBase64($file, $path, $attr['key'], "tmp", null, 2);
				$fileType   	= Helper::getFileType($file);
				$fileExtension  = explode('/', $fileType)[1];
			}
			else
			{
				$uploadedFile 	= $this->uploadFile($file, $path);
				$extension 		= $file->extension();
			}

			return $uploadedFile;
		}
	}

	public function upload_image_submenu(array $attr, string $index_file)
	{
		if ( ! empty($attr['submenu_icon_'.$index_file]))
		{
			$file = $attr['submenu_icon_'.$index_file];
			$path = 'sub_menu';

			if (is_string($attr['submenu_icon_'.$index_file]))
			{
				if ( ! preg_match('/^data:image\/(\w+);base64,/', $file))
				{
					return false;
				}

				$imageData 		= substr($file, strpos($file, ',') + 1);
				$decodedImage 	= base64_decode($imageData, true);

				if ( ! $decodedImage || ! $this->isValidImage($decodedImage))
				{
					return false;
				}

				$uploadedFile 	= Helper::decodeBase64($file, $path, $attr['key'], "tmp", null, 2);
				$fileType   	= Helper::getFileType($file);
				$fileExtension  = explode('/', $fileType)[1];
			}
			else
			{
				$uploadedFile 	= $this->uploadFile($file, $path);
				$extension 		= $file->extension();
			}

			return $uploadedFile;
		}
	}

	protected function uploadFile($file, $path)
	{
		if ($file) 
		{
			$fileName = uniqid("{$path}_").'.'.$file->getClientOriginalExtension();
			$file->storeAs('icons/'.$path, $fileName, 'public');

			return $fileName;
		}

		return null;
	}

	protected function deleteFile($fileImage = '', $path = 'icons/parent_menu')
	{
		if ($fileImage) 
		{
			Storage::disk('public')->delete($path.'/'.$fileImage);
		}

		return null;
	}

	public function getImageURL($fileImage = '', $path = 'icons/parent_menu')
	{
		if (Storage::disk('public')->exists($path.'/'.$fileImage))
		{
			return Storage::url($path.'/'.$fileImage);
		}

		return null;
	}

	private function isValidImage($data)
	{
		// Write your validation logic here
		// For example, you can use getimagesize or imagecreatefromstring
		// Here's a simple example using getimagesize:
		$imageInfo = getimagesizefromstring($data);
	}

	public function getParentName($parent_code)
	{
		$menu_parentlist = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->get();

		foreach ($menu_parentlist as $key0 => $value0) 
		{
			if ($value0['menu_vars'] !== 'null')
			{
				$menu_vars_decode = json_decode($value0['menu_vars'], true);
			}
			else
			{
				$menu_vars_decode = [];
			}
		}

		if (is_array($menu_vars_decode) && count($menu_vars_decode) > 0)
		{
			$menu_vars_decode2 = null;

			foreach ($menu_vars_decode as $key => $value) 
			{
				if ($menu_vars_decode[$key]['parent_code'] == $parent_code &&
					$menu_vars_decode[$key]['is_for_parent_menu'] == 'parent')
				{
					$menu_vars_decode2 = $value['parent_name'];
				}
			}

			return $menu_vars_decode2;
		}
		else
		{
			$menu_vars_decode2 = null;

			return $menu_vars_decode2;
		}
	}

	public function getVersionMenu()
	{
		return response()->json(site_config()->management_menu);
	}

	public function checkParentMenuLink(AddMenuRequest $request)
	{
		$parentMenu = Parent_Menu_BE_JSON::where('menu_page', 'awesome_admin')->first();
		$parentMenuVars_decode = json_decode($parentMenu['menu_vars'], TRUE);

		if (is_array($parentMenuVars_decode) && count($parentMenuVars_decode) > 0)
		{
			$getResultParentMenu = collect($parentMenuVars_decode)->filter(fn($value, $key) => $value['parent_link'] == $request->input('parent_link'))->first();

			if ($getResultParentMenu)
			{
				$response = ['success' => false, 'status' => 'failed', 'message' => t('The entered link already exists in the menu')];
			}
			else
			{
				$response = ['success' => true, 'status' => 'success', 'message' => t('Link can be added')];
			}
		}
		else
		{
			$response = ['success' => true, 'status' => 'success', 'message' => t('Link can be added')];
		}

		return response()->json($response);
	}

	public function checkSubmenuLink(AddSubmenuRequest $request)
	{
		$subMenu = Sub_Menu_BE_JSON::where('parent_code', $request->input('parent_code'))->first();
		$subMenuVars_decode = json_decode($subMenu['menu_vars'], TRUE);

		if (is_array($subMenuVars_decode) && count($subMenuVars_decode) > 0)
		{
			$getResultSubmenu = collect($subMenuVars_decode)->filter(fn($value, $key) => $value['submenu_link'] == $request->input('submenu_link'))->first();

			if ($getResultSubmenu)
			{
				$response = ['success' => false, 'status' => 'failed', 'message' => t('The entered link already exists in the menu')];
			}
			else
			{
				$response = ['success' => true, 'status' => 'success', 'message' => t('Link can be added')];
			}
		}
		else
		{
			$response = ['success' => true, 'status' => 'success', 'message' => t('Link can be added')];
		}

		return response()->json($response);
	}
}
