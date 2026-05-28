<?php

namespace App\Http\Controllers\Web\File_Manager;

use App\Http\Controllers\Controller;
use App\Models\Awesome_Admin\Account;

use Illuminate\Http\Request;

class File_Manager_Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		session_start();

		// $request->session()->put('LaraCKFinder_UserRole', 'Super Admin');
		// $request->session()->put('LaraCKFinder_UserRole', 'Administrator');

		// $_SESSION['CKFinder_UserRole'] = '';
		// $_SESSION['CKFinder_UserRole'] = 'Administrator';
		$_SESSION['CKFinder_UserRole_UUID'] = auth()->user()->uuid;
		$_SESSION['CKFinder_UserRole'] =  $request->session()->get('LaraCKFinder_UserRole');
		// $_SESSION['CKFinder_UserRole_'.auth()->user()->uuid] = $request->session()->get('LaraCKFinder_UserRole_'.auth()->user()->uuid);

		return view('file_manager.file_manager');
	}
}
