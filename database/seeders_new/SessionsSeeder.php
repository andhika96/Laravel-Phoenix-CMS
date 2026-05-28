<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sessions')->insert([

        ]);
    }
}
