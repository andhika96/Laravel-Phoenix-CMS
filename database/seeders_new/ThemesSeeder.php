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
            'id' => 6,
            'theme_code' => 'arunika_aurora',
            'theme_name' => 'Arunika Aurora',
            'theme_foldername' => 'arunika_aurora',
            'theme_cms' => 'cms_layout',
            'theme_auth' => 'auth_layout',
            'theme_frontend' => 'frontend_layout',
            'theme_version' => '1.0.0',
            'updated_at' => '2025-06-18 03:54:21',
            'created_at' => '2025-06-18 03:54:21'
		],
		[
			'id' => 7,
			'theme_code' => 'arunika_prism',
			'theme_name' => 'Arunika Prism',
			'theme_foldername' => 'arunika_prism',
			'theme_cms' => 'cms_layout',
			'theme_auth' => 'auth_layout',
			'theme_frontend' => 'frontend_layout',
			'theme_version' => '1.0.0',
			'updated_at' => '2026-07-16 00:00:00',
			'created_at' => '2026-07-16 00:00:00'
		],
		[
			'id' => 8,
			'theme_code' => 'arunika_equinox',
			'theme_name' => 'Arunika Equinox',
			'theme_foldername' => 'arunika_equinox',
			'theme_cms' => 'cms_layout',
			'theme_auth' => 'auth_layout',
			'theme_frontend' => 'frontend_layout',
			'theme_version' => '1.0.0',
			'updated_at' => '2026-07-18 00:00:00',
			'created_at' => '2026-07-18 00:00:00'
		],
		[
			'id' => 9,
			'theme_code' => 'arunika_lucent',
			'theme_name' => 'Arunika Lucent',
			'theme_foldername' => 'arunika_lucent',
			'theme_cms' => 'cms_layout',
			'theme_auth' => 'auth_layout',
			'theme_frontend' => 'frontend_layout',
			'theme_version' => '1.0.0',
			'updated_at' => '2026-09-01 00:00:00',
			'created_at' => '2026-09-01 00:00:00'
		]
		]);
    }
}
