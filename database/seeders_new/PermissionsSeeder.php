<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->insert([
        [
            'id' => 1,
            'name' => 'read data',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:22:30',
            'updated_at' => '2025-05-07 23:36:54'
        ],
        [
            'id' => 2,
            'name' => 'add data',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:22:35',
            'updated_at' => '2025-01-24 01:22:35'
        ],
        [
            'id' => 3,
            'name' => 'edit data',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:22:38',
            'updated_at' => '2025-01-24 01:22:38'
        ],
        [
            'id' => 4,
            'name' => 'delete data',
            'guard_name' => 'web',
            'created_at' => '2025-01-24 01:22:42',
            'updated_at' => '2025-01-24 01:22:42'
        ]
        ]);
    }
}
