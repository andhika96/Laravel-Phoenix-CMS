<?php

namespace App\Http\Controllers\Web\Profile;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Information;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Profile_Controller extends Controller
{
	public function index()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		return view('profile.profile', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);

	}
}