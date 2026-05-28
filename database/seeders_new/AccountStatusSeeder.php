<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('account_status')->insert([
        [
            'id' => 1,
            'name' => 'Active',
            'code_name' => 'active',
            'class_name' => 'badge text-bg-success',
            'is_active' => 0
        ],
        [
            'id' => 2,
            'name' => 'Not Active',
            'code_name' => 'not-active',
            'class_name' => 'badge text-bg-secondary',
            'is_active' => 0
        ],
        [
            'id' => 3,
            'name' => 'Suspended',
            'code_name' => 'suspended',
            'class_name' => 'badge text-bg-danger',
            'is_active' => 0
        ],
        [
            'id' => 4,
            'name' => 'Suspended Temporarily',
            'code_name' => 'suspended-temporarily',
            'class_name' => 'badge text-bg-warning',
            'is_active' => 0
        ]
        ]);
    }
}
