<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('custom_permissions')->insert([
            $this->permission(1, 1, 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', ['read data', 'add data', 'edit data', 'delete data'], '2025-10-22 04:17:10'),
            $this->permission(32, 1, 'evT7uJ4nM8pQ2xL5cR9sK1', 'Manage Events', 'manage_event', ['read data', 'add data', 'edit data', 'delete data'], '2026-08-24 21:00:00'),
            $this->permission(4, 1, 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', ['read data', 'add data', 'edit data', 'delete data'], '2025-10-22 04:17:10'),
            $this->permission(9, 2, 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', ['read data', 'add data', 'edit data', 'delete data'], '2025-10-22 04:19:32'),
            $this->permission(33, 2, 'evT7uJ4nM8pQ2xL5cR9sK1', 'Manage Events', 'manage_event', ['read data', 'add data', 'edit data', 'delete data'], '2026-08-24 21:00:00'),
            $this->permission(12, 2, 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', ['read data', 'add data', 'edit data', 'delete data'], '2025-10-22 04:19:32'),
            $this->permission(17, 3, 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', null, '2025-10-22 04:19:45'),
            $this->permission(34, 3, 'evT7uJ4nM8pQ2xL5cR9sK1', 'Manage Events', 'manage_event', null, '2026-08-24 21:00:00'),
            $this->permission(20, 3, 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', null, '2025-10-22 04:19:45'),
            $this->permission(28, 26, 'hBMt85z8I4p3dgfZCt1sf4', 'Manage Articles', 'manage_article', ['read data'], '2026-01-08 08:57:08'),
            $this->permission(35, 26, 'evT7uJ4nM8pQ2xL5cR9sK1', 'Manage Events', 'manage_event', ['read data'], '2026-08-24 21:00:00'),
            $this->permission(31, 26, 'ktGbOw0EloeZX73WIs50VO', 'Manage Cover Image', 'manage_coverimage', ['read data'], '2026-01-08 08:57:08'),
        ]);
    }

    private function permission(int $id, int $roleId, string $code, string $name, string $link, ?array $permissions, string $timestamp): array
    {
        return [
            'id' => $id,
            'role_id' => $roleId,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => $code,
            'menu_type' => 'single',
            'menu_code' => $code,
            'menu_name' => $name,
            'menu_link' => $link,
            'permissions' => $permissions === null ? null : json_encode($permissions, JSON_THROW_ON_ERROR),
            'updated_at' => $timestamp,
            'created_at' => $timestamp,
        ];
    }
}
