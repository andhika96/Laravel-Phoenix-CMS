<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Awesome_Admin\Awesome_Admin_Header_Navigation_Controller;
use App\Http\Requests\Awesome_Admin\HeaderNavigationRequest;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Header_Navigation_Setting;
use App\Support\FrontendHeaderNavigationConfigNormalizer;
use App\Support\FrontendMenuBuilder;

use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

class HeaderNavigationSettingsTest extends TestCase
{
	use DatabaseTransactions;

	public function test_admin_controller_renders_and_persists_header_navigation_settings(): void
	{
		$controller = $this->headerNavigationController();
		$view = $controller->index();

		$this->assertSame('awesome_admin.header_navigation.awesome_admin_header_navigation', $view->name());
		$this->assertStringContainsString('saveHeaderNavigation', $view->render());

		$config = FrontendHeaderNavigationConfigNormalizer::defaultConfig();
		$config['sizing']['height'] = '88px';
		$request = HeaderNavigationRequest::create('/awesome_admin/header-navigation', 'POST',
		[
			'is_active' => true,
			'config_json' => $config
		]);
		$request->setContainer($this->app);
		$request->setRedirector($this->app->make('redirect'));
		$request->validateResolved();

		$response = $controller->update($request);
		$responseData = $response->getData(true);
		$setting = Header_Navigation_Setting::where('menu_page', 'awesome_admin')->first();

		$this->assertSame(200, $response->getStatusCode());
		$this->assertTrue($responseData['success']);
		$this->assertTrue($setting->is_active);
		$this->assertSame('88px', $setting->config_json['sizing']['height']);
	}

	public function test_preview_data_uses_the_frontend_menu_builder_dropdown_contract(): void
	{
		$controller = $this->headerNavigationController();
		$responseData = $controller->previewData()->getData(true);
		$previewItems = $responseData['data'];
		$frontendItems = FrontendMenuBuilder::items();

		$this->assertTrue($responseData['success']);
		$this->assertCount(count($frontendItems), $previewItems);

		foreach ($frontendItems as $index => $frontendItem)
		{
			$dropdownType = $frontendItem['dropdown_config']['dropdown_type'];
			$hasDropdown = in_array($dropdownType, ['bootstrap', 'mega'], true) && count($frontendItem['submenus']) > 0;

			$this->assertSame($frontendItem['parent_name'], $previewItems[$index]['parent_name']);
			$this->assertSame($dropdownType, $previewItems[$index]['dropdown_type']);
			$this->assertSame(count($frontendItem['submenus']), $previewItems[$index]['submenu_count']);
			$this->assertSame($hasDropdown, $previewItems[$index]['has_dropdown']);
			$this->assertSame((bool) $frontendItem['dropdown_config']['config_json']['dropdown']['show_arrow'], $previewItems[$index]['show_arrow']);
		}
	}

	protected function headerNavigationController(): Awesome_Admin_Header_Navigation_Controller
	{
		$account = Account::role('Super Admin')->first() ?? Account::role('Administrator')->first();

		if ( ! $account)
		{
			$this->markTestSkipped('An administrator account is required for the Awesome Admin layout test.');
		}

		$this->actingAs($account);

		return new Awesome_Admin_Header_Navigation_Controller($account);
	}
}
