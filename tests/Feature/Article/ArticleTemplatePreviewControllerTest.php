<?php

namespace Tests\Feature\Article;

use App\Http\Controllers\Web\Manage_Article\ManageArticleTemplateController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ArticleTemplatePreviewControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.prefix' => '',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('article_template_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('archive_template', 64);
            $table->string('detail_template', 64);
            $table->unsignedSmallInteger('archive_per_page');
            $table->unsignedBigInteger('updated_by')->nullable();
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
            $table->string('title', 255);
            $table->text('content');
            $table->string('tags', 255)->nullable();
            $table->string('thumb_s', 255)->nullable();
            $table->string('thumb_l', 255)->nullable();
            $table->string('visibility', 32)->default('public');
            $table->string('status', 32)->default('draft');
            $table->timestamps();
        });
    }

    public function test_preview_uses_curated_fixture_for_archive_and_detail_without_article_database_queries(): void
    {
        $controller = app(ManageArticleTemplateController::class);

        $archive = $controller->preview(Request::create('/manage_article/templates/preview/archive/mosaic-classic?template_options='.urlencode(json_encode([
            'toolbar' => ['search' => ['enabled' => false, 'position' => 'center']],
            'thumbnail' => ['mode' => 'asset', 'fit' => 'contain', 'frame' => ['enabled' => true, 'border_width' => '2pt']],
            'pagination' => ['show_total' => false, 'position' => 'left', 'padding' => ['enabled' => true, 'desktop' => ['top' => '1rem', 'right' => '2px', 'bottom' => '3%', 'left' => '4pt']]],
            'article_title' => ['tag' => 'h2'],
            'shell' => ['frame' => ['enabled' => true, 'radius' => '1.5rem']],
        ], JSON_THROW_ON_ERROR)), 'GET'), 'archive', 'mosaic-classic');
        $detail = $controller->preview(Request::create('/manage_article/templates/preview/detail/focused-reader', 'GET'), 'detail', 'focused-reader');

        $archiveData = $archive->getData();
        $detailData = $detail->getData();

        $this->assertTrue($archiveData['isPreviewFixture']);
        $this->assertSame(48, $archiveData['articles']->total());
        $this->assertStringContainsString('/manage_article/templates/preview/archive/mosaic-classic?template_options=', $archiveData['articles']->url(2));
        $this->assertFalse(data_get($archiveData['templateOptions'], 'toolbar.search.enabled'));
        $this->assertSame('center', data_get($archiveData['templateOptions'], 'toolbar.search.position'));
        $this->assertSame('asset', data_get($archiveData['templateOptions'], 'thumbnail.mode'));
        $this->assertSame('contain', data_get($archiveData['templateOptions'], 'thumbnail.fit'));
        $this->assertSame('2pt', data_get($archiveData['templateOptions'], 'thumbnail.frame.border_width'));
        $this->assertFalse(data_get($archiveData['templateOptions'], 'pagination.show_total'));
        $this->assertSame('left', data_get($archiveData['templateOptions'], 'pagination.position'));
        $this->assertSame('1rem', data_get($archiveData['templateOptions'], 'pagination.padding.desktop.top'));
        $this->assertSame('h2', data_get($archiveData['templateOptions'], 'article_title.tag'));
        $this->assertTrue(data_get($archiveData['templateOptions'], 'shell.frame.enabled'));
        $this->assertSame('1.5rem', data_get($archiveData['templateOptions'], 'shell.frame.radius'));
        $this->assertTrue($detailData['isPreviewFixture']);
        $this->assertFalse($detailData['article']->exists);
        $this->assertNotNull($detailData['previousArticle']);
        $this->assertNotNull($detailData['nextArticle']);
    }
}
