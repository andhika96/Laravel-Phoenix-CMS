<?php

use App\Support\AuthThemeCatalog;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AuthThemeCatalogTest extends TestCase
{
    public function test_catalog_keeps_legacy_and_adds_four_reviewed_themes(): void
    {
        $this->assertTrue(class_exists(AuthThemeCatalog::class), 'The auth theme catalog is missing.');
        $this->assertSame(['clean_peach', 'ocean_split', 'midnight_story', 'verdant_split'], array_keys(AuthThemeCatalog::themes()));
        foreach (['default', 'card', 'split_left', 'split_right', ...array_keys(AuthThemeCatalog::themes())] as $theme) {
            $this->assertTrue(AuthThemeCatalog::supports($theme));
        }
        $this->assertFalse(AuthThemeCatalog::supports('../../outside'));
    }

    public function test_invalid_options_are_rejected_and_defaults_are_page_specific(): void
    {
        $this->assertTrue(class_exists(AuthThemeCatalog::class), 'The auth theme catalog is missing.');
        $this->assertFalse(Validator::make(['control_radius' => 200], AuthThemeCatalog::rules('clean_peach'))->passes());
        $this->assertFalse(Validator::make(['primary_color' => 'red; background:url(https://example.test)'], AuthThemeCatalog::rules('clean_peach'))->passes());
        $this->assertFalse(Validator::make(['panel_side' => 'outside'], AuthThemeCatalog::rules('ocean_split'))->passes());
        $this->assertNotSame(AuthThemeCatalog::defaults('clean_peach', 'login')['heading'], AuthThemeCatalog::defaults('clean_peach', 'resetPassword')['heading']);
    }

    public function test_image_paths_cannot_be_saved_through_regular_options(): void
    {
        $this->assertTrue(class_exists(AuthThemeCatalog::class), 'The auth theme catalog is missing.');
        $this->assertFalse(Validator::make(['illustration_image' => '../../private.jpg'], AuthThemeCatalog::rules('clean_peach'))->passes());
        $this->assertSame(['background_image'], AuthThemeCatalog::imageSlots('default'));
        $this->assertContains('avatars', AuthThemeCatalog::imageSlots('midnight_story'));
    }

    public function test_built_in_images_are_defaults_but_explicit_removal_stays_empty(): void
    {
        $setting = new \App\Models\Awesome_Admin\Page_Theme_Setting();
        $default = AuthThemeCatalog::options('clean_peach', 'login', $setting)['illustration_image'];
        $this->assertNotNull($default);
        $this->assertTrue(AuthThemeCatalog::validImagePath($default));
        $this->assertFileExists(public_path($default));
        $setting->page_theme_options = ['clean_peach' => ['illustration_image' => null]];
        $this->assertNull(AuthThemeCatalog::options('clean_peach', 'login', $setting)['illustration_image']);
    }
}
