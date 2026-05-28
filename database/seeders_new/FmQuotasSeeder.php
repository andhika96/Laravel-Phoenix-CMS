<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FmQuotasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fm_quotas')->insert([
        [
            'id' => 1,
            'user_id' => 1,
            'max_storage' => null,
            'max_file_size' => null,
            'used_storage' => 45378404,
            'created_at' => '2026-03-27 21:26:59',
            'updated_at' => '2026-03-28 12:18:42'
        ]
        ]);
    }
}
