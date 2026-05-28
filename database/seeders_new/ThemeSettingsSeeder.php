<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('theme_settings')->insert([
        [
            'id' => 1,
            'theme_id' => 5,
            'theme_code' => 'arunika_v1',
            'theme_name' => 'Arunika v1 Theme'
        ]
        ]);
    }
}
