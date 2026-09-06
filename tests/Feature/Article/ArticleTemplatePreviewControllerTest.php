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
        Schema::create('language', function (Blueprint $table): void {
            $table->id();
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
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

    public function test_preview_device_selects_the_requested_pagination_range_and_model(): void
    {
        $controller = app(ManageArticleTemplateController::class);
        $draft = [
            'pagination' => [
                'type' => 'underline',
                'range' => ['desktop' => 7, 'tablet' => 4, 'mobile' => 2],
            ],
        ];
        $request = Request::create('/manage_article/templates/preview/archive/minimal-reading-list?preview_device=mobile&template_options='.urlencode(json_encode($draft, JSON_THROW_ON_ERROR)), 'GET');
        $response = $controller->preview($request, 'archive', 'minimal-reading-list');
        $data = $response->getData();

        $html = view($data['templateView'], [
            'articles' => $data['articles'],
            'templateSettings' => $data['templateSettings'],
            'templateOptions' => $data['templateOptions'],
            'articleCategories' => $data['articleCategories'],
            'popularArticles' => $data['popularArticles'],
            'previewDevice' => $data['previewDevice'],
        ])->render();

        $this->assertSame('mobile', $data['previewDevice']);
        $this->assertStringContainsString('article-pagination--model-underline', $html);
        $this->assertStringContainsString('data-pagination-range-desktop="7"', $html);
        $this->assertStringContainsString('data-pagination-range-tablet="4"', $html);
        $this->assertStringContainsString('data-pagination-range-mobile="2"', $html);
        $this->assertStringContainsString('data-pagination-range-active="2"', $html);
    }

    public function test_editorial_journal_preview_carries_normalized_template_options_into_the_blade_renderer(): void
    {
        $draft = [
            'thumbnail' => ['height' => '12.5rem'],
            'editorial_journal' => [
                'lead_grid' => ['divider' => ['enabled' => false], 'spacing' => ['without_divider' => '3rem']],
                'thumbnail' => ['edge_to_edge' => true],
                'card' => [
                    'border' => ['enabled' => false, 'type' => 'dashed', 'width' => '2px', 'color' => '#123456', 'radius' => '8px'],
                    'background' => ['type' => 'image', 'image' => '/userfiles/articles/editorial-bg.jpg'],
                    'height' => ['mode' => 'fixed', 'desktop' => '22rem', 'tablet' => '20rem', 'mobile' => '18rem'],
                ],
                'read_more' => ['enabled' => true, 'position' => 'center', 'icon' => 'fas fa-chevron-right'],
            ],
        ];
        $url = '/manage_article/templates/preview/archive/editorial-journal?template_options='.urlencode(json_encode($draft, JSON_THROW_ON_ERROR));
        $data = app(ManageArticleTemplateController::class)->preview(Request::create($url, 'GET'), 'archive', 'editorial-journal')->getData();

        $this->assertSame('12.5rem', data_get($data['templateOptions'], 'thumbnail.height'));
        $this->assertFalse(data_get($data['templateOptions'], 'editorial_journal.lead_grid.divider.enabled'));
        $this->assertTrue(data_get($data['templateOptions'], 'editorial_journal.thumbnail.edge_to_edge'));
        $this->assertSame('fixed', data_get($data['templateOptions'], 'editorial_journal.card.height.mode'));
        $this->assertTrue(data_get($data['templateOptions'], 'editorial_journal.read_more.enabled'));

        $html = view($data['templateView'], [
            'articles' => $data['articles'],
            'templateSettings' => $data['templateSettings'],
            'templateOptions' => $data['templateOptions'],
            'articleCategories' => $data['articleCategories'],
            'popularArticles' => $data['popularArticles'],
        ])->render();

        $this->assertStringContainsString('article-editorial-lead--without-divider', $html);
        $this->assertStringContainsString('article-editorial-card--thumbnail-edge', $html);
        $this->assertStringContainsString('article-editorial-card--height-fixed', $html);
        $this->assertStringContainsString('article-editorial-read-more--center', $html);
        $this->assertStringContainsString('fas fa-chevron-right', $html);
    }

    public function test_minimal_reading_list_options_reach_both_preview_and_public_renderers(): void
    {
        $draft = [
            'header' => [
                'eyebrow' => ['enabled' => true, 'text' => 'Audit eyebrow'],
                'title' => ['enabled' => true, 'text' => 'Audit title'],
                'description' => ['enabled' => true, 'text' => 'Audit description'],
            ],
            'toolbar' => [
                'search' => ['enabled' => true, 'position' => 'center'],
                'category' => ['enabled' => true, 'position' => 'left', 'mode' => 'button-list'],
            ],
            'post_list' => ['item_gap' => '2rem'],
            'sidebar' => [
                'enabled' => true,
                'categories' => ['enabled' => true, 'position' => 'sticky'],
                'popular' => ['enabled' => true, 'position' => 'sticky'],
            ],
            'thumbnail' => [
                'mode' => 'background',
                'fit' => 'contain',
                'height' => '12rem',
                'background_color' => '#123456',
                'frame' => [
                    'enabled' => true,
                    'border_color' => '#abcdef',
                    'border_width' => '2px',
                    'radius' => '4px 8px 12px 16px',
                    'background_color' => '#fefefe',
                ],
            ],
            'pagination' => [
                'show_total' => false,
                'position' => 'left',
                'frame' => [
                    'enabled' => true,
                    'border_color' => '#0f172a',
                    'border_width' => '2px',
                    'radius' => '4px 8px 12px 16px',
                    'background_color' => '#f8fafc',
                ],
                'padding' => [
                    'enabled' => true,
                    'desktop' => ['top' => '3rem', 'right' => '4px', 'bottom' => '5%', 'left' => '6pt'],
                    'tablet' => ['top' => '2rem', 'right' => '3px', 'bottom' => '4%', 'left' => '5pt'],
                    'mobile' => ['top' => '1rem', 'right' => '2px', 'bottom' => '3%', 'left' => '4pt'],
                ],
                'margin' => [
                    'enabled' => true,
                    'desktop' => ['top' => '1rem', 'right' => '2px', 'bottom' => '3%', 'left' => '4pt'],
                    'tablet' => ['top' => '2rem', 'right' => '3px', 'bottom' => '4%', 'left' => '5pt'],
                    'mobile' => ['top' => '3rem', 'right' => '4px', 'bottom' => '5%', 'left' => '6pt'],
                ],
            ],
            'article_title' => ['tag' => 'h6'],
            'shell' => [
                'padding' => [
                    'enabled' => true,
                    'desktop' => ['top' => '3rem', 'right' => '4px', 'bottom' => '5%', 'left' => '6pt'],
                    'tablet' => ['top' => '2rem', 'right' => '3px', 'bottom' => '4%', 'left' => '5pt'],
                    'mobile' => ['top' => '1rem', 'right' => '2px', 'bottom' => '3%', 'left' => '4pt'],
                ],
                'margin' => [
                    'enabled' => true,
                    'desktop' => ['top' => '1rem', 'right' => '2px', 'bottom' => '3%', 'left' => '4pt'],
                    'tablet' => ['top' => '2rem', 'right' => '3px', 'bottom' => '4%', 'left' => '5pt'],
                    'mobile' => ['top' => '3rem', 'right' => '4px', 'bottom' => '5%', 'left' => '6pt'],
                ],
                'frame' => [
                    'enabled' => true,
                    'border_color' => '#1d4ed8',
                    'border_width' => '2px',
                    'radius' => '2rem',
                    'background_color' => '#ffffff',
                ],
            ],
        ];

        $controller = app(ManageArticleTemplateController::class);
        $url = '/manage_article/templates/preview/archive/minimal-reading-list?template_options='.urlencode(json_encode($draft, JSON_THROW_ON_ERROR));
        $previewData = $controller->preview(Request::create($url, 'GET'), 'archive', 'minimal-reading-list')->getData();
        $options = $previewData['templateOptions'];

        $this->assertSame('2rem', data_get($options, 'post_list.item_gap'));
        $this->assertSame('sticky', data_get($options, 'sidebar.categories.position'));
        $this->assertSame('sticky', data_get($options, 'sidebar.popular.position'));
        $this->assertSame('12rem', data_get($options, 'thumbnail.height'));
        $this->assertSame('h6', data_get($options, 'article_title.tag'));
        $this->assertSame('3rem', data_get($options, 'shell.padding.desktop.top'));

        $rendererData = [
            'articles' => $previewData['articles'],
            'templateSettings' => $previewData['templateSettings'],
            'templateOptions' => $options,
            'articleCategories' => $previewData['articleCategories'],
            'popularArticles' => $previewData['popularArticles'],
        ];
        $previewHtml = view($previewData['templateView'], $rendererData)->render();
        $publicHtml = view($previewData['templateView'], $rendererData)->render();

        foreach ([$previewHtml, $publicHtml] as $html) {
            $this->assertStringContainsString('Audit eyebrow', $html);
            $this->assertStringContainsString('Audit title', $html);
            $this->assertStringContainsString('Audit description', $html);
            $this->assertStringContainsString('article-template-toolbar__zone--center', $html);
            $this->assertStringContainsString('--article-reading-list-post-gap:2rem', $html);
            $this->assertStringContainsString('article-reading-list__categories', $html);
            $this->assertStringContainsString('data-article-sidebar-position="sticky"', $html);
            $this->assertStringContainsString('article-reading-list__popular', $html);
            $this->assertStringContainsString('article-background-media', $html);
            $this->assertStringContainsString('--article-thumbnail-height:12rem', $html);
            $this->assertStringContainsString('--article-thumbnail-fit:contain', $html);
            $this->assertStringContainsString('article-title-clamp', $html);
            $this->assertStringContainsString('<h6 class="article-title-clamp">', $html);
            $this->assertStringContainsString('article-pagination--position-left', $html);
            $this->assertStringContainsString('article-pagination--without-total', $html);
            $this->assertStringContainsString('--article-pagination-padding-desktop-top:3rem', $html);
            $this->assertStringContainsString('--article-pagination-margin-mobile-top:3rem', $html);
            $this->assertStringContainsString('data-article-shell-padding="true"', $html);
            $this->assertStringContainsString('data-article-shell-margin="true"', $html);
            $this->assertStringContainsString('data-article-shell-frame="true"', $html);
            $this->assertStringNotContainsString('data-article-category-select', $html);
        }

        $selectDraft = $draft;
        $selectDraft['toolbar']['category']['mode'] = 'select';
        $selectDraft['thumbnail']['mode'] = 'asset';
        $selectUrl = '/manage_article/templates/preview/archive/minimal-reading-list?template_options='.urlencode(json_encode($selectDraft, JSON_THROW_ON_ERROR));
        $selectData = $controller->preview(Request::create($selectUrl, 'GET'), 'archive', 'minimal-reading-list')->getData();
        $selectHtml = view($selectData['templateView'], [
            'articles' => $selectData['articles'],
            'templateSettings' => $selectData['templateSettings'],
            'templateOptions' => $selectData['templateOptions'],
            'articleCategories' => $selectData['articleCategories'],
            'popularArticles' => $selectData['popularArticles'],
        ])->render();

        $this->assertStringContainsString('data-article-category-select', $selectHtml);
        $this->assertStringNotContainsString('article-reading-list__categories', $selectHtml);
        $this->assertStringContainsString('article-asset-media', $selectHtml);
        $this->assertStringContainsString('<img src=', $selectHtml);
        $this->assertStringNotContainsString('--article-thumbnail-height:', $selectHtml);
        $this->assertStringNotContainsString('article-background-media', $selectHtml);
    }
}
