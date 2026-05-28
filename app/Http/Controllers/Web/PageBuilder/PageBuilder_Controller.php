<?php

namespace App\Http\Controllers\Web\PageBuilder;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\Awesome_Admin\Account;
use App\Models\Page_Builder\Page_Builder;

use App\Http\Requests\Page_Builder\AddPageBuilderRequest;
use App\Http\Requests\Page_Builder\EditPageBuilderRequest;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PageBuilder_Controller extends Controller
{
	public function index()
	{
		return view('pagebuilder.pagebuilder');
	}

	public function create()
	{
		return view('pagebuilder.pagebuilder_add', ['pageData' => null]);
	}

	public function edit(Request $request, $idOrSlug)
	{
		// $pageData = Page_Builder::findOrFail($idOrSlug);
		$pageData = Page_Builder::where('uri', $idOrSlug)->first();

		if ($pageData)
		{
			return view('pagebuilder.editor', compact('pageData'));
		}

		abort(404);
	}

	public function store(AddPageBuilderRequest $request)
	{
		if ($request->validated())
		{
			DB::beginTransaction();	

			try
			{
				$slug = strtolower($request->input('pageName', 'untitled'));
				$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
				$slug = preg_replace("/[\s-]+/", " ", $slug);
				$slug = preg_replace("/[\s_]/", "-", $slug);

				$new_data = [
					'user_id'	 => 1,
					'uri'		 => $slug,
					'page_name'  => $request->input('pageName', 'Untitled'),
					'custom_css' => $request->input('customCss', ''),
					'vars'		 => $request->input('layout', '[]'),
					'status'	 => $request->input('pageStatus', 'draft'),
				];

				$pageBuilder = Page_Builder::create($new_data);

				if ($pageBuilder)
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
						'message'		=> t('Page Builder created successfully'),
						// 'redirect_url' 	=> url('manage_course_super_trainer/add_content_course/'.$super_trainer->id)
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Page Builder created successfully'));
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

	public function update(EditPageBuilderRequest $request, $idOrSlug)
	{
		$pageData = Page_Builder::where('uri', $idOrSlug)->first();

		if ($request->validated())
		{
			DB::beginTransaction();	

			try
			{
				$slug = strtolower($request->input('pageName', 'untitled'));
				$slug = preg_replace("/[^a-z0-9_\s-]/", "", $slug);
				$slug = preg_replace("/[\s-]+/", " ", $slug);
				$slug = preg_replace("/[\s_]/", "-", $slug);

				$new_data = [
					'user_id'	 => 1,
					'uri'		 => $slug,
					'page_name'  => $request->input('pageName', 'Untitled'),
					'custom_css' => $request->input('customCss', ''),
					'vars'		 => $request->input('layout', '[]'),
					'status'	 => $request->input('pageStatus', 'draft'),
				];

				$pageData->fill($new_data);

				if ($pageData->save())
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
						'message'		=> t('Page edited successfully')
					]);
				} 
				else 
				{
					$response = redirect()
					->back()
					->with('success', t('Page edited successfully'));
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

		abort(404);
	}
}

?>
