<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthenticationLogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('authentication_log')->insert([

        ]);
    }
}
