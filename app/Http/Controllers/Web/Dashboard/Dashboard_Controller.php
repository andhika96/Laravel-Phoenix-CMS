<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Sub_Menu_JSON;
use App\Models\Awesome_Admin\Parent_Menu_JSON;
use App\Models\Awesome_Admin\Category_Menu_JSON;
use App\Models\Awesome_Admin\Custom_Permissions;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Dashboard_Controller extends Controller
{
	public function index()
	{
		return view('dashboard.dashboard');
	}

	public function setVerticalSidebarMenuCollapse()
	{
		$request = new Request;

		try
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> true,
					'status'	=> 'success',
					'message' 	=> t('Vertical Sidebar Menu successfully updated')

				], 200)->cookie()->forever('phoenix_vertical_sidebar_menu', 'collapsed');
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Vertical Sidebar Menu successfully updated'))
				->withCookie(cookie()->forever('phoenix_vertical_sidebar_menu', 'collapsed'));
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
		} 
		finally 
		{
			return $response;
		}
	}

	public function removeVerticalSidebarMenuCollapse()
	{
		$request = new Request;
		
		try
		{
			if ($request->wantsJson()) 
			{
				$response = response()->json(
				[
					'success' 	=> true,
					'status'	=> 'success',
					'message' 	=> t('Vertical Sidebar Menu successfully updated')

				], 200)->withoutCookie('phoenix_vertical_sidebar_menu', 'collapsed');
			} 
			else 
			{
				$response = redirect()
				->back()
				->with('success', t('Vertical Sidebar Menu successfully updated'))
				->withoutCookie('phoenix_vertical_sidebar_menu');
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
		} 
		finally 
		{
			return $response;
		}
	}

	public function getIsVerticalSidebarMenuCollapse()
	{
		if (request()->cookie('phoenix_vertical_sidebar_menu') == 'collapsed')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>
