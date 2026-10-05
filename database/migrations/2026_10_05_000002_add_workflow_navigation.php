<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CODE = 'wfEditor20261005Phoenix';

    public function up(): void
    {
        DB::transaction(function (): void {
            if (Schema::hasTable('menu_parentmenu_json')) {
                $row = DB::table('menu_parentmenu_json')->where('menu_page', 'awesome_admin')->lockForUpdate()->first();
                if ($row) {
                    $updates = [];
                    foreach (['menu_vars', 'menu_vars_backup'] as $column) {
                        $items = json_decode($row->$column ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                        if (! is_array($items)) { throw new RuntimeException('Navigation JSON must be an array.'); }
                        if (! collect($items)->contains(fn ($item) => ($item['parent_link'] ?? null) === 'workflow')) {
                            $items[] = $this->defaultMenu();
                        }
                        $updates[$column] = json_encode($items, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                    }
                    DB::table('menu_parentmenu_json')->where('id', $row->id)->update($updates);
                }
            }
            if (Schema::hasTable('custom_permissions') && Schema::hasTable('roles')) {
                foreach (DB::table('roles')->whereIn('name', ['Administrator', 'Super Admin'])->get() as $role) {
                    if (! DB::table('custom_permissions')->where('role_id', $role->id)->where('menu_link', 'workflow')->exists()) {
                        DB::table('custom_permissions')->insert([
                            'id' => ((int) DB::table('custom_permissions')->max('id')) + 1, 'role_id' => $role->id,
                            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x', 'parent_code' => self::CODE, 'menu_code' => self::CODE, 'menu_type' => 'single',
                            'menu_name' => 'Workflow Editor', 'menu_link' => 'workflow',
                            'permissions' => json_encode(['read data', 'add data', 'edit data', 'delete data'], JSON_THROW_ON_ERROR), 'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Remove only our untouched defaults; retain rows customized by administrators.
        DB::transaction(function (): void {
            if (Schema::hasTable('menu_parentmenu_json')) {
                foreach (DB::table('menu_parentmenu_json')->where('menu_page', 'awesome_admin')->lockForUpdate()->get() as $row) {
                    $updates = [];
                    foreach (['menu_vars', 'menu_vars_backup'] as $column) {
                        $items = json_decode($row->$column ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                        $items = array_values(array_filter($items, fn ($item) => $item !== $this->defaultMenu()));
                        $updates[$column] = json_encode($items, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                    }
                    DB::table('menu_parentmenu_json')->where('id', $row->id)->update($updates);
                }
            }
            if (Schema::hasTable('custom_permissions')) {
                foreach (DB::table('custom_permissions')->where('menu_code', self::CODE)->where('menu_link', 'workflow')->lockForUpdate()->get() as $row) {
                    $adminRole = Schema::hasTable('roles') && DB::table('roles')->where('id', $row->role_id)->whereIn('name', ['Administrator', 'Super Admin'])->exists();
                    if ($adminRole && $row->parent_code === self::CODE && $row->category_code === 'uIxTa0lV3L4EaV9A6BvJ7x'
                        && $row->menu_type === 'single' && $row->menu_name === 'Workflow Editor'
                        && $row->updated_at === $row->created_at
                        && (json_decode($row->permissions, true) ?: []) === ['read data', 'add data', 'edit data', 'delete data']) {
                        DB::table('custom_permissions')->where('id', $row->id)->delete();
                    }
                }
            }
        });
    }

    private function defaultMenu(): array
    {
        return [
            'parent_code' => self::CODE, 'parent_name' => 'Workflow Editor', 'parent_link' => 'workflow',
            'parent_type' => 'custom', 'parent_roles' => ['Administrator', 'Super Admin'],
            'category_code' => 'uIxTa0lV3L4EaV9A6BvJ7x', 'parent_icon' => '', 'parent_icon_url' => '', 'parent_icon_path' => '',
            'parent_icon_type' => 'custom_input', 'parent_icon_custom' => '<i class="fal fa-project-diagram fa-fw"></i>',
            'is_for_parent_menu' => 'single', 'parent_permissions' => '',
        ];
    }
};
