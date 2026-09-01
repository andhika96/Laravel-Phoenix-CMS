<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Themes;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class Awesome_Admin_Themes_Controller extends Controller
{
	private const MANAGEABLE_THEME_CODES = [
		'arunika_mosaic',
		'arunika_aurora',
		'arunika_prism',
		'arunika_equinox',
		'arunika_lucent',
	];

	public function __construct(Account $user)
	{
		if ( ! $user->isAdmin())
		{
			abort(403);
		}
	}

	public function index(): View
	{
		$themeMetadata = [
			'arunika_mosaic' => [
				'display_name' => 'Arunika Mosaic',
				'preview_image' => 'assets/images/themes/previews/arunika-mosaic-theme-preview.png',
				'description' => 'The original Arunika experience with a light green navigation system and compact dashboard rhythm.',
			],
			'arunika_aurora' => [
				'display_name' => 'Arunika Aurora',
				'preview_image' => 'assets/images/themes/previews/arunika-aurora-theme-preview.png',
				'description' => 'A refined Arunika interface with stronger typography, improved navigation, and modern responsive controls.',
			],
			'arunika_prism' => [
				'display_name' => 'Arunika Prism',
				'preview_image' => 'assets/images/themes/previews/arunika-prism-theme-preview.png',
				'description' => 'A calm commerce-inspired dashboard shell with compact navigation, focused search, and a clean profile header.',
			],
			'arunika_equinox' => [
				'display_name' => 'Arunika Equinox',
				'preview_image' => 'assets/images/themes/previews/arunika-equinox-theme-preview.png',
				'description' => 'A balanced mint-and-teal dashboard that moves cleanly between airy light and deep dark surfaces.',
			],
			'arunika_lucent' => [
				'display_name' => 'Arunika Lucent',
				'preview_image' => 'assets/images/themes/previews/arunika-lucent-theme-preview.png',
				'description' => 'A clean Swiss-inspired admin shell with a bright neutral canvas and a calm green action system.',
			],
		];

		$themes = Themes::query()
			->whereIn('theme_code', self::MANAGEABLE_THEME_CODES)
			->orderByRaw("CASE theme_code WHEN 'arunika_mosaic' THEN 1 WHEN 'arunika_aurora' THEN 2 WHEN 'arunika_prism' THEN 3 WHEN 'arunika_equinox' THEN 4 WHEN 'arunika_lucent' THEN 5 ELSE 6 END")
			->get()
			->map(function (Themes $theme) use ($themeMetadata): array
			{
				$metadata = $themeMetadata[$theme->theme_code];

				return [
					'id' => $theme->id,
					'code' => $theme->theme_code,
					'name' => $metadata['display_name'],
					'version' => $theme->theme_version,
					'preview_image' => $metadata['preview_image'],
					'description' => $metadata['description'],
				];
			});

		$activeThemeCode = DB::table('theme_settings')->where('id', 1)->value('theme_code');

		return view('awesome_admin.awesome_admin_themes', [
			'themes' => $themes,
			'activeThemeCode' => $activeThemeCode,
		]);
	}

	public function update(Request $request): JsonResponse
	{
		$validated = $request->validate([
			'theme_code' => [
				'required',
				'string',
				Rule::in(self::MANAGEABLE_THEME_CODES),
				Rule::exists('themes', 'theme_code'),
			],
		]);

		$theme = Themes::query()->where('theme_code', $validated['theme_code'])->firstOrFail();

		DB::transaction(function () use ($theme): void
		{
			DB::table('theme_settings')->updateOrInsert(
				['id' => 1],
				[
					'theme_id' => $theme->id,
					'theme_code' => $theme->theme_code,
					'theme_name' => $theme->theme_name,
				]
			);
		});

		return response()->json([
			'success' => true,
			'status' => 'success',
			'message' => t('Theme successfully activated'),
			'active_theme' => $theme->theme_code,
			'redirect_url' => route('cms.admin.awesome_admin.themes'),
		]);
	}
}
