<?php

namespace App\Http\Controllers\Web\Awesome_Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Awesome_Admin\HeaderNavigationRequest;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Header_Navigation_Setting;
use App\Support\FrontendHeaderNavigationConfigNormalizer;

use Illuminate\Support\Facades\DB;

class Awesome_Admin_Header_Navigation_Controller extends Controller
{
	public function __construct(Account $user)
	{
		if ( ! $user->isAdmin())
		{
			abort(403);
		}
	}

	/**
	 * Display the header navigation settings page.
	 */
	public function index()
	{
		$setting = Header_Navigation_Setting::where('menu_page', 'awesome_admin')->first();
		$config = FrontendHeaderNavigationConfigNormalizer::normalize($setting?->config_json ?? []);

		return view('awesome_admin.header_navigation.awesome_admin_header_navigation',
		[
			'is_active' => $setting?->is_active ?? true,
			'config' => $config
		]);
	}

	/**
	 * Update the active frontend header navigation settings.
	 */
	public function update(HeaderNavigationRequest $request)
	{
		DB::beginTransaction();

		try
		{
			$config = FrontendHeaderNavigationConfigNormalizer::normalize($request->input('config_json', []));

			$setting = Header_Navigation_Setting::updateOrCreate(
			[
				'menu_page' => 'awesome_admin'
			],
			[
				'is_active' => $request->boolean('is_active'),
				'config_json' => $config
			]);

			DB::commit();

			$response = response()->json(
			[
				'success' => true,
				'status' => 'success',
				'message' => t('Header navigation settings successfully updated'),
				'data' =>
				[
					'is_active' => $setting->is_active,
					'config_json' => $config,
					'updated_at' => $setting->updated_at?->toIso8601String()
				]
			]);
		}
		catch (\Throwable $th)
		{
			DB::rollBack();

			$response = response()->json(
			[
				'success' => false,
				'status' => 'failed',
				'message' => $th->getMessage()
			], 500);
		}
		finally
		{
			return $response;
		}
	}
}
