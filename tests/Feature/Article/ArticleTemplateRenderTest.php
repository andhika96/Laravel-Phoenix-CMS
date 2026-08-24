<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Support\Article\ArticleTemplateCatalog;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ArticleTemplateRenderTest extends TestCase
{
    public function test_every_registered_article_template_has_a_blade_view(): void
    {
        $catalog = app(ArticleTemplateCatalog::class);

        foreach ([$catalog->archive(), $catalog->detail()] as $templates) {
            foreach ($templates as $template) {
                $this->assertTrue(view()->exists($template['view']), $template['view'].' must exist');
            }
        }
    }

    public function test_archive_templates_use_accessible_article_links_and_numbered_pagination_contract(): void
    {
        foreach ([
            'minimal-reading-list',
            'mosaic-magazine',
            'editorial-journal',
            'mosaic-classic',
            'balanced-card-grid',
        ] as $key) {
            $source = file_get_contents(resource_path('views/article/templates/archive/'.$key.'.blade.php'));

            $this->assertStringContainsString("route('cms.core.article.detail'", $source);
            $this->assertStringContainsString("article.templates.partials.pagination", $source);
            $this->assertStringContainsString('article-image-placeholder.svg', $source);
            $this->assertStringContainsString('article-title-clamp', $source);
            $this->assertStringContainsString('article-excerpt-clamp', $source);
            $this->assertStringContainsString('article.templates.partials.media-link', $source);
        }

        $sharedMedia = file_get_contents(resource_path('views/article/templates/partials/media-link.blade.php'));
        $this->assertStringContainsString('article-media-frame', $sharedMedia);
        $this->assertStringContainsString('article-background-media', $sharedMedia);

        foreach ([
            'focused-reader',
            'editorial-feature',
            'knowledge-toc',
        ] as $key) {
            $source = file_get_contents(resource_path('views/article/templates/detail/'.$key.'.blade.php'));

            $this->assertStringContainsString('article-image-placeholder.svg', $source);
            $this->assertStringContainsString('article.templates.partials.detail-navigation', $source);
            $this->assertStringContainsString('article-detail__header', $source);
        }

        $pagination = file_get_contents(resource_path('views/article/templates/partials/pagination.blade.php'));
        $this->assertStringContainsString('aria-label="{{ t(\'Article pagination\') }}"', $pagination);
        $this->assertStringContainsString("aria-current=\"page\"", $pagination);
    }

    public function test_mosaic_classic_renders_a_placeholder_when_an_article_has_no_thumbnail(): void
    {
        $article = new Article([
            'uri' => 'mosaic-classic-test',
            'title' => 'Mosaic Classic Test',
            'content' => '<p>Preview content</p>',
        ]);
        $article->setRelation('category', null);
        $article->created_at = now();
        $articles = new LengthAwarePaginator([$article], 1, 12, 1, [
            'path' => route('cms.core.article'),
        ]);

        $html = view('article.templates.archive.mosaic-classic', [
            'articles' => $articles,
            'templateSettings' => null,
        ])->render();

        $this->assertStringContainsString('Mosaic Classic', $html);
        $this->assertStringContainsString('article-image-placeholder.svg', $html);
        $this->assertStringContainsString('mosaic-classic-test', $html);
    }

    public function test_public_pagination_uses_the_cms_visual_contract_with_result_context(): void
    {
        $articles = new LengthAwarePaginator(range(1, 12), 96, 12, 5, [
            'path' => route('cms.core.article'),
        ]);

        $html = view('article.templates.partials.pagination', ['articles' => $articles])->render();

        $this->assertStringContainsString('article-pagination__summary', $html);
        $this->assertStringContainsString('Showing 49–60 of 96', $html);
        $this->assertStringContainsString('ph-pagination', $html);
        $this->assertStringContainsString('aria-label="First page"', $html);
        $this->assertStringContainsString('aria-label="Last page"', $html);
        $this->assertStringContainsString('data-article-pagination-link', $html);
    }
}
