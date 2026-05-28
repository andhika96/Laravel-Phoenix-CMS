<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategorymenuJsonSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_categorymenu_json')->insert([
        [
            'id' => 1,
            'menu_page' => 'awesome_admin',
            'menu_vars' => '[{\\"category_code\\": \\"uIxTa0lV3L4EaV9A6BvJ7x\\", \\"category_name\\": \\"All Menus\\", \\"category_roles\\": \\"\\"}]',
            'menu_vars_backup' => '[{\\"category_code\\": \\"uIxTa0lV3L4EaV9A6BvJ7x\\", \\"category_name\\": \\"All Menus\\", \\"category_roles\\": \\"\\"}]'
        ]
        ]);
    }
}
