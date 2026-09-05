<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Support\Article\ArticleTemplateOptions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ArticleMinimalReadingListHeaderRenderTest extends TestCase
{
    public function test_enabled_header_content_renders_above_the_search_toolbar(): void
    {
        $article = new Article([
            'id' => 7101,
            'uri' => 'minimal-header-render-test',
            'title' => 'Minimal Header Render Test',
            'content' => '<p>Preview content for header rendering.</p>',
            'created_at' => now(),
        ]);
        $articles = new LengthAwarePaginator([$article], 1, 1, 1, [
            'path' => route('cms.core.article'),
        ]);
        $options = app(ArticleTemplateOptions::class)->archive('minimal-reading-list', [
            'header' => [
                'eyebrow' => ['enabled' => true, 'text' => 'Journal'],
                'title' => ['enabled' => true, 'text' => 'Minimal Reading List'],
                'description' => ['enabled' => true, 'text' => 'Thoughtful reads on design, technology, and the web.'],
            ],
            'toolbar' => ['search' => ['enabled' => true, 'position' => 'left']],
        ]);

        $html = view('article.templates.archive.minimal-reading-list', [
            'articles' => $articles,
            'templateSettings' => null,
            'templateOptions' => $options,
            'articleCategories' => collect(),
            'popularArticles' => collect(),
        ])->render();

        $headerCopy = strpos($html, 'article-template-header__copy');
        $toolbar = strpos($html, 'article-template-toolbar');

        $this->assertNotFalse($headerCopy);
        $this->assertNotFalse($toolbar);
        $this->assertLessThan($toolbar, $headerCopy);
        $this->assertStringContainsString('Journal', $html);
        $this->assertStringContainsString('Minimal Reading List', $html);
        $this->assertStringContainsString('Thoughtful reads on design, technology, and the web.', $html);
        $this->assertStringContainsString('article-minimal-reading-list-search', $html);
    }
}
