<?php

namespace App\Http\Controllers\Web\Homepage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Resources\Manage_Article\ManageArticleListWidgetResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Sub_Menu_JSON;
use App\Models\Awesome_Admin\Parent_Menu_JSON;
use App\Models\Awesome_Admin\Category_Menu_JSON;
use App\Models\Awesome_Admin\Custom_Permissions;

use App\Models\Article\Article;


use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Homepage_Controller extends Controller
{
	public function index()
	{
		return view('homepage.homepage');
	}

	public function listData(Request $request)
	{
		$api = new Base_API_Rev_Controller();
		$result = Article::with(['getStatus'])->whereLike('title', '%'.$request->input('search').'%')
											->where(function($query) use($request) 
											{ 													
												$query->whereLike('status', '%'.$request->input('filter_by_status').'%');
												$query->whereLike('scheduled', '%'.$request->input('filter_by_scheduled').'%');
												$query->WhereLike('category_id', '%'.$request->input('filter_by_category').'%'); 

											})->paginate($request->input('perPage'));

		$formattedResponse = $api->paginateResponse($result, ManageArticleListWidgetResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}
}

?>
