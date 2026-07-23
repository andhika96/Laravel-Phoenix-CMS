<?php

namespace App\Http\Controllers\Web\PageBuilderElementor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page_Builder_Elementor\AddPageBuilderElementorRequest;
use App\Http\Requests\Page_Builder_Elementor\EditPageBuilderElementorRequest;
use App\Models\Page_Builder\Page_Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageBuilderElementor_Controller extends Controller
{
	public function create(Request $request)
	{
		$this->prepareCkfinderSession($request);

		return view('pagebuilder_elementor.editor_shell',
		[
			'pageData' => null,
			'mode' => 'create',
			'saveUrl' => route('cms.core.pagebuilder_elementor.store'),
		]);
	}

	public function edit(Request $request, $idOrSlug)
	{
		$this->prepareCkfinderSession($request);

		$pageData = Page_Builder::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if ($pageData)
		{
			return view('pagebuilder_elementor.editor_shell',
			[
				'pageData' => $pageData,
				'mode' => 'edit',
				'saveUrl' => route('cms.core.pagebuilder_elementor.update', $pageData->uri),
			]);
		}

		abort(404);
	}

	public function store(AddPageBuilderElementorRequest $request)
	{
		if ($request->validated())
		{
			DB::beginTransaction();

			try
			{
				$pageName = $request->input('pageName', 'Untitled');
				$uri = $this->buildUniqueUri($pageName);
				$layoutPayload = $this->normalizeLayoutPayload($request->input('layout', '[]'));

				$newData = [
					'user_id' => 1,
					'uri' => $uri,
					'page_name' => $pageName,
					'custom_css' => $this->normalizeCustomCssPayload($request->input('customCss', '')),
					'vars' => $layoutPayload,
					'status' => $request->input('pageStatus', 'draft'),
				];

				$pageBuilder = Page_Builder::create($newData);

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
						'message' => t('Page Builder Elementor created successfully'),
						'editUrl' => route('cms.core.pagebuilder_elementor.edit', $uri),
						'uri' => $uri,
					]);
				}
				else
				{
					$response = redirect()
						->route('cms.core.pagebuilder_elementor.edit', $uri)
						->with('success', t('Page Builder Elementor created successfully'));
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

	public function update(EditPageBuilderElementorRequest $request, $idOrSlug)
	{
		$pageData = Page_Builder::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if (! $pageData)
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

				$newData = [
					'user_id' => $pageData->user_id ?? 1,
					'uri' => $uri,
					'page_name' => $pageName,
					'custom_css' => $this->normalizeCustomCssPayload($request->input('customCss', '')),
					'vars' => $layoutPayload,
					'status' => $request->input('pageStatus', 'draft'),
				];

				$pageData->fill($newData);

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
						'message' => t('Page Builder Elementor updated successfully'),
						'uri' => $uri,
					]);
				}
				else
				{
					$response = redirect()
						->route('cms.core.pagebuilder_elementor.edit', $uri)
						->with('success', t('Page Builder Elementor updated successfully'));
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

	public function preview($idOrSlug)
	{
		$pageData = Page_Builder::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if (! $pageData)
		{
			abort(404);
		}

		$nodes = is_array($pageData->vars)
			? $pageData->vars
			: (json_decode($pageData->vars ?? '[]', true) ?? []);

		return view('pagebuilder_elementor.frontend_renderer', [
			'pageData' => $pageData,
			'nodes'    => $nodes,
		]);
	}

	public function getData($idOrSlug)
	{
		$pageData = Page_Builder::query()
			->where('uri', $idOrSlug)
			->orWhere('id', $idOrSlug)
			->first();

		if (! $pageData)
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
		$slug = preg_replace('/[^a-z0-9_\s-]/', '', $slug);
		$slug = preg_replace('/[\s-]+/', ' ', $slug);
		$slug = preg_replace('/[\s_]/', '-', $slug);
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
		$query = Page_Builder::query()->where('uri', $uri);

		if ($ignoreId)
		{
			$query->where('id', '!=', $ignoreId);
		}

		return $query->exists();
	}

	private function normalizeLayoutPayload($layout): string
	{
		$normalized = [];

		if (is_array($layout))
		{
			$normalized = $layout;
		}
		elseif (is_string($layout))
		{
			$decoded = json_decode($layout, true);

			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
			{
				$normalized = $decoded;
			}
		}

		$encoded = json_encode($normalized, JSON_UNESCAPED_UNICODE);

		return $encoded === false ? '[]' : $encoded;
	}

	private function normalizeCustomCssPayload($customCss): string
	{
		if (is_string($customCss))
		{
			return $customCss;
		}

		if (is_array($customCss))
		{
			$flatten = array_map(function ($line)
			{
				if (is_scalar($line) || $line === null)
				{
					return (string) $line;
				}

				return json_encode($line, JSON_UNESCAPED_UNICODE) ?: '';
			}, $customCss);

			return implode("\n", $flatten);
		}

		return '';
	}

	private function prepareCkfinderSession(Request $request): void
	{
		if (session_status() === PHP_SESSION_NONE)
		{
			@session_start();
		}

		$user = auth()->user();
		if (! $user)
		{
			return;
		}

		$role = $request->session()->get('LaraCKFinder_UserRole');
		if (empty($role) && method_exists($user, 'getRoleNames'))
		{
			$role = $user->getRoleNames()->first();
		}

		$role = $this->normalizeCkfinderRole($role);

		$_SESSION['CKFinder_UserRole_UUID'] = $user->uuid ?? '';
		$_SESSION['CKFinder_UserRole'] = $role;
	}

	private function normalizeCkfinderRole($role): string
	{
		$raw = is_scalar($role) || $role === null ? (string) $role : '';
		$normalized = strtolower(trim($raw));
		$normalized = preg_replace('/[^a-z]/', '', $normalized);

		if ($normalized === 'superadmin')
		{
			return 'Super Admin';
		}

		if ($normalized === 'administrator' || $normalized === 'admin')
		{
			return 'Administrator';
		}

		// Fallback supaya ACL CKFinder tetap match dan tidak blank loading.
		return 'Administrator';
	}
}
