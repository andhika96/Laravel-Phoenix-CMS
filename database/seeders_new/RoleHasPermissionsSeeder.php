<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('role_has_permissions')->insert([
        [
            'permission_id' => 1,
            'role_id' => 1
        ],
        [
            'permission_id' => 2,
            'role_id' => 1
        ],
        [
            'permission_id' => 3,
            'role_id' => 1
        ],
        [
            'permission_id' => 4,
            'role_id' => 1
        ],
        [
            'permission_id' => 1,
            'role_id' => 2
        ],
        [
            'permission_id' => 2,
            'role_id' => 2
        ],
        [
            'permission_id' => 3,
            'role_id' => 2
        ],
        [
            'permission_id' => 4,
            'role_id' => 2
        ],
        [
            'permission_id' => 1,
            'role_id' => 3
        ],
        [
            'permission_id' => 2,
            'role_id' => 3
        ],
        [
            'permission_id' => 3,
            'role_id' => 3
        ],
        [
            'permission_id' => 4,
            'role_id' => 3
        ],
        [
            'permission_id' => 1,
            'role_id' => 4
        ],
        [
            'permission_id' => 2,
            'role_id' => 4
        ],
        [
            'permission_id' => 3,
            'role_id' => 4
        ],
        [
            'permission_id' => 4,
            'role_id' => 4
        ]
        ]);
    }
}
