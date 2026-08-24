<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuParentmenuJsonSeeder extends Seeder
{
    public function run(): void
    {
        $active = [
            $this->menu('hBMt85z8I4p3dgfZCt1sf4', 'manage_article', 'Manage Articles', ['Administrator', 'Super Admin'], 'uIxTa0lV3L4EaV9A6BvJ7x', '<i class="fal fa-newspaper fa-fw"></i>'),
            $this->menu('ktGbOw0EloeZX73WIs50VO', 'manage_coverimage', 'Manage Cover Image', [], 'uIxTa0lV3L4EaV9A6BvJ7x', '<i class="fal fa-puzzle-piece fa-fw"></i>'),
        ];

        $backup = [
            ...$active,
            $this->menu('nT21heY6KH8npGso0DM6yl', 'account', 'Accounts', ['Super Admin']),
        ];

        DB::table('menu_parentmenu_json')->insert([
            'id' => 1,
            'menu_page' => 'awesome_admin',
            'menu_vars' => json_encode($active, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            'menu_vars_backup' => json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        ]);
    }

    private function menu(string $code, string $link, string $name, array $roles, string $category = '', string $icon = ''): array
    {
        return [
            'parent_code' => $code,
            'parent_icon' => '',
            'parent_link' => $link,
            'parent_name' => $name,
            'parent_type' => 'custom',
            'parent_roles' => $roles,
            'category_code' => $category,
            'parent_icon_url' => '',
            'parent_icon_path' => '',
            'parent_icon_type' => $icon === '' ? '' : 'custom_input',
            'is_for_parent_menu' => 'single',
            'parent_icon_custom' => $icon,
            'parent_permissions' => '',
        ];
    }
}
