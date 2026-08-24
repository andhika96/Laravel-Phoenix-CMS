<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Models\Article\ArticleTemplateSetting;
use App\Support\Article\ArticleTemplateCatalog;
use App\Support\Article\PublicArticleQuery;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ArticleTemplateCatalogTest extends TestCase
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
    }

    public function test_catalog_exposes_the_approved_archive_and_detail_templates(): void
    {
        $catalog = app(ArticleTemplateCatalog::class);

        $this->assertSame([
            'minimal-reading-list',
            'editorial-journal',
            'mosaic-magazine',
            'mosaic-classic',
            'balanced-card-grid',
        ], array_keys($catalog->archive()));
        $this->assertSame([
            'focused-reader',
            'editorial-feature',
            'knowledge-toc',
        ], array_keys($catalog->detail()));
        $this->assertSame('minimal-reading-list', $catalog->defaultSettings()['archive_template']);
        $this->assertSame('focused-reader', $catalog->defaultSettings()['detail_template']);
        $this->assertTrue($catalog->isAllowed('archive', 'mosaic-classic'));
        $this->assertFalse($catalog->isAllowed('detail', 'mosaic-magazine'));
        $this->assertSame(
            'assets/images/article/template-previews/mosaic-classic.svg',
            $catalog->archive()['mosaic-classic']['preview_image']
        );
        $this->assertSame('Long-form reading', $catalog->archive()['minimal-reading-list']['best_for']);
        $this->assertSame('Visual discovery', $catalog->archive()['balanced-card-grid']['best_for']);

        foreach ([$catalog->archive(), $catalog->detail()] as $templates) {
            foreach ($templates as $template) {
                $this->assertFileExists(public_path($template['preview_image']));
            }
        }
        $this->assertFileExists(public_path('assets/images/article/article-image-placeholder.svg'));
    }

    public function test_template_settings_are_singleton_and_start_from_catalog_defaults(): void
    {
        $settings = ArticleTemplateSetting::current();

        $this->assertSame('minimal-reading-list', $settings->archive_template);
        $this->assertSame('focused-reader', $settings->detail_template);
        $this->assertSame(12, $settings->archive_per_page);

        $settings->update([
            'archive_template' => 'mosaic-magazine',
            'detail_template' => 'editorial-feature',
            'archive_per_page' => 18,
        ]);

        $this->assertSame($settings->id, ArticleTemplateSetting::current()->id);
        $this->assertSame('mosaic-magazine', ArticleTemplateSetting::current()->archive_template);
        $this->assertSame(18, ArticleTemplateSetting::current()->archive_per_page);
    }

    public function test_public_article_query_only_returns_currently_published_public_articles(): void
    {
        $publicArticle = Article::create([
            'uri' => 'published-public',
            'title' => 'Published public',
            'content' => '<p>Visible</p>',
            'status' => 'publish',
            'visibility' => 'public',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);
        Article::create([
            'uri' => 'draft',
            'title' => 'Draft',
            'content' => '<p>Hidden</p>',
            'status' => 'draft',
            'visibility' => 'public',
        ]);
        Article::create([
            'uri' => 'private',
            'title' => 'Private',
            'content' => '<p>Hidden</p>',
            'status' => 'publish',
            'visibility' => 'private',
        ]);
        Article::create([
            'uri' => 'future',
            'title' => 'Future',
            'content' => '<p>Hidden</p>',
            'status' => 'publish',
            'visibility' => 'public',
            'created_at' => now()->addDay(),
            'updated_at' => now()->addDay(),
        ]);

        $result = app(PublicArticleQuery::class)
            ->builder(Request::create('/article', 'GET'))
            ->pluck('id')
            ->all();

        $this->assertSame([$publicArticle->id], $result);
    }
}
