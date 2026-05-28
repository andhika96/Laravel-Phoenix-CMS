<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageThemesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('page_themes')->insert([
        [
            'id' => 1,
            'uri' => 'login',
            'theme_group' => 'auth',
            'theme_code' => 'default',
            'theme_name' => 'Default',
            'theme_preview_image' => 'storage/page_themes/auth/default/default_login_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 2,
            'uri' => 'signup',
            'theme_group' => 'auth',
            'theme_code' => 'default',
            'theme_name' => 'Default',
            'theme_preview_image' => 'storage/page_themes/auth/default/default_signup_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 3,
            'uri' => 'forgotpassword',
            'theme_group' => 'auth',
            'theme_code' => 'default',
            'theme_name' => 'Default',
            'theme_preview_image' => 'storage/page_themes/auth/default/default_forgotpassword_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 4,
            'uri' => 'resetpassword',
            'theme_group' => 'auth',
            'theme_code' => 'default',
            'theme_name' => 'Default',
            'theme_preview_image' => 'storage/page_themes/auth/default/default_recoverypassword_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 5,
            'uri' => 'login',
            'theme_group' => 'auth',
            'theme_code' => 'split_left',
            'theme_name' => 'Split Left View',
            'theme_preview_image' => 'storage/page_themes/auth/split_left/split_left_login_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 6,
            'uri' => 'signup',
            'theme_group' => 'auth',
            'theme_code' => 'split_left',
            'theme_name' => 'Split Left View',
            'theme_preview_image' => 'storage/page_themes/auth/split_left/split_left_signup_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 7,
            'uri' => 'forgotpassword',
            'theme_group' => 'auth',
            'theme_code' => 'split_left',
            'theme_name' => 'Split Left View',
            'theme_preview_image' => 'storage/page_themes/auth/split_left/split_left_forgotpassword_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 8,
            'uri' => 'resetpassword',
            'theme_group' => 'auth',
            'theme_code' => 'split_left',
            'theme_name' => 'Split Left View',
            'theme_preview_image' => 'storage/page_themes/auth/split_left/split_left_recoverypassword_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 9,
            'uri' => 'login',
            'theme_group' => 'auth',
            'theme_code' => 'split_right',
            'theme_name' => 'Split Right View',
            'theme_preview_image' => 'storage/page_themes/auth/split_right/split_right_login_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 10,
            'uri' => 'signup',
            'theme_group' => 'auth',
            'theme_code' => 'split_right',
            'theme_name' => 'Split Right View',
            'theme_preview_image' => 'storage/page_themes/auth/split_right/split_right_signup_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 11,
            'uri' => 'forgotpassword',
            'theme_group' => 'auth',
            'theme_code' => 'split_right',
            'theme_name' => 'Split Right View',
            'theme_preview_image' => 'storage/page_themes/auth/split_right/split_right_forgotpassword_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 12,
            'uri' => 'resetpassword',
            'theme_group' => 'auth',
            'theme_code' => 'split_right',
            'theme_name' => 'Split Right View',
            'theme_preview_image' => 'storage/page_themes/auth/split_right/split_right_recoverypassword_view.png',
            'is_active_color_nuances' => 1,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 13,
            'uri' => 'login',
            'theme_group' => 'auth',
            'theme_code' => 'card',
            'theme_name' => 'Card View',
            'theme_preview_image' => 'storage/page_themes/auth/card/card_login_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 14,
            'uri' => 'signup',
            'theme_group' => 'auth',
            'theme_code' => 'card',
            'theme_name' => 'Card View',
            'theme_preview_image' => 'storage/page_themes/auth/card/card_signup_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 15,
            'uri' => 'forgotpassword',
            'theme_group' => 'auth',
            'theme_code' => 'card',
            'theme_name' => 'Card View',
            'theme_preview_image' => 'storage/page_themes/auth/card/card_forgotpassword_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ],
        [
            'id' => 16,
            'uri' => 'resetpassword',
            'theme_group' => 'auth',
            'theme_code' => 'card',
            'theme_name' => 'Card View',
            'theme_preview_image' => 'storage/page_themes/auth/card/card_recoverypassword_view.png',
            'is_active_color_nuances' => 0,
            'updated_at' => '2025-07-21 19:35:27',
            'created_at' => '2025-07-21 19:35:27'
        ]
        ]);
    }
}
