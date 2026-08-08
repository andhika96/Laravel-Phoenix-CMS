<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderEditorVersionMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Schema::create('page_builder', function (Blueprint $table): void {
            $table->id();
            $table->string('uri')->unique();
            $table->timestamps();
        });
    }

    public function test_migration_backfills_v20_and_defaults_new_rows_to_v20(): void
    {
        DB::table('page_builder')->insert(['uri' => 'existing', 'created_at' => now(), 'updated_at' => now()]);
        $migration = require database_path('migrations/2026_08_08_170000_add_editor_version_to_page_builder_table.php');
        $migration->up();

        $this->assertSame('2.0', DB::table('page_builder')->where('uri', 'existing')->value('editor_version'));
        DB::table('page_builder')->insert(['uri' => 'new', 'created_at' => now(), 'updated_at' => now()]);
        $this->assertSame('2.0', DB::table('page_builder')->where('uri', 'new')->value('editor_version'));
        $this->assertSame('2.0', Page_Builder::EDITOR_VERSION_V20);
        $this->assertSame('2.3', Page_Builder::EDITOR_VERSION_V23);
    }
}
