<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Awesome_Admin\Awesome_Admin_Header_Navigation_Controller;
use App\Http\Requests\Awesome_Admin\HeaderNavigationRequest;
use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Header_Navigation_Setting;
use App\Support\FrontendHeaderNavigationConfigNormalizer;

use Illuminate\Foundation\Testing\DatabaseTransactions;

use Tests\TestCase;

class HeaderNavigationSettingsTest extends TestCase
{
	use DatabaseTransactions;

	public function test_admin_controller_renders_and_persists_header_navigation_settings(): void
	{
		$account = Account::role('Super Admin')->first() ?? Account::role('Administrator')->first();

		if ( ! $account)
		{
			$this->markTestSkipped('An administrator account is required for the Awesome Admin layout test.');
		}

		$this->actingAs($account);
		$controller = new Awesome_Admin_Header_Navigation_Controller($account);
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
}
