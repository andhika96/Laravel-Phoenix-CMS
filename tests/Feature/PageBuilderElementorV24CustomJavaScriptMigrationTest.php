<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV24CustomJavaScriptMigrationTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = (string) config('database.default');
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Schema::create('page_builder', function (Blueprint $table): void {
            $table->id();
            $table->string('uri')->unique();
            $table->text('vars');
        });
        DB::table('page_builder')->insert(['uri' => 'legacy', 'vars' => '[]']);
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);
        parent::tearDown();
    }

    public function test_migration_adds_reversible_fields_and_defaults_legacy_rows_to_disabled(): void
    {
        $migration = require base_path('database/migrations/2026_08_29_000300_add_custom_javascript_to_page_builder_table.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumn('page_builder', 'custom_js'));
        $this->assertTrue(Schema::hasColumn('page_builder', 'custom_js_mode'));
        $this->assertNull(DB::table('page_builder')->where('uri', 'legacy')->value('custom_js'));
        $this->assertSame('disabled', DB::table('page_builder')->where('uri', 'legacy')->value('custom_js_mode'));

        $migration->down();

        $this->assertFalse(Schema::hasColumn('page_builder', 'custom_js'));
        $this->assertFalse(Schema::hasColumn('page_builder', 'custom_js_mode'));
    }
}
