<?php

namespace Tests\Feature;

use App\Models\Article\Article;
use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageArticleThumbnailTest extends TestCase
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
        EventFacade::fake();

        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('fullname')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('status')->default(2);
            $table->timestamps();
        });
        Schema::create('language', function (Blueprint $table): void {
            $table->id();
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
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
    }

    public function test_admin_can_create_article_thumbnail_from_ckfinder_articles_image(): void
    {
        Storage::fake('public');
        $admin = Account::create(['email' => 'article-thumbnail-admin@example.test', 'username' => 'article-thumbnail-admin', 'fullname' => 'Article Thumbnail Admin']);
        $categoryId = DB::table('article_categories')->insertGetId(['name' => 'News', 'code' => 'news', 'created_at' => now(), 'updated_at' => now()]);
        $sourcePath = 'ckfinder/articles/article-source.jpg';
        $sourceImage = UploadedFile::fake()->image('article-source.jpg', 640, 360);
        Storage::disk('public')->put($sourcePath, file_get_contents($sourceImage->getRealPath()));

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.store'), [
            'title' => 'CKFinder Article Thumbnail',
            'content' => '<p>Article content</p>',
            'category_id' => $categoryId,
            'status' => 'draft',
            'visibility' => 'public',
            'thumbnail_source' => 'ckfinder',
            'thumbnail_ckfinder_url' => '/storage/ckfinder/articles/article-source.jpg',
        ])->assertOk()->assertJsonPath('success', true);

        $article = Article::query()->firstOrFail();
        $this->assertNotNull($article->thumb_l);
        $this->assertNotNull($article->thumb_s);
        $this->assertNotSame($sourcePath, $article->thumb_l);
        Storage::disk('public')->assertExists($article->thumb_l);
        Storage::disk('public')->assertExists($article->thumb_s);
    }

    public function test_admin_can_remove_an_existing_article_thumbnail(): void
    {
        Storage::fake('public');
        $admin = Account::create(['email' => 'article-remove-admin@example.test', 'username' => 'article-remove-admin', 'fullname' => 'Article Remove Admin']);
        $categoryId = DB::table('article_categories')->insertGetId(['name' => 'News', 'code' => 'news', 'created_at' => now(), 'updated_at' => now()]);
        $largePath = 'articles/test/removable.jpg';
        $smallPath = 'articles/test/removable_small.jpg';
        Storage::disk('public')->put($largePath, 'large-thumbnail');
        Storage::disk('public')->put($smallPath, 'small-thumbnail');
        $article = Article::create([
            'uri' => 'removable-thumbnail-article',
            'user_id' => $admin->id,
            'category_id' => $categoryId,
            'title' => 'Removable Thumbnail Article',
            'content' => '<p>Article content</p>',
            'status' => 'draft',
            'visibility' => 'public',
            'thumb_l' => $largePath,
            'thumb_s' => $smallPath,
        ]);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.update', $article->id), [
            'title' => $article->title,
            'uri' => $article->uri,
            'content' => $article->content,
            'category_id' => $categoryId,
            'status' => $article->status,
            'visibility' => $article->visibility,
            'remove_thumbnail' => true,
        ])->assertOk()->assertJsonPath('success', true);

        $article->refresh();
        $this->assertNull($article->thumb_l);
        $this->assertNull($article->thumb_s);
        Storage::disk('public')->assertMissing($largePath);
        Storage::disk('public')->assertMissing($smallPath);
    }

    public function test_admin_can_replace_an_article_thumbnail_from_ckfinder_articles_image(): void
    {
        Storage::fake('public');
        $admin = Account::create(['email' => 'article-replace-admin@example.test', 'username' => 'article-replace-admin', 'fullname' => 'Article Replace Admin']);
        $categoryId = DB::table('article_categories')->insertGetId(['name' => 'News', 'code' => 'news', 'created_at' => now(), 'updated_at' => now()]);
        $oldLargePath = 'articles/test/old.jpg';
        $oldSmallPath = 'articles/test/old_small.jpg';
        $sourcePath = 'ckfinder/articles/replacement.jpg';
        Storage::disk('public')->put($oldLargePath, 'old-large-thumbnail');
        Storage::disk('public')->put($oldSmallPath, 'old-small-thumbnail');
        $sourceImage = UploadedFile::fake()->image('replacement.jpg', 640, 360);
        Storage::disk('public')->put($sourcePath, file_get_contents($sourceImage->getRealPath()));
        $article = Article::create([
            'uri' => 'replace-thumbnail-article',
            'user_id' => $admin->id,
            'category_id' => $categoryId,
            'title' => 'Replace Thumbnail Article',
            'content' => '<p>Article content</p>',
            'status' => 'draft',
            'visibility' => 'public',
            'thumb_l' => $oldLargePath,
            'thumb_s' => $oldSmallPath,
        ]);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.update', $article->id), [
            'title' => $article->title,
            'uri' => $article->uri,
            'content' => $article->content,
            'category_id' => $categoryId,
            'status' => $article->status,
            'visibility' => $article->visibility,
            'thumbnail_source' => 'ckfinder',
            'thumbnail_ckfinder_url' => '/storage/ckfinder/articles/replacement.jpg',
        ])->assertOk()->assertJsonPath('success', true);

        $article->refresh();
        $this->assertNotSame($oldLargePath, $article->thumb_l);
        $this->assertNotSame($oldSmallPath, $article->thumb_s);
        Storage::disk('public')->assertMissing($oldLargePath);
        Storage::disk('public')->assertMissing($oldSmallPath);
        Storage::disk('public')->assertExists($article->thumb_l);
        Storage::disk('public')->assertExists($article->thumb_s);
    }

    public function test_article_thumbnail_ckfinder_url_must_stay_within_articles_resource(): void
    {
        $admin = Account::create(['email' => 'article-thumbnail-invalid@example.test', 'username' => 'article-thumbnail-invalid', 'fullname' => 'Article Thumbnail Invalid']);

        $this->withoutMiddleware()->actingAs($admin, 'web')->postJson(route('cms.core.manage_article.store'), [
            'title' => 'Invalid CKFinder Source',
            'content' => '<p>Article content</p>',
            'status' => 'draft',
            'visibility' => 'public',
            'thumbnail_source' => 'ckfinder',
            'thumbnail_ckfinder_url' => '/storage/ckfinder/events/not-allowed.jpg',
        ])->assertStatus(422);
    }
}
