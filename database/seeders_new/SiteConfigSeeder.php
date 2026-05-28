<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('site_config')->insert([

        ]);
    }
}
