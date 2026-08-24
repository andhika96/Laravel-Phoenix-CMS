<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ArticleFrontendRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        $this->app['view']->addLocation(base_path('tests/Fixtures/views'));

        Schema::create('language', function (Blueprint $table): void {
            $table->id();
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
        });
        Schema::create('themes', function (Blueprint $table): void {
            $table->id();
            $table->string('theme_code', 32)->nullable();
            $table->string('theme_name', 255)->nullable();
            $table->string('theme_foldername', 155)->nullable();
            $table->string('theme_cms', 155)->nullable();
            $table->string('theme_auth', 155)->nullable();
            $table->string('theme_frontend', 155)->nullable();
            $table->string('theme_version', 12)->nullable();
            $table->timestamps();
        });
        Schema::create('theme_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('theme_id');
            $table->string('theme_code', 155);
            $table->string('theme_name', 255);
        });
        Schema::create('site_config', function (Blueprint $table): void {
            $table->id();
            $table->string('site_name', 255)->nullable();
            $table->string('font_family', 32)->nullable();
            $table->decimal('font_size', 8, 3)->nullable();
            $table->string('font_size_unit', 8)->nullable();
            $table->timestamps();
        });
        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('fullname')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('article_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 32)->nullable();
            $table->string('code', 32)->nullable();
            $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('uri', 255);
            $table->unsignedBigInteger('user_id')->default(0);
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

        $migration = require database_path('migrations/2026_08_24_000007_create_article_template_settings_table.php');
        $migration->up();

        DB::table('themes')->insert([
            'id' => 1,
            'theme_code' => 'test-article',
            'theme_name' => 'Test Article',
            'theme_foldername' => 'test_article',
            'theme_frontend' => 'frontend_layout',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('theme_settings')->insert([
            'id' => 1,
            'theme_id' => 1,
            'theme_code' => 'test-article',
            'theme_name' => 'Test Article',
        ]);
        DB::table('site_config')->insert([
            'id' => 1,
            'site_name' => 'Test Site',
            'font_family' => 'nunito',
            'font_size' => 14,
            'font_size_unit' => 'px',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_archive_static_listdata_route_and_detail_only_expose_eligible_articles(): void
    {
        $categoryId = DB::table('article_categories')->insertGetId(['name' => 'Design', 'code' => 'design', 'created_at' => now(), 'updated_at' => now()]);
        $accountId = DB::table('accounts')->insertGetId(['fullname' => 'Article Author', 'username' => 'author', 'email' => 'author@example.test', 'created_at' => now(), 'updated_at' => now()]);
        $visible = Article::create([
            'uri' => 'visible-article',
            'user_id' => $accountId,
            'category_id' => $categoryId,
            'title' => 'Visible Article',
            'content' => '<p>Visible content</p>',
            'tags' => 'design,frontend',
            'status' => 'publish',
            'visibility' => 'public',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        $older = Article::create([
            'uri' => 'older-article',
            'user_id' => $accountId,
            'category_id' => $categoryId,
            'title' => 'Older Article',
            'content' => '<p>Older</p>',
            'status' => 'publish',
            'visibility' => 'public',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);
        $newer = Article::create([
            'uri' => 'newer-article',
            'user_id' => $accountId,
            'category_id' => $categoryId,
            'title' => 'Newer Article',
            'content' => '<p>Newer</p>',
            'status' => 'publish',
            'visibility' => 'public',
            'created_at' => now()->subSeconds(10),
            'updated_at' => now()->subSeconds(10),
        ]);
        Article::create([
            'uri' => 'private-article',
            'title' => 'Private Article',
            'content' => '<p>Private</p>',
            'status' => 'publish',
            'visibility' => 'private',
        ]);

        $this->withoutMiddleware()->get(route('cms.core.article'))
            ->assertOk()
            ->assertSee('Visible Article');

        $this->withoutMiddleware()->getJson(route('cms.core.article.listdata', ['search' => 'Visible']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.uri', $visible->uri)
            ->assertJsonPath('data.0.category.name', 'Design');

        $this->withoutMiddleware()->get(route('cms.core.article.detail', $visible->uri))
            ->assertOk()
            ->assertSee('Visible Article')
            ->assertViewHas('previousArticle', fn ($article): bool => $article?->is($older) ?? false)
            ->assertViewHas('nextArticle', fn ($article): bool => $article?->is($newer) ?? false);

        $this->withoutMiddleware()->get(route('cms.core.article.detail', 'private-article'))->assertNotFound();
    }
}
