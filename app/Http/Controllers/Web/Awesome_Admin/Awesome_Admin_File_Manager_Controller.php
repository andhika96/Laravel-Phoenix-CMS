<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;
use App\Models\Awesome_Admin\Account;

use Illuminate\Http\Request;

class Awesome_Admin_File_Manager_Controller extends Controller
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
	public function index(Request $request)
	{
		session_start();

		// $request->session()->put('LaraCKFinder_UserRole', 'Super Admin');
		// $request->session()->put('LaraCKFinder_UserRole', 'Administrator');

		// $_SESSION['CKFinder_UserRole'] = 'Administrator';
		// $_SESSION['CKFinder_UserRole'] =  $request->session()->get('LaraCKFinder_UserRole');
		$_SESSION['CKFinder_UserRole'] = $request->session()->get('LaraCKFinder_UserRole');

		return view('awesome_admin.awesome_admin_file_manager');
	}
}
