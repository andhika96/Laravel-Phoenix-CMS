<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
        [
            'id' => 1,
            'name' => 'Super Admin',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:23:02',
            'updated_at' => '2025-01-24 01:23:02'
        ],
        [
            'id' => 2,
            'name' => 'Administrator',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:23:09',
            'updated_at' => '2025-01-24 01:23:09'
        ],
        [
            'id' => 3,
            'name' => 'General Member',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:23:31',
            'updated_at' => '2025-01-24 01:23:31'
        ],
        [
            'id' => 4,
            'name' => 'Premium Member',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:23:38',
            'updated_at' => '2025-01-24 01:23:38'
        ],
        [
            'id' => 26,
            'name' => 'Sales',
            'guard_name' => 'web',
            'created_at' => '2026-01-08 08:57:08',
            'updated_at' => '2026-01-08 08:57:08'
        ]
        ]);
    }
}
