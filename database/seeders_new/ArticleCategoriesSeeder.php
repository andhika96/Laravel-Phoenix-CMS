<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('article_categories')->insert([
        [
            'id' => 1,
            'name' => 'Uncategorized',
            'code' => 'uncategorized',
            'status' => 'active',
            'updated_at' => '2025-08-19 02:49:39',
            'created_at' => '2025-08-19 02:49:39'
        ]
        ]);
    }
}
