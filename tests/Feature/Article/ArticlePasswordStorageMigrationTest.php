<?php

namespace Tests\Feature\Article;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ArticlePasswordStorageMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('visibility', 32)->default('public');
            $table->string('password_protected', 64)->nullable();
        });
    }

    public function test_password_storage_migration_hashes_legacy_protected_passwords_without_touching_other_values(): void
    {
        $existingHash = Hash::make('already-secure');
        DB::table('articles')->insert([
            ['id' => 1, 'visibility' => 'password_protected', 'password_protected' => 'legacy-secret'],
            ['id' => 2, 'visibility' => 'password_protected', 'password_protected' => $existingHash],
            ['id' => 3, 'visibility' => 'public', 'password_protected' => 'not-a-public-access-password'],
        ]);

        $widenMigration = require database_path('migrations/2026_08_25_000010_widen_article_password_protected_column.php');
        $widenMigration->up();

        $hashMigration = require database_path('migrations/2026_08_25_000011_hash_legacy_article_passwords.php');
        $hashMigration->up();

        $legacyHash = (string) DB::table('articles')->where('id', 1)->value('password_protected');
        $this->assertNotSame('legacy-secret', $legacyHash);
        $this->assertTrue(Hash::check('legacy-secret', $legacyHash));
        $this->assertSame($existingHash, DB::table('articles')->where('id', 2)->value('password_protected'));
        $this->assertSame('not-a-public-access-password', DB::table('articles')->where('id', 3)->value('password_protected'));
    }
}
