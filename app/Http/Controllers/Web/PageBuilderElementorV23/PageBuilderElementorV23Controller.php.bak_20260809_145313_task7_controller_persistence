<?php

namespace App\Http\Controllers\Web\PageBuilderElementorV23;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page_Builder_Elementor_V23\AddPageBuilderElementorV23Request;
use App\Http\Requests\Page_Builder_Elementor_V23\EditPageBuilderElementorV23Request;
use App\Models\Page_Builder\Page_Builder;
use App\Support\PageBuilderElementorV23\FormSubmissionHandler;
use App\Support\PageBuilderElementorV23\ImageRenditionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PageBuilderElementorV23Controller extends Controller
{
	public function imageRendition(Request $request, ImageRenditionResolver $resolver)
	{
		$validated = $request->validate([
			'url' => ['required', 'string', 'max:2048'],
			'size' => ['required', 'string', 'in:thumbnail,medium,medium_large,large,1536x1536,2048x2048,full,custom'],
			'width' => ['nullable', 'integer', 'min:1', 'max:4096'],
			'height' => ['nullable', 'integer', 'min:1', 'max:4096'],
		]);
		if ($validated['size'] === 'custom' && empty($validated['width']) && empty($validated['height']))
		{
			return response()->json(['message' => 'Custom image resolution requires a width or height.'], 422);
		}

		$sourceUrl = trim($validated['url']);
		$customWidth = isset($validated['width']) ? (int) $validated['width'] : null;
		$customHeight = isset($validated['height']) ? (int) $validated['height'] : null;
		if (!str_starts_with($sourceUrl, '/') && !preg_match('#^https?://#i', $sourceUrl))
		{
			return response()->json(['message' => 'The image URL is invalid.'], 422);
		}

		return response()->json([
			'sourceUrl' => $sourceUrl,
			'size' => $validated['size'],
			'width' => $customWidth,
			'height' => $customHeight,
			'url' => $resolver->resolve($sourceUrl, $validated['size'], $customWidth, $customHeight),
		]);
	}

	public function create(Request $request)
	{
		$this->prepareCkfinderSession($request);

		return view('pagebuilder_elementor_v23.editor_shell',
		[
			'pageData' => null,
			'mode' => 'create',
			'saveUrl' => route('cms.core.pagebuilder_elementor_v23.store'),
		]);
	}

	public function edit(Request $request, $idOrSlug)
	{
		$this->prepareCkfinderSession($request);

		$pageData = $this->resolveOwnedPage($idOrSlug);

		if (! $pageData)
		{
			return $this->missingOrVersionConflict($idOrSlug, false);
		}

		if ($pageData)
		{
			return view('pagebuilder_elementor_v23.editor_shell',
			[
				'pageData' => $pageData,
				'mode' => 'edit',
				'saveUrl' => route('cms.core.pagebuilder_elementor_v23.update', $pageData->uri),
			]);
		}

		abort(404);
	}

	public function store(AddPageBuilderElementorV23Request $request)
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
					'editor_version' => Page_Builder::EDITOR_VERSION_V23,
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
						'editUrl' => route('cms.core.pagebuilder_elementor_v23.edit', $uri),
						'uri' => $uri,
					]);
				}
				else
				{
					$response = redirect()
						->route('cms.core.pagebuilder_elementor_v23.edit', $uri)
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

	public function update(EditPageBuilderElementorV23Request $request, $idOrSlug)
	{
		$pageData = $this->resolveOwnedPage($idOrSlug);

		if (! $pageData)
		{
			return $this->missingOrVersionConflict($idOrSlug, $request->wantsJson());
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
						->route('cms.core.pagebuilder_elementor_v23.edit', $uri)
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
		$pageData = $this->resolveOwnedPage($idOrSlug);

		if (! $pageData)
		{
			return $this->missingOrVersionConflict($idOrSlug, false);
		}

		$nodes = is_array($pageData->vars)
			? $pageData->vars
			: (json_decode($pageData->vars ?? '[]', true) ?? []);

		return view('pagebuilder_elementor_v23.frontend_renderer', [
			'pageData' => $pageData,
			'nodes'    => $nodes,
		]);
	}

	public function submitForm(Request $request, $idOrSlug, $nodeId, FormSubmissionHandler $handler)
	{
		$pageData = $this->resolveOwnedPage($idOrSlug);

		if (! $pageData)
		{
			return $this->missingOrVersionConflict($idOrSlug, true);
		}

		try
		{
			return response()->json($handler->handle($pageData, (string) $nodeId, $request));
		}
		catch (ValidationException $exception)
		{
			return response()->json([
				'success' => false,
				'message' => collect($exception->errors())->flatten()->first() ?: 'Please check the form fields.',
				'errors' => $exception->errors(),
			], 422);
		}
		catch (HttpExceptionInterface $exception)
		{
			throw $exception;
		}
		catch (\Throwable $exception)
		{
			report($exception);

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while submitting the form.',
			], 500);
		}
	}

	public function getData($idOrSlug)
	{
		$pageData = $this->resolveOwnedPage($idOrSlug);

		if (! $pageData)
		{
			return $this->missingOrVersionConflict($idOrSlug, true);
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

	private function resolveOwnedPage(string|int $idOrSlug): ?Page_Builder
	{
		return Page_Builder::query()
			->where('editor_version', Page_Builder::EDITOR_VERSION_V23)
			->where(fn ($query) => $query
				->where('uri', $idOrSlug)
				->orWhere('id', $idOrSlug))
			->first();
	}

	private function missingOrVersionConflict(string|int $idOrSlug, bool $wantsJson)
	{
		$page = Page_Builder::query()
			->where(fn ($query) => $query
				->where('uri', $idOrSlug)
				->orWhere('id', $idOrSlug))
			->first();

		if (! $page)
		{
			if ($wantsJson)
			{
				return response()->json([
					'success' => false,
					'status' => 'failed',
					'message' => 'Page data not found',
				], 404);
			}

			abort(404);
		}

		if ($wantsJson)
		{
			return response()->json([
				'success' => false,
				'status' => 'failed',
				'message' => 'This page belongs to a different editor version',
				'editorVersion' => $page->editor_version,
			], 409);
		}

		abort(409, 'This page belongs to a different editor version.');
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
