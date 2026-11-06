<?php
use App\Support\AuthThemeCatalog;
use App\Models\Awesome_Admin\Page_Theme_Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthNoticeRefinementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_all_auth_views_use_the_cms_notice_contract(): void
    {
        foreach ([...AuthThemeCatalog::LEGACY,...array_keys(AuthThemeCatalog::themes())] as $theme) {
            foreach (['login','signup','forgotpassword','resetpassword'] as $page) {
                $html=view('auth.'.$theme.'.'.$page,['data'=>['recovery_code'=>'non-submittable-qa-fixture']])->render();
                $this->assertStringContainsString('ph-callout-no-border', $html, $theme.'/'.$page);
                $this->assertStringContainsString('toast-header-title toast-header-icon me-auto', $html, $theme.'/'.$page);
                $this->assertStringContainsString('just now', $html);
                $this->assertStringContainsString('data-bs-delay="3000"', $html);
                $this->assertStringContainsString('<teleport to="body">', $html);
                $this->assertStringContainsString('ref="noticeToast"', $html);
            }
        }
    }

    public function test_midnight_defaults_have_demo_content_but_keep_explicit_empty_values(): void
    {
        $setting=new Page_Theme_Setting();
        $defaults=AuthThemeCatalog::options('midnight_story','login',$setting);
        $this->assertGreaterThanOrEqual(2,count($defaults['testimonials']));
        $this->assertCount(3,$defaults['avatars']);
        foreach($defaults['avatars'] as $avatar) {
            $this->assertTrue(AuthThemeCatalog::validImagePath($avatar));
            $this->assertFileExists(public_path($avatar));
        }
        $setting->page_theme_options=['midnight_story'=>['testimonials'=>[],'avatars'=>[]]];
        $options=AuthThemeCatalog::options('midnight_story','login',$setting);
        $this->assertSame([],$options['testimonials']);
        $this->assertSame([],$options['avatars']);
    }

    public function test_default_avatars_can_be_removed_individually_without_deleting_shared_icons(): void
    {
        $this->app['env']='testing';
        config(['session.driver'=>'array','cache.default'=>'array']);
        $this->actingAs(\App\Models\Awesome_Admin\Account::whereHas('roles',fn($query)=>$query->whereIn('name',['Super Admin','Administrator']))->firstOrFail());
        $setting=Page_Theme_Setting::where('uri','login')->firstOrFail();
        $options=$setting->page_theme_options??[];
        unset($options['midnight_story']['avatars']);
        $setting->update(['page_theme_options'=>$options]);
        $avatars=AuthThemeCatalog::options('midnight_story','login',$setting)['avatars'];
        foreach($avatars as $index=>$avatar) {
            $this->deleteJson('/awesome_admin/appearance/image',['page'=>'login','theme'=>'midnight_story','slot'=>'avatars','path'=>$avatar])->assertOk()->assertJsonCount(2-$index,'paths');
            $this->assertFileExists(public_path($avatar));
        }
        $this->assertSame([],AuthThemeCatalog::options('midnight_story','login',$setting->fresh())['avatars']);
    }

    public function test_appearance_reuses_the_cms_notice_instead_of_a_separate_save_message(): void
    {
        $this->actingAs(\App\Models\Awesome_Admin\Account::whereHas('roles',fn($query)=>$query->whereIn('name',['Super Admin','Administrator']))->firstOrFail());
        $response=$this->get('/awesome_admin/appearance')->assertOk();
        $response->assertSee('toast-header-title toast-header-icon me-auto',false)->assertSee('ref="noticeToast"',false);
        $response->assertDontSee('v-if="saveMessage"',false);
    }
}
