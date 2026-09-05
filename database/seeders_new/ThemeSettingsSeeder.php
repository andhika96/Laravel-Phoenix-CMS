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
            'theme_id' => 7,
            'theme_code' => 'arunika_prism',
            'theme_name' => 'Arunika Prism'
        ]
        ]);
    }
}
