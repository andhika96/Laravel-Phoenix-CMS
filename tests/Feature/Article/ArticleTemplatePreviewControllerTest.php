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
    }

    public function test_preview_uses_curated_fixture_for_archive_and_detail_without_article_database_queries(): void
    {
        $controller = app(ManageArticleTemplateController::class);

        $archive = $controller->preview(Request::create('/manage_article/templates/preview/archive/mosaic-classic', 'GET'), 'archive', 'mosaic-classic');
        $detail = $controller->preview(Request::create('/manage_article/templates/preview/detail/focused-reader', 'GET'), 'detail', 'focused-reader');

        $archiveData = $archive->getData();
        $detailData = $detail->getData();

        $this->assertTrue($archiveData['isPreviewFixture']);
        $this->assertSame(48, $archiveData['articles']->total());
        $this->assertStringContainsString('/manage_article/templates/preview/archive/mosaic-classic?page=2', $archiveData['articles']->url(2));
        $this->assertTrue($detailData['isPreviewFixture']);
        $this->assertFalse($detailData['article']->exists);
        $this->assertNotNull($detailData['previousArticle']);
        $this->assertNotNull($detailData['nextArticle']);
    }
}
