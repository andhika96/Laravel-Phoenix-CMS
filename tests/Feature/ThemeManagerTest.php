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
			->assertSee('Arunika Prism')
			->assertSee('Arunika Aurora')
			->assertSee('Arunika Lucent')
			->assertSee('Arunika Equinox')
			->assertDontSee('Arunika Mosaic')
			->assertDontSee('Browse installed themes');
	}

	public function test_theme_manager_lists_the_active_arunika_themes_in_the_approved_order(): void
	{
		$this->actingAsAdministrator();

		$html = $this->get(route('cms.admin.awesome_admin.themes'))
			->assertOk()
			->getContent();

		$positions = array_map(
			fn (string $themeName): int => strpos($html, $themeName),
			['Arunika Prism', 'Arunika Aurora', 'Arunika Lucent', 'Arunika Equinox'],
		);

		$this->assertNotContains(-1, $positions);
		$sortedPositions = $positions;
		sort($sortedPositions);
		$this->assertSame($sortedPositions, $positions);
	}

	public function test_prism_is_the_default_cms_theme_after_mosaic_cleanup(): void
	{
		$this->assertDatabaseHas('theme_settings', [
			'id' => 1,
			'theme_code' => 'arunika_prism',
			'theme_name' => 'Arunika Prism',
		]);
	}

	public function test_administrator_can_activate_an_installed_arunika_theme(): void
	{
		$this->actingAsAdministrator();
		$originalSetting = DB::table('theme_settings')->where('id', 1)->first();
		$targetThemeCode = $originalSetting?->theme_code === 'arunika_equinox' ? 'arunika_prism' : 'arunika_equinox';
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
