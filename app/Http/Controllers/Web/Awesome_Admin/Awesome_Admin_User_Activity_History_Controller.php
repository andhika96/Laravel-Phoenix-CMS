<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Awesome_Admin\Account;

use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog as Log;

use GeoIP;

class Awesome_Admin_User_Activity_History_Controller extends Controller
{
	public function index()
	{
		$LogUser = Log::query()->where('authenticatable_type', Account::class)->paginate(15);

		return response()->json($LogUser);
	}
}