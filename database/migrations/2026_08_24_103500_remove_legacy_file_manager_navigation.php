<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LINKS = ['filemanager', 'file_manager', 'awesome_admin/filemanager'];

    public function up(): void
    {
        if (Schema::hasTable('custom_permissions')) {
            DB::table('custom_permissions')->whereIn('menu_link', self::LINKS)->delete();
        }

        $this->rewriteMenus(static fn (array $items): array => array_values(array_filter(
            $items,
            static fn (array $item): bool => ! in_array($item['parent_link'] ?? null, self::LINKS, true),
        )));
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_permissions')) {
            DB::table('custom_permissions')->insertOrIgnore([
                $this->permission(5, 1, ['read data', 'add data', 'edit data', 'delete data'], '2025-10-22 04:17:10'),
                $this->permission(13, 2, ['read data', 'add data', 'edit data', 'delete data'], '2025-10-22 04:19:32'),
                $this->permission(21, 3, null, '2025-10-22 04:19:45'),
                $this->permission(32, 26, ['read data'], '2026-01-08 08:57:08'),
            ]);
        }

        $legacy = [
            'parent_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'parent_icon' => '',
            'parent_link' => 'filemanager',
            'parent_name' => 'File Manager',
            'parent_type' => 'custom',
            'parent_roles' => ['Super Admin', 'Administrator', 'General Member'],
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_icon_url' => '',
            'parent_icon_path' => '',
            'parent_icon_type' => 'custom_input',
            'is_for_parent_menu' => 'single',
            'parent_icon_custom' => '<i class="fal fa-file-alt fa-fw"></i>',
            'parent_permissions' => '',
        ];

        $this->rewriteMenus(static function (array $items) use ($legacy): array {
            if (! collect($items)->contains('parent_link', 'filemanager')) {
                $items[] = $legacy;
            }

            return $items;
        });
    }

    private function permission(int $id, int $roleId, ?array $permissions, string $timestamp): array
    {
        return [
            'id' => $id,
            'role_id' => $roleId,
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_type' => 'single',
            'menu_code' => 'dqV84cjEjCrmp0BWF0fxpn',
            'menu_name' => 'File Manager',
            'menu_link' => 'filemanager',
            'permissions' => $permissions === null ? null : json_encode($permissions, JSON_THROW_ON_ERROR),
            'updated_at' => $timestamp,
            'created_at' => $timestamp,
        ];
    }

    private function rewriteMenus(callable $rewrite): void
    {
        if (! Schema::hasTable('menu_parentmenu_json')) {
            return;
        }

        DB::table('menu_parentmenu_json')
            ->select(['id', 'menu_vars', 'menu_vars_backup'])
            ->orderBy('id')
            ->each(function (object $row) use ($rewrite): void {
                $updates = [];
                foreach (['menu_vars', 'menu_vars_backup'] as $column) {
                    $items = json_decode((string) ($row->{$column} ?? '[]'), true, 512, JSON_THROW_ON_ERROR);
                    $updates[$column] = json_encode($rewrite(is_array($items) ? $items : []), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                }

                DB::table('menu_parentmenu_json')->where('id', $row->id)->update($updates);
            });
    }
};
