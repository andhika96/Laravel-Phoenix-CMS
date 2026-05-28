<?php

namespace App\Http\Controllers\Web\Manage_Article;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Http\Resources\Manage_Article\CategoryResource;
use App\Http\Resources\Manage_Article\DetailCategoryResource;
use App\Http\Resources\Manage_Article\ManageArticleListResource;
use App\Http\Resources\Manage_Article\ManageArticleEditResource;

use App\Models\Awesome_Admin\Account;

use App\Models\Article\Article;
use App\Models\Article\Article_Categories;
use App\Models\Article\Article_Subcategories;
use App\Models\Article\Article_Status;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Str;
use Illuminate\Support\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Article\AddCategoryRequest;
use App\Http\Requests\Article\EditCategoryRequest;

use App\Http\Requests\Article\AddArticleRequest;
use App\Http\Requests\Article\EditArticleRequest;

use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManagerStatic as Image;

use App\Events\CMS\ArticleCreated;
use App\Events\CMS\ArticleUpdated;

class Manage_Article_Controller extends Controller
{
	public function __construct()
	{
		// We manually set timezone to Asia Jakarta, Indonesia
		date_default_timezone_set('Asia/Jakarta');
	}

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return view('manage_article.manage_article', ['article_status' => Article_Status::where('is_active', 0)->get(), 'categories' => Article_Categories::get()]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function add(Request $request)
	{
		session_start();

		$_SESSION['CKFinder_UserRole_UUID'] = auth()->user()->uuid;
		$_SESSION['CKFinder_UserRole'] = $request->session()->get('LaraCKFinder_UserRole');

		return view('manage_article.manage_article_add', ['categories' => Article_Categories::get()]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(AddArticleRequest $request)
	{
		if ($request->validated())
		{
			DB::beginTransaction();	

			try
			{
				if ($request->input('uri'))
				{
					$slug = strtolower($request->input('uri'));
					$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
					$slug = preg_replace("/[\s-]+/", " ", $slug);
					$slug = preg_replace("/[\s_]/", "-", $slug);
				}
				else
				{
					$slug = strtolower($request->input('title'));
					$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
					$slug = preg_replace("/[\s-]+/", " ", $slug);
					$slug = preg_replace("/[\s_]/", "-", $slug);
				}

				$new_data['user_id']			??= auth()->user()->id;

				$new_data['uri']				??= $slug;
				$new_data['category_id']		??= $request->input('category_id');
				$new_data['title']				??= $request->input('title');
				$new_data['content']			??= $request->input('content');
				$new_data['status']				??= $request->input('status');
				$new_data['visibility']			??= $request->input('visibility');

				if ($request->input('visibility') == 'password_protected')
				{
					$new_data['password_protected']	??= $request->input('password_protected');
				}

				if ($request->input('created_at'))
				{
					$new_data['scheduled']		??= true;
					$new_data['created_at']		??= $request->input('created_at').' '.$request->input('hours').':'.$request->input('minutes').':00';
				}

				$article = Article::create($new_data);

				if ($article)
				{
					if ($request->hasFile('thumbnail'))
					{
						$new_data_thumbnail['thumb_l'] ??= $this->uploadThumbnail($request->file('thumbnail'));
						$new_data_thumbnail['thumb_s'] ??= $this->uploadThumbnailSmall($request->file('thumbnail'));
					
						$getArticleData = Article::find($article->id);
						$getArticleData->fill($new_data_thumbnail);
						$getArticleData->save();
					}

					DB::commit();

					// Broadcast real-time notification
					$categoryName = Article_Categories::find($new_data['category_id'])?->name ?? 'Uncategorized';
					event(new ArticleCreated(
						actorName: auth()->user()->fullname ?? auth()->user()->username,
						articleTitle: $new_data['title'],
						articleSlug: $new_data['uri'],
						category: $categoryName,
						status: (string) $new_data['status'],
						createdAt: Carbon::now()->toDateTimeString(),
					));
				}
				else
				{
					DB::rollBack();
				}

				if ($request->wantsJson())
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Article created successfully'),
					]);
				}
				else
				{
					$response = redirect()
					->back()
					->with('success', t('Article created successfully'));
				}
			}
			catch (\Throwable $th) 
			{
				DB::rollBack();

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
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function storeCategory(AddCategoryRequest $request)
	{
		if ($request->validated())
		{
			DB::beginTransaction();	

			try
			{
				$slug = strtolower($request->input('category_name'));
				$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
				$slug = preg_replace("/[\s-]+/", " ", $slug);
				$slug = preg_replace("/[\s_]/", "-", $slug);

				$new_data['name']		??= $request->input('category_name');
				$new_data['code']		??= $slug;
				$new_data['status']		??= $request->input('category_status');

				$category = Article_Categories::create($new_data);

				if ($category)
				{
					DB::commit();
				}
				else
				{
					DB::rollBack();
				}

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Category created successfully')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Category created successfully'));
				}
			}
			catch (\Throwable $th) 
			{
				DB::rollBack();

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
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Account $user, Request $request, $idOrSlug)
	{
		if ($user->isAdmin())
		{
			$getArticleDetail = Article::with(['getStatus'])->find($idOrSlug);
		}
		else
		{
			$getArticleDetail = Article::with(['getStatus'])->where('user_id', auth()->user()->id)->find($idOrSlug);
		}

		if ($getArticleDetail)
		{
			session_start();

			$_SESSION['CKFinder_UserRole_UUID'] = auth()->user()->uuid;
			$_SESSION['CKFinder_UserRole'] = $request->session()->get('LaraCKFinder_UserRole');

			return view('manage_article.manage_article_edit', ['article' => $getArticleDetail, 'categories' => Article_Categories::get()]);
		}

		abort(404);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(EditArticleRequest $request, $idOrSlug)
	{
		$getArticleData = Article::find($idOrSlug);

		if ($getArticleData)
		{
			if ($request->validated())
			{
				DB::beginTransaction();	

				try
				{
					if ($request->input('uri'))
					{
						$slug = strtolower($request->input('uri'));
						$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
						$slug = preg_replace("/[\s-]+/", " ", $slug);
						$slug = preg_replace("/[\s_]/", "-", $slug);
					}
					else
					{
						$slug = strtolower($request->input('title'));
						$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
						$slug = preg_replace("/[\s-]+/", " ", $slug);
						$slug = preg_replace("/[\s_]/", "-", $slug);
					}

					$new_data['uri']				??= $slug;
					$new_data['category_id']		??= $request->input('category_id');
					$new_data['title']				??= $request->input('title');
					$new_data['content']			??= $request->input('content');
					$new_data['status']				??= $request->input('status');
					$new_data['visibility']			??= $request->input('visibility');

					if ($request->input('visibility') == 'password_protected')
					{
						$new_data['password_protected']	??= $request->input('password_protected');
					}
					else
					{
						$new_data['password_protected']	??= null;
					}

					if ($request->input('created_at'))
					{
						$new_data['scheduled']		??= true;
						$new_data['created_at']		??= $request->input('created_at').' '.$request->input('hours').':'.$request->input('minutes').':00';
					}
					else
					{
						$new_data['scheduled']		??= 'false';
						$new_data['created_at']		??= Carbon::now();
					}

					if ($request->hasFile('thumbnail'))
					{
						// Delete old image and replace with the below one
						if ($getArticleData->thumb_l !== NULL)
						{
							if (Storage::disk('public')->exists($getArticleData->thumb_l))
							{
								$this->deleteFile($getArticleData->thumb_l);
							}						
						}

						$new_data['thumb_l'] ??= $this->uploadThumbnail($request->file('thumbnail'));

						// Delete old image and replace with the below one
						if ($getArticleData->thumb_s !== NULL)
						{
							if (Storage::disk('public')->exists($getArticleData->thumb_s))
							{
								$this->deleteFile($getArticleData->thumb_s);
							}						
						}
						
						$new_data['thumb_s'] ??= $this->uploadThumbnailSmall($request->file('thumbnail'));
					}

					$getArticleData->fill($new_data);

					if ($getArticleData->save())
					{
						DB::commit();

						// Broadcast real-time notification
						$categoryName = Article_Categories::find($new_data['category_id'])?->name ?? 'Uncategorized';
						event(new ArticleUpdated(
							actorName: auth()->user()->fullname ?? auth()->user()->username,
							articleTitle: $new_data['title'],
							articleSlug: $new_data['uri'],
							category: $categoryName,
							status: (string) $new_data['status'],
							updatedAt: Carbon::now()->toDateTimeString(),
						));
					}
					else
					{
						DB::rollBack();
					}

					if ($request->wantsJson())
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('Article edited successfully')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('Article edited successfully'));
					}
				}
				catch (\Throwable $th) 
				{
					DB::rollBack();

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
		}

		abort(404);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function updateCategory(EditCategoryRequest $request)
	{
		// $getCategoryData = Article_Categories::find($idOrSlug);
		$getCategoryData = Article_Categories::find($request->input('idOrSlug'));

		if ($getCategoryData)
		{
			if ($request->validated())
			{
				DB::beginTransaction();	

				try
				{
					$slug = strtolower($request->input('category_name'));
					$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
					$slug = preg_replace("/[\s-]+/", " ", $slug);
					$slug = preg_replace("/[\s_]/", "-", $slug);

					$new_data['name']		??= $request->input('category_name');
					$new_data['code']		??= $slug;
					$new_data['status']		??= $request->input('category_status');

					$getCategoryData->fill($new_data);

					if ($getCategoryData->save())
					{
						DB::commit();
					}
					else
					{
						DB::rollBack();
					}

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('Category edited successfully')
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('Category edited successfully'));
					}
				}
				catch (\Throwable $th) 
				{
					DB::rollBack();

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
		}

		abort(404);
	}

	/**
	 * Bulk update the specified resource in storage.
	 */

	public function bulk_update(Request $request)
	{
		// We use DB transaction for safety
		DB::beginTransaction();

		try 
		{
			if ($request->input('mode') == 'bulk_update')
			{
				if ($request->input('changeStatus') == '')
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'	=> false,
							'status' 	=> 'failed',
							'message'	=> t("You haven't selected the options yet")
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('failed', t("You haven't selected the options yet"));
					}
				}
				else
				{
					if (is_array($request->input('getSelected')))
					{
						foreach ($request->input('getSelected') as $key) 
						{
							if ($request->input('changeStatus') != '')
							{
								$getDetailArticle = Article::find($key);
								$getDetailArticle->fill(['status' => $request->input('changeStatus')]);

								if ($getDetailArticle->save())
								{
									// DB Commit if data successfully saved
									DB::commit();
								}

								// DB Rollback to get data back if fail to saving
								DB::rollBack();
							}
						}
					}

					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'	=> true,
							'status' 	=> 'success',
							'message'	=> t('Article successfully updated')
						]);
					} 
					else 
					{
						$response = redirect()
							->back()
							->with('success', t('Article successfully updated'));
					}
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
					'message' 	=> $th->getMessage(),

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
	public function delete(Account $user, Request $request, $idOrSlug)
	{
		if ($user->isAdmin())
		{
			$getDetailArticle = Article::where('id', $idOrSlug)->first();
		}
		else
		{
			$getDetailArticle = Article::where('user_id', auth()->user()->id)->where('id', $idOrSlug)->first();
		}

		if ($getDetailArticle)
		{
			DB::beginTransaction();	

			try
			{
				if ($getDetailArticle['thumb_l'] !== NULL)
				{
					if (Storage::disk('public')->exists($getDetailArticle['thumb_l']))
					{
						$this->deleteFile($getDetailArticle['thumb_l']);
					}
				}

				if ($getDetailArticle['thumb_s'] !== NULL)
				{
					if (Storage::disk('public')->exists($getDetailArticle['thumb_s']))
					{
						$this->deleteFile($getDetailArticle['thumb_s']);
					}
				}

				$getDetailArticle->delete();

				DB::commit();

				if ($request->wantsJson()) 
				{
					$response = response()->json(
					[
						'success'		=> true,
						'status'		=> 'success',
						'message'		=> t('Article successfully deleted'),
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Article successfully deleted'));
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

		abort(404);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function deleteCategory(Request $request, $idOrSlug)
	{
		$getDetailCategory = Article_Categories::where('id', $idOrSlug)->first();

		if ($getDetailCategory)
		{
			DB::beginTransaction();	

			try
			{
				if ($getDetailCategory->code == 'uncategorized')
				{
					DB::rollback();
				
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success' 	=> false,
							'status'	=> 'failed',
							'message' 	=> t('Default category cannot be deleted')

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
				else
				{
					$countArticles = Article::where('category_id', $getDetailCategory->id)->count();

					if ($countArticles > 0) 
					{
						$uncategorized = Article_Categories::where('code', 'uncategorized')->first();

						if ($uncategorized)
						{
							Article::where('category_id', $getDetailCategory->id)->update(['category_id' => $uncategorized->id]);
						
							$getDetailCategory->delete();

							DB::commit();

							if ($request->wantsJson()) 
							{
								$response = response()->json(
								[
									'success'		=> true,
									'status'		=> 'success',
									'message'		=> t('Category successfully deleted and articles moved to Uncategorized'),
								]);
							} 
							else 
							{
								$response = redirect()
								->back()
								->with('success', t('Category successfully deleted and articles moved to Uncategorized'));
							}
						}
						else
						{
							DB::rollback();
				
							if ($request->wantsJson()) 
							{
								$response = response()->json(
								[
									'success' 	=> false,
									'status'	=> 'failed',
									'message' 	=> t('Destination category {1} not found.', 'Uncategorized')

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
					}
					else
					{
						$getDetailCategory->delete();

						DB::commit();

						if ($request->wantsJson()) 
						{
							$response = response()->json(
							[
								'success'		=> true,
								'status'		=> 'success',
								'message'		=> t('Category successfully deleted'),
							]);
						} 
						else 
						{
							$response = redirect()
							->back()
							->with('success', t('Category successfully deleted'));
						}
					}
				}
			}
			catch (\Throwable $th) 
			{
				DB::rollback();

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

		abort(404);
	}

	public function detailData(Account $user, $idOrSlug)
	{
		if ($user->isAdmin())
		{
			$getArticleDetail = Article::with(['getStatus'])->where('id', $idOrSlug)->get();
		}
		else
		{
			$getArticleDetail = Article::with(['getStatus'])->where('user_id', auth()->user()->id)->where('id', $idOrSlug)->get();
		}
		
		$resource = ManageArticleEditResource::class;

		if (is_subclass_of($resource, JsonResource::class)) 
		{
			$items = $resource::collection($getArticleDetail);
		} 
		else 
		{
			$items = $getArticleDetail;
		}

		if ($getArticleDetail)
		{
			$response = response()->json(
			[
				'success' 	=> true,
				'status'	=> 'success',
				'message' 	=> 'Data found',
				'data'		=> isset($items[0]) ? $items[0] : []
			]);
		}
		else
		{
			$response = response()->json(
			[
				'success' 	=> false,
				'status'	=> 'failed',
				'message' 	=> 'Data not found'

			], 500);
		}

		return $response;
	}

	public function detailDataCategory($idOrSlug)
	{
		$getCategoryDetail = Article_Categories::where('id', $idOrSlug)->get();
		
		$resource = DetailCategoryResource::class;

		if (is_subclass_of($resource, JsonResource::class)) 
		{
			$items = $resource::collection($getCategoryDetail);
		} 
		else 
		{
			$items = $getCategoryDetail;
		}

		if ($getCategoryDetail)
		{
			$response = response()->json(
			[
				'success' 	=> true,
				'status'	=> 'success',
				'message' 	=> 'Data found',
				'data'		=> isset($items[0]) ? $items[0] : []
			]);
		}
		else
		{
			$response = response()->json(
			[
				'success' 	=> false,
				'status'	=> 'failed',
				'message' 	=> 'Data not found'

			], 500);
		}

		return $response;
	}

	public function checkDataCategory(Request $request, $idOrSlug)
	{
		$getDetailCategory = Article_Categories::where('id', $idOrSlug)->first();

		if ($getDetailCategory)
		{
			try
			{
				$countArticles = Article::where('category_id', $getDetailCategory->id)->count();

				if ($countArticles > 0) 
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('This category contains data. If you delete it, all associated data will automatically move to {1}. Are you sure?', 'Uncategorized'),
							'data'			=> $getDetailCategory
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('This category contains data. If you delete it, all associated data will automatically move to {1}. Are you sure?', 'Uncategorized'));
					}
				}
				else
				{
					if ($request->wantsJson()) 
					{
						$response = response()->json(
						[
							'success'		=> true,
							'status'		=> 'success',
							'message'		=> t('Do you really want to delete these data? {1} This process cannot be undone.', '<br/>'),
							'data'			=> $getDetailCategory
						]);
					} 
					else 
					{
						$response = redirect()
						->back()
						->with('success', t('Do you really want to delete these data? {1} This process cannot be undone.', '<br/>'));
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
			} 
			finally 
			{
				return $response;
			}
		}

		abort(404);
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

											})->paginate(15);

		$formattedResponse = $api->paginateResponse($result, ManageArticleListResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	public function listCategories(Request $request)
	{
		$api = new Base_API_Rev_Controller();
		$result = Article_Categories::whereLike('name', '%'.$request->input('search').'%')->paginate(25);

		$formattedResponse = $api->paginateResponse($result, CategoryResource::class);

		$response = $api->setStatusMsg($formattedResponse['total'] ? 'success' : 'failed')
						->respondOK($formattedResponse, $formattedResponse['total'] ? 'Data found' : 'No data found');

		return $response;
	}

	public function uploadThumbnail($attribute, $path = 'articles')
	{
		if ( ! empty($attribute))
		{
			$file = $attribute;

			$directoryMonthYear	= date("mY", time());
			$subDirectoryDate	= 'date_'.date("d", time());
			
			// For database only without dot and slash at the front folder
			$x_folder = $path.'/'.$directoryMonthYear.'/'.$subDirectoryDate;

			if (is_string($attribute))
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

				// Get file type and extension before filtering
				$fileType   	= getFileType($file);
				$fileExtension  = explode('/', $fileType)[1];

				// Filtering image base64encode
				list($type, $file)	= explode(';', $file);
				list(, $file)		= explode(',', $file);

				// Get file temporary from temporary directory
				$temp_file_path = tempnam(sys_get_temp_dir(), 'contents'); // might not work on some systems, specify your temp path if system temp dir is not writeable
				file_put_contents($temp_file_path, base64_decode($file));

				// Create new file for image base64encode
				$_FILES['userFile']['name']			= uniqid().'.'.$fileExtension;
				$_FILES['userFile']['tmp_name']		= $temp_file_path;
				$_FILES['userFile']['size']			= filesize($temp_file_path);
				$_FILES['userFile']['type']			= $fileType;
				$_FILES['userFile']['extension']	= $fileExtension;
				$_FILES['userFile']['error']		= 0;

				$newFile = new UploadedFile($_FILES['userFile']['tmp_name'], $_FILES['userFile']['name'], $_FILES['userFile']['type']);
				
				// Create new request file				
				request()->files->set('userFile', $newFile);

				// New file and request file already created, now time to upload to server
				$uploadedFile 	= $this->uploadFile(request()->file('userFile'), $x_folder);
			}
			else
			{
				$uploadedFile 	= $this->uploadFile($file, $x_folder);
				$extension 		= $file->extension();
			}

			return $uploadedFile;
		}
	}

	public function uploadThumbnailSmall($attribute, $path = 'articles')
	{
		if ( ! empty($attribute))
		{
			$file = $attribute;

			$directoryMonthYear	= date("mY", time());
			$subDirectoryDate	= 'date_'.date("d", time());
			
			// For database only without dot and slash at the front folder
			$x_folder = $path.'/'.$directoryMonthYear.'/'.$subDirectoryDate;

			if (is_string($attribute))
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

				// Get file type and extension before filtering
				$fileType   	= getFileType($file);
				$fileExtension  = explode('/', $fileType)[1];

				// Filtering image base64encode
				list($type, $file)	= explode(';', $file);
				list(, $file)		= explode(',', $file);

				// Get file temporary from temporary directory
				$temp_file_path = tempnam(sys_get_temp_dir(), 'contents'); // might not work on some systems, specify your temp path if system temp dir is not writeable
				file_put_contents($temp_file_path, base64_decode($file));

				// Create new file for image base64encode
				$_FILES['userFile']['name']			= uniqid().'.'.$fileExtension;
				$_FILES['userFile']['tmp_name']		= $temp_file_path;
				$_FILES['userFile']['size']			= filesize($temp_file_path);
				$_FILES['userFile']['type']			= $fileType;
				$_FILES['userFile']['extension']	= $fileExtension;
				$_FILES['userFile']['error']		= 0;

				$newFile = new UploadedFile($_FILES['userFile']['tmp_name'], $_FILES['userFile']['name'], $_FILES['userFile']['type']);
				
				// Create new request file				
				request()->files->set('userFile', $newFile);

				// New file and request file already created, now time to upload to server
				$uploadedFile 	= $this->uploadFileSmall(request()->file('userFile'), $x_folder);
			}
			else
			{
				$uploadedFile 	= $this->uploadFileSmall($file, $x_folder);
				$extension 		= $file->extension();
			}

			return $uploadedFile;
		}
	}

	protected function uploadFile($file, $path)
	{
		if ($file) 
		{
			$fileNameWithExt = $file->getClientOriginalName();
			$fileNameWithoutExt = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

			$fileName = random_string('md5').'.'.$file->getClientOriginalExtension();
			$resultUpload = $file->storeAs($path, $fileName, 'public');

			return $resultUpload;
		}

		return null;
	}

	protected function uploadFileSmall($file, $path)
	{	
		if ($file) 
		{
			$fileNameWithExt = $file->getClientOriginalName();
			$fileNameWithoutExt = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

			$fileName = random_string('md5').'_small.'.$file->getClientOriginalExtension();

			// Create new image for smaller size
			// Create image manager with desired driver
			$manager 	= new ImageManager(Driver::class);
			$image 		= $manager->read($file);
			$image->scale(width: 720);

			$realPath = public_path('storage/'.$path).'/'.$fileName;
			$resultUpload = $image->save($realPath);

			return $path.'/'.$fileName;
		}

		return null;
	}

	protected function deleteFile($file)
	{
		if ($file) 
		{
			Storage::disk('public')->delete($file);
		}

		return null;
	}

	protected function getImageURL($fileImage = '', $path = 'articles')
	{
		if (Storage::disk('public')->exists($path.'/'.$fileImage))
		{
			return Storage::url($path.'/'.$fileImage);
		}

		return null;
	}

	protected function isValidImage($data)
	{
		// Write your validation logic here
		// For example, you can use getimagesize or imagecreatefromstring
		// Here's a simple example using getimagesize:
		$imageInfo = getimagesizefromstring($data);

		return $imageInfo !== false;
	}
}
