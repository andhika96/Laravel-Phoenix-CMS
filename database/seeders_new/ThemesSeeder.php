<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('themes')->insert([
        [
            'id' => 1,
            'theme_code' => 'default',
            'theme_name' => 'Default Theme',
            'theme_foldername' => 'default',
            'theme_cms' => 'cms_layout',
            'theme_auth' => 'auth_layout',
            'theme_frontend' => 'frontend_layout',
            'theme_version' => '1.0.0',
            'updated_at' => '2025-06-18 03:54:21',
            'created_at' => '2025-06-18 03:54:21'
        ],
        [
            'id' => 2,
            'theme_code' => 'simple',
            'theme_name' => 'Simple Theme',
            'theme_foldername' => 'simple',
            'theme_cms' => 'cms_layout',
            'theme_auth' => 'auth_layout',
            'theme_frontend' => 'frontend_layout',
            'theme_version' => '1.0.0',
            'updated_at' => '2025-06-18 03:54:21',
            'created_at' => '2025-06-18 03:54:21'
        ],
        [
            'id' => 3,
            'theme_code' => 'simple_part_2',
            'theme_name' => 'Simple Part 2 Theme',
            'theme_foldername' => 'simple_part_2',
            'theme_cms' => 'cms_layout',
            'theme_auth' => 'auth_layout',
            'theme_frontend' => 'frontend_layout',
            'theme_version' => '1.0.0',
            'updated_at' => '2025-06-18 03:54:21',
            'created_at' => '2025-06-18 03:54:21'
        ],
        [
            'id' => 4,
            'theme_code' => 'calm_green',
            'theme_name' => 'Calm Green Theme',
            'theme_foldername' => 'calm_green',
            'theme_cms' => 'cms_layout',
            'theme_auth' => 'auth_layout',
            'theme_frontend' => 'frontend_layout',
            'theme_version' => '1.0.0',
            'updated_at' => '2025-06-18 03:54:21',
            'created_at' => '2025-06-18 03:54:21'
        ],
        [
            'id' => 5,
            'theme_code' => 'arunika_v1',
            'theme_name' => 'Arunika V1 Theme',
            'theme_foldername' => 'arunika_v1',
            'theme_cms' => 'cms_layout',
            'theme_auth' => 'auth_layout',
            'theme_frontend' => 'frontend_layout',
            'theme_version' => '1.0.0',
            'updated_at' => '2025-06-18 03:54:21',
            'created_at' => '2025-06-18 03:54:21'
        ]
        ]);
    }
}
