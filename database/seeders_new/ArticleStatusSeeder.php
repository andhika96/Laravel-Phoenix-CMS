<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('article_status')->insert([
        [
            'id' => 1,
            'name' => 'Publish',
            'code_name' => 'publish',
            'class_name' => 'badge text-bg-success',
            'is_active' => 0
        ],
        [
            'id' => 2,
            'name' => 'Draft',
            'code_name' => 'draft',
            'class_name' => 'badge text-bg-secondary',
            'is_active' => 0
        ],
        [
            'id' => 3,
            'name' => 'Pending',
            'code_name' => 'pending',
            'class_name' => 'badge text-bg-info',
            'is_active' => 0
        ],
        [
            'id' => 4,
            'name' => 'Scheduled',
            'code_name' => 'scheduled',
            'class_name' => 'badge text-bg-warning',
            'is_active' => 1
        ]
        ]);
    }
}
