<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MENU_CODE = 'evT7uJ4nM8pQ2xL5cR9sK1';

    public function up(): void
    {
        $this->syncActiveParentMenu();
        $this->syncLegacyMenuLinks();
        $this->syncCustomPermissions();
    }

    public function down(): void
    {
        // Keep seeded menu and permission data intact on rollback; it may have been edited by an administrator.
    }

    private function syncActiveParentMenu(): void
    {
        if (! Schema::hasTable('menu_parentmenu_json')) {
            return;
        }

        $row = DB::table('menu_parentmenu_json')->where('id', 1)->first();
        if (! $row) {
            return;
        }

        $active = json_decode((string) $row->menu_vars, true) ?: [];
        $backup = json_decode((string) $row->menu_vars_backup, true) ?: $active;
        $menu = $this->parentMenuPayload();

        if (! collect($active)->contains(fn ($item) => ($item['parent_link'] ?? null) === 'manage_event')) {
            $active[] = $menu;
        }
        if (! collect($backup)->contains(fn ($item) => ($item['parent_link'] ?? null) === 'manage_event')) {
            $backup[] = $menu;
        }

        DB::table('menu_parentmenu_json')->where('id', 1)->update([
            'menu_vars' => json_encode($active, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            'menu_vars_backup' => json_encode($backup, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        ]);
    }

    private function syncLegacyMenuLinks(): void
    {
        if (! Schema::hasTable('menu')) {
            return;
        }

        DB::table('menu')->where('module', 'manage_event')->where('menu_name', 'Add New')->update(['url' => 'manage_event/add']);
        DB::table('menu')->where('module', 'manage_event')->where('menu_name', 'Event Categories')->update(['url' => 'manage_event?panel=categories']);
        DB::table('menu')->where('module', 'manage_event')->where('menu_name', 'Layout')->update(['status' => 1]);
    }

    private function syncCustomPermissions(): void
    {
        if (! Schema::hasTable('custom_permissions')) {
            return;
        }

        $roles = [
            1 => ['read data', 'add data', 'edit data', 'delete data'],
            2 => ['read data', 'add data', 'edit data', 'delete data'],
            3 => null,
            26 => ['read data'],
        ];

        foreach ($roles as $roleId => $permissions) {
            $payload = [
                'role_id' => $roleId,
                'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
                'parent_code' => self::MENU_CODE,
                'menu_type' => 'single',
                'menu_code' => self::MENU_CODE,
                'menu_name' => 'Manage Events',
                'menu_link' => 'manage_event',
                'permissions' => $permissions === null ? null : json_encode($permissions, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ];

            $existing = DB::table('custom_permissions')->where('role_id', $roleId)->where('menu_code', self::MENU_CODE)->first();
            if ($existing) {
                DB::table('custom_permissions')->where('id', $existing->id)->update($payload);
                continue;
            }

            $payload['id'] = ((int) DB::table('custom_permissions')->max('id')) + 1;
            $payload['created_at'] = now();
            DB::table('custom_permissions')->insert($payload);
        }
    }

    private function parentMenuPayload(): array
    {
        return [
            'parent_code' => self::MENU_CODE,
            'parent_icon' => '',
            'parent_link' => 'manage_event',
            'parent_name' => 'Manage Event',
            'parent_type' => 'custom',
            'parent_roles' => ['Administrator', 'Super Admin'],
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x',
            'parent_icon_url' => '',
            'parent_icon_path' => '',
            'parent_icon_type' => 'custom_input',
            'is_for_parent_menu' => 'single',
            'parent_icon_custom' => '<i class="fal fa-calendar-star fa-fw"></i>',
            'parent_permissions' => '',
        ];
    }
};
