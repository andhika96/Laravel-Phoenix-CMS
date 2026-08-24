<?php

namespace Tests\Feature\Article;

use Database\Seeders\ArticleLoadTestSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleLoadTestSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
            'database.connections.sqlite.prefix' => '',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Storage::fake('public');

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('article_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 32)->nullable();
            $table->string('code', 32)->nullable();
            $table->string('status', 16)->default('active');
            $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('uri', 255);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->default(0);
            $table->unsignedBigInteger('subcategory_id')->default(0);
            $table->string('title', 255);
            $table->text('content');
            $table->string('tags', 255)->nullable();
            $table->string('thumb_s', 255)->nullable();
            $table->string('thumb_l', 255)->nullable();
            $table->string('visibility', 32)->default('public');
            $table->string('password_protected', 32)->nullable();
            $table->string('status', 32)->default('draft');
            $table->string('scheduled', 8)->default('false');
            $table->timestamps();
        });

        DB::table('accounts')->insert(['id' => 1, 'email' => 'load-test@example.test', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('article_categories')->insert(['id' => 99, 'name' => 'Existing Category', 'code' => 'existing-category', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('articles')->insert([
            'id' => 99,
            'uri' => 'existing-article',
            'user_id' => 1,
            'category_id' => 99,
            'subcategory_id' => 0,
            'title' => 'Existing Article',
            'content' => '<p>Existing content</p>',
            'tags' => 'existing',
            'visibility' => 'private',
            'status' => 'draft',
            'scheduled' => 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_it_keeps_the_explicit_5000_article_and_seven_category_load_test_contract_prefix_aware(): void
    {
        $seeder = app(ArticleLoadTestSeeder::class);
        $source = file_get_contents(database_path('seeders/ArticleLoadTestSeeder.php'));

        $this->assertSame(5000, $seeder->count);
        $this->assertSame(7, $seeder->categoryCount);
        $this->assertStringContainsString("DB::table('articles')", $source);
        $this->assertStringNotContainsString('lr_articles', $source);
    }

    public function test_it_creates_complete_idempotent_article_load_test_data_with_category_thumbnail_assets(): void
    {
        $seeder = app(ArticleLoadTestSeeder::class);
        $seeder->count = 21;
        $seeder->prefix = 'test-load-articles';
        $seeder->run();

        $articles = DB::table('articles')->where('uri', 'like', 'test-load-articles-%')->get();

        $this->assertCount(21, $articles);
        $this->assertSame(7, DB::table('article_categories')->where('code', 'like', 'load-test-%')->count());
        $this->assertCount(14, Storage::disk('public')->allFiles('articles/load-test/test-load-articles'));
        $this->assertTrue($articles->every(fn ($article): bool => $article->status === 'publish' && $article->visibility === 'public' && $article->scheduled === 'false'));
        $this->assertTrue($articles->every(fn ($article): bool => filled($article->thumb_s) && filled($article->thumb_l) && str_contains($article->content, 'Load-test dataset')));
        $this->assertTrue(Storage::disk('public')->exists($articles->first()->thumb_s));
        $this->assertTrue(Storage::disk('public')->exists($articles->first()->thumb_l));
        $this->assertStringContainsString('<svg ', Storage::disk('public')->get($articles->first()->thumb_l));
        $this->assertStringContainsString('Article performance dataset', Storage::disk('public')->get($articles->first()->thumb_l));
        $this->assertStringNotContainsString('".(', Storage::disk('public')->get($articles->first()->thumb_l));
        $this->assertSame('Existing Article', DB::table('articles')->where('uri', 'existing-article')->value('title'));
        $this->assertSame('Existing Category', DB::table('article_categories')->where('code', 'existing-category')->value('name'));

        $seeder->run();

        $this->assertSame(21, DB::table('articles')->where('uri', 'like', 'test-load-articles-%')->count());
        $this->assertSame(0, $seeder->result['created']);
        $this->assertSame(21, $seeder->result['skipped']);
        $this->assertSame('Existing Article', DB::table('articles')->where('uri', 'existing-article')->value('title'));
        $this->assertSame('Existing Category', DB::table('article_categories')->where('code', 'existing-category')->value('name'));
    }
}
