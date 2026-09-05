<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Models\Article\Article_Categories;
use App\Support\Article\ArticleTemplateOptions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ArticleMinimalReadingListRenderTest extends TestCase
{
    public function test_sidebar_options_control_panels_without_removing_the_archive_pagination(): void
    {
        $article = new Article([
            'id' => 7001,
            'uri' => 'minimal-sidebar-render-test',
            'title' => 'Minimal Sidebar Render Test',
            'content' => '<p>Preview content for the Minimal Reading List.</p>',
            'created_at' => now(),
        ]);
        $article->setRelation('category', new Article_Categories(['id' => 7002, 'name' => 'Design']));
        $articles = new LengthAwarePaginator([$article], 24, 1, 1, [
            'path' => route('cms.core.article'),
        ]);
        $popularArticles = collect([$article]);
        $categories = collect([new Article_Categories(['id' => 7002, 'name' => 'Design'])]);
        $normalizer = app(ArticleTemplateOptions::class);

        $defaultHtml = view('article.templates.archive.minimal-reading-list', [
            'articles' => $articles,
            'templateSettings' => null,
            'templateOptions' => $normalizer->archive('minimal-reading-list'),
            'articleCategories' => $categories,
            'popularArticles' => $popularArticles,
        ])->render();

        $this->assertStringContainsString('article-reading-list__categories', $defaultHtml);
        $this->assertStringContainsString('article-reading-list__popular', $defaultHtml);
        $this->assertStringContainsString('article-pagination--ssr', $defaultHtml);

        $categoriesOnlyHtml = view('article.templates.archive.minimal-reading-list', [
            'articles' => $articles,
            'templateSettings' => null,
            'templateOptions' => $normalizer->archive('minimal-reading-list', [
                'sidebar' => [
                    'categories' => ['enabled' => true],
                    'popular' => ['enabled' => false],
                ],
            ]),
            'articleCategories' => $categories,
            'popularArticles' => $popularArticles,
        ])->render();

        $this->assertStringContainsString('article-reading-list__categories', $categoriesOnlyHtml);
        $this->assertStringNotContainsString('article-reading-list__popular', $categoriesOnlyHtml);
        $this->assertStringContainsString('article-pagination--ssr', $categoriesOnlyHtml);

        $withoutSidebarHtml = view('article.templates.archive.minimal-reading-list', [
            'articles' => $articles,
            'templateSettings' => null,
            'templateOptions' => $normalizer->archive('minimal-reading-list', [
                'sidebar' => ['enabled' => false],
            ]),
            'articleCategories' => $categories,
            'popularArticles' => $popularArticles,
        ])->render();

        $this->assertStringContainsString('article-reading-list-layout--without-sidebar', $withoutSidebarHtml);
        $this->assertStringNotContainsString('article-reading-list__sidebar', $withoutSidebarHtml);
        $this->assertStringContainsString('article-pagination--ssr', $withoutSidebarHtml);
    }
}
