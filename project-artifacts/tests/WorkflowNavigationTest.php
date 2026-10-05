<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WorkflowNavigationTest extends TestCase
{
    public function test_navigation_is_additive_idempotent_and_respects_existing_permissions(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite'); DB::setDefaultConnection('sqlite');
        Schema::create('menu_parentmenu_json', function (Blueprint $table) { $table->id(); $table->string('menu_page'); $table->text('menu_vars'); $table->text('menu_vars_backup'); });
        Schema::create('roles', function (Blueprint $table) { $table->id(); $table->string('name'); });
        Schema::create('custom_permissions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('role_id');
            foreach (['category_code', 'parent_code', 'menu_code', 'menu_type', 'menu_name', 'menu_link'] as $column) { $table->string($column)->nullable(); }
            $table->text('permissions'); $table->timestamps();
        });
        DB::table('menu_parentmenu_json')->insert(['id' => 1, 'menu_page' => 'awesome_admin', 'menu_vars' => '[{"parent_link":"manage_article","custom":"retain"}]', 'menu_vars_backup' => '[{"parent_link":"dashboard"}]']);
        DB::table('roles')->insert([['id' => 10, 'name' => 'Administrator'], ['id' => 11, 'name' => 'Super Admin'], ['id' => 12, 'name' => 'Staff']]);
        DB::table('custom_permissions')->insert(['role_id' => 10, 'menu_link' => 'workflow', 'permissions' => '["read data"]']);
        $migration = require database_path('migrations/2026_10_05_000002_add_workflow_navigation.php');
        $migration->up(); $migration->up();
        $row = DB::table('menu_parentmenu_json')->first();
        $active = json_decode($row->menu_vars, true);
        $this->assertCount(2, $active);
        $this->assertSame('retain', $active[0]['custom']);
        $this->assertSame('workflow', $active[1]['parent_link']);
        $this->assertCount(2, json_decode($row->menu_vars_backup, true));
        $this->assertSame(2, DB::table('custom_permissions')->count());
        $this->assertSame('["read data"]', DB::table('custom_permissions')->where('role_id', 10)->value('permissions'));
        $this->assertSame(0, DB::table('custom_permissions')->where('role_id', 12)->count());
        $migration->down();
        $this->assertSame([['parent_link' => 'manage_article', 'custom' => 'retain']], json_decode(DB::table('menu_parentmenu_json')->value('menu_vars'), true));
        $this->assertSame(1, DB::table('custom_permissions')->count());
        $this->assertSame('["read data"]', DB::table('custom_permissions')->where('role_id', 10)->value('permissions'));
        $migration->up();
        $customizedMenu = json_decode(DB::table('menu_parentmenu_json')->value('menu_vars'), true);
        $customizedMenu[1]['parent_icon_type'] = 'media';
        DB::table('menu_parentmenu_json')->update(['menu_vars' => json_encode($customizedMenu)]);
        DB::table('custom_permissions')->where('role_id', 11)->update(['parent_code' => 'admin-customization']);
        $migration->down();
        $this->assertCount(2, json_decode(DB::table('menu_parentmenu_json')->value('menu_vars'), true));
        $this->assertSame(2, DB::table('custom_permissions')->count());
    }
}
