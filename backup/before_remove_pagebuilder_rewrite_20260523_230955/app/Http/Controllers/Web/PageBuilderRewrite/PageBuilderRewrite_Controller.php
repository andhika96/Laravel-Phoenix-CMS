<?php

namespace App\Http\Controllers\Web\PageBuilderRewrite;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page_Builder_Rewrite\AddPageBuilderRewriteRequest;
use App\Http\Requests\Page_Builder_Rewrite\EditPageBuilderRewriteRequest;
use App\Models\Page_Builder\Page_Builder_Rewrite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageBuilderRewrite_Controller extends Controller
{
	public function index()
	{
		$pages = Page_Builder_Rewrite::query()
			->orderByDesc('id')
			->get();

		return view('pagebuilder_rewrite.pagebuilder_rewrite', compact('pages'));
	}

	public function create()
	{
		return view('pagebuilder_rewrite.editor_shell',
		[
			'pageData' => null,
			'mode' => 'create',
			'saveUrl' => route('cms.core.pagebuilder_rewrite.store'),
		]);
	}

	public function edit(Request $request, $idOrSlug)
	{
		$pageData = Page_Builder_Rewrite::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if ($pageData)
		{
			return view('pagebuilder_rewrite.editor_shell',
			[
				'pageData' => $pageData,
				'mode' => 'edit',
				'saveUrl' => route('cms.core.pagebuilder_rewrite.update', $pageData->uri),
			]);
		}

		abort(404);
	}

	public function store(AddPageBuilderRewriteRequest $request)
	{
		if ($request->validated())
		{
			DB::beginTransaction();

			try
			{
				$pageName = $request->input('pageName', 'Untitled');
				$uri = $this->buildUniqueUri($pageName);
				$layoutPayload = $this->normalizeLayoutPayload($request->input('layout', '[]'));

				$new_data = [
					'user_id' => 1,
					'uri' => $uri,
					'page_name' => $pageName,
					'custom_css' => $request->input('customCss', ''),
					'vars' => $layoutPayload,
					'status' => $request->input('pageStatus', 'draft'),
				];

				$pageBuilder = Page_Builder_Rewrite::create($new_data);

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
						'success' => true,
						'status' => 'success',
						'message' => t('Page Builder rewrite created successfully'),
					]);
				}
				else
				{
					$response = redirect()
					->route('cms.core.pagebuilder_rewrite.edit', $uri)
					->with('success', t('Page Builder rewrite created successfully'));
				}
			}
			catch (\Throwable $th)
			{
				DB::rollBack();

				if ($request->wantsJson())
				{
					$response = response()->json(
					[
						'success' => false,
						'status' => 'failed',
						'message' => $th->getMessage(),

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

	public function update(EditPageBuilderRewriteRequest $request, $idOrSlug)
	{
		$pageData = Page_Builder_Rewrite::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if ( ! $pageData)
		{
			abort(404);
		}

		if ($request->validated())
		{
			DB::beginTransaction();

			try
			{
				$pageName = $request->input('pageName', 'Untitled');
				$uri = $this->buildUniqueUri($pageName, $pageData->id);
				$layoutPayload = $this->normalizeLayoutPayload($request->input('layout', '[]'));

				$new_data = [
					'user_id' => $pageData->user_id ?? 1,
					'uri' => $uri,
					'page_name' => $pageName,
					'custom_css' => $request->input('customCss', ''),
					'vars' => $layoutPayload,
					'status' => $request->input('pageStatus', 'draft'),
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
						'success' => true,
						'status' => 'success',
						'message' => t('Page Builder rewrite updated successfully'),
					]);
				}
				else
				{
					$response = redirect()
					->route('cms.core.pagebuilder_rewrite.edit', $uri)
					->with('success', t('Page Builder rewrite updated successfully'));
				}
			}
			catch (\Throwable $th)
			{
				DB::rollBack();

				if ($request->wantsJson())
				{
					$response = response()->json(
					[
						'success' => false,
						'status' => 'failed',
						'message' => $th->getMessage(),

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

	public function getData($idOrSlug)
	{
		$pageData = Page_Builder_Rewrite::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if ( ! $pageData)
		{
			return response()->json(
			[
				'success' => false,
				'status' => 'failed',
				'message' => t('Page data not found'),
			], 404);
		}

		return response()->json(
		[
			'success' => true,
			'status' => 'success',
			'data' => $pageData,
		]);
	}

	private function buildUniqueUri($pageName, $ignoreId = null)
	{
		$slug = strtolower($pageName ?: 'untitled');
		$slug = preg_replace("/[^a-z0-9_\\s-]/", "", $slug);
		$slug = preg_replace("/[\\s-]+/", " ", $slug);
		$slug = preg_replace("/[\\s_]/", "-", $slug);
		$slug = trim($slug, '-');

		if (empty($slug))
		{
			$slug = 'untitled';
		}

		$base = $slug;
		$counter = 1;

		while ($this->uriExists($slug, $ignoreId))
		{
			$slug = $base . '-' . $counter;
			$counter++;
		}

		return $slug;
	}

	private function uriExists($uri, $ignoreId = null)
	{
		$query = Page_Builder_Rewrite::query()->where('uri', $uri);

		if ($ignoreId)
		{
			$query->where('id', '!=', $ignoreId);
		}

		return $query->exists();
	}

	private function normalizeLayoutPayload($layout)
	{
		if (is_array($layout))
		{
			return $layout;
		}

		if (is_string($layout))
		{
			$decoded = json_decode($layout, true);

			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
			{
				return $decoded;
			}
		}

		return [];
	}
}

?>
