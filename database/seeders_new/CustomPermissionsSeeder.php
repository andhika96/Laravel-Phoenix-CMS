<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('custom_permissions')->insert([
        [
            'id' => 1,
            'role_id' => 1,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_type' => 'single',
            'menu_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_name' => 'Manage Articles',
            'menu_link' => 'manage_article',
            'permissions' => '[\\"read data\\", \\"add data\\", \\"edit data\\", \\"delete data\\"]',
            'updated_at' => '2025-10-22 04:17:10',
            'created_at' => '2025-10-22 04:17:10'
        ],
        [
            'id' => 4,
            'role_id' => 1,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_type' => 'single',
            'menu_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_name' => 'Manage Cover Image',
            'menu_link' => 'manage_coverimage',
            'permissions' => '[\\"read data\\", \\"add data\\", \\"edit data\\", \\"delete data\\"]',
            'updated_at' => '2025-10-22 04:17:10',
            'created_at' => '2025-10-22 04:17:10'
        ],
        [
            'id' => 5,
            'role_id' => 1,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_type' => 'single',
            'menu_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_name' => 'File Manager',
            'menu_link' => 'filemanager',
            'permissions' => '[\\"read data\\", \\"add data\\", \\"edit data\\", \\"delete data\\"]',
            'updated_at' => '2025-10-22 04:17:10',
            'created_at' => '2025-10-22 04:17:10'
        ],
        [
            'id' => 9,
            'role_id' => 2,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_type' => 'single',
            'menu_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_name' => 'Manage Articles',
            'menu_link' => 'manage_article',
            'permissions' => '[\\"read data\\", \\"add data\\", \\"edit data\\", \\"delete data\\"]',
            'updated_at' => '2025-10-22 04:19:32',
            'created_at' => '2025-10-22 04:19:32'
        ],
        [
            'id' => 12,
            'role_id' => 2,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_type' => 'single',
            'menu_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_name' => 'Manage Cover Image',
            'menu_link' => 'manage_coverimage',
            'permissions' => '[\\"read data\\", \\"add data\\", \\"edit data\\", \\"delete data\\"]',
            'updated_at' => '2025-10-22 04:19:32',
            'created_at' => '2025-10-22 04:19:32'
        ],
        [
            'id' => 13,
            'role_id' => 2,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_type' => 'single',
            'menu_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_name' => 'File Manager',
            'menu_link' => 'filemanager',
            'permissions' => '[\\"read data\\", \\"add data\\", \\"edit data\\", \\"delete data\\"]',
            'updated_at' => '2025-10-22 04:19:32',
            'created_at' => '2025-10-22 04:19:32'
        ],
        [
            'id' => 17,
            'role_id' => 3,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_type' => 'single',
            'menu_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_name' => 'Manage Articles',
            'menu_link' => 'manage_article',
            'permissions' => null,
            'updated_at' => '2025-10-22 04:19:45',
            'created_at' => '2025-10-22 04:19:45'
        ],
        [
            'id' => 20,
            'role_id' => 3,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_type' => 'single',
            'menu_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_name' => 'Manage Cover Image',
            'menu_link' => 'manage_coverimage',
            'permissions' => null,
            'updated_at' => '2025-10-22 04:19:45',
            'created_at' => '2025-10-22 04:19:45'
        ],
        [
            'id' => 21,
            'role_id' => 3,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_type' => 'single',
            'menu_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_name' => 'File Manager',
            'menu_link' => 'filemanager',
            'permissions' => null,
            'updated_at' => '2025-10-22 04:19:45',
            'created_at' => '2025-10-22 04:19:45'
        ],
        [
            'id' => 28,
            'role_id' => 26,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_type' => 'single',
            'menu_code' => 'hBMt85z8I4p3dgfZCt1sf4',
            'menu_name' => 'Manage Articles',
            'menu_link' => 'manage_article',
            'permissions' => '[\\"read data\\"]',
            'updated_at' => '2026-01-08 08:57:08',
            'created_at' => '2026-01-08 08:57:08'
        ],
        [
            'id' => 31,
            'role_id' => 26,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_type' => 'single',
            'menu_code' => 'ktGbOw0EloeZX73WIs50VO',
            'menu_name' => 'Manage Cover Image',
            'menu_link' => 'manage_coverimage',
            'permissions' => '[\\"read data\\"]',
            'updated_at' => '2026-01-08 08:57:08',
            'created_at' => '2026-01-08 08:57:08'
        ],
        [
            'id' => 32,
            'role_id' => 26,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_type' => 'single',
            'menu_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_name' => 'File Manager',
            'menu_link' => 'filemanager',
            'permissions' => '[\\"read data\\"]',
            'updated_at' => '2026-01-08 08:57:08',
            'created_at' => '2026-01-08 08:57:08'
        ]
        ]);
    }
}
