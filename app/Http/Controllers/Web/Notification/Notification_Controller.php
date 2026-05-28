<?php

namespace App\Http\Controllers\Web\Notification;

use App\Http\Controllers\Controller;

use App\Models\Awesome_Admin\Roles;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Information;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Str;
use Illuminate\Support\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class Notification_Controller extends Controller
{
	public function index()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		send_notification(1, 'general', '', 'new status', 'You role has been changed to Premium Member');

		return view('notification.notification', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);
	}

	public function emptyNotification()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		return view('notification.notification_empty', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);
	}

	public function filledNotification()
	{
		$getListRoles = Roles::get();
		$getDetailUser = Account::find(auth()->user()->id);
		$getDetailUserInformation = Account_Information::where('user_id', $getDetailUser->id)->first();

		return view('notification.notification_filled', ['data' => $getDetailUser, 'data_information' => $getDetailUserInformation]);
	}

	public function readNotification(Requests $request)
	{

	}

	public function notificationTestingPage()
	{
		return view('notification.notification_testing');
	}
}

?>
