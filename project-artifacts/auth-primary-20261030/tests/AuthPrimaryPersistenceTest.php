<?php

use App\Http\Controllers\Web\Awesome_Admin\Awesome_Admin_Appearance_Controller;
use App\Models\Awesome_Admin\Page_Theme_Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;

final class AuthPrimaryPersistenceTest extends Tests\TestCase
{
    use DatabaseTransactions;

    private function saveColor(?string $colors = null)
    {
        $request = Request::create('/awesome_admin/appearance/update', 'POST', array_filter([
            'interface_theme' => '{}',
            'interface_theme_options' => '{}',
            'interface_primary_colors' => $colors,
        ], fn ($value) => $value !== null));
        $request->headers->set('Accept', 'application/json');
        return (new ReflectionClass(Awesome_Admin_Appearance_Controller::class))->newInstanceWithoutConstructor()->update($request);
    }

    public function test_color_save_keeps_layout_background_and_nuances(): void
    {
        $before = Page_Theme_Setting::where('uri', 'login')->first()->toArray();
        $response = $this->saveColor('{"login":"#abcdef","signup":"#112233","forgotPassword":"#FFFFFF","resetPassword":"#000000"}');
        $this->assertSame(200, $response->status());
        $after = Page_Theme_Setting::where('uri', 'login')->first()->toArray();
        $this->assertSame('#ABCDEF', $after['page_primary_color']);
        foreach (['page_theme', 'page_background_image', 'page_color_nuances'] as $column) {
            $this->assertSame($before[$column], $after[$column]);
        }
        $this->assertSame('#000000', Page_Theme_Setting::where('uri', 'resetPassword')->value('page_primary_color'));
    }

    public function test_invalid_values_and_unknown_pages_do_not_write(): void
    {
        $before = Page_Theme_Setting::get()->toArray();
        foreach (['{"login":"red; color:white"}', '{"cms":"#112233"}', '{"login":"#11223344"}', '{"login":null}', 'invalid', 'null'] as $json) {
            $this->assertSame(422, $this->saveColor($json)->status());
            $this->assertSame($before, Page_Theme_Setting::get()->toArray());
        }
    }

    public function test_older_save_without_primary_preserves_colors(): void
    {
        $before = Page_Theme_Setting::get()->toArray();
        $this->assertSame(200, $this->saveColor()->status());
        $this->assertSame($before, Page_Theme_Setting::get()->toArray());
    }
}
