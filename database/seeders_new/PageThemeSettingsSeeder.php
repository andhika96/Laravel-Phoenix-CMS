<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('page_theme_settings')->insert([
        [
            'id' => 1,
            'uri' => 'login',
            'page_name' => 'Login',
            'page_theme' => 'default',
            'page_color_nuances' => '#1fa6759f',
            'page_background_image' => 'images/page_themes/082025/date_05/e1ad653cbc3c3da22f8270e6054e597a.jpg',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-24 08:58:41',
            'created_at' => '2025-07-24 08:58:41'
        ],
        [
            'id' => 2,
            'uri' => 'signup',
            'page_name' => 'Signup',
            'page_theme' => 'split_left',
            'page_color_nuances' => '#1fa6759f',
            'page_background_image' => 'images/page_themes/082025/date_05/535d6dc7141b8be51ae359f992d3813f.jpg',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-24 09:00:13',
            'created_at' => '2025-07-24 09:00:13'
        ],
        [
            'id' => 3,
            'uri' => 'forgotPassword',
            'page_name' => 'Forgot Password',
            'page_theme' => 'split_left',
            'page_color_nuances' => '#1fa6759f',
            'page_background_image' => 'images/page_themes/082025/date_05/9c00282725c642ebd5aeaf6cf06a75cf.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-24 09:00:13',
            'created_at' => '2025-07-24 09:00:13'
        ],
        [
            'id' => 4,
            'uri' => 'resetPassword',
            'page_name' => 'Reset Password',
            'page_theme' => 'split_left',
            'page_color_nuances' => '#1fa6759f',
            'page_background_image' => 'images/page_themes/082025/date_05/6dccdc76d026c511a093aaf710bc162b.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-24 09:00:13',
            'created_at' => '2025-07-24 09:00:13'
        ]
        ]);
    }
}
