<?php

namespace App\Http\Controllers\Web\Article;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Resources\Manage_Article\ManageArticleListResource;

use App\Models\Awesome_Admin\Account;

use App\Models\Article\Article;
use App\Models\Article\Article_Categories;
use App\Models\Article\Article_Subcategories;
use App\Models\Article\Article_Status;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Article_Controller extends Controller
{
	public function __construct()
	{
		// We manually set timezone to Asia Jakarta, Indonesia
		date_default_timezone_set('Asia/Jakarta');
	}

	public function index()
	{
		return view('article.article');
	}

	public function detail($idOrSlug)
	{
		$getArticleDetail = Article::with(['getStatus'])->find($idOrSlug);

		if ($getArticleDetail)
		{
			return view('article.article_detail');
		}

		abort(404);
	}

	public function listData(Request $request)
	{
		$api = new Base_API_Rev_Controller();
		$result = Article::with(['getStatus'])->whereLike('title', '%'.$request->input('search').'%')->paginate(15);

		$formattedResponse = $api->paginateResponse($result, ManageArticleListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

}