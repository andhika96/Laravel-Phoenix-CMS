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
		$this->actingAsAdministrator();

		$view = file_get_contents(resource_path('views/awesome_admin/awesome_admin_config.blade.php'));
		$script = file_get_contents(public_path('assets/js/vue3/manage_config/vueV3-manage-config-2026.js'));
		$styles = file_get_contents(public_path('assets/css/themes/arunika_v2/arunika_v2.css'));
		$migrationPath = database_path('migrations/2026_07_15_133500_add_typography_units_to_site_config_table.php');
		$response = $this->get(route('cms.admin.awesome_admin.config'));

		$response->assertOk();
		$response->assertSee('id="siteTypographyPreview"', false);
		$response->assertSee('name="font_size_unit"', false);

		$this->assertStringContainsString('<v-select label="name"', $view);
		$this->assertStringContainsString('v-model="responseData.font_family"', $view);
		$this->assertStringContainsString('id="siteTypographyPreview"', $view);
		$this->assertStringContainsString('name="font_size"', $view);
		$this->assertStringContainsString('v-model.number="responseData.font_size"', $view);
		$this->assertStringContainsString('name="font_size_unit"', $view);
		$this->assertStringContainsString('v-model="responseData.font_size_unit"', $view);
		$this->assertStringContainsString('id="siteInformationLayout"', $view);
		$this->assertStringContainsString('id="siteThumbnailCard"', $view);
		$this->assertStringContainsString('id="typographySettingsLayout"', $view);
		$this->assertStringContainsString('.site-information-grid > section', $view);
		$this->assertStringContainsString('min-width: 0;', $view);
		$this->assertGreaterThan(strpos($view, 'id="siteInformationLayout"'), strpos($view, 'id="typographySettingsLayout"'));
		$this->assertSame(1, preg_match('/<select[^>]*name="font_size_unit"[\s\S]*?<\/select>/', $view, $fontUnitSelect));
		$this->assertStringContainsString('<option value="px">px</option>', $fontUnitSelect[0]);
		$this->assertStringContainsString('<option value="em">em</option>', $fontUnitSelect[0]);
		$this->assertStringContainsString('<option value="rem">rem</option>', $fontUnitSelect[0]);
		$this->assertStringNotContainsString('<option value="pt">pt</option>', $fontUnitSelect[0]);
		$this->assertStringContainsString('siteTypographyPreviewStyle: function()', $script);
		$this->assertStringContainsString('handleSiteTypographyUnitChange: function()', $script);
		$this->assertStringContainsString('resetSiteTypographyPreview: function()', $script);
		$this->assertStringContainsString('applySiteTypographySettings: function()', $script);
		$this->assertStringContainsString('this.applySiteTypographySettings();', $script);
		$this->assertStringContainsString('font-family: var(--ph-font-family);', $styles);
		$this->assertFileExists($migrationPath);
	}

	public function test_administrator_can_save_decimal_font_size_with_em_unit(): void
	{
		$this->actingAsAdministrator();
		$originalConfig = Site_Config::findOrFail(1)->getAttributes();

		try
		{
			$this->postJson(route('cms.admin.awesome_admin.config.update'), [
				'font_family' => 'fira_sans',
				'font_size' => 0.875,
				'font_size_unit' => 'em',
			])->assertOk()->assertJson([
				'success' => true,
				'status' => 'success',
			]);

			$config = Site_Config::findOrFail(1);

			$this->assertSame('fira_sans', $config->font_family);
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
		$originalConfig = Site_Config::findOrFail(1)->getAttributes();

		try
		{
			$this->postJson(route('cms.admin.awesome_admin.config.update'), [
				'font_size' => 14,
				'font_size_unit' => 'pt',
			])->assertUnprocessable()->assertJsonValidationErrors('font_size_unit');

			$this->postJson(route('cms.admin.awesome_admin.config.update'), [
				'font_size' => 5,
				'font_size_unit' => 'rem',
			])->assertUnprocessable()->assertJsonValidationErrors('font_size');

			$this->postJson(route('cms.admin.awesome_admin.config.update'), [
				'font_family' => 'font_not_installed',
			])->assertUnprocessable()->assertJsonValidationErrors('font_family');
		}
		finally
		{
			DB::table('site_config')->where('id', 1)->update(
				collect($originalConfig)->except('id')->all()
			);
		}
	}

	public function test_saved_typography_is_rendered_as_global_arunika_v2_css_variables(): void
	{
		$this->actingAsAdministrator();
		$originalConfig = Site_Config::findOrFail(1)->getAttributes();

		try
		{
			DB::table('site_config')->where('id', 1)->update([
				'font_family' => 'fira_sans',
				'font_size' => 15,
				'font_size_unit' => 'px',
			]);

			$response = $this->get(route('cms.admin.awesome_admin.config'));

			$response->assertOk();
			$response->assertSee('id="arunikaActiveFontStylesheet"', false);
			$response->assertSee('storage/fonts/fira_sans/fonts.css', false);
			$response->assertSee("--ph-font-family: 'Fira Sans'", false);
			$response->assertSee('--ph-font-size: 15px', false);
		}
		finally
		{
			DB::table('site_config')->where('id', 1)->update(
				collect($originalConfig)->except('id')->all()
			);
		}
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
