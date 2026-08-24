<?php

namespace Tests\Feature\Event;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventNavigationMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('menu_parentmenu_json', function (Blueprint $table): void {
            $table->id();
            $table->string('menu_page');
            $table->text('menu_vars');
            $table->text('menu_vars_backup');
        });
        Schema::create('menu', function (Blueprint $table): void {
            $table->id();
            $table->string('module');
            $table->string('menu_name');
            $table->string('url');
            $table->tinyInteger('status')->default(0);
        });
        Schema::create('custom_permissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('category_code')->nullable();
            $table->string('menu_code')->nullable();
            $table->string('parent_code')->nullable();
            $table->string('menu_type')->nullable();
            $table->string('menu_name')->nullable();
            $table->string('menu_link')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        DB::table('menu_parentmenu_json')->insert([
            'id' => 1,
            'menu_page' => 'awesome_admin',
            'menu_vars' => json_encode([['parent_link' => 'manage_article']]),
            'menu_vars_backup' => json_encode([['parent_link' => 'manage_article']]),
        ]);
        foreach (['List of Event', 'Add New', 'Event Categories', 'Layout'] as $index => $name) {
            DB::table('menu')->insert(['module' => 'manage_event', 'menu_name' => $name, 'url' => $name, 'status' => 0]);
        }
        DB::table('custom_permissions')->insert(['role_id' => 1, 'menu_code' => 'old', 'parent_code' => 'old', 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_migration_adds_active_event_parent_normalizes_legacy_links_and_upserts_permissions(): void
    {
        $migration = require database_path('migrations/2026_08_24_000006_sync_manage_event_navigation_and_permissions.php');
        $migration->up();

        $menu = DB::table('menu')->where('module', 'manage_event')->pluck('url', 'menu_name');
        $this->assertSame('manage_event/add', $menu['Add New']);
        $this->assertSame('manage_event?panel=categories', $menu['Event Categories']);
        $this->assertSame(1, (int) DB::table('menu')->where('menu_name', 'Layout')->value('status'));

        $active = json_decode(DB::table('menu_parentmenu_json')->where('id', 1)->value('menu_vars'), true);
        $this->assertTrue(collect($active)->contains(fn (array $item) => ($item['parent_link'] ?? null) === 'manage_event'));
        $this->assertSame(4, DB::table('custom_permissions')->where('menu_code', 'evT7uJ4nM8pQ2xL5cR9sK1')->count());
    }
}
