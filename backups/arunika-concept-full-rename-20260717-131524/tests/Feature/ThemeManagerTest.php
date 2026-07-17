<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;

use Illuminate\Support\Facades\DB;

use Tests\TestCase;

class ThemeManagerTest extends TestCase
{
	public function test_administrator_can_open_theme_manager_from_awesome_admin(): void
	{
		$this->actingAsAdministrator();

		$this->get(route('cms.admin.awesome_admin'))
			->assertOk()
			->assertSee(route('cms.admin.awesome_admin.themes'), false)
			->assertSee('Manage Themes');

		$this->get(route('cms.admin.awesome_admin.themes'))
			->assertOk()
			->assertSee('id="ph-app-theme-manager"', false)
			->assertSee('Arunika V1')
			->assertSee('Arunika V2')
			->assertSee('Arunika V3')
			->assertDontSee('Browse installed themes');
	}

	public function test_administrator_can_activate_an_installed_arunika_theme(): void
	{
		$this->actingAsAdministrator();
		$originalSetting = DB::table('theme_settings')->where('id', 1)->first();
		$targetThemeCode = $originalSetting?->theme_code === 'arunika_v3' ? 'arunika_v2' : 'arunika_v3';
		$targetTheme = DB::table('themes')->where('theme_code', $targetThemeCode)->firstOrFail();

		try
		{
			$this->postJson(route('cms.admin.awesome_admin.themes.update'), [
				'theme_code' => $targetThemeCode,
			])->assertOk()->assertJson([
				'success' => true,
				'status' => 'success',
				'active_theme' => $targetThemeCode,
			]);

			$this->assertDatabaseHas('theme_settings', [
				'id' => 1,
				'theme_id' => $targetTheme->id,
				'theme_code' => $targetThemeCode,
			]);
		}
		finally
		{
			if ($originalSetting)
			{
				DB::table('theme_settings')->where('id', 1)->update((array) $originalSetting);
			}
		}
	}

	public function test_theme_manager_rejects_non_arunika_theme_codes(): void
	{
		$this->actingAsAdministrator();

		$this->postJson(route('cms.admin.awesome_admin.themes.update'), [
			'theme_code' => 'default',
		])->assertUnprocessable()->assertJsonValidationErrors('theme_code');
	}

	protected function actingAsAdministrator(): Account
	{
		$account = Account::role('Super Admin')->first() ?? Account::role('Administrator')->first();

		if ( ! $account)
		{
			$this->markTestSkipped('An administrator account is required for the Theme Manager feature test.');
		}

		$this->actingAs($account);
		$this->withSession(['_token' => 'theme-manager-test-token']);
		$this->withHeader('X-CSRF-TOKEN', 'theme-manager-test-token');

		return $account;
	}
}
