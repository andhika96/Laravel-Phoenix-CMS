<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FmFoldersSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fm_folders')->insert([
        [
            'id' => 1,
            'parent_id' => null,
            'user_id' => 1,
            'name' => 'Testing',
            'slug' => 'testing',
            'path' => 'filemanager/testing',
            'disk' => 's3',
            'is_public' => 1,
            'created_at' => '2026-03-27 21:30:16',
            'updated_at' => '2026-03-27 21:30:16',
            'deleted_at' => null
        ],
        [
            'id' => 2,
            'parent_id' => null,
            'user_id' => 1,
            'name' => 'Kwkwkwkwk',
            'slug' => 'kwkwkwkwk',
            'path' => 'filemanager/kwkwkwkwk',
            'disk' => 'public',
            'is_public' => 1,
            'created_at' => '2026-03-27 22:07:18',
            'updated_at' => '2026-03-27 22:07:18',
            'deleted_at' => null
        ]
        ]);
    }
}
