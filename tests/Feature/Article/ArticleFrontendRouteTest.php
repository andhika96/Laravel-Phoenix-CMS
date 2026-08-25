<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            $table->string('status', 16)->default('active');
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
            $table->string('password_protected', 255)->nullable();
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

    public function test_guest_can_open_archive_detail_and_vue_listdata_while_only_eligible_articles_are_exposed(): void
    {
        $categoryId = DB::table('article_categories')->insertGetId(['name' => 'Design', 'code' => 'design', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
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
        Article::create([
            'uri' => 'password-protected-archive-article',
            'title' => 'Password Protected Archive Article',
            'content' => '<p>Password protected archive content</p>',
            'status' => 'publish',
            'visibility' => 'password_protected',
            'password_protected' => 'archive-access-secret',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        Article::create([
            'uri' => 'draft-article',
            'title' => 'Draft Article',
            'content' => '<p>Draft</p>',
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        Article::create([
            'uri' => 'future-article',
            'title' => 'Future Article',
            'content' => '<p>Future</p>',
            'status' => 'publish',
            'visibility' => 'public',
            'created_at' => now()->addMinute(),
            'updated_at' => now()->addMinute(),
        ]);

        $this->get(route('cms.core.article'))
            ->assertOk()
            ->assertViewIs('article.archive')
            ->assertViewHas('archiveView', 'article.templates.archive.minimal-reading-list')
            ->assertSee('Visible Article')
            ->assertDontSee('Private Article')
            ->assertDontSee('Password Protected Archive Article')
            ->assertDontSee('Draft Article')
            ->assertDontSee('Future Article')
            ->assertViewHas('articleCategories', fn ($categories): bool => $categories->pluck('name')->all() === ['Design']);

        $this->getJson(route('cms.core.article.listdata', ['search' => 'Visible']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.uri', $visible->uri)
            ->assertJsonPath('data.0.category.name', 'Design')
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'Visible Article'));

        $this->getJson(route('cms.core.article.listdata', ['search' => 'Password Protected Archive Article']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 0)
            ->assertJsonPath('data', [])
            ->assertJsonPath('html', fn (string $html): bool => ! str_contains($html, 'password-protected-archive-article')
                && ! str_contains($html, 'Password protected archive content'));

        $this->getJson(route('cms.core.article.listdata', ['search' => 'No matching public article']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 0)
            ->assertJsonPath('html', fn (string $html): bool => str_contains($html, 'No articles found'));

        $this->get(route('cms.core.article.detail', $visible->uri))
            ->assertOk()
            ->assertViewIs('article.detail')
            ->assertViewHas('detailView', 'article.templates.detail.focused-reader')
            ->assertSee('Visible Article')
            ->assertViewHas('previousArticle', fn ($article): bool => $article?->is($older) ?? false)
            ->assertViewHas('nextArticle', fn ($article): bool => $article?->is($newer) ?? false);

        $this->get(route('cms.core.article.detail', 'private-article'))->assertForbidden();
        $this->get(route('cms.core.article.detail', 'draft-article'))->assertNotFound();
        $this->get(route('cms.core.article.detail', 'future-article'))->assertNotFound();
    }

    public function test_private_and_password_protected_articles_use_access_gates_without_leaking_content(): void
    {
        $privateArticle = Article::create([
            'uri' => 'private-access-gate',
            'title' => 'Private article title',
            'content' => '<p>Private article content</p>',
            'status' => 'publish',
            'visibility' => 'private',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        $protectedArticle = Article::create([
            'uri' => 'password-access-gate',
            'title' => 'Password article title',
            'content' => '<p>Password article content</p>',
            'status' => 'publish',
            'visibility' => 'password_protected',
            'password_protected' => 'open-sesame',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $this->get(route('cms.core.article.detail', $privateArticle->uri))
            ->assertForbidden()
            ->assertSee('This article is private')
            ->assertDontSee('Private article title')
            ->assertDontSee('Private article content');

        $this->get(route('cms.core.article.detail', $protectedArticle->uri))
            ->assertOk()
            ->assertSee('Protected article')
            ->assertSee('Enter the password set by the author to continue reading.')
            ->assertDontSee('Password article title')
            ->assertDontSee('Password article content');

        $this->postJson(route('cms.core.article.unlock', $protectedArticle->uri), ['password' => 'wrong-password'])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'The password is incorrect. Please try again.');

        $this->postJson(route('cms.core.article.unlock', $protectedArticle->uri), ['password' => 'open-sesame'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('redirect', route('cms.core.article.detail', $protectedArticle->uri));

        $this->assertTrue(Hash::check('open-sesame', $protectedArticle->fresh()->password_protected));

        $this->get(route('cms.core.article.detail', $protectedArticle->uri))
            ->assertOk()
            ->assertSee('Password article title')
            ->assertSee('Password article content');
    }

    public function test_vue_listdata_pagination_keeps_the_canonical_article_archive_url(): void
    {
        $categoryId = DB::table('article_categories')->insertGetId([
            'name' => 'Pagination',
            'code' => 'pagination',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (range(1, 13) as $index) {
            Article::create([
                'uri' => 'vue-pagination-'.$index,
                'category_id' => $categoryId,
                'title' => 'Vue Pagination '.$index,
                'content' => '<p>Pagination content '.$index.'</p>',
                'status' => 'publish',
                'visibility' => 'public',
                'created_at' => now()->subMinutes($index),
                'updated_at' => now()->subMinutes($index),
            ]);
        }

        $response = $this->getJson(route('cms.core.article.listdata', ['search' => 'Vue Pagination']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 13);

        $html = (string) $response->json('html');
        $this->assertStringContainsString(route('cms.core.article').'?search=Vue%20Pagination&amp;page=2', $html);
        $this->assertStringNotContainsString(route('cms.core.article.listdata'), $html);
    }
}
