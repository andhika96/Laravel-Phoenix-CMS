<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Site_Config;

use Illuminate\Support\Facades\DB;

use Tests\TestCase;

class SiteTypographyPreviewSettingsTest extends TestCase
{
	public function test_site_config_uses_existing_vue_select_with_live_typography_preview(): void
	{
		$view = file_get_contents(resource_path('views/awesome_admin/awesome_admin_config.blade.php'));
		$script = file_get_contents(public_path('assets/js/vue3/manage_config/vueV3-manage-config-2026.js'));
		$migrationPath = database_path('migrations/2026_07_15_133500_add_typography_units_to_site_config_table.php');

		$this->assertStringContainsString('<v-select label="name"', $view);
		$this->assertStringContainsString('v-model="responseData.font_family"', $view);
		$this->assertStringContainsString('id="siteTypographyPreview"', $view);
		$this->assertStringContainsString('name="font_size"', $view);
		$this->assertStringContainsString('v-model.number="responseData.font_size"', $view);
		$this->assertStringContainsString('name="font_size_unit"', $view);
		$this->assertStringContainsString('v-model="responseData.font_size_unit"', $view);
		$this->assertSame(1, preg_match('/<select[^>]*name="font_size_unit"[\s\S]*?<\/select>/', $view, $fontUnitSelect));
		$this->assertStringContainsString('<option value="px">px</option>', $fontUnitSelect[0]);
		$this->assertStringContainsString('<option value="em">em</option>', $fontUnitSelect[0]);
		$this->assertStringContainsString('<option value="rem">rem</option>', $fontUnitSelect[0]);
		$this->assertStringNotContainsString('<option value="pt">pt</option>', $fontUnitSelect[0]);
		$this->assertStringContainsString('siteTypographyPreviewStyle: function()', $script);
		$this->assertStringContainsString('handleSiteTypographyUnitChange: function()', $script);
		$this->assertStringContainsString('resetSiteTypographyPreview: function()', $script);
		$this->assertFileExists($migrationPath);
	}

	public function test_administrator_can_save_decimal_font_size_with_em_unit(): void
	{
		$this->actingAsAdministrator();
		$originalConfig = Site_Config::findOrFail(1)->getAttributes();

		try
		{
			$this->postJson(route('cms.admin.awesome_admin.config.update'), [
				'font_family' => 'Nunito',
				'font_size' => 0.875,
				'font_size_unit' => 'em',
			])->assertOk()->assertJson([
				'success' => true,
				'status' => 'success',
			]);

			$config = Site_Config::findOrFail(1);

			$this->assertSame('Nunito', $config->font_family);
			$this->assertSame(0.875, (float) $config->font_size);
			$this->assertSame('em', $config->font_size_unit);
		}
		finally
		{
			DB::table('site_config')->where('id', 1)->update(
				collect($originalConfig)->except('id')->all()
			);
		}
	}

	public function test_font_size_unit_and_range_are_validated(): void
	{
		$this->actingAsAdministrator();

		$this->postJson(route('cms.admin.awesome_admin.config.update'), [
			'font_size' => 14,
			'font_size_unit' => 'pt',
		])->assertUnprocessable()->assertJsonValidationErrors('font_size_unit');

		$this->postJson(route('cms.admin.awesome_admin.config.update'), [
			'font_size' => 5,
			'font_size_unit' => 'rem',
		])->assertUnprocessable()->assertJsonValidationErrors('font_size');
	}

	protected function actingAsAdministrator(): Account
	{
		$account = Account::role('Super Admin')->first() ?? Account::role('Administrator')->first();

		if ( ! $account)
		{
			$this->markTestSkipped('An administrator account is required for the Site Config feature test.');
		}

		$this->actingAs($account);
		$this->withSession(['_token' => 'site-typography-test-token']);
		$this->withHeader('X-CSRF-TOKEN', 'site-typography-test-token');

		return $account;
	}
}
