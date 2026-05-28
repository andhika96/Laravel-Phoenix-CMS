<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FmSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fm_settings')->insert([
        [
            'id' => 1,
            'key' => 'global_max_storage',
            'value' => '10737418240',
            'description' => 'Default max total storage per user (bytes). -1 = unlimited',
            'created_at' => '2026-03-27 20:19:02',
            'updated_at' => '2026-03-27 20:19:02'
        ],
        [
            'id' => 2,
            'key' => 'global_max_file_size',
            'value' => '104857600',
            'description' => 'Default max single file upload size (bytes). -1 = unlimited',
            'created_at' => '2026-03-27 20:19:02',
            'updated_at' => '2026-03-27 20:19:02'
        ],
        [
            'id' => 3,
            'key' => 'allowed_mime_types',
            'value' => null,
            'description' => 'JSON array of allowed MIME types. NULL = all allowed',
            'created_at' => '2026-03-27 20:19:02',
            'updated_at' => '2026-03-27 20:19:02'
        ],
        [
            'id' => 4,
            'key' => 'default_disk',
            'value' => 'public',
            'description' => 'Default storage disk: local | public | s3',
            'created_at' => '2026-03-27 20:19:02',
            'updated_at' => '2026-03-27 20:19:02'
        ]
        ]);
    }
}
