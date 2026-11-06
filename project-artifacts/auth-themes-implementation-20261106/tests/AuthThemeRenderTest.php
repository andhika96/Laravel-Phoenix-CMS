<?php

use App\Models\Awesome_Admin\Page_Theme_Setting;
use App\Support\AuthThemeCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class AuthThemeRenderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_new_auth_views_render_existing_form_contracts_and_escape_custom_text(): void
    {
        foreach (array_keys(AuthThemeCatalog::themes()) as $theme) {
            foreach (['login' => 'login', 'signup' => 'signup', 'forgotPassword' => 'forgotpassword', 'resetPassword' => 'resetpassword'] as $page => $file) {
                $view = 'auth.'.$theme.'.'.$file;
                $this->assertTrue(View::exists($view), 'Missing '.$view);
                $setting = Page_Theme_Setting::where('uri', $page)->firstOrFail();
                $setting->update(['page_theme' => $theme, 'page_theme_options' => [$theme => ['heading' => '<img src=x onerror=alert(1)>']]]);
                $html = view($view, ['data' => ['recovery_code' => 'non-submittable-qa-fixture']])->render();
                $this->assertStringContainsString('id="ph-app-auth"', $html);
                $this->assertStringContainsString('data-modern-theme="'.$theme.'"', $html);
                $this->assertStringContainsString('&lt;img src=x', $html);
                $this->assertStringNotContainsString('<img src=x onerror=', $html);
                $this->assertStringNotContainsString('css/cms/', $html);
                if ($page === 'resetPassword') {
                    $this->assertStringContainsString('name="password_confirmation"', $html);
                    $this->assertStringContainsString('non-submittable-qa-fixture', $html);
                }
            }
        }
    }
}
