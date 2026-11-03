<?php
use App\Models\Awesome_Admin\Page_Theme_Setting;
use App\Models\Awesome_Admin\Theme_Settings;
use App\Http\Controllers\Web\Awesome_Admin\Awesome_Admin_Appearance_Controller;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;

final class AuthCmsIsolationTest extends Tests\TestCase
{
    use DatabaseTransactions;

    public function test_saving_auth_primary_does_not_modify_cms_theme_settings(): void
    {
        $cmsBefore=Theme_Settings::get()->toArray();
        $request=Request::create('/awesome_admin/appearance/update','POST',[
            'interface_theme'=>'{}',
            'interface_theme_options'=>'{}',
            'interface_primary_colors'=>'{"login":"#FFFF00","signup":"#000000","forgotPassword":"#C7CCD8","resetPassword":"#112233"}',
        ]);
        $request->headers->set('Accept','application/json');
        $controller=(new ReflectionClass(Awesome_Admin_Appearance_Controller::class))->newInstanceWithoutConstructor();
        $this->assertSame(200,$controller->update($request)->status());
        $this->assertSame('#FFFF00',Page_Theme_Setting::where('uri','login')->value('page_primary_color'));
        $this->assertSame($cmsBefore,Theme_Settings::get()->toArray());
    }
}
